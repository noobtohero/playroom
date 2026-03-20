import subprocess
import shutil
import zipfile
import secrets
import os
import argparse
from pathlib import Path
from tqdm import tqdm

# ─────────────────────────────────────────────
# CONFIG
# ─────────────────────────────────────────────

INPUT_DIR = Path("input")
OUTPUT_DIR = Path("output")
TEMP_DIR = Path("temp")

SEGMENT_TIME = 6

FFMPEG = r"C:\Users\NTH\AppData\Local\Microsoft\WinGet\Packages\Gyan.FFmpeg_Microsoft.Winget.Source_8wekyb3d8bbwe\ffmpeg-8.0.1-full_build\bin\ffmpeg.exe"

# Base URL ของ key API endpoint บน server
# ต้องตรงกับ app.baseURL ใน .env ของ CI4 project
KEY_API_BASE = "http://playroom.test/media/stream/key/"

# Variants: (folder_name, height_px, video_bitrate, resolution_label)
VARIANTS = [
    ("1080p", 1080, "5000k"),
    ("720p", 720, "2800k"),
    ("480p", 480, "1200k"),
]


# ─────────────────────────────────────────────
# RUN FFMPEG
# ─────────────────────────────────────────────


def run_ffmpeg(cmd: list, desc: str = ""):
    process = subprocess.Popen(
        cmd, stderr=subprocess.PIPE, stdout=subprocess.DEVNULL, text=True
    )
    for line in process.stderr:
        if "time=" in line:
            print(f"  [{desc}] {line.strip()}", end="\r")
    process.wait()
    if process.returncode != 0:
        raise RuntimeError(f"FFmpeg failed for: {desc}")


# ─────────────────────────────────────────────
# CREATE KEY  (UUID filename + keyinfo)
# ─────────────────────────────────────────────


def create_key(temp_dir: Path) -> tuple[Path, str]:
    """
    Generate a random AES-128 key.
    - Key file is named with a UUID → ไม่สามารถเดาชื่อไฟล์ได้
    - keyinfo.txt ใช้ KEY_API_BASE URL → ffmpeg embed URL ที่ถูกต้องใน m3u8 ตั้งแต่ต้น
    - keyinfo.txt ถูก exclude ออกจาก zip (ไม่จำเป็น และเปิดเผย path)

    Returns:
        (keyinfo_path, key_filename)
    """
    key_filename = secrets.token_hex(16) + ".key"  # เช่น a3f9b2c1d0e4...key
    key_path = temp_dir / key_filename
    key_path.write_bytes(secrets.token_bytes(16))

    # Line 1: URI ที่จะ embed ใน m3u8  (ชี้ไปที่ API endpoint บน server)
    # Line 2: absolute path ของ key file บนเครื่องนี้  (ffmpeg อ่าน key จากที่นี่)
    # Line 3: (optional) IV — เว้นว่างเพื่อให้ ffmpeg generate per-segment IV อัตโนมัติ
    keyinfo_path = temp_dir / "keyinfo.txt"
    keyinfo_path.write_text(
        f"{KEY_API_BASE}{key_filename}\n" f"{key_path.absolute()}\n", encoding="utf-8"
    )

    return keyinfo_path, key_filename


# ─────────────────────────────────────────────
# ENCODE VARIANT
# ─────────────────────────────────────────────


def encode_variant(
    input_file: Path, name: str, scale: int, bitrate: str, keyinfo: Path
):
    outdir = TEMP_DIR / name
    outdir.mkdir(parents=True, exist_ok=True)

    playlist = outdir / "playlist.m3u8"
    segment = outdir / "segment_%03d.ts"

    cmd = [
        FFMPEG,
        "-y",
        "-i",
        str(input_file),
        "-vf",
        f"scale=-2:{scale}",
        "-c:v",
        "libx264",
        "-preset",
        "veryfast",
        "-b:v",
        bitrate,
        "-c:a",
        "aac",
        "-b:a",
        "128k",
        "-f",
        "hls",
        "-hls_time",
        str(SEGMENT_TIME),
        "-hls_list_size",
        "0",
        # Rotating IV per segment → แต่ละ segment ใช้ IV ต่างกัน ปลอดภัยกว่า IV=0 ตายตัว
        "-hls_flags",
        "periodic_rekey",
        "-hls_key_info_file",
        str(keyinfo),
        "-hls_segment_filename",
        str(segment),
        str(playlist),
    ]

    run_ffmpeg(cmd, desc=name)


# ─────────────────────────────────────────────
# MASTER PLAYLIST  (พร้อม RESOLUTION + CODECS)
# ─────────────────────────────────────────────


def create_master():
    """
    สร้าง master.m3u8 พร้อม RESOLUTION และ CODECS attributes
    → hls.js อ่านได้ถูกต้อง แสดง label resolution ใน quality selector
    """
    master = TEMP_DIR / "master.m3u8"

    lines = ["#EXTM3U"]
    entries = [
        (5000000, "1920x1080", "1080p/playlist.m3u8"),
        (2800000, "1280x720", "720p/playlist.m3u8"),
        (1200000, "854x480", "480p/playlist.m3u8"),
    ]
    for bw, res, uri in entries:
        lines.append(
            f"#EXT-X-STREAM-INF:BANDWIDTH={bw},RESOLUTION={res},"
            f'CODECS="avc1.42e01e,mp4a.40.2"'
        )
        lines.append(uri)

    master.write_text("\n".join(lines) + "\n", encoding="utf-8")


# ─────────────────────────────────────────────
# ZIP OUTPUT  (exclude keyinfo.txt)
# ─────────────────────────────────────────────


def zip_output(video_name: str):
    """
    Pack ทุกไฟล์ใน temp/ ยกเว้น keyinfo.txt
    keyinfo.txt มี absolute path ของเครื่อง → ไม่ควร ship ไปกับ zip
    """
    zip_path = OUTPUT_DIR / f"{video_name}.zip"

    with zipfile.ZipFile(zip_path, "w", zipfile.ZIP_DEFLATED) as z:
        for root, dirs, files in os.walk(TEMP_DIR):
            for f in files:
                if f == "keyinfo.txt":
                    continue  # exclude
                p = Path(root) / f
                z.write(p, p.relative_to(TEMP_DIR))

    print(f"\n✅ Created: {zip_path}")
    return zip_path


# ─────────────────────────────────────────────
# SUMMARY
# ─────────────────────────────────────────────


def print_summary(zip_path: Path, key_filename: str):
    print("\n" + "─" * 50)
    print("📦 Package ready:", zip_path.name)
    print(f"🔑 Key file name: {key_filename}")
    print(f"   API URL: {KEY_API_BASE}{key_filename}")
    print()
    print("📋 Upload steps:")
    print("   1. Admin → Lessons → Create Lesson")
    print("   2. Upload zip file")
    print("   3. Server จะย้าย .key ไป hls_keys/ อัตโนมัติ")
    print("─" * 50)


# ─────────────────────────────────────────────
# CLEAN TEMP
# ─────────────────────────────────────────────


def clean_temp():
    if TEMP_DIR.exists():
        shutil.rmtree(TEMP_DIR)
    TEMP_DIR.mkdir()


# ─────────────────────────────────────────────
# PROCESS VIDEO
# ─────────────────────────────────────────────


def process_video(mp4: Path):
    name = mp4.stem
    print(f"\n🎬 Processing: {name}")

    clean_temp()

    keyinfo, key_filename = create_key(TEMP_DIR)

    with tqdm(VARIANTS, desc="Encoding", unit="variant") as pbar:
        for v_name, v_scale, v_bitrate in pbar:
            pbar.set_postfix(variant=v_name)
            encode_variant(mp4, v_name, v_scale, v_bitrate, keyinfo)

    create_master()
    zip_path = zip_output(name)
    print_summary(zip_path, key_filename)


# ─────────────────────────────────────────────
# MAIN
# ─────────────────────────────────────────────


def main():
    global KEY_API_BASE, INPUT_DIR  # ต้องประกาศก่อนใช้

    parser = argparse.ArgumentParser(description="HLS Packager for Playroom LMS")
    parser.add_argument(
        "--input",
        type=Path,
        default=INPUT_DIR,
        help="Input directory containing .mp4 files (default: ./input)",
    )
    parser.add_argument(
        "--key-url",
        type=str,
        default=KEY_API_BASE,
        help=f"Key API base URL (default: {KEY_API_BASE})",
    )
    args = parser.parse_args()

    KEY_API_BASE = args.key_url.rstrip("/") + "/"
    INPUT_DIR = args.input

    INPUT_DIR.mkdir(exist_ok=True)
    OUTPUT_DIR.mkdir(exist_ok=True)

    mp4_files = sorted(INPUT_DIR.glob("*.mp4"))

    if not mp4_files:
        print("❌ No MP4 files found in:", INPUT_DIR)
        return

    print(f"Found {len(mp4_files)} video(s) to process")

    for mp4 in mp4_files:
        process_video(mp4)

    clean_temp()
    print("\n🎉 All done!")


if __name__ == "__main__":
    main()

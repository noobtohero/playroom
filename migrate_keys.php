<?php
/**
 * One-time migration script:
 * - Moves existing .key / .bin files from uploads/lessons/ to uploads/hls_keys/
 * - Rewrites EXT-X-KEY URIs in all .m3u8 files to point to /media/stream/key/{filename}
 *
 * Run once: php migrate_keys.php
 */

define('HLS_BASE', __DIR__ . '/writable/uploads/hls_keys/');
define('LESSONS_BASE', __DIR__ . '/writable/uploads/lessons/');
define('KEY_API_URL', 'http://playroom.test/media/stream/key/');

if (!is_dir(HLS_BASE)) {
    mkdir(HLS_BASE, 0777, true);
    echo "Created: " . HLS_BASE . "\n";
}

// Walk all files under lessons/
$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(LESSONS_BASE, RecursiveDirectoryIterator::SKIP_DOTS)
);

$movedKeys = []; // originalBasename → safeFilename

// PASS 1: Move key/bin files
foreach ($it as $file) {
    $ext = strtolower($file->getExtension());
    if (!in_array($ext, ['key', 'bin'])) continue;

    $origName = $file->getBasename();
    $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $origName);
    $dest     = HLS_BASE . $safeName;

    if (rename($file->getPathname(), $dest)) {
        $movedKeys[$origName] = $safeName;
        echo "Moved key: {$file->getPathname()} → {$dest}\n";
    } else {
        echo "FAILED to move: {$file->getPathname()}\n";
    }
}

if (empty($movedKeys)) {
    echo "No key files found. Done.\n";
    exit(0);
}

// PASS 2: Rewrite EXT-X-KEY URI in all .m3u8 files
$it2 = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(LESSONS_BASE, RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($it2 as $file) {
    if (strtolower($file->getExtension()) !== 'm3u8') continue;

    $content  = file_get_contents($file->getPathname());
    $modified = false;

    foreach ($movedKeys as $original => $safe) {
        $pattern = '/(#EXT-X-KEY:[^\n]*URI=")([^"]*\/|)' . preg_quote($original, '/') . '(")/';
        $replace = '$1' . KEY_API_URL . $safe . '$3';
        $new     = preg_replace($pattern, $replace, $content);

        if ($new !== $content) {
            $content  = $new;
            $modified = true;
        }
    }

    if ($modified) {
        file_put_contents($file->getPathname(), $content);
        echo "Rewritten: {$file->getPathname()}\n";
    }
}

echo "\nDone! Run this script again to check (should say 'No key files found').\n";

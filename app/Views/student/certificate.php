<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Completion - <?= esc($course['title']) ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Great+Vibes&family=Inter:wght@400;500;600&family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-gold: #c5a059;
            --dark-gold: #a67c37;
            --light-gold: #f1e4c0;
            --slate-base: #1e293b;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Certificate Container */
        .certificate-container {
            width: 1122px; /* A4 Landscape width in pixels at 96dpi approx */
            height: 793px;
            background: #fff;
            padding: 50px;
            position: relative;
            box-shadow: 0 40px 100px rgba(0,0,0,0.1);
            border: 2px solid var(--primary-gold);
            overflow: hidden;
            box-sizing: border-box;
            background-image: 
                radial-gradient(circle at 50% 50%, rgba(255,255,255,1) 0%, rgba(248,250,252,1) 100%),
                url('https://www.transparenttextures.com/patterns/cream-paper.png');
        }

        /* Border Ornament */
        .certificate-border {
            position: absolute;
            top: 20px;
            bottom: 20px;
            left: 20px;
            right: 20px;
            border: 8px double var(--primary-gold);
            pointer-events: none;
        }

        .corner {
            position: absolute;
            width: 100px;
            height: 100px;
            border: 15px solid var(--primary-gold);
            z-index: 5;
        }
        .top-left { top: -10px; left: -10px; border-right: none; border-bottom: none; border-radius: 15px 0 0 0; }
        .top-right { top: -10px; right: -10px; border-left: none; border-bottom: none; border-radius: 0 15px 0 0; }
        .bottom-left { bottom: -10px; left: -10px; border-right: none; border-top: none; border-radius: 0 0 0 15px; }
        .bottom-right { bottom: -10px; right: -10px; border-left: none; border-top: none; border-radius: 0 0 15px 0; }

        /* Content Styling */
        .content {
            text-align: center;
            position: relative;
            z-index: 10;
        }

        .logo {
            font-family: 'Cinzel', serif;
            font-size: 3rem;
            color: var(--slate-base);
            margin-bottom: 2rem;
            letter-spacing: 5px;
            font-weight: 700;
        }
        .logo span { color: var(--primary-gold); }

        .title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 8px;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
            font-weight: 400;
        }

        .award-to {
            font-family: 'Great Vibes', cursive;
            font-size: 4rem;
            color: var(--primary-gold);
            margin-bottom: 1rem;
        }

        .user-name {
            font-family: 'Cinzel', serif;
            font-size: 3.5rem;
            color: var(--slate-base);
            margin-bottom: 1.5rem;
            font-weight: 700;
            border-bottom: 2px solid var(--primary-gold);
            display: inline-block;
            padding-bottom: 10px;
            min-width: 600px;
        }

        .congrats {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.1rem;
            color: #64748b;
            margin-top: 2rem;
            line-height: 1.8;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .course-title {
            font-weight: 700;
            color: var(--slate-base);
            font-size: 1.4rem;
        }

        .footer {
            margin-top: 4rem;
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            padding: 0 50px;
        }

        .sig-block {
            text-align: center;
            border-top: 1px solid #cbd5e1;
            padding-top: 10px;
            min-width: 200px;
        }
        .sig-img {
            font-family: 'Great Vibes', cursive;
            font-size: 2rem;
            color: var(--slate-base);
            margin-bottom: 5px;
        }
        .sig-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #64748b;
        }

        .seal {
            width: 120px;
            height: 120px;
            background: var(--primary-gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-family: 'Cinzel', serif;
            font-size: 0.9rem;
            text-align: center;
            box-shadow: 0 10px 20px rgba(197, 160, 89, 0.3);
            border: 4px double var(--light-gold);
            transform: rotate(-15deg);
        }

        /* Controls */
        .controls {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        .btn-action {
            padding: 12px 24px;
            border-radius: 50px;
            border: none;
            background: var(--slate-base);
            color: white;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            background: #0f172a;
        }
        .btn-print {
            background: var(--primary-gold);
        }
        .btn-print:hover {
            background: var(--dark-gold);
        }

        @media print {
            body { background: white; }
            .controls { display: none; }
            .certificate-container { 
                box-shadow: none; 
                margin: 0; 
                border: none;
                width: 100%;
                height: 100vh;
            }
            @page {
                size: A4 landscape;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="controls">
        <a href="<?= base_url('student/course/' . $course['id']) ?>" class="btn-action">
            <i class="bi bi-arrow-left"></i> Back to Course
        </a>
        <button onclick="window.print()" class="btn-action btn-print">
            <i class="bi bi-printer"></i> Print Certificate
        </button>
    </div>

    <div class="certificate-container">
        <div class="certificate-border">
            <div class="corner top-left"></div>
            <div class="corner top-right"></div>
            <div class="corner bottom-left"></div>
            <div class="corner bottom-right"></div>
        </div>

        <div class="content">
            <div class="logo">PLAY<span>ROOM</span></div>
            <div class="title">Certificate of Completion</div>
            
            <p style="color: #64748b; letter-spacing: 2px;">This is to certify that</p>
            
            <div class="user-name"><?= esc($user['name']) ?></div>
            
            <div class="congrats">
                has successfully completed the online course<br>
                <span class="course-title">"<?= esc($course['title']) ?>"</span><br>
                demonstrating proficiency and dedication in the pursuit of knowledge.
            </div>

            <div class="footer">
                <div class="sig-block">
                    <div class="sig-img">Playroom Academy</div>
                    <div class="sig-label">Official Provider</div>
                </div>

                <div class="seal">
                    CERTIFIED<br>GRADUATE<br>2026
                </div>

                <div class="sig-block">
                    <div class="sig-img"><?= $completed_at ?></div>
                    <div class="sig-label">Date of Issue</div>
                </div>
            </div>
            
            <div style="margin-top: 3rem; font-size: 0.7rem; color: #cbd5e1; letter-spacing: 1px;">
                Verification ID: CERT-<?= strtoupper(substr(md5($user['id'] . $course['id']), 0, 12)) ?>
            </div>
        </div>
    </div>

</body>
</html>

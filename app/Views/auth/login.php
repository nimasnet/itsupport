<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Support - System Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00d2ff;
            --secondary: #3a7bd5;
            --dark: #0f172a;
            --darker: #020617;
            --text: #f8fafc;
            --glass-bg: rgba(15, 23, 42, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--darker);
            color: var(--text);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* Animated Background Elements */
        .bg-grid {
            position: absolute;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background-image: 
                linear-gradient(rgba(0, 210, 255, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 210, 255, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: 0;
            perspective: 1000px;
            animation: gridMove 20s linear infinite;
        }

        @keyframes gridMove {
            0% { transform: translateY(0); }
            100% { transform: translateY(40px); }
        }

        .glow-circle {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            opacity: 0.5;
            animation: pulse 8s alternate infinite;
        }

        .glow-1 {
            width: 400px; height: 400px;
            background: var(--primary);
            top: -100px; left: -100px;
        }

        .glow-2 {
            width: 500px; height: 500px;
            background: var(--secondary);
            bottom: -150px; right: -150px;
            animation-delay: 2s;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.3; }
            100% { transform: scale(1.2); opacity: 0.6; }
        }

        /* Glassmorphism Card */
        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            z-index: 10;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transform: translateY(20px);
            opacity: 0;
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes slideUp {
            to { transform: translateY(0); opacity: 1; }
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            box-shadow: 0 10px 20px rgba(0, 210, 255, 0.3);
        }

        .login-title {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }

        .login-subtitle {
            font-size: 14px;
            color: #94a3b8;
            font-weight: 300;
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group label {
            display: block;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .input-group input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: white;
            font-size: 15px;
            transition: all 0.3s ease;
            outline: none;
        }

        .input-group input:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0, 210, 255, 0.1);
        }

        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 10px 20px rgba(0, 210, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(0, 210, 255, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login::after {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 50%; height: 100%;
            background: linear-gradient(to right, transparent, rgba(255,255,255,0.3), transparent);
            transform: skewX(-20deg);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { left: -100%; }
            20% { left: 200%; }
            100% { left: 200%; }
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            text-align: center;
            margin-bottom: 20px;
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }

        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }

        .footer-text {
            text-align: center;
            margin-top: 25px;
            font-size: 12px;
            color: rgba(255,255,255,0.4);
        }

        /* =============================================
           LOGIN TRANSITION OVERLAY
           ============================================= */
        #login-overlay {
            position: fixed;
            inset: 0;
            background: #020617;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s ease;
        }

        #login-overlay.active {
            opacity: 1;
            pointer-events: all;
        }

        /* Scan line effect */
        #login-overlay::before {
            content: '';
            position: absolute;
            inset: 0;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 3px,
                rgba(0, 210, 255, 0.015) 3px,
                rgba(0, 210, 255, 0.015) 4px
            );
            pointer-events: none;
            z-index: 1;
        }

        /* Moving scan beam */
        .scan-beam {
            position: absolute;
            top: -4px;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg,
                transparent 0%,
                rgba(0, 210, 255, 0.3) 30%,
                rgba(0, 210, 255, 0.9) 50%,
                rgba(0, 210, 255, 0.3) 70%,
                transparent 100%
            );
            box-shadow: 0 0 20px rgba(0, 210, 255, 0.8), 0 0 60px rgba(0, 210, 255, 0.3);
            animation: scanDown 2.5s linear infinite;
            z-index: 2;
        }

        @keyframes scanDown {
            0%   { top: -4px; }
            100% { top: 100%; }
        }

        /* Grid background on overlay */
        .overlay-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(0, 210, 255, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 210, 255, 0.04) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: 0;
        }

        /* Central content container */
        .overlay-content {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 28px;
        }

        /* Hexagon spinner */
        .hex-spinner-wrap {
            position: relative;
            width: 120px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hex-ring {
            position: absolute;
            border-radius: 50%;
            border: 2px solid transparent;
        }

        .hex-ring-1 {
            width: 120px; height: 120px;
            border-top-color: var(--primary);
            border-right-color: var(--primary);
            animation: spinCW 1.2s linear infinite;
        }

        .hex-ring-2 {
            width: 90px; height: 90px;
            border-bottom-color: var(--secondary);
            border-left-color: var(--secondary);
            animation: spinCCW 0.9s linear infinite;
        }

        .hex-ring-3 {
            width: 60px; height: 60px;
            border-top-color: rgba(0, 210, 255, 0.5);
            border-right-color: rgba(0, 210, 255, 0.5);
            animation: spinCW 0.6s linear infinite;
        }

        @keyframes spinCW  { to { transform: rotate(360deg);  } }
        @keyframes spinCCW { to { transform: rotate(-360deg); } }

        /* Center icon in spinner */
        .hex-center-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px rgba(0, 210, 255, 0.6);
            animation: iconPulse 1.5s ease-in-out infinite alternate;
        }

        @keyframes iconPulse {
            from { box-shadow: 0 0 15px rgba(0, 210, 255, 0.5); transform: scale(0.95); }
            to   { box-shadow: 0 0 35px rgba(0, 210, 255, 0.9); transform: scale(1.05); }
        }

        /* Text area */
        .overlay-title {
            font-size: 20px;
            font-weight: 700;
            color: #f8fafc;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-align: center;
        }

        .overlay-status {
            font-size: 13px;
            color: var(--primary);
            letter-spacing: 3px;
            text-transform: uppercase;
            text-align: center;
            min-height: 20px;
        }

        /* Progress bar */
        .progress-track {
            width: 280px;
            height: 4px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 99px;
            overflow: hidden;
            border: 1px solid rgba(0, 210, 255, 0.1);
        }

        .progress-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--secondary), var(--primary));
            border-radius: 99px;
            box-shadow: 0 0 10px rgba(0, 210, 255, 0.6);
            transition: width 0.3s ease;
        }

        /* Corner decorations */
        .corner-deco {
            position: absolute;
            width: 20px; height: 20px;
            z-index: 10;
        }
        .corner-deco::before,
        .corner-deco::after {
            content: '';
            position: absolute;
            background: var(--primary);
        }
        .corner-deco::before { width: 100%; height: 2px; top: 0; left: 0; }
        .corner-deco::after  { width: 2px; height: 100%; top: 0; left: 0; }

        .corner-tl { top: 20px; left: 20px; }
        .corner-tr { top: 20px; right: 20px; transform: scaleX(-1); }
        .corner-bl { bottom: 20px; left: 20px; transform: scaleY(-1); }
        .corner-br { bottom: 20px; right: 20px; transform: scale(-1); }

        /* Particle dots */
        .particles {
            position: absolute;
            inset: 0;
            overflow: hidden;
            z-index: 1;
        }

        .particle {
            position: absolute;
            width: 2px; height: 2px;
            border-radius: 50%;
            background: var(--primary);
            opacity: 0;
            animation: floatUp var(--dur, 4s) var(--delay, 0s) linear infinite;
        }

        @keyframes floatUp {
            0%   { opacity: 0; transform: translateY(0) scale(0); }
            10%  { opacity: 0.8; }
            90%  { opacity: 0.4; }
            100% { opacity: 0; transform: translateY(-100vh) scale(1.5); }
        }
    </style>
</head>
<body>

    <!-- =============================================
         LOGIN TRANSITION OVERLAY
         ============================================= -->
    <div id="login-overlay">
        <div class="overlay-grid"></div>
        <div class="scan-beam"></div>
        <div class="particles" id="particles"></div>

        <!-- Corner decorations -->
        <div class="corner-deco corner-tl"></div>
        <div class="corner-deco corner-tr"></div>
        <div class="corner-deco corner-bl"></div>
        <div class="corner-deco corner-br"></div>

        <div class="overlay-content">
            <!-- Spinner -->
            <div class="hex-spinner-wrap">
                <div class="hex-ring hex-ring-1"></div>
                <div class="hex-ring hex-ring-2"></div>
                <div class="hex-ring hex-ring-3"></div>
                <div class="hex-center-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="white" style="width:22px;height:22px;">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                    </svg>
                </div>
            </div>

            <!-- Text -->
            <div>
                <div class="overlay-title">IT Support Portal</div>
                <div class="overlay-status" id="overlay-status">Memverifikasi Kredensial...</div>
            </div>

            <!-- Progress -->
            <div class="progress-track">
                <div class="progress-fill" id="progress-fill"></div>
            </div>
        </div>
    </div>

    <!-- ======================== -->

    <div class="bg-grid"></div>
    <div class="glow-circle glow-1"></div>
    <div class="glow-circle glow-2"></div>

    <div class="login-card">
        <div class="logo-container">
            <div class="logo-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 36px; height: 36px; color: white;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                </svg>
            </div>
            <h1 class="login-title">IT Support Portal</h1>
            <p class="login-subtitle">Silakan masukkan kredensial Anda</p>
        </div>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="error-msg"><?php echo session()->getFlashdata('error'); ?></div>
        <?php endif; ?>

        <form id="login-form" action="<?= base_url('login/process') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required autocomplete="off">
            </div>
            
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required>
            </div>
            
            <button type="submit" id="btn-login" class="btn-login">Masuk ke Sistem</button>
        </form>

        <div class="footer-text">
            &copy; <?= date("Y"); ?> IT Support Database System
        </div>
    </div>

    <script>
    (function () {
        /* ── Generate floating particles ── */
        const container = document.getElementById('particles');
        for (let i = 0; i < 40; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            p.style.cssText = `
                left: ${Math.random() * 100}%;
                bottom: ${Math.random() * 20}%;
                --dur: ${3 + Math.random() * 5}s;
                --delay: ${Math.random() * 6}s;
                width: ${1 + Math.random() * 3}px;
                height: ${1 + Math.random() * 3}px;
                opacity: 0;
            `;
            container.appendChild(p);
        }

        /* ── Status messages sequence ── */
        const statuses = [
            { text: 'Memverifikasi Kredensial...', pct: 20 },
            { text: 'Menghubungkan ke Server...', pct: 45 },
            { text: 'Memuat Konfigurasi Sistem...', pct: 65 },
            { text: 'Menyiapkan Dashboard...',      pct: 85 },
            { text: 'Akses Diberikan ✓',             pct: 100 },
        ];

        const overlay    = document.getElementById('login-overlay');
        const statusEl   = document.getElementById('overlay-status');
        const progressEl = document.getElementById('progress-fill');
        const form       = document.getElementById('login-form');
        const btn        = document.getElementById('btn-login');

        let formSubmitted = false;

        form.addEventListener('submit', function (e) {
            // If native validation fails, let browser handle it
            if (!form.checkValidity()) return;

            e.preventDefault(); // intercept

            if (formSubmitted) return;
            formSubmitted = true;

            // Disable button to prevent double-click
            btn.disabled = true;
            btn.textContent = 'Memproses...';

            // Show overlay with fade-in
            overlay.classList.add('active');

            // Animate through status messages
            let idx = 0;

            function nextStatus() {
                if (idx >= statuses.length) {
                    // Submit the form for real after animation
                    form.submit();
                    return;
                }
                const s = statuses[idx++];
                statusEl.style.opacity = '0';
                setTimeout(() => {
                    statusEl.textContent = s.text;
                    progressEl.style.width = s.pct + '%';
                    statusEl.style.transition = 'opacity 0.3s ease';
                    statusEl.style.opacity = '1';
                }, 200);

                const delay = (idx < statuses.length) ? 500 : 400;
                setTimeout(nextStatus, delay);
            }

            // Small pause so the overlay appears before animation starts
            setTimeout(nextStatus, 300);
        });
    })();
    </script>

</body>
</html>

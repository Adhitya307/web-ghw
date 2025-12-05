<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Monitoring PLTA Saguling</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0052D4;
            --primary-light: #0070ff;
            --dark: #101820;
            --light: #f8f9fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
            position: relative;
        }

        /* Particle Background */
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
        }

        /* Animated Grid Background */
        .grid-background {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            opacity: 0.15;
        }

        .grid-background::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(0, 82, 212, 0.3) 2px, transparent 2px),
                radial-gradient(circle at 75% 75%, rgba(0, 112, 255, 0.3) 2px, transparent 2px);
            background-size: 60px 60px;
            animation: moveGrid 40s linear infinite;
        }

        @keyframes moveGrid {
            0% {
                transform: translate(0, 0);
            }
            100% {
                transform: translate(-30px, -30px);
            }
        }

        /* Floating Dots */
        .floating-dots {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
            pointer-events: none;
        }

        .dot {
            position: absolute;
            background: rgba(0, 112, 255, 0.2);
            border-radius: 50%;
            animation: float 20s infinite ease-in-out;
        }

        .dot:nth-child(1) {
            width: 10px;
            height: 10px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .dot:nth-child(2) {
            width: 8px;
            height: 8px;
            top: 80%;
            left: 20%;
            animation-delay: -5s;
            background: rgba(0, 82, 212, 0.3);
        }

        .dot:nth-child(3) {
            width: 12px;
            height: 12px;
            top: 30%;
            left: 85%;
            animation-delay: -10s;
        }

        .dot:nth-child(4) {
            width: 6px;
            height: 6px;
            top: 70%;
            left: 75%;
            animation-delay: -15s;
            background: rgba(0, 82, 212, 0.3);
        }

        .dot:nth-child(5) {
            width: 9px;
            height: 9px;
            top: 50%;
            left: 15%;
            animation-delay: -7s;
        }

        .dot:nth-child(6) {
            width: 7px;
            height: 7px;
            top: 20%;
            left: 60%;
            animation-delay: -12s;
            background: rgba(0, 112, 255, 0.25);
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) scale(1);
                opacity: 0.5;
            }
            25% {
                transform: translate(30px, -20px) scale(1.1);
                opacity: 0.8;
            }
            50% {
                transform: translate(-20px, 30px) scale(0.9);
                opacity: 0.6;
            }
            75% {
                transform: translate(-30px, -30px) scale(1.05);
                opacity: 0.7;
            }
        }

        /* Connect Lines Animation */
        .connect-lines {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
            pointer-events: none;
        }

        .line {
            position: absolute;
            background: linear-gradient(90deg, transparent, rgba(0, 112, 255, 0.1), transparent);
            height: 1px;
            animation: lineFlow 10s linear infinite;
        }

        @keyframes lineFlow {
            0% {
                transform: translateX(-100%);
                opacity: 0;
            }
            10% {
                opacity: 0.5;
            }
            90% {
                opacity: 0.5;
            }
            100% {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        /* Login Container */
        .login-container {
            width: 100%;
            max-width: 400px;
            z-index: 10;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Login Card */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 40px 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
        }

        .login-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 50% 0%, rgba(0, 82, 212, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Header */
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 24px;
            box-shadow: 0 8px 20px rgba(0, 82, 212, 0.3);
            animation: pulse 2s infinite;
            position: relative;
            overflow: hidden;
        }

        .logo::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, rgba(255,255,255,0.3) 0%, transparent 70%);
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .login-header h1 {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .login-header p {
            font-size: 13px;
            color: #666;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 500;
            color: #555;
            margin-bottom: 8px;
            display: block;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 16px;
            z-index: 1;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            transition: all 0.3s;
            position: relative;
            z-index: 2;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 82, 212, 0.1);
        }

        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            padding: 5px;
            font-size: 16px;
            z-index: 2;
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        /* Remember Me */
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 13px;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-check-input {
            width: 16px;
            height: 16px;
            border: 2px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .form-check-label {
            color: #555;
            cursor: pointer;
        }

        .forgot-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        /* Login Button */
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(0, 82, 212, 0.3);
            position: relative;
            overflow: hidden;
            z-index: 2;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 82, 212, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Back Link */
        .back-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .back-link a {
            color: #666;
            text-decoration: none;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: color 0.3s;
        }

        .back-link a:hover {
            color: var(--primary);
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.7);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        /* Alerts */
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            border: none;
            animation: fadeIn 0.3s;
            position: relative;
            z-index: 2;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .alert-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
            }
            
            .login-header h1 {
                font-size: 16px;
            }
        }

        /* Loading State */
        .btn-login.loading {
            opacity: 0.8;
            pointer-events: none;
        }

        .btn-login.loading::after {
            content: '';
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid white;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Subtle Glow Effect */
        .glow-effect {
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(0, 112, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(40px);
            z-index: 0;
            animation: glowMove 15s infinite ease-in-out;
        }

        @keyframes glowMove {
            0%, 100% { transform: translate(0, 0); }
            33% { transform: translate(50px, -30px); }
            66% { transform: translate(-30px, 50px); }
        }
    </style>
</head>
<body>
    <!-- Background Elements -->
    <div class="grid-background"></div>
    
    <!-- Floating Dots -->
    <div class="floating-dots">
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>

    <!-- Connect Lines -->
    <div class="connect-lines">
        <div class="line" style="top: 30%; width: 50%; animation-delay: 0s;"></div>
        <div class="line" style="top: 60%; width: 70%; animation-delay: -2s;"></div>
        <div class="line" style="top: 80%; width: 40%; animation-delay: -4s;"></div>
    </div>

    <!-- Subtle Glow -->
    <div class="glow-effect" style="top: 20%; left: 20%;"></div>
    <div class="glow-effect" style="bottom: 20%; right: 20%; animation-delay: -5s;"></div>

    <!-- Login Container -->
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <div class="logo">
                    <i class="bi bi-lightning-charge"></i>
                </div>
                <h1>MONITORING PLTA SAGULING</h1>
                <p>Login untuk melanjutkan</p>
            </div>

            <!-- Alerts -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" style="filter: brightness(0) invert(1);" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" style="filter: brightness(0) invert(1);" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="<?= base_url('/auth/process-login') ?>" method="post" id="loginForm">
                <?= csrf_field() ?>
                
                <!-- Username -->
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <div class="input-icon">
                            <i class="bi bi-person"></i>
                        </div>
                        <input type="text" 
                               class="form-control" 
                               name="username" 
                               value="<?= old('username') ?>" 
                               placeholder="Masukkan username" 
                               required
                               autofocus>
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <div class="input-icon">
                            <i class="bi bi-lock"></i>
                        </div>
                        <input type="password" 
                               class="form-control" 
                               name="password" 
                               id="password" 
                               placeholder="Masukkan password" 
                               required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember & Forgot -->
                <div class="remember-forgot">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>
                    <a href="#" class="forgot-link">Lupa password?</a>
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn-login" id="loginButton">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>MASUK</span>
                </button>

                <!-- Back Link -->
                <div class="back-link">
                    <a href="<?= base_url('/') ?>">
                        <i class="bi bi-arrow-left"></i>
                        Kembali ke Beranda
                    </a>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="login-footer">
            &copy; <?= date('Y') ?> PT Indonesia Power Saguling POMU
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle Password Visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
            
            // Add feedback animation
            this.style.transform = 'translateY(-50%) scale(1.1)';
            setTimeout(() => {
                this.style.transform = 'translateY(-50%) scale(1)';
            }, 200);
        });

        // Form Submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const button = document.getElementById('loginButton');
            const icon = button.querySelector('i');
            const text = button.querySelector('span');
            
            // Show loading state
            button.classList.add('loading');
            text.textContent = 'MEMPROSES...';
            
            // Simulate network delay for UX
            setTimeout(() => {
                // In production, remove this timeout
            }, 1000);
        });

        // Auto-focus username field
        document.addEventListener('DOMContentLoaded', function() {
            const usernameField = document.querySelector('input[name="username"]');
            usernameField.focus();
            
            // Add ripple effect to logo on load
            const logo = document.querySelector('.logo');
            setTimeout(() => {
                logo.style.animation = 'pulse 2s infinite, logoRipple 1s ease-out';
            }, 500);
            
            // Auto-hide alerts after 4 seconds
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 4000);
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Enter to submit
            if (e.key === 'Enter' && !e.shiftKey && !e.ctrlKey) {
                e.preventDefault();
                document.getElementById('loginForm').requestSubmit();
            }
            
            // Esc to clear
            if (e.key === 'Escape') {
                document.getElementById('loginForm').reset();
            }
            
            // Space to toggle password when focused
            if (e.key === ' ' && document.activeElement === document.getElementById('password')) {
                e.preventDefault();
                document.getElementById('togglePassword').click();
            }
        });

        // Add subtle hover effect to card
        const loginCard = document.querySelector('.login-card');
        loginCard.addEventListener('mouseenter', () => {
            loginCard.style.transform = 'translateY(-2px)';
            loginCard.style.boxShadow = '0 25px 50px rgba(0, 0, 0, 0.5)';
        });

        loginCard.addEventListener('mouseleave', () => {
            loginCard.style.transform = 'translateY(0)';
            loginCard.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.4)';
        });

        // Animate form inputs on focus
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.style.transform = 'scale(1.02)';
                this.style.boxShadow = '0 0 0 4px rgba(0, 82, 212, 0.1)';
            });
            
            input.addEventListener('blur', function() {
                this.style.transform = 'scale(1)';
                this.style.boxShadow = 'none';
            });
        });

        // Create more floating dots dynamically
        function createFloatingDots() {
            const container = document.querySelector('.floating-dots');
            const dotCount = 12;
            
            for (let i = 7; i <= dotCount; i++) {
                const dot = document.createElement('div');
                dot.className = 'dot';
                
                // Random properties
                const size = Math.random() * 8 + 4;
                const top = Math.random() * 100;
                const left = Math.random() * 100;
                const delay = Math.random() * 20 - 20;
                const duration = Math.random() * 10 + 15;
                const opacity = Math.random() * 0.3 + 0.2;
                
                dot.style.cssText = `
                    width: ${size}px;
                    height: ${size}px;
                    top: ${top}%;
                    left: ${left}%;
                    animation-delay: ${delay}s;
                    animation-duration: ${duration}s;
                    opacity: ${opacity};
                    background: ${i % 2 === 0 ? 'rgba(0, 82, 212, 0.3)' : 'rgba(0, 112, 255, 0.2)'};
                `;
                
                container.appendChild(dot);
            }
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', createFloatingDots);

        // Create more connect lines
        function createConnectLines() {
            const container = document.querySelector('.connect-lines');
            const lineCount = 6;
            
            for (let i = 4; i <= lineCount; i++) {
                const line = document.createElement('div');
                line.className = 'line';
                
                // Random properties
                const top = Math.random() * 100;
                const width = Math.random() * 40 + 30;
                const delay = Math.random() * 10 - 10;
                
                line.style.cssText = `
                    top: ${top}%;
                    width: ${width}%;
                    animation-delay: ${delay}s;
                    background: linear-gradient(90deg, 
                        transparent, 
                        rgba(0, ${Math.random() * 100 + 100}, 255, ${Math.random() * 0.1 + 0.05}), 
                        transparent);
                `;
                
                container.appendChild(line);
            }
        }

        // Initialize lines
        document.addEventListener('DOMContentLoaded', createConnectLines);
    </script>
</body>
</html>
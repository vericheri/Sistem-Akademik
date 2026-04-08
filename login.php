<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        $db = new db();
        $conn = $db->getconn();
        
        $stmt = $conn->prepare("SELECT id, username, password, nama_lengkap, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            if ($password == $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama'] = $user['nama_lengkap'];
                $_SESSION['role'] = $user['role'];
                
                header("Location: index.php");
                exit();
            } else {
                $error = 'Password salah!';
            }
        } else {
            $error = 'Username tidak ditemukan!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Login - Sistem Akademik Mini</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1a3a5c;
            --primary-dark: #0f2a44;
            --secondary: #c9a03d;
            --secondary-dark: #b08a2c;
            --danger: #c62828;
            --success: #2e7d32;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            
            --gradient-primary: linear-gradient(135deg, #1a3a5c 0%, #2c4a6e 100%);
            --gradient-gold: linear-gradient(135deg, #c9a03d 0%, #d4b05c 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1a3a5c 0%, #0f2a44 100%);
            min-height: 100vh;
            display: flex;
            position: relative;
            overflow-y: auto;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(201, 160, 61, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(201, 160, 61, 0.1) 0%, transparent 50%);
            animation: pulse 10s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }

        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            background: rgba(201, 160, 61, 0.15);
            border-radius: 50%;
            animation: float linear infinite;
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }

        .login-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        .login-hero {
            flex: 1;
            background: linear-gradient(135deg, rgba(26, 58, 92, 0.95), rgba(15, 42, 68, 0.95));
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .login-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(201, 160, 61, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveGrid 20s linear infinite;
            opacity: 0.3;
        }

        @keyframes moveGrid {
            0% {
                transform: translate(0, 0);
            }
            100% {
                transform: translate(50px, 50px);
            }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            animation: slideInLeft 0.8s ease-out;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .hero-logo {
            font-size: 64px;
            margin-bottom: 30px;
            display: inline-block;
            animation: bounce 2s ease-in-out infinite;
            color: var(--secondary);
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .hero-title {
            font-size: 42px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
            letter-spacing: -0.02em;
        }

        .hero-subtitle {
            font-size: 16px;
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 40px;
        }

        .hero-features {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 15px;
            animation: slideInLeft 0.8s ease-out;
            animation-fill-mode: both;
        }

        .feature-item:nth-child(1) { animation-delay: 0.1s; }
        .feature-item:nth-child(2) { animation-delay: 0.2s; }
        .feature-item:nth-child(3) { animation-delay: 0.3s; }
        .feature-item:nth-child(4) { animation-delay: 0.4s; }

        .feature-icon {
            width: 32px;
            height: 32px;
            background: rgba(201, 160, 61, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: var(--secondary);
        }

        .login-form-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            padding: 40px;
            position: relative;
            overflow-y: auto;
        }

        .login-card {
            max-width: 440px;
            width: 100%;
            animation: slideInRight 0.8s ease-out;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .login-header p {
            color: var(--gray-500);
            font-size: 14px;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            animation: shake 0.5s ease-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .alert-error {
            background: #fef2f2;
            border-left: 4px solid var(--danger);
            color: #b71c1c;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--gray-700);
            font-size: 13px;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: var(--gray-400);
            font-size: 16px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 14px 12px 44px;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: inherit;
            background: white;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(26, 58, 92, 0.1);
        }

        .input-group input:hover {
            border-color: var(--gray-300);
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            cursor: pointer;
            color: var(--gray-400);
            transition: color 0.3s ease;
        }

        .toggle-password:hover {
            color: var(--gray-600);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 13px;
            color: var(--gray-600);
        }

        .checkbox-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: var(--primary);
        }

        .forgot-link {
            font-size: 13px;
            color: var(--primary);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-login:hover::before {
            width: 100%;
            height: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(26, 58, 92, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .demo-accounts {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--gray-200);
        }

        .demo-title {
            font-size: 12px;
            font-weight: 600;
            color: var(--gray-500);
            text-align: center;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .demo-grid {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .demo-card {
            flex: 1;
            background: var(--gray-50);
            border-radius: 10px;
            padding: 12px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid var(--gray-200);
        }

        .demo-card:hover {
            background: white;
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .demo-role {
            font-size: 20px;
            margin-bottom: 6px;
        }

        .demo-username {
            font-size: 11px;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 4px;
        }

        .demo-password {
            font-size: 10px;
            color: var(--gray-500);
            font-family: monospace;
        }

        .login-footer {
            margin-top: 32px;
            text-align: center;
            font-size: 11px;
            color: var(--gray-400);
            padding-bottom: 10px;
        }

        @media (max-width: 1024px) {
            .login-hero {
                padding: 40px;
            }
            
            .hero-title {
                font-size: 32px;
            }
        }

        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
            }
            
            .login-hero {
                padding: 40px 30px;
                min-height: auto;
            }
            
            .hero-title {
                font-size: 28px;
            }
            
            .hero-subtitle {
                font-size: 14px;
                margin-bottom: 25px;
            }
            
            .hero-features {
                gap: 10px;
            }
            
            .feature-item {
                font-size: 13px;
            }
            
            .login-form-container {
                padding: 30px;
                min-height: auto;
            }
            
            .login-card {
                max-width: 100%;
            }
        }

        @media (max-width: 480px) {
            .login-hero {
                padding: 30px 20px;
            }
            
            .hero-title {
                font-size: 24px;
            }
            
            .hero-logo {
                font-size: 40px;
                margin-bottom: 20px;
            }
            
            .demo-grid {
                flex-direction: column;
            }
            
            .login-form-container {
                padding: 20px;
            }
        }
        
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--gray-200);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    </style>
</head>
<body>

<div class="particles" id="particles"></div>

<div class="login-wrapper">
    <div class="login-hero">
        <div class="hero-content">
            <div class="hero-logo">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h1 class="hero-title">
                Sistem Akademik<br>
                Universitas Iga Bakar
            </h1>
            <p class="hero-subtitle">
                Platform manajemen akademik modern<br>
                untuk kemudahan pengelolaan data mahasiswa.
            </p>
            <div class="hero-features">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <span>Manajemen Mahasiswa</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <span>Manajemen Mata Kuliah</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <span>Input & Hitung IPK</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-print"></i>
                    </div>
                    <span>Cetak KHS</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="login-form-container">
        <div class="login-card">
            <div class="login-header">
                <h2>Selamat Datang</h2>
                <p>Silakan masuk ke akun Anda</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= $error ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="loginForm">
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-group">
                        <span class="input-icon">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" name="username" placeholder="Masukkan username" required autofocus>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-group">
                        <span class="input-icon">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" placeholder="Masukkan password" required>
                        <span class="toggle-password" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>
                
                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Ingat saya</span>
                    </label>
                    <a href="#" class="forgot-link">Lupa password?</a>
                </div>
                
                <button type="submit" class="btn-login" id="loginBtn">
                    <i class="fas fa-arrow-right-to-bracket"></i> Masuk
                </button>
            </form>
            
            <div class="demo-accounts">
                <div class="demo-title">
                    <i class="fas fa-flask"></i> Demo Akun
                </div>
                <div class="demo-grid">
                    <div class="demo-card" onclick="fillCredentials('admin', 'admin123')">
                        <div class="demo-role"></div>
                        <div class="demo-username">Administrator</div>
                        <div class="demo-password">admin / admin123</div>
                    </div>
                    <div class="demo-card" onclick="fillCredentials('dosen', 'dosen123')">
                        <div class="demo-role"></div>
                        <div class="demo-username">Dosen</div>
                        <div class="demo-password">dosen / dosen123</div>
                    </div>
                </div>
            </div>
            
            <div class="login-footer">
                <p>&copy; 2025 Sistem Akademik Universitas Iga Bakar. All rights reserved.</p>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
    
    function fillCredentials(username, password) {
        document.querySelector('input[name="username"]').value = username;
        document.querySelector('input[name="password"]').value = password;
        
        const cards = document.querySelectorAll('.demo-card');
        cards.forEach(card => {
            card.style.transform = 'scale(0.98)';
            setTimeout(() => {
                card.style.transform = '';
            }, 200);
        });
    }
    
    if (localStorage.getItem('rememberedUsername')) {
        document.querySelector('input[name="username"]').value = localStorage.getItem('rememberedUsername');
        document.getElementById('remember').checked = true;
    }
    
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        if (document.getElementById('remember').checked) {
            const username = document.querySelector('input[name="username"]').value;
            localStorage.setItem('rememberedUsername', username);
        } else {
            localStorage.removeItem('rememberedUsername');
        }
    });
    
    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('loginBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        btn.classList.add('loading');
        btn.disabled = true;
    });
    
    function createParticles() {
        const particlesContainer = document.getElementById('particles');
        const particleCount = 50;
        
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.classList.add('particle');
            const size = Math.random() * 5 + 2;
            particle.style.width = size + 'px';
            particle.style.height = size + 'px';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDuration = Math.random() * 10 + 5 + 's';
            particle.style.animationDelay = Math.random() * 5 + 's';
            particlesContainer.appendChild(particle);
        }
    }

    createParticles();
    
    const inputs = document.querySelectorAll('.input-group input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'translateY(-2px)';
        });
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'translateY(0)';
        });
    });
</script>

</body>
</html>
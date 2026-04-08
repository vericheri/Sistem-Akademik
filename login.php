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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Akademik Mini</title>
    <link rel="stylesheet" href="login.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                    Mini
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
                    <p>&copy; 2026 Sistem Akademik Mini. All rights reserved.</p>
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
        
        function fillCredential(username, password) {
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
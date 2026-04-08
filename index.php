<?php
date_default_timezone_set('Asia/Jakarta');
require_once 'auth.php';
require_once 'db.php';
require_once 'mhs.php';
require_once 'matkul.php';
require_once 'nilai.php';

requireLogin();

$database = new db();
$conn = $database->getconn();
$nilai = new nilai($conn);

$currentUser = getCurrentUser();
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

$berita_terbaru = $conn->query("SELECT * FROM berita ORDER BY created_at DESC LIMIT 5");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_mhs'])) {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $jurusan = $_POST['jurusan'];
    $angkatan = $_POST['angkatan'];
    
    $stmt = $conn->prepare("INSERT INTO mhs (nim, nama, jurusan, angkatan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $nim, $nama, $jurusan, $angkatan);
    $stmt->execute();
    $_SESSION['success'] = 'Mahasiswa berhasil ditambahkan!';
    header("Location: index.php?page=mhs");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_mhs'])) {
    $id = $_POST['id'];
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $jurusan = $_POST['jurusan'];
    $angkatan = $_POST['angkatan'];
    
    $stmt = $conn->prepare("UPDATE mhs SET nim=?, nama=?, jurusan=?, angkatan=? WHERE id=?");
    $stmt->bind_param("sssii", $nim, $nama, $jurusan, $angkatan, $id);
    $stmt->execute();
    $_SESSION['success'] = 'Mahasiswa berhasil diupdate!';
    header("Location: index.php?page=mhs");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_matkul'])) {
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $sks = $_POST['sks'];
    $semester = $_POST['semester'];
    
    $stmt = $conn->prepare("INSERT INTO matkul (kode, nama, sks, semester) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $kode, $nama, $sks, $semester);
    $stmt->execute();
    $_SESSION['success'] = 'Mata Kuliah berhasil ditambahkan!';
    header("Location: index.php?page=matkul");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_matkul'])) {
    $id = $_POST['id'];
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $sks = $_POST['sks'];
    $semester = $_POST['semester'];
    
    $stmt = $conn->prepare("UPDATE matkul SET kode=?, nama=?, sks=?, semester=? WHERE id=?");
    $stmt->bind_param("ssiii", $kode, $nama, $sks, $semester, $id);
    $stmt->execute();
    $_SESSION['success'] = 'Mata Kuliah berhasil diupdate!';
    header("Location: index.php?page=matkul");
    exit();
}

if (isset($_GET['hapus_mhs'])) {
    $id = $_GET['hapus_mhs'];
    $conn->query("DELETE FROM mhs WHERE id = $id");
    $_SESSION['success'] = 'Mahasiswa berhasil dihapus!';
    header("Location: index.php?page=mhs");
    exit();
}

if (isset($_GET['hapus_matkul'])) {
    $id = $_GET['hapus_matkul'];
    $conn->query("DELETE FROM matkul WHERE id = $id");
    $_SESSION['success'] = 'Mata Kuliah berhasil dihapus!';
    header("Location: index.php?page=matkul");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $user_id = $_SESSION['user_id'];
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $no_telp = $_POST['no_telp'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($nama_lengkap)) {
        $_SESSION['profile_error'] = 'Nama lengkap harus diisi!';
    } else {
        $sql = "UPDATE users SET nama_lengkap = ?, no_telp = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nama_lengkap, $no_telp, $user_id);
        $stmt->execute();
        $stmt->close();
        
        if (!empty($new_password) && $new_password === $confirm_password) {
            $sql_pass = "UPDATE users SET password = ? WHERE id = ?";
            $stmt_pass = $conn->prepare($sql_pass);
            $stmt_pass->bind_param("si", $new_password, $user_id);
            $stmt_pass->execute();
            $stmt_pass->close();
            $_SESSION['profile_success'] = 'Profile dan password berhasil diupdate!';
        } elseif (!empty($new_password)) {
            $_SESSION['profile_error'] = 'Password baru tidak cocok!';
        } else {
            $_SESSION['profile_success'] = 'Profile berhasil diupdate!';
        }
        $_SESSION['nama'] = $nama_lengkap;
    }
    header("Location: index.php?page=profile");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_berita'])) {
    $judul = $_POST['judul'] ?? '';
    $konten = $_POST['konten'] ?? '';
    $penulis = $_SESSION['nama'] ?? 'Administrator';
    
    $stmt = $conn->prepare("INSERT INTO berita (judul, konten, penulis) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $judul, $konten, $penulis);
    $stmt->execute();
    $_SESSION['berita_success'] = 'Berita berhasil ditambahkan!';
    header("Location: index.php?page=berita");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_berita'])) {
    $id = $_POST['id'] ?? 0;
    $judul = $_POST['judul'] ?? '';
    $konten = $_POST['konten'] ?? '';
    
    $stmt = $conn->prepare("UPDATE berita SET judul = ?, konten = ? WHERE id = ?");
    $stmt->bind_param("ssi", $judul, $konten, $id);
    $stmt->execute();
    $_SESSION['berita_success'] = 'Berita berhasil diupdate!';
    header("Location: index.php?page=berita");
    exit();
}

if (isset($_GET['hapus_berita'])) {
    $id = $_GET['hapus_berita'];
    $conn->query("DELETE FROM berita WHERE id = $id");
    $_SESSION['berita_success'] = 'Berita berhasil dihapus!';
    header("Location: index.php?page=berita");
    exit();
}

$edit_mhs = null;
if (isset($_GET['edit_mhs_id'])) {
    $id = $_GET['edit_mhs_id'];
    $result = $conn->query("SELECT * FROM mhs WHERE id = $id");
    $edit_mhs = $result->fetch_assoc();
}

$edit_matkul = null;
if (isset($_GET['edit_matkul_id'])) {
    $id = $_GET['edit_matkul_id'];
    $result = $conn->query("SELECT * FROM matkul WHERE id = $id");
    $edit_matkul = $result->fetch_assoc();
}

$user_data = [];
if ($page == 'profile') {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user_data = $stmt->get_result()->fetch_assoc();
}

$berita_list = [];
if ($page == 'berita') {
    $berita_list = $conn->query("SELECT * FROM berita ORDER BY created_at DESC");
}

$edit_berita = null;
if (isset($_GET['edit_berita_id'])) {
    $id = $_GET['edit_berita_id'];
    $result = $conn->query("SELECT * FROM berita WHERE id = $id");
    $edit_berita = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Akademik Mini Universitas Iga Bakar</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .profile-avatar-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #1a3a5c, #c9a03d);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }
        .profile-avatar-icon i {
            font-size: 28px;
            color: white;
        }
        .user-profile {
            text-align: center;
            padding: 0 16px 20px 16px;
            border-bottom: 1px solid var(--gray-200);
            margin-bottom: 20px;
        }
        .profile-name {
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 4px;
            font-size: 14px;
        }
        .profile-role {
            margin-bottom: 8px;
        }
        .news-widget {
            background: white;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e5e7eb;
            margin-bottom: 24px;
        }
        .news-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
        }
        .news-header h4 {
            font-size: 16px;
            color: #1f2937;
            margin: 0;
        }
        .news-header h4 i {
            color: #1a3a5c;
            margin-right: 8px;
        }
        .btn-news {
            background: linear-gradient(135deg, #1a3a5c, #c9a03d);
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
        }
        .news-item {
            padding: 15px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .news-item:last-child {
            border-bottom: none;
        }
        .news-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .news-meta {
            font-size: 11px;
            color: #9ca3af;
            display: flex;
            gap: 15px;
            margin-bottom: 5px;
        }
        .news-excerpt {
            font-size: 13px;
            color: #6b7280;
            margin-top: 5px;
            line-height: 1.4;
        }
        .empty-news {
            text-align: center;
            padding: 40px;
            color: #9ca3af;
        }
        .welcome-card {
            background: linear-gradient(135deg, #1a3a5c, #2c4a6e);
            border-radius: 16px;
            padding: 20px 24px;
            color: white;
            margin-bottom: 24px;
        }
        .welcome-card h3 {
            margin: 0 0 8px 0;
            font-size: 18px;
        }
        .welcome-card p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }
        .profile-header-card {
            background: linear-gradient(135deg, #1a3a5c, #2c4a6e);
            border-radius: 24px;
            padding: 40px;
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        .profile-icon-large {
            width: 120px;
            height: 120px;
            border-radius: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .profile-name-large {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .info-card {
            background: #f9fafb;
            border-radius: 16px;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid #e5e7eb;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #6b7280;
        }
        .info-value {
            color: #1f2937;
        }
        .berita-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        .berita-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .berita-judul {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }
        .berita-meta {
            font-size: 12px;
            color: #9ca3af;
            margin-bottom: 12px;
            display: flex;
            gap: 15px;
        }
        .berita-konten {
            color: #6b7280;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .form-buttons {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }
        .btn-cancel {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #e5e7eb;
            color: #374151;
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .btn-cancel:hover {
            background: #d1d5db;
            transform: translateY(-2px);
        }
        .form-card {
            background: #f9fafb;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid #e5e7eb;
        }
        .btn-primary {
            background: linear-gradient(135deg, #1a3a5c, #c9a03d);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26, 58, 92, 0.3);
        }
        .btn-warning {
            background: #c9a03d;
            color: white;
        }
        .btn-danger {
            background: linear-gradient(135deg, #c62828, #d32f2f);
            color: white;
        }
        .btn-submit {
            background: linear-gradient(135deg, #1a3a5c, #c9a03d);
            color: white;
        }
        .data-table thead {
            background: #1a3a5c;
        }
        .data-table th {
            color: white;
        }
        .stat-card::before {
            background: linear-gradient(135deg, #1a3a5c, #c9a03d);
        }
        .section-title::after {
            background: linear-gradient(135deg, #1a3a5c, #c9a03d);
        }
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="particles" id="particles"></div>

<div class="dashboard-wrapper">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <div class="logo-icon" style="background: linear-gradient(135deg, #1a3a5c, #c9a03d);">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1>Sistem Akademik</h1>
                <p>Universitas Iga Bakar</p>
            </div>
        </div>
        
        <div class="user-profile">
            <div style="display: flex; justify-content: center;">
                <div class="profile-avatar-icon">
                    <i class="fas fa-user"></i>
                </div>
            </div>
            <div class="profile-name">
                <?= htmlspecialchars($_SESSION['nama'] ?? 'Pengguna') ?>
            </div>
            <div class="profile-role">
                <?php 
                $role = $_SESSION['role'] ?? 'staff';
                echo '<span class="role-badge ' . $role . '">' . ucfirst($role) . '</span>';
                ?>
            </div>
            <a href="index.php?page=profile" style="display: inline-block; margin-top: 12px; padding: 8px 16px; background: rgba(26, 58, 92, 0.1); color: #1a3a5c; border-radius: 12px; text-decoration: none; font-size: 12px; font-weight: 500;">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
        </div>
        
        <div class="nav-menu">
            <div class="nav-item">
                <a href="index.php" class="nav-link <?= $page == 'home' ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="index.php?page=profile" class="nav-link <?= $page == 'profile' ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-user-circle"></i></span>
                    <span>Edit Profile</span>
                </a>
            </div>
            <?php if (isAdmin()): ?>
            <div class="nav-item">
                <a href="index.php?page=berita" class="nav-link <?= $page == 'berita' ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-newspaper"></i></span>
                    <span>Kelola Berita</span>
                </a>
            </div>
            <?php endif; ?>
            <?php if (isAdmin() || isDosen()): ?>
            <div class="nav-item">
                <a href="index.php?page=mhs" class="nav-link <?= $page == 'mhs' ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-users"></i></span>
                    <span>Mahasiswa</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="index.php?page=matkul" class="nav-link <?= $page == 'matkul' ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-book"></i></span>
                    <span>Mata Kuliah</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="index.php?page=nilai" class="nav-link <?= $page == 'nilai' ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                    <span>Input Nilai</span>
                </a>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="sidebar-footer">
            <a href="logout.php" class="btn-logout-sidebar">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>
    
    <main class="main-content">
        <div class="top-bar">
            <h2 class="page-title">
                <span>
                    <?php
                    if ($page == 'mhs') echo 'Mahasiswa';
                    elseif ($page == 'matkul') echo 'Mata Kuliah';
                    elseif ($page == 'nilai') echo 'Input Nilai';
                    elseif ($page == 'profile') echo 'Edit Profile';
                    elseif ($page == 'berita') echo 'Kelola Berita';
                    elseif ($page == 'tambah_mhs') echo 'Tambah Mahasiswa';
                    elseif ($page == 'edit_mhs') echo 'Edit Mahasiswa';
                    elseif ($page == 'tambah_matkul') echo 'Tambah Mata Kuliah';
                    elseif ($page == 'edit_matkul') echo 'Edit Mata Kuliah';
                    else echo 'Dashboard';
                    ?>
                </span>
            </h2>
            <div class="date-time">
                <i class="far fa-calendar-alt"></i> <?= date('d F Y') ?> &nbsp;|&nbsp;
                <i class="far fa-clock"></i> <?= date('H:i') ?>
            </div>
        </div>
        
        <div class="content-container">
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <?php if ($page == 'tambah_mhs' && (isAdmin() || isDosen())): ?>
                <div class="form-card">
                    <h3><i class="fas fa-plus" style="color: #c9a03d;"></i> Tambah Mahasiswa Baru</h3>
                    <form method="POST" action="index.php?page=tambah_mhs">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">NIM</label>
                                <input type="text" name="nim" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jurusan</label>
                                <input type="text" name="jurusan" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Angkatan</label>
                                <input type="number" name="angkatan" class="form-input" required>
                            </div>
                        </div>
                        <div class="form-buttons">
                            <button type="submit" name="tambah_mhs" class="btn-submit"><i class="fas fa-save"></i> Simpan</button>
                            <a href="index.php?page=mhs" class="btn-cancel"><i class="fas fa-times"></i> Batal</a>
                        </div>
                    </form>
                </div>

            <?php elseif ($page == 'edit_mhs' && $edit_mhs && (isAdmin() || isDosen())): ?>
                <div class="form-card">
                    <h3><i class="fas fa-edit" style="color: #c9a03d;"></i> Edit Mahasiswa</h3>
                    <form method="POST" action="index.php?page=edit_mhs">
                        <input type="hidden" name="id" value="<?= $edit_mhs['id'] ?>">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">NIM</label>
                                <input type="text" name="nim" class="form-input" value="<?= htmlspecialchars($edit_mhs['nim']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-input" value="<?= htmlspecialchars($edit_mhs['nama']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jurusan</label>
                                <input type="text" name="jurusan" class="form-input" value="<?= htmlspecialchars($edit_mhs['jurusan']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Angkatan</label>
                                <input type="number" name="angkatan" class="form-input" value="<?= $edit_mhs['angkatan'] ?>" required>
                            </div>
                        </div>
                        <div class="form-buttons">
                            <button type="submit" name="edit_mhs" class="btn-submit"><i class="fas fa-save"></i> Update</button>
                            <a href="index.php?page=mhs" class="btn-cancel"><i class="fas fa-times"></i> Batal</a>
                        </div>
                    </form>
                </div>

            <?php elseif ($page == 'tambah_matkul' && (isAdmin() || isDosen())): ?>
                <div class="form-card">
                    <h3><i class="fas fa-plus" style="color: #c9a03d;"></i> Tambah Mata Kuliah Baru</h3>
                    <form method="POST" action="index.php?page=tambah_matkul">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Kode MK</label>
                                <input type="text" name="kode" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nama Mata Kuliah</label>
                                <input type="text" name="nama" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">SKS</label>
                                <input type="number" name="sks" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Semester</label>
                                <input type="number" name="semester" class="form-input" required>
                            </div>
                        </div>
                        <div class="form-buttons">
                            <button type="submit" name="tambah_matkul" class="btn-submit"><i class="fas fa-save"></i> Simpan</button>
                            <a href="index.php?page=matkul" class="btn-cancel"><i class="fas fa-times"></i> Batal</a>
                        </div>
                    </form>
                </div>

            <?php elseif ($page == 'edit_matkul' && $edit_matkul && (isAdmin() || isDosen())): ?>
                <div class="form-card">
                    <h3><i class="fas fa-edit" style="color: #c9a03d;"></i> Edit Mata Kuliah</h3>
                    <form method="POST" action="index.php?page=edit_matkul">
                        <input type="hidden" name="id" value="<?= $edit_matkul['id'] ?>">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Kode MK</label>
                                <input type="text" name="kode" class="form-input" value="<?= htmlspecialchars($edit_matkul['kode']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nama Mata Kuliah</label>
                                <input type="text" name="nama" class="form-input" value="<?= htmlspecialchars($edit_matkul['nama']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">SKS</label>
                                <input type="number" name="sks" class="form-input" value="<?= $edit_matkul['sks'] ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Semester</label>
                                <input type="number" name="semester" class="form-input" value="<?= $edit_matkul['semester'] ?>" required>
                            </div>
                        </div>
                        <div class="form-buttons">
                            <button type="submit" name="edit_matkul" class="btn-submit"><i class="fas fa-save"></i> Update</button>
                            <a href="index.php?page=matkul" class="btn-cancel"><i class="fas fa-times"></i> Batal</a>
                        </div>
                    </form>
                </div>
            
            <?php elseif ($page == 'profile'): ?>
                <?php if (isset($_SESSION['profile_error'])): ?>
                    <div class="alert alert-error"><?= $_SESSION['profile_error']; unset($_SESSION['profile_error']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['profile_success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['profile_success']; unset($_SESSION['profile_success']); ?></div>
                <?php endif; ?>
                
                <div class="profile-header-card">
                    <div class="profile-icon-large" style="background: linear-gradient(135deg, #1a3a5c, #c9a03d);">
                        <i class="fas fa-user-graduate" style="font-size: 60px; color: white;"></i>
                    </div>
                    <div class="profile-name-large"><?= htmlspecialchars($user_data['nama_lengkap']) ?></div>
                    <div class="profile-role-large">
                        <span style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 20px; font-size: 12px;"><?= ucfirst($user_data['role']) ?></span>
                    </div>
                    <div style="font-size: 13px; opacity: 0.8; margin-top: 8px;">
                        <i class="fas fa-user"></i> <?= htmlspecialchars($user_data['username']) ?>
                    </div>
                </div>
                
                <div class="info-card" style="border-left: 4px solid #c9a03d;">
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-phone"></i> No. Telepon</span>
                        <span class="info-value"><?= !empty($user_data['no_telp']) ? htmlspecialchars($user_data['no_telp']) : '<span style="color: #94a3b8;">Belum diisi</span>' ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-calendar-alt"></i> Bergabung Sejak</span>
                        <span class="info-value"><?= date('d F Y', strtotime($user_data['created_at'] ?? 'now')) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-key"></i> Status Password</span>
                        <span class="info-value"><span style="color: #2e7d32;"><i class="fas fa-check-circle"></i> Terenkripsi</span></span>
                    </div>
                </div>
                
                <div style="margin-top: 30px;">
                    <h3 style="border-left: 4px solid #c9a03d; padding-left: 12px;"><i class="fas fa-pen" style="color: #c9a03d;"></i> Ubah Data Profile</h3>
                    <form method="POST" action="index.php?page=profile">
                        <div class="form-grid">
                            <div class="form-group"><label class="form-label">Nama Lengkap</label><input type="text" name="nama_lengkap" class="form-input" value="<?= htmlspecialchars($user_data['nama_lengkap']) ?>" required></div>
                            <div class="form-group"><label class="form-label">No. Telepon</label><input type="text" name="no_telp" class="form-input" value="<?= htmlspecialchars($user_data['no_telp'] ?? '') ?>" placeholder="Contoh: 08123456789"></div>
                            <div class="form-group"><label class="form-label">Password Baru</label><input type="password" name="new_password" class="form-input" placeholder="Kosongkan jika tidak diubah"></div>
                            <div class="form-group"><label class="form-label">Konfirmasi Password</label><input type="password" name="confirm_password" class="form-input" placeholder="Ketik ulang password baru"></div>
                        </div>
                        <div class="form-buttons">
                            <button type="submit" name="update_profile" class="btn-submit"><i class="fas fa-save"></i> Simpan Perubahan</button>
                            <a href="index.php" class="btn-cancel"><i class="fas fa-times"></i> Batal</a>
                        </div>
                    </form>
                </div>

            <?php elseif ($page == 'berita' && isAdmin()): ?>
                <?php if (isset($_SESSION['berita_success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['berita_success']; unset($_SESSION['berita_success']); ?></div>
                <?php endif; ?>
                
                <?php if ($edit_berita): ?>
                    <div class="form-card">
                        <h3><i class="fas fa-edit" style="color: #c9a03d;"></i> Edit Berita</h3>
                        <form method="POST" action="index.php?page=berita">
                            <input type="hidden" name="id" value="<?= $edit_berita['id'] ?>">
                            <div class="form-group"><label class="form-label">Judul Berita</label><input type="text" name="judul" class="form-input" value="<?= htmlspecialchars($edit_berita['judul']) ?>" required></div>
                            <div class="form-group"><label class="form-label">Konten Berita</label><textarea name="konten" class="form-input" rows="8" required><?= htmlspecialchars($edit_berita['konten']) ?></textarea></div>
                            <div class="form-buttons"><button type="submit" name="edit_berita" class="btn-submit">Update Berita</button><a href="index.php?page=berita" class="btn-cancel">Batal</a></div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="form-card">
                        <h3><i class="fas fa-plus" style="color: #c9a03d;"></i> Tambah Berita Baru</h3>
                        <form method="POST" action="index.php?page=berita">
                            <div class="form-group"><label class="form-label">Judul Berita</label><input type="text" name="judul" class="form-input" required></div>
                            <div class="form-group"><label class="form-label">Konten Berita</label><textarea name="konten" class="form-input" rows="8" required></textarea></div>
                            <button type="submit" name="tambah_berita" class="btn-submit">Simpan Berita</button>
                        </form>
                    </div>
                <?php endif; ?>
                
                <h3>Daftar Berita</h3>
                <?php if ($berita_list && $berita_list->num_rows > 0): ?>
                    <?php while ($berita = $berita_list->fetch_assoc()): ?>
                    <div class="berita-card">
                        <div class="berita-judul"><?= htmlspecialchars($berita['judul']) ?></div>
                        <div class="berita-meta"><span><i class="far fa-calendar-alt"></i> <?= date('d F Y', strtotime($berita['created_at'])) ?></span><span><i class="fas fa-user"></i> <?= htmlspecialchars($berita['penulis']) ?></span></div>
                        <div class="berita-konten"><?= nl2br(htmlspecialchars(substr($berita['konten'], 0, 200))) ?>...</div>
                        <div class="btn-group">
                            <a href="index.php?page=berita&edit_berita_id=<?= $berita['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="index.php?hapus_berita=<?= $berita['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="alert alert-info">Belum ada berita.</div>
                <?php endif; ?>

            <?php elseif ($page == 'mhs' && (isAdmin() || isDosen())): ?>
                <div class="section-header">
                    <h3 class="section-title">Data Mahasiswa</h3>
                    <a href="index.php?page=tambah_mhs" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Mahasiswa</a>
                </div>
                <?php
                $mhslist = $conn->query("SELECT * FROM mhs ORDER BY angkatan DESC, nama");
                if ($mhslist && $mhslist->num_rows > 0):
                ?>
                <div class="table-container">
                    <table class="data-table">
                        <thead><tr><th>NIM</th><th>Nama</th><th>Jurusan</th><th>Angkatan</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php while ($m = $mhslist->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($m['nim'] ?? '') ?></td>
                                <td><?= htmlspecialchars($m['nama'] ?? '') ?></td>
                                <td><?= htmlspecialchars($m['jurusan'] ?? '') ?></td>
                                <td><?= $m['angkatan'] ?? '' ?></td>
                                <td class="btn-group">
                                    <a href="index.php?page=edit_mhs&edit_mhs_id=<?= $m['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <?php if (isAdmin()): ?>
                                    <a href="index.php?hapus_mhs=<?= $m['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
                                    <?php endif; ?>
                                    <a href="khs.php?id=<?= $m['id'] ?>" class="btn btn-primary btn-sm" target="_blank">Cetak KHS</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">Belum ada data mahasiswa.</div>
                <?php endif; ?>

            <?php elseif ($page == 'matkul' && (isAdmin() || isDosen())): ?>
                <div class="section-header">
                    <h3 class="section-title">Data Mata Kuliah</h3>
                    <a href="index.php?page=tambah_matkul" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Mata Kuliah</a>
                </div>
                <?php
                $matkullist = matkul::getall($conn);
                if (count($matkullist) > 0):
                ?>
                <div class="table-container">
                    <table class="data-table">
                        <thead><tr><th>Kode</th><th>Nama MK</th><th>SKS</th><th>Semester</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php foreach ($matkullist as $mk): ?>
                            <tr>
                                <td><?= htmlspecialchars($mk['kode'] ?? '') ?></td>
                                <td><?= htmlspecialchars($mk['nama'] ?? '') ?></td>
                                <td><?= $mk['sks'] ?? '' ?></td>
                                <td><?= $mk['semester'] ?? '' ?></td>
                                <td class="btn-group">
                                    <a href="index.php?page=edit_matkul&edit_matkul_id=<?= $mk['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <?php if (isAdmin()): ?>
                                    <a href="index.php?hapus_matkul=<?= $mk['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">Belum ada data mata kuliah.</div>
                <?php endif; ?>

            <?php elseif ($page == 'nilai' && (isAdmin() || isDosen())): ?>
                <h3 class="section-title">Input Nilai Mahasiswa</h3>
                
                <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpannilai'])):
                    $result = $nilai->simpannilai($_POST['mhs_id'], $_POST['matkul_id'], $_POST['nilai_angka']);
                    echo $result ? '<div class="alert alert-success">Nilai berhasil disimpan!</div>' : '<div class="alert alert-error">Gagal menyimpan nilai!</div>';
                endif;
                ?>
                
                <div class="form-card">
                    <form method="POST" action="index.php?page=nilai">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Mahasiswa</label>
                                <select name="mhs_id" class="form-select" required>
                                    <option value="">Pilih Mahasiswa</option>
                                    <?php $mhslist = $conn->query("SELECT * FROM mhs ORDER BY nama"); while ($m = $mhslist->fetch_assoc()): ?>
                                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nama'] ?? '') ?> (<?= $m['nim'] ?? '' ?>)</option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Mata Kuliah</label>
                                <select name="matkul_id" class="form-select" required>
                                    <option value="">Pilih Mata Kuliah</option>
                                    <?php $matkullist = matkul::getall($conn); foreach ($matkullist as $mk): ?>
                                    <option value="<?= $mk['id'] ?>"><?= htmlspecialchars($mk['kode'] ?? '') ?> - <?= htmlspecialchars($mk['nama'] ?? '') ?> (<?= $mk['sks'] ?? '' ?> SKS)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nilai Angka (0-100)</label>
                                <input type="number" step="any" name="nilai_angka" min="0" max="100" class="form-input" required>
                            </div>
                        </div>
                        <button type="submit" name="simpannilai" class="btn-submit">Simpan Nilai</button>
                    </form>
                </div>
                
                <h3>Daftar Nilai Mahasiswa</h3>
                <?php
                $nilaijoin = $conn->query("SELECT m.nama AS mhs, mk.nama AS matkul, n.nilai_angka, n.nilai_huruf FROM nilai n JOIN mhs m ON n.mhs_id = m.id JOIN matkul mk ON n.matkul_id = mk.id ORDER BY m.nama");
                if ($nilaijoin && $nilaijoin->num_rows > 0):
                ?>
                <div class="table-container">
                    <table class="data-table">
                        <thead><tr><th>Mahasiswa</th><th>Mata Kuliah</th><th>Nilai Angka</th><th>Nilai Huruf</th></tr></thead>
                        <tbody>
                            <?php while ($row = $nilaijoin->fetch_assoc()): ?>
                            <tr><td><?= htmlspecialchars($row['mhs'] ?? '') ?></td><td><?= htmlspecialchars($row['matkul'] ?? '') ?></td><td><?= $row['nilai_angka'] ?? '' ?></td><td><?= $row['nilai_huruf'] ?? '' ?></td></tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">Belum ada nilai yang tersimpan.</div>
                <?php endif; ?>
            
            <?php else: ?>
                <div class="welcome-card">
                    <h3>Selamat Datang, <?= htmlspecialchars($_SESSION['nama'] ?? 'Pengguna') ?>! 👋</h3>
                    <p>Sistem Akademik Universitas Iga Bakar dirancang untuk membantu Anda mengelola data akademik dengan mudah dan cepat.</p>
                </div>
                
                <div class="news-widget">
                    <div class="news-header">
                        <h4><i class="fas fa-newspaper"></i> Informasi & Berita Terbaru</h4>
                        <?php if (isAdmin()): ?>
                        <a href="index.php?page=berita" class="btn-news"><i class="fas fa-plus"></i> Kelola Berita</a>
                        <?php endif; ?>
                    </div>
                    <?php if ($berita_terbaru && $berita_terbaru->num_rows > 0): ?>
                        <?php while ($berita = $berita_terbaru->fetch_assoc()): ?>
                        <div class="news-item">
                            <div class="news-title"><?= htmlspecialchars($berita['judul']) ?></div>
                            <div class="news-meta"><span><i class="far fa-calendar-alt"></i> <?= date('d M Y', strtotime($berita['created_at'])) ?></span><span><i class="fas fa-user"></i> <?= htmlspecialchars($berita['penulis']) ?></span></div>
                            <div class="news-excerpt"><?= nl2br(htmlspecialchars(substr($berita['konten'], 0, 150))) ?>...</div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-news"><i class="fas fa-newspaper" style="font-size: 40px; margin-bottom: 10px; display: block;"></i><p>Belum ada berita atau informasi.</p><?php if (isAdmin()): ?><a href="index.php?page=berita" class="btn-news" style="display: inline-block; margin-top: 10px;">Buat Berita Pertama</a><?php endif; ?></div>
                    <?php endif; ?>
                </div>
                
                <?php
                $totalmhs = $conn->query("SELECT COUNT(*) as total FROM mhs")->fetch_assoc()['total'] ?? 0;
                $totalmatkul = $conn->query("SELECT COUNT(*) as total FROM matkul")->fetch_assoc()['total'] ?? 0;
                $totalnilai = $conn->query("SELECT COUNT(*) as total FROM nilai")->fetch_assoc()['total'] ?? 0;
                ?>
                
                <div class="stats-grid">
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-users"></i></div><div class="stat-value"><?= $totalmhs ?></div><div class="stat-label">Total Mahasiswa</div></div>
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-book"></i></div><div class="stat-value"><?= $totalmatkul ?></div><div class="stat-label">Total Mata Kuliah</div></div>
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-chart-line"></i></div><div class="stat-value"><?= $totalnilai ?></div><div class="stat-label">Total Nilai Terekam</div></div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="app-footer">
            <p><i class="fas fa-graduation-cap"></i> Sistem Akademik Mini Universitas Iga Bakar | Version 1.0</p>
            <p style="font-size: 11px; margin-top: 6px;">&copy; 2025 - All rights reserved</p>
        </div>
    </main>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar) sidebar.classList.toggle('open');
    if (overlay) overlay.classList.toggle('active');
}

function createParticles() {
    const container = document.getElementById('particles');
    if (!container) return;
    for (let i = 0; i < 40; i++) {
        const particle = document.createElement('div');
        particle.classList.add('particle');
        particle.style.width = (Math.random() * 5 + 2) + 'px';
        particle.style.height = particle.style.width;
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDuration = Math.random() * 10 + 8 + 's';
        particle.style.animationDelay = Math.random() * 5 + 's';
        container.appendChild(particle);
    }
}
createParticles();
</script>

</body>
</html>
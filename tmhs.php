<?php
require_once 'auth.php';
requireadmin();

require_once 'db.php';
$db = new db();
$conn = $db->getconn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conn->prepare("INSERT INTO mhs (nim, nama, jurusan, angkatan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $_POST['nim'], $_POST['nama'], $_POST['jurusan'], $_POST['angkatan']);
    $stmt->execute();
    header("Location: index.php?page=mhs");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>Tambah Mahasiswa</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <h2>Tambah Mahasiswa</h2>
    <form method="POST">
        <label>NIM</label><input type="text" name="nim" required>
        <label>Nama Lengkap</label><input type="text" name="nama" required>
        <label>Jurusan</label><input type="text" name="jurusan" required>
        <label>Angkatan</label><input type="number" name="angkatan" required>
        <button type="submit">Simpan</button>
        <a href="index.php?page=mhs" style="margin-left:10px;">Batal</a>
    </form>
</div>
</body>
</html>
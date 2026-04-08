<?php
require_once 'auth.php';
requireadmin();

require_once 'db.php';
$db = new db();
$conn = $db->getconn();
$id = $_GET['id'];
$m = $conn->query("SELECT * FROM mhs WHERE id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conn->prepare("UPDATE mhs SET nim=?, nama=?, jurusan=?, angkatan=? WHERE id=?");
    $stmt->bind_param("sssii", $_POST['nim'], $_POST['nama'], $_POST['jurusan'], $_POST['angkatan'], $id);
    $stmt->execute();
    header("Location: index.php?page=mhs");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit mahasiswa</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <h2>Edit mahasiswa</h2>
    <form method="POST">
        <label>NIM</label><input type="text" name="nim" value="<?= $m['nim'] ?>" required>
        <label>Nama lengkap</label><input type="text" name="nama" value="<?= $m['nama'] ?>" required>
        <label>Jurusan</label><input type="text" name="jurusan" value="<?= $m['jurusan'] ?>" required>
        <label>Angkatan</label><input type="number" name="angkatan" value="<?= $m['angkatan'] ?>" required>
        <button type="submit">Update</button>
        <a href="index.php?page=mhs">Batal</a>
    </form>
</div>
</body>
</html>
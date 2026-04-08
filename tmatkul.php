<?php
require_once 'auth.php';
requireadmin();

require_once 'db.php';
require_once 'matkul.php';
$db = new db();
$conn = $db->getconn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mk = new matkul($conn, null, $_POST['kode'], $_POST['nama'], $_POST['sks'], $_POST['semester']);
    $mk->save();
    header("Location: index.php?page=matkul");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>Tambah mata kuliah</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <h2>Tambah mata kuliah</h2>
    <form method="POST">
        <label>Kode mata kuliah</label><input type="text" name="kode" required>
        <label>Nama mata kuliah</label><input type="text" name="nama" required>
        <label>SKS</label><input type="number" name="sks" required>
        <label>Semester</label><input type="number" name="semester" required>
        <button type="submit">Simpan</button>
        <a href="index.php?page=matkul">Batal</a>
    </form>
</div>
</body>
</html>
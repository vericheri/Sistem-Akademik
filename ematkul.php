<?php
require_once 'auth.php';
requireadmin();

require_once 'db.php';
require_once 'matkul.php';
$db = new db();
$conn = $db->getconn();
$id = $_GET['id'];
$data = matkul::getbyid($conn, $id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mk = new matkul($conn, $id, $_POST['kode'], $_POST['nama'], $_POST['sks'], $_POST['semester']);
    $mk->update();
    header("Location: index.php?page=matkul");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Mata Kuliah</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Edit Mata Kuliah</h2>
    <form method="POST">
        <label>Kode MK</label>
        <input type="text" name="kode" value="<?= htmlspecialchars($data['kode']) ?>" required>
        
        <label>Nama MK</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>
        
        <label>SKS</label>
        <input type="number" name="sks" value="<?= $data['sks'] ?>" required>
        
        <label>Semester</label>
        <input type="number" name="semester" value="<?= $data['semester'] ?>" required>
        
        <button type="submit">Update</button>
        <a href="index.php?page=matkul">Batal</a>
    </form>
</div>
</body>
</html>
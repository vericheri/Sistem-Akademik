<?php
require_once 'db.php';
require_once 'mhs.php';
$db = new db();
$conn = $db->getconn();
$id = $_GET['id'];
$data = $conn->query("SELECT * FROM mhs WHERE id=$id")->fetch_assoc();
$m = new mhs($conn, $data['id'], $data['nama'], '', $data['nim'], $data['jurusan'], $data['angkatan']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cetak KHS</title>
    <link rel="stylesheet" href="style.css">
    <style>
        @media print { .btn-cetak { display: none; } body { background: white; padding: 0; } .container { box-shadow: none; } }
        .btn-cetak { margin: 20px 0; display: inline-block; }
    </style>
</head>
<body>
<div class="container">
    <?= $m->cetaklaporan() ?>
    <button class="btn btn-cetak" onclick="window.print()">Cetak / simpan PDF</button>
    <br><a href="index.php?page=mhs">← Kembali</a>
</div>
</body>
</html>
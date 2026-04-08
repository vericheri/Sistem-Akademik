<?php
require_once 'auth.php';
requireadmin();

require_once 'db.php';
$db = new db();
$conn = $db->getconn();
$conn->query("DELETE FROM matkul WHERE id=" . $_GET['id']);
header("Location: index.php?page=matkul");
?>
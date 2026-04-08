<?php
require_once 'auth.php';
requireAdmin();

require_once 'db.php';
$db = new db();
$conn = $db->getconn();
$conn->query("DELETE FROM mhs WHERE id=" . $_GET['id']);
header("Location: index.php?page=mhs");
?>
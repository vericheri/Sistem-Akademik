<?php
class db {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "akademik_mini";
    private $conn;

    public function __construct() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        if ($this->conn->connect_error) die("Koneksi gagal:" . $this->conn->connect_error);
    }

    public function getconn() {
        return $this->conn;
    }
}
?>
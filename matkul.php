<?php
class matkul {
    private $id;
    private $kode;
    private $nama;
    private $sks;
    private $semester;
    private $conn;

    public function __construct($conn, $id = null, $kode = null, $nama = null, $sks = null, $semester = null) {
        $this->conn = $conn;
        $this->id = $id;
        $this->kode = $kode;
        $this->nama = $nama;
        $this->sks = $sks;
        $this->semester = $semester;
    }

    public function getid() { return $this->id; }
    public function getkode() { return $this->kode; }
    public function getnama() { return $this->nama; }
    public function getsks() { return $this->sks; }
    public function getsemester() { return $this->semester; }

    public static function getall($conn) {
        $result = $conn->query("SELECT * FROM matkul ORDER BY semester, kode");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function delete($conn, $id) {
        $stmt = $conn->prepare("DELETE FROM matkul WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public static function getbyid($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM matkul WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function save() {
        $stmt = $this->conn->prepare("INSERT INTO matkul (kode, nama, sks, semester) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $this->kode, $this->nama, $this->sks, $this->semester);
        return $stmt->execute();
    }

    public function update() {
        $stmt = $this->conn->prepare("UPDATE matkul SET kode=?, nama=?, sks=?, semester=? WHERE id=?");
        $stmt->bind_param("ssiii", $this->kode, $this->nama, $this->sks, $this->semester, $this->id);
        return $stmt->execute();
    }
}
?>
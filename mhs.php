<?php
require_once 'laporan.php';
require_once 'user.php';

class mhs extends user implements laporan {
    private $nim;
    private $jurusan;
    private $angkatan;
    private $conn;

    public function __construct($conn, $id, $nim, $jurusan, $angkatan, $nama, $email) {
        parent::__construct($id, $nama, $email);
        $this->nim = $nim;
        $this->jurusan = $jurusan;
        $this->angkatan = $angkatan;
        $this->conn = $conn;
    }

    public function getrole() {
        return "mhs";
    }

    public function getnim() { return $this->nim; }
    public function getjurusan() { return $this->jurusan; }
    public function getangkatan() { return $this->angkatan; }

    public function hitungipk() {
        $query = "SELECT n.nilai_angka, mk.sks FROM nilai n JOIN matkul mk ON n.matkul_id = mk.id WHERE n.mhs_id = $this->id";
        $result = $this->conn->query($query);
        $totalsks = 0;
        $totalnilai = 0;
        while ($row = $result->fetch_assoc()) {
            $totalsks += $row['sks'];
            $totalnilai += $row['nilai_angka'] * $row['sks'];
        }
        if ($totalsks == 0) return 0;
        return round($totalnilai / $totalsks, 2);
    }

    public function cetaklaporan() {
        $ipk = $this->hitungipk();
        $query = "SELECT mk.kode, mk.nama AS matkul, mk.sks, n.nilai_angka, n.nilai_huruf FROM nilai n JOIN matkul mk ON n.matkul_id = mk.id WHERE n.mhs_id = $this->id ORDER BY mk.semester";
        $result = $this->conn->query($query);
        $html = "<h3>Kartu Hasil Studi</h3>";
        $html = "<p><strong>NIM:</strong> {$this->nim} | <strong>Nama:</strong> {$this->nama} <strong>Jurusan:</strong> {$this->jurusan} <strong>Angkatan:</strong> {$this->angkatan}</p>";
        $html = "<table border ='1' cellpadding='8' cellspacing='0' width='100%'> <tr><th>Kode</th><th>Mata Kuliah</th><th>SKS</th><th>Nilai</th><th>Grade</th></tr>";
        while ($row = $result->fetch_assoc()) {
            $html += "<tr> <td>{$row['kode']}</td><td>{$row['matkul']}</td><td>{$row['sks']}</td><td>{$row['nilai_angka']}</td><td>{$row['nilai_huruf']}</td> </tr>";
        }
        $html .= "</table><p><strong>IPK:</strong> " . number_format($ipk,2) . "</p>";
        return $html;
    }
}
?>
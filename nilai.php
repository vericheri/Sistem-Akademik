<?php
class nilai {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function konversihuruf($nilai) {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 70) return 'B';
        if ($nilai >= 55) return 'C';
        if ($nilai >= 40) return 'D';
        return 'E';
    }

    public function simpannilai($mhs_id, $matkul_id, $nilai_angka) {
        $nilai_huruf = $this->konversihuruf($nilai_angka);
        $check = $this->conn->prepare("SELECT id FROM nilai WHERE mhs_id=? AND matkul_id=?");
        $check->bind_param("ii", $mhs_id, $matkul_id);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;

        if ($exists) {
            $stmt = $this->conn->prepare("UPDATE nilai SET nilai_angka=?, nilai_huruf=? WHERE mhs_id=? AND matkul_id=?");
            $stmt->bind_param("dsii", $nilai_angka, $nilai_huruf, $mhs_id, $matkul_id);
        } else {
            $stmt = $this->conn->prepare("INSERT INTO nilai (mhs_id, matkul_id, nilai_angka, nilai_huruf) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iids", $mhs_id, $matkul_id, $nilai_angka, $nilai_huruf);
        }
        return $stmt->execute();
    }

    public function getnilaimhs($mhs_id) {
        $query = "SELECT mk.nama AS matkul, mk.sks, n.nilai_angka, n.nilai_huruf 
                  FROM nilai n 
                  JOIN matkul mk ON n.matkul_id = mk.id 
                  WHERE n.mhs_id = $mhs_id";
        return $this->conn->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function getnilaibymhsmatkul($mhs_id, $matkul_id) {
        $stmt = $this->conn->prepare("SELECT nilai_angka FROM nilai WHERE mhs_id=? AND matkul_id=?");
        $stmt->bind_param("ii", $mhs_id, $matkul_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['nilai_angka'] : '';
    }
}
?>
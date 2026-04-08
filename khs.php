<?php
require_once 'db.php';
require_once 'mhs.php';

$db = new db();
$conn = $db->getconn();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: ID mahasiswa tidak ditemukan!");
}

$id = $_GET['id'];

$query = "SELECT * FROM mhs WHERE id = $id";
$result = $conn->query($query);

if ($result->num_rows == 0) {
    die("Error: Mahasiswa dengan ID $id tidak ditemukan!");
}

$data = $result->fetch_assoc();

$mhs = new mhs($conn, $data['id'], $data['nama'], '', $data['nim'], $data['jurusan'], $data['angkatan']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cetak KHS - <?= $data['nama'] ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        @media print {
            .btn-cetak, .no-print { display: none; }
            body { background: white; padding: 0; margin: 0; }
            .container { box-shadow: none; padding: 10px; }
        }
        .btn-cetak {
            margin: 20px 10px 20px 0;
            display: inline-block;
            background: #4299e1;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-cetak:hover {
            background: #3182ce;
        }
        .btn-back {
            background: #718096;
        }
        .btn-back:hover {
            background: #4a5568;
        }
        .khs-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .khs-header h2 {
            border-bottom: none;
            font-size: 24px;
        }
        .info-mhs {
            background: #f7fafc;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 5px solid #667eea;
        }
        .ipk {
            font-size: 18px;
            font-weight: bold;
            color: #2c7a7b;
            margin-top: 20px;
            text-align: right;
            padding: 10px;
            background: #e6fffa;
            border-radius: 8px;
        }
        table {
            margin-top: 20px;
        }
        .footer-cetak {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #a0aec0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="khs-header">
        <h2>KARTU HASIL STUDI (KHS)</h2>
        <p>UNIVERSITAS AKADEMIK MINI</p>
        <hr>
    </div>

    <div class="info-mhs">
        <table style="border: none; box-shadow: none; margin: 0;">
            <tr style="background: transparent;">
                <td style="border: none; padding: 5px;"><strong>NIM</strong></td>
                <td style="border: none; padding: 5px;">: <?= $data['nim'] ?></td>
                <td style="border: none; padding: 5px; width: 50px;"></td>
                <td style="border: none; padding: 5px;"><strong>Jurusan</strong></td>
                <td style="border: none; padding: 5px;">: <?= $data['jurusan'] ?></td>
            </tr>
            <tr style="background: transparent;">
                <td style="border: none; padding: 5px;"><strong>Nama</strong></td>
                <td style="border: none; padding: 5px;">: <?= $data['nama'] ?></td>
                <td style="border: none; padding: 5px;"></td>
                <td style="border: none; padding: 5px;"><strong>Angkatan</strong></td>
                <td style="border: none; padding: 5px;">: <?= $data['angkatan'] ?></td>
            </tr>
        </table>
    </div>

    <?php
    $query_nilai = "SELECT mk.kode, mk.nama AS matkul, mk.sks, n.nilai_angka, n.nilai_huruf, mk.semester 
                    FROM nilai n 
                    JOIN matkul mk ON n.matkul_id = mk.id 
                    WHERE n.mhs_id = " . $id . " 
                    ORDER BY mk.semester ASC, mk.kode ASC";
    $result_nilai = $conn->query($query_nilai);
    
    if ($result_nilai->num_rows > 0) {
        echo "<table border='1' cellpadding='10' cellspacing='0' width='100%'>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Mata Kuliah</th>
                        <th>SKS</th>
                        <th>Nilai Angka</th>
                        <th>Nilai Huruf</th>
                    </tr>
                </thead>
                <tbody>";
        
        $no = 1;
        $total_sks = 0;
        $total_nilai_sks = 0;
        
        while ($row = $result_nilai->fetch_assoc()) {
            echo "<tr>
                    <td style='text-align: center;'>" . $no++ . "</td>
                    <td>{$row['kode']}</td>
                    <td>{$row['matkul']}</td>
                    <td style='text-align: center;'>{$row['sks']}</td>
                    <td style='text-align: center;'>{$row['nilai_angka']}</td>
                    <td style='text-align: center;'><strong>{$row['nilai_huruf']}</strong></td>
                  </tr>";
            $total_sks += $row['sks'];
            $total_nilai_sks += $row['nilai_angka'] * $row['sks'];
        }

        $ipk = ($total_sks > 0) ? round($total_nilai_sks / $total_sks, 2) : 0;
        
        echo "</tbody>
              </table>";
        
        echo "<div class='ipk'>
                <strong>INDEKS PRESTASI KUMULATIF (IPK)</strong><br>
                Total SKS: {$total_sks} | Total Nilai x SKS: {$total_nilai_sks}<br>
                <span style='font-size: 24px; color: #2b6cb0;'>IPK = {$ipk}</span>
              </div>";
    } else {
        echo "<div class='alert' style='background: #fed7d7; color: #9b2c2c; border-left-color: #e53e3e;'>
                Belum ada nilai untuk mahasiswa ini. Silakan input nilai terlebih dahulu.
              </div>";
    }
    ?>

    <div class="footer-cetak">
        <hr>
        <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?></p>
        <p>© Sistem Akademik Mini Universitas Iga Bakar</p>
    </div>

    <div class="no-print">
        <button class="btn-cetak" onclick="window.print()">CETAK / SIMPAN PDF</button>
        <a href="index.php?page=mhs" class="btn btn-back" style="background: #718096; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; margin-left: 10px;">← KEMBALI</a>
    </div>
</div>

<script>
</script>
</body>
</html>
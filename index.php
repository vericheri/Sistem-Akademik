<?php
require_once 'auth.php';
require_once 'db.php';
require_once 'mhs.php';
require_once 'matkul.php';
require_once 'nilai.php';

requireLogin();

$database = new db();
$conn = $database->getconn();

$nilai = new nilai($conn);

$mhslist = $conn->query("SELECT * FROM mhs ORDER BY angkatan DESC, nama");
$matkullist = matkul::getall($conn);

$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Akademik Mini</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1>Sistem Akademik Mini</h1>
    <div style="text-align: right;">
        <div style="margin-bottom: 5px;">
            Halo, 
            <strong>
                <?php 
                if (isset($_SESSION['nama']) && !empty($_SESSION['nama'])) {
                    echo htmlspecialchars($_SESSION['nama']);
                } else {
                    echo "User";
                }
                ?>
            </strong> 
            (
            <?php 
            if (isset($_SESSION['role']) && !empty($_SESSION['role'])) {
                echo htmlspecialchars($_SESSION['role']);
            } else {
                echo "guest";
            }
            ?>
            )
        </div>
        <a href="logout.php" style="background: #e53e3e; color: white; padding: 5px 15px; border-radius: 5px; text-decoration: none; font-size: 14px;">Logout</a>
    </div>
</div>
    
    <nav>
        <a href="index.php">Beranda</a>
        <?php if (isAdmin() || isDosen()): ?>
            <a href="?page=mhs">Manajemen Mahasiswa</a>
            <a href="?page=matkul">Manajemen Mata Kuliah</a>
            <a href="?page=nilai">Input Nilai</a>
        <?php endif; ?>
    </nav>

    <?php
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    
    if ($page == 'mhs' && (isAdmin() || isDosen())) {
        echo "<h2>Daftar Mahasiswa</h2>";
        echo "<a href='tmhs.php' class='btn btn-add'>+ Tambah Mahasiswa</a>";
        echo "<table>";
        echo "<tr><th>NIM</th><th>Nama</th><th>Jurusan</th><th>Angkatan</th><th>Aksi</th></tr>";
        while ($m = $mhslist->fetch_assoc()) {
            echo "<tr>
                    <td>{$m['nim']}</td>
                    <td>{$m['nama']}</td>
                    <td>{$m['jurusan']}</td>
                    <td>{$m['angkatan']}</td>
                    <td>
                        <a href='emhs.php?id={$m['id']}' class='btn btn-edit'>Edit</a>
                        " . (isAdmin() ? "<a href='hmhs.php?id={$m['id']}' class='btn btn-delete' onclick='return confirm(\"Yakin hapus?\")'>Hapus</a>" : "") . "
                        <a href='khs.php?id={$m['id']}' class='btn btn-cetak' target='_blank'>Cetak KHS</a>
                    </td>
                  </tr>";
        }
        echo "</table>";
    } 
    elseif ($page == 'matkul' && (isAdmin() || isDosen())) {
        echo "<h2>Daftar Mata Kuliah</h2>";
        echo "<a href='tmatkul.php' class='btn btn-add'>+ Tambah Mata Kuliah</a>";
        echo "<table>";
        echo "<tr><th>Kode</th><th>Nama MK</th><th>SKS</th><th>Semester</th><th>Aksi</th></tr>";
        foreach ($matkullist as $mk) {
            echo "<tr>
                    <td>{$mk['kode']}</td>
                    <td>{$mk['nama']}</td>
                    <td>{$mk['sks']}</td>
                    <td>{$mk['semester']}</td>
                    <td>
                        <a href='ematkul.php?id={$mk['id']}' class='btn btn-edit'>Edit</a>
                        " . (isAdmin() ? "<a href='hmatkul.php?id={$mk['id']}' class='btn btn-delete' onclick='return confirm(\"Yakin hapus?\")'>Hapus</a>" : "") . "
                    </td>
                  </tr>";
        }
        echo "</table>";
    }
    elseif ($page == 'nilai' && (isAdmin() || isDosen())) {
        echo "<h2>Input / Edit Nilai Mahasiswa</h2>";
        $mhslist2 = $conn->query("SELECT * FROM mhs");
        $matkullist2 = matkul::getall($conn);
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpannilai'])) {
            $result = $nilai->simpannilai($_POST['mhs_id'], $_POST['matkul_id'], $_POST['nilai_angka']);
            if ($result) {
                echo "<div class='alert'>Nilai berhasil disimpan!</div>";
            } else {
                echo "<div class='alert' style='background:#fed7d7; border-left-color:#e53e3e;'>Gagal menyimpan nilai!</div>";
            }
        }
        
        echo "<form method='POST'>
                <label>Mahasiswa</label>
                <select name='mhs_id' required>";
        $mhslist2->data_seek(0);
        while ($m = $mhslist2->fetch_assoc()) {
            echo "<option value='{$m['id']}'>{$m['nama']} ({$m['nim']})</option>";
        }
        echo "</select>
                <label>Mata Kuliah</label>
                <select name='matkul_id' required>";
        foreach ($matkullist2 as $mk) {
            echo "<option value='{$mk['id']}'>{$mk['kode']} - {$mk['nama']} ({$mk['sks']} SKS)</option>";
        }
        echo "</select>
                <label>Nilai Angka (0-100)</label>
                <input type='number' step='any' name='nilai_angka' min='0' max='100' required>
                <button type='submit' name='simpannilai'>Simpan Nilai</button>
              </form>";
        
        echo "<h3>Daftar Nilai Mahasiswa</h3>";
        $nilaijoin = $conn->query("SELECT m.nama AS mhs, mk.nama AS matkul, n.nilai_angka, n.nilai_huruf 
                                   FROM nilai n 
                                   JOIN mhs m ON n.mhs_id = m.id 
                                   JOIN matkul mk ON n.matkul_id = mk.id 
                                   ORDER BY m.nama");
        
        if ($nilaijoin->num_rows > 0) {
            echo "<table>
                    <thead>
                        <tr><th>Mahasiswa</th><th>Mata Kuliah</th><th>Nilai Angka</th><th>Grade</th></tr>
                    </thead>
                    <tbody>";
            while ($row = $nilaijoin->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['mhs']}</td>
                        <td>{$row['matkul']}</td>
                        <td>{$row['nilai_angka']}</td>
                        <td>{$row['nilai_huruf']}</td>
                      </tr>";
            }
            echo "</tbody>
                  </table>";
        } else {
            echo "<div class='alert' style='background:#e2e8f0; color:#4a5568;'>Belum ada nilai yang tersimpan.</div>";
        }
    }
    else {
        $totalmhs = $conn->query("SELECT COUNT(*) as total FROM mhs")->fetch_assoc()['total'];
        $totalmatkul = $conn->query("SELECT COUNT(*) as total FROM matkul")->fetch_assoc()['total'];
        $totalnilai = $conn->query("SELECT COUNT(*) as total FROM nilai")->fetch_assoc()['total'];
        $query_ipk_avg = "SELECT AVG(
            (SELECT AVG(
                CASE 
                    WHEN n.nilai_angka >= 85 THEN 4.00
                    WHEN n.nilai_angka >= 70 THEN 3.00
                    WHEN n.nilai_angka >= 55 THEN 2.00
                    WHEN n.nilai_angka >= 40 THEN 1.00
                    ELSE 0.00
                END
            ) FROM nilai n WHERE n.mhs_id = m.id)
        ) as avg_ipk FROM mhs m";
        $avg_ipk = $conn->query($query_ipk_avg)->fetch_assoc()['avg_ipk'];
        
        echo "<div class='card'>
                <h2>Dashboard</h2>
                <div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;'>
                    <div style='background: #ebf8ff; padding: 15px; border-radius: 10px; text-align: center;'>
                        <h3 style='margin-bottom: 5px; color: #2b6cb0;'>$totalmhs</h3>
                        <p>Total Mahasiswa</p>
                    </div>
                    <div style='background: #e6fffa; padding: 15px; border-radius: 10px; text-align: center;'>
                        <h3 style='margin-bottom: 5px; color: #2c7a7b;'>$totalmatkul</h3>
                        <p>Total Mata Kuliah</p>
                    </div>
                    <div style='background: #fefcbf; padding: 15px; border-radius: 10px; text-align: center;'>
                        <h3 style='margin-bottom: 5px; color: #975a16;'>$totalnilai</h3>
                        <p>Total Nilai</p>
                    </div>
                    <div style='background: #e9d8fd; padding: 15px; border-radius: 10px; text-align: center;'>
                        <h3 style='margin-bottom: 5px; color: #6b46c1;'>" . number_format($avg_ipk, 2) . "</h3>
                        <p>Rata-rata IPK</p>
                    </div>
                </div>
              </div>";
        
        $query_top = "SELECT m.id, m.nama, 
                      AVG(
                          CASE 
                              WHEN n.nilai_angka >= 85 THEN 4.00
                              WHEN n.nilai_angka >= 70 THEN 3.00
                              WHEN n.nilai_angka >= 55 THEN 2.00
                              WHEN n.nilai_angka >= 40 THEN 1.00
                              ELSE 0.00
                          END
                      ) as ipk
                      FROM mhs m
                      LEFT JOIN nilai n ON m.id = n.mhs_id
                      GROUP BY m.id
                      HAVING ipk IS NOT NULL
                      ORDER BY ipk DESC
                      LIMIT 5";
        $top_mhs = $conn->query($query_top);
        
        if ($top_mhs->num_rows > 0) {
            echo "<div class='card'>
                    <h3>Top 5 Mahasiswa (IPK Tertinggi)</h3>
                    <table>
                        <tr><th>Nama</th><th>IPK (4.00)</th><th>Aksi</th></tr>";
            while ($row = $top_mhs->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['nama']}</td>
                        <td style='text-align: center; font-weight: bold; color: #27ae60;'>" . number_format($row['ipk'], 2) . "</td>
                        <td><a href='khs.php?id={$row['id']}' class='btn btn-cetak' target='_blank'>Lihat KHS</a></td>
                      </tr>";
            }
            echo "</table></div>";
        }
        
        echo "<div class='card'>
                <h3>Fitur Tersedia</h3>
                <ul>
                    <li>Manajemen Mahasiswa (Tambah, Edit, Hapus)</li>
                    <li>Manajemen Mata Kuliah (Tambah, Edit, Hapus)</li>
                    <li>Input Nilai (Otomatis konversi huruf & bobot)</li>
                    <li>Cetak KHS per Mahasiswa (Dengan IPK skala 4.00)</li>
                    <li>Sistem Login (Admin, Dosen, Staff)</li>
                </ul>
              </div>";
    }
    ?>
    
    <div class="footer">
        Sistem Akademik Mini Universitas Iga Bakar
    </div>
</div>
</body>
</html>
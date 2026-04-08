<?php
date_default_timezone_set('Asia/Jakarta');
require_once 'auth.php';
requireLogin();
require_once 'db.php';

$db = new db();
$conn = $db->getconn();

$mhs_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$selected_semester = isset($_GET['semester']) ? (int)$_GET['semester'] : 0;

$query_mhs = "SELECT * FROM mhs WHERE id = $mhs_id";
$result_mhs = $conn->query($query_mhs);

if ($result_mhs->num_rows == 0) {
    die("Data mahasiswa tidak ditemukan!");
}

$mhs = $result_mhs->fetch_assoc();

$query_semester = "SELECT DISTINCT mk.semester 
                   FROM nilai n 
                   JOIN matkul mk ON n.matkul_id = mk.id 
                   WHERE n.mhs_id = $mhs_id 
                   ORDER BY mk.semester ASC";
$semester_list = $conn->query($query_semester);

if ($selected_semester == 0 && $semester_list->num_rows > 0) {
    $first = $semester_list->fetch_assoc();
    $selected_semester = $first['semester'];
    $semester_list->data_seek(0);
}

if ($selected_semester > 0) {
    $query_nilai = "SELECT mk.kode, mk.nama AS matkul, mk.sks, n.nilai_angka, n.nilai_huruf, mk.semester
                    FROM nilai n 
                    JOIN matkul mk ON n.matkul_id = mk.id 
                    WHERE n.mhs_id = $mhs_id AND mk.semester = $selected_semester
                    ORDER BY mk.kode ASC";
    $nilai_list = $conn->query($query_nilai);
}

$total_sks = 0;
$total_bobot = 0;
$ipk_semester = 0;

if ($selected_semester > 0 && isset($nilai_list) && $nilai_list->num_rows > 0) {
    $nilai_list->data_seek(0);
    while ($row = $nilai_list->fetch_assoc()) {
        $bobot = 0;
        $nilai = $row['nilai_angka'];
        if ($nilai >= 85) $bobot = 4.00;
        elseif ($nilai >= 70) $bobot = 3.00;
        elseif ($nilai >= 55) $bobot = 2.00;
        elseif ($nilai >= 40) $bobot = 1.00;
        else $bobot = 0.00;
        
        $total_sks += $row['sks'];
        $total_bobot += $bobot * $row['sks'];
    }
    $ipk_semester = $total_sks > 0 ? round($total_bobot / $total_sks, 2) : 0;
    $nilai_list->data_seek(0);
}

$query_all = "SELECT n.nilai_angka, mk.sks 
              FROM nilai n 
              JOIN matkul mk ON n.matkul_id = mk.id 
              WHERE n.mhs_id = $mhs_id";
$all_nilai = $conn->query($query_all);
$total_all_sks = 0;
$total_all_bobot = 0;
while ($row = $all_nilai->fetch_assoc()) {
    $bobot = 0;
    $nilai = $row['nilai_angka'];
    if ($nilai >= 85) $bobot = 4.00;
    elseif ($nilai >= 70) $bobot = 3.00;
    elseif ($nilai >= 55) $bobot = 2.00;
    elseif ($nilai >= 40) $bobot = 1.00;
    else $bobot = 0.00;
    
    $total_all_sks += $row['sks'];
    $total_all_bobot += $bobot * $row['sks'];
}
$ipk_kumulatif = $total_all_sks > 0 ? round($total_all_bobot / $total_all_sks, 2) : 0;

$predikat = '';
if ($ipk_kumulatif >= 3.51) $predikat = 'Cumlaude';
elseif ($ipk_kumulatif >= 3.00) $predikat = 'Sangat Memuaskan';
elseif ($ipk_kumulatif >= 2.50) $predikat = 'Memuaskan';
elseif ($ipk_kumulatif >= 2.00) $predikat = 'Cukup';
else $predikat = 'Kurang';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Hasil Studi (KHS) - <?= htmlspecialchars($mhs['nama']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', 'Georgia', 'Cambria', serif;
            background: #e8ecf1;
            padding: 30px;
            min-height: 100vh;
        }
        
        .khs-container {
            max-width: 1100px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            border-radius: 4px;
            overflow: hidden;
        }
        
        .khs-header {
            background: #1a3a5c;
            color: white;
            padding: 25px 30px;
            text-align: center;
            border-bottom: 3px solid #c9a03d;
        }
        
        .university-name {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }
        
        .university-sub {
            font-size: 12px;
            opacity: 0.8;
            letter-spacing: 1px;
        }
        
        .khs-title {
            font-size: 22px;
            font-weight: bold;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid rgba(255,255,255,0.3);
            display: inline-block;
            padding: 8px 30px;
        }
        
        .khs-body {
            padding: 30px;
        }
        
        .info-mahasiswa {
            background: #f5f7fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 5px solid #1a3a5c;
        }
        
        .info-title {
            font-size: 16px;
            font-weight: bold;
            color: #1a3a5c;
            margin-bottom: 15px;
            border-bottom: 2px solid #c9a03d;
            display: inline-block;
            padding-bottom: 5px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-top: 15px;
        }
        
        .info-item {
            display: flex;
            border-bottom: 1px dotted #ddd;
            padding-bottom: 6px;
        }
        
        .info-label {
            width: 120px;
            font-weight: 600;
            color: #555;
        }
        
        .info-value {
            flex: 1;
            color: #222;
            font-weight: 500;
        }
        
        .semester-selector {
            background: #f0f4f8;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .semester-label {
            font-weight: 600;
            color: #1a3a5c;
            font-size: 14px;
        }
        
        .semester-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .semester-btn {
            background: white;
            border: 1px solid #cbd5e1;
            padding: 8px 18px;
            border-radius: 25px;
            cursor: pointer;
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
            color: #333;
        }
        
        .semester-btn:hover {
            background: #e2e8f0;
            border-color: #1a3a5c;
        }
        
        .semester-btn.active {
            background: #1a3a5c;
            color: white;
            border-color: #1a3a5c;
        }
        
        .table-nilai {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }
        
        .table-nilai th {
            background: #1a3a5c;
            color: white;
            padding: 12px 10px;
            text-align: center;
            font-weight: 600;
            border: 1px solid #2c4a6e;
        }
        
        .table-nilai td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        
        .table-nilai td:first-child,
        .table-nilai td:nth-child(2) {
            text-align: left;
        }
        
        .table-nilai tbody tr:hover {
            background: #f5f7fa;
        }
        
        .ringkasan {
            background: #f5f7fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .ringkasan-item {
            flex: 1;
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .ringkasan-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .ringkasan-value {
            font-size: 22px;
            font-weight: bold;
            color: #1a3a5c;
        }
        
        .ringkasan-value.small {
            font-size: 16px;
        }
        
        .khs-footer {
            background: #f5f7fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 11px;
            color: #888;
            border-top: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
        }
        
        .action-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 15px;
            padding: 20px 30px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }
        
        .btn-print, .btn-back {
            padding: 10px 24px;
            border-radius: 6px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        
        .btn-print {
            background: #1a3a5c;
            color: white;
            border: none;
        }
        
        .btn-print:hover {
            background: #0f2a44;
            transform: translateY(-1px);
        }
        
        .btn-back {
            background: #e2e8f0;
            color: #333;
            border: none;
        }
        
        .btn-back:hover {
            background: #cbd5e1;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            
            .khs-container {
                box-shadow: none;
                border-radius: 0;
            }
            
            .semester-selector,
            .action-buttons,
            .btn-print,
            .btn-back {
                display: none;
            }
            
            .khs-header {
                background: #1a3a5c !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .table-nilai th {
                background: #1a3a5c !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .ringkasan-item {
                border: 1px solid #ddd;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .khs-body {
                padding: 20px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
                gap: 8px;
            }
            
            .info-label {
                width: 100px;
            }
            
            .semester-selector {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .ringkasan {
                flex-direction: column;
            }
            
            .table-nilai {
                font-size: 11px;
            }
            
            .table-nilai th,
            .table-nilai td {
                padding: 6px 4px;
            }
        }
        
        .ttd {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }
        
        .ttd-box {
            text-align: center;
            width: 250px;
        }
        
        .ttd-line {
            margin-top: 50px;
            border-top: 1px solid #333;
            padding-top: 8px;
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="khs-container">
    <div class="khs-header">
        <div class="university-name">UNIVERSITAS IGA BAKAR</div>
        <div class="university-sub">Jl. Arctic Monkey No.505, Kota Anything You Want | Telp. (505) 21112</div>
        <div class="khs-title">KARTU HASIL STUDI (KHS)</div>
    </div>
    
    <div class="khs-body">
        <div class="info-mahasiswa">
            <div class="info-title">▸ DATA MAHASISWA</div>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">NIM</span>
                    <span class="info-value">: <?= htmlspecialchars($mhs['nim']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Nama Lengkap</span>
                    <span class="info-value">: <?= htmlspecialchars($mhs['nama']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Program Studi</span>
                    <span class="info-value">: <?= htmlspecialchars($mhs['jurusan']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Angkatan</span>
                    <span class="info-value">: <?= $mhs['angkatan'] ?></span>
                </div>
            </div>
        </div>
        
        <div class="semester-selector">
            <span class="semester-label"><i class="fas fa-calendar-alt"></i> Pilih Semester:</span>
            <div class="semester-buttons">
                <?php if ($semester_list && $semester_list->num_rows > 0): ?>
                    <?php $semester_list->data_seek(0); ?>
                    <?php while ($sem = $semester_list->fetch_assoc()): ?>
                        <a href="?id=<?= $mhs_id ?>&semester=<?= $sem['semester'] ?>" 
                           class="semester-btn <?= $selected_semester == $sem['semester'] ? 'active' : '' ?>">
                            Semester <?= $sem['semester'] ?>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <span class="semester-btn" style="background:#f0f0f0;">Belum ada nilai</span>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($selected_semester > 0 && isset($nilai_list) && $nilai_list->num_rows > 0): ?>
            <table class="table-nilai">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th width="100">Kode MK</th>
                        <th align="left">Mata Kuliah</th>
                        <th width="60">SKS</th>
                        <th width="100">Nilai Angka</th>
                        <th width="80">Nilai Huruf</th>
                        <th width="80">Bobot</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $nilai_list->data_seek(0);
                    while ($row = $nilai_list->fetch_assoc()):
                        $bobot = 0;
                        $nilai = $row['nilai_angka'];
                        if ($nilai >= 85) $bobot = 4.00;
                        elseif ($nilai >= 70) $bobot = 3.00;
                        elseif ($nilai >= 55) $bobot = 2.00;
                        elseif ($nilai >= 40) $bobot = 1.00;
                        else $bobot = 0.00;
                        
                        $bobot_color = ($bobot >= 3.0) ? '#2e7d32' : (($bobot >= 2.0) ? '#ed6c02' : '#d32f2f');
                    ?>
                    <tr>
                        <td align="center"><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['kode']) ?></td>
                        <td align="left"><?= htmlspecialchars($row['matkul']) ?></td>
                        <td align="center"><?= $row['sks'] ?></td>
                        <td align="center"><?= $row['nilai_angka'] ?></td>
                        <td align="center"><strong><?= $row['nilai_huruf'] ?></strong></td>
                        <td align="center" style="color: <?= $bobot_color ?>; font-weight: bold;"><?= number_format($bobot, 2) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <div class="ringkasan">
                <div class="ringkasan-item">
                    <div class="ringkasan-label">Jumlah SKS Semester <?= $selected_semester ?></div>
                    <div class="ringkasan-value"><?= $total_sks ?> SKS</div>
                </div>
                <div class="ringkasan-item">
                    <div class="ringkasan-label">Total Bobot × SKS</div>
                    <div class="ringkasan-value"><?= $total_bobot ?></div>
                </div>
                <div class="ringkasan-item">
                    <div class="ringkasan-label">IPK Semester <?= $selected_semester ?></div>
                    <div class="ringkasan-value"><?= number_format($ipk_semester, 2) ?></div>
                </div>
            </div>
        <?php elseif ($selected_semester > 0): ?>
            <div style="text-align: center; padding: 40px; background: #f8fafc; border-radius: 8px;">
                <p style="color: #666;">Belum ada nilai untuk Semester <?= $selected_semester ?></p>
            </div>
        <?php endif; ?>
        
        <div class="ringkasan" style="margin-top: 15px; background: #e8f0fe;">
            <div class="ringkasan-item">
                <div class="ringkasan-label">Total SKS Keseluruhan</div>
                <div class="ringkasan-value"><?= $total_all_sks ?> SKS</div>
            </div>
            <div class="ringkasan-item">
                <div class="ringkasan-label">IPK Kumulatif</div>
                <div class="ringkasan-value"><?= number_format($ipk_kumulatif, 2) ?></div>
            </div>
            <div class="ringkasan-item">
                <div class="ringkasan-label">Predikat</div>
                <div class="ringkasan-value small"><?= $predikat ?></div>
            </div>
        </div>
        
        <div class="ttd">
            <div class="ttd-box">
                <div class="ttd-line">Kepala Program Studi</div>
                <div style="margin-top: 15px; font-weight: bold;">Dr. Ashlyn Kennedy, M.T.</div>
                <div style="font-size: 11px; color: #666;">NIP. 197512251999031001</div>
            </div>
        </div>
    </div>
    
    <div class="khs-footer">
        <span>Dicetak pada: <?= date('d F Y H:i:s') ?></span>
        <span>Dokumen resmi</span>
    </div>
</div>

<div class="action-buttons">
    <button class="btn-print" onclick="window.print()">
        <i class="fas fa-print"></i> Cetak / Simpan PDF
    </button>
    <a href="index.php?page=mhs" class="btn-back">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Mahasiswa
    </a>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
    window.onbeforeprint = function() {
        document.body.style.background = 'white';
    };
    
    window.onafterprint = function() {
        document.body.style.background = '#e8ecf1';
    };
</script>

</body>
</html>
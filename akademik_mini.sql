-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 08, 2026 at 09:49 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `akademik_mini`
--

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `id` int NOT NULL,
  `judul` varchar(200) NOT NULL,
  `konten` text NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `penulis` varchar(100) DEFAULT 'Administrator',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `berita`
--

INSERT INTO `berita` (`id`, `judul`, `konten`, `gambar`, `penulis`, `created_at`, `updated_at`) VALUES
(1, 'Selamat Datang di Sistem Akademik Mini', 'Sistem ini membantu Anda mengelola data mahasiswa, mata kuliah, nilai, dan mencetak KHS dengan mudah.hh', NULL, 'Administrator', '2026-04-08 07:19:44', '2026-04-08 08:12:01'),
(2, 'Update Sistem Terbaru', 'Sistem telah diperbarui dengan fitur manajemen profile dan tampilan yang lebih modern.', NULL, 'Administrator', '2026-04-08 07:19:44', '2026-04-08 07:19:44');

-- --------------------------------------------------------

--
-- Table structure for table `matkul`
--

CREATE TABLE `matkul` (
  `id` int NOT NULL,
  `kode` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `sks` int NOT NULL,
  `semester` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `matkul`
--

INSERT INTO `matkul` (`id`, `kode`, `nama`, `sks`, `semester`) VALUES
(1, '001', 'Digital Marketing', 2, 2),
(2, '002', 'Bahasa Indonesia', 2, 2),
(3, '003', 'Reaksi Kimia', 4, 2),
(4, '005', 'Biodegradasi', 2, 2),
(6, '006', 'Baking', 4, 2),
(7, '007', 'Waste Reduction', 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `mhs`
--

CREATE TABLE `mhs` (
  `id` int NOT NULL,
  `nim` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jurusan` varchar(50) NOT NULL,
  `angkatan` year NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `mhs`
--

INSERT INTO `mhs` (`id`, `nim`, `nama`, `jurusan`, `angkatan`) VALUES
(1, '00745672', 'Henry Kennedy', 'Bisnis Digital', '2022'),
(2, 'I43250575', 'Aurella Meidista Septiasa', 'Bisnis Digital', '2026');

-- --------------------------------------------------------

--
-- Table structure for table `nilai`
--

CREATE TABLE `nilai` (
  `id` int NOT NULL,
  `mhs_id` int NOT NULL,
  `matkul_id` int NOT NULL,
  `nilai_angka` decimal(5,2) NOT NULL,
  `nilai_huruf` char(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `nilai`
--

INSERT INTO `nilai` (`id`, `mhs_id`, `matkul_id`, `nilai_angka`, `nilai_huruf`) VALUES
(1, 1, 1, 67.00, 'C'),
(2, 1, 4, 80.00, 'B'),
(3, 1, 2, 98.00, 'A'),
(4, 1, 3, 90.00, 'A'),
(5, 2, 2, 99.00, 'A'),
(6, 2, 3, 90.00, 'A'),
(7, 2, 4, 79.00, 'B'),
(8, 2, 1, 91.00, 'A'),
(10, 1, 6, 90.00, 'A');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `foto_profile` varchar(255) DEFAULT NULL,
  `bio` text,
  `no_telp` varchar(20) DEFAULT NULL,
  `role` enum('admin','dosen','staff') DEFAULT 'staff',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `foto_profile`, `bio`, `no_telp`, `role`, `created_at`) VALUES
(1, 'admin1', 'adminmainpabji', 'Selly Permatasari', NULL, NULL, NULL, 'admin', '2026-04-08 02:01:27'),
(2, 'dosen1', 'mabarepep', 'Ahmad Kurniawan', NULL, NULL, NULL, 'dosen', '2026-04-08 02:01:27'),
(3, 'dosen', 'dosen123', 'dosen', NULL, 'Dosen tetap Fakultas Teknik', '08123456788', 'dosen', '2026-04-08 06:41:53'),
(4, 'admin', 'admin123', '	admin', NULL, 'Administrator sistem akademik', '08123456780', 'admin', '2026-04-08 06:41:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `matkul`
--
ALTER TABLE `matkul`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode`);

--
-- Indexes for table `mhs`
--
ALTER TABLE `mhs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nim` (`nim`);

--
-- Indexes for table `nilai`
--
ALTER TABLE `nilai`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mhs_id` (`mhs_id`),
  ADD KEY `matkul_id` (`matkul_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `berita`
--
ALTER TABLE `berita`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `matkul`
--
ALTER TABLE `matkul`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `mhs`
--
ALTER TABLE `mhs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `nilai`
--
ALTER TABLE `nilai`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `nilai`
--
ALTER TABLE `nilai`
  ADD CONSTRAINT `nilai_ibfk_1` FOREIGN KEY (`mhs_id`) REFERENCES `mhs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nilai_ibfk_2` FOREIGN KEY (`matkul_id`) REFERENCES `matkul` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

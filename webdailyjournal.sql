-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Generation Time: Jan 14, 2026 at 04:03 AM
-- Server version: 8.0.44
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webdailyjournal`
--

-- --------------------------------------------------------

--
-- Table structure for table `article`
--

CREATE TABLE `article` (
  `id` int NOT NULL,
  `judul` text,
  `isi` text,
  `gambar` text,
  `tanggal` datetime DEFAULT NULL,
  `username` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `article`
--

INSERT INTO `article` (`id`, `judul`, `isi`, `gambar`, `tanggal`, `username`) VALUES
(3, 'epoll vs io_uring: Evolusi I/O Asinkron di Linux', 'Performa jaringan adalah fondasi dari sistem terdistribusi dan layanan berskala besar. Linux menawarkan mekanisme io_uring menghadirkan model ring buffer yang memotong banyak context switching dan syscall overhead—memberikan peningkatan throughput signifikan untuk server modern.', '20251217205224_02e1d728.png', '2025-12-10 17:06:36', 'admin'),
(11, 'Analisis Malware Linux: Teknik Packing, Syscall Injection, dan Hooking', 'Di dunia Linux modern, ancaman tidak lagi sebatas brute-force atau skrip sederhana. Banyak malware mengadopsi teknik low level seperti packing custom, ELF injection, hingga hooking syscall (intercept / func proxy) untuk mengaburkan jejak dan mendapatkan persistensi.', '20251217204740_f49670a0.webp', '2025-12-17 13:47:40', 'admin'),
(12, 'Linux 6.17-rc5 has been released', 'Things continue to look pretty normal. Thanks to everyone who made last week go well. Please continue testing and report any issues you find!\r\nNote from Linus, stop using automated tools to add a \"Link:\" tag in the commit message.', '20251217204855_6af9f600.jpg', '2025-12-17 13:48:55', 'admin'),
(13, 'Aplikasi Kriptografi ChaCha20 & CFB-like chaining berbasis Rust & Tauri', 'Pengembangan aplikasi enkripsi/dekripsi untuk format video dengan algoritma ChaCha20 dan CFB-like chaining, dengan pemrosesan multi-threaded yang dapat menghasilkan throughput sangat tinggi/high performance.', '20251217205751_5ecf71a6.png', '2025-12-17 13:57:51', 'admin'),
(26, 'Test', 'Hello World', '', '2025-12-24 16:36:18', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int NOT NULL,
  `judul` text COLLATE utf8mb4_general_ci,
  `deskripsi` text COLLATE utf8mb4_general_ci,
  `gambar` text COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` datetime DEFAULT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `judul`, `deskripsi`, `gambar`, `tanggal`, `username`) VALUES
(4, '', 'Kernel 6.x Rootkit', '20260114100902_5bae5973.png', '2026-01-14 03:09:02', 'admin'),
(5, '', 'RE dan Security dalam Game', '20260114100935_0f6988bb.jpg', '2026-01-14 03:09:35', 'admin'),
(6, '', 'Test1', '20260114101035_046b00e7.jpg', '2026-01-14 03:10:35', 'admin'),
(7, '', 'Test2', '20260114101136_45908e2f.jpg', '2026-01-14 03:11:36', 'admin'),
(8, '', 'Koleksi Lagu', '20260114101202_9635ad04.jpg', '2026-01-14 03:12:02', 'admin'),
(10, '', '', '20260114103325_846fa39a.jpg', '2026-01-14 03:33:25', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` text NOT NULL,
  `foto` text NOT NULL,
  `role` enum('superadmin','admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `foto`, `role`) VALUES
(1, 'admin', '344af1d56d3f5cfcdb2da799ca91de60597c76378d19c1dc6a3cd5f1e866cff4', '', 'superadmin'),
(3, 'danny', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', '20260114095929_77600f30.jpg', 'admin'),
(4, 'budi', 'e8979d2eb704c94fa2fa5044edba1c29232526eec3965ffc64308b6783f2de12', '', 'user'),
(5, 'siti', '71c6e47969179c1e831fcf41f4979a3557290a65d7925e6760cfd316389f0729', '', 'user'),
(6, 'andi', 'a589ffa7732ffd2f26d23953e26af5c8f6c006690b7982d5f07f671915c0b561', '', 'user'),
(7, 'dewi', '452471a1b359ca017ed315f3e673d2a45210c26f62d06798b60c9eceae680165', '', 'user'),
(8, 'rudi', '3ad4e3ca913054dc0f23bbdd52ac8a34f63b381b92d904ccea8b908997da7104', '', 'user'),
(9, 'adminbaru', 'ef797c8118f02dfb649607dd5d3f8c7623048c9c063d532cc95c5ed7a898a64f', '', 'admin'),
(10, 'test12', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', '', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `article`
--
ALTER TABLE `article`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql303.infinityfree.com
-- Generation Time: Jan 04, 2026 at 06:51 AM
-- Server version: 11.4.9-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_40772775_webdailyjournal`
--

-- --------------------------------------------------------

--
-- Table structure for table `article`
--

CREATE TABLE `article` (
  `id` int(11) NOT NULL,
  `judul` text DEFAULT NULL,
  `isi` text DEFAULT NULL,
  `gambar` text DEFAULT NULL,
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
(26, 'Test', 'Hello World', '', '2025-12-24 16:36:18', 'admin'),
(43, 'Singularity: Deep Dive into a Modern Stealth Linux Kernel Rootkit', 'Singularity is a Loadable Kernel Module (LKM) rootkit developed for Linux 6.x kernels that demonstrates advanced evasion and persistence techniques.  This article shows its architecture, from the ftrace-based hooking infrastructure to the anti-forensics mechanisms, offering insights for both security researchers and defenders who need to detect and mitigate these threats.', '20260104184914_5c183455.png', '2026-01-04 03:49:14', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` text NOT NULL,
  `foto` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `foto`) VALUES
(1, 'admin', '344af1d56d3f5cfcdb2da799ca91de60597c76378d19c1dc6a3cd5f1e866cff4', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `article`
--
ALTER TABLE `article`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE IF NOT EXISTS `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` text DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` text NOT NULL,
  `tanggal` datetime DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

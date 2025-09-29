-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 26 Sep 2025 pada 09.26
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory.`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang`
--

CREATE TABLE `barang` (
  `id` int(11) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `status` enum('tersedia','dipinjam') NOT NULL DEFAULT 'tersedia',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `barang`
--

INSERT INTO `barang` (`id`, `nama_barang`, `gambar`, `stok`, `status`, `created_at`) VALUES
(2, 'qq', '', 2, 'tersedia', '2025-09-24 02:56:37'),
(3, 'ee', '1758682610_Screenshot_2024-04-08_112542.png', 2, 'tersedia', '2025-09-24 02:56:50'),
(4, 'rr', '1758682637_Screenshot_2024-04-08_113319.png', 4, 'dipinjam', '2025-09-24 02:57:17'),
(5, 'aa', '', 3, 'tersedia', '2025-09-24 03:04:46'),
(6, 'rr', '', 3, 'dipinjam', '2025-09-24 06:44:50'),
(7, 'RR', '1758764970_Screenshot_2025-06-17_113011.png', 36, 'dipinjam', '2025-09-25 01:49:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `nama_peminjam` varchar(100) NOT NULL,
  `qty_pinjam` int(11) NOT NULL,
  `tgl_pinjam` date NOT NULL,
  `catatan` text DEFAULT NULL,
  `status` enum('dipinjam','dikembalikan') DEFAULT 'dipinjam',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `peminjaman`
--

INSERT INTO `peminjaman` (`id`, `barang_id`, `nama_peminjam`, `qty_pinjam`, `tgl_pinjam`, `catatan`, `status`, `created_at`) VALUES
(2, 4, 'Admin', 2, '2025-09-24', '', 'dipinjam', '2025-09-24 02:57:17'),
(3, 2, 'acc', 1, '2025-09-24', '', 'dipinjam', '2025-09-24 03:00:12'),
(4, 3, 'add', 1, '2025-09-24', 'QC', 'dipinjam', '2025-09-24 03:00:36'),
(5, 5, 'yy', 0, '2025-08-24', 'q', 'dikembalikan', '2025-09-24 03:05:13'),
(6, 5, 'yy', 0, '2025-08-24', '', 'dikembalikan', '2025-09-24 03:06:56'),
(7, 2, 'q', 0, '2025-10-24', '', 'dikembalikan', '2025-09-24 03:10:38'),
(8, 6, 'Admin', 3, '2025-09-24', '', 'dipinjam', '2025-09-24 06:44:50'),
(9, 7, 'Admin', 30, '2025-09-25', '', 'dipinjam', '2025-09-25 01:49:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengembalian`
--

CREATE TABLE `pengembalian` (
  `id` int(11) NOT NULL,
  `peminjaman_id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `nama_peminjam` varchar(100) NOT NULL,
  `qty_kembali` int(11) NOT NULL,
  `tgl_pinjam` date NOT NULL,
  `tgl_pengembalian` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengembalian`
--

INSERT INTO `pengembalian` (`id`, `peminjaman_id`, `barang_id`, `nama_peminjam`, `qty_kembali`, `tgl_pinjam`, `tgl_pengembalian`, `created_at`) VALUES
(1, 2, 4, 'Admin', 1, '2025-09-24', '2025-09-24', '2025-09-24 02:59:25'),
(3, 5, 5, 'yy', 1, '2025-08-24', '2025-09-24', '2025-09-24 03:05:58'),
(4, 6, 5, 'yy', 1, '2025-08-24', '2025-09-24', '2025-09-24 03:07:23'),
(5, 6, 5, 'yy', 1, '2025-08-24', '2025-09-24', '2025-09-24 03:07:54'),
(6, 5, 5, 'yy', 1, '2025-08-24', '2025-09-24', '2025-09-24 03:09:55'),
(7, 7, 2, 'q', 1, '2025-10-24', '2025-09-24', '2025-09-24 03:11:07'),
(8, 7, 2, 'q', 1, '2025-10-24', '2025-09-24', '2025-09-24 03:34:04'),
(11, 9, 7, 'Admin', 3, '2025-09-25', '2025-09-25', '2025-09-25 01:49:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'Muhamad', '$2y$10$f0Xt8PeTkQgmIahvI2Qf0Ox8oi/2UAcfn4RBx1fWSGRNGcO44wfR2', 'admin', '2025-09-24 03:03:05'),
(2, 'tt', '$2y$10$N7x5k299Zf/Ikk7yg/Bua./nzpcHOOaBHeaZREbfB85Hs.F2PV64K', 'user', '2025-09-24 03:03:10'),
(3, 'abdul@gmail.com', '$2y$10$bMoDqDt9zRgEZd6rTj7se.aFyjbcTM5QMjigdg67tABVwVWh.Vfzi', 'user', '2025-09-25 02:14:06'),
(4, 'qquen@gmail.com', '$2y$10$yTNFfaYzHe6NBcBPPPq/zeiQ4/mbxxVCnXYDpcD/6m5A5ruZ2PEai', 'user', '2025-09-25 02:14:34');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barang_id` (`barang_id`);

--
-- Indeks untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `peminjaman_id` (`peminjaman_id`),
  ADD KEY `barang_id` (`barang_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD CONSTRAINT `pengembalian_ibfk_1` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjaman` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengembalian_ibfk_2` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

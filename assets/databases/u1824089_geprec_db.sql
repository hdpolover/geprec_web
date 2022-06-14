-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 14, 2022 at 11:08 PM
-- Server version: 10.5.15-MariaDB-cll-lve
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u1824089_geprec_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(250) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `foto_admin` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `nama`, `foto_admin`) VALUES
(1, 'admin_geprec', '123admin456', 'admin A', 'default.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `daftar_kunjungan`
--

CREATE TABLE `daftar_kunjungan` (
  `id_daftar_kunjungan` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `id_kunjungan` int(11) NOT NULL,
  `tgl_ditambahkan` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `daftar_kunjungan`
--

INSERT INTO `daftar_kunjungan` (`id_daftar_kunjungan`, `id_pengguna`, `id_kunjungan`, `tgl_ditambahkan`) VALUES
(1, 1, 1, '2022-06-14 23:03:21');

-- --------------------------------------------------------

--
-- Table structure for table `kunjungan`
--

CREATE TABLE `kunjungan` (
  `id_kunjungan` int(11) NOT NULL,
  `nomor_pelanggan` varchar(100) NOT NULL,
  `nomor_meteran` varchar(100) NOT NULL,
  `nama_kunjungan` varchar(100) NOT NULL,
  `alamat` varchar(500) NOT NULL,
  `catatan` varchar(500) NOT NULL,
  `latitude` varchar(100) NOT NULL,
  `longitude` varchar(100) NOT NULL,
  `foto_kunjungan` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kunjungan`
--

INSERT INTO `kunjungan` (`id_kunjungan`, `nomor_pelanggan`, `nomor_meteran`, `nama_kunjungan`, `alamat`, `catatan`, `latitude`, `longitude`, `foto_kunjungan`) VALUES
(1, '765373863', '2672672', 'Rumah ABC', 'Jl. Nusantara No. 1 Karang besuki, Malang, Jawa Timur', 'Rumah warna hijau dekat gang', '-7.973006', '112.6079458', 'foto_1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `foto_pengguna` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `username`, `password`, `nama`, `foto_pengguna`) VALUES
(1, 'pengguna1', 'pengguna123@', 'Pengguna 1', 'default.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_kunjungan`
--

CREATE TABLE `riwayat_kunjungan` (
  `id_riwayat_kunjungan` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `id_kunjungan` int(11) NOT NULL,
  `foto_meteran` varchar(250) NOT NULL,
  `foto_selfie` varchar(250) NOT NULL,
  `id_gas_pelanggan` varchar(100) NOT NULL,
  `pembacaan_meter` varchar(250) NOT NULL,
  `tgl_kunjungan` datetime NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `riwayat_kunjungan`
--

INSERT INTO `riwayat_kunjungan` (`id_riwayat_kunjungan`, `id_pengguna`, `id_kunjungan`, `foto_meteran`, `foto_selfie`, `id_gas_pelanggan`, `pembacaan_meter`, `tgl_kunjungan`, `status`) VALUES
(1, 1, 1, 'foto_meteran_1.jpg', 'selfie_1.jpg', 'HGY1223', '787', '2022-06-15 23:03:49', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `daftar_kunjungan`
--
ALTER TABLE `daftar_kunjungan`
  ADD PRIMARY KEY (`id_daftar_kunjungan`),
  ADD KEY `fk_daftar_id_pengguna` (`id_pengguna`),
  ADD KEY `fk_daftar_id_kunjungan` (`id_kunjungan`);

--
-- Indexes for table `kunjungan`
--
ALTER TABLE `kunjungan`
  ADD PRIMARY KEY (`id_kunjungan`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`);

--
-- Indexes for table `riwayat_kunjungan`
--
ALTER TABLE `riwayat_kunjungan`
  ADD PRIMARY KEY (`id_riwayat_kunjungan`),
  ADD KEY `fk_id_pengguna` (`id_pengguna`),
  ADD KEY `fk_id_kunjungan` (`id_kunjungan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `daftar_kunjungan`
--
ALTER TABLE `daftar_kunjungan`
  MODIFY `id_daftar_kunjungan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kunjungan`
--
ALTER TABLE `kunjungan`
  MODIFY `id_kunjungan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `riwayat_kunjungan`
--
ALTER TABLE `riwayat_kunjungan`
  MODIFY `id_riwayat_kunjungan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `daftar_kunjungan`
--
ALTER TABLE `daftar_kunjungan`
  ADD CONSTRAINT `fk_daftar_id_kunjungan` FOREIGN KEY (`id_kunjungan`) REFERENCES `kunjungan` (`id_kunjungan`),
  ADD CONSTRAINT `fk_daftar_id_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`);

--
-- Constraints for table `riwayat_kunjungan`
--
ALTER TABLE `riwayat_kunjungan`
  ADD CONSTRAINT `fk_id_kunjungan` FOREIGN KEY (`id_kunjungan`) REFERENCES `kunjungan` (`id_kunjungan`),
  ADD CONSTRAINT `fk_id_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

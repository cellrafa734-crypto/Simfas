-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 11, 2026 at 08:40 AM
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
-- Database: `simfas`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mahasiswa',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Test User', 'test@example.com', 'mahasiswa', '2026-11-09 23:36:49', '$2y$12$nV.fHSnQgBwYZl5pWAka2eZdHiKrJPl3Kkojhx04SW5Q/XMoNbPHm', 'MkrjilrHrc', '2026-11-09 23:36:49', '2026-11-09 23:36:49'),
(2, 'Admin SIMFAS', 'admin@simfas.test', 'admin', NULL, '$2y$12$rmm9JesyM.4wmkABOTcJDePLPLPgG6FsvGsmsmVQ/Rln/44CR4Leu', NULL, '2026-11-09 23:49:11', '2026-11-09 23:49:11'),
(3, 'Mahasiswa Demo', 'mahasiswa@simfas.test', 'mahasiswa', NULL, '$2y$12$WI7UCGYIM6JS5j39c0LWdenAnV2m7e/PJu14oqeF/5SDqv77j7qoe', NULL, '2026-11-09 23:49:11', '2026-11-09 23:49:11'),
(4, 'BEM Demo', 'organisasi@simfas.test', 'organisasi', NULL, '$2y$12$37PQrzucxSkuElqekOVYu.3cBYQWzkeWKvZaTO5dki439AeLEpl.O', NULL, '2026-11-09 23:49:11', '2026-11-09 23:49:11'),
(5, 'Petugas Demo', 'petugas@simfas.test', 'petugas', NULL, '$2y$12$gL1LjsWJFpyQovzCNL9J6eRAaR4KkRtu4hw3oBqqWHxOlo18ePgAW', NULL, '2026-11-09 23:49:11', '2026-11-09 23:49:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_index` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

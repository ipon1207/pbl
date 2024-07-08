-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2024-07-08 02:02:56
-- サーバのバージョン： 10.4.32-MariaDB
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `pbl_db`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `accounts`
--

CREATE TABLE `accounts` (
  `id` int(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `attribute` int(1) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `accounts`
--

INSERT INTO `accounts` (`id`, `username`, `password`, `attribute`, `email`) VALUES
(1, 'test', '$2y$10$23g4EdEDqf9o06WWUBmeBefc3iCHLHofJts2EhIVZ.ZX2CJkkkiT2', 0, 'test@test.email'),
(2, 'test1', '$2y$10$.0Zyj3eLZzCQZ7scQnzox.k2i5BFm6yf9PwzRusxsx1mTBG47LZhi', 0, 'test1@test1.email.com'),
(3, 'test2', '$2y$10$i5UTRlBlPBndwjbKKrxbRuXcCw2cp.2.GUmtWuUmTLbGjDReTXJ2i', 1, 'test2@test2.email.com'),
(4, 'parent1', '$2y$10$jCNwhBR3rwAPB3uEWTx90.abktcsdu82Tx0Ueom9rUiYTxjhW9aGW', 3, 'parent1@parent1.email'),
(5, 'teacher1', '$2y$10$O21y6/.L3XQsA0ng4eLKT.3PTeOQloTfQR8.aD0B34W7UZ9vXpPQS', 1, 'teacher1@teacher1');

-- --------------------------------------------------------

--
-- テーブルの構造 `appointments`
--

CREATE TABLE `appointments` (
  `id` int(10) NOT NULL,
  `parent_username` varchar(50) NOT NULL,
  `teacher_username` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `appointments`
--

INSERT INTO `appointments` (`id`, `parent_username`, `teacher_username`, `date`, `time`, `status`) VALUES
(1, 'parent1', 'tec', '0000-00-00', '00:00:00', 'pending');

-- --------------------------------------------------------

--
-- テーブルの構造 `schedules`
--

CREATE TABLE `schedules` (
  `id` int(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `event` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `schedules`
--

INSERT INTO `schedules` (`id`, `username`, `event`, `date`, `time`) VALUES
(1, 'test', '運動会', '2024-07-09', '07:00:00'),
(2, 'test', '文化祭', '2024-09-18', '08:00:00'),
(3, 'test1', '運動会', '2024-07-15', '15:19:00'),
(4, 'teacher', '面談', '2024-07-09', '18:41:46');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- テーブルの AUTO_INCREMENT `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- テーブルの AUTO_INCREMENT `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

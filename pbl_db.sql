-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 21, 2024 at 03:50 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pbl_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `attribute` int(1) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `username`, `password`, `attribute`, `email`) VALUES
(1, 'test', '$2y$10$23g4EdEDqf9o06WWUBmeBefc3iCHLHofJts2EhIVZ.ZX2CJkkkiT2', 1, 'test@test.email'),
(2, 'test1', '$2y$10$.0Zyj3eLZzCQZ7scQnzox.k2i5BFm6yf9PwzRusxsx1mTBG47LZhi', 0, 'test1@test1.email.com'),
(3, 'test2', '$2y$10$i5UTRlBlPBndwjbKKrxbRuXcCw2cp.2.GUmtWuUmTLbGjDReTXJ2i', 1, 'test2@test2.email.com'),
(4, 'parent1', '$2y$10$jCNwhBR3rwAPB3uEWTx90.abktcsdu82Tx0Ueom9rUiYTxjhW9aGW', 3, 'parent1@parent1.email'),
(5, 'teacher1', '$2y$10$O21y6/.L3XQsA0ng4eLKT.3PTeOQloTfQR8.aD0B34W7UZ9vXpPQS', 1, 'teacher1@teacher1'),
(6, 'teacher', '$2y$10$Kj68KwNhIsSENhcHDTWkh.WCJJw6PP0MtZoZ5Pb1GVrFkN5yyu4ji', 1, 'teacher@teacher.com'),
(7, 'student', '$2y$10$7fClxBwlmWoa0ONKHChCi.n/zzyF7dLrxybnF79ES4e3Byof4YIkq', 2, 'student@student'),
(8, 'student01', '$2y$10$1rXOAeczkkB5GbjN2XnRg.6GqUHeum62iqysFSQkOCRINzkLr27ai', 2, 'student01@student01'),
(9, 'student02', '$2y$10$9SlwxkKyJAQFeF/8ws4eW.3YgTyVTYJ9KLIdbDzivlTJrZ0xfBE1y', 2, 'student02@student02'),
(10, 'parent', '$2y$10$qrE.gBTYZt1eamO7cnH2eu9hRjpk5fmb5jr8ciZOmRdhirXtoSVGy', 3, 'parent@parent');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
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
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `parent_username`, `teacher_username`, `date`, `time`, `status`) VALUES
(1, 'parent1', 'tec', '0000-00-00', '00:00:00', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `event` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `username`, `event`, `date`, `time`) VALUES
(1, 'test', 'kjno', '2024-07-09', '07:00:00'),
(2, 'test', '文化祭', '2024-09-18', '08:00:00'),
(3, 'test1', '運動会', '2024-07-15', '15:19:00'),
(4, 'teacher', '面談', '2024-07-09', '18:41:46');

CREATE TABLE `chat` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `time` datetime NOT NULL,
  `teacher` varchar(50) NOT NULL,
  `student` varchar(50) NOT NULL,
  `chat` varchar(216) NOT NULL,
  `attribute` int(1) NOT NULL, -- 0なら生徒、1なら保護者に
  `sent_user` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS parents_students (
    parent_id INT NOT NULL,
    student_id INT NOT NULL,
    PRIMARY KEY (parent_id, student_id),
    FOREIGN KEY (parent_id) REFERENCES accounts(id),
    FOREIGN KEY (student_id) REFERENCES accounts(id)
);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2023-01-23 15:43:12
-- サーバのバージョン： 10.4.27-MariaDB
-- PHP のバージョン: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `shift`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `days_closed`
--

CREATE TABLE `days_closed` (
  `days_closed` date NOT NULL,
  `d_name` varchar(15) NOT NULL,
  `delete_flag` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `edit_shift_date`
--

CREATE TABLE `edit_shift_date` (
  `id` int(11) NOT NULL,
  `staff_id` int(5) NOT NULL,
  `name` varchar(11) NOT NULL,
  `position` varchar(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` varchar(5) NOT NULL,
  `ending_time` varchar(5) NOT NULL,
  `delete_flag` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `manager`
--

CREATE TABLE `manager` (
  `id` int(5) NOT NULL,
  `staff_name` varchar(15) NOT NULL,
  `password` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `manager`
--

INSERT INTO `manager` (`id`, `staff_name`, `password`) VALUES
(99999, '管理者', '00000000a');

-- --------------------------------------------------------

--
-- テーブルの構造 `original_shift_date`
--

CREATE TABLE `original_shift_date` (
  `shift_id` int(11) NOT NULL,
  `staff_id` int(5) NOT NULL,
  `position` varchar(10) NOT NULL,
  `name` varchar(15) NOT NULL,
  `name_y` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `start_time` varchar(5) NOT NULL,
  `ending_time` varchar(5) NOT NULL,
  `add_flag` int(1) NOT NULL,
  `change_t` varchar(12) DEFAULT NULL,
  `delete_r` varchar(15) NOT NULL,
  `delete_flag` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `release_date`
--

CREATE TABLE `release_date` (
  `shift_date` varchar(7) NOT NULL,
  `release_date` date NOT NULL,
  `update_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `release_shift`
--

CREATE TABLE `release_shift` (
  `id` int(11) NOT NULL,
  `staff_id` int(5) NOT NULL,
  `name` varchar(11) NOT NULL,
  `position` varchar(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` varchar(5) NOT NULL,
  `ending_time` varchar(5) NOT NULL,
  `delete_flag` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `shift_date`
--

CREATE TABLE `shift_date` (
  `shift_id` int(11) NOT NULL,
  `staff_id` int(5) NOT NULL,
  `position` varchar(10) NOT NULL,
  `name` varchar(15) NOT NULL,
  `name_y` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `start_time` varchar(5) NOT NULL,
  `ending_time` varchar(5) NOT NULL,
  `delete_flag` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `shift_deadline`
--

CREATE TABLE `shift_deadline` (
  `deadline_month` varchar(7) NOT NULL,
  `deadline_date` date NOT NULL,
  `delete_flag` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `staff`
--

CREATE TABLE `staff` (
  `id` int(5) NOT NULL,
  `position` varchar(8) NOT NULL,
  `staff_name` varchar(15) NOT NULL,
  `staff_name_y` varchar(20) NOT NULL,
  `password` varchar(15) NOT NULL,
  `delete_flag` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `staff_shift`
--

CREATE TABLE `staff_shift` (
  `shift_id` int(11) NOT NULL,
  `staff_id` int(5) NOT NULL,
  `position` varchar(5) NOT NULL,
  `staff_name` varchar(13) NOT NULL,
  `date` date NOT NULL,
  `start_time` varchar(5) NOT NULL,
  `ending_time` varchar(5) NOT NULL,
  `delete_flag` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `edit_shift_date`
--
ALTER TABLE `edit_shift_date`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `original_shift_date`
--
ALTER TABLE `original_shift_date`
  ADD PRIMARY KEY (`shift_id`);

--
-- テーブルのインデックス `shift_date`
--
ALTER TABLE `shift_date`
  ADD PRIMARY KEY (`shift_id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `edit_shift_date`
--
ALTER TABLE `edit_shift_date`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- テーブルの AUTO_INCREMENT `original_shift_date`
--
ALTER TABLE `original_shift_date`
  MODIFY `shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- テーブルの AUTO_INCREMENT `shift_date`
--
ALTER TABLE `shift_date`
  MODIFY `shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

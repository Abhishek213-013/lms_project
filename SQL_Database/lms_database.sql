-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2025 at 08:02 AM
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
-- Database: `lms_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `points` int(11) NOT NULL DEFAULT 100,
  `due_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('draft','active','completed','archived') NOT NULL DEFAULT 'active',
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `grade` int(11) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `capacity` int(11) NOT NULL DEFAULT 30,
  `type` enum('regular','other') NOT NULL DEFAULT 'regular',
  `category` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'Main image for the course/class',
  `thumbnail` varchar(255) DEFAULT NULL COMMENT 'Thumbnail version for better performance',
  `image_alt` varchar(255) DEFAULT NULL COMMENT 'Alt text for accessibility',
  `image_caption` varchar(255) DEFAULT NULL COMMENT 'Optional image caption',
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `schedule` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`schedule`)),
  `status` enum('active','inactive','upcoming') NOT NULL DEFAULT 'active',
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete support'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`, `grade`, `subject`, `code`, `capacity`, `type`, `category`, `image`, `thumbnail`, `image_alt`, `image_caption`, `teacher_id`, `schedule`, `status`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Class 1', 1, 'Bangla', 'BAN', 30, 'regular', NULL, 'courses/images/subject_bangla_1_1762667007_4665.jpg', NULL, 'Class 1 image', 'Class 1 - Bangla', 3, NULL, 'active', NULL, '2025-11-01 10:50:54', '2025-11-08 23:43:27', NULL),
(2, 'Class 1', 1, 'English', 'ENG', 30, 'regular', NULL, 'courses/images/subject_English_1762667180_5020.jpg', NULL, 'Class 1 image', 'Class 1 - English', 4, NULL, 'active', NULL, '2025-11-01 10:50:54', '2025-11-08 23:46:20', NULL),
(3, 'Class 1', 1, 'Mathematics', 'MATH', 30, 'regular', NULL, 'courses/images/subject_Math_1762667193_6175.jpg', NULL, 'Class 1 image', 'Class 1 - Mathematics', 5, NULL, 'active', NULL, '2025-11-01 10:50:54', '2025-11-08 23:46:33', NULL),
(4, 'Class 1', 1, 'Science', 'SCI', 30, 'regular', NULL, 'courses/images/subject_Science_1762667204_4113.jpg', NULL, 'Class 1 image', 'Class 1 - Science', 6, NULL, 'active', NULL, '2025-11-01 10:50:54', '2025-11-08 23:46:44', NULL),
(5, 'Class 1', 1, 'History', 'HIST', 30, 'regular', NULL, 'courses/images/subject_History_1762667216_3864.jpg', NULL, 'Class 1 image', 'Class 1 - History', 8, NULL, 'active', NULL, '2025-11-01 10:50:54', '2025-11-08 23:46:56', NULL),
(6, 'Class 1', 1, 'ICT', 'ICT', 30, 'regular', NULL, 'courses/images/subject_ICT_1762667224_3294.jpg', NULL, 'Class 1 image', 'Class 1 - ICT', 10, NULL, 'active', NULL, '2025-11-01 10:50:54', '2025-11-08 23:47:04', NULL),
(7, 'Class 2', 2, 'Bangla', 'BAN', 30, 'regular', 'Primary', 'courses/images/subject_Untitled_1762667299_3779.jpg', NULL, 'Class 2 image', 'Class 2 - Bangla', 3, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-08 23:48:19', NULL),
(8, 'Class 2', 2, 'English', 'ENG', 30, 'regular', 'Primary', 'courses/images/subject_English_1762667310_6289.jpg', NULL, 'Class 2 image', 'Class 2 - English', 4, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-08 23:48:30', NULL),
(9, 'Class 2', 2, 'Mathematics', 'MATH', 30, 'regular', 'Primary', 'courses/images/subject_Math_1762667321_1231.jpg', NULL, 'Class 2 image', 'Class 2 - Mathematics', 5, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-08 23:48:41', NULL),
(10, 'Class 2', 2, 'Science', 'SCI', 30, 'regular', 'Primary', 'courses/images/subject_Science_1762667332_7464.jpg', NULL, 'Class 2 image', 'Class 2 - Science', 6, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-08 23:48:52', NULL),
(11, 'Class 2', 2, 'History', 'HIST', 30, 'regular', 'Primary', 'courses/images/subject_History_1762667340_2866.jpg', NULL, 'Class 2 image', 'Class 2 - History', 8, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-08 23:49:00', NULL),
(12, 'Class 2', 2, 'ICT', 'ICT', 30, 'regular', 'Primary', 'courses/images/subject_ICT_1762667346_7573.jpg', NULL, 'Class 2 image', 'Class 2 - ICT', 10, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-08 23:49:06', NULL),
(13, 'Class 3', 3, 'Bangla', 'BAN', 30, 'regular', 'Primary', 'courses/images/subject_Untitled_1762667369_9174.jpg', NULL, 'Class 3 image', 'Class 3 - Bangla', 3, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-08 23:49:29', NULL),
(14, 'Class 3', 3, 'English', 'ENG', 30, 'regular', 'Primary', 'courses/images/subject_English_1762667377_4559.jpg', NULL, 'Class 3 image', 'Class 3 - English', 4, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-08 23:49:38', NULL),
(15, 'Class 3', 3, 'Mathematics', 'MATH', 30, 'regular', 'Primary', 'courses/images/subject_Math_1762667385_1861.jpg', NULL, 'Class 3 image', 'Class 3 - Mathematics', 5, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-08 23:49:45', NULL),
(16, 'Class 3', 3, 'Science', 'SCI', 30, 'regular', 'Primary', 'courses/images/subject_Science_1762667398_9388.jpg', NULL, 'Class 3 image', 'Class 3 - Science', 6, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-08 23:49:58', NULL),
(17, 'Class 3', 3, 'History', 'HIST', 30, 'regular', 'Primary', 'courses/images/subject_History_1762667406_3830.jpg', NULL, 'Class 3 image', 'Class 3 - History', 8, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-08 23:50:06', NULL),
(18, 'Class 3', 3, 'ICT', 'ICT', 30, 'regular', 'Primary', 'courses/images/subject_ICT_1762667414_3962.jpg', NULL, 'Class 3 image', 'Class 3 - ICT', 10, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-08 23:50:14', NULL),
(19, 'Class 4', 4, 'Bangla', 'BAN', 35, 'regular', 'Elementary', 'courses/images/subject_Untitled_1762667655_2123.jpg', NULL, 'Class 4 image', 'Class 4 - Bangla', 3, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-08 23:54:15', NULL),
(20, 'Class 4', 4, 'English', 'ENG', 35, 'regular', 'Elementary', 'courses/images/subject_English_1762667670_6068.jpg', NULL, 'Class 4 image', 'Class 4 - English', 4, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-08 23:54:30', NULL),
(21, 'Class 4', 4, 'Mathematics', 'MATH', 35, 'regular', 'Elementary', 'courses/images/subject_Math_1762667689_2923.jpg', NULL, 'Class 4 image', 'Class 4 - Mathematics', 5, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-08 23:54:49', NULL),
(22, 'Class 4', 4, 'Science', 'SCI', 35, 'regular', 'Elementary', 'courses/images/subject_Science_1762667802_7851.jpg', NULL, 'Class 4 image', 'Class 4 - Science', 6, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-08 23:56:42', NULL),
(23, 'Class 4', 4, 'History', 'HIST', 35, 'regular', 'Elementary', 'courses/images/subject_History_1762667811_2859.jpg', NULL, 'Class 4 image', 'Class 4 - History', 8, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-08 23:56:51', NULL),
(24, 'Class 4', 4, 'ICT', 'ICT', 35, 'regular', 'Elementary', 'courses/images/subject_ICT_1762667819_9607.jpg', NULL, 'Class 4 image', 'Class 4 - ICT', 10, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-08 23:56:59', NULL),
(25, 'Class 5', 5, 'Bangla', 'BAN', 35, 'regular', 'Elementary', 'courses/images/subject_Untitled_1762667939_4332.jpg', NULL, 'Class 5 image', 'Class 5 - Bangla', 3, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-08 23:58:59', NULL),
(26, 'Class 5', 5, 'English', 'ENG', 35, 'regular', 'Elementary', 'courses/images/subject_English_1762667960_3704.jpg', NULL, 'Class 5 image', 'Class 5 - English', 4, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-08 23:59:20', NULL),
(27, 'Class 5', 5, 'Mathematics', 'MATH', 35, 'regular', 'Elementary', 'courses/images/subject_Math_1762669315_5815.jpg', NULL, 'Class 5 image', 'Class 5 - Mathematics', 5, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 00:21:55', NULL),
(28, 'Class 5', 5, 'Science', 'SCI', 35, 'regular', 'Elementary', 'courses/images/subject_Science_1762669348_1410.jpg', NULL, 'Class 5 image', 'Class 5 - Science', 6, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 00:22:28', NULL),
(29, 'Class 5', 5, 'History', 'HIST', 35, 'regular', 'Elementary', 'courses/images/subject_History_1762669388_2065.jpg', NULL, 'Class 5 image', 'Class 5 - History', 8, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 00:23:08', NULL),
(30, 'Class 5', 5, 'ICT', 'ICT', 35, 'regular', 'Elementary', 'courses/images/subject_ICT_1762669396_2573.jpg', NULL, 'Class 5 image', 'Class 5 - ICT', 10, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 00:23:16', NULL),
(31, 'Class 6', 6, 'Bangla', 'BAN', 40, 'regular', 'Middle School', 'courses/images/subject_Untitled_1762670009_1552.jpg', NULL, 'Class 6 image', 'Class 6 - Bangla', 3, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 00:33:30', NULL),
(32, 'Class 6', 6, 'English', 'ENG', 40, 'regular', 'Middle School', 'courses/images/subject_English_1762670044_5419.jpg', NULL, 'Class 6 image', 'Class 6 - English', 4, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 00:34:05', NULL),
(33, 'Class 6', 6, 'Mathematics', 'MATH', 40, 'regular', 'Middle School', 'courses/images/subject_Math_1762670109_3811.jpg', NULL, 'Class 6 image', 'Class 6 - Mathematics', 5, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 00:35:10', NULL),
(34, 'Class 6', 6, 'Science', 'SCI', 40, 'regular', 'Middle School', 'courses/images/subject_Science_1762670129_7512.jpg', NULL, 'Class 6 image', 'Class 6 - Science', 6, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 00:35:29', NULL),
(35, 'Class 6', 6, 'History', 'HIST', 40, 'regular', 'Middle School', 'courses/images/subject_History_1762670152_3886.jpg', NULL, 'Class 6 image', 'Class 6 - History', 8, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 00:35:52', NULL),
(36, 'Class 6', 6, 'ICT', 'ICT', 40, 'regular', 'Middle School', 'courses/images/subject_ICT_1762670181_2725.jpg', NULL, 'Class 6 image', 'Class 6 - ICT', 10, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 00:36:21', NULL),
(37, 'Class 7', 7, 'Bangla', 'BAN', 40, 'regular', 'Middle School', 'courses/images/subject_Untitled_1762672221_6868.jpg', NULL, 'Class 7 image', 'Class 7 - Bangla', 3, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:10:21', NULL),
(38, 'Class 7', 7, 'English', 'ENG', 40, 'regular', 'Middle School', 'courses/images/subject_English_1762672247_7422.jpg', NULL, 'Class 7 image', 'Class 7 - English', 4, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:10:47', NULL),
(39, 'Class 7', 7, 'Mathematics', 'MATH', 40, 'regular', 'Middle School', 'courses/images/subject_Math_1762672260_2922.jpg', NULL, 'Class 7 image', 'Class 7 - Mathematics', 5, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:11:00', NULL),
(40, 'Class 7', 7, 'Science', 'SCI', 40, 'regular', 'Middle School', 'courses/images/subject_Science_1762672276_2824.jpg', NULL, 'Class 7 image', 'Class 7 - Science', 6, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:11:16', NULL),
(41, 'Class 7', 7, 'History', 'HIST', 40, 'regular', 'Middle School', 'courses/images/subject_History_1762672286_5082.jpg', NULL, 'Class 7 image', 'Class 7 - History', 8, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:11:26', NULL),
(42, 'Class 7', 7, 'ICT', 'ICT', 40, 'regular', 'Middle School', 'courses/images/subject_ICT_1762672296_7100.jpg', NULL, 'Class 7 image', 'Class 7 - ICT', 10, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:11:36', NULL),
(43, 'Class 8', 8, 'Bangla', 'BAN', 45, 'regular', 'Middle School', 'courses/images/subject_Untitled_1762672554_4644.jpg', NULL, 'Class 8 image', 'Class 8 - Bangla', 3, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:15:54', NULL),
(44, 'Class 8', 8, 'English', 'ENG', 45, 'regular', 'Middle School', 'courses/images/subject_English_1762672568_3710.jpg', NULL, 'Class 8 image', 'Class 8 - English', 4, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:16:08', NULL),
(45, 'Class 8', 8, 'Mathematics', 'MATH', 45, 'regular', 'Middle School', 'courses/images/subject_Math_1762672610_2248.jpg', NULL, 'Class 8 image', 'Class 8 - Mathematics', 5, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:16:50', NULL),
(46, 'Class 8', 8, 'Science', 'SCI', 45, 'regular', 'Middle School', 'courses/images/subject_Science_1762672630_9309.jpg', NULL, 'Class 8 image', 'Class 8 - Science', 6, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:17:11', NULL),
(47, 'Class 8', 8, 'History', 'HIST', 45, 'regular', 'Middle School', 'courses/images/subject_History_1762672646_9946.jpg', NULL, 'Class 8 image', 'Class 8 - History', 8, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:17:26', NULL),
(48, 'Class 8', 8, 'ICT', 'ICT', 45, 'regular', 'Middle School', 'courses/images/subject_ICT_1762672655_5918.jpg', NULL, 'Class 8 image', 'Class 8 - ICT', 10, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:17:35', NULL),
(49, 'Class 9', 9, 'Bangla', 'BAN', 45, 'regular', 'High School', 'courses/images/subject_Untitled_1762672686_8846.jpg', NULL, 'Class 9 image', 'Class 9 - Bangla', 3, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:18:06', NULL),
(50, 'Class 9', 9, 'English', 'ENG', 45, 'regular', 'High School', 'courses/images/subject_English_1762672712_9124.jpg', NULL, 'Class 9 image', 'Class 9 - English', 4, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:18:32', NULL),
(51, 'Class 9', 9, 'Mathematics', 'MATH', 45, 'regular', 'High School', 'courses/images/subject_Math_1762672732_6251.jpg', NULL, 'Class 9 image', 'Class 9 - Mathematics', 5, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:18:53', NULL),
(52, 'Class 9', 9, 'Science', 'SCI', 45, 'regular', 'High School', 'courses/images/subject_Science_1762672780_9627.jpg', NULL, 'Class 9 image', 'Class 9 - Science', 6, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:19:40', NULL),
(53, 'Class 9', 9, 'History', 'HIST', 45, 'regular', 'High School', 'courses/images/subject_History_1762672793_2667.jpg', NULL, 'Class 9 image', 'Class 9 - History', 8, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:19:53', NULL),
(54, 'Class 9', 9, 'ICT', 'ICT', 45, 'regular', 'High School', 'courses/images/subject_ICT_1762672805_8762.jpg', NULL, 'Class 9 image', 'Class 9 - ICT', 10, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:20:05', NULL),
(55, 'Class 10', 10, 'Bangla', 'BAN', 50, 'regular', 'High School', 'courses/images/subject_Untitled_1762672836_8958.jpg', NULL, 'Class 10 image', 'Class 10 - Bangla', 3, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:20:38', NULL),
(56, 'Class 10', 10, 'English', 'ENG', 50, 'regular', 'High School', 'courses/images/subject_English_1762673239_1780.jpg', NULL, 'Class 10 image', 'Class 10 - English', 4, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:27:19', NULL),
(57, 'Class 10', 10, 'Mathematics', 'MATH', 50, 'regular', 'High School', 'courses/images/subject_Math_1762673253_8176.jpg', NULL, 'Class 10 image', 'Class 10 - Mathematics', 5, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:27:33', NULL),
(58, 'Class 10', 10, 'Science', 'SCI', 50, 'regular', 'High School', 'courses/images/subject_Science_1762673278_8674.jpg', NULL, 'Class 10 image', 'Class 10 - Science', 6, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:27:58', NULL),
(59, 'Class 10', 10, 'History', 'HIST', 50, 'regular', 'High School', 'courses/images/subject_History_1762673287_9192.jpg', NULL, 'Class 10 image', 'Class 10 - History', 8, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:28:07', NULL),
(60, 'Class 10', 10, 'ICT', 'ICT', 50, 'regular', 'High School', 'courses/images/subject_ICT_1762673296_7379.jpg', NULL, 'Class 10 image', 'Class 10 - ICT', 10, NULL, 'active', NULL, '2025-11-01 16:57:03', '2025-11-09 01:28:16', NULL),
(61, 'Class 11', 11, 'Bangla', 'BAN', 30, 'regular', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', '', '2025-11-08 11:29:18', '2025-11-08 21:54:13', '2025-11-08 21:54:13'),
(62, 'Class 11', 11, 'English', 'ENG', 30, 'regular', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', '', '2025-11-08 11:29:19', '2025-11-08 21:54:18', '2025-11-08 21:54:18'),
(63, 'Class 11', 11, 'Mathematics', 'MATH', 30, 'regular', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', '', '2025-11-08 11:29:19', '2025-11-08 21:54:22', '2025-11-08 21:54:22'),
(64, 'Class 11', 11, 'Science', 'SCI', 30, 'regular', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', '', '2025-11-08 11:29:19', '2025-11-08 21:54:25', '2025-11-08 21:54:25'),
(65, 'Class 11', 11, 'History', 'HIS', 30, 'regular', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', '', '2025-11-08 11:29:19', '2025-11-08 21:54:28', '2025-11-08 21:54:28'),
(66, 'Class 11', 11, 'ICT', 'ICT', 30, 'regular', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', '', '2025-11-08 11:29:19', '2025-11-08 21:54:31', '2025-11-08 21:54:31'),
(67, 'Class 12', 12, 'Bangla', 'BAN', 30, 'regular', NULL, 'courses/subject-images/subject_Untitled_1762629033_9241.jpg', NULL, 'Bangla image', 'Bangla - Class 12', NULL, NULL, 'active', '', '2025-11-08 13:10:35', '2025-11-08 21:54:50', '2025-11-08 21:54:50'),
(68, 'Class 11', 11, 'Bangla', 'BAN', 30, 'regular', NULL, 'courses/subject-images/subject_Untitled_1762660756_4708.jpg', NULL, 'Bangla image', 'Bangla - Class 11', 3, NULL, 'active', '', '2025-11-08 21:59:16', '2025-11-09 22:21:37', NULL),
(69, 'Class 11', 11, 'English', 'ENG', 30, 'regular', NULL, 'courses/subject-images/subject_eng_1762660756_6658.jpg', NULL, 'English image', 'English - Class 11', 4, NULL, 'active', '', '2025-11-08 21:59:16', '2025-11-09 22:21:48', NULL),
(70, 'Class 11', 11, 'Mathematics', 'MATH', 30, 'regular', NULL, 'courses/subject-images/subject_math_1762660757_7560.jpg', NULL, 'Mathematics image', 'Mathematics - Class 11', 5, NULL, 'active', '', '2025-11-08 21:59:17', '2025-11-09 22:21:57', NULL),
(71, 'Class 11', 11, 'Science', 'SCI', 30, 'regular', NULL, 'courses/subject-images/subject_science_1762660757_6811.jpg', NULL, 'Science image', 'Science - Class 11', 6, NULL, 'active', '', '2025-11-08 21:59:17', '2025-11-09 22:22:07', NULL),
(72, 'Class 11', 11, 'History', 'HIS', 30, 'regular', NULL, 'courses/subject-images/subject_history_1762660757_2928.jpg', NULL, 'History image', 'History - Class 11', 8, NULL, 'active', '', '2025-11-08 21:59:17', '2025-11-09 22:22:17', NULL),
(73, 'Class 11', 11, 'ICT', 'ICT', 30, 'regular', NULL, 'courses/subject-images/subject_ICT_1762660757_4032.jpg', NULL, 'ICT image', 'ICT - Class 11', 10, NULL, 'active', '', '2025-11-08 21:59:17', '2025-11-09 22:22:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `class_student`
--

CREATE TABLE `class_student` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `content_management`
--

CREATE TABLE `content_management` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `value_bn` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `content_management`
--

INSERT INTO `content_management` (`id`, `key`, `value`, `value_bn`, `created_at`, `updated_at`) VALUES
(1, 'home_hero_title', 'Learning is What You Make of it. Make it Yours at PathShala LMS.', 'শেখাই হলো তোমার জন্য উপকারী। পাঠশালা LMS এর সাহায্যে এটিকে সহজ করে নাও।', '2025-11-03 04:24:49', '2025-11-03 05:08:05'),
(2, 'about_our_story_title', 'Transforming Education Through Innovation', 'উদ্ভাবনের মাধ্যমে শিক্ষার রূপান্তর', '2025-11-03 04:25:25', '2025-11-03 09:46:55'),
(3, 'about_our_story_content', 'Pathshala LMS was founded with a simple yet powerful vision: to make quality education accessible to everyone, everywhere. We believe that learning should be engaging, personalized, and available to all regardless of geographical or financial barriers.', 'পাঠশালা LMS একটি সহজ কিন্তু শক্তিশালী দৃষ্টিভঙ্গি নিয়ে প্রতিষ্ঠিত হয়েছিল: সর্বত্র, সকলের জন্য মানসম্মত শিক্ষা সহজলভ্য করা। আমরা বিশ্বাস করি যে শিক্ষাগ্রহণ আকর্ষণীয়, ব্যক্তিগতকৃত এবং ভৌগোলিক বা আর্থিক বাধা নির্বিশেষে সকলের জন্য উপলব্ধ হওয়া উচিত।', '2025-11-03 04:25:26', '2025-11-03 09:47:48'),
(4, 'about_mission_title', 'Our Mission', 'আমাদের লক্ষ্য', '2025-11-03 04:25:27', '2025-11-03 09:48:21'),
(5, 'about_mission_content', 'To democratize education by providing high-quality, accessible, and affordable learning opportunities that empower individuals to achieve their personal and professional goals.', 'উচ্চমানের, সহজলভ্য এবং সাশ্রয়ী মূল্যের শিক্ষার সুযোগ প্রদানের মাধ্যমে শিক্ষাকে গণতন্ত্রীকরণ করা যা ব্যক্তিদের তাদের ব্যক্তিগত এবং পেশাগত লক্ষ্য অর্জনে সক্ষম করে।', '2025-11-03 04:25:27', '2025-11-03 09:48:38'),
(6, 'about_vision_title', 'Our Vision', 'আমাদের পরিকল্পনা', '2025-11-03 04:25:28', '2025-11-03 09:50:27'),
(7, 'about_vision_content', 'To create a world where anyone, anywhere can transform their life through access to the world\'s best learning experiences and educational resources.', 'এমন একটি পৃথিবী তৈরি করা যেখানে যে কেউ, যে কোনও জায়গা থেকে বিশ্বের সেরা শেখার অভিজ্ঞতা এবং শিক্ষামূলক সম্পদের অ্যাক্সেসের মাধ্যমে তাদের জীবনকে বদলে দিতে পারে।', '2025-11-03 04:25:29', '2025-11-03 09:50:42'),
(8, 'home_hero_subtitle', 'Unlock your potential with our expert-led courses and transform your life.', 'আমাদের বিশেষজ্ঞ-নেতৃত্বাধীন কোর্সগুলির মাধ্যমে আপনার সম্ভাবনাকে উন্মোচন করুন এবং আপনার জীবনকে রূপান্তরিত করুন।', '2025-11-03 04:26:52', '2025-11-03 04:27:22'),
(9, 'home_hero_primary_button', 'Start Free Trial', 'ফ্রি ট্র্যায়াল দিয়ে শুরু করুন', '2025-11-03 04:28:32', '2025-11-03 04:28:42'),
(10, 'home_hero_secondary_button', 'Meet Your Instructors', 'আপনার প্রশিক্ষকদের চিনুন', '2025-11-03 04:29:19', '2025-11-03 04:29:31'),
(11, 'home_courses_title', 'Popular Courses', 'জনপ্রিয় কোর্সেস', '2025-11-03 04:30:58', '2025-11-03 04:31:08'),
(12, 'home_courses_subtitle', 'Discover our most enrolled courses', 'আমাদের সর্বাধিক নথিভুক্ত কোর্সগুলি আবিষ্কার করুন', '2025-11-03 04:31:45', '2025-11-03 04:31:52'),
(13, 'home_courses_button', 'View All Courses', 'সমস্ত কোর্স দেখুন', '2025-11-03 04:32:21', '2025-11-03 04:32:33'),
(14, 'home_instructors_title', 'Meet Our Expert Instructors', 'আমাদের বিশেষজ্ঞ প্রশিক্ষকদের সাথে দেখা করুন', '2025-11-03 04:33:04', '2025-11-03 04:33:20'),
(15, 'home_instructors_subtitle', 'Learn from experienced professionals', 'অভিজ্ঞ পেশাদারদের কাছ থেকে শিখুন', '2025-11-03 04:33:55', '2025-11-03 04:34:54'),
(16, 'home_instructors_button', 'View All Instructors', 'সমস্ত প্রশিক্ষক দেখুন', '2025-11-03 04:35:25', '2025-11-03 04:35:31'),
(17, 'home_stats_title', 'Our Impact', 'আমাদের প্রভাব', '2025-11-03 04:35:39', '2025-11-03 04:35:50'),
(18, 'home_cta_title', 'Are You Ready To Learn?', 'তুমি কি শেখার জন্য প্রস্তুত?', '2025-11-03 04:36:56', '2025-11-03 04:37:05'),
(19, 'home_cta_subtitle', 'Join thousands of students already learning with Pathshala', 'পাঠশালার সাথে ইতিমধ্যেই শিখছেন এমন হাজার হাজার শিক্ষার্থীর সাথে যোগ দিন', '2025-11-03 04:37:40', '2025-11-03 04:37:46'),
(20, 'home_cta_button', 'Get Started', 'শুরু করুন', '2025-11-03 04:37:56', '2025-11-03 04:38:05'),
(21, 'about_banner_title', 'About Pathshala', 'পাঠশালার ব্যাপারে', '2025-11-03 09:45:33', '2025-11-03 09:45:50'),
(22, 'about_banner_description', 'Empowering learners worldwide with quality education and innovative learning solutions', 'বিশ্বব্যাপী শিক্ষার্থীদের মানসম্মত শিক্ষা এবং উদ্ভাবনী শিক্ষণ সমাধানের মাধ্যমে ক্ষমতায়ন করা', '2025-11-03 09:45:53', '2025-11-03 09:46:41'),
(23, 'home_hero_image', 'http://localhost:8000/storage/content-images/home_hero_image_en_1762330496.png', 'http://localhost:8000/storage/content-images/home_hero_image_bn_1762330503.png', '2025-11-05 01:33:47', '2025-11-05 02:15:03');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_10_13_034338_create_personal_access_tokens_table', 1),
(5, '2025_10_13_041348_create_sessions_table', 1),
(6, '2025_10_15_103628_create_classes_table', 1),
(7, '2025_10_15_103629_create_resources_table', 1),
(8, '2025_10_15_103956_add_experience_to_users_table', 1),
(9, '2025_10_15_183619_update_classes_table_add_new_columns', 1),
(10, '2025_10_18_071718_add_email_verified_at_to_users_table', 1),
(11, '2025_10_18_071913_update_users_table_role_enum', 1),
(12, '2025_10_19_035239_create_assignments_table', 1),
(13, '2025_10_19_035300_create_schedules_table', 1),
(14, '2025_10_21_054742_create_students_table', 1),
(15, '2025_10_22_202137_create_class_student_pivot_table', 1),
(16, '2025_10_22_213535_add_thumbnail_path_to_resources_table', 1),
(17, '2025_10_22_223518_add_download_count_to_resources_table', 1),
(18, '2025_10_23_034754_fix_video_resources_content', 1),
(19, '2025_10_25_154950_add_admission_date_to_students_table', 1),
(20, '2025_10_26_192927_add_status_to_users_table', 1),
(21, '2025_10_28_090142_create_content_management_table', 1),
(22, '2025_10_30_072927_add_bio_to_users_table', 1),
(23, '2025_11_01_210704_create_blog_categories_table', 2),
(24, '2025_11_01_210704_create_blog_tags_table', 2),
(25, '2025_11_01_213458_create_simple_blogs_table', 3),
(26, '2025_11_02_030212_create_blogs_table', 4),
(27, '2025_11_02_171119_create_blogs_table', 5),
(28, '2025_11_02_172558_create_blogs_table', 6),
(29, '2025_11_03_102333_create_content_management_table', 7),
(30, '2025_11_05_093337_add_image_fields_to_classes_table', 8),
(31, '2025_11_09_075042_add_profile_picture_to_users_table', 9),
(32, '2025_11_09_181651_add_order_column_to_users_table', 10);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('video','pdf','document','link') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `content` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `download_count` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`id`, `teacher_id`, `class_id`, `type`, `title`, `description`, `content`, `file_path`, `thumbnail_path`, `download_count`, `status`, `created_at`, `updated_at`) VALUES
(1, 10, 6, 'video', 'ICT', 'https://youtu.be/Q7Ryv1M7CvI?si=2N8UBhrAQgqJO1_O', 'https://youtu.be/Q7Ryv1M7CvI?si=2N8UBhrAQgqJO1_O', NULL, 'youtube_Q7Ryv1M7CvI', 0, 'active', '2025-11-01 23:38:13', '2025-11-01 23:38:13'),
(2, 3, 1, 'video', 'Bangla', 'https://youtu.be/lETL9gaEwUA?si=OmSJQinNfI4eYrK0', 'https://youtu.be/lETL9gaEwUA?si=OmSJQinNfI4eYrK0', NULL, 'youtube_lETL9gaEwUA', 0, 'active', '2025-11-01 23:39:36', '2025-11-01 23:39:36'),
(3, 4, 2, 'video', 'English', 'https://youtu.be/henIVlCPVIY?si=CYjK021iXpre_RWH', 'https://youtu.be/henIVlCPVIY?si=CYjK021iXpre_RWH', NULL, 'youtube_henIVlCPVIY', 0, 'active', '2025-11-01 23:41:07', '2025-11-01 23:41:07'),
(4, 5, 3, 'video', 'Math', 'https://youtu.be/STmx4vV5md8?si=Ud9B0zn-vn7-n5Q-', 'https://youtu.be/STmx4vV5md8?si=Ud9B0zn-vn7-n5Q-', NULL, 'youtube_STmx4vV5md8', 0, 'active', '2025-11-01 23:44:37', '2025-11-01 23:44:37'),
(5, 6, 4, 'video', 'Science', 'https://youtu.be/jF7ybsCRfhg?si=iSBPoInA_wyKWulr', 'https://youtu.be/jF7ybsCRfhg?si=iSBPoInA_wyKWulr', NULL, 'youtube_jF7ybsCRfhg', 0, 'active', '2025-11-01 23:46:18', '2025-11-01 23:46:18'),
(6, 8, 5, 'video', 'History', 'https://youtu.be/yqc9zX04DXs?si=52E9lIH6X9AnvJS0', 'https://youtu.be/yqc9zX04DXs?si=52E9lIH6X9AnvJS0', NULL, 'youtube_yqc9zX04DXs', 0, 'active', '2025-11-01 23:47:38', '2025-11-01 23:47:38'),
(7, 3, 1, 'video', 'Bangla 2', 'https://youtu.be/11a5lqCvI5c?si=ToKhC5dnBOT0U6AX', 'https://youtu.be/11a5lqCvI5c?si=ToKhC5dnBOT0U6AX', NULL, 'youtube_11a5lqCvI5c', 0, 'active', '2025-11-03 11:27:21', '2025-11-03 11:27:21'),
(8, 3, 7, 'video', 'Bangla Basic', 'https://youtu.be/aluBSnJvojI?si=HMmrm6kfvbD56Eyd', 'https://youtu.be/aluBSnJvojI?si=HMmrm6kfvbD56Eyd', NULL, 'youtube_aluBSnJvojI', 0, 'active', '2025-11-03 11:28:17', '2025-11-03 11:28:17'),
(9, 3, 7, 'video', 'Bangla 2', 'https://youtu.be/JxLJAgeDI5E?si=pSeKJHyloySqMvdH', 'https://youtu.be/JxLJAgeDI5E?si=pSeKJHyloySqMvdH', NULL, 'youtube_JxLJAgeDI5E', 0, 'active', '2025-11-03 11:28:37', '2025-11-03 11:28:37'),
(10, 3, 13, 'video', 'Bangla', 'https://youtu.be/N_brpUL4Pps?si=zGOVy9JW-63YcBAY', 'https://youtu.be/N_brpUL4Pps?si=zGOVy9JW-63YcBAY', NULL, 'youtube_N_brpUL4Pps', 0, 'active', '2025-11-03 11:30:11', '2025-11-03 11:30:11'),
(11, 3, 13, 'video', 'Bangla 2', 'https://youtu.be/NqTtW-jgoXY?si=Hjo7ICjF-hU2RO4k', 'https://youtu.be/NqTtW-jgoXY?si=Hjo7ICjF-hU2RO4k', NULL, 'youtube_NqTtW-jgoXY', 0, 'active', '2025-11-03 11:30:49', '2025-11-03 11:30:49'),
(12, 3, 19, 'video', 'Bangla', 'https://youtu.be/W8eXb-sX5uo?si=lHgTYJB2CWi42zDv', 'https://youtu.be/W8eXb-sX5uo?si=lHgTYJB2CWi42zDv', NULL, 'youtube_W8eXb-sX5uo', 0, 'active', '2025-11-03 11:31:27', '2025-11-03 11:31:27'),
(13, 3, 19, 'video', 'Bangla 2', 'https://youtu.be/wZzzBzv0f8Y?si=uFhqIEbD6clEWcR_', 'https://youtu.be/wZzzBzv0f8Y?si=uFhqIEbD6clEWcR_', NULL, 'youtube_wZzzBzv0f8Y', 0, 'active', '2025-11-03 11:31:52', '2025-11-03 11:31:52'),
(14, 3, 25, 'video', 'Bangla', 'https://youtu.be/TDON11VZUow?si=m1liIrnXb1UakFy6', 'https://youtu.be/TDON11VZUow?si=m1liIrnXb1UakFy6', NULL, 'youtube_TDON11VZUow', 0, 'active', '2025-11-03 11:32:39', '2025-11-03 11:32:39'),
(15, 3, 25, 'video', 'Bangla 2', 'https://youtu.be/GDOaUA1nrwQ?si=MubhqwH3mLlAWWqo', 'https://youtu.be/GDOaUA1nrwQ?si=MubhqwH3mLlAWWqo', NULL, 'youtube_GDOaUA1nrwQ', 0, 'active', '2025-11-03 11:33:10', '2025-11-03 11:33:10'),
(16, 3, 31, 'video', 'Bangla', 'https://youtu.be/1ggvhk-J1gE?si=dg6NhSGA0g9pAjF2', 'https://youtu.be/1ggvhk-J1gE?si=dg6NhSGA0g9pAjF2', NULL, 'youtube_1ggvhk-J1gE', 0, 'active', '2025-11-03 11:34:01', '2025-11-03 11:34:01'),
(17, 3, 31, 'video', 'Bangla 2', 'https://youtu.be/03nEbp8ZY20?si=GdWFXDFK6af9doWa', 'https://youtu.be/03nEbp8ZY20?si=GdWFXDFK6af9doWa', NULL, 'youtube_03nEbp8ZY20', 0, 'active', '2025-11-03 11:34:32', '2025-11-03 11:34:32'),
(18, 3, 37, 'video', 'Bangla', 'https://youtu.be/hCQxpAegmFI?si=R0m1PH-ZHkTJSegs', 'https://youtu.be/hCQxpAegmFI?si=R0m1PH-ZHkTJSegs', NULL, 'youtube_hCQxpAegmFI', 0, 'active', '2025-11-03 11:35:16', '2025-11-03 11:35:16'),
(19, 3, 37, 'video', 'Bangla 2', 'https://youtu.be/vz4U14qaFtQ?si=g_VFVcM6g9cNU68g', 'https://youtu.be/vz4U14qaFtQ?si=g_VFVcM6g9cNU68g', NULL, 'youtube_vz4U14qaFtQ', 0, 'active', '2025-11-03 11:35:39', '2025-11-03 11:35:39'),
(20, 3, 43, 'video', 'Bangla', 'https://youtu.be/QnchpZgbk28?si=eLEoc4rJ0s22dIHW', 'https://youtu.be/QnchpZgbk28?si=eLEoc4rJ0s22dIHW', NULL, 'youtube_QnchpZgbk28', 0, 'active', '2025-11-03 11:36:17', '2025-11-03 11:36:17'),
(21, 3, 43, 'video', 'Bangla 2', 'https://youtu.be/o3muOjLXNrg?si=Jx9OmdwaTTZdcBYi', 'https://youtu.be/o3muOjLXNrg?si=Jx9OmdwaTTZdcBYi', NULL, 'youtube_o3muOjLXNrg', 0, 'active', '2025-11-03 11:36:40', '2025-11-03 11:36:40'),
(22, 3, 49, 'video', 'Bangla', 'https://youtu.be/W57-sIOLkXU?si=W5_kXY7KxXAmCEpq', 'https://youtu.be/W57-sIOLkXU?si=W5_kXY7KxXAmCEpq', NULL, 'youtube_W57-sIOLkXU', 0, 'active', '2025-11-03 11:37:29', '2025-11-03 11:37:29'),
(23, 3, 49, 'video', 'Bangla 2', 'https://youtu.be/EXK4wVROLj8?si=sfDgmE-WC1cNW80O', 'https://youtu.be/EXK4wVROLj8?si=sfDgmE-WC1cNW80O', NULL, 'youtube_EXK4wVROLj8', 0, 'active', '2025-11-03 11:37:54', '2025-11-03 11:37:54'),
(24, 3, 55, 'video', 'Bangla', 'https://youtu.be/oTvKhZzFBlg?si=bv1C7ix-091IyXl_', 'https://youtu.be/oTvKhZzFBlg?si=bv1C7ix-091IyXl_', NULL, 'youtube_oTvKhZzFBlg', 0, 'active', '2025-11-03 11:38:45', '2025-11-03 11:38:45'),
(25, 3, 55, 'video', 'Bangla 2', 'https://youtu.be/ycAO4O0DQH0?si=JQ2Aq8iq_yKmlY_F', 'https://youtu.be/ycAO4O0DQH0?si=JQ2Aq8iq_yKmlY_F', NULL, 'youtube_ycAO4O0DQH0', 0, 'active', '2025-11-03 11:39:09', '2025-11-03 11:39:09'),
(26, 3, 55, 'video', 'Intro', 'https://youtu.be/pYMaZipd1QY?si=34qttBeEeEJBSdaW', 'https://youtu.be/pYMaZipd1QY?si=34qttBeEeEJBSdaW', NULL, 'youtube_pYMaZipd1QY', 0, 'active', '2025-11-03 11:39:53', '2025-11-03 11:39:53');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `duration` varchar(255) NOT NULL,
  `type` enum('regular','extra','revision','test','meeting','workshop') NOT NULL DEFAULT 'regular',
  `status` enum('scheduled','cancelled','completed','postponed') NOT NULL DEFAULT 'scheduled',
  `recurring` tinyint(1) NOT NULL DEFAULT 0,
  `recurrence_pattern` enum('daily','weekly','monthly') DEFAULT NULL,
  `recurrence_end_date` date DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `max_attendees` int(11) DEFAULT NULL,
  `students_attending` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `roll_number` varchar(255) NOT NULL,
  `father_name` varchar(255) NOT NULL,
  `mother_name` varchar(255) NOT NULL,
  `parent_contact` varchar(255) NOT NULL,
  `country_code` varchar(255) NOT NULL DEFAULT '+880',
  `address` text DEFAULT NULL,
  `admission_date` date DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `class_id`, `roll_number`, `father_name`, `mother_name`, `parent_contact`, `country_code`, `address`, `admission_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 7, 4, 'ST202504001', 'Father', 'Mother', '+8801842376477', '+880', NULL, '2025-11-01', 'active', '2025-11-01 11:08:35', '2025-11-01 11:08:35'),
(2, 11, 7, 'ST202507001', 'Student\'s Father', 'Student\'s Mother', '+8801842376477', '+880', NULL, '2025-11-03', 'active', '2025-11-02 21:36:17', '2025-11-02 21:36:17'),
(3, 12, 5, 'ST202505001', 'Student\'s Father', 'Student\'s Mother', '+8801842376477', '+880', NULL, '2025-11-05', 'active', '2025-11-05 03:03:42', '2025-11-05 03:03:42'),
(4, 13, 5, 'ST202505002', 'Father', 'Mother', '+8801842376477', '+880', NULL, '2025-11-09', 'active', '2025-11-09 09:12:05', '2025-11-09 09:12:05'),
(5, 14, 7, 'ST202507002', 'Father', 'Mother', '+8801312150423', '+880', NULL, '2025-11-10', 'active', '2025-11-09 22:28:58', '2025-11-09 22:28:58'),
(6, 15, 4, 'ST202504002', 'Father', 'Mother', '+8801312140452', '+880', NULL, '2025-11-10', 'active', '2025-11-09 22:40:11', '2025-11-09 22:40:11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `education_qualification` enum('HSC','BSC','BA','MA','MSC','PhD','Other') NOT NULL,
  `institute` varchar(255) DEFAULT NULL,
  `experience` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `order_column` int(11) NOT NULL DEFAULT 0,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','teacher','student') NOT NULL,
  `status` enum('pending','active','inactive','rejected') NOT NULL DEFAULT 'active',
  `rejection_reason` text DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `dob`, `education_qualification`, `institute`, `experience`, `bio`, `profile_picture`, `order_column`, `password`, `role`, `status`, `rejection_reason`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin', 'superadmin@skillgro.com', '2025-11-01 10:48:54', '1990-01-01', 'MSC', 'SkillGro', '5 years', NULL, NULL, 0, '$2y$12$OU9b1Pk5v8x5NDI0rauuI.lPj4ibZ6YQp5eJUSoUNOs6S1q.qzQBm', 'super_admin', 'active', NULL, 'cKM5rglWEJLwmwb9befIq1RVzii9oC6WexiAyBLthWD0rncA4kPkf6bQH8cj', '2025-11-01 10:48:54', '2025-11-01 10:48:54'),
(2, 'Administrator', 'admin', 'admin@skillgro.com', '2025-11-01 10:48:54', '1990-01-01', 'BSC', 'SkillGro', '3 years', NULL, NULL, 0, '$2y$12$/RxYrygp95CFlK8Oy7UzJ.ptYj43O5iB61NvN5wR790JW7BuQXWcu', 'admin', 'active', NULL, NULL, '2025-11-01 10:48:54', '2025-11-01 10:48:54'),
(3, 'John Teacher', 'teacher', 'teacher@skillgro.com', '2025-11-01 10:48:55', '1985-05-15', 'MSC', 'University of Education', '8 years', NULL, 'profile-pictures/profile_1762676115_69104d930f1c6.jpg', 5, '$2y$12$Sqbyb1ohghs9O.rQp3xA4ey/dcVQpUtSoXS0OzPSUC2tsx2WPBbjq', 'teacher', 'active', NULL, 'i1lF4xlbGq0OYtmav5QWYUUnJKUC4VFoZOm8d1RbSktse6GMczDM7q2UL6rc', '2025-11-01 10:48:55', '2025-11-10 00:48:27'),
(4, 'Abhishek Chowdhury', 'teacherAbhishek', 'teacherabhishek@gmail.com', NULL, '2000-05-30', 'MSC', 'Metropolitan University', '3-5 years', NULL, 'profile-pictures/profile_1762676654_69104fae863d9.jpg', 1, '$2y$12$/pgUZN.YBA8.76WaoRRm.eKPWWppB1QJpZAiKTyLmDE3lrNeE9gKe', 'teacher', 'active', NULL, 'erC1TBkVrzPCkz0BscqiJ1SBGJPnklvN82KhDbREm3mCEJiL4WwrXvmEuqyl', '2025-11-01 10:58:51', '2025-11-10 00:48:27'),
(5, 'Abhishek Chowdhury', 'teacher_Abhishek', 't_abhishek@gmail.com', NULL, '2001-05-30', 'BSC', 'Metropolitan University', '3-5 years', NULL, 'profile-pictures/profile_1762676741_6910500539f78.jpg', 2, '$2y$12$mFzaGcH./g.l/qG3JRcQR.J5UidBocQNCHn40vmk31DaKAw6m.Z72', 'teacher', 'active', NULL, NULL, '2025-11-01 11:03:01', '2025-11-10 00:48:27'),
(6, 'Abhishek Chowdhury', 't_Abhishek', 'tabhishek@lms.com', NULL, '2001-05-30', 'MA', 'LMS', '3-5 years', NULL, 'profile-pictures/profile_1762676820_69105054c3637.jpg', 3, '$2y$12$UJd7v99FB2gfw8z/7l.pv.MHjs2HVPeXdHmKHrWuY3cqg.s2MrRp2', 'teacher', 'active', NULL, NULL, '2025-11-01 11:06:58', '2025-11-10 00:48:27'),
(7, 'Abhishek Chowdhury', 'std_Abhishek', 'abhishekchowdhury054@gmail.com', NULL, NULL, 'HSC', NULL, NULL, NULL, NULL, 0, '$2y$12$kg/wQfx2zHsiV2yRGZ./vut5TJyM0WqeW5qgtKgH.7H5gTOL8Ob4.', 'student', 'active', NULL, NULL, '2025-11-01 11:08:35', '2025-11-01 11:08:35'),
(8, 'New Teacher', 'new_teacher', 'newteacher@lms.com', NULL, '2000-04-20', 'MA', 'LMS', '1-3 years', NULL, 'profile-pictures/profile_1762676901_691050a5d9420.jpg', 4, '$2y$12$WLHCk8puPWQ2zBbuMvyZw.gTabUdbheKzaWJCntXBthOTYd0M.B6W', 'teacher', 'active', NULL, NULL, '2025-11-01 11:46:39', '2025-11-10 00:48:27'),
(9, 'Second Super Admin', 'sec_super_admin', 'secAdmin@lms.com', NULL, '2000-04-20', 'Other', 'LMS', NULL, NULL, NULL, 0, '$2y$12$rxaSDLibPgEjY2Daqwjw2.cM7XL/cP.YLFTO47PNsOELx7tQyD.3u', 'super_admin', 'active', NULL, NULL, '2025-11-01 21:18:15', '2025-11-01 21:18:15'),
(10, 'Brand New Teacher', 'b_n_teacher', 'brandNewTeacher@lms.com', NULL, '2000-03-22', 'BA', 'LMS', '7-10 years', NULL, 'profile-pictures/profile_1762676959_691050df33ecd.jpg', 6, '$2y$12$1JL/RcyOVIcIJO9NlxcT7OYCT5TA5vwUyLfe4ce10mOKtWaou.pTu', 'teacher', 'active', NULL, 'EdaVD04cbMxJHFzyVabeY8p2jIOPvxsdDSx92UseAl6WZKZgziGL1Ghd59pz', '2025-11-01 23:34:07', '2025-11-10 00:48:27'),
(11, 'New Student User', 'nStudent', 'abhishekchowdhury0541@gmail.com', NULL, NULL, 'HSC', NULL, NULL, NULL, NULL, 0, '$2y$12$LDDNRDsOOCn9tkqiQJAauOlxJhnIrf7CS8LxuxSx689HlcYZcFZ9e', 'student', 'active', NULL, NULL, '2025-11-02 21:36:17', '2025-11-02 21:36:17'),
(12, 'Abhishek Chowdhury Durjoy', 'st__Abhishek', 'abhishekchowdhury05411@gmail.com', NULL, NULL, 'HSC', NULL, NULL, NULL, NULL, 0, '$2y$12$ZQ/4kn6FiFAE5HACy3N98eXjVaw0lUz9jY7WJotiZyC3uJI0bUiPO', 'student', 'active', NULL, NULL, '2025-11-05 03:03:41', '2025-11-05 03:03:41'),
(13, 'Abhi', 'Chowdhury', 'aChowdhury054@gmail.com', NULL, NULL, 'HSC', NULL, NULL, NULL, NULL, 0, '$2y$12$8Xh0wrdqyai2jHURIubJEeKaRFyQ1Pb7NVJrzR3kRhS1pi1hAVkmq', 'student', 'active', NULL, NULL, '2025-11-09 09:12:05', '2025-11-09 09:12:05'),
(14, 'Tushar Sinha', 't.sinha', 'tsinha@gmail.com', NULL, NULL, 'HSC', NULL, NULL, NULL, NULL, 0, '$2y$12$L.LplzZfUR0iZNkQpJk7VuwiVhJw73DnKuD7lPFkK7okxInE9F5Ce', 'student', 'active', NULL, NULL, '2025-11-09 22:28:58', '2025-11-09 22:28:58'),
(15, 'Try Student', 't_Student', 'tStudent@gmail.com', NULL, NULL, 'HSC', NULL, NULL, NULL, NULL, 0, '$2y$12$0xzvRF.LxM91mlijETSR/OhZIqEQT0MAi3yREKnxvjQrWgx1N3sOq', 'student', 'active', NULL, NULL, '2025-11-09 22:40:11', '2025-11-09 22:40:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignments_teacher_id_foreign` (`teacher_id`),
  ADD KEY `assignments_class_id_teacher_id_index` (`class_id`,`teacher_id`),
  ADD KEY `assignments_due_date_status_index` (`due_date`,`status`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type_status` (`type`,`status`),
  ADD KEY `idx_grade` (`grade`),
  ADD KEY `idx_teacher` (`teacher_id`);

--
-- Indexes for table `class_student`
--
ALTER TABLE `class_student`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `class_student_class_id_student_id_unique` (`class_id`,`student_id`),
  ADD KEY `class_student_student_id_foreign` (`student_id`);

--
-- Indexes for table `content_management`
--
ALTER TABLE `content_management`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `content_management_key_unique` (`key`),
  ADD KEY `content_management_key_index` (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resources_teacher_id_foreign` (`teacher_id`),
  ADD KEY `resources_class_id_foreign` (`class_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedules_class_id_date_index` (`class_id`,`date`),
  ADD KEY `schedules_teacher_id_date_index` (`teacher_id`,`date`),
  ADD KEY `schedules_date_status_index` (`date`,`status`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_roll_number_unique` (`roll_number`),
  ADD KEY `students_class_id_foreign` (`class_id`),
  ADD KEY `students_user_id_class_id_index` (`user_id`,`class_id`),
  ADD KEY `students_roll_number_index` (`roll_number`),
  ADD KEY `students_status_index` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_status_index` (`status`),
  ADD KEY `users_role_status_index` (`role`,`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `class_student`
--
ALTER TABLE `class_student`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `content_management`
--
ALTER TABLE `content_management`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignments_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_student`
--
ALTER TABLE `class_student`
  ADD CONSTRAINT `class_student_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_student_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `resources_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resources_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

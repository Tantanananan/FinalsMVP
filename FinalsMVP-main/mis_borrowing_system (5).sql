-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 13, 2026 at 05:59 AM
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
-- Database: `mis_borrowing_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `status` enum('Available','Borrowed','Defective','Lost','Archived') NOT NULL DEFAULT 'Available',
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item_name`, `serial_number`, `status`, `image_path`) VALUES
(4, 'HP Projector', 'EPSON - 12345', 'Defective', NULL),
(5, 'Tv5', 'Tv123', 'Available', NULL),
(7, 'Keyboard', 'Logitech 201239', 'Defective', '../UPLOADS/1776009115_Activity1_Midterm_LamsonChristan.jpg'),
(10, 'Hp Mouse', 'Logitech 2012392', 'Lost', '../UPLOADS/1776002738_unnamed (1).jpg'),
(12, 'Electricfan', 'ULTRASONIC 2023', 'Available', NULL),
(13, 'Projector', 'SN - 234', 'Defective', '../UPLOADS/1776009328_domain.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `request_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `duration_hours` int(11) NOT NULL DEFAULT 3,
  `student_id` varchar(50) NOT NULL,
  `request_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `request_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`request_id`, `item_id`, `duration_hours`, `student_id`, `request_status`, `request_date`) VALUES
(1, 7, 3, '20240285-C', 'Approved', '2026-04-11 13:29:46'),
(2, 13, 3, '20240285-C', 'Approved', '2026-04-11 14:25:52'),
(3, 13, 3, '20240285-C', 'Approved', '2026-04-11 14:30:08'),
(4, 13, 5, '20240285-C', 'Approved', '2026-04-11 14:31:26'),
(5, 13, 3, '20240285-C', 'Approved', '2026-04-11 14:39:49'),
(6, 7, 3, '20240285-C', 'Approved', '2026-04-11 14:42:33'),
(7, 5, 3, '20240285-C', 'Approved', '2026-04-11 14:44:52'),
(8, 5, 3, '20240285-C', 'Approved', '2026-04-12 01:24:50'),
(9, 13, 3, '20240285-C', 'Approved', '2026-04-12 01:35:28'),
(10, 10, 3, '20240366-C', 'Approved', '2026-04-12 15:42:10'),
(11, 12, 3, '20240285-C', 'Approved', '2026-04-13 01:02:34');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` varchar(50) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `course_section` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `first_name`, `last_name`, `email`, `course_section`) VALUES
('20240285-C', 'Christan', 'Lamson', 'lamsonchristan@gmail.com', 'BSIS - 2A'),
('20240366-C', 'Karl', 'Telan', 'acadtan@gmail.com', 'BSIS - 2A'),
('20241234-C', 'Student', 'Account', 'Student@gmail.com', 'BSIS - 2A');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `item_id` int(11) NOT NULL,
  `transaction_status` enum('Active','Completed') DEFAULT 'Active',
  `return_condition` varchar(50) DEFAULT NULL,
  `issued_by` int(11) DEFAULT NULL,
  `received_by` int(11) DEFAULT NULL,
  `borrow_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `expected_return_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `student_id`, `item_id`, `transaction_status`, `return_condition`, `issued_by`, `received_by`, `borrow_date`, `expected_return_time`) VALUES
(23, '20240285-C', 7, 'Completed', NULL, NULL, NULL, '2026-04-11 13:32:57', NULL),
(24, '20240285-C', 12, 'Completed', NULL, NULL, NULL, '2026-04-11 13:34:43', NULL),
(25, '20240285-C', 10, 'Completed', 'Defective', NULL, 2, '2026-04-11 13:38:46', '23:38:00'),
(26, '20240285-C', 12, 'Completed', NULL, NULL, NULL, '2026-04-11 14:03:56', '01:03:00'),
(27, '20240285-C', 12, 'Completed', NULL, NULL, 2, '2026-04-11 14:04:23', '01:04:00'),
(28, '20240285-C', 13, 'Completed', NULL, NULL, NULL, '2026-04-11 14:26:02', '19:26:02'),
(29, '20240285-C', 13, 'Completed', NULL, NULL, 2, '2026-04-11 14:30:17', '01:30:17'),
(30, '20240285-C', 13, 'Completed', NULL, NULL, NULL, '2026-04-11 14:31:41', '03:31:41'),
(31, '20240285-C', 13, 'Completed', NULL, NULL, NULL, '2026-04-11 14:39:54', '01:39:54'),
(32, '20240285-C', 7, 'Completed', NULL, NULL, NULL, '2026-04-11 14:42:39', '01:42:39'),
(33, '20240285-C', 5, 'Completed', 'Returned', 3, 1, '2026-04-11 16:16:56', '03:16:56'),
(34, '20240285-C', 4, 'Completed', 'Defective', 1, 1, '2026-04-12 01:20:36', '12:20:00'),
(35, '20240285-C', 5, 'Completed', 'Lost', 1, 1, '2026-04-12 01:24:58', '12:24:58'),
(36, '20240285-C', 13, 'Completed', 'Defective', 1, 2, '2026-04-12 01:36:08', '12:36:08'),
(37, '20240285-C', 4, 'Completed', 'Defective', 1, 1, '2026-04-12 01:37:15', '12:37:00'),
(38, '20240366-C', 10, 'Completed', 'Defective', 1, 2, '2026-04-12 15:42:30', '02:42:30'),
(39, '20240285-C', 12, 'Completed', 'Returned', 1, 1, '2026-04-13 01:03:05', '12:03:05'),
(40, '20240285-C', 12, 'Completed', 'Returned', 1, 1, '2026-04-13 01:13:08', '11:13:00'),
(41, '20240285-C', 12, 'Completed', 'Returned', 1, 1, '2026-04-13 01:19:59', '10:19:00'),
(43, '20240285-C', 12, 'Completed', 'Returned', 1, 2, '2026-04-13 01:35:52', '01:35:00'),
(44, '20240285-C', 7, 'Completed', 'Defective', 1, 1, '2026-04-13 02:23:02', '13:23:02');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `role` varchar(50) NOT NULL,
  `status` enum('Active','Archived','Pending') DEFAULT 'Active',
  `verification_token` varchar(255) DEFAULT NULL,
  `timeout_until` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `strike_count` int(11) DEFAULT 0,
  `penalty_end_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `email`, `password`, `first_name`, `last_name`, `role`, `status`, `verification_token`, `timeout_until`, `created_at`, `strike_count`, `penalty_end_date`) VALUES
(1, 'Staff@gmail.com', '$2y$10$1uO8bsXKc0isITRDp4wgaertOiI9DRealLV56noD7jlrqNTWFJLFq', 'Staff', 'Account', 'Staff', 'Active', NULL, NULL, '2026-04-12 15:47:51', 0, NULL),
(2, 'Admin@gmail.com', '$2y$10$EidmxhlOt.cg6BhH/eikA.o2gJQAuABr9/0wrLZqh2k9wyJZN3GOS', 'Admin', 'Account', 'Admin', 'Active', NULL, NULL, '2026-04-12 15:47:51', 0, NULL),
(3, 'Student@gmail.com', '$2y$10$Nd50Q2EUgB/l6394.bNWuuO8QhIBWt1TXOkZsOlj8HYRq0TjurRPm', 'Student Assistant', 'Account', 'Staff', 'Active', NULL, NULL, '2026-04-12 15:47:51', 0, NULL),
(30, 'lamsonchristan@gmail.com', '$2y$10$vz13hcGR9uY4AFRO5m1zu.Pot0UDTKBDHFEZKUM8aqYBA/ZrXfjOK', 'Christan', 'Lamson', 'Student', 'Active', NULL, NULL, '2026-04-12 15:47:51', 1, '2026-04-14 10:23:13'),
(31, 'acadtan@gmail.com', '$2y$10$/ua3ZGC3zGUBxE9q19f1WuBTMrLB9p4d4KJKRzgMXWyO7FOWuj6c2', 'Karl', 'Telan', 'Student', 'Active', NULL, NULL, '2026-04-12 15:47:51', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `requests_ibfk_1` (`item_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `transactions_ibfk_2` (`item_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

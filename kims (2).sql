-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Apr 17, 2026 at 01:05 PM
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
-- Database: `kims`
--

-- --------------------------------------------------------

--
-- Table structure for table `cell_blocks`
--

CREATE TABLE `cell_blocks` (
  `block_id` int(11) NOT NULL,
  `block_name` varchar(50) NOT NULL,
  `classification` enum('General','Maximum','Special','Sick Bay') NOT NULL,
  `capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cell_blocks`
--

INSERT INTO `cell_blocks` (`block_id`, `block_name`, `classification`, `capacity`) VALUES
(1, 'Block A', 'General', 150),
(2, 'Block B', 'Maximum', 50),
(3, 'Block C', 'Special', 20),
(4, 'Block D', 'Sick Bay', 15);

-- --------------------------------------------------------

--
-- Table structure for table `court_records`
--

CREATE TABLE `court_records` (
  `record_id` int(11) NOT NULL,
  `inmate_id` int(11) DEFAULT NULL,
  `court_name` varchar(100) DEFAULT NULL,
  `next_hearing_date` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `zoom_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `court_records`
--

INSERT INTO `court_records` (`record_id`, `inmate_id`, `court_name`, `next_hearing_date`, `remarks`, `zoom_link`) VALUES
(14, 24, 'Nyeri law courts', '2026-04-18', 'Mention', 'https://zoom.us/j/93583666761?pwd=BbNG3xYKFEErZosR3Z6UHdbYP9swbb.1');

-- --------------------------------------------------------

--
-- Table structure for table `disciplinary_records`
--

CREATE TABLE `disciplinary_records` (
  `record_id` int(11) NOT NULL,
  `inmate_id` int(11) DEFAULT NULL,
  `incident_detail` text DEFAULT NULL,
  `status` enum('Pending','Resolved') DEFAULT 'Pending',
  `date_recorded` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inmates`
--

CREATE TABLE `inmates` (
  `inmate_id` int(11) NOT NULL,
  `kims_id` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `offence_category` varchar(100) DEFAULT NULL,
  `date_admitted` date DEFAULT NULL,
  `block_id` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'In Custody',
  `discharged_by` varchar(255) DEFAULT NULL,
  `id_number` varchar(9) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT 'default.png',
  `edd` date DEFAULT NULL,
  `sentence_years` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inmates`
--

INSERT INTO `inmates` (`inmate_id`, `kims_id`, `full_name`, `offence_category`, `date_admitted`, `block_id`, `status`, `discharged_by`, `id_number`, `dob`, `gender`, `photo_url`, `edd`, `sentence_years`) VALUES
(24, 'KIMS-2026-6409', 'Ken Emmanuel Njenga', 'Obtaining by False Pretences', '2026-03-18', 1, 'In Custody', NULL, '665658877', '2008-04-02', 'Male', 'KIMS-2026-6409.jpg', '2031-03-18', 5.03),
(25, 'KIMS-2026-1477', 'Dennis Gatweku Kambo', 'Other Misdemeanor', '2026-03-31', NULL, 'Released', 'Dennis Nderitu', '543676788', '2001-10-17', 'Male', 'KIMS-2026-1477.jpg', '2026-04-10', 0.03),
(26, 'KIMS-2026-8462', 'Jackson Kariuki Njane', 'Attempted Rape', '2026-04-17', NULL, 'In Custody', NULL, '987655777', '2008-04-15', 'Male', 'KIMS-2026-8462.jpg', '2043-04-17', 17.00);

-- --------------------------------------------------------

--
-- Table structure for table `offence_updates`
--

CREATE TABLE `offence_updates` (
  `update_id` int(11) NOT NULL,
  `inmate_id` int(11) NOT NULL,
  `old_offence` varchar(100) DEFAULT NULL,
  `new_offence` varchar(100) DEFAULT NULL,
  `updated_by` varchar(100) DEFAULT NULL,
  `update_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offence_updates`
--

INSERT INTO `offence_updates` (`update_id`, `inmate_id`, `old_offence`, `new_offence`, `updated_by`, `update_date`) VALUES
(4, 24, 'Other Felony', 'Obtaining by False Pretences', 'C01-1098', '2026-04-17 08:34:19');

-- --------------------------------------------------------

--
-- Table structure for table `sentence_updates`
--

CREATE TABLE `sentence_updates` (
  `id` int(11) NOT NULL,
  `inmate_id` int(11) NOT NULL,
  `old_years` decimal(5,2) NOT NULL,
  `new_years` decimal(5,2) NOT NULL,
  `update_date` datetime DEFAULT current_timestamp(),
  `updated_by` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sentence_updates`
--

INSERT INTO `sentence_updates` (`id`, `inmate_id`, `old_years`, `new_years`, `update_date`, `updated_by`) VALUES
(1, 24, 0.03, 5.03, '2026-04-17 11:40:04', 'C01-1098'),
(2, 25, 0.05, 0.03, '2026-04-17 11:50:29', 'C01-1098');

-- --------------------------------------------------------

--
-- Table structure for table `training_logs`
--

CREATE TABLE `training_logs` (
  `log_id` int(11) NOT NULL,
  `inmate_id` int(11) DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `workshop_name` varchar(50) DEFAULT NULL,
  `hours_logged` decimal(5,2) DEFAULT NULL,
  `date_logged` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training_logs`
--

INSERT INTO `training_logs` (`log_id`, `inmate_id`, `instructor_id`, `workshop_name`, `hours_logged`, `date_logged`) VALUES
(9, 24, 0, 'Carpentry & Joinery', 7.00, '2026-04-17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `full_name`, `role`) VALUES
(33, 'maxwell', 'C01-1234', 'Maxwell Chege Njane', 'Super Admin'),
(38, 'C01-1098', '$2y$10$rEuOhGuO7NjbLteduF6Ia.2lz2dr05fzgK.NSjhi7B09MNdIcU44.', 'Dennis Nderitu', 'Instructor'),
(41, 'C01-1122', '$2y$10$vT4suSPoCzkHAcphrCq.2e84hc1NSmiuf/oTSMDP7JkmVJwpWRx6S', 'Ruth Mutanu', 'Admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cell_blocks`
--
ALTER TABLE `cell_blocks`
  ADD PRIMARY KEY (`block_id`),
  ADD UNIQUE KEY `block_name` (`block_name`);

--
-- Indexes for table `court_records`
--
ALTER TABLE `court_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `inmate_id` (`inmate_id`);

--
-- Indexes for table `disciplinary_records`
--
ALTER TABLE `disciplinary_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `inmate_id` (`inmate_id`);

--
-- Indexes for table `inmates`
--
ALTER TABLE `inmates`
  ADD PRIMARY KEY (`inmate_id`),
  ADD UNIQUE KEY `kims_id` (`kims_id`),
  ADD KEY `block_id` (`block_id`);

--
-- Indexes for table `offence_updates`
--
ALTER TABLE `offence_updates`
  ADD PRIMARY KEY (`update_id`),
  ADD KEY `inmate_id` (`inmate_id`);

--
-- Indexes for table `sentence_updates`
--
ALTER TABLE `sentence_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inmate_id` (`inmate_id`);

--
-- Indexes for table `training_logs`
--
ALTER TABLE `training_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `inmate_id` (`inmate_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cell_blocks`
--
ALTER TABLE `cell_blocks`
  MODIFY `block_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `court_records`
--
ALTER TABLE `court_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `disciplinary_records`
--
ALTER TABLE `disciplinary_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inmates`
--
ALTER TABLE `inmates`
  MODIFY `inmate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `offence_updates`
--
ALTER TABLE `offence_updates`
  MODIFY `update_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sentence_updates`
--
ALTER TABLE `sentence_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `training_logs`
--
ALTER TABLE `training_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `court_records`
--
ALTER TABLE `court_records`
  ADD CONSTRAINT `court_records_ibfk_1` FOREIGN KEY (`inmate_id`) REFERENCES `inmates` (`inmate_id`) ON DELETE CASCADE;

--
-- Constraints for table `disciplinary_records`
--
ALTER TABLE `disciplinary_records`
  ADD CONSTRAINT `disciplinary_records_ibfk_1` FOREIGN KEY (`inmate_id`) REFERENCES `inmates` (`inmate_id`);

--
-- Constraints for table `inmates`
--
ALTER TABLE `inmates`
  ADD CONSTRAINT `inmates_ibfk_1` FOREIGN KEY (`block_id`) REFERENCES `cell_blocks` (`block_id`);

--
-- Constraints for table `offence_updates`
--
ALTER TABLE `offence_updates`
  ADD CONSTRAINT `offence_updates_ibfk_1` FOREIGN KEY (`inmate_id`) REFERENCES `inmates` (`inmate_id`) ON DELETE CASCADE;

--
-- Constraints for table `sentence_updates`
--
ALTER TABLE `sentence_updates`
  ADD CONSTRAINT `sentence_updates_ibfk_1` FOREIGN KEY (`inmate_id`) REFERENCES `inmates` (`inmate_id`) ON DELETE CASCADE;

--
-- Constraints for table `training_logs`
--
ALTER TABLE `training_logs`
  ADD CONSTRAINT `training_logs_ibfk_1` FOREIGN KEY (`inmate_id`) REFERENCES `inmates` (`inmate_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

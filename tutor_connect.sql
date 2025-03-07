-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2025 at 06:22 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tutor_connect`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`, `full_name`, `last_login`, `created_at`) VALUES
(1, 'Albert', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', 'Albert', NULL, '2025-03-03 04:47:40'),
(2, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', NULL, '2025-03-03 04:52:39'),
(4, 'adminn', '$2y$10$FtspSJ3.RCVEyJTZelBhL.BM0qoTJ53EI.0D8op1edWkpGTfpGYQ6', 'Main Admin', NULL, '2025-03-03 04:58:59');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `rating_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `rated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_requests`
--

CREATE TABLE `student_requests` (
  `request_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_name`, `description`) VALUES
(1, 'Mathematics', 'Mathematical concepts and problem-solving'),
(2, 'Computer Science', 'Programming and computational theory'),
(3, 'Physics', 'Physical science and theoretical mechanics'),
(4, 'Chemistry', 'Study of matter and its interactions'),
(5, 'Biology', 'Study of living organisms');

-- --------------------------------------------------------

--
-- Table structure for table `tutor_applications`
--

CREATE TABLE `tutor_applications` (
  `application_id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `transcript_path` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutor_applications`
--

INSERT INTO `tutor_applications` (`application_id`, `tutor_id`, `transcript_path`, `status`, `submitted_at`, `reviewed_at`) VALUES
(1, 6, 'transcripts/67c432da9d8d3_Task_2.pdf', 'approved', '2025-03-02 10:28:42', '2025-03-03 06:27:42'),
(2, 8, 'transcripts/67c586d1cc525_Task_2__1_.pdf', 'rejected', '2025-03-03 10:39:13', '2025-03-03 10:42:05'),
(3, 15, 'transcripts/67c90f4a357a8_Task_2.pdf', 'approved', '2025-03-06 02:58:18', '2025-03-06 03:00:04'),
(4, 17, 'transcripts/67c9197ee55cc_ALBERT_NG___ANG___A_KUNG___U.pdf', 'approved', '2025-03-06 03:41:50', '2025-03-06 11:30:36'),
(5, 18, 'transcripts/67c98721b3145_ALBERT_NG___ANG___A_KUNG___U.pdf', 'approved', '2025-03-06 11:29:37', '2025-03-06 11:30:32'),
(6, 21, 'transcripts/67ca7899e3d6e_Transcript-ABT10424921__1_.pdf', 'approved', '2025-03-07 04:39:53', '2025-03-07 04:58:34');

-- --------------------------------------------------------

--
-- Table structure for table `tutor_subjects`
--

CREATE TABLE `tutor_subjects` (
  `tutor_id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutor_subjects`
--

INSERT INTO `tutor_subjects` (`tutor_id`, `subject_name`) VALUES
(6, 'Computer Science'),
(6, 'Digital Electronics'),
(6, 'Mathematics'),
(8, 'Computer Science'),
(8, 'EAPE 422'),
(8, 'Mathematics'),
(8, 'RELI470'),
(8, 'RELI474'),
(15, 'cosc143'),
(15, 'math121'),
(15, 'maths141'),
(15, 'zool153'),
(17, 'maths121'),
(17, 'maths141'),
(17, 'zool143'),
(18, 'cosc100'),
(21, 'Advanced mathematics'),
(21, 'Calculus 2'),
(21, 'Structured Programming');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `unit_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `role` enum('student','tutor') NOT NULL,
  `reg_number` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `year_of_study` int(11) DEFAULT NULL,
  `semester` int(11) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `is_authorized` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `role`, `reg_number`, `password`, `email`, `phone`, `year_of_study`, `semester`, `profile_pic`, `is_authorized`, `created_at`) VALUES
(1, 'student', 'EBST1/10178/23', '$2y$10$JVOSUUV1HlSaBWOPFwb1CewXg/5uHQB6TiGhn3vxBeGZEzfmAFMrm', 'albertkungu290@gmail.com', '0712870654', 3, 2, '../uploads/profile_pics/67c7ba0fe45bb_me-removebg-preview.png', 0, '2025-03-02 10:20:20'),
(6, 'tutor', 'EBST1/9/23', '$2y$10$fWy4eYqrOeMKAEAG/PicB.8O.0IHARisrsugqtGetkGCZp7S.YaN2', 'test@gmail.com', '0115726301', 4, NULL, NULL, 1, '2025-03-02 10:28:42'),
(8, 'tutor', 'ABT1/04249/21', '$2y$10$G9A8/dP.anV3J.nAHLWBXOsYADGh/dSMIcgRWdT.LFSvmgWLP.QqO', 'njokimuchiri03@gmail.com', '0717700366', NULL, NULL, NULL, 0, '2025-03-03 10:39:13'),
(9, 'student', 'test/student', '$2y$10$2.EXvHRtY6i8RDetsWeRhenJbl65ioMrlgiV6zUFp8q7b.ZCMFr5.', 'student@gmail.com', '1234567890', 2, 1, NULL, 0, '2025-03-05 10:38:57'),
(12, 'student', 'studenttest', '$2y$10$UJgWdmHfFaP8.Yu30Nbs6e70J4ieUnIj2TEsWJn0RM1VvcAJ/Wnq2', 'studenttest@gmail.com', '0712870654', 2, 2, NULL, 0, '2025-03-05 12:14:04'),
(13, 'tutor', 'tutor', '$2y$10$t.ZpxnM.fZCgAfjk5rRNbuhsKlMoZSQTEmHsK1dnFnRF9W2X7YWyC', 'tutortest@gmail.com', '098765432', NULL, NULL, NULL, 0, '2025-03-06 02:55:51'),
(15, 'tutor', 'test tutor', '$2y$10$qXRvuKEzq9ii0mEOEFG1.uFvXeZncIGLJTUGYp2zdT7oQQhi2UTXO', 'testtutor@gmail.com', '098765', NULL, NULL, NULL, 1, '2025-03-06 02:58:18'),
(17, 'tutor', 'testt2', '$2y$10$O5NexCiTHE0sdJfRPrVa6e/xOpVtXZdPuG4TS7ih3FqmJwFXuChSm', 'testt2@gmail.com', '63634723t4', NULL, NULL, NULL, 1, '2025-03-06 03:41:50'),
(18, 'tutor', 'qwerty', '$2y$10$1scNYDueATZFjZXcYXbLFerkhE1XKJxn0/2AJBiZBwBcMUxboSZgu', 'qwerty@gmail.com', '4564565', NULL, NULL, NULL, 1, '2025-03-06 11:29:37'),
(19, 'student', 'ABT1/07368/22', '$2y$10$hJCjLVk8hE8kUfAWm2aToubZaoWUnz5LcEtPTN0bPsZW20rrieUqm', 'wachira@gmail.com', '0987898', 3, 2, NULL, 0, '2025-03-06 12:29:27'),
(20, 'student', 'EBST1/10179/23', '$2y$10$L2551E/sEMdHlT0wMzGCiO2f5R.YtDpCZwEo7iVZ8H9Fw5BwhR4qS', 'ebst1@gmail.com', '0115726300', 4, 2, '../uploads/profile_pics/67ca7421b0191_me-removebg-preview.png', 0, '2025-03-07 04:07:35'),
(21, 'tutor', 'test3', '$2y$10$MXamWVNiTQtKxTn/YzN5V.kyGZ3FRwZEJKZLFd4FJy2fGTto/u/ii', 'test3@tutorhub', '644763464', NULL, NULL, NULL, 1, '2025-03-07 04:39:53');

-- --------------------------------------------------------

--
-- Table structure for table `user_logs`
--

CREATE TABLE `user_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `action_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `tutor_id` (`tutor_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `student_requests`
--
ALTER TABLE `student_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `tutor_id` (`tutor_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`),
  ADD UNIQUE KEY `subject_name` (`subject_name`);

--
-- Indexes for table `tutor_applications`
--
ALTER TABLE `tutor_applications`
  ADD PRIMARY KEY (`application_id`),
  ADD UNIQUE KEY `tutor_id` (`tutor_id`);

--
-- Indexes for table `tutor_subjects`
--
ALTER TABLE `tutor_subjects`
  ADD PRIMARY KEY (`tutor_id`,`subject_name`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `reg_number` (`reg_number`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_requests`
--
ALTER TABLE `student_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tutor_applications`
--
ALTER TABLE `tutor_applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user_logs`
--
ALTER TABLE `user_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `student_requests`
--
ALTER TABLE `student_requests`
  ADD CONSTRAINT `student_requests_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `student_requests_ibfk_2` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `tutor_applications`
--
ALTER TABLE `tutor_applications`
  ADD CONSTRAINT `tutor_applications_ibfk_1` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `tutor_subjects`
--
ALTER TABLE `tutor_subjects`
  ADD CONSTRAINT `tutor_subjects_ibfk_1` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD CONSTRAINT `user_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

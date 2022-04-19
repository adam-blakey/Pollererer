-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 192.168.1.241:3306
-- Generation Time: Apr 19, 2022 at 08:22 AM
-- Server version: 10.5.15-MariaDB-log
-- PHP Version: 8.0.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `attendance`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `ID` int(10) NOT NULL,
  `member_ID` int(10) NOT NULL,
  `edit_datetime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `edit_member_ID` int(10) NOT NULL,
  `term_dates_ID` int(10) NOT NULL,
  `ensemble_ID` int(10) NOT NULL,
  `IP` varchar(32) NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ensembles`
--

CREATE TABLE `ensembles` (
  `ID` int(10) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `logins`
--

CREATE TABLE `logins` (
  `ID` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `logins_sessions`
--

CREATE TABLE `logins_sessions` (
  `ID` varchar(255) NOT NULL,
  `member_ID` int(10) NOT NULL,
  `start` datetime NOT NULL,
  `expiry` datetime NOT NULL,
  `IP` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `ID` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `instrument` varchar(255) NOT NULL,
  `user_level` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `members-ensembles`
--

CREATE TABLE `members-ensembles` (
  `ID` int(10) NOT NULL,
  `member_ID` int(10) NOT NULL,
  `ensemble_ID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `terms`
--

CREATE TABLE `terms` (
  `ID` int(10) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `term_dates`
--

CREATE TABLE `term_dates` (
  `ID` int(10) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `term_ID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `edit_user_ID` (`edit_member_ID`),
  ADD KEY `ensemble_ID` (`ensemble_ID`),
  ADD KEY `member_ID` (`member_ID`),
  ADD KEY `term_dates_ID` (`term_dates_ID`);

--
-- Indexes for table `ensembles`
--
ALTER TABLE `ensembles`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `logins`
--
ALTER TABLE `logins`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `logins_sessions`
--
ALTER TABLE `logins_sessions`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `user_ID` (`member_ID`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `members-ensembles`
--
ALTER TABLE `members-ensembles`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ensemble_ID` (`ensemble_ID`),
  ADD KEY `member_ID` (`member_ID`);

--
-- Indexes for table `terms`
--
ALTER TABLE `terms`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `term_dates`
--
ALTER TABLE `term_dates`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `term_ID` (`term_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ensembles`
--
ALTER TABLE `ensembles`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `members-ensembles`
--
ALTER TABLE `members-ensembles`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `terms`
--
ALTER TABLE `terms`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `term_dates`
--
ALTER TABLE `term_dates`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`edit_member_ID`) REFERENCES `members` (`ID`),
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`ensemble_ID`) REFERENCES `ensembles` (`ID`),
  ADD CONSTRAINT `attendance_ibfk_3` FOREIGN KEY (`member_ID`) REFERENCES `members` (`ID`),
  ADD CONSTRAINT `attendance_ibfk_4` FOREIGN KEY (`term_dates_ID`) REFERENCES `term_dates` (`ID`);

--
-- Constraints for table `logins`
--
ALTER TABLE `logins`
  ADD CONSTRAINT `logins_ibfk_1` FOREIGN KEY (`ID`) REFERENCES `members` (`ID`);

--
-- Constraints for table `logins_sessions`
--
ALTER TABLE `logins_sessions`
  ADD CONSTRAINT `logins_sessions_ibfk_1` FOREIGN KEY (`member_ID`) REFERENCES `logins` (`ID`);

--
-- Constraints for table `members-ensembles`
--
ALTER TABLE `members-ensembles`
  ADD CONSTRAINT `members-ensembles_ibfk_1` FOREIGN KEY (`ensemble_ID`) REFERENCES `ensembles` (`ID`),
  ADD CONSTRAINT `members-ensembles_ibfk_2` FOREIGN KEY (`member_ID`) REFERENCES `members` (`ID`);

--
-- Constraints for table `term_dates`
--
ALTER TABLE `term_dates`
  ADD CONSTRAINT `term_dates_ibfk_1` FOREIGN KEY (`term_ID`) REFERENCES `terms` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 29, 2022 at 08:42 PM
-- Server version: 10.3.35-MariaDB-log-cll-lve
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pollererer`
--

--
-- Dumping data for table `ensembles`
--

INSERT INTO `ensembles` (`ID`, `safe_name`, `name`, `admin_email`, `image`) VALUES
(1, 'test-ensemble', 'Test Ensemble', 'spam@adamblakey.co.uk', 'https://attendance.nsw.org.uk/uploads/ensemble-logos/test.svg');

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`ID`, `first_name`, `last_name`, `instrument`, `row`, `seat`, `user_level`, `image`) VALUES
(-1, 'NSWO', 'User', 'N/A', 0, 0, -1, 'https://attendance.nsw.org.uk/uploads/ensemble-logos/test.svg'),
(0, 'Guest', 'User', '', 0, 0, -1, ''),
(1, 'Kacey', 'Low', 'Bb Clarinet', 0, 0, 0, ''),
(2, 'Kate', 'Haig', 'Flute', 0, 0, 0, ''),
(3, 'Baldric', 'Sandford', 'Bb Clarinet', 0, 0, 0, ''),
(4, 'Ivan', 'Post', 'Percussion', 0, 0, 0, ''),
(5, 'Tiffani', 'Richard', 'Trumpet', 0, 0, 0, '');

--
-- Dumping data for table `members-ensembles`
--

INSERT INTO `members-ensembles` (`ID`, `member_ID`, `ensemble_ID`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1);

--
-- Dumping data for table `terms`
--

INSERT INTO `terms` (`ID`, `safe_name`, `name`, `image`) VALUES
(1, 'summer-2022', 'Summer 2022', 'https://attendance.nsw.org.uk/uploads/term-logos/summer.svg'),
(2, 'test', 'Test', 'https://attendance.nsw.org.uk/uploads/term-logos/test.svg');

--
-- Dumping data for table `term_dates`
--

INSERT INTO `term_dates` (`ID`, `datetime`, `datetime_end`, `is_featured`, `term_ID`) VALUES
(1, 4102477200, 4102488000, 0, 1),
(2, 4103082000, 4103092800, 0, 1),
(3, 4103686800, 4103697600, 0, 1);

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`ID`, `member_ID`, `edit_datetime`, `edit_member_ID`, `term_dates_ID`, `ensemble_ID`, `IP`, `status`) VALUES
(1, 1, 1650355779, -1, 1, 1, '', 1),
(2, 2, 1650355779, -1, 1, 1, '', 1),
(3, 3, 1650356655, -1, 2, 1, '', 1),
(4, 5, 1650356655, -1, 2, 1, '', 0);

--
-- Dumping data for table `logins`
--

INSERT INTO `logins` (`ID`, `email`, `password`) VALUES
(-1, 'test-ensemble', '$2y$10$jjve4ShtvdqFJXNKHzGSte1bGLkyxcyJROKpzGVgsYiAH4q6gyeWG'),
(1, 'admin@example.com', '$2y$10$f0TP0bKkLkmL7iIM4C3Cvu18EkBUBoGS7MH1ZNugltn9zXmdMD2Em');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 02, 2025 at 12:01 PM
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
-- Database: `ticket`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_wallet`
--

CREATE TABLE `admin_wallet` (
  `id` int(11) NOT NULL,
  `available_money` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_wallet`
--

INSERT INTO `admin_wallet` (`id`, `available_money`) VALUES
(1, 21750.00);

-- --------------------------------------------------------

--
-- Table structure for table `admin_wallet_transactions`
--

CREATE TABLE `admin_wallet_transactions` (
  `id` int(11) NOT NULL,
  `admin_wallet_id` int(11) NOT NULL,
  `transaction_type` enum('credit','debit') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_wallet_transactions`
--

INSERT INTO `admin_wallet_transactions` (`id`, `admin_wallet_id`, `transaction_type`, `amount`, `transaction_date`, `description`) VALUES
(1, 1, 'credit', 550.00, '2025-03-03 15:54:50', 'Booking payment for ticket 16 from mukkukalyan309@gmail.com'),
(2, 1, 'debit', 500.00, '2025-03-03 15:54:50', 'Payment to seller kalyanm1198.sse@saveetha.com for ticket 16'),
(3, 1, 'credit', 1050.00, '2025-03-04 05:58:32', 'Booking payment for ticket 17 from mukkukalyan309@gmail.com'),
(4, 1, 'debit', 1000.00, '2025-03-04 05:58:32', 'Payment to seller kalyankrishnaa768@gmail.com for ticket 17'),
(5, 1, 'credit', 500.00, '2025-03-08 08:34:36', 'Booking payment for ticket 18 from kalyanm1198.sse@saveetha.com'),
(6, 1, 'debit', 500.00, '2025-03-08 08:34:36', 'Payment to seller mukkukalyan309@gmail.com for ticket 18'),
(7, 1, 'credit', 550.00, '2025-03-08 09:21:47', 'Booking payment for ticket 18 from kalyanm1198.sse@saveetha.com'),
(8, 1, 'debit', 500.00, '2025-03-08 09:21:47', 'Payment to seller mukkukalyan309@gmail.com for ticket 18'),
(9, 1, 'credit', 500.00, '2025-03-21 02:55:48', 'Manual wallet top-up'),
(10, 1, 'credit', 550.00, '2025-03-23 11:26:48', 'Booking payment for ticket 23 from kalyankrishnaa768@gmail.com'),
(11, 1, 'debit', 500.00, '2025-03-23 11:26:48', 'Payment to seller kalyankrishnaa768@gmail.com for ticket 23'),
(12, 1, 'credit', 26.00, '2025-03-31 11:29:17', 'Manual wallet top-up'),
(13, 1, 'credit', 150.00, '2025-04-01 05:13:28', 'Manual wallet top-up'),
(14, 1, 'credit', 1550.00, '2025-04-01 05:14:02', 'Booking payment for ticket 25 from mukkukalyan309@gmail.com'),
(15, 1, 'debit', 1500.00, '2025-04-01 05:14:02', 'Payment to seller kalyankrishnaa768@gmail.com for ticket 25'),
(16, 1, 'credit', 150.00, '2025-04-01 05:29:21', 'Manual wallet top-up'),
(17, 1, 'credit', 1550.00, '2025-04-02 09:03:09', 'Booking payment for ticket 29 from kalyankrishnaa768@gmail.com'),
(18, 1, 'debit', 1500.00, '2025-04-02 09:03:09', 'Payment to seller kalyankrishnaa768@gmail.com for ticket 29');

-- --------------------------------------------------------

--
-- Table structure for table `agency_bookings`
--

CREATE TABLE `agency_bookings` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `bus_id` int(11) NOT NULL,
  `journey_date` date NOT NULL,
  `agency_email` varchar(255) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `passenger_name` varchar(255) NOT NULL,
  `seat_number` varchar(10) NOT NULL,
  `ticket_price` decimal(10,2) NOT NULL,
  `booking_status` enum('Confirmed','Cancelled','Completed') NOT NULL DEFAULT 'Confirmed',
  `boarding_point` varchar(255) NOT NULL,
  `dropping_point` varchar(255) NOT NULL,
  `from_location` varchar(255) NOT NULL,
  `to_location` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agency_bookings`
--

INSERT INTO `agency_bookings` (`id`, `booking_id`, `route_id`, `bus_id`, `journey_date`, `agency_email`, `user_email`, `passenger_name`, `seat_number`, `ticket_price`, `booking_status`, `boarding_point`, `dropping_point`, `from_location`, `to_location`, `created_at`) VALUES
(1, 31, 1, 1949, '2025-03-25', 'ytr@gmail.com', 'kalyankrishnaa768@gmail.com', 'kalyan krishna', '7', 1000.00, 'Completed', 'railway station area', 'madhavaram busstand', 'vijayawada', 'chennai', '2025-02-02 16:45:54'),
(2, 31, 1, 1949, '2025-03-25', 'ytr@gmail.com', 'kalyankrishnaa768@gmail.com', 'ram', '8', 1000.00, 'Completed', 'railway station area', 'madhavaram busstand', 'vijayawada', 'chennai', '2025-02-04 16:45:54'),
(3, 32, 6, 1989, '2025-03-25', 'abcd@gmail.com', 'kalyankrishnaa768@gmail.com', 'kalyan krishna', '12', 1150.00, 'Completed', 'Benz Circle', 'madhavaram', 'vijayawada', 'chennai', '2025-02-06 16:45:54'),
(4, 33, 6, 1989, '2025-03-25', 'abcd@gmail.com', 'kalyankrishnaa768@gmail.com', 'kalyan krishna', '3', 1150.00, 'Completed', 'Main bus stand', 'madhavaram', 'vijayawada', 'chennai', '2025-02-11 16:45:54'),
(5, 34, 1, 1949, '2025-03-25', 'ytr@gmail.com', 'kalyankrishnaa768@gmail.com', 'kalyan krishna', '20', 1000.00, 'Completed', 'railway station area', 'madhavaram busstand', 'vijayawada', 'chennai', '2025-02-13 16:45:54'),
(6, 34, 1, 1949, '2025-03-25', 'ytr@gmail.com', 'kalyankrishnaa768@gmail.com', 'ram', '24', 1000.00, 'Completed', 'railway station area', 'madhavaram busstand', 'vijayawada', 'chennai', '2025-02-15 16:45:54'),
(7, 35, 1, 1949, '2025-03-25', 'ytr@gmail.com', 'abcdddd@gmail.com', 'vinay', '3', 1000.00, 'Completed', 'railway station area', 'CMBT', 'vijayawada', 'chennai', '2025-02-17 16:45:54'),
(8, 35, 1, 1949, '2025-03-25', 'ytr@gmail.com', 'abcdddd@gmail.com', 'sathvik', '10', 1000.00, 'Completed', 'railway station area', 'CMBT', 'vijayawada', 'chennai', '2025-02-20 16:45:54'),
(9, 36, 1, 1949, '2025-03-25', 'ytr@gmail.com', 'abcdddd@gmail.com', 'sathvik', '13', 1000.00, 'Completed', 'railway station area', 'madhavaram busstand', 'vijayawada', 'chennai', '2025-02-21 16:45:54'),
(10, 37, 1, 1949, '2025-03-25', 'ytr@gmail.com', 'kalyanm1198.sse@saveetha.com', 'ram', '4', 1000.00, 'Completed', 'railway station area', 'CMBT', 'vijayawada', 'chennai', '2025-02-21 16:45:54'),
(11, 38, 6, 1989, '2025-03-25', 'abcd@gmail.com', 'kalyanm1198.sse@saveetha.com', 'kalyan', '8', 1150.00, 'Completed', 'Benz Circle', 'madhavaram', 'vijayawada', 'chennai', '2025-02-22 16:45:54'),
(12, 39, 6, 1989, '2025-03-25', 'abcd@gmail.com', 'kalyanm1198.sse@saveetha.com', 'ram', '10', 1150.00, 'Cancelled', 'Benz Circle', 'madhavaram', 'vijayawada', 'chennai', '2025-02-22 16:45:54'),
(13, 40, 1, 1949, '2025-03-25', 'ytr@gmail.com', 'kalyankrishnaa768@gmail.com', 'kalyan', '12', 1000.00, 'Completed', 'railway station area', 'CMBT', 'vijayawada', 'chennai', '2025-02-23 16:45:54'),
(14, 41, 3, 1989, '2025-03-26', 'abcd@gmail.com', 'mukkukalyan309@gmail.com', 'ram', '6', 1200.00, 'Completed', 'KPHB', 'R.K Beach', 'hyderabad', 'vizag', '2025-02-23 16:45:54'),
(15, 42, 3, 1989, '2025-03-26', 'abcd@gmail.com', 'mukkukalyan309@gmail.com', 'ram', '8', 1200.00, 'Completed', 'kukatpalli', 'RTC Complex', 'hyderabad', 'vizag', '2025-02-25 06:38:35'),
(16, 43, 4, 1949, '2025-03-26', 'ytr@gmail.com', 'kalyankrishnaa768@gmail.com', 'murari', '3', 500.00, 'Completed', 'poonamallie', 'aathmakur bus stand', 'chennai', 'nellore', '2025-02-27 16:35:43'),
(17, 44, 4, 1949, '2025-03-26', 'ytr@gmail.com', 'mukkukalyan309@gmail.com', 'murari', '6', 500.00, 'Cancelled', 'poonamallie', 'main bus stand', 'chennai', 'nellore', '2025-02-27 17:59:50'),
(18, 45, 4, 1949, '2025-03-26', 'ytr@gmail.com', 'mukkukalyan309@gmail.com', 'Kalyan', '15', 500.00, 'Cancelled', 'poonamallie', 'main bus stand', 'chennai', 'nellore', '2025-02-28 04:13:06'),
(19, 45, 4, 1949, '2025-03-26', 'ytr@gmail.com', 'mukkukalyan309@gmail.com', 'murari', '20', 500.00, 'Cancelled', 'poonamallie', 'main bus stand', 'chennai', 'nellore', '2025-02-28 04:13:06'),
(20, 46, 5, 1949, '2025-03-26', 'ytr@gmail.com', 'kalyanm1198.sse@saveetha.com', 'murari', '15', 500.00, 'Completed', 'aathmakur bus stand', 'koyambedu', 'nellore', 'chennai', '2025-03-02 14:49:41'),
(21, 47, 1, 1949, '2025-03-25', 'ytr@gmail.com', 'kalyankrishnaa768@gmail.com', 'murari', '11', 1000.00, 'Completed', 'railway station area', 'CMBT', 'vijayawada', 'chennai', '2025-03-04 02:51:06'),
(22, 48, 3, 1989, '2025-03-26', 'abcd@gmail.com', 'mukkukalyan309@gmail.com', 'kalyan krishna', '14', 1200.00, 'Completed', 'KPHB', 'RTC Complex', 'hyderabad', 'vizag', '2025-03-05 11:23:56'),
(23, 49, 5, 1949, '2025-03-26', 'ytr@gmail.com', 'mukkukalyan309@gmail.com', 'rasool', '8', 500.00, 'Completed', 'aathmakur bus stand', 'madhavaram', 'nellore', 'chennai', '2025-03-07 17:56:27'),
(24, 50, 4, 1949, '2025-03-26', 'ytr@gmail.com', 'kalyankrishnaa768@gmail.com', 'ram', '25', 500.00, 'Completed', 'poonamallie', 'main bus stand', 'chennai', 'Nellore', '2025-03-08 08:44:53'),
(25, 51, 3, 1989, '2025-03-26', 'abcd@gmail.com', 'kalyankrishnaa768@gmail.com', 'jeevan', '19', 1200.00, 'Completed', 'KPHB', 'RTC Complex', 'hyderabad', 'vizag', '2025-03-21 09:24:26'),
(26, 52, 17, 1939, '2025-05-25', 'abcd@gmail.com', 'kalyankrishnaa768@gmail.com', 'rasool', '10', 1500.00, 'Cancelled', 'RTC bus stand', 'punjagutta', 'nellore', 'Hyderabad', '2025-04-01 04:05:48'),
(27, 53, 17, 1939, '2025-05-25', 'abcd@gmail.com', 'kalyankrishnaa768@gmail.com', 'rasool', '10', 1500.00, 'Cancelled', 'RTC bus stand', 'punjagutta', 'nellore', 'Hyderabad', '2025-04-01 05:05:02'),
(28, 54, 17, 1939, '2025-05-25', 'abcd@gmail.com', 'kalyankrishnaa768@gmail.com', 'rakhi bhai', '16', 1500.00, 'Confirmed', 'RTC bus stand', 'KPHB', 'nellore', 'Hyderabad', '2025-04-01 05:35:50'),
(29, 55, 18, 1939, '2025-05-25', 'ytr@gmail.com', 'mukkukalyan309@gmail.com', 'Asif', '1', 1300.00, 'Confirmed', 'Potti Sriramulu Bus Stand', 'KPHB', 'nellore', 'Hyderabad', '2025-04-01 10:30:51'),
(30, 55, 18, 1939, '2025-05-25', 'ytr@gmail.com', 'mukkukalyan309@gmail.com', 'Venky', '3', 1300.00, 'Confirmed', 'Potti Sriramulu Bus Stand', 'KPHB', 'nellore', 'Hyderabad', '2025-04-01 10:30:51'),
(31, 55, 18, 1939, '2025-05-25', 'ytr@gmail.com', 'mukkukalyan309@gmail.com', 'Rasool', '6', 1300.00, 'Confirmed', 'Potti Sriramulu Bus Stand', 'KPHB', 'nellore', 'Hyderabad', '2025-04-01 10:30:51'),
(32, 55, 18, 1939, '2025-05-25', 'ytr@gmail.com', 'mukkukalyan309@gmail.com', 'kalyan krishna', '11', 1300.00, 'Confirmed', 'Potti Sriramulu Bus Stand', 'KPHB', 'nellore', 'Hyderabad', '2025-04-01 10:30:51'),
(33, 56, 17, 1939, '2025-05-25', 'abcd@gmail.com', 'kalyanm1198.sse@saveetha.com', 'ramesh', '3', 1500.00, 'Confirmed', 'RTC bus stand', 'KPHB', 'nellore', 'Hyderabad', '2025-04-01 10:38:25'),
(34, 57, 18, 1939, '2025-05-25', 'ytr@gmail.com', 'kalyanm1198.sse@saveetha.com', 'ram', '21', 1300.00, 'Confirmed', 'Potti Sriramulu Bus Stand', 'Hightech city', 'nellore', 'Hyderabad', '2025-04-01 10:39:23'),
(35, 58, 17, 1939, '2025-05-25', 'abcd@gmail.com', 'kalyankrishnaa768@gmail.com', 'ram', '26', 1500.00, 'Confirmed', 'RTC bus stand', 'KPHB', 'nellore', 'hyderabad', '2025-04-02 08:18:47'),
(36, 58, 17, 1939, '2025-05-25', 'abcd@gmail.com', 'kalyankrishnaa768@gmail.com', 'nikhil', '27', 1500.00, 'Confirmed', 'RTC bus stand', 'KPHB', 'nellore', 'hyderabad', '2025-04-02 08:18:47');

-- --------------------------------------------------------

--
-- Table structure for table `agency_wallet`
--

CREATE TABLE `agency_wallet` (
  `email` varchar(255) NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agency_wallet`
--

INSERT INTO `agency_wallet` (`email`, `balance`) VALUES
('abcd@gmail.com', 11600.00),
('ytr@gmail.com', 12010.00);

-- --------------------------------------------------------

--
-- Table structure for table `agency_wallet_transactions`
--

CREATE TABLE `agency_wallet_transactions` (
  `id` int(11) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `type` enum('credit','debit') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT NULL,
  `transaction_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agency_wallet_transactions`
--

INSERT INTO `agency_wallet_transactions` (`id`, `transaction_id`, `email`, `amount`, `type`, `description`, `status`, `transaction_date`) VALUES
(1, 'TXN17388335661005', 'abcd@gmail.com', 500.00, 'credit', 'Wallet money addition', 'completed', '2025-02-06 14:49:26'),
(2, 'TXN17388336326334', 'ytr@gmail.com', 1000.00, 'credit', 'Wallet money addition', 'completed', '2025-02-06 14:50:32'),
(7, 'TXN17399884531227', 'abcd@gmail.com', 1150.00, 'credit', 'Booking payment received', 'completed', '2025-02-19 23:37:33'),
(8, 'TXN17399889397890', 'abcd@gmail.com', 1150.00, 'credit', 'Booking payment received', 'completed', '2025-02-19 23:45:39'),
(9, 'TXN17401076789818', 'ytr@gmail.com', 1000.00, 'credit', 'Booking payment received', 'completed', '2025-02-21 08:44:38'),
(10, 'TXN17403256568873', 'abcd@gmail.com', 1200.00, 'credit', 'Booking payment received', 'completed', '2025-02-23 21:17:36'),
(11, 'TXN17404655155732', 'abcd@gmail.com', 1200.00, 'credit', 'Booking payment received', 'completed', '2025-02-25 12:08:35'),
(12, 'TXN17405634475658', 'abcd@gmail.com', 920.00, '', 'Refund for booking #33', 'completed', NULL),
(13, 'TXN17405666279499', 'abcd@gmail.com', 920.00, '', 'Refund for booking #33', 'completed', NULL),
(14, 'TXN17405678623284', 'abcd@gmail.com', 920.00, '', 'Refund for booking #33', 'completed', NULL),
(15, 'TXN17405703882841', 'abcd@gmail.com', 920.00, 'debit', 'Refund for booking #33', 'completed', '2025-02-26 12:46:28'),
(16, 'TXN17406252603327', 'abcd@gmail.com', 2000.00, 'credit', 'Wallet money addition', 'completed', '2025-02-27 08:31:00'),
(17, 'TXN17406741438603', 'ytr@gmail.com', 500.00, 'credit', 'Booking payment received', 'completed', '2025-02-27 22:05:43'),
(18, 'TXN17406791901174', 'ytr@gmail.com', 500.00, 'credit', 'Booking payment received', 'completed', '2025-02-27 23:29:50'),
(19, 'TXN17406792185100', 'ytr@gmail.com', 400.00, 'debit', 'Refund for booking #44', 'completed', '2025-02-27 19:00:18'),
(20, 'TXN17407156598958', 'ytr@gmail.com', 400.00, 'debit', 'Refund for booking #44', 'completed', '2025-02-28 05:07:39'),
(21, 'TXN17407159867639', 'ytr@gmail.com', 1000.00, 'credit', 'Booking payment received', 'completed', '2025-02-28 09:43:06'),
(22, 'TXN17407161729798', 'ytr@gmail.com', 800.00, 'debit', 'Refund for booking #45', 'completed', '2025-02-28 05:16:12'),
(23, 'TXN17407165491690', 'ytr@gmail.com', 600.00, 'credit', 'Wallet money addition', 'completed', '2025-02-28 09:52:29'),
(24, 'TXN17409269815734', 'ytr@gmail.com', 500.00, 'credit', 'Booking payment received', 'completed', '2025-03-02 20:19:41'),
(25, 'TXN17410566661477', 'ytr@gmail.com', 1000.00, 'credit', 'Booking payment received', 'completed', '2025-03-04 08:21:06'),
(26, 'TXN17411738362225', 'abcd@gmail.com', 1200.00, 'credit', 'Booking payment received', 'completed', '2025-03-05 16:53:56'),
(27, 'TXN17413701879535', 'ytr@gmail.com', 500.00, 'credit', 'Booking payment received', 'completed', '2025-03-07 23:26:27'),
(28, 'TXN17414234938245', 'ytr@gmail.com', 500.00, 'credit', 'Booking payment received', 'completed', '2025-03-08 14:14:53'),
(29, 'TXN17415087211374', 'abcd@gmail.com', 920.00, 'debit', 'Refund for booking #39', 'completed', '2025-03-09 09:25:21'),
(30, 'TXN17415978294394', 'ytr@gmail.com', 10.00, 'credit', 'Wallet money addition', 'completed', '2025-03-10 14:40:29'),
(31, 'TXN17425490665523', 'abcd@gmail.com', 1200.00, 'credit', 'Booking payment received', 'completed', '2025-03-21 14:54:26'),
(32, 'TXN17434803482797', 'abcd@gmail.com', 1500.00, 'credit', 'Booking payment received', 'completed', '2025-04-01 09:35:48'),
(33, 'TXN17434817771981', 'abcd@gmail.com', 1200.00, 'debit', 'Refund for booking #52', 'completed', '2025-04-01 06:29:37'),
(34, 'TXN17434839024404', 'abcd@gmail.com', 1500.00, 'credit', 'Booking payment received', 'completed', '2025-04-01 10:35:02'),
(35, 'TXN17434839253213', 'abcd@gmail.com', 1200.00, 'debit', 'Refund for booking #53', 'completed', '2025-04-01 10:35:25'),
(36, 'TXN17434857506143', 'abcd@gmail.com', 1500.00, 'credit', 'Booking payment received', 'completed', '2025-04-01 11:05:50'),
(37, 'TXN17435034518940', 'ytr@gmail.com', 5200.00, 'credit', 'Booking payment received', 'completed', '2025-04-01 16:00:51'),
(38, 'TXN17435039057655', 'abcd@gmail.com', 1500.00, 'credit', 'Booking payment received', 'completed', '2025-04-01 16:08:25'),
(39, 'TXN17435039634717', 'ytr@gmail.com', 1300.00, 'credit', 'Booking payment received', 'completed', '2025-04-01 16:09:23'),
(40, 'TXN17435819277588', 'abcd@gmail.com', 3000.00, 'credit', 'Booking payment received', 'completed', '2025-04-02 13:48:47');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bus_id` int(11) NOT NULL,
  `busname` varchar(255) NOT NULL,
  `journeydate` date NOT NULL,
  `ticketprice` decimal(10,2) NOT NULL,
  `booking_date` datetime NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `boarding_point` varchar(255) NOT NULL,
  `boarding_time` time DEFAULT NULL,
  `dropping_point` varchar(255) NOT NULL,
  `dropping_time` time DEFAULT NULL,
  `from_location` varchar(255) NOT NULL,
  `to_location` varchar(255) NOT NULL,
  `selected_seats` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `booking_status` enum('confirmed','cancelled') NOT NULL DEFAULT 'confirmed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `bus_id`, `busname`, `journeydate`, `ticketprice`, `booking_date`, `email`, `boarding_point`, `boarding_time`, `dropping_point`, `dropping_time`, `from_location`, `to_location`, `selected_seats`, `total_amount`, `booking_status`) VALUES
(31, 1, 1949, 'YTR TRAVELS', '2025-03-25', 1000.00, '2025-02-19 09:43:47', 'kalyankrishnaa768@gmail.com', 'railway station area', '10:30:00', 'madhavaram busstand', '17:00:00', 'vijayawada', 'chennai', '7, 8', 2000.00, 'confirmed'),
(32, 1, 1989, 'ABCD TRAVELS', '2025-03-25', 1150.00, '2025-02-19 09:50:05', 'kalyankrishnaa768@gmail.com', 'Benz Circle', '12:00:00', 'madhavaram', '17:00:00', 'vijayawada', 'chennai', '12', 1150.00, 'confirmed'),
(33, 1, 1989, 'ABCD TRAVELS', '2025-03-25', 1150.00, '2025-02-19 09:51:14', 'kalyankrishnaa768@gmail.com', 'Main bus stand', '10:30:00', 'madhavaram', '17:00:00', 'vijayawada', 'chennai', '3', 1150.00, 'cancelled'),
(34, 1, 1949, 'YTR TRAVELS', '2025-03-25', 1000.00, '2025-02-19 10:53:42', 'kalyankrishnaa768@gmail.com', 'railway station area', '10:00:00', 'madhavaram busstand', '17:00:00', 'vijayawada', 'chennai', '20, 24', 2000.00, 'confirmed'),
(35, 8, 1949, 'YTR TRAVELS', '2025-03-25', 1000.00, '2025-02-19 12:53:20', 'abcdddd@gmail.com', 'railway station area', '10:30:00', 'CMBT', '17:00:00', 'vijayawada', 'chennai', '3, 10', 2000.00, 'confirmed'),
(36, 8, 1949, 'YTR TRAVELS', '2025-03-25', 1000.00, '2025-02-19 13:01:16', 'abcdddd@gmail.com', 'railway station area', '10:30:00', 'madhavaram busstand', '17:00:00', 'vijayawada', 'chennai', '13', 1000.00, 'confirmed'),
(37, 3, 1949, 'YTR TRAVELS', '2025-03-25', 1000.00, '2025-02-19 22:52:41', 'kalyanm1198.sse@saveetha.com', 'railway station area', '12:00:00', 'CMBT', '17:00:00', 'vijayawada', 'chennai', '4', 1000.00, 'confirmed'),
(38, 3, 1989, 'ABCD TRAVELS', '2025-03-25', 1150.00, '2025-02-19 23:37:33', 'kalyanm1198.sse@saveetha.com', 'Benz Circle', '10:00:00', 'madhavaram', '17:00:00', 'vijayawada', 'chennai', '8', 1150.00, 'confirmed'),
(39, 3, 1989, 'ABCD TRAVELS', '2025-03-25', 1150.00, '2025-02-19 23:45:39', 'kalyanm1198.sse@saveetha.com', 'Benz Circle', '10:00:00', 'madhavaram', '17:00:00', 'vijayawada', 'chennai', '10', 1150.00, 'cancelled'),
(40, 1, 1949, 'YTR TRAVELS', '2025-03-25', 1000.00, '2025-02-21 08:44:38', 'kalyankrishnaa768@gmail.com', 'railway station area', '11:00:00', 'CMBT', '20:30:00', 'vijayawada', 'chennai', '12', 1000.00, 'confirmed'),
(41, 2, 1989, 'ABCD TRAVELS', '2025-03-26', 1200.00, '2025-02-23 21:17:36', 'mukkukalyan309@gmail.com', 'KPHB', '06:30:00', 'R.K Beach', '20:30:00', 'hyderabad', 'vizag', '6', 1200.00, 'confirmed'),
(42, 2, 1989, 'ABCD TRAVELS', '2025-03-26', 1200.00, '2025-02-25 12:08:35', 'mukkukalyan309@gmail.com', 'kukatpalli', '06:00:00', 'RTC Complex', '20:00:00', 'hyderabad', 'vizag', '8', 1200.00, 'confirmed'),
(43, 1, 1949, 'YTR TRAVELS', '2025-03-26', 500.00, '2025-02-27 22:05:43', 'kalyankrishnaa768@gmail.com', 'poonamallie', '08:00:00', 'aathmakur bus stand', '12:15:00', 'chennai', 'nellore', '3', 500.00, 'confirmed'),
(44, 2, 1949, 'YTR TRAVELS', '2025-03-26', 500.00, '2025-02-27 23:29:50', 'mukkukalyan309@gmail.com', 'poonamallie', '08:00:00', 'main bus stand', '12:00:00', 'chennai', 'nellore', '6', 500.00, 'cancelled'),
(45, 2, 1949, 'YTR TRAVELS', '2025-03-26', 500.00, '2025-02-28 09:43:06', 'mukkukalyan309@gmail.com', 'poonamallie', '08:00:00', 'main bus stand', '12:00:00', 'chennai', 'nellore', '15, 20', 1000.00, 'cancelled'),
(46, 3, 1949, 'YTR TRAVELS', '2025-03-26', 500.00, '2025-03-02 20:19:41', 'kalyanm1198.sse@saveetha.com', 'aathmakur bus stand', '13:00:00', 'koyambedu', '17:15:00', 'nellore', 'chennai', '15', 500.00, 'confirmed'),
(47, 1, 1949, 'YTR TRAVELS', '2025-03-25', 1000.00, '2025-03-04 08:21:06', 'kalyankrishnaa768@gmail.com', 'railway station area', '11:00:00', 'CMBT', '20:30:00', 'vijayawada', 'chennai', '11', 1000.00, 'confirmed'),
(48, 2, 1989, 'ABCD TRAVELS', '2025-03-26', 1200.00, '2025-03-05 16:53:56', 'mukkukalyan309@gmail.com', 'KPHB', '06:30:00', 'RTC Complex', '20:00:00', 'hyderabad', 'vizag', '14', 1200.00, 'confirmed'),
(49, 2, 1949, 'YTR TRAVELS', '2025-03-26', 500.00, '2025-03-07 23:26:27', 'mukkukalyan309@gmail.com', 'aathmakur bus stand', '13:00:00', 'madhavaram', '17:00:00', 'nellore', 'chennai', '8', 500.00, 'confirmed'),
(50, 1, 1949, 'YTR TRAVELS', '2025-03-26', 500.00, '2025-03-08 14:14:53', 'kalyankrishnaa768@gmail.com', 'poonamallie', '08:00:00', 'main bus stand', '12:00:00', 'chennai', 'Nellore', '25', 500.00, 'confirmed'),
(51, 1, 1989, 'ABCD TRAVELS', '2025-03-26', 1200.00, '2025-03-21 14:54:26', 'kalyankrishnaa768@gmail.com', 'KPHB', '06:30:00', 'RTC Complex', '20:00:00', 'hyderabad', 'vizag', '19', 1200.00, 'confirmed'),
(53, 1, 1939, 'ABCD TRAVELS', '2025-05-25', 1500.00, '2025-04-01 10:35:02', 'kalyankrishnaa768@gmail.com', 'RTC bus stand', '10:00:00', 'punjagutta', '20:00:00', 'nellore', 'Hyderabad', '10', 1500.00, 'confirmed'),
(54, 1, 1939, 'ABCD TRAVELS', '2025-05-25', 1500.00, '2025-04-01 11:05:50', 'kalyankrishnaa768@gmail.com', 'RTC bus stand', '10:00:00', 'KPHB', '20:30:00', 'nellore', 'Hyderabad', '16', 1500.00, 'confirmed'),
(55, 2, 1939, 'YTR TRAVELS', '2025-05-25', 1300.00, '2025-04-01 16:00:51', 'mukkukalyan309@gmail.com', 'Potti Sriramulu Bus Stand', '11:20:00', 'KPHB', '22:45:00', 'nellore', 'Hyderabad', '1, 3, 6, 11', 5200.00, 'confirmed'),
(56, 3, 1939, 'ABCD TRAVELS', '2025-05-25', 1500.00, '2025-04-01 16:08:25', 'kalyanm1198.sse@saveetha.com', 'RTC bus stand', '10:00:00', 'KPHB', '20:30:00', 'nellore', 'Hyderabad', '3', 1500.00, 'confirmed'),
(57, 3, 1939, 'YTR TRAVELS', '2025-05-25', 1300.00, '2025-04-01 16:09:23', 'kalyanm1198.sse@saveetha.com', 'Potti Sriramulu Bus Stand', '11:20:00', 'Hightech city', '22:30:00', 'nellore', 'Hyderabad', '21', 1300.00, 'confirmed'),
(58, 1, 1939, 'ABCD TRAVELS', '2025-05-25', 1500.00, '2025-04-02 13:48:47', 'kalyankrishnaa768@gmail.com', 'RTC bus stand', '10:00:00', 'KPHB', '20:30:00', 'nellore', 'hyderabad', '26, 27', 3000.00, 'confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `buses`
--

CREATE TABLE `buses` (
  `id` int(11) NOT NULL,
  `bus_number` varchar(50) NOT NULL,
  `capacity` int(11) NOT NULL,
  `type` enum('Standard','Luxury','AC','Sleeper') NOT NULL,
  `status` enum('Active','Maintenance','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `username` varchar(255) NOT NULL DEFAULT 'default_user',
  `email` varchar(255) NOT NULL DEFAULT 'default@example.com'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buses`
--

INSERT INTO `buses` (`id`, `bus_number`, `capacity`, `type`, `status`, `created_at`, `updated_at`, `username`, `email`) VALUES
(1, '1949', 40, 'Luxury', 'Active', '2025-01-29 08:56:57', '2025-02-05 10:31:35', 'YTR TRAVELS', 'ytr@gmail.com'),
(6, '1989', 40, 'AC', 'Active', '2025-02-05 12:26:03', '2025-03-05 05:12:45', 'ABCD TRAVELS', 'abcd@gmail.com'),
(8, '1939', 36, 'Luxury', 'Active', '2025-03-09 17:33:46', '2025-03-09 17:33:46', 'YTR TRAVELS', 'ytr@gmail.com'),
(10, '1919', 36, 'AC', 'Active', '2025-03-09 17:36:06', '2025-03-11 03:27:42', 'YTR TRAVELS', 'ytr@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `bus_sell`
--

CREATE TABLE `bus_sell` (
  `id` int(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `seat_no` int(255) NOT NULL,
  `bus_id` int(11) NOT NULL,
  `busname` varchar(255) NOT NULL,
  `journeydate` date NOT NULL,
  `fromplace` varchar(255) NOT NULL,
  `toplace` varchar(255) NOT NULL,
  `boarding_time` time(6) NOT NULL,
  `ticketprice` int(255) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `ticketUpload` varchar(255) NOT NULL,
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus_sell`
--

INSERT INTO `bus_sell` (`id`, `email`, `seat_no`, `bus_id`, `busname`, `journeydate`, `fromplace`, `toplace`, `boarding_time`, `ticketprice`, `booking_id`, `ticketUpload`, `status`) VALUES
(10, 'kalyankrishnaa768@gmail.com', 12, 1949, 'ytr', '2025-03-25', 'vijayawada', 'chennai', '11:00:00.000000', 1000, 40, 'uploads/Joker knife [1920 x 1080] (2).jpg', 'accepted'),
(11, 'mukkukalyan309@gmail.com', 4, 1989, 'abcd', '2025-02-22', 'vijayawada', 'chennai', '12:00:00.000000', 1000, 0, 'uploads/charan.webp', 'rejected'),
(12, 'kalyankrishnaa768@gmail.com', 14, 1989, 'abcd', '2025-02-22', 'vijayawada', 'chennai', '12:00:00.000000', 1000, 0, 'uploads/Joker knife [1920 x 1080] (2).jpg', 'accepted'),
(13, 'mukkukalyan309@gmail.com', 6, 1989, 'abcd', '2025-03-26', 'hyderabad', 'vizag', '06:30:00.000000', 1200, 41, 'uploads/mukkukalyan309@gmail.pdf', 'accepted'),
(14, 'mukkukalyan309@gmail.com', 8, 1989, 'abcd', '2025-03-26', 'hyderabad', 'vizag', '06:00:00.000000', 1200, 42, 'uploads/mukkukalyan309@gmail.pdf', 'accepted'),
(15, 'kalyankrishnaa768@gmail.com', 3, 1949, 'ytr', '2025-03-26', 'chennai', 'nellore', '08:00:00.000000', 500, 43, 'uploads/charan.webp', 'accepted'),
(16, 'kalyanm1198.sse@saveetha.com', 15, 1949, 'ytr', '2025-03-26', 'nellore', 'chennai', '13:00:00.000000', 500, 46, 'uploads/kalyanm1198@gmail.pdf', 'accepted'),
(17, 'kalyankrishnaa768@gmail.com', 11, 1949, 'ytr', '2025-03-25', 'vijayawada', 'chennai', '10:30:00.000000', 1000, 32, 'uploads/kalyankrishnaa768@gmail.pdf', 'accepted'),
(18, 'mukkukalyan309@gmail.com', 8, 1949, 'ytr', '2025-03-26', 'Nellore', 'Chennai', '13:00:00.000000', 500, 49, 'uploads/charan.webp', 'accepted'),
(23, 'kalyankrishnaa768@gmail.com', 25, 1949, 'YTR TRAVELS', '2025-03-26', 'chennai', 'Nellore', '00:00:08.000000', 500, 50, 'from my bookings', 'accepted'),
(25, 'kalyankrishnaa768@gmail.com', 10, 1939, 'ABCD TRAVELS', '2025-05-25', 'nellore', 'Hyderabad', '00:00:10.000000', 1500, 53, 'from my bookings', 'accepted'),
(26, 'kalyanm1198.sse@saveetha.com', 3, 1939, 'ABCD TRAVELS', '2025-05-25', 'nellore', 'Hyderabad', '00:00:10.000000', 1500, 56, 'from my bookings', 'accepted'),
(27, 'kalyanm1198.sse@saveetha.com', 21, 1939, 'YTR TRAVELS', '2025-05-25', 'nellore', 'Hyderabad', '00:00:11.000000', 1300, 57, 'from my bookings', 'accepted'),
(29, 'kalyankrishnaa768@gmail.com', 26, 1939, 'ABCD TRAVELS', '2025-05-25', 'nellore', 'hyderabad', '00:00:10.000000', 1500, 58, 'from my bookings', 'accepted'),
(30, 'kalyankrishnaa768@gmail.com', 27, 1939, 'ABCD TRAVELS', '2025-05-25', 'nellore', 'hyderabad', '00:00:10.000000', 1500, 58, 'from my bookings', 'accepted');

-- --------------------------------------------------------

--
-- Table structure for table `bus_upload`
--

CREATE TABLE `bus_upload` (
  `id` int(255) NOT NULL,
  `bookingplatform` varchar(255) NOT NULL,
  `upi_id` varchar(255) NOT NULL,
  `ticketupload` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus_upload`
--

INSERT INTO `bus_upload` (`id`, `bookingplatform`, `upi_id`, `ticketupload`) VALUES
(1, 'red bus', '7989250320@ybl', 'uploads/bus buy 1.jpg'),
(2, 'red bus', '7989250320@ybl', 'uploads/train sell 1.jpg'),
(3, 'abhi bus', '7989250320@ibl', 'uploads/contact.jpg'),
(4, 'abhi bus', '22223', 'uploads/contact.jpg'),
(5, 'red bus', '7989250320@ibl', 'uploads/search_bus.jpg'),
(6, 'abhi bus', '7989250320@ibl', 'uploads/search_train1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` int(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `username`, `email`, `subject`, `message`) VALUES
(1, 'kalyan', 'kalyankrishnaa768@gmail.com', 'nb', 'kjhgvb'),
(2, 'kalyan', 'kalyankrishnaa768@gmail.com', 'nb', 'ghhghjhv'),
(3, 'kalyan', 'kalyankrishnaa768@gmail.com', 'nb', 'tdfgf'),
(4, 'kalyan', 'kalyankrishnaa768@gmail.com', 'nb', 'oiuy');

-- --------------------------------------------------------

--
-- Table structure for table `new_bookings`
--

CREATE TABLE `new_bookings` (
  `id` int(11) NOT NULL,
  `ticket_id` varchar(50) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `passenger_name` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` varchar(20) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `passenger_email` varchar(255) NOT NULL,
  `journey_date` date NOT NULL,
  `from_location` varchar(100) NOT NULL,
  `to_location` varchar(100) NOT NULL,
  `boarding_point` varchar(100) NOT NULL,
  `boarding_time` time NOT NULL,
  `dropping_point` varchar(100) NOT NULL,
  `dropping_time` time NOT NULL,
  `seat_no` varchar(20) NOT NULL,
  `ticket_price` decimal(10,2) NOT NULL,
  `convenience_fee` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `route_id` int(11) NOT NULL,
  `bus_id` int(11) NOT NULL,
  `agency_email` varchar(100) NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `booking_status` varchar(20) NOT NULL DEFAULT 'CONFIRMED',
  `payment_status` varchar(20) DEFAULT 'PAID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `new_bookings`
--

INSERT INTO `new_bookings` (`id`, `ticket_id`, `user_email`, `passenger_name`, `age`, `gender`, `phone_number`, `passenger_email`, `journey_date`, `from_location`, `to_location`, `boarding_point`, `boarding_time`, `dropping_point`, `dropping_time`, `seat_no`, `ticket_price`, `convenience_fee`, `total_amount`, `route_id`, `bus_id`, `agency_email`, `booking_date`, `booking_status`, `payment_status`) VALUES
(1, '13', 'kalyankrishnaa768@gmail.com', 'karthik', 20, 'male', '8794125630', 'karthik@gmail.com', '2025-03-26', 'hyderabad', 'vizag', 'KPHB', '06:30:00', 'RTC Complex', '20:00:00', '6', 1250.00, 50.00, 1300.00, 3, 1989, 'abcd@gmail.com', '2025-02-24 07:36:13', 'COMPLETED', 'PAID'),
(5, '10', 'mukkukalyan309@gmail.com', 'ram', 21, 'male', '9397683728', 'mukkukalyan309@gmail.com', '2025-03-25', 'vijayawada', 'chennai', 'railway station area', '11:00:00', 'poonamallie', '21:30:00', '12', 1000.00, 50.00, 1050.00, 1, 1949, 'ytr@gmail.com', '2025-02-25 04:12:03', 'COMPLETED', 'PAID'),
(13, '14', 'kalyankrishnaa768@gmail.com', 'kalyan', 20, 'male', '7989250320', 'kalyankrishnaa768@gmail.com', '2025-03-26', 'hyderabad', 'vizag', 'kukatpalli', '06:00:00', 'gajuwaka', '19:00:00', '8', 1200.00, 50.00, 1250.00, 3, 1989, 'abcd@gmail.com', '2025-02-26 18:11:42', 'COMPLETED', 'PAID'),
(14, '15', 'mukkukalyan309@gmail.com', 'venky', 20, 'male', '7845123694', 'venkey@gmail.com', '2025-03-26', 'chennai', 'nellore', 'koyambedu', '08:00:00', 'aathmakur bus stand', '12:15:00', '3', 500.00, 50.00, 550.00, 4, 1949, 'ytr@gmail.com', '2025-02-27 17:49:44', 'COMPLETED', 'PAID'),
(15, '16', 'mukkukalyan309@gmail.com', 'Asif', 22, 'male', '9398600153', 'mukkukalyan309@gmail.com', '2025-03-26', 'nellore', 'chennai', 'aathmakur bus stand', '13:00:00', 'koyambedu', '17:15:00', '15', 500.00, 50.00, 550.00, 5, 1949, 'ytr@gmail.com', '2025-03-03 15:54:50', 'COMPLETED', 'PAID'),
(16, '17', 'mukkukalyan309@gmail.com', 'krishna', 22, 'male', '7989234543', 'krishna@gmail.com', '2025-03-25', 'vijayawada', 'chennai', 'railway station area', '10:30:00', 'CMBT', '20:30:00', '11', 1000.00, 50.00, 1050.00, 1, 1949, 'ytr@gmail.com', '2025-03-04 05:58:32', 'COMPLETED', 'PAID'),
(18, '18', 'kalyanm1198.sse@saveetha.com', 'nikhil', 22, 'male', '7989250320', 'kalyanm1198.sse@saveetha.com', '2025-03-26', 'Nellore', 'Chennai', 'aathmakur bus stand', '13:00:00', 'madhavaram', '17:00:00', '8', 500.00, 50.00, 550.00, 5, 1949, 'ytr@gmail.com', '2025-03-08 09:21:47', 'COMPLETED', 'PAID'),
(20, '25', 'mukkukalyan309@gmail.com', 'venky', 22, 'male', '7989234543', 'venky@gmail.com', '2025-05-25', 'nellore', 'Hyderabad', 'Aathmakur bus stand', '00:00:10', 'KPHB', '20:30:00', '10', 1500.00, 50.00, 1550.00, 17, 1939, 'abcd@gmail.com', '2025-04-01 05:14:02', 'CONFIRMED', 'PAID'),
(21, '29', 'kalyankrishnaa768@gmail.com', 'kalyan krishna', 22, 'male', '09397683728', 'mukkukalyan309@gmail.com', '2025-05-25', 'nellore', 'hyderabad', 'RTC bus stand', '00:00:10', 'punjagutta', '20:00:00', '26', 1500.00, 50.00, 1550.00, 17, 1939, 'abcd@gmail.com', '2025-04-02 09:03:09', 'CONFIRMED', 'PAID');

-- --------------------------------------------------------

--
-- Table structure for table `passengers`
--

CREATE TABLE `passengers` (
  `id` int(11) NOT NULL,
  `agency_email` varchar(255) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `age` int(3) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `passenger_email` varchar(255) NOT NULL,
  `seat_number` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `passengers`
--

INSERT INTO `passengers` (`id`, `agency_email`, `booking_id`, `name`, `age`, `gender`, `phone_number`, `passenger_email`, `seat_number`, `created_at`) VALUES
(1, 'ytr@gmail.com', 31, 'kalyan krishna', 21, 'male', '7989250321', 'kalyankrishnaa768@gmail.com', '7', '2025-02-19 04:13:47'),
(2, 'ytr@gmail.com', 31, 'ram', 22, 'male', '6312547899', 'mukkukalyan309@gmail.com', '8', '2025-02-19 04:13:47'),
(3, 'abcd@gmail.com', 32, 'kalyan krishna', 21, 'male', '7989250321', 'kalyankrishnaa768@gmail.com', '12', '2025-02-19 04:20:05'),
(4, 'abcd@gmail.com', 33, 'kalyan krishna', 12, 'male', '7989250321', 'kalyankrishnaa768@gmail.com', '3', '2025-02-19 04:21:14'),
(5, 'ytr@gmail.com', 34, 'kalyan krishna', 21, 'male', '7989250321', 'kalyankrishnaa768@gmail.com', '20', '2025-02-19 05:23:42'),
(6, 'ytr@gmail.com', 34, 'ram', 22, 'male', '6312547899', 'mukkukalyan309@gmail.com', '24', '2025-02-19 05:23:42'),
(7, 'ytr@gmail.com', 35, 'saatvik', 21, 'male', '7412589630', 'abcdddd@gmail.com', '3', '2025-02-19 07:23:20'),
(8, 'ytr@gmail.com', 35, 'vinay', 22, 'male', '7412589630', 'abcdddd@gmail.com', '10', '2025-02-19 07:23:20'),
(9, 'ytr@gmail.com', 36, 'kalyan krishna', 12, 'male', '7989250321', 'kalyankrishnaa768@gmail.com', '13', '2025-02-19 07:31:16'),
(10, 'ytr@gmail.com', 37, 'ram', 20, 'male', '6312547899', 'mukkukalyan309@gmail.com', '4', '2025-02-19 17:22:41'),
(11, 'abcd@gmail.com', 38, 'Kalyan', 20, 'male', '7989250321', 'kalyankrishnaa768@gmail.com', '8', '2025-02-19 18:07:33'),
(12, 'abcd@gmail.com', 39, 'ram', 20, 'male', '6312547899', 'mukkukalyan309@gmail.com', '10', '2025-02-19 18:15:39'),
(13, 'ytr@gmail.com', 40, 'Kalyan', 34, 'male', '7989250321', 'kalyankrishnaa768@gmail.com', '12', '2025-02-21 03:14:38'),
(14, 'abcd@gmail.com', 41, 'ram', 21, 'male', '6312547899', 'mukkukalyan309@gmail.com', '6', '2025-02-23 15:47:36'),
(15, 'abcd@gmail.com', 42, 'ram', 20, 'male', '9397683728', 'mukkukalyan309@gmail.com', '8', '2025-02-25 06:38:35'),
(16, 'ytr@gmail.com', 43, 'murari', 25, 'male', '9876543210', 'murari@gmail.com', '3', '2025-02-27 16:35:43'),
(17, 'ytr@gmail.com', 44, 'murari', 25, 'male', '9876543210', 'murari@gmail.com', '6', '2025-02-27 17:59:50'),
(18, 'ytr@gmail.com', 45, 'Kalyan', 20, 'male', '07989250320', 'kalyankrishnaa768@gmail.com', '15', '2025-02-28 04:13:06'),
(19, 'ytr@gmail.com', 45, 'murari', 26, 'male', '09876543210', 'murari@gmail.com', '20', '2025-02-28 04:13:06'),
(20, 'ytr@gmail.com', 46, 'murari', 25, 'male', '9876543210', 'murari@gmail.com', '15', '2025-03-02 14:49:41'),
(21, 'ytr@gmail.com', 47, 'murari', 25, 'male', '9876543210', 'murari@gmail.com', '11', '2025-03-04 02:51:06'),
(22, 'abcd@gmail.com', 48, 'kalyan krishna', 25, 'male', '07989250320', 'kalyankrishnaa768@gmail.com', '14', '2025-03-05 11:23:56'),
(23, 'ytr@gmail.com', 49, 'rasool', 20, 'male', '7989250320', 'kalyankrishnaa768@gmail.com', '8', '2025-03-07 17:56:27'),
(24, 'ytr@gmail.com', 50, 'ram', 22, 'male', '7894563218', 'kalyankrishnaa768@gmail.com', '25', '2025-03-08 08:44:53'),
(25, 'abcd@gmail.com', 51, 'jeevan', 40, 'male', '9876543211', 'jeevan@gmail.com', '19', '2025-03-21 09:24:26'),
(26, 'abcd@gmail.com', 52, 'rasool', 20, 'male', '1234567890', 'xyz@gmail.com', '10', '2025-04-01 04:05:48'),
(27, 'abcd@gmail.com', 53, 'rasool', 20, 'male', '1234567890', 'xyz@gmail.com', '10', '2025-04-01 05:05:02'),
(28, 'abcd@gmail.com', 54, 'rakhi bhai', 21, 'male', '8418522587', 'rakhi@gmail.com', '16', '2025-04-01 05:35:50'),
(29, 'ytr@gmail.com', 55, 'Asif', 20, 'male', '7989250320', 'asif@gmail.com', '1', '2025-04-01 10:30:51'),
(30, 'ytr@gmail.com', 55, 'Venky', 20, 'male', '7989250320', 'venky@gmail.com', '3', '2025-04-01 10:30:51'),
(31, 'ytr@gmail.com', 55, 'Rasool', 20, 'male', '7989250320', 'rasool@gmail.com', '6', '2025-04-01 10:30:51'),
(32, 'ytr@gmail.com', 55, 'kalyan krishna', 20, 'male', '7989250320', 'kal@gmail.com', '11', '2025-04-01 10:30:51'),
(33, 'abcd@gmail.com', 56, 'ramesh', 22, 'male', '7989250320', 'Ramesh@gmail.com', '3', '2025-04-01 10:38:25'),
(34, 'ytr@gmail.com', 57, 'ram', 22, 'male', '09397683728', 'mukkukalyan309@gmail.com', '21', '2025-04-01 10:39:23'),
(35, 'abcd@gmail.com', 58, 'ram', 22, 'male', '09397683728', 'mukkukalyan309@gmail.com', '26', '2025-04-02 08:18:47'),
(36, 'abcd@gmail.com', 58, 'nikhil', 21, 'male', '07989250320', 'kalyanm1198.sse@saveetha.com', '27', '2025-04-02 08:18:47');

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `id` int(11) NOT NULL,
  `from_location` varchar(100) NOT NULL,
  `to_location` varchar(100) NOT NULL,
  `departure_date` date NOT NULL,
  `departure_time` time NOT NULL,
  `distance` double DEFAULT 0,
  `duration` double DEFAULT NULL,
  `base_fare` double(10,2) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Scheduled',
  `bus_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `from_points` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`from_points`)),
  `to_points` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`to_points`)),
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`id`, `from_location`, `to_location`, `departure_date`, `departure_time`, `distance`, `duration`, `base_fare`, `status`, `bus_id`, `created_at`, `updated_at`, `from_points`, `to_points`, `username`, `email`) VALUES
(1, 'Vijayawada', 'Chennai', '2025-03-25', '11:00:00', 450, 8, 1000.00, 'Active', 1949, '2025-02-03 05:16:21', '2025-02-06 04:28:28', '[{\"name\":\"railway station area\",\"time\":\"11:00\"},{\"name\":\"main busstand\",\"time\":\"11:30\"}]', '[{\"name\":\"madhavaram busstand\",\"time\":\"20:00\"},{\"name\":\"CMBT\",\"time\":\"20:30\"},{\"name\":\"poonamallie\",\"time\":\"21:30\"}]', 'YTR TRAVELS', 'ytr@gmail.com'),
(2, 'Chennai', 'vijayawada', '2025-03-25', '19:00:00', 450, 8, 1000.00, 'Active', 1949, '2025-02-03 06:11:56', '2025-02-06 04:28:38', '[{\"name\":\"madhavaram busstand\",\"time\":\"19:00\"},{\"name\":\"CMBT\",\"time\":\"19:30\"},{\"name\":\"poonamallie\",\"time\":\"20:30\"}]', '[{\"name\":\"railway station area\",\"time\":\"5:30\"},{\"name\":\"main busstand\",\"time\":\"6:00\"}]', 'YTR TRAVELS', 'ytr@gmail.com'),
(3, 'Hyderabad', 'Vizag', '2025-03-26', '06:00:00', 500, 10, 1200.00, 'Active', 1989, '2025-02-03 17:49:34', '2025-02-06 04:28:04', '[{\"name\":\"kukatpalli\",\"time\":\"06:00\"},{\"name\":\"KPHB\",\"time\":\"06:30\"},{\"name\":\"punjagutta\",\"time\":\"07:00\"}]', '[{\"name\":\"gajuwaka\",\"time\":\"19:00\"},{\"name\":\"RTC Complex\",\"time\":\"20:00\"},{\"name\":\"R.K Beach\",\"time\":\"20:30\"}]', 'ABCD TRAVELS', 'abcd@gmail.com'),
(4, 'Chennai', 'nellore', '2025-03-26', '08:00:00', 176, 4, 500.00, 'Active', 1949, '2025-02-05 16:27:05', '2025-02-06 04:28:45', '[{\"name\":\"poonamallie\",\"time\":\"08:00\"},{\"name\":\"koyambedu\",\"time\":\"08:15\"}]', '[{\"name\":\"main bus stand\",\"time\":\"12:00\"},{\"name\":\"aathmakur bus stand\",\"time\":\"12:15\"}]', 'YTR TRAVELS', 'ytr@gmail.com'),
(5, 'Nellore', 'Chennai', '2025-03-26', '13:00:00', 176, 4, 500.00, 'Active', 1949, '2025-02-05 16:50:37', '2025-02-06 04:28:52', '[{\"name\":\"aathmakur bus stand\",\"time\":\"13:00\"},{\"name\":\"RTC bus stand\",\"time\":\"13:15\"}]', '[{\"name\":\"madhavaram\",\"time\":\"17:00\"},{\"name\":\"koyambedu\",\"time\":\"17:15\"}]', 'YTR TRAVELS', 'ytr@gmail.com'),
(6, 'Vijayawada', 'Chennai', '2025-03-25', '10:00:00', 450, 9, 1150.00, 'Active', 1989, '2025-02-05 17:09:22', '2025-02-07 03:45:22', '[{\"name\":\"Benz Circle\",\"time\":\"10:00\"},{\"name\":\"Main bus stand\",\"time\":\"10:15\"}]', '[{\"name\":\"madhavaram\",\"time\":\"17:00\"},{\"name\":\"koyambedu\",\"time\":\"17:15\"}]', 'ABCD TRAVELS', 'abcd@gmail.com'),
(7, 'Chennai', 'Vijayawada', '2025-03-25', '12:00:00', 450, 9, 1150.00, 'Active', 1989, '2025-02-05 18:00:27', '2025-02-06 04:27:46', '[{\"name\":\"poonamallie\",\"time\":\"12:00\"},{\"name\":\"koyambedu\",\"time\":\"12:20\"},{\"name\":\"madhavaram\",\"time\":\"12:30\"}]', '[{\"name\":\"Benz Circle\",\"time\":\"21:30\"},{\"name\":\"main bus stand\",\"time\":\"21:45\"}]', 'ABCD TRAVELS', 'abcd@gmail.com'),
(10, 'Chennai ', 'Benguluru', '2025-03-25', '12:00:00', 400, 6, 800.00, 'Active', 1919, '2025-03-09 17:37:31', '2025-03-31 09:53:55', '[{\"name\":\"poonamallie\",\"time\":\"12:00\"},{\"name\":\"koyambedu\",\"time\":\"12:20\"},{\"name\":\"madhavaram\",\"time\":\"12:30\"}]', '[{\"name\":\"Tin factory\",\"time\":\"14:00\"},{\"name\":\"hebbal\",\"time\":\"15:00\"}]', 'YTR TRAVELS', 'ytr@gmail.com'),
(11, 'chennai', 'vizag', '2025-03-25', '06:00:00', 600, 12, 1500.00, 'Active', 1929, '2025-03-09 17:38:49', '2025-03-31 09:52:28', '[{\"name\":\"poonamallie\",\"time\":\"12:00\"},{\"name\":\"koyambedu\",\"time\":\"12:20\"},{\"name\":\"madhavaram\",\"time\":\"12:30\"}]', '[{\"name\":\"gajuwaka\",\"time\":\"19:00\"},{\"name\":\"RTC Complex\",\"time\":\"20:00\"},{\"name\":\"R.K Beach\",\"time\":\"20:30\"}]', 'YTR TRAVELS', 'ytr@gmail.com'),
(12, 'Vizag', 'Vijayawada', '2025-03-25', '08:00:00', 250, 4, 500.00, 'Active', 1939, '2025-03-09 17:40:21', '2025-03-31 09:53:35', '[{\"name\":\"gajuwaka\",\"time\":\"19:00\"},{\"name\":\"RTC Complex\",\"time\":\"20:00\"},{\"name\":\"R.K Beach\",\"time\":\"20:30\"}]', '[{\"name\":\"Benz Circle\",\"time\":\"10:00\"},{\"name\":\"Main bus stand\",\"time\":\"10:15\"}]', 'YTR TRAVELS', 'ytr@gmail.com'),
(13, 'chennai', 'tirupathi', '2025-03-14', '11:30:00', 120, 3, 1000.00, 'Active', 1989, '2025-03-13 12:01:10', '2025-03-13 12:09:10', '[{\"name\":\"poonamallie\",\"time\":\"10:00\"},{\"name\":\"koyambedu\",\"time\":\"10:20\"},{\"name\":\"madhavaram\",\"time\":\"10:30\"}]', '[{\"name\":\"renugunta\",\"time\":\"13:00\"},{\"name\":\"main bus stand\",\"time\":\"13:20\"}]', 'ABCD TRAVELS', 'abcd@gmail.com'),
(14, 'tirupathi', 'chennai', '2025-03-16', '10:00:00', 120, 3, 1000.00, 'Active', 1989, '2025-03-13 12:25:22', '2025-03-13 12:25:22', '[{\"name\":\"renugunta\",\"time\":\"10:20\"},{\"name\":\"main bus stand\",\"time\":\"10:00\"}]', '[{\"name\":\"madhavaram\",\"time\":\"13:20\"},{\"name\":\"koyambedu\",\"time\":\"13:50\"},{\"name\":\"poonamallie\",\"time\":\"14:20\"}]', 'ABCD TRAVELS', 'abcd@gmail.com'),
(15, 'Chennai', 'Benguluru', '2025-03-23', '08:00:00', 400, 6, 800.00, 'Active', 1939, '2025-03-22 07:54:33', '2025-03-31 09:43:19', '[{\"name\":\"madhavaram\",\"time\":\"08:00\"},{\"name\":\"poonamallie\",\"time\":\"08:40\"}]', '[{\"name\":\"Tin factory\",\"time\":\"14:00\"},{\"name\":\"hebbal\",\"time\":\"15:00\"}]', 'YTR TRAVELS', 'ytr@gmail.com'),
(17, 'Nellore ', 'Hyderabad', '2025-05-25', '10:00:00', 500, 10, 1500.00, 'Active', 1939, '2025-03-31 11:54:58', '2025-03-31 11:54:58', '[{\"name\":\"RTC bus stand\",\"time\":\"10:00\"},{\"name\":\"Aathmakur bus stand\",\"time\":\"10:20\"}]', '[{\"name\":\"punjagutta\",\"time\":\"20:00\"},{\"name\":\"KPHB\",\"time\":\"20:30\"},{\"name\":\"KP\",\"time\":\"20:40\"}]', 'ABCD TRAVELS', 'abcd@gmail.com'),
(18, 'Nellore', 'Hyderabad', '2025-05-25', '11:00:00', 500, 11, 1300.00, 'Active', 1939, '2025-04-01 10:19:08', '2025-04-01 10:21:17', '[{\"name\":\"RTC Bus Stand\",\"time\":\"11:00\"},{\"name\":\"Potti Sriramulu Bus Stand\",\"time\":\"11:20\"},{\"name\":\"Railway Station\",\"time\":\"11:30\"}]', '[{\"name\":\"Hightech city\",\"time\":\"22:30\"},{\"name\":\"KPHB\",\"time\":\"22:45\"},{\"name\":\"Jublihills\",\"time\":\"23:30\"}]', 'YTR TRAVELS', 'ytr@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `signup`
--

CREATE TABLE `signup` (
  `id` int(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `user_type` varchar(255) DEFAULT '2',
  `phonenumber` bigint(10) NOT NULL,
  `gender` enum('Male','Female','Others') NOT NULL,
  `address` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `signup`
--

INSERT INTO `signup` (`id`, `username`, `email`, `user_type`, `phonenumber`, `gender`, `address`, `password`) VALUES
(1, 'kalyan krishna', 'kalyankrishnaa768@gmail.com', '2', 7989250320, 'Male', 'vaggam palli, 4-86', 'Kalyan11'),
(2, 'Ram', 'mukkukalyan309@gmail.com', '2', 7989250320, 'Male', 'Nellore, AP', 'Kalyan12'),
(3, 'Charan', 'kalyanm1198.sse@saveetha.com', '2', 7989250320, 'Male', 'vaggam palli, 4-86', 'Kalyan13'),
(4, 'ADMIN', 'admin@gmail.com', '1', 7989250320, '', 'station area, Nellore', 'Kalyan33'),
(6, 'YTR TRAVELS', 'ytr@gmail.com', '3', 7989250320, '', 'Market Area, Nellore', 'Kalyan21'),
(7, 'ABCD TRAVELS', 'abcd@gmail.com', '3', 7989250320, '', 'bus stand opposite, Nellore', 'Kalyan22');

-- --------------------------------------------------------

--
-- Table structure for table `train_bookings`
--

CREATE TABLE `train_bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `train_id` int(11) NOT NULL,
  `trainname` varchar(255) NOT NULL,
  `journeydate` date NOT NULL,
  `startingtime` time NOT NULL,
  `ticketprice` decimal(10,2) NOT NULL,
  `departure_location` varchar(255) DEFAULT NULL,
  `dropping_location` varchar(255) DEFAULT NULL,
  `booking_date` datetime NOT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `train_bookings`
--

INSERT INTO `train_bookings` (`id`, `user_id`, `train_id`, `trainname`, `journeydate`, `startingtime`, `ticketprice`, `departure_location`, `dropping_location`, `booking_date`, `email`) VALUES
(1, 1, 3, 'fjhgg', '2025-01-07', '16:07:00', 400.00, NULL, NULL, '2025-01-15 16:09:03', 'kalyankrishnaa768@gmail.com'),
(4, 3, 1, 'fjhgg', '2025-01-02', '09:45:00', 600.00, NULL, NULL, '2025-01-27 22:01:52', NULL),
(5, 3, 1, 'fjhgg', '2025-01-02', '09:45:00', 600.00, 'hyderabad charminar', 'vizag junction', '2025-01-27 22:47:03', NULL),
(6, 3, 1, 'fjhgg', '2025-01-02', '09:45:00', 600.00, 'hyderabad junction', 'vizag junction', '2025-01-27 23:02:00', NULL),
(7, 3, 1, 'fjhgg', '2025-01-02', '09:45:00', 600.00, 'hyderabad junction', 'vizag junction', '2025-01-28 09:09:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `train_sell`
--

CREATE TABLE `train_sell` (
  `id` int(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `seatno` int(255) NOT NULL,
  `coachname` varchar(255) NOT NULL,
  `trainname` varchar(255) NOT NULL,
  `journeydate` date NOT NULL,
  `fromplace` varchar(255) NOT NULL,
  `toplace` varchar(255) NOT NULL,
  `startingtime` time(6) NOT NULL,
  `ticketprice` int(255) NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `train_sell`
--

INSERT INTO `train_sell` (`id`, `email`, `seatno`, `coachname`, `trainname`, `journeydate`, `fromplace`, `toplace`, `startingtime`, `ticketprice`, `status`) VALUES
(1, 'mukkukalyan309@gmail.com', 44, 'c2', 'fjhgg', '2025-01-02', 'hyderabad', 'vizag', '09:45:00.000000', 600, 'accepted'),
(2, 'mukkukalyan309@gmail.com', 32, 's1', 'fjhgg', '2025-01-06', 'mumbai', 'hyderabad', '15:51:00.000000', 1000, 'accepted'),
(3, 'kalyankrishnaa768@gmail.com', 25, 'c2', 'fjhgg', '2025-01-07', 'vizag', 'nellore', '16:07:00.000000', 400, 'accepted'),
(4, 'mukkukalyan309@gmail.com', 17, 'c1', 'fjhgg', '2025-01-07', 'chennai', 'vijayawada', '18:38:00.000000', 500, 'pending'),
(5, 'kalyankrishnaa768@gmail.com', 23, 's2', 'fjhgg', '2025-01-02', 'guntur', 'vizag', '10:25:00.000000', 400, 'pending'),
(6, 'kalyankrishnaa768@gmail.com', 42, 's1', 'fjhgg', '2025-01-08', 'bangaluru', 'chennai', '10:31:00.000000', 500, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `train_upload`
--

CREATE TABLE `train_upload` (
  `id` int(255) NOT NULL,
  `bookingplatform` varchar(255) NOT NULL,
  `upi_id` varchar(255) NOT NULL,
  `ticketupload` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `train_upload`
--

INSERT INTO `train_upload` (`id`, `bookingplatform`, `upi_id`, `ticketupload`) VALUES
(1, 'abhi bus', '22223', 'uploads/train sell 1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `user_wallets`
--

CREATE TABLE `user_wallets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_wallets`
--

INSERT INTO `user_wallets` (`id`, `user_id`, `balance`, `created_at`, `updated_at`) VALUES
(1, 1, 3950.00, '2025-01-24 14:58:28', '2025-04-02 09:03:09'),
(2, 3, 300.00, '2025-01-24 15:22:56', '2025-04-01 10:39:23'),
(3, 2, 300.00, '2025-01-24 15:25:26', '2025-04-01 10:30:51');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` int(11) NOT NULL,
  `wallet_id` int(11) NOT NULL,
  `type` enum('Deposit','Withdrawal') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallet_transactions`
--

INSERT INTO `wallet_transactions` (`id`, `wallet_id`, `type`, `amount`, `transaction_date`) VALUES
(1, 1, 'Deposit', 400.00, '2025-01-24 15:02:18'),
(2, 1, 'Deposit', 600.00, '2025-01-24 15:02:24'),
(3, 1, 'Deposit', 600.00, '2025-01-24 15:07:28'),
(4, 2, 'Deposit', 30.00, '2025-01-24 15:23:05'),
(5, 2, 'Deposit', 70.00, '2025-01-24 15:23:10'),
(6, 3, 'Deposit', 200.00, '2025-01-24 15:25:36'),
(7, 3, 'Deposit', 300.00, '2025-01-24 15:25:42'),
(8, 3, 'Withdrawal', 600.00, '2025-01-25 12:27:05'),
(9, 3, 'Withdrawal', 1000.00, '2025-01-25 16:59:44'),
(10, 1, 'Withdrawal', 654.00, '2025-01-27 03:38:00'),
(11, 1, 'Deposit', 6.00, '2025-01-27 03:43:46'),
(12, 2, 'Deposit', 1000.00, '2025-01-27 07:35:04'),
(13, 2, 'Deposit', 1000.00, '2025-01-27 07:35:09'),
(14, 2, 'Withdrawal', -600.00, '2025-01-27 16:31:52'),
(15, 2, 'Withdrawal', -600.00, '2025-01-27 17:15:59'),
(16, 2, 'Withdrawal', -600.00, '2025-01-27 17:31:30'),
(17, 2, 'Deposit', 1000.00, '2025-01-28 03:39:30'),
(18, 2, 'Withdrawal', -600.00, '2025-01-28 03:39:49'),
(19, 2, 'Withdrawal', -600.00, '2025-01-28 03:40:04'),
(20, 3, 'Withdrawal', -600.00, '2025-01-31 14:45:49'),
(21, 3, 'Withdrawal', -600.00, '2025-01-31 14:55:32'),
(22, 3, 'Withdrawal', -600.00, '2025-01-31 16:01:55'),
(23, 1, 'Withdrawal', -600.00, '2025-02-03 07:43:38'),
(26, 1, 'Deposit', 2000.00, '2025-02-09 17:41:17'),
(27, 3, 'Deposit', 2000.00, '2025-02-10 02:59:48'),
(28, 2, 'Deposit', 1000.00, '2025-02-11 04:50:02'),
(29, 2, 'Deposit', 900.00, '2025-02-11 04:50:10'),
(30, 1, 'Deposit', 340.00, '2025-02-11 16:46:00'),
(35, 2, 'Withdrawal', 1150.00, '2025-02-19 18:07:33'),
(36, 2, 'Deposit', 2000.00, '2025-02-19 18:14:35'),
(37, 2, 'Withdrawal', 1150.00, '2025-02-19 18:15:39'),
(38, 1, 'Withdrawal', 1000.00, '2025-02-21 03:14:38'),
(39, 3, 'Withdrawal', 1200.00, '2025-02-23 15:47:36'),
(40, 3, 'Deposit', 2000.00, '2025-02-25 06:37:12'),
(41, 3, 'Withdrawal', 1200.00, '2025-02-25 06:38:35'),
(42, 3, 'Deposit', 100.00, '2025-02-25 16:38:12'),
(45, 1, 'Withdrawal', 1250.00, '2025-02-25 18:35:44'),
(46, 3, 'Deposit', 1200.00, '2025-02-25 18:35:44'),
(47, 1, 'Withdrawal', 1250.00, '2025-02-25 18:48:08'),
(48, 3, 'Deposit', 1200.00, '2025-02-25 18:48:08'),
(49, 1, 'Deposit', 2000.00, '2025-02-26 04:26:19'),
(52, 1, 'Deposit', 920.00, '2025-02-26 06:34:22'),
(53, 1, 'Deposit', 920.00, '2025-02-26 07:16:28'),
(54, 1, 'Deposit', 160.00, '2025-02-26 17:06:46'),
(55, 3, 'Deposit', 100.00, '2025-02-26 17:07:28'),
(56, 1, 'Withdrawal', 1250.00, '2025-02-26 17:23:30'),
(57, 3, 'Deposit', 1200.00, '2025-02-26 17:23:30'),
(58, 3, 'Deposit', 100.00, '2025-02-26 17:28:01'),
(59, 3, 'Deposit', 100.00, '2025-02-26 18:06:02'),
(60, 3, 'Deposit', 100.00, '2025-02-26 18:07:07'),
(61, 1, 'Deposit', 250.00, '2025-02-26 18:07:52'),
(62, 1, 'Withdrawal', 1250.00, '2025-02-26 18:11:42'),
(63, 3, 'Deposit', 1200.00, '2025-02-26 18:11:42'),
(64, 1, 'Withdrawal', 500.00, '2025-02-27 16:35:43'),
(65, 3, 'Withdrawal', 550.00, '2025-02-27 17:49:44'),
(66, 1, 'Deposit', 500.00, '2025-02-27 17:49:44'),
(67, 3, 'Withdrawal', 500.00, '2025-02-27 17:59:50'),
(68, 3, 'Deposit', 400.00, '2025-02-27 13:30:18'),
(69, 3, 'Deposit', 400.00, '2025-02-27 23:37:39'),
(70, 3, 'Withdrawal', 1000.00, '2025-02-28 04:13:06'),
(71, 3, 'Deposit', 800.00, '2025-02-27 23:46:12'),
(72, 2, 'Withdrawal', 500.00, '2025-03-02 14:49:41'),
(73, 3, 'Withdrawal', 550.00, '2025-03-03 15:54:50'),
(74, 2, 'Deposit', 500.00, '2025-03-03 15:54:50'),
(75, 1, 'Withdrawal', 1000.00, '2025-03-04 02:51:06'),
(76, 3, 'Withdrawal', 1050.00, '2025-03-04 05:58:32'),
(77, 1, 'Deposit', 1000.00, '2025-03-04 05:58:32'),
(78, 3, 'Withdrawal', 1200.00, '2025-03-05 11:23:56'),
(79, 3, 'Deposit', 50.00, '2025-03-07 07:14:33'),
(80, 3, 'Deposit', 50.00, '2025-03-07 07:16:13'),
(81, 3, 'Withdrawal', 500.00, '2025-03-07 17:56:27'),
(82, 2, 'Withdrawal', 500.00, '2025-03-08 08:34:36'),
(83, 3, 'Deposit', 500.00, '2025-03-08 08:34:36'),
(84, 1, 'Withdrawal', 500.00, '2025-03-08 08:44:53'),
(85, 2, 'Deposit', 500.00, '2025-03-08 09:02:59'),
(86, 2, 'Withdrawal', 550.00, '2025-03-08 09:21:47'),
(87, 3, 'Deposit', 500.00, '2025-03-08 09:21:47'),
(88, 2, 'Deposit', 920.00, '2025-03-09 03:55:21'),
(89, 1, 'Withdrawal', 1200.00, '2025-03-21 09:24:26'),
(90, 1, 'Withdrawal', 550.00, '2025-03-23 11:26:48'),
(91, 1, 'Deposit', 500.00, '2025-03-23 11:26:48'),
(92, 1, 'Deposit', 500.00, '2025-04-01 03:46:02'),
(93, 1, 'Deposit', 500.00, '2025-04-01 03:56:35'),
(94, 1, 'Withdrawal', 1500.00, '2025-04-01 04:05:48'),
(95, 1, 'Deposit', 1200.00, '2025-04-01 00:59:37'),
(96, 1, 'Deposit', 300.00, '2025-04-01 05:04:07'),
(97, 1, 'Withdrawal', 1500.00, '2025-04-01 05:05:02'),
(98, 1, 'Deposit', 1200.00, '2025-04-01 05:05:25'),
(99, 3, 'Withdrawal', 1550.00, '2025-04-01 05:14:02'),
(100, 1, 'Deposit', 1500.00, '2025-04-01 05:14:02'),
(101, 1, 'Withdrawal', 1500.00, '2025-04-01 05:35:50'),
(102, 3, 'Deposit', 600.00, '2025-04-01 10:27:16'),
(103, 3, 'Deposit', 1400.00, '2025-04-01 10:27:29'),
(104, 3, 'Deposit', 500.00, '2025-04-01 10:27:49'),
(105, 3, 'Withdrawal', 5200.00, '2025-04-01 10:30:51'),
(106, 2, 'Deposit', 1030.00, '2025-04-01 10:36:45'),
(107, 2, 'Withdrawal', 1500.00, '2025-04-01 10:38:25'),
(108, 2, 'Withdrawal', 1300.00, '2025-04-01 10:39:23'),
(109, 1, 'Deposit', 1000.00, '2025-04-02 08:09:12'),
(110, 1, 'Deposit', 800.00, '2025-04-02 08:16:41'),
(111, 1, 'Withdrawal', 3000.00, '2025-04-02 08:18:47'),
(112, 1, 'Deposit', 4000.00, '2025-04-02 08:22:28'),
(113, 1, 'Withdrawal', 1550.00, '2025-04-02 09:03:09'),
(114, 1, 'Deposit', 1500.00, '2025-04-02 09:03:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_wallet`
--
ALTER TABLE `admin_wallet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_wallet_transactions`
--
ALTER TABLE `admin_wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_wallet_id` (`admin_wallet_id`);

--
-- Indexes for table `agency_bookings`
--
ALTER TABLE `agency_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `route_id` (`route_id`);

--
-- Indexes for table `agency_wallet`
--
ALTER TABLE `agency_wallet`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `agency_wallet_transactions`
--
ALTER TABLE `agency_wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bus_number` (`bus_number`);

--
-- Indexes for table `bus_sell`
--
ALTER TABLE `bus_sell`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bus_upload`
--
ALTER TABLE `bus_upload`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `new_bookings`
--
ALTER TABLE `new_bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `passengers`
--
ALTER TABLE `passengers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `signup`
--
ALTER TABLE `signup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `train_bookings`
--
ALTER TABLE `train_bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `train_sell`
--
ALTER TABLE `train_sell`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `train_upload`
--
ALTER TABLE `train_upload`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_wallets`
--
ALTER TABLE `user_wallets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallet_id` (`wallet_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_wallet`
--
ALTER TABLE `admin_wallet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_wallet_transactions`
--
ALTER TABLE `admin_wallet_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `agency_bookings`
--
ALTER TABLE `agency_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `agency_wallet_transactions`
--
ALTER TABLE `agency_wallet_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `buses`
--
ALTER TABLE `buses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `bus_sell`
--
ALTER TABLE `bus_sell`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `bus_upload`
--
ALTER TABLE `bus_upload`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `new_bookings`
--
ALTER TABLE `new_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `passengers`
--
ALTER TABLE `passengers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `signup`
--
ALTER TABLE `signup`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `train_bookings`
--
ALTER TABLE `train_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `train_sell`
--
ALTER TABLE `train_sell`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `train_upload`
--
ALTER TABLE `train_upload`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_wallets`
--
ALTER TABLE `user_wallets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_wallet_transactions`
--
ALTER TABLE `admin_wallet_transactions`
  ADD CONSTRAINT `admin_wallet_transactions_ibfk_1` FOREIGN KEY (`admin_wallet_id`) REFERENCES `admin_wallet` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `agency_bookings`
--
ALTER TABLE `agency_bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_wallets`
--
ALTER TABLE `user_wallets`
  ADD CONSTRAINT `user_wallets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `signup` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD CONSTRAINT `wallet_transactions_ibfk_1` FOREIGN KEY (`wallet_id`) REFERENCES `user_wallets` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

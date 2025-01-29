-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 21, 2025 at 07:10 PM
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
-- Database: `nextcity`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `address_id` int(11) NOT NULL,
  `ward_no` varchar(50) DEFAULT NULL,
  `street_no` varchar(50) DEFAULT NULL,
  `building_no` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `address`
--

INSERT INTO `address` (`address_id`, `ward_no`, `street_no`, `building_no`) VALUES
(2, '10', '2 ', '15'),
(3, '11', '3', '4'),
(4, '22', '5', '6'),
(5, '13', '2', '9'),
(6, '25', '7', '2'),
(7, '9', '4', '10'),
(8, '14', '6', '9'),
(9, '18', '9', '5'),
(10, '11', '5', '9'),
(11, '16', '8', '3'),
(12, '20', '1', '2'),
(13, '19', '8', '6'),
(20, '2', '5', '9'),
(21, '11', '6', '15'),
(22, '15', '6', '3'),
(23, '10', '5', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ailments`
--

CREATE TABLE `ailments` (
  `ailment_id` int(11) NOT NULL,
  `ailment` varchar(255) DEFAULT NULL,
  `ailment_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ailments`
--

INSERT INTO `ailments` (`ailment_id`, `ailment`, `ailment_description`) VALUES
(1, 'Weakness', 'Feeling fatigued, lack of energy, or decreased strength.'),
(2, 'Chest Pain', 'Discomfort or pain in the chest, possibly radiating to the arm or jaw.'),
(4, 'Fever', 'Elevated body temperature, often accompanied by chills, sweating, and fatigue.'),
(5, 'Headache', 'Persistent pain or pressure in the head, often caused by tension or stress.'),
(6, 'cold', 'klmdzm'),
(7, 'nmmm', 'jnsdmsd');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `ailment_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `user_id`, `doctor_id`, `hospital_id`, `appointment_date`, `status`, `ailment_id`) VALUES
(14, 24, 6, 3, '2025-01-16', 'canceled', 1),
(15, 24, 4, 2, '2025-01-12', 'approved', 2),
(17, 24, 8, 2, '2025-01-24', 'approved', 4),
(18, 8, 7, 3, '2025-01-14', 'approved', 5),
(20, 28, 7, 3, '2025-01-28', 'approved', 6),
(24, 24, 13, 1, '2025-01-28', 'canceled', 7);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `name`, `contact_email`, `contact_number`) VALUES
(1, 'Cleaner', 'cleaner@example.com', '1234567890'),
(2, 'Wi-Fi Support', 'wifi@example.com', '0987654321'),
(3, 'Dish Service', 'dish@example.com', '1122334455'),
(4, 'Chef', 'chef@example.com', '2233445566'),
(5, 'Home Cleaning', 'homecleaning@example.com', '3344556677'),
(6, 'Home Security', 'homesecurity@example.com', '4455667788');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `contact_number` int(11) DEFAULT NULL,
  `appointment_time` varchar(100) NOT NULL,
  `appointment_days` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `name`, `specialization`, `hospital_id`, `contact_number`, `appointment_time`, `appointment_days`, `email`, `user_id`) VALUES
(4, 'kapa', 'Hematologist', 2, 998876542, '10AM-6PM', 'Sunday,Tuesday', 'kapa@gmail.com', 22),
(6, 'Dr. Kazi Nruf', 'Medicine Specialist', 3, 1770116695, '11PM-4AM', 'Sunday,Thursday', 'kmaruf@gmail.com', 25),
(7, 'Dr.  Asif Anawar', 'Medicine Specialist', 3, 1770116690, '2PM-6PM', 'Tuesday', 'asif@gmail.com', 26),
(8, 'Dr. Kamrun Nahar', 'Child specialist', 2, 1770116645, '12AM-4PM', 'Sunday,Tuesday,Friday', 'kamrun@gmail.com', 27),
(10, 'Dr. Mamun Elahi', 'Cardiologist', 3, 998871548, '2PM-6PM', 'Sunday,Wednesday', 'mamunelahi@gmail.com', 31),
(11, 'Dr. Niloy', 'Cardiologist', 1, 998871548, '2PM-6PM', 'Tuesday', 'niloy@gmail.com', 36),
(12, 'Dr. kolim', 'Medicine Specialist', 1, 998871548, '10AM-6PM', 'Monday,Thursday', 'kolim@gmail.com', 35),
(13, 'Dr. Hridoy', 'Hematologist', 1, 998871042, '10AM-6PM', 'Tuesday,Thursday', 'hridoy@gmail.com', 33);

-- --------------------------------------------------------

--
-- Table structure for table `educational_institutions`
--

CREATE TABLE `educational_institutions` (
  `institution_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `type` enum('school','college','university') NOT NULL,
  `location_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `educational_institutions`
--

INSERT INTO `educational_institutions` (`institution_id`, `name`, `contact_number`, `type`, `location_id`) VALUES
(1, 'Greenwood High School', '1234567890', 'school', 2),
(2, 'Springfield College', '0987654321', 'college', 3),
(6, 'National University', '1122334455', 'university', 7);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `feedback_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `feedback_type` varchar(255) NOT NULL,
  `rating` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `feedback_text`, `created_at`, `feedback_type`, `rating`) VALUES
(1, 4, 'This is a sample feedback.', '2025-01-06 03:59:01', 'Complaint', 3),
(2, 24, 'cv ', '2025-01-14 21:14:20', 'Query', 3),
(3, 24, 'sxms', '2025-01-14 21:15:24', 'Suggestion', 5);

-- --------------------------------------------------------

--
-- Table structure for table `hospital`
--

CREATE TABLE `hospital` (
  `hospital_id` int(11) NOT NULL,
  `hospital_name` varchar(100) NOT NULL,
  `contact_number` int(11) NOT NULL,
  `location_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospital`
--

INSERT INTO `hospital` (`hospital_id`, `hospital_name`, `contact_number`, `location_id`) VALUES
(1, 'Ibn Sina', 7781, 11),
(2, 'Popular Diagnostic', 8801, 12),
(3, 'United Hospital', 9910, 13);

-- --------------------------------------------------------

--
-- Table structure for table `issues`
--

CREATE TABLE `issues` (
  `issue_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `issue_type` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `reported_date` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `assigned_department_id` int(11) DEFAULT NULL,
  `location_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `issues`
--

INSERT INTO `issues` (`issue_id`, `user_id`, `issue_type`, `description`, `reported_date`, `status`, `assigned_department_id`, `location_id`) VALUES
(1, 4, 'Drainage Problem', 'jnadkcxdv', '2025-01-06 17:31:27', 'Pending', NULL, 5),
(2, 4, 'Drainage Problem', 'jnadkcxdv', '2025-01-06 17:31:37', 'Assigned', 1, 10),
(3, 4, 'Street Lighting', 'somuch issue', '2025-01-06 17:32:52', 'Pending', NULL, 9),
(4, 4, 'Garbage Collection', 'Thers so much garbage in front of my road', '2025-01-06 23:55:44', 'Closed', 1, 6),
(5, 24, 'Road Maintenance', 'heavy damage in our street-9', '2025-01-07 21:56:50', 'Pending', NULL, 2),
(6, 24, 'Street Lighting', 'My street light has some issue..Its been off for 10 days', '2025-01-08 10:08:50', 'Pending', NULL, 21),
(7, 8, 'Drainage Problem', 'bsnsd', '2025-01-08 10:27:51', 'Pending', NULL, 23),
(8, 29, 'Drainage Problem', 'drainage issue', '2025-01-14 20:39:28', 'Closed', 1, 23);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `admin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `message`, `created_at`, `admin_id`) VALUES
(44, 'hiiiiiiiiiiii', '2025-01-21 23:17:35', 5),
(45, 'hello', '2025-01-21 23:28:05', 5),
(46, 'ula', '2025-01-21 23:29:23', 5);

-- --------------------------------------------------------

--
-- Table structure for table `notification_recipients`
--

CREATE TABLE `notification_recipients` (
  `id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('unread','read') DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification_recipients`
--

INSERT INTO `notification_recipients` (`id`, `notification_id`, `user_id`, `status`) VALUES
(506, 44, 5, 'unread'),
(507, 45, 31, 'unread'),
(508, 46, 24, 'unread');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `patient_situation` text DEFAULT NULL,
  `appointment_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`patient_id`, `name`, `age`, `patient_situation`, `appointment_id`) VALUES
(2, 'raju', 25, 'Gastric issue', 15),
(4, 'goku', 31, 'Mild', 18);

-- --------------------------------------------------------

--
-- Table structure for table `patient_medicine`
--

CREATE TABLE `patient_medicine` (
  `patient_id` int(11) NOT NULL,
  `prescribed_medicine` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_medicine`
--

INSERT INTO `patient_medicine` (`patient_id`, `prescribed_medicine`) VALUES
(4, 'Napa extra');

-- --------------------------------------------------------

--
-- Table structure for table `patient_test`
--

CREATE TABLE `patient_test` (
  `patient_id` int(11) NOT NULL,
  `prescribed_test` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `restaurant_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `cuisine_type` varchar(50) DEFAULT NULL,
  `average_cost` decimal(10,2) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`restaurant_id`, `name`, `contact_number`, `cuisine_type`, `average_cost`, `location_id`) VALUES
(1, 'Tasty Treats', '1239876540', 'Italian', 25.50, 9),
(2, 'Spice Heaven', '4567891234', 'Indian', 30.00, 5),
(3, 'Sushi World', '7891234567', 'Japanese', 40.75, 10),
(4, 'The Green Plate', '2345678901', 'Vegetarian', 22.50, 12),
(5, 'Pasta Palace', '3456789012', 'Italian', 28.00, 8),
(6, 'Burger Haven', '4567890123', 'American', 15.25, 6),
(7, 'Curry Corner', '5678901234', 'Indian', 18.50, 7),
(8, 'Noodle Nirvana', '6789012345', 'Chinese', 21.75, 9);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(3, 'admin'),
(1, 'citizen'),
(2, 'doctor');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `name`, `description`, `department_id`) VALUES
(1, 'Wi-Fi Subscription', 'Subscription service for Wi-Fi connection', 2),
(2, 'Dish Subscription', 'Subscription for satellite dish services', 3),
(3, 'Cleaner Subscription', 'Monthly cleaning service for home', 1),
(4, 'Home Security Subscription', 'Monthly home security monitoring service', 6),
(5, 'Graden Maintainance Subscription', 'Monthly service for trimming,watering,and maintaining gardens or lawns', 1),
(6, 'aaaa', 'aa', NULL),
(7, 'aaa', 'abcd', NULL),
(8, 'aaa', 'abcd', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `subscription_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`subscription_id`, `user_id`, `service_id`, `start_date`, `end_date`, `status`) VALUES
(1, 4, 2, '2025-01-15 00:00:00', NULL, 'approved'),
(2, 24, 5, '2025-01-18 00:00:00', NULL, 'approved'),
(3, 34, 4, '2025-02-01 00:00:00', NULL, 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `registration_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `phone_number`, `dob`, `registration_date`) VALUES
(4, 'Ifty', '$2y$10$7TI5BQvaje6MuEXu0liu9OrxpEMxL1/nHcAMWIRdfdFfEvc.X30M.', 'iftyl1234@gmail.com', '01940432541', '1995-02-10', '2025-01-10 12:24:43'),
(5, 'Admin', '$2y$10$wDk1LU4CoULdPC7o31wA8.mbjWMXpttNZYGZnrCnNsUH2LhLo7Nba', 'admin@gmail.com', '99999999', '1988-06-25', '2025-01-10 12:24:43'),
(7, 'kader', '$2y$10$aFOFKCOiBTVA8RyM3dQUgOhSpmyiZ/.0aQJr7ADVNmEKBuqvEC1Jq', 'kader@gmail.com', '0998876542', '1990-08-14', '2025-01-10 12:24:43'),
(8, 'goku', '$2y$10$Gba55/o20ciTJxSwAMHEEOyRfhxjJSZe0b5IXlEFY26U1M6lpbDjm', 'goku@gmail.com', '0998876541', '1993-11-30', '2025-01-10 12:24:43'),
(22, 'kapa', '$2y$10$c5s7Mintitd1SRhb2dfYXuwvznQk7HnNWZVhTyuho3rEWKILrqfBS', 'kapa@gmail.com', '0998876542', '1985-04-18', '2025-01-10 12:24:43'),
(23, 'Nurjahan Begum', '$2y$10$vHxVpTcHcnkVr9815OX7m.ydBw/0094ftqKxiUV82t/Z6gUMMz.6O', 'nurjahan@gmail.com', '0998876541', '1991-07-21', '2025-01-10 12:24:43'),
(24, 'Raju Dastagir', '$2y$10$RF3MS0iWFcuhh0XmffIo3.YcQwNwWNt8XJHILe5GjPmG3CIAFqWRy', 'raju@gmail.com', '0998877541', '1999-04-12', '2025-01-10 12:24:43'),
(25, 'Dr. Kazi Nruf', '$2y$10$8MP/.JLxLoaW8G.J8T25z.5T2iFADlS82H/3PvC8kOixmZvURpixq', 'kmaruf@gmail.com', '01770116695', '1976-09-09', '2025-01-10 12:24:43'),
(26, 'Dr.  Asif Anawar', '$2y$10$y0r5trpEZvB4FNCX.RJ0euYJcbAKHoYXWnEnhTNGL9vW1lDZwcmJO', 'asif@gmail.com', '01770116690', '1990-03-15', '2025-01-10 12:24:43'),
(27, 'Dr. Kamrun Nahar', '$2y$10$JyUxpMrr9m9EeSGoh908VOB.rxXdolFIrtuvL9BFttHe6D7xWz2Na', 'kamrun@gmail.com', '01770116645', '1970-04-23', '2025-01-10 12:24:43'),
(28, 'Kazi', '$2y$10$bU8C.StbY99/ItG/atGpl.QDtE4FF6gMnsTqFam6Q7cJ0k843BaMO', 'kazi@gmail.com', '0998876540', '2003-01-07', '2025-01-10 12:24:50'),
(29, 'Umme Fatima', '$2y$10$He4Mqex97O9x2U65Bj.XBedRzwzbeoBtua24ndRkVybBkKfZG8Bpa', 'fatima@gmail.com', '0998871542', '1999-11-12', '2025-01-10 12:41:08'),
(31, 'Dr. Mamun Elahi', '$2y$10$SqO.VEWKTBcQAEYYPX0DyeBkJqIGiDQdK91C8pcDoGXA3.vMwbFRe', 'mamunelahi@gmail.com', '0998871548', '1978-11-12', '2025-01-10 19:19:35'),
(33, 'Dr. Hridoy', '$2y$10$DRU67WMfs2RBe1cMtvcT1eu3w4Gz0JRrkVSES0B8BR78w7RPp14sK', 'hridoy@gmail.com', '0998871042', '1992-01-08', '2025-01-12 22:14:12'),
(34, 'alim', '$2y$10$6sfbGjTn52qEcJruKyTUW.o/1Jw1YjS8Gjg/Ni0thCE6lVR/Zn9rW', 'alim@gmail.com', '0998871548', '2002-02-11', '2025-01-12 23:40:45'),
(35, 'Dr. kolim', '$2y$10$KiDmlJeeq8lG3Cam4bFzMuoOklTGqGWU.nABqsjK5krcCC8m5j0j2', 'kolim@gmail.com', '0998871548', '2005-01-22', '2025-01-12 23:52:05'),
(36, 'Dr. Niloy', '$2y$10$Ht5Yy3tXiaQ0.kk3WCbWGeH53ZRN6blec8gxjt9pYMw01BLotWLsi', 'niloy@gmail.com', '0998871548', '1980-01-01', '2025-01-13 02:12:12');

-- --------------------------------------------------------

--
-- Table structure for table `user_address`
--

CREATE TABLE `user_address` (
  `user_id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_address`
--

INSERT INTO `user_address` (`user_id`, `address_id`) VALUES
(4, 9),
(5, 2),
(7, 3),
(8, 4),
(22, 12),
(23, 11),
(24, 5),
(25, 13),
(26, 6),
(27, 7),
(28, 8),
(29, 10),
(31, 13),
(33, 20),
(34, 21),
(35, 22);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES
(4, 1),
(5, 3),
(7, 1),
(8, 1),
(22, 2),
(23, 2),
(24, 1),
(25, 2),
(26, 2),
(27, 2),
(28, 1),
(29, 1),
(31, 1),
(31, 2),
(33, 1),
(33, 2),
(34, 1),
(35, 1),
(35, 2),
(36, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`address_id`);

--
-- Indexes for table `ailments`
--
ALTER TABLE `ailments`
  ADD PRIMARY KEY (`ailment_id`),
  ADD UNIQUE KEY `ailment` (`ailment`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `hospital_id` (`hospital_id`),
  ADD KEY `ailment_id` (`ailment_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD KEY `hospital_id` (`hospital_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `educational_institutions`
--
ALTER TABLE `educational_institutions`
  ADD PRIMARY KEY (`institution_id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `hospital`
--
ALTER TABLE `hospital`
  ADD PRIMARY KEY (`hospital_id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `issues`
--
ALTER TABLE `issues`
  ADD PRIMARY KEY (`issue_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `assigned_department_id` (`assigned_department_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `notification_recipients`
--
ALTER TABLE `notification_recipients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_id` (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `patient_medicine`
--
ALTER TABLE `patient_medicine`
  ADD PRIMARY KEY (`patient_id`,`prescribed_medicine`);

--
-- Indexes for table `patient_test`
--
ALTER TABLE `patient_test`
  ADD PRIMARY KEY (`patient_id`,`prescribed_test`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`restaurant_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`subscription_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_address`
--
ALTER TABLE `user_address`
  ADD PRIMARY KEY (`user_id`,`address_id`),
  ADD KEY `address_id` (`address_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `ailments`
--
ALTER TABLE `ailments`
  MODIFY `ailment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `educational_institutions`
--
ALTER TABLE `educational_institutions`
  MODIFY `institution_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `issues`
--
ALTER TABLE `issues`
  MODIFY `issue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `notification_recipients`
--
ALTER TABLE `notification_recipients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=509;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `restaurant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `subscription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`),
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`hospital_id`) REFERENCES `hospital` (`hospital_id`),
  ADD CONSTRAINT `appointments_ibfk_4` FOREIGN KEY (`ailment_id`) REFERENCES `ailments` (`ailment_id`);

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `hospital` (`hospital_id`),
  ADD CONSTRAINT `doctors_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `educational_institutions`
--
ALTER TABLE `educational_institutions`
  ADD CONSTRAINT `educational_institutions_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `address` (`address_id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `hospital`
--
ALTER TABLE `hospital`
  ADD CONSTRAINT `hospital_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `address` (`address_id`);

--
-- Constraints for table `issues`
--
ALTER TABLE `issues`
  ADD CONSTRAINT `issues_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `issues_ibfk_3` FOREIGN KEY (`assigned_department_id`) REFERENCES `departments` (`department_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notification_recipients`
--
ALTER TABLE `notification_recipients`
  ADD CONSTRAINT `notification_recipients_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`notification_id`),
  ADD CONSTRAINT `notification_recipients_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`);

--
-- Constraints for table `patient_medicine`
--
ALTER TABLE `patient_medicine`
  ADD CONSTRAINT `patient_medicine_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`);

--
-- Constraints for table `patient_test`
--
ALTER TABLE `patient_test`
  ADD CONSTRAINT `patient_test_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`);

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`);

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `subscriptions_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`);

--
-- Constraints for table `user_address`
--
ALTER TABLE `user_address`
  ADD CONSTRAINT `user_address_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_address_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `address` (`address_id`);

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

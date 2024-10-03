-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 16, 2024 at 05:08 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `colens`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `role`) VALUES
(1, 'Patrick01', '$2y$10$OCSUtczgBZUWG7SLGosQsuUzKNiSnm5O/9CtAzKwF3TpEqw5d9HYW', 'Admin'),
(2, 'Demo', '$2y$10$yDtRO9zaHHgltJU9pJ0fDO8/lxGAg016uc1/Ot.MP06CJS.lJIDLC', '');

-- --------------------------------------------------------

--
-- Table structure for table `health_insurance_applications`
--

CREATE TABLE `health_insurance_applications` (
  `id` int(11) NOT NULL,
  `member_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `birthday` date NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `insurance_type` varchar(50) NOT NULL,
  `coverage_amount` decimal(10,2) NOT NULL,
  `pre_existing_conditions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `health_insurance_applications`
--

INSERT INTO `health_insurance_applications` (`id`, `member_id`, `name`, `birthday`, `email`, `phone_number`, `address`, `insurance_type`, `coverage_amount`, `pre_existing_conditions`) VALUES
(1, '202408140000382', 'Jego Kuhon', '2000-02-08', 'jego@gmail.com', '2147483647', 'Phase4 Pkg3 Blk 26 Lot1 Bagong Silang', 'Family', '123.00', '123');

-- --------------------------------------------------------

--
-- Table structure for table `loan_applications`
--

CREATE TABLE `loan_applications` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `annual_income` decimal(15,2) NOT NULL,
  `loan_amount` decimal(15,2) NOT NULL,
  `loan_term` int(11) NOT NULL,
  `loan_purpose` enum('Home','Car','Education','Personal','Other') NOT NULL,
  `employment_status` enum('Employed','Self-Employed','Unemployed','Retired') NOT NULL,
  `collateral` text DEFAULT NULL,
  `application_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `member_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_applications`
--

INSERT INTO `loan_applications` (`id`, `name`, `email`, `phone_number`, `address`, `annual_income`, `loan_amount`, `loan_term`, `loan_purpose`, `employment_status`, `collateral`, `application_date`, `member_id`) VALUES
(7, 'Gab', 'nccarino@onedoc.ph', '+639052758997', 'Phase4 Pkg3 Blk 26 Lot1 Bagong Silang', '2.00', '2.00', 2, 'Home', 'Employed', '1', '2024-08-15 01:50:54', ''),
(9, 'Jego Kuhon', 'jego@gmail.com', '2147483647', 'Phase4 Pkg3 Blk 26 Lot1 Bagong Silang', '25.00', '25.00', 25, 'Home', 'Employed', '25', '2024-08-15 02:11:40', '202408140000382');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `member_id` varchar(15) NOT NULL,
  `role` enum('Admin','Member') NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_no` int(11) NOT NULL,
  `age` int(11) NOT NULL,
  `birthday` date NOT NULL,
  `sex` enum('Male','Female','Other') NOT NULL,
  `civil_status` enum('Single','Married','Widowed','Divorced','Separated') NOT NULL,
  `address` text NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(250) NOT NULL,
  `verified` enum('True','False') NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `member_id`, `role`, `name`, `contact_no`, `age`, `birthday`, `sex`, `civil_status`, `address`, `email`, `password`, `verified`, `profile_pic`) VALUES
(14, '202408130000955', 'Admin', 'Gabz', 123, 23, '2000-12-12', 'Male', 'Single', '123', '123444@gmail.com', '$2y$10$nGufbC0Lzs0LzVRSR2huLuLY13OvClXS1atpEQglCJDgV9vw2QHKq', 'True', 'company_id_1.jpg'),
(15, '202408130000716', 'Member', 'Gabz', 123, 701, '1322-12-13', 'Male', 'Single', '123', '1234444@gmail.com', '$2y$10$gSGvM7ySFLbs0FqIVCfCHOmrC2Zw5hU0q///UeQryCHjOLkccC9Bq', 'False', NULL),
(17, '202408150000036', 'Member', '1', 1, 0, '0001-01-01', 'Male', 'Single', '1', '1', '$2y$10$Y67kuaKqF92XfUNjmrKq/.myV65JFM0/hVEcfKKzos8Ta0PGxIwAy', 'False', NULL),
(18, '202408150691958', 'Member', '1', 147483647, 811, '1212-12-12', 'Male', 'Married', '1', '1@gmail.com', '$2y$10$VR7pfErse.B1DWcvLjGX1.hMEmaaCGgbw1AzVR6kEfqA4/dNysXBu', 'False', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `details` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `type`, `amount`, `date`, `details`) VALUES
(7, 'Finance 1', 500, '1212-12-12', '1'),
(8, 'Finance 2', 4444, '1212-12-12', '1'),
(10, 'Finance 3', 2000, '1212-12-12', '444'),
(11, 'Finance 3', 2000, '2222-12-22', '1'),
(12, 'Electric Bills', 500, '1212-12-12', '1'),
(13, 'Test', 123, '0000-00-00', '2');

-- --------------------------------------------------------

--
-- Table structure for table `receivable`
--

CREATE TABLE `receivable` (
  `id` int(11) NOT NULL,
  `member_name` text NOT NULL,
  `invoice_date` int(11) NOT NULL,
  `due_date` date NOT NULL,
  `amount_due` int(11) NOT NULL,
  `amount_paid` int(11) NOT NULL,
  `payment_status` text NOT NULL,
  `note` text NOT NULL,
  `type` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receivable`
--

INSERT INTO `receivable` (`id`, `member_name`, `invoice_date`, `due_date`, `amount_due`, `amount_paid`, `payment_status`, `note`, `type`) VALUES
(4, 'Gab', 2024, '2024-08-29', 2000, 1234, 'Pending', '', 'Loan'),
(5, 'Gab', 2024, '2024-08-22', 2, 2, 'Pending', '', 'Loan'),
(6, 'Gab', 1212, '1212-12-12', 4, 12, 'Pending', '', 'Service Fee');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `employer_name` varchar(255) DEFAULT NULL,
  `sss` varchar(20) DEFAULT NULL,
  `gsis` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `age`, `birthday`, `sex`, `status`, `address`, `guardian_name`, `contact_number`, `employer_name`, `sss`, `gsis`) VALUES
(1, 'John Doesnt', 32, '1994-06-15', 'Male', 'Single', '123 Main St, Springfield', 'Jane Doe', '123-456-7890', 'Emily Smith', '123-45-6789', '987-65-4321');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `health_insurance_applications`
--
ALTER TABLE `health_insurance_applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_applications`
--
ALTER TABLE `loan_applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `member_id` (`member_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `receivable`
--
ALTER TABLE `receivable`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `health_insurance_applications`
--
ALTER TABLE `health_insurance_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `loan_applications`
--
ALTER TABLE `loan_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `receivable`
--
ALTER TABLE `receivable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

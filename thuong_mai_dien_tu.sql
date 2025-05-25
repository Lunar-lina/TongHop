-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2025 at 05:42 AM
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
-- Database: `thuong_mai_dien_tu`
--

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `users` int(3) NOT NULL,
  `Product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `order_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `users`, `Product_id`, `quantity`, `order_id`) VALUES
(15, 2, 6, 10, 0);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `Category` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`Category`, `Description`) VALUES
('Áo Quần', ''),
('Trang Sức', '');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `User` int(3) NOT NULL,
  `Fullname` varchar(255) NOT NULL,
  `Phone` int(11) NOT NULL,
  `Address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`User`, `Fullname`, `Phone`, `Address`) VALUES
(2, 'Hoan', 947707856, 'Dien Bien Phu');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `Waiting For Confirmation` tinyint(1) NOT NULL,
  `Confirmed` tinyint(1) NOT NULL,
  `Delivering` tinyint(1) NOT NULL,
  `Delivered` tinyint(1) NOT NULL,
  `Cancelled` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders_details`
--

CREATE TABLE `orders_details` (
  `Orders_id` int(11) NOT NULL,
  `items` varchar(255) NOT NULL,
  `Amounts` int(11) NOT NULL,
  `Price` int(11) NOT NULL,
  `Status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `ID` int(3) NOT NULL,
  `Creators` int(255) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Description` varchar(2000) NOT NULL,
  `Categories` varchar(255) NOT NULL,
  `Price` int(11) NOT NULL,
  `Stocks` int(11) NOT NULL,
  `Picture` varchar(255) NOT NULL,
  `Visibility` tinyint(1) NOT NULL,
  `Created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`ID`, `Creators`, `Name`, `Description`, `Categories`, `Price`, `Stocks`, `Picture`, `Visibility`, `Created_at`) VALUES
(6, 2, 'Cmoni', 'HI!sdasd', 'Áo Quần', 100000, 50, '', 1, '2025-05-25 01:29:36'),
(9, 2, 'nah', 'test', 'Trang Sức', 10000, 90, '1748143663_4nmc533ib2pe1.png', 1, '2025-05-25 03:41:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `Id` int(3) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL DEFAULT '@',
  `Passwords` varchar(255) NOT NULL,
  `Hashed_password` varchar(255) NOT NULL,
  `Date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Permission` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`Id`, `Username`, `Email`, `Passwords`, `Hashed_password`, `Date`, `Permission`) VALUES
(1, 'ohmygod', 'ohmygawd@gmail.com', '$2y$10$ztHJbC2bRKT6dlXXIHBAN.0Ow5TdPRxTuh/JqI3l6piKU0PRtUnzm', '', '2025-05-15 14:01:56', 0),
(2, 'dasdsa', 'hoan@gmail.com', '123456789', '$2y$10$yzGHwE4BFjgJgL8WeI1I/.5Ep.B31T3qZeF/sXEPHfEasrbINuwGO', '2025-05-25 01:54:06', 10);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users` (`users`,`Product_id`),
  ADD KEY `Product_id` (`Product_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`Category`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD UNIQUE KEY `User` (`User`) USING BTREE,
  ADD UNIQUE KEY `User_2` (`User`,`Fullname`,`Phone`,`Address`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Categories` (`Categories`),
  ADD KEY `Creators` (`Creators`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `ID` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `Id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`Product_id`) REFERENCES `products` (`ID`),
  ADD CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`users`) REFERENCES `users` (`Id`);

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`User`) REFERENCES `users` (`Id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`Categories`) REFERENCES `category` (`Category`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`Creators`) REFERENCES `users` (`Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

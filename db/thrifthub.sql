-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 28, 2025 at 09:42 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `thrifthub`
--

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `brand_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`brand_id`, `user_id`, `brand_name`) VALUES
(1, 1, 'Brand 1'),
(4, 7, 'Jasmine Peaches'),
(3, 7, 'plantain chip');

-- --------------------------------------------------------

--
-- Table structure for table `business_types`
--

CREATE TABLE `business_types` (
  `type_id` int(11) NOT NULL,
  `type_description` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business_types`
--

INSERT INTO `business_types` (`type_id`, `type_description`) VALUES
(1, 'Individual'),
(2, 'small'),
(3, 'Organizational');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL,
  `ip_add` varchar(50) NOT NULL,
  `c_id` int(11) DEFAULT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `cat_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cat_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `user_id`, `cat_name`) VALUES
(1, 1, 'Category'),
(3, 1, 'Zoie'),
(6, 1, 'manage'),
(7, 1, 'new feild'),
(9, 7, 'Cream Crackerssss'),
(10, 7, 'fun');

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `conversation_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`conversation_id`, `buyer_id`, `seller_id`, `product_id`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 4, '2025-11-28 21:31:18', '2025-11-28 21:31:18'),
(2, 2, 7, 5, '2025-11-28 21:33:23', '2025-11-28 21:35:00');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`user_id`) VALUES
(1),
(2),
(3),
(4),
(9);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message_text` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `conversation_id`, `sender_id`, `receiver_id`, `message_text`, `is_read`, `created_at`) VALUES
(1, 2, 2, 7, 'Hi Khaela, how are you?', 1, '2025-11-28 21:33:39'),
(2, 2, 7, 2, 'I am good \r\ni am so excited \r\nMy Code workssssssssss', 0, '2025-11-28 21:35:00');

-- --------------------------------------------------------

--
-- Table structure for table `orderdetails`
--

CREATE TABLE `orderdetails` (
  `orderdetail_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderdetails`
--

INSERT INTO `orderdetails` (`orderdetail_id`, `order_id`, `product_id`, `qty`, `price`) VALUES
(1, 1, 4, 5, 500.00),
(2, 2, 4, 3, 500.00),
(3, 3, 2, 2, 800.00),
(4, 4, 2, 1, 800.00),
(5, 4, 4, 2, 500.00),
(6, 5, 4, 1, 500.00),
(7, 5, 2, 1, 800.00),
(8, 6, 2, 1, 800.00),
(9, 6, 4, 1, 500.00),
(10, 6, 5, 1, 40.00),
(11, 7, 5, 3, 40.00),
(12, 8, 4, 2, 500.00),
(13, 8, 5, 1, 40.00),
(14, 9, 5, 13, 40.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `order_status` enum('pending','paid','shipped','completed','cancelled') NOT NULL DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `order_date`, `order_status`, `total_amount`) VALUES
(1, 1, '2025-11-27 17:57:53', 'paid', 2500.00),
(2, 3, '2025-11-28 11:12:08', 'paid', 1500.00),
(3, 3, '2025-11-28 11:45:37', 'paid', 1600.00),
(4, 3, '2025-11-28 11:54:04', 'paid', 1800.00),
(5, 2, '2025-11-28 13:31:54', 'paid', 1300.00),
(6, 4, '2025-11-28 18:56:35', 'paid', 1340.00),
(7, 4, '2025-11-28 18:58:36', 'paid', 120.00),
(8, 2, '2025-11-28 19:33:27', 'paid', 1040.00),
(9, 9, '2025-11-28 19:44:40', 'paid', 520.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `pay_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'GHS',
  `payment_method` enum('momo','card','cash','bank_transfer') NOT NULL DEFAULT 'momo',
  `payment_status` enum('pending','successful','failed') NOT NULL DEFAULT 'successful',
  `payment_date` datetime NOT NULL DEFAULT current_timestamp(),
  `transaction_ref` varchar(100) DEFAULT NULL,
  `authorization_code` varchar(100) DEFAULT NULL,
  `payment_channel` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`pay_id`, `amount`, `customer_id`, `order_id`, `currency`, `payment_method`, `payment_status`, `payment_date`, `transaction_ref`, `authorization_code`, `payment_channel`) VALUES
(1, 2500.00, 1, 1, 'GHS', 'momo', 'successful', '2025-11-27 17:57:53', 'TH17642662739959', NULL, NULL),
(2, 1500.00, 3, 2, 'GHS', 'momo', 'successful', '2025-11-28 11:12:08', 'TH17643283287580', NULL, NULL),
(3, 1600.00, 3, 3, 'GHS', 'momo', 'successful', '2025-11-28 11:45:37', 'TH17643303373100', NULL, NULL),
(4, 1800.00, 3, 4, 'GHS', 'momo', 'successful', '2025-11-28 11:54:04', 'TH-3-1764330822-7308', NULL, NULL),
(5, 1300.00, 2, 5, 'GHS', 'momo', 'successful', '2025-11-28 13:31:54', 'TH-2-1764336675-4815', NULL, NULL),
(6, 1340.00, 4, 6, 'GHS', 'momo', 'successful', '2025-11-28 18:56:35', 'TH-4-1764356179-8393', NULL, NULL),
(7, 120.00, 4, 7, 'GHS', 'momo', 'successful', '2025-11-28 18:58:36', 'TH-4-1764356301-5447', NULL, NULL),
(8, 1040.00, 2, 8, 'GHS', 'momo', 'successful', '2025-11-28 19:33:27', 'TH-2-1764358392-4047', NULL, NULL),
(9, 520.00, 9, 9, 'GHS', 'momo', 'successful', '2025-11-28 19:44:40', 'TH-9-1764359064-6340', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_cat` int(11) NOT NULL,
  `product_brand` int(11) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `product_title` varchar(200) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_desc` text DEFAULT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `product_keywords` varchar(255) DEFAULT NULL,
  `product_condition` enum('new','like-new','good','fair') NOT NULL DEFAULT 'good',
  `product_status` enum('active','inactive','sold','hidden') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_cat`, `product_brand`, `seller_id`, `product_title`, `product_price`, `product_desc`, `product_image`, `product_keywords`, `product_condition`, `product_status`, `created_at`) VALUES
(2, 6, 1, 1, 'sorry', 800.00, 'great', 'uploads/u1/p2/image_1.png', 'vintage', 'like-new', 'active', '2025-11-27 15:56:06'),
(4, 1, 1, 1, 'Test', 500.00, 'great squared', 'uploads/u1/p4/image_1.png', 'vintage', 'like-new', 'active', '2025-11-27 16:11:25'),
(5, 10, 4, 7, 'Cocoa Radiantt', 40.00, 'feels good on skin', 'uploads/u7/p5/image_1.jpg', 'summer, great, cocoa', 'fair', 'active', '2025-11-28 18:37:56');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_no` int(11) NOT NULL,
  `role_description` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_no`, `role_description`) VALUES
(1, 'admin'),
(2, 'customer'),
(3, 'seller');

-- --------------------------------------------------------

--
-- Table structure for table `sectors`
--

CREATE TABLE `sectors` (
  `sector_id` int(11) NOT NULL,
  `sector_description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sectors`
--

INSERT INTO `sectors` (`sector_id`, `sector_description`) VALUES
(1, 'clothing'),
(2, 'electronics'),
(3, 'Accessories'),
(4, 'Home & decor'),
(5, 'Books');

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `user_id` int(11) NOT NULL,
  `shop_name` varchar(150) NOT NULL,
  `type_id` int(11) NOT NULL,
  `sector_id` int(11) NOT NULL,
  `store_logo` varchar(255) DEFAULT NULL,
  `store_banner` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`user_id`, `shop_name`, `type_id`, `sector_id`, `store_logo`, `store_banner`, `description`, `verified`) VALUES
(1, 'Admin Store', 1, 1, NULL, NULL, 'Admin account for testing', 0),
(7, 'Kh\'s', 3, 2, 'uploads/u7/store/logo_6928ae8018b82.png', 'uploads/u7/store/banner_6928ae8019223.png', 'store', 1),
(8, 'Eric\'s', 1, 3, 'uploads/u8/store/logo_6929c50dd1fd3.png', 'uploads/u8/store/banner_6929c50dd3050.jpeg', 'fantastic', 1);

-- --------------------------------------------------------

--
-- Table structure for table `seller_applications`
--

CREATE TABLE `seller_applications` (
  `application_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `documentation_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seller_applications`
--

INSERT INTO `seller_applications` (`application_id`, `user_id`, `documentation_path`, `status`, `submitted_at`, `reviewed_at`, `reviewed_by`) VALUES
(2, 7, 'uploads/u7/documentation.csv', 'approved', '2025-11-27 20:03:12', '2025-11-28 17:50:57', 1),
(3, 8, 'uploads/u8/documentation.csv', 'approved', '2025-11-28 15:51:41', '2025-11-28 19:52:30', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `user_role` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `phone_number`, `user_role`, `created_at`) VALUES
(1, 'Zoie M Atakora-Nsiah', 'zoiemaby@gmail.com', '$2y$10$Fkf5drHRLO0hqmAN0YZ0iOfeoSJpSyvbWjWy1Ya4awb/ftVv.Pz7a', '0543391388', 1, '2025-11-25 22:41:19'),
(2, 'Caleb Sixzo', 'sixzo@gmail.com', '$2y$10$JTXbwtRxfn1gw04WHQo2Iejb5A54X.GGGSixcI4Dbcls0L3WEXVUa', '0204994057', 2, '2025-11-25 22:46:04'),
(3, 'Amma Ntowaa', 'ntowaa@gmail.com', '$2y$10$sxnNZbTfC9bVMyh3tBG1.ewGCi0yGyCjBhWTv6cNyQ9VGmq3lGrrW', '0248245544', 2, '2025-11-25 22:51:25'),
(4, 'Angel Welbeck', 'welbeck@gmail.com', '$2y$10$9GCDVptnJxb8XOhPp/4R5e5EZ3kqhWLzsDHGhWp/0KB52bMzAwebq', '0908776654', 2, '2025-11-25 22:59:25'),
(5, 'Ann Marie', 'ann@gmail.com', '$2y$10$p4olOaiPeH/naPh2j.U5J.E6qnBI79t/i68WUsCY5KMn.iznG5ISW', '0223344567', 3, '2025-11-26 15:48:12'),
(7, 'Khaela Atakora', 'khaela@gmail.com', '$2y$10$ngakIYJfiJBFVoin96mXsOBzFmr74s3QzW39mheT6kt.bt7fPSOdu', '0542291388', 3, '2025-11-27 19:32:39'),
(8, 'Eric Nsiah', 'eric@gnail.com', '$2y$10$jG9usQRXdHGjEWF9uczsTeVayKyEur7xdPKWIXGK6/zx09UdKK/IW', '0244238783', 3, '2025-11-28 15:49:29'),
(9, 'Ibrahim Dasuki', 'khaleel@gmail.com', '$2y$10$hmvUFH52L/M2xYdcMeYcs.Ep5/U2AWuV7YnDFgNerKEllvoRIvVCC', '0555128832', 2, '2025-11-28 19:39:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brand_id`),
  ADD UNIQUE KEY `unique_brand_name_per_user` (`user_id`,`brand_name`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `business_types`
--
ALTER TABLE `business_types`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `cart_p_id_idx` (`p_id`),
  ADD KEY `cart_c_id_idx` (`c_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`conversation_id`),
  ADD KEY `idx_buyer` (`buyer_id`),
  ADD KEY `idx_seller` (`seller_id`),
  ADD KEY `idx_product` (`product_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `idx_conversation` (`conversation_id`),
  ADD KEY `idx_sender` (`sender_id`),
  ADD KEY `idx_receiver` (`receiver_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD PRIMARY KEY (`orderdetail_id`),
  ADD KEY `orderdetails_order_idx` (`order_id`),
  ADD KEY `orderdetails_product_idx` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `orders_customer_idx` (`customer_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`pay_id`),
  ADD KEY `payments_customer_idx` (`customer_id`),
  ADD KEY `payments_order_idx` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `product_cat_idx` (`product_cat`),
  ADD KEY `product_brand_idx` (`product_brand`),
  ADD KEY `product_seller_idx` (`seller_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_no`);

--
-- Indexes for table `sectors`
--
ALTER TABLE `sectors`
  ADD PRIMARY KEY (`sector_id`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `sellers_type_id` (`type_id`),
  ADD KEY `sellers_sector_id` (`sector_id`);

--
-- Indexes for table `seller_applications`
--
ALTER TABLE `seller_applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `seller_app_user_id` (`user_id`),
  ADD KEY `seller_app_reviewed_by` (`reviewed_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email_unique` (`email`),
  ADD KEY `users_fk_role` (`user_role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `conversation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orderdetails`
--
ALTER TABLE `orderdetails`
  MODIFY `orderdetail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `pay_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `seller_applications`
--
ALTER TABLE `seller_applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `brands`
--
ALTER TABLE `brands`
  ADD CONSTRAINT `brands_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_fk_customer` FOREIGN KEY (`c_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_fk_product` FOREIGN KEY (`p_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `fk_conv_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_conv_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_conv_seller` FOREIGN KEY (`seller_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_msg_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_msg_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_msg_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD CONSTRAINT `orderdetails_fk_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orderdetails_fk_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_fk_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_fk_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payments_fk_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_fk_brand` FOREIGN KEY (`product_brand`) REFERENCES `brands` (`brand_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `products_fk_cat` FOREIGN KEY (`product_cat`) REFERENCES `categories` (`cat_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `products_fk_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `sellers`
--
ALTER TABLE `sellers`
  ADD CONSTRAINT `sellers_fk_sector` FOREIGN KEY (`sector_id`) REFERENCES `sectors` (`sector_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sellers_fk_type` FOREIGN KEY (`type_id`) REFERENCES `business_types` (`type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sellers_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `seller_applications`
--
ALTER TABLE `seller_applications`
  ADD CONSTRAINT `seller_app_fk_reviewed_by` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `seller_app_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_fk_role` FOREIGN KEY (`user_role`) REFERENCES `roles` (`role_no`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- مضيف: localhost
-- وقت الجيل: 14 ديسمبر 2025 الساعة 01:07
-- إصدار الخادم: 8.0.43
-- نسخة PHP: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- قاعدة بيانات: `book_exchange`
--

-- --------------------------------------------------------

--
-- بنية الجدول `books`
--

CREATE TABLE `books` (
  `edition` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `id` int NOT NULL,
  `title` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `author` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `category` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `books`
--

INSERT INTO `books` (`edition`, `price`, `id`, `title`, `author`, `user_id`, `image`, `category`) VALUES
('الطبعه الثانية', 99, 19, 'عصر التنقيب في البيانات عبر تقنيات الذكاء الاصطناعي التطور – التحديات الفنية والقانونية – العيوب خوارزمية الذكاء الاصطناعي دراسة مقارنة', ' احمد حسين علي', 14, '1765048450_3ea13b05-6e1a-4de8-a80e-1c3849436b98-thumbnail-1000x1000-70.jpg', NULL),
('الطبعة الأولى', 99, 20, 'عصر التنقيب في البيانات', 'أحمد حسين علي', 1, 'ai.jpg', 'تقنية'),
('الطبعه الثانية', 77, 21, 'علوم الحاسب الالي و المعلومات المدخل إلى البرمجة الخطية وتطبيقاتها في الإدارة', 'خالد موسى الطاسان', 14, '1765230460_1444.png', NULL),
('الطبعه الثانية', 77, 22, 'l', 'خالد موسى الطاسان', 14, '1765234849_1444.png', NULL),
('الرابعة', 77, 23, 'المهارات اللغوية عرب 101', 'خالد محمد', 19, '1765670488_47b1ca32-5389-41bb-ba13-1051c2ad40c3-1000x1000-d47NWMD2DVegn8rOwTZltjzCJM9SFJU5G2P92efw.webp', NULL),
('الرابعة', 99, 24, 'المهارات اللغوية', 'احمد علي', 14, '1765674099_47b1ca32-5389-41bb-ba13-1051c2ad40c3-1000x1000-d47NWMD2DVegn8rOwTZltjzCJM9SFJU5G2P92efw.webp', NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `cart`
--

CREATE TABLE `cart` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `book_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `book_id`, `quantity`) VALUES
(9, 1, 20, 1),
(13, 14, 20, 1),
(14, 19, 23, 1),
(15, 14, 21, 2);

-- --------------------------------------------------------

--
-- بنية الجدول `messages`
--

CREATE TABLE `messages` (
  `id` int NOT NULL,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `date` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `book_id` int DEFAULT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `date`, `book_id`, `seen`) VALUES
(1, 14, 14, 'gt', '2025-12-14 03:49:05', 19, 1),
(2, 14, 14, 'www', '2025-12-14 03:49:05', 19, 1),
(3, 14, 14, 'z', '2025-12-14 03:49:05', 19, 1),
(4, 14, 14, 'ي', '2025-12-14 03:49:05', 19, 1),
(5, 14, 14, 'السلام عليكم ', '2025-12-14 03:49:05', 19, 1),
(6, 14, 14, 'كيف حالك', '2025-12-14 03:49:05', 19, 1),
(7, 14, 14, 'ا', '2025-12-14 03:49:05', 19, 1),
(8, 14, 14, 'ت', '2025-12-14 03:49:05', 19, 1),
(9, 14, 14, 'ت', '2025-12-14 03:49:05', 19, 1),
(10, 14, 1, 'ا ى', '2025-12-14 02:40:36', NULL, 0),
(11, 19, 14, 's', '2025-12-14 03:24:33', 19, 1),
(12, 19, 14, 's', '2025-12-14 03:24:33', 19, 1),
(13, 14, 19, 'ا', '2025-12-14 03:13:53', 23, 1),
(14, 19, 14, 'السلام عليكم ', '2025-12-14 03:24:33', 19, 1),
(15, 19, 14, 'السلام عليكم ', '2025-12-14 03:24:33', 19, 1),
(16, 14, 19, 'يرءي', '2025-12-14 03:24:39', 23, 0),
(17, 14, 19, 'ب', '2025-12-14 03:24:43', 23, 0);

-- --------------------------------------------------------

--
-- بنية الجدول `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `name`, `email`, `address`, `payment_method`, `total_amount`, `created_at`) VALUES
(1, 14, 'shmael sultan', 'shamael1423@gmail.com', 'المدينة المنوره ', 'cash', 113.85, '2025-12-06 21:50:17'),
(2, 14, 'shmael sultan', 'shamael1423@gmail.com', 'المدينة المنوره ', 'cash', 113.85, '2025-12-06 21:51:15'),
(3, 14, 'shmael sultan', 'shamael1423@gmail.com', 'المدينة المنوره ', 'cash', 113.85, '2025-12-06 21:51:18'),
(4, 14, 'shmael sultan', 'shamael1423@gmail.com', 'المدينة المنوره ', 'cash', 113.85, '2025-12-06 21:53:34'),
(5, 14, 'shmael sultan', 'shamael1423@gmail.com', 'المدينة المنوره ', 'cash', 113.85, '2025-12-06 22:17:57'),
(6, 14, 'shmael sultan', 'shamael1423@gmail.com', 'المدينة المنوره ', 'cash', 113.85, '2025-12-13 22:05:08');

-- --------------------------------------------------------

--
-- بنية الجدول `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `book_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `book_id`, `quantity`, `price`) VALUES
(1, 4, 19, 1, 99.00),
(2, 5, 19, 1, 99.00),
(3, 6, 19, 1, 99.00);

-- --------------------------------------------------------

--
-- بنية الجدول `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `phone` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `location` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `shipping_company` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `profile`
--

CREATE TABLE `profile` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `bio` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `avatar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `join_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `location` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `profile`
--

INSERT INTO `profile` (`id`, `user_id`, `bio`, `phone`, `avatar`, `join_date`, `location`) VALUES
(1, 14, '', '0536910118', 'uploads/avatar_14_1765664903.png', '2025-12-14 00:24:38', NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id` int NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`password`, `created_at`, `id`, `name`, `email`) VALUES
('$2y$10$zg58cT9rCZ.eb2QA8RrpY.r0KuJlLO.PX4fqRpl19b.UFxpsu5yXS', '2025-10-08 22:58:23', 1, 'Shamael sultan', 'shamaelm2003@gmail.com'),
('$2y$10$mOnZv9ur7MaZf4vkeGMM0.8CD7oTQRZXllybRVQDertIZ.UGG.R4q', '2025-10-14 08:49:22', 14, 'Shamael sultan', 'shamael1423@gmail.com'),
('$2y$10$sZPM9VPvAS1Q8DadqBi6suZBlVI3dwQ0546XoLRSCgjkhq5FXbLJK', '2025-12-13 23:53:51', 19, 'ammar', 'amaaar.1427@gmail.com');

--
-- Indexes for dumped tables
--

--
-- فهارس للجدول `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- فهارس للجدول `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- فهارس للجدول `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- فهارس للجدول `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- فهارس للجدول `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `book_id` (`book_id`);

--
-- فهارس للجدول `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- فهارس للجدول `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- فهارس للجدول `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- القيود المفروضة على الجداول الملقاة
--

--
-- قيود الجداول `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `fk_books_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- قيود الجداول `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- قيود الجداول `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);

--
-- قيود الجداول `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- قيود الجداول `profile`
--
ALTER TABLE `profile`
  ADD CONSTRAINT `profile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

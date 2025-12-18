-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- مضيف: localhost
-- وقت الجيل: 16 ديسمبر 2025 الساعة 20:16
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
('2', 60, 31, 'effective academic writing intro', 'Philip Kotler, Gary Armstrong', 19, '1765680671_7EYNyxGELBAq0CWyAdCx3ftZO5lbjrSyPmky1IYn.webp', NULL),
('2', 90, 32, 'english vocabulary in usa 3rd edition', 'Philip Kotler', 19, '1765680725_t3IX9XGjpPz7i71NylCMFbzG6kbO8AT56EAHGb7Y.webp', NULL),
('1', 65, 33, 'الاجراءات الجزئية', 'د.فهد الطريسي', 20, '1765914525_a2dc1cc1-32b3-478b-86ee-b17a95e9c3ad.jpg', NULL);

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
(29, 14, 19, 'السلام عليكم ', '2025-12-15 06:17:24', NULL, 1),
(32, 19, 14, 'وعليكم السلام', '2025-12-15 06:50:08', NULL, 1),
(33, 14, 19, 'وعليكم السلام', '2025-12-15 07:14:39', NULL, 0),
(34, 20, 14, 'السلام عليكم', '2025-12-16 22:43:47', NULL, 1),
(35, 14, 20, 'وعليكم السلام', '2025-12-16 22:47:46', NULL, 1);

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
(6, 14, 'shmael sultan', 'shamael1423@gmail.com', 'المدينة المنوره ', 'cash', 113.85, '2025-12-13 22:05:08'),
(7, 19, 'shmael sultan', 'shamael1423@gmail.com', 'المدينة المنوره ', 'card', 0.00, '2025-12-14 02:50:35'),
(8, 14, 'shmael sultan', 'shamael1423@gmail.com', 'المدينة المنوره ', 'cash', 69.00, '2025-12-15 04:26:41'),
(9, 20, 'Arwa', 'Arwa12@gmail.cim', 'المدينة المنوره ', 'cash', 113.85, '2025-12-16 19:41:34');

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
(3, 6, 19, 1, 99.00),
(4, 8, 31, 1, 60.00),
(5, 9, 19, 1, 99.00);

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
(1, 14, '', '0536910118', 'uploads/avatar_14_1765664903.png', '2025-12-14 00:24:38', NULL),
(2, 19, '', '', 'avatars/avatar_19_1765678651.webp', '2025-12-14 05:10:29', NULL);

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
('$2y$10$sZPM9VPvAS1Q8DadqBi6suZBlVI3dwQ0546XoLRSCgjkhq5FXbLJK', '2025-12-13 23:53:51', 19, 'ammar', 'amaaar.1427@gmail.com'),
('$2y$10$Z6mbOegJ2CivOwgDvR6WW.bUf9YXTu41Q84zZS5FKYo.UG7gMwP/m', '2025-12-16 19:40:03', 20, 'Arwa', 'Arwa12@gmail.com');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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

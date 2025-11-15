-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- مضيف: localhost
-- وقت الجيل: 15 نوفمبر 2025 الساعة 15:31
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
  `title` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `author` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `books`
--

INSERT INTO `books` (`edition`, `price`, `id`, `title`, `author`, `user_id`, `image`) VALUES
('الطبعه الثانية', 60, 1, 'Calculus: Early Transcendentals – James Stewart', 'James Stewart', 1, NULL),
('الطبعه الثانية', 70, 2, 'Principles of Marketing', 'Philip Kotler, Gary Armstrong', 1, NULL),
('الطبعه الثانية', 70, 3, 'Principles of Marketing', 'Philip Kotler, Gary Armstrong', 1, NULL),
('الطبعه الثانية', 70, 4, 'Principles of Marketing', 'Philip Kotler, Gary Armstrong', 1, NULL),
('الطبعه الثانية', 70, 5, 'Principles of Marketing', 'Philip Kotler, Gary Armstrong', 1, NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `messages`
--

CREATE TABLE `messages` (
  `id` int NOT NULL,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `sent_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
('$2y$10$kugFgQZBoqgdPiVLilRnKuzOLUBxEgAH7sO3LGCcHkf98NqaQUb6W', '2025-10-08 22:58:31', 2, '', 'Shamael'),
('$2y$10$bRw76/x/aLL/sFwbXFTF3e0gvaTSk2XJ//6KhX6ngTAFP0eeEeFHC', '2025-10-10 09:30:31', 7, 'Shamael sultan', 'shamawwelm2003@gmail.com'),
('$2y$10$xLr52g5JtUwcgb5m6oKwgOG2a4TidsjF9yF4K1j0ZadRbADrwfVkW', '2025-10-10 09:38:26', 8, 'Shamael sultan', 'shamaelrrm2003@gmail.com'),
('$2y$10$PhMsP.5igCKj3F/5cXz6quljlnxXl4Hk7qqD4OO.hmA05CzBdbplO', '2025-10-10 10:03:07', 9, 'SA', 'shamaelmR2003@gmail.com'),
('$2y$10$fe4G5Rzj9VXjsglvdjDwIetoVq5ztC88ROqbMdi4mQaR8mCIBnByu', '2025-10-14 07:11:09', 10, 'Shamael sultan', 'ahd12@gmail.com'),
('$2y$10$XzwjvzixgDMC1TkplPsTje8954VK1aTa4gp8B7p.Sx/x8NPRAc.6O', '2025-10-14 07:20:27', 11, 'Shamael sultan', 'ddff1423@gmail.com'),
('$2y$10$zl85RuBWBecHawsnvzYKQ.EUwDTRwktTHOhMo8JW71/5cp4PFFYkK', '2025-10-14 07:25:08', 12, 'Shamael sultan', 'jjii1423@gmail.com'),
('$2y$10$tiZ1wUKYLERUpqYhQLR9TeSe69aMMJuKyQa2BxLgvy.cr4vGdWhTi', '2025-10-14 08:38:36', 13, 'Shamael sultan', '1423@gmail.com'),
('$2y$10$mOnZv9ur7MaZf4vkeGMM0.8CD7oTQRZXllybRVQDertIZ.UGG.R4q', '2025-10-14 08:49:22', 14, 'Shamael sultan', 'shamael1423@gmail.com'),
('$2y$10$hM/25WLlgNB773BGEPSUCeAietiuwYftqRZjlokfxSEG0D5V9cjae', '2025-10-15 08:22:59', 15, 'Shamael sultan', 'shama777el1423@gmail.com'),
('$2y$10$nh/BX15Fm3eSKfgfcoCuLuQzt9CZQWHB4FsYlaqVM3KBsdGe5K5QS', '2025-10-15 08:45:45', 16, 'Shamael sultan', 'shamael45@gmail.com'),
('$2y$10$jFZ9bRqRyHE89L56oXzxwuzNqEJYf.sS7ijMQRdoUWS3IX46KE6I.', '2025-11-15 14:43:50', 17, 'Shamael sultan', 'shamaellll1423@gmail.com'),
('$2y$10$DGFacST0efd.N11VOZO2VuGyyipzLZk5psm5jsgiO/YcGSpRfw0lq', '2025-11-15 15:00:25', 18, 'Shamael sultan', 'sh8@gmail.com');

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
-- فهارس للجدول `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- فهارس للجدول `payments`
--
ALTER TABLE `payments`
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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- القيود المفروضة على الجداول الملقاة
--

--
-- قيود الجداول `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `fk_books_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- قيود الجداول `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- قيود الجداول `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
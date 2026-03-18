-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Gegenereerd op: 17 mrt 2026 om 23:13
-- Serverversie: 12.0.2-MariaDB-ubu2404
-- PHP-versie: 8.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `SmartLibraryManagementSystem`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `genre` varchar(80) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `published_year` smallint(6) DEFAULT NULL,
  `cover_url` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `total_copies` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Gegevens worden geëxporteerd voor tabel `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `isbn`, `genre`, `description`, `published_year`, `cover_url`, `created_at`, `total_copies`) VALUES
(1, '1984', 'George Orwell', '9780451524935', 'Dystopian', 'A dystopian novel about surveillance and totalitarianism.', 1949, 'assets/Uploads/covers/cover_01_a9f3c2d8e1.webp', '2026-02-03 19:47:03', 5),
(2, 'To Kill a Mockingbird', 'Harper Lee', '9780061120084', 'Classic', 'A story of racial injustice and moral growth in the American South.', 1960, 'assets/Uploads/covers/cover_02_b7e4f1c9a3.webp', '2026-02-03 19:47:03', 5),
(3, 'The Great Gatsby', 'F. Scott Fitzgerald', '9780743273565', 'Classic', 'A critique of the American Dream set in the Jazz Age.', 1925, 'assets/Uploads/covers/cover_03_d2c8a9f1e7.webp', '2026-02-03 19:47:03', 5),
(4, 'Brave New World', 'Aldous Huxley', '9780060850524', 'Dystopian', 'A future society shaped by technology, conditioning, and control.', 1932, 'assets/Uploads/covers/cover_04_f1a7e3c9d2.webp', '2026-02-03 19:47:03', 5),
(5, 'The Catcher in the Rye', 'J.D. Salinger', '9780316769488', 'Classic', 'A coming-of-age story about alienation and identity.', 1951, 'assets/Uploads/covers/cover_05_c9e1d7a3f8.webp', '2026-02-03 19:47:03', 5),
(6, 'Fahrenheit 451', 'Ray Bradbury', '9781451673319', 'Dystopian', 'A society where books are outlawed and “firemen” burn them.', 1953, 'assets/Uploads/covers/cover_06_e3f9c1a7d2.webp', '2026-02-03 19:47:03', 5),
(7, 'Moby-Dick', 'Herman Melville', '9781503280786', 'Classic', 'A sea captain’s obsession with hunting the white whale.', 1851, 'assets/Uploads/covers/cover_07_7c9a2f1e3d.webp', '2026-02-03 19:47:03', 5),
(8, 'Pride and Prejudice', 'Jane Austen', '9781503290563', 'Romance', 'Love, manners, and misunderstandings in Regency England.', 1813, 'assets/Uploads/covers/cover_08_9e1c7d3f2a.webp', '2026-02-03 19:47:03', 5),
(9, 'The Hobbit', 'J.R.R. Tolkien', '9780547928227', 'Fantasy', 'Bilbo Baggins joins a quest to reclaim a lost dwarf kingdom.', 1937, 'assets/Uploads/covers/cover_09_3f7a9d1c2e.webp', '2026-02-03 19:47:03', 5),
(10, 'Harry Potter and the Philosopher\'s Stone', 'J.K. Rowling', '9780747532699', 'Fantasy', 'A boy discovers he is a wizard and begins his journey at Hogwarts.', 1997, 'assets/Uploads/covers/cover_10_d9a3f7c2e1.webp', '2026-02-03 19:47:03', 5),
(11, 'Animal Farm', 'George Orwell', '9780451585935', 'Dystopia', 'A satirical allegorical novella that tells the story of a group of farm animals who rebel against their human farmer, hoping to create a society where the animals can be equal, free, and happy.', 1945, 'assets/Uploads/covers/cover_11_d9a3f7c2e1.jpg', '2026-02-19 18:36:45', 5);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `book_copies`
--

CREATE TABLE `book_copies` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `copy_code` varchar(50) NOT NULL,
  `status` enum('available','maintenance','lost','retired') NOT NULL DEFAULT 'available',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Gegevens worden geëxporteerd voor tabel `book_copies`
--

INSERT INTO `book_copies` (`id`, `book_id`, `copy_code`, `status`, `created_at`) VALUES
(1, 4, 'COPY-4-001', 'available', '2026-02-03 19:47:03'),
(2, 2, 'COPY-2-001', 'available', '2026-02-03 19:47:03'),
(3, 5, 'COPY-5-001', 'available', '2026-02-03 19:47:03'),
(4, 1, 'COPY-1-001', 'available', '2026-02-03 19:47:03'),
(5, 9, 'COPY-9-001', 'available', '2026-02-03 19:47:03'),
(6, 3, 'COPY-3-001', 'available', '2026-02-03 19:47:03'),
(7, 10, 'COPY-10-001', 'available', '2026-02-03 19:47:03'),
(8, 6, 'COPY-6-001', 'available', '2026-02-03 19:47:03'),
(9, 7, 'COPY-7-001', 'available', '2026-02-03 19:47:03'),
(10, 8, 'COPY-8-001', 'available', '2026-02-03 19:47:03'),
(16, 4, 'COPY-4-002', 'available', '2026-02-03 19:47:04'),
(17, 2, 'COPY-2-002', 'available', '2026-02-03 19:47:04'),
(18, 5, 'COPY-5-002', 'available', '2026-02-03 19:47:04'),
(19, 1, 'COPY-1-002', 'available', '2026-02-03 19:47:04'),
(20, 9, 'COPY-9-002', 'available', '2026-02-03 19:47:04'),
(21, 3, 'COPY-3-002', 'available', '2026-02-03 19:47:04'),
(22, 10, 'COPY-10-002', 'available', '2026-02-03 19:47:04'),
(23, 6, 'COPY-6-002', 'available', '2026-02-03 19:47:04'),
(24, 7, 'COPY-7-002', 'available', '2026-02-03 19:47:04'),
(25, 8, 'COPY-8-002', 'available', '2026-02-03 19:47:04'),
(127, 1, 'COPY-1-003', 'available', '2026-02-25 12:01:16'),
(128, 1, 'COPY-1-004', 'available', '2026-02-25 12:01:16'),
(129, 1, 'COPY-1-005', 'available', '2026-02-25 12:01:16'),
(130, 2, 'COPY-2-003', 'available', '2026-02-25 12:01:16'),
(131, 2, 'COPY-2-004', 'available', '2026-02-25 12:01:16'),
(132, 2, 'COPY-2-005', 'available', '2026-02-25 12:01:16'),
(133, 4, 'COPY-4-003', 'available', '2026-02-25 12:01:16'),
(134, 4, 'COPY-4-004', 'available', '2026-02-25 12:01:16'),
(135, 4, 'COPY-4-005', 'available', '2026-02-25 12:01:16');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `loans`
--

CREATE TABLE `loans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `copy_id` int(11) NOT NULL,
  `loaned_at` datetime NOT NULL DEFAULT current_timestamp(),
  `due_at` datetime NOT NULL,
  `returned_at` datetime DEFAULT NULL,
  `renew_count` tinyint(4) NOT NULL DEFAULT 0,
  `fine_amount` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Gegevens worden geëxporteerd voor tabel `loans`
--

INSERT INTO `loans` (`id`, `user_id`, `copy_id`, `loaned_at`, `due_at`, `returned_at`, `renew_count`, `fine_amount`) VALUES
(1, 1, 4, '2026-02-10 10:25:54', '2026-02-24 10:25:54', '2026-02-12 11:13:43', 0, 0.00),
(2, 1, 19, '2026-02-10 10:43:42', '2026-02-24 10:43:42', '2026-02-10 11:02:04', 0, 0.00),
(3, 1, 7, '2026-02-10 11:02:14', '2026-02-24 11:02:14', '2026-02-12 11:18:22', 0, 0.00),
(4, 1, 19, '2026-02-10 11:33:35', '2026-02-24 11:33:35', '2026-02-12 11:13:36', 0, 0.00),
(5, 2, 5, '2026-02-10 13:23:40', '2026-02-24 13:23:40', NULL, 0, 0.00),
(6, 2, 1, '2026-02-10 13:32:23', '2026-02-24 13:32:23', NULL, 0, 0.00),
(7, 1, 8, '2026-02-10 13:51:12', '2026-02-24 13:51:12', '2026-02-10 13:51:23', 0, 0.00),
(8, 1, 16, '2026-02-10 15:27:43', '2026-02-24 15:27:43', '2026-02-12 11:13:22', 0, 0.00),
(9, 1, 4, '2026-02-12 11:18:32', '2026-02-26 11:18:32', '2026-02-12 11:33:18', 0, 0.00),
(10, 1, 8, '2026-02-12 11:18:41', '2026-02-26 11:18:41', '2026-02-12 11:19:04', 0, 0.00),
(11, 1, 8, '2026-02-19 14:11:09', '2026-03-05 14:11:09', '2026-03-17 22:55:09', 0, 0.00),
(12, 1, 4, '2026-02-25 12:30:11', '2026-03-11 12:30:11', '2026-03-17 22:45:51', 0, 0.00),
(13, 1, 9, '2026-03-05 09:28:05', '2026-03-19 09:28:05', NULL, 0, 0.00),
(14, 1, 16, '2026-03-17 22:45:32', '2026-03-31 22:45:32', '2026-03-17 23:03:18', 0, 0.00);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('waiting','ready','fulfilled','canceled','expired') NOT NULL DEFAULT 'waiting',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `ready_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Gegevens worden geëxporteerd voor tabel `reservations`
--

INSERT INTO `reservations` (`id`, `book_id`, `user_id`, `status`, `created_at`, `ready_at`, `expires_at`) VALUES
(1, 1, 2, 'waiting', '2026-02-10 13:14:50', NULL, NULL),
(2, 1, 1, 'canceled', '2026-02-10 14:57:32', NULL, NULL),
(3, 4, 1, 'canceled', '2026-02-10 15:27:54', NULL, NULL),
(4, 4, 3, 'waiting', '2026-02-10 15:29:01', NULL, NULL),
(5, 1, 1, 'canceled', '2026-02-12 11:12:36', NULL, NULL),
(6, 1, 1, 'canceled', '2026-02-12 11:19:20', NULL, NULL),
(7, 4, 1, 'canceled', '2026-02-12 11:33:37', NULL, NULL);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role` enum('member','librarian') NOT NULL DEFAULT 'member',
  `name` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Gegevens worden geëxporteerd voor tabel `users`
--

INSERT INTO `users` (`id`, `role`, `name`, `email`, `password_hash`, `is_blocked`, `created_at`, `updated_at`) VALUES
(1, 'member', 'Darlington Jones', 'darlingtonjones15@gmail.com', '$2y$12$EziqK0VCs38a.QCz5Ak05OLcnTpvN5GyaAC8yQupoQG7H1hDyFfd2', 0, '2026-02-06 16:48:36', '2026-02-19 14:16:03'),
(2, 'member', 'Noku Landing', 'darlingtonjones20@gmail.com', '$2y$12$kOwuJonxjMdwOhTAHXpObe45QfqmEjs7n.XxdxADUSK88jAKyDDbi', 0, '2026-02-10 11:38:23', '2026-02-10 11:38:23'),
(3, 'member', 'Dave Jones', 'morrislouise32@gmail.com', '$2y$12$quKIiGEg7DKC.RTvKqJ5Y.HFWM/AG77FcRWNhAa2Ga8T6Idc5EuR.', 0, '2026-02-10 15:28:40', '2026-02-10 15:28:40'),
(4, 'librarian', 'Daniel Breczinski', 'danielbreczinski30@gmail.com', '$2y$12$tjnWa0NP3qupEla.gsekK.PrQCcjf01KNtC04fvmt/rr3W38csaku', 0, '2026-02-12 18:52:56', '2026-02-25 11:14:48');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `idx_books_title` (`title`),
  ADD KEY `idx_books_author` (`author`),
  ADD KEY `idx_books_genre` (`genre`);

--
-- Indexen voor tabel `book_copies`
--
ALTER TABLE `book_copies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_copy_code` (`copy_code`),
  ADD KEY `idx_copies_book` (`book_id`),
  ADD KEY `idx_copies_status` (`status`);

--
-- Indexen voor tabel `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_loans_user` (`user_id`),
  ADD KEY `idx_loans_copy` (`copy_id`),
  ADD KEY `idx_loans_active` (`returned_at`,`due_at`);

--
-- Indexen voor tabel `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_res_book_status_created` (`book_id`,`status`,`created_at`),
  ADD KEY `idx_res_user` (`user_id`);

--
-- Indexen voor tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT voor een tabel `book_copies`
--
ALTER TABLE `book_copies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT voor een tabel `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT voor een tabel `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT voor een tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `book_copies`
--
ALTER TABLE `book_copies`
  ADD CONSTRAINT `fk_copies_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Beperkingen voor tabel `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `fk_loans_copy` FOREIGN KEY (`copy_id`) REFERENCES `book_copies` (`id`),
  ADD CONSTRAINT `fk_loans_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Beperkingen voor tabel `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `fk_res_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_res_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2024 at 11:38 AM
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
-- Database: `library`
--

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `authorid` int(9) NOT NULL,
  `name` char(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`authorid`, `name`) VALUES
(16, 'Author Jay'),
(20, 'Author T'),
(21, 'Author U'),
(22, 'Author V'),
(23, 'Author W'),
(24, 'Author X'),
(25, 'Author Y'),
(26, 'Author Z'),
(27, 'Author AA'),
(28, 'Author BB'),
(29, 'Author CC'),
(30, 'Author DD'),
(31, 'Author EE'),
(32, 'Author FF'),
(33, 'Author GG'),
(35, 'Author II'),
(36, 'Author JJ'),
(37, 'Author KK'),
(38, 'Author LL'),
(39, 'Author MM'),
(40, 'Author NN'),
(41, 'Author OO'),
(42, 'Author PP'),
(43, 'Author QQ'),
(44, 'Author RR'),
(45, 'Author SS'),
(46, 'Author TT'),
(47, 'Author UU'),
(48, 'Author VV'),
(49, 'Author WW'),
(50, 'Author XX'),
(51, 'Author YY'),
(52, 'Author ZZ'),
(53, 'Author AAA'),
(54, 'Author BBB'),
(55, 'Author CCC'),
(56, 'Author DDD'),
(57, 'Author EEE'),
(58, 'Author FFF'),
(59, 'Author GGG'),
(60, 'Author HHH'),
(61, 'Author III'),
(62, 'Author JJJ'),
(63, 'Author KKK'),
(64, 'Author LLL'),
(65, 'Author MMM'),
(66, 'Author NNN'),
(67, 'Author OOO'),
(68, 'Author PPP'),
(69, 'Author QQQ'),
(70, 'Author RRR'),
(71, 'Author SSS'),
(72, 'Author TTT'),
(73, 'Author UUU'),
(74, 'Author VVV'),
(75, 'Author WWW'),
(76, 'Author XXX'),
(77, 'Author YYY'),
(78, 'Author ZZZ'),
(79, 'Author AAAA'),
(80, 'Author BBBB'),
(81, 'Author CCCC'),
(82, 'Author DDDD'),
(83, 'Author EEEE'),
(84, 'Author FFFF'),
(85, 'Author GGGG'),
(86, 'Author HHHH'),
(87, 'Author IIII'),
(88, 'Author JJJJ'),
(89, 'Author KKKK'),
(90, 'Author LLLL'),
(91, 'Author MMMM'),
(92, 'Author NNNN'),
(93, 'Author OOOO'),
(94, 'Author PPPP'),
(95, 'Author QQQQ'),
(96, 'Author RRRR'),
(97, 'Author SSSS'),
(98, 'Author TTTT'),
(99, 'Author UUUU'),
(101, 'J.K. Rowling'),
(106, 'Rob'),
(107, 'denrf'),
(108, 'CAE'),
(110, 'AEC'),
(111, 'AEC'),
(112, 'ECA'),
(113, '32'),
(114, 'hrt'),
(115, 'kascbjal'),
(116, 'kacsbakj'),
(117, 'efbql'),
(118, 'nd v,'),
(119, 'ndsv ,'),
(120, ',mb d'),
(121, ',mb dm'),
(122, 'nd c'),
(123, 'jwnbgrl'),
(124, 'bdjas'),
(125, 'jhefja');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `bookid` int(11) NOT NULL,
  `title` char(255) NOT NULL,
  `authorid` int(9) NOT NULL,
  `is_borrowed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`bookid`, `title`, `authorid`, `is_borrowed`) VALUES
(70, 'Book Title 70', 70, 0),
(71, 'Book Title 71', 71, 0),
(72, 'Book Title 72', 72, 0),
(73, 'Book Title 73', 73, 0),
(74, 'Book Title 74', 74, 0),
(75, 'Book Title 75', 75, 0),
(76, 'Book Title 76', 76, 0),
(77, 'Book Title 77', 77, 1),
(78, 'Book Title 78', 78, 0),
(79, 'Book Title 79', 79, 1),
(80, 'Book Title 80', 80, 0),
(81, 'Book Title 81', 81, 0),
(82, 'Book Title 82', 82, 0),
(83, 'Book Title 83', 83, 1),
(84, 'Book Title 84', 84, 0),
(85, 'Book Title 85', 85, 1),
(86, 'Book Title 86', 86, 1),
(87, 'Book Title 87', 87, 0),
(88, 'Book Title 88', 88, 0),
(89, 'Book Title 89', 89, 1),
(90, 'Book Title 90', 90, 1),
(91, 'Book Title 91', 91, 0),
(92, 'Book Title 92', 92, 0),
(93, 'Book Title 93', 93, 0),
(94, 'Book Title 94', 94, 0),
(95, 'Book Title 95', 95, 0),
(96, 'Book Title 96', 96, 0),
(97, 'Book Title 97', 97, 0),
(98, 'Book Title 98', 98, 1),
(99, 'Book Title 99', 99, 1),
(141, 'The Great Gatsby', 83, 0),
(144, 'book101', 99, 0),
(145, 'thisisabook', 77, 0),
(146, 'thisisabook', 77, 0),
(147, 'thisisabook', 77, 1),
(149, 'ewf', 66, 1),
(150, 'fewf', 45, 0),
(151, 'dv', 45, 0),
(152, '56', 67, 0),
(153, '55', 45, 1),
(154, '45', 45, 1),
(156, 'sd', 45, 0),
(158, 'sfgv', 45, 0),
(159, 'ewcewce', 67, 0),
(162, 'feq', 56, 0),
(163, '56', 56, 0),
(164, 'trs', 45, 1),
(165, 'grew', 45, 0),
(166, 'qdq', 45, 0),
(169, 'acdc', 23, 0),
(172, 'cda', 45, 0),
(173, 'dfg', 67, 0),
(179, 'bf4t', 45, 0),
(180, 'vrfsa', 45, 0),
(182, 'bfzs', 45, 1),
(183, 'aS', 43, 0),
(184, 'zc', 45, 0),
(188, 'sgve', 45, 0),
(193, 'd z', 44, 0),
(208, 'vsjh', 89, 0),
(209, 'vsjh', 89, 0),
(210, 'vsjh', 89, 0),
(211, 'vsjh', 89, 0),
(212, 'vsjh', 89, 0),
(214, 'vsjh', 89, 0),
(215, 'vsjh', 89, 0),
(216, 'vsjh', 89, 0),
(217, 'asc', 44, 0),
(221, 'wfe', 56, 1),
(222, 'cs', 90, 1),
(223, 'asd', 56, 0),
(224, 'fe', 46, 1),
(225, 'evw', 54, 1),
(232, 'wef', 45, 1),
(237, 'qdw', 45, 1),
(272, 'efa', 45, 0),
(279, 'q2w4', 53, 0),
(283, '32r', 32, 0),
(285, 'dq', 56, 0),
(288, 'qW', 56, 0),
(289, 'cqa', 78, 0),
(291, 'WE', 65, 0),
(292, 'asc', 67, 0),
(293, 'fw', 67, 0),
(294, 'cas', 45, 0),
(295, 'vrw', 67, 0),
(296, 'acd', 56, 0),
(297, 'cw', 46, 0),
(300, 'Invictus', 50, 0),
(301, ',ascba', 56, 0),
(302, 'mds', 56, 0),
(305, 'asbcn', 45, 0);

-- --------------------------------------------------------

--
-- Table structure for table `books_authors`
--

CREATE TABLE `books_authors` (
  `collectionid` int(9) NOT NULL,
  `bookid` int(9) NOT NULL,
  `authorid` int(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books_authors`
--

INSERT INTO `books_authors` (`collectionid`, `bookid`, `authorid`) VALUES
(70, 70, 70),
(71, 71, 71),
(72, 72, 72),
(73, 73, 73),
(74, 74, 74),
(75, 75, 75),
(76, 76, 76),
(77, 77, 77),
(78, 78, 78),
(79, 79, 79),
(80, 80, 80),
(81, 81, 81),
(82, 82, 82),
(83, 83, 83),
(84, 84, 84),
(85, 85, 85),
(86, 86, 86),
(87, 87, 87),
(88, 88, 88),
(89, 89, 89),
(90, 90, 90),
(91, 91, 91),
(92, 92, 92),
(93, 93, 93),
(94, 94, 94),
(95, 95, 95),
(96, 96, 96),
(97, 97, 97),
(98, 98, 98),
(99, 99, 99);

-- --------------------------------------------------------

--
-- Table structure for table `borrowed_books`
--

CREATE TABLE `borrowed_books` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `book_id` int(11) NOT NULL,
  `borrow_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `borrower_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowed_books`
--

INSERT INTO `borrowed_books` (`id`, `user_id`, `book_id`, `borrow_date`, `due_date`, `return_date`, `borrower_name`) VALUES
(50, NULL, 95, '2024-12-18', '2024-12-19', '2024-12-08', 'sthret'),
(51, NULL, 98, '2024-12-11', '2024-12-13', '2024-12-08', 'qef'),
(52, NULL, 98, '2024-12-17', '2024-12-19', NULL, 'csC'),
(53, NULL, 99, '2024-12-18', '2024-12-18', '2024-12-08', 'svsr'),
(54, NULL, 154, '2024-12-03', '2024-12-04', NULL, 'acS'),
(55, NULL, 156, '2024-12-24', '2024-12-19', '2024-12-08', 'cda'),
(56, NULL, 164, '2024-12-12', '2024-12-13', NULL, 'qef'),
(57, NULL, 182, '2024-12-03', '2024-12-13', NULL, 'ef'),
(58, NULL, 221, '2024-12-12', '2024-12-12', NULL, 'fwrf'),
(59, NULL, 222, '2024-12-12', '2024-12-13', NULL, 'df'),
(70, NULL, 86, '2024-12-17', '2024-12-04', NULL, 'sva'),
(71, NULL, 79, '2024-12-10', '2024-12-19', NULL, 'qew'),
(72, NULL, 147, '2024-12-12', '2024-12-21', NULL, 'svr'),
(73, NULL, 149, '2024-12-10', '2024-12-12', NULL, 'sv'),
(74, NULL, 179, '2024-12-19', '2024-12-05', '2024-12-09', 'cwe'),
(75, NULL, 291, '2024-12-19', '2024-12-05', '2024-12-09', 'cwe'),

-- --------------------------------------------------------

--
-- Table structure for table `jwt_tokens`
--

CREATE TABLE `jwt_tokens` (
  `id` int(11) NOT NULL,
  `token` text NOT NULL,
  `iat` int(11) NOT NULL,
  `exp` int(11) NOT NULL,
  `type` enum('access','refresh') NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jwt_tokens`
--

INSERT INTO `jwt_tokens` (`id`, `token`, `iat`, `exp`, `type`, `used`, `created_at`) VALUES
(1515, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzM2NTkwNjQsImV4cCI6MTczMzY2MjY2NH0.loRUvQ4USuuMf2T_L_UY0iozvL20NN4w_UeYQky14YA', 0, 0, 'access', 1, '2024-12-08 19:57:44'),
(1516, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzM2NTkwNjQsImV4cCI6MTczMzY2MjY2NH0.loRUvQ4USuuMf2T_L_UY0iozvL20NN4w_UeYQky14YA', 0, 0, 'access', 1, '2024-12-08 19:57:44'),
(1670, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzM3MDQwNzIsImV4cCI6MTczMzcwNzY3Mn0.ezoZkudpzp5lDTKhhj8WTKfh7g-92SeWkL6o-8AwjVY', 0, 0, 'access', 1, '2024-12-09 08:27:52'),
(1671, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzM3MDQwNzIsImV4cCI6MTczMzcwNzY3Mn0.ezoZkudpzp5lDTKhhj8WTKfh7g-92SeWkL6o-8AwjVY', 0, 0, 'access', 1, '2024-12-09 08:27:52'),
(1672, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzM3MDQxMjUsImV4cCI6MTczMzcwNzcyNX0._vLg258ZProhYsFVnD_WJ1_0qFrbXeX8G2w96ptG5VY', 0, 0, 'access', 1, '2024-12-09 08:28:45'),
(1673, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzM3MDQxODMsImV4cCI6MTczMzcwNzc4M30.UW7MFXgNEXz32uIpzwpvx9RVoVC72FU9K1RrQiGOGEk', 0, 0, 'access', 1, '2024-12-09 08:29:43');
INSERT INTO `jwt_tokens` (`id`, `token`, `iat`, `exp`, `type`, `used`, `created_at`) VALUES
(1674, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzM3MDY0OTMsImV4cCI6MTczMzcxMDA5M30.FRGxC1zdyToU0CNo7VNoe7g9UCBYhKjOr6Oiu-rM5rE', 0, 0, 'access', 1, '2024-12-09 09:08:13'),
(1675, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzM3MDY0OTMsImV4cCI6MTczMzcxMDA5M30.FRGxC1zdyToU0CNo7VNoe7g9UCBYhKjOr6Oiu-rM5rE', 0, 0, 'access', 1, '2024-12-09 09:08:13'),
(1676, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzM3MDY0OTcsImV4cCI6MTczMzcxMDA5N30.DdwYk-Nx6JngWJHF_VnCxU2upE0v0vTz-L7WHcGrG4s', 0, 0, 'access', 1, '2024-12-09 09:08:17'),
(1677, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzM3MDY0OTcsImV4cCI6MTczMzcxMDA5N30.DdwYk-Nx6JngWJHF_VnCxU2upE0v0vTz-L7WHcGrG4s', 0, 0, 'access', 1, '2024-12-09 09:08:17'),
(1678, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzM3MDY1MDMsImV4cCI6MTczMzcxMDEwM30.9a4XHKJlM0YEvb-ypMLuSOtGbMGe906024xK8rTl3G8', 0, 0, 'access', 0, '2024-12-09 09:08:23'),
(1701, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzM3MDk5NTgsImV4cCI6MTczMzcxMzU1OH0.8UZIdWYwhkE_2x1aj7UuKTUGTidq6KkEcephhKO620E', 0, 0, 'access', 1, '2024-12-09 10:05:58'),

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userid` int(9) NOT NULL,
  `username` char(255) NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `username`, `password`) VALUES
(101, 'user101', '5c773b22ea79d367b38810e7e9ad108646ed62e231868cefb0b1280ea88ac4f0'),
(126, 'admin', '$2y$10$4rOL5Ldj4pMgObD5.bzQ/OISZPk.fGogHm.M8QQGK5YxAtzSKwpse'),
(127, 'dean', '$2y$10$PDv3Viay71Tr8JFOirC7.uMiqszdW.DAKsVLnW/qTlDnXK9heTKOC'),


--
-- Indexes for dumped tables
--

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`authorid`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`bookid`),
  ADD KEY `authorid` (`authorid`);

--
-- Indexes for table `books_authors`
--
ALTER TABLE `books_authors`
  ADD PRIMARY KEY (`collectionid`),
  ADD KEY `authorid` (`authorid`),
  ADD KEY `bookid` (`bookid`);

--
-- Indexes for table `borrowed_books`
--
ALTER TABLE `borrowed_books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `jwt_tokens`
--
ALTER TABLE `jwt_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `authorid` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `bookid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=306;

--
-- AUTO_INCREMENT for table `books_authors`
--
ALTER TABLE `books_authors`
  MODIFY `collectionid` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `borrowed_books`
--
ALTER TABLE `borrowed_books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `jwt_tokens`
--
ALTER TABLE `jwt_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1836;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`authorid`) REFERENCES `authors` (`authorid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `books_authors`
--
ALTER TABLE `books_authors`
  ADD CONSTRAINT `books_authors_ibfk_1` FOREIGN KEY (`authorid`) REFERENCES `authors` (`authorid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `books_authors_ibfk_2` FOREIGN KEY (`bookid`) REFERENCES `books` (`bookid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `borrowed_books`
--
ALTER TABLE `borrowed_books`
  ADD CONSTRAINT `borrowed_books_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`bookid`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Mar 14, 2021 at 07:24 PM
-- Server version: 5.7.29
-- PHP Version: 7.2.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `ninjalinks`
--
USE `ninjalinks`;

-- --------------------------------------------------------

--
-- Table structure for table `nl_banned`
--

CREATE TABLE `nl_banned` (
    `id` int(11) NOT NULL,
    `type` enum('ip','email') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ip',
    `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nl_categories`
--

CREATE TABLE `nl_categories` (
    `id` int(11) NOT NULL,
    `catname` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `catparent` int(11) NOT NULL DEFAULT '0'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nl_categories`
--

INSERT INTO `nl_categories` (`id`, `catname`, `catparent`) VALUES
    (1, 'Blog', 0),
    (2, 'Directory', 0),
    (3, 'Clique', 0),
    (4, 'Fansite', 0),
    (5, 'Forum', 0);

-- --------------------------------------------------------

--
-- Table structure for table `nl_links`
--

CREATE TABLE `nl_links` (
    `id` int(11) NOT NULL,
    `ownername` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
    `owneremail` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
    `linkname` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
    `linkurl` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
    `linkbutton` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
    `linkdesc` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `linktags` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `category` int(11) NOT NULL DEFAULT '0',
    `rating` tinyint(1) NOT NULL DEFAULT '0',
    `approved` tinyint(1) NOT NULL DEFAULT '0',
    `premium` tinyint(1) NOT NULL DEFAULT '0',
    `dateadded` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `dateupdated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `hits` int(11) NOT NULL DEFAULT '0'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nl_links`
--

INSERT INTO `nl_links` (`id`, `ownername`, `owneremail`, `linkname`, `linkurl`, `linkbutton`, `linkdesc`, `linktags`, `category`, `rating`, `approved`, `premium`, `dateadded`, `dateupdated`, `hits`) VALUES
    (1, 'test', 'tehiijgrjdfdo@jijfdgjdfkdfl.com', 'gfuhdidfgdj', 'http://djgdfjdd.com', '', 'sdgsdgghfh', 'hfguio', 1, 0, 1, 0, '2021-03-10 20:55:57', '2021-03-14 19:21:56', 0),
    (2, 'dfgdfgdgdf', 'gdfgdfgd@dkgkdf.com', 'dfsdds', 'http://fhjgffdffsdDSDFFDSkfg.com', '', 'gdfggdg', '', 1, 0, 0, 0, '2021-03-14 19:15:35', '2021-03-14 19:22:53', 0);

-- --------------------------------------------------------

--
-- Table structure for table `nl_updates`
--

CREATE TABLE `nl_updates` (
    `id` int(11) NOT NULL,
    `title` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `entry` text CHARACTER SET utf8,
    `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nl_updates`
--

INSERT INTO `nl_updates` (`id`, `title`, `entry`, `datetime`) VALUES
    (1, 'gdfgdf', 'fff', '2021-03-14 19:21:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `nl_banned`
--
ALTER TABLE `nl_banned`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nl_categories`
--
ALTER TABLE `nl_categories`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nl_links`
--
ALTER TABLE `nl_links`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nl_updates`
--
ALTER TABLE `nl_updates`
    ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `nl_banned`
--
ALTER TABLE `nl_banned`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nl_categories`
--
ALTER TABLE `nl_categories`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `nl_links`
--
ALTER TABLE `nl_links`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `nl_updates`
--
ALTER TABLE `nl_updates`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

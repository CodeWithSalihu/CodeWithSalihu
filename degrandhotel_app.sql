-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 06, 2026 at 04:44 PM
-- Server version: 11.4.10-MariaDB
-- PHP Version: 8.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `degrandhotel_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','manager','receptionist') DEFAULT 'receptionist',
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `full_name`, `email`, `phone`, `password`, `role`, `status`, `last_login`, `created_at`) VALUES
(1, 'Oga Boss', 'admin@degrandhotel.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', 'active', '2025-12-02 17:21:05', '2025-11-27 07:16:51'),
(2, 'Hotel Manager', 'manager@degrandhotel.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager', 'active', '2025-11-28 12:37:06', '2025-11-27 07:16:51'),
(4, 'degrandbayhotel rooftop', 'degrandbayhotel.rooftop@gmail.xom', NULL, '$2y$10$FUhTgRLWjnmJI8fdWmyHGu/7FCevTF88yuV/mNhAYpHn5EkTzfEyq', 'manager', 'active', '2025-11-28 21:10:10', '2025-11-28 19:09:15'),
(5, 'Salihu', 'salihubarup@gmail.com', NULL, '$2y$10$FeUkephJSnzQoWjAZDk.MeWKu89BtRmYSjP.nz4TqV6I/ZfkIDhY6', 'manager', 'active', '2026-05-06 16:33:23', '2025-12-02 15:18:49'),
(6, 'Obasanjo Omoleke', 'sojadesh@gmail.com', NULL, '$2y$10$YOxrRJC9b8GdRtL4YfW3WeaP0s2szszVn8S/y8ivPjiBVJmX3ASEi', 'manager', 'active', '2026-03-27 09:51:27', '2025-12-09 18:08:53');

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(100) NOT NULL DEFAULT 'Royal Editor',
  `category` enum('LIFESTYLE','EXCLUSIVE','CULINARY') NOT NULL,
  `status` enum('published','draft') DEFAULT 'draft',
  `content` longtext NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `title`, `author`, `category`, `status`, `content`, `image`, `created_at`, `updated_at`) VALUES
(1, 'A Night at The Royals: Where Lagos Meets True Luxury', 'Adesua Etomi-Wellington', 'LIFESTYLE', 'published', '<p>Elegance is not about being noticed &mdash; it&rsquo;s about being remembered.</p>\r\n<p>From the moment you step into the marble-floored lobby of The Royals Hotel, Victoria Island, you are no longer just a guest &mdash; you are royalty. The golden chandeliers dance with light, the scent of oud and fresh orchids fills the air, and the soft sound of live saxophone from the rooftop bar whispers: <em>&ldquo;Welcome home, Your Majesty.&rdquo;</em></p>\r\n<p>Our Presidential Suite isn&rsquo;t just a room &mdash; it&rsquo;s a palace within a palace. 400 square meters of pure opulence, private infinity pool overlooking the Atlantic, 24-karat gold bathroom fittings, and a personal butler who anticipates your needs before you even speak them.</p>\r\n<p>Tonight, Lagos sleeps. But at The Royals, the night is just beginning.</p>\r\n<p><strong>The Royals Hotel &mdash; Where Every Stay Feels Like Coronation.</strong></p>', 'blog_1764057546_9156.jpg', '2025-11-25 08:56:21', '2025-11-25 08:59:06'),
(2, 'Inside The Diamond Club: Lagos’ Most Exclusive Membership', 'Chief Olu Jacobs', 'EXCLUSIVE', 'published', '<p>There are hotels. Then there is The Royals &mdash; and then, far above everything else, there is <strong>The Diamond Club</strong>.</p>\r\n<p>Membership is by invitation only. Only 100 exist in the world. 27 are in Nigeria. And only 7 are currently available.</p>\r\n<p>As a Diamond Club member, you don&rsquo;t check in &mdash; you arrive. Your private elevator opens directly into your penthouse. Your name is engraved in gold on the door. Your favorite vintage champagne is already chilling.</p>\r\n<p>Last week, a Diamond Club member requested a private dinner with Burna Boy. It happened the same night.</p>\r\n<p>Because at The Royals, impossible is just a word for people who stay somewhere else.</p>', 'blog_1764057565_5764.jpg', '2025-11-25 08:56:21', '2025-11-25 08:59:25'),
(6, 'Experience Comfort and Elegance at De Grand Hotel & Rooftop', 'De Grand Editor', 'LIFESTYLE', 'published', '<p data-start=\"285\" data-end=\"500\">Located in the heart of Calabar, <strong data-start=\"333\" data-end=\"361\">De Grand Hotel &amp; Rooftop</strong> offers contemporary 3-star accommodations designed for modern comfort while celebrating the warmth of authentic Cross River hospitality.</p>\r\n<p data-start=\"502\" data-end=\"814\">Whether you&rsquo;re visiting for business, leisure, or a special occasion, our hotel provides a serene retreat with stylish rooms, top-notch amenities, and breathtaking rooftop views. Guests can enjoy a seamless blend of comfort and sophistication, coupled with personalized service that makes every stay memorable.</p>\r\n<p data-start=\"816\" data-end=\"841\"><strong data-start=\"816\" data-end=\"839\">Highlights include:</strong></p>\r\n<ul data-start=\"842\" data-end=\"1086\">\r\n<li data-start=\"842\" data-end=\"896\">\r\n<p data-start=\"844\" data-end=\"896\">Contemporary rooms equipped with modern facilities</p>\r\n</li>\r\n<li data-start=\"897\" data-end=\"941\">\r\n<p data-start=\"899\" data-end=\"941\">Rooftop lounge with panoramic city views</p>\r\n</li>\r\n<li data-start=\"942\" data-end=\"1016\">\r\n<p data-start=\"944\" data-end=\"1016\">Delicious culinary offerings featuring local and international cuisine</p>\r\n</li>\r\n<li data-start=\"1017\" data-end=\"1086\">\r\n<p data-start=\"1019\" data-end=\"1086\">Prime location close to cultural attractions and business centers</p>\r\n</li>\r\n</ul>\r\n<p data-start=\"1088\" data-end=\"1243\">Experience the perfect combination of luxury, comfort, and Cross River charm at <strong data-start=\"1168\" data-end=\"1196\">De Grand Hotel &amp; Rooftop</strong> &ndash; where every guest is treated like royalty.</p>', 'blog_1766168990_9219.jpg', '2025-12-19 18:29:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `booked_dates`
--

CREATE TABLE `booked_dates` (
  `id` int(11) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `stay_date` date NOT NULL,
  `booking_ref` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sort_order` int(11) DEFAULT 999
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `icon`, `created_at`, `sort_order`) VALUES
(2, 'VEGETERIAN DISH', 'cat_6942e67d82ca9_1765992061.jpg', '2025-11-28 08:00:27', 2),
(3, 'GREEN IS HEALTHY', 'cat_692967eb3c73f_1764321259.jpg', '2025-11-28 08:00:27', 3),
(4, 'Dessert', 'cat_6943a79245f4f_1766041490.jpg', '2025-11-28 08:00:27', 4),
(5, 'BEERS/SOFT DRINKS', 'cat_6929682259d75_1764321314.jpg', '2025-11-28 08:00:27', 5),
(6, 'Seafood', 'cat_6929683c7332c_1764321340.jpg', '2025-11-28 08:00:27', 6),
(7, 'Pasta', 'cat_6929684e7e8df_1764321358.jpg', '2025-11-28 08:00:27', 7),
(9, 'WINES', 'cat_6942e84f97e2d_1765992527.jpg', '2025-11-28 08:00:27', 8),
(10, 'AFRICAN DELIGHT COMBO', 'cat_6929688cb9756_1764321420.jpg', '2025-11-28 08:00:27', 9),
(11, 'MAIN CONTINENTAL', 'cat_6929689e4fc7b_1764321438.jpg', '2025-11-28 08:00:27', 10),
(12, 'Breakfast', 'cat_6943c6221ab60_1766049314.jpg', '2025-11-28 08:00:27', 11),
(13, 'STARTER', 'cat_6942e4ce154cc_1765991630.jpg', '2025-12-17 14:24:54', 12),
(14, 'JUICE', 'cat_6942e7a8211ef_1765992360.jpg', '2025-12-17 17:26:00', 13),
(15, 'SPECIAL WINES', 'cat_6942e94118095_1765992769.jpg', '2025-12-17 17:32:49', 14),
(16, 'CHAMPAGNES', 'cat_6942ea287e1f6_1765993000.jpg', '2025-12-17 17:36:40', 15),
(17, 'BRANDY', 'cat_6942ea7e4f0c0_1765993086.jpg', '2025-12-17 17:38:06', 16),
(18, 'WHISKY', 'cat_6942eadfa16c4_1765993183.jpg', '2025-12-17 17:39:43', 17),
(19, 'VODKA', 'cat_6942eb195aa3e_1765993241.jpg', '2025-12-17 17:40:41', 18),
(20, 'LIQUEUR', 'cat_6942eb8f91b5c_1765993359.jpg', '2025-12-17 17:42:39', 19),
(21, 'TEQUILA', 'cat_6942ebf1c4d86_1765993457.jpg', '2025-12-17 17:44:17', 20),
(22, 'ENERGY DRINK', 'cat_6942ecdb1b931_1765993691.jpg', '2025-12-17 17:48:11', 21),
(24, 'COOKTAIL', 'cat_6942edf4b834f_1765993972.jpg', '2025-12-17 17:52:52', 22),
(25, 'SPECIAL COCKTAILS', 'cat_6942ee4d11271_1765994061.jpg', '2025-12-17 17:54:21', 23),
(26, 'BURGER SANDWICH', 'cat_6943b0ec93ad2_1766043884.jpg', '2025-12-18 07:44:44', 24);

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `message_ref` varchar(16) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `subject` enum('booking','wedding','corporate','general','feedback','other') DEFAULT 'general',
  `message` text NOT NULL,
  `status` enum('new','read','replied','archived') DEFAULT 'new',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `message_ref`, `full_name`, `email`, `phone`, `subject`, `message`, `status`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 'ROYAL2025-7569', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'wedding', 'hello', 'new', '::1', NULL, '2025-11-17 19:53:22'),
(2, 'DEGRAND20251127-', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'booking', 'can i talk to someone', 'new', '::1', NULL, '2025-11-27 13:19:13'),
(3, 'DEGRAND20251205-', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'feedback', 'hello', 'new', '::1', NULL, '2025-12-05 17:30:35'),
(4, 'DEGRAND20251208-', 'Brianna Belton', 'briannawebsolution@gmail.com', '1201201200', 'feedback', 'Hi,\r\n\r\nHope you are doing well!\r\n\r\nWe are interested to increase organic traffic to your website, please get back to us in order to discuss the possibility in further detail.\r\n\r\nTo improve your business, we can help you with business optimization services like SEO, SMO, ORM, CRO, SMM, PPC, web design, and development to enhance your operation.\r\n\r\nIf you wish to take advantage of this chance.\r\n\r\nPlease respond with your phone number and country code, along with the services (listed above like SEO, SMO, SMM, PPC, web design ) you are interested in, I\'d be glad to go over our plan with you.\r\n\r\nHave a nice day :)\r\n\r\nRegards,\r\nBrianna Belton\r\n\r\n\r\n\r\nNote : Web platform expertise across Squarespace, Shopify, Wix, WordPress, GoDaddy etc.', 'new', '106.219.166.85', NULL, '2025-12-08 10:27:18'),
(5, 'DEGRAND20251209-', 'Broderick McLerie', 'broderick@avail.zone', '6173274449', 'booking', 'Hey,\r\n\r\nI came across degrandhotel.com. Exciting stuff!\r\n\r\nAs you grow, clients will ask: \"Who should I use for xyz service}?\"\r\n\r\nInstead of digging up contacts, you can launch your own partner directory. It\'s what big companies like Google and Shopify use to build their referral networks.\r\n\r\nWhat makes it powerful: It tracks every referral you send. This proves your value to partners and makes them much more likely to send referrals back to you.\r\n\r\nI set up a preview directory for you here:\r\nhttps://avail.zone/network/preview/degrandhotel.com\r\n\r\nSee what you think. Feel free to claim it — it\'s free.\r\n\r\nCheers,\r\nBroderick', 'new', '104.28.254.75', NULL, '2025-12-09 15:01:52'),
(6, 'DEGRAND20260106-', 'Jayrn Smith', 'ovens.alfie@outlook.com', '24888866', 'feedback', 'Hi, it’s Jayrn.\r\n\r\nIf your site already uses — or is preparing to use — affiliate links, this will be relevant.\r\n\r\nOne issue I see constantly is that monetization is treated as something you “add later,” instead of something that’s designed into the site from the beginning.\r\n\r\nThat usually leads to:\r\nrandom placement of links\r\nunclear visitor intent\r\nunpredictable income\r\n\r\nIt works, but never consistently.\r\n\r\nI put together a short explanation of why this happens and what changes once monetization is structured properly:\r\n\r\nhttps://marketersmentor.com/recurring-income-system.php?refer=degrandhotel.com\r\n\r\nYou’ll know quickly whether this applies to your situation.\r\n\r\nJayrn\r\n\r\n\r\n\r\nPS: And one quick note so you’re not wondering why you’re hearing from me:\r\nI only reach out to website owners because they’re the ones actively building something online. I’m not blasting random emails. \r\nI’m simply sharing a resource that has been helping a lot of people create predictable online income. If it resonates, great. If not, no worries.\r\n\r\n\r\n\r\n\r\nUnsubscribe: \r\nhttps://marketersmentor.com/unsubscribe.php?d=degrandhotel.com', 'read', '209.50.172.55', NULL, '2026-01-06 05:40:08'),
(7, 'DEGRAND20260109-', 'Mike Hugo Jones', 'mike@monkeydigital.co', '84785827534', 'general', 'Hello, \r\n \r\nI wanted to check in with something that could seriously improve your website’s reach. We work with a trusted ad network that allows us to deliver authentic, location-based social ads traffic for just $10 per 10,000 visits. \r\n \r\nThis isn\'t bot traffic—it’s engaged traffic, tailored to your preferred location and niche. \r\n \r\nWhat you get: \r\n \r\n10,000+ real visitors for just $10 \r\nLocalized traffic for your chosen location \r\nHigher volumes available based on your needs \r\nTrusted by SEO experts—we even use this for our SEO clients! \r\n \r\nInterested? Check out the details here: \r\nhttps://www.monkeydigital.co/product/country-targeted-traffic/ \r\n \r\nOr connect instantly on WhatsApp: \r\nhttps://monkeydigital.co/whatsapp-us/ \r\n \r\nLet\'s get started today! \r\n \r\nBest, \r\nMike Hugo Jones\r\n \r\nPhone/whatsapp: +1 (775) 314-7914', 'read', '158.173.156.41', NULL, '2026-01-09 12:00:10'),
(8, 'DEGRAND20260109-', 'Mike Pierre Fischer', 'info@digital-x-press.com', '82267794964', 'general', 'Hi, \r\nI understand that some companies have difficulties recognizing that SEO is a continuous effort and a strategically planned regular commitment. \r\n \r\nSadly, very few marketers have the willingness to wait for the incremental yet impactful benefits that can completely boost their search performance. \r\n \r\nWith regular search engine updates, a stable, continuous SEO strategy including Answer Engine Optimization (AEO) is essential for getting a strong return on investment. \r\n \r\nIf you recognize this as the ideal strategy, collaborate with us! \r\n \r\nDiscover Our Monthly SEO Services https://www.digital-x-press.com/unbeatable-seo/ \r\n \r\nTalk to Us on Instant Messaging https://www.digital-x-press.com/whatsapp-us/ \r\n \r\nWe offer remarkable performance for your budget, and you will enjoy choosing us as your digital marketing ally. \r\n \r\nKind regards, \r\nDigital X SEO Experts \r\nPhone/WhatsApp: +1 (844) 754-1148', 'read', '181.214.218.250', NULL, '2026-01-09 20:51:39'),
(9, 'DEGRAND20260213-', 'Mike Markus Smit', 'info@strictlydigital.net', '86464619583', 'general', 'Hi there, \r\n \r\nHaving some bunch of links redirecting to degrandhotel.com could have zero worth or worse for your business. \r\n \r\nIt really makes no difference the total external links you have, what matters is the total of ranking terms those domains are optimized for. \r\n \r\nThat is the key thing. \r\nNot the meaningless Domain Authority or Domain Rating. \r\nAnyone can manipulate those. \r\nBUT the amount of Google-ranked terms the domains that send backlinks to you rank for. \r\nThat’s it. \r\n \r\nMake sure these backlinks redirect to your site and you will ROCK! \r\n \r\nWe are providing this exclusive service here: \r\nhttps://www.strictlydigital.net/product/semrush-backlinks/ \r\n \r\nNeed more details, or need more information, message us here: \r\nhttps://www.strictlydigital.net/whatsapp-us/ \r\n \r\nBest regards, \r\nMike Markus Smit\r\n \r\nstrictlydigital.net \r\nPhone/WhatsApp: +1 (877) 566-3738', 'read', '78.138.99.185', NULL, '2026-02-13 20:39:55'),
(10, 'DEGRAND20260331-', '* * * $3,222 deposit available! Confirm your operation here: http://northfloridaprintingcoinc.com/?h', 'ydx~nwa9pwyxz@mailbox.in.ua', '313581715858', 'feedback', 'j8u63i', 'new', '37.114.63.5', NULL, '2026-03-31 05:19:27'),
(11, 'DEGRAND20260331-', '* * * <a href=\"http://northfloridaprintingcoinc.com/?hqccts\">$3,222 payment available</a> * * * hs=b', 'ydx~nwa9pwyxz@mailbox.in.ua', '798196384582', 'feedback', 'q3g15g', 'new', '37.114.63.5', NULL, '2026-03-31 05:19:29'),
(12, 'DEGRAND20260411-', 'Denise Morrell', 'morrell.denise@gmail.com', '4307760', 'booking', 'Hi,\r\n\r\nCame across your site and figured this was worth sending.\r\n\r\nThere’s a free tool that lets you get listed fast across multiple classified sites with one form.\r\n\r\nHere’s the URL:\r\nhttps://classifiedsubmitter.com\r\n\r\nIt’s totally free and takes under a minute.\r\n\r\nHappy to share more free exposure tools.', 'new', '195.210.112.4', NULL, '2026-04-11 20:57:27');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_system_flags`
--

CREATE TABLE `hotel_system_flags` (
  `id` int(11) NOT NULL,
  `force_full` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `hotel_system_flags`
--

INSERT INTO `hotel_system_flags` (`id`, `force_full`) VALUES
(1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `net_bookings`
--

CREATE TABLE `net_bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_ref` varchar(30) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `country` char(2) DEFAULT 'NG',
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `nights` smallint(4) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `adults` tinyint(2) NOT NULL DEFAULT 1,
  `children` tinyint(2) NOT NULL DEFAULT 0,
  `special_requests` text DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','checked_in','checked_out') DEFAULT 'confirmed',
  `payment_method` enum('online','transfer','pos','cash','wallet') DEFAULT 'online',
  `payment_proof` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `booked_by` enum('website','walk_in','phone','agent') DEFAULT 'website',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `net_bookings`
--

INSERT INTO `net_bookings` (`id`, `booking_ref`, `full_name`, `email`, `phone`, `country`, `check_in`, `check_out`, `nights`, `room_type`, `adults`, `children`, `special_requests`, `total_amount`, `status`, `payment_method`, `payment_proof`, `transaction_id`, `booked_by`, `created_at`, `updated_at`) VALUES
(87, 'DEGRAND1765039461429', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-06', '2025-12-07', 1, 'classic', 1, 0, NULL, 5000.00, 'checked_out', 'online', NULL, 'DEGRAND1765039461429', 'website', '2025-12-06 16:46:09', '2025-12-06 17:00:51'),
(88, 'DEGRAND1765301818326', 'Mrs Banigo', 'ibimtumini@gmail.com', '09076383866', 'NG', '2025-12-27', '2025-12-30', 3, 'classic', 1, 0, NULL, 210000.00, 'confirmed', 'online', NULL, 'DEGRAND1765301818326', 'website', '2025-12-09 17:53:37', '2025-12-09 17:53:37'),
(92, 'DEGRAND20251227153', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'penthouse-single', 2, 0, NULL, 800000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227153.jpg', NULL, '', '2025-12-27 08:42:31', '2025-12-27 08:42:31'),
(93, 'DEGRAND20251227852', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'penthouse-double', 2, 0, NULL, 800000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227852.jpg', NULL, '', '2025-12-27 08:45:52', '2025-12-27 08:45:52'),
(94, 'DEGRAND20251227766', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'executive-max', 2, 0, NULL, 420000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227766.jpg', NULL, '', '2025-12-27 08:47:09', '2025-12-27 08:47:09'),
(95, 'DEGRAND20251227611', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'executive-max', 2, 0, NULL, 420000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227611.jpg', NULL, '', '2025-12-27 08:48:00', '2025-12-27 08:48:00'),
(96, 'DEGRAND20251227386', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'executive-max', 2, 0, NULL, 420000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227386.jpg', NULL, '', '2025-12-27 08:49:18', '2025-12-27 08:49:18'),
(97, 'DEGRAND20251227924', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'executive-max', 2, 0, NULL, 420000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227924.jpg', NULL, '', '2025-12-27 08:50:33', '2025-12-27 08:50:33'),
(98, 'DEGRAND20251227411', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'executive-mini', 2, 0, NULL, 380000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227411.jpg', NULL, '', '2025-12-27 08:55:32', '2025-12-27 08:55:32'),
(99, 'DEGRAND20251227298', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'executive-mini', 2, 0, NULL, 380000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227298.jpg', NULL, '', '2025-12-27 08:55:54', '2025-12-27 08:55:54'),
(100, 'DEGRAND20251227923', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'executive-mini', 2, 0, NULL, 380000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227923.jpg', NULL, '', '2025-12-27 08:56:19', '2025-12-27 08:56:19'),
(101, 'DEGRAND20251227222', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'executive-mini', 2, 0, NULL, 380000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227222.jpg', NULL, '', '2025-12-27 08:56:41', '2025-12-27 08:56:41'),
(102, 'DEGRAND20251227367', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'deluxe', 2, 0, NULL, 320000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227367.jpg', NULL, '', '2025-12-27 08:57:04', '2025-12-27 08:57:04'),
(103, 'DEGRAND20251227714', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'deluxe', 2, 0, NULL, 320000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227714.jpg', NULL, '', '2025-12-27 08:57:29', '2025-12-27 08:57:29'),
(104, 'DEGRAND20251227124', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'deluxe', 2, 0, NULL, 320000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227124.jpg', NULL, '', '2025-12-27 08:58:24', '2025-12-27 08:58:24'),
(105, 'DEGRAND20251227893', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'deluxe', 2, 0, NULL, 320000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227893.jpg', NULL, '', '2025-12-27 08:58:49', '2025-12-27 08:58:49'),
(106, 'DEGRAND20251227702', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-28', 1, 'deluxe', 2, 0, NULL, 80000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227702.jpg', NULL, '', '2025-12-27 08:59:13', '2025-12-27 08:59:13'),
(108, 'DEGRAND20251227746', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'deluxe', 2, 0, NULL, 320000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227746.jpg', NULL, '', '2025-12-27 09:00:11', '2025-12-27 09:00:11'),
(109, 'DEGRAND20251227340', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'deluxe', 2, 0, NULL, 320000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227340.jpg', NULL, '', '2025-12-27 09:00:36', '2025-12-27 09:00:36'),
(110, 'DEGRAND20251227196', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'classic', 2, 0, NULL, 280000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227196.jpg', NULL, '', '2025-12-27 09:01:03', '2025-12-27 09:01:03'),
(111, 'DEGRAND20251227507', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'classic', 2, 0, NULL, 280000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227507.jpg', NULL, '', '2025-12-27 09:01:28', '2025-12-27 09:01:28'),
(112, 'DEGRAND20251227731', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'classic', 2, 0, NULL, 280000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227731.jpg', NULL, '', '2025-12-27 09:01:49', '2025-12-27 09:01:49'),
(113, 'DEGRAND20251227277', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'classic', 2, 0, NULL, 280000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227277.jpg', NULL, '', '2025-12-27 09:02:08', '2025-12-27 09:02:08'),
(114, 'DEGRAND20251227378', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'classic', 2, 0, NULL, 280000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227378.jpg', NULL, '', '2025-12-27 09:02:31', '2025-12-27 09:02:31'),
(115, 'DEGRAND20251227719', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'classic', 2, 0, NULL, 280000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227719.jpg', NULL, '', '2025-12-27 09:02:54', '2025-12-27 09:02:54'),
(116, 'DEGRAND20251227703', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'classic', 2, 0, NULL, 280000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227703.jpg', NULL, '', '2025-12-27 09:03:19', '2025-12-27 09:03:19'),
(117, 'DEGRAND20251227964', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'classic', 2, 0, NULL, 280000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227964.jpg', NULL, '', '2025-12-27 09:03:41', '2025-12-27 09:03:41'),
(118, 'DEGRAND20251227935', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-27', '2025-12-31', 4, 'classic', 2, 0, NULL, 280000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251227935.jpg', NULL, '', '2025-12-27 09:04:03', '2025-12-27 09:04:03'),
(119, 'DEGRAND20251229602', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-29', '2025-12-31', 2, 'deluxe', 2, 0, NULL, 160000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251229602.jpg', NULL, '', '2025-12-29 06:35:34', '2025-12-29 06:35:34'),
(120, 'DEGRAND20251230122', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-12-30', '2025-12-31', 1, 'classic', 2, 0, NULL, 70000.00, 'confirmed', 'cash', 'proofs/DEGRAND20251230122.jpg', NULL, '', '2025-12-30 07:41:44', '2025-12-30 07:41:44'),
(121, 'DEGRAND1768152133839', 'Philip Paul Odibu', 'odibuphilip69@gmail.com', '08136008564', 'NG', '2026-01-12', '2026-01-13', 1, 'deluxe', 1, 0, NULL, 82902.00, 'confirmed', 'online', NULL, 'DEGRAND1768152133839', 'website', '2026-01-11 17:22:37', '2026-01-11 17:22:37'),
(122, 'DEGRAND1770410535134', 'Echem Emmanuel', 'emmanuelechem21@gmail.com', '08144309175', 'NG', '2026-02-07', '2026-02-08', 1, 'deluxe', 2, 0, NULL, 82902.00, 'confirmed', 'online', NULL, 'DEGRAND1770410535134', 'website', '2026-02-06 20:46:14', '2026-02-06 20:46:14'),
(123, 'DEGRAND1770918271986', 'Samuel Francis', 'samuelfrancis41@yahoo.co.uk', '13104886740', 'NG', '2026-02-15', '2026-02-17', 2, 'executive-mini', 1, 1, NULL, 196892.00, 'confirmed', 'online', NULL, 'DEGRAND1770918271986', 'website', '2026-02-12 17:46:17', '2026-02-12 17:46:17'),
(124, 'DEGRAND1771083205393', 'Bassey, Ojong Ogar', 'ojbassey@yahoo.com', '08069099033', 'NG', '2026-02-16', '2026-02-17', 1, 'executive-mini', 1, 0, NULL, 98446.00, 'confirmed', 'online', NULL, 'DEGRAND1771083205393', 'website', '2026-02-14 15:37:50', '2026-02-14 15:37:50'),
(126, 'DEGRAND20260218537', 'salihu', 'salihubarup@gmail.com', '08066610571', 'NG', '2026-02-18', '2026-02-19', 1, 'classic', 2, 0, NULL, 70000.00, 'checked_out', 'cash', 'proofs/DEGRAND20260218537.jpg', NULL, '', '2026-02-18 13:16:36', '2026-02-18 13:19:28'),
(133, 'DEGRAND1772617894862', 'Victor Nwadinogbu', 'cjokcy@yahoo.com', '09080440000', 'NG', '2026-03-27', '2026-03-28', 1, 'deluxe', 1, 0, NULL, 82902.00, 'confirmed', 'online', NULL, 'DEGRAND1772617894862', 'website', '2026-03-04 09:54:49', '2026-03-04 09:54:49');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','preparing','ready','delivered','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT 'cash',
  `table_number` varchar(10) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(15) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `payment_method`, `table_number`, `customer_name`, `customer_phone`, `notes`, `created_at`, `updated_at`) VALUES
(13, 0, 2600.00, 'pending', 'cash', '3b/5 water', 'Salihu Hassan', '08066610571', '', '2025-12-09 09:51:59', '2025-12-09 09:51:59'),
(15, 0, 2600.00, 'pending', 'cash', 'Room 3', 'Salihu Barup', '+2348066610571', 'No Pepe', '2025-12-15 17:48:53', '2025-12-15 17:48:53');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(200) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `quantity`, `price`, `created_at`) VALUES
(3, 3, NULL, 'Cheese Burger Deluxe', 1, 10000.00, '2025-12-03 10:46:47'),
(4, 4, 9, 'Chicken Crispy', 1, 2600.00, '2025-12-03 10:55:53'),
(5, 5, 7, 'Cheese Burger Deluxe', 1, 10000.00, '2025-12-03 10:58:49'),
(13, 13, 9, 'Chicken Crispy', 1, 2600.00, '2025-12-09 09:51:59'),
(15, 15, 9, 'Chicken Crispy', 1, 2600.00, '2025-12-15 17:48:53');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'General',
  `rating` decimal(2,1) DEFAULT 4.5,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`, `description`, `category`, `rating`, `created_at`) VALUES
(1, 'Jollof Rice & Chicken', 2500.00, 'prod_692940d27e65d_1764311250.jpg', 'Authentic Nigerian Jollof with fried chicken', 'Rice', 4.5, '2025-11-19 18:52:41'),
(2, 'Egusi Soup + Pounded Yam', 3800.00, 'prod_692940bbb3929_1764311227.jpg', 'Rich egusi with ugu and assorted meat', 'Soup', 4.5, '2025-11-19 18:52:41'),
(5, 'CONTINENTAL BREAKFAST', 5500.00, 'prod_6943c5f878e10_1766049272.jpg', 'Fruit juices Coffee or Tea§\r\n Bread basket (Pancake, bread toast and bread roll)\r\n Fruits plate co', 'Breakfast', 4.5, '2025-11-19 18:52:41'),
(7, 'Cheese Burger Deluxe', 10000.00, 'prod_6929410c5bca5_1764311308.jpg', 'Double beef patty with melted cheddar, caramelized onions, pickles & house sauce', 'Burger', 4.9, '2025-11-19 19:21:06'),
(9, 'Chicken Crispy', 2600.00, 'prod_692940eec7d4b_1764311278.jpg', 'Crispy fried chicken fillet, lettuce, mayo & soft bun – Lagos street style', 'Burger', 4.7, '2025-11-19 19:21:06'),
(10, 'English Breakfast', 7500.00, 'prod_6942e1ba64252_1765990842.jpg', 'Bread roll or Bread Toast - Choice of Egg Style (Vegetable omelet, English scrambled egg, Sunny side up, Fried egg, Plain omelet, boiled egg) Fresh fruit juice Coffee or Tea', 'Breakfast', 4.5, '2025-12-17 14:29:25'),
(14, 'Mineral', 1000.00, 'prod_6942f2d0785db_1765995216.jpg', 'Cool Mineral', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 18:13:36'),
(15, 'Malta Guiness', 2000.00, 'prod_6942fde6d4294_1765998054.jpg', 'Cool Malta Guiness', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 19:00:54'),
(16, 'Fayrouz', 2000.00, 'prod_69430a48e4c3c_1766001224.jpg', 'Cool Fayrouz', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 19:53:44'),
(18, 'Amstel Malta', 1500.00, 'prod_694310ebd464e_1766002923.jpg', 'Cool Amstel Malta', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 20:22:03'),
(19, 'Beta malt', 1500.00, 'prod_69431260f2f73_1766003296.jpg', 'Cool Beta malt', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 20:28:16'),
(20, 'Star beers', 2500.00, 'prod_694312d21aacf_1766003410.jpg', 'cool Star beer', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 20:30:10'),
(21, 'Smirnoff Ice', 2000.00, 'prod_694313290a6ed_1766003497.jpg', 'Cool Smirnoff Ice', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 20:31:37'),
(22, 'Heinekens', 2500.00, 'prod_694313d7b4db2_1766003671.jpg', 'Cool Heinekens', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 20:34:31'),
(23, 'Extra smooth (18)', 2500.00, 'prod_69431479989e1_1766003833.jpg', 'Cool Extra smooth (18)', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 20:37:13'),
(24, 'Small stout', 2500.00, 'prod_6943150646727_1766003974.jpg', 'Cool Small stout', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 20:39:34'),
(25, 'Budweisser', 2500.00, 'prod_694315d56daba_1766004181.jpg', 'Cool Budweisser', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 20:43:01'),
(26, 'Hero', 2500.00, 'prod_6943162e27255_1766004270.jpg', 'Cool Hero', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 20:44:30'),
(27, 'Star radler (20)', 2500.00, 'prod_69431660e000f_1766004320.jpg', 'Cool Star radler (20)', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 20:45:20'),
(28, 'Tiger', 2500.00, 'prod_694316c892a36_1766004424.jpg', 'Cool Tiger', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 20:47:04'),
(29, 'Trophy', 2500.00, 'prod_6943170577e0e_1766004485.jpg', 'Cool Trophy', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 20:48:05'),
(30, 'Life', 2500.00, 'prod_6943175ba4767_1766004571.jpg', 'Cool Life', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 20:49:31'),
(31, 'Gulder', 2500.00, 'prod_6943186b4e3e5_1766004843.jpg', 'Cool Gulder', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 20:54:03'),
(32, 'Trophy Stout', 2500.00, 'prod_694319057df2f_1766004997.webp', 'Col Trophy Stout', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 20:56:37'),
(33, 'Eagle Stout', 2500.00, 'prod_694319b912049_1766005177.jpg', 'Cool Eagle Stout', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 20:59:37'),
(34, 'Desperado', 3000.00, 'prod_694319f36bfce_1766005235.jpg', 'Cool Desperado', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 21:00:35'),
(35, 'Legend', 2500.00, 'prod_69431a4805f92_1766005320.jpg', 'cool Legend', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 21:02:00'),
(36, 'Origin', 2500.00, 'prod_69431a8f54c48_1766005391.jpg', 'Cool Origin', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 21:03:11'),
(37, 'Medium Stout', 2500.00, 'prod_69431ae9afaae_1766005481.jpg', 'Cool Medium Stout', 'BEERS/SOFT DRINKS', 4.5, '2025-12-17 21:04:41'),
(38, 'Vitalmilk', 5000.00, 'prod_69431b545ddfd_1766005588.jpg', 'chill Vitalmilk', 'JUICE', 4.5, '2025-12-17 21:06:28'),
(39, 'Chiexotic', 5000.00, 'prod_69431bb4bcb79_1766005684.jpg', 'chill Chiexotic', 'JUICE', 4.5, '2025-12-17 21:08:04'),
(40, 'Chivita', 5000.00, 'prod_69431bebd69fc_1766005739.jpg', 'chill Chivita', 'JUICE', 4.5, '2025-12-17 21:08:59'),
(41, 'Cranberry', 10000.00, 'prod_69431c88ab84e_1766005896.jpg', 'chil Cranberry', 'JUICE', 4.5, '2025-12-17 21:11:36'),
(42, 'Holladia yogurt', 6000.00, 'prod_69431cf7516b1_1766006007.jpg', 'chill Holladia yogurt', 'JUICE', 4.5, '2025-12-17 21:13:27'),
(43, 'Peak yoghurt', 6000.00, 'prod_69431d5e6e721_1766006110.jpg', 'Chill Peak yoghurt', 'JUICE', 4.5, '2025-12-17 21:15:10'),
(44, 'Carlos Rossi', 30000.00, 'prod_69431e02eae91_1766006274.jpg', 'chill Carlos Rossi', 'WINES', 4.5, '2025-12-17 21:17:54'),
(45, 'Domino', 30000.00, 'prod_69431e724d44a_1766006386.jpg', 'chiil Domino', 'WINES', 4.5, '2025-12-17 21:19:46'),
(46, 'Asconi Agor', 30000.00, 'prod_69431ec64c803_1766006470.jpg', 'Chill Asconi Agor', 'WINES', 4.5, '2025-12-17 21:21:10'),
(47, 'B & G', 30000.00, 'prod_69431f03f0bf0_1766006531.jpg', 'Chill B & G', 'WINES', 4.5, '2025-12-17 21:22:11'),
(48, 'Chamdor', 30000.00, 'prod_69431f3792815_1766006583.jpg', 'Chill Chamdor', 'WINES', 4.5, '2025-12-17 21:23:03'),
(49, 'Andre Rose', 350000.00, 'prod_69431f994f530_1766006681.jpg', 'Chill Andre Rose', 'WINES', 4.5, '2025-12-17 21:24:41'),
(50, 'Four Cousins', 30000.00, 'prod_69431fd3828d8_1766006739.jpg', 'Chill Four Cousins', 'WINES', 4.5, '2025-12-17 21:25:39'),
(51, 'Challenge', 30000.00, 'prod_694320127bf2c_1766006802.jpg', 'Chill Challenge', 'WINES', 4.5, '2025-12-17 21:26:42'),
(52, 'Torley', 65000.00, 'prod_6943205fe6f14_1766006879.jpg', 'Chill Torley', 'WINES', 4.5, '2025-12-17 21:27:59'),
(53, 'Blue Nun - Pink', 35000.00, 'prod_694320a1ebbe3_1766006945.jpg', 'Chill Blue Nun - Pink', 'WINES', 4.5, '2025-12-17 21:29:05'),
(54, 'Castello Del Poggio', 30000.00, 'prod_69432106ede02_1766007046.jpg', 'chill Castello Del Poggio', 'SPECIAL WINES', 4.5, '2025-12-17 21:30:46'),
(55, 'Wave Dancer', 25000.00, 'prod_6943216bb0e8e_1766007147.jpg', 'chill Wave Dancer', 'SPECIAL WINES', 4.5, '2025-12-17 21:32:27'),
(56, 'Bella Bellina', 40000.00, 'prod_694321c714e84_1766007239.jpg', 'Chill Bella Bellina', 'SPECIAL WINES', 4.5, '2025-12-17 21:33:59'),
(57, 'Terra Magna Sweet wine', 40000.00, 'prod_69432232ec82b_1766007346.jpg', 'Chill', 'SPECIAL WINES', 4.5, '2025-12-17 21:35:46'),
(58, 'Saxeburg Cabernet Sauvignon', 60000.00, 'prod_6943228f52b22_1766007439.jpg', 'Chill Saxeburg Cabernet Sauvignon', 'SPECIAL WINES', 4.5, '2025-12-17 21:37:19'),
(59, 'Cronier Charming Chadonnay', 35000.00, 'prod_694323570a42c_1766007639.jpg', 'Chill Cronier Charming Chadonnay', 'SPECIAL WINES', 4.5, '2025-12-17 21:40:39'),
(60, 'San Antonio', 35000.00, 'prod_694323af35ec3_1766007727.jpg', 'Chill San Antonio', 'SPECIAL WINES', 4.5, '2025-12-17 21:42:07'),
(61, 'JB Merlot', 35000.00, 'prod_69432436ad44f_1766007862.jpg', 'Chill', 'SPECIAL WINES', 4.5, '2025-12-17 21:44:22'),
(62, 'Ostoros Egri Rose', 40000.00, 'prod_694327f6c7f68_1766008822.jpg', 'chill Ostoros Egri Rose', 'SPECIAL WINES', 4.5, '2025-12-17 21:46:13'),
(63, 'Dornfelder Red', 3500.00, 'prod_694324ed225b0_1766008045.jpg', 'Chil Dornfelder Red', 'SPECIAL WINES', 4.5, '2025-12-17 21:47:25'),
(64, 'Copper and thief', 85000.00, 'prod_69432553e0601_1766008147.jpg', 'Chill Copper and thief', 'SPECIAL WINES', 4.5, '2025-12-17 21:49:07'),
(65, 'Casa Solis', 45000.00, 'prod_69432739d7c0f_1766008633.jpg', 'Chill Casa Solis', 'SPECIAL WINES', 4.5, '2025-12-17 21:50:43'),
(66, 'Gerard beetrand cote', 25000.00, 'prod_694325fbe7f4f_1766008315.jpg', 'Chill  Gerard beetrand cote', 'SPECIAL WINES', 4.5, '2025-12-17 21:51:55'),
(67, 'Straw Hat', 40000.00, 'prod_694326407d420_1766008384.jpg', 'Chill Straw Hat', 'SPECIAL WINES', 4.5, '2025-12-17 21:53:04'),
(68, 'Straw Hat', 40000.00, 'prod_6943264122d3f_1766008385.jpg', 'Chill Straw Hat', 'SPECIAL WINES', 4.5, '2025-12-17 21:53:05'),
(69, 'Nederburg', 75000.00, 'prod_694326913a21f_1766008465.jpg', 'Chill Nederburg', 'SPECIAL WINES', 4.5, '2025-12-17 21:54:25'),
(70, 'Moet Rose', 270000.00, 'prod_6943287f518b6_1766008959.jpg', 'Chill Moet Rose', 'CHAMPAGNES', 4.5, '2025-12-17 22:02:39'),
(71, 'Moet Nectar', 180000.00, 'prod_6943290e16c83_1766009102.jpg', 'Chill Moet Nectar', 'CHAMPAGNES', 4.5, '2025-12-17 22:05:02'),
(72, 'Moet Brut', 250000.00, 'prod_69432a876aa12_1766009479.jpg', 'Chill Moet Brut', 'CHAMPAGNES', 4.5, '2025-12-17 22:11:19'),
(73, 'Dom Perignon', 1200000.00, 'prod_69432afd630ec_1766009597.jpg', 'Chill Dom Perignon', 'CHAMPAGNES', 4.5, '2025-12-17 22:13:17'),
(74, 'Ace of Spade', 1300000.00, 'prod_69432ba996afc_1766009769.jpg', 'Chill Ace of Spade', 'CHAMPAGNES', 4.5, '2025-12-17 22:16:09'),
(75, 'Blue Nun Rose', 90000.00, 'prod_69432ca2a87a4_1766010018.jpg', 'Chill Blue Nun Rose', 'CHAMPAGNES', 4.5, '2025-12-17 22:20:18'),
(76, 'Blue Nun Gold', 90000.00, 'prod_69432d7000b0e_1766010224.jpg', 'chill Blue Nun Gold', 'CHAMPAGNES', 4.5, '2025-12-17 22:23:44'),
(77, 'Blue Nun - AUTHENTIC', 70000.00, 'prod_69432df30a540_1766010355.jpg', 'Chill Blue Nun - AUTHENTIC', 'CHAMPAGNES', 4.5, '2025-12-17 22:25:55'),
(78, 'Belaire Rose', 170000.00, 'prod_69432e4268564_1766010434.jpg', 'Chill Belaire Rose', 'CHAMPAGNES', 4.5, '2025-12-17 22:27:14'),
(79, 'Angelus', 175000.00, 'prod_69432ee32350b_1766010595.jpg', 'Chill', 'CHAMPAGNES', 4.5, '2025-12-17 22:29:55'),
(80, 'Martel Vs Cognac Single', 140000.00, 'prod_69432fbb2fef6_1766010811.jpg', 'Chill Martel Vs Cognac Single', 'BRANDY', 4.5, '2025-12-17 22:33:31'),
(81, 'Martel VSOP', 200000.00, 'prod_6943301874bf9_1766010904.jpg', 'Chill Martel VSOP', 'BRANDY', 4.5, '2025-12-17 22:35:04'),
(82, 'Martel Blue Swift', 240000.00, 'prod_694330973867a_1766011031.jpg', 'Chill Martel Blue Swift', 'BRANDY', 4.5, '2025-12-17 22:37:11'),
(83, 'Martel XO', 840000.00, 'prod_694330e5df97e_1766011109.jpg', 'chill Martel XO', 'BRANDY', 4.5, '2025-12-17 22:38:29'),
(84, 'Hennessy VS', 170000.00, 'prod_69433147b39cd_1766011207.jpg', 'Chill Hennessy VS', 'BRANDY', 4.5, '2025-12-17 22:40:07'),
(85, 'Hennessy VSOP', 270000.00, 'prod_6943318b4239e_1766011275.jpg', 'Chill Hennessy VSOP', 'BRANDY', 4.5, '2025-12-17 22:41:15'),
(86, 'Hennessy XO', 850000.00, 'prod_694331fce9aa0_1766011388.jpg', 'Chill Hennessy XO', 'BRANDY', 4.5, '2025-12-17 22:43:08'),
(87, 'Remy Martins VS', 140000.00, 'prod_6943324bef03d_1766011467.jpg', 'Chill Remy Martins VS', 'BRANDY', 4.5, '2025-12-17 22:44:27'),
(88, 'Remy Martins VSOP', 220000.00, 'prod_694333265af41_1766011686.jpg', 'Chil Remy Martins VSOP', 'BRANDY', 4.5, '2025-12-17 22:47:11'),
(89, 'Remy Martins XO', 830000.00, 'prod_69433376f10da_1766011766.jpg', 'Chill Remy Martins XO', 'BRANDY', 4.5, '2025-12-17 22:49:26'),
(90, 'Jack Danie', 100000.00, 'prod_694333e28e151_1766011874.jpg', 'Chill Jack Danie', 'WHISKY', 4.5, '2025-12-17 22:51:14'),
(91, 'Glenfiddich 21yrs', 850000.00, 'prod_694334759766c_1766012021.jpg', 'Chill Glenfiddich 21yrs', 'WHISKY', 4.5, '2025-12-17 22:53:41'),
(92, 'Glenfiddich 18yrs', 270000.00, 'prod_694334d7000d2_1766012119.jpg', 'Chill', 'WHISKY', 4.5, '2025-12-17 22:55:19'),
(93, 'Glenfiddich 15yrs', 140000.00, 'prod_69433521d728f_1766012193.jpg', 'Chill', 'WHISKY', 4.5, '2025-12-17 22:56:33'),
(94, 'Jameson Green', 70000.00, 'prod_69433630ce104_1766012464.jpg', 'Chill', 'WHISKY', 4.5, '2025-12-17 23:01:04'),
(95, 'Blue label', 650000.00, 'prod_6943370a81b80_1766012682.jpg', 'Chill Blue label', 'WHISKY', 4.5, '2025-12-17 23:04:42'),
(96, 'Jameson black', 110000.00, 'prod_694337b325914_1766012851.jpg', 'Chill', 'WHISKY', 4.5, '2025-12-17 23:07:31'),
(97, 'William Lawson', 70000.00, 'prod_6943380681bd9_1766012934.jpg', 'Chill', 'WHISKY', 4.5, '2025-12-17 23:08:54'),
(98, 'Gold label', 140000.00, 'prod_6943384e83873_1766013006.jpg', 'Chill', 'WHISKY', 4.5, '2025-12-17 23:10:06'),
(99, 'Monkey Shoulder', 100000.00, 'prod_6943389d8c618_1766013085.jpg', 'Chill Monkey Shoulder', 'WHISKY', 4.5, '2025-12-17 23:11:25'),
(100, 'Singleton-12yrs', 125000.00, 'prod_6943390187840_1766013185.jpg', 'Chill Singleton-12yrs', 'WHISKY', 4.5, '2025-12-17 23:13:05'),
(101, 'Black Label', 75000.00, 'prod_6943394dc7df9_1766013261.jpg', 'Chill Black Label', 'WHISKY', 4.5, '2025-12-17 23:14:21'),
(102, 'Odogwu Bitters', 4000.00, 'prod_694339a0c6cb7_1766013344.jpg', 'Chil Odogwu Bitters', 'WHISKY', 4.5, '2025-12-17 23:15:44'),
(103, 'Ciroc', 110000.00, 'prod_694339ee1737e_1766013422.jpg', 'Chill Ciroc', 'VODKA', 4.5, '2025-12-17 23:17:02'),
(104, 'Smirnoff vodka X1', 60000.00, 'prod_69433a4f6c5b7_1766013519.jpg', 'Chill Smirnoff vodka X1', 'VODKA', 4.5, '2025-12-17 23:18:39'),
(105, 'Bailey\'s', 60000.00, 'prod_69433ab92ec5d_1766013625.jpg', 'Chill Bailey\'s', 'LIQUEUR', 4.5, '2025-12-17 23:20:25'),
(106, 'Alize', 50000.00, 'prod_69433af806ebb_1766013688.jpg', 'Chill Alize', 'LIQUEUR', 4.5, '2025-12-17 23:21:28'),
(107, 'Campari -1ltr', 60000.00, 'prod_69433b6606fd6_1766013798.jpg', 'Chill Campari -1ltr', 'LIQUEUR', 4.5, '2025-12-17 23:23:18'),
(108, 'Don Julio (1942)', 850000.00, 'prod_69433bca1c2d1_1766013898.jpg', 'Chill Don Julio (1942)', 'TEQUILA', 4.5, '2025-12-17 23:24:58'),
(109, 'Olmeca', 70000.00, 'prod_69433c41dead2_1766014017.jpg', 'Chill Olmeca', 'TEQUILA', 4.5, '2025-12-17 23:26:57'),
(110, 'Volcan', 50000.00, 'prod_69433cacc4754_1766014124.jpg', 'Chill Volcan', 'TEQUILA', 4.5, '2025-12-17 23:28:44'),
(111, 'Olmeca - Chocolate', 60000.00, 'prod_69433d1c72041_1766014236.jpg', 'Chill Olmeca - Chocolate', 'TEQUILA', 4.5, '2025-12-17 23:30:36'),
(112, 'Blue Azul', 800000.00, 'prod_69433d90b6dd9_1766014352.jpg', 'Chil Blue Azul', 'TEQUILA', 4.5, '2025-12-17 23:32:05'),
(113, 'Buen Amigo', 60000.00, 'prod_69433debb596f_1766014443.jpg', 'Chill Buen Amigo', 'TEQUILA', 4.5, '2025-12-17 23:34:03'),
(114, 'blue bullet', 5000.00, 'prod_694340054d2ba_1766014981.jpg', 'Chill blue bullet drink', 'ENERGY DRINK', 4.5, '2025-12-17 23:43:01'),
(115, 'Black Bullet', 5500.00, 'prod_6943405b37b3d_1766015067.jpg', 'Chill Black Bullet', 'ENERGY DRINK', 4.5, '2025-12-17 23:44:27'),
(116, 'Monster', 5000.00, 'prod_6943408fde297_1766015119.jpg', 'Chill Monster', 'ENERGY DRINK', 4.5, '2025-12-17 23:45:19'),
(117, 'Climax', 5000.00, 'prod_6943411f1b073_1766015263.jpg', 'Chill Climax', 'ENERGY DRINK', 4.5, '2025-12-17 23:46:19'),
(118, 'Fruit platter', 6500.00, 'prod_6943c2e9d1ba0_1766048489.jpg', 'Papaya, watermelon and pineapple', 'Dessert', 4.5, '2025-12-18 06:58:06'),
(119, 'Browny on the rock', 6500.00, 'prod_6943c2576c5fc_1766048343.jpg', 'Vanilla Sponge cake, beetroot puree, chocolate ice cream', 'Dessert', 4.5, '2025-12-18 07:00:21'),
(120, 'chocolate ice cream', 6500.00, 'prod_6943a738793b5_1766041400.jpg', 'Browny on the rock', 'Dessert', 4.5, '2025-12-18 07:03:20'),
(121, 'Meaty minced meat', 9500.00, 'prod_6943aa245d21a_1766042148.jpg', 'I got meat with you', 'BURGERLICIOUS', 4.5, '2025-12-18 07:15:48'),
(122, 'sticky bacon', 9500.00, 'prod_6943aabe963ba_1766042302.jpg', 'fresh made', 'BURGERLICIOUS', 4.5, '2025-12-18 07:18:22'),
(123, 'cheddar cheese', 9500.00, 'prod_6943ab7c0a211_1766042492.jpg', 'fresh made', 'BURGERLICIOUS', 4.5, '2025-12-18 07:21:32'),
(124, 'gherkins', 9500.00, 'prod_6943ac7f5db22_1766042751.jpg', 'freshly made', 'BURGERLICIOUS', 4.5, '2025-12-18 07:25:51'),
(125, 'lettuce', 9500.00, 'prod_6943ace018ef9_1766042848.jpg', 'it\'s fresh', 'BURGERLICIOUS', 4.5, '2025-12-18 07:27:28'),
(126, 'tomato', 9500.00, 'prod_6943ae2ccc1d1_1766043180.jpg', 'sweet onion and signature dressing', 'BURGERLICIOUS', 4.5, '2025-12-18 07:33:00'),
(127, 'sticky bacon', 9500.00, 'prod_6943b16822372_1766044008.jpg', 'fresh made', 'BURGER SANDWICH', 4.5, '2025-12-18 07:46:48'),
(128, 'Meaty minced meat', 9500.00, 'prod_6943b1b10d919_1766044081.jpg', 'I got meat with you', 'BURGER SANDWICH', 4.5, '2025-12-18 07:48:01'),
(129, 'cheddar cheese', 9500.00, 'prod_6943b1e6f159d_1766044134.jpg', 'fresh made', 'BURGER SANDWICH', 4.5, '2025-12-18 07:48:54'),
(130, 'gherkins', 9500.00, 'prod_6943b21b35195_1766044187.jpg', 'fresh made', 'BURGER SANDWICH', 4.5, '2025-12-18 07:49:47'),
(131, 'lettuce', 9500.00, 'prod_6943b24a9a17d_1766044234.jpg', 'it\'s  fresh', 'BURGER SANDWICH', 4.5, '2025-12-18 07:50:34'),
(132, 'tomato', 9500.00, 'prod_6943b2956d31c_1766044309.jpg', 'sweet onion and signature dressing', 'BURGER SANDWICH', 4.5, '2025-12-18 07:51:49'),
(133, 'Pan fried chicken breast', 9500.00, 'prod_6943b2dfe1614_1766044383.jpg', 'fresh made', 'BURGER SANDWICH', 4.5, '2025-12-18 07:53:03'),
(134, 'avocado', 9500.00, 'prod_6943b360b0df7_1766044512.jpg', 'fresh made', 'BURGER SANDWICH', 4.5, '2025-12-18 07:55:12'),
(135, 'caramelize onion', 9500.00, 'prod_6943b48b92ba7_1766044811.jpg', 'fresh made', 'BURGER SANDWICH', 4.5, '2025-12-18 08:00:11'),
(136, 'fried egg', 9500.00, 'prod_6943b649e78d7_1766045257.jpg', 'fresh  made', 'BURGER SANDWICH', 4.5, '2025-12-18 08:07:37'),
(137, 'Meaty red sauce', 7500.00, 'prod_6943b8605a532_1766045792.jpg', 'Pasta Bolognese', 'Pasta', 4.5, '2025-12-18 08:16:32'),
(138, 'Vegetable Pasta Onion', 9000.00, 'prod_6943b918a3481_1766045976.jpg', 'Onion, garlic, carrot, baby marrow, cauliflower, tomato sauce, pasta', 'VEGETERIAN DISH', 4.5, '2025-12-18 08:19:36'),
(139, 'Prawn Salad', 10000.00, 'prod_6943b9d941b3a_1766046169.jpg', 'Thousand Island, vinaigrette dressing, tomato, lettuce, garlic sauté prawn, white\r\npepper, cucumber, lemon wedge', 'GREEN IS HEALTHY', 4.5, '2025-12-18 08:22:49'),
(140, 'Egusi Soup', 12000.00, 'prod_6943bcfb1bfbd_1766046971.jpg', 'Grounded melon, grounded crayfish, dry catfish, Uziza Leaf, Stock fish, dry catfish,\r\n\r\nponmo and local spices.', 'AFRICAN DELIGHT COMBO', 4.5, '2025-12-18 08:36:11'),
(141, 'Tilapia crabmeat', 14500.00, 'prod_6943be9f59bc3_1766047391.jpg', 'Catfish/Tilapia crabmeat, Shrimps, okra, pepper, crayfish powder, dry whole\r\ncrayfish, Cameroon pepper, uziza leave', 'Seafood', 4.5, '2025-12-18 08:43:11'),
(142, 'Grilled tiger prawn', 18000.00, 'prod_6943bfa4c9290_1766047652.jpg', 'Seasonal vegetables, sweated tomato Concasse', 'MAIN CONTINENTAL', 4.5, '2025-12-18 08:47:32'),
(143, 'Creamy Chicken Corn Soup', 5500.00, 'prod_6943c407e70d8_1766048775.jpg', 'Puree corn, cream, chopped chicken, chicken broth seasoning and butter bread', 'STARTER', 4.5, '2025-12-18 09:06:15'),
(144, 'Roasted tomato soup', 5500.00, 'prod_6943c46fe457b_1766048879.jpg', 'Fresh roasted tomato, chicken broth, seasoning and butter bread', 'STARTER', 4.5, '2025-12-18 09:07:59'),
(145, 'GIMLET', 7500.00, 'prod_6943c6b75d444_1766049463.jpg', 'fresh', 'COOKTAIL', 4.5, '2025-12-18 09:17:43'),
(146, 'MAI TAI', 15000.00, 'prod_6943c72db3660_1766049581.jpg', 'fresh', 'SPECIAL COCKTAILS', 4.5, '2025-12-18 09:19:41'),
(147, 'Afang Soup', 12000.00, 'prod_69455e2a77fe6_1766153770.jpg', 'Stock fish, grounded crayfish,Cameroon pepper, ponmo, Afang leaf, Palm oil', 'AFRICAN DELIGHT COMBO', 4.5, '2025-12-19 00:18:13'),
(148, 'OGBONO', 12000.00, 'prod_69455ef616713_1766153974.jpg', 'Uziza, stock fish, grounded crayfish, dry hake fish, grounded ogbono seed and\r\n\r\nponmo', 'AFRICAN DELIGHT COMBO', 4.5, '2025-12-19 14:19:34'),
(149, 'OKRO SOUP', 12000.00, 'prod_69455f532ded1_1766154067.jpg', 'Uziza, stockfish, crayfish powder, dry hake fish, and ponmo', 'AFRICAN DELIGHT COMBO', 4.5, '2025-12-19 14:21:07'),
(150, 'EDIKANKONG', 12000.00, 'prod_69455faedaf75_1766154158.jpg', 'Ugu leave, water leaf, stock fish, grounded crayfish, ponmo, local spice and pepper', 'AFRICAN DELIGHT COMBO', 4.5, '2025-12-19 14:22:38'),
(151, 'SEAFOOD OKRO', 14500.00, 'prod_6945603a03fc7_1766154298.jpg', 'Ugu leave, water leaf, stock fish, grounded crayfish, ponmo, local spice and pepper', 'AFRICAN DELIGHT COMBO', 4.5, '2025-12-19 14:24:58'),
(152, 'SEAFOOD OKRO', 14500.00, 'prod_694561188e973_1766154520.jpg', 'TILAPIA/RED SNAPPER/CATFISH, crab meat, SHRIMPS, okra optional, grounded\r\npepper, grounded crayfish, dry whole crayfish, Cameroon pepper, uziza leaf.', 'AFRICAN DELIGHT COMBO', 4.5, '2025-12-19 14:28:40'),
(153, 'SPICY ESCARGOT', 16000.00, 'prod_694561c65cefa_1766154694.jpg', 'Served with kelewele, bell pepper', 'AFRICAN DELIGHT COMBO', 4.5, '2025-12-19 14:31:34');

-- --------------------------------------------------------

--
-- Table structure for table `room_categories`
--

CREATE TABLE `room_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `total_rooms` int(11) NOT NULL DEFAULT 10,
  `occupied_rooms` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT 'default-room.jpg',
  `description` text DEFAULT NULL,
  `max_guests` int(11) DEFAULT 2,
  `size_sqm` int(11) DEFAULT 40,
  `bed_type` varchar(100) DEFAULT '1 King Bed',
  `amenities` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_categories`
--

INSERT INTO `room_categories` (`id`, `name`, `slug`, `price_per_night`, `total_rooms`, `occupied_rooms`, `image`, `description`, `max_guests`, `size_sqm`, `bed_type`, `amenities`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Classic Room', 'classic', 70000.00, 20, 12, 'classic-room.jpg', 'Cozy and comfortable with city view. Perfect for short stays.', 2, 38, '1 King Bed', 'Free WiFi, Mini-fridge, Work desk, Sitting area, Daily housekeeping', 1, '2025-11-25 11:03:58', '2025-11-25 11:03:58'),
(2, 'Deluxe Room', 'deluxe', 80000.00, 18, 9, 'deluxe-room.jpg', 'Spacious room with modern amenities and premium bedding.', 2, 42, '1 King Bed', 'City view, Sitting area, Mini-fridge, Laptop workspace, Smart TV', 1, '2025-11-25 11:03:58', '2025-11-25 11:03:58'),
(3, 'Executive Mini', 'executive-mini', 95000.00, 15, 6, 'executive-mini.jpg', 'Designed for business travelers with enhanced workspace.', 2, 48, '1 King Bed', 'City view, Large desk, Mini-fridge, High-speed WiFi, Premium toiletries', 1, '2025-11-25 11:03:58', '2025-11-25 11:03:58'),
(4, 'Executive Max', 'executive-max', 105000.00, 12, 4, 'executive-max.jpg', 'Larger executive room with premium finishes and lighting.', 2, 55, '1 King Bed', 'Panoramic city view, Enhanced workspace, Mini bar, Rain shower', 1, '2025-11-25 11:03:58', '2025-11-25 11:03:58'),
(6, 'Penthouse Single', 'penthouse-single', 200000.00, 4, 1, 'penthouse-single.jpg', 'Exclusive penthouse with private dining and panoramic rooftop views.', 4, 120, '1 King Bed + 1 Large Sofa Bed', 'Private balcony, Full dining area, Living room, Chef service on call', 1, '2025-11-25 11:03:58', '2025-11-25 11:03:58'),
(7, 'Penthouse Double', 'penthouse-double', 200000.00, 3, 1, 'penthouse-double.jpg', 'The ultimate luxury experience with dual access and premium everything.', 2, 110, '1 King Bed', 'Rooftop access, Private jacuzzi, Full entertainment system, 24/7 butler', 1, '2025-11-25 11:03:58', '2025-11-25 11:03:58');

-- --------------------------------------------------------

--
-- Table structure for table `room_inventory`
--

CREATE TABLE `room_inventory` (
  `id` int(11) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `total_rooms` int(11) NOT NULL,
  `occupied_rooms` int(11) NOT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `image` varchar(100) DEFAULT NULL,
  `max_guests` int(11) DEFAULT 2,
  `size_sqm` int(11) DEFAULT 40,
  `bed_type` varchar(100) DEFAULT '1 King Bed',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_inventory`
--

INSERT INTO `room_inventory` (`id`, `room_type`, `room_name`, `total_rooms`, `occupied_rooms`, `price_per_night`, `image`, `max_guests`, `size_sqm`, `bed_type`, `updated_at`) VALUES
(8, 'classic', 'Classic Room', 10, 17, 70000.00, '1764570658_0.jpg,1764570752_0.jpg,1764570785_0.jpg,1764570811_0.jpg,1764570822_0.jpg', 2, 38, '1 King Bed', '2025-12-06 17:02:53'),
(9, 'deluxe', 'Deluxe Room', 7, 16, 80000.00, '1764571022_0.jpg,1764571069_0.jpg,1764571082_0.jpg,1764571120_0.jpg,1764571134_0.jpg', 2, 42, '1 King Bed', '2025-12-06 17:01:45'),
(10, 'executive-mini', 'Executive Mini', 4, 13, 95000.00, '1764571610_0.jpg,1764571618_0.jpg,1764571628_0.jpg,1764571644_0.jpg,1764571656_0.jpg', 2, 48, '1 King Bed', '2025-12-06 17:01:24'),
(11, 'executive-max', 'Executive Max', 4, 10, 105000.00, '1764571321_0.jpg,1764571365_0.jpg,1764571392_0.jpg,1764571400_0.jpg,1764571409_0.jpg', 2, 55, '1 King Bed', '2025-12-06 17:00:27'),
(13, 'penthouse-single', 'Penthouse Single', 1, 1, 200000.00, '1764573387_0.jpg,1764573444_0.jpg,1764573481_0.jpg,1764573492_0.jpg,1764573509_0.jpg', 4, 120, '1 King Bed + 1 Large Sofa Bed', '2025-12-05 23:47:24'),
(14, 'penthouse-double', 'Penthouse Double', 1, 4, 200000.00, '1764877444_0.jpg,1764877479_0.jpg,1764877500_0.jpg,1764877522_0.jpg,1764877597_0.jpg', 2, 110, '2 King Beds', '2025-12-08 14:50:17');

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','unsubscribed') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscribers`
--

INSERT INTO `subscribers` (`id`, `email`, `subscribed_at`, `status`) VALUES
(0, 'salihubarup@gmail.com', '2025-11-25 08:33:03', 'active'),
(0, 'salihubarup@gmail.com', '2025-11-25 08:33:03', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `walkin_bookings`
--

CREATE TABLE `walkin_bookings` (
  `id` int(11) NOT NULL,
  `booking_ref` varchar(50) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `room_type` varchar(100) DEFAULT NULL,
  `check_in` date DEFAULT NULL,
  `check_out` date DEFAULT NULL,
  `total_amount` decimal(12,2) DEFAULT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booked_dates`
--
ALTER TABLE `booked_dates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_booking_date` (`room_type`,`stay_date`,`booking_ref`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_name` (`name`),
  ADD KEY `idx_sort` (`sort_order`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hotel_system_flags`
--
ALTER TABLE `hotel_system_flags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `net_bookings`
--
ALTER TABLE `net_bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_ref` (`booking_ref`),
  ADD UNIQUE KEY `unique_ref` (`booking_ref`),
  ADD KEY `idx_checkin` (`check_in`),
  ADD KEY `idx_checkout` (`check_out`),
  ADD KEY `idx_room_type` (`room_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_categories`
--
ALTER TABLE `room_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `room_inventory`
--
ALTER TABLE `room_inventory`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `booked_dates`
--
ALTER TABLE `booked_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `hotel_system_flags`
--
ALTER TABLE `hotel_system_flags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `net_bookings`
--
ALTER TABLE `net_bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT for table `room_categories`
--
ALTER TABLE `room_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `room_inventory`
--
ALTER TABLE `room_inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

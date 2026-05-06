-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 02, 2025 at 05:28 PM
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
-- Database: `de-grand_db`
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
(5, 'Salihu', 'salihubarup@gmail.com', NULL, '$2y$10$FeUkephJSnzQoWjAZDk.MeWKu89BtRmYSjP.nz4TqV6I/ZfkIDhY6', 'manager', 'active', '2025-12-02 17:27:59', '2025-12-02 15:18:49');

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
(3, 'Suya-Crusted Wagyu & 24K Gold Jollof: A Night at Aurum Restaurant', 'Chef Hilda Baci', 'CULINARY', 'published', '<p>Forget everything you thought you knew about fine dining in Nigeria.</p>\r\n<p>At <strong>Aurum</strong> &mdash; our 3-time Michelin-recognized rooftop restaurant &mdash; we don&rsquo;t just serve food. We serve legacy.</p>\r\n<p>Our signature dish: <strong>24K Gold Jollof Rice</strong> with suya-crusted Japanese A5 Wagyu, smoked plantain pur&eacute;e, and truffle ata sauce. Yes, real edible gold.</p>\r\n<p>Last month, Wizkid said it was &ldquo;the best jollof I&rsquo;ve ever tasted &mdash; and I&rsquo;ve tasted everywhere.&rdquo;</p>\r\n<p>Book your table 6 months in advance. Or be a Diamond Club member &mdash; and walk in anytime.</p>', 'blog_1764057598_6868.jpg', '2025-11-25 08:56:21', '2025-11-25 08:59:58'),
(4, 'The Royals Spa: Where Kings & Queens Come to Recharge', 'Tiwa Savage', 'LIFESTYLE', 'published', '<p>After a long week of running empires, even kings need rest.</p>\r\n<p>Welcome to <strong>The Royals Spa</strong> &mdash; 5,000 sqm of pure tranquility. Floating treatment rooms. 24-karat gold facials. Diamond dust body scrubs.</p>\r\n<p>Our signature treatment: <strong>The Queen&rsquo;s Ritual</strong> &mdash; 4 hours of pure indulgence: rose milk bath, caviar facial, hot stone massage with gold-infused oils, and ends with champagne and strawberries on a private balcony watching the Lagos sunset.</p>\r\n<p>I come here every month. My skin has never looked better. My soul? Even better.</p>', 'blog_1764057614_8386.jpg', '2025-11-25 08:56:21', '2025-11-25 09:00:14'),
(5, 'The Night Burna Boy Rented The Entire Hotel', 'Royal Editor', 'EXCLUSIVE', 'published', '<p>It was 2:47 AM when the call came.</p>\r\n<p>&ldquo;Burna wants the entire hotel. Tonight. Everyone out.&rdquo;</p>\r\n<p>Within 3 hours, all 187 rooms were cleared. The rooftop was transformed into a private African Giant concert. The pool became a floating stage.</p>\r\n<p>At 4:12 AM, Burna Boy performed &ldquo;Ye&rdquo; live &mdash; just for 50 of his closest friends and family.</p>\r\n<p>At 7:00 AM, everything was back to normal. Not a single guest knew what happened.</p>\r\n<p>That&rsquo;s the power of The Royals.</p>', 'blog_1764057630_1366.jpg', '2025-11-25 08:56:21', '2025-11-25 09:00:31');

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
(1, 'Burger', 'cat_6929924d0f37f_1764332109.jpg', '2025-11-28 08:00:27', 1),
(2, 'Pizza', 'cat_692967ccb5bf1_1764321228.jpg', '2025-11-28 08:00:27', 2),
(3, 'Salad', 'cat_692967eb3c73f_1764321259.jpg', '2025-11-28 08:00:27', 3),
(4, 'Dessert', 'cat_692967fcbd45a_1764321276.jpg', '2025-11-28 08:00:27', 4),
(5, 'Drinks', 'cat_6929682259d75_1764321314.jpg', '2025-11-28 08:00:27', 5),
(6, 'Seafood', 'cat_6929683c7332c_1764321340.jpg', '2025-11-28 08:00:27', 6),
(7, 'Pasta', 'cat_6929684e7e8df_1764321358.jpg', '2025-11-28 08:00:27', 7),
(9, 'Rice', 'cat_69296872c0f46_1764321394.jpg', '2025-11-28 08:00:27', 9),
(10, 'Soup', 'cat_6929688cb9756_1764321420.jpg', '2025-11-28 08:00:27', 10),
(11, 'Grilled', 'cat_6929689e4fc7b_1764321438.jpg', '2025-11-28 08:00:27', 11),
(12, 'Breakfast', 'cat_692968b4c1c64_1764321460.jpg', '2025-11-28 08:00:27', 12);

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
(0, 'DEGRAND20251127-', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'booking', 'can i talk to someone', 'new', '::1', NULL, '2025-11-27 13:19:13');

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
(34, 'DEGRAND1764184065002', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-11-26', '2025-11-27', 1, 'executive-mini', 1, 1, 'no', 106400.00, 'confirmed', 'online', NULL, 'DEGRAND1764184065002', 'website', '2025-11-26 20:08:01', '2025-11-26 20:08:01'),
(35, 'DEGRAND1764185510772', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-11-26', '2025-11-27', 1, 'penthouse-single', 2, 1, 'no', 224000.00, 'confirmed', 'online', NULL, 'DEGRAND1764185510772', 'website', '2025-11-26 20:32:10', '2025-11-26 20:32:10'),
(39, 'DEGRAND1764226600561', 'Salihu Hassan', 'salihubarup@gmail.com', '08066610571', 'NG', '2025-11-27', '2025-11-28', 1, 'penthouse-single', 2, 0, 'no', 224000.00, 'confirmed', 'online', NULL, 'DEGRAND1764226600561', 'website', '2025-11-27 07:56:57', '2025-11-27 07:56:57');

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
(1, 0, 10000.00, 'ready', 'cash', '3b/5 water', 'Salihu Hassan', '08066610571', 'be faster', '2025-12-02 10:24:45', '2025-12-02 14:42:15');

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
(1, 1, 7, 'Cheese Burger Deluxe', 1, 10000.00, '2025-12-02 10:24:45');

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
(5, 'Moi Moi & Pap', 1200.00, 'prod_692938bb2828b_1764309179.jpg', 'Steamed bean pudding with custard', 'Breakfast', 4.5, '2025-11-19 18:52:41'),
(7, 'Cheese Burger Deluxe', 10000.00, 'prod_6929410c5bca5_1764311308.jpg', 'Double beef patty with melted cheddar, caramelized onions, pickles & house sauce', 'Burger', 4.9, '2025-11-19 19:21:06'),
(9, 'Chicken Crispy', 2600.00, 'prod_692940eec7d4b_1764311278.jpg', 'Crispy fried chicken fillet, lettuce, mayo & soft bun – Lagos street style', 'Burger', 4.7, '2025-11-19 19:21:06');

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
(8, 'classic', 'Classic Room', 20, 17, 70000.00, '1764570658_0.jpg,1764570752_0.jpg,1764570785_0.jpg,1764570811_0.jpg,1764570822_0.jpg', 2, 38, '1 King Bed', '2025-12-01 05:33:42'),
(9, 'deluxe', 'Deluxe Room', 18, 16, 80000.00, '1764571022_0.jpg,1764571069_0.jpg,1764571082_0.jpg,1764571120_0.jpg,1764571134_0.jpg', 2, 42, '1 King Bed', '2025-12-02 14:59:24'),
(10, 'executive-mini', 'Executive Mini', 15, 7, 95000.00, '1764571610_0.jpg,1764571618_0.jpg,1764571628_0.jpg,1764571644_0.jpg,1764571656_0.jpg', 2, 48, '1 King Bed', '2025-12-01 05:47:36'),
(11, 'executive-max', 'Executive Max', 12, 10, 105000.00, '1764571321_0.jpg,1764571365_0.jpg,1764571392_0.jpg,1764571400_0.jpg,1764571409_0.jpg', 2, 55, '1 King Bed', '2025-12-01 05:43:29'),
(13, 'penthouse-single', 'Penthouse Single', 10, 1, 200000.00, '1764573387_0.jpg,1764573444_0.jpg,1764573481_0.jpg,1764573492_0.jpg,1764573509_0.jpg', 4, 120, '1 King Bed + 1 Large Sofa Bed', '2025-12-01 06:20:17'),
(14, 'penthouse-double', 'Penthouse Double', 6, 3, 200000.00, '1764194264_1764193669_0.jpg,1764196550_0.jpg,1764196561_0.jpg,1764196584_0.jpg,1764196601_0.jpg', 2, 110, '1 King Bed', '2025-11-26 21:36:41');

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
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_name` (`name`),
  ADD KEY `idx_sort` (`sort_order`),
  ADD KEY `idx_name` (`name`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `net_bookings`
--
ALTER TABLE `net_bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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

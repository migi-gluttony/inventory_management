-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 07, 2025 at 07:15 PM
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
-- Database: `inventory_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `invoice_date` datetime NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` varchar(50) NOT NULL DEFAULT 'pending',
  `order_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_date`, `total_amount`, `payment_status`, `order_id`) VALUES
(1, '2025-04-06 23:23:20', 2969.67, 'paid', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_date`, `status`, `user_id`) VALUES
(1, '2025-04-06 23:18:39', 'pending', 38);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 38, 33, 89.99);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `provider_id` int(11) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `stock`, `date_added`, `date_modified`, `provider_id`, `is_deleted`) VALUES
(38, 'Office Chair', 89.99, 17, '2024-10-09 18:54:32', '2025-04-06 23:18:39', 1, 0),
(39, 'Laptop Stand', 29.99, 150, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 2, 0),
(40, 'Eco-Friendly Notebook', 12.50, 200, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 3, 0),
(41, 'Wireless Mouse', 25.99, 120, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 4, 0),
(42, 'Ergonomic Keyboard', 45.00, 75, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 5, 0),
(43, 'Desk Lamp', 22.75, 180, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 1, 0),
(44, 'Smartphone Charger', 15.50, 300, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 2, 0),
(45, 'Recycled Paper Pen', 1.20, 500, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 3, 0),
(46, 'Bluetooth Speaker', 59.99, 90, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 4, 0),
(47, 'Noise-Cancelling Headphones', 199.99, 30, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 5, 0),
(48, '4K Monitor', 299.99, 20, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 1, 0),
(49, 'Gaming Mouse', 49.99, 60, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 2, 0),
(50, 'Wireless Earbuds', 79.99, 80, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 3, 0),
(51, 'HDMI Cable', 8.99, 150, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 4, 0),
(52, 'Laptop Backpack', 39.99, 100, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 5, 0),
(53, 'Smartwatch', 199.00, 40, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 1, 0),
(54, 'Portable SSD', 89.00, 70, '2024-10-09 18:54:32', '2024-10-10 21:09:24', 2, 0),
(55, 'USB-C Hub', 25.00, 120, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 3, 0),
(56, 'Smartphone Case', 14.99, 200, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 4, 0),
(58, 'Desk Organizer', 19.99, 110, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 1, 0),
(59, 'Blue Light Blocking Glasses', 29.00, 90, '2024-10-09 18:54:32', '2024-10-10 18:59:52', 2, 1),
(60, 'Whiteboard Markers', 5.99, 250, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 3, 0),
(61, 'Cable Management Sleeve', 10.00, 300, '2024-10-09 18:54:32', '2024-10-10 18:59:53', 4, 1),
(62, 'Ergonomic Footrest', 35.00, 60, '2024-10-09 18:54:32', '2024-10-10 18:59:55', 5, 1),
(63, 'Portable Power Bank', 39.99, 150, '2024-10-09 18:54:32', '2024-10-10 18:59:54', 1, 1),
(64, 'Virtual Reality Headset', 299.99, 25, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 2, 0),
(65, 'Fitness Tracker', 59.99, 80, '2024-10-09 18:54:32', '2024-10-10 18:59:48', 3, 1),
(66, 'Digital Drawing Tablet', 199.00, 30, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 4, 0),
(67, 'Mechanical Keyboard', 79.99, 55, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 5, 0),
(68, 'Smart Home Hub', 89.00, 40, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 1, 0),
(69, 'Surge Protector', 24.99, 200, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 2, 0),
(70, 'Phone Mount for Car', 15.99, 100, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 3, 0),
(71, 'Wireless Game Controller', 49.99, 70, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 4, 0),
(72, 'Streaming Webcam', 89.99, 30, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 5, 0),
(73, 'Home Security Camera', 99.99, 20, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 1, 0),
(74, 'Smart LED Light Bulb', 19.99, 150, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 2, 0),
(75, 'Air Purifier', 149.99, 15, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 3, 0),
(76, 'Cordless Vacuum Cleaner', 299.99, 10, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 4, 0),
(77, 'Electric Kettle', 39.99, 80, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 5, 0),
(78, 'Non-Stick Cookware Set', 99.99, 35, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 1, 0),
(79, 'Kitchen Scale', 25.00, 90, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 2, 0),
(80, 'Bluetooth Food Thermometer', 39.99, 70, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 3, 0),
(81, 'Portable Blender', 29.99, 100, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 4, 0),
(82, 'Stainless Steel Water Bottle', 19.99, 200, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 5, 0),
(83, 'Electric Grill', 89.99, 25, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 1, 0),
(84, 'Coffee Maker', 49.99, 40, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 2, 0),
(85, 'Food Processor', 99.99, 15, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 3, 0),
(86, 'Cast Iron Skillet', 34.99, 80, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 4, 0),
(87, 'Bamboo Cutting Board', 12.99, 150, '2024-10-09 18:54:32', '2024-10-09 18:54:32', 5, 0),
(130, 'Enchanted Elixir', 199.99, 40, '2024-10-09 18:56:42', '2024-10-09 18:56:42', 1, 0),
(131, 'Mystic Orb', 39.99, 80, '2024-10-09 18:56:42', '2024-10-09 18:56:42', 2, 0),
(132, 'Phantom Projector', 299.99, 25, '2024-10-09 18:56:42', '2024-10-09 18:56:42', 3, 0),
(133, 'Wand of Creation', 199.00, 60, '2024-10-09 18:56:42', '2024-10-09 18:56:42', 4, 0),
(134, 'Glimmering Gem', 19.99, 150, '2024-10-09 18:56:42', '2024-10-09 18:56:42', 5, 0),
(135, 'Sorcerer’s Scepter', 29.99, 100, '2024-10-09 18:56:42', '2024-10-09 18:56:42', 1, 0),
(136, 'Spellbound Scroll', 49.99, 70, '2024-10-09 18:56:42', '2024-10-09 18:56:42', 2, 0),
(137, 'Magical Quill', 15.99, 200, '2024-10-09 18:56:42', '2024-10-09 18:56:42', 3, 0),
(138, 'Familiar’s Feather', 109.99, 30, '2024-10-09 18:56:42', '2024-10-09 18:56:42', 4, 0),
(139, 'Potion of Whimsy', 10.99, 180, '2024-10-09 18:56:42', '2024-10-09 18:56:42', 5, 0),
(140, 'Crystal Ball', 12.00, 250, '2024-10-09 18:56:42', '2024-10-09 18:56:42', 1, 0),
(141, 'Cauldron of Dreams', 35.00, 110, '2024-10-09 18:56:42', '2024-10-09 18:56:42', 2, 0),
(142, 'Mystical Mirror', 89.00, 40, '2024-10-09 18:56:42', '2024-10-09 18:56:42', 3, 0),
(143, 'Grimoire of Shadows', 49.99, 120, '2024-10-09 18:56:42', '2024-10-09 18:56:42', 4, 0),
(144, 'Talisman of Fortune', 99.99, 50, '2024-10-09 18:56:42', '2024-10-09 18:56:42', 5, 0),
(145, 'Charmed Amulet', 69.99, 80, '2024-10-09 18:56:42', '2024-10-09 18:56:42', 1, 0),
(146, 'Broomstick of Speed', 89.99, 30, '2024-10-09 18:56:42', '2024-10-09 18:56:42', 2, 0),
(147, 'Enigma Box', 149.00, 20, '2024-10-09 18:56:42', '2024-10-09 18:56:42', 3, 0),
(148, 'Wishing Stone', 39.99, 90, '2024-10-09 18:56:42', '2024-10-09 18:56:42', 4, 0),
(149, 'Lumos Lamp', 79.99, 70, '2024-10-09 18:56:42', '2024-10-09 18:56:42', 5, 0),
(150, 'Amoeba Culture', 15.99, 150, '2024-10-09 18:58:28', '2024-10-09 18:58:28', 1, 0),
(151, 'Eukaryotic Cells Kit', 59.99, 50, '2024-10-09 18:58:28', '2024-10-09 18:58:28', 1, 0),
(152, 'Microscopic Algae Sample', 24.99, 75, '2024-10-09 18:58:28', '2024-10-09 18:58:28', 2, 0),
(153, 'Bacterial Growth Medium', 12.50, 200, '2024-10-09 18:58:28', '2024-10-09 18:58:28', 2, 0),
(154, 'Fungal Spores Collection', 30.00, 10, '2024-10-09 18:58:28', '2024-10-09 18:58:28', 3, 0),
(155, 'Paramecium Observation Slide', 19.99, 80, '2024-10-09 18:58:28', '2024-10-09 18:58:28', 3, 0),
(156, 'Yeast Fermentation Starter', 14.99, 65, '2024-10-09 18:58:28', '2024-10-09 18:58:28', 4, 0),
(157, 'Plankton Sampling Net', 45.00, 15, '2024-10-09 18:58:28', '2024-10-09 18:58:28', 4, 0),
(158, 'DNA Extraction Kit', 99.99, 25, '2024-10-09 18:58:28', '2024-10-09 18:58:28', 5, 0),
(159, 'Genetic Sequencer', 499.99, 5, '2024-10-09 18:58:28', '2024-10-09 18:58:28', 5, 0),
(160, 'Antibody Test Reagents', 75.00, 120, '2024-10-09 18:58:28', '2024-10-09 18:58:28', 1, 0),
(161, 'Stem Cell Culture Media', 100.00, 30, '2024-10-09 18:58:28', '2024-10-09 18:58:28', 2, 0),
(162, 'Mitosis Model Set', 22.50, 90, '2024-10-09 18:58:28', '2024-10-09 18:58:28', 3, 0),
(163, 'RNA Sequencing Tools', 350.00, 8, '2024-10-09 18:58:28', '2024-10-09 18:58:28', 4, 0),
(164, 'Microbial Fuel Cell', 200.00, 20, '2024-10-09 18:58:28', '2024-10-09 18:58:28', 5, 0),
(165, 'Biodiversity Survey Kit', 125.00, 40, '2024-10-09 18:58:28', '2024-10-09 18:58:28', 1, 0),
(166, 'Enzyme Activity Test Set', 55.00, 50, '2024-10-09 18:58:28', '2024-10-09 18:58:28', 2, 0),
(167, 'Chloroplast Isolation Kit', 45.00, 12, '2024-10-09 18:58:28', '2024-10-09 18:58:28', 3, 0),
(168, 'Microbiome Analysis Tools', 80.00, 100, '2024-10-09 18:58:28', '2024-10-09 18:58:28', 4, 0),
(169, 'Bioluminescent Plankton', 9.99, 200, '2024-10-09 18:58:28', '2024-10-09 18:58:28', 5, 0),
(170, 'Quantum Mechanics Textbook', 49.99, 50, '2024-10-09 19:00:26', '2024-10-09 19:00:26', 1, 0),
(171, 'Newtonian Mechanics Kit', 39.99, 80, '2024-10-09 19:00:26', '2024-10-09 19:00:26', 1, 0),
(172, 'Laser Diode Module', 75.00, 30, '2024-10-09 19:00:26', '2024-10-09 19:00:26', 1, 0),
(173, 'Optics Experiment Set', 45.00, 20, '2024-10-09 19:00:26', '2024-10-09 19:00:26', 1, 0),
(174, 'Electromagnetic Field Simulator', 199.99, 15, '2024-10-09 19:00:26', '2024-10-09 19:00:26', 1, 0),
(175, 'Thermodynamics Lab Equipment', 120.00, 25, '2024-10-09 19:00:26', '2024-10-09 19:00:26', 1, 0),
(176, 'Wave Dynamics Model', 55.00, 40, '2024-10-09 19:00:26', '2024-10-09 19:00:26', 1, 0),
(177, 'Particle Physics Detector', 299.99, 5, '2024-10-09 19:00:26', '2024-10-09 19:00:26', 1, 0),
(178, 'Superconductivity Experiment Kit', 89.99, 10, '2024-10-09 19:00:26', '2024-10-09 19:00:26', 1, 0),
(179, 'Gravity Measurement Tool', 29.99, 100, '2024-10-09 19:00:26', '2024-10-09 19:00:26', 1, 0),
(180, 'Kinematics Motion Sensors', 59.99, 45, '2024-10-09 19:00:26', '2024-10-09 19:00:26', 1, 0),
(181, 'Electrostatics Experiment Kit', 22.50, 60, '2024-10-09 19:00:26', '2024-10-09 19:00:26', 1, 0),
(182, 'Physics Lab Safety Gear', 19.99, 75, '2024-10-09 19:00:26', '2024-10-09 19:00:26', 1, 0),
(183, 'Relativity Theory Model', 65.00, 30, '2024-10-09 19:00:26', '2024-10-09 19:00:26', 1, 0),
(184, 'String Theory Simulation Kit', 150.00, 12, '2024-10-09 19:00:26', '2024-10-09 19:00:26', 1, 0),
(191, 'Metronome with Tuner', 29.99, 60, '2024-10-09 19:01:16', '2024-10-09 19:01:16', 2, 0),
(192, 'Percussion Instrument Set', 89.99, 50, '2024-10-09 19:01:16', '2024-10-09 19:01:16', 2, 0),
(193, 'Studio Headphones', 69.99, 45, '2024-10-09 19:01:16', '2024-10-09 19:01:16', 2, 0),
(194, 'Karaoke Machine', 199.99, 5, '2024-10-09 19:01:16', '2024-10-09 19:01:16', 2, 0),
(195, 'Music Theory Workbook', 15.00, 80, '2024-10-09 19:01:16', '2024-10-09 19:01:16', 2, 0),
(196, 'Guitar Pedal Effects', 119.99, 30, '2024-10-09 19:01:16', '2024-10-09 19:01:16', 2, 0),
(197, 'Organic Fertilizer', 25.99, 100, '2024-10-09 19:01:52', '2024-10-09 19:01:52', 3, 0),
(198, 'Hydroponic System Kit', 199.99, 20, '2024-10-09 19:01:52', '2024-10-09 19:01:52', 3, 0),
(199, 'Soil pH Tester', 15.50, 150, '2024-10-09 19:01:52', '2024-10-09 19:01:52', 3, 0),
(200, 'Seedling Starter Tray', 10.00, 200, '2024-10-09 19:01:52', '2024-10-09 19:01:52', 3, 0),
(201, 'Compost Bin', 49.99, 80, '2024-10-09 19:01:52', '2024-10-09 19:01:52', 3, 0),
(202, 'Drip Irrigation System', 79.99, 50, '2024-10-09 19:01:52', '2024-10-09 19:01:52', 3, 0),
(203, 'Garden Trowel', 12.99, 120, '2024-10-09 19:01:52', '2024-10-09 19:01:52', 3, 0),
(204, 'Plant Nutrient Solution', 19.99, 70, '2024-10-09 19:01:52', '2024-10-09 19:01:52', 3, 0),
(205, 'Greenhouse Film', 29.99, 30, '2024-10-09 19:01:52', '2024-10-09 19:01:52', 3, 0),
(206, 'Insect Netting', 15.00, 90, '2024-10-09 19:01:52', '2024-10-09 19:01:52', 3, 0),
(207, 'Garden Shears', 24.99, 40, '2024-10-09 19:01:52', '2024-10-09 19:01:52', 3, 0),
(208, 'Digital Weather Station', 79.99, 50, '2024-10-09 19:02:33', '2024-10-09 19:02:33', 4, 0),
(209, 'Anemometer', 45.00, 30, '2024-10-09 19:02:33', '2024-10-09 19:02:33', 4, 0),
(210, 'Rain Gauge', 12.99, 100, '2024-10-09 19:02:33', '2024-10-09 19:02:33', 4, 0),
(211, 'Hygrometer', 25.00, 70, '2024-10-09 19:02:33', '2024-10-09 19:02:33', 4, 0),
(212, 'Indoor Thermometer', 15.50, 120, '2024-10-09 19:02:33', '2024-10-09 19:02:33', 4, 0),
(213, 'Weather Balloons', 99.99, 20, '2024-10-09 19:02:33', '2024-10-09 19:02:33', 4, 0),
(214, 'UV Index Monitor', 35.00, 40, '2024-10-09 19:02:33', '2024-10-09 19:02:33', 4, 0),
(215, 'Portable Wind Meter', 29.99, 60, '2024-10-09 19:02:33', '2024-10-09 19:02:33', 4, 0),
(216, 'Thermal Camera', 199.99, 15, '2024-10-09 19:02:33', '2024-10-09 19:02:33', 4, 0),
(217, 'Lightning Detector', 89.99, 25, '2024-10-09 19:02:33', '2024-10-09 19:02:33', 4, 0),
(218, 'Weatherproof Notepad', 8.99, 200, '2024-10-09 19:02:33', '2024-10-09 19:02:33', 4, 0),
(219, 'Emergency Weather Radio', 49.99, 80, '2024-10-09 19:02:33', '2024-10-09 19:02:33', 4, 0),
(220, 'Solar-Powered Weather Station', 129.00, 10, '2024-10-09 19:02:33', '2024-10-09 19:02:33', 4, 0),
(221, 'Acetic Acid', 12.50, 100, '2024-10-09 19:03:21', '2024-10-09 19:03:21', 6, 0),
(222, 'Sodium Chloride (Salt)', 5.00, 200, '2024-10-09 19:03:21', '2024-10-09 19:03:21', 6, 0),
(223, 'Hydrochloric Acid', 15.75, 50, '2024-10-09 19:03:21', '2024-10-09 19:03:21', 6, 0),
(224, 'Ethanol (Alcohol)', 8.99, 150, '2024-10-09 19:03:21', '2024-10-09 19:03:21', 6, 0),
(225, 'Sodium Bicarbonate (Baking Soda)', 3.00, 300, '2024-10-09 19:03:21', '2024-10-09 19:03:21', 6, 0),
(226, 'Ammonium Hydroxide', 14.50, 40, '2024-10-09 19:03:21', '2024-10-09 19:03:21', 6, 0),
(227, 'Calcium Carbonate', 6.25, 180, '2024-10-09 19:03:21', '2024-10-09 19:03:21', 6, 0),
(228, 'Hydrogen Peroxide', 7.99, 120, '2024-10-09 19:03:21', '2024-10-09 19:03:21', 6, 0),
(229, 'kjhgfd', 22.00, 22, '2025-04-06 23:57:13', '2025-04-06 23:57:13', 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `providers`
--

CREATE TABLE `providers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `providers`
--

INSERT INTO `providers` (`id`, `name`, `contact_info`, `address`) VALUES
(1, 'Acme Supplies', 'email: contact@acmesupplies.com, phone: +1-555-1234', '123 Industrial Way, New York, NY, USA'),
(2, 'Global Tech Solutions', 'email: info@globaltech.com, phone: +1-555-5678', '456 Innovation Drive, San Francisco, CA, USA'),
(3, 'EcoGoods Co.', 'email: sales@ecogoods.com, phone: +1-555-8765', '789 Green Street, Portland, OR, USA'),
(4, 'Omega Wholesale', 'email: support@omegawholesale.com, phone: +1-555-4321', '321 Warehouse Lane, Chicago, IL, USA'),
(5, 'Quantum Supplies', 'email: contact@quantumsupplies.com, phone: +1-555-9876', '654 Future Blvd, Austin, TX, USA'),
(6, 'mohamed ', '9f456789', 'skjdhfa;kjs ');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `generation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `report_type` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `password_hash`, `email`, `date_added`, `date_modified`, `is_admin`) VALUES
(29, 'Admin User', '$2y$10$PGmTruQD2abK3KYzZfP4IeQ0OhMyzlx5TeIcoKAuhYK/yeCj5ZbvS', 'admin@example.com', '2024-10-09 18:11:40', '2024-10-24 02:45:08', 1),
(30, 'Mohamed', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mohamed@example.com', '2024-10-09 18:11:40', '2024-10-24 02:45:43', 0),
(37, 'Admin Recovery', '$2y$10$PGmTruQD2abK3KYzZfP4IeQ0OhMyzlx5TeIcoKAuhYK/yeCj5ZbvS', 'admin@recovery.com', '2025-04-06 23:05:03', '2025-04-06 23:05:03', 1),
(38, 'mohamed', '$2y$10$fJl65UX2ZAD5Gk7M3dae4Oi9/nxPqh8P1gq4ykIsPsfArBLaQfHdK', 'mohamed@email.com', '2025-04-06 23:08:50', '2025-04-06 23:08:50', 0),
(39, 'hicham', '$2y$10$T3uNmHA218t6j8ymR6ambOIPUt/udfNzvKNi.vwdMnPoFPpUddxcy', 'hicham@email.com', '2025-04-06 23:11:29', '2025-04-06 23:11:29', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

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
  ADD PRIMARY KEY (`order_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Indexes for table `providers`
--
ALTER TABLE `providers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=230;

--
-- AUTO_INCREMENT for table `providers`
--
ALTER TABLE `providers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

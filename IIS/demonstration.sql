-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1:3306
-- Vytvořeno: Sob 23. lis 2024, 20:32
-- Verze serveru: 8.3.0
-- Verze PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `honeveg`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vypisuji data pro tabulku `categories`
--

INSERT INTO `categories` (`category_id`, `parent_category_id`, `name`) VALUES
(1, 1, 'base category'),
(2, 1, 'Vegetables'),
(3, 1, 'Fruits'),
(4, 1, 'Mushrooms'),
(5, 1, 'Herbs'),
(6, 2, 'Leafy vegetables'),
(7, 2, 'Root vegetables'),
(8, 2, 'Fruiting vegetables'),
(9, 3, 'Tree Fruits'),
(10, 3, 'Citruses'),
(11, 3, 'Berries'),
(12, 6, 'Lettuce'),
(13, 6, 'Spinach'),
(14, 7, 'Potato'),
(15, 7, 'Carrot'),
(16, 8, 'Tomatoes'),
(17, 8, 'EggPlants'),
(18, 9, 'Apples'),
(19, 10, 'Apples'),
(20, 10, 'Lemons'),
(21, 11, 'Strawberries'),
(22, 11, 'Currant'),
(23, 4, 'Champignons'),
(24, 4, 'Truffles');

-- --------------------------------------------------------

--
-- Struktura tabulky `change_categories_designs`
--

DROP TABLE IF EXISTS `change_categories_designs`;
CREATE TABLE IF NOT EXISTS `change_categories_designs` (
  `design_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `moderator_id` bigint UNSIGNED DEFAULT NULL,
  `creator_id` bigint UNSIGNED NOT NULL,
  `parent_category_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `creation_date` datetime NOT NULL,
  `close_date` datetime DEFAULT NULL,
  `status` enum('created','approved','declined') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'created',
  PRIMARY KEY (`design_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `design_labels`
--

DROP TABLE IF EXISTS `design_labels`;
CREATE TABLE IF NOT EXISTS `design_labels` (
  `design_label_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `design_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('number','text') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  PRIMARY KEY (`design_label_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `event_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` bigint UNSIGNED NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `event_participants`
--

DROP TABLE IF EXISTS `event_participants`;
CREATE TABLE IF NOT EXISTS `event_participants` (
  `event_participants_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`event_participants_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `event_products`
--

DROP TABLE IF EXISTS `event_products`;
CREATE TABLE IF NOT EXISTS `event_products` (
  `event_product_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`event_product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `labels`
--

DROP TABLE IF EXISTS `labels`;
CREATE TABLE IF NOT EXISTS `labels` (
  `label_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('number','text') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  PRIMARY KEY (`label_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vypisuji data pro tabulku `labels`
--

INSERT INTO `labels` (`label_id`, `category_id`, `name`, `type`) VALUES
(1, 1, 'price type', 'text'),
(2, 2, 'Production Method', 'text'),
(3, 3, 'Production Method', 'text'),
(4, 4, 'Wild/cultivated', 'text'),
(5, 5, 'Dry/fresh', 'text'),
(6, 6, 'Freshness duration in days', 'number'),
(7, 7, 'Sort', 'text'),
(8, 8, 'Sort', 'text'),
(9, 9, 'Sort', 'text'),
(10, 10, 'Seeds presence', 'text'),
(11, 11, 'Sweet/sour', 'text'),
(12, 11, 'Sort', 'text'),
(13, 12, 'Sort', 'text'),
(14, 13, 'Type', 'text'),
(15, 14, 'Type', 'text'),
(16, 15, 'Length in cm', 'number'),
(17, 16, 'Juicy/fleshy', 'text'),
(18, 16, 'Color', 'text'),
(19, 17, 'color', 'text'),
(20, 18, 'Sweet/sour', 'text'),
(21, 19, 'Sweet/sour', 'text'),
(22, 20, 'Weight in g', 'number'),
(23, 21, 'Juicy/firm', 'text'),
(24, 22, 'Color', 'text'),
(25, 23, 'Color', 'text'),
(26, 23, 'Fresh/pickled', 'text'),
(27, 24, 'Color', 'text');

-- --------------------------------------------------------

--
-- Struktura tabulky `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vypisuji data pro tabulku `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(3, '2024_09_27_223009_create_roles', 1),
(4, '2024_09_27_223510_create_products', 1),
(5, '2024_09_27_225203_create_events', 1),
(6, '2024_09_28_101227_create_categories', 1),
(7, '2024_09_28_102407_create_orders', 1),
(8, '2024_09_28_103536_create_ratings', 1),
(9, '2024_09_28_114922_create_product_label_values', 1),
(10, '2024_09_28_115457_create_order_product_lists', 1),
(11, '2024_09_28_115726_create_seller_orders', 1),
(12, '2024_09_28_115952_create_event_products', 1),
(13, '2024_09_28_120343_create_event_participants', 1),
(14, '2024_09_28_120438_create_change_categories_designs', 1),
(15, '2024_09_28_123812_create_labels', 1),
(16, '2024_10_05_222141_create_design_labels', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_user_id` bigint UNSIGNED NOT NULL,
  `creation_date` datetime NOT NULL,
  `close_date` datetime DEFAULT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `status` enum('cart','in process','canceled','delivered') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cart',
  PRIMARY KEY (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `order_product_lists`
--

DROP TABLE IF EXISTS `order_product_lists`;
CREATE TABLE IF NOT EXISTS `order_product_lists` (
  `order_product_list_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_amount` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`order_product_list_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `product_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_user_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `price` double(8,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `available_amount` int NOT NULL,
  `total_rating` double(8,2) NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vypisuji data pro tabulku `products`
--

INSERT INTO `products` (`product_id`, `seller_user_id`, `category_id`, `price`, `description`, `available_amount`, `total_rating`, `name`) VALUES
(1, 3, 12, 167.00, 'Occaecati reprehenderit voluptas consequatur.', 174966, 0.00, 'Iceberg lettuce'),
(2, 3, 12, 198.19, 'Odit ea vero alias pariatur molestiae molestiae.', 184, 0.00, 'Arugula lettuce'),
(3, 3, 13, 66.82, 'Esse molestiae omnis sint vero.', 45887, 0.00, 'Savoy spinach'),
(4, 3, 13, 143.66, 'Aperiam sunt ut nobis.', 5848, 0.00, 'Baby spinach'),
(5, 3, 14, 38.10, 'Qui est voluptatum id quis accusantium facere et.', 12207, 0.00, 'potato'),
(6, 3, 15, 129.00, 'Minima consequatur rerum consequuntur consequatur non.', 98097655, 0.00, 'Carrot'),
(7, 3, 16, 33.06, 'Odit molestias ut et rem rerum est minima.', 43892, 0.00, 'Tomatoes Roma'),
(8, 3, 16, 193.95, 'Beatae a doloribus odit aspernatur nihil laboriosam.', 89857, 0.00, 'Tomatoes Golden Queen'),
(9, 3, 17, 167.38, 'Sed voluptates odio quos recusandae incidunt ut illo.', 425, 0.00, 'Eggplants Rosita'),
(10, 3, 17, 140.30, 'Facere sequi corporis sit sed labore nihil sunt.', 4515545, 0.00, 'Eggplants white casper'),
(11, 3, 18, 72.02, 'Temporibus accusantium vel voluptas aut.', 38686, 0.00, 'Apples'),
(12, 3, 18, 133.90, 'Ea consectetur provident qui ad et.', 608721, 0.00, 'Apples'),
(13, 3, 19, 14.00, 'Nobis nobis voluptate nostrum omnis.', 853, 0.00, 'Oranges'),
(14, 3, 20, 154.50, 'Inventore consequuntur a error quod voluptatem.', 4918, 0.00, 'Lemons'),
(15, 3, 21, 195.41, 'Omnis ut consequatur quisquam impedit omnis.', 744, 0.00, 'Strawberries'),
(16, 3, 22, 173.75, 'Maiores cumque recusandae et eos ducimus quae molestiae id.', 825912, 0.00, 'Red currant'),
(17, 3, 23, 100.69, 'Adipisci neque labore repudiandae tempore.', 691, 0.00, 'Pickled champignons'),
(18, 3, 23, 166.00, 'Illo consectetur modi deleniti exercitationem veniam.', 944, 0.00, 'Champignons'),
(19, 3, 24, 167.40, 'Eligendi est dolore eveniet.', 78513194, 0.00, 'Truffles'),
(20, 3, 5, 84.94, 'Possimus labore iste temporibus consectetur hic omnis est nulla.', 10, 0.00, 'Oregano');

-- --------------------------------------------------------

--
-- Struktura tabulky `product_label_values`
--

DROP TABLE IF EXISTS `product_label_values`;
CREATE TABLE IF NOT EXISTS `product_label_values` (
  `product_label_values_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `label_id` bigint UNSIGNED NOT NULL,
  `label_value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`product_label_values_id`)
) ENGINE=MyISAM AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vypisuji data pro tabulku `product_label_values`
--

INSERT INTO `product_label_values` (`product_label_values_id`, `product_id`, `label_id`, `label_value`) VALUES
(1, 1, 1, 'piece'),
(2, 2, 1, 'piece'),
(3, 3, 1, '100 g'),
(4, 4, 1, '100 g'),
(5, 5, 1, '1 kg'),
(6, 6, 1, '1 kg'),
(7, 7, 1, '1 kg'),
(8, 8, 1, '1 kg'),
(9, 9, 1, 'piece'),
(10, 10, 1, 'piece'),
(11, 11, 1, '1 kg'),
(12, 12, 1, '1 kg'),
(13, 13, 1, 'piece'),
(14, 14, 1, 'piece'),
(15, 15, 1, '100 g'),
(16, 16, 1, '100 g'),
(17, 17, 1, 'piece'),
(18, 18, 1, '100 g'),
(19, 19, 1, '100 g'),
(20, 20, 1, '100 g'),
(21, 1, 2, 'Organic'),
(22, 2, 2, 'Organic'),
(23, 3, 2, 'Organic'),
(24, 4, 2, 'Organic'),
(25, 5, 2, 'Non-organic'),
(26, 6, 2, 'Organic'),
(27, 7, 2, 'Organic'),
(28, 8, 2, 'Organic'),
(29, 9, 2, 'Organic'),
(30, 10, 2, 'Organic'),
(31, 11, 3, 'Organic'),
(32, 12, 3, 'Organic'),
(33, 13, 3, 'Organic'),
(34, 14, 3, 'Organic'),
(35, 15, 3, 'Organic'),
(36, 16, 3, 'Organic'),
(37, 18, 4, 'Wild'),
(38, 17, 4, 'Cultivated'),
(39, 19, 4, 'Wild'),
(40, 20, 5, 'Dried'),
(41, 1, 6, '7'),
(42, 2, 6, '7'),
(43, 3, 6, '7'),
(44, 4, 6, '7'),
(45, 5, 7, 'Yukon Gold'),
(46, 6, 7, 'Imperator'),
(47, 7, 8, 'Roma'),
(48, 9, 8, 'Rosita'),
(49, 10, 8, 'White casper'),
(50, 11, 9, 'Red delicious'),
(51, 12, 9, 'Granny Smith'),
(52, 13, 10, 'Seedless'),
(53, 14, 10, 'Seeds'),
(54, 15, 11, 'Sweet'),
(55, 16, 11, 'Sweet'),
(56, 15, 12, 'Chandler'),
(57, 16, 12, 'Rovada'),
(58, 1, 13, 'Iceberg'),
(59, 2, 13, 'Arugula'),
(60, 3, 14, 'Savoy'),
(61, 4, 14, 'Baby'),
(62, 5, 15, 'type A'),
(63, 1, 16, '20'),
(64, 7, 17, 'Juicy'),
(65, 8, 17, 'Juicy'),
(66, 7, 18, 'Red'),
(67, 8, 18, 'Yellow'),
(68, 9, 19, 'Lilac'),
(69, 10, 19, 'White'),
(70, 11, 20, 'Sweet'),
(71, 12, 20, 'Sour'),
(72, 13, 21, 'Sweet'),
(73, 14, 22, '80'),
(74, 15, 23, 'Juicy'),
(75, 16, 24, 'Black'),
(76, 18, 25, 'White'),
(77, 17, 25, ''),
(78, 18, 26, 'Fresh'),
(79, 17, 26, 'Pickled'),
(80, 19, 27, 'Black');

-- --------------------------------------------------------

--
-- Struktura tabulky `ratings`
--

DROP TABLE IF EXISTS `ratings`;
CREATE TABLE IF NOT EXISTS `ratings` (
  `rating_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `rating` double(8,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`rating_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `role` enum('admin','moderator','user','seller','suspended') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vypisuji data pro tabulku `roles`
--

INSERT INTO `roles` (`role_id`, `user_id`, `role`) VALUES
(1, 1, 'admin'),
(2, 2, 'moderator'),
(3, 3, 'seller'),
(4, 4, 'user');

-- --------------------------------------------------------

--
-- Struktura tabulky `seller_orders`
--

DROP TABLE IF EXISTS `seller_orders`;
CREATE TABLE IF NOT EXISTS `seller_orders` (
  `seller_order_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `status` enum('accepted','canceled','delivered') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'accepted',
  PRIMARY KEY (`seller_order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(65) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `remember_token`) VALUES
(1, 'Kristian Faiz Santiago', 'admin@admin.com', '$2y$12$n2eUwgAA4MdKVzXw.eRM1uLo9rqJZ5lbPJ7yq3ao2UW2t2p9lY.OO', '3RJMD1VFn7'),
(2, 'Dana Omphile Mullen', 'moder@moder.com', '$2y$12$n2eUwgAA4MdKVzXw.eRM1uLo9rqJZ5lbPJ7yq3ao2UW2t2p9lY.OO', 'J4CZa9R89z'),
(3, 'Agathinos Baar', 'prodavac@prodavac.com', '$2y$12$n2eUwgAA4MdKVzXw.eRM1uLo9rqJZ5lbPJ7yq3ao2UW2t2p9lY.OO', '043ZWCVuQq'),
(4, 'Kelvin Carter', 'franta@franta.com', '$2y$12$n2eUwgAA4MdKVzXw.eRM1uLo9rqJZ5lbPJ7yq3ao2UW2t2p9lY.OO', '3kbzYfs5uI');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

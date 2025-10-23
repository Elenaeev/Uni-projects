-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1:3306
-- Vytvořeno: Sob 23. lis 2024, 13:35
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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vypisuji data pro tabulku `categories`
--

INSERT INTO `categories` (`category_id`, `parent_category_id`, `name`) VALUES
(1, 1, 'base category'),
(2, 1, 'Vegetables'),
(3, 1, 'Fruits'),
(4, 1, 'Mushrooms'),
(5, 1, 'Herbs');

-- --------------------------------------------------------

--
-- Struktura tabulky `change_categories_designs`
--

DROP TABLE IF EXISTS `change_categories_designs`;
CREATE TABLE IF NOT EXISTS `change_categories_designs` (
  `design_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `moderator_id` bigint UNSIGNED DEFAULT NULL,
  `creator_id` bigint UNSIGNED NOT NULL,
  `parent_category_id` bigint UNSIGNED NOT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vypisuji data pro tabulku `labels`
--

INSERT INTO `labels` (`label_id`, `category_id`, `name`, `type`) VALUES
(1, 1, 'price type', 'text'),
(2, 2, 'Production Method', 'text'),
(3, 3, 'Production Method', 'text'),
(4, 4, 'Wild/cultivated', 'text'),
(5, 5, 'Dry/fresh', 'text');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'Kristian Faiz Santiago', 'admin@admin.com', '$2y$12$bRM6QII5HGwH0jivz7LmIu/wQ.6OzpF..zxooDKpsMpZTDkLfRHQG', 'QPLSvTBU2uIgpa4OVPendZziYmyD2iqpx28D3ldPBtgd5cKw2bZXC71AJNZH'),
(2, 'Dana Omphile Mullen', 'moder@moder.com', '$2y$12$bRM6QII5HGwH0jivz7LmIu/wQ.6OzpF..zxooDKpsMpZTDkLfRHQG', 'luMiiFPfOv'),
(3, 'Agathinos Baar', 'prodavac@prodavac.com', '$2y$12$bRM6QII5HGwH0jivz7LmIu/wQ.6OzpF..zxooDKpsMpZTDkLfRHQG', 'UW7ShwSfEh'),
(4, 'Kelvin Carter', 'franta@franta.com', '$2y$12$bRM6QII5HGwH0jivz7LmIu/wQ.6OzpF..zxooDKpsMpZTDkLfRHQG', 'yc3yzUNzV5');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

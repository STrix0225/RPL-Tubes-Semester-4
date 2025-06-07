-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for gems_web
CREATE DATABASE IF NOT EXISTS `gems_web` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `gems_web`;

-- Dumping structure for table gems_web.admins
CREATE TABLE IF NOT EXISTS `admins` (
  `admin_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(100) NOT NULL,
  `admin_email` varchar(100) NOT NULL,
  `admin_phone` varchar(15) DEFAULT NULL,
  `admin_password` varchar(100) NOT NULL,
  `admin_photo` varchar(255) NOT NULL,
  `admin_photo2` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Dumping data for table gems_web.admins: ~1 rows (approximately)
INSERT INTO `admins` (`admin_id`, `admin_name`, `admin_email`, `admin_phone`, `admin_password`, `admin_photo`, `admin_photo2`) VALUES
	(1, 'Khaleed', 'admin@gmail.com', '62897765432', '0287040c474dbf44cdeb17eebb99d828', 'admin_profile.jpg', 'admin_profile2.jpg');

-- Dumping structure for table gems_web.blogs
CREATE TABLE IF NOT EXISTS `blogs` (
  `blog_id` int(11) NOT NULL AUTO_INCREMENT,
  `blog_title` varchar(100) DEFAULT NULL,
  `blog_description` text DEFAULT NULL,
  `blog_quotes` text DEFAULT NULL,
  `blog_quotes_writer` varchar(100) DEFAULT NULL,
  `blog_image` varchar(100) DEFAULT NULL,
  `blog_image2` varchar(100) DEFAULT NULL,
  `blog_tags` varchar(255) DEFAULT NULL,
  `blog_date` date DEFAULT current_timestamp(),
  `admin_id` int(11) NOT NULL,
  PRIMARY KEY (`blog_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


-- Dumping structure for table gems_web.customers
CREATE TABLE IF NOT EXISTS `customers` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_password` varchar(100) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_address` varchar(255) DEFAULT NULL,
  `customer_city` varchar(100) DEFAULT NULL,
  `customer_photo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping structure for table gems_web.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_cost` decimal(10,2) NOT NULL,
  `order_status` varchar(100) NOT NULL DEFAULT 'on_hold',
  `customer_id` int(11) NOT NULL,
  `customer_phone` varchar(15) NOT NULL,
  `customer_city` varchar(255) NOT NULL,
  `customer_address` varchar(255) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping structure for table gems_web.order_items
CREATE TABLE IF NOT EXISTS `order_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


-- Dumping structure for table gems_web.payments
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_id` varchar(250) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


-- Dumping structure for table gems_web.products
CREATE TABLE IF NOT EXISTS `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(100) DEFAULT NULL,
  `product_brand` varchar(100) DEFAULT NULL,
  `product_category` varchar(100) DEFAULT NULL,
  `product_description` varchar(255) DEFAULT NULL,
  `product_criteria` varchar(50) DEFAULT NULL,
  `product_image1` varchar(100) DEFAULT NULL,
  `product_image2` varchar(100) DEFAULT NULL,
  `product_image3` varchar(100) DEFAULT NULL,
  `product_image4` varchar(100) DEFAULT NULL,
  `product_price` decimal(10,2) DEFAULT NULL,
  `product_discount` decimal(10,2) DEFAULT NULL,
  `product_color` varchar(50) DEFAULT NULL,
  `product_sold` int(11) DEFAULT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table gems_web.products: ~5 rows (approximately)
INSERT INTO `products` (`product_id`, `product_name`, `product_brand`, `product_category`, `product_description`, `product_criteria`, `product_image1`, `product_image2`, `product_image3`, `product_image4`, `product_price`, `product_discount`, `product_color`, `product_sold`) VALUES
	(1, 'Nitro 5 Pro', 'ACER', 'Laptop', 'usgfuipbaougbuaoicbuaouyheiaohcabouahfoia', 'New', 'ACER2.jpeg', NULL, NULL, NULL, 1000.00, 20.00, NULL, 1),
	(2, 'J11', 'SAMSUNG', 'Handphone', 'dsuigsughsoiioshfiosdadadwagucuaca', 'Non', 'product2.jpeg', NULL, NULL, NULL, 1000.00, 10.00, NULL, 3),
	(3, 'ZT95', 'ROG', 'Laptop', 'u9sgh9f8sghvuobouishvsvssfsers', 'New', 'ROG2.jpeg', NULL, NULL, NULL, 1500.00, 50.00, NULL, 2),
	(4, 'IPhone 14', 'Apple', 'Handphone', 'idusgbu9sofusabfuofabfouah', 'New', 'product1.jpeg', NULL, NULL, NULL, 2000.00, 2.00, NULL, 4),
	(5, 'Hetzer', 'LADA', 'Accessories', 'dfghjkl;adeawtghyjumadsadwa', 'Non', 'Headphone1.webp', NULL, NULL, NULL, 600.00, NULL, NULL, 2);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

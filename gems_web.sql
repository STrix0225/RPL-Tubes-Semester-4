-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
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
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(50) NOT NULL DEFAULT '0',
  `admin_email` varchar(50) NOT NULL,
  `admin_phone` varchar(50) NOT NULL,
  `admin_password` varchar(50) NOT NULL,
  `admin_photo` varchar(50) DEFAULT NULL,
  `admin_photo2` varchar(50) DEFAULT '0',
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table gems_web.admins: ~2 rows (approximately)
REPLACE INTO `admins` (`admin_id`, `admin_name`, `admin_email`, `admin_phone`, `admin_password`, `admin_photo`, `admin_photo2`) VALUES
	(4, 'adika haikal', 'haikaladika272@gmail.com', '1231124124', 'e10adc3949ba59abbe56e057f20f883e', 'adika.jpg', '0'),
	(5, 'agus herman', 'admin@gmail.com', '08131212121', 'e10adc3949ba59abbe56e057f20f883e', 'agus.jpg', '0');

-- Dumping structure for table gems_web.customers
CREATE TABLE IF NOT EXISTS `customers` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(256) NOT NULL,
  `customer_email` varchar(50) NOT NULL,
  `customer_password` varchar(40) NOT NULL,
  `customer_phone` varchar(50) DEFAULT NULL,
  `customer_address` varchar(50) DEFAULT NULL,
  `customer_city` varchar(50) DEFAULT NULL,
  `customer_photo` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table gems_web.customers: ~1 rows (approximately)
REPLACE INTO `customers` (`customer_id`, `customer_name`, `customer_email`, `customer_password`, `customer_phone`, `customer_address`, `customer_city`, `customer_photo`) VALUES
	(2, 'Adika haikal', 'adika@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0812345678', 'Ciguruwik', 'Bandung', 'default.jpg');

-- Dumping structure for table gems_web.product
CREATE TABLE IF NOT EXISTS `product` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(100) NOT NULL,
  `product_brand` varchar(100) NOT NULL,
  `product_category` varchar(100) DEFAULT NULL,
  `product_description` varchar(256) NOT NULL,
  `product_photo1` varchar(100) NOT NULL,
  `product_photo2` varchar(100) NOT NULL,
  `product_photo3` varchar(100) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_color` varchar(50) NOT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table gems_web.product: ~2 rows (approximately)
REPLACE INTO `product` (`product_id`, `product_name`, `product_brand`, `product_category`, `product_description`, `product_photo1`, `product_photo2`, `product_photo3`, `product_price`, `product_color`) VALUES
	(1, 'Redmi 9A', 'XIAOMI', 'Handphone', 'Hape satu jutaan', 'redmi9A.jpeg', 'redmi9A2.jpeg', 'Redmi_9A3.jpg', 1000000.00, 'Ireng'),
	(8, 'J2 prime', 'Samsung', 'Handphone', 'BIsa buat main genshin', 'J2_prime.1jpg', 'J2_prime.2jpg', 'J2_prime.3jspg', 1200000.00, 'Silver');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

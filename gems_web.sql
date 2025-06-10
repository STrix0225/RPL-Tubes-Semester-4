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
DELETE FROM `admins`;
INSERT INTO `admins` (`admin_id`, `admin_name`, `admin_email`, `admin_phone`, `admin_password`, `admin_photo`, `admin_photo2`) VALUES
	(1, 'Khaleed', 'admin@gmail.com', '62897765432', '25d55ad283aa400af464c76d713c07ad', 'admin_profile.jpg', 'admin_profile2.jpg');

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

-- Dumping data for table gems_web.blogs: ~29 rows (approximately)
DELETE FROM `blogs`;
INSERT INTO `blogs` (`blog_id`, `blog_title`, `blog_description`, `blog_quotes`, `blog_quotes_writer`, `blog_image`, `blog_image2`, `blog_tags`, `blog_date`, `admin_id`) VALUES
	(1, 'What Curling Irons Are The Best Ones', 'Spesifikasi Technoplast GB-200:\nTerbuat dari bahan plastik berkualitas( Food Grade), serta bebas dari bahan-bahan berbahaya(BPA free)\n\nTersedia dalam warna hitam, biru, merah\n\nTumbler ini sangat cocok untuk kalian yang ingin memberikan souvenir perusahaan, hadiah wisuda, ulang tahun atau bahkan sebagai koleksi pribadi.\n\nKalian bisa menggunakan desain buatan kalian sendiri untuk botol minum kalian nih. Caranya dengan\n1. order via chat Whatsapp\n2. Kirim desain kalian via email mfbinary18@gmail.com.\n3. Desain yang kalian kirim hanya dalam bentuk .eps ya\n', '“When designing an advertisement for a particular product many things should be researched like where it should be displayed.”', 'JOHN SMITH', 'blog-1.jpg', 'banner1.jpg', '#Poster #Tumbler #2022\n', '2022-11-04', 1),
	(2, 'Eternity Bands Do Last Forever', 'Spesifikasi Technoplast GS-400:\nTerbuat dari bahan plastik berkualitas( Food Grade), serta bebas dari bahan-bahan berbahaya(BPA free)\n\nTersedia dalam warna Hitam\n\nTumbler ini sangat cocok untuk kalian yang ingin memberikan souvenir perusahaan, hadiah wisuda, ulang tahun atau bahkan sebagai koleksi pribadi.\n\nKalian bisa menggunakan desain buatan kalian sendiri untuk botol minum kalian nih. Caranya dengan\n1. order via chat Whatsapp\n2. Kirim desain kalian via email mfbinary18@gmail.com.\n3. Desain yang kalian kirim hanya dalam bentuk .eps ya\n', '“When designing an advertisement for a particular product many things should be researched like where it should be displayed.”', 'JOHN SMITH', 'blog-2.jpg', 'banner1.jpg', '#Poster #Tumbler #Edukasi #2022\n', '2022-11-05', 1),
	(3, 'The Health Benefits Of Sunglasses', 'Miliki sekarang Tumbler Custom GS-400 2 side.\nDimensi:\nVolume 370 ml\nTinggi 17.1 cm\nLebar 7.5 cm', '“When designing an advertisement for a particular product many things should be researched like where it should be displayed.”', 'JOHN SMITH', 'blog-3.jpg', 'banner1.jpg', '#Tumbler', '2022-11-05', 1),
	(4, 'Aiming For Higher The Mastopexy', 'Tu kan Lucu banget, ayo meriahkan hari spesial sahabat mu dengan order sekarang juga tumbler cantik ini di @mfbinary ya', '“When designing an advertisement for a particular product many things should be researched like where it should be displayed.”', 'JOHN SMITH', 'blog-4.jpg', 'banner1.jpg', '#Tumbler', '2022-11-05', 1),
	(5, 'Wedding Rings A Gift For A Lifetime', '<p>Hydroderm is the highly desired anti-aging cream on the block. This serum restricts the\n occurrence of early aging sings on the skin and keeps the skin younger, tighter and\n healthier. It reduces the wrinkles and loosening of skin. This cream nourishes the skin\n and brings back the glow that had lost in the run of hectic years.</p>\n <p>The most essential ingredient that makes hydroderm so effective is Vyo-Serum, which is a\n product of natural selected proteins. This concentrate works actively in bringing about\n the natural youthful glow of the skin. It tightens the skin along with its moisturizing\n effect on the skin. The other important ingredient, making hydroderm so effective is\n “marine collagen” which along with Vyo-Serum helps revitalize the skin.</p>', '“When designing an advertisement for a particular product many things should be researched like where it should be displayed.”', 'JOHN SMITH', 'blog-5.jpg', 'banner1.jpg', '#Poster #Tumbler #Edukasi #2022\n', '2022-11-05', 1),
	(6, 'The Different Methods Of Hair Removal', 'Kamu bisa beli satuan ya. No min. Order.\r\nJadikan hari spesial keluargamu semakin berkesan, dengan hadiah spesial dari kamu nih.', '“When designing an advertisement for a particular product many things should be researched like where it should be displayed.”', 'JOHN SMITH', 'blog-6.jpg', 'banner1.jpg', '#Tumbler #Quotes', '2022-11-05', 1),
	(7, 'Hoop Earrings A Style From History', 'Ada juga ni pulpen dengan desain unik dan lucu, bisa custom sesuai keinginan kamu, dan cocok banget ni buat hadiah perpisahan sekolah.', '“When designing an advertisement for a particular product many things should be researched like where it should be displayed.”', 'JOHN SMITH', 'blog-7.jpg', 'banner1.jpg', '#Pulpen', '2022-11-05', 1),
	(8, 'Lasik Eye Surgery Are You Ready', 'Kalender dan Poster Edukasi bisa kamu dapatkan dengan harga terjangkan, untuk desainnya bisa kamu tentukan sendiri loh.., ayo! tunggu apa lagi untuk berkunjung ke toko kami @mfbinary', '“When designing an advertisement for a particular product many things should be researched like where it should be displayed.”', 'JOHN SMITH', 'blog-8.jpg', 'banner1.jpg', '#Poster #Tumbler #Edukasi #2022\n', '2022-11-05', 1),
	(9, 'Enjoying Beautiful Scarf', 'Khusus poster edukasi lagi diskon gede-gedean ini sampe 50%, kamu jangan sampai ketinggalan, promonya hanya bulan ini saja, ayo tunggu apa lagi :D.', '“When designing an advertisement for a particular product many things should be researched like where it should be displayed.”', 'JOHN SMITH', 'blog-9.jpg', 'banner1.jpg', '#Poster', '2022-11-05', 1),
	(10, 'Artikel 1', 'Deskripsi artikel 1', 'Quote menarik 1', 'Daniel West', 'blog1.jpg', NULL, 'tag1, tag2, tag1', '2025-01-25', 1),
	(11, 'Artikel 2', 'Deskripsi artikel 2', 'Quote menarik 2', 'Jillian Robinson', 'blog2.jpg', NULL, 'tag1, tag2, tag2', '2024-09-11', 1),
	(12, 'Artikel 3', 'Deskripsi artikel 3', 'Quote menarik 3', 'Lisa Barrett', 'blog3.jpg', NULL, 'tag1, tag2, tag3', '2023-06-12', 1),
	(13, 'Artikel 4', 'Deskripsi artikel 4', 'Quote menarik 4', 'Lee Marshall', 'blog4.jpg', NULL, 'tag1, tag2, tag4', '2023-11-01', 1),
	(14, 'Artikel 5', 'Deskripsi artikel 5', 'Quote menarik 5', 'Jonathan Bolton', 'blog5.jpg', NULL, 'tag1, tag2, tag5', '2025-04-13', 1),
	(15, 'Artikel 6', 'Deskripsi artikel 6', 'Quote menarik 6', 'Linda Bailey', 'blog6.jpg', NULL, 'tag1, tag2, tag6', '2024-04-21', 1),
	(16, 'Artikel 7', 'Deskripsi artikel 7', 'Quote menarik 7', 'Breanna Jefferson', 'blog7.jpg', NULL, 'tag1, tag2, tag7', '2023-08-12', 1),
	(17, 'Artikel 8', 'Deskripsi artikel 8', 'Quote menarik 8', 'Maria Ramirez', 'blog8.jpg', NULL, 'tag1, tag2, tag8', '2024-06-16', 1),
	(18, 'Artikel 9', 'Deskripsi artikel 9', 'Quote menarik 9', 'Thomas Cobb', 'blog9.jpg', NULL, 'tag1, tag2, tag9', '2024-08-23', 1),
	(19, 'Artikel 10', 'Deskripsi artikel 10', 'Quote menarik 10', 'Stephanie Mueller', 'blog10.jpg', NULL, 'tag1, tag2, tag10', '2023-07-08', 1),
	(20, 'Artikel 11', 'Deskripsi artikel 11', 'Quote menarik 11', 'George White', 'blog11.jpg', NULL, 'tag1, tag2, tag11', '2023-08-25', 1),
	(21, 'Artikel 12', 'Deskripsi artikel 12', 'Quote menarik 12', 'Patrick Hogan', 'blog12.jpg', NULL, 'tag1, tag2, tag12', '2024-03-18', 1),
	(22, 'Artikel 13', 'Deskripsi artikel 13', 'Quote menarik 13', 'Amber Salinas', 'blog13.jpg', NULL, 'tag1, tag2, tag13', '2024-10-21', 1),
	(23, 'Artikel 14', 'Deskripsi artikel 14', 'Quote menarik 14', 'Amy Allen', 'blog14.jpg', NULL, 'tag1, tag2, tag14', '2024-11-18', 1),
	(24, 'Artikel 15', 'Deskripsi artikel 15', 'Quote menarik 15', 'Ashley Cummings', 'blog15.jpg', NULL, 'tag1, tag2, tag15', '2023-08-03', 1),
	(25, 'Artikel 16', 'Deskripsi artikel 16', 'Quote menarik 16', 'Janet Phillips', 'blog16.jpg', NULL, 'tag1, tag2, tag16', '2023-12-03', 1),
	(26, 'Artikel 17', 'Deskripsi artikel 17', 'Quote menarik 17', 'Trevor Newman', 'blog17.jpg', NULL, 'tag1, tag2, tag17', '2023-07-21', 1),
	(27, 'Artikel 18', 'Deskripsi artikel 18', 'Quote menarik 18', 'Belinda Johnson', 'blog18.jpg', NULL, 'tag1, tag2, tag18', '2025-04-27', 1),
	(28, 'Artikel 19', 'Deskripsi artikel 19', 'Quote menarik 19', 'Robin Davis', 'blog19.jpg', NULL, 'tag1, tag2, tag19', '2023-08-20', 1),
	(29, 'Artikel 20', 'Deskripsi artikel 20', 'Quote menarik 20', 'Sarah Turner', 'blog20.jpg', NULL, 'tag1, tag2, tag20', '2023-07-02', 1);

-- Dumping structure for table gems_web.customers
CREATE TABLE IF NOT EXISTS `customers` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_password` varchar(100) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_city` varchar(255) DEFAULT NULL,
  `customer_address` varchar(100) DEFAULT NULL,
  `customer_photo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table gems_web.customers: ~3 rows (approximately)
DELETE FROM `customers`;
INSERT INTO `customers` (`customer_id`, `customer_name`, `customer_email`, `customer_password`, `customer_phone`, `customer_city`, `customer_address`, `customer_photo`) VALUES
	(1, 'Adika', 'Adika@gmail.com', '25d55ad283aa400af464c76d713c07ad', '08996806320', 'Bandung', 'Ciguruwik', 'muka.jpg'),
	(32, 'Lanz Alexander', 'Lanz@gmail.com', '$2y$10$6MtCG8UKKU4qYmUhW708lO7TVUc3zf3J5rmNPORGT5V58C/KpfJ1K', '0895411811612', 'Buitenzorg', 'JlCikutrano90', 'muka.jpg'),
	(33, 'Haikal', 'Haikal@gmail.com', '$2y$10$orn6Gidrd.9s/l3A/eMiX.1RqI4SiHEF1s9a/Tpi1UhyQJgp/insK', '08996806320', 'Bandung', 'Jl. Soekarno Hatta No.KM. 10, Jatisari, Kec. Buahbatu, Kota Bandung, Jawa Barat', 'uploads/6846d7b859d5e.jpeg');

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
  `payment_method` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table gems_web.orders: ~15 rows (approximately)
DELETE FROM `orders`;
INSERT INTO `orders` (`order_id`, `order_cost`, `order_status`, `customer_id`, `customer_phone`, `customer_city`, `customer_address`, `order_date`, `payment_method`) VALUES
	(25, 6460.00, 'pending', 32, '0895411811612', 'Buitenzorg', 'JlCikutrano90', '2025-06-01 23:28:34', 'paypal'),
	(26, 900.00, 'cancelled', 32, '0895411811612', 'Buitenzorg', 'JlCikutrano90', '2025-06-02 00:09:40', 'paypal'),
	(27, 600.00, 'completed', 32, '0895411811612', 'Buitenzorg', 'JlCikutrano90', '2025-06-02 00:11:01', 'paypal'),
	(28, 600.00, 'completed', 32, '0895411811612', 'Buitenzorg', 'JlCikutrano90', '2025-06-03 07:25:58', 'paypal'),
	(29, 22800.00, 'pending', 33, '08996806320', 'Bandung', 'Jl. Soekarno Hatta No.KM. 10, Jatisari, Kec. Buahbatu, Kota Bandung, Jawa Barat', '2025-06-09 15:01:54', 'paypal'),
	(30, 22800.00, 'pending', 33, '08996806320', 'Bandung', 'Jl. Soekarno Hatta No.KM. 10, Jatisari, Kec. Buahbatu, Kota Bandung, Jawa Barat', '2025-06-09 15:02:42', 'paypal'),
	(31, 22800.00, 'pending', 33, '08996806320', 'Bandung', 'Jl. Soekarno Hatta No.KM. 10, Jatisari, Kec. Buahbatu, Kota Bandung, Jawa Barat', '2025-06-09 15:04:17', 'paypal'),
	(32, 22800.00, 'pending', 33, '08996806320', 'Bandung', 'Jl. Soekarno Hatta No.KM. 10, Jatisari, Kec. Buahbatu, Kota Bandung, Jawa Barat', '2025-06-09 15:05:20', 'paypal'),
	(33, 22800.00, 'pending', 33, '08996806320', 'Bandung', 'Jl. Soekarno Hatta No.KM. 10, Jatisari, Kec. Buahbatu, Kota Bandung, Jawa Barat', '2025-06-09 15:06:13', 'paypal'),
	(34, 22800.00, 'pending', 33, '08996806320', 'Bandung', 'Jl. Soekarno Hatta No.KM. 10, Jatisari, Kec. Buahbatu, Kota Bandung, Jawa Barat', '2025-06-09 15:07:44', 'paypal'),
	(35, 22800.00, 'pending', 33, '08996806320', 'Bandung', 'Jl. Soekarno Hatta No.KM. 10, Jatisari, Kec. Buahbatu, Kota Bandung, Jawa Barat', '2025-06-09 15:10:55', 'paypal'),
	(36, 22800.00, 'pending', 33, '08996806320', 'Bandung', 'Jl. Soekarno Hatta No.KM. 10, Jatisari, Kec. Buahbatu, Kota Bandung, Jawa Barat', '2025-06-09 15:15:08', 'paypal'),
	(37, 22800.00, 'pending', 33, '08996806320', 'Bandung', 'Jl. Soekarno Hatta No.KM. 10, Jatisari, Kec. Buahbatu, Kota Bandung, Jawa Barat', '2025-06-09 15:19:15', 'paypal'),
	(38, 24734.06, 'pending', 33, '08996806320', 'Bandung', 'Jl. Soekarno Hatta No.KM. 10, Jatisari, Kec. Buahbatu, Kota Bandung, Jawa Barat', '2025-06-09 16:08:32', 'paypal'),
	(39, 22800.00, 'pending', 33, '08996806320', 'Bandung', 'Jl. Soekarno Hatta No.KM. 10, Jatisari, Kec. Buahbatu, Kota Bandung, Jawa Barat', '2025-06-09 16:20:01', 'paypal');

-- Dumping structure for table gems_web.order_items
CREATE TABLE IF NOT EXISTS `order_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `product_color` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table gems_web.order_items: ~12 rows (approximately)
DELETE FROM `order_items`;
INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `product_name`, `product_image`, `product_price`, `product_quantity`, `customer_id`, `order_date`, `product_color`) VALUES
	(27, 25, '2', 'J11', 'product2.jpeg', 900.00, 1, 32, '2025-06-01 23:28:34', NULL),
	(28, 25, '4', 'IPhone 14', 'product1.jpeg', 780.00, 2, 32, '2025-06-01 23:28:34', NULL),
	(29, 25, '5', 'Hetzer', 'Headphone1.webp', 600.00, 4, 32, '2025-06-01 23:28:34', NULL),
	(30, 25, '6', 'Funiculi ', 'ACER2.jpeg', 1600.00, 1, 32, '2025-06-01 23:28:34', NULL),
	(31, 26, '2', 'J11', 'product2.jpeg', 900.00, 1, 32, '2025-06-02 00:09:40', NULL),
	(32, 27, '5', 'Hetzer', 'Headphone1.webp', 600.00, 1, 32, '2025-06-02 00:11:01', NULL),
	(33, 28, '5', 'Hetzer', 'Headphone1.webp', 600.00, 1, 32, '2025-06-03 07:25:58', NULL),
	(34, 37, '47', 'MacBook Air M3', 'macbook_air_m31.jpeg', 22800.00, 1, 33, '2025-06-09 15:19:15', 'black'),
	(35, 38, '15', 'Acer Swift GO 14 AI OLED EVO SFG14 73 56A7', 'acerswift1.png', 1151.04, 1, 33, '2025-06-09 16:08:32', 'Grey'),
	(36, 38, '21', 'Xiaomi 15 Ultra', 'xiaomi15ultra1.png', 783.02, 1, 33, '2025-06-09 16:08:32', 'Grey'),
	(37, 38, '47', 'MacBook Air M3', 'macbook_air_m31.jpeg', 22800.00, 1, 33, '2025-06-09 16:08:32', 'blue'),
	(38, 39, '47', 'MacBook Air M3', 'macbook_air_m31.jpeg', 22800.00, 1, 33, '2025-06-09 16:20:01', 'purple');

-- Dumping structure for table gems_web.order_stock
CREATE TABLE IF NOT EXISTS `order_stock` (
  `id_stock` int(11) NOT NULL AUTO_INCREMENT,
  `id_supplier` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `product_brand` varchar(100) DEFAULT NULL,
  `product_category` varchar(100) DEFAULT NULL,
  `product_color` varchar(50) DEFAULT NULL,
  `product_qty` int(11) DEFAULT NULL,
  `product_ori_price` decimal(10,2) DEFAULT NULL,
  `total_restock_price` decimal(10,2) DEFAULT NULL,
  `order_date` date DEFAULT curdate(),
  `status_order` varchar(20) DEFAULT 'pending',
  `catatan` text DEFAULT NULL,
  PRIMARY KEY (`id_stock`),
  KEY `id_supplier` (`id_supplier`),
  CONSTRAINT `order_stock_ibfk_1` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table gems_web.order_stock: ~3 rows (approximately)
DELETE FROM `order_stock`;
INSERT INTO `order_stock` (`id_stock`, `id_supplier`, `product_id`, `product_name`, `product_brand`, `product_category`, `product_color`, `product_qty`, `product_ori_price`, `total_restock_price`, `order_date`, `status_order`, `catatan`) VALUES
	(2, 1, 32, 'HP-15 db006wm', 'HP', 'Laptop', 'Black', 10, 5000600.00, 50006000.00, '2025-06-07', 'processing', 'Pajak 20%'),
	(4, 2, 34, 'MacBook Air M3', 'Apple', 'Laptop', 'white', 10, 24999000.00, 99999999.99, '2025-06-07', 'completed', 'Gaji bulanan dipotong'),
	(5, 2, 34, 'MacBook Air M3', 'Apple', 'Laptop', 'white', 1, 99999999.99, 99999999.99, '2025-06-09', 'pending', 'Update Harga Nambah 1 Stok');

-- Dumping structure for table gems_web.payments
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_id` varchar(250) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table gems_web.payments: ~22 rows (approximately)
DELETE FROM `payments`;
INSERT INTO `payments` (`payment_id`, `order_id`, `user_id`, `transaction_id`, `payment_date`) VALUES
	(0, 1, 1, '5GF373104L5481926', '2023-03-12 23:47:35'),
	(0, 3, 1, '4EA15211WD712624R', '2023-03-19 13:03:05'),
	(1, 9, 2, '23c98d7a-a385-47ce-b9c6-e7dc6e5f8b83', '2023-09-15 12:09:44'),
	(2, 4, 7, '8d95ea49-85dd-4d4b-8a5b-770ff33c14cf', '2024-09-23 15:41:14'),
	(3, 13, 5, '5bcb2ab2-b77e-490f-96cc-52a76f803c7d', '2024-05-28 13:42:50'),
	(4, 17, 11, 'd1039ac9-9e26-4b7f-b3a2-3eb38e0fd5aa', '2025-04-12 02:36:03'),
	(5, 10, 15, '073cd915-97c4-4e93-80e3-479bc8c72a36', '2025-04-23 00:23:53'),
	(6, 12, 13, '4f6a1aa0-e056-4505-9af1-5fe7ec2d63c3', '2023-07-09 04:55:26'),
	(7, 10, 10, '6c870ed0-1daf-41d2-936c-f7663f9bfc6c', '2023-12-20 05:32:18'),
	(8, 5, 12, '9c610e37-e96d-4cc1-8464-18b5ab2829e2', '2023-11-17 06:56:17'),
	(9, 13, 6, 'c2091e3a-f055-4fc9-8aed-561c2b9779bc', '2025-03-29 14:58:45'),
	(10, 11, 6, '795a4ecc-0f04-4b62-96f5-5e1bc3df4feb', '2024-08-23 22:15:35'),
	(11, 18, 9, 'bdaddde6-4da5-4e9d-ad12-b82ab9759769', '2024-03-08 07:17:21'),
	(12, 17, 6, 'e2d4a640-e229-4bd4-9a98-dc8b62ad8c53', '2025-01-07 20:38:25'),
	(13, 3, 12, '46865013-e900-4a85-858f-5883a540e5d9', '2023-06-23 21:26:21'),
	(14, 3, 5, 'b3708dc3-79fa-4806-b12b-54fb06385208', '2023-08-29 04:28:45'),
	(15, 15, 4, '07df2c7e-36ec-4036-adce-07c53cebd371', '2024-09-19 09:07:01'),
	(16, 8, 5, '9b56d4ec-c1c4-4413-b6d4-781266002c11', '2024-05-15 02:35:49'),
	(17, 12, 13, '4fc4d02d-2d3c-40fa-b433-593529e2d837', '2024-10-22 17:21:30'),
	(18, 16, 18, '5e9a0e26-01bd-4865-a7c1-3d61ad90c9ff', '2024-07-27 10:32:08'),
	(19, 4, 16, 'f1022a34-93cc-456f-95f4-511f77ff38e4', '2023-09-21 00:04:17'),
	(20, 16, 16, 'b2353c79-1d43-4be1-9ff9-029dbe525c1e', '2024-08-06 06:22:59');

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
  `product_qty` int(11) DEFAULT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table gems_web.products: ~22 rows (approximately)
DELETE FROM `products`;
INSERT INTO `products` (`product_id`, `product_name`, `product_brand`, `product_category`, `product_description`, `product_criteria`, `product_image1`, `product_image2`, `product_image3`, `product_image4`, `product_price`, `product_discount`, `product_color`, `product_sold`, `product_qty`) VALUES
	(1, 'Asus ROG Zephyrus G14', 'Asus', 'Laptop', 'Spesifikasi : Asus ROG Zephyrus G14 OLED GA403UU R7 8845HS RTX4050 16GB 1TB 14"QHD\r\nPartnumber : GA403UU-R745OL6G-OM (Black) \r\nDisplay : 14" QHD IPS Display \r\nProcessor : AMD Ryzen 7 8845HS CPU\r\nGraphic : Nvidia RTX4050 6GB GPU\r\nMemory : 16GB Memory 1x16\r', 'Favorite', 'asusROGG141.jpg', 'asusROGG142.jpg', 'asusROGG143.jpg', NULL, 1999.00, 5.00, 'Black', 10, 0),
	(2, 'Acer Nitro V16', 'Acer', 'Laptop', 'Spesifikasi : AMD Ryzen 7 8845HS Processor 3.8GHz\r\nGraphics : NVIDIA GeForce RTX4050 6GB GDDR6 (Max Power : 140W)\r\nDisplay : 16" 165Hz WUXGA Ultra Slim Design sRGB 100% IPS Display\r\nMemory : 16GB DDR5 5600Mhz (2x8GB - DDR5-5200 - 2x SODIMM Slot)\r\nStorage ', 'Favorite', 'NITROV16.jpg', 'nitrov162.jpg', 'nitrov163.webp', NULL, 1599.00, 10.00, 'Black', 15, 0),
	(3, 'Lenovo Legion Pro 5i', 'Lenovo', 'Laptop', 'Spesifikasi:\r\nGraphics :NVIDIA GeForce RTX 4060 8GB GDDR6, Boost Clock 2370MHz, TGP 140W, 233 AI TOPS\r\nProcessor :Intel Core i7-13650HX, 14C (6P + 8E) / 20T, P-core 2.6 / 4.9GHz, E-core 1.9 / 3.6GHz, 24MB\r\nMemory : 24GB RAM (2x 12GB SO-DIMM DDR5-4800)\r\nSt', 'Non-Favorite', 'legionpro5i1.avif', 'legionpro5i2.jpg', 'legionpro5i3.jpg', NULL, 2099.00, 10.00, 'Grey', 5, 0),
	(4, 'Lenovo LOQ 15IAX9', 'Lenovo', 'Laptop', 'Spesifikasi:\r\nProcessor: Intel Core i5-12450HX, 8C (4P + 4E) / 12T, P-core up to 4.4GHz, E-core up to 3.1GHz, 12MB\r\nAI PC Category: AI-Powered Gaming PC\r\nGraphics: NVIDIA GeForce RTX 3050 6GB GDDR6, Boost Clock 1432MHz, TGP 65W, 142 AI TOPS\r\nMemory : 12GB', 'Favorite', 'LOQ1.jpg', 'LOQ2.avif', 'LOQ3.png', NULL, 1099.00, 2.00, 'Grey', 20, 0),
	(5, 'HP Victus 15', 'HP', 'Laptop', 'Spesifikasi :\r\nProcessor : AMD Ryzen 7 8845HS Processor 3.8GHz (24MB Cache, up to 5.1 GHz, 8 cores, 16 Threads)\r\nGraphics : NVIDIA GeForce RTX 4050 Laptop GPU (6 GB GDDR6 dedicated)\r\nMemory : 8GB DDR5-5600 MHz RAM\r\nStorage : 512GB PCIe Gen4 NVMe TLC M.2 S', 'Non-Favorite', 'Victus15_1.png', 'victus152.webp', NULL, NULL, 1299.00, 10.00, 'Black', 8, 0),
	(6, 'Asus ROG Zephyrus G16', 'Asus', 'Laptop', 'Spesifikasi :\r\nGrafis : NVIDIA GeForce RTX 5070 Laptop GPU\r\nROG Boost: 1595MHz* at 105W (1545MHz Boost Clock+50MHz OC, 90W+15W Dynamic Boost)\r\n8GB GDDR7\r\nProsesor : Intel Core Ultra 9 Processor 285H 2.9 GHz (24MB Cache, up to 5.4 GHz, 16 cores, 16 Threads', 'Non-Favorite', 'rogg162.png', 'rogg163.jpeg', 'rogg164.jpg', NULL, 2299.00, 10.00, 'Grey', 3, 0),
	(7, 'iPhone 16', 'Apple', 'Handphone', 'Spesifikasi :\r\nDisplay : Super Retina XDR OLED, 6.1 inches, 91.7 cm2, 1179 x 2556 pixels, 19.5:9 ratio\r\nPlatform : Apple A18 (3 nm), Apple GPU (5-core graphics)\r\nMemory : 128GB 8GB RAM, 256GB 8GB RAM, 512GB 8GB RAM NVMe\r\nCamera : 48 MP, f/1.6, 26mm (wide)', 'Favorite', 'IP2.webp', 'IP3.webp', 'IP4.webp', NULL, 899.00, 3.00, 'Purple', 20, 0),
	(8, 'Samsung S25', 'Samsung', 'Handphone', 'Spesifikasi :\r\nDisplay : Dynamic LTPO AMOLED 2X, 120Hz, HDR10+, 2600 nits, 6.2 inches, 94.4 cm2 (~91.1% screen-to-body ratio), 1080 x 2340 pixels, 19.5:9 ratio\r\nPlatform : Qualcomm SM8750-AB Snapdragon 8 Elite (3 nm), Adreno 830\r\nMemory : 128GB 12GB RAM, ', 'Favorite', 'samsungs251.jpg', 'samsungs252.png', 'samsungs253.png', NULL, 859.00, 2.00, 'Grey', 15, 0),
	(9, 'Samsung A56', 'Samsung', 'Handphone', 'Spesifikasi :\r\nDisplay : Super AMOLED, 120Hz, HDR10+, 1200 nits (HBM), 1900 nits (peak), 6.7 inches, 110.2 cm2, 1080 x 2340 pixels, 19.5:9 ratio\r\nPlatform : Exynos 1580 (4 nm), Xclipse 540\r\nMemory : 128GB 6GB RAM, 128GB 8GB RAM, 256GB 6GB RAM, 256GB 8GB R', 'Favorite', 'A56.jpg', 'samsunga561.avif', 'samsunga562.avif', NULL, 799.00, 2.00, 'Pink', 30, 0),
	(10, 'Vivo X200', 'Vivo', 'Handphone', 'Spesifikasi :\r\nDisplay : AMOLED, 1B colors, 120Hz, HDR10+, 4500 nits (peak), 6.67 inches, 107.4 cm2, 1260 x 2800 pixels, 20:9 ratio\r\nPlatform : Mediatek Dimensity 9400 (3 nm), Immortalis-G925\r\nMemory : 256GB 12GB RAM, 512GB 12GB RAM, 512GB 16GB RAM, 1TB 1', 'Non-Favorite', 'Vivox200.png', 'vivox2001.jpg', 'vivox2002.jpg', NULL, 1099.00, 8.00, 'Green', 10, 0),
	(11, 'Vivo X200 Pro', 'Vivo', 'Handphone', 'Spesifikasi :\r\nDisplay : LTPO AMOLED, 1B colors, 120Hz, HDR10+, Dolby Vision, 4500 nits (peak), 6.78 inches, 111.5 cm2, 1260 x 2800 pixels, 20:9 ratio\r\nPlatform : Mediatek Dimensity 9400 (3 nm), Immortalis-G925\r\nMemory : 256GB 12GB RAM, 512GB 16GB RAM, 1T', 'Non-Favorite', 'vivox200pro1.jpg', 'vivox200pro2.jpg', 'vivox200pro3.jpg', NULL, 1499.00, 8.00, 'Blue', 5, 0),
	(12, 'Oppo Find X8', 'Oppo', 'Handphone', 'Spesifikasi :\r\nDisplay : AMOLED, 1B colors, 120Hz, Dolby Vision, HDR10+, 800 nits (typ), 1600 nits (HBM), 4500 nits (peak), 6.59 inches, 105.6 cm2, 1256 x 2760 pixels\r\nPlatform : Mediatek Dimensity 9400 (3 nm), Immortalis-G925\r\nMemory : 256GB 12GB RAM, 25', 'Non-Favorite', 'oppox81.png', 'oppox82.jpg', 'oppox83.jpg', NULL, 1599.00, 5.00, 'White', 2, 0),
	(13, 'Asus Zenbook 14 UM3406HA', 'Asus', 'Laptop', 'Spesifikasi :\r\nProcessor : AMD Ryzen 7 8840HS Processor 3.3GHz\r\nGraphics : AMD Radeon Graphics\r\nDisplay : 14" OLED WUXGA Bend Glare Non-touch, 400nits(HDR), DCI-P3:100%\r\nMemory : 16GB LPDDR5X 2x8GB Soldered\r\nStorage : SSD 512GB', 'Favorite', 'asuszenbook141.jpg', 'asuszenbook142.jpg', 'asuszenbook143.jpg', NULL, 1399.00, 3.00, 'Black', 10, 0),
	(14, 'Asus Vivobook S15 S5506MA', 'Asus', 'Laptop', 'Spesifikasi :\r\nProcessor : Intel Core Ultra 7-155H 1.4 GHz (24MB Cache, up to 4.8G GHz, 16 Cores, 22 Threads)\r\nGraphics : Intel Arc Graphics\r\nDisplay : 15.6” 3K OLED (2880 x 1620) 16:9 120Hz NanoEdge display, 100% RGB color gamut, and 500 nits peak bright', 'Favorite', 'asusvivobooks151.jpeg', 'asusvivobooks152.webp', 'asusvivobook153.webp', NULL, 1199.00, 5.00, 'Black', 15, 0),
	(15, 'Acer Swift GO 14 AI OLED EVO SFG14 73 56A7', 'Acer', 'Laptop', 'Spesifikasi :\r\nProcessor : Intel Core Ultra 5 processor 125H with AI Boost (Intel Evo)\r\nDisplay : 14.0" OLED, 2.8K (2880 x 1800), high-brightness (400 nits)\r\nMemory : 32 GB LPDDR5X Dual Channel Onboard Memory\r\nStorage : 512 GB SSD NVMe Gen4 (2 slot for up', 'Favorite', 'acerswift1.png', 'acerswift2.jpg', 'acerswift3.webp', NULL, 1199.00, 4.00, 'Grey', 10, 0),
	(16, 'MSI Katana 15', 'MSI', 'Laptop', 'Spesifikasi :\r\nProcessor : Intel I7 14650HX\r\nGraphics : NVIDIA GeForce RTX 5070 8GB Laptop GPU powers advanced AI with 798 AI TOPS\r\nDisplay : 15.6" QHD(2560x1440), 165Hz Refresh Rate, IPS-Level, 100% DCI-P3\r\nMemory : 16GB ( 8GB*2 DDR5-5600,Up to DDR5-5600', 'Non-Favorite', 'msikatana151.webp', 'msikatana152.png', 'msikatana153.png', NULL, 999.00, 8.00, 'Black', 10, 0),
	(17, 'MSI Prestige 14 Ai Studio', 'MSI', 'Laptop', 'Spesifikasi :\r\nProcessor : Intel Ultra 7 155H\r\nGraphic : RTX 4050 Laptop GPU 6GB GDDR6\r\nMemory : 16GB DDR5-5600 2 Slots\r\nStorage : 1TB 1x M.2 SSD slot (NVMe PCIe Gen4)\r\nDisplay : 14” 2.8K (2880x1800)\r\nBattery : 4-Cell, 90 Battery (Whr)', 'Non-Favorite', 'msiprestige1411.webp', 'msiprestige142.jpeg', 'msiprestige143.jpg', NULL, 1399.00, 3.00, 'Black', 4, 0),
	(18, 'ASUS Gaming V16 V3607VJ', 'Asus', 'Laptop', 'Spesifikasi : \r\nProcessor : Intel Core 5 Processor 210H\r\nGraphics : NVIDIA GeForce RTX 3050 6GB Laptop GPU Graphics\r\nMemory : 16GB DDR5 SO-DIMM\r\nStorage : 512GB M.2 NVMe PCIe 4.0 SSD\r\nBattery : 63WHrs, 3S1P, 3-cell Li-ion', 'Favorite', 'AsusgamingV161.jpg', 'asusgamingv162.jpg', 'asusgamingv163.jpg', NULL, 1199.00, 3.00, 'Grey', 6, 0),
	(19, 'iPhone 15', 'Apple ', 'Handphone', 'Spesifikasi :\r\nDisplay : Super Retina XDR OLED, HDR10, Dolby Vision, 1000 nits (HBM), 2000 nits (peak), 6.1 inches, 91.3 cm2 (~86.4% screen-to-body ratio), 1179 x 2556 pixels, 19.5:9 ratio (~461 ppi density)\r\nPlatform : Apple A16 Bionic (4 nm)\r\nMemory : 1', 'Favorite', 'iphone15blue.webp', 'iphone15blue2.webp', 'iphone15blue3.webp', NULL, 799.00, 2.00, 'Blue', 25, 0),
	(20, 'iPhone 16e', 'Apple', 'Handphone', 'Spesifikasi : \r\nDisplay : Super Retina XDR OLED, HDR10, 800 nits (HBM), 1200 nits (peak), 6.1 inches, 91.4 cm2, 1170 x 2532 pixels, 19.5:9 ratio\r\nPlatform : Apple A18 (3 nm), Hexa-core (2x4.04 GHz + 4x2.20 GHz)\r\nCamera : 48 MP, f/1.6, 26mm (wide), 1/2.55"', 'Non-Favorite', 'iphone16e1.webp', 'iphone16e2.webp', 'iphone16e3.webp', NULL, 599.00, 3.00, 'Black', 15, 0),
	(21, 'Xiaomi 15 Ultra', 'Xiaomi', 'Handphone', 'Spesifikasi :\r\nDisplay : LTPO AMOLED, 68B colors, 120Hz, 1920Hz PWM, Dolby Vision, HDR10+, HDR Vivid, 3200 nits (peak), 6.73 inches, 108.9 cm2, 1440 x 3200 pixels, 20:9 ratio\r\nPlatform : Qualcomm SM8750-AB Snapdragon 8 Elite (3 nm), Octa-core (2x4.32 GHz ', 'Favorite', 'xiaomi15ultra1.png', 'xiaomi15ultra2.png', NULL, NULL, 799.00, 2.00, 'Grey', 10, 0),
	(47, 'MacBook Air M3', 'Apple', 'Laptop', 'Bagus bat dah ', 'Non-Favorite', 'macbook_air_m31.jpeg', 'macbook_air_m32.jpeg', 'macbook_air_m33.jpeg', 'macbook_air_m34.jpeg', 24000.00, 5.00, 'black, red, blue, purple', 0, 0);

-- Dumping structure for table gems_web.reviews
CREATE TABLE IF NOT EXISTS `reviews` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL CHECK (`rating` between 1 and 5),
  `review_text` text DEFAULT NULL,
  `review_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`review_id`),
  KEY `product_id` (`product_id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table gems_web.reviews: ~1 rows (approximately)
DELETE FROM `reviews`;
INSERT INTO `reviews` (`review_id`, `product_id`, `customer_id`, `rating`, `review_text`, `review_date`) VALUES
	(1, 32, 32, 3, 'Lapto nya gampang overheat', '2025-06-08 10:58:09');

-- Dumping structure for table gems_web.supplier
CREATE TABLE IF NOT EXISTS `supplier` (
  `id_supplier` int(11) NOT NULL AUTO_INCREMENT,
  `nama_PT_supplier` varchar(100) NOT NULL,
  `alamat_supplier` text NOT NULL,
  `contact_PT` varchar(20) NOT NULL,
  `email_supplier` varchar(100) DEFAULT NULL,
  `sales_name` varchar(50) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `product_brand` varchar(100) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_supplier`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table gems_web.supplier: ~2 rows (approximately)
DELETE FROM `supplier`;
INSERT INTO `supplier` (`id_supplier`, `nama_PT_supplier`, `alamat_supplier`, `contact_PT`, `email_supplier`, `sales_name`, `contact_person`, `product_brand`, `status`, `created_at`, `updated_at`) VALUES
	(1, ' PT Sat Nusapersada Tbk', 'Jl. Pelita VI No. 99, Kelurahan Kampung Pelita, Kecamatan Lubuk Baja, Kota Batam', '(0778) 5708888', 'corporate.secretary@satnusa.com', 'Jamals', '08974520321', 'HP', 1, '2025-06-07 13:06:04', '2025-06-07 13:06:04'),
	(2, 'PT Data Citra Mandiri', 'Jln.Bandengan Selatan No. 19-20, Pekojan, Jakarta Barat 11240. ', '08216915401', 'corporate@ibox.co.id', 'Herman', '081478650', 'Apple', 1, '2025-06-07 14:23:18', '2025-06-07 14:23:18');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

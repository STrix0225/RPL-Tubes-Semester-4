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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table gems_web.customers: ~1 rows (approximately)
DELETE FROM `customers`;
INSERT INTO `customers` (`customer_id`, `customer_name`, `customer_email`, `customer_password`, `customer_phone`, `customer_city`, `customer_address`, `customer_photo`) VALUES
	(32, 'Lanz Alexander', 'Lanz@gmail.com', '$2y$10$6MtCG8UKKU4qYmUhW708lO7TVUc3zf3J5rmNPORGT5V58C/KpfJ1K', '0895411811612', 'Buitenzorg', 'JlCikutrano90', 'uploads/683b07bc67423.jpg');

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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table gems_web.orders: ~4 rows (approximately)
DELETE FROM `orders`;
INSERT INTO `orders` (`order_id`, `order_cost`, `order_status`, `customer_id`, `customer_phone`, `customer_city`, `customer_address`, `order_date`, `payment_method`) VALUES
	(25, 6460.00, 'pending', 32, '0895411811612', 'Buitenzorg', 'JlCikutrano90', '2025-06-01 23:28:34', 'paypal'),
	(26, 900.00, 'cancelled', 32, '0895411811612', 'Buitenzorg', 'JlCikutrano90', '2025-06-02 00:09:40', 'paypal'),
	(27, 600.00, 'completed', 32, '0895411811612', 'Buitenzorg', 'JlCikutrano90', '2025-06-02 00:11:01', 'paypal'),
	(28, 600.00, 'completed', 32, '0895411811612', 'Buitenzorg', 'JlCikutrano90', '2025-06-03 07:25:58', 'paypal');

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
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table gems_web.order_stock: ~2 rows (approximately)
DELETE FROM `order_stock`;
INSERT INTO `order_stock` (`id_stock`, `id_supplier`, `product_id`, `product_name`, `product_brand`, `product_category`, `product_color`, `product_qty`, `product_ori_price`, `total_restock_price`, `order_date`, `status_order`, `catatan`) VALUES
	(2, 1, 32, 'HP-15 db006wm', 'HP', 'Laptop', 'Black', 10, 5000600.00, 50006000.00, '2025-06-07', 'processing', 'Pajak 20%'),
	(4, 2, 34, 'MacBook Air M3', 'Apple', 'Laptop', 'white', 10, 24999000.00, 99999999.99, '2025-06-07', 'completed', 'Gaji bulanan dipotong');

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
  `product_price` decimal(10,2) DEFAULT NULL,
  `product_discount` decimal(10,2) DEFAULT NULL,
  `product_color` varchar(50) DEFAULT NULL,
  `product_sold` int(11) DEFAULT NULL,
  `product_qty` int(11) DEFAULT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO products (product_id, product_name, product_brand, product_category, product_description, product_criteria, product_image1, product_image2, product_image3, product_image4, product_price, product_discount, product_color, product_sold) VALUES
	(1, 'Asus ROG Zephyrus G14', 'Asus', 'Laptop', 'Spesifikasi : Asus ROG Zephyrus G14 OLED GA403UU R7 8845HS RTX4050 16GB 1TB 14"QHD\r\nPartnumber : GA403UU-R745OL6G-OM (Black) \r\nDisplay : 14" QHD IPS Display \r\nProcessor : AMD Ryzen 7 8845HS CPU\r\nGraphic : Nvidia RTX4050 6GB GPU\r\nMemory : 16GB Memory 1x16\r\nStorage : 1TB SSD Storage (PCIe 4.0 - 1x M.2 NVMe Slot)\r\n', 'Favorite', 'asusROGG141.jpg', 'asusROGG142.jpg', 'asusROGG143.jpg', NULL, 1999.00, 5.00, 'Black', 10),
	(2, 'Acer Nitro V16', 'Acer', 'Laptop', 'Spesifikasi : AMD Ryzen 7 8845HS Processor 3.8GHz\r\nGraphics : NVIDIA GeForce RTX4050 6GB GDDR6 (Max Power : 140W)\r\nDisplay : 16" 165Hz WUXGA Ultra Slim Design sRGB 100% IPS Display\r\nMemory : 16GB DDR5 5600Mhz (2x8GB - DDR5-5200 - 2x SODIMM Slot)\r\nStorage : 512GB SSD PCIe NVMe (PCIe 4.0 - 2x M.2 NVMe Slot)', 'Favorite', 'NITROV16.jpg', 'nitrov162.jpg', 'nitrov163.webp', NULL, 1599.00, 10.00, 'Black', 15),
	(3, 'Lenovo Legion Pro 5i', 'Lenovo', 'Laptop', 'Spesifikasi:\r\nGraphics :NVIDIA GeForce RTX 4060 8GB GDDR6, Boost Clock 2370MHz, TGP 140W, 233 AI TOPS\r\nProcessor :Intel Core i7-13650HX, 14C (6P + 8E) / 20T, P-core 2.6 / 4.9GHz, E-core 1.9 / 3.6GHz, 24MB\r\nMemory : 24GB RAM (2x 12GB SO-DIMM DDR5-4800)\r\nStorage : 512GB SSD Storage (PCIe 4.0 - 2x M.2 NVMe Slot)', 'Non-Favorite', 'legionpro5i1.avif', 'legionpro5i2.jpg', 'legionpro5i3.jpg', NULL, 2099.00, 10.00, 'Grey', 5),
	(4, 'Lenovo LOQ 15IAX9', 'Lenovo', 'Laptop', 'Spesifikasi:\r\nProcessor: Intel Core i5-12450HX, 8C (4P + 4E) / 12T, P-core up to 4.4GHz, E-core up to 3.1GHz, 12MB\r\nAI PC Category: AI-Powered Gaming PC\r\nGraphics: NVIDIA GeForce RTX 3050 6GB GDDR6, Boost Clock 1432MHz, TGP 65W, 142 AI TOPS\r\nMemory : 12GB Memory (1x12GB - DDR5-4800 - 2x SODIMM Slot)\r\nStorage : 512GB SSD Storage (PCIe 4.0 - 2x M.2 NVMe Slot)', 'Favorite', 'LOQ1.jpg', 'LOQ2.avif', 'LOQ3.png', NULL, 1099.00, 2.00, 'Grey', 20),
	(5, 'HP Victus 15', 'HP', 'Laptop', 'Spesifikasi :\r\nProcessor : AMD Ryzen 7 8845HS Processor 3.8GHz (24MB Cache, up to 5.1 GHz, 8 cores, 16 Threads)\r\nGraphics : NVIDIA GeForce RTX 4050 Laptop GPU (6 GB GDDR6 dedicated)\r\nMemory : 8GB DDR5-5600 MHz RAM\r\nStorage : 512GB PCIe Gen4 NVMe TLC M.2 S', 'Non-Favorite', 'Victus15_1.png', 'victus152.webp', NULL, NULL, 1299.00, 10.00, 'Black', 8),
	(6, 'Asus ROG Zephyrus G16', 'Asus', 'Laptop', 'Spesifikasi :\r\nGrafis : NVIDIA GeForce RTX 5070 Laptop GPU\r\nROG Boost: 1595MHz* at 105W (1545MHz Boost Clock+50MHz OC, 90W+15W Dynamic Boost)\r\n8GB GDDR7\r\nProsesor : Intel Core Ultra 9 Processor 285H 2.9 GHz (24MB Cache, up to 5.4 GHz, 16 cores, 16 Threads', 'Non-Favorite', 'rogg162.png', 'rogg163.jpeg', 'rogg164.jpg', NULL, 2299.00, 10.00, 'Grey', 3),
	(7, 'iPhone 16', 'Apple', 'Handphone', 'Spesifikasi :\r\nDisplay : Super Retina XDR OLED, 6.1 inches, 91.7 cm2, 1179 x 2556 pixels, 19.5:9 ratio\r\nPlatform : Apple A18 (3 nm), Apple GPU (5-core graphics)\r\nMemory : 128GB 8GB RAM, 256GB 8GB RAM, 512GB 8GB RAM NVMe\r\nCamera : 48 MP, f/1.6, 26mm (wide), 1/1.56", 1.0µm, dual pixel PDAF, sensor-shift OIS\r\n         12 MP, f/2.2, 13mm, 120˚ (ultrawide), 0.7µm, dual pixel PDAF, 4K@24/25/30/60fps, 1080p@25/30/60/120/240fps, HDR, Dolby Vision HDR (up to 60fps), stereo sound rec.\r\nSelfie Camera : 12 MP, f/1.9, 23mm (wide), 1/3.6", 1.0µm, PDAF\r\n                SL 3D, (depth/biometrics sensor), 4K@24/25/30/60fps, 1080p@25/30/60/120fps, gyro-EIS\r\nBattery : Li-Ion 3561 mAh', 'Favorite', 'IP2.webp', 'IP3.webp', 'IP4.webp', NULL, 899.00, 3.00, 'Purple', 20),
	(8, 'Samsung S25', 'Samsung', 'Handphone', 'Spesifikasi :\r\nDisplay : Dynamic LTPO AMOLED 2X, 120Hz, HDR10+, 2600 nits, 6.2 inches, 94.4 cm2 (~91.1% screen-to-body ratio), 1080 x 2340 pixels, 19.5:9 ratio\r\nPlatform : Qualcomm SM8750-AB Snapdragon 8 Elite (3 nm), Adreno 830\r\nMemory : 128GB 12GB RAM, 256GB 12GB RAM, 512GB 12GB RAM\r\n 	 UFS 4.0\r\nCamera : 50 MP, f/1.8, 24mm (wide), 1/1.56", 1.0µm, dual pixel PDAF, OIS,\r\n         10 MP, f/2.4, 67mm (telephoto), 1/3.94", 1.0µm, PDAF, OIS, 3x optical zoom,\r\n         12 MP, f/2.2, 13mm, 120˚ (ultrawide), 1/2.55" 1.4µm, Super Steady video\r\n         8K@24/30fps, 4K@30/60fps, 1080p@30/60/120/240fps, 10-bit HDR, HDR10+, stereo sound rec., gyro-EIS\r\nSelfie Camera : 12 MP, f/2.2, 26mm (wide), 1/3.2", 1.12µm, dual pixel PDAF, HDR, HDR10+, 4K@30/60fps, 1080p@30fps\r\nBattery : Li-Ion 4000 mAh', 'Favorite', 'samsungs251.jpg', 'samsungs252.png', 'samsungs253.png', NULL, 859.00, 2.00, 'Grey', 15),
	(9, 'Samsung A56', 'Samsung', 'Handphone', 'Spesifikasi :\r\nDisplay : Super AMOLED, 120Hz, HDR10+, 1200 nits (HBM), 1900 nits (peak), 6.7 inches, 110.2 cm2, 1080 x 2340 pixels, 19.5:9 ratio\r\nPlatform : Exynos 1580 (4 nm), Xclipse 540\r\nMemory : 128GB 6GB RAM, 128GB 8GB RAM, 256GB 6GB RAM, 256GB 8GB RAM, 256GB 12GB RAM\r\n         UFS 3.1\r\nCamera : 50 MP, f/1.8, (wide), 1/1.56", 1.0µm, PDAF, OIS,\r\n         12 MP, f/2.2, 123˚ (ultrawide), 1/3.06", 1.12µm,\r\n         5 MP, f/2.4, (macro), 4K@30fps, 1080p@30/60fps, gyro-EIS\r\nSelfie Camera : 12 MP, f/2.2, (wide), 4K@30fps, 1080p@30/60fps, 10-bit HDR\r\nBattery : 5000 mAh', 'Favorite', 'A56.jpg', 'samsunga561.avif', 'samsunga562.avif', NULL, 799.00, 2.00, 'Pink', 30),
	(10, 'Vivo X200', 'Vivo', 'Handphone', 'Spesifikasi :\r\nDisplay : AMOLED, 1B colors, 120Hz, HDR10+, 4500 nits (peak), 6.67 inches, 107.4 cm2, 1260 x 2800 pixels, 20:9 ratio\r\nPlatform : Mediatek Dimensity 9400 (3 nm), Immortalis-G925\r\nMemory : 256GB 12GB RAM, 512GB 12GB RAM, 512GB 16GB RAM, 1TB 16GB RAM UFS 4.0\r\nCamera : 50 MP, f/1.6, 23mm (wide), 1/1.56", PDAF, OIS\r\n         50 MP, f/2.6, 70mm (periscope telephoto), 1/1.95", PDAF, OIS, 3x optical zoom\r\n         50 MP, f/2.0, 15mm, 119˚ (ultrawide), 1/2.76", 0.64µm, AF\r\nSelfie Camera : 32 MP, f/2.0, 20mm (ultrawide), 4K@30/60fps, 1080p@30/60fps\r\nBattery : Si/C Li-Ion 5800 mAh', 'Non-Favorite', 'Vivox200.png', 'vivox2001.jpg', 'vivox2002.jpg', NULL, 1099.00, 8.00, 'Green', 10),
	(11, 'Vivo X200 Pro', 'Vivo', 'Handphone', 'Spesifikasi :\r\nDisplay : LTPO AMOLED, 1B colors, 120Hz, HDR10+, Dolby Vision, 4500 nits (peak), 6.78 inches, 111.5 cm2, 1260 x 2800 pixels, 20:9 ratio\r\nPlatform : Mediatek Dimensity 9400 (3 nm), Immortalis-G925\r\nMemory : 256GB 12GB RAM, 512GB 16GB RAM, 1TB 16GB RAM UFS 4.0\r\nCamera : 50 MP, f/1.6, 23mm (wide), 1/1.28", 1.22µm, PDAF, OIS\r\n         200 MP, f/2.7, 85mm (periscope telephoto), 1/1.4", 0.56µm, multi-directional PDAF, OIS, 3.7x optical zoom, macro 2.7:1\r\n         50 MP, f/2.0, 15mm, 119˚ (ultrawide), 1/2.76", 0.64µm, AF, 8K@30fps, 4K@30/60/120fps, 1080p@30/60/120/240fps, gyro-EIS, 10-bit Log, Dolby Vision HDR\r\nSelfie Camera : 32 MP, f/2.0, 20mm (ultrawide), 4K@30/60fps, 1080p@30/60fps\r\nBattery : Si/C Li-Ion 6000 mAh', 'Non-Favorite', 'vivox200pro1.jpg', 'vivox200pro2.jpg', 'vivox200pro3.jpg', NULL, 1499.00, 8.00, 'Blue', 5),
	(12, 'Oppo Find X8', 'Oppo', 'Handphone', 'Spesifikasi :\r\nDisplay : AMOLED, 1B colors, 120Hz, Dolby Vision, HDR10+, 800 nits (typ), 1600 nits (HBM), 4500 nits (peak), 6.59 inches, 105.6 cm2, 1256 x 2760 pixels\r\nPlatform : Mediatek Dimensity 9400 (3 nm), Immortalis-G925\r\nMemory : 256GB 12GB RAM, 256GB 16GB RAM, 512GB 12GB RAM, 512GB 16GB RAM, 1TB 16GB RAM UFS 4.0\r\nCamera : 50 MP, f/1.8, 24mm (wide), 1/1.56", 1.0µm, multi-directional PDAF, OIS\r\n         50 MP, f/2.6, 73mm (periscope telephoto), 1/1.95", 0.61µm, 3x optical zoom, multi-directional PDAF, OIS\r\n         50 MP, f/2.0, 15mm, 120˚ (ultrawide), 1/2.75", 0.64µm, multi-directional PDAF, 4K@30/60fps, 1080p@30/60/240fps; gyro-EIS; HDR, 10‑bit video, Dolby Vision\r\nSelfie Camera : 32 MP, f/2.4, 21mm (wide), 1/2.74", 0.8µm, 4K@30/60fps, 1080p@30/60fps, gyro-EIS\r\nBattery : Si/C Li-Ion 5630 mAh', 'Non-Favorite', 'oppox81.png', 'oppox82.jpg', 'oppox83.jpg', NULL, 1599.00, 5.00, 'White', 2),
	(13, 'Asus Zenbook 14 UM3406HA', 'Asus', 'Laptop', 'Spesifikasi :\r\nProcessor : AMD Ryzen 7 8840HS Processor 3.3GHz\r\nGraphics : AMD Radeon Graphics\r\nDisplay : 14" OLED WUXGA Bend Glare Non-touch, 400nits(HDR), DCI-P3:100%\r\nMemory : 16GB LPDDR5X 2x8GB Soldered\r\nStorage : SSD 512GB', 'Favorite', 'asuszenbook141.jpg', 'asuszenbook142.jpg', 'asuszenbook143.jpg', NULL, 1399.00, 3.00, 'Black', 10),
	(14, 'Asus Vivobook S15 S5506MA', 'Asus', 'Laptop', 'Spesifikasi :\r\nProcessor : Intel Core Ultra 7-155H 1.4 GHz (24MB Cache, up to 4.8G GHz, 16 Cores, 22 Threads)\r\nGraphics : Intel Arc Graphics\r\nDisplay : 15.6” 3K OLED (2880 x 1620) 16:9 120Hz NanoEdge display, 100% RGB color gamut, and 500 nits peak brightness\r\nMemory : 16GB LPDDR5X RAM on board\r\nStorage : 1TB PCI-E NVMe 4 SSD', 'Favorite', 'asusvivobooks151.jpeg', 'asusvivobooks152.webp', 'asusvivobook153.webp', NULL, 1199.00, 5.00, 'Black', 15),
	(15, 'Acer Swift GO 14 AI OLED EVO SFG14 73 56A7', 'Acer', 'Laptop', 'Spesifikasi :\r\nProcessor : Intel Core Ultra 5 processor 125H with AI Boost (Intel Evo)\r\nDisplay : 14.0" OLED, 2.8K (2880 x 1800), high-brightness (400 nits)\r\nMemory : 32 GB LPDDR5X Dual Channel Onboard Memory\r\nStorage : 512 GB SSD NVMe Gen4 (2 slot for upgrade)', 'Favorite', 'acerswift1.png', 'acerswift2.jpg', 'acerswift3.webp', NULL, 1199.00, 4.00, 'Grey', 10),
	(16, 'MSI Katana 15', 'MSI', 'Laptop', 'Spesifikasi :\r\nProcessor : Intel I7 14650HX\r\nGraphics : NVIDIA GeForce RTX 5070 8GB Laptop GPU powers advanced AI with 798 AI TOPS\r\nDisplay : 15.6" QHD(2560x1440), 165Hz Refresh Rate, IPS-Level, 100% DCI-P3\r\nMemory : 16GB ( 8GB*2 DDR5-5600,Up to DDR5-5600, 2 Slots, Max 96GB )\r\nStorage : 512GB NVMe SSD PCIe Gen4, 1x M.2 SSD slot (NVMe PCIe Gen4)', 'Non-Favorite', 'msikatana151.webp', 'msikatana152.png', 'msikatana153.png', NULL, 999.00, 8.00, 'Black', 10),
	(17, 'MSI Prestige 14 Ai Studio', 'MSI', 'Laptop', 'Spesifikasi :\r\nProcessor : Intel Ultra 7 155H\r\nGraphic : RTX 4050 Laptop GPU 6GB GDDR6\r\nMemory : 16GB DDR5-5600 2 Slots\r\nStorage : 1TB 1x M.2 SSD slot (NVMe PCIe Gen4)\r\nDisplay : 14” 2.8K (2880x1800)\r\nBattery : 4-Cell, 90 Battery (Whr)', 'Non-Favorite', 'MSIprestige141.png', NULL, NULL, NULL, 1399.00, 3.00, 'Black', 4),
	(18, 'ASUS Gaming V16 V3607VJ', 'Asus', 'Laptop', 'Spesifikasi : \r\nProcessor : Intel Core 5 Processor 210H\r\nGraphics : NVIDIA GeForce RTX 3050 6GB Laptop GPU Graphics\r\nMemory : 16GB DDR5 SO-DIMM\r\nStorage : 512GB M.2 NVMe PCIe 4.0 SSD\r\nBattery : 63WHrs, 3S1P, 3-cell Li-ion', 'Favorite', 'AsusgamingV161.jpg', NULL, NULL, NULL, 1199.00, 3.00, 'Grey', 6),
	(19, 'iPhone 15 Pro', 'Apple ', 'Handphone', 'Spesifikasi :\r\nDisplay : Super Retina XDR OLED, HDR10, Dolby Vision, 1000 nits (HBM), 2000 nits (peak), 6.1 inches, 91.3 cm2 (~86.4% screen-to-body ratio), 1179 x 2556 pixels, 19.5:9 ratio (~461 ppi density)\r\nPlatform : Apple A16 Bionic (4 nm)\r\nMemory : 128GB 6GB RAM, 256GB 6GB RAM, 512GB 6GB RAM NVMe\r\nCamera : 48 MP, f/1.6, 26mm (wide), 1/1.56", 1.0µm, dual pixel PDAF, sensor-shift OIS 12 MP, f/2.4, 13mm, 120˚ (ultrawide), 0.7µm, 4K@24/25/30/60fps, 1080p@25/30/60/120/240fps, HDR, Dolby Vision HDR (up to 60fps)\r\nSelfie Camera : 12 MP, f/1.9, 23mm (wide), 1/3.6", 1.0µm, PDAF SL 3D, (depth/biometrics sensor), 4K@24/25/30/60fps, 1080p@25/30/60/120fps, gyro-EIS\r\nBattery : Li-Ion 3349 mAh', 'Favorite', 'iphone15blue.webp', 'iphone15blue2.webp', 'iphone15blue3.webp', NULL, 799.00, 2.00, 'Blue', 25);

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

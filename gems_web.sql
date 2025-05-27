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

-- Dumping data for table gems_web.blogs: ~29 rows (approximately)
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
  `customer_address` varchar(255) DEFAULT NULL,
  `customer_city` varchar(100) DEFAULT NULL,
  `customer_photo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table gems_web.customers: ~23 rows (approximately)
INSERT INTO `customers` (`customer_id`, `customer_name`, `customer_email`, `customer_password`, `customer_phone`, `customer_address`, `customer_city`, `customer_photo`) VALUES
	(9, 'aaaaa', 'aaaa@gm', '$2y$10$3LbuzhXIotDBPgvTuCpNo.M/46fWkw.o4NtD2qwHNYjIqD.6XP/Ze', '12341412412', 'aaa', 'dadada', 'uploads/68334e29b14b5.png'),
	(10, 'a', 'a@aa', '$2y$10$ZdwZ0tmFIWbqMnskxtjYJ.bFlACihU9E6To5xdVrHUkPKixioCZe.', '12121212121', 'w', 'w', 'uploads/68335339b3391.png'),
	(11, 'aaa', 'b@b', '$2y$10$y83.OQzfS04IfKyCo1jO/eotoxFUqvaCFYTjAx8GbiuUBelXe9RA2', '525423423', 'q', 'q', 'uploads/683355165f23b.jpg'),
	(12, 'William Salinas', 'brettchavez@yahoo.com', 'password1', '095.135.6806x68761', '11692 Christie Causeway Apt. 673, South Laurafort, IN 59768', 'Natalietown', 'cust1.jpg'),
	(13, 'Jerry Berg', 'karenbennett@peterson.biz', 'password2', '978-814-0679', '865 Wilson Mission, Annatown, AZ 41893', 'Port Priscilla', 'cust2.jpg'),
	(14, 'Lee Diaz', 'perezeric@ward.biz', 'password3', '963.433.2337x751', '929 Jason Lane, Toddhaven, AR 95244', 'West Allisonmouth', 'cust3.jpg'),
	(15, 'Dr. Lisa Allison', 'marychang@gmail.com', 'password4', '(232)407-4370x0510', '805 Melanie Summit, Hughesville, CA 67234', 'Port Bobby', 'cust4.jpg'),
	(16, 'Ryan Alvarez', 'nathan04@yahoo.com', 'password5', '610-444-4641x8362', '9009 Lindsey Parkway, South Katherine, NM 81834', 'Port Randy', 'cust5.jpg'),
	(17, 'Dr. Brian Francis Jr.', 'christopher31@hotmail.com', 'password6', '149-908-6144x369', '21809 Holly Overpass Apt. 178, Port Jonathan, TN 47531', 'Lopezstad', 'cust6.jpg'),
	(18, 'Darrell Diaz', 'ushaw@king-hansen.biz', 'password7', '(264)065-2731', '71547 Reynolds Via, Joanneside, KY 16437', 'Thomasberg', 'cust7.jpg'),
	(19, 'Dustin Smith', 'johnwilliamson@franco.com', 'password8', '796-490-5128x540', '94012 Wilson Neck Apt. 853, Benjaminfort, NJ 24486', 'Martineztown', 'cust8.jpg'),
	(20, 'Jennifer Castillo', 'michael98@gmail.com', 'password9', '766.247.2843x8754', '9326 Jones Terrace, North Christinaville, FL 77081', 'Carrolltown', 'cust9.jpg'),
	(21, 'Michelle Perez DVM', 'michelle01@gmail.com', 'password10', '(939)604-5409', '0690 Blair Stravenue Apt. 976, Port Josephfurt, OK 78731', 'New Christophermouth', 'cust10.jpg'),
	(22, 'Curtis Price', 'vtrujillo@roberts.info', 'password11', '+1-719-818-3536x9279', '9934 Nathan Rest, Port Douglasmouth, MN 99463', 'New Brianbury', 'cust11.jpg'),
	(23, 'Aaron Anderson', 'dwilson@gmail.com', 'password12', '863.138.1403x771', '431 Morgan Street, Lake Nicholas, SD 54883', 'Gabriellefurt', 'cust12.jpg'),
	(24, 'Jacob Herrera', 'jasonsharp@yahoo.com', 'password13', '+1-955-151-6131x8994', '8444 Ward Creek, Wagnershire, TX 65012', 'Port Cameronburgh', 'cust13.jpg'),
	(25, 'Craig Gilbert', 'lbrown@martinez-thompson.com', 'password14', '+1-460-660-0426', '757 Susan Grove Suite 275, North Jacqueline, MN 35527', 'North Aaron', 'cust14.jpg'),
	(26, 'Carla Evans', 'wmartinez@holmes-smith.com', 'password15', '+1-239-051-3338x2234', '994 Hines View, South Holly, AK 89893', 'Lake Meganfort', 'cust15.jpg'),
	(27, 'Anne Dunn', 'hannahvaldez@gmail.com', 'password16', '404.683.7137', 'Unit 2418 Box 2200, DPO AP 05210', 'East Joseph', 'cust16.jpg'),
	(28, 'Manuel Myers', 'bpeterson@jones.com', 'password17', '001-445-257-6115', 'Unit 4256 Box 0559, DPO AP 54541', 'Ortegachester', 'cust17.jpg'),
	(29, 'Brian Barton', 'deanna90@gmail.com', 'password18', '001-935-781-0073x066', 'Unit 5641 Box 0309, DPO AE 44169', 'Lambertside', 'cust18.jpg'),
	(30, 'Brian Castillo', 'nathanielrobinson@wright.biz', 'password19', '457.408.1876x4763', '7965 Duncan Shore, Schroedershire, AK 93588', 'East Robertbury', 'cust19.jpg'),
	(31, 'John Davis', 'greendebra@sanchez.info', 'password20', '001-903-193-8104', '63367 Jackson Causeway Apt. 878, Lake Timothy, AK 06905', 'Lindaport', 'cust20.jpg');

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

-- Dumping data for table gems_web.orders: ~24 rows (approximately)
INSERT INTO `orders` (`order_id`, `order_cost`, `order_status`, `customer_id`, `customer_phone`, `customer_city`, `customer_address`, `order_date`) VALUES
	(1, 34.50, 'approved', 1, '08763655111', 'Bandung', 'Arcamanik Residence Regency, Bandung', '2023-03-12 11:46:53'),
	(2, 48.00, 'rejected', 4, '08763655111', 'Bandung', 'Arcamanik Residence', '2023-03-13 09:17:54'),
	(3, 43.00, 'missing', 1, '3242523423423', 'Garut', 'efrsfraddwadad', '2025-05-24 15:35:39'),
	(4, 23.00, 'pending', 4, '4352423423', 'Subang', 'jdbguisbfu', '2025-05-24 15:36:30'),
	(5, 1454.94, 'on_hold', 8, '066-954-4738x80', 'North Michaelshire', '51545 Mario Point', '2023-09-30 05:02:53'),
	(6, 1465.84, 'shipped', 13, '4883467295', 'East Christian', '926 Laura Landing Suite 994', '2024-02-09 07:43:35'),
	(7, 1397.96, 'on_hold', 12, '+1-090-322-3709', 'Brendaport', '43353 Savage Plains', '2024-09-09 05:01:04'),
	(8, 811.15, 'shipped', 5, '221-677-1127', 'New Stephanietown', '78615 Jeffrey Brooks', '2024-05-07 23:01:42'),
	(9, 1234.24, 'shipped', 1, '375.337.2380', 'Port Brianville', '0383 David Green Suite 382', '2024-04-25 17:51:30'),
	(10, 1455.55, 'processing', 14, '523-565-4471', 'Erikaview', '55378 Simmons Ports', '2024-01-30 04:07:18'),
	(11, 1478.10, 'shipped', 5, '171-411-5878x69', 'Youngmouth', '708 Kelly Port Suite 373', '2024-11-03 09:48:50'),
	(12, 1105.62, 'shipped', 19, '001-496-468-280', 'Jessicaview', '4027 Brandi Harbor Apt. 065', '2023-10-21 13:32:25'),
	(13, 295.87, 'processing', 10, '+1-951-027-9325', 'Michaelton', '2011 Clayton Track', '2024-01-22 16:31:50'),
	(14, 610.27, 'processing', 2, '379-817-3585x72', 'Jonesside', '33492 George Common Apt. 041', '2023-10-08 22:39:42'),
	(15, 1372.38, 'processing', 8, '(740)288-0667', 'Scotthaven', '63408 Scott Views Apt. 360', '2023-10-06 11:50:23'),
	(16, 835.81, 'shipped', 11, '8414822987', 'New Markhaven', '8728 Hurst Trail Apt. 212', '2023-09-24 08:51:09'),
	(17, 1075.11, 'shipped', 3, '(714)480-7941', 'Danielleberg', '6817 James Dam Apt. 990', '2025-03-14 13:07:21'),
	(18, 631.77, 'processing', 16, '001-073-335-332', 'Stephanieville', '149 Vasquez Terrace', '2025-01-06 21:54:50'),
	(19, 1266.02, 'processing', 20, '351.425.9025x58', 'New Johnnyland', '2425 Zamora Square', '2025-01-05 12:04:18'),
	(20, 335.02, 'on_hold', 4, '3038160173', 'Davidbury', '348 Vanessa Lodge', '2025-02-02 12:25:15'),
	(21, 650.70, 'shipped', 17, '+1-193-339-1287', 'Danielleside', '761 Henry Springs Suite 595', '2025-01-11 02:29:09'),
	(22, 1091.05, 'processing', 9, '983.231.3933', 'Crystalborough', '0889 Curry Glens', '2023-12-19 09:18:17'),
	(23, 1357.36, 'on_hold', 1, '922-110-6561', 'Janebury', '39106 Evans Via', '2024-10-13 07:41:01'),
	(24, 1016.92, 'processing', 12, '065.443.4118', 'Raybury', '8099 Kathryn Trafficway Apt. 800', '2024-08-10 19:24:15');

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

-- Dumping data for table gems_web.order_items: ~26 rows (approximately)
INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `product_name`, `product_image`, `product_price`, `product_quantity`, `user_id`, `order_date`) VALUES
	(1, 1, '1', 'Sepatu Sneakers Pria Import Original Khamzo D05Terbaru', 'product-1.jpg', 20.00, 1, 1, '2023-03-12 11:46:53'),
	(2, 1, '7', 'AD012 Pashmina Cashmere Syal Scarf Shawl Kasmir Bahan Viscose', 'product-6.jpg', 14.50, 1, 1, '2023-03-12 11:46:53'),
	(3, 2, '3', 'Jaket pria kantor formal casual TRENDY', 'product-2.jpg', 13.50, 1, 1, '2023-03-13 09:17:54'),
	(4, 2, '7', 'AD012 Pashmina Cashmere Syal Scarf Shawl Kasmir Bahan Viscose', 'product-6.jpg', 14.50, 1, 1, '2023-03-13 09:17:54'),
	(5, 2, '1', 'Sepatu Sneakers Pria Import Original Khamzo D05Terbaru', 'product-1.jpg', 20.00, 1, 1, '2023-03-13 09:17:54'),
	(6, 3, '1', 'Sepatu Sneakers Pria Import Original Khamzo D05Terbaru', 'product-1.jpg', 20.00, 1, 1, '2023-03-19 01:01:33'),
	(7, 20, '5', 'Gadget 5', 'p5_1.jpg', 634.76, 2, 12, '2025-02-03 20:27:30'),
	(8, 14, '9', 'Gadget 9', 'p9_1.jpg', 733.36, 1, 10, '2024-01-17 01:32:36'),
	(9, 14, '17', 'Gadget 17', 'p17_1.jpg', 901.87, 5, 3, '2025-03-15 06:46:48'),
	(10, 7, '19', 'Gadget 19', 'p19_1.jpg', 256.05, 4, 7, '2025-02-28 11:00:48'),
	(11, 4, '18', 'Gadget 18', 'p18_1.jpg', 786.55, 2, 8, '2024-06-26 10:01:55'),
	(12, 7, '17', 'Gadget 17', 'p17_1.jpg', 833.54, 1, 15, '2025-04-30 12:26:50'),
	(13, 5, '20', 'Gadget 20', 'p20_1.jpg', 533.96, 2, 9, '2024-08-22 03:29:30'),
	(14, 2, '16', 'Gadget 16', 'p16_1.jpg', 418.70, 5, 3, '2024-03-11 02:53:54'),
	(15, 1, '13', 'Gadget 13', 'p13_1.jpg', 866.35, 2, 8, '2023-12-05 11:40:48'),
	(16, 11, '11', 'Gadget 11', 'p11_1.jpg', 196.49, 2, 19, '2023-06-22 20:08:06'),
	(17, 12, '13', 'Gadget 13', 'p13_1.jpg', 177.42, 2, 8, '2025-01-11 09:02:04'),
	(18, 12, '8', 'Gadget 8', 'p8_1.jpg', 664.54, 2, 7, '2024-02-05 12:00:03'),
	(19, 19, '7', 'Gadget 7', 'p7_1.jpg', 501.45, 4, 4, '2024-08-23 20:45:44'),
	(20, 12, '12', 'Gadget 12', 'p12_1.jpg', 718.21, 4, 1, '2025-02-12 09:21:53'),
	(21, 15, '12', 'Gadget 12', 'p12_1.jpg', 419.79, 5, 12, '2023-06-30 04:13:37'),
	(22, 9, '16', 'Gadget 16', 'p16_1.jpg', 438.64, 5, 4, '2025-01-11 16:47:05'),
	(23, 3, '18', 'Gadget 18', 'p18_1.jpg', 951.67, 3, 15, '2023-06-22 17:10:07'),
	(24, 3, '6', 'Gadget 6', 'p6_1.jpg', 586.71, 1, 19, '2024-05-26 07:30:10'),
	(25, 18, '12', 'Gadget 12', 'p12_1.jpg', 196.21, 2, 2, '2024-01-17 09:00:20'),
	(26, 7, '4', 'Gadget 4', 'p4_1.jpg', 921.56, 3, 7, '2023-09-17 06:24:08');

-- Dumping structure for table gems_web.payments
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_id` varchar(250) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table gems_web.payments: ~22 rows (approximately)
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

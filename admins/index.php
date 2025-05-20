<?php
session_start();
include('../Database/connection.php');

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Query jumlah customer
$query_customers = "SELECT COUNT(*) AS total_customers FROM customers";
$result_customers = mysqli_query($conn, $query_customers);
$data_customers = mysqli_fetch_assoc($result_customers);

// Query jumlah produk
$query_products = "SELECT COUNT(*) AS total_products FROM product";
$result_products = mysqli_query($conn, $query_products);
$data_products = mysqli_fetch_assoc($result_products);

// Query jumlah brand unik
$query_brands = "SELECT COUNT(DISTINCT product_brand) AS total_brands FROM product";
$result_brands = mysqli_query($conn, $query_brands);
$data_brands = mysqli_fetch_assoc($result_brands);

// Query jumlah admin
$query_admins = "SELECT COUNT(*) AS total_admins FROM admins";
$result_admins = mysqli_query($conn, $query_admins);
$data_admins = mysqli_fetch_assoc($result_admins);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
</head>
<body>

    <h2>Selamat datang di Beranda Admin</h2>

    <p>Total Customers Terdaftar: <?php echo $data_customers['total_customers']; ?></p>
    <p>Total Produk: <?php echo $data_products['total_products']; ?></p>
    <p>Total Brand Terdaftar: <?php echo $data_brands['total_brands']; ?></p>
    <p>Total Admin Terdaftar: <?php echo $data_admins['total_admins']; ?></p>

    <form method="GET" action="create-product.php">
        <input type="submit" value="Tambah Produk">
    </form>

    <form method="GET" action="list-product.php">
        <input type="submit" value="Lihat Daftar Produk">
    </form>

    <form method="GET" action="list-customers.php">
        <input type="submit" value="List customers">
    </form>

    <form method="POST" action="logout.php">
        <input type="submit" name="logout_btn" value="Logout">
    </form>

</body>
</html>

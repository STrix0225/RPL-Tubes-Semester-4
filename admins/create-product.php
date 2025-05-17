<?php
session_start();
include('../Database/connection.php');

if (!isset($_SESSION['admin_id'])) {
    header('location: login.php');
    exit;
}

if (isset($_POST['create_btn'])) {
    $product_name = trim($_POST['product_name']);
    $product_brand = trim($_POST['product_brand']);
    $product_category = trim($_POST['product_category']);
    $product_color = trim($_POST['product_color']);
    $product_description = trim($_POST['product_description']);
    $product_price = $_POST['product_price'];

    $check_query = "SELECT product_id FROM product WHERE product_name = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $product_name);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
    echo "<script>alert('Produk dengan nama tersebut sudah ada.'); window.location.href='create-product.php';</script>";
    exit;
    }


    $product_image1 = $_FILES['product_image1']['tmp_name'];
    $product_image2 = $_FILES['product_image2']['tmp_name'];
    $product_image3 = $_FILES['product_image3']['tmp_name'];

    $image_name1 = str_replace(' ', '_', $product_name) . ".1jpg";
    $image_name2 = str_replace(' ', '_', $product_name) . ".2jpg";
    $image_name3 = str_replace(' ', '_', $product_name) . ".3jspg";

    $target_dir = "../PictureProducts/";

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $upload1 = move_uploaded_file($product_image1, $target_dir . $image_name1);
    $upload2 = move_uploaded_file($product_image2, $target_dir . $image_name2);
    $upload3 = move_uploaded_file($product_image3, $target_dir . $image_name3);

    if (!$upload1 || !$upload2 || !$upload3) {
    echo "<script>alert('Gagal mengunggah gambar produk.'); window.location.href='create-product.php';</script>";
    exit;
    }


    // Simpan ke database
    $query = "INSERT INTO product 
        (product_name, product_brand, product_category, product_description, 
        product_photo1, product_photo2, product_photo3, product_price, product_color)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssss", 
        $product_name, 
        $product_brand, 
        $product_category, 
        $product_description,
        $image_name1,
        $image_name2,
        $image_name3,
        $product_price,
        $product_color
    );

    if ($stmt->execute()) {
    echo "<script>alert('Produk berhasil ditambahkan.'); window.location.href='create-product.php';</script>";
    } else {
    echo "<script>alert('Gagal menambahkan produk: " . addslashes($stmt->error) . "'); window.location.href='create-product.php';</script>";
    }

}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk</title>
</head>
<body>

    <h2>Tambah Produk</h2>

    <form method="POST" enctype="multipart/form-data">
        <label>Nama Produk:<br>
            <input type="text" name="product_name" required>
        </label><br><br>

        <label>Brand:<br>
            <input type="text" name="product_brand" required>
        </label><br><br>

        <label>Kategori:<br>
            <input type="text" name="product_category" required>
        </label><br><br>

        <label>Deskripsi:<br>
            <textarea name="product_description" rows="4" cols="50" required></textarea>
        </label><br><br>

        <label>Harga:<br>
            <input type="number" name="product_price" step="0.01" required>
        </label><br><br>

        <label>Warna:<br>
            <input type="text" name="product_color" required>
        </label><br><br>

        <label>Foto 1:<br>
            <input type="file" name="product_image1" accept="image/*" required>
        </label><br><br>

        <label>Foto 2:<br>
            <input type="file" name="product_image2" accept="image/*" required>
        </label><br><br>

        <label>Foto 3:<br>
            <input type="file" name="product_image3" accept="image/*" required>
        </label><br><br>

        <button type="submit" name="create_btn">Simpan Produk</button>
    </form>

    <p><a href="index.php">← Kembali ke Beranda Admin</a></p>

</body>
</html>

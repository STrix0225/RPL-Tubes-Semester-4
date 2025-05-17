<?php
session_start();
include('../Database/connection.php');

if (!isset($_GET['product_id'])) {
    echo "ID produk tidak ditemukan.";
    exit();
}

$product_id = intval($_GET['product_id']);

$stmt = $conn->prepare("SELECT product_photo1, product_photo2, product_photo3 FROM product WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Produk tidak ditemukan.";
    exit();
}

$product = $result->fetch_assoc();
$stmt->close();

$image_dir = "../PictureProducts/";

$images = ['product_photo1', 'product_photo2', 'product_photo3'];
foreach ($images as $img_field) {
    if (!empty($product[$img_field])) {
        $file_path = $image_dir . $product[$img_field];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
}

$stmt_delete = $conn->prepare("DELETE FROM product WHERE product_id = ?");
$stmt_delete->bind_param("i", $product_id);

if ($stmt_delete->execute()) {
    echo "<script>
        alert('Produk berhasil dihapus.');
        window.location.href = 'list-product.php';
    </script>";
} else {
    echo "<p>Gagal menghapus produk: " . $stmt_delete->error . "</p>";
}
?>

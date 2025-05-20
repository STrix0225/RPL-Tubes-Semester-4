<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

include('../Database/connection.php');

$query_products = "SELECT * FROM product";
$stmt_products = $conn->prepare($query_products);
$stmt_products->execute();
$products = $stmt_products->get_result();

$kurs_dollar = 15722;
function setRupiah($price)
{
    return "Rp" . number_format($price, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product List</title>
</head>
<body>

<h2>Daftar Produk</h2>
<p><a href="index.php">Dashboard</a> > Products</p>

<?php
$alerts = [
    'success_update_message',
    'fail_update_message',
    'success_delete_message',
    'fail_delete_message',
    'success_create_message',
    'fail_create_message',
    'image_success',
    'image_failed'
];

foreach ($alerts as $alert) {
    if (isset($_GET[$alert])) {
        echo "<p>" . htmlspecialchars($_GET[$alert]) . "</p>";
    }
}
?>

<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Foto</th>
        <th>Nama</th>
        <th>Brand</th>
        <th>Kategori</th>
        <th>Harga (Rp)</th>
        <th>Aksi</th>
    </tr>

    <?php while ($product = $products->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $product['product_id']; ?></td>
            <td>
                <img src="<?php echo '../PictureProducts/' . $product['product_photo1']; ?>" width="80" height="80" alt="gambar produk">
                <img src="<?php echo '../PictureProducts/' . $product['product_photo2']; ?>" width="80" height="80" alt="gambar produk">
                <img src="<?php echo '../PictureProducts/' . $product['product_photo3']; ?>" width="80" height="80" alt="gambar produk">
            </td>
            <td><?php echo $product['product_name']; ?></td>
            <td><?php echo $product['product_brand']; ?></td>
            <td><?php echo $product['product_category']; ?></td>
            <td><?php echo setRupiah($product['product_price']); ?></td>
            <td>
                <a href="./edit-image.php?product_id=<?php echo $product['product_id']; ?>">Edit Gambar</a> |
                <a href="./edit-product.php?product_id=<?php echo $product['product_id']; ?>">Edit</a> |
                <a href="./delete-product.php?product_id=<?php echo $product['product_id']; ?>">Hapus</a>
            </td>
        </tr>
    <?php } ?>
</table>

</body>
</html>

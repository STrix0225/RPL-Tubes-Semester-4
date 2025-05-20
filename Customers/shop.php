<?php
include('../Database/connection.php');

$category_query = $conn->query("SELECT DISTINCT product_category FROM product");

if (isset($_POST['search']) && isset($_POST['product_category'])) {
    $product_category = $_POST['product_category'];
    $stmt = $conn->prepare("SELECT * FROM product WHERE product_category = ?");
    $stmt->bind_param("s", $product_category);
    $stmt->execute();
    $products = $stmt->get_result();
} else {
    $stmt = $conn->prepare("SELECT * FROM product");
    $stmt->execute();
    $products = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shop Page</title>
</head>
<body>

<h1>Shop</h1>

<a href="./Dashboard.php">Beranda</a> | <a href="shop.php">Shop</a>
<hr>

<h2>Filter Kategori</h2>
<form method="POST" action="shop.php">
    <?php while ($cat = $category_query->fetch_assoc()): ?>
        <label>
            <input type="radio" name="product_category" value="<?= htmlspecialchars($cat['product_category']) ?>"
                <?= (isset($_POST['product_category']) && $_POST['product_category'] == $cat['product_category']) ? 'checked' : '' ?>>
            <?= htmlspecialchars($cat['product_category']) ?>
        </label><br>
    <?php endwhile; ?>

    <input type="submit" name="search" value="Cari">
    <button type="button" onclick="window.location='shop.php'">Reset</button>
</form>

<hr>

<h2>Daftar Produk</h2>

<?php while($row = $products->fetch_assoc()): ?>
    <div style="border: 1px solid #000; margin-bottom: 10px; padding: 10px;">
        <p><strong>Nama:</strong> <?= htmlspecialchars($row['product_name']); ?></p>
        <p><strong>Brand:</strong> <?= htmlspecialchars($row['product_brand']); ?></p>
        <p><strong>Kategori:</strong> <?= htmlspecialchars($row['product_category']); ?></p>
        <p><strong>Deskripsi:</strong> <?= htmlspecialchars($row['product_description']); ?></p>
        <p><strong>Harga:</strong> Rp <?= number_format($row['product_price'], 0, ',', '.'); ?></p>
        <p><strong>Warna:</strong> <?= htmlspecialchars($row['product_color']); ?></p>
        <p><strong>Foto:</strong></p>
        <ul>
            <?php if ($row['product_photo1']) echo "<li><img src='../PictureProducts/{$row['product_photo1']}' width='100'></li>"; ?>
            <?php if ($row['product_photo2']) echo "<li><img src='../PictureProducts/{$row['product_photo2']}' width='100'></li>"; ?>
            <?php if ($row['product_photo3']) echo "<li><img src='../PictureProducts/{$row['product_photo3']}' width='100'></li>"; ?>
        </ul>
        <a href="shop-details.php?product_id=<?= $row['product_id']; ?>">Lihat Detail</a>
    </div>
<?php endwhile; ?>

</body>
</html>

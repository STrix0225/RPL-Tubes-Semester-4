<?php
session_start();
include('../Database/connection.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    $query = "SELECT * FROM product WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
    } else {
        echo "Produk tidak ditemukan.";
        exit();
    }
}

if (isset($_POST['edit_btn'])) {
    $id = $_POST['product_id'];
    $name = $_POST['product_name'];
    $brand = $_POST['product_brand'];
    $category = $_POST['product_category'];
    $description = $_POST['product_description'];
    $price = $_POST['product_price'];
    $color = $_POST['product_color'];

    $updateQuery = "UPDATE product SET 
        product_name = ?, 
        product_brand = ?, 
        product_category = ?, 
        product_description = ?, 
        product_price = ?, 
        product_color = ?
        WHERE product_id = ?";

    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param(
        "ssssdsi", 
        $name, $brand, $category, $description,
        $price, $color, $id
    );

    if ($stmt->execute()) {
        echo "<script>
            alert('Produk berhasil diperbarui.');
            window.location.href = './list-product.php';
        </script>";
        exit();
    } else {
        echo "Gagal memperbarui produk: " . $stmt->error;
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
</head>
<body>

<h2>Edit Product</h2>

<form method="POST" action="./edit-product.php" enctype="multipart/form-data">
    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

    <label>Name:</label><br>
    <input type="text" name="product_name" value="<?php echo $product['product_name']; ?>"><br><br>

    <label>Brand:</label><br>
    <input type="text" name="product_brand" value="<?php echo $product['product_brand']; ?>"><br><br>

    <label>Category:</label><br>
    <input type="text" name="product_category" value="<?php echo $product['product_category']; ?>"><br><br>

    <label>Description:</label><br>
    <textarea name="product_description"><?php echo $product['product_description']; ?></textarea><br><br>

    <label>Price:</label><br>
    <input type="text" name="product_price" value="<?php echo $product['product_price']; ?>"><br><br>

    <label>Color:</label><br>
    <input type="text" name="product_color" value="<?php echo $product['product_color']; ?>"><br><br>

    <button type="submit" name="edit_btn">Update Product</button>
    <a href="./list-product.php">Cancel</a>
</form>

</body>
</html>

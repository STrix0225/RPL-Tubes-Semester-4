<?php
session_start();
include('../Database/connection.php');

$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : (isset($_POST['product_id']) ? intval($_POST['product_id']) : 0);

if ($product_id <= 0) {
    echo "ID produk tidak valid.";
    exit();
}

$query = $conn->prepare("SELECT * FROM product WHERE product_id = ?");
$query->bind_param("i", $product_id);
$query->execute();
$result = $query->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Produk tidak ditemukan.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_image_btn'])) {
    $foto_fields = ['image1', 'image2', 'image3'];
    $update_fields = [];
    $update_values = [];
    $targetDir = "../PictureProducts/";

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    foreach ($foto_fields as $index => $field) {
        if (!empty($_FILES[$field]['name'])) {
            $file_tmp = $_FILES[$field]["tmp_name"];
            $file_type = mime_content_type($file_tmp);

            if (!in_array($file_type, $allowed_types)) {
                echo "Tipe file tidak diizinkan untuk " . htmlspecialchars($field);
                exit();
            }

            $fileName = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', basename($_FILES[$field]['name']));
            $targetFilePath = $targetDir . $fileName;

            $db_field = "product_photo" . ($index + 1);
            $oldFile = $product[$db_field];

            if (!empty($oldFile) && $oldFile !== $fileName && file_exists($targetDir . $oldFile)) {
                unlink($targetDir . $oldFile);
            }

            if (!move_uploaded_file($file_tmp, $targetFilePath)) {
                echo "Gagal mengunggah file untuk " . htmlspecialchars($field);
                exit();
            }

            $update_fields[] = "$db_field = ?";
            $update_values[] = $fileName;
        }
    }

    if (count($update_fields) > 0) {
        $sql = "UPDATE product SET " . implode(", ", $update_fields) . " WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $types = str_repeat("s", count($update_values)) . "i";
        $update_values[] = $product_id;
        $stmt->bind_param($types, ...$update_values);

        if ($stmt->execute()) {
            echo "<script>
                alert('Gambar berhasil diperbarui.');
                window.location.href = 'edit-image.php?product_id={$product_id}';
                </script>";
                exit();
            $query->execute();
            $result = $query->get_result();
            $product = $result->fetch_assoc();
        } else {
            echo "<p style='color:red;'>Gagal memperbarui gambar: " . $stmt->error . "</p>";
        }
    } else {
        echo "<p style='color:orange;'>Tidak ada gambar yang diunggah.</p>";
    }
}
?>

<h2>Edit Gambar Produk</h2>

<form action="edit-image.php?product_id=<?php echo $product['product_id']; ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

    <p><strong>Nama Produk:</strong> <?php echo htmlspecialchars($product['product_name']); ?></p>

    <label>Gambar 1 Saat Ini:</label><br>
    <?php if (!empty($product['product_photo1'])): ?>
        <img src="../PictureProducts/<?php echo htmlspecialchars($product['product_photo1']); ?>" width="100"><br>
    <?php endif; ?>
    <input type="file" name="image1"><br><br>

    <label>Gambar 2 Saat Ini:</label><br>
    <?php if (!empty($product['product_photo2'])): ?>
        <img src="../PictureProducts/<?php echo htmlspecialchars($product['product_photo2']); ?>" width="100"><br>
    <?php endif; ?>
    <input type="file" name="image2"><br><br>

    <label>Gambar 3 Saat Ini:</label><br>
    <?php if (!empty($product['product_photo3'])): ?>
        <img src="../PictureProducts/<?php echo htmlspecialchars($product['product_photo3']); ?>" width="100"><br>
    <?php endif; ?>
    <input type="file" name="image3"><br><br>

    <button type="submit" name="update_image_btn">Update Gambar</button>
    <a href="./list-product.php">Kembali</a>
</form>

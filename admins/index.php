<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Admin</title>
</head>
<body>

    <h2>Selamat datang di Beranda Admin</h2>

    <form method="GET" action="./create-product.php" style="margin-bottom: 10px;">
        <input type="submit" value="Tambah Produk">
    </form>

    <form method="GET" action="./list-product.php" style="margin-bottom: 10px;">
        <input type="submit" value="Lihat Daftar Produk">
    </form>

    <form method="POST" action="./logout.php">
        <input type="submit" name="logout_btn" value="Logout">
    </form>

</body>
</html>

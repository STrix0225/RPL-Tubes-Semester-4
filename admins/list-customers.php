<?php
session_start();
include('../Database/connection.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM customers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>List Customers</title>
</head>
<body>

<h2>Daftar Customers</h2>
<p><a href="index.php">Dashboard</a> > Customers</p>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Telepon</th>
        <th>Alamat</th>
        <th>Kota</th>
        <th>Foto</th>
        <th>Aksi</th>
    </tr>

    <?php
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['customer_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['customer_email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['customer_phone']) . "</td>";
            echo "<td>" . htmlspecialchars($row['customer_address']) . "</td>";
            echo "<td>" . htmlspecialchars($row['customer_city']) . "</td>";
            echo "<td>";
            if (!empty($row['customer_photo'])) {
                echo "<img src='../img/" . htmlspecialchars($row['customer_photo']) . "' width='80'>";
            } else {
                echo "Tidak ada foto";
            }
            echo "</td>";
            echo "<td>";
            echo "<form action='delete-customers.php' method='POST' onsubmit=\"return confirm('Yakin ingin menghapus customer ini?');\">";
            echo "<input type='hidden' name='customer_id' value='" . htmlspecialchars($row['customer_id']) . "'>";
            echo "<button type='submit'>Hapus</button>";
            echo "</form>";
            echo "</td>";

            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8'>Tidak ada customer ditemukan.</td></tr>";
    }
    ?>
</table>

</body>
</html>

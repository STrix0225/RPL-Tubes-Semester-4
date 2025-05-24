<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/../Database/connection.php';

$sql = "SELECT COUNT(DISTINCT product_brand) AS total FROM product";
$result = mysqli_query($conn, $sql);

if ($result && $row = mysqli_fetch_assoc($result)) {
    echo $row['total'] . ' BRAND';
} else {
    echo "0 BRAND";
}

mysqli_close($conn);
?>

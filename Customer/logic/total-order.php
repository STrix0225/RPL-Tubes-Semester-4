<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/../Database/connection.php';

$sql = "SELECT COUNT(*) AS total FROM orders";
$result = mysqli_query($conn, $sql);

if ($result && $row = mysqli_fetch_assoc($result)) {
    echo $row['total'] . ' ORDER';
} else {
    echo "0 ORDER";
}

mysqli_close($conn);
?>

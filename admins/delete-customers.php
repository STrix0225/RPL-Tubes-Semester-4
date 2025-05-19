<?php
session_start();
include('../Database/connection.php');

if (!isset($_SESSION['admin_logged_in'])) {
    echo "<script>alert('Akses ditolak!'); window.location.href = 'login.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_id'])) {
    $customer_id = intval($_POST['customer_id']);
    $stmt_select = $conn->prepare("SELECT customer_photo FROM customers WHERE customer_id = ?");
    if ($stmt_select === false) {
        echo "<script>alert('Gagal mempersiapkan perintah SQL untuk select.'); window.location.href = 'list-customers.php';</script>";
        exit;
    }
    $stmt_select->bind_param("i", $customer_id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $photo = $row['customer_photo'];
    } else {
        $photo = null;
    }
    $stmt_select->close();
    $stmt_delete = $conn->prepare("DELETE FROM customers WHERE customer_id = ?");
    if ($stmt_delete === false) {
        echo "<script>alert('Gagal mempersiapkan perintah SQL untuk delete.'); window.location.href = 'list-customers.php';</script>";
        exit;
    }
    $stmt_delete->bind_param("i", $customer_id);

    if ($stmt_delete->execute()) {
        if (!empty($photo)) {
            $file_path = '../img/' . $photo;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        echo "<script>alert('Customer telah dihapus.'); window.location.href = 'list-customers.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus customer.'); window.location.href = 'list-customers.php';</script>";
    }

    $stmt_delete->close();
    $conn->close();
} else {
    echo "<script>alert('Permintaan tidak valid.'); window.location.href = 'list-customers.php';</script>";
    exit;
}
?>

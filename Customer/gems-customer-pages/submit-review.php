<?php
include('../../Database/connection.php');

// Cek apakah user sudah login
if (!isset($_SESSION['customer_id'])) {
    header("Location: login-customer.php");
    exit();
}

// Validasi input
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $customer_id = (int)$_SESSION['customer_id'];
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $review_text = trim($_POST['message']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    // Validasi rating (1-5)
    if ($rating < 1 || $rating > 5) {
        $_SESSION['review_error'] = "Please select a rating between 1 and 5 stars";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Validasi review text
    if (empty($review_text)) {
        $_SESSION['review_error'] = "Review text cannot be empty";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }


    // Simpan review ke database
    try {
        $stmt = $conn->prepare("INSERT INTO reviews (product_id, customer_id, rating, review_text, review_date) 
                               VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param('iiis', $product_id, $customer_id, $rating, $review_text);
        $stmt->execute();

        $_SESSION['review_success'] = "Thank you for your review!";
        header("Location: shop-detail.php?id=$product_id#tab_3");
        exit();
    } catch (Exception $e) {
        $_SESSION['review_error'] = "Failed to submit review: " . $e->getMessage();
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
} else {
    header("Location: shop.php");
    exit();
}
?>
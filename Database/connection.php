<?php
    $conn = mysqli_connect("localhost", "root", "", "gems_web") 
        or die("Can't connect to the database");
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");


// Helper function to redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}
?>
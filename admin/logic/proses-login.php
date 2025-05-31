<?php
session_start();
include '../../Database/connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password_input = $_POST['password'];

    // Coba login sebagai admin
    $admin = checkAdminLogin($conn, $email, $password_input);
    if ($admin) {
        $_SESSION['login_type'] = 'admin';
        $_SESSION['user'] = $admin;
        header("Location: ../index.php");
        exit;
    }

    // Coba login sebagai customer
    $customer = checkCustomerLogin($conn, $email, $password_input);
    if ($customer) {
        $_SESSION['login_type'] = 'customer';
        $_SESSION['user'] = $customer;
        header("Location: ../../UI/dashboard-customers.php");
        exit;
    }

    // Jika gagal
    $_SESSION['login_error'] = "Email atau password salah!";
    header("Location: /../login.php");
    exit;
}

// ===================
// Fungsi Login Admin
// ===================
function checkAdminLogin($conn, $email, $password) {
    $query = "SELECT * FROM admins WHERE admin_email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($admin = mysqli_fetch_assoc($result)) {
        // Password admin disimpan pakai MD5
        if (md5($password) === $admin['admin_password']) {
            return $admin;
        }
    }
    return false;
}

// =======================
// Fungsi Login Customer
// =======================
function checkCustomerLogin($conn, $email, $password) {
    $query = "SELECT * FROM customers WHERE customer_email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $customer = mysqli_fetch_assoc($result);
        if (md5($password) === $customer['customer_password']) {
            return $customer;
        }
    }
    return false;
}

?>

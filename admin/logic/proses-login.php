<?php

session_start();
include '../../UI/Database/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password_input = trim($_POST['password']);

    // Panggil fungsi login admin dan customer
    $admin = checkAdminLogin($conn, $email, $password_input);
    $customer = checkCustomerLogin($conn, $email, $password_input);

    // Coba login sebagai admin
    if ($admin) {
        $_SESSION['login_type'] = 'admin';
        $_SESSION['user'] = $admin;  // Simpan data admin di session
        $_SESSION['login_success'] = true; // Tandai login berhasil
        header("Location: ../index.php");
        exit;
    }

    // Coba login sebagai customer
    if ($customer) {
        $_SESSION['login_type'] = 'customer';
        $_SESSION['user'] = $customer;  // Simpan data customer di session
        $_SESSION['login_success'] = true; // Tandai login berhasil
        header("Location: ../../index.php");
        exit;
    }

    // Jika gagal login
    $_SESSION['login_error'] = "Email atau password salah!";
    header("Location: ../../login.php?error=1");
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
        // Perhatikan disini kamu pakai 'admin_password' harusnya 'customer_password' kalau sesuai tabel customer
        if (md5($password) === $customer['customer_password']) {
            return $customer;
        }
    }
    return false;
}
?>

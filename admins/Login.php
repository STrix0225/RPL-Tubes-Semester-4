<?php
session_start();
include('../Database/connection.php');

if (isset($_POST['login_btn'])) {
    $email = trim($_POST['email']);
    $password = md5(trim($_POST['password']));

    $query = "SELECT admin_id, admin_name FROM admins WHERE admin_email = ? AND admin_password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows() == 1) {
        $stmt->bind_result($admin_id, $admin_name);
        $stmt->fetch();

        $_SESSION['admin_id'] = $admin_id;
        $_SESSION['admin_name'] = $admin_name;
        $_SESSION['admin_logged_in'] = true;

        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <!-- Animasi kucing selalu muncul -->
    <div style="text-align: center; margin-bottom: 20px;">
        <h3 style="animation: fadeIn 2s ease-in-out;">🐾 Selamat Datang Atmin! 🐾</h3>
        <img src="./cat.gif/download.gif" alt="Kucing muter-muter" width="160" style="animation: spin 5s linear infinite;">
    </div>

    <h2>Login Admin</h2>

    <?php
    if (isset($_GET['error'])) {
        echo '<p style="color:red;">' . htmlspecialchars($_GET['error']) . '</p>';
    }

    if (isset($_SESSION['success_message'])) {
        echo '<p style="color:green;">' . $_SESSION['success_message'] . '</p>';
        unset($_SESSION['success_message']);
    }
    ?>

    <form method="POST" action="login.php">
        <label for="email">Email:</label><br>
        <input type="email" name="email" id="email" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" name="password" id="password" required><br><br>

        <input type="submit" name="login_btn" value="Login"><br><br>
    </form>

    <p><strong><a href="./forgot-password.php">Lupa Password?</a></strong></p>
    <p>Belum punya akun? <a href="./register.php">Daftar di sini</a>.</p>
    <p><a href="../Customers/login.php">Kembali ke Login Customers</a>.</p>

</body>
</html>

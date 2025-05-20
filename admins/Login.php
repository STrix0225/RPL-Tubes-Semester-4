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
</head>
<body>

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

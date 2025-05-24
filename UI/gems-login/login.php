<?php
session_start();
include '../Database/connection.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Sesuaikan jika DB pakai md5

    // Cek di tabel admins
    $query_admin = mysqli_query($conn, "SELECT * FROM admins WHERE admin_email='$email' AND admin_password='$password'");
    if (mysqli_num_rows($query_admin) > 0) {
        $_SESSION['login_type'] = 'admin';
        $_SESSION['user'] = mysqli_fetch_assoc($query_admin);
        header("Location: ../dashboard-Admin.php");
        exit;
    }

    // Cek di tabel customers
    $query_customer = mysqli_query($conn, "SELECT * FROM customers WHERE customer_email='$email' AND customer_password='$password'");
    if (mysqli_num_rows($query_customer) > 0) {
        $_SESSION['login_type'] = 'customer';
        $_SESSION['user'] = mysqli_fetch_assoc($query_customer);
        header("Location: ../dashboard-customers.php");
        exit;
    }

    $error = "Email atau password salah!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | GEMS</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>
  <div class="login-container">
    <form action="login.php" method="POST" class="login-form">
      <h2>Welcome to GEMS</h2>

      <div class="input-group">
        <label>Email</label>
        <input type="email" name="email" required />
      </div>

      <div class="input-group">
        <label>Password</label>
        <input type="password" name="password" id="password" required />
      </div>

      <div class="checkbox-group">
        <input type="checkbox" id="showPassword" onclick="togglePassword()">
        <label for="showPassword">Tampilkan Password</label>
      </div>

      <button type="submit">Login</button>

      <?php if ($error): ?>
        <p class="error"><?= $error; ?></p>
      <?php endif; ?>
    </form>
  </div>

  <script>
    function togglePassword() {
      const passwordInput = document.getElementById("password");
      passwordInput.type = passwordInput.type === "password" ? "text" : "password";
    }
  </script>
</body>
</html>

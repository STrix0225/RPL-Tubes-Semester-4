<?php
// Display error message if login failed
if (isset($_GET['error']) && $_GET['error'] == 1) {
echo '
<div class="alert alert-danger" style="
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    background-color: #f44336;
    color: white;
    padding: 15px 20px;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
">
    <strong>Error!</strong> Email atau password salah!
    <span style="cursor:pointer; float:right; margin-left:10px;" onclick="this.parentElement.remove();">&times;</span>
</div>
<script>
  setTimeout(function() {
      const alert = document.querySelector(".alert");
      if(alert) alert.remove();
  }, 5000);
</script>
';

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | GEMS</title>
  <link rel="stylesheet" href="admin/UI_Login/css/login.css">
  <link rel="shortcut icon" href="admin/UI_Login/img/favicon.png">
</head>
<body>
  <div class="login-container">
    <form class="login-form" method="POST" action="admin/logic/proses-login.php">
      <h2>Welcome to GEMS</h2>

      <div class="input-group">
        <label for="email">Email *</label>
        <input type="email" id="email" name="email" required>
      </div>

      <div class="input-group">
        <label for="password">Password *</label>
        <input type="password" id="password" name="password" required>
      </div>

      <div class="checkbox-group">
        <input type="checkbox" id="showPassword" onclick="togglePassword()">
        <label for="showPassword">Show Password</label>
      </div>

      <button type="submit">Login</button>

      <p class="sign-up">
        Don't have an Account? <a href="register-customer.php">Sign Up</a>
      </p>
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

<?php
session_start();
include '../Database/connection.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password_input = $_POST['password'];

    // Ambil data customer berdasarkan email
    $query_customer = mysqli_query($conn, "SELECT * FROM customers WHERE customer_email='$email'");

    if (mysqli_num_rows($query_customer) > 0) {
        $data = mysqli_fetch_assoc($query_customer);

        // Verifikasi password dengan password_verify
        if (password_verify($password_input, $data['customer_password'])) {
            $_SESSION['login_type'] = 'customer';
            $_SESSION['user'] = $data;
            header("Location: ../gems-customer-pages/dashboard.php");
            exit;
        }
    }

    // Kalau email tidak ditemukan atau password salah
    $error = "Email atau password salah!";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Customer Login</title>
  <link rel="stylesheet" href="../assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="shortcut icon" href="../gems-customer-pages/images/Background3.jpg" />
  <style>
  
    .error-message {
  color: #ff4c4c;
  font-weight: 500;
  margin-top: 5px;
  padding: 10px 5px;
  border-radius: 5px;
  text-align: center;
    }

  </style>
</head>
<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="row w-100 m-0">
        <div class="content-wrapper full-page-wrapper d-flex align-items-center auth login-bg">
          <div class="card col-lg-4 mx-auto">
            <div class="card-body px-5 py-5">
              <h3 class="card-title text-left mb-3">Login</h3>
              <form method="POST">
  <div class="form-group">
    <label>Email *</label>
    <input type="text" name="email" class="form-control p_input" required>
  </div>
  <div class="form-group">
    <label>Password *</label>
    <input type="password" name="password" id="password" class="form-control p_input" required>
  </div>
  <div class="form-group d-flex align-items-center justify-content-between">
    <div class="form-check">
  <input type="checkbox" class="form-check-input" id="showPassword" onclick="togglePassword()">
  <label class="form-check-label" for="showPassword">Tampilkan Password</label>
</div>

  </div>
  <div class="text-center">
    <button type="submit" class="btn btn-primary btn-block enter-btn">Login</button>
  </div>
  <p class="sign-up text-center">Don't have an account? <a href="register-customer.php">Sign Up</a></p>
</form>



<!-- Error message pindah ke bawah form -->
<?php if ($error): ?>
  <div class="error-message text-center mt-2"><?= $error; ?></div>
<?php endif; ?>


            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function togglePassword() {
      const pass = document.getElementById("password");
      pass.type = pass.type === "password" ? "text" : "password";
    }
  </script>

  <script src="../assets/vendors/js/vendor.bundle.base.js"></script>
  <script src="../assets/js/off-canvas.js"></script>
  <script src="../assets/js/hoverable-collapse.js"></script>
  <script src="../assets/js/misc.js"></script>
  <script src="../assets/js/settings.js"></script>
  <script src="../assets/js/todolist.js"></script>
</body>
</html>

<?php
session_start();
include('../../Database/connection.php');

if (isset($_POST['login_btn'])) {
    $email = trim($_POST['email']);
    $password = md5(trim($_POST['password']));

    $query = "SELECT admin_id, admin_name FROM admins WHERE admin_email = ? AND admin_password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows() == 1) {
    header("Location: /index.php");
    exit();
      } else {
    header("Location: login.php?error=Email atau password salah");
    exit();
      }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login Admin GEMS</title>
    <script>
        function togglePasswordVisibility() {
            var pass = document.getElementById("password");
            var confirm = document.getElementById("password");
            if (pass.type === "password") {
                pass.type = "text";
                confirm.type = "text";
            } else {
                pass.type = "password";
                confirm.type = "password";
            }
        }
    </script>
    <link rel="stylesheet" href="../../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../../assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="shortcut icon" href="../../assets/images/favicon.png" />
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="row w-100 m-0">
          <div class="content-wrapper full-page-wrapper d-flex align-items-center auth login-bg">
            <div class="card col-lg-4 mx-auto">
              <div class="card-body px-5 py-5">
                <h3 class="card-title text-left mb-3">Login Admin</h3>
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
                  <div class="form-group">
                    <label>Username or email *</label>
                    <input type="text" class="form-control p_input" name="email" id="email" required>
                  </div>
                  <div class="form-group">
                    <label>Password *</label>
                    <input type="password" class="form-control p_input" name="password" required>
                    <input type="checkbox" onclick="togglePasswordVisibility()"> Tampilkan Password<br>
                  </div>
                  <div class="form-group d-flex align-items-center justify-content-between">
                    <div class="form-check">
                      <label class="form-check-label">
                        <input type="checkbox" class="form-check-input">Remember me</label>
                    </div>
                    <a href="register.php" class="forgot-pass">Forgot password</a><!--error handling klo lupa password mau gimana?-->
                  </div>
                  <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-block enter-btn" name="login_btn" value="Login">Login</button>
                  </div>
                  <div class="d-flex">
                    <button class="btn btn-facebook mr-2 col">
                      <i class="mdi mdi-facebook"></i> Facebook </button>
                    <button class="btn btn-google col">
                      <i class="mdi mdi-google-plus"></i> Google plus </button>
                  </div>
                  <p class="sign-up">Belum punya akun? <a href="./register.php">Daftar di sini</a></p>
                  <p class="sign-up"><a href="../Customers/login.php">Kembali ke Login Customers</a></p>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="../../assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="../../assets/js/off-canvas.js"></script>
    <script src="../../assets/js/hoverable-collapse.js"></script>
    <script src="../../assets/js/misc.js"></script>
    <script src="../../assets/js/settings.js"></script>
    <script src="../../assets/js/todolist.js"></script>
  </body>
</html>
<?php
session_start();
include('../../Database/connection.php');

if (isset($_SESSION['admin_logged_in'])) {
    header('location: index.php');
    exit;
}
if (isset($_POST['register_btn'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Password tidak cocok.";
    } else {
        $hashed_password = md5($password);
        $admin_name = $first_name . ' ' . $last_name;

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['photo']['tmp_name'];
    $first_name_clean = strtolower(str_replace(' ', '', $first_name));
    $photo_file = $first_name_clean . ".jpg";
    
    $target_dir = "./photos/";
    $target_path = $target_dir . $photo_file;

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    if (move_uploaded_file($file_tmp, $target_path)) {
        $query = "INSERT INTO admins (admin_name, admin_email, admin_phone, admin_password, admin_photo) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $admin_name, $email, $phone, $hashed_password, $photo_file);

        if ($stmt->execute()) {
    echo "<script>
        alert('Akun berhasil dibuat, silakan login.');
        window.location.href = 'login.php';
    </script>";
    exit;
}


        $stmt->close();
    } else {
        $error = "Gagal mengunggah foto.";
    }
} else {
    $error = "Foto wajib diunggah.";
}
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Registrasi Admin GEMS</title>
    <script>
        function togglePasswordVisibility() {
            var pass = document.getElementById("password");
            var confirm = document.getElementById("confirm_password");
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
                <h3 class="card-title text-left mb-3">Registrasi Admin</h3>

                <?php if (isset($error)): ?>
                  <p style="color:red;"><?php echo $error; ?></p>
                <?php endif; ?>

                <form method="POST" action="register.php" enctype="multipart/form-data">
                  <div class="form-group">
                    <label>Nama Depan</label>
                    <input type="text" class="form-control p_input" id="first_name" name="first_name" required>
                  </div>

                  <div class="form-group">
                    <label>Nama Belakang</label>
                    <input type="text" class="form-control p_input" id="last_name" name="last_name" required>
                  </div>

                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control p_input" id="email" name="email" required>
                  </div>

                  <div class="form-group">
                    <label>No Telepon</label>
                    <input type="phone" class="form-control p_input"  id="phone" name="phone" required>
                  </div>

                  <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control p_input" id="password" name="password" required>
                  </div>

                  <div class="form-group">
                    <label>Ulangi Password</label>
                    <input type="password" class="form-control p_input" id="confirm_password" name="confirm_password" required>
                    <input type="checkbox" onclick="togglePasswordVisibility()"> Tampilkan Password<br>
                  </div>

                  <div class="form-group">
                    <label class="form-label">Upload Foto</label>
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="photo" name="photo" accept="image/*" required>
                    <label class="custom-file-label form-control p_input">Pilih file...</label>
                  </div>
                  <small class="form-text text-muted">Format: JPG, PNG (Maks. 2MB)</small>
                  </div>

                  <div class="form-group d-flex align-items-center justify-content-between">
                    <div class="form-check">
                      <label class="form-check-label">
                        <input type="checkbox" class="form-check-input"> Remember me </label>
                    </div>
                    <a href="#" class="forgot-pass">Forgot password</a>
                  </div>
                  <div class="text-center">
                    <button type="submit" name="register_btn" class="btn btn-primary btn-block enter-btn" value="Register Account">Submit</button>
                  </div>
                  <p class="sign-up text-center">Already have an Account?<a href="./login.php">Login!</a></p>
                  <!--<p class="terms">By creating an account you are accepting our<a href="#"> Terms & Conditions</a></p>-->
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
    <script>
      document.querySelector('.custom-file-input').addEventListener('change', function(e) {
      var fileName = e.target.files[0] ? e.target.files[0].name : "Pilih file...";
      var label = this.nextElementSibling;
      label.textContent = fileName;
        if (fileName.length > 50) {
          label.textContent = fileName.substring(0, 15) + '...' + fileName.substring(fileName.length - 10);
        }
      });
    </script>
  </body>
</html>
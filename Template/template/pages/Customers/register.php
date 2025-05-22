<?php
session_start();
include('../../Database/connection.php');

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $address = $_POST['address'];

    if ($_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo = $_FILES['photo']['tmp_name'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($photo);
        
        if (!in_array($file_type, $allowed_types)) {
            header('location: register.php?error=Invalid file type. Only JPG, PNG, and GIF are allowed.');
            exit;
        }
        
        $photo_name = str_replace(' ', '_', $name) . ".jpg";
        move_uploaded_file($photo, "../img/" . $photo_name);
    } else {
        $photo_name = 'default.jpg';
    }

    if ($password !== $confirm_password) {
        header('location: register.php?error=Password did not match');
        exit;
    } else if (strlen($password) < 6) {
        header('location: register.php?error=Password must be at least 6 characters');
        exit;
    } else {
        $query_check_customer = "SELECT COUNT(*) FROM customers WHERE customer_email = ?";
        $stmt_check_customer = $conn->prepare($query_check_customer);
        $stmt_check_customer->bind_param('s', $email);
        $stmt_check_customer->execute();
        $stmt_check_customer->bind_result($num_rows);
        $stmt_check_customer->store_result();
        $stmt_check_customer->fetch();

        if ($num_rows !== 0) {
            header('location: register.php?error=Email telah terdaftar!');
            exit;
        } else {
            $query_save_customer = "INSERT INTO customers (customer_name, customer_email, customer_password, customer_phone, customer_address, customer_city, customer_photo) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt_save_customer = $conn->prepare($query_save_customer);
            $hashed_password = md5($password); // Gunakan password_hash() jika memungkinkan
            $stmt_save_customer->bind_param('sssssss', $name, $email, $hashed_password, $phone, $address, $city, $photo_name);

            if ($stmt_save_customer->execute()) {
                $customer_id = $stmt_save_customer->insert_id;

                $_SESSION['customer_id'] = $customer_id;
                $_SESSION['customer_email'] = $email;
                $_SESSION['customer_name'] = $name;
                $_SESSION['customer_phone'] = $phone;
                $_SESSION['customer_address'] = $address;
                $_SESSION['customer_city'] = $city;
                $_SESSION['customer_photo'] = $photo_name;
                $_SESSION['logged_in'] = true;

                header('location: account.php?register_success=You registered successfully!');
                exit;
            } else {
                header('location: register.php?error=Could not create an account at the moment');
                exit;
            }
        }
    }
}
?>

<?php include('layouts/header.php'); ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Registrasi Customer GEMS</title>
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
                <h3 class="card-title text-left mb-3">Hello !</h3>
                <h5 class="card-title text-left mb-3">Welcome to GEMS</h5>
                <h6 class="card-title text-left mb-3">Please fill the form first</h6>

                <?php if (isset($_GET['error'])): ?>
                    <p style="color:red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
                <?php endif; ?>
                
                <form method="POST" action="register.php" enctype="multipart/form-data">
                  <div class="form-group">
                    <label>Nama</label>
                    <input type="text" class="form-control p_input" id="first_name" name="name" required>
                  </div>

                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control p_input" id="email" name="email" required>
                  </div>
              
                  <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control p_input" id="password" name="password" required>
                  </div>

                  <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" class="form-control p_input" id="confirm_password" name="confirm_password" required>
                    <input type="checkbox" onclick="togglePasswordVisibility()"> Tampilkan Password<br>
                  </div>

                  <div class="form-group">
                    <label>No Telepon</label>
                    <input type="phone" class="form-control p_input"  id="phone" name="phone" required>
                  </div>

                  <div class="form-group">
                    <label>Kota</label>
                    <input type="phone" class="form-control p_input"  id="city" name="city" required>
                  </div>

                  <div class="form-group">
                    <label>Alamat</label>
                    <input type="phone" class="form-control p_input"  id="address" name="address" required>
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
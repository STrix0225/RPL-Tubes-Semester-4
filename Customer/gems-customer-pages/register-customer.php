<?php
include '../../Database/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $name = htmlspecialchars(trim($_POST['customer_name']));
    $email = filter_var(trim($_POST['customer_email']), FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['customer_password'], PASSWORD_DEFAULT);
    $phone = htmlspecialchars(trim($_POST['customer_phone']));
    $address = htmlspecialchars(trim($_POST['customer_address']));
    $city = htmlspecialchars(trim($_POST['customer_city']));

    // File upload handling
    $uploadOk = false;
    $path = null;
    
    if (isset($_FILES['customer_photo']) && $_FILES['customer_photo']['error'] === UPLOAD_ERR_OK) {
        $dir = "uploads/";
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedType = finfo_file($fileInfo, $_FILES['customer_photo']['tmp_name']);
        finfo_close($fileInfo);
        
        if (in_array($detectedType, $allowedTypes)) {
            $extension = pathinfo($_FILES['customer_photo']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $path = $dir . $filename;
            
            if (move_uploaded_file($_FILES['customer_photo']['tmp_name'], $path)) {
                $uploadOk = true;
            }
        }
    }

    if (!$uploadOk) {
        die("<script>alert('File upload failed. Please try again.'); window.location='register_customer.php';</script>");
    }

    // Check database connection
    if (!$conn) {
        die("<script>alert('Database connection failed.'); window.location='register_customer.php';</script>");
    }

    // Prepare and execute SQL with prepared statement
    $query = "INSERT INTO customers
              (customer_name, customer_email, customer_password, customer_phone, customer_address, customer_city, customer_photo)
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        // Show the error (for debugging, remove in production)
        die("<script>alert('Database error: " . mysqli_error($conn) . "'); window.location='register_customer.php';</script>");
    }
    
    mysqli_stmt_bind_param($stmt, 'sssssss', $name, $email, $password, $phone, $address, $city, $path);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Registration successful!'); window.location='login-customer.php';</script>";
    } else {
        error_log("Database error: " . mysqli_error($conn));
        echo "<script>alert('Registration failed. Please try again.'); window.location='register_customer.php';</script>";
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Customer Registration</title>
    <link rel="stylesheet" href="../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="shortcut icon" href="../assets/images/favicon.png" />
    <style>
      #file_name_display {
        background-color: rgb(47, 54, 63);
        color: rgb(203, 213, 223); 
      }
      .error-message {
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 0.25rem;
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
                <h3 class="card-title text-left mb-3">Register</h3>
                <form id="registrationForm" action="../gems-login/register-customer.php" method="POST" enctype="multipart/form-data" novalidate>
                  <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="customer_name" class="form-control p_input" required>
                    <div class="error-message" id="name-error"></div>
                  </div>
                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="customer_email" class="form-control p_input" required>
                    <div class="error-message" id="email-error"></div>
                  </div>
                  <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="customer_password" id="password" class="form-control p_input" required minlength="8">
                    <div class="error-message" id="password-error"></div>
                  </div>
                  <div class="form-group">
                    <label>Telepon</label>
                    <input type="text" name="customer_phone" class="form-control p_input" required>
                    <div class="error-message" id="phone-error"></div>
                  </div>
                  <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" name="customer_address" class="form-control p_input" required>
                    <div class="error-message" id="address-error"></div>
                  </div>
                  <div class="form-group">
                    <label>Kota</label>
                    <input type="text" name="customer_city" class="form-control p_input" required>
                    <div class="error-message" id="city-error"></div>
                  </div>
                  <div class="form-group">
                    <label>Foto (JPEG/PNG only)</label>
                    <input type="file" name="customer_photo" id="customer_photo" class="d-none" required accept="image/jpeg,image/png" onchange="updateFileName()">
                    <div class="input-group">
                      <input type="text" id="file_name_display" class="form-control" placeholder="Pilih file..." readonly>
                      <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('customer_photo').click();">
                          <i class="mdi mdi-upload"></i> Upload
                        </button>
                      </div>
                    </div>
                    <div class="error-message" id="photo-error"></div>
                  </div>
                  <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-block enter-btn">Register</button>
                  </div>
                  <p class="sign-up text-center mt-3">Already have an Account?<a href="login-customer.php"> Login</a></p>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="../assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="../assets/js/off-canvas.js"></script>
    <script src="../assets/js/hoverable-collapse.js"></script>
    <script src="../assets/js/misc.js"></script>
    
    <script>
      function updateFileName() {
        const fileInput = document.getElementById('customer_photo');
        const fileNameDisplay = document.getElementById('file_name_display');
        if (fileInput.files.length > 0) {
          fileNameDisplay.value = fileInput.files[0].name;
        } else {
          fileNameDisplay.value = '';
        }
      }

      // Basic client-side validation
      document.getElementById('registrationForm').addEventListener('submit', function(e) {
        let isValid = true;
        const fields = ['customer_name', 'customer_email', 'customer_password', 'customer_phone', 'customer_address', 'customer_city'];
        
        fields.forEach(field => {
          const element = document.querySelector(`[name="${field}"]`);
          const errorElement = document.getElementById(`${field.replace('customer_', '')}-error`);
          
          if (!element.value.trim()) {
            errorElement.textContent = 'This field is required';
            isValid = false;
          } else {
            errorElement.textContent = '';
          }
        });

        // Password validation
        const password = document.getElementById('password');
        const passwordError = document.getElementById('password-error');
        if (password.value.length < 8) {
          passwordError.textContent = 'Password must be at least 8 characters';
          isValid = false;
        } else {
          passwordError.textContent = '';
        }

        // Photo validation
        const photo = document.getElementById('customer_photo');
        const photoError = document.getElementById('photo-error');
        if (photo.files.length === 0) {
          photoError.textContent = 'Please upload a photo';
          isValid = false;
        } else {
          photoError.textContent = '';
        }

        if (!isValid) {
          e.preventDefault();
        }
      });
    </script>
  </body>
</html>
<?php
session_start();
include('../Database/connection.php');

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
    <meta charset="UTF-8">
    <title>Admin - Register</title>
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
</head>
<body>

    <h2>Buat Akun Admin</h2>

    <?php if (isset($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="register.php" enctype="multipart/form-data">
        <label for="first_name">Nama depan:</label><br>
        <input type="text" id="first_name" name="first_name" required><br><br>

        <label for="last_name">Nama belakang:</label><br>
        <input type="text" id="last_name" name="last_name" required><br><br>

        <label for="email">Alamat Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="phone">No telepon:</label><br>
        <input type="phone" id="phone" name="phone" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
    
        <label for="confirm_password">Ulangi Password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>
        <input type="checkbox" onclick="togglePasswordVisibility()"> Tampilkan Password<br><br>
        
        <label for="photo">Upload Foto:</label><br>
        <input type="file" name="photo" id="photo" accept="image/*" required><br><br>

        <input type="submit" name="register_btn" value="Register Account"><br><br>
        <hr>

        <p><a href="./index.php">Register dengan Google</a></p>
        <p><a href="./index.php">Register dengan Facebook</a></p>
    </form>

    <p><a href="forgot-password.html">Lupa password?</a></p>
    <p><a href="./login.php">Sudah punya akun? Login!</a></p>

</body>
</html>

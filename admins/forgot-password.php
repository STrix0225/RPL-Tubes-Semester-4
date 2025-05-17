<?php
session_start();
include('../Database/connection.php');

$step = 1;
$email = '';

if (isset($_POST['submit_email'])) {
    $email = trim($_POST['email']);

    $query = "SELECT admin_id FROM admins WHERE admin_email = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows() == 1) {
        $_SESSION['reset_email'] = $email;
        $step = 2;
    } else {
        $error = "Email tidak terdaftar.";
    }

    $stmt->close();
}

if (isset($_POST['reset_password'])) {
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($new_password !== $confirm_password) {
        $error = "Password tidak cocok.";
        $step = 2;
    } else {
        $hashed_password = md5($new_password);
        $email = $_SESSION['reset_email'];

        $update = "UPDATE admins SET admin_password = ? WHERE admin_email = ?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param('ss', $hashed_password, $email);

       if ($stmt->execute()) {
    unset($_SESSION['reset_email']);
    echo "<script>
        alert('Password berhasil diubah. Silakan login.');
        window.location.href = 'login.php';
    </script>";
    exit;
} else {
    $error = "Gagal mengubah password.";
    $step = 2;
}

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password</title>
</head>
<body>

<h2>Lupa Password</h2>

<?php if (isset($error)): ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php elseif (isset($success)): ?>
    <p style="color:green;"><?php echo $success; ?></p>
<?php endif; ?>

<?php if ($step === 1): ?>
    <form method="POST" action="forgot-password.php">
        <label for="email">Masukkan Email Anda:</label><br>
        <input type="email" name="email" id="email" required><br><br>
        <input type="submit" name="submit_email" value="Lanjutkan">
    </form>
<?php elseif ($step === 2): ?>
    <form method="POST" action="forgot-password.php">
        <p>Email: <strong><?php echo htmlspecialchars($_SESSION['reset_email']); ?></strong></p>

        <label for="new_password">Password Baru:</label><br>
        <input type="password" name="new_password" id="new_password" required>
        <input type="checkbox" id="showPassword" onclick="togglePassword()"> Tampilkan Password<br><br>

        <label for="confirm_password">Konfirmasi Password:</label><br>
        <input type="password" name="confirm_password" id="confirm_password" required><br><br>

        <input type="submit" name="reset_password" value="Ubah Password">
    </form>
<?php endif; ?>

<script>
function togglePassword() {
    var passwordField = document.getElementById("new_password");
    if (passwordField.type === "password") {
        passwordField.type = "text";
    } else {
        passwordField.type = "password";
    }
}
</script>

</body>
</html>

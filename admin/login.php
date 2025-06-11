<?php
session_start();
require_once '../Database/connection.php';
require_once '../vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// Function generate dan kirim OTP
function generateOTP() {
    return strval(rand(100000, 999999));
}

function sendOTP($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'gevam2024@gmail.com';     // Ganti sesuai email pengirim
        $mail->Password   = 'hufk tbmn egug uqsk';            // Ganti sesuai password/email app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('no-reply@gemsadmin.com', 'GEMS Admin');
        $mail->addAddress($email);
        
        $mail->isHTML(true);
        $mail->Subject = 'Your GEMS Admin Login OTP';
        $mail->Body    = "Your OTP code is: <b>$otp</b><br><br>This code will expire in 10 minutes.";
        $mail->AltBody = "Your OTP code is: $otp\n\nThis code will expire in 10 minutes.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

function clearOTP($email) {
    global $conn;
    $stmt = $conn->prepare("UPDATE admins SET otp_code = NULL, otp_expiry = NULL WHERE admin_email = ?");
    $stmt->bind_param("s", $email);
    return $stmt->execute();
}

function verifyOTP($email, $userOTP) {
    global $conn;
    $stmt = $conn->prepare("SELECT otp_code, otp_expiry FROM admins WHERE admin_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if ($admin['otp_code'] === $userOTP && strtotime($admin['otp_expiry']) > time()) {
            return true;
        }
    }
    return false;
}

$error = '';

// Step 1: Jika sudah login
if (isAdminLoggedIn()) {
    redirect('index.php');
}

// Step 2: Jika verifikasi OTP
if (isset($_POST['verify_otp'])) {
    $otp = trim($_POST['otp']);
    $email = $_SESSION['temp_email'];

    if (verifyOTP($email, $otp)) {
        $stmt = $conn->prepare("SELECT admin_id, admin_name FROM admins WHERE admin_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['admin_name'];
            $_SESSION['admin_email'] = $email;
            clearOTP($email);
            unset($_SESSION['temp_email']);
            redirect('index.php');
        }
    } else {
        $error = "Invalid or expired OTP.";
    }
}
// Step 3: Login form submitted
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT admin_password FROM admins WHERE admin_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            if (md5($password) === $admin['admin_password']) {
                // Kirim OTP
                $otp = generateOTP();
                $expiry = date('Y-m-d H:i:s', time() + 600); // 10 menit
                $update = $conn->prepare("UPDATE admins SET otp_code = ?, otp_expiry = ? WHERE admin_email = ?");
                $update->bind_param("sss", $otp, $expiry, $email);
                $update->execute();

                if (sendOTP($email, $otp)) {
                    $_SESSION['temp_email'] = $email;
                } else {
                    $error = "Failed to send OTP. Please try again.";
                }
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!-- HTML FORM -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GEMS Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">
</head>
<body>
    <div id="stars"></div>
    <div class="twinkling"></div>

    <div class="container login-container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="card floating">
                    <div class="card-header text-center">
                        <h3><i class="fas fa-gem me-2"></i>GEMS Admin</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['temp_email'])): ?>
                            <!-- OTP Form -->
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="otp" class="form-label">Enter OTP</label>
                                    <input type="text" class="form-control" id="otp" name="otp" required>
                                </div>
                                <button type="submit" name="verify_otp" class="btn btn-success w-100">Verify OTP</button>
                            </form>
                        <?php else: ?>
                            <!-- Login Form -->
                            <form method="POST">
                                <div class="mb-3">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Login</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Animasi perangkat -->
    <div class="flying-devices">
        <div class="flying-device laptop" style="animation-delay: 0s;"></div>
        <div class="flying-device smartphone" style="animation-delay: 5s;"></div>
        <div class="flying-device desktop" style="animation-delay: 10s;"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

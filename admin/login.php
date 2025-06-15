<?php
require_once '../Database/connection.php';  // Use consistent connection file
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Start session properly
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to generate OTP
function generateOTP() {
    return strval(rand(100000, 999999));
}

// Function to send OTP
function sendOTP($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'gevam2024@gmail.com';
        $mail->Password   = 'hufk tbmn egug uqsk';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('no-reply@gemsadmin.com', 'GEMS Admin');
        $mail->addAddress($email);
        
        $mail->isHTML(true);
        $mail->Subject = 'Your GEMS Admin Login OTP';
        $mail->Body    = "Your OTP code is: <b>$otp</b><br><br>This code will expire in 1 minute.";
        $mail->AltBody = "Your OTP code is: $otp\n\nThis code will expire in 1 minute.";

        return $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Handle AJAX resend OTP request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["resend_otp"])) {
    header('Content-Type: application/json');
    
    if (!isset($_SESSION["temp_username"])) {
        echo json_encode(["success" => false, "message" => "Session expired. Please login again."]);
        exit;
    }

    $temp_username = $_SESSION["temp_username"];
    $stmt = $conn->prepare("SELECT admin_email FROM admins WHERE admin_name = ?");
    $stmt->bind_param("s", $temp_username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        $otp = generateOTP();
        $expiry = date('Y-m-d H:i:s', time() + 60);
        
        // Update OTP in database
        $update = $conn->prepare("UPDATE admins SET otp_code = ?, otp_expiry = ? WHERE admin_name = ?");
        $update->bind_param("sss", $otp, $expiry, $temp_username);
        $update->execute();
        
        if (sendOTP($admin['admin_email'], $otp)) {
            echo json_encode(["success" => true, "message" => "OTP has been resent."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to send OTP. Please try again."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "User not found."]);
    }
    exit;
}

function clearOTP($username) {
    if (empty($username)) return false;
    global $conn;
    $stmt = $conn->prepare("UPDATE admins SET otp_code = NULL, otp_expiry = NULL WHERE admin_name = ?");
    $stmt->bind_param("s", $username);
    return $stmt->execute();
}


// Resend OTP
if (isset($_POST['resend_otp']) && isset($_SESSION['temp_username'])) {
    $username = $_SESSION['temp_username'];
    $stmt = $conn->prepare("SELECT admin_email FROM admins WHERE admin_name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        $otp = generateOTP();
        $expiry = date('Y-m-d H:i:s', time() + 60);
        $update = $conn->prepare("UPDATE admins SET otp_code = ?, otp_expiry = ? WHERE admin_name = ?");
        $update->bind_param("sss", $otp, $expiry, $username);
        $update->execute();

        if (sendOTP($admin['admin_email'], $otp)) {
            $error = "OTP has been resent.";
        } else {
            $error = "Failed to resend OTP.";
        }
    }
}

elseif (isset($_POST['back_to_login'])) {
    if (isset($_SESSION['temp_username'])) {
        clearOTP($_SESSION['temp_username']);
        unset($_SESSION['temp_username']);
    }
    header("Location: login.php");
    exit();
}

function verifyOTP($username, $userOTP) {
    global $conn;
    $stmt = $conn->prepare("SELECT otp_code, otp_expiry, admin_id FROM admins WHERE admin_name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if ($admin['otp_code'] === $userOTP && strtotime($admin['otp_expiry']) > time()) {
            // Set session admin data
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_name'] = $username;
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
    $username = $_SESSION['temp_username'];

    if (verifyOTP($username, $otp)) {
        // Clear OTP data
        clearOTP($username);
        unset($_SESSION['temp_username']);
        
        // Redirect to admin dashboard
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid or expired OTP.";
    }
}
// Step 3: Login form submitted
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT admin_password, admin_email FROM admins WHERE admin_name = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            if (md5($password) === $admin['admin_password']) {
                // Kirim OTP
                $otp = generateOTP();
                $expiry = date('Y-m-d H:i:s', time() + 60); 
                $update = $conn->prepare("UPDATE admins SET otp_code = ?, otp_expiry = ? WHERE admin_name = ?");
                $update->bind_param("sss", $otp, $expiry, $username);
                $update->execute();

                if (sendOTP($admin['admin_email'], $otp)) {
                    $_SESSION['temp_username'] = $username;
                } else {
                    $error = "Failed to send OTP. Please try again.";
                }
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="../admin/css/login.css" rel="stylesheet">
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

                        <?php if (!isset($_SESSION['temp_username'])): ?>
                            <!-- Login Form -->
                            <form method="POST">
                                <div class="mb-3">
                                    <label>Username</label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                                <div class="mb-3 position-relative">
                                    <label>Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="password" class="form-control" required>
                                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                            <i class="fas fa-eye" id="eyeIcon"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Login</button>
                            </form>

                        <?php else: ?>
                            <!-- OTP Form -->
                            <!-- OTP Form -->
                        <form method="POST">
                            <div class="mb-3">
                                <label for="otp" class="form-label">Enter OTP</label>
                                <input type="text" class="form-control" id="otp" name="otp" required>
                            </div>
                            <button type="submit" name="verify_otp" class="btn btn-primary w-100 mb-2">Verify OTP</button>
                        </form>
                            <!-- Resend OTP Button -->
                            <form method="POST">
                                <button type="submit" name="resend_otp" id="resendBtn" class="btn btn-secondary w-100" disabled>
                                    Resend OTP (<span id="countdown">60</span>s)
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Animasi perangkat -->
   <div class="flying-devices">
    <div class="flying-device laptop" style="top: 60%; left: -80px;"></div>
    <div class="flying-device smartphone" style="top: 30%; left: 100vw;"></div>
    <div class="flying-device desktop" style="top: -100px; left: 50vw;"></div>
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/auth.js"></script>
    <script src="./js/login.js"></script>
    <script>
    let countdown = 60;
    let resendBtn = document.getElementById("resendBtn");
    let countdownSpan = document.getElementById("countdown");
    let timer;

    function startCountdown() {
        resendBtn.disabled = true;
        countdown = 60;
        countdownSpan.innerText = countdown;

        timer = setInterval(() => {
            countdown--;
            countdownSpan.innerText = countdown;

            if (countdown <= 0) {
                clearInterval(timer);
                resendBtn.disabled = false;
                resendBtn.innerHTML = 'Resend OTP';
            }
        }, 1000);
    }

    // Jalankan countdown saat halaman dimuat (jika form OTP aktif)
    <?php if (isset($_SESSION['temp_username'])): ?>
        document.addEventListener("DOMContentLoaded", function () {
            startCountdown();
        });

        // Resend button diklik, reset countdown
        resendBtn.addEventListener("click", function () {
            resendBtn.innerHTML = 'Resend OTP (<span id="countdown">60</span>s)';
            countdownSpan = document.getElementById("countdown");
            startCountdown();
        });
    <?php endif; ?>
</script>
</body>
<style>
    .btn-link{
        color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    }

    .btn-link {
    background: #e54e5d;
    color: white;
    text-decoration: none;
}

.btn-link:hover {
    background: #d23c4b;
    transform: translateY(-2px);
    box-shadow: 0 5px 12px rgba(229, 78, 93, 0.2);
}
</style>
</html>
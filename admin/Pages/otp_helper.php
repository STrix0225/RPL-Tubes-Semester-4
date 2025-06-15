<?php
require_once '../Database/connection.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

function generateOTP() {
    return strval(rand(100000, 999999));
}

function sendOTP($email, $otp) {
    // In a real application, you would implement email sending here
    // For demonstration, we'll just simulate it
    
    // Example using PHP mail() function (uncomment to use):
    /*
    $subject = "Your GEMS Admin Login OTP";
    $message = "Your OTP code is: $otp\n\nThis code will expire in 10 minutes.";
    $headers = "From: no-reply@gemsadmin.com";
    
    return mail($email, $subject, $message, $headers);
    */
    
    // For development, we'll just log the OTP
    error_log("OTP for $email: $otp");
    return true;
}

function verifyOTP($email, $userOTP) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT otp_code, otp_expiry FROM admins WHERE admin_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        $storedOTP = $admin['otp_code'];
        $expiry = $admin['otp_expiry'];
        
        // Check if OTP matches and isn't expired
        if ($storedOTP && $storedOTP === $userOTP && strtotime($expiry) > time()) {
            return true;
        }
    }
    
    return false;
}

function clearOTP($email) {
    global $conn;
    
    $stmt = $conn->prepare("UPDATE admins SET otp_code = NULL, otp_expiry = NULL WHERE admin_email = ?");
    $stmt->bind_param("s", $email);
    return $stmt->execute();
}
?>
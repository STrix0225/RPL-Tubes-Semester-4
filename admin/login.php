<?php
require_once '../Database/connection.php';

// Check if already logged in
if (isAdminLoggedIn()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        // Prepare SQL to prevent SQL injection
        $stmt = $conn->prepare("SELECT admin_id, admin_name, admin_email, admin_password FROM admins WHERE admin_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            // Verify password (assuming passwords are stored using MD5 in your database)
            if (md5($password) === $admin['admin_password']) {
                // Set session variables
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_name'] = $admin['admin_name'];
                $_SESSION['admin_email'] = $admin['admin_email'];
                
                // Redirect to dashboard
                redirect('index.php');
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
        
        $stmt->close();
    } else {
        $error = "Please fill in all fields";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GEMS Admin - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="css/login.css" rel="stylesheet">

</head>
<body>
    <!-- Galaxy Background Elements -->
    <div id="stars"></div>
    <div class="twinkling"></div>
    
    <div class="container login-container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="card floating">
                    <div class="card-header">
                        <h3><i class="fas fa-gem me-2"></i>GEMS Admin</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form action="login.php" method="POST">
                            <div class="mb-4">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>

                            </div>
                            <button type="submit" class="btn btn-primary w-100 mt-3">
                                <i class="fas fa-sign-in-alt me-2"></i> Login
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="flying-devices">
    <div class="flying-device laptop" style="animation-delay: 0s; left: 10%; top: 20%;"></div>
    <div class="flying-device smartphone" style="animation-delay: 5s; left: 70%; top: 30%;"></div>
    <div class="flying-device desktop" style="animation-delay: 10s; left: 30%; top: 60%;"></div>
    <div class="flying-device laptop" style="animation-delay: 15s; left: 80%; top: 10%;"></div>
    <div class="flying-device smartphone" style="animation-delay: 20s; left: 20%; top: 40%;"></div>
    <div class="flying-device desktop" style="animation-delay: 25s; left: 60%; top: 70%;"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/login.js"></script>
    
</body>
</html>
<?php
require_once '../../Database/connection.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT * FROM admins WHERE admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

$result = $conn->query("SELECT COUNT(*) AS count FROM orders WHERE order_status = 'pending'");
if ($result) {
    $stats['pending_orders'] = (int)$result->fetch_assoc()['count'];
}

$recent_orders = [];
$result = $conn->query("
    SELECT o.order_id, o.order_cost, o.order_status, o.order_date, c.customer_name 
    FROM orders o 
    JOIN customers c ON o.customer_id = c.customer_id 
    ORDER BY o.order_date DESC 
    LIMIT 5
");
if ($result) {
    $recent_orders = $result->fetch_all(MYSQLI_ASSOC);
}

$header_data = [
    'pending_orders' => $stats['pending_orders'],
    'recent_orders' => array_slice($recent_orders, 0, 5)
];
// === Proses Update Profile ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $photo_name = $admin['admin_photo']; // default ke foto lama
    $old_photo = $admin['admin_photo'];
    $upload_folder = "../../admin/foto_admin/";

    // Jika ada file baru diupload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $tmp_file = $_FILES['profile_picture']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        $new_filename = "admin_2." . $ext;
        $target_path = $upload_folder . $new_filename;

        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowed)) {
            $error = "Format file tidak valid (hanya jpg, jpeg, png, gif)";
        } else {
            // Hapus foto lama jika bukan default
            if (!empty($old_photo) && $old_photo !== 'admin.jpeg') {
                $old_path = $upload_folder . $old_photo;
                if (file_exists($old_path)) {
                    unlink($old_path); // HAPUS FOTO LAMA
                }
            }

            // Upload foto baru
            if (move_uploaded_file($tmp_file, $target_path)) {
                $photo_name = $new_filename;
            } else {
                $error = "Gagal upload foto baru";
            }
        }
    }

    // Update profil ke DB
    if (!isset($error)) {
        $stmt = $conn->prepare("UPDATE admins SET admin_name = ?, admin_email = ?, admin_photo = ? WHERE admin_id = ?");
        $stmt->bind_param("sssi", $name, $email, $photo_name, $admin_id);

        if ($stmt->execute()) {
            $success = "Profil berhasil diperbarui";

            // Refresh data dari DB
            $stmt = $conn->prepare("SELECT * FROM admins WHERE admin_id = ?");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();
        } else {
            $error = "Gagal menyimpan ke database: " . $conn->error;
        }
    }
}

// === Proses Update Password ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_pass = "Semua field password harus diisi";
    } elseif ($new_password !== $confirm_password) {
        $error_pass = "Password baru tidak cocok";
    } elseif (strlen($new_password) < 8) {
        $error_pass = "Password minimal 8 karakter";
    } else {
        // Verifikasi password saat ini
        if (md5($current_password) === $admin['admin_password']) {
            // Update password
            $new_password_hash = md5($new_password);
            $stmt = $conn->prepare("UPDATE admins SET admin_password = ? WHERE admin_id = ?");
            $stmt->bind_param("si", $new_password_hash, $admin_id);
            
            if ($stmt->execute()) {
                $success_pass = "Password berhasil diubah";
            } else {
                $error_pass = "Gagal mengubah password: " . $conn->error;
            }
        } else {
            $error_pass = "Password saat ini salah";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Profile - GEMS Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="../css/style.css" rel="stylesheet" />
    <style>
        /* Profile Container */
        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: var(--bg-card);
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 var(--shadow-color);
        }
        
        /* Profile Header */
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid var(--bg-card);
            box-shadow: 0 5px 15px var(--shadow-color);
            background-color: var(--bg-body);
        }
        
        /* Form Sections */
        .form-section {
            margin-bottom: 40px;
            padding: 20px;
            background-color: var(--bg-card);
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 var(--shadow-color);
            border: 1px solid var(--border-color);
        }
        
        .section-title {
            color: var(--color-primary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }
        
        /* Form Elements */
        .form-control, .form-select {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
        }
        
        .form-control:focus, .form-select:focus {
            background-color: var(--bg-card);
            color: var(--text-primary);
            border-color: var(--color-primary);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .form-label {
            color: var(--text-primary);
        }
        
        .text-muted {
            color: var(--text-secondary) !important;
        }
        
        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-secondary);
        }
        
        .password-input-group {
            position: relative;
        }
        
        /* Buttons */
        .btn-primary {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
        }
        
        .btn-warning {
            background-color: var(--color-warning);
            border-color: var(--color-warning);
            color: var(--text-inverse);
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .profile-container {
                padding: 15px;
            }
            
            .profile-img {
                width: 120px;
                height: 120px;
            }
            
            .form-section {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include '../Layout/sidebar.php'; ?>
    <div id="content">
        <?php include '../Layout/header.php'; ?>

        <div class="container-fluid mt-4">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="profile-container">
                        <div class="profile-header">
                            <?php $foto = !empty($admin['admin_photo']) ? $admin['admin_photo'] : 'admin.jpeg'; ?>
                            <img src="../../admin/foto_admin/<?= htmlspecialchars($foto) ?>" alt="Profile" class="profile-img mb-3">
                            <h3><?= htmlspecialchars($admin['admin_name']) ?></h3>
                            <p class="text-muted">Administrator</p>
                        </div>

                        <!-- Form Edit Profile -->
                        <div class="form-section">
                            <h4 class="section-title"><i class="fas fa-user-edit me-2"></i>Edit Profil</h4>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php elseif (isset($success)): ?>
                                <div class="alert alert-success"><?= $success ?></div>
                            <?php endif; ?>

                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Nama</label>
                                    <input type="text" name="name" class="form-control" 
                                           value="<?= htmlspecialchars($admin['admin_name']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" 
                                           value="<?= htmlspecialchars($admin['admin_email']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Foto Profil</label>
                                    <input type="file" name="profile_picture" class="form-control" accept="image/*">
                                </div>
                                <div class="d-grid">
                                    <button type="submit" name="update_profile" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Form Ubah Password -->
                        <div class="form-section">
                            <h4 class="section-title"><i class="fas fa-key me-2"></i>Ubah Password</h4>
                            <?php if (isset($error_pass)): ?>
                                <div class="alert alert-danger"><?= $error_pass ?></div>
                            <?php elseif (isset($success_pass)): ?>
                                <div class="alert alert-success"><?= $success_pass ?></div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="mb-3 password-input-group">
                                    <label class="form-label">Password Saat Ini</label>
                                    <input type="password" name="current_password" id="current_password" class="form-control" required>
                                    <i class="fas fa-eye-slash password-toggle" onclick="togglePassword('current_password', this)"></i>
                                </div>
                                <div class="mb-3 password-input-group">
                                    <label class="form-label">Password Baru</label>
                                    <input type="password" name="new_password" id="new_password" class="form-control" required>
                                    <i class="fas fa-eye-slash password-toggle" onclick="togglePassword('new_password', this)"></i>
                                    <small class="text-muted">Minimal 8 karakter</small>
                                </div>
                                <div class="mb-3 password-input-group">
                                    <label class="form-label">Konfirmasi Password Baru</label>
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                                    <i class="fas fa-eye-slash password-toggle" onclick="togglePassword('confirm_password', this)"></i>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" name="update_password" class="btn btn-warning">
                                        <i class="fas fa-key me-1"></i> Ubah Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include '../Layout/footer.php'; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../js/sidebar.js"></script>
<script src="../js/script.js"></script>
<script>
    // Fungsi toggle password visibility
    function togglePassword(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        }
    }

    // Validasi form sebelum submit
    document.querySelector('form[name="update_password"]').addEventListener('submit', function(e) {
        const newPass = document.getElementById('new_password').value;
        const confirmPass = document.getElementById('confirm_password').value;
        
        if (newPass !== confirmPass) {
            e.preventDefault();
            alert('Password baru dan konfirmasi password tidak cocok!');
        }
    });
</script>
</body>
</html>
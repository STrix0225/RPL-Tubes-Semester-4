<?php
$header_data = $header_data ?? [
    'pending_orders' => 0,
    'recent_orders' => []
];

$pending_orders = (int)($header_data['pending_orders'] ?? 0);
$recent_orders_notif = $header_data['recent_orders'] ?? [];
?>

<div class="card mb-4 shadow-sm">
  <nav class="navbar navbar-expand px-3" style="background-color: var(--bg-card); color: var(--text-primary);">
    <button type="button" id="sidebarCollapse" class="btn btn-primary">
      <i class="fas fa-bars"></i>
    </button>
    <a class="navbar-brand ms-3" href="index.php">
        <i class="fas fa-gem me-1"></i> GEMS Admin
    </a>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto">
            <!-- Notifikasi -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    <span class="badge bg-danger"><?= $pending_orders ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><h6 class="dropdown-header">New Orders</h6></li>
                    <?php if (!empty($recent_orders_notif)): ?>
                        <?php foreach ($recent_orders_notif as $order): ?>
                            <li>
                                <a class="dropdown-item" href="order_details.php?id=<?= htmlspecialchars($order['order_id']) ?>">
                                    Order #<?= htmlspecialchars($order['order_id']) ?> - $<?= number_format($order['order_cost'], 2) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><span class="dropdown-item text-muted">No recent orders</span></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="orders.php">View All Orders</a></li>
                </ul>
            </li>

            <!-- Tombol Dark/Light Mode -->
            <li class="nav-item d-flex align-items-center ms-3">
                <button id="themeToggle" class="btn btn-outline-secondary btn-sm" title="Toggle Theme">
                    <i id="themeIcon" class="fas fa-moon"></i>
                </button>
            </li>

            <!-- User Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle"></i>
                    <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="../../admin/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>
  </nav>
</div>
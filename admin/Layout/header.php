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
    <a class="navbar-brand ms-3" href="../../admin/index.php">
        <i class="fas fa-gem me-1"></i> GEMS Admin
    </a>
    
    <!-- Search Form -->
    <div class="search-container ms-3 me-auto" style="max-width: 400px; flex-grow: 1;">
    <form id="globalSearchForm" class="d-flex">
        <div class="input-group">
            <input type="text" 
                   class="form-control" 
                   id="globalSearch" 
                   name="q" 
                   placeholder="Search products, orders, customers..." 
                   aria-label="Search"
                   autocomplete="off">
            <button class="btn btn-outline-secondary" type="submit">
                <i class="fas fa-search"></i>
            </button>
            <div class="dropdown-menu w-100" id="searchResultsDropdown" style="display: none;">
                <div class="list-group" id="searchResults">
                    <!-- Results will appear here -->
                </div>
            </div>
        </div>
    </form>
</div>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="../../Customer/gems-customer-pages/dashboard.php" title="Go to Customer Dashboard">
                    <i class="fas fa-store"></i>
                </a>
            </li>
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
                                <a class="dropdown-item" href="../../admin/Pages/listOrder.php?id=<?= htmlspecialchars($order['order_id']) ?>">
                                    Order #<?= htmlspecialchars($order['order_id']) ?> - $<?= number_format($order['order_cost'], 2) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><span class="dropdown-item text-muted">No recent orders</span></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="../../admin/Pages/listOrder.php">View All Orders</a></li>
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
                    <li><a class="dropdown-item" href="../../admin/Pages/profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="../../admin/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>
  </nav>
</div>

<!-- Add this CSS for the search dropdown -->
<style>
    .search-container {
        position: relative;
    }
    
    #searchResultsDropdown {
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid rgba(0,0,0,.15);
        border-radius: 0.25rem;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.175);
    }
    
    #searchResults .list-group-item {
        border-left: none;
        border-right: none;
        padding: 0.5rem 1rem;
    }
    
    #searchResults .list-group-item:first-child {
        border-top: none;
    }
    
    #searchResults .list-group-item:last-child {
        border-bottom: none;
    }
    
    #searchResults .list-group-item:hover {
        background-color: #f8f9fa;
    }
    
    .search-result-category {
        font-size: 0.75rem;
        color: #6c757d;
        text-transform: uppercase;
        font-weight: bold;
        padding: 0.25rem 1rem;
        background-color: #f8f9fa;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('globalSearchForm');
    const searchInput = document.getElementById('globalSearch');
    const searchResults = document.getElementById('searchResults');
    const searchDropdown = document.getElementById('searchResultsDropdown');
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const query = searchInput.value.trim();
        if (query.length >= 2) {
            window.location.href = `../../admin/Pages/search.php?q=${encodeURIComponent(query)}`;
        }
    });
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        
        if (query.length < 2) {
            searchDropdown.style.display = 'none';
            return;
        }
        
        fetch(`../../admin/api/search.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    searchResults.innerHTML = '<div class="list-group-item text-muted">No results found</div>';
                    searchDropdown.style.display = 'block';
                    return;
                }
                
                let html = '';
                data.forEach(item => {
                    html += `
                        <a href="${item.link}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">${item.title}</h6>
                                <small>${item.subtitle || ''}</small>
                            </div>
                            ${item.description ? `<p class="mb-1 small text-muted">${item.description}</p>` : ''}
                        </a>
                    `;
                });
                
                searchResults.innerHTML = html;
                searchDropdown.style.display = 'block';
            });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchForm.contains(e.target)) {
            searchDropdown.style.display = 'none';
        }
    });
});
</script>
<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="detailModalBody">
        <!-- Content will be loaded here via AJAX -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
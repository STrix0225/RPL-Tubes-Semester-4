<nav id="sidebar">
    <div class="sidebar-header d-flex align-items-center justify-content-start px-3 py-2 gap-2">
        <i class="bi bi-gem fs-4"></i>
        <h3 class="m-0 ms-2">GEMS Admin</h3>
    </div>
    <ul class="list-unstyled components px-2">
        <li class="active">
            <a href="../index.php"><i class="fas fa-home"></i> Dashboard</a>
        </li>
        <li>
            <a href="#productSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-plus-square"></i> Products
            </a>
            <ul class="collapse list-unstyled ps-3" id="productSubmenu">
                <li><a href="../../Admin/Pages/addProducts.php"><i class="fas fa-box"></i> Add Products</a></li>
                <li><a href="../../Admin/Pages/listProducts.php"><i class="fas fa-boxes"></i> List Products</a></li>
            </ul>
        </li>
        <li>
            <a href="#supplayerMenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-truck"></i> Supplayer
            </a>
            <ul class="collapse list-unstyled ps-3" id="supplayerMenu">
                <li><a href="../../admin/Pages/addSupplier.php"><i class="fas fa-user"></i> Add Supplayers</a></li>
                <li><a href="../../admin/Pages/listSupplier.php"><i class="fas fa-users"></i> List Suplayers</a></li>
                <li><a href="admins.php"><i class="fas fa-truck"></i> Restock Products</a></li>
                <li><a href="admins.php"><i class="fas fa-history"></i> History Restock</a></li>
            </ul>
        </li>
        <li>
            <a href="#orderMenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>
            <ul class="collapse list-unstyled ps-3" id="orderMenu">
                <li><a href="admins.php"><i class="fas fa-list-alt"></i> List Orders</a></li>
                <li><a href="admins.php"><i class="fas fa-history"></i> History Orders</a></li>
            </ul>
        </li>
        <li><a href="blogs.php"><i class="fas fa-blog"></i> Blogs</a></li>
        <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
    </ul>
    <button type="button" id="sidebarCollapse" 
        class="btn btn-sm btn-outline-secondary position-absolute start-50 translate-middle-x mt-3">
        <i class="fas fa-bars"></i>
    </button>
</nav>

<nav id="sidebar">
    <div class="sidebar-header d-flex align-items-center justify-content-start px-3 py-2 gap-2">
        <i class="bi bi-gem fs-4"></i>
        <h3 class="m-0 ms-2">GEMS Admin</h3>
    </div>
    <ul class="list-unstyled components px-2">
        <li class="active">
            <a href="../../admin/index.php"><i class="fas fa-home"></i> Dashboard</a>
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
                <i class="fas fa-truck"></i> Supplier
            </a>
            <ul class="collapse list-unstyled ps-3" id="supplayerMenu">
                <li><a href="../../admin/Pages/addSupplier.php"><i class="fas fa-user"></i> Add Supplier</a></li>
                <li><a href="../../admin/Pages/listSupplier.php"><i class="fas fa-users"></i> List Suppliers</a></li>
                <li><a href="../../admin/Pages/restockProduct.php"><i class="fas fa-truck"></i> Restock Products</a></li>
                <li><a href="../../admin/Pages/historyRestock.php"><i class="fas fa-history"></i> History Restock</a></li>
            </ul>
        </li>
        <li>
            <a href="#orderMenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>
            <ul class="collapse list-unstyled ps-3" id="orderMenu">
                <li><a href="../../admin/Pages/listOrder.php"><i class="fas fa-list-alt"></i> List Orders</a></li>
                <li><a href="../../admin/Pages/historyOrder.php"><i class="fas fa-history"></i> History Orders</a></li>
                <li><a href="../../admin/Pages/paymentCustomer.php"><i class="fas fa-wallet"></i> Payment</a></li>
            </ul>
        </li>
        <li>
            <a href="#customerMenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-user"></i> Customers
            </a>
            <ul class="collapse list-unstyled ps-3" id="customerMenu">
                <li><a href="../../admin/Pages/listCustomers.php"><i class="fas fa-list-alt"></i> List Customers</a></li>
                <li><a href="../../admin/Pages/riviewCustomer.php"><i class="fas fa-star"></i> Review Coment</a></li>
            </ul>
        </li>
    </ul>
    <button type="button" id="sidebarCollapse" 
        class="btn btn-sm btn-outline-secondary position-absolute start-50 translate-middle-x mt-3">
        <i class="fas fa-arrow-left"></i>
    </button>
</nav>


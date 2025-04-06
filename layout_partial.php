<header class="navbar sticky-top" style="background-color: #343a40; height: 70px; padding-left: 20px;">
    <a class="d-flex align-items-center text-white" href="index.php" style="color: white; text-decoration: none;">
        <img src="design/images/favicon/favicon.ico" alt="Home" width="32" height="32" class="me-2">
        Inventory Management
    </a>
</header>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
            <div class="position-sticky sidebar-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center <?php echo ($current_page === 'dashboard') ? 'active' : ''; ?>" href="dashboard.php">
                            <img src="design/images/house-fill.svg" alt="Dashboard" width="16" height="16">
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center <?php echo ($current_page === 'products') ? 'active' : ''; ?>" href="products.php">
                            <img src="design/images/cart.svg" alt="Products" width="16" height="16">
                            Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center <?php echo ($current_page === 'add_product') ? 'active' : ''; ?>" href="add_product.php">
                            <img src="design/images/file-earmark.svg" alt="Add Product" width="16" height="16">
                            Add Product
                        </a>
                    </li>
                    <!-- Ajout des nouveaux liens de menu -->
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center <?php echo ($current_page === 'orders') ? 'active' : ''; ?>" href="orders.php">
                            <img src="design/images/file-earmark-text.svg" alt="Orders" width="16" height="16">
                            Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center <?php echo ($current_page === 'invoices') ? 'active' : ''; ?>" href="invoices.php">
                            <img src="design/images/receipt.svg" alt="Invoices" width="16" height="16">
                            Invoices
                        </a>
                    </li>
                    <!-- Fin des ajouts -->
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center <?php echo ($current_page === 'deleted_products') ? 'active' : ''; ?>" href="deleted_products.php">
                            <img src="design/images/trash.svg" alt="Deleted Products" width="16" height="16">
                            Deleted Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center <?php echo ($current_page === 'reports') ? 'active' : ''; ?>" href="reports.php">
                            <img src="design/images/graph-up.svg" alt="Reports" width="16" height="16">
                            Reports
                        </a>
                    </li>
                </ul>

                <hr>

                <ul class="nav flex-column mb-auto">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="signout.php">
                            <img src="design/images/door-closed.svg" alt="Sign Out" width="16" height="16">
                            Sign Out
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
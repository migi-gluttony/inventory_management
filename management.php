<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="design/images/favicon/favicon-16x16.png" type="image/x-icon">
    <title>Admin Panel</title>
    <link href="design/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --text-color: #5a5c69;
            --success-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --info-color: #36b9cc;
        }
        
        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background-color: #f8f9fc;
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .admin-card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.10);
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 1.5rem;
            height: 100%;
        }
        
        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .admin-container {
            max-width: 1140px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .admin-card-body {
            padding: 2rem;
            text-align: center;
        }
        
        .card-title {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 0.75rem;
        }
        
        .card-description {
            color: #858796;
            margin-bottom: 1.5rem;
        }
        
        .btn-admin {
            padding: 0.375rem 1.5rem;
            border-radius: 0.35rem;
            font-weight: 600;
            width: 100%;
        }
        
        .card-icon-users { color: var(--primary-color); }
        .card-icon-providers { color: var(--success-color); }
        .card-icon-products { color: var(--warning-color); }
        .card-icon-reports { color: var(--info-color); }
        .card-icon-orders { color: var(--danger-color); }
        
        .footer {
            margin-top: auto;
            background-color: white;
            box-shadow: 0 -0.15rem 1.75rem 0 rgba(58, 59, 69, 0.05);
            padding: 1rem 0;
            text-align: center;
            color: #858796;
        }
        
        .admin-header {
            padding: 1.5rem 0;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .admin-header h1 {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .admin-header p {
            color: #858796;
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="management.php">
                <img src="design/images/favicon/favicon-32x32.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top me-2">
                Inventory Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="user_management.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="provider_management.php">Providers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="signout.php">Sign Out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <h1>Admin Management Panel</h1>
            <p>Welcome to the admin control panel. Here you can manage users and providers.</p>
        </div>
    </div>

    <!-- Admin Cards -->
    <div class="admin-container">
        <div class="row g-4">
            <!-- User Management Card -->
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card admin-card">
                    <div class="admin-card-body">
                        <i class="bi bi-people-fill card-icon card-icon-users"></i>
                        <h5 class="card-title">User Management</h5>
                        <p class="card-description">Add, edit, and remove user accounts. Manage user permissions and access levels.</p>
                        <a href="user_management.php" class="btn btn-primary btn-admin">Manage Users</a>
                    </div>
                </div>
            </div>
            
            <!-- Provider Management Card -->
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card admin-card">
                    <div class="admin-card-body">
                        <i class="bi bi-building card-icon card-icon-providers"></i>
                        <h5 class="card-title">Provider Management</h5>
                        <p class="card-description">Add, edit, and manage product providers. Update provider information.</p>
                        <a href="provider_management.php" class="btn btn-success btn-admin">Manage Providers</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p class="mb-0">&copy; 2025 Inventory Management System. All rights reserved.</p>
        </div>
    </footer>
    
    <script src="design/js/bootstrap.bundle.min.js"></script>
</body>
</html>
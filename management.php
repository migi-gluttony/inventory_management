<?php
$current_page = 'management'; // Add this for sidebar highlighting
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: dashboard.php");
    exit();
}

// Fetch key statistics for admin dashboard
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM Users")->fetch_assoc()['count'];
$adminUsers = $conn->query("SELECT COUNT(*) as count FROM Users WHERE is_admin = 1")->fetch_assoc()['count'];
$normalUsers = $totalUsers - $adminUsers;

$totalProviders = $conn->query("SELECT COUNT(*) as count FROM Providers")->fetch_assoc()['count'];
$totalProducts = $conn->query("SELECT COUNT(*) as count FROM Products WHERE is_deleted = 0")->fetch_assoc()['count'];
$deletedProducts = $conn->query("SELECT COUNT(*) as count FROM Products WHERE is_deleted = 1")->fetch_assoc()['count'];

$lowStockCount = $conn->query("SELECT COUNT(*) as count FROM Products WHERE stock < 10 AND is_deleted = 0")->fetch_assoc()['count'];
$totalOrders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'] ?? 0;
$pendingOrders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'")->fetch_assoc()['count'] ?? 0;

$totalInvoices = $conn->query("SELECT COUNT(*) as count FROM invoices")->fetch_assoc()['count'] ?? 0;
$unpaidInvoices = $conn->query("SELECT COUNT(*) as count FROM invoices WHERE payment_status = 'pending'")->fetch_assoc()['count'] ?? 0;

// Fetch recent user activity
$recentUsers = $conn->query("SELECT name, email, date_added FROM Users ORDER BY date_added DESC LIMIT 5");

// Provider breakdown for chart
$providerProductCounts = $conn->query("SELECT p.name as provider_name, COUNT(pr.id) as product_count 
                                       FROM Providers p
                                       LEFT JOIN Products pr ON p.id = pr.provider_id AND pr.is_deleted = 0
                                       GROUP BY p.id
                                       ORDER BY product_count DESC
                                       LIMIT 6");

$providerLabels = [];
$providerData = [];

while ($row = $providerProductCounts->fetch_assoc()) {
    $providerLabels[] = $row['provider_name'];
    $providerData[] = $row['product_count'];
}

$conn->close();
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
    <link href="design/css/products.css" rel="stylesheet">
    <link rel="stylesheet" href="design/css/layout_partial.css">
    <style>
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.2s;
            margin-bottom: 20px;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card-primary {
            border-left-color: #4e73df;
        }

        .stat-card-success {
            border-left-color: #1cc88a;
        }

        .stat-card-warning {
            border-left-color: #f6c23e;
        }

        .stat-card-danger {
            border-left-color: #e74a3b;
        }

        .stat-card-info {
            border-left-color: #36b9cc;
        }

        .stat-card .icon {
            font-size: 2rem;
            opacity: 0.3;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .stat-label {
            text-transform: uppercase;
            font-size: 0.8rem;
            font-weight: 700;
            color: #6c757d;
        }

        .admin-card {
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
            margin-bottom: 20px;
        }

        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
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
            font-weight: 600;
            width: 100%;
        }

        .card-icon-users {
            color: #4e73df;
        }

        .card-icon-providers {
            color: #1cc88a;
        }

        .activity-list {
            list-style: none;
            padding-left: 0;
        }

        .activity-list li {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .activity-list li:last-child {
            border-bottom: none;
        }

        .chart-container {
            height: 250px;
        }
    </style>
</head>

<body>
    <?php include 'layout_partial.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="container-fluid mt-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Admin Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                </div>
            </div>

            <!-- Top Stats Cards -->
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card stat-card-primary shadow h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col">
                                    <div class="stat-label">Total Users</div>
                                    <div class="stat-value"><?php echo $totalUsers; ?></div>
                                    <div class="small mt-2"><?php echo $adminUsers; ?> Admins, <?php echo $normalUsers; ?> Normal Users</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-people icon text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card stat-card-success shadow h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col">
                                    <div class="stat-label">Providers</div>
                                    <div class="stat-value"><?php echo $totalProviders; ?></div>
                                    <div class="small mt-2">Supporting <?php echo $totalProducts; ?> active products</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-building icon text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card stat-card-warning shadow h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col">
                                    <div class="stat-label">Inventory Alerts</div>
                                    <div class="stat-value"><?php echo $lowStockCount; ?></div>
                                    <div class="small mt-2">Products with low stock</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-exclamation-triangle icon text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card stat-card-info shadow h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col">
                                    <div class="stat-label">Pending Orders</div>
                                    <div class="stat-value"><?php echo $pendingOrders; ?></div>
                                    <div class="small mt-2">Out of <?php echo $totalOrders; ?> total orders</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-cart icon text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle Section: Charts and Activity -->
            <div class="row g-3 mb-4">
                <!-- Provider Products Chart -->
                <div class="col-lg-7">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold">Products by Provider</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="providerProductsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold">Recent User Activity</h6>
                        </div>
                        <div class="card-body">
                            <ul class="activity-list">
                                <?php if ($recentUsers->num_rows > 0): ?>
                                    <?php while ($user = $recentUsers->fetch_assoc()): ?>
                                        <li>
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($user['name']); ?></strong>
                                                    <span class="text-muted">(<?php echo htmlspecialchars($user['email']); ?>)</span>
                                                </div>
                                                <div class="small text-muted">
                                                    <?php echo date('M d, Y', strtotime($user['date_added'])); ?>
                                                </div>
                                            </div>
                                            <div class="small">User account created</div>
                                        </li>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <li>No recent user activity</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </main>

    <script src="design/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Provider Products Chart
        const providerLabels = <?php echo json_encode($providerLabels); ?>;
        const providerData = <?php echo json_encode($providerData); ?>;

        const ctx = document.getElementById('providerProductsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: providerLabels,
                datasets: [{
                    label: 'Number of Products',
                    data: providerData,
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.7)',
                        'rgba(28, 200, 138, 0.7)',
                        'rgba(246, 194, 62, 0.7)',
                        'rgba(231, 74, 59, 0.7)',
                        'rgba(54, 185, 204, 0.7)',
                        'rgba(133, 135, 150, 0.7)'
                    ],
                    borderColor: [
                        'rgba(78, 115, 223, 1)',
                        'rgba(28, 200, 138, 1)',
                        'rgba(246, 194, 62, 1)',
                        'rgba(231, 74, 59, 1)',
                        'rgba(54, 185, 204, 1)',
                        'rgba(133, 135, 150, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Products'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Provider Name'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>

</html>
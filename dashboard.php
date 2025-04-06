<?php
$current_page = 'dashboard';
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin']) {
  header("Location: user_management.php");
  exit();
}

if (!isset($_SESSION['user_id'])) {
  header("Location: sign-in.php");
  exit();
}

// Fetch basic stats
$providersCount = $conn->query("SELECT COUNT(*) AS count FROM Providers")->fetch_assoc()['count'];
$productsCount = $conn->query("SELECT COUNT(*) AS count FROM Products WHERE is_deleted = FALSE")->fetch_assoc()['count'];
$usersCount = $conn->query("SELECT COUNT(*) AS count FROM Users")->fetch_assoc()['count'];
$totalStock = $conn->query("SELECT SUM(stock) AS total FROM Products WHERE is_deleted = FALSE")->fetch_assoc()['total'] ?? 0;
$totalCapital = $conn->query("SELECT SUM(price * stock) AS total FROM Products WHERE is_deleted = FALSE")->fetch_assoc()['total'] ?? 0;

// Key business metrics
$lowStockCount = $conn->query("SELECT COUNT(*) AS count FROM Products WHERE is_deleted = FALSE AND stock < 10")->fetch_assoc()['count'];
$ordersCount = $conn->query("SELECT COUNT(*) AS count FROM orders")->fetch_assoc()['count'] ?? 0;
$pendingOrdersCount = $conn->query("SELECT COUNT(*) AS count FROM orders WHERE status = 'pending'")->fetch_assoc()['count'] ?? 0;
$invoicesCount = $conn->query("SELECT COUNT(*) AS count FROM invoices")->fetch_assoc()['count'] ?? 0;
$totalInvoiced = $conn->query("SELECT SUM(total_amount) AS total FROM invoices")->fetch_assoc()['total'] ?? 0;

// Products by provider for chart
$productsByProvider = $conn->query("SELECT P.name AS provider_name, COUNT(pr.id) AS product_count 
                                     FROM Providers P 
                                     LEFT JOIN Products pr ON P.id = pr.provider_id 
                                     WHERE pr.is_deleted = FALSE 
                                     GROUP BY P.name
                                     LIMIT 6");

$providerNames = [];
$productCounts = [];

while ($row = $productsByProvider->fetch_assoc()) {
  $providerNames[] = $row['provider_name'];
  $productCounts[] = $row['product_count'];
}

// Fetch the last 5 products added
$lastProducts = $conn->query("SELECT name, price, stock, date_added FROM Products WHERE is_deleted = FALSE ORDER BY date_added DESC LIMIT 5");
$lastProductsData = [];
while ($row = $lastProducts->fetch_assoc()) {
  $lastProductsData[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link href="design/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="shortcut icon" href="design/images/favicon/favicon-16x16.png" type="image/x-icon">
  <link rel="stylesheet" href="design/css/layout_partial.css">
  <style>
    :root {
      --primary-color: #3498db;
      --secondary-color: #2ecc71;
      --accent-color: #e74c3c;
      --background-color: #f8f9fa;
      --text-color: #333333;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--background-color);
      color: var(--text-color);
    }

    .dashboard-card {
      background-color: #ffffff;
      border-radius: 10px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
      transition: transform 0.2s ease-in-out;
      margin-bottom: 20px;
      height: 100%;
    }

    .dashboard-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .stat-card {
      color: #ffffff;
      padding: 20px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      height: 100%;
      min-height: 120px;
      border-radius: 10px;
    }

    .stat-value {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 0;
    }

    .stat-label {
      font-size: 0.9rem;
      margin-bottom: 0;
      opacity: 0.9;
    }

    .stat-icon {
      font-size: 1.5rem;
      margin-bottom: 8px;
    }

    .chart-container {
      height: 300px;
      padding: 15px;
    }

    .chart-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 15px;
      color: #555;
      padding: 15px 15px 0 15px;
    }

    .table-container {
      padding: 15px;
    }

    .alert-badge {
      padding: 6px 12px;
      border-radius: 50px;
      font-size: 0.85rem;
      font-weight: 500;
      display: inline-block;
    }

    /* Color variants for stat cards */
    .bg-primary-gradient {
      background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
    }

    .bg-success-gradient {
      background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
    }

    .bg-danger-gradient {
      background: linear-gradient(135deg, #ff5f6d 0%, #ffc371 100%);
    }

    .bg-purple-gradient {
      background: linear-gradient(135deg, #834d9b 0%, #d04ed6 100%);
    }

    .bg-orange-gradient {
      background: linear-gradient(135deg, #f46b45 0%, #eea849 100%);
    }

    .bg-teal-gradient {
      background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
    }

    ::-webkit-scrollbar-track {
      background: transparent;
    }

    ::-webkit-scrollbar-thumb {
      background-color: rgba(0, 0, 0, 0.2);
      border-radius: 4px;
    }
  </style>
</head>

<body>
  <?php include 'layout_partial.php'; ?>

  <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
      <h1 class="h2">Dashboard</h1>
      <div class="btn-toolbar mb-2 mb-md-0">
        <a href="reports.php" class="btn btn-sm btn-outline-secondary">
          <i class="bi bi-file-earmark-text"></i> View Reports
        </a>
      </div>
    </div>

    <!-- Main KPI Cards -->
    <div class="row g-4 mb-4">
      <div class="col-12 col-sm-6 col-md-4">
        <div class="dashboard-card h-100">
          <div class="stat-card bg-primary-gradient">
            <div>
              <i class="bi bi-box stat-icon"></i>
              <h3 class="stat-label">Products</h3>
            </div>
            <p class="stat-value"><?php echo $productsCount; ?></p>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-4">
        <div class="dashboard-card h-100">
          <div class="stat-card bg-success-gradient">
            <div>
              <i class="bi bi-archive stat-icon"></i>
              <h3 class="stat-label">Total Stock</h3>
            </div>
            <p class="stat-value"><?php echo $totalStock; ?></p>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-4">
        <div class="dashboard-card h-100">
          <div class="stat-card bg-purple-gradient">
            <div>
              <i class="bi bi-currency-dollar stat-icon"></i>
              <h3 class="stat-label">Inventory Value</h3>
            </div>
            <p class="stat-value"><?php echo number_format((float)$totalCapital, 0, '.', ','); ?> MAD</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Business Metrics -->
    <div class="row g-4 mb-4">
      <div class="col-12 col-sm-6 col-md-3">
        <div class="dashboard-card h-100">
          <div class="stat-card bg-orange-gradient">
            <div>
              <i class="bi bi-exclamation-triangle stat-icon"></i>
              <h3 class="stat-label">Low Stock Items</h3>
            </div>
            <p class="stat-value"><?php echo $lowStockCount; ?></p>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <div class="dashboard-card h-100">
          <div class="stat-card bg-danger-gradient">
            <div>
              <i class="bi bi-file-text stat-icon"></i>
              <h3 class="stat-label">Orders</h3>
            </div>
            <p class="stat-value"><?php echo $ordersCount; ?> <small>(<?php echo $pendingOrdersCount; ?> pending)</small></p>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <div class="dashboard-card h-100">
          <div class="stat-card bg-teal-gradient">
            <div>
              <i class="bi bi-receipt stat-icon"></i>
              <h3 class="stat-label">Invoices</h3>
            </div>
            <p class="stat-value"><?php echo $invoicesCount; ?></p>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <div class="dashboard-card h-100">
          <div class="stat-card bg-primary-gradient">
            <div>
              <i class="bi bi-shop stat-icon"></i>
              <h3 class="stat-label">Providers</h3>
            </div>
            <p class="stat-value"><?php echo $providersCount; ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts and Tables Row -->
    <div class="row g-4 mb-4">
      <div class="col-12 col-lg-7">
        <div class="dashboard-card h-100">
          <div class="chart-title">Products by Provider</div>
          <div class="chart-container">
            <canvas id="productsByProviderChart"></canvas>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-5">
        <div class="dashboard-card h-100">
          <div class="chart-title">Recently Added Products</div>
          <div class="table-container">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Price</th>
                  <th>Stock</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($lastProductsData as $product): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo number_format((float)$product['price'], 2, '.', ','); ?> MAD</td>
                    <td>
                      <?php if($product['stock'] <= 0): ?>
                        <span class="alert-badge bg-danger text-white">Out of Stock</span>
                      <?php elseif($product['stock'] < 10): ?>
                        <span class="alert-badge bg-warning text-dark">Low: <?php echo $product['stock']; ?></span>
                      <?php else: ?>
                        <span class="alert-badge bg-success text-white"><?php echo $product['stock']; ?></span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    
  </main>

  <script src="design/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Provider Products Chart
    const providerNames = <?php echo json_encode($providerNames); ?>;
    const productCounts = <?php echo json_encode($productCounts); ?>;

    const providerCtx = document.getElementById('productsByProviderChart').getContext('2d');
    new Chart(providerCtx, {
      type: 'bar',
      data: {
        labels: providerNames,
        datasets: [{
          label: 'Number of Products',
          data: productCounts,
          backgroundColor: [
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 99, 132, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)'
          ],
          borderColor: [
            'rgba(54, 162, 235, 1)',
            'rgba(255, 99, 132, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
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
              text: 'Providers'
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
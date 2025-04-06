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

// Additional metrics for improved dashboard
$lowStockCount = $conn->query("SELECT COUNT(*) AS count FROM Products WHERE is_deleted = FALSE AND stock < 10")->fetch_assoc()['count'];
$outOfStockCount = $conn->query("SELECT COUNT(*) AS count FROM Products WHERE is_deleted = FALSE AND stock = 0")->fetch_assoc()['count'];
$avgProductPrice = $conn->query("SELECT AVG(price) AS avg FROM Products WHERE is_deleted = FALSE")->fetch_assoc()['avg'] ?? 0;

// Order metrics
$ordersCount = $conn->query("SELECT COUNT(*) AS count FROM orders")->fetch_assoc()['count'] ?? 0;
$pendingOrdersCount = $conn->query("SELECT COUNT(*) AS count FROM orders WHERE status = 'pending'")->fetch_assoc()['count'] ?? 0;
$invoicesCount = $conn->query("SELECT COUNT(*) AS count FROM invoices")->fetch_assoc()['count'] ?? 0;
$totalInvoiced = $conn->query("SELECT SUM(total_amount) AS total FROM invoices")->fetch_assoc()['total'] ?? 0;
$pendingPayments = $conn->query("SELECT COUNT(*) AS count FROM invoices WHERE payment_status = 'pending'")->fetch_assoc()['count'] ?? 0;

// Products by provider for pie chart
$productsByProvider = $conn->query("SELECT P.name AS provider_name, COUNT(pr.id) AS product_count 
                                     FROM Providers P 
                                     LEFT JOIN Products pr ON P.id = pr.provider_id 
                                     WHERE pr.is_deleted = FALSE 
                                     GROUP BY P.name");

$providerNames = [];
$productCounts = [];

while ($row = $productsByProvider->fetch_assoc()) {
  $providerNames[] = $row['provider_name'];
  $productCounts[] = $row['product_count'];
}

// Products by stock level for bar chart
$stockLevels = $conn->query("SELECT 
                               CASE 
                                 WHEN stock = 0 THEN 'Out of Stock' 
                                 WHEN stock BETWEEN 1 AND 10 THEN 'Low Stock' 
                                 WHEN stock BETWEEN 11 AND 50 THEN 'Medium Stock' 
                                 ELSE 'High Stock' 
                               END AS stock_level, 
                               COUNT(*) AS count 
                             FROM Products 
                             WHERE is_deleted = FALSE 
                             GROUP BY stock_level 
                             ORDER BY 
                               CASE stock_level 
                                 WHEN 'Out of Stock' THEN 1 
                                 WHEN 'Low Stock' THEN 2 
                                 WHEN 'Medium Stock' THEN 3 
                                 WHEN 'High Stock' THEN 4 
                               END");

$stockLevelLabels = [];
$stockLevelData = [];

while ($row = $stockLevels->fetch_assoc()) {
  $stockLevelLabels[] = $row['stock_level'];
  $stockLevelData[] = $row['count'];
}

// Get monthly product additions trend (last 6 months)
$productTrendQuery = "SELECT 
                        DATE_FORMAT(date_added, '%Y-%m') as month,
                        COUNT(*) as count
                      FROM Products
                      WHERE 
                        date_added >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                        AND is_deleted = FALSE
                      GROUP BY DATE_FORMAT(date_added, '%Y-%m')
                      ORDER BY month ASC";
$productTrend = $conn->query($productTrendQuery);

$trendLabels = [];
$trendData = [];

while ($row = $productTrend->fetch_assoc()) {
  $trendLabels[] = $row['month'];
  $trendData[] = $row['count'];
}

// Fetch the last 5 products added
$lastProducts = $conn->query("SELECT name, price, stock, date_added FROM Products WHERE is_deleted = FALSE ORDER BY date_added DESC LIMIT 5");
$lastProductsData = [];
while ($row = $lastProducts->fetch_assoc()) {
  $lastProductsData[] = $row;
}

// Fetch the top 5 products by inventory value
$topProducts = $conn->query("SELECT name, price, stock, (price * stock) as value FROM Products WHERE is_deleted = FALSE ORDER BY value DESC LIMIT 5");
$topProductsData = [];
while ($row = $topProducts->fetch_assoc()) {
  $topProductsData[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
  <link rel="shortcut icon" href="design/images/favicon/favicon-16x16.png" type="image/x-icon">
  <link href="design/css/bootstrap.min.css" rel="stylesheet">
  <link href="design/css/products.css" rel="stylesheet">
  <link rel="stylesheet" href="design/css/layout_partial.css">
  <style>
    :root {
      --primary-color: #3498db;
      --secondary-color: #2ecc71;
      --accent-color: #e74c3c;
      --background-color: #f8f9fa;
      --text-color: #333333;
      --danger-color: #e74c3c;
      --warning-color: #f39c12;
      --success-color: #2ecc71;
      --info-color: #3498db;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--background-color);
      color: var(--text-color);
    }

    .dashboard-card {
      background-color: #ffffff;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s ease-in-out;
      margin-bottom: 20px;
      overflow: hidden;
    }

    .dashboard-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .stat-card {
      color: #ffffff;
      padding: 20px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      min-height: 140px;
    }

    .stat-value {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 0;
    }

    .stat-label {
      font-size: 1rem;
      margin-bottom: 0;
      opacity: 0.8;
    }

    .stat-icon {
      font-size: 1.75rem;
      margin-bottom: 10px;
    }

    .chart-container {
      height: 300px;
      padding: 15px;
    }

    .chart-title {
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 15px;
      color: #555;
      padding: 15px 15px 0 15px;
    }

    .table-container {
      padding: 15px;
    }

    .table-dashboard th {
      font-weight: 600;
      color: #555;
    }

    .alert-statistic {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 15px;
      border-radius: 5px;
      margin-bottom: 10px;
      color: white;
    }

    .alert-danger-stat {
      background-color: var(--danger-color);
    }

    .alert-warning-stat {
      background-color: var(--warning-color);
    }

    .alert-success-stat {
      background-color: var(--success-color);
    }

    .alert-info-stat {
      background-color: var(--info-color);
    }

    .alert-statistic .value {
      font-weight: 700;
      font-size: 1.25rem;
    }

    /* scroll bar style */
    ::-webkit-scrollbar {
      width: 8px;
    }

    ::-webkit-scrollbar-track {
      background: transparent;
    }

    ::-webkit-scrollbar-thumb {
      background-color: rgba(0, 0, 0, 0.2);
      border-radius: 4px;
      border: 2px solid transparent;
      background-clip: padding-box;
    }

    ::-webkit-scrollbar-thumb:hover {
      background-color: rgba(0, 0, 0, 0.3);
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

    .bg-warning-gradient {
      background: linear-gradient(135deg, #f46b45 0%, #eea849 100%);
    }

    .bg-info-gradient {
      background: linear-gradient(135deg, #56CCF2 0%, #2F80ED 100%);
    }

    .bg-purple-gradient {
      background: linear-gradient(135deg, #834d9b 0%, #d04ed6 100%);
    }

    .bg-dark-gradient {
      background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
    }

    .bg-teal-gradient {
      background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
  </style>
</head>

<body>
  <?php include 'layout_partial.php'; ?>

  <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
      <h1 class="h2">Dashboard</h1>
      <div class="btn-toolbar mb-2 mb-md-0">
        
      </div>
    </div>

    <!-- Main KPI Cards -->
    <div class="row g-4 mb-4">
      <div class="col-12 col-sm-6 col-md-3">
        <div class="dashboard-card">
          <div class="stat-card bg-primary-gradient">
            <div>
              <i class="bi bi-box stat-icon"></i>
              <h3 class="stat-label">Total Products</h3>
            </div>
            <p class="stat-value"><?php echo $productsCount; ?></p>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <div class="dashboard-card">
          <div class="stat-card bg-success-gradient">
            <div>
              <i class="bi bi-archive stat-icon"></i>
              <h3 class="stat-label">Total Stock</h3>
            </div>
            <p class="stat-value"><?php echo $totalStock; ?></p>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <div class="dashboard-card">
          <div class="stat-card bg-info-gradient">
            <div>
              <i class="bi bi-people stat-icon"></i>
              <h3 class="stat-label">Total Users</h3>
            </div>
            <p class="stat-value"><?php echo $usersCount; ?></p>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <div class="dashboard-card">
          <div class="stat-card bg-warning-gradient">
            <div>
              <i class="bi bi-shop stat-icon"></i>
              <h3 class="stat-label">Total Providers</h3>
            </div>
            <p class="stat-value"><?php echo $providersCount; ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Secondary KPI Cards -->
    <div class="row g-4 mb-4">
      <div class="col-12 col-sm-6 col-md-3">
        <div class="dashboard-card">
          <div class="stat-card bg-danger-gradient">
            <div>
              <i class="bi bi-exclamation-triangle stat-icon"></i>
              <h3 class="stat-label">Low Stock Items</h3>
            </div>
            <p class="stat-value"><?php echo $lowStockCount; ?></p>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <div class="dashboard-card">
          <div class="stat-card bg-purple-gradient">
            <div>
              <i class="bi bi-currency-dollar stat-icon"></i>
              <h3 class="stat-label">Total Capital</h3>
            </div>
            <p class="stat-value"><?php echo number_format((float)$totalCapital, 0, '.', ','); ?></p>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <div class="dashboard-card">
          <div class="stat-card bg-dark-gradient">
            <div>
              <i class="bi bi-file-text stat-icon"></i>
              <h3 class="stat-label">Orders</h3>
            </div>
            <p class="stat-value"><?php echo $ordersCount; ?></p>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <div class="dashboard-card">
          <div class="stat-card bg-teal-gradient">
            <div>
              <i class="bi bi-receipt stat-icon"></i>
              <h3 class="stat-label">Invoices</h3>
            </div>
            <p class="stat-value"><?php echo $invoicesCount; ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
      <div class="col-12 col-lg-8">
        <div class="dashboard-card">
          <div class="chart-title">Products by Provider</div>
          <div class="chart-container">
            <canvas id="productsByProviderChart"></canvas>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="dashboard-card">
          <div class="chart-title">Stock Level Distribution</div>
          <div class="chart-container">
            <canvas id="stockLevelChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Trend Chart Row -->
    <div class="row g-4 mb-4">
      <div class="col-12">
        <div class="dashboard-card">
          <div class="chart-title">Product Additions - 6 Month Trend</div>
          <div class="chart-container">
            <canvas id="productTrendChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Alerts Row -->
    <div class="row g-4 mb-4">
      <div class="col-md-6">
        <div class="dashboard-card">
          <div class="chart-title">Inventory Alerts</div>
          <div class="p-3">
            <div class="alert-statistic alert-danger-stat">
              <span>Out of Stock Items</span>
              <span class="value"><?php echo $outOfStockCount; ?></span>
            </div>
            <div class="alert-statistic alert-warning-stat">
              <span>Low Stock Items</span>
              <span class="value"><?php echo $lowStockCount; ?></span>
            </div>
            <div class="alert-statistic alert-info-stat">
              <span>Average Product Price</span>
              <span class="value"><?php echo number_format((float)$avgProductPrice, 2, '.', ','); ?> MAD</span>
            </div>
            <div class="alert-statistic alert-success-stat">
              <span>Total Inventory Value</span>
              <span class="value"><?php echo number_format((float)$totalCapital, 2, '.', ','); ?> MAD</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="dashboard-card">
          <div class="chart-title">Order & Invoice Status</div>
          <div class="p-3">
            <div class="alert-statistic alert-warning-stat">
              <span>Pending Orders</span>
              <span class="value"><?php echo $pendingOrdersCount; ?></span>
            </div>
            <div class="alert-statistic alert-danger-stat">
              <span>Pending Payments</span>
              <span class="value"><?php echo $pendingPayments; ?></span>
            </div>
            <div class="alert-statistic alert-info-stat">
              <span>Total Invoiced</span>
              <span class="value"><?php echo number_format((float)$totalInvoiced, 2, '.', ','); ?> MAD</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-4 mb-4">
      <div class="col-md-6">
        <div class="dashboard-card">
          <div class="chart-title">Recent Products</div>
          <div class="table-container">
            <table class="table table-hover table-dashboard">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Price</th>
                  <th>Stock</th>
                  <th>Date Added</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($lastProductsData as $product): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo number_format((float)$product['price'], 2, '.', ','); ?> MAD</td>
                    <td>
                      <?php if($product['stock'] <= 0): ?>
                        <span class="badge bg-danger">Out of Stock</span>
                      <?php elseif($product['stock'] < 10): ?>
                        <span class="badge bg-warning text-dark">Low: <?php echo $product['stock']; ?></span>
                      <?php else: ?>
                        <?php echo $product['stock']; ?>
                      <?php endif; ?>
                    </td>
                    <td><?php echo date("Y-m-d", strtotime($product['date_added'])); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="dashboard-card">
          <div class="chart-title">Top Products by Value</div>
          <div class="table-container">
            <table class="table table-hover table-dashboard">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Price</th>
                  <th>Stock</th>
                  <th>Total Value</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($topProductsData as $product): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo number_format((float)$product['price'], 2, '.', ','); ?> MAD</td>
                    <td><?php echo $product['stock']; ?></td>
                    <td><?php echo number_format((float)$product['value'], 2, '.', ','); ?> MAD</td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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

    // Stock Level Chart
    const stockLevelLabels = <?php echo json_encode($stockLevelLabels); ?>;
    const stockLevelData = <?php echo json_encode($stockLevelData); ?>;

    const stockCtx = document.getElementById('stockLevelChart').getContext('2d');
    new Chart(stockCtx, {
      type: 'pie',
      data: {
        labels: stockLevelLabels,
        datasets: [{
          data: stockLevelData,
          backgroundColor: [
            'rgba(255, 99, 132, 0.7)',
            'rgba(255, 159, 64, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)'
          ],
          borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)'
          ],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right'
          }
        }
      }
    });

    // Product Trend Chart
    const trendLabels = <?php echo json_encode($trendLabels); ?>;
    const trendData = <?php echo json_encode($trendData); ?>;

    const trendCtx = document.getElementById('productTrendChart').getContext('2d');
    new Chart(trendCtx, {
      type: 'line',
      data: {
        labels: trendLabels,
        datasets: [{
          label: 'New Products Added',
          data: trendData,
          fill: true,
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          borderColor: 'rgba(75, 192, 192, 1)',
          tension: 0.3,
          pointBackgroundColor: 'rgba(75, 192, 192, 1)',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 5
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
              text: 'Number of Products Added'
            }
          },
          x: {
            title: {
              display: true,
              text: 'Month'
            }
          }
        }
      }
    });
  </script>
</body>

</html>
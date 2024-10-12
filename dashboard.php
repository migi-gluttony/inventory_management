<?php

$current_page = 'dashboard';



session_start();
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Check if the user is logged in and is NOT an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin']) {
  // Redirect admin users to the user management page
  header("Location: user_management.php");
  exit();
}

if (!isset($_SESSION['user_id'])) {
  header("Location: sign-in.php");
  exit();
}

// Database connection
$host = 'localhost';
$db = 'stock_management';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch counts for providers, products, and users
$providersCount = $conn->query("SELECT COUNT(*) AS count FROM Providers")->fetch_assoc()['count'];
$productsCount = $conn->query("SELECT COUNT(*) AS count FROM Products WHERE is_deleted = FALSE")->fetch_assoc()['count'];
$usersCount = $conn->query("SELECT COUNT(*) AS count FROM Users")->fetch_assoc()['count'];

// Fetch total stock and total capital of products
$totalStock = $conn->query("SELECT SUM(stock) AS total FROM Products WHERE is_deleted = FALSE")->fetch_assoc()['total'] ?? 0;
$totalCapital = $conn->query("SELECT SUM(price * stock) AS total FROM Products WHERE is_deleted = FALSE")->fetch_assoc()['total'] ?? 0;

// Fetch number of products by each provider
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

// Fetch the last 5 products added
$lastProducts = $conn->query("SELECT name, price, date_added FROM Products WHERE is_deleted = FALSE ORDER BY date_added DESC LIMIT 15");
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
      transition: transform 0.3s ease-in-out;
    }

    .dashboard-card:hover {
      transform: translateY(-5px);
    }

    .stat-card {
      background-color: var(--primary-color);
      color: #ffffff;
    }

    .chart-container {
      height: 300px;
    }

    .table-container {
      max-height: 400px;
      overflow-y: auto;
    }

    @media (max-width: 768px) {
      .chart-container {
        height: 200px;
      }
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

    /*# sourceMappingURL=style.css.map */
  </style>
</head>

<body>
  <div>
    <?php include 'layout_partial.php'; ?>
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
      </div>

      <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="dashboard-card stat-card p-3" style="background-color: #222831;"> <!-- Darker Gray -->
            <h3 class="fs-5 mb-1 text-white">Total Products</h3>
            <p class="fs-2 mb-0 text-white"><?php echo $productsCount; ?></p>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="dashboard-card stat-card p-3" style="background-color: #393E46;"> <!-- Dark Gray -->
            <h3 class="fs-5 mb-1 text-white">Total Stock</h3>
            <p class="fs-2 mb-0 text-white"><?php echo $totalStock; ?></p>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="dashboard-card stat-card p-3" style="background-color: #00ADB5;"> <!-- Teal -->
            <h3 class="fs-5 mb-1 text-white">Total Users</h3>
            <p class="fs-2 mb-0 text-white"><?php echo $usersCount; ?></p>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="dashboard-card stat-card p-3" style="background-color: #EEEEEE;"> <!-- Light Gray -->
            <h3 class="fs-5 mb-1 text-dark">Total Providers</h3>
            <p class="fs-2 mb-0 text-dark"><?php echo $providersCount; ?></p>
          </div>
        </div>


        <div class="row g-4 mb-4">
          <div class="col-12 col-lg-8">
            <div class="dashboard-card p-3">
              <h2 class="h4 mb-3">Products by Provider</h2>
              <div class="chart-container">
                <canvas id="productsByProviderChart"></canvas>
              </div>
            </div>
          </div>
          <div class="col-12 col-lg-4">
            <div class="dashboard-card p-3">
              <h2 class="h4 mb-3">Total Capital in Stock</h2>
              <p class="fs-1 text-center mb-0"><?php echo number_format((float)$totalCapital, 2, '.', ','); ?> MAD</p>
            </div>
          </div>
        </div>

        <div class="dashboard-card p-3 mb-4">
          <h2 class="h4 mb-3">Last Products Added</h2>
          <div class="table-container">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Price</th>
                  <th>Date Added</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($lastProductsData as $product): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo number_format((float)$product['price'], 2, '.', ','); ?> MAD</td>
                    <td><?php echo date("Y-m-d H:i:s", strtotime($product['date_added'])); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
    </main>
  </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Data for the chart
    const providerNames = <?php echo json_encode($providerNames); ?>;
    const productCounts = <?php echo json_encode($productCounts); ?>;

    // Chart configuration
    const ctx = document.getElementById('productsByProviderChart').getContext('2d');
    const productsByProviderChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: providerNames,
        datasets: [{
          label: 'Number of Products',
          data: productCounts,
          backgroundColor: 'rgba(52, 152, 219, 0.7)',
          borderColor: 'rgba(52, 152, 219, 1)',
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
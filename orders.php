<?php
$current_page = 'orders';
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin']) {
  header("Location: user_management.php");
  exit();
}
if (!isset($_SESSION['user_id'])) {
  header("Location: sign-in.php");
  exit();
}

// Fetch orders with related information
$ordersQuery = "SELECT o.*, u.name as user_name 
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.order_date DESC";
$ordersResult = $conn->query($ordersQuery);



$conn->close();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Orders</title>
  <link rel="shortcut icon" href="design/images/favicon/favicon-16x16.png" type="image/x-icon">
  <link href="design/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="design/css/products.css" rel="stylesheet">
  <link rel="stylesheet" href="design/css/layout_partial.css">
</head>
<body>
  <?php include 'layout_partial.php'; ?>

  <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="container mt-5">
      <h2>Orders</h2>
      <div class="table-responsive small">
        <table class="table table-striped table-sm">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Date</th>
              <th>Status</th>
              <th>User</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($ordersResult && $ordersResult->num_rows > 0): ?>
              <?php while($order = $ordersResult->fetch_assoc()): ?>
                <tr>
                  <td><?php echo htmlspecialchars($order['id']); ?></td>
                  <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                  <td><?php echo htmlspecialchars($order['status']); ?></td>
                  <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                  <td>
                    <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-info">View</a>
                    <a href="create_invoice.php?order_id=<?php echo $order['id']; ?>" class="btn btn-sm btn-success">Generate Invoice</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="5">No orders found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <a href="create_order.php" class="btn btn-primary mt-3">Create New Order</a>
    </div>
  </main>

  <script src="design/js/bootstrap.bundle.min.js"></script>
</body>
</html>
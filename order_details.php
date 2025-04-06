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

// Check if order ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: orders.php");
  exit();
}

$order_id = $_GET['id'];

// Fetch order details
$orderQuery = "SELECT o.*, u.name as user_name 
               FROM orders o
               LEFT JOIN users u ON o.user_id = u.id
               WHERE o.id = ?";
$stmt = $conn->prepare($orderQuery);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$orderResult = $stmt->get_result();

if ($orderResult->num_rows === 0) {
  header("Location: orders.php");
  exit();
}

$order = $orderResult->fetch_assoc();

// Fetch order items
$itemsQuery = "SELECT oi.*, p.name as product_name 
               FROM order_items oi
               LEFT JOIN products p ON oi.product_id = p.id
               WHERE oi.order_id = ?";
$stmt = $conn->prepare($itemsQuery);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$itemsResult = $stmt->get_result();
$orderItems = $itemsResult->fetch_all(MYSQLI_ASSOC);

// Check if there's an invoice for this order
$invoiceQuery = "SELECT id FROM invoices WHERE order_id = ?";
$stmt = $conn->prepare($invoiceQuery);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$invoiceResult = $stmt->get_result();
$hasInvoice = $invoiceResult->num_rows > 0;
if ($hasInvoice) {
  $invoice = $invoiceResult->fetch_assoc();
  $invoice_id = $invoice['id'];
}

// Calculate order total
$orderTotal = 0;
foreach ($orderItems as $item) {
  $orderTotal += $item['quantity'] * $item['price'];
}

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $updateQuery = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $new_status, $order_id);
    
    if ($stmt->execute()) {
      $success_message = "Order status updated successfully.";
      // Refresh order data
      $order['status'] = $new_status;
    } else {
      $error_message = "Failed to update order status: " . $conn->error;
    }
  }
}

$conn->close();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Order Details</title>
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
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Order Details #<?php echo $order_id; ?></h2>
        <a href="orders.php" class="btn btn-outline-secondary">Back to Orders</a>
      </div>
      
      <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
      <?php endif; ?>
      
      <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
      <?php endif; ?>
      
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              Order Information
            </div>
            <div class="card-body">
              <p><strong>Date:</strong> <?php echo $order['order_date']; ?></p>
              <p><strong>Status:</strong> <?php echo $order['status']; ?></p>
              <p><strong>Created by:</strong> <?php echo $order['user_name']; ?></p>
              
              <form method="POST" class="mt-3">
                <div class="input-group">
                  <select class="form-select" name="status">
                    <option value="pending" <?php echo ($order['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="processing" <?php echo ($order['status'] === 'processing') ? 'selected' : ''; ?>>Processing</option>
                    <option value="shipped" <?php echo ($order['status'] === 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                    <option value="delivered" <?php echo ($order['status'] === 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                    <option value="canceled" <?php echo ($order['status'] === 'canceled') ? 'selected' : ''; ?>>Canceled</option>
                  </select>
                  <button type="submit" class="btn btn-primary" name="update_status">Update Status</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              Invoice Information
            </div>
            <div class="card-body">
              <?php if ($hasInvoice): ?>
                <p>Invoice has been generated for this order.</p>
                <a href="view_invoice.php?id=<?php echo $invoice_id; ?>" class="btn btn-info">View Invoice</a>
              <?php else: ?>
                <p>No invoice has been generated for this order yet.</p>
                <a href="create_invoice.php?order_id=<?php echo $order_id; ?>" class="btn btn-success">Generate Invoice</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      
      <div class="card mb-4">
        <div class="card-header">
          Order Items
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Quantity</th>
                  <th>Unit Price</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count($orderItems) > 0): ?>
                  <?php foreach ($orderItems as $item): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                      <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                      <td><?php echo number_format($item['price'], 2); ?> MAD</td>
                      <td><?php echo number_format($item['quantity'] * $item['price'], 2); ?> MAD</td>
                    </tr>
                  <?php endforeach; ?>
                  <tr>
                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                    <td><strong><?php echo number_format($orderTotal, 2); ?> MAD</strong></td>
                  </tr>
                <?php else: ?>
                  <tr>
                    <td colspan="4" class="text-center">No items in this order</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="design/js/bootstrap.bundle.min.js"></script>
</body>
</html>
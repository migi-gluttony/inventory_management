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
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
  header("Location: orders.php");
  exit();
}

$order_id = $_GET['order_id'];

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

// Check if invoice already exists
$invoiceCheckQuery = "SELECT id FROM invoices WHERE order_id = ?";
$stmt = $conn->prepare($invoiceCheckQuery);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$invoiceCheckResult = $stmt->get_result();

if ($invoiceCheckResult->num_rows > 0) {
  // Invoice already exists, redirect to the invoice view
  $invoice = $invoiceCheckResult->fetch_assoc();
  header("Location: view_invoice.php?id=" . $invoice['id']);
  exit();
}

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

// Calculate order total
$orderTotal = 0;
foreach ($orderItems as $item) {
  $orderTotal += $item['quantity'] * $item['price'];
}

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['create_invoice'])) {
    $total_amount = $_POST['total_amount'];
    $payment_status = 'pending'; // Default status
    
    // Create invoice
    $stmt = $conn->prepare("INSERT INTO invoices (order_id, total_amount, payment_status) VALUES (?, ?, ?)");
    $stmt->bind_param("ids", $order_id, $total_amount, $payment_status);
    
    if ($stmt->execute()) {
      $invoice_id = $conn->insert_id;
      // Redirect to the invoice view
      header("Location: view_invoice.php?id=" . $invoice_id);
      exit();
    } else {
      $error_message = "Failed to create invoice: " . $conn->error;
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
  <title>Create Invoice</title>
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
        <h2>Create Invoice for Order #<?php echo $order_id; ?></h2>
        <a href="order_details.php?id=<?php echo $order_id; ?>" class="btn btn-outline-secondary">Back to Order</a>
      </div>
      
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
              <p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>
              <p><strong>Status:</strong> <?php echo $order['status']; ?></p>
              <p><strong>Created by:</strong> <?php echo $order['user_name']; ?></p>
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
      
      <div class="card mb-4">
        <div class="card-header">
          Invoice Details
        </div>
        <div class="card-body">
          <form method="POST">
            <div class="mb-3">
              <label for="total_amount" class="form-label">Total Amount</label>
              <input type="number" class="form-control" id="total_amount" name="total_amount" value="<?php echo $orderTotal; ?>" step="0.01" required>
              <small class="text-muted">You can adjust the total if needed (discounts, shipping, etc.)</small>
            </div>
            
            <button type="submit" name="create_invoice" class="btn btn-primary">Create Invoice</button>
            <a href="order_details.php?id=<?php echo $order_id; ?>" class="btn btn-outline-secondary">Cancel</a>
          </form>
        </div>
      </div>
    </div>
  </main>

  <script src="design/js/bootstrap.bundle.min.js"></script>
</body>
</html>
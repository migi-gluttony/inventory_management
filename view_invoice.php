<?php
$current_page = 'invoices';
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin']) {
  header("Location: user_management.php");
  exit();
}
if (!isset($_SESSION['user_id'])) {
  header("Location: sign-in.php");
  exit();
}

// Check if invoice ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: invoices.php");
  exit();
}

$invoice_id = $_GET['id'];

// Fetch invoice details
$invoiceQuery = "SELECT i.*, o.order_date, o.id as order_id 
                FROM invoices i
                LEFT JOIN orders o ON i.order_id = o.id
                WHERE i.id = ?";
$stmt = $conn->prepare($invoiceQuery);
$stmt->bind_param("i", $invoice_id);
$stmt->execute();
$invoiceResult = $stmt->get_result();

if ($invoiceResult->num_rows === 0) {
  header("Location: invoices.php");
  exit();
}

$invoice = $invoiceResult->fetch_assoc();
$order_id = $invoice['order_id'];

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

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update_status'])) {
    $new_status = $_POST['payment_status'];
    $updateQuery = "UPDATE invoices SET payment_status = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $new_status, $invoice_id);

    if ($stmt->execute()) {
      $success_message = "Invoice payment status updated successfully.";
      // Refresh invoice data
      $invoice['payment_status'] = $new_status;
    } else {
      $error_message = "Failed to update invoice status: " . $conn->error;
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
  <title>View Invoice</title>
  <link rel="shortcut icon" href="design/images/favicon/favicon-16x16.png" type="image/x-icon">
  <link href="design/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="design/css/products.css" rel="stylesheet">
  <link rel="stylesheet" href="design/css/layout_partial.css">
  <style>
    @media print {
      .no-print {
        display: none;
      }

      body {
        padding: 0;
        margin: 0;
      }

      .container {
        width: 100%;
        max-width: 100%;
      }
    }

    .invoice-header {
      padding: 20px 0;
      border-bottom: 1px solid #ddd;
    }

    .invoice-title {
      font-size: 28px;
      color: #555;
    }

    .invoice-details {
      margin-top: 20px;
      margin-bottom: 20px;
    }

    .invoice-table {
      margin-top: 30px;
    }

    .invoice-total {
      margin-top: 30px;
      text-align: right;
    }
  </style>
</head>

<body>
  <div class="no-print">
    <?php include 'layout_partial.php'; ?>
  </div>

  <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="container mt-5">
      <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h2>Invoice #<?php echo $invoice_id; ?></h2>
        <div>
          <a href="print_invoice.php?id=<?php echo $invoice_id; ?>" class="btn btn-primary">Print Invoice</a>
          <a href="invoices.php" class="btn btn-outline-secondary">Back to Invoices</a>
        </div>
      </div>

      <?php if (isset($success_message)): ?>
        <div class="alert alert-success no-print"><?php echo $success_message; ?></div>
      <?php endif; ?>

      <?php if (isset($error_message)): ?>
        <div class="alert alert-danger no-print"><?php echo $error_message; ?></div>
      <?php endif; ?>

      <div class="card mb-4">
        <div class="card-body">
          <div class="invoice-header d-flex justify-content-between align-items-center">
            <div>
              <h1 class="invoice-title">INVOICE</h1>
              <p>Invoice #: <?php echo $invoice_id; ?></p>
              <p>Date: <?php echo date('Y-m-d', strtotime($invoice['invoice_date'])); ?></p>
            </div>
            <div>
              <h3>JK-TEX</h3>
              <p>123 Main Street</p>
              <p>Casablanca, Morocco</p>
              <p>Email: contact@jk-tex.com</p>
            </div>
          </div>

          <div class="row invoice-details">
            <div class="col-md-6">
              <h5>Invoice To:</h5>
              <p>Customer</p>
              <p>Order #: <?php echo $order_id; ?></p>
              <p>Order Date: <?php echo date('Y-m-d', strtotime($invoice['order_date'])); ?></p>
            </div>
            <div class="col-md-6 text-md-end no-print">
              <h5>Payment Status:</h5>
              <form method="POST" class="d-inline">
                <div class="input-group" style="max-width: 300px; float: right;">
                  <select class="form-select" name="payment_status">
                    <option value="pending" <?php echo ($invoice['payment_status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="partial" <?php echo ($invoice['payment_status'] === 'partial') ? 'selected' : ''; ?>>Partial</option>
                    <option value="paid" <?php echo ($invoice['payment_status'] === 'paid') ? 'selected' : ''; ?>>Paid</option>
                    <option value="canceled" <?php echo ($invoice['payment_status'] === 'canceled') ? 'selected' : ''; ?>>Canceled</option>
                  </select>
                  <button type="submit" class="btn btn-primary" name="update_status">Update</button>
                </div>
              </form>
            </div>
            <div class="col-md-6 text-md-end d-none d-print-block">
              <h5>Payment Status:</h5>
              <p><?php echo ucfirst($invoice['payment_status']); ?></p>
            </div>
          </div>

          <div class="invoice-table">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Quantity</th>
                  <th>Unit Price</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orderItems as $item): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo number_format($item['price'], 2); ?> MAD</td>
                    <td><?php echo number_format($item['quantity'] * $item['price'], 2); ?> MAD</td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div class="invoice-total">
            <table class="table table-borderless" style="max-width: 300px; margin-left: auto;">
              <tr>
                <td><strong>Subtotal:</strong></td>
                <td><?php echo number_format($invoice['total_amount'], 2); ?> MAD</td>
              </tr>
              <tr>
                <td><strong>Tax (20%):</strong></td>
                <td><?php echo number_format($invoice['total_amount'] * 0.2, 2); ?> MAD</td>
              </tr>
              <tr>
                <td><strong>Total:</strong></td>
                <td><strong><?php echo number_format($invoice['total_amount'] * 1.2, 2); ?> MAD</strong></td>
              </tr>
            </table>
          </div>

          <div class="mt-5 mb-3">
            <p><strong>Notes:</strong></p>
            <?php
            // Display different messages based on payment status
            switch ($invoice['payment_status']) {
              case 'pending':
                echo "<p>Thank you for your business! Payment is due within 30 days.</p>";
                break;
              case 'partial':
                echo "<p>Thank you for your partial payment. Please remit the remaining balance within 15 days.</p>";
                break;
              case 'paid':
                echo "<p>Thank you for your business! This invoice has been fully paid.</p>";
                break;
              case 'canceled':
                echo "<p>This invoice has been canceled and requires no payment.</p>";
                break;
              default:
                echo "<p>Thank you for your business!</p>";
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="design/js/bootstrap.bundle.min.js"></script>
</body>

</html>
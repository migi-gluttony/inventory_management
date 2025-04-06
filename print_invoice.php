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

$conn->close();
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Print Invoice #<?php echo $invoice_id; ?></title>
  <link rel="shortcut icon" href="design/images/favicon/favicon-16x16.png" type="image/x-icon">
  <link href="design/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: white;
      font-family: Arial, sans-serif;
      padding: 20px;
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

    @media print {
      @page {
        size: A4;
        margin: 0;
      }

      .no-print, .no-print * {
      display: none !important;
    }
    }
  </style>
  <script src="design/js/bootstrap.bundle.min.js"></script>
</head>

<body>
  <div class="container">
    <div class="no-print d-flex justify-content-between align-items-center mb-4">
      <h2>Invoice #<?php echo $invoice_id; ?></h2>
      <div>
        <button onclick="window.print();" class="btn btn-primary">Print</button>
        <a href="view_invoice.php?id=<?php echo $invoice_id; ?>" class="btn btn-outline-secondary">Back</a>
      </div>
      <script>
        window.addEventListener('load', function() {
          setTimeout(function() {
            window.print();
          }, 500);
        });
      </script>
    </div>

    <div class="card">
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
          <div class="col-md-6 text-md-end">
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
</body>

</html>
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

// Fetch invoices with related information
$invoicesQuery = "SELECT i.*, o.order_date 
                  FROM invoices i
                  LEFT JOIN orders o ON i.order_id = o.id
                  ORDER BY i.invoice_date DESC";
$invoicesResult = $conn->query($invoicesQuery);

$conn->close();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Invoices</title>
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
      <h2>Invoices</h2>
      <div class="table-responsive small">
        <table class="table table-striped table-sm">
          <thead>
            <tr>
              <th>Invoice ID</th>
              <th>Date</th>
              <th>Order Date</th>
              <th>Total Amount</th>
              <th>Payment Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($invoicesResult && $invoicesResult->num_rows > 0): ?>
              <?php while($invoice = $invoicesResult->fetch_assoc()): ?>
                <tr>
                  <td><?php echo htmlspecialchars($invoice['id']); ?></td>
                  <td><?php echo htmlspecialchars($invoice['invoice_date']); ?></td>
                  <td><?php echo htmlspecialchars($invoice['order_date']); ?></td>
                  <td><?php echo htmlspecialchars($invoice['total_amount']); ?> MAD</td>
                  <td><?php echo htmlspecialchars($invoice['payment_status']); ?></td>
                  <td>
                    <a href="view_invoice.php?id=<?php echo $invoice['id']; ?>" class="btn btn-sm btn-info">View</a>
                    
                    <a href="print_invoice.php?id=<?php echo $invoice['id']; ?>" class="btn btn-sm btn-secondary">Print</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6">No invoices found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <script src="design/js/bootstrap.bundle.min.js"></script>
</body>
</html>
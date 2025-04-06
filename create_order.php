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

// Process form submission for creating a new order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['create_order'])) {
    // Start a transaction
    $conn->begin_transaction();
    
    try {
      // Create the order
      $stmt = $conn->prepare("INSERT INTO orders (user_id) VALUES (?)");
      $stmt->bind_param("i", $_SESSION['user_id']);
      $stmt->execute();
      $orderId = $conn->insert_id;
      $stmt->close();
      
      // Add order items
      $products = $_POST['products'];
      $quantities = $_POST['quantities'];
      $prices = $_POST['prices'];
      
      for ($i = 0; $i < count($products); $i++) {
        if (!empty($products[$i]) && !empty($quantities[$i]) && !empty($prices[$i])) {
          $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
          $stmt->bind_param("iiid", $orderId, $products[$i], $quantities[$i], $prices[$i]);
          $stmt->execute();
          $stmt->close();
          
          // Update product stock
          $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
          $stmt->bind_param("ii", $quantities[$i], $products[$i]);
          $stmt->execute();
          $stmt->close();
        }
      }
      
      // Commit the transaction
      $conn->commit();
      header("Location: orders.php");
      exit();
    } catch (Exception $e) {
      // Rollback transaction on error
      $conn->rollback();
      $error = "Error creating order: " . $e->getMessage();
    }
  }
}

// Fetch available products
$productsQuery = "SELECT id, name, price, stock FROM products WHERE is_deleted = 0 AND stock > 0";
$productsResult = $conn->query($productsQuery);
$products = $productsResult->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create Order</title>
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
      <h2>Create New Order</h2>
      
      <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php endif; ?>
      
      <form method="POST">
        <div id="orderItems">
          <div class="row mb-3 order-item">
            <div class="col-md-5">
              <label for="products0" class="form-label">Product</label>
              <select name="products[]" id="products0" class="form-control product-select" required>
                <option value="">Select Product</option>
                <?php foreach ($products as $product): ?>
                  <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>" data-stock="<?php echo $product['stock']; ?>">
                    <?php echo htmlspecialchars($product['name'] . ' - $' . $product['price'] . ' (Stock: ' . $product['stock'] . ')'); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <label for="quantities0" class="form-label">Quantity</label>
              <input type="number" name="quantities[]" id="quantities0" class="form-control quantity-input" min="1" required>
            </div>
            <div class="col-md-3">
              <label for="prices0" class="form-label">Price</label>
              <input type="number" name="prices[]" id="prices0" class="form-control price-input" step="0.01" readonly>
            </div>
            <div class="col-md-1 d-flex align-items-end">
              <button type="button" class="btn btn-danger mb-3 remove-item" disabled>Remove</button>
            </div>
          </div>
        </div>
        
        <button type="button" id="addItem" class="btn btn-secondary mb-3">Add Another Item</button>
        
        <div class="row">
          <div class="col-md-6">
            <button type="submit" name="create_order" class="btn btn-primary">Create Order</button>
            <a href="orders.php" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </div>
      </form>
    </div>
  </main>

  <script src="design/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Add item button functionality
      document.getElementById('addItem').addEventListener('click', function() {
        const items = document.querySelectorAll('.order-item');
        const lastItem = items[items.length - 1];
        const newItem = lastItem.cloneNode(true);
        const itemIndex = items.length;
        
        // Update IDs and clear values
        newItem.querySelector('.product-select').id = 'products' + itemIndex;
        newItem.querySelector('.quantity-input').id = 'quantities' + itemIndex;
        newItem.querySelector('.quantity-input').value = '';
        newItem.querySelector('.price-input').id = 'prices' + itemIndex;
        newItem.querySelector('.price-input').value = '';
        
        // Enable remove button
        const removeButton = newItem.querySelector('.remove-item');
        removeButton.disabled = false;
        removeButton.addEventListener('click', function() {
          newItem.remove();
        });
        
        // Add event listeners to new elements
        const productSelect = newItem.querySelector('.product-select');
        const quantityInput = newItem.querySelector('.quantity-input');
        const priceInput = newItem.querySelector('.price-input');
        
        setupProductSelect(productSelect, quantityInput, priceInput);
        
        document.getElementById('orderItems').appendChild(newItem);
      });
      
      // Setup event listeners for the first item
      const firstProductSelect = document.getElementById('products0');
      const firstQuantityInput = document.getElementById('quantities0');
      const firstPriceInput = document.getElementById('prices0');
      
      setupProductSelect(firstProductSelect, firstQuantityInput, firstPriceInput);
      
      function setupProductSelect(productSelect, quantityInput, priceInput) {
        productSelect.addEventListener('change', function() {
          const selectedOption = this.options[this.selectedIndex];
          const price = selectedOption.getAttribute('data-price');
          const stock = selectedOption.getAttribute('data-stock');
          
          quantityInput.max = stock;
          priceInput.value = price;
          
          updatePrice();
        });
        
        quantityInput.addEventListener('input', updatePrice);
        
        function updatePrice() {
          const quantity = quantityInput.value;
          const unitPrice = priceInput.value;
          if (quantity && unitPrice) {
            priceInput.value = unitPrice;
          }
        }
      }
    });
  </script>
</body>
</html>
<?php
session_start();

if (session_status() == PHP_SESSION_NONE) {
  session_start();
};
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






ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$current_page = 'products';

// Database connection
$host = 'localhost';
$db = 'stock_management';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['action'])) {
    switch ($_POST['action']) {
      case 'edit':
        updateProduct($conn);
        break;
      case 'delete':
        deleteProduct($conn);
        break;
    }
  }
}

// Fetch products with provider information
$productQuery = "SELECT p.*, pr.name as provider_name 
                 FROM Products p
                 LEFT JOIN Providers pr ON p.provider_id = pr.id
                 WHERE p.is_deleted = 0";
$productsResult = $conn->query($productQuery);

if (!$productsResult) {
  die("Error fetching products: " . $conn->error);
}

$products = $productsResult->fetch_all(MYSQLI_ASSOC);

// Fetch all providers
$providerQuery = "SELECT * FROM Providers";
$providersResult = $conn->query($providerQuery);

if (!$providersResult) {
  die("Error fetching providers: " . $conn->error);
}

$providers = $providersResult->fetch_all(MYSQLI_ASSOC);

$conn->close();

function updateProduct($conn)
{
  $id = $_POST['productId'];
  $name = $_POST['productName'];
  $price = $_POST['productPrice'];
  $stock = $_POST['productStock'];
  $provider_id = $_POST['productProvider'];
  $date_modified = date('Y-m-d H:i:s');

  $stmt = $conn->prepare("UPDATE Products SET name = ?, price = ?, stock = ?, provider_id = ?, date_modified = ? WHERE id = ?");
  $stmt->bind_param("sdiiis", $name, $price, $stock, $provider_id, $date_modified, $id);
  $stmt->execute();
  $stmt->close();
}

function deleteProduct($conn)
{
  $id = $_POST['productId'];
  $date_modified = date('Y-m-d H:i:s');

  $stmt = $conn->prepare("UPDATE Products SET is_deleted = 1, date_modified = ? WHERE id = ?");
  $stmt->bind_param("si", $date_modified, $id);
  $stmt->execute();
  $stmt->close();
}
?>

<!doctype html>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Products</title>
  <link rel="shortcut icon" href="design/images/favicon/favicon-16x16.png" type="image/x-icon">
  <link href="design/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="design/css/products.css" rel="stylesheet">
  <link rel="stylesheet" href="design/css/layout_partial.css">
  <style>
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
  </style>
</head>

<body>
  <?php include 'layout_partial.php'; ?>

  <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="container mt-5">
      <h2>Products</h2>
      <div class="table-responsive small">
        <table class="table table-striped table-sm" id="productTable">
          <thead>
            <tr>
              <th>Product ID</th>
              <th>Product Name</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Provider</th>
              <th>Date Added</th>
              <th>Date Modified</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($products)): ?>
              <?php foreach ($products as $product): ?>
                <tr>
                  <td><?php echo htmlspecialchars($product['id']); ?></td>
                  <td><?php echo htmlspecialchars($product['name']); ?></td>
                  <td><?php echo htmlspecialchars($product['price']); ?> MAD</td>
                  <td><?php echo htmlspecialchars($product['stock']); ?></td>
                  <td><?php echo htmlspecialchars($product['provider_name'] ?? 'Unknown'); ?></td>
                  <td><?php echo htmlspecialchars($product['date_added']); ?></td>
                  <td><?php echo htmlspecialchars($product['date_modified']); ?></td>
                  <td>
                    <button class="btn btn-sm btn-primary" onclick="populateEditForm(<?php echo $product['id']; ?>)">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteProduct(<?php echo $product['id']; ?>)">Delete</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="8">No products found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Edit Product Modal -->
  <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editProductForm" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" id="editProductId" name="productId">
            <div class="mb-3">
              <label for="editProductName" class="form-label">Product Name</label>
              <input type="text" class="form-control" id="editProductName" name="productName" required>
            </div>
            <div class="mb-3">
              <label for="editProductPrice" class="form-label">Price</label>
              <input type="number" class="form-control" id="editProductPrice" name="productPrice" step="0.01" required>
            </div>
            <div class="mb-3">
              <label for="editProductStock" class="form-label">Stock Quantity</label>
              <input type="number" class="form-control" id="editProductStock" name="productStock" required>
            </div>
            <div class="mb-3">
              <label for="editProductProvider" class="form-label">Provider</label>
              <select class="form-control" id="editProductProvider" name="productProvider" required>
                <?php foreach ($providers as $provider): ?>
                  <option value="<?php echo $provider['id']; ?>"><?php echo htmlspecialchars($provider['name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Save changes</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="design/js/bootstrap.bundle.min.js"></script>
  <script>
    // Pass PHP arrays to JavaScript
    const products = <?php echo json_encode($products); ?>;
    const providers = <?php echo json_encode($providers); ?>;

    function populateEditForm(productId) {
      const product = products.find(p => p.id == productId);
      if (product) {
        document.getElementById('editProductId').value = product.id;
        document.getElementById('editProductName').value = product.name;
        document.getElementById('editProductPrice').value = product.price;
        document.getElementById('editProductStock').value = product.stock;
        document.getElementById('editProductProvider').value = product.provider_id;

        new bootstrap.Modal(document.getElementById('editProductModal')).show();
      }
    }

    function deleteProduct(productId) {
      if (confirm("Are you sure you want to delete this product?")) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="productId" value="${productId}">
                `;
        document.body.appendChild(form);
        form.submit();
      }
    }

    document.getElementById('editProductForm').addEventListener('submit', function(e) {
      e.preventDefault();
      this.submit();
    });

    console.log('Products:', products);
    console.log('Providers:', providers);
  </script>
</body>

</html>
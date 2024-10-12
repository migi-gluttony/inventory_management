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

$current_page = 'deleted_products';

// Database connection
$host = 'localhost';
$db = 'stock_management';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'restore':
                $message = restoreProduct($conn);
                break;
            case 'permanent_delete':
                $message = permanentlyDeleteProduct($conn);
                break;
        }
    }
}

// Fetch deleted products with provider information
$productQuery = "SELECT p.*, pr.name as provider_name 
                 FROM Products p
                 LEFT JOIN Providers pr ON p.provider_id = pr.id
                 WHERE p.is_deleted = 1";
$productsResult = $conn->query($productQuery);

if (!$productsResult) {
    die("Error fetching deleted products: " . $conn->error);
}

$deletedProducts = $productsResult->fetch_all(MYSQLI_ASSOC);

function restoreProduct($conn)
{
    $id = $_POST['productId'];
    $date_modified = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("UPDATE Products SET is_deleted = 0, date_modified = ? WHERE id = ?");
    $stmt->bind_param("si", $date_modified, $id);

    if ($stmt->execute()) {
        $stmt->close();
        return "Product restored successfully.";
    } else {
        $stmt->close();
        return "Error restoring product: " . $conn->error;
    }
}

function permanentlyDeleteProduct($conn)
{
    $id = $_POST['productId'];

    $stmt = $conn->prepare("DELETE FROM Products WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $stmt->close();
        return "Product permanently deleted.";
    } else {
        $stmt->close();
        return "Error deleting product: " . $conn->error;
    }
}

$conn->close();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Deleted Products</title>
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
            <h2>Deleted Products</h2>
            <?php if (!empty($message)): ?>
                <div class="alert alert-info" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <div class="table-responsive small">
                <table class="table table-striped table-sm" id="deletedProductTable">
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
                        <?php if (!empty($deletedProducts)): ?>
                            <?php foreach ($deletedProducts as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['id']); ?></td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['price']); ?> MAD</td>
                                    <td><?php echo htmlspecialchars($product['stock']); ?></td>
                                    <td><?php echo htmlspecialchars($product['provider_name'] ?? 'Unknown'); ?></td>
                                    <td><?php echo htmlspecialchars($product['date_added']); ?></td>
                                    <td><?php echo htmlspecialchars($product['date_modified']); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="restore">
                                            <input type="hidden" name="productId" value="<?php echo $product['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-success">Restore</button>
                                        </form>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to permanently delete this product? This action cannot be undone.');">
                                            <input type="hidden" name="action" value="permanent_delete">
                                            <input type="hidden" name="productId" value="<?php echo $product['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Permanently Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">No deleted products found</td>
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
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



$current_page = 'add_product';

// Database connection
$servername = "localhost";
$username = "root";  // default XAMPP username
$password = "";      // default XAMPP password (empty)
$dbname = "stock_management";  // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = ''; // Variable to store success or error messages
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $provider_id = $_POST['provider_id'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO Products (name, price, stock, provider_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdii", $name, $price, $stock, $provider_id);

    // Execute the statement
    if ($stmt->execute()) {
        $message = "New product added successfully";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch providers
$sql = "SELECT id, name FROM Providers";
$result = $conn->query($sql);
$providers = $result->fetch_all(MYSQLI_ASSOC);

?>

<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Product</title>
    <link rel="shortcut icon" href="design/images/favicon/favicon-16x16.png" type="image/x-icon">
    <link href="design/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="design/css/dashboard.css" rel="stylesheet">
    <link href="design/css/layout_partial.css" rel="stylesheet">
</head>

<body>

    <?php include 'layout_partial.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="container mt-5">
            <h2>Add New Product</h2>
            
            <?php
            if (!empty($message)) {
                echo "<div class='alert " . (strpos($message, 'Error') !== false ? 'alert-danger' : 'alert-success') . "' role='alert'>$message</div>";
            }
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="mb-3">
                    <label for="productName" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="productName" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="productPrice" class="form-label">Price</label>
                    <input type="number" class="form-control" id="productPrice" name="price" step="0.01" required>
                </div>

                <div class="mb-3">
                    <label for="productStock" class="form-label">Stock Quantity</label>
                    <input type="number" class="form-control" id="productStock" name="stock" required>
                </div>

                <div class="mb-3">
                    <label for="providerId" class="form-label">Provider</label>
                    <select class="form-select" id="providerId" name="provider_id" required>
                        <option selected disabled>Select Provider</option>
                        <?php foreach ($providers as $provider): ?>
                            <option value="<?php echo $provider['id']; ?>"><?php echo htmlspecialchars($provider['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Add Product</button>
            </form>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </main>
    </div>
    </div>

    <script src="design/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$conn->close();
?>
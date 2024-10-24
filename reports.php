<?php
// to know which page you're in for the left navbar highlight

$current_page = 'reports';


// Include the database connection file

include 'db_connection.php';



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


// Fetching report based on selected type
$reportType = isset($_POST['report_type']) ? $_POST['report_type'] : '';
$reportData = '';

if ($reportType == 'products') {
    $sql = "SELECT Products.id, Products.name, Products.price, Products.stock, Products.date_added, Products.date_modified, Providers.name AS provider_name 
            FROM Products 
            JOIN Providers ON Products.provider_id = Providers.id 
            WHERE Products.is_deleted = FALSE";
    $result = $conn->query($sql);
    $reportData = '<h2>Products Report</h2><table class="table"><tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Date Added</th><th>Date Modified</th><th>Provider</th></tr>';
    while ($row = $result->fetch_assoc()) {
        $reportData .= "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['price']} MAD</td><td>{$row['stock']}</td><td>{$row['date_added']}</td><td>{$row['date_modified']}</td><td>{$row['provider_name']}</td></tr>";
    }
    $reportData .= '</table>';
} elseif ($reportType == 'deleted_products') {
    $sql = "SELECT Products.id, Products.name, Products.price, Products.stock, Products.date_added, Products.date_modified, Providers.name AS provider_name 
            FROM Products 
            JOIN Providers ON Products.provider_id = Providers.id 
            WHERE Products.is_deleted = TRUE";
    $result = $conn->query($sql);
    $reportData = '<h2>Deleted Products Report</h2><table class="table"><tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Date Added</th><th>Date Modified</th><th>Provider</th></tr>';
    while ($row = $result->fetch_assoc()) {
        $reportData .= "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['price']} MAD</td><td>{$row['stock']}</td><td>{$row['date_added']}</td><td>{$row['date_modified']}</td><td>{$row['provider_name']}</td></tr>";
    }
    $reportData .= '</table>';
} elseif ($reportType == 'users') {
    $sql = "SELECT id, name, email FROM Users"; // Exclude password hash
    $result = $conn->query($sql);
    $reportData = '<h2>Users Report</h2><table class="table"><tr><th>ID</th><th>Name</th><th>Email</th></tr>';
    while ($row = $result->fetch_assoc()) {
        $reportData .= "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['email']}</td></tr>";
    }
    $reportData .= '</table>';
} elseif ($reportType == 'providers') {
    $sql = "SELECT * FROM Providers";
    $result = $conn->query($sql);
    $reportData = '<h2>Providers Report</h2><table class="table"><tr><th>ID</th><th>Name</th><th>Contact Info</th><th>Address</th></tr>';
    while ($row = $result->fetch_assoc()) {
        $reportData .= "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['contact_info']}</td><td>{$row['address']}</td></tr>";
    }
    $reportData .= '</table>';
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reports</title>
    <link rel="shortcut icon" href="design/images/favicon/favicon-16x16.png" type="image/x-icon">
    <link href="design/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="design/css/products.css" rel="stylesheet">
    <link rel="stylesheet" href="design/css/layout_partial.css">
    <style>
        .report-button {
            margin: 5px;
        }

        /* Print styles */
        @media print {

            /* Hide all non-report content */
            body * {
                visibility: hidden;
            }

            /* Only show the report content */
            .printable-content,
            .printable-content * {
                visibility: visible;
            }

            /* Ensure the report is printed from the top */
            .printable-content {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }

            /* Hide the buttons */
            .report-button,
            .print-button {
                display: none;
            }
        }
    </style>
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
            <h1 class="h2">Reports</h1>
            <form method="POST">
                <button name="report_type" value="products" class="btn btn-primary report-button">Products Report</button>
                <button name="report_type" value="deleted_products" class="btn btn-primary report-button">Deleted Products Report</button>
                <button name="report_type" value="users" class="btn btn-primary report-button">Users Report</button>
                <button name="report_type" value="providers" class="btn btn-primary report-button">Providers Report</button>
            </form>

            <?php if ($reportData): ?>
                <div class="mt-4">
                    <button onclick="window.print()" class="btn btn-secondary print-button">Print Report</button>
                    <div class="mt-2 printable-content"> <!-- Add this class for print visibility -->
                        <?php echo $reportData; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <script src="design/js/bootstrap.bundle.min.js"></script>
</body>

</html>
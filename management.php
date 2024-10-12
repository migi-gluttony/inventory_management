<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="design/images/favicon/favicon-16x16.png" type="image/x-icon">
    <title>Admin Selection</title>
    <link href="design/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .centered {
            height: 100vh; /* Full viewport height */
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
        }
    </style>
</head>
<body>
    <div class="container centered">
        <div class="text-center">
            <h1 class="my-4">Admin Management</h1>
            <p>Select an option below:</p>
            <a href="user_management.php" class="btn btn-primary btn-lg mx-2">Manage Users</a>
            <a href="provider_management.php" class="btn btn-secondary btn-lg mx-2">Manage Providers</a>
        </div>
    </div>
    <script src="design/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: dashboard.php");
    exit();
}

// Database connection
$host = 'localhost';
$db = 'stock_management';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == 'addProvider') {
        $name = $_POST['name'];
        $contact_info = $_POST['contact_info'];
        $address = $_POST['address'];

        $stmt = $conn->prepare("INSERT INTO Providers (name, contact_info, address) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $contact_info, $address);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Provider added successfully.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error adding provider: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } elseif ($action == 'editProvider') {
        $provider_id = $_POST['provider_id'];
        $name = $_POST['name'];
        $contact_info = $_POST['contact_info'];
        $address = $_POST['address'];

        $stmt = $conn->prepare("UPDATE Providers SET name = ?, contact_info = ?, address = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $contact_info, $address, $provider_id);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Provider updated successfully.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error updating provider: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } elseif ($action == 'removeProvider') {
        $provider_id = $_POST['provider_id'];
    
        // Check if the provider has associated products
        $checkProductsStmt = $conn->prepare("SELECT id FROM products WHERE provider_id = ?");
        $checkProductsStmt->bind_param("i", $provider_id);
        $checkProductsStmt->execute();
        $checkProductsResult = $checkProductsStmt->get_result();
    
        if ($checkProductsResult->num_rows > 0) {
            echo "<div class='alert alert-danger'>This provider cannot be deleted because they have associated products.</div>";
        } else {
            // Proceed to delete the provider if no products are found
            $stmt = $conn->prepare("DELETE FROM Providers WHERE id = ?");
            $stmt->bind_param("i", $provider_id);
    
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Provider removed successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error removing provider: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }
    
        $checkProductsStmt->close();
    }
    
}

function listProviders($conn)
{
    $result = $conn->query("SELECT id, name, contact_info, address FROM Providers");
    if ($result->num_rows > 0) {
        echo "<table class='table'>";
        echo "<tr><th>Name</th><th>Contact Info</th><th>Address</th><th>Actions</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['contact_info'] . "</td>";
            echo "<td>" . $row['address'] . "</td>";
            echo "<td>";
            echo "<button class='btn btn-primary' data-id='" . $row['id'] . "' data-name='" . $row['name'] . "' data-contact_info='" . $row['contact_info'] . "' data-address='" . $row['address'] . "' data-bs-toggle='modal' data-bs-target='#editProviderModal'>Edit</button> ";
            echo "<form method='POST' action='' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to remove this provider?\");'>
            <input type='hidden' name='action' value='removeProvider'>
            <input type='hidden' name='provider_id' value='" . $row['id'] . "'>
            <button type='submit' class='btn btn-danger'>Remove</button>
            </form>";
            echo "</td>";
            echo "</tr>";
            
        }
        echo "</table>";
        echo "    <br>";
        echo "    <br>";
        echo "    <br>";
        echo "    <br>";
        echo '<a href="signout.php" class="btn btn-primary" style="float: right;">Sign Out</a>';
        echo "    <br>";
        echo "    <br>";
        echo "    <br>";
        echo "    <br>";
    } else {
        echo "<div class='alert alert-info'>No providers found.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Management</title>
    <link rel="shortcut icon" href="design/images/favicon/favicon-16x16.png" type="image/x-icon">
    <link href="design/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Provider Management</h1>

        <h2>Add Provider</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="addProvider">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="contact_info" class="form-label">Contact Info:</label>
                <input type="text" class="form-control" id="contact_info" name="contact_info" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address:</label>
                <textarea class="form-control" id="address" name="address" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Provider</button>
        </form>

        <?php listProviders($conn); ?>
    </div>

    <!-- Edit Provider Modal -->
    <div class="modal fade" id="editProviderModal" tabindex="-1" aria-labelledby="editProviderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProviderModalLabel">Edit Provider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="editProvider">
                        <input type="hidden" name="provider_id" id="provider_id">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Name:</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_contact_info" class="form-label">Contact Info:</label>
                            <input type="text" class="form-control" id="edit_contact_info" name="contact_info" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_address" class="form-label">Address:</label>
                            <textarea class="form-control" id="edit_address" name="address" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="design/js/bootstrap.bundle.min.js"></script>
    <script>
        const editProviderModal = document.getElementById('editProviderModal');
        editProviderModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            const providerId = button.getAttribute('data-id');
            const providerName = button.getAttribute('data-name');
            const providerContactInfo = button.getAttribute('data-contact_info');
            const providerAddress = button.getAttribute('data-address');

            const modalTitle = editProviderModal.querySelector('.modal-title');
            const inputId = editProviderModal.querySelector('#provider_id');
            const inputName = editProviderModal.querySelector('#edit_name');
            const inputContactInfo = editProviderModal.querySelector('#edit_contact_info');
            const inputAddress = editProviderModal.querySelector('#edit_address');

            modalTitle.textContent = 'Edit Provider: ' + providerName;
            inputId.value = providerId;
            inputName.value = providerName;
            inputContactInfo.value = providerContactInfo;
            inputAddress.value = providerAddress;
        });
    </script>
</body>
</html>

<?php
// Include the database connection file
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: dashboard.php");
    exit();
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
            $success_message = "Provider added successfully.";
        } else {
            $error_message = "Error adding provider: " . $stmt->error;
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
            $success_message = "Provider updated successfully.";
        } else {
            $error_message = "Error updating provider: " . $stmt->error;
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
            $error_message = "This provider cannot be deleted because they have associated products.";
        } else {
            // Proceed to delete the provider if no products are found
            $stmt = $conn->prepare("DELETE FROM Providers WHERE id = ?");
            $stmt->bind_param("i", $provider_id);
    
            if ($stmt->execute()) {
                $success_message = "Provider removed successfully.";
            } else {
                $error_message = "Error removing provider: " . $stmt->error;
            }
            $stmt->close();
        }
    
        $checkProductsStmt->close();
    }
}

// Fetch providers to display
function listProviders($conn)
{
    $result = $conn->query("SELECT id, name, contact_info, address FROM Providers");
    return $result;
}

$providers = listProviders($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Management</title>
    <link rel="shortcut icon" href="design/images/favicon/favicon-16x16.png" type="image/x-icon">
    <link href="design/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --text-color: #5a5c69;
            --success-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --info-color: #36b9cc;
        }
        
        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background-color: #f8f9fc;
            color: var(--text-color);
        }
        
        .page-header {
            padding: 1.5rem 0;
            margin-bottom: 1.5rem;
            background-color: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.10);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 700;
            color: var(--success-color);
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .table th {
            background-color: #f8f9fc;
            color: var(--success-color);
            font-weight: 600;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }
        
        .provider-table {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.05);
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 2rem;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .form-label {
            font-weight: 600;
        }
        
        .btn-circle {
            border-radius: 100%;
            height: 2.5rem;
            width: 2.5rem;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .back-button {
            margin-bottom: 1rem;
        }
        
        .address-col {
            max-width: 250px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="management.php">
                <img src="design/images/favicon/favicon-32x32.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top me-2">
                Inventory Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="user_management.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="provider_management.php">Providers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="management.php">Admin Panel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="signout.php">Sign Out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Provider Management</h1>
            <a href="management.php" class="btn btn-sm btn-success shadow-sm back-button">
                <i class="bi bi-arrow-left"></i> Back to Admin Panel
            </a>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold">Add New Provider</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="addProvider">
                            <div class="mb-3">
                                <label for="name" class="form-label">Provider Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="contact_info" class="form-label">Contact Information</label>
                                <input type="text" class="form-control" id="contact_info" name="contact_info" required>
                                <small class="text-muted">Phone number, email, etc.</small>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Add Provider</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold">Provider List</h6>
                    </div>
                    <div class="card-body">
                        <?php if ($providers->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped provider-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Contact Info</th>
                                            <th>Address</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $providers->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['contact_info']); ?></td>
                                                <td class="address-col"><?php echo htmlspecialchars($row['address']); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-success" 
                                                            data-id="<?php echo $row['id']; ?>" 
                                                            data-name="<?php echo htmlspecialchars($row['name']); ?>" 
                                                            data-contact_info="<?php echo htmlspecialchars($row['contact_info']); ?>" 
                                                            data-address="<?php echo htmlspecialchars($row['address']); ?>" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editProviderModal">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Are you sure you want to remove this provider?');">
                                                        <input type="hidden" name="action" value="removeProvider">
                                                        <input type="hidden" name="provider_id" value="<?php echo $row['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">No providers found.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
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
                            <label for="edit_name" class="form-label">Provider Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_contact_info" class="form-label">Contact Information</label>
                            <input type="text" class="form-control" id="edit_contact_info" name="contact_info" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_address" class="form-label">Address</label>
                            <textarea class="form-control" id="edit_address" name="address" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
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
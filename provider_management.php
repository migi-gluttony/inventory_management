<?php
// Include the database connection file
$current_page = 'provider_management'; // Add this for sidebar highlighting
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
    <link href="design/css/products.css" rel="stylesheet">
    <link rel="stylesheet" href="design/css/layout_partial.css">
    <style>
        .address-col {
            max-width: 250px;
        }
    </style>
</head>

<body>
    <?php include 'layout_partial.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="container mt-5">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Provider Management</h1>
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
                            <h6 class="m-0 fw-bold">Add New Provider</h6>
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
                            <h6 class="m-0 fw-bold">Provider List</h6>
                        </div>
                        <div class="card-body">
                            <?php if ($providers->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
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
    </main>

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
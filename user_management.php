<?php
// Include the database connection file
include 'db_connection.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    // Redirect non-admin users or guests to the dashboard
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == 'addUser') {
        // Add user logic
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $is_admin = isset($_POST['is_admin']) ? 1 : 0;

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO Users (name, email, password_hash, is_admin) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $email, $password_hash, $is_admin);

        if ($stmt->execute()) {
            $success_message = "User added successfully.";
        } else {
            $error_message = "Error adding user: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($action == 'editUser') {
        // Edit user logic
        $user_id = $_POST['user_id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $old_password = $_POST['old_password'];
        $is_admin = isset($_POST['is_admin']) ? 1 : 0;

        // Verify old password
        $stmt = $conn->prepare("SELECT password_hash FROM Users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($password_hash);
        $stmt->fetch();
        $stmt->close();

        if (password_verify($old_password, $password_hash)) {
            $new_password = $_POST['password'];
            if (!empty($new_password)) {
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE Users SET name = ?, email = ?, password_hash = ?, is_admin = ? WHERE id = ?");
                $stmt->bind_param("sssii", $name, $email, $new_password_hash, $is_admin, $user_id);
            } else {
                $stmt = $conn->prepare("UPDATE Users SET name = ?, email = ?, is_admin = ? WHERE id = ?");
                $stmt->bind_param("ssii", $name, $email, $is_admin, $user_id);
            }

            if ($stmt->execute()) {
                $success_message = "User updated successfully.";
            } else {
                $error_message = "Error updating user.";
            }
            $stmt->close();
        } else {
            $error_message = "Old password is incorrect.";
        }
    } elseif ($action == 'removeUser') {
        // Remove user logic
        $email = $_POST['email'];

        $stmt = $conn->prepare("DELETE FROM Users WHERE email = ?");
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $success_message = "User removed successfully.";
            } else {
                $error_message = "No user found with that email.";
            }
        } else {
            $error_message = "Error removing user: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch users to display
function listUsers($conn)
{
    $result = $conn->query("SELECT id, name, email, is_admin FROM Users");
    return $result;
}

$users = listUsers($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
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
            color: var(--primary-color);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .table th {
            background-color: #f8f9fc;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }
        
        .user-table {
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
        
        .user-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-admin {
            background-color: var(--primary-color);
            color: white;
        }
        
        .badge-user {
            background-color: var(--info-color);
            color: white;
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
                        <a class="nav-link active" href="user_management.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="provider_management.php">Providers</a>
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
            <h1 class="h3 mb-0 text-gray-800">User Management</h1>
            <a href="management.php" class="btn btn-sm btn-primary shadow-sm back-button">
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
                        <h6 class="m-0 font-weight-bold">Add New User</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="addUser">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin">
                                <label class="form-check-label" for="is_admin">Admin Privileges</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add User</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold">User List</h6>
                    </div>
                    <div class="card-body">
                        <?php if ($users->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped user-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $users->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $row['name']; ?></td>
                                                <td><?php echo $row['email']; ?></td>
                                                <td>
                                                    <?php if ($row['is_admin']): ?>
                                                        <span class="user-badge badge-admin">Admin</span>
                                                    <?php else: ?>
                                                        <span class="user-badge badge-user">User</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" data-id="<?php echo $row['id']; ?>" data-name="<?php echo $row['name']; ?>" data-email="<?php echo $row['email']; ?>" data-bs-toggle="modal" data-bs-target="#editUserModal">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Are you sure you want to remove this user?');">
                                                        <input type="hidden" name="action" value="removeUser">
                                                        <input type="hidden" name="email" value="<?php echo $row['email']; ?>">
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
                            <div class="alert alert-info">No users found.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="editUser">
                        <input type="hidden" name="user_id" id="user_id">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="old_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="old_password" name="old_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_password" class="form-label">New Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_admin_edit" name="is_admin">
                            <label class="form-check-label" for="is_admin_edit">Admin Privileges</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="design/js/bootstrap.bundle.min.js"></script>
    <script>
        const editUserModal = document.getElementById('editUserModal');
        editUserModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-id');
            const userName = button.getAttribute('data-name');
            const userEmail = button.getAttribute('data-email');

            const modalTitle = editUserModal.querySelector('.modal-title');
            const userIdInput = editUserModal.querySelector('#user_id');
            const userNameInput = editUserModal.querySelector('#edit_name');
            const userEmailInput = editUserModal.querySelector('#edit_email');

            modalTitle.textContent = `Edit User: ${userName}`;
            userIdInput.value = userId;
            userNameInput.value = userName;
            userEmailInput.value = userEmail;
        });
    </script>
</body>

</html>
<?php
// Include the database connection file
$current_page = 'user_management'; // Add this for sidebar highlighting
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
        // Edit user logic - removed old password requirement
        $user_id = $_POST['user_id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $is_admin = isset($_POST['is_admin']) ? 1 : 0;

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
    <link href="design/css/products.css" rel="stylesheet">
    <link rel="stylesheet" href="design/css/layout_partial.css">
    <style>
        .user-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-admin {
            background-color: #4e73df;
            color: white;
        }

        .badge-user {
            background-color: #36b9cc;
            color: white;
        }
    </style>
</head>

<body>
    <?php include 'layout_partial.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="container mt-5">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">User Management</h1>
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
                            <h6 class="m-0 fw-bold">Add New User</h6>
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
                            <h6 class="m-0 fw-bold">User List</h6>
                        </div>
                        <div class="card-body">
                            <?php if ($users->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
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
                                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                                    <td>
                                                        <?php if ($row['is_admin']): ?>
                                                            <span class="user-badge badge-admin">Admin</span>
                                                        <?php else: ?>
                                                            <span class="user-badge badge-user">User</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary"
                                                            data-id="<?php echo $row['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                                            data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                                            data-is-admin="<?php echo $row['is_admin']; ?>"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editUserModal">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Are you sure you want to remove this user?');">
                                                            <input type="hidden" name="action" value="removeUser">
                                                            <input type="hidden" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
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
    </main>

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

    <script src="design/js/bootstrap.bundle.min.js"></script>
    <script>
        const editUserModal = document.getElementById('editUserModal');
        editUserModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-id');
            const userName = button.getAttribute('data-name');
            const userEmail = button.getAttribute('data-email');
            const isAdmin = button.getAttribute('data-is-admin');

            const modalTitle = editUserModal.querySelector('.modal-title');
            const userIdInput = editUserModal.querySelector('#user_id');
            const userNameInput = editUserModal.querySelector('#edit_name');
            const userEmailInput = editUserModal.querySelector('#edit_email');
            const isAdminCheckbox = editUserModal.querySelector('#is_admin_edit');

            modalTitle.textContent = `Edit User: ${userName}`;
            userIdInput.value = userId;
            userNameInput.value = userName;
            userEmailInput.value = userEmail;
            isAdminCheckbox.checked = isAdmin === "1";
        });
    </script>
</body>

</html>
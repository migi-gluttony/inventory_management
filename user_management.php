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
            echo "<div class='alert alert-success'>User added successfully.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error adding user: " . $stmt->error . "</div>";
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
                echo "<div class='alert alert-success'>User updated successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error updating user.</div>";
            }
            $stmt->close();
        } else {
            echo "<div class='alert alert-danger'>Old password is incorrect.</div>";
        }
    } elseif ($action == 'removeUser') {
        // Remove user logic
        $email = $_POST['email'];

        $stmt = $conn->prepare("DELETE FROM Users WHERE email = ?");
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "<div class='alert alert-success'>User removed successfully.</div>";
            } else {
                echo "<div class='alert alert-warning'>No user found with that email.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Error removing user: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

// Fetch users to display
function listUsers($conn)
{
    $result = $conn->query("SELECT id, name, email, is_admin FROM Users");
    if ($result->num_rows > 0) {
        echo "    <br>";
        echo "    <br>";
        echo "    <br>";
        echo "    <br>";
        echo "<h2>User List</h2>";
        echo "    <br>";
        echo "<table class='table'>";
        echo "<tr><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . ($row['is_admin'] ? 'Admin' : 'User') . "</td>";
            echo "<td>";
            // Edit button
            echo "<button class='btn btn-primary' data-id='" . $row['id'] . "' data-name='" . $row['name'] . "' data-email='" . $row['email'] . "' data-bs-toggle='modal' data-bs-target='#editUserModal'>Edit</button> ";
            // Remove button with a form
            echo "<form method='POST' action='' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to remove this user?\");'>
        <input type='hidden' name='action' value='removeUser'>
        <input type='hidden' name='email' value='" . $row['email'] . "'>
        <button type='submit' class='btn btn-danger'>Remove</button>
        </form>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='alert alert-info'>No users found.</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <!-- Bootstrap CSS -->
    <link rel="shortcut icon" href="design/images/favicon/favicon-16x16.png" type="image/x-icon">

    <link href="design/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="my-4">User Management</h1>
        <h2>Add User</h2>
        <br>
        <form method="POST" action="">
            <input type="hidden" name="action" value="addUser">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin">
                <label class="form-check-label" for="is_admin">Is Admin</label>
            </div>
            <button type="submit" class="btn btn-primary">Add User</button>
        </form>

        <?php listUsers($conn); ?>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>

        <a href="signout.php" class="btn btn btn-primary" style="float: right;">sign out</a>
        <br>
        <br>
        <br>
        <br>

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
                            <label for="edit_name" class="form-label">Name:</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email:</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="old_password" class="form-label">Old Password:</label>
                            <input type="password" class="form-control" id="old_password" name="old_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_password" class="form-label">New Password (Leave blank to keep current):</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                        </div>
                        <div class="mb-3">
                            <label for="is_admin_edit" class="form-label">Is Admin:</label>
                            <input type="checkbox" class="form-check-input" id="is_admin_edit" name="is_admin">
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
<?php
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection

$servername = "localhost";
$username = "root";  // default XAMPP username
$password = "";      // default XAMPP password (empty)
$dbname = "stock_management";  // your database name

// Create

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT id, name, password_hash, is_admin FROM Users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password_hash'])) {
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['user_name'] = $user['name'];
          $_SESSION['is_admin'] = $user['is_admin'];
          
          // Redirect based on user role
          if ($user['is_admin']) {
              header("Location: user_management.php");  // Admin user goes to user management
          } else {
              header("Location: dashboard.php");  // Normal user goes to dashboard
          }
          exit();
      } else {
          $error_message = "Invalid email or password.";
      }
      
    } else {
        $error_message = "Invalid email or password.";
    }
    
    $stmt->close();
    $conn->close();
}
?>
<!doctype html>
<html lang="en" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In</title>
    <link rel="shortcut icon" href="design/images/favicon/favicon-16x16.png" type="image/x-icon">
    <link href="design/css/bootstrap.min.css" rel="stylesheet">
    <link href="design/css/sign-in.css" rel="stylesheet">
    <style>
    .bgimg {
  position: relative;
  background-position: center 65%;
  background-size: cover;
  background-image: url("design/images/AdobeStock_481003062.jpg");
  min-height: 75%;
}

.bgimg::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.7); /* Dark overlay */
  z-index: -1; /* Place behind the text */
}
.form-signin {
            background: rgba(255, 255, 255, 0.6); /* Light background for the form */
            border-radius: 10px; /* Rounded corners */
            padding: 2rem; /* Padding around the form */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Soft shadow */
            position: relative; /* To sit above the overlay */
            max-width: 450px; /* Optional: Limit max width, adjust as needed */
            width: 100%; /* Allow form to be responsive */
        }
</style>
</head>
<body class="d-flex align-items-center py-4 bg-body-tertiary bgimg">
    <main class="form-signin w-100 m-auto">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <img class="mb-4" src="design/images/favicon/android-chrome-512x512.png" alt="" width="72" height="72">
            <h1 class="h3 mb-3 fw-normal">Please sign in</h1>
            
            <?php
            if (!empty($error_message)) {
                echo '<div class="alert alert-danger">' . $error_message . '</div>';
            }
            ?>

            <div class="form-floating">
                <input type="email" class="form-control" id="floatingInput" name="email" placeholder="name@example.com" required>
                <label for="floatingInput">Email address</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password" required>
                <label for="floatingPassword">Password</label>
            </div>

            <button class="btn btn-primary w-100 py-2" type="submit">Sign in</button>
            <p class="mt-5 mb-3 text-body-secondary">&copy; 2024–2024</p>
        </form>
    </main>
</body>
</html>
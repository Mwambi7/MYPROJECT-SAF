<?php
session_start();
require 'db_connect.php'; // Include the database connection file

// Initialize variables
$username = '';
$password = '';
$error_message = '';

// Create a database connection
$conn = getDbConnection(); // Get the connection from db_connect.php

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize user input
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Prepare SQL query to find the user
    $query = "SELECT user_id, password_hash, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch user data
    if ($user = $result->fetch_assoc()) {
        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on user role
            if ($user['role'] === 'admin') {
                header('Location: admin_dashboard.php');
            } else {
                header('Location: dashboard.php');
            }
            exit();
        } else {
            $error_message = 'Invalid credentials';
        }
    } else {
        $error_message = 'Invalid credentials';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="Styles/login.css"> <!-- Link to your CSS file -->
    <header id="main-header">
        <div class="container">
            <h1 id="site-title">Welcome back to Safiri</h1>
            <nav id="main-nav">
                <ul>
                    <li><a href="index.php" id="home-link">Home</a></li>
                    <li><a href="tour_destination.php" id="tour-link">Tour Destinations</a></li>
                    <li><a href="book_tour.php" id="booking-link">Book a Tour</a></li>
                    
                        <li><a href="login.php" id="login-link">Login</a></li>
                        <li><a href="register.php" id="register-link">Register</a></li>
                        <li><a href="contact_us.php" id="contact_us-link">Contact Us</a></li>
                  
                </ul>
            </nav>
        </div>
    </header>
</head>
<body>

    
    <main id="main-content">
    <section id="hero-section">
    <div id="login-container">
        <h1 class="login-title">Login</h1>
        <form action="login.php" method="post" id="login-form">
            <div class="form-group">
                <label for="username" class="form-label">Username:</label>
                <input type="text" id="username" name="username" class="form-input" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>
            
            <div class="form-group">
                <input type="submit" value="Login" id="login-button" class="form-button">
            </div>

            <p class="register-link">New here? <a href="register.php">Register here</a>.</p>
        </form>

        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
    </div>
    </section>
    </main>

<footer id="main-footer">
    <div class="container">
        <p>&copy; 2024 Safiri. All rights reserved.</p>
        <p><a href="contact_us.php" id="contact-link">Contact Us</a></p>
    </div>
</footer>
</body>
</html>

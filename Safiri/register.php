<?php
session_start();
require 'db_connect.php'; // Ensure you have a working database connection

// Initialize variables
$username = '';
$email = '';
$password = '';
$confirm_password = '';
$error_message = '';
$success_message = '';

// Create a database connection
$conn = getDbConnection(); // Get the connection from db_connect.php

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize user input
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = 'All fields are required';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match';
    } else {
        // Check if username or email already exists
        $query = "SELECT user_id FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = 'Username or email already exists';
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insert new user into the database
            $query = "INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'user')";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('sss', $username, $email, $hashed_password);
            $stmt->execute();

            $success_message = 'Registration successful. Please log in.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="Styles/login.css"> <!-- Link to your CSS file -->
    <header id="main-header">
        <div class="container">
            <h1 id="site-title">Sign up!</h1>
            <nav id="main-nav">
                <ul>
                    <li><a href="index.php" id="home-link">Home</a></li>
                    <li><a href="tour_destination.php" id="tour-link">Tour Destinations</a></li>
                    <li><a href="book_tour.php" id="booking-link">Book a Tour</a></li>
                    
                        <li><a href="login.php" id="login-link">Login</a></li>
                        <li><a href="register.php" id="register-link">Register</a></li>
                        
                  
                </ul>
            </nav>
        </div>
    </header>
</head>
<body>
    
<main id="main-content">
    <section id="hero-section">
    
    <div id="register-container">
        <h1 class="register-title">Register</h1>
        <form action="register.php" method="post" id="register-form">
            <div class="form-group">
                <label for="username" class="form-label">Username:</label>
                <input type="text" id="username" name="username" class="form-input" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
            </div>
            
            <div class="form-group">
                <input type="submit" value="Register" id="register-button" class="form-button">
            </div>

            <p class="login-link">Already have an account? <a href="login.php">Login here</a>.</p>
        </form>

        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>
    </div>
    </div>
    </section>
    </main>

<footer id="main-footer">
    <div class="container">
        <p>&copy; 2024 Safiri. All rights reserved.</p>
      
    </div>
</footer>

</body>
</html>

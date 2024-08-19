<?php
session_start();
require 'db_connect.php'; // Include the database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Initialize variables

$username = '';
$email = '';
$error_message = '';
$success_message = '';

// Create a database connection
$conn = getDbConnection(); // Get the connection from db_connect.php

// Fetch current user details
$query = "SELECT username, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
      
        $username = $user['username'];
        $email = $user['email'];
    }
} else {
    $error_message = 'Failed to prepare SQL statement';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
   
    $email = $_POST['email'] ?? '';

    if (empty($username) || empty($email)) {
        $error_message = 'All fields are required';
    } else {
        $query = "UPDATE users SET username = ?, email = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('ssi', $username, $email, $user_id);
            if ($stmt->execute()) {
                $success_message = 'Profile updated successfully';
            } else {
                $error_message = 'Failed to update profile';
            }
        } else {
            $error_message = 'Failed to prepare SQL statement';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <header>
    <nav id="main-nav">
          <ul>
          <li><a href="dashboard.php">Dashboard</a></li>
          <li><a href="payment.php">Payment</a></li>
            <li><a href="profile.php">Edit Profile</a></li>
            <li><a href="contact_us.php">Contact Us</a></li>
            <li><a href="book_tour.php">Book a New Tour</a></li>
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </nav>
    </header>
    <style>
      /* General Page Styles */
body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

/* Header and Navigation Styles */
header {
    background-color: #14332C; /* Match your theme color */
    color: #fff;
    padding: 10px 0;
}

#main-nav {
    display: flex;
    justify-content: center;
}

#main-nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
}

#main-nav li {
    margin: 0 15px;
}

#main-nav a {
    color: #fff;
    text-decoration: none;
    font-size: 16px;
}

#main-nav a:hover {
    text-decoration: underline;
}

/* Page Content Styles */
h1 {
    text-align: center;
    color: #333;
    margin: 20px 0;
}

/* Form Styles */
form {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

form label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

form input[type="text"],
form input[type="email"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    margin-bottom: 15px;
}

form input[type="submit"] {
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    background-color: #28a745;
    color: white;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

form input[type="submit"]:hover {
    background-color: #218838;
}

/* Alert Styles */
.alert-success {
    color: #155724;
    background-color: #d4edda;
    padding: 10px;
    margin: 15px 0;
    border-radius: 4px;
}

.alert-error {
    color: #721c24;
    background-color: #f8d7da;
    padding: 10px;
    margin: 15px 0;
    border-radius: 4px;
}

/* Link Styles */
a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>
    <h1>Edit Profile</h1>
    <?php if (!empty($success_message)): ?>
        <p style="color: green;"><?php echo htmlspecialchars($success_message); ?></p>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>
    <form action="profile.php" method="post">
        <label for="first_name">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required><br><br>
       
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br><br>
        <input type="submit" value="Update Profile">
    </form>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>

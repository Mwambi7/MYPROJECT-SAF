
<?php
session_start();
// Ensure only admins can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}
require 'db_connect.php'; // Include the database connection file

$error_message = '';
$success_message = '';

// Create a database connection
$conn = getDbConnection(); // Get the connection from db_connect.php

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize user input
    $destination_name = $_POST['destination_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $currency = 'KES';
    $availability_status = $_POST['availability_status'] ?? '';

    // Handle file upload
    $image = $_FILES['image'] ?? null;
    $image_name = $image['name'] ?? '';
    $image_tmp = $image['tmp_name'] ?? '';
    $image_error = $image['error'] ?? UPLOAD_ERR_NO_FILE;

    // Validate file upload
    if ($image_error === UPLOAD_ERR_OK) {
        $image_url = 'images/' . basename($image_name);

        // Check if the 'images' directory exists
        if (!is_dir('images')) {
            mkdir('images', 0777, true); // Create the directory with full permissions if it doesn't exist
        }

        if (!move_uploaded_file($image_tmp, $image_url)) {
            $error_message = 'Failed to upload image';
        }
    } else {
        $error_message = 'No image uploaded or there was an error uploading the image';
    }

    // Insert new destination into the database
    if (empty($error_message)) {
        $query = "INSERT INTO tour_destinations (destination_name, description, image_url, price, availability_status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssss', $destination_name, $description, $image_url, $price, $availability_status);
        if ($stmt->execute()) {
            $success_message = 'Destination added successfully!';
        } else {
            $error_message = 'Failed to add destination';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Tour Destination</title>
    <link rel="stylesheet" href="Styles/destinations.css"> <!-- Link to your CSS file -->
    

    <nav id="main-nav">
        <header>
            <nav>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="view_bookings.php">View Bookings</a></li>
                <li><a href="add_destination.php">Manage Destinations</a></li>
                <li><a href="admin_logs.php">Admin Logs</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>  
</header>

<style> 
     header{ 
    background-color: #14332C ;
    padding: 10px 0;
    border-bottom: 1px solid #ddd;
    display: flex;
    flex-direction: column;
    align-items: center;
}  </style>
</head>
<body>
    <div class="container">
        <h1 class="title">Add Tour Destination</h1>
        <form action="add_destination.php" method="post" enctype="multipart/form-data" class="destination-form">
            <div class="form-group">
                <label for="destination_name" class="form-label">Destination Name:</label>
                <input type="text" id="destination_name" name="destination_name" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description:</label>
                <textarea id="description" name="description" class="form-textarea" required></textarea>
            </div>

            <div class="form-group">
                <label for="image" class="form-label">Image:</label>
                <input type="file" id="image" name="image" class="form-input-file" required>
            </div>

            <div class="form-group">
                <label for="price" class="form-label">Price (KES):</label>
                <input type="text" id="price" name="price" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="availability_status" class="form-label">Availability:</label>
                <select id="availability_status" name="availability_status" class="form-select">
                    <option value="available">Available</option>
                    <option value="unavailable">Unavailable</option>
                </select>
            </div>

            <div class="form-group">
                <input type="submit" value="Add Destination" class="submit-button">
            </div>
        </form>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <p><?php echo htmlspecialchars($error_message); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <p><?php echo htmlspecialchars($success_message); ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

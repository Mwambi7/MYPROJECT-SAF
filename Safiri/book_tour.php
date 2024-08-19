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
$destinations = [];
$error_message = '';
$success_message = '';

// Create a database connection
$conn = getDbConnection(); // Get the connection from db_connect.php

// Fetch available tour destinations
$query = "SELECT destination_id, destination_name FROM tour_destinations WHERE availability_status = 'available'";
$result = $conn->query($query);

if ($result) {
    $destinations = $result->fetch_all(MYSQLI_ASSOC);
}

// Handle form submission
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destination_id = $_POST['destination_id'] ?? '';
    $booking_date = $_POST['booking_date'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';

    if (empty($destination_id) || empty($booking_date) || empty($phone_number))  {
        $error_message = 'All fields are required';
    } else {
        // Fetch the price of the selected destination
        $query = "SELECT price FROM tour_destinations WHERE destination_id = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('i', $destination_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $destination = $result->fetch_assoc();
                $price = $destination['price']; // Get the price of the selected destination

                // Check if price was fetched correctly
                if (!empty($price)) {
                    // Insert the booking with the price
                    $query = "INSERT INTO bookings (user_id, destination_id, booking_date, phone_number, price) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    if ($stmt) {
                        $stmt->bind_param('iissd', $user_id, $destination_id, $booking_date, $phone_number, $price);
                        if ($stmt->execute()) {
                            $success_message = 'Booking successful';

                            // Redirect to the dashboard page after successful booking
                            header('Location: dashboard.php');
                            exit();
                        } else {
                            $error_message = 'Failed to book the tour';
                        }
                    } else {
                        $error_message = 'Failed to prepare SQL statement';
                    }
                } else {
                    $error_message = 'Failed to fetch price for the selected destination';
                }
            } else {
                $error_message = 'Destination not found';
            }
        } else {
            $error_message = 'Failed to fetch destination price';
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Tour</title>
    <link rel="stylesheet" href="Styles/bookings.css"> <!-- Link to your CSS file -->
    <header id="main-header">
        <div class="container1">
            <h1 id="site-title">Welcome to Safiri</h1>
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
    <div class="container">
        <h1 class="title">Book a Tour</h1>
        
        <?php if (!empty($error_message)) : ?>
            <div class="alert alert-error" id="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)) : ?>
            <div class="alert alert-success" id="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="form" id="booking-form">
            <div class="form-group">
                <label for="destination_id" class="form-label">Select Destination:</label>
                <select name="destination_id" id="destination_id" class="form-select" required>
                    <option value="">--Select Destination--</option>
                    <?php foreach ($destinations as $destination) : ?>
                        <option value="<?php echo $destination['destination_id']; ?>">
                            <?php echo $destination['destination_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="booking_date" class="form-label">Select Date:</label>
                <input type="date" name="booking_date" id="booking_date" class="form-input" required>
            </div>
            <div class="form-group">
                <label for="phone_number" class="form-label">Phone Number:</label>
                <input type="text" name="phone_number" id="phone_number" class="form-input" required placeholder="Enter your phone number">
            </div>
            <button type="submit" class="submit-button">Book Now</button>
        </form>
    </div>
    </section>
    </main>

    <footer id="main-footer">
        <div class="container1">
            <p>&copy; 2024 Safiri. All rights reserved.</p>
            <p><a href="contact_us.php" id="contact-link">Contact Us</a></p>
        </div>
    </footer>
</body>
</html>

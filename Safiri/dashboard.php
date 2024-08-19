<?php
session_start();
require 'db_connect.php'; // Include the database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Create a database connection
$conn = getDbConnection(); // Get the connection from db_connect.php

// Fetch user information
$query = "SELECT username, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch user bookings
$query = "SELECT b.booking_id, d.destination_name, b.booking_date, b.status, b.price 
          FROM bookings b
          JOIN tour_destinations d ON b.destination_id = d.destination_id
          WHERE b.user_id = ?";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$bookings_result = $stmt->get_result();

if ($bookings_result === false) {
    die('Query failed: ' . htmlspecialchars($stmt->error));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="Styles/dashboa.css"> <!-- Link to CSS file -->

    <style>
    /* General Container Styles */
.dashboard-container {
    padding: 20px;
    max-width: 1200px;
    margin: auto;
}

/* Header Styles */
header {
    background-color: #14332C ;
    padding: 10px 0;
    border-bottom: 1px solid #ddd;
    display: flex;
    flex-direction: column;
    align-items: center;
}

#welcome-message {
    margin-bottom: 10px;
    text-align: center;
}

#welcome-message h1 {
    margin: 0;
    color: #14332C; /* Primary theme color */
}

/* Navigation Styles */
#main-nav {
    margin-bottom: 20px;
}

#main-nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    justify-content: center;
}

#main-nav li {
    margin: 0 15px;
}

#main-nav a {
    text-decoration: none;
    color: #fff; /* Primary theme color */
    font-weight: bold;
}

#main-nav a:hover {
    text-decoration: underline;
}

/* Section Styling */
.dashboard-container section {
    margin-bottom: 20px;
}

/* Table Styling */
.dashboard-container table {
    width: 100%;
    border-collapse: collapse;
}

.dashboard-container table th, .dashboard-container table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.dashboard-container table th {
    background-color: #f2f2f2;
    color: #14332C; /* Primary theme color */
}

/* Form Styling */
.dashboard-container form {
    margin-top: 20px;
}

.dashboard-container form label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.dashboard-container form input, .dashboard-container form select {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.dashboard-container form input[type="submit"] {
    background-color: #14332C; /* Primary theme color */
    color: #fff;
    border: none;
    cursor: pointer;
    font-size: 16px;
}

.dashboard-container form input[type="submit"]:hover {
    background-color: #0d2a1e;
}
#site-title{
  text-align: center;
  margin: 0;
  color:#fff;
}
 </style>
</head>
<body>
    <header>
    <div class="welcome-message">
            <h1 id=site-title>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
        </div>
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
    
    <div class="dashboard-container">
        <section>
            <h2>Your Bookings</h2>
            <?php if ($bookings_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Destination</th>
                            <th>Booking Date</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                                <td><?php echo htmlspecialchars($booking['destination_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                                <td><?php echo htmlspecialchars($booking['status']); ?></td>
                                <td><?php echo htmlspecialchars($booking['price']); ?>KES</td> <!-- Display the price -->
                                <td>
                                    <?php if ($booking['status'] === 'pending'): ?>
                                        <form action="payment.php" method="GET">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                            <input type="hidden" name="price" value="<?php echo $booking['price']; ?>">
                                            <input type="submit" value="Pay Now" class="submit-button">
                                        </form>
                                    <?php else: ?>
                                        <span>N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You have no bookings yet.</p>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>

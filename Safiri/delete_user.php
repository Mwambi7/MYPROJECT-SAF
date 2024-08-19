<?php
session_start();
require 'db_connect.php'; // Ensure this file contains your database connection logic

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Check if booking_id is set in the query string and is a valid number
if (!isset($_GET['booking_id']) || !is_numeric($_GET['booking_id'])) {
    die('Invalid booking ID');
}

// Fetch booking ID from the query string
$booking_id = intval($_GET['booking_id']);
$conn = getDbConnection();

// Prepare the SQL query to delete the booking
$sql = "DELETE FROM bookings WHERE booking_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}

// Bind parameters and execute the statement
$stmt->bind_param("i", $booking_id);
$stmt->execute();

// Check if the deletion was successful
if ($stmt->affected_rows > 0) {
    header('Location: view_bookings.php'); // Redirect to the view bookings page
} else {
    die('Deletion failed: ' . $conn->error);
}

$stmt->close();
$conn->close();
?>

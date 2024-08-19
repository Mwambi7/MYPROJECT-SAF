<?php
session_start();
require 'db_connect.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch booking details
$user_id = $_SESSION['user_id'];
$booking_id = $_SESSION['booking_id'];  // Ensure you set this during booking

$query = "SELECT * FROM bookings WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

include 'confirmation.html';  // Assuming you have a separate HTML file for confirmation view
?>

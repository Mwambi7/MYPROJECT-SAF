<?php
session_start();
require 'db_connect.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Process booking
$user_id = $_SESSION['user_id'];
$tour_id = $_POST['tour_id'];
$booking_date = date('Y-m-d H:i:s');

$query = "INSERT INTO bookings (user_id, tour_id, booking_date) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param('iis', $user_id, $tour_id, $booking_date);
$stmt->execute();

header('Location: confirmation.php');
exit();
?>

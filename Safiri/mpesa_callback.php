<?php
session_start();

function db_conn() {
    require 'db_connect.php'; // Include the database connection file
    $conn = getDbConnection(); // Get the connection from db_connect.php

    if (!$conn) {
        die('Database connection failed: ' . mysqli_connect_error());
    }
    
    return $conn;
}

function handleCallback() {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (isset($data['Body']['stkCallback'])) {
        $callback = $data['Body']['stkCallback'];

        if ($callback['ResultCode'] == 0) {
            // Payment success
            $amount = $callback['CallbackMetadata']['Item'][0]['Value'];
            $receiptNumber = $callback['CallbackMetadata']['Item'][1]['Value'];
            $transactionDate = $callback['CallbackMetadata']['Item'][2]['Value'];
            $phoneNumber = $callback['CallbackMetadata']['Item'][3]['Value'];
            $booking_id = $callback['CallbackMetadata']['Item'][4]['Value']; // Ensure this is correct for your use case

            // Process success
            $conn = db_conn();

            $stmt = $conn->prepare("
                UPDATE bookings 
                SET 
                    status = 'Paid',
                    payment_status = 'Successful',
                    receipt_number = ?,
                    payment_date = ?,
                    phone_number = ?
                WHERE booking_id = ?
            ");

            if ($stmt) {
                $stmt->bind_param('ssss', $receiptNumber, $transactionDate, $phoneNumber, $booking_id);
                $stmt->execute();
                $stmt->close();
            } else {
                error_log("Failed to prepare statement: " . $conn->error);
            }

            $conn->close();
        } else {
            // Payment failure
            $resultDesc = $callback['ResultDesc'];
            error_log("Payment failed: $resultDesc");
        }
    } else {
        error_log("Invalid callback response");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handleCallback();
}
?>

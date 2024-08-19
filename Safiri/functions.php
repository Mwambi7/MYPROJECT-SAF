<?php
session_start();

function db_conn() {
    require 'db_connect.php'; // Ensure this file contains your database connection logic
    $conn = getDbConnection(); // Get the connection from db_connect.php

    if (!$conn) {
        die('Database connection failed: ' . mysqli_connect_error());
    }
    
    return $conn;
}

function getAccessToken() {
    $consumerKey = 'YOUR_CONSUMER_KEY'; // Replace with your consumer key
    $consumerSecret = 'YOUR_CONSUMER_SECRET'; // Replace with your consumer secret
    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

    $credentials = base64_encode($consumerKey . ':' . $consumerSecret);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . $credentials
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        error_log("cURL Error: " . curl_error($ch));
        curl_close($ch);
        return false;
    }

    curl_close($ch);

    $response_data = json_decode($response, true);

    return $response_data['access_token'] ?? false;
}

function mpesa($phone, $amount, $booking_id) {
    $accessToken = getAccessToken();
    if (!$accessToken) {
        error_log('Access token retrieval failed.');
        return false;
    }

    $BusinessShortCode = 'YOUR_BUSINESS_SHORT_CODE'; // Replace with your business shortcode
    $PassKey = 'YOUR_PASS_KEY'; // Replace with your passkey
    $phone = preg_replace('/^0/', '254', str_replace("+", "", $phone));
    $timestamp = date('YmdHis');
    $password = base64_encode($BusinessShortCode . $PassKey . $timestamp);

    $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

    $payload = json_encode([
        "BusinessShortCode" =>174379 ,
        "Password" =>"MTc0Mzc5YmZiMjc5ZjlhYTliZGJjZjE1OGU5N2RkNzFhNDY3Y2QyZTBjODkzMDU5YjEwZjc4ZTZiNzJhZGExZWQyYzkxOTIwMjQwODE5MDk1MjQ2" ,
        "Timestamp" => "20240819095246",
        "TransactionType" => "CustomerPayBillOnline",
        "Amount" => 1,
        "PartyA" => 254742538757,
        "PartyB" =>  174379,
        "PhoneNumber" =>254742538757,
        "CallBackURL" => "http://192.168.0.15/Safiri/mpesa_callback.php",
        "AccountReference" => "CompanyXLTD",
        "TransactionDesc" => 'Pay Booking'
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);

    if ($response === false) {
        error_log("cURL Error: " . curl_error($ch));
        curl_close($ch);
        return false;
    }

    curl_close($ch);

    $response_data = json_decode($response, true);

    if (isset($response_data['ResponseCode']) && $response_data['ResponseCode'] != '0') {
        error_log("M-Pesa API Error: " . $response_data['ResponseDescription']);
        return false;
    }

    return $response_data;
}

function handleCallback() {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (isset($data['Body']['stkCallback'])) {
        $callback = $data['Body']['stkCallback'];

        if ($callback['ResultCode'] == 0) {
            $amount = $callback['CallbackMetadata']['Item'][0]['Value'];
            $receiptNumber = $callback['CallbackMetadata']['Item'][1]['Value'];
            $transactionDate = $callback['CallbackMetadata']['Item'][2]['Value'];
            $phoneNumber = $callback['CallbackMetadata']['Item'][3]['Value'];
            $booking_id = $callback['CallbackMetadata']['Item'][4]['Value'];

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

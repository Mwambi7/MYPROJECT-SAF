<?php
include "functions.php";
require 'db_connect.php';

// Database connection
$conn = getDbConnection();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['submit'])) {
    $phone = $_POST['phone'];
    $amount = $_POST['amount'];
    $invoice = date('YmdHis'); // Use the current timestamp as invoice number
    $status = "pending"; // Initial status is pending

    // Retrieve the booking_id based on the phone number
    $booking_id = null;
    $stmt = $conn->prepare("SELECT booking_id FROM bookings WHERE phone_number = ? ORDER BY created_at DESC LIMIT 1");
    if ($stmt === false) {
        die('Error preparing statement: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $stmt->bind_result($booking_id);
    $stmt->fetch();
    $stmt->close();

    if ($booking_id) {
        // Call mpesa stkpush function
        $response = mpesa($phone, $amount, $invoice);

        // Assuming the `mpesa()` function returns an array, no need for `json_decode()`
        $response_data = $response; // If `mpesa()` returns an array

        // Check if the response is successful based on Mpesa API response format
        if (isset($response_data['ResponseCode']) && $response_data['ResponseCode'] === '0') {
            // Insert the transaction into the payments table
            $created_at = date('Y-m-d H:i:s'); // Current timestamp

            // Prepare and bind the SQL statement
            $stmt = $conn->prepare("INSERT INTO payments (booking_id, phone_number, amount, invoice, status, created_at) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt === false) {
                die('Error preparing statement: ' . htmlspecialchars($conn->error));
            }
            $stmt->bind_param("isdsss", $booking_id, $phone, $amount, $invoice, $status, $created_at);

            // Execute the query
            if ($stmt->execute()) {
                header("Location: payment.php?message=Please complete the payment on your phone.");
            } else {
                header("Location: payment.php?error=An error occurred while inserting the transaction record.");
            }

            // Close the statement
            $stmt->close();
        } else {
            // Log the exact error message for debugging
            $error_message = isset($response_data['errorMessage']) ? $response_data['errorMessage'] : 'Unknown error';
            header("Location: payment.php?error=An error occurred during the payment process: " . htmlspecialchars($error_message));
        }
    } else {
        header("Location: payment.php?error=No booking found for this phone number.");
    }
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safiri Payment</title>
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
      /* Payment Container Styles */
.payment-container {
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f9f9f9;
}

/* Title Styles */
.payment-title {
    font-size: 24px;
    color: #14332C; /* Primary theme color */
    margin-bottom: 20px;
    text-align: center;
}

/* Form Styles */
#payment-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

/* Form Group Styles */
.form-group {
    display: flex;
    flex-direction: column;
}

/* Label Styles */
.form-label {
    font-size: 16px;
    color: #333;
    margin-bottom: 5px;
}

/* Input Styles */
.form-input {
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
    width: 100%;
}

/* Submit Button Styles */
.submit-button {
    padding: 10px 20px;
    background-color: #14332C; /* Primary theme color */
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
    width: 100%;
}

.submit-button:hover {
    background-color: #0d2a1e; /* Darker shade of theme color */
}

/* Alert Styles */
.alert {
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 4px;
    font-size: 16px;
    text-align: center;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
}

    </style>
</head>
<body>
    <div class="payment-container">
        <h2 class="payment-title">Complete Your Payment</h2>
        <!-- Display any messages -->
<?php if (isset($_GET['message'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_GET['message']); ?>
    </div>
<?php endif; ?>


        <!-- Display any error messages -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <form id="payment-form" action="payment.php" method="POST">
            <div class="form-group">
                <label for="phone" class="form-label">Phone Number (Mpesa):</label>
                <input type="text" id="phone" name="phone" class="form-input" required placeholder="Enter your Mpesa phone number">
            </div>

            <div class="form-group">
                <label for="amount" class="form-label">Amount to Pay (KES):</label>
                <input type="text" id="amount" name="amount" class="form-input" required placeholder="Enter the amount to pay">
            </div>
            

            <div class="form-group">
                <input type="submit" name="submit" id="submit-button" value="Pay Now" class="submit-button">
            </div>
        </form>
    </div>
</body>
</html>

<?php
session_start(); // Start the session

// Include the database connection file
require 'db_connect.php'; // Ensure this path is correct

// Initialize an empty array for error messages
$errors = [];

// Get the database connection
$conn = getDbConnection();

// Check if the connection is valid
if (!$conn) {
    die("Database connection failed.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input from the form
    $user_id = $_POST['user_id']; // Ensure this is set appropriately
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Validate the input
    if (empty($subject)) {
        $errors[] = "Subject is required.";
    }
    if (empty($message)) {
        $errors[] = "Message is required.";
    }

    // If no errors, insert into the database
    if (empty($errors)) {
        $sql = "INSERT INTO contact_us (user_id, subject, message) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {  // Make sure $conn is properly initialized
            $stmt->bind_param("iss", $user_id, $subject, $message);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Message sent successfully!</div>";
            } else {
                echo "<div class='alert alert-error'>Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        } else {
            echo "<div class='alert alert-error'>Error: " . $conn->error . "</div>";
        }
    } else {
        // Display errors
        foreach ($errors as $error) {
            echo "<div class='alert alert-error'>$error</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
   
    <style>/* General Container Styles */
/* General Container Styles */
/* General Page Styles */
body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

/* Header Styles */
#main-header {
    background-color: #14332C; /* Safiri theme color */
    color: #fff;
    padding: 10px 0;
}

#main-header nav {
    display: flex;
    justify-content: center;
}

#main-header ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
}

#main-header li {
    margin: 0 15px;
}

#main-header a {
    color: #fff;
    text-decoration: none;
    font-size: 16px;
}

#main-header a:hover {
    text-decoration: underline;
}

/* Main Content Styles */
#main-contentt {
    display: block;
    padding: 2em 1em;
}

/* Title Styles */
.title {
    font-size: 24px;
    margin-bottom: 20px;
    color: #333;
}

/* Form Styles */
#contact-form {
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

/* Input and Textarea Styles */
.form-input, .form-textarea {
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

/* Textarea Specific Styles */
.form-textarea {
    resize: vertical;
}

/* Submit Button Styles */
.submit-button {
    padding: 10px 20px;
    background-color: #162e28; /* Safiri theme color */
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

.submit-button:hover {
    background-color: #0e1e1a; /* Darker shade of Safiri theme color */
}

/* Alert Styles */
.alert {
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 4px;
    font-size: 16px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
}

/* Footer Styles */
#main-footer {
    background-color: #14332C; /* Safiri theme color */
    color: #fff;
    padding: 10px 0;
    text-align: center;
}

#main-footer p {
    margin: 0;
}

#main-footer a {
    color: #fff;
    text-decoration: none;
}

#main-footer a:hover {
    text-decoration: underline;
}

/* Header Styles for Edit Profile */
header {
    margin-bottom: 20px;
}

h1 {
    font-size: 28px;
    margin-bottom: 20px;
    color: #333;
}

/* Form Styles for Edit Profile */
form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

input[type="text"], input[type="email"] {
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

input[type="submit"] {
    padding: 10px 20px;
    background-color: #162e28; /* Safiri theme color */
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

input[type="submit"]:hover {
    background-color: #0e1e1a; /* Darker shade of Safiri theme color */
}

a {
    color: #162e28; /* Safiri theme color */
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}


     </style>
    <header id="main-header">
        <div class="container">
        <header>
        <nav>
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
        </div>
    </header>
</head>
<body>
   <main id="main-contentt">
  <section id="hero-section">
    <div class="container">
        <h2 class="title">Contact Us</h2>
        <form id="contact-form" action="contact_us.php" method="POST">
            <input type="hidden" id="user-id" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?> <!-- Ensure user is logged in and session is set -->

            <div class="form-group">
                <label for="subject" class="form-label">Subject:</label>
                <input type="text" id="subject" name="subject" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="message" class="form-label">Message:</label>
                <textarea id="message" name="message" class="form-textarea" rows="5" required></textarea>
            </div>

            <input type="submit" value="Send Message" class="submit-button">
        </form>
    </div>
    </section>
    </main>


    <footer id="main-footer">
        <div class="container1">
            <p>&copy; 2024 Safiri. All rights reserved.</p>
        
        </div>
    </footer>
</body>
</html>
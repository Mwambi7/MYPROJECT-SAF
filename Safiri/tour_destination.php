<?php
session_start();
require 'db_connect.php'; // Include the database connection file

// Create a database connection
$conn = getDbConnection(); // Get the connection from db_connect.php

// Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch available tour destinations from the database
$query = "SELECT * FROM tour_destinations WHERE availability_status = 'available'";
$result = $conn->query($query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Destinations - Safiri</title>
    <link rel="stylesheet" href="Styles/destinations.css"> <!-- Link to external CSS file -->
</head>
<body>
    <header id="main-header">
    <div class="containert">
        <h1 class="site-title">Explore Our Destinations</h1>
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

    <section class="destination-section">
        <?php if ($result->num_rows > 0): ?>
            <div class="destination-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="destination-card">
                        <div class="image-container">
                            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['destination_name']); ?>" class="destination-image">
                        </div>
                        <div class="destination-details">
                            <h2 class="destination-name"><?php echo htmlspecialchars($row['destination_name']); ?></h2>
                            <p class="destination-description"><?php echo htmlspecialchars($row['description']); ?></p>
                            <p class="destination-price"><?php echo htmlspecialchars($row['price']); ?> KES</p> <!-- Updated currency display -->
                            <a href="book_tour.php?destination_id=<?php echo $row['destination_id']; ?>" class="cta-button">Book Now</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="no-destinations">No destinations available at the moment. Please check back later.</p>
        <?php endif; ?>
    </section>

    <footer>
        <p>&copy; 2024 Safiri. All Rights Reserved.</p>
    </footer>
</body>
</html>
<?php
// Close the database connection
$conn->close();
?>

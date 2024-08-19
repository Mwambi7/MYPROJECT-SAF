<?php
// Include any necessary PHP files or functions here, e.g., for dynamic content or session management
session_start(); // Start the session if user data or authentication is required
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safiri - Home</title>
    <link rel="stylesheet" href="Styles/index.css"> <!-- Link to main stylesheet -->
   <style> .image-gallery {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
        }

        .image-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 300px;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .image-container img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }

        .image-container p {
            margin-top: 10px;
            font-size: 14px;
            text-align: center;
            #contact-link{
  color:#39cc14;
}
        } </style>
</head>
<body>
    <header id="main-header">
        <div class="container">
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

    <main id="main-content">
        <section id="hero-section">
        <div class="image-gallery">
        <div class="image-container">
            <img src="images/camping.jpg" alt="Description of Image 1">
            <p>Find serenity amidst the summit with mountain camping retreats, 
              
            where you can disconnect from the hustle and bustle and reconnect with nature's tranquility and beauty</p>
        </div>

        <div class="image-container">
            <img src="images/beach.jpg" alt="Description of Image 2">
            <p>Let the waves hit your feet and the sand be your seat</p>
        </div>

        <div class="image-container">
            <img src="images/elephant.jpg" alt="Description of Image 3">
            <p>Dive into the essence of the safari at dawn, where every sunrise paints a masterpiece of colors across the untamed wilderness.
               This moment, where the quiet of nature meets the first light, offers a unique glimpse into the heart of the safari experience. 
               It's a daily spectacle that redefines the beauty of the natural world, inviting onlookers into a realm where wildlife and landscape unite in perfect harmony</p>
        </div>
    </div>
            <div class="container">
                <h2 id="hero-title">Explore Our Amazing Tours</h2>
                <p id="hero-text">Discover the best destinations and experiences tailored just for you. From exotic beaches to vibrant cities, Safiri offers unforgettable adventures.</p>
                <a href="tour_destination.php" class="cta-button" id="explore-button">Explore Destinations</a>
            </div>
        </section>

        <section id="features-section">
            <div class="container">
                <div class="feature" id="personalized-tours">
                    <h3>Personalized Tours</h3>
                    <p>Get recommendations based on your interests and preferences.</p>
                </div>
                <div class="feature" id="secure-booking">
                    <h3>Secure Booking</h3>
                    <p>Book your tours with confidence, knowing your payment details are safe.</p>
                </div>
                <div class="feature" id="support">
                    <h3>24/7 Support</h3>
                    <p>Our support team is here to help you with any questions or issues.</p>
                </div>
            </div>
        </section>

        <section id="testimonial-section">
            <div class="container">
                <h2 id="testimonial-title">What Our Customers Say</h2>
                <blockquote id="customer-testimonial">
                    <p>"Safiri made planning my vacation a breeze. The recommendations were spot on, and the booking process was seamless!"</p>
                    <footer>- Jane Doe</footer>
                </blockquote>
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

<?php
$servername = "localhost:3307";
$username = "root"; // Default username
$password = ""; // Default password
$dbname = "safiridb";

// Create connection
$link = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
}
?>

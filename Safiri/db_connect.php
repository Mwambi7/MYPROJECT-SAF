<?php
function getDbConnection() {
    $servername = "localhost:3307"; // or your specific server port
    $username = "root";
    $password = ""; // or your database password
    $dbname = "safiridb";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>


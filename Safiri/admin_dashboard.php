<?php
session_start();

// Ensure only admins can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';

function fetchUsers($conn) {
    $query = "SELECT user_id, username, email, role, created_at FROM users";
    return $conn->query($query);
}

function fetchBookings($conn) {
    $query = "SELECT b.booking_id, u.username, d.destination_name, b.booking_date, b.price, b.status, b.payment_status 
              FROM bookings b 
              JOIN users u ON b.user_id = u.user_id 
              JOIN tour_destinations d ON b.destination_id = d.destination_id";
    return $conn->query($query);
}

$conn = getDbConnection();
$users = fetchUsers($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
      body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
      }
      header { 
        background-color: #14332C;
        padding: 10px 0;
        border-bottom: 1px solid #ddd;
        display: flex;
        flex-direction: column;
        align-items: center;
      }
      .container {
        width: 80%;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-top: 30px;
      }
      h1 {
        text-align: center;
        margin-bottom: 20px;
      }
      nav ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
      }
      nav ul li {
        display: inline;
        margin-right: 10px;
      }
      nav ul li a {
        text-decoration: none;
        color: #007BFF;
      }
      nav ul li a:hover {
        text-decoration: underline;
      }
      table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
      }
      table, th, td {
        border: 1px solid #ddd;
      }
      th, td {
        padding: 10px;
        text-align: left;
      }
      th {
        background-color: #f2f2f2;
      }
      a {
        text-decoration: none;
        color: #007BFF;
      }
      a.edit {
        color: #28a745;
      }
      a.delete {
        color: #dc3545;
      }
      a.back-button {
        display: inline-block;
        padding: 10px 15px;
        background-color: #007BFF;
        color: #fff;
        border-radius: 5px;
      }
      a.back-button:hover {
        background-color: #0056b3;
      }
    </style>
</head>
<body>
    <header>
        <!-- Navigation Links -->
        <nav>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="view_bookings.php">View Bookings</a></li>
                <li><a href="add_destination.php">Manage Destinations</a></li>
                <li><a href="admin_logs.php">Admin Logs</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <!-- Users Section -->
        <section>
            <h2>Registered Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users->num_rows > 0): ?>
                        <?php while ($row = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['user_id']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['role']; ?></td>
                                <td><?php echo $row['created_at']; ?></td>
                                <td>
                                    <a href="edit_user.php?user_id=<?php echo $row['user_id']; ?>" class="edit">Edit</a> | 
                                    <a href="delete_user.php?user_id=<?php echo $row['user_id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a> | 
                                    <a href="view_bookings.php?user_id=<?php echo $row['user_id']; ?>" class="view">View Bookings</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan='6'>No users found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <!-- Bookings Section -->
        <section>
            <h2>User Bookings</h2>
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>User</th>
                        <th>Destination</th>
                        <th>Booking Date</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $bookings = fetchBookings($conn);
                    if ($bookings->num_rows > 0) {
                        while ($row = $bookings->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['booking_id']}</td>
                                    <td>{$row['username']}</td>
                                    <td>{$row['destination_name']}</td>
                                    <td>{$row['booking_date']}</td>
                                    <td>{$row['price']}</td>
                                    <td>{$row['status']}</td>
                                    <td>{$row['payment_status']}</td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No bookings found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>
    <?php $conn->close(); ?>
</body>
</html>

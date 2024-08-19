<?php
session_start();
require 'db_connect.php'; // Ensure this file contains your database connection logic

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch users from the database
$conn = getDbConnection();
$sql = "SELECT user_id, username, email, role, created_at FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="admin_dashboard.css">

    <
    <header>
        <!-- Navigation Links -->
        <nav>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="view_bookings.php?user_id=1">View Bookings</a></li>
                <li><a href="add_destination.php">Manage Destinations</a></li>
                <li><a href="admin_logs.php">Admin Logs</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
</header>
    
<style> 
  /* dashboard.css */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}
header{ 
    background-color: #14332C ;
    padding: 10px 0;
    border-bottom: 1px solid #ddd;
    display: flex;
    flex-direction: column;
    align-items: center;}


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
    <div class="container">
        <h1>Manage Users</h1>
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
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['user_id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['role']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td>
                            <a href="edit_user.php?user_id=<?php echo $row['user_id']; ?>" class="edit">Edit</a>
                            <a href="delete_user.php?user_id=<?php echo $row['user_id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            <a href="view_bookings.php?user_id=<?php echo $row['user_id']; ?>" class="view">View Bookings</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>

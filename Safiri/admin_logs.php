<?php
session_start();
require 'db_connect.php'; // Ensure this file contains your database connection logic

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch admin logs from the database
$conn = getDbConnection();
$sql = "SELECT * FROM admin_logs";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Logs</title>
    <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
    <div class="container">
        <h1>Admin Logs</h1>
        <table>
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>Admin ID</th>
                    <th>Action</th>
                    <th>Target ID</th>
                    <th>Target Type</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['log_id']; ?></td>
                        <td><?php echo $row['admin_id']; ?></td>
                        <td><?php echo $row['action']; ?></td>
                        <td><?php echo $row['target_id']; ?></td>
                        <td><?php echo $row['target_type']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
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

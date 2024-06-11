<?php

include 'connect.php';

$select_users = $conn->prepare("SELECT * FROM `users`");
$select_users->execute();
$users = $select_users->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Users</title>
    <link rel="stylesheet" type="text/css" href="users.css">
</head>

<body>
<nav class="navbar">
    <h2>Admin Dashboard</h2>
    <ul>
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="all_users.php">Users</a></li>
        <li><a href="admin_panel.php">Approve Users</a></li>
        <li><a href="admin_contact.php">Messages</a></li>
        <li><a href="admin_logout.php">Log Out</a></li>
    </ul>
</nav>


<header>
        <h1>All Users</h1>
    </header>
    <table class="user-table">
            <thead>
                <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Number</th>
                
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $user): ?>
                <tr class="user-row">
                    <td><?php echo $user['user_id']; ?></td>
                    <td><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['date_of_birth']; ?></td>
                    <!-- Add more columns as needed -->
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>

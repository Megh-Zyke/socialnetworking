
<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="admin_dashboard.css" />
</head>

<body>
    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="all_users.php">Users</a></li>
            <li><a href="admin_panel.php">Approve Users</a></li>
            <li><a href="admin_contact.php">Messages</a></li>
            <li><a href="admin_logout.php">Log Out</a></li>
        </ul>
    </div>

    <div class="content">
        <h1>Welcome, Admin!</h1>
        
            <div class="card-container">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Add Users</h5>
                            <a href="admin_panel.php" class="btn btn-primary">Approve Users</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">All Users</h5>
                            <a href="all_users.php" class="btn btn-primary">All Users</a>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</body>

</html>
    
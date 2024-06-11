<?php
session_start();

// if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
//     header('Location: login.php');
//     exit();
// }

// Check if the user_id parameter is set in the POST request
if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // Include the file that establishes the database connection
    include 'connect.php';

    // Update the user's 'approved' status in the database
    $update_user = $conn->prepare("UPDATE `users` SET approved = 1 WHERE user_id = ?");
    $update_user->execute([$user_id]);

    if ($update_user->rowCount() > 0) {
        // Approval successful
        header('Location: admin_panel.php');
        exit();
    } else {
        // Approval failed
        header('Location: admin_panel.php?approval_failed=1');
        exit();
    }
} else {
    // Redirect if user_id is not set
    header('Location: admin_panel.php');
    exit();
}
?>

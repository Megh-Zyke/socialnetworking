<?php
include 'connect.php';

if (isset($_POST['reject'])) {
    $user_id = $_POST['user_id'];

    $update_user = $conn->prepare("UPDATE `users` SET approved = 2 WHERE user_id = ?");
    $update_user->execute([$user_id]);

    if ($update_user->rowCount() > 0) {
        header('Location: admin_panel.php');
        exit();
    } else {
        header('Location: admin_panel.php');
        exit();
    }
} else {
    header('Location: admin_panel.php');
    exit();
}
?>

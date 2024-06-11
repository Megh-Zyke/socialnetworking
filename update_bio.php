<?php
include 'connection.php';


if (!isset($_SESSION['user_id'])) {
    echo "User not logged in";
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['bio'])) {
    $bio = $_POST['bio'];

    $update_bio = $conn->prepare("UPDATE users SET bio = ? WHERE user_id = ?");
    $update_bio->bind_param("si", $bio, $user_id);
    $update_bio->execute();
    $affected_rows = $update_bio->affected_rows;
    $update_bio->close();

    if ($affected_rows > 0) {
        echo "Bio updated successfully";
    } else {
        // Error updating bio
        echo "Error updating bio";
    }
} else {
    // Bio data not received
    echo "Bio data not received";
}
?>

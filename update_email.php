<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $current_user_id = $_SESSION['user_id'];
    $new_email = $_POST['new_email'];

    // Update the user's email in the database
    $update_email_query = "UPDATE users SET email = ? WHERE user_id = ?";
    $update_email_stmt = $conn->prepare($update_email_query);
    $update_email_stmt->bind_param("si", $new_email, $current_user_id);
    $update_email_stmt->execute();

    echo json_encode(["status" => "success"]);

    // Close statements and database connection
    $update_email_stmt->close();
    $conn->close();
}
?>
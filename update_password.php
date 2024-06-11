<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $current_user_id = $_SESSION['user_id'];

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    // Retrieve the user's current password from the database
    $query = "SELECT password FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $stmt->bind_result($stored_password);
    $stmt->fetch();
    $stmt->close();

    // Verify if the provided current password matches the stored password
    if (password_verify($current_password, $stored_password)) {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the user's password in the database
        $update_password_query = "UPDATE users SET password = ? WHERE user_id = ?";
        $update_password_stmt = $conn->prepare($update_password_query);
        $update_password_stmt->bind_param("si", $hashed_password, $current_user_id);
        $update_password_stmt->execute();

        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Current password is incorrect."]);
    }

    // Close statements and database connection
    $conn->close();
}
?>
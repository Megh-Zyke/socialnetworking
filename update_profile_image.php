<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $current_user_id = $_SESSION['user_id'];

    // Check if a file was uploaded
    if ($_FILES['profile_image']['size'] > 0) {
        // Define the directory where the file will be saved
        $target_dir = "uploads/";

        // Generate a unique filename to prevent overwriting existing files
        $target_file = $target_dir . uniqid() . '_' . basename($_FILES["profile_image"]["name"]);

        // Check if the file was successfully uploaded
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            // File uploaded successfully, update the database with the new file path
            $update_image_query = "UPDATE users SET profile_image_url = ? WHERE user_id = ?";
            $update_image_stmt = $conn->prepare($update_image_query);
            $update_image_stmt->bind_param("si", $target_file, $current_user_id);
            $update_image_stmt->execute();

            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error uploading file."]);
        }
    }

    // Close statements and database connection
    $update_image_stmt->close();
    $conn->close();
}
?>
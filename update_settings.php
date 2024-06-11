<?php
include 'connection.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$update_image_stmt = null;
$update_password_stmt = null;
$update_email_stmt = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $current_user_id = $_SESSION['user_id'];
    echo "User ID: " . $current_user_id;

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $new_email = $_POST['new_email'];

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
            if (!$update_image_stmt) {
                echo "Error preparing statement: " . $conn->error;
                exit;
            }
            if (!$update_image_stmt->bind_param("si", $target_file, $current_user_id)) {
                echo "Error binding parameters: " . $update_image_stmt->error;
                exit;
            }
            if (!$update_image_stmt->execute()) {
                echo "Error executing statement: " . $update_image_stmt->error;
                exit;
            }

            echo "Profile image updated successfully!";
        } else {
            echo "Error uploading file.";
        }
    }

    // Retrieve the user's current password and email from the database
    $query = "SELECT password, email FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $stmt->bind_result($stored_password, $stored_email);
    $stmt->fetch();

    // Close the statement to free the result set
    $stmt->close();

    // Verify if the provided current password matches the stored password
    if (password_verify($current_password, $stored_password)) {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the user's password in the database
        $update_password_query = "UPDATE users SET password = ? WHERE user_id = ?";
        $update_password_stmt = $conn->prepare($update_password_query);
        if (!$update_password_stmt) {
            echo "Error preparing statement: " . $conn->error;
            exit;
        }
        if (!$update_password_stmt->bind_param("si", $hashed_password, $current_user_id)) {
            echo "Error binding parameters: " . $update_password_stmt->error;
            exit;
        }
        if (!$update_password_stmt->execute()) {
            echo "Error executing statement: " . $update_password_stmt->error;
            exit;
        }

        echo "Password updated successfully!";
    } else {
        echo "Current password is incorrect.";
    }

    // Update the user's email in the database
    $update_email_query = "UPDATE users SET email = ? WHERE user_id = ?";
    $update_email_stmt = $conn->prepare($update_email_query);
    if (!$update_email_stmt) {
        echo "Error preparing statement: " . $conn->error;
        exit;
    }
    if (!$update_email_stmt->bind_param("si", $new_email, $current_user_id)) {
        echo "Error binding parameters: " . $update_email_stmt->error;
        exit;
    }
    if (!$update_email_stmt->execute()) {
        echo "Error executing statement: " . $update_email_stmt->error;
        exit;
    }

    echo "Email updated successfully!";

    // Close statements and database connection
    if ($update_image_stmt) {
        $update_image_stmt->close();
    }
    if ($update_password_stmt) {
        $update_password_stmt->close();
    }
    if ($update_email_stmt) {
        $update_email_stmt->close();
    }
    $conn->close();
}
?>
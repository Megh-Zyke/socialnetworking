<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start(); 

    include 'connection.php';

    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $postText = isset($_POST['post_content']) ? htmlspecialchars(trim($_POST['post_content'])) : '';

    if ($user_id == 0) {
        echo "Error: User is not logged in.";
        exit();
    }
    $targetDir = 'uploads/';
    if (!file_exists($targetDir) && !is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $mediaFileName = '';
    $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $allowedVideoTypes = ['video/mp4', 'video/webm', 'video/ogg'];

    if (isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
        if (in_array($_FILES['photo']['type'], $allowedImageTypes)) {
            $mediaFileName = $targetDir . uniqid('img_', true) . '.' . pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            move_uploaded_file($_FILES['photo']['tmp_name'], $mediaFileName);
        } else {
            echo "Error: Invalid image file type.";
            exit();
        }
    } elseif (isset($_FILES['video']) && is_uploaded_file($_FILES['video']['tmp_name'])) {
        if (in_array($_FILES['video']['type'], $allowedVideoTypes)) {
            $mediaFileName = $targetDir . uniqid('vid_', true) . '.' . pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
            move_uploaded_file($_FILES['video']['tmp_name'], $mediaFileName);
        } else {
            echo "Error: Invalid video file type.";
            exit();
        }
    }

    if ($postText === '' && $mediaFileName === '') {
        echo "Error: Both post content and media cannot be empty.";
        exit();
    }

    $sql = "INSERT INTO posts (user_id, post_content, post_image_path, post_date) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "Error: " . $conn->error;
        exit();
    }

    $stmt->bind_param("iss", $user_id, $postText, $mediaFileName);

    if ($stmt->execute()) {
        header("Location: user-page.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

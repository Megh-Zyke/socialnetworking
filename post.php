<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    include 'connection.php';

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
    $postText = isset($_POST['post_content']) ? $_POST['post_content'] : '';

    $targetDir = 'uploads/';
    if (!file_exists($targetDir) && !is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $mediaFileName = '';
    if (isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
        $mediaFileName = $targetDir . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $mediaFileName);
    } elseif (isset($_FILES['video']) && is_uploaded_file($_FILES['video']['tmp_name'])) {
        $mediaFileName = $targetDir . basename($_FILES['video']['name']);
        move_uploaded_file($_FILES['video']['tmp_name'], $mediaFileName);
    }

    if ($postText === '' && $mediaFileName === '') {
        echo "Error: Both post content and image cannot be null.";
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

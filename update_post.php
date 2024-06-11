<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postId = $_POST['postId'];
    $updatedPostText = $_POST['postText'];

    $update_post = $conn->prepare("UPDATE posts SET post_content = ? WHERE post_id = ?");
    $update_post->bind_param("si", $updatedPostText, $postId);
    if ($update_post->execute()) {
        header("Location: user-page.php");
    } else {
        echo "Error updating post";
    }
    $update_post->close();
}
?>

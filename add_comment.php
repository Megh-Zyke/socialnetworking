<?php
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    echo "User not logged in";
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['post_id']) && isset($_POST['comment_text'])) {
    $post_id = $_POST['post_id'];
    $comment_text = $_POST['comment_text'];

    // Insert the comment into the database
    $insert_comment = $conn->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
    $insert_comment->bind_param("iis", $post_id, $user_id, $comment_text);
    if ($insert_comment->execute()) {
        // Comment added successfully
        $comment_id = $insert_comment->insert_id;
        $insert_comment->close();

        // Fetch the user details for the comment
        $select_user = $conn->prepare("SELECT first_name, last_name, profile_image_url FROM users WHERE user_id = ?");
        $select_user->bind_param("i", $user_id);
        $select_user->execute();
        $select_user->bind_result($first_name, $last_name, $profile_image_url);
        $select_user->fetch();
        $select_user->close();

        // Format the comment HTML
        $comment_html = '<div class="comment_item">';
        $comment_html .= '<div class="comment_user">';
        $comment_html .= '<img src="' . $profile_image_url . '" alt="Profile Image" class="profile_image_rounded">';
        $comment_html .= '<span>' . ucwords(strtolower($first_name . ' ' . $last_name)) . '</span>';
        $comment_html .= '</div>';
        $comment_html .= '<div class="comment_text">';
        $comment_html .= '<p>' . htmlspecialchars($comment_text) . '</p>';
        $comment_html .= '</div>';
        $comment_html .= '<div class="comment_date">';
        $comment_html .= '<span>' . date('Y-m-d H:i:s') . '</span>'; // You can customize the date format
        $comment_html .= '</div>';
        $comment_html .= '</div>';

        echo $comment_html; // Return the HTML of the new comment
    } else {
        // Failed to add comment
        echo "Error adding comment";
    }
} else {
    // Invalid request
    echo "Invalid request";
}
?>

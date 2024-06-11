<?php
session_start();
include 'connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    exit('Error: User is not logged in');
}

// Check if the post ID is provided
if (!isset($_POST['post_id'])) {
    http_response_code(400); // Bad request
    exit('Error: Post ID is missing');
}

$current_user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id']);

// Toggle the like status
$stmt = $conn->prepare("SELECT likes FROM posts WHERE post_id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->bind_result($likes_json);
$stmt->fetch();
$stmt->close();

$likes_array = json_decode($likes_json, true) ?: [];
$like_count = count($likes_array);

// Check if the user already liked the post
$user_index = array_search($current_user_id, $likes_array);
if ($user_index !== false) {
    // Unlike the post
    unset($likes_array[$user_index]);
} else {
    // Like the post
    $likes_array[] = $current_user_id;
}

// Update the likes in the database
$new_likes_json = json_encode($likes_array);
$update_stmt = $conn->prepare("UPDATE posts SET likes = ? WHERE post_id = ?");
$update_stmt->bind_param("si", $new_likes_json, $post_id);
$update_success = $update_stmt->execute();
$update_stmt->close();

if ($update_success) {
    // Return the updated like count as a response
    $response = [
        'success' => true,
        'likeCount' => count($likes_array)
    ];
    echo json_encode($response);
} else {
    http_response_code(500); // Internal server error
    exit('Error: Failed to update like status');
}
?>

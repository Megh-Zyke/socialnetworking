<?php
include 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(array("success" => false, "message" => "User not logged in"));
    exit();
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(array("success" => false, "message" => "Invalid request method"));
    exit();
}

// Get the post ID from the request
if (!isset($_POST['post_id'])) {
    echo json_encode(array("success" => false, "message" => "Post ID not provided"));
    exit();
}

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_id'];

// Fetch the current likes for the post
$select_post_likes = $conn->prepare("SELECT likes FROM posts WHERE post_id = ?");
$select_post_likes->bind_param("i", $post_id);
$select_post_likes->execute();
$select_post_likes->bind_result($likes_json);
$select_post_likes->fetch();
$select_post_likes->close();

$likes_array = json_decode($likes_json, true);

if ($likes_array == null) {
    $likes_array = array();
}

// Check if the user has already liked the post
$user_index = array_search($user_id, $likes_array);

if ($user_index !== false) {
    // If user already liked the post, remove the like
    unset($likes_array[$user_index]);
} else {
    // If user hasn't liked the post, add the like
    $likes_array[] = $user_id;
}

// Update the likes for the post in the database
$updated_likes_json = json_encode($likes_array);

$update_post_likes = $conn->prepare("UPDATE posts SET likes = ? WHERE post_id = ?");
$update_post_likes->bind_param("si", $updated_likes_json, $post_id);

if ($update_post_likes->execute()) {
    // Successfully added or removed like, return updated like count
    $like_count = count($likes_array);
    echo json_encode(array("success" => true, "likeCount" => $like_count));
} else {
    // Failed to update likes
    echo json_encode(array("success" => false, "message" => "Failed to update likes"));
}

$update_post_likes->close();
?>

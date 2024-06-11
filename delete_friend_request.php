<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json_data = file_get_contents("php://input");
    $data = json_decode($json_data);

    $friend_id = $data->user_id;
    $user_id = $_SESSION['user_id'];

    // Delete the friend request
    $delete_request = $conn->prepare("DELETE FROM friend_requests WHERE (sender_id = ? AND recipient_id = ?) OR (sender_id = ? AND recipient_id = ?)");
    $delete_request->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);

    if ($delete_request->execute()) {
        echo json_encode(array("status" => "success", "message" => "Friend request rejected"));
    } else {
        echo json_encode(array("status" => "error", "message" => "Failed to reject friend request"));
    }
    
    $delete_request->close();
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request method"));
}
?>

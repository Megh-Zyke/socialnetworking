<?php
include 'connection.php';   

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_data = file_get_contents("php://input");
    $data = json_decode($json_data);

    $senderUserId = $data->user_id;

    // Check if the friend request already exists
    $check_request = $conn->prepare("SELECT * FROM friend_requests WHERE (sender_id = ? AND recipient_id = ?) OR (sender_id = ? AND recipient_id = ?)");
    $check_request->bind_param("iiii", $user_id, $senderUserId, $senderUserId, $user_id);
    $check_request->execute();
    $check_request->store_result();
    $num_rows = $check_request->num_rows;

    if ($num_rows > 0) {
        // Friend request already exists, return error response
        echo json_encode(['success' => false, 'message' => 'Friend request already exists']);
    } else {
        // Insert a new row into the friend_requests table to represent the received request
        $insert_request = $conn->prepare("INSERT INTO friend_requests (sender_id, recipient_id) VALUES (?, ?)");
        $insert_request->bind_param("ii", $senderUserId , $user_id);
        if ($insert_request->execute()) {
            // Request successfully received and inserted into the database
            echo json_encode(['success' => true]);
        } else {
            // Error occurred while inserting the request
            echo json_encode(['success' => false, 'message' => 'Error receiving friend request']);
        }
    }

    // Close prepared statements
    $check_request->close();
    $insert_request->close();
} else {
    // Invalid request method or missing parameters
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>

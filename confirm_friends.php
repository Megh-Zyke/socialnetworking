<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json_data = file_get_contents("php://input");

    $data = json_decode($json_data);

    $friend_id = $data->user_id;

    $user_id = $_SESSION['user_id'];
    
    $select_friends = $conn->prepare("SELECT friends FROM users WHERE user_id = ?");
    $select_friends->bind_param("i", $user_id);
    $select_friends->execute();
    $select_friends->bind_result($friends_list);
    $select_friends->fetch();
    $select_friends->close();

    $friends_array = json_decode($friends_list, true);

    if ($friends_array == null) {
        $friends_array = array();
    }

    $friends_array[] = $friend_id;


    $updated_friends = json_encode($friends_array);

    $update_friends = $conn->prepare("UPDATE users SET friends = ? WHERE user_id = ?");
    $update_friends->bind_param("si", $updated_friends, $user_id);

    if ($update_friends->execute()) {
        echo "Friend Added";
    } else {
        echo json_encode(array("status" => "error", "message" => "Failed to add like"));
    }

  


    $select_friends2 = $conn->prepare("SELECT friends FROM users WHERE user_id = ?");
    $select_friends2->bind_param("i", $friend_id);
    $select_friends2->execute();
    $select_friends2->bind_result($friends_list2);
    $select_friends2->fetch();
    $select_friends2->close();

    $friends_array2= json_decode($friends_list2, true);

    if ($friends_array2 == null) {
        $friends_array2 = array();
    }

    $friends_array2[] = $user_id;


    $updated_friends2 = json_encode($friends_array2);

    $update_friends = $conn->prepare("UPDATE users SET friends = ? WHERE user_id = ?");
    $update_friends->bind_param("si", $updated_friends2, $friend_id);


    $check_request = $conn->prepare("delete  FROM friend_requests WHERE (sender_id = ? AND recipient_id = ?) OR (sender_id = ? AND recipient_id = ?)");
    $check_request->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
    $check_request->execute();

    if ($update_friends->execute()) {
        header("loaction : user.php " );
    } else {
        echo json_encode(array("status" => "error", "message" => "Failed to add like"));
    }

    
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request method"));
}
?>

<?php
if(isset($_POST['postId']) && !empty($_POST['postId'])) {
    include 'connection.php';
    $postId = mysqli_real_escape_string($conn, $_POST['postId']);

    $sql_1 = "SELECT post_image_path FROM posts WHERE post_id = '$postId'";
    $result = mysqli_query($conn, $sql_1);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $filePath = $row['post_image_path'];
        
        $sql_2 = "DELETE FROM comments WHERE post_id = '$postId'";
        if(mysqli_query($conn, $sql_2)) {
         
            $sql = "DELETE FROM posts WHERE post_id = '$postId'";
            if(mysqli_query($conn, $sql)) {
                if (!empty($filePath) && file_exists($filePath)) {
                    unlink($filePath);
                }
                
                http_response_code(200);
                header('Location: ../user-page.php');
                exit();
            } else {
                http_response_code(500);
                echo json_encode(array("message" => "Error: Unable to delete post."));
            }
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Error: Unable to delete comments."));
        }
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Error: Post not found."));
    }

    mysqli_close($conn);
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Bad request: postId is missing or empty."));
}
?>

<?php
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$current_user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
      rel="stylesheet"
    />
    <script
      src="https://kit.fontawesome.com/ecb4fa4f8c.js"
      crossorigin="anonymous"
    ></script>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="css/homepage.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
  </head>
  <body>
    <?php include 'navbar.php'; ?>

    <div class="container friend-container">
      <div class="component">
      </div>
      <!-- end of component 1 -->
      <div class="component">

      <div class="inner-component friend-requests">
          <div class="title">Pending Requests</div>
          

          <div class="new-users">
          <?php


$friend_requests = $conn->prepare("SELECT * FROM friend_requests JOIN users ON friend_requests.recipient_id = users.user_id WHERE sender_id = ? ");

$friend_requests->bind_param("i", $current_user_id);
$friend_requests->execute();
$result = $friend_requests->get_result();

if ($result->num_rows == 0) {
    echo "<div class='no-requests'> No pending requests </div>";
} else {

while ($row = $result->fetch_assoc()) {

    ?>
            <!-- user templates starts here -->
            <div class="user-template-request view-friends-page">
              <div class="new-user-component">
                <div class="new-user-image">
                  <img src="<?php echo $row['profile_image_url'] ?>" alt="" class="new-user-img" />
                </div>

                <div class="new-user-name">
                  <span> <?php echo $row['first_name'] . ' ' . $row['last_name'] ?></span>
                </div>
              </div>

              <div class="add-reject-friend friends-page" id=<?php echo "Friend" . $row['recipient_id'] ?>>
                <div class="add-user" >
                  <button onclick="confirmFriend( <?php echo $row['recipient_id'] ?>)">Add Friend</button>
                </div>

                <div class="reject-friend">
                  <button onclick="deleteFriend(<?php echo $row['recipient_id'] ?>">Reject User</button>
                </div>
              </div>

              <div class="dialog-message-<?php echo $row['recipient_id'] ?>)">

              
            </div>
            <?php }
            } ?>
         
          </div>
        </div>
      </div>
      <div class="component">
      </div>
    </div>
  </body>


<script src="js/home-page.js" defer></script>
</html>

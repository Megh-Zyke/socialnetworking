<?php
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$current_user_id = $_SESSION['user_id'];

// Fetch user bio from the database
$select_bio = $conn->prepare("SELECT bio , profile_image_url , first_name , last_name  FROM users WHERE user_id = ?");
$select_bio->bind_param("i", $current_user_id);
$select_bio->execute();
$select_bio->bind_result($default_bio, $profile, $first_name, $last_name);
$select_bio->fetch();
$select_bio->close();

$default_bio = trim($default_bio);

$friends_number = $conn->prepare("SELECT users.friends , COUNT(posts.post_id)  
                                FROM users
                                JOIN posts ON users.user_id = ? AND posts.user_id = ?");
$friends_number->bind_param("ii", $current_user_id, $current_user_id);
$friends_number->execute();
$friends_number->bind_result($friends_count_list, $posts_count);
$friends_number->fetch();
$friends_number->close();
$decoded_friends = json_decode($friends_count_list, true);
if ($decoded_friends == null) {
    $decoded_friends = array();
}
$friends_count = count($decoded_friends) ;
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

    <div class="container">
      <div class="component">
        <div class="inner-component user-info">
          <!-- user profile picture and quick info -->
          <div class="user-profile-info">
            <div class="user-posts-info">
              <div class="posts-info">
                <div class="count"><?php echo $posts_count ?></div>
                <div class="label">Posts</div>
              </div>
            </div>
            <div class="user-profile-image">
              <img src=<?php echo $profile ?>  alt="profile-image" class="profile-image" />
            </div>
            <div class="user-friends-info">
              <div class="count"><?php echo $friends_count ?></div>
              <div class="label">Friends</div>
            </div>
          </div>

          <!-- user name and bio -->
          <div class="user-name-bio">
            <div class="user-name">---  <?php echo $first_name . " " . $last_name ?> ---</div>
            <div class="user-bio">
              <textarea readonly class="bio" id="bio-info">
              <?php echo ($default_bio) ?>
            </textarea >
            </div>
          </div>

          <hr class="break-line" />

          <div class="view-profile-button">
            <button class="view-profile" onclick="openEditBioView()">Edit Bio</button>
          </div>
        </div>

        <div class="inner-component third-components requests">
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
            <div class="user-template-request">
              <div class="new-user-component">
                <div class="new-user-image">
                  <img src="<?php echo $row['profile_image_url'] ?>" alt="" class="new-user-img" />
                </div>

                <div class="new-user-name">
                  <span> <?php echo $row['first_name'] . ' ' . $row['last_name'] ?></span>
                </div>
              </div>

              <div class="add-reject-friend" id=<?php echo "Friend" . $row['recipient_id'] ?>>
                <div class="add-user" >
                  <button onclick="confirmFriend( <?php echo $row['recipient_id'] ?>)">Add Friend</button>
                </div>

                <div class="reject-friend">
                  <button onclick="deleteFriend(<?php echo $row['recipient_id'] ?>">Reject User</button>
                </div>
              </div>
            </div>
            <?php } }?>
            <!-- user template ends -->
          </div>
        </div>
      </div>

      <!-- end of component 1 -->

      <div class="component">
        <div class="inner-component">
          <div class="title">My Friends</div>

          <div class="new-users">
          <?php
                //Fetch users from database
                $ids_string = implode(',', $decoded_friends);
                $users = $conn->prepare("SELECT user_id , first_name , last_name , profile_image_url FROM users WHERE user_id IN ( $ids_string )");
                $users->execute();
                $users->bind_result($db_user_id, $fname, $lname, $profile_image_url);
                ?>
                <?php while ($users->fetch()) { 
                  ?>
            <!-- user templates starts here -->
            <div class="user-template">
              <div class="new-user-component">
                <div class="new-user-image">
                  <img src="<?php echo $profile_image_url; ?>"  alt="" class="new-user-img" />
                </div>

                <div class="new-user-name">
                  <span> <?php echo $fname . " " . $lname ?></span>
                </div>
              </div>

              <div class="accept-view-user">
                <div >
                <i></i>
                </div>
                <div class="search">
                  <i class="fa-solid fa-magnifying-glass" onclick="viewOtherUsers(<?php echo $db_user_id ?>)"></i>
                </div>
              </div>
            </div>
<?php } ?>
            <!-- user template ends -->

           
          </div>
        </div>

        <!-- template ends -->
      </div>
    </div>

    <div class="cover-element"></div>
    <div class="share-options-avaliable">
      <div class="close-button">
        <i class="fa-solid fa-x"></i>
      </div>

      <div class="share-text">
        <div class="share-text-def">How do you want to share the post?</div>
        <div id="share-text"></div>
      </div>

      <div class="options">
        <div class="share-option" id ="whatsappLogo">
          <i class="fa-brands fa-whatsapp" style="color: rgb(37, 211, 102)"  ></i>
        </div>

        <div class="share-option" id = "twitterButton">
          <i class="fa-brands fa-x-twitter"  ></i>
        </div>

        <div class="share-option" id ="copyLinkLogo" >
          <i class="fa-solid fa-copy" style="color: blue"></i>
        </div>
      </div>
    </div>


    <div class="edit-bio-view">
      <div class="editBioContainer">
        <div class="close-edit-bio">
          <i class="fa-solid fa-x" onclick="closeEditBio()"></i>
          </div>
        <div class="edit-bio-heading">
          <h2>Edit your bio !</h2>

          </div>
          <div class="previous-bio">
            <div class="previous-bio-heading">
              <h3>Previous Bio</h3>
            </div>
            <div class="previous-bio-data">
            </div>
            <div class="edit-bio-content">
          <div class="edit-bio-caption">
            <input type="hidden" class="bio-id-edit-bio" name="bioId" value = "">
            <textarea class="edit-bio-caption-text" id="edit-bio-caption" name = "bioText"></textarea>
          </div>
          <div class="edit-bio-image">
              <button class="media-button bio-btn" id="edit-bio-btn"  onclick = "saveBio()">Submit</button>
            </div>
          </div>
                  
          </div>  
        </div>
    </div>

    <div class="edit-post-view">
      <div class="edit-post-container">
       
        <div class="close-edit-post">
          <i class="fa-solid fa-x" onclick = "closeEditView()"></i>
        </div>
        <form action="update_post.php" method="post" enctype="multipart/form-data">
        <div class="edit-heading">
        <h2>Edit your post !</h2>
        </div>
        <div class="previous-caption">
          <div class="previous-caption-heading">
            <h3>Previous Caption</h3>
          </div> 
          <div class="previous-caption-data">

          </div>
        </div>
        <div class="edit-post-content">
          <div class="edit-post-caption">
            <input type="hidden" class="post-id-edit-post" name="postId" value = "">
            <textarea class="edit-post-caption-text" id="edit-post-caption" name = "postText"></textarea>
          </div>
       <div class="edit-post-image">
              <button class="media-button post-btn" id="edit-post-btn" type = "submit">Post</button>
            </div>
          </div>

          </form>
        </div>
      </div>


    </div>


    <?php include 'mobile_tabbar.php'; ?>
    <script src="js/home-page.js" defer></script>
<script src="js/user-page.js" defer></script>
  </body>



</html>

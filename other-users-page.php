<?php
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$current_user_id = $_SESSION['user_id'];


// Retrieve user_id from the URL
if (isset($_GET['user-id'])) {
    $selected_user = $_GET['user-id'];
} else {
    // Handle the case where user_id is not present in the URL
    header('Location: index.php');
    exit();
}

if ($current_user_id == $selected_user) {
    header('Location: user-page.php');
    exit();
}
// Fetch user bio from the database
$select_bio = $conn->prepare("SELECT bio , profile_image_url , first_name , last_name , friends FROM users WHERE user_id = ?");
$select_bio->bind_param("i", $current_user_id);
$select_bio->execute();
$select_bio->bind_result($default_bio, $profile, $first_name, $last_name , $friends_list);
$select_bio->fetch();
$select_bio->close();

$decoded_friends_user = json_decode($friends_list, true);

if ($decoded_friends_user == null) {
    $decoded_friends_user = array();
}

$select_other_bio = $conn->prepare("SELECT bio , profile_image_url , first_name , last_name  FROM users WHERE user_id = ?");
$select_other_bio->bind_param("i", $selected_user);
$select_other_bio->execute();
$select_other_bio->bind_result($user_bio, $user_profile, $user_first_name, $user_last_name);
$select_other_bio->fetch();
$select_other_bio->close();

$friends_number = $conn->prepare("SELECT users.friends , COUNT(posts.post_id)  
                                FROM users
                                JOIN posts ON users.user_id = ? AND posts.user_id = ?");

$friends_number->bind_param("ii", $selected_user, $selected_user);
$friends_number->execute();

$friends_number->bind_result($friends_count_list, $posts_count);
$friends_number->fetch();
$friends_number->close();
$decoded_friends = json_decode($friends_count_list, true);

if ($decoded_friends == null) {
    $decoded_friends = array();
    $decoded_friends[] = $selected_user;
}
$friends_count = count($decoded_friends) - 1;


$friends_number_user = $conn->prepare("SELECT friends FROM users WHERE user_id = ?");
$friends_number_user->bind_param("i", $current_user_id);
$friends_number_user->execute();
$friends_number_user->bind_result($friends_count_list_user);
$friends_number_user->fetch();
$friends_number_user->close();
$decoded_friends_user = json_decode($friends_count_list_user, true);
if ($decoded_friends_user == null) {
    $decoded_friends_user = array();
}


$requests_sent = $conn->prepare("SELECT sender_id FROM friend_requests WHERE recipient_id = ?;");
$requests_sent->bind_param("i", $current_user_id); 
$requests_sent->execute();
$requests_sent->bind_result($sender_id);

$requests_sent_array = [];
while ($requests_sent->fetch()) {
    $requests_sent_array[] = $sender_id;
}
// Check if the array is empty and initialize if needed
if (empty($requests_sent_array)) {
    $requests_sent_array = [];
}

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
    <link rel="stylesheet" href="css/user.css" />
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
              <img src=<?php echo $user_profile ?>  alt="profile-image" class="profile-image" />
            </div>
            <div class="user-friends-info">
              <div class="count"><?php echo $friends_count ?></div>
              <div class="label">Friends</div>
            </div>
          </div>

          <!-- user name and bio -->
          <div class="user-name-bio">
            <div class="user-name">---  <?php echo $user_first_name . " " . $user_last_name ?> ---</div>
            <div class="user-bio">
              <textarea readonly class="bio" id="">
              <?php echo  ltrim($user_bio) ?>
            </textarea
              >
            </div>
          </div>

          <hr class="break-line" />

          <div class="view-profile-button">
            <button class="view-profile">Viewing <?php echo $user_first_name . " " . $user_last_name."'s"?> Profile</button>
          </div>
        </div>
      </div>

      <!-- end of component 1 -->
      <div class="component">
        <!-- post content div  -->

        <form action="post.php" method="post" enctype="multipart/form-data" class="inner-component post-content">
  <div class="post-content-userinfo">
    <div class="post-content-userimage">
      <img src="<?php echo $profile ?>" alt="profile-image" class="post-user-image" />
    </div>

    <div class="post-info">
      <input
        type="text"
        name="post_content"
        class="post-info-input"
        placeholder="Tell your friends about your thoughts.."
      />
    </div>
  </div>
  
  <div id="previewContainer"></div>

  <div class="post-options">
    <div class="upload-media">
      <div class="image-btn">
        <input type="file" id="photoInput" name="photo" accept="image/*" style="display: none;" onchange="previewMedia(event, 'photo')">
        <button type="button" class="media-button img" onclick="document.getElementById('photoInput').click()">
          <i class="fa-solid fa-image" style="color: rgb(38, 255, 38)"></i>
          Photo
        </button>
      </div>

      <div class="video-btn">
        <input type="file" id="videoInput" name="video" accept="video/*" style="display: none;" onchange="previewMedia(event, 'video')">
        <button type="button" class="media-button img" onclick="document.getElementById('videoInput').click()">
          <i class="fa-solid fa-video" style="color: blue"></i>
          Video
        </button>
      </div>
    </div>

    <div class="image">
      <button type="submit" class="media-button post-btn">Post</button>
    </div>
  </div>
</form>
        <!-- end of post content div -->

        <!-- posts div begin here -->
        <div class="posts-container">
        <?php    

        //Fetch posts from database
        if (!in_array($selected_user, $decoded_friends_user)) {
            $get_friend = $conn->prepare("SELECT * FROM posts join users on users.user_id = ? and posts.user_id = ?  limit 3 ");
            } else {
                $get_friend = $conn->prepare("SELECT * FROM posts join users on users.user_id = ? and posts.user_id = ? ");
            }
            $get_friend->bind_param("ii", $selected_user , $selected_user);
            $get_friend->execute();
            $result = $get_friend->get_result();
            
        $posts = array();

        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }

        if (empty($posts)) {
            echo "<div class='inner-component post'>No posts available</div>";
        }
        else {
        foreach (array_reverse($posts) as $row) {
        ?>

          <!-- single post contianer -->
          <div class="inner-component post">
            <div class="post-user-info">
              <div class="post-uploader-img">
                <img src=<?php echo $row['profile_image_url']; ?> alt="" class="post-uploader-image" />
              </div>

              <div class="uploader-username">
                <div class="username"><?php echo $row['first_name'] . ' ' . $row['last_name'] ?></div>

                <div class="time-upload">
                  <ul>
                    <li>
                      <div class="date">
                        <span><?php
                                $postDate = new DateTime($row['post_date']);
                                echo $postDate->format('d-m-Y');
                                ?></span>
                        <span><?php
                            
                                ?></span>
                      </div>
                    </li>
                  </ul>
                </div>

              
              </div>
            </div>

            <div class="post-content-info">
              <div class="post-caption">
                <span>
                <?php if (!empty($row['post_content'])) { ?>


<div class="post_description">
    <p class="post_description_text">
        <?php echo $row['post_content']; ?>
    </p>
</div>
<?php } ?>

                </span>
              </div>

              <?php
$postMediaPath = !empty($row['post_image_path']) ? $row['post_image_path'] : '';
$mediaExtension = pathinfo($postMediaPath, PATHINFO_EXTENSION);
?>

<?php if (!empty($postMediaPath)) { ?>
    <div class="post-media">
      <?php if (strtolower($mediaExtension) === 'mp4') { ?>
       
        <video src="<?php echo $postMediaPath; ?>" controls class="media-post-styles"  onclick="navigateToPostDetails(<?php echo $row['post_id']; ?>)"></video>
      <?php } else { ?>
        <img src="<?php echo $postMediaPath; ?>" alt="Error getting the Post" class="media-post-styles"  onclick="navigateToPostDetails(<?php echo $row['post_id']; ?>)" />
      <?php } ?>
   
    </div>
  <?php } ?>
            </div>
            

            <div class="post-reactions">
            <?php

$likes_array = $row['likes'];
$likes_array = json_decode($likes_array, true);
$like_count = count($likes_array);
if ($likes_array == null) {
    $likes_array = array();
}

$color = " #f1f0f0;";
$user_index = array_search($current_user_id, $likes_array);

if ($user_index !== false) {
    $color = " #fffd00";
} 
?>
              <div class="like"  onclick="addLike(<?php echo $row['post_id'] ?>); changeColor( <?php echo $row['post_id'] ?>);"> <i class="fa-solid fa-heart"  id=<?php echo 'like' . $row['post_id'] ?>
              style="color :  <?php echo $color ?> " ></i></div>
              <div class="comment" onclick="openComments('<?php echo 'postID' . $row['post_id']; ?>')"><i class="fa-solid fa-comment"></i></div>
              <div class="share" id="share-button" onclick = "openShareDiv('<?php echo $row['post_id']; ?>')">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
              </div>
            </div>
            <hr />

            <div class="post-comments">
              <div class="post-content-userimage">
                <img src=<?php echo $profile ?>  alt="" class="post-user-image" />
              </div>

              <div class="comment-text-field" >
                <input
                  type="text"
                  class="comment-field"
                  placeholder="Write your comment"
                  id = "comment-text-field-<?php echo $row['post_id']; ?>"
                />

                <button class="comment-button"  onclick="postComment(<?php echo $row['post_id']; ?>)" >Comment</button>
                  
              </div>
           
            </div>
            <div class="comment-sent" id  = "<?php echo 'commented'.$row['post_id'] ?>">
                    <span>Commented!</span>
              </div>


               <!-- view comments div  -->
            <div class="view-all-comments" id="<?php echo "postID".$row['post_id']; ?>">
          <div class="close-comments" onclick = "closeComments('<?php echo 'postID'.$row['post_id']; ?>')">
            <i class="fa-solid fa-x"></i>
          </div>
                
         
           <div class="viewcomments" >

           <?php
$get_comments = $conn->prepare("SELECT c.*, u.first_name, u.last_name, u.profile_image_url 
FROM comments c 
LEFT JOIN users u ON c.user_id = u.user_id 
WHERE c.post_id = ?
ORDER BY c.comment_date DESC");
$get_comments->bind_param("i", $row['post_id']);
$get_comments->execute();
$result_comments = $get_comments->get_result();
$comment_count = $result_comments->num_rows; // Get the number of rows

if ($comment_count > 0) {
    while ($comment = $result_comments->fetch_assoc()) { 
        $commentDate = new DateTime($comment['comment_date']);
        $formattedDate = $commentDate->format('d-m-Y g:i a'); ?>

    
        <!-- comment -->
        <div class="view-comment">
            <div class="view-comment-user-image">
                <img src="<?php echo $comment['profile_image_url']; ?>" alt="" class="comment-user-img" />
            </div>
            <div class="view-comment-user-info">
                <div class="view-comment-username"><?php echo ucwords(strtolower($comment['first_name'] . ' ' . $comment['last_name'])); ?></div>
                <div class="view-comment-time"><?php echo $formattedDate; ?></div>
                <div class="view-comment-text">
                    <?php echo $comment['comment_text']; ?>
                </div>
            </div>
        </div>
    <?php }
} else { ?>
    <div class="view-comment">
        No comments
    </div>
<?php } 
$get_comments->close();
?>

            <!-- comment ends -->
          </div>
        </div>
        <!-- comments div ends here -->
          </div>
          <?php  } } ?>

          <!-- single post ends here -->


          <?php if (!in_array($selected_user, $decoded_friends_user)) { ?>
          <div class='inner-component post locked-message'>  
          <div class="lock-image">
          <i class="fa-solid fa-lock"></i>
          </div>  
          <div class="lock-image">
          Become Friends to view all posts
          </div>
         </div>
            <?php } ?>
        </div>
          

        <!-- posts div end here -->
      </div>
      <div class="component">
        <div class="inner-component third-components">
          <div class="title">New Users Onboard</div>

          <div class="new-users">
          <?php

                //Fetch users from database
                $users = $conn->prepare("SELECT user_id, first_name, last_name, profile_image_url
                FROM users u
                LEFT JOIN friend_requests fr ON u.user_id = fr.recipient_id AND fr.status = 'pending'
                WHERE user_id != ?
                AND u.approved = 1
                AND fr.request_id IS NULL -- Exclude users with pending friend requests
                ORDER BY user_id DESC 
                LIMIT 5;");
                $users->bind_param("i", $current_user_id);
                $users->execute();
                $users->bind_result($db_user_id, $fname, $lname, $profile_image_url);
               
                ?>                
                <?php
                 while ($users->fetch()) { 
                  if(!in_array($db_user_id, $decoded_friends_user))  {
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

                <?php 
                if (!in_array($db_user_id, $requests_sent_array)) {
                ?>
                <div class="add-friend" onclick="addFriend(<?php echo $db_user_id ?>)">
                <i id="<?php echo $db_user_id ?>"
                class="fa-solid fa-plus <?php echo $db_user_id ?>"></i>
                </div>

                <?php } else { ?>
                  <div class="add-friend">
                <i id="<?php echo $db_user_id ?>"
                class="fa-solid fa-paper-plane <?php echo $db_user_id ?>"></i>
                </div>
                <?php } ?>

                <div class="search">
                  <i class="fa-solid fa-magnifying-glass"  onclick="viewOtherUsers(<?php echo $db_user_id ?>)"></i>
                </div>
              </div>
            </div>
<?php } }?>
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

    <?php include 'mobile_tabbar.php'; ?>
  </body>


<script src="js/home-page.js" defer></script>
</html>

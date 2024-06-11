
<?php
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
  $image_url_profile = "images/profile.png";
}
else {
  $get_user = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
  $get_user->bind_param("i", $_SESSION['user_id']);
  $get_user->execute();
  $user = $get_user->get_result()->fetch_assoc();
  $image_url_profile = $user['profile_image_url'];
}


// Function to extract numeric part from a string
function extract_number($string) {
    preg_match_all('/\d+/', $string, $matches);
    return isset($matches[0]) ? (int)$matches[0][0] : null;
}

// Get the post ID from the URL parameter
if(isset($_GET['post_id'])) {
    $post_id_series = $_GET['post_id']; // Series of characters
    $post_id = extract_number($post_id_series); // Extract numeric part from the series

    // Fetch post details
    $get_post_details = $conn->prepare("SELECT p.*, u.first_name, u.last_name, u.profile_image_url 
        FROM posts p 
        JOIN users u ON p.user_id = u.user_id 
        WHERE p.post_id = ?");
    $get_post_details->bind_param("i", $post_id);
    $get_post_details->execute();
    $post_details = $get_post_details->get_result()->fetch_assoc();

    // Fetch comments for the post
    $get_comments = $conn->prepare("SELECT c.*, u.first_name, u.last_name, u.profile_image_url 
        FROM comments c 
        JOIN users u ON c.user_id = u.user_id 
        WHERE c.post_id = ?
        ORDER BY c.comment_date DESC");
    $get_comments->bind_param("i", $post_id);
    $get_comments->execute();
    $comments = $get_comments->get_result()->fetch_all(MYSQLI_ASSOC);

    // Close prepared statements
    $get_post_details->close();
    $get_comments->close();
} else {
    // Handle case where post ID is not provided
    echo "Error: Post ID not provided.";
    exit();
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
    <link rel="stylesheet" href="css/homepage.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
  </head>
  <body>
    <div class="container">
      <div class="component"></div>
    

    <!-- end of component 1 -->
    <div class="component">
      <!-- post content div  -->

      <div class="inner-component post-content">
        <!-- posts div begin here -->
        <div class="posts-container">
          <!-- single post contianer -->
          <div class="inner-component post">
            <div class="post-user-info">
              <div class="post-uploader-img">
                <img  src="<?php echo $post_details['profile_image_url']; ?>" alt="Profile Picture" class="post-uploader-image" />
              </div>

              <div class="uploader-username">
                <div class="username"><?php echo $post_details['first_name'] . ' ' . $post_details['last_name']; ?></div>

                <div class="time-upload">
                  <ul>
                    <li>
                      <div class="date">
                        <span><?php
                                $postDate = new DateTime($post_details['post_date']);
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
                <?php if (!empty($post_details['post_content'])) { ?>


<div class="post_description">
    <p class="post_description_text">
        <?php echo $post_details['post_content']; ?>
    </p>
</div>
<?php } ?>


                </span>
              </div>
              <?php
$postMediaPath = !empty($post_details['post_image_path']) ? $post_details['post_image_path'] : '';
$mediaExtension = pathinfo($postMediaPath, PATHINFO_EXTENSION);
?>

<?php if (!empty($postMediaPath)) { ?>
    <div class="post-media">
      <?php if (strtolower($mediaExtension) === 'mp4') { ?>
        <video src="<?php echo $postMediaPath; ?>" controls class="media-post-styles"></video>
      <?php } else { ?>
        <img src="<?php echo $postMediaPath; ?>" alt="Error getting the Post" class="media-post-styles" />
      <?php } ?>
    </div>
  <?php } ?>
            </div>
            

            <div class="post-reactions">
              <div class="like"  onclick="addLike(<?php echo $post_details['post_id'] ?>); changeColor(<?php echo $post_details['post_id'] ?>);"> <i class="fa-solid fa-heart"  id=<?php echo 'like' . $post_details['post_id'] ?> ></i></div>
              <div class="comment" onclick="openComments('<?php echo 'postID' . $post_details['post_id']; ?>')"><i class="fa-solid fa-comment"></i></div>
              <div class="share" id="share-button" onclick = "openShareDiv('<?php echo $post_details['post_id']; ?>')">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
              </div>
            </div>

            <hr />

            <div class="post-comments">
              <div class="post-content-userimage">
                <img src="<?php echo $image_url_profile ?>" alt="" class="post-user-image" />
              </div>

              <div class="comment-text-field" >
                <input
                  type="text"
                  class="comment-field"
                  placeholder="Write your comment"
                  id = "comment-text-field-<?php echo $post_details['post_id']; ?>"
                />

                <button class="comment-button"  onclick="postComment(<?php echo $post_details['post_id']; ?>)" >Comment</button>

              </div>
            </div>


               <!-- view comments div  -->
            <div class="view-all-comments" id="<?php echo "postID".$post_details['post_id']; ?>">
          <div class="close-comments" onclick = "closeComments('<?php echo 'postID'.$post_details['post_id']; ?>')">
            <i class="fa-solid fa-x"></i>
          </div>
                
         
           <div class="viewcomments" >

           <?php
$get_comments = $conn->prepare("SELECT c.*, u.first_name, u.last_name, u.profile_image_url 
FROM comments c 
LEFT JOIN users u ON c.user_id = u.user_id 
WHERE c.post_id = ?
ORDER BY c.comment_date DESC");
$get_comments->bind_param("i", $post_details['post_id']);
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
          
       
          <!-- single post ends here -->
                  <!-- single post ends here -->
        </div>
        <!-- posts div end here -->
      </div>
      <div class="component"></div>
    </div>

    <div class="cover-element"></div>
    <div class="share-options-avaliable">
      <div class="close-button" onclick = "closeShareView()">
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
    
  </body>
  <script src = "js/post-details.js">
  </script>
</html>



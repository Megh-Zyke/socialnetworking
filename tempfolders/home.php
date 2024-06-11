<?php
    include 'connection.php';   
    
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    $current_user_id = $_SESSION['user_id'];

    

    // Fetch user bio from the database
    $select_bio = $conn->prepare("SELECT bio , profile_image_url  FROM users WHERE user_id = ?");
    $select_bio->bind_param("i", $current_user_id);
    $select_bio->execute();
    $select_bio->bind_result($default_bio , $profile);
    $select_bio->fetch();
    $select_bio->close();
    ?>


<!DOCTYPE html>
<html>

<head>
    <title></title>
    <style>
    /* CSS styles for the user profile page */
    /* Add your custom styles here */
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/user2.css">
    <script src="https://kit.fontawesome.com/ecb4fa4f8c.js" crossorigin="anonymous"></script>

    <script>
    function openPosts() {
        console.log("open");
        document.querySelector(".addPostsPage").style.display = "block";
    }

    function closePage() {
        document.querySelector(".addPostsPage").style.display = "none";
    }
    </script>

</head>

<body>
    <header>
        <!-- User profile header section -->
        <!-- Add your header content here -->
    </header>

    <main>

        <div class="mainPage">
            <div class="userInformation">
                
            <div class="friendRequests">
                       
                       <div class="heading"> 
                           <div> <span> New Users Onboard !! </span> </div>           
                       </div>
   
                       <?php 
                           //Fetch users from database
                           $users = $conn->prepare("SELECT user_id , first_name , last_name , profile_image_url FROM users WHERE user_id != ? ORDER BY user_id DESC LIMIT 3 ");
                           $users->bind_param("i", $current_user_id);
                           $users->execute();
                           $users->bind_result( $db_user_id , $first_name, $last_name,  $profile_image_url);
   
                       ?>
   
                       <div class="requests">
                           <?php while ($users->fetch()) { ?>
   
                           <!-- <form action="send_requests.php" method="post"> -->
                           
                           <div class="friendRequest">
                               <div class="friendRequestImage">
                                   <img src="<?php echo $profile_image_url; ?>" alt="profile picture" class="userImage">
                               </div>
   
                               <div class="friendRequestInfo">
                                   <h2> <?php echo $first_name ." " .$last_name ?> </h2>
                                   <button class="acceptButton" onclick = "addFriend(<?php echo $db_user_id ?>)" > <i id = "<?php echo $db_user_id ?>" class="fa-solid fa-plus <?php echo $db_user_id ?>"></i></button>
   
                                   <!-- <button class="declineButton"> <i class="fa-solid fa-cancel"></i> </button> -->
                               </div>
                           </div>
   
                           <!-- </form> -->
                           
                           <?php } ?>
                       </div>
   
                   </div>

                <div class="friendRequests">

                    <div class="heading">
                        <div> <span> Friend Requests </span> </div>
                        <div> <span class="requests">See All Requests</span></div>

                    </div>

                    <div class="requests">

                   <?php 

    
                    $friend_requests = $conn->prepare("SELECT * FROM friend_requests JOIN users ON friend_requests.recipient_id = users.user_id WHERE sender_id = ? ");

                    $friend_requests->bind_param("i", $current_user_id);
                    $friend_requests->execute();
                    $result = $friend_requests->get_result();

                    while ($row = $result->fetch_assoc()) {
                   ?>
                        <div class="friendRequest">
                            <div class="friendRequestImage">
                                <img src="<?php echo $row['profile_image_url']?>" alt="profile picture" class="userImage">
                            </div>

                            <div class="friendRequestInfo">
                                <h2> <?php echo $row['first_name'].' '.$row['last_name'] ?> </h2>
                                <button class="acceptButton" onclick = "confirmFriend( <?php echo $row['recipient_id']?>)"> <i class="fa-solid fa-check"></i></button>
                                <button class="declineButton"> <i class="fa-solid fa-cancel"></i> </button>
                            </div>

                            <div class="accepted">
                                <button class = "acceted_friend"> Hooray! <br> You and <?php echo $row['first_name']?> are now Friends  </button>
                            </div>

                            <div class="not_accepted">
                                <button class = "reject_friend"> You and <?php echo $row['first_name']?> are not Friends  </button>
                            </div>
                        </div>

             
                        <?php } ?>
                    </div>
                    
                </div>



                <div class="friends">
                    <div class="heading">
                        <div> <span> Friends </span> </div>
                        <div> <span class="requests">See All Friends</span></div>
                    </div>


                    <div class="friendList">
                        
                    <?php 

$friends = $conn->prepare("SELECT friends FROM users WHERE user_id = ? ");
$friends->bind_param("i", $current_user_id);
$friends->execute();
$friends->bind_result($friends_list);
$friends->fetch();

$friends_array = json_decode($friends_list, true);

if ($friends_array == null) {
    $friends_array = array();
}
$friends->close();

foreach ($friends_array as $friend) {
    $get_friend = $conn->prepare("SELECT * FROM users WHERE user_id = ? ");
    $get_friend->bind_param("i", $friend);
    $get_friend->execute();
    $result = $get_friend->get_result();
    $row = $result->fetch_assoc();

?>

<div class="friend">
    <div class="friendImage">
        <img src="<?php echo $row['profile_image_url']; ?>" alt="profile picture" class="userImage">
    </div>

    <div class="friendsInfo">
        <h2><?php echo $row['first_name']. ' ' . $row['last_name'] ?></h2>
    </div>

</div>

<?php } ?>
                     
                    </div>
                </div>

                <div>

                </div>

            </div>





            <div class="usersPosts">

                <div class="addPosts">
                    <div class="addPost">
                        <div class="profileIconDiv">
                            <img src="<?php echo $profile ?>" alt="" class="profileIcon">
                        </div>

                        <div class="PostsAdd">
                       
                            <div class="Buttons">
                                <div>

                                    <button onclick=openPosts()> <i class="fa-solid fa-image"></i> Photo</button>

                                </div>

                                <div>
                                    <button onclick=openPosts()> <i class="fa-solid fa-video"></i> Video</button>
                                </div>
                            </div>

                        </div>

                    </div>


                </div>

                <div class="usersPosts">

                    <?php
                    $sql = "SELECT * 
                    FROM posts
                    JOIN users ON posts.user_id = users.user_id
                    ORDER BY posts.post_date DESC";


$result = $conn->query($sql);

$posts = array();

while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}

foreach ($posts as $row) {
    $postId = isset($row['post_id']) ? $row['post_id'] : null;
?>
                    <div class="post">
                        <div class="userDetails">
                            <div class="userImageDiv">
                                <img src=<?php echo $row['profile_image_url']; ?>  alt="profile picture" class="userImageImg">
                            </div>
                            <div>
                                <div class="userName">
                                    <h2><?php echo $row['first_name']. ' ' . $row['last_name'] ?></h2>
                                </div>
                                <div class="date">
                                    <?php
                                    $postDate = new DateTime($row['post_date']);
                                    echo $postDate->format('d-m-Y');
                        ?>
                                </div>
                            </div>
                        </div>

                        <div class="Post">
                            <?php
                if (!empty($row['post_image_path'])) {
                ?>
                            <div class="postImage">
                                <img src="<?php echo $row['post_image_path']; ?>" alt="Post Image">
                            </div>
                            <?php
                }
                ?>

                            <?php if (!empty($row['post_content'])) { ?>
                            <p><?php echo $row['post_content']; ?></p>
                            <?php } ?>
                        </div>
                        <div class="likeComments">
                            <div class="like">
                                <button> <i class="fa-solid fa-heart"></i> </button>
                            </div>
                            <div class="comments">
                                <button onclick="showComments(<?php echo $postId; ?>)"> <i
                                        class="fa-solid fa-comment"></i> </button>
                            </div>
                            <div class="share">
                                <button> <i class="fa-solid fa-share"></i> </button>
                            </div>
                        </div>

                        <!-- Comments Section -->
                        <div id="commentsSection_<?php echo $postId; ?>" style="display: none;">
                            <?php
                if ($postId !== null) {
                   // $comments = getComments($conn, $postId);
                    foreach ($comments as $comment) {
                ?>
                            <div class="comment">
                                <p><?php echo $comment['content']; ?></p>
                            </div>
                            <?php
                    }
                }
                ?>
                            <div class="commentForm">
                                <form action="add_comment.php" method="post">
                                    <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
                                    <input type="text" name="comment" placeholder="Add a comment">
                                    <input type="submit" value="Comment">
                                </form>
                            </div>
                        </div>

                    </div>

                    <?php
    }
    ?>
                </div>
                <div class="addPostsPage">
                    <div class="close">
                        <i class="fas fa-times" onclick="closePage()"></i>
                    </div>

                    <form action="post.php" method="post" enctype="multipart/form-data">
                        <div class="addPostsHeading">
                            <h1> Add Post </h1>
                        </div>

                        <div id="postForm">
                            <textarea name="postText" id="postText" placeholder="What's on your mind?"></textarea>


                            <img id="imagePreview" alt="Image Preview" />
                            <input type="file" name="mediaButton" id="mediaButton" accept="image/*" />

                            <button class="submitPost" type="submit">Post</button>
                        </div>
                    </form>
                </div>

    </main>



    <footer>
        <!-- User profile footer section -->
        <!-- Add your footer content here -->
    </footer>
    <script>
    document.getElementById('mediaButton').addEventListener('change', previewImage);

    function previewImage() {
        const imageInput = document.getElementById('mediaButton');
        const imagePreview = document.getElementById('imagePreview');

        if (imageInput.files && imageInput.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            };

            reader.readAsDataURL(imageInput.files[0]);
        }
    }
    </script>

<script src="JS/userPage.js"></script>


</body>

</html>
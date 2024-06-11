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

    <div class="container">
      <div class="component">
      </div>
      <div class="component">
        <div class="inner-component third-components users-component">
          <div class="title">Get users</div>

          <div class="new-users">
  <?php 
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $search_query_name = $_GET['search-input'];
    if (empty($search_query_name)) {
        $search_query = "";
    }
    $search_query = '%' . $search_query_name . '%';

    $search_users = $conn->prepare("SELECT user_id, first_name, last_name, profile_image_url FROM users WHERE first_name LIKE ? OR last_name LIKE ? order by user_id desc");
    $search_users->bind_param("ss", $search_query, $search_query);
    $search_users->execute();
    $search_users->bind_result($user_id, $first_name, $last_name, $profile_image_url);

    $users_found = false; // Track if any users were found

    while ($search_users->fetch()) {
        $users_found = true;
  ?>
            <!-- user templates starts here -->
            <div class="user-template">
              <div class="new-user-component">
                <div class="new-user-image">
                  <img src="<?php echo $profile_image_url; ?>"  alt="" class="new-user-img" />
                </div>

                <div class="new-user-name">
                  <span> <?php echo $first_name . " " . $last_name ?></span>
                </div>
              </div>

              <div class="accept-view-user">
                <div class="add-friend" onclick="addFriend(<?php echo $user_id ?>)">

                <?php  
                 if ($user_id == $current_user_id) {
                  
                ?>
                <i id="<?php echo $user_id ?>" class="fa-solid fa-face-smile-wink <?php echo $user_id ?>"></i>
                <?php
                } else { ?>
                <i id="<?php echo $user_id ?>"
                class="fa-solid fa-plus <?php echo $user_id ?>"></i>
                <?php
                }
                ?>
                </div>
                <div class="search">
                  <i class="fa-solid fa-magnifying-glass" onclick="viewOtherUsers(<?php echo $user_id ?>)"></i>
                </div>
              </div>
            </div>
<?php 
    }
    if (!$users_found) {
        echo '<div class="no-users-message">There is no user named '.$search_query_name.'</div>';
    }
}
?>
            <!-- user template ends -->
          </div>
        </div>
        <!-- template ends -->
      </div>
    </div>

      <div class="component">
      </div>
    </div>
  </body>

<script src="js/home-page.js" defer></script>
</html>

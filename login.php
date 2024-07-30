<?php
include 'connection.php';

if (isset($_SESSION['user_id'])) {
    header('Location: home-page.php');
    exit();
}

$user_id = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : '';

if (isset($_POST['submit'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $password = $_POST['password'];  

    // Prepare a statement to select user by email
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
    $select_user->bind_param("s", $email);
    $select_user->execute();
    $result = $select_user->get_result();
    
    // Fetch the row
    $row = $result->fetch_assoc();
    
    if ($result->num_rows > 0) {
        // Verify password using password_verify function
        if (password_verify($password, $row['password'])) {
            if ($row['approved']) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['user_name'] = $row['first_name'];
                
                header('location: home-page.php');
                exit();
            } else {
                $error_message = 'Need to be approved by admin';
            }
        } else {
            $error_message = 'Incorrect username or password!';
        }
    } else {
        $error_message = 'Incorrect username or password!';
    }
}

// Debugging: Display session and error message

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="css/login.css" rel="stylesheet">
    <style>
    .error-message {
        color: red;
        font-weight: bold;
        margin-bottom: 10px;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="left-side">
            <h1>Welcome Back !</h1>
            <div class="upload-btn-wrapper">
            </div>
        </div>
        <div class="right-side">
            <h2>Login </h2>

            <div class="error-message">
                <?php
                if (isset($error_message)) {
                    echo $error_message;
                }
                ?>
            </div>

            <form method="post" autocomplete = "off">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="submit" class="submit">Login</button>
                <div id="forgot-password">
                    <a href="forget-password-page.php" class="forgot-password">Forgotten Password?</a>
                </div>
                <hr>
                <button type="button" class="create"><a href="register.php">Create Account</a></button>
            </form>
        </div>
    </div>
</body>

</html>

<?php
include 'connect.php';
session_start();

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $password = filter_var($password, FILTER_SANITIZE_STRING);

    $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE email = ?");
    $select_admin->execute([$email]);

    if ($select_admin->rowCount() > 0) {
        $fetch_admin = $select_admin->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password, $fetch_admin['password'])) {
            $_SESSION['admin_id'] = $fetch_admin['id'];
            header('Location: admin_dashboard.php');
            exit();
        } else {
            $error_message = 'Incorrect password!';
        }
    } else {
        $error_message = 'Incorrect email!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="login.css" rel="stylesheet">
</head>
<body>
    <?php if (isset($error_message)): ?>
        <p><?php echo $error_message; ?></p>
    <?php endif; ?>
    <div class="container">
        <div class="left-side">
            <h1>Welcome Back Admin!</h1>
        </div>
        <div class="right-side">
            <h2>Login</h2>
            <form action="#" method="post" autocomplete="off">
                <input type="email" name="email" placeholder="Email" required autocomplete="off">
                <input type="password" name="password" placeholder="Password" required autocomplete="off">
                <button type="submit" name="submit" class="submit">Login</button>
                <div id="forgot-password">
                    <a href="#" class="forgot-password">Forgotten Password?</a>
                </div>
                <hr>
            </form>
        </div>
    </div>
</body>
</html>

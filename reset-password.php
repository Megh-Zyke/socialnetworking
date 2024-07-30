<?php
include 'connection.php';

$error = '';
$success = '';
$usercode = isset($_GET['code']) ? mysqli_real_escape_string($conn, $_GET['code']) : '';
$default = '0';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = isset($_POST['password']) ? mysqli_real_escape_string($conn, $_POST['password']) : '';
    $confirmPassword = isset($_POST['confirm-password']) ? mysqli_real_escape_string($conn, $_POST['confirm-password']) : '';
    $usercode = isset($_POST['code']) ? mysqli_real_escape_string($conn, $_POST['code']) : '';

    $sql = "SELECT email FROM users WHERE code = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $error = "Error preparing statement: " . $conn->error;
        exit();
    }

    $stmt->bind_param("s", $usercode);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email);
        $stmt->fetch();
        $stmt->close();

        if ($newPassword !== $confirmPassword) {
            $error = "New password and confirm password do not match.";
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);    
            $updateSql = "UPDATE users SET password = ?, code = ? WHERE email = ?";
            $updateStmt = $conn->prepare($updateSql);
    
            if (!$updateStmt) {
                $error = "Error preparing update statement: " . $conn->error;
                exit();
            }
    
            $updateStmt->bind_param("sss", $hashedPassword, $default, $email);
            $updateStmt->execute();
    
            if ($updateStmt->affected_rows > 0) {
                $success = "Your password has been successfully reset.";
                $redirect = true;  // Flag for successful reset
            } else {
                $error = "Failed to update password. Please try again.";
            }
    
            $updateStmt->close();
        }
    } else {
        $error = "Invalid reset code.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/reset-password.css">
    <script>
        function redirectToLogin() {
            setTimeout(function() {
                window.location.href = 'login.php';
            }, 5000); // Redirect after 5 seconds
        }
    </script>

    <link rel="stylesheet" href="reset-password.css">
</head>
<body>
    <div class="reset-password">
        <div class="reset-password-form">
            <h1>Reset Password</h1>
            <?php if (!empty($error)) : ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <?php if (!empty($success)) : ?>
                <p class="success-message"><?php echo htmlspecialchars($success); ?></p>

                <p>Redirecting you to the Login!</p>
                <script>
                    redirectToLogin(); 
                </script>
            <?php endif; ?>

            <form action="reset-password.php" method="POST" class="password">
                <input type="hidden" name="code" value="<?php echo htmlspecialchars($usercode); ?>">
                <div class="form-contents">
                    <div class="password-details">
                        <label for="password">Enter New Password</label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <div class="confirm-password">
                        <label for="confirm-password">Confirm New Password</label>
                        <input type="password" name="confirm-password" id="confirm-password" required>
                    </div>
                    <div class="btn">
                        <button type="submit">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

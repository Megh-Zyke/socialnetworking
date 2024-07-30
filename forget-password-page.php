<?php
include 'connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

function generateCode($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';

    $sql = "SELECT email FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "Error: " . $conn->error;
        exit();
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $resetCode = generateCode();

        $updateSql = "UPDATE users SET code = ? WHERE email = ?";
        $updateStmt = $conn->prepare($updateSql);

        if (!$updateStmt) {
            echo "Error: " . $conn->error;
            exit();
        }

        $updateStmt->bind_param("ss", $resetCode, $email);
        $updateStmt->execute();
        $updateStmt->close();
        try {
            include 'emailsmtp.php';

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
            $mail->Port       = 587;    
            $mail->setFrom('noreplyy@example.com');
            $mail->addAddress($email);     
   
            $mail->isHTML(false);                                
            $mail->Subject = 'Password Reset Code';
            $mail->Body    = 'Looks like you forgot your password. No worries! Click on the link below to reset your password. http://localhost/socialnetworking/reset-password.php?code=' . $resetCode;
        
            $mail->send();
            $success = "Password reset link has been sent to your email address. Please check your email!";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }        
    } else {
        $error = "Opps! Looks like you've entered a wrong email address. Please check it and try again!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
    <link rel="stylesheet" href="css/forget-password-page.css">
</head>
<body>
    <div class="forget-password-container">
        <div class="forget-password-form">
            <h1>Forget Password</h1>
            <?php if (!empty($error)) : ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>


            <?php if (!empty($success)) : ?>
                <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>

            <form action="forget-password-page.php" method="POST">
               <div class="form-contents">
               
               <div class="email-details">
               <label for="email">Enter your Email</label>
               <input type="email" name="email" id="email" required>

               </div>

               <div class="btn">
                <button type="submit">Submit</button>
               </div>
               </div>

               <a href="index.php" class="return">Know your password? Login!</a>
            </form>
        </div>
    </div>
</body>
</html>

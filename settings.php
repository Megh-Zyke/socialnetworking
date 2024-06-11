<!DOCTYPE html>
<html lang="en">
<?php

include 'header.php';
include 'navbar.php';
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="settings.css">
</head>

<body>
    <div class="container">
        <h2>Settings</h2>
        <form id="updatePasswordForm" action="update_password.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="1" size="5">

            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" placeholder="Current Password"
                size="30" required>

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" placeholder="New Password" required size="30">

            <button type="submit" id="updatePasswordBtn">Save Password Changes</button>
        </form>

        <form id="updateEmailForm" action="update_email.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="1" size="30">

            <label for="new_email">New Email:</label>
            <input type="email" id="new_email" name="new_email" placeholder="New Email" size="30" required>

            <button type="submit" id="updateEmailBtn">Save Email Changes</button>
        </form>

        <form id="updateProfileImageForm" action="update_profile_image.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="1">

            <label for="profile_image">Change Profile Image:</label>
            <input type="file" id="profile_image" name="profile_image" accept="image/*">

            <button type="submit" id="updateProfileImageBtn">Save Profile Image Changes</button>
        </form>

        <script>
        function handleFormSubmit(formId, successMessage) {
            document.getElementById(formId).addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(this);

                fetch(this.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert(successMessage);
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        }

        handleFormSubmit('updatePasswordForm', 'Password updated successfully!');
        handleFormSubmit('updateEmailForm', 'Email updated successfully!');
        handleFormSubmit('updateProfileImageForm', 'Profile image updated successfully!');
        </script>
    </div>
</body>

</html>
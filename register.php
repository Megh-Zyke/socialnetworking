<?php
include 'connection.php';

if (isset($_POST['submit'])) {
    $first_name = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
    $last_name = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $date_of_birth = $_POST['year'] . '-' . $_POST['month'] . '-' . $_POST['day'];
    $gender = filter_var($_POST['gender'], FILTER_SANITIZE_STRING);

    $dateOfBirth = new DateTime($date_of_birth);
    $today = new DateTime();
    $age = $today->diff($dateOfBirth)->y;

    // Check if age is greater than 12
    if ($age <= 12) {
        echo 'You must be at least 13 years old to register.';
        exit(); // Stop further execution if age requirement is not met
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'Invalid email address!';
        exit(); // Stop further execution if email is invalid
    }

    $targetDir = 'profileImage/';
    if (!file_exists($targetDir) && !is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $profile_image_url = '';
    if ($_FILES["fileInput"]['error'] == 0) {
        $fileName = basename($_FILES["fileInput"]['name']);
        $targetFile = $targetDir . $fileName;
        move_uploaded_file($_FILES["fileInput"]['tmp_name'], $targetFile);
        $profile_image_url = $targetFile;
    } else {
        
        if ($gender === 'male') {
            $profile_image_url = 'profileImage/male.jpg'; 
        } elseif ($gender === 'female') {
            $profile_image_url = 'profileImage/female.jpg'; 
        } else {
            $profile_image_url = 'profileImage/custom.jpg';
        }
    }

    if ($profile_image_url === '') {
        echo 'Error uploading image. Please try again.';
        exit();
    }
    
    $select_user = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $select_user->bind_param("s", $email);
    $select_user->execute();
    $select_user->store_result();
    
    if ($select_user->num_rows > 0) {
        echo 'Email already exists!';
    } else {
        $bio = "Hi " . $first_name . " " . $last_name;
        $insert_user = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, date_of_birth, gender, profile_image_url,bio, approved) VALUES (?, ?, ?, ?, ?,?, ?, ?, ?)");
        $approved = 0; // Assuming $approved is defined elsewhere
        $insert_user->bind_param("ssssssssi", $first_name, $last_name, $email, $password, $date_of_birth, $gender, $profile_image_url,$bio, $approved);
        $insert_user->execute();
    

        if ($insert_user->affected_rows > 0) {
            $_SESSION['user_id'] = $conn->insert_id;

            
            // Check if the user needs admin approval
            if ($_POST['gender'] === 'custom') {
                header('Location: admin_panel.php?registration_success=1');
            } else {
                header('Location: registration_pending.php');
            }
            
            exit();
        } else {
            echo 'Registration failed. Please try again.';
            echo $conn->error;
        }
    }
}

function uploadImage() {
    $targetDir = "proflieImages/";
    $uploadOk = 1;

    if (isset($_FILES["fileInput"]) && $_FILES["fileInput"]["name"]) {
        $targetFile = $targetDir . basename($_FILES["fileInput"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["fileInput"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        $allowedExtensions = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowedExtensions)) {
            echo  "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else 
        {
            if (move_uploaded_file($_FILES["fileInput"]["tmp_name"], $targetFile)) {
                echo "The file " . htmlspecialchars(basename($_FILES["fileInput"]["name"])) . " has been uploaded.";
                return $targetFile;
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    return '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <link href="register.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/2.7.0/cropper.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/2.7.0/cropper.min.css">

    <head>
        <script>
        function populateDropdowns() {
            var dayDropdown = document.getElementsByName("day")[0];
            var monthDropdown = document.getElementsByName("month")[0];
            var yearDropdown = document.getElementsByName("year")[0];

            var currentYear = new Date().getFullYear();
            var currentMonth = new Date().getMonth() + 1;
            var currentDay = new Date().getDate();

            for (var i = 1; i <= 31; i++) {
                var option = document.createElement("option");
                option.value = i;
                option.text = i;
                dayDropdown.add(option);
                if (i === currentDay) {
                    dayDropdown.selectedIndex = i - 1;
                }
            }

            var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September",
                "October", "November", "December"
            ];
            for (var j = 0; j < months.length; j++) {
                var option = document.createElement("option");
                option.value = j + 1;
                option.text = months[j];
                monthDropdown.add(option);
                if (j + 1 === currentMonth) {
                    monthDropdown.selectedIndex = j;
                }
            }

            for (var k = currentYear; k >= 1905; k--) {
                var option = document.createElement("option");
                option.value = k;
                option.text = k;
                yearDropdown.add(option);
                if (k === currentYear) {
                    yearDropdown.selectedIndex = currentYear - 1905;
                }
            }
        }

        function displayImage() {
            var fileInput = document.getElementById('fileInput');
            var image = document.getElementsByClassName('image')[0];
            var cropper;

            fileInput.addEventListener('change', function() {
                var file = fileInput.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        image.src = e.target.result;
                        if (cropper) {
                            cropper.destroy();
                        }
                        if (image.width > 200 || image.height > 200) {
                            cropper = new Cropper(image, {
                                aspectRatio: 1,
                                viewMode: 1,
                                dragMode: 'move',
                                crop: function(event) {}
                            });
                            cropper.crop();
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        function calculateAge() {
            var dobDay = document.getElementsByName("day")[0].value;
            var dobMonth = document.getElementsByName("month")[0].value;
            var dobYear = document.getElementsByName("year")[0].value;

            var dob = new Date(dobYear, dobMonth - 1, dobDay);
            var today = new Date();
            var age = today.getFullYear() - dob.getFullYear();
            var monthDiff = today.getMonth() - dob.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                age--;
            }

            var ageMessage = document.getElementById("age-message");
            if (age < 13) {
                ageMessage.innerText = "You must be at least 13 years old to register.";
            } else {
                ageMessage.innerText = "";
            }

            document.getElementById("age").innerHTML = "Age: " + age;
        }
        document.addEventListener("DOMContentLoaded", function() {
            populateDropdowns();
            displayImage();
            calculateAge();

            // Attach event listener to date dropdowns to update age dynamically
            var dateDropdowns = document.querySelectorAll("[name='day'], [name='month'], [name='year']");
            dateDropdowns.forEach(function(dropdown) {
                dropdown.addEventListener("change", calculateAge);
            });
        });

        function displayMessage(message, messageType) {
            var messageContainer = document.getElementById('message-container');
            messageContainer.innerHTML = '<div class="' + messageType + '">' + message + '</div>';
        }
        </script>

    </head>

<body>

    <div id="message-container" class="message-container"></div>
    <form method="post" enctype="multipart/form-data">

        <div class="container">
            <div class="left-side">
                <h2>Upload Your Image</h2>
                <div class="upload-btn-wrapper">
                    <img class="image" name="image" src="../images/profile.png" alt="">
                    <button class="btn choose" onclick="document.getElementById('fileInput').click()">Add Your
                        Photo</button>
                    <input type="file" id="fileInput" name="fileInput" accept="image/*">

                </div>
            </div>
            <div class="right-side">
                <h2>Sign Up</h2>

                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required onkeyup="checkPasswordStrength()">
                <div id="password-strength"></div>
                <div class="date">
                    <label for="dob">Date of Birth:</label>
                </div>
                <div class="dob-container">
                    <select name="day" required></select>
                    <select name="month" required></select>
                    <select name="year" required></select>
                </div>
                <div class="gender-container">
                    <label>Gender:</label>
                    <input type="radio" name="gender" value="male" id="male" required>
                    <label for="male">Male</label>
                    <input type="radio" name="gender" value="female" id="female" required>
                    <label for="female">Female</label>
                    <input type="radio" name="gender" value="custom" id="custom" required>
                    <label for="custom">Custom</label>
                </div>
                <button type="submit" name="submit" class="submit-btn">Create Account</button>
    </form>
    <h3>If you already have an account</h3>
    <div class="login-link">
        <a href="login.php">Login</a></p>
    </div>
    </div>
    </div>
    <script>
        function checkPasswordStrength() {
            var password = document.getElementsByName("password")[0].value;
            var strengthMeter = document.getElementById("password-strength");
        
            // Define the criteria for a strong password
            var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.{8,})");
        
            if (strongRegex.test(password)) {
                strengthMeter.innerHTML = "Password is strong";
                strengthMeter.style.color = "green";
            } else if (password.length > 0) {
                strengthMeter.innerHTML = "Password must contain at least 8 characters, including uppercase, lowercase, numbers, and special characters.";
                strengthMeter.style.color = "red";
            } else {
                strengthMeter.innerHTML = ""; // Clear the message when the password field is empty
            }
        }
</script>

</script>

</body>

</html>
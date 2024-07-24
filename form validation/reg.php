<?php
include 'conn.php';




$nameErr = $emailErr = $passwordErr = $confirmPasswordErr = $phoneErr = "";
$name = $email = $password = $confirmPassword = $phone = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        email VARCHAR(255) UNIQUE,
        password VARCHAR(255),
        phone VARCHAR(15),
        image_path VARCHAR(255)
    )";
    
    $con->query($sql);

    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = test_input($_POST["name"]);
        if(!preg_match('/^[A-Za-z]+ [A-Za-z]+ [A-Za-z]+ [A-Za-z]+$/', $name)) {
            $nameErr = "Invalid name format";
    }
    
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }
    if (empty($_POST["phone"])) {
        $phoneErr = "Phone number is required";
    } else {
        $phone = test_input($_POST["phone"]);
        if (!preg_match('/^07[789]\d{7}$/', $phone)) {
            $phoneErr = "Invalid phone number format";
        }
    }
    
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = $_POST["password"];
        // Password regex validation
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*()_+\[\]{};:\'\"\\|,.<>\/?`~\-])(?=.{8,})(?!.*\s).*$/', $password)) {
            $passwordErr = "Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, one number, and one special character.";
        }
    }
    
    if (empty($_POST["confirmPassword"])) {
        $confirmPasswordErr = "Confirm Password is required";
    } else {
        $confirmPassword = test_input($_POST["confirmPassword"]);
        if ($password !== $confirmPassword) {
            $confirmPasswordErr = "Passwords do not match";
        }
    }
    
    // Validate and handle file upload
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES["file"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Allow certain file formats
        $allowedTypes = array("jpg", "jpeg", "png", "gif");
        if (!in_array(strtolower($fileType), $allowedTypes)) {
            $fileErr = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        } else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
                $filePath = $targetFilePath;
            } else {
                $fileErr = "There was an error uploading your file.";
            }
        }
    } else {
        $fileErr = "Please upload an image file.";
    }
    
    if (empty($nameErr) && empty($emailErr) && empty($passwordErr) && empty($confirmPasswordErr) && empty($fileErr)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (name, email, password, phone_number, user_image) VALUES ('$name', '$email', '$password', '$phone', '$filePath')";
        if (mysqli_query($con, $sql)) {
            header("Location: login.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
    }
}
}
function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f8f9fa;
        }
        .form-container {
            max-width: 500px;
            margin: auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .form-label {
            color: #333;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-warning {
            background-color: #ffc107;
            border: none;
        }
        .text-danger {
            font-size: 0.875em;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="form-container">
            <h2 class="text-center">Sign Up</h2>
            <p style="text-align: center;">create an account it is free</p>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data" onsubmit="return validateForm()">

                <div class="mb-3">
                    <label for="file">Choose an image:</label>
                    <input type="file" name="file" id="file" required>
                    <div class="text-danger" id="fileErr"></div>
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $name;?>">
                    <div class="text-danger" id="nameErr"><?php echo $nameErr;?></div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $email;?>">
                    <div class="text-danger" id="emailErr"><?php echo $emailErr;?></div>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $phone;?>">
                    <div class="text-danger" id="phoneErr"><?php echo $phoneErr;?></div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <div class="text-danger" id="passwordErr"><?php echo $passwordErr;?></div>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
                    <div class="text-danger" id="confirmPasswordErr"><?php echo $confirmPasswordErr;?></div>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-danger">Register</button>
                    <p style="text-align: center;">Already have an account? <a style="text-decoration: none; font-weight: bold; color: grey;" href="login.php">Login</a></p>
                </div>
            </form>
        </div>
    </div>

    <script>
        function validateForm() {
            let valid = true;

            // Clear previous error messages
            document.getElementById("nameErr").innerHTML = "";
            document.getElementById("emailErr").innerHTML = "";
            document.getElementById("phoneErr").innerHTML = "";
            document.getElementById("passwordErr").innerHTML = "";
            document.getElementById("confirmPasswordErr").innerHTML = "";

            // Validate name
            let name = document.getElementById("name").value;
            const pattern = /^[A-Za-z]+ [A-Za-z]+ [A-Za-z]+ [A-Za-z]+$/;
            if (name === "") {
                document.getElementById("nameErr").innerHTML = "Name is required";
                valid = false;
            }
            elseif (!pattern.test(name)) {
                document.getElementById("nameErr").innerHTML = "Invalid name format";
                valid = false;
            }
            

            // Validate email
            let email = document.getElementById("email").value;
            let emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            if (email === "") {
                document.getElementById("emailErr").innerHTML = "Email is required";
                valid = false;
            } else if (!emailPattern.test(email)) {
                document.getElementById("emailErr").innerHTML = "Invalid email format";
                valid = false;
            }

            // Validate phone
            let phone = document.getElementById("phone").value;
            let phonePattern = /^07[789]\d{7}$/;
            if (phone === "") {
                document.getElementById("phoneErr").innerHTML = "Phone number is required";
                valid = false;
            } else if (!phonePattern.test(phone)) {
                document.getElementById("phoneErr").innerHTML = "Invalid phone number format";
                valid = false;
            }

            // Validate password
            let password = document.getElementById("password").value;
            let passwordPattern = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*()_+\[\]{};:\'\"\\|,.<>\/?`~\-])(?=.{8,})(?!.*\s).*$/;
            if (password === "") {
                document.getElementById("passwordErr").innerHTML = "Password is required";
                valid = false;
            } else if (!passwordPattern.test(password)) {
                document.getElementById("passwordErr").innerHTML = "Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, one number, and one special character.";
                valid = false;
            }

            // Validate confirm password
            let confirmPassword = document.getElementById("confirmPassword").value;
            if (confirmPassword === "") {
                document.getElementById("confirmPasswordErr").innerHTML = "Confirm Password is required";
                valid = false;
            } else if (password !== confirmPassword) {
                document.getElementById("confirmPasswordErr").innerHTML = "Passwords do not match";
                valid = false;
            }

            return valid;
        }
    </script>
</body>
</html>

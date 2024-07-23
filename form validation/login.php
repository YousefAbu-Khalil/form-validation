<?php
include 'conn.php';



$emailErr = $passwordErr = "";
$email = $password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }
    
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = test_input($_POST["password"]);
    }
    
    if (empty($emailErr) && empty($passwordErr)) {
        $sql = "SELECT id, password FROM users WHERE email='$email'";
        $result = mysqli_query($con, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if ($password === $row['password']) {
                echo "<script>alert('Login successful');</script>";
                // Start a session and redirect if needed
                session_start();
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $row['id'];
                $_SESSION['email'] = $email;
                
                header("Location: welcome.php"); // Redirect to a protected page
                exit();
            } else {
                $passwordErr = "Invalid password";
            }
        } else {
            $emailErr = "No account found with that email";
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
    <title>Login</title>
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
            <h2 class="text-center">Login</h2>
            <p style="text-align: center;">welcome back login with your credentials</p>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $email;?>">
                    <div class="text-danger"><?php echo $emailErr;?></div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <div class="text-danger"><?php echo $passwordErr;?></div>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Login</button>
                    <p style="text-align: center;">Already have an account? <a style="text-decoration: none; font-weight: bold; color: grey; " href="reg.php">signup</a></p>

                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

<script>
        function validateForm() {
            let valid = true;

            // Clear previous error messages
            document.getElementById("emailErr").innerHTML = "";
            document.getElementById("passwordErr").innerHTML = "";

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

            // Validate password
            let password = document.getElementById("password").value;
            if (password === "") {
                document.getElementById("passwordErr").innerHTML = "Password is required";
                valid = false;
            }

            return valid;
        }
    </script>
</html>

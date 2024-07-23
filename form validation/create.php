<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- CSS -->
    <style>
        body {
            background: #1d2630;
        }
        * {
            color: #fff;
        }
        #Update {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mt-5 mb-5">Create Account</h2>
        <div class="mb-5">
            <div class="row">
                <!-- Input field for Name -->
                <form action="create.php" method="post" enctype="multipart/form-data">
                    <div class="form-group col-md-6 mb-3">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control" id="name" placeholder="Enter Name" required />
                    </div>
                    <!-- Input field for Email -->
                    <div class="form-group col-md-6 mb-3">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" id="email" placeholder="Enter Email" required />
                    </div>
                    <!-- Input field for Password -->
                    <div class="form-group col-md-6 mb-3">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter Password" required />
                    </div>
                    <!-- Input field for Phone Number -->
                    <div class="form-group col-md-6 mb-3">
                        <label for="phone">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="Enter Phone Number" required />
                    </div>
                    <!-- Input field for Profile Image -->
                    <div class="form-group col-md-6 mb-3">
                        <label for="user_image">Profile Image</label>
                        <input type="file" name="user_image" class="form-control" id="user_image" />
                    </div>
                    <!-- Buttons for adding and updating data -->
                    <div class="col-lg-12 mt-5">
                        <button type="submit" class="btn btn-success" id="Submit">Add Data</button>
                        <button type="button" class="btn btn-primary" id="Update">Update</button>
                    </div>
                </form>
            </div>
        </div>
        <hr />
    </div>
</body>
</html>

<?php
// Only execute PHP code if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'conn.php';

    // Get form data
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);

    // Handle file upload
    $user_image = null; // Default to null
    if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['user_image']['tmp_name'];
        $fileName = $_FILES['user_image']['name'];
        $fileSize = $_FILES['user_image']['size'];
        $fileType = $_FILES['user_image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Define allowed file extensions
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedExts)) {
            // Define the path to save the image
            $uploadDir = 'uploads/';
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            // Move the file to the server directory
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $user_image = $destPath; // Save the path to the image
            } else {
                die("Error uploading image.");
            }
        } else {
            die("Unsupported file type.");
        }
    }

    // Insert data into the database
    $sql = "INSERT INTO users (name, email, password, phone_number, user_image) VALUES ('$name', '$email', '$password', '$phone', '$user_image')";
    if (mysqli_query($con, $sql)) {
        // Redirect to welcome.php after successful insertion
        header("Location: welcome.php");
        exit();
    } else {
        die("Error inserting data: " . mysqli_error($con));
    }

    // Close connection
    mysqli_close($con);
}
?>

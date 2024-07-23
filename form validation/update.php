<?php
include 'conn.php';

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    // SQL query to update the record
    $sql = "UPDATE users SET name = '$name', email = '$email', password = '$password' WHERE id = $id";

    if (mysqli_query($con, $sql)) {
        echo "Record updated successfully";
        // Redirect to the main page or a confirmation page
        header("Location: welcome.php");
        exit();
    } else {
        die("Error updating record: " . mysqli_error($con));
    }
} else {
    die("Invalid request.");
}

// Close connection
mysqli_close($con);
?>

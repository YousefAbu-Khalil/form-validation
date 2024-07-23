<?php
include 'conn.php';

// Check if the ID is set in the URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Get the ID from the URL and convert it to an integer

    // SQL query to delete the record
    $sql = "DELETE FROM users WHERE id = $id";

    if (mysqli_query($con, $sql)) {
        echo "Record deleted successfully";
        // Redirect to the main page or a confirmation page
        header("Location: welcome.php");
        exit();
    } else {
        die("Error deleting record: " . mysqli_error($con));
    }
} else {
    die("ID not specified.");
}

// Close connection
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Record</title>
    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <h2 class="mt-5 mb-5">Edit Record</h2>
        <?php
        include 'conn.php';

        // Check if the ID is set in the URL
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']); // Get the ID from the URL and convert it to an integer

            // SQL query to fetch the record
            $sql = "SELECT * FROM users WHERE id = $id";
            $result = mysqli_query($con, $sql);

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                ?>
                <!-- Form to edit the record -->
                <form action="update.php" method="post">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" id="name" value="<?php echo $row['name']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">email</label>
                        <input type="text" name="email" class="form-control" id="address" value="<?php echo $row['email']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="salary" class="form-label">password</label>
                        <input type="text" name="password" class="form-control" id="salary" value="<?php echo $row['password']; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
                <?php
            } else {
                echo "No record found.";
            }
        } else {
            die("ID not specified.");
        }

        // Close connection
        mysqli_close($con);
        ?>
    </div>
</body>
</html>

<?php
session_start();
include 'conn.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Get the user's ID from the session
$user_id = $_SESSION["id"];

// Fetch user details from the database
$sql = "SELECT role_id, name, email, phone_number, password, date_created, user_image FROM users WHERE id = ?";
if ($stmt = $con->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $stmt->bind_result($role_id, $name, $email, $phone_number, $password, $date_created, $user_image);
        if ($stmt->fetch()) {
            $is_admin = ($role_id == 1);
        }
    }
    $stmt->close();
}

// Handle logout
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f8f9fa;
        }
        .welcome-container, .profile-container {
            width: auto;
            margin: auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .welcome-container h2, .profile-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .user-image {
            width: 150px; /* Adjust size as needed */
            height: auto;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="welcome-container">
            <h2 class="text-center">
                <?php echo $is_admin ? 'Hello Admin' : 'Welcome, ' . htmlspecialchars($name); ?>
            </h2>
            <?php if ($is_admin): ?>
                <!-- Create User Button -->
                <div class="text-center mb-4">
                    <a href="create.php" class="btn btn-primary">Create User</a>
                </div>
                
                <table class="table table-bordered table-hover" id="crudTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Password</th>
                            <th>Date Created</th>
                            <th>Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT id, name, email, phone_number, password, date_created, user_image FROM users WHERE role_id != 1";
                        $result = mysqli_query($con, $sql);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $imagePath = htmlspecialchars($row['user_image']);
                                $imageSrc = $imagePath ? $imagePath : 'default-image.png'; // Default image if no path

                                echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['name']}</td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['phone_number']}</td>
                                    <td>{$row['password']}</td>
                                    <td>{$row['date_created']}</td>
                                    <td><img src='{$imageSrc}' alt='User Image' class='user-image'></td>
                                    <td>
                                        <a class='btn btn-primary' href='view.php?id={$row['id']}'>View</a>
                                        <a class='btn btn-warning' href='edit.php?id={$row['id']}'>Edit</a>
                                        <a class='btn btn-danger' href='delete.php?id={$row['id']}'>Delete</a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center'>No records found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="profile-container text-center">
                    <img src="<?php echo htmlspecialchars($user_image); ?>" alt="User Image" class="user-image">
                    <div class="mt-4">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($phone_number); ?></p>
                        <p><strong>Date Created:</strong> <?php echo htmlspecialchars($date_created); ?></p>
                        <!-- Hide password or handle securely -->
                        <p><strong>Password:</strong> ********</p>
                    </div>
                </div>
            <?php endif; ?>
            <div class='text-center mt-4'>
                <form method='post' action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>'>
                    <button type='submit' name="logout" class='btn btn-danger'>Logout</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<?php
mysqli_close($con);
?>

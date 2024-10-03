
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="dashboard/assets/css/style.css">
    <style>  .footer-padding {
            padding-top: 40px; /* Adjust padding as needed */
        }</style>
</head>
<body>
    <div class="login-container">
        <form action="" method="post">
            <h2>Admin Login</h2>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" 
            
            >

            <label for="password">Password</label>
            <input type="password" id="password" name="password" >

            <button type="submit">Login</button>
        </form>
        <p>Have no account? <a href="signup.php">Signup</a></p>
    </div>
    <footer class="footer">
        <div class="footer-container container">
            <p class="footer-text">© 2023 Plantex. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>

<?php
// include_once('dashboard/db.php');
// session_start();

// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     $username = $_POST['username'];
//     $password = $_POST['password'];

//     // Prepare and execute the SQL statement
//     $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
//     $stmt->bind_param("s", $username);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     // Check if a user with the provided username exists
//     if ($result->num_rows == 1) {
//         $user = $result->fetch_assoc();

//         // Verify the password using password_verify()
//         if (password_verify($password, $user['password'])) {
//             // Set session variables
//             $_SESSION['user_id'] = $user['id'];
//             $_SESSION['username'] = $user['username'];  
//             $_SESSION['role'] = $user['role'];

//             // Redirect based on role
//             if ($_SESSION['role'] === 'admin') {
//                 header('Location: dashboard/dashboard.php'); // Admin Dashboard
//                 exit();
//             } else {
//                 // Redirect non-admin users to a different page
//                 echo "<script type='text/javascript'>
//                         alert('welcome user');
//                         window.location.href = 'index.php';
//                       </script>";
//                 exit();
//             }
//         } else {
//             echo "<p>Invalid credentials</p>";
//         }
//     } else {
//         echo "<p>Invalid credentials</p>";
//     }
// }

?>




<?php
include_once('dashboard/db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Log to the browser console
    echo "<script>console.log('Username entered: $username');</script>";
    echo "<script>console.log('Password entered: $password');</script>";

    // Vulnerable SQL query (no prepared statements for testing purposes)
    $query = "SELECT id, username, password, role FROM users WHERE username = '$username'";

    // Log SQL query to the console
    echo "<script>console.log('SQL Query: ".addslashes($query)."');</script>";

    $result = $conn->query($query);

    // Log if the query was successful
    if ($result) {
        echo "<script>console.log('Query executed successfully');</script>";
    } else {
        echo "<script>console.log('Query execution failed: " . addslashes($conn->error) . "');</script>";
    }

    // Check if a user with the provided username exists
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Log user details retrieved from the database to the console
        echo "<script>console.log('User details fetched from the database: " . json_encode($user) . "');</script>";

        // Use password_verify() to compare the plain text password with the hashed password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Save username and password into cookies (insecure)
            setcookie('username', $username, time() + (86400 * 30), "/"); // 30 days
            setcookie('password', $password, time() + (86400 * 30), "/"); // 30 days

            // Log successful login
            echo "<script>console.log('Login successful for user: $username');</script>";

            // Redirect based on role
            if ($_SESSION['role'] === 'admin') {
                header('Location: dashboard/dashboard.php'); // Admin Dashboard
                exit();
            } else {
                // Redirect non-admin users to a different page
                echo "<script type='text/javascript'>
                        alert('Welcome user');
                        window.location.href = 'index.php';
                      </script>";
                exit();
            }
        } else {
            // Log invalid password
            echo "<script>console.log('Invalid password for user: $username');</script>";
            echo "<p>Invalid credentials</p>";
        }
    } else {
        // Log invalid username
        echo "<script>console.log('No user found with username: $username');</script>";
        echo "<p>Invalid credentials</p>";
    }
}
?>


<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate username and password
    if (empty($username) || empty($password)) {
        $loginError = 'Please enter both username and password.';
    } else {
        // Connect to the database
        $conn = new mysqli("localhost", "root", "", "guitarslap");

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Retrieve user from the database
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $dbUsername, $dbPassword);
            $stmt->fetch();

            // Verify the password
            if (password_verify($password, $dbPassword)) {
                $_SESSION['username'] = $dbUsername;
                header('Location: index.php'); // Redirect to the main page after successful login
                exit();
            } else {
                $loginError = 'Invalid username or password';
            }
        } else {
            $loginError = 'Invalid username or password';
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
    <script>
        function validateForm() {
            var username = document.forms["loginForm"]["username"].value;
            var password = document.forms["loginForm"]["password"].value;
            var errorMessage = "";

            if (username === "") {
                errorMessage += "Username is required. ";
            }
            if (password === "") {
                errorMessage += "Password is required. ";
            }

            if (errorMessage !== "") {
                alert(errorMessage);
                return false;
            }
            return true;
        }
    </script>
</head>
<body>

<h1>Login into <h1 class="maintitle"> Guitarslap</h1></h1>

<?php if (isset($loginError)) : ?>
    <p style="color: red;"><?php echo $loginError; ?></p>
<?php endif; ?>

<form name="loginForm" method="post" action="" onsubmit="return validateForm();">
    <label for="username">Username:</label>
    <input type="text" name="username">
    <br>
    <label for="password">Password:</label>
    <input type="password" name="password">
    <br>
    <input type="submit" name="login" value="Login">
</form>
<p>If you don't have an account yet, please <a href="register.php">register</a>.</p>

</body>
</html>

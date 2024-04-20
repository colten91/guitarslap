<?php
session_start();

// Initialize variables
$registerError = '';
$enteredUsername = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Store the entered username for display in case of errors
    $enteredUsername = $username;

    // Validate username and password
    if (empty($username) || empty($password) || empty($confirmPassword)) {
        $registerError = 'Please enter both username and password.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $registerError = 'Username can only contain letters, numbers, and underscores.';
    } elseif (strlen($password) < 6) {
        $registerError = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirmPassword) {
        $registerError = 'Passwords do not match.';
    } else {
        // Connect to the database
        $conn = new mysqli("localhost", "root", "", "guitarslap");

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if the username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $registerError = 'Username already taken. Please choose another one.';
        } else {
            // Perform user registration
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashedPassword);

            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                header('Location: index.php'); // Redirect to the main page after successful registration
                exit();
            } else {
                $registerError = 'Registration failed';
            }

            $stmt->close();
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<h1>Register</h1>

<?php if (!empty($registerError)) : ?>
    <p style="color: red;"><?php echo $registerError; ?></p>
<?php endif; ?>

<form method="post" action="">
    <label for="username">Username:</label>
    <input type="text" name="username" value="<?php echo $enteredUsername; ?>" >
    <br>
    <label for="password">Password:</label>
    <input type="password" name="password" >
    <br>
    <label for="confirm_password">Confirm Password:</label>
    <input type="password" name="confirm_password" >
    <br>
    <input type="submit" name="register" value="Register">
</form>
<p>If you already have an account, you can <a href="login.php">login</a> here.</p>

</body>
</html>

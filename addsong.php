<?php
// Database connection
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'guitarslap';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$error = "";
$success = "";

// Initialize input variables
$title = "";
$artist = "";
$category = "";
$image_url = "";
$tabs = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form inputs
    $title = trim($_POST['title']);
    $artist = trim($_POST['artist']);
    $category = trim($_POST['category']);
    $image_url = trim($_POST['image_url']);
    $tabs = trim($_POST['tabs']);

    // Validate inputs
    if (empty($title)) {
        $error = "Title is required.";
    } elseif (empty($artist)) {
        $error = "Artist is required.";
    } elseif (empty($category)) {
        $error = "Category is required.";
    } elseif (empty($image_url)) {
        $error = "Image URL is required.";
    } elseif (!filter_var($image_url, FILTER_VALIDATE_URL)) {
        $error = "Please provide a valid URL for the image.";
    } elseif (empty($tabs)) {
        $error = "Tabs are required.";
    } else {
        // Sanitize inputs for SQL
        $title = mysqli_real_escape_string($conn, $title);
        $artist = mysqli_real_escape_string($conn, $artist);
        $category = mysqli_real_escape_string($conn, $category);
        $image_url = mysqli_real_escape_string($conn, $image_url);
        $tabs = mysqli_real_escape_string($conn, $tabs);

        // Prepare the insert statement
        $insertQuery = "INSERT INTO songs (title, artist, category, image_url, tabs) VALUES ('$title', '$artist', '$category', '$image_url', '$tabs')";

        if (mysqli_query($conn, $insertQuery)) {
            $success = "Song added successfully!";
            // Clear the inputs
            $title = $artist = $category = $image_url = $tabs = "";

            // Redirect to index.php after 1 second
            echo '<script>
                    setTimeout(function() {
                        window.location.href = "index.php";
                    }, 1000);
                  </script>';
        } else {
            $error = "Error adding song: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Song</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="addsong.css">
    <a href="index.php" class="addsong">Home</a>
    <style>

    </style>
</head>
<body>

<div class="form-container">
    <h2>Add a New Song</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="post" action="addsong.php">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>">

        <label for="artist">Artist:</label>
        <input type="text" id="artist" name="artist" value="<?php echo htmlspecialchars($artist); ?>">

        <label for="category">Category:</label>
        <select id="category" name="category">
            <option value="" <?php echo $category === "" ? "selected" : ""; ?>>Select a category</option>
            <option value="Guitar" <?php echo $category === "Guitar" ? "selected" : ""; ?>>Guitar</option>
            <option value="Bass" <?php echo $category === "Bass" ? "selected" : ""; ?>>Bass</option>
        </select>

        <label for="image_url">Image URL:</label>
        <input type="text" id="image_url" name="image_url" value="<?php echo htmlspecialchars($image_url); ?>">

        <label for="tabs">Tabs:</label>
        <textarea id="tabs" name="tabs" rows="10"><?php echo htmlspecialchars($tabs); ?></textarea>

        <button type="submit">Add Song</button>
    </form>
</div>

</body>
</html>

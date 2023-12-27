<?php

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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
// Fetch songs based on the search query and category
$searchQuery = isset($_GET['q']) ? $_GET['q'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$query = "SELECT * FROM songs WHERE (title LIKE '%$searchQuery%' OR artist LIKE '%$searchQuery%')";

// Include category condition if provided
if (!empty($category)) {
    $query .= " AND category = '$category'";
}

$result = $conn->query($query);

if ($result) {
    $songs = array();

    while ($row = $result->fetch_assoc()) {
        $songs[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($songs);
} else {
    http_response_code(500);
    echo json_encode(array("message" => "Error fetching songs."));
}

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add a new song
    $data = json_decode(file_get_contents("php://input"));

    $title = $data->title;
    $artist = $data->artist;
    $tabs = $data->tabs;
    $image_url = $data->image_url;
    $category = $data->category; // Add category to the input data

    $stmt = $conn->prepare("INSERT INTO songs (title, artist, tabs, image_url, category) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $artist, $tabs, $image_url, $category);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(array("message" => "Song added successfully."));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Error adding song."));
    }

    $stmt->close();
}

$conn->close();

?>

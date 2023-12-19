<?php

$servername = 'fdb34.awardspace.net';
$username = '3931222_jhonny';
$password = '120704-22486Aa';
$dbname = '3931222_jhonny';



// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if a search query is provided
    $searchQuery = isset($_GET['q']) ? $_GET['q'] : '';

    // Fetch songs based on the search query
    $query = "SELECT * FROM songs WHERE title LIKE '%$searchQuery%' OR artist LIKE '%$searchQuery%'";
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

    $stmt = $conn->prepare("INSERT INTO songs (title, artist, tabs, image_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $artist, $tabs, $image_url);

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
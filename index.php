<?php
// Assume you have a database connection established
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

// Fetch unique categories from the database
$categoriesQuery = "SELECT DISTINCT category FROM songs";
$categoriesResult = mysqli_query($conn, $categoriesQuery);

if (!$categoriesResult) {
    die("Error fetching categories: " . mysqli_error($conn));
}

$categories = mysqli_fetch_all($categoriesResult, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guitar Tabs App</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styles for the overlay/modal */
        .overlay {
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            align-items: center;
            justify-content: center;
        }

        .modal {
            background: #fff;
            padding: 20px;
            max-width: 80%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            margin: auto; /* Center the modal horizontally and vertically */
        }

        /* Media query for smaller screens */
        @media (max-width: 450px) {
            pre {
                font-size: 8px; /* Adjust the font size as needed */
            }
        }

        /* Additional styles for the heading section */
        #heading {
            text-align: center;
            margin-bottom: 20px;
        }

        #searchBar {
            display: inline-block;
            margin-right: 10px;
        }
        #heading {
            text-align: center;
            margin-bottom: 20px;
        }

        #categoryButtons {
            margin-bottom: 10px;
        }

        #categoryButtons button {
            margin-right: 10px;
        }
    </style>
</head>
<body>

<div id="heading">
<h1>Guitar Tabs App</h1>
<div id="categoryButtons">
<?php
        // Check if $categories is set before using it in a foreach loop
        if (isset($categories) && is_array($categories)) {
            echo '<button onclick="setCategory(\'\')">All</button>'; // All button
            foreach ($categories as $category) {
                echo '<button onclick="setCategory(\'' . $category['category'] . '\')">' . $category['category'] . '</button>';
            }
        } else {
            echo '<p>No categories available.</p>';
        }
        ?>
    </div>
    <input type="text" id="searchInput" placeholder="Search for a song" oninput="fetchAndDisplaySongs()" id="searchBar">
    <a href="logout.php">Logout</a>
</div>

<div id="songs"></div>

<!-- Modal content... -->
<div class="overlay" id="overlay">
    <div class="modal" id="modal">
        <h2 id="modalTitle"></h2>
        <p id="modalArtist"></p>
        <img id="modalImage" alt="Song Image">
        <h3>Song Tabs</h3>
        <pre id="modalTabs"></pre>
        <button onclick="closeModal()">Close</button>
    </div>
</div>

<script>
    // Variable to store the selected category
    let selectedCategory = '';
    // Function to set the selected category
    function setCategory(category) {
        selectedCategory = category;
        // Fetch and display songs when a category is selected
        fetchAndDisplaySongs();
    }

    // Function to open the modal
    function openModal(title, artist, image, tabs) {
        const modalTitle = document.getElementById('modalTitle');
        const modalArtist = document.getElementById('modalArtist');
        const modalImage = document.getElementById('modalImage');
        const modalTabs = document.getElementById('modalTabs');

        modalTitle.innerText = title;
        modalArtist.innerText = `By ${artist}`;
        modalImage.src = image;
        modalTabs.innerText = tabs;

        const overlay = document.getElementById('overlay');
        overlay.style.display = 'flex';
    }

    // Function to close the modal
    function closeModal() {
        const overlay = document.getElementById('overlay');
        overlay.style.display = 'none';
    }

    // Fetch songs from the API and display them based on the search query and category
    async function fetchAndDisplaySongs() {
        const searchInput = document.getElementById('searchInput').value;
        try {
            // Include the selected category in the API request
            const response = await fetch(`/guitarslap/api.php?q=${encodeURIComponent(searchInput)}&category=${encodeURIComponent(selectedCategory)}`);

            if (!response.ok) {
                throw new Error('Failed to fetch songs: ' + response.statusText);
            }

            const songs = await response.json();

            if (Array.isArray(songs)) {
                const songsDiv = document.getElementById('songs');
                songsDiv.innerHTML = '';

                songs.forEach(song => {
                    const songDiv = document.createElement('div');
                    songDiv.classList.add('song');

                    const button = document.createElement('button');
                    button.innerText = 'Show Tabs';
                    button.addEventListener('click', function () {
                        openModal(song.title, song.artist, song.image_url, song.tabs);
                    });

                    songDiv.innerHTML = `
                        <h3>${song.title} by ${song.artist}</h3>
                        <img src="${song.image_url}" alt="Song Image">
                    `;

                    songDiv.appendChild(button);
                    songsDiv.appendChild(songDiv);
                });
            } else {
                console.error('Invalid response format:', songs);
            }

        } catch (error) {
            console.error('Error fetching songs:', error.message);
        }
    }

    // Initial fetch and display
    fetchAndDisplaySongs();

    // Hide the overlay when the page loads or refreshes
    window.addEventListener('load', function () {
        const overlay = document.getElementById('overlay');
        overlay.style.display = 'none';
    });
</script>

</body>
</html>

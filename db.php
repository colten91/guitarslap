<?php
$servername = 'fdb34.awardspace.net';
$username = '3931222_jhonny';
$password = '120704-22486Aa';
$dbname = '3931222_jhonny';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
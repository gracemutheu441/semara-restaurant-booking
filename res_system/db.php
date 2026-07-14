<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "semara_restaurant-db";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
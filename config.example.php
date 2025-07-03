<?php
// Database Configuration Example
// Copy this file to config.php and update with your database credentials

$host = 'localhost';
$username = 'your_username';
$password = 'your_password';
$database = 'simprak_db';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?> 
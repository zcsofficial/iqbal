<?php
$host = 'localhost';
$user = 'root';
$password = 'Adnan@66202';
$database = 'syncgo';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

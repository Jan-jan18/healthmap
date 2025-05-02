<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['username'])) {
    exit();
}

$username = $_SESSION['username'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

$stmt = $conn->prepare("UPDATE users SET latitude = ?, longitude = ? WHERE username = ?");
$stmt->bind_param("dds", $latitude, $longitude, $username);
$stmt->execute();
?>

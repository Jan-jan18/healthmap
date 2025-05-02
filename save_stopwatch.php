<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['username'])) {
    exit("error");
}

// Kunin ang user ID
$username = $_SESSION['username'];
$userQuery = $conn->prepare("SELECT id FROM users WHERE username = ?");
$userQuery->bind_param("s", $username);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();
$user_id = $user['id'];

// Kunin ang duration mula sa AJAX request
$duration = isset($_POST['duration']) ? (int)$_POST['duration'] : 0;
if ($duration <= 0) {
    exit("error");
}

// Mag-set ng default exercise name (pwedeng baguhin)
$exercise_name = "Custom Exercise";
$date = date("Y-m-d");

// I-save sa database
$insertQuery = $conn->prepare("INSERT INTO daily_records (user_id, exercise_name, duration, date) VALUES (?, ?, ?, ?)");
$insertQuery->bind_param("isis", $user_id, $exercise_name, $duration, $date);
if ($insertQuery->execute()) {
    echo "success";
} else {
    echo "error";
}
?>

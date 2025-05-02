<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Kunin ang user ID mula sa session
$username = $_SESSION['username'];
$userQuery = $conn->prepare("SELECT id FROM users WHERE username = ?");
$userQuery->bind_param("s", $username);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();
$user_id = $user['id'];

// Check kung may form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $exercise = mysqli_real_escape_string($conn, $_POST['exercise']);
    $duration = intval($_POST['duration']);

    if (!empty($exercise) && $duration > 0) {
        $stmt = $conn->prepare("INSERT INTO workouts (user_id, exercise, duration) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $user_id, $exercise, $duration);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Workout logged successfully!";
        } else {
            $_SESSION['error'] = "Error logging workout: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Please enter valid workout details.";
    }
}

// Redirect back to dashboard
header("Location: dashboard.php");
exit();
?>

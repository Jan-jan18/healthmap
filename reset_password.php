<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
    echo "Unauthorized access!";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_SESSION['email'];

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $new_password, $email);

    if ($stmt->execute()) {
        echo "Password successfully reset! <a href='login.php'>Login here</a>";
        session_destroy();
    } else {
        echo "Error resetting password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>
    <form action="reset_password.php" method="POST">
        <input type="password" name="password" placeholder="Enter new password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>

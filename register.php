<?php
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $age = intval($_POST['age']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $blood_type = mysqli_real_escape_string($conn, $_POST['blood_type']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $error = ""; // Initialize error variable

    // ✅ Check if passwords match
    if ($password !== $confirm_password) {
        $error = "❌ Passwords do not match!";
    } else {
        // ✅ Check if email or username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        if ($stmt) {
            $stmt->bind_param("ss", $email, $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "❌ Username or Email already exists!";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // ✅ Prepare and execute the INSERT statement
                $stmt = $conn->prepare("INSERT INTO users (first_name, middle_name, last_name, age, gender, blood_type, email, username, password) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                if ($stmt) {
                    $stmt->bind_param("sssisssss", $first_name, $middle_name, $last_name, $age, $gender, $blood_type, $email, $username, $hashed_password);

                    if ($stmt->execute()) {
                        header("Location: login.php");
                        exit();
                    } else {
                        $error = "❌ Registration failed: " . $stmt->error;
                    }
                } else {
                    $error = "❌ Database error: " . $conn->error;
                }
            }
            $stmt->close();
        } else {
            $error = "❌ Database query error: " . $conn->error;
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            background: url('col.jpg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }
        .register-container {
            background: rgba(10, 3, 3, 0.2);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        h2 { color: white; font-size: 24px; margin-bottom: 15px; }
        input, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: rgb(29, 231, 10);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            transition: 0.3s;
        }
        button:hover { background-color: rgb(20, 12, 239); }
        .error-message { color: yellow; margin-bottom: 10px; }
        .login-link { margin-top: 15px; }
        .login-link a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .login-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <?php if (!empty($error)) { echo "<p class='error-message'>$error</p>"; } ?>
        <form method="POST">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="middle_name" placeholder="Middle Name">
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="number" name="age" placeholder="Age" required>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="GAY">GAY</option>
                <option value="TOMBOY">TOMBOY</option>
            </select>
            <select name="blood_type" required>
                <option value="">Select Blood Type</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select>
            <input type="text" name="email" placeholder="Email" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Register</button>
        </form>
        <div class="login-link">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>

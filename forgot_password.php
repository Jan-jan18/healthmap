<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        /* Background styling */
        body {
            background: url('foody.jpg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }

        /* Glassmorphism effect */
        .forgot-container {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        h2 {
            color: white;
            font-size: 24px;
            margin-bottom: 15px;
        }

        input[type="email"] {
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
            background-color: #ff6b6b;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            transition: 0.3s;
        }

        button:hover {
            background-color: #e63946;
        }

        .link-container {
            margin-top: 15px;
        }

        .link-container a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .link-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <h2>Forgot Password</h2>

        <?php if (isset($_SESSION['otp_sent']) && $_SESSION['otp_sent']) { ?>
            <p style="color: yellow;">OTP has been sent to your email!</p>
            <script>
                setTimeout(function() {
                    window.location.href = "login.php";
                }, 3000); // Redirect to login after 3 seconds
            </script>
        <?php 
            unset($_SESSION['otp_sent']); // Clear session variable
        } 
        ?>

        <form action="send_otp.php" method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Send OTP</button>
        </form>

        <div class="link-container">
            <p><a href="login.php">Back to Login</a></p>
        </div>
    </div>
</body>
</html>

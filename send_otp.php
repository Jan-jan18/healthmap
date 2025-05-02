<?php
session_start();
require 'db_connect.php'; // Database connection

// ✅ Adjust PHPMailer paths based on your file structure
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // ✅ Check if email exists in the database
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$stmt) {
        die("❌ Database error: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "❌ Email not found!";
        exit();
    }

    // ✅ Generate OTP and store in session
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['email'] = $email;

    // ✅ Send OTP via Email using PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com';  // 🔹 Replace with your email
        $mail->Password = 'your-email-password';   // 🔹 Replace with your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('your-email@gmail.com', 'HealthMap');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = "Your OTP Code";
        $mail->Body = "<h3>Your OTP code is: <b>$otp</b></h3><p>This code will expire in 5 minutes.</p>";

        if ($mail->send()) {
            header("Location: verify_otp.php");
            exit();
        } else {
            echo "❌ Failed to send OTP!";
        }
    } catch (Exception $e) {
        echo "❌ Mailer Error: " . $mail->ErrorInfo;
    }
}

<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
require 'db_connect.php';

$username = $_SESSION['username'];
$userQuery = $conn->prepare("SELECT id, username, nickname, age, profile_pic FROM users WHERE username = ?");
$userQuery->bind_param("s", $username);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();
$user_id = $user['id'] ?? 0;

// Update Profile Info
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $nickname = $_POST['nickname'] ?? '';
    $age = $_POST['age'] ?? 0;

    $updateQuery = $conn->prepare("UPDATE users SET nickname = ?, age = ? WHERE id = ?");
    $updateQuery->bind_param("sii", $nickname, $age, $user_id);
    
    if ($updateQuery->execute()) {
        $message = "Profile updated successfully!";
        header("Refresh:0");
        exit();
    } else {
        $message = "Error updating profile.";
    }
}

// Upload Profile Picture
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $target_dir = "uploads/";
    $file_name = basename($_FILES["profile_pic"]["name"]);
    $target_file = $target_dir . uniqid() . "_" . $file_name;
    
    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
        $updatePic = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
        $updatePic->bind_param("si", $target_file, $user_id);
        
        if ($updatePic->execute()) {
            $message = "Profile picture updated!";
            header("Refresh:0");
            exit();
        } else {
            $message = "Error updating profile picture.";
        }
    } else {
        $message = "Error uploading file.";
    }
}

// Delete Workout History
if (isset($_POST['delete_history'])) {
    $deleteQuery = $conn->prepare("DELETE FROM workouts WHERE user_id = ?");
    $deleteQuery->bind_param("i", $user_id);
    
    if ($deleteQuery->execute()) {
        $message = "Workout history deleted successfully!";
    } else {
        $message = "Error deleting workout history.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: url('profile-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center my-4 text-white">User Profile</h2>
        <div class="card p-4 text-center">
            <!-- Display Profile Picture -->
            <img src="<?= !empty($user['profile_pic']) ? $user['profile_pic'] : 'default-avatar.png'; ?>" class="profile-img mb-3" alt="Profile Picture">
            
            <h4>Username: <?= htmlspecialchars($user['username'] ?? 'Unknown'); ?></h4>
            <h5>Nickname: <?= htmlspecialchars($user['nickname'] ?? 'Not set'); ?></h5>
            <h5>Age: <?= htmlspecialchars($user['age'] ?? 'Not set'); ?></h5>

            <?php if (isset($message)) { ?>
                <div class="alert alert-info"><?= $message; ?></div>
            <?php } ?>

            <!-- Form to Update Nickname & Age -->
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nickname</label>
                    <input type="text" class="form-control" name="nickname" value="<?= htmlspecialchars($user['nickname'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Age</label>
                    <input type="number" class="form-control" name="age" value="<?= htmlspecialchars($user['age'] ?? ''); ?>">
                </div>
                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
            </form>

            <!-- Form to Upload Profile Picture -->
            <form method="POST" enctype="multipart/form-data" class="mt-3">
                <label class="form-label">Update Profile Picture</label>
                <input type="file" class="form-control mb-2" name="profile_pic" accept="image/*">
                <button type="submit" class="btn btn-warning">Upload</button>
            </form>

            <!-- Delete Workout History -->
            <form method="POST" class="mt-3">
                <button type="submit" name="delete_history" class="btn btn-danger">Delete Workout History</button>
            </form>

            <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>

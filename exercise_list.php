<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
require 'db_connect.php';

// Fetch exercise list
$exerciseQuery = $conn->query("SELECT name FROM exercises");
$exercises = $exerciseQuery ? $exerciseQuery->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Exercise List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background: url('gym.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
        }
        .container { margin-left: 180px; margin-top: 30px; }
        .timer { font-size: 1.5rem; font-weight: bold; }
        
        /* Sidebar styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 160px;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            padding-top: 20px;
        }
        .sidebar a {
            display: block;
            text-align: center;
            color: white;
            padding: 15px;
            margin: 10px;
            border: 2px solid white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .sidebar a:hover {
            background: white;
            color: black;
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <a href="dashboard.php">🏠 Home</a>
        <a href="profile.php">👤 Profile</a>
        <a href="exercise_list.php">🏋️ Exercise List</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h2 class="text-center my-4 text-white">Exercise List</h2>

        <!-- Exercise List with Stopwatch -->
        <div class="card my-4 p-3">
            <h4>Exercises</h4>
            <ul class="list-group">
                <?php foreach ($exercises as $exercise) { 
                    $safeName = str_replace(' ', '-', htmlspecialchars($exercise['name']));
                ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($exercise['name']); ?>
                        <button class="btn btn-primary start-exercise" data-exercise="<?= $safeName; ?>">Start</button>
                        <button class="btn btn-danger stop-exercise" data-exercise="<?= $safeName; ?>">Stop</button>
                        <span class="timer" id="timer-<?= $safeName; ?>">00:00</span>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <script>
        let timers = JSON.parse(localStorage.getItem("timers")) || {};
        let intervals = {};

        function updateTimerDisplay(exercise) {
            let time = timers[exercise] || 0;
            let mins = Math.floor(time / 60);
            let secs = time % 60;
            $(`#timer-${exercise}`).text(`${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`);
        }

        function saveTimers() {
            localStorage.setItem("timers", JSON.stringify(timers));
        }

        $(document).ready(function() {
            // Restore timers from localStorage
            Object.keys(timers).forEach(exercise => updateTimerDisplay(exercise));
        });

        $(document).on("click", ".start-exercise", function() {
            let exercise = $(this).data("exercise");
            if (!intervals[exercise]) {
                timers[exercise] = timers[exercise] || 0;
                intervals[exercise] = setInterval(() => {
                    timers[exercise]++;
                    updateTimerDisplay(exercise);
                    saveTimers();
                }, 1000);
            }
        });

        $(document).on("click", ".stop-exercise", function() {
            let exercise = $(this).data("exercise");
            clearInterval(intervals[exercise]);
            delete intervals[exercise];
        });
    </script>

</body>
</html>

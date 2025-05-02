<!DOCTYPE html>
<html lang="en">
<head>
    <title>Health Map Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background: url('gym.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
        }
        .container { margin-left: 180px; margin-top: 30px; }
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
        .health-map {
            text-align: center;
            margin-top: 20px;
        }
        .body-part {
            position: absolute;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: red;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="dashboard.php">🏠 Home</a>
        <a href="profile.php">👤 Profile</a>
        <a href="exercise_list.php">🏋️ Exercise List</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <div class="container">
        <h2 class="text-center my-4 text-white">Health Map Dashboard</h2>
        <div class="card p-4">
            <h4 class="text-center">Health Map</h4>
            <div class="health-map position-relative">
                <img src="lawasnato.png" alt="Health Map" style="width: 100%; max-width: 400px;">
                <div class="body-part" style="top: 30%; left: 45%;" data-part="Head"></div>
                <div class="body-part" style="top: 50%; left: 45%;" data-part="Chest"></div>
                <div class="body-part" style="top: 70%; left: 45%;" data-part="Knee"></div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $(".body-part").click(function() {
                let bodyPart = $(this).data("part");
                let symptom = prompt("Enter symptom for " + bodyPart + ":");
                if (symptom) {
                    $.post("save_symptom.php", { body_part: bodyPart, symptom: symptom }, function(response) {
                        if (response === "success") {
                            alert("Symptom saved for " + bodyPart + "!");
                        } else {
                            alert("Error saving symptom.");
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>

<?php

require_once("database.php");

$daily_query = "SELECT COUNT(*) AS daily_complaints FROM feedback WHERE DATE(created_at) = CURDATE()";
$daily_result = mysqli_query($conn, $daily_query);
$daily_complaints = mysqli_fetch_assoc($daily_result)['daily_complaints'];


$weekly_query = "SELECT COUNT(*) AS weekly_complaints FROM feedback WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
$weekly_result = mysqli_query($conn, $weekly_query);
$weekly_complaints = mysqli_fetch_assoc($weekly_result)['weekly_complaints'];


$monthly_query = "SELECT COUNT(*) AS monthly_complaints FROM feedback WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
$monthly_result = mysqli_query($conn, $monthly_query);
$monthly_complaints = mysqli_fetch_assoc($monthly_result)['monthly_complaints'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1e1e2f, #292940);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #4caf50;
        }
        .dashboard-container {
            width: 100%;
            max-width: 1200px;
            background: rgba(25, 25, 35, 0.9);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            padding: 40px;
            position: relative;
        }
        .button {
            display: inline-block;
            padding: 12px 20px;
            font-size: 16px;
            background: #4caf50;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 20px;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s;
        }
        .button:hover {
            background: #388e3c;
        }
        .stats {
            margin: 20px 0;
            font-size: 18px;
        }
        .go-back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: #4caf50;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .go-back-btn:hover {
            background-color: #388e3c;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
    <a href="admin_dashboard.php" class="go-back-btn">&#8592; Go Back</a>
        <h1>Feedback Dashboard</h1>
        <p class="stats"><strong>Daily Complaints:</strong> <?php echo $daily_complaints; ?></p>
        <p class="stats"><strong>Weekly Complaints:</strong> <?php echo $weekly_complaints; ?></p>
        <p class="stats"><strong>Monthly Complaints:</strong> <?php echo $monthly_complaints; ?></p>

        <!-- Button to navigate to the 'View All Feedbacks' page -->
        <a href="all_feedback.php" class="button">View All Feedbacks</a>
    </div>
</body>
</html>


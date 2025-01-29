<?php 
session_start(); 


$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Issues</title>
    <style>
     
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        .dashboard-container {
            width: 100%;
            max-width: 1200px;
            background: rgba(25, 25, 35, 0.9);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            padding: 40px;
            position: relative;
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

        h1, h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #4caf50;
        }

        .service-card {
            background: linear-gradient(145deg, #2a2a3d, #1d1d2e);
            border-radius: 12px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.4);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.6);
        }

        .service-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: #4caf50;
        }

        .service-card p {
            font-size: 16px;
            color: #cfcfcf;
        }

        .service-card a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: #4caf50;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        .service-card a:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
    <a href="admin_dashboard.php" class="go-back-btn">&#8592; Go Back</a>
        <h1>Manage Reported Issues</h1>

        <div class="service-card">
            <h3>View All Issues</h3>
            <p>View all the issues reported by citizens and their current status.</p>
            <a href="view_issues.php">View Issues</a>
        </div>
        
        <div class="service-card">
            <h3>Assign Issues</h3>
            <p>Assign reported issues to the appropriate department or team.</p>
            <a href="assign_issues.php">Assign Issues</a>
        </div>
        
        <div class="service-card">
            <h3>Close Issues</h3>
            <p>Mark issues as resolved and close them.</p>
            <a href="close_issues.php">Close Issues</a>
        </div>
    </div>
</body>
</html>

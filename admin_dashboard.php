<?php
session_start();

require_once("database.php"); 


$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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

        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #ff4b5c;
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

        .logout-btn:hover {
            background-color: #e33a47;
        }

        h1, h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #4caf50;
        }

        .user-info {
            margin-bottom: 30px;
            text-align: center;
        }

        .user-info p {
            font-size: 18px;
            margin: 10px 0;
        }

        .services {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
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

        footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #bbb;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <a href="logout.php" class="logout-btn">Logout</a>
        <h1>ADMIN DASHBOARD</h1>
        <div class="user-info">
            <h2>User Information</h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
           
            
        </div>

        <div class="services">
            <!-- Admin Services -->
            <div class="service-card">
                <h3>Manage Users</h3>
                <p>View and manage registered users and their data.</p>
                <a href="manage_users.php">Manage Users</a>
            </div>
            <div class="service-card">
                <h3>Manage Services</h3>
                <p>View and manage available city services.</p>
                <a href="manage_services.php">Manage Services</a>
            </div>
            <div class="service-card">
                <h3>Manage Issues</h3>
                <p>View and assign issues reported by citizens.</p>
                <a href="manage_issues.php">Manage Issues</a>
            </div>
            <div class="service-card">
                <h3>View Feedback</h3>
                <p>View feedback submitted by citizens.</p>
                <a href="view_feedback.php">View Feedback</a>
            </div>
            <div class="service-card">
                <h3>Send Notification</h3>
                <p>Create and send notifications to users.</p>
                <a href="send_notification.php">Send Notification</a>
            </div>
            <div class="service-card">
                <h3>Manage Restaurants</h3>
                <p>View, add, and delete restaurant names.</p>
                <a href="manage_restaurants.php">Manage Restaurants</a>
            </div>
            <div class="service-card">
                <h3>Manage Institution</h3>
                <p>Add and remove institution details.</p>
                <a href="manage_institution.php">Manage Institution</a>
            </div>

        </div>
    </div>
</body>
</html>
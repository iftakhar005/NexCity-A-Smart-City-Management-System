<?php 
session_start(); 

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

 //<a href="Dashboard.php" class="-btn">Shift to Citizen</a>

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
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
        .bell-icon {
            position: absolute;
            top: 80px;
            left: 20px;
            width: 40px;
            height: 40px;
            background-color: #f39c12;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .bell-icon:hover {
            transform: scale(1.1);
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
        .change-btn {
            position: absolute;
            top: 20px;
           left: 20px;
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
    </style>
</head>
<body>
    <div class="dashboard-container">
        <a href="logout.php" class="logout-btn">Logout</a>
       
        <h1>Welcome, Doctor!</h1>
        <div class="user-info">
            <h2>Your Information</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Role:</strong> Doctor</p>
        </div>
 <!-- Bell Icon for Notifications inside dashboard container -->
 <a href="view_notifications.php" class="bell-icon">
            <span>🔔</span>
        </a>
        <div class="services">
            <div class="service-card">
                <h3>View Your Patients</h3>
                <p>Access details of patients under your care.</p>
                <a href="view_patients.php">View Patients</a>
            </div>
            <div class="service-card">
                <h3>Manage Appointments</h3>
                <p>Check, approve, or reschedule your upcoming appointments.</p>
                <a href="appointments.php">Manage Appointments</a>
            </div>
           
            <div class="service-card">
                <h3>Availability Schedule</h3>
                <p>Update or view your available time slots for appointments.</p>
                <a href="availability_schedule.php">Update Schedule</a>
            </div>
        </div>
    </div>
</body>
</html>

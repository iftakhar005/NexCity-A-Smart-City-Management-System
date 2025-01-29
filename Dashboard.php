<?php 
session_start(); 

require_once("database.php");
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$user_id = $user['user_id'];


$notifications = []; 


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $feedback_type = $_POST['feedback_type'];
    $feedback_text = $_POST['feedback_text'];
    $rating = (int) $_POST['rating'];
    $created_at = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("INSERT INTO feedback (user_id, feedback_text, created_at, feedback_type, rating) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('isssi', $user_id, $feedback_text, $created_at, $feedback_type, $rating);

    if ($stmt->execute()) {
        $_SESSION['feedback_success'] = "Feedback submitted successfully!";
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Error submitting feedback: " . $conn->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen Dashboard</title>
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
            right: 20px;
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

        .toggle-menu-btn {
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

        .toggle-menu-btn:hover {
            background-color: #45a049;
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
            font-size: 23px;
            margin: 10px 0;
        }

        .menu {
            display: none;
            text-align: center;
            margin-top: 20px;
        }

        .menu a {
            display: block;
            margin: 10px 0;
            font-size: 18px;
            color: #4caf50;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .menu a:hover {
            color: #45a049;
        }

        .section-header {
            text-align: center;
            font-size: 24px;
            color: #4caf50;
            margin-top: 30px;
            margin-bottom: 20px;
            font-weight: bold;
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

        .feedback-section {
            margin-top: 40px;
        }

        .feedback-form {
            background: #1d1d2e;
            border-radius: 12px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.4);
            padding: 20px;
            margin-bottom: 20px;
        }

        .feedback-form label {
            display: block;
            margin-bottom: 10px;
            color: #4caf50;
            font-weight: bold;
        }

        .feedback-form input, .feedback-form select, .feedback-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: none;
            border-radius: 8px;
            background: #292940;
            color: #fff;
        }

        .feedback-form button {
            background: #4caf50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .feedback-form button:hover {
            background: #45a049;
        }

        .feedback-table {
            width: 100%;
            border-collapse: collapse;
        }

        .feedback-table th, .feedback-table td {
            border: 1px solid #4caf50;
            padding: 10px;
            text-align: left;
            color: #fff;
        }

        .feedback-table th {
            background: #292940;
        }

        footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #bbb;
        }
        .light-mode {
    background: #f4f4f4;
    color: #333;
}

.light-mode .dashboard-container {
    background: rgba(255, 255, 255, 0.9);
    color: #222;
}

.light-mode .service-card {
    background: linear-gradient(145deg, #ddd, #fff);
    color: #333;
}

.theme-toggle-btn {
    position: absolute;
    top: 20px;
    right: 70px;
    background: #ffcc00;
    color: #000;
    padding: 10px;
    border: none;
    border-radius: 50%;
    font-size: 20px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.theme-toggle-btn:hover {
    background: #ffdb4d;
}

    </style>
    <script>
        function toggleMenu() {
            var menu = document.getElementById('userMenu');
            menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
        }
        function toggleTheme() {
    const body = document.body;
    const btn = document.querySelector(".theme-toggle-btn");

    if (body.classList.contains("light-mode")) {
        body.classList.remove("light-mode");
        btn.textContent = "🌙";
    } else {
        body.classList.add("light-mode");
        btn.textContent = "☀️";
    }
}

    </script>
</head>
<body>
    <div class="dashboard-container">
        <a href="logout.php" class="logout-btn">Logout</a>

        <h1>Welcome to NextCity</h1>
        <div class="user-info">
            <h2>User Information</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Role:</strong> Citizen</p>
        </div>

        <!-- Bell Icon for Notifications inside dashboard container -->
        <a href="view_notifications.php" class="bell-icon">
            <span>🔔</span>
        </a>

        <button class="toggle-menu-btn" onclick="toggleMenu()">View Options</button>

        <div id="userMenu" class="menu">
            <a href="view_profile.php">View Your Profile</a>
            <a href="view_appointments_U.php">Your Appointments</a>
            <a href="view_issues_U.php">Your Reported Issues</a>
            <a href="view_subscriptions_U.php">Your Subscriptions</a>
        </div>

        <div class="section-header">City Services</div>

        <div class="services">
            <div class="service-card">
                <h3>Report an Issue</h3>
                <p>Submit a report for city-related issues such as drainage or road maintenance.</p>
                <a href="submit_report.php">Report Now</a>
            </div>
            <div class="service-card">
                <h3>Book a Doctor Appointment</h3>
                <p>Find and book appointments with specialists in your city.</p>
                <a href="book_appointment.php">Book Appointment</a>
            </div>
            <div class="service-card">
                <h3>Subscribe to Services</h3>
                <p>Manage subscriptions for city services like garbage collection.</p>
                <a href="subscribe_service.php">Subscribe Now</a>
            </div>
            <div class="service-card">
                <h3>View Emergency Contacts</h3>
                <p>Access contact numbers for police, fire, and medical emergencies.</p>
                <a href="emergency_contacts.php">View Contacts</a>
            </div>
            <div class="service-card">
                <h3>View Educational Institutions</h3>
                <p>Explore schools, colleges, and universities in your city.</p>
                <a href="view_institiuations.php">View Institutions</a>
            </div>
            <div class="service-card">
                <h3>View Restaurants</h3>
                <p>Find the best restaurants and dining options nearby.</p>
                <a href="view_restaurants.php">View Restaurants</a>
            </div>
            <div class="service-card">
    <h3>View Shops</h3>
    <p>Explore all shops in the city, categorized by type and location.</p>
    <a href="view_shops.php">View Shops</a>
</div>

        </div>

        <div class="feedback-section">
            <h2>Submit Feedback</h2>
            <form class="feedback-form" method="POST" action="">
                <label for="feedback_type">Feedback Type</label>
                <select id="feedback_type" name="feedback_type" required>
                    <option value="Suggestion">Suggestion</option>
                    <option value="Complaint">Complaint</option>
                    <option value="Query">Query</option>
                </select>

                <label for="feedback_text">Feedback Description</label>
                <textarea id="feedback_text" name="feedback_text" rows="4" required></textarea>

                <label for="rating">Rating (1-5)</label>
                <input id="rating" name="rating" type="number" min="1" max="5" required>

                <button type="submit" name="submit_feedback">Submit Feedback</button>
                <button class="theme-toggle-btn" onclick="toggleTheme()">🌙</button>

            </form>
        </div>
    </div>
</body>
</html>
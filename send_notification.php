<?php
session_start();
require_once("database.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['username'] !== 'Admin') {
    header("Location: index.php");
    exit();
}

$admin_id = $_SESSION['user']['user_id'];
$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_notification'])) {
    $message = trim($_POST['message']);
    $filter_type = isset($_POST['filter_type']) ? $_POST['filter_type'] : null;
    $specific_user = isset($_POST['specific_user']) ? trim($_POST['specific_user']) : null;
    $ward_no = isset($_POST['ward_no']) ? (int)$_POST['ward_no'] : null;
    $street_no = isset($_POST['street_no']) ? (int)$_POST['street_no'] : null;
    $building_no = isset($_POST['building_no']) ? (int)$_POST['building_no'] : null;

    if (empty($message) || empty($filter_type)) {
        $error_message = "Message and filter type are required.";
    } else {
        $query = "";
        $users = [];

   
if ($filter_type === "all_users") {
    $query = "SELECT u.user_id 
              FROM users u
              JOIN user_roles ur ON u.user_id = ur.user_id
              JOIN roles r ON ur.role_id = r.role_id
              WHERE r.role_name = 'citizen'";
} elseif ($filter_type === "specific_user" && $specific_user) {
    $query = "SELECT u.user_id 
              FROM users u
              JOIN user_roles ur ON u.user_id = ur.user_id
              JOIN roles r ON ur.role_id = r.role_id
              WHERE r.role_name = 'citizen' AND (u.email = '$specific_user' OR u.username = '$specific_user')";
} elseif ($filter_type === "all_doctors") {
    $query = "SELECT u.user_id 
              FROM users u
              JOIN user_roles ur ON u.user_id = ur.user_id
              JOIN roles r ON ur.role_id = r.role_id
              WHERE r.role_name = 'doctor'";
} elseif ($filter_type === "specific_doctor" && $specific_user) {
    $query = "SELECT u.user_id 
              FROM users u
              JOIN user_roles ur ON u.user_id = ur.user_id
              JOIN roles r ON ur.role_id = r.role_id
              WHERE r.role_name = 'doctor' AND (u.email = '$specific_user' OR u.username = '$specific_user')";
} elseif ($filter_type === "address" && $ward_no) {
    $query = "SELECT u.user_id 
              FROM users u
              JOIN user_address ua ON u.user_id = ua.user_id
              JOIN address a ON ua.address_id = a.address_id
              JOIN user_roles ur ON u.user_id = ur.user_id
              JOIN roles r ON ur.role_id = r.role_id
              WHERE a.ward_no = '$ward_no'";
    if ($street_no) {
        $query .= " AND a.street_no = '$street_no'";
    }
    if ($building_no) {
        $query .= " AND a.building_no = '$building_no'";
    }
}


        if (!empty($query)) {
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $users[] = $row['user_id'];
                }

     
                $conn->begin_transaction();
                try {
                    $stmt = $conn->prepare("INSERT INTO notifications (message, admin_id) VALUES (?, ?)");
                    $stmt->bind_param("si", $message, $admin_id);
                    $stmt->execute();
                    $notification_id = $stmt->insert_id;
                    $stmt->close();

                    foreach ($users as $user_id) {
                        $stmt = $conn->prepare("INSERT INTO notification_recipients (notification_id, user_id) VALUES (?, ?)");
                        $stmt->bind_param("ii", $notification_id, $user_id);
                        $stmt->execute();
                    }
                    $conn->commit();
                    $success_message = "Notification sent successfully!";
                } catch (Exception $e) {
                    $conn->rollback();
                    $error_message = "Failed to send notification: " . $e->getMessage();
                }
            } else {
                $error_message = "No users found for the selected filter.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Notification</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1e1e2f, #292940);
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: rgba(25, 25, 35, 0.9);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.6);
            width: 100%;
            max-width: 600px;
        }

        h1 {
            color: #fff;
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            color: #ccc;
            font-size: 14px;
            margin-bottom: 8px;
        }

        textarea, select, input, button {
            background-color: #2a2a2a;
            border: 1px solid #444;
            border-radius: 8px;
            color: #fff;
            font-size: 16px;
            padding: 12px;
            margin-bottom: 15px;
        }

        button {
            background-color: #4caf50;
            border: none;
            cursor: pointer;
            font-weight: bold;
            color: white;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        .message {
            color: #4caf50;
            font-size: 16px;
            margin-bottom: 15px;
            text-align: center;
        }

        .error {
            color: #ff4444;
            font-size: 16px;
            margin-bottom: 15px;
            text-align: center;
        }

        .optional-fields {
            display: none;
        }

        .optional-fields input {
            width: 100%;
            padding: 10px;
        }
        back-btn {
            background: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            display: inline-block;
            margin-bottom: 20px;
        }

        .back-btn:hover {
            background-color: #45a049;
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Send Notification</h1>
        <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>

        <?php if (!empty($success_message)) : ?>
            <p class="message"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <?php if (!empty($error_message)) : ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="filter_type">Filter Type:</label>
            <select name="filter_type" id="filter_type" required>
                <option value="">Select Filter</option>
                <option value="all_users">All Users</option>
                <option value="specific_user">Specific User</option>
                <option value="all_doctors">All Doctors</option>
                <option value="specific_doctor">Specific Doctor</option>
                <option value="address">Address</option>
            </select>

            <label for="message">Message:</label>
            <textarea name="message" id="message" placeholder="Type your notification here..." required></textarea>

            <div class="optional-fields" id="specific-user-section">
                <label for="specific_user">User Email or Name:</label>
                <input type="text" name="specific_user" id="specific_user" placeholder="Enter Email or Name">
            </div>

            <div class="optional-fields" id="address-section">
                <label for="ward_no">Ward Number:</label>
                <input type="number" name="ward_no" id="ward_no" placeholder="Enter Ward Number">
                <label for="street_no">Street Number:</label>
                <input type="number" name="street_no" id="street_no" placeholder="Enter Street Number">
                <label for="building_no">Building Number:</label>
                <input type="number" name="building_no" id="building_no" placeholder="Enter Building Number">
            </div>

            <button type="submit" name="send_notification">Send Notification</button>
        </form>
    </div>

    <script>
        const filterType = document.getElementById('filter_type');
        const specificUserSection = document.getElementById('specific-user-section');
        const addressSection = document.getElementById('address-section');

        filterType.addEventListener('change', function () {
            specificUserSection.style.display = 'none';
            addressSection.style.display = 'none';

            if (this.value === 'specific_user' || this.value === 'specific_doctor') {
                specificUserSection.style.display = 'block';
            } else if (this.value === 'address') {
                addressSection.style.display = 'block';
            }
        });
    </script>
</body>
</html>

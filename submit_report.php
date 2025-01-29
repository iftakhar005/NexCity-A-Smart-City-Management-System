<?php
session_start();
require_once("database.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $issue_type = isset($_POST['issue_type']) ? trim($_POST['issue_type']) : null;
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $ward_no = isset($_POST['ward_no']) ? intval($_POST['ward_no']) : null;
    $street_no = isset($_POST['street_no']) ? intval($_POST['street_no']) : null;
    $building_no = isset($_POST['building_no']) && $_POST['building_no'] !== "" ? intval($_POST['building_no']) : null; // Optional field
    $user_id = isset($_SESSION['user']['user_id']) ? $_SESSION['user']['user_id'] : null;

    if ($issue_type && $description && $ward_no && $street_no && $user_id) {

        $address_query = "INSERT INTO address (ward_no, street_no, building_no) VALUES (?, ?, ?)";
        $address_stmt = $conn->prepare($address_query);
        $address_stmt->bind_param("iii", $ward_no, $street_no, $building_no);

        if ($address_stmt->execute()) {
    
            $location_id = $conn->insert_id;

        
            $issue_query = "
                INSERT INTO issues (user_id, issue_type, description, reported_date, status, assigned_department_id, location_id) 
                VALUES (?, ?, ?, NOW(), 'Pending', NULL, ?)
            ";
            $issue_stmt = $conn->prepare($issue_query);
            $issue_stmt->bind_param("issi", $user_id, $issue_type, $description, $location_id);

            if ($issue_stmt->execute()) {
                echo "<script>alert('Issue reported successfully! We will address it soon.'); window.location.href='dashboard.php';</script>";
            } else {
                echo "<script>alert('Error: Could not report the issue. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('Error: Could not save address. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Error: Please fill in all required fields (Ward No and Street No are required).');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report an Issue</title>
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

        .report-container {
            background: rgba(25, 25, 35, 0.9);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            padding: 40px;
            width: 100%;
            max-width: 600px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #4caf50;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 16px;
            margin-bottom: 10px;
            color: #cfcfcf;
        }

        input, select, textarea {
            font-size: 16px;
            padding: 10px;
            margin-bottom: 20px;
            border: none;
            border-radius: 8px;
            background: #2a2a3d;
            color: #fff;
        }

        textarea {
            resize: none;
            height: 120px;
        }

        .submit-btn {
            background: #4caf50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .submit-btn:hover {
            background: #45a049;
        }

        .back-btn {
            text-align: center;
            margin-top: 20px;
        }

        .back-btn a {
            color: #4caf50;
            text-decoration: none;
            font-size: 14px;
        }

        .back-btn a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <h1>Report an Issue</h1>
        <form action="submit_report.php" method="POST">
            <label for="issue-type">Issue Type</label>
            <select id="issue-type" name="issue_type" required>
                <option value="Drainage Problem">Drainage Problem</option>
                <option value="Road Maintenance">Road Maintenance</option>
                <option value="Street Lighting">Street Lighting</option>
                <option value="Garbage Collection">Garbage Collection</option>
                <option value="Other">Other</option>
            </select>

            <label for="description">Description</label>
            <textarea id="description" name="description" placeholder="Describe the issue in detail" required></textarea>

            <label for="ward_no">Ward No</label>
            <input type="number" id="ward_no" name="ward_no" placeholder="Enter ward number" required>

            <label for="street_no">Street No</label>
            <input type="number" id="street_no" name="street_no" placeholder="Enter street number" required>

            <label for="building_no">Building No (Optional)</label>
<input type="number" id="building_no" name="building_no" placeholder="Enter building number (optional)">

            <button type="submit" class="submit-btn">Submit Report</button>
        </form>

        <div class="back-btn">
            <a href="dashboard.php">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>

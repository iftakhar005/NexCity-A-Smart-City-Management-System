<?php
session_start();
require_once("database.php");


if (!isset($_SESSION['user']) || $_SESSION['user']['selected_role'] !== 'doctor') {
    header("Location: login.php");
    exit();
}


$user_id = $_SESSION['user']['user_id'];


$query = "SELECT * FROM doctors WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

if (!$doctor) {
    echo "Doctor details not found.";
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_time = trim($_POST['appointment_time']);
    $appointment_days = trim($_POST['appointment_days']);

 
    $update_query = "
        UPDATE doctors 
        SET appointment_time = ?, appointment_days = ? 
        WHERE user_id = ?
    ";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ssi", $appointment_time, $appointment_days, $user_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Availability updated successfully.');</script>";
    } else {
        echo "<script>alert('Error updating availability.');</script>";
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Availability</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
        }

        button {
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .back-button {
            margin-top: 15px;
            text-align: center;
        }

        .back-button a {
            text-decoration: none;
            color: #fff;
            background-color: #6c757d;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 16px;
        }

        .back-button a:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update Your Availability</h2>

        <form method="POST">
            <label for="appointment_time">Appointment Time</label>
            <input type="text" name="appointment_time" id="appointment_time" 
                   value="<?php echo htmlspecialchars($doctor['appointment_time']); ?>" required>

            <label for="appointment_days">Appointment Days</label>
            <input type="text" name="appointment_days" id="appointment_days" 
                   value="<?php echo htmlspecialchars($doctor['appointment_days']); ?>" required>

            <button type="submit">Update Availability</button>
        </form>

        <div class="back-button">
            <a href="doctor_dashboard.php">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>

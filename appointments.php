<?php
session_start();
require_once("database.php");

if (!isset($_SESSION['user']['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user']['user_id'];


$doctorQuery = "SELECT doctor_id FROM doctors WHERE user_id = ?";
$stmt = $conn->prepare($doctorQuery);
if (!$stmt) {
    die("Error: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No doctor found for the logged-in user.");
}

$doctorData = $result->fetch_assoc();
$doctor_id = $doctorData['doctor_id'];
$stmt->close();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {

        $approveQuery = "UPDATE appointments SET status = 'approved' WHERE appointment_id = ?";
        $stmt = $conn->prepare($approveQuery);
        if (!$stmt) {
            die("Error: " . $conn->error);
        }
        $stmt->bind_param("i", $appointment_id);
        if ($stmt->execute()) {
           
            $insertPatientQuery = "
           
    INSERT INTO patients (appointment_id, name,age, patient_situation)
    SELECT a.appointment_id, u.username, 
           FLOOR(DATEDIFF(CURDATE(), u.dob) / 365.25) AS age, 'N/A'
    FROM appointments a
    JOIN users u ON a.user_id = u.user_id
    WHERE a.appointment_id = ?";

           
            $stmt = $conn->prepare($insertPatientQuery);
            if (!$stmt) {
                die("Error: " . $conn->error);
            }
            $stmt->bind_param("i", $appointment_id);
            if ($stmt->execute()) {
                echo "Patient record inserted successfully.";
            } else {
                echo "Failed to insert patient record.";
            }
            $stmt->close();
        }
   
    } elseif ($action === 'cancel') {
  
        $cancelQuery = "UPDATE appointments SET status = 'canceled' WHERE appointment_id = ?";
        $stmt = $conn->prepare($cancelQuery);
        if (!$stmt) {
            die("Error: " . $conn->error);
        }
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();
        $stmt->close();
    }
}


$countQuery = "
    SELECT COUNT(*) AS total_upcoming 
    FROM appointments 
    WHERE doctor_id = ? AND status IN ('pending')";
$stmt = $conn->prepare($countQuery);
if (!$stmt) {
    die("Error: " . $conn->error);
}
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

$totalUpcoming = 0;
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $totalUpcoming = $data['total_upcoming'];
}
$stmt->close();


$query = "
SELECT 
    appointments.appointment_id,
    appointments.appointment_date AS date,
    appointments.status,
    ailments.ailment AS ailment,
    ailments.ailment_description,
    users.username AS patient
FROM 
    appointments
JOIN 
    users ON appointments.user_id = users.user_id
JOIN 
    ailments ON appointments.ailment_id = ailments.ailment_id
WHERE 
    appointments.doctor_id = ? 
    AND appointments.status = 'pending'
ORDER BY 
    appointments.appointment_id ASC";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query Preparation Failed: " . $conn->error);
}
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments</title>
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
        .container {
            width: 90%;
            max-width: 1000px;
            background: rgba(25, 25, 35, 0.9);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #4caf50;
        }
        .stats {
            margin-bottom: 20px;
            font-size: 18px;
            color: #ccc;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #444;
        }
        table th {
            background: #4caf50;
            color: #fff;
        }
        .approve-btn, .cancel-btn {
            padding: 5px 10px;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .approve-btn {
            background: #4caf50;
        }
        .cancel-btn {
            background: #e53935;
        }
        .approve-btn:hover {
            background: #45a049;
        }
        .cancel-btn:hover {
            background: #d32f2f;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #4caf50;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
        }
        .back-btn:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Pending Appointments</h1>
        <div class="stats">
            Total Upcoming Appointments: <strong><?php echo $totalUpcoming; ?></strong>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Ailment</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($appointments) > 0): ?>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['patient']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['ailment']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['ailment_description']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['appointment_id']; ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="approve-btn">Approve</button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['appointment_id']; ?>">
                                    <input type="hidden" name="action" value="cancel">
                                    <button type="submit" class="cancel-btn">Cancel</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No pending appointments</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="doctor_dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>

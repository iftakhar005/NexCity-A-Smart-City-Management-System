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
    die("Query Preparation Failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Doctor not found for the logged-in user.");
}

$doctorData = $result->fetch_assoc();
$doctor_id = $doctorData['doctor_id'];
$stmt->close();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_patient'])) {
    $patient_id = $_POST['patient_id'];
    $prescribed_medicine = $_POST['prescribed_medicine'];
    $prescribed_test = $_POST['prescribed_test'];
    $patient_situation = $_POST['patient_situation'];

    
    $updateQuery = "
        UPDATE patients 
        SET 
            patient_situation = ? 
        WHERE patient_id = ?";
    $stmt = $conn->prepare($updateQuery);
    if (!$stmt) {
        die("Query Preparation Failed: " . $conn->error);
    }
    $stmt->bind_param("si", $patient_situation, $patient_id);
    if ($stmt->execute()) {
        echo "Patient information updated successfully.";
    } else {
        echo "Error updating patient information.";
    }
    $stmt->close();

   
    if (!empty($prescribed_medicine)) {
        $updateMedicineQuery = "
            INSERT INTO patient_medicine (patient_id, prescribed_medicine)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE prescribed_medicine = ?";
        $stmt = $conn->prepare($updateMedicineQuery);
        if (!$stmt) {
            die("Query Preparation Failed: " . $conn->error);
        }
        $stmt->bind_param("iss", $patient_id, $prescribed_medicine, $prescribed_medicine);
        $stmt->execute();
        $stmt->close();
    }

    if (!empty($prescribed_test)) {
        $updateTestQuery = "
            INSERT INTO patient_test (patient_id, prescribed_test)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE prescribed_test = ?";
        $stmt = $conn->prepare($updateTestQuery);
        if (!$stmt) {
            die("Query Preparation Failed: " . $conn->error);
        }
        $stmt->bind_param("iss", $patient_id, $prescribed_test, $prescribed_test);
        $stmt->execute();
        $stmt->close();
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_patient'])) {
    $patient_id = $_POST['patient_id'];


    $deleteQuery = "DELETE FROM patients WHERE patient_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    if (!$stmt) {
        die("Query Preparation Failed: " . $conn->error);
    }
    $stmt->bind_param("i", $patient_id);
    if ($stmt->execute()) {
        echo "Patient record deleted successfully.";
    } else {
        echo "Error deleting patient record.";
    }
    $stmt->close();
}


$query = "
SELECT 
    p.patient_id,
    p.name AS patient_name,
    p.age AS patient_age,
    pm.prescribed_medicine,
    pt.prescribed_test,
    p.patient_situation,
    a.appointment_id,
    a.appointment_date AS date,
    a.status,
    ail.ailment,
    ail.ailment_description,
    (
        SELECT COUNT(*) 
        FROM appointments a2 
        WHERE a2.appointment_id = p.appointment_id AND a2.status = 'approved'
    ) AS total_patient
FROM 
    appointments a
JOIN 
    patients p ON a.appointment_id = p.appointment_id
JOIN 
    ailments ail ON a.ailment_id = ail.ailment_id
LEFT JOIN 
    patient_medicine pm ON p.patient_id = pm.patient_id
LEFT JOIN 
    patient_test pt ON p.patient_id = pt.patient_id
WHERE 
    a.doctor_id = ? 
    AND a.status = 'approved'
ORDER BY 
    a.appointment_id ASC";

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


$totalPatientsQuery = "SELECT COUNT(DISTINCT appointment_id) AS total_patients 
                       FROM appointments 
                       WHERE doctor_id = ? AND status = 'approved'";
$stmt = $conn->prepare($totalPatientsQuery);
if (!$stmt) {
    die("Query Preparation Failed: " . $conn->error);
}
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$totalPatientsResult = $stmt->get_result();
$totalPatientsData = $totalPatientsResult->fetch_assoc();
$totalPatients = $totalPatientsData['total_patients'];
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients Overview</title>
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
        .input-field {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .input-field:focus {
            border-color: #4caf50;
            outline: none;
        }
        .update-btn {
            padding: 5px 10px;
            background: #4caf50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .update-btn:hover {
            background: #45a049;
        }
        .delete-btn {
            padding: 5px 10px;
            background: #e53935;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background: #c62828;
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
        <p style="text-align: center; color: #4caf50; font-size: 24px;">Patients Overview</p>
        <p style="text-align: center; color: #fff; font-size: 18px;">Total Patients: <?php echo $totalPatients; ?></p>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Patient Name</th>
                    <th>Age</th>
                    <th>Ailment</th>
                    <th>Description</th>
                    <th>Prescribed Medicine</th>
                    <th>Prescribed Test</th>
                    <th>Patient Situation</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($appointments) > 0): ?>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['patient_age']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['ailment']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['ailment_description']); ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="patient_id" value="<?php echo $appointment['patient_id']; ?>">
                                    <input type="text" name="prescribed_medicine" class="input-field" value="<?php echo htmlspecialchars($appointment['prescribed_medicine']); ?>" placeholder="Medicine">
                            </td>
                            <td>
                                <input type="text" name="prescribed_test" class="input-field" value="<?php echo htmlspecialchars($appointment['prescribed_test']); ?>" placeholder="Test">
                            </td>
                            <td>
                                <input type="text" name="patient_situation" class="input-field" value="<?php echo htmlspecialchars($appointment['patient_situation']); ?>" placeholder="Situation">
                            </td>
                            <td>
                                <button type="submit" name="update_patient" class="update-btn">Update</button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="patient_id" value="<?php echo $appointment['patient_id']; ?>">
                                    <button type="submit" name="delete_patient" class="delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center;">No approved appointments</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="doctor_dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>

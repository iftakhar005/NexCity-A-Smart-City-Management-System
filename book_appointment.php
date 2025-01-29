<?php
session_start(); 

require_once("database.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

$specialization_query = "SELECT DISTINCT specialization FROM doctors";
$specialization_result = mysqli_query($conn, $specialization_query);
$specializations = [];
while ($row = mysqli_fetch_assoc($specialization_result)) {
    $specializations[] = $row['specialization'];
}

$hospital_query = "SELECT hospital_id, hospital_name FROM hospital";
$hospital_result = mysqli_query($conn, $hospital_query);
$hospitals = [];
while ($row = mysqli_fetch_assoc($hospital_result)) {
    $hospitals[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $specialization = $_POST['specialization'];
    $hospital_id = $_POST['hospital'];

    $doctor_query = "SELECT doctor_id, name, contact_number, appointment_time, appointment_days 
                     FROM doctors 
                     WHERE specialization = ? AND hospital_id = ?";
    $stmt = mysqli_prepare($conn, $doctor_query);
    mysqli_stmt_bind_param($stmt, "si", $specialization, $hospital_id);
    mysqli_stmt_execute($stmt);
    $doctor_result = mysqli_stmt_get_result($stmt);
    $doctors = [];
    while ($row = mysqli_fetch_assoc($doctor_result)) {
        $doctors[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book an Appointment</title>
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

        .appointment-container {
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

        select, input {
            font-size: 16px;
            padding: 10px;
            margin-bottom: 20px;
            border: none;
            border-radius: 8px;
            background: #2a2a3d;
            color: #fff;
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

        ul {
            list-style: none;
            margin-top: 20px;
        }

        ul li {
            margin-bottom: 15px;
            background: #2a2a3d;
            padding: 10px;
            border-radius: 8px;
        }

        ul li a {
            color: #4caf50;
            text-decoration: none;
            font-size: 14px;
            display: block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="appointment-container">
        <h1>Book an Appointment</h1>
        <form method="POST">
            <label for="specialization">Select Specialization</label>
            <select id="specialization" name="specialization" required>
                <option value="" disabled selected>Select Specialization</option>
                <?php foreach ($specializations as $specialization): ?>
                    <option value="<?php echo htmlspecialchars($specialization); ?>">
                        <?php echo htmlspecialchars($specialization); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="hospital">Select Hospital</label>
            <select id="hospital" name="hospital" required>
                <option value="" disabled selected>Select Hospital</option>
                <?php foreach ($hospitals as $hospital): ?>
                    <option value="<?php echo htmlspecialchars($hospital['hospital_id']); ?>">
                        <?php echo htmlspecialchars($hospital['hospital_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="submit-btn">Search Doctors</button>
        </form>

        <?php if (isset($doctors)): ?>
            <h2 style="margin-top: 20px;">Available Doctors</h2>
            <ul>
                <?php if (count($doctors) > 0): ?>
                    <?php foreach ($doctors as $doctor): ?>
                        <li>
                            <strong>Name:</strong> <?php echo htmlspecialchars($doctor['name']); ?><br>
                            <strong>Contact:</strong> <?php echo htmlspecialchars($doctor['contact_number']); ?><br>
                            <strong>Appointment Time:</strong> <?php echo htmlspecialchars($doctor['appointment_time']); ?><br>
                            <strong>Available Days:</strong> <?php echo htmlspecialchars($doctor['appointment_days']); ?><br>
                            <!-- Replace form with a simple anchor link -->
                            <a href="take_appointment.php?doctor_id=<?php echo urlencode($doctor['doctor_id']); ?>&doctor_name=<?php echo urlencode($doctor['name']); ?>&available_days=<?php echo urlencode($doctor['appointment_days']); ?>&appointment_time=<?php echo urlencode($doctor['appointment_time']); ?>&hospital_id=<?php echo urlencode($hospital_id); ?>" class="submit-btn">Book Now</a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No doctors found for the selected criteria.</li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>

        <div class="back-btn">
            <a href="dashboard.php">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>

<?php

session_start();


require_once("database.php");


if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];


if (isset($_GET['cancel']) && isset($_GET['appointment_id'])) {
    $appointment_id = $_GET['appointment_id'];

    
    $check_sql = "SELECT status FROM appointments WHERE appointment_id = ? AND user_id = ? AND status = 'pending'";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $appointment_id, $user['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
      
        $cancel_sql = "UPDATE appointments SET status = 'canceled' WHERE appointment_id = ? AND user_id = ?";
        $stmt = $conn->prepare($cancel_sql);
        $stmt->bind_param("ii", $appointment_id, $user['user_id']);
        $stmt->execute();
        $stmt->close();
        
        header("Location: view_appointments_U.php");
        exit();
    } else {
        
        echo "<script>alert('Appointment cannot be canceled.');</script>";
    }
}


$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = " AND (d.name LIKE '%$search_term%' OR h.hospital_name LIKE '%$search_term%' OR a.status LIKE '%$search_term%')";
}


$sql = "
    SELECT 
        a.appointment_id,
        a.appointment_date,
        d.appointment_time,
        a.status,
        d.name,
        d.specialization,
        h.hospital_name,
        ad.street_no AS hospital_street,
        ad.ward_no AS hospital_ward,
        ad.building_no AS hospital_building
    FROM 
        appointments a
    JOIN doctors d ON a.doctor_id = d.doctor_id
    JOIN hospital h ON d.hospital_id = h.hospital_id
    JOIN address ad ON h.location_id = ad.address_id
    WHERE 
        a.user_id = ? 
        $search_query
    ORDER BY 
        a.appointment_date DESC, d.appointment_time DESC;
";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user['user_id']); 
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
    <title>Your Appointments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #1e1e2f;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .appointments-container {
            background: #292940;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            width: 90%;
            max-width: 900px;
        }
        h1 {
            text-align: center;
            color: #4caf50;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4caf50;
            color: white;
        }
        td {
            background-color: #333;
        }
        .cancel-btn {
            background: #f44336;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .cancel-btn:hover {
            background: #d32f2f;
        }
        .status {
            font-weight: bold;
            padding: 5px;
            border-radius: 4px;
        }
        .status.active {
            background-color: #4caf50;
            color: white;
        }
        .status.canceled {
            background-color: #f44336;
            color: white;
        }
        .status.pending {
            background-color: #ff9800;
            color: white;
        }
        .back-btn {
            background: #4caf50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .back-btn:hover {
            background: #45a049;
        }
        .search-bar {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .search-bar input {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 250px;
            margin-right: 10px;
        }
        .search-bar button {
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #4caf50;
            color: white;
            border: none;
        }
        .search-bar button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <div class="appointments-container">
        <h1>Your Appointments</h1>

        <!-- Search Bar -->
        <div class="search-bar">
            <form method="GET" action="view_appointments_U.php">
                <input type="text" name="search" placeholder="Search by Doctor, Hospital, or Status" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <?php if (empty($appointments)) : ?>
            <p style="text-align: center; color: #f44336;">You have no active appointments.</p>
        <?php else : ?>
            <table>
                <thead>
                    <tr>
                        <th>Doctor's Name</th>
                        <th>Specialization</th>
                        <th>Hospital</th>
                        <th>Hospital Location</th>
                        <th>Appointment Date</th>
                        <th>Appointment Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['specialization']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['hospital_name']); ?></td>
                            <td>
    <?php 
        echo "Ward: " . htmlspecialchars($appointment['hospital_ward']) . ", "; 
        echo "Street: " . htmlspecialchars($appointment['hospital_street']) . ", "; 
        echo "Building: " . htmlspecialchars($appointment['hospital_building']);
    ?>
</td>

                            <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                            <td>
                                <span class="status <?php echo strtolower($appointment['status']); ?>">
                                    <?php echo ucfirst($appointment['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                    if (strtolower($appointment['status']) === 'pending') : ?>
                                    <a href="view_appointments_U.php?cancel=true&appointment_id=<?php echo $appointment['appointment_id']; ?>" 
                                       class="cancel-btn" 
                                       onclick="return confirm('Are you sure you want to cancel this appointment?');">
                                       Cancel
                                    </a>
                                <?php else : ?>
                                    <span>-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Back Button -->
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>

</body>
</html>

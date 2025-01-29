<?php

session_start();

require_once("database.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $phone_number = $_POST['phone_number'];
    $dob = $_POST['dob'];
    $ward_no = $_POST['ward_no'];
    $street_no = $_POST['street_no'];
    $building_no = $_POST['building_no'];

    $update_sql = "UPDATE users SET username = ?, phone_number = ?, dob = ? WHERE user_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssi", $username, $phone_number, $dob, $user['user_id']);
    $stmt->execute();

    $_SESSION['user']['username'] = $username;
    $_SESSION['user']['phone_number'] = $phone_number;
    $_SESSION['user']['dob'] = $dob;

    $check_address_sql = "SELECT address_id FROM user_address WHERE user_id = ?";
    $stmt = $conn->prepare($check_address_sql);
    $stmt->bind_param("i", $user['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $update_address_sql = "UPDATE address SET ward_no = ?, street_no = ?, building_no = ? WHERE address_id = (SELECT address_id FROM user_address WHERE user_id = ?)";
        $stmt = $conn->prepare($update_address_sql);
        $stmt->bind_param("sssi", $ward_no, $street_no, $building_no, $user['user_id']);
        $stmt->execute();
    } else {
        $insert_address_sql = "INSERT INTO address (ward_no, street_no, building_no) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_address_sql);
        $stmt->bind_param("sss", $ward_no, $street_no, $building_no);
        $stmt->execute();

        $address_id = $stmt->insert_id;

        $insert_user_address_sql = "INSERT INTO user_address (user_id, address_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_user_address_sql);
        $stmt->bind_param("ii", $user['user_id'], $address_id);
        $stmt->execute();
    }

    $stmt->close();
}

$sql = "SELECT 
            u.user_id,
            u.username,
            u.email,
            u.phone_number,
            u.dob,
            u.registration_date,
            a.ward_no,
            a.street_no,
            a.building_no,
            (SELECT COUNT(*) FROM issues WHERE user_id = u.user_id) AS total_issues_reported,
            (SELECT COUNT(*) FROM appointments WHERE user_id = u.user_id) AS total_appointments_booked
        FROM 
            users u
        LEFT JOIN user_address ua ON u.user_id = ua.user_id
        LEFT JOIN address a ON ua.address_id = a.address_id
        WHERE 
            u.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user['user_id']);  
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
} else {
    die("User not found.");
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
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
            min-height: 100vh;
        }
        .profile-container {
            background: #292940;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            width: 90%;
            max-width: 800px;
        }
        h1 {
            text-align: center;
            color: #4caf50;
            margin-bottom: 20px;
        }
        .profile-info {
            margin: 20px 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .profile-info p {
            font-size: 18px;
        }
        .profile-info strong {
            color: #4caf50;
        }
        .back-btn, .edit-btn {
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
        .back-btn:hover, .edit-btn:hover {
            background: #45a049;
        }
        .edit-form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .edit-form input {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #4caf50;
            background-color: #fff;
            color: #333;
        }
        .edit-form label {
            font-size: 16px;
            color: #4caf50;
        }
        .edit-form button {
            width: 100%;
            padding: 12px;
            background-color: #4caf50;
            border: none;
            color: white;
            border-radius: 8px;
            cursor: pointer;
        }
        .edit-form button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h1>User Profile</h1>

        <?php if (isset($_GET['edit']) && $_GET['edit'] == 'true') : ?>
            <form action="view_profile.php" method="POST" class="edit-form">
    <label for="username">Username</label>
    <input type="text" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>

    <label for="phone_number">Phone Number</label>
    <input type="text" name="phone_number" value="<?php echo htmlspecialchars($user_data['phone_number']); ?>" required>

    <label for="dob">Date of Birth</label>
    <input type="date" name="dob" value="<?php echo htmlspecialchars($user_data['dob']); ?>" required>

    <label for="total_issues_reported">Total Issues Reported</label>
    <input type="number" name="total_issues_reported" value="<?php echo htmlspecialchars($user_data['total_issues_reported']); ?>" readonly>

    <label for="total_appointments_booked">Total Appointments Booked</label>
    <input type="number" name="total_appointments_booked" value="<?php echo htmlspecialchars($user_data['total_appointments_booked']); ?>" readonly>

    <label for="ward_no">Ward No</label>
    <input type="text" name="ward_no" value="<?php echo htmlspecialchars($user_data['ward_no']); ?>" required>

    <label for="street_no">Street No</label>
    <input type="text" name="street_no" value="<?php echo htmlspecialchars($user_data['street_no']); ?>" required>

    <label for="building_no">Building No</label>
    <input type="text" name="building_no" value="<?php echo htmlspecialchars($user_data['building_no']); ?>" required>

    <button type="submit">Save Changes</button>
</form>

        <?php else : ?>
            <div class="profile-info">
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user_data['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email']); ?></p>
                <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user_data['phone_number']); ?></p>
                <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user_data['dob']); ?></p>
                <p><strong>Address:</strong> Ward <?php echo htmlspecialchars($user_data['ward_no']); ?>, Street <?php echo htmlspecialchars($user_data['street_no']); ?>, Building <?php echo htmlspecialchars($user_data['building_no']); ?></p>
                <p><strong>Total Issues Reported:</strong> <?php echo htmlspecialchars($user_data['total_issues_reported']); ?></p>
                <p><strong>Total Appointments Booked:</strong> <?php echo htmlspecialchars($user_data['total_appointments_booked']); ?></p>
            </div>
            <a href="view_profile.php?edit=true" class="edit-btn">Edit Profile</a>
        <?php endif; ?>

        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>

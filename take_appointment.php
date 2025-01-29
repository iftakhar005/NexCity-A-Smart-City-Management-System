<?php
session_start();
require_once("database.php");


if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];


$doctor_id = $_GET['doctor_id'];
$doctor_name = $_GET['doctor_name'];
$available_days = $_GET['available_days'];
$appointment_time = $_GET['appointment_time'];
$hospital_id = $_GET['hospital_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $selected_day = $_POST['selected_day'];
    $appointment_date = $_POST['appointment_date'];
    $ailment = $_POST['ailment'];
    $ailment_description = $_POST['ailment_description'];
    $status = 'pending';


    $selected_date_day = date('l', strtotime($appointment_date));
    if ($selected_day != $selected_date_day) {
        echo "<script>alert('Selected day does not match the date. Please try again.');</script>";
        exit();
    }


    $pending_query = "SELECT * FROM appointments WHERE user_id = ? AND appointment_date = ? AND status = 'pending'";
    $stmt = mysqli_prepare($conn, $pending_query);
    mysqli_stmt_bind_param($stmt, "is", $user['user_id'], $appointment_date);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('You already have a pending appointment on this day.');</script>";
        exit();
    }

   
    $ailment_query = "INSERT INTO ailments (ailment, ailment_description) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $ailment_query);
    mysqli_stmt_bind_param($stmt, "ss", $ailment, $ailment_description);
    if (!mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Failed to save ailment details.');</script>";
        exit();
    }
    $ailment_id = mysqli_insert_id($conn); 

    $appointment_query = "INSERT INTO appointments (user_id, doctor_id, hospital_id, ailment_id, appointment_date, status) 
                          VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $appointment_query);
    mysqli_stmt_bind_param($stmt, "iiiiss", $user['user_id'], $doctor_id, $hospital_id, $ailment_id, $appointment_date, $status);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Appointment booked successfully!'); window.location.href='book_appointment.php';</script>";
    } else {
        echo "<script>alert('Failed to book appointment. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Appointment</title>
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

select, input, textarea {
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
    height: 80px;
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

ul {
    list-style: none;
    padding: 0;
}

li {
    margin-bottom: 20px;
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
    <div class="appointment-container">
        <h1>Book Appointment with <?php echo htmlspecialchars($doctor_name); ?></h1>
        <p><strong>Available Days:</strong> <?php echo htmlspecialchars($available_days); ?></p>
        <p><strong>Time:</strong> <?php echo htmlspecialchars($appointment_time); ?></p>

        <form method="POST">
            <label for="selected_day">Select Appointment Day:</label>
            <select name="selected_day" required>
                <?php
                $days = explode(',', $available_days);
                foreach ($days as $day): ?>
                    <option value="<?php echo htmlspecialchars($day); ?>">
                        <?php echo htmlspecialchars($day); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="appointment_date">Select Appointment Date:</label>
            <input type="date" name="appointment_date" required>

            <label for="ailment">Ailment:</label>
            <input type="text" name="ailment" placeholder="Enter your ailment" required>

            <label for="ailment_description">Ailment Description:</label>
            <textarea name="ailment_description" placeholder="Describe your ailment in detail" required></textarea>

            <button type="submit" class="submit-btn">Confirm Appointment</button>
        </form>

        <div class="back-btn">
            <a href="book_appointment.php">Back to Search</a>
        </div>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>

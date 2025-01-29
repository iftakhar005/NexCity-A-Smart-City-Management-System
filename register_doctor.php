<?php
session_start();
require_once("database.php");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$hospitals_query = "SELECT hospital_id, hospital_name, location_id FROM hospital";
$hospitals_result = $conn->query($hospitals_query);
$hospitals = $hospitals_result->fetch_all(MYSQLI_ASSOC);

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['contact_number'], $_POST['appointment_time'], $_POST['name'], $_POST['email'], $_POST['password'], $_POST['specialization'],
     $_POST['working_days'], $_POST['hospital'], $_POST['dob'])) {

        $name = "Dr. " . $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $contact_number = $_POST['contact_number'];
        $appointment_time = $_POST['appointment_time'];
        $specialization = $_POST['specialization'];
        $hospital_id = $_POST['hospital'];
        $working_days = implode(",", $_POST['working_days']);
        $dob = $_POST['dob'];
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $conn->begin_transaction();

        try {
     
            $user_check_query = "SELECT user_id FROM users WHERE email = ?";
            $user_check_stmt = $conn->prepare($user_check_query);
            $user_check_stmt->bind_param("s", $email);
            $user_check_stmt->execute();
            $user_check_result = $user_check_stmt->get_result();

            if ($user_check_result->num_rows > 0) {
                $user_row = $user_check_result->fetch_assoc();
                $user_id = $user_row['user_id'];

              
                $role_check_query = "SELECT r.role_name FROM user_roles ur
                                     JOIN roles r ON ur.role_id = r.role_id
                                     WHERE ur.user_id = ?";
                $role_check_stmt = $conn->prepare($role_check_query);
                $role_check_stmt->bind_param("i", $user_id);
                $role_check_stmt->execute();
                $role_check_result = $role_check_stmt->get_result();

                $roles = [];
                while ($role_row = $role_check_result->fetch_assoc()) {
                    $roles[] = $role_row['role_name'];
                }

                if (in_array("doctor", $roles)) {
                    $message = "This email is already registered as a doctor.";
                    throw new Exception($message);  
                } else {
                   
                    $update_user_query = "UPDATE users SET username = ?, password = ?, phone_number = ? WHERE email = ?";
                    $stmt_update_user = $conn->prepare($update_user_query);
                    $stmt_update_user->bind_param("ssss", $name, $hashed_password, $contact_number, $email);

                    if ($stmt_update_user->execute()) {
                       
                        $doctor_query = "INSERT INTO doctors (name, specialization, hospital_id, contact_number, appointment_time, appointment_days, email, user_id) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt_doctor = $conn->prepare($doctor_query);
                        $stmt_doctor->bind_param("ssiisssi", $name, $specialization, $hospital_id, $contact_number, $appointment_time, $working_days, $email, $user_id);

                        if ($stmt_doctor->execute()) {
                           
                            $role_query = "INSERT INTO user_roles (user_id, role_id) VALUES (?, 2)";
                            $stmt_role = $conn->prepare($role_query);
                            $stmt_role->bind_param("i", $user_id);
                            if ($stmt_role->execute()) {
                                $conn->commit();
                                $message = "Doctor registration successful!";
                            } else {
                                $conn->rollback();
                                $message = "Error assigning doctor role: " . $conn->error;
                            }
                        } else {
                            $conn->rollback();
                            $message = "Error inserting doctor details: " . $conn->error;
                        }
                    } else {
                        $conn->rollback();
                        $message = "Error updating user data: " . $conn->error;
                    }
                }
            } else {
             
                $insert_user_query = "INSERT INTO users (username, email, password, phone_number, dob) VALUES (?, ?, ?, ?, ?)";
                $stmt_insert_user = $conn->prepare($insert_user_query);
                $stmt_insert_user->bind_param("sssss", $name, $email, $hashed_password, $contact_number, $dob);

                if ($stmt_insert_user->execute()) {
                    $user_id = $stmt_insert_user->insert_id;

            
                    $doctor_query = "INSERT INTO doctors (name, specialization, hospital_id, contact_number, appointment_time, appointment_days, email, user_id) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt_doctor = $conn->prepare($doctor_query);
                    $stmt_doctor->bind_param("ssiisssi", $name, $specialization, $hospital_id, $contact_number, $appointment_time, $working_days, $email, $user_id);

                    if ($stmt_doctor->execute()) {
                       
                        $role_query = "INSERT INTO user_roles (user_id, role_id) VALUES (?, 2)";
                        $stmt_role = $conn->prepare($role_query);
                        $stmt_role->bind_param("i", $user_id);
                        if ($stmt_role->execute()) {
                            $conn->commit();
                            $message = "Doctor registration successful!";
                        } else {
                            $conn->rollback();
                            $message = "Error assigning doctor role: " . $conn->error;
                        }
                    } else {
                        $conn->rollback();
                        $message = "Error inserting doctor details: " . $conn->error;
                    }
                } else {
                    $conn->rollback();
                    $message = "Error inserting user details: " . $conn->error;
                }
            }
        } catch (Exception $e) {
            $conn->rollback();
            $message = "An error occurred: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Registration</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1c1b2f, #3b3b98);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 500px;
            text-align: center;
            backdrop-filter: blur(10px);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
            font-size: 28px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            font-size: 16px;
            color: #dcdcdc;
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"], input[type="password"], input[type="email"], input[type="datetime-local"], select, textarea, input[type="date"] {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            border: none;
            background-color: rgba(0, 0, 0, 0.2);
            color: #fff;
            outline: none;
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        input[type="text"]:focus, input[type="password"]:focus, input[type="email"]:focus, input[type="datetime-local"]:focus, select:focus, input[type="date"]:focus, textarea:focus {
            background-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 8px rgba(72, 196, 255, 0.8);
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            font-size: 18px;
            background: linear-gradient(135deg, #6c63ff, #00d4ff);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s, background 0.3s;
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #5734b8, #009bce);
            transform: translateY(-3px);
        }

        .form-group input {
  
            margin-bottom: 20px;
            text-align: left;
        }
        }

        p {
            margin-top: 20px;
            color: #ccc;
        }

        a {
            color: #72d4ff;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
            color: #fff;
        }

        @media (max-width: 768px) {
            .container {
                padding: 25px;
                width: 90%;
            }
        }

        .form-group input, .form-group select, .form-group textarea {
            box-sizing: border-box;  
        }

        .submit-btn:active {
            transform: scale(0.98);
        }

        .success-message {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .error-message {
            background-color: #f44336;
            color: white;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .info-message {
            background-color: #2196F3;
            color: white;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .btn {
            padding: 14px;
            font-size: 16px;
            color: #fff;
            background: linear-gradient(135deg, rgb(47, 43, 130), rgb(38, 36, 138));
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            display: inline-block;
            transition: transform 0.2s, background 0.3s;
        }

        .btn:hover {
            background: linear-gradient(135deg, #d84315, #ad1457);
            transform: translateY(-3px);
        }
        

    </style>
</head>
<body>
    <div class="container">
        <h2>Doctor Registration</h2>

        <form action="" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="contact_number">Phone Number:</label>
                <input type="text" id="contact_number" name="contact_number" required>
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" required>
            </div>
            <div class="form-group">
                <label for="appointment_time">Appointment Time:</label>
                <input type="text" id="appointment_time" name="appointment_time" required>
            </div>
            <div class="form-group">
                <label for="working_days">Working Days:</label>
                <select name="working_days[]" id="working_days" multiple required>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>
            </div>
            <div class="form-group">
                <label for="hospital">Hospital:</label>
                <select name="hospital" id="hospital" required>
                    <?php foreach ($hospitals as $hospital) { ?>
                        <option value="<?php echo $hospital['hospital_id']; ?>"><?php echo $hospital['hospital_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="specialization">Specialization:</label>
                <input type="text" id="specialization" name="specialization" placeholder="Enter specialization" required>
            </div>
            
            <button type="submit" class="submit-btn">Register as Doctor</button>
        </form>

        <button onclick="window.location.href='register_citizen.php'" class="btn" style="margin-top: 20px;">Are You a Citizen? Register Here</button>

<p style="text-align: center; margin-top: 20px;">Already have an account? <a href="login.php" style="color: #72d4ff;">Login here</a></p>
    </div>
    <script>
        <?php if (!empty($message)): ?>
            alert("<?php echo $message; ?>");
        <?php endif; ?>
    </script>
</body>
</html>
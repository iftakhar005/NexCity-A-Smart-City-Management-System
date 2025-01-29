<?php
require_once("database.php");

$message = "";  
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $ward_no = $_POST['ward_no'];
    $street_no = $_POST['street_no'];
    $building_no = $_POST['building_no'];
    $dob = $_POST['dob'];
    $role = "citizen"; 

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

            
            if (in_array("citizen", $roles)) {
             
                $message = "This email is already registered as a citizen.";
                throw new Exception($message);  
            }

           
            if (in_array("doctor", $roles)) {
               
                $role_query = "SELECT role_id FROM roles WHERE role_name = 'citizen'";
                $role_stmt = $conn->prepare($role_query);
                $role_stmt->execute();
                $role_result = $role_stmt->get_result();

                if ($role_result->num_rows > 0) {
                    $role_row = $role_result->fetch_assoc();
                    $role_id = $role_row['role_id'];

    
                    $user_role_query = "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)";
                    $user_role_stmt = $conn->prepare($user_role_query);
                    $user_role_stmt->bind_param("ii", $user_id, $role_id);
                    $user_role_stmt->execute();
                } else {
                    
                    $message = "Role not found in the roles table.";
                    throw new Exception($message); 
                }
            }
        } else {
           

    
            $address_check_query = "SELECT address_id FROM address WHERE ward_no = ? AND street_no = ? AND building_no = ?";
            $address_check_stmt = $conn->prepare($address_check_query);
            $address_check_stmt->bind_param("sss", $ward_no, $street_no, $building_no);
            $address_check_stmt->execute();
            $address_check_result = $address_check_stmt->get_result();

            if ($address_check_result->num_rows > 0) {
                
                $address_row = $address_check_result->fetch_assoc();
                $address_id = $address_row['address_id'];
            } else {
              
                $address_query = "INSERT INTO address (ward_no, street_no, building_no) VALUES (?, ?, ?)";
                $address_stmt = $conn->prepare($address_query);
                $address_stmt->bind_param("sss", $ward_no, $street_no, $building_no);
                $address_stmt->execute();
                $address_id = $conn->insert_id;
            }

           
            $user_query = "INSERT INTO users (username, `password`, email, phone_number, dob, registration_date, `role`) 
                           VALUES (?, ?, ?, ?, ?, NOW(), ?)";
            $user_stmt = $conn->prepare($user_query);
            $user_stmt->bind_param("ssssss", $username, $hashed_password, $email, $phone_number, $dob, $role);
            $user_stmt->execute();
            $user_id = $conn->insert_id; 

            
            $user_address_query = "INSERT INTO user_address (user_id, address_id) VALUES (?, ?)";
            $user_address_stmt = $conn->prepare($user_address_query);
            $user_address_stmt->bind_param("ii", $user_id, $address_id);
            $user_address_stmt->execute();

            
            $role_query = "SELECT role_id FROM roles WHERE role_name = ?";
            $role_stmt = $conn->prepare($role_query);
            $role_stmt->bind_param("s", $role);
            $role_stmt->execute();
            $role_result = $role_stmt->get_result();

            if ($role_result->num_rows > 0) {
                $role_row = $role_result->fetch_assoc();
                $role_id = $role_row['role_id'];

               
                $user_role_query = "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)";
                $user_role_stmt = $conn->prepare($user_role_query);
                $user_role_stmt->bind_param("ii", $user_id, $role_id);
                $user_role_stmt->execute();
            } else {
                
                $message = "Role not found in roles table.";
                throw new Exception($message);  
            }
        }

        $conn->commit();
        $message = "Registration successful!";
    } catch (Exception $e) {
        $conn->rollback();
        $message = $e->getMessage();  
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen Registration Portal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
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
            max-width: 400px;
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

        input[type="text"], input[type="password"], input[type="email"], input[type="date"] {
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

        input:focus {
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
            margin-bottom: 15px;
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #5734b8, #009bce);
            transform: translateY(-3px);
        }

        .extra-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
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

        .btn:active {
            transform: scale(0.98);
        }

        @media (max-width: 768px) {
            .container {
                padding: 25px;
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Citizen Registration</h2>

        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="text" id="phone_number" name="phone_number" required>
            </div>
            <div class="form-group">
                <h3 style="color: #dcdcdc; font-size: 18px; margin-bottom: 10px;">Enter your address:</h3>
            </div>
            <div class="form-group">
                <label for="ward_no">Ward No:</label>
                <input type="text" id="ward_no" name="ward_no" required>
            </div>
            <div class="form-group">
                <label for="street_no">Street No:</label>
                <input type="text" id="street_no" name="street_no" required>
            </div>
            <div class="form-group">
                <label for="building_no">Building No:</label>
                <input type="text" id="building_no" name="building_no" required>
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" required>
            </div>
            <button type="submit" class="submit-btn" name="submit">Register as Citizen</button>
        </form>
        <div class="extra-buttons">
            <a href="register_doctor.php" class="btn">Are you a Doctor? Register here</a>
            <a href="login.php" class="btn">Already have an account? Login here</a>
        </div>
    </div>

    <script>
        <?php if (!empty($message)): ?>
            alert("<?php echo $message; ?>");
        <?php endif; ?>
    </script>
</body>
</html>


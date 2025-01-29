<?php
session_start();
require_once("database.php");

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $selected_role = $_POST['role']; 

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "<p>Invalid email.</p>";
    } else {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $user_id = $user['user_id'];

            $role_stmt = $conn->prepare("
                SELECT r.role_name 
                FROM user_roles ur
                JOIN roles r ON ur.role_id = r.role_id
                WHERE ur.user_id = ?
            ");
            $role_stmt->bind_param("i", $user_id);
            $role_stmt->execute();
            $role_result = $role_stmt->get_result();

            $roles = [];
            while ($row = $role_result->fetch_assoc()) {
                $roles[] = $row['role_name'];
            }

            if (in_array($selected_role, $roles)) {
                $_SESSION['user'] = [
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'city' => $user['city'],
                    'roles' => $roles, 
                    'selected_role' => $selected_role,
                    'user_id' => $user['user_id'],
                ];

                switch ($selected_role) {
                    case 'admin':
                        header("Location: admin_dashboard.php");
                        break;
                    case 'doctor':
                        header("Location: doctor_dashboard.php");
                        break;
                    case 'citizen':
                        header("Location: Dashboard.php");
                        break;
                }
                exit();
            } else {
                echo "<p>You do not have access as a $selected_role.</p>";
            }
        } else {
            echo "<p>Incorrect password.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
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

        input[type="text"], input[type="password"], select {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            border: none;
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            outline: none;
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        input[type="text"]:focus, input[type="password"]:focus, select:focus {
            background-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 8px rgba(72, 196, 255, 0.8);
        }

        select {
            appearance: none;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.3));
            background-image: url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"%3E%3Cpath fill="white" d="M7 7l3 3 3-3h-6z"/%3E%3C/svg%3E');
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 1rem;
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        select:hover {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.4));
        }

        option {
            background: #1c1b2f;
            color: #fff;
            padding: 10px;
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
            margin-top: 5px;
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

        .submit-btn:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Login</h2>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
                <label for="role">Select Role:</label>
                <select id="role" name="role" required>
                    <option value="" disabled selected>Choose a role</option>
                    <option value="admin">Admin</option>
                    <option value="doctor">Doctor</option>
                    <option value="citizen">Citizen</option>
                </select>
            </div>
            <button type="submit" name="submit" class="submit-btn">Login</button>
        </form>
        <p>Don't have an account? <a href="index.php">Register here</a></p>
    </div>
</body>
</html>

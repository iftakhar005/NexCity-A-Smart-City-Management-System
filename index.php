<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to NextCity</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
       
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1d2671, #c33764);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
            color: #fff;
        }

        .container {
            max-width: 900px; 
            padding: 50px 30px; 
            background: rgba(0, 0, 0, 0.7);
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        h1 {
            font-size: 60px;
            margin-bottom: 20px;
            color: #ff6f61;
        }

        p {
            font-size: 18px;
            margin-bottom: 40px;
            color: #f1f1f1;
            line-height: 1.8;
        }

        .button-group {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .button {
            padding: 15px 40px;
            font-size: 20px;
            font-weight: 600;
            color: #fff;
            border: 2px solid transparent;
            border-radius: 8px;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .button:hover {
            background: transparent;
            border-color: #00f2fe;
            color: #00f2fe;
            transform: scale(1.1);
        }

        .button:active {
            transform: scale(0.98);
        }

        .login-link {
            font-size: 16px;
            margin-top: 20px;
            color: #f1f1f1;
        }

        .login-link a {
            color: #00f2fe;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 40px;
            }

            p {
                font-size: 16px;
            }

            .button {
                padding: 12px 30px;
                font-size: 18px;
            }

            .container {
                padding: 40px 20px; 
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to NextCity</h1>
        <p>
            Discover smart urban living with NextCity. Whether you're a doctor managing your schedule or a citizen reporting issues, 
            we empower you with tools for a modern and convenient city experience.
        </p>
        <div class="button-group">
            <a href="register_doctor.php" class="button">Register as a Doctor</a>
            <a href="register_citizen.php" class="button">Register as a Citizen</a>
        </div>
        <div class="login-link">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>

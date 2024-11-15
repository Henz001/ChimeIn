<?php
// Include your database connection file (if using an external file for DB connection)
require_once 'config.php'; // Adjust the path if needed

// Initialize error message variable
$error_message = "";
$email = $password = ""; // To retain the values after the form is submitted

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the data from the POST request and sanitize it
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic validation (e.g., check if all fields are filled)
    if (empty($email) || empty($password)) {
        $error_message = "Both email and password are required.";
    }
    // Check if email exists in the database
    else {
        $stmt = $pdo->prepare("SELECT id, email, password FROM users WHERE email = ?");
        $stmt->bindParam(1, $email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Compare the plaintext password directly
            if ($password === $user['password']) {
                // Start a session and log the user in
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                
                // Redirect to the dashboard or home page after successful login
                header("Location: home.php");
                exit();
            } else {
                $error_message = "Incorrect password.";
            }
        } else {
            $error_message = "No account found with that email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - ChimeIn</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap');

        /* Basic reset for the body and html */
        html, body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Open Sans', sans-serif; /* Apply Open Sans font here */
        }

        body {
            background-color: #f5f5f5;
            color: #333;
            font-size: 16px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 30px;
            box-sizing: border-box;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo span {
            font-size: 32px;
            font-weight: 600;
            color: #4169E1;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border-radius: 20px;
            border: 1px solid #ddd;
            outline: none;
            font-size: 1rem;
            font-family: 'Open Sans', sans-serif; /* Ensure input uses Open Sans */
            box-sizing: border-box;
        }

        .form-group input:focus {
            border-color: #4169E1;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: #4169E1;
            border: none;
            border-radius: 20px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #5A8FE5;
        }

        .text-center {
            text-align: center;
            margin-top: 20px;
        }

        .text-center a {
            color: #4169E1;
            text-decoration: none;
        }

        .text-center a:hover {
            text-decoration: underline;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <span>ChimeIn</span>
        </div>

        <!-- Show error message if there's any -->
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <strong>Error!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Form to collect login credentials -->
        <form action="login.php" method="POST">
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>

        <div class="text-center">
            <p>Don't have an account? <a href="signup.php">Sign up</a></p>
        </div>
    </div>
</body>
</html>
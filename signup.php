<?php
// Include your database connection file (if using an external file for DB connection)
require_once 'config.php'; // Adjust the path if needed

// Initialize error message variable
$error_message = "";
$username = $email = ""; // To retain the values after the form is submitted

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the data from the POST request and sanitize it
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic validation (e.g., check if all fields are filled)
    if (empty($username) || empty($email) || empty($password)) {
        $error_message = "All fields are required.";
    }

    // Validate the email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    }

    // Password validation (minimum 8 characters, for example)
    elseif (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters.";
    }

    // Check if the username or email already exists in the database
    else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR username = ?");
        $stmt->bindParam(1, $email);
        $stmt->bindParam(2, $username);
        $stmt->execute();

        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $error_message = "This email or username is already taken. Please choose a different one.";
        }
    }

    // If no error, insert the user into the database
    if (empty($error_message)) {
        // **Updated line**: Insert the user with the current timestamp (created_at will be automatically set)
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bindParam(1, $username);
        $stmt->bindParam(2, $email);
        $stmt->bindParam(3, $password); // No hashing here

        try {
            // Execute the prepared statement
            $stmt->execute();
            
            // Redirect the user to the login page after successful sign-up
            header("Location: login.html");
            exit(); // Ensure no further code is executed after the redirect
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - ChimeIn</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap');

        /* Basic reset for the body and html */
        html, body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Open Sans', sans-serif;
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
            box-sizing: border-box;
            font-family: 'Open Sans', sans-serif; /* Add this line */
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

        <!-- Form to collect user data -->
        <form action="signup.php" method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Username" required value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn">Sign Up</button>
        </form>

        <div class="text-center">
            <p>Already have an account? <a href="login.html">Login</a></p>
        </div>
    </div>
</body>
</html>

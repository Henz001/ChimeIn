<?php
// Start the session to access session variables
session_start();

// Include database connection
require_once 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_title = trim($_POST['post_title']);
    $post_content = trim($_POST['post_content']);
    
    // Validate input
    if (!empty($post_title) && !empty($post_content)) {
        try {
            // Get the logged-in user's ID and username from session
            $user_id = $_SESSION['user_id'];
            $username = $_SESSION['username'];  // Assuming 'username' is stored in the session

            // Insert the new post into the database
            $stmt = $pdo->prepare("INSERT INTO posts (user_id, username, post_title, content) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $username, $post_title, $post_content]);

            // Redirect to home.php after post is created
            header("Location: home.php");
            exit(); // Make sure to stop further execution of code after redirect
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } else {
        $error_message = "Please fill in both the title and content fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Post | ChimeIn</title>
    <style>
        /* Include styles for the page */
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap');
        
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
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #4169E1; /* RoyalBlue */
            color: white;
        }

        header .logo span {
            font-size: 24px;
            font-weight: 600;
        }

        header .search-bar input {
            padding: 8px;
            width: 300px;
            border-radius: 20px;
            border: none;
            outline: none;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        header .post-btn {
            padding: 8px 20px;
            background-color: #ffffff;
            border: none;
            color: #4169E1; /* RoyalBlue */
            border-radius: 20px;
            font-weight: bold;
            cursor: pointer;
        }

        header .post-btn:hover {
            background-color: #5A8FE5;
        }

        .hamburger {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: 25px;
            height: 20px;
            cursor: pointer;
        }

        .hamburger div {
            width: 100%;
            height: 4px;
            background-color: white;
        }

        .menu {
            display: none;
            position: absolute;
            top: 60px;
            right: 20px;
            background-color: #4169E1;
            border-radius: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 200px;
            padding: 10px 0;
        }

        .menu a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            display: block;
        }

        .menu a:hover {
            background-color: #5A8FE5;
        }

        main {
            padding: 20px;
        }

        .form-container {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-container input, .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-container button {
            background-color: #4169E1;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #5A8FE5;
        }

        .form-container .message {
            text-align: center;
            margin-top: 20px;
            font-size: 1.1rem;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #4169E1;
            color: white;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <span>ChimeIn</span>
    </div>
    <div class="search-bar">
        <input type="text" placeholder="Search...">
    </div>
    <div class="header-right">
        <a href="home.php">
            <button class="post-btn">Home</button>
        </a>
        <div class="hamburger" onclick="toggleMenu()">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
</header>

<div class="menu" id="menu">
<a href="#">Profile</a>
    <a href="#">Messages</a>
    <a href="#">Friend Requests</a>
    <a href="#">Friends</a>
    <a href="#">Settings</a>
</div>

<main>
    <div class="form-container">
        <h2>Create New Post</h2>

        <?php if (isset($message)): ?>
            <div class="message" style="color: green;"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="message" style="color: red;"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="create-post.php" method="POST">
            <input type="text" name="post_title" placeholder="Post Title" required>
            <textarea name="post_content" placeholder="Write your post here..." rows="5" required></textarea>
            <button type="submit">Create Post</button>
        </form>
    </div>
</main>

<footer>
    <p>&copy; 2024 ClickSpace. All Rights Reserved.</p>
</footer>

<script>
    function toggleMenu() {
        const menu = document.getElementById("menu");
        const hamburger = document.querySelector('.hamburger');
        menu.style.display = menu.style.display === "block" ? "none" : "block";
        hamburger.classList.toggle('active');
    }
</script>

</body>
</html>

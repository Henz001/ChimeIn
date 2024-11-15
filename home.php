<?php
// Start the session to access session variables
session_start();

// Include your database connection
require_once 'config.php';

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Query to get posts from the database
try {
    $stmt = $pdo->query("SELECT p.id, p.post_title, p.content, p.created_at, u.username 
                         FROM posts p 
                         JOIN users u ON p.user_id = u.id 
                         ORDER BY p.created_at DESC");

    $posts = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

// Function to display time ago
function time_ago($timestamp) {
    $time_ago = strtotime($timestamp);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    $minutes = round($seconds / 60); // value 60 is seconds
    $hours = round($seconds / 3600); // value 3600 is 60 minutes * 60 sec
    $days = round($seconds / 86400); // value 86400 is 24 hours * 60 minutes * 60 sec
    $weeks = round($seconds / 604800); // value 604800 is 7 days * 24 hours * 60 minutes * 60 sec
    $months = round($seconds / 2629440); // value 2629440 is (365 days in a year / 12 months) * 24 hours * 60 minutes * 60 sec
    $years = round($seconds / 31553280); // value 31553280 is 365.25 days * 24 hours * 60 minutes * 60 sec

    if ($seconds <= 60) {
        return "Just Now";
    } else if ($minutes <= 60) {
        if ($minutes == 1) {
            return "one minute ago";
        } else {
            return "$minutes minutes ago";
        }
    } else if ($hours <= 24) {
        if ($hours == 1) {
            return "an hour ago";
        } else {
            return "$hours hours ago";
        }
    } else if ($days <= 7) {
        if ($days == 1) {
            return "yesterday";
        } else {
            return "$days days ago";
        }
    } else if ($weeks <= 4.3) { // 4.3 == 30/7
        if ($weeks == 1) {
            return "one week ago";
        } else {
            return "$weeks weeks ago";
        }
    } else if ($months <= 12) {
        if ($months == 1) {
            return "one month ago";
        } else {
            return "$months months ago";
        }
    } else {
        if ($years == 1) {
            return "one year ago";
        } else {
            return "$years years ago";
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
    <title>ChimeIn</title>
    <style>
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
        }

        /* Header styles */
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

        /* Container for Hamburger Icon and Post Button */
        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* POST Button styles */
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
            background-color: #5A8FE5; /* Lighter shade of RoyalBlue */
        }

        /* Hamburger Menu Styles */
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

        /* Menu styles */
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

        /* Show the menu when the hamburger is clicked */
        .menu.active {
            display: block;
        }

        /* Main content area */
        main {
            padding: 20px;
        }

        /* Post styles */
        .post {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 15px;
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .post-user .username {
            font-weight: bold;
        }

        .post-meta .time-ago {
            font-size: 0.9rem;
            color: #777;
        }

        .post-body {
            margin-bottom: 15px;
        }

        .post-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .post-content {
            font-size: 1rem;
            line-height: 1.5;
            color: #555;
        }

        .post-footer {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: #777;
        }

        .post-footer span {
            cursor: pointer;
        }

        /* Styling for the Like/Dislike buttons container */
        .like-dislike-buttons {
            display: flex;
            gap: 10px;
        }

        .like-button, .dislike-button, .comment-button {
            padding: 6px 12px;
            background-color: #ffffff;
            color: #4169E1;
            border: 2px solid #4169E1;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s;
        }

        .like-button:hover, .dislike-button:hover, .comment-button:hover {
            background-color: #5A8FE5;
            color: white;
        }

        /* Footer styles */
        footer {
            text-align: center;
            padding: 10px;
            background-color: #4169E1; /* RoyalBlue */
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
        <!-- Use an anchor tag to wrap the Post button for redirection -->
        <a href="create_post_page.php"><button class="post-btn">Post</button></a>
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
    <?php if (empty($posts)): ?>
        <p>No posts available.</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <div class="post-header">
                    <div class="post-user">
                        <span class="username"><?php echo htmlspecialchars($post['username']); ?></span>
                    </div>
                    <div class="post-meta">
                        <span class="time-ago"><?php echo time_ago($post['created_at']); ?></span>
                    </div>
                </div>
                <div class="post-body">
                    <h2 class="post-title"><?php echo htmlspecialchars($post['post_title']); ?></h2>
                    <p class="post-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                </div>
                <div class="post-footer">
                    <div class="like-dislike-buttons">
                        <button class="like-button">Like</button>
                        <button class="dislike-button">Dislike</button>
                        <button class="comment-button">Comment</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<footer>
    <p>&copy; 2024 ClickSpace</p>
</footer>

<script>
    function toggleMenu() {
        const menu = document.getElementById("menu");
        menu.classList.toggle("active");
    }
</script>
</body>
</html>

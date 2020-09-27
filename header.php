<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <title>Manga Website</title>
</head>

<body>

    <header>
        <?php
        require 'backend/database.php';
        $query = "SELECT * FROM pagecontent WHERE contentName = 'groupName'";
        $result = mysqli_query($conn, $query) or die("Could not execute query on Line 30");
        $row = mysqli_fetch_array($result);
        ?>
        <h1><?php echo $row['content'];
            $conn->close(); ?></h1>
        <nav class='pubNav'>
            <ul>
                <a href="index.php?page=home">Home</a>
                <a href="index.php?page=reader">Series</a>
            </ul>
        </nav>

        <?php
        if ($_SESSION) {
            echo "<nav class='staffNav'>";
            echo "<ul>";
            if ($_SESSION['role'] == "staff") {
                echo    "<a href='index.php?page=createseries'>Create Series</a>";
                echo    "<a href='index.php?page=createchapter'>Create Chapter</a>";
            }
            echo "<a href='index.php?page=logout'>Logout</a>";
            echo "</ul>";
            echo "</nav>";
        } else {
            echo "<nav>";
            echo "<ul>";
            echo "<a href='index.php?page=signup'>Signup</a>
                <a href='index.php?page=login'>Login</a>";
            echo "</ul>";
            echo "</nav>";
        }
        ?>
    </header>
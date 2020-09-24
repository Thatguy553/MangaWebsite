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
        <h1>Scanlation_Group</h1>
        <nav class='pubNav'>
            <ul>
                <a href="/home">Home</a>
                <a href="/reader">Series</a>
            </ul>
        </nav>

        <?php
        if ($_SESSION) {
            echo "<nav class='staffNav'>";
            echo "<ul>";
            if ($_SESSION['role'] == "staff") {
                echo    "<a href='/createseries'>Create Series</a>";
                echo    "<a href='/createchapter'>Create Chapter</a>";
            }
            echo "<a href='/logout'>Logout</a>";
            echo "</ul>";
            echo "</nav>";
        } else {
            echo "<nav>";
            echo "<ul>";
            echo "<a href='/signup'>Signup</a>
                <a href='/login'>Login</a>";
            echo "</ul>";
            echo "</nav>";
        }
        ?>
    </header>
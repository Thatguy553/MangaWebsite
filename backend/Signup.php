<?php

if (isset($_POST['signup-enter'])) {

    require 'Database.php';

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordc = $_POST['passwordc'];

    if (empty($username) || empty($email) || empty($password) || empty($passwordc)) {
        header("Location: ../Signup.php?error=emptyfields&uid=" . $username . "&email=" . $email);
        exit();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match("/^[a-zA-Z0-9]*$/", $username)) {
        header("Location: ../Signup.php?error=invalidmailusername");
        exit();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../Signup.php?error=invalidmail&username=" . $username);
        exit();
    } else if (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
        header("Location: ../Signup.php?error=invalidmail=" . $email);
        exit();
    } else if ($password !== $passwordc) {
        header("Location: ../Signup.php?error=passwordcheck&username=" . $username . "&mail=" . $email);
        exit();
    } else {
        $sql = "SELECT UserName FROM users WHERE UserName=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../Signup.php?error=sqlerror");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $resultCheck = mysqli_stmt_num_rows($stmt);
            if ($resultCheck > 0) {
                header("Location: ../Signup.php?error=usertaken&mail=" . $email);
                exit();
            } else {

                $sql = "INSERT INTO users (UserName, UserEmail, UserPassword) VALUES (?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    header("Location: ../Signup.php?error=sqlerrorinsert");
                    exit();
                } else {
                    $hashedpwd = password_hash($password, PASSWORD_DEFAULT);

                    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashedpwd);
                    mysqli_stmt_execute($stmt);
                    header("Location: ../Signup.php?signup=success");
                    exit();
                }
            }
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    header("Location: ../index.php");
    exit();
}
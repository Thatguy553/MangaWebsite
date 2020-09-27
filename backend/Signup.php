<?php

if (isset($_POST['signup-enter'])) {

    require 'database.php';

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordc = $_POST['passwordc'];

    if (empty($username) || empty($email) || empty($password) || empty($passwordc)) {
        header("Location: ../signup?error=emptyfields&uid=" . $username . "&email=" . $email);
        exit();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match("/^[a-zA-Z0-9]*$/", $username)) {
        header("Location: ../signup?error=invalidmailusername");
        exit();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../signup?error=invalidmail&username=" . $username);
        exit();
    } else if (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
        header("Location: ../signup?error=invalidmail=" . $email);
        exit();
    } else if ($password !== $passwordc) {
        header("Location: ../signup?error=passwordcheck&username=" . $username . "&mail=" . $email);
        exit();
    } else {
        $sql = "SELECT username FROM users WHERE username =?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../signup?error=sqlerror");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $resultCheck = mysqli_stmt_num_rows($stmt);
            if ($resultCheck > 0) {
                header("Location: ../signup?error=usertaken&mail=" . $email);
                exit();
            } else {

                $sql = "INSERT INTO users (username, gmail, password, role) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    header("Location: ../signup?error=sqlerrorinsert");
                    exit();
                } else {
                    $hashedpwd = password_hash($password, PASSWORD_DEFAULT);
                    $role = "reader";

                    mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $hashedpwd, $role);
                    mysqli_stmt_execute($stmt);
                    header("Location: ../signup?signup=success");
                    exit();
                }
            }
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    header("Location: ../home");
    exit();
}
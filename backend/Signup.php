<?php

if (isset($_POST['signup-enter'])) {

    require 'Database.php';

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordc = $_POST['passwordc'];

    if (empty($username) || empty($email) || empty($password) || empty($passwordc)) {
<<<<<<< Updated upstream
        header("Location: ../home?page=signup&error=emptyfields&uid=" . $username . "&email=" . $email);
        exit();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match("/^[a-zA-Z0-9]*$/", $username)) {
        header("Location: ../home?page=signup&error=invalidmailusername");
        exit();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../home?page=signup&error=invalidmail&username=" . $username);
        exit();
    } else if (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
        header("Location: ../home?page=signup&error=invalidmail=" . $email);
        exit();
    } else if ($password !== $passwordc) {
        header("Location: ../home?page=signup&error=passwordcheck&username=" . $username . "&mail=" . $email);
=======
        header("Location: ../index.php?page=signup&error=emptyfields&uid=" . $username . "&email=" . $email);
        exit();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match("/^[a-zA-Z0-9]*$/", $username)) {
        header("Location: ../index.php?page=signup&error=invalidmailusername");
        exit();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../index.php?page=signup&error=invalidmail&username=" . $username);
        exit();
    } else if (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
        header("Location: ../index.php?page=signup&error=invalidmail=" . $email);
        exit();
    } else if ($password !== $passwordc) {
        header("Location: ../index.php?page=signup&error=passwordcheck&username=" . $username . "&mail=" . $email);
>>>>>>> Stashed changes
        exit();
    } else {
        $sql = "SELECT username FROM users WHERE username =?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
<<<<<<< Updated upstream
            header("Location: ../home?page=signup&error=sqlerror");
=======
            header("Location: ../index.php?page=signup&error=sqlerror");
>>>>>>> Stashed changes
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $resultCheck = mysqli_stmt_num_rows($stmt);
            if ($resultCheck > 0) {
<<<<<<< Updated upstream
                header("Location: ../home?page=signup&error=usertaken&mail=" . $email);
=======
                header("Location: ../index.php?page=signup&error=usertaken&mail=" . $email);
>>>>>>> Stashed changes
                exit();
            } else {

                $sql = "INSERT INTO users (username, gmail, password, role) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
<<<<<<< Updated upstream
                    header("Location: ../home?page=signup&error=sqlerrorinsert");
=======
                    header("Location: ../index.php?page=signup&error=sqlerrorinsert");
>>>>>>> Stashed changes
                    exit();
                } else {
                    $hashedpwd = password_hash($password, PASSWORD_DEFAULT);
                    $role = "reader";

                    mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $hashedpwd, $role);
                    mysqli_stmt_execute($stmt);
<<<<<<< Updated upstream
                    header("Location: ../home?page=signup&signup=success");
=======
                    header("Location: ../index.php?page=signup&signup=success");
>>>>>>> Stashed changes
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
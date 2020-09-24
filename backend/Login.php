<?php

if (isset($_POST['loginb'])) {
    require 'Database.php';

    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        header("Location: ../home?page=login&error=emptyfields");
        exit();
    } else {
        $sql = "SELECT * FROM users WHERE username=? OR username=?;";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../home?page=login&error=sqlerror");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "ss", $username, $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($result)) {
                $Pcheck = password_verify($password, $row['password']);
                if ($Pcheck == false) {
                    header("Location: ../home?page=login&error=wrongpwd");
                    exit();
                } else if ($Pcheck == true) {
                    session_start();
                    $_SESSION['ID'] = $row['UID'];
                    $_SESSION['name'] = $row['username'];
                    $_SESSION['mail'] = $row['gmail'];
                    $_SESSION['role'] = $row['role'];


                    header("Location: ../home?page=login&login=success!");
                    exit();
                } else {
                    header("Location: ../home?page=login&error=wrongpwd");
                    exit();
                }
            } else {
                header("Location: ../home?page=login&error=nouser");
                exit();
            }
        }
    }
} else {
    header("Location: ../home");
    exit();
}
<?php

$servername = "localhost";
$username = "secret";
$password = "secret";
$dbname = "secret";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

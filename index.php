<?php
include 'header.php';
$page = $_GET['page'] ?? "";
if ($_GET) {
    if ($page == "createseries") {
        include 'pages/createSeries.php';
    } else if ($page == "createchapter") {
        include 'pages/chapter.php';
    } else {
        include 'pages/home.php';
    }
} else {
    include 'pages/home.php';
}
include 'footer.php';
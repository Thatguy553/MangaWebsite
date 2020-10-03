<?php
include 'header.php';
$page = $_GET['page'] ?? "";
if ($_GET) {
    if ($page == "createseries") {
        if ($_SESSION['role'] == "staff") {
            include 'administration/createSeries.php';
        } else {
            header("Location: index.php?page=errors&error=restricted");
            exit();
        }
    } else if ($page == "createchapter") {
        if ($_SESSION['role'] == "staff") {
            include 'administration/chapter.php';
        } else {
            header("Location: index.php?page=errors&error=restricted");
            exit();
        }
    } else if ($page == "reader") {
        echo "<main class='reader'>";
        require 'backend/database.php';
        # Display Series links
        $query = "SELECT * FROM series";
        $result = mysqli_query($conn, $query) or die("Could not execute query on Line 24.");
        # Display Series Links
        if (!($_GET['series'] ?? "")) {
            echo "<section class='seriesSection'>";
            while ($row = mysqli_fetch_array($result)) {
                echo "<div class='series'>";
                echo "<a href='index.php?page=reader&series=" . rawurlencode($row['seriesTitle']) . "'><img src='series/" . $row['seriesFolder'] . "/" . $row['seriesImage'] . "' alt='" . $row['seriesTitle'] . " Image'></a>";
                echo "<a href='index.php?page=reader&series=" . rawurlencode($row['seriesTitle']) . "'>" . $row['seriesTitle'] . "</a>";
                echo "</div>";
            }
            echo "</section>";
        }

        # Display Series Infomation
        $result2 = mysqli_query($conn, $query) or die("Could not execute query on Line 33.");
        if (($_GET['series'] ?? "") && !($_GET['chapter'] ?? "")) {
            while ($row2 = mysqli_fetch_array($result2)) {
                if ($row2['seriesTitle'] == $_GET['series']) {
                    echo "<section class='seriesInfoDisplay'>";
                    echo "<div class='seriesInfo'>";
                    echo "<img src='series/" . $row2['seriesFolder'] . "/" . $row2['seriesImage'] . "' alt='" . $row2['seriesTitle'] . " Image'>";
                    echo "<p>" . $row2['seriesDescription'] . " </p>";
                    echo "</div>";
                    $chapterQuery = "SELECT * FROM chapters ORDER BY chapterNumber ASC";
                    $chapterResult = mysqli_query($conn, $chapterQuery) or die("Could not execute query on Line 29");
                    echo "<div class='chapters'>";
                    while ($chapters = mysqli_fetch_array($chapterResult)) {
                        if ($chapters['series'] == $_GET['series'])
                            echo "<a href='index.php?page=reader&series=" . rawurlencode($row2['seriesTitle']) . "&chapter=" . rawurlencode($chapters['chapterFolder']) . "'>" . $chapters['chapterName'] . "</a>";
                    }
                    echo "</div>";
                    echo "</section>";
                }
            }
        }

        #Display Chapter
        if (($_GET['series'] ?? "") && ($_GET['chapter'] ?? "")) {
            $chapString = $_GET['chapter'] ?? "";
            $newLink = preg_replace("/[^0-9]/", "", $chapString);
            $displayQuery = "SELECT * FROM chapters";
            $displayResult = mysqli_query($conn, $displayQuery) or die("Could not execute query on Line 29");
            $pageIndex = 0;
            while ($row3 = mysqli_fetch_array($result)) {
                if ($row3['seriesTitle'] == ($_GET['series'])) {
                    while ($chapters2 = mysqli_fetch_array($displayResult)) {
                        if ($chapters2['chapterFolder'] == $_GET['chapter']) {
                            echo "<section class='pages'>";
                            $pages = scandir("series/" . $row3['seriesFolder'] . "/series_" . $newLink . "/");
                            $pagesLength = count($pages);
                            $temp = $_GET['series'];
                            $stmt = $conn->prepare("SELECT series FROM chapters WHERE series=?");
                            $stmt->bind_param("s", $temp);
                            $stmt->execute();
                            $count = 0;
                            $result = $stmt->get_result();
                            while ($row = $result->fetch_assoc()) {
                                $count = $count + 1;
                            }

                            echo "<div class='chapterButtons'>";
                            if ($count > 1 && $count != $newLink && $newLink > 1) {
                                echo "<a class='last' href='index.php?page=" . $_GET['page'] . "&series=" . $_GET['series'] . "&chapter=series_" . ($newLink - 1) . "'>Last</a>";
                                echo "<a class='next' href='index.php?page=" . $_GET['page'] . "&series=" . $_GET['series'] . "&chapter=series_" . ($newLink + 1) . "'>Next</a>";
                            } else if ($newLink > 1) {
                                echo "<a class='last' href='index.php?page=" . $_GET['page'] . "&series=" . $_GET['series'] . "&chapter=series_" . ($newLink - 1) . "'>Last</a>";
                            } else if ($count > 1 && $count != $newLink) {
                                echo "<a class='next' href='index.php?page=" . $_GET['page'] . "&series=" . $_GET['series'] . "&chapter=series_" . ($newLink + 1) . "'>Next</a>";
                            }
                            echo "</div>";

                            for ($i = 2; $i < $pagesLength; $i++) {
                                echo "<img src='series/series_" . rawurlencode($_GET['series']) . "/series_" . rawurlencode($newLink) . "/" . rawurlencode($pages[$i]) . "'>";
                            }

                            echo "<div class='chapterButtons'>";
                            if ($count > 1 && $count != $newLink && $newLink > 1) {
                                echo "<a class='last' href='index.php?page=" . $_GET['page'] . "&series=" . $_GET['series'] . "&chapter=series_" . ($newLink - 1) . "'>Last</a>";
                                echo "<a class='next' href='index.php?page=" . $_GET['page'] . "&series=" . $_GET['series'] . "&chapter=series_" . ($newLink + 1) . "'>Next</a>";
                            } else if ($newLink > 1) {
                                echo "<a class='last' href='index.php?page=" . $_GET['page'] . "&series=" . $_GET['series'] . "&chapter=series_" . ($newLink - 1) . "'>Last</a>";
                            } else if ($count > 1 && $count != $newLink) {
                                echo "<a class='next' href='index.php?page=" . $_GET['page'] . "&series=" . $_GET['series'] . "&chapter=series_" . ($newLink + 1) . "'>Next</a>";
                            }
                            echo "</div>";
                            echo "</section>";
                            break;
                        }
                    }
                    break;
                }
            }
            echo "</main>";
        }
    } else if ($page == "signup") {
        include 'pages/SignupBody.php';
    } else if ($page == "login") {
        include 'pages/loginBody.php';
    } else if ($page == "logout") {
        include 'backend/Logout.php';
    } else {
        include 'pages/home.php';
    }
} else {
    include 'pages/home.php';
}
include 'footer.php';
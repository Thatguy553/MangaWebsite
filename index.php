<?php
include 'header.php';
$page = $_GET['page'] ?? "";
if ($_GET) {
    if ($page == "createseries") {
        include 'administration/createSeries.php';
    } else if ($page == "createchapter") {
        include 'administration/chapter.php';
    } else if ($page == "reader") {
        require 'backend/database.php';

        # Display Series links
        $query = "SELECT * FROM series";
        $result = mysqli_query($conn, $query) or die("Could not execute query on Line 12.");
        # Display Series Links
        if (!($_GET['series'] ?? "")) {
            while ($row = mysqli_fetch_array($result)) {
                echo "<a href='index.php?page=reader&series=" . urlencode($row['seriesTitle']) . "'>" . $row['seriesTitle'] . "</a>";
            }
        }

        # Display Series Infomation
        $result2 = mysqli_query($conn, $query) or die("Could not execute query on Line 20.");
        if (($_GET['series'] ?? "") && !($_GET['chapter'] ?? "")) {
            while ($row2 = mysqli_fetch_array($result2)) {
                if ($row2['seriesTitle'] == $_GET['series']) {
                    echo "<img src='series/" . $row2['seriesFolder'] . "/" . $row2['seriesImage'] . "' alt='" . $row2['seriesTitle'] . " Image'>";
                    echo "<p>" . $row2['seriesDescription'] . " </p>";
                    $chapterQuery = "SELECT * FROM chapters";
                    $chapterResult = mysqli_query($conn, $chapterQuery) or die("Could not execute query on Line 29");
                    while ($chapters = mysqli_fetch_array($chapterResult)) {
                        if ($chapters['series'] == $_GET['series'])
                            echo "<a href='index.php?page=reader&series=" . urlencode($row2['seriesTitle']) . "&chapter=" . urlencode($chapters['chapterName']) . "'>" . $chapters['chapterName'] . "</a>";
                    }
                }
            }
        }
        #Display Chapter
        if (($_GET['series'] ?? "") && ($_GET['chapter'] ?? "")) {
            $displayQuery = "SELECT * FROM chapters";
            $displayResult = mysqli_query($conn, $displayQuery) or die("Could not execute query on Line 29");
            $pageIndex = 0;
            while ($row3 = mysqli_fetch_array($result2)) {
                if ($row3['seriesTitle'] == $_GET['series']) {
                    while ($chapters2 = mysqli_fetch_array($displayResult)) {
                        if ($chapters2['chapterFolder'] == "series_" . $_GET['chapter']) {
                            $pages = scandir("series/" . $row3['seriesFolder'] . "/" . $chapters2['chapterFolder'] . "/");
                            $pagesLength = count($pages);
                            $i = 0;
                            while ($i < $pagesLength) {
                                if ($pages[$pageIndex] != "." || $pages[$pageIndex] != "..") {
                                    echo "<img src='series/" . rawurlencode($row3['seriesFolder']) . "/" . rawurlencode($chapters2['chapterFolder']) . "/" . rawurlencode($pages[$pageIndex]) . "'>";
                                    $pageIndex = $pageIndex + 1;
                                }
                                $i++;
                            }
                            echo "<a href='index.php?page=reader&series=" . rawurlencode($row3['seriesTitle']) . "'>Return to series</a>";
                        }
                    }
                }
            }
        }
    } else {
        include 'pages/home.php';
    }
} else {
    include 'pages/home.php';
}
include 'footer.php';
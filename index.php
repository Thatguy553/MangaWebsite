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
                echo "<a href='index.php?page=reader&series=" . rawurlencode($row['seriesTitle']) . "'>" . $row['seriesTitle'] . "</a>";
            }
        }

        # Display Series Infomation
        $result2 = mysqli_query($conn, $query) or die("Could not execute query on Line 20.");
        if (($_GET['series'] ?? "") && !($_GET['chapter'] ?? "")) {
            while ($row2 = mysqli_fetch_array($result2)) {
                if ($row2['seriesTitle'] == $_GET['series']) {
                    echo "<img src='series/" . $row2['seriesFolder'] . "/" . $row2['seriesImage'] . "' alt='" . $row2['seriesTitle'] . " Image'>";
                    echo "<p>" . $row2['seriesDescription'] . " </p>";
                    $chapterQuery = "SELECT * FROM chapters ORDER BY chapterNumber ASC";
                    $chapterResult = mysqli_query($conn, $chapterQuery) or die("Could not execute query on Line 29");
                    while ($chapters = mysqli_fetch_array($chapterResult)) {
                        if ($chapters['series'] == $_GET['series'])
                            echo "<a href='index.php?page=reader&series=" . rawurlencode($row2['seriesTitle']) . "&chapter=" . rawurlencode($chapters['chapterFolder']) . "'>" . $chapters['chapterName'] . "</a>";
                    }
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

                            if ($count > 1 && $count != $newLink && $newLink > 1) {
                                echo "<a href='index.php?page=" . $_GET['page'] . "&series=" . $_GET['series'] . "&chapter=series_" . ($newLink + 1) . "'>Next</a>";
                                echo "<a href='index.php?page=" . $_GET['page'] . "&series=" . $_GET['series'] . "&chapter=series_" . ($newLink - 1) . "'>Last</a>";
                            } else if ($count > 1 && $count != $newLink) {
                                echo "<a href='index.php?page=" . $_GET['page'] . "&series=" . $_GET['series'] . "&chapter=series_" . ($newLink + 1) . "'>Next</a>";
                            } else if ($newLink > 1) {
                                echo "<a href='index.php?page=" . $_GET['page'] . "&series=" . $_GET['series'] . "&chapter=series_" . ($newLink - 1) . "'>Last</a>";
                            }

                            for ($i = 2; $i < $pagesLength; $i++) {
                                echo "<img src='series/series_" . rawurlencode($_GET['series']) . "/series_" . rawurlencode($newLink) . "/" . rawurlencode($pages[$i]) . "'>";
                                #$pageIndex = $pageIndex + 1;
                            }
                            if ($count > 1 && $count != $newLink && $newLink > 1) {
                                echo "<a href='index.php?page=" . $_GET['page'] . "&series=" . $_GET['series'] . "&chapter=series_" . ($newLink + 1) . "'>Next</a>";
                                echo "<a href='index.php?page=" . $_GET['page'] . "&series=" . $_GET['series'] . "&chapter=series_" . ($newLink - 1) . "'>Last</a>";
                            } else if ($count > 1 && $count != $newLink) {
                                echo "<a href='index.php?page=" . $_GET['page'] . "&series=" . $_GET['series'] . "&chapter=series_" . ($newLink + 1) . "'>Next</a>";
                            } else if ($newLink > 1) {
                                echo "<a href='index.php?page=" . $_GET['page'] . "&series=" . $_GET['series'] . "&chapter=series_" . ($newLink - 1) . "'>Last</a>";
                            }
                            break;
                        }
                    }
                    break;
                }
            }


            #print_r($newLink);

            #echo "<a href='index.php?page=" . $_GET['page'] . "&series=" . $_GET['series'] . "&chapter=series_" . ($newLink + 1) . "'>Next</a>";
            #echo "<a href='index.php?page=" . $_GET['page'] . "&series=" . $_GET['series'] . "&chapter=series_" . ($newLink - 1) . "'>Last</a>";

            #echo "<a href='index.php?page=reader&series=" . rawurlencode($row3['seriesTitle']) . "'>Return to series</a>";
        }
    } else {
        include 'pages/home.php';
    }
} else {
    include 'pages/home.php';
}
include 'footer.php';
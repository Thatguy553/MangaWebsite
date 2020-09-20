<?php
require 'backend/database.php';

$query = "SELECT * FROM chapters";
$result = mysqli_query($conn, $query) or die("Could not execute query on Line 5.");

echo "<table class='userTable'>";

echo "<tr>";
echo "<th>Chapter UID</th>";
echo "<th>series</th>";
echo "<th>Chapter Name</th>";
echo "<th>Chapter Folder</th>";
echo "</tr>";

while ($row = mysqli_fetch_array($result)) {

    echo "<tr>";
    echo "<td>" . $row['chapterUID'] . "</td>";
    echo "<td>" . $row['series'] . "</td>";
    echo "<td>" . $row['chapterName'] . "</td>";
    echo "<td>" . $row['chapterFolder'] . "</td>";
    echo "</tr>";
}

echo "</table>";

$seriesQuery = "SELECT seriesTitle FROM series";
$seriesResult = mysqli_query($conn, $seriesQuery) or die("Could not select titles from series.");

?>

<main>
    <section id="createchapter">
        <form action="" method="post">
            <label>Series:</label>
            <select name="series" id="">
                <?php
                $index = 0;
                while ($row2 = mysqli_fetch_array($seriesResult)) {
                    echo "<option value='" . $row2[$index] . "'>" . $row2[$index] . "</option>";
                    $index = $index++;
                }
                ?>
            </select>
            <input name="chapter" type="text" placeholder="Chapter Name..." required>
            <label>Zip Files Only</label>
            <input type="file" name="image">
            <input type="submit" name="submitChapter">
        </form>
    </section>

    <?php

    # Variables
    $chosenSeries = $_POST['series'] ?? "";
    $name = $_POST['chapter'] ?? "";
    $seriesPath = "series/";

    #Series Information Storage
    if (isset($_POST['submitChapter'])) {
        $allowedExts = array("zip", "rar");
        $temp = explode(".", $_FILES["image"]["name"]);
        $extension = end($temp);
        require 'backend/database.php';

        # Folder Creation Method
        $query = "SELECT chapterName FROM chapters limit 1";
        $result = mysqli_query($conn, $query) or die("Could not execute query on Line 68");
        $row3 = mysqli_fetch_array($result);

        if ($name != $row3) {
            $prefix = "series_";
            $chapName = $name;
            $newPath = $seriesPath . $prefix . $chosenSeries;
            $imagePath = $seriesPath . $prefix . $chosenSeries . "/";

            #Series Cover Storage Method
            if ((($_FILES["image"]["type"] == "rar") || ($_FILES["image"]["type"] == "zip"))) {
                if (($_FILES["image"]["size"] < 5000000)) {
                    if ($_FILES["image"]["error"] > 0) {
                        echo "Return Code: " . $_FILES["image"]["error"] . "<br>";
                    } else {

                        $fileName = $temp[0] . "." . $temp[1];
                        $temp[0] = rand(0, 3000); //Set to random number
                        $fileName;

                        if (file_exists($imagePath . $_FILES["image"]["name"])) {
                            echo $_FILES["image"]["name"] . " already exists. ";
                        } else {
                            $temp = explode(".", $_FILES["image"]["name"]);
                            $newfilename = round(microtime(true)) . '.' . end($temp);

                            #Series name and series Upload Method
                            if (empty($name) || empty($chosenSeries)) {
                                header("Location: index.php?error=noChapterInfo");
                                exit();
                            } else {
                                $sql = "INSERT INTO chapters (series, chapterName, chapterFolder) VALUES (?, ?, ?)";
                                $stmt = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt, $sql)) {
                                    header("Location: index.php?error=SQLChapterfail");
                                    exit();
                                } else {

                                    mysqli_stmt_bind_param($stmt, "sss", $chosenSeries, $name, $newfilename);

                                    if (mkdir($newPath)) {
                                        if (mysqli_stmt_execute($stmt)) {
                                            move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath . $newfilename);
                                            echo "Stored in: " . $imagePath . $_FILES["image"]["name"];
                                        } else {
                                            rmdir($newPath);
                                            echo "Information was not inserted for some reason";
                                        }
                                    } else {
                                        echo "Chapter Already Exists";
                                    }

                                    #header("Location: index.php?series=created");
                                    exit();
                                }
                            }
                        }
                    }
                } else {
                    echo "image too large";
                }
            } else {
                echo "Invalid image";
                exit();
            }
        }
    }

    ?>
</main>
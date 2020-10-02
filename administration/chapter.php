<?php
require 'backend/database.php';

$query = "SELECT * FROM chapters";
$result = mysqli_query($conn, $query) or die("Could not execute query on Line 5.");

$chapData = array();
while ($row = mysqli_fetch_array($result)) {
    $chapData[] = $row;
}

echo "<table class='userTable'>";
echo "<tr>";
echo "<th>Chapter UID</th>";
echo "<th>series</th>";
echo "<th>Chapter Name</th>";
echo "<th>Chapter Folder</th>";
echo "</tr>";

foreach ($chapData as $row) {
    echo "<tr>";
    echo "<td>" . $row['chapterUID'] . "</td>";
    echo "<td>" . $row['series'] . "</td>";
    echo "<td>" . $row['chapterName'] . "</td>";
    echo "<td>" . $row['chapterFolder'] . "</td>";
    echo "<form action='' method='post'>";
    echo "<td><button name='cDelete' type='submit' value='" . $row['chapterUID'] . "'>Delete</button></td>";
    echo "</form>";
    echo "</tr>";
}

echo "</table>";

$seriesQuery = "SELECT * FROM series";
$seriesResult = mysqli_query($conn, $seriesQuery) or die("Could not select titles from series.");

$seriesData = array();
while ($row = mysqli_fetch_array($seriesResult)) {
    $seriesData[] = $row;
}

if (isset($_POST['cDelete'])) {
    $value = $_POST['cDelete'];
    seriesDelete($value);
}

function seriesDelete($chapterUID)
{
    global $conn;
    global $chapData;
    global $seriesData;
    print_r($chapterUID);
    foreach ($chapData as $row) {
        foreach ($seriesData as $row2) {
            if ($row['chapterUID'] == $chapterUID) {
                if ($row['series'] == $row2['seriesTitle']) {
                    if (!array_map('unlink', glob("series/" . $row2['seriesFolder'] . "/" . $row['chapterFolder'] . "/*"))) {
                        $sth = $conn->prepare("DELETE FROM chapters WHERE chapterUID=$chapterUID");

                        if ($sth->execute()) {
                            print("Series Deleted.");
                        } else {
                            print("Returned False?");
                        }
                        rmdir("series/" . $row2['seriesFolder'] . "/" . $row['chapterFolder']);
                    }
                } else {
                    echo "Something went wrong";
                    #header("Location: index.php?page=createchapter&error=uidDidntMatch");
                    #exit();
                }
            }
        }
    }
}


?>



<main>
    <section id="createchapter">
        <form action="" method="post" enctype="multipart/form-data">
            <label>Series:</label>
            <select name="series" id="">
                <?php
                foreach ($seriesData as $row2) {
                    echo "<option value='" . $row2['seriesTitle'] . "'>" . $row2['seriesTitle'] . "</option>";
                }
                ?>
            </select>
            <input type="number" name="chapterNum" placeholder="Chapter Number..." required>
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
    $number = $_POST['chapterNum'] ?? "";
    $seriesPath = "series/";

    #Series Information Storage
    if (isset($_POST['submitChapter'])) {
        $temp = explode(".", $_FILES["image"]["name"]);
        $extension = end($temp);
        require 'backend/database.php';

        # Folder Creation Method
        $query = "SELECT chapterName FROM chapters limit 1";
        $result = mysqli_query($conn, $query) or die("Could not execute query on Line 68");
        $row3 = mysqli_fetch_array($result);

        if ($name != $row3) {
            $prefix = "series_";
            $chapName = $prefix . $number;
            $newPath = $seriesPath . $prefix . $chosenSeries . "/" . $chapName . "/";
            $imagePath = $seriesPath . $prefix . $chosenSeries . "/" . $chapName . "/";

            #Series Cover Storage Method
            if ((($_FILES["image"]["type"] == "application/zip") || ($_FILES["image"]["type"] == "application/x-zip-compressed"))) {
                if (($_FILES["image"]["size"] < 100000000)) {
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
                                $sql = "INSERT INTO chapters (series, chapterNumber, chapterName, chapterFolder) VALUES (?, ?, ?, ?)";
                                $stmt = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt, $sql)) {
                                    header("Location: index.php?error=SQLChapterfail");
                                    exit();
                                } else {

                                    mysqli_stmt_bind_param($stmt, "ssss", $chosenSeries, $number, $name, $chapName);

                                    if (mkdir($newPath)) {
                                        if (mysqli_stmt_execute($stmt)) {
                                            move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath . $newfilename);
                                            echo "Stored in: " . $imagePath . $_FILES["image"]["name"];
                                            $zip = new ZipArchive;
                                            // Zip File Name 
                                            if ($zip->open($imagePath . $newfilename) === TRUE) {

                                                // Unzip Path 
                                                $zip->extractTo($imagePath);
                                                $zip->close();
                                                echo 'Unzipped Process Successful!';
                                                unlink($imagePath . $newfilename);
                                            } else {
                                                echo 'Unzipped Process failed';
                                            }
                                        } else {
                                            rmdir($newPath);
                                            echo "Information was not inserted for some reason";
                                            exit();
                                        }
                                    } else {
                                        echo "Chapter Already Exists";
                                        exit();
                                    }

                                    header("Location: index.php?chapter=created");
                                    exit();
                                }
                            }
                        }
                    }
                } else {
                    echo "image too large";
                    exit();
                }
            } else {
                echo "Invalid image";
                exit();
            }
        }
    }

    ?>
</main>
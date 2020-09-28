<?php
require 'backend/database.php';
$query2 = "SELECT * FROM series";
$resultSeries = mysqli_query($conn, $query2) or die("Could not execute query on Line 18");

echo "<table class='seriesTable'>";

echo "<tr>";
echo "<th>Series</th>";
echo "<th>Likes</th>";
echo "<th>Dislikes</th>";
echo "<th>Chapters</th>";
echo "</tr>";
while ($rowList = mysqli_fetch_array($resultSeries)) {
    echo "<tr>";
    echo "<td>" . $rowList['seriesTitle'] . "</td>";
    echo "<td>" . $rowList['seriesLikes'] . "</td>";
    echo "<td>" . $rowList['seriesDislikes'] . "</td>";
    echo "<td>" . $rowList['seriesChapters'] . "</td>";
    echo "<form action='' method='post'>";
    echo "<td><button name='sDelete' type='submit' value='" . $rowList['seriesUID'] . "'>Delete</button></td>";
    echo "</form>";
    echo "</tr>";
}
echo "</table>";

if (isset($_POST['sDelete'])) {
    $value = $_POST['sDelete'];
    seriesDelete($value);
}

function seriesDelete($seriesID)
{
    global $resultSeries;
    global $conn;
    while ($rowList = mysqli_fetch_array($resultSeries)) {
        if ($rowList['seriesUID'] == $seriesID) {
            if (!array_map('unlink', glob("series/" . $rowList['seriesFolder'] . "/*"))) {
                $sqlDel = $conn->prepare("DELETE * FROM series WHERE seriesUID = ?");
                $stmt = mysqli_stmt_init($conn);
                if (mysqli_stmt_prepare($stmt, $sqlDel)) {
                    mysqli_stmt_bind_param($stmt, "s", $seriesID);
                    if ($conn->query($sqlDel) === TRUE) {
                        echo "Series and Chapters deleted.";
                    } else {
                        echo "Series could not be deleted";
                    }
                } else {
                    echo "sql didnt prepare";
                }
                rmdir("series/" . $rowList['seriesFolder']);
            }
        } else {
            echo "seriesUID apparently didnt match";
            #header("Location: index.php?page=createseries&error=uidDidntMatch");
            #exit();
        }
    }
}
?>

<section>
    <h1>Create Series</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <input name="title" type="text" placeholder="Series Name..." required>
        <textarea name="description" cols="30" rows="10"></textarea>
        <input type="file" name="image">
        <input type="submit" name="seriesCreate">
    </form>
</section>

<?php
# Variables
$title = $_POST['title'] ?? "";
$description = $_POST['description'] ?? "";
$seriesPath = "series/";

#Series Information Storage
if (isset($_POST['seriesCreate'])) {
    $allowedExts = array("jpeg", "jpg", "png");
    $temp = explode(".", $_FILES["image"]["name"]);
    $extension = end($temp);

    # Folder Creation Method
    $query = "SELECT seriesTitle FROM series limit 1";
    $result = mysqli_query($conn, $query) or die("Could not execute query on Line 30");
    $row = mysqli_fetch_array($result);

    if ($title != $row) {
        $prefixtitle = "series_" . $title;
        $newPath = $seriesPath . $prefixtitle;
        $imagePath = $seriesPath . $prefixtitle . "/";

        #Series Cover Storage Method
        if ((($_FILES["image"]["type"] == "image/jpeg") || ($_FILES["image"]["type"] == "image/jpg") || ($_FILES["image"]["type"] == "image/pjpeg") || ($_FILES["image"]["type"] == "image/x-png") || ($_FILES["image"]["type"] == "image/png"))) {
            if (($_FILES["image"]["size"] < 5000000)) {
                if ($_FILES["image"]["error"] > 0) {
                    echo "Return Code: " . $_FILES["image"]["error"] . "<br>";
                    exit();
                } else {

                    $fileName = $temp[0] . "." . $temp[1];
                    $temp[0] = rand(0, 3000); //Set to random number
                    $fileName;

                    if (file_exists($imagePath . $_FILES["image"]["name"])) {
                        echo $_FILES["image"]["name"] . " already exists. ";
                    } else {
                        $temp = explode(".", $_FILES["image"]["name"]);
                        $newfilename = round(microtime(true)) . '.' . end($temp);

                        #Series Title and Description Upload Method
                        if (empty($title) || empty($description)) {
                            header("Location: index.php?error=noSeriesInfo");
                            exit();
                        } else {
                            $sql = "INSERT INTO series (seriesTitle, seriesDescription, seriesImage, seriesFolder) VALUES (?, ?, ?, ?)";
                            $stmt = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt, $sql)) {
                                header("Location: index.php?error=SQLseriesfail");
                                exit();
                            } else {

                                mysqli_stmt_bind_param($stmt, "ssss", $title, $description, $newfilename, $prefixtitle);

                                if (mkdir($newPath)) {
                                    if (mysqli_stmt_execute($stmt)) {
                                        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath . $newfilename);
                                        echo "Series information uploaded.";
                                    } else {
                                        rmdir($newPath);
                                        echo "Information was not inserted for some reason";
                                        exit();
                                    }
                                } else {
                                    echo "Series Already Exists";
                                    exit();
                                }

                                header("Location: index.php?series=created");
                                exit();
                            }
                        }
                    }
                }
            } else {
                echo "Image too large";
                exit();
            }
        } else {
            echo "Invalid image";
            exit();
        }
    }
}

?>
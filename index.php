<?php
include 'header.php';
include 'router.php';

$router = new Router();

$router->new('/', function ($args) {
    include 'pages/home.php';
}, ['GET']);

$router->new('', function ($args) {
    include 'pages/home.php';
}, ['GET']);

$router->new('/home', function ($args) {
    include 'pages/home.php';
}, ['GET']);

$router->new('/signup(.*)', function ($args) {
    echo "<main id='mains'>
    <section class='Signup-form'>";
    if (isset($_GET['error'])) {
        if ($_GET['error'] == "emptyfields") {
            echo '<p class=signuperror>You left some fields empty!</p>';
        } else if ($_GET['error'] == "usertaken") {
            echo '<p class=signuperror>That username is already in use!</p>';
        } else if ($_GET['error'] == "passwordcheck") {
            echo '<p class=signuperror>Make sure your passwords match!</p>';
        } else if ($_GET['error'] == "invalidmail") {
            echo '<p class=signuperror>Make sure your email is correct!</p>';
        }
    }
    echo "<form class='signupForm' action='backend/Signup.php' method='post'>
            <h1>Create Account</h1>
            <input type='text' name='username' id='username' placeholder='USERNAME'> <!-- Username Input -->

            <input type='email' name='email' id='email' placeholder='EMAIL'> <!-- Email Input -->

            <input type='password' name='password' id='password' placeholder='PASSWORD'> <!-- Password Input -->

            <input type='password' name='passwordc' id='passwordc' placeholder='CONFIRM PASSWORD'>
            <!-- Password Confirm Input -->

            <button type='submit' value='' name='signup-enter'>Signup</button> <!-- Signup Button -->
        </form>

        <p class='rtwbutton'><a href='index.php?page=home'>Return</a> to the website home page.</p>

    </section>
    </main>";
}, ['GET']);

$router->new('/login(.*)', function ($args) {
    echo "<main class='Login'>
        <section class='LoginSection'>
        <h1>Note Storage</h1>
        <h2>Login</h2>
        <form class='LoginForm' action='backend/Login.php' method='post' required>
            <input type='text' name='username' placeholder='USERNAME' required>
            <input type='password' name='password' placeholder='PASSWORD' required>
            <button type='submit' name='loginb'>Login</button>
        </form>
        </section>
        </main>";
}, ['GET']);

$router->new('/logout(.*)', function ($args) {
    include 'backend/Logout.php';
}, ['GET']);

$router->new(
    '/createseries(.*)',
    function ($args) {
        if ($_SESSION['role'] == "Staff" || $_SESSION['role'] == "Admin") {
            # code...
        }
    },
    ['GET']
);

$router->new('/createchapter(.*)', function ($args) {
    if ($_SESSION['role'] == "Staff" || $_SESSION['role'] == "Admin") {
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

        echo "<main>
    <section id='createchapter'>
        <form action='' method='post' enctype='multipart/form-data'>
            <label>Series:</label>
            <select name='series' id=''>";
        $index = 0;
        while ($row2 = mysqli_fetch_array($seriesResult)) {
            echo "<option value='" . $row2[$index] . "'>" . $row2[$index] . "</option>";
            $index = $index++;
        }
        echo "</select>
            <input type='number' name='chapterNum' placeholder='Chapter Number...' required>
            <input name='chapter' type='text' placeholder='Chapter Name...' required>
            <label>Zip Files Only</label>
            <input type='file' name='image'>
            <input type='submit' name='submitChapter'>
        </form>
    </section>";

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
                                    header("Location: createchapter?error=noChapterInfo");
                                    exit();
                                } else {
                                    $sql = "INSERT INTO chapters (series, chapterNumber, chapterName, chapterFolder) VALUES (?, ?, ?, ?)";
                                    $stmt = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                                        header("Location: createchapter?error=SQLChapterfail");
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

                                        header("Location: createchapter?chapter=created");
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
        echo "</main>";
    }
}, ['GET']);

$router->new(
    '/reader(.*)',
    function ($args) {
        require 'backend/database.php';

        # Display Series links
        $query = "SELECT * FROM series";
        $result = mysqli_query($conn, $query) or die("Could not execute query on Line 12.");
        # Display Series Links
        if (!($_GET['series'] ?? "")) {
            while ($row = mysqli_fetch_array($result)) {
                echo "<a href='reader?series=" . rawurlencode($row['seriesTitle']) . "'>" . $row['seriesTitle'] . "</a>";
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
                    $chapterResult = mysqli_query(
                        $conn,
                        $chapterQuery
                    ) or die("Could not execute query on Line 29");
                    while ($chapters = mysqli_fetch_array($chapterResult)) {
                        if ($chapters['series'] == $_GET['series']) echo "<a href='reader?series=" . rawurlencode($row2['seriesTitle'])
                            . "&chapter=" . rawurlencode($chapters['chapterFolder']) . "'>" . $chapters['chapterName'] . "</a>";
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
                                echo "<a class='next' href='reader?series=" . $_GET['series'] . "&chapter=series_" . ($newLink + 1) . "'>Next</a>";
                                echo "<a class='last' href='reader?series=" . $_GET['series'] . "&chapter=series_" . ($newLink - 1) . "'>Last</a>";
                            } else if ($count > 1 && $count != $newLink) {
                                echo "<a class='next' href='reader?series=" . $_GET['series'] . "&chapter=series_" . ($newLink + 1) . "'>Next</a>";
                            } else if ($newLink > 1) {
                                echo "<a class='last' href='raeder?series=" . $_GET['series'] . "&chapter=series_" . ($newLink - 1) . "'>Last</a>";
                            }
                            echo "<div class='chap'>";
                            for ($i = 2; $i < $pagesLength; $i++) {
                                echo "<img class='pages' src='series/series_" . rawurlencode($_GET['series']) . "/series_" . rawurlencode($newLink) . "/" . rawurlencode($pages[$i]) . "'>";
                            }
                            echo "</div>";
                            if ($count > 1 && $count != $newLink && $newLink > 1) {
                                echo "<a class='next' href='reader?series=" . $_GET['series'] . "&chapter=series_" . ($newLink + 1) . "'>Next</a>";
                                echo "<a class='last' href='reader?series=" . $_GET['series'] . "&chapter=series_" . ($newLink - 1) . "'>Last</a>";
                            } else if ($count > 1 && $count != $newLink) {
                                echo "<a class='next' href='reader?series=" . $_GET['series'] . "&chapter=series_" . ($newLink + 1) . "'>Next</a>";
                            } else if ($newLink > 1) {
                                echo "<a class='last' href='raeder?series=" . $_GET['series'] . "&chapter=series_" . ($newLink - 1) . "'>Last</a>";
                            }
                            break;
                        }
                    }
                    break;
                }
            }
        }
    },
    ['GET']
);
$router->new('/error(.*)', function ($args) {
    echo "<p>Access Denied</p>";
}, ['GET']);

include 'footer.php';
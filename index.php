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
<<<<<<< Updated upstream
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
        if ($_SESSION['role'] == "staff" || $_SESSION['role'] == "admin") {
            echo "<section>
                        <h1>Create Series</h1>
                        <form action='' method='post' enctype='multipart/form-data'>
                        <input name='title' type='text' placeholder='Series Name...'' required>
                        <textarea name='description' cols='30' rows='10' required></textarea>
                        <input type='file' name='image'>
                        <input type='submit' name='seriesCreate'>
                    </form>
                </section>";

            # Variables
            $title = $_POST['title'] ?? "";
            $description = $_POST['description'] ?? "";
            $seriesPath = "series/";

            #Series Information Storage
            if (isset($_POST['seriesCreate'])) {
                $allowedExts = array("jpeg", "jpg", "png");
                $temp = explode(".", $_FILES["image"]["name"]);
                $extension = end($temp);
                require 'backend/database.php';

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
=======
    } else if ($page == "createchapter") {
        if ($_SESSION['role'] == "staff") {
            include 'administration/chapter.php';
        } else {
            header("Location: index.php?page=errors&error=restricted");
            exit();
>>>>>>> Stashed changes
        }
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
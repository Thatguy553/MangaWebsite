<?php

session_start();

session_unset();

session_destroy();

<<<<<<< Updated upstream
header("Location: home");
=======
header("Location: /index.php?page=home");
>>>>>>> Stashed changes

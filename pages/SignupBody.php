<main id="mains">

    <section class="Signup-form">
        <?php
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

        ?>
        <form class="signupForm" action="backend/Signup.php" method="post">
            <h1>Create Account</h1>
            <input type="text" name="username" id="username" placeholder="USERNAME"> <!-- Username Input -->

            <input type="email" name="email" id="email" placeholder="EMAIL"> <!-- Email Input -->

            <input type="password" name="password" id="password" placeholder="PASSWORD"> <!-- Password Input -->

            <input type="password" name="passwordc" id="passwordc" placeholder="CONFIRM PASSWORD">
            <!-- Password Confirm Input -->

            <button type="submit" value="" name="signup-enter">Signup</button> <!-- Signup Button -->
        </form>

        <p class="rtwbutton"><a href="index.php?page=home">Return</a> to the website home page.</p>

    </section>

</main>
<?php
$page_title = 'KOTESOL Workshops';
include_once( 'header.php' );
?>

<main class="main-area">
    <form id="login">
        <label for="username">
            <span class="label">Username:</span>
            <input type="text" name="username"></input>
        </label>
        <label for="password">
            <span class="label">Password:</span>
            <input type="password" name="password"></input>
        </label>
        <input type="submit" value="Login"></input>
    </form>
</main>

<?php

include_once( 'footer.php' );

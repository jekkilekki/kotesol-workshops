<?php
// Header file
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $page_title; ?></title>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,400i,600,600i">
    <link rel="stylesheet" href="style.css" type="text/css" media="all">

    <script src="js/libs/jquery-3.2.1.min.js" defer></script>
    <script src="js/workshop.ajax.js" defer></script>

    <script src="js/jwt.js" defer></script>
</head>

<body class="index">
    <a class="skip-link screen-reader-text" href="#content">Skip to content</a>

    <header class="masthead">
        <div class="site-header">
            <div class="site-branding">
                <img class="site-logo" src="images/Kotesol%20logo_2_0.png">
                <h1 class="site-title">Workshops</h1>
            </div><!-- .site-branding -->
            <div class="logout">
                <button id="logout" class="logout-button">Logout</button>
            </div><!-- .logout -->
        </div><!-- .site-header -->
    </header><!-- .masthead -->

<?php
session_start();




require_once './includes/connect.php';
require_once './includes/fonctions.php';
require_once './includes/pagination.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link type="text/css" rel="stylesheet" href="css/style.css" />
        <title>Telepro-photos.fr</title>
    </head>
    <body>
        <div id="h1">Telepro-photos.fr</div> 
        <?php
        require_once './includes/menu.php';
        ?>
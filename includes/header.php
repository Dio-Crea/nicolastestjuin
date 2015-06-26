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
        <script src="./lightbox/js/jquery-1.11.0.min.js"></script>
	<script src="./lightbox/js/lightbox.min.js"></script>
	<link href="./lightbox/css/lightbox.css" rel="stylesheet" />
	<link href="./galerie_lightbox.css" rel="stylesheet" />
        <link type="text/css" rel="stylesheet" href="css/style.css" />
        <title>Telepro-photos.fr</title>
    </head>
    <body>
        <div id="h1">Telepro-photos.fr </div>   <form action="./cherche.php?q=mots" method="post" name="recherche" id="contact"><input type="text" name="recherche" placeholder="Recherche" required/><input type="submit" id="submit" value="Go" /></form>
        <?php
        require_once './includes/menu.php';
        ?>
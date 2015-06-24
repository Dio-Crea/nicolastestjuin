<?php
define("DB_SERVER", "localhost");
define("DB_USER", "root");
define("DB_MDP", "");
define("DB_NAME", "nicolastestjuin");

define("CHEMIN_RACINE", "http://localhost/nicolastestjuin/");


$dossier_ori = "images/originales/";
$dossier_gd = "images/affichees/"; 
$dossier_mini = "images/miniatures/"; 

$grande_large = 900; // taille maximale en largeur
$grande_haute = 720; // taille maximale en hauteur

$mini_large = 150; // taille maximale en largeur
$mini_haute = 150; // taille maximale en hauteur

// qualité de l'image d'affichage (jpg de 0 Ã  100)
$grande_qualite = 85;

// qualité de l'image de la miniature (jpg de 0 Ã  100)
$mini_qualite = 70;

// formats acceptÃ©s en minuscule dans un tableau, sÃ©parÃ© par des ','
$formats_acceptes = array('jpg','jpeg','png');
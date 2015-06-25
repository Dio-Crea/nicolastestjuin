<?php // Numero de page (1 par défaut)
if( isset($_GET['page']) && is_numeric($_GET['page']) )
    $page = $_GET['page'];
else
    $page = 1;

// Nombre d'info par page
$pagination = 10;
// Numéro du 1er enregistrement à lire
$limit_start = ($page - 1) * $pagination;

// Préparation de la requête
$sql = "SELECT * FROM photo LIMIT $limit_start, $pagination";

// Requête SQL
$resultat = mysqli_query($mysqli,$sql);

// Traitement et affichage des données
while ( $donnee = mysqli_fetch_assoc($resultat) ) {

    /* ICI VOTRE CODE NORMAL */
    /* Affichage d'un élément */

}

// Nb d'enregistrement total
$nb_total = mysqli_query($mysqli,'SELECT COUNT(*) AS nb_total FROM photo ');
$nb_total = mysqli_fetch_array($nb_total);
$nb_total = $nb_total['nb_total'];

// Pagination
$nb_pages = ceil($nb_total / $pagination);


echo "<div id='pagination'>" .'<p>Page :';
		// Boucle sur les pages
		for ($i = 1 ; $i <= $nb_pages ; $i++) {
		if ($i == $page )
			echo " $i";
		else
			echo " <a href=\"?page=$i\">$i</a> ";
		}
		echo ' </p>'."</div>";
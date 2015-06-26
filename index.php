<?php
require_once 'includes/header.php';

?>
<?php 
$select_query=mysqli_query($mysqli,"SELECT p.*, u.lenom as ulenom, GROUP_CONCAT(r.id) AS idrub, GROUP_CONCAT(r.lintitule SEPARATOR '|||' ) AS lintitule 
    FROM photo p
	LEFT JOIN photo_has_rubriques h ON h.photo_id = p.id
         INNER JOIN utilisateur u ON u.id = p.utilisateur_id
    LEFT JOIN rubriques r ON h.rubriques_id = r.id

        GROUP BY p.id
        ORDER BY p.id DESC
       LIMIT 20 ;
    ")
?>
<h2>Bienvenue sur Telepro-photos.fr</h2> 
<?php 

if (isset($_SESSION['sid']) && $_SESSION['sid'] == session_id()) {
$utilisateur_query=  mysqli_query($mysqli, "SELECT * FROM utilisateur where id=$_SESSION[id]");
$utilisateur_assoc=  mysqli_fetch_assoc($utilisateur_query);
$lenom=$utilisateur_assoc['lenom'];
    // texte d'accueil
    echo "<h3>Bonjour " .$lenom. "</h3>";
    echo "<p>Vous étes connecté en tant que <span title='" . $_SESSION['lenom'] . "'>" . $_SESSION['nom_perm'] . "</span></p>";
    echo "<h5><a href='./includes/deconnect.php'>Déconnexion</a></h5>";
}
while($ligne = mysqli_fetch_assoc($select_query)){
                 echo "<div class='miniatures'>";
                 echo "<h4>".$ligne['letitre']."</h4>";
                 echo "<a href='".CHEMIN_RACINE.$dossier_gd.$ligne['lenom'].".jpg' data-lightbox='groupe'><img src='".CHEMIN_RACINE.$dossier_mini.$ligne['lenom'].".jpg' alt='' /></a>";
                 echo "<p>".$ligne['ladesc']."<br /><br /> </p>";
                 echo "<p>".$ligne['ulenom']."<br /><br />";
                 
                 echo "</div>";
   
}

?>
<?php
require_once 'includes/footer.php';
?>

<?php
require_once 'includes/header.php';
// si tentative de connexion
if (isset($_POST['lelogin'])) {
    $lelogin = $_POST['lelogin'];
    $lemdp = $_POST['lepass'];

    // vérification de l'utilisateur dans la db
    $sql = "SELECT  u.id, u.lemail, u.lenom,u.lepass, 
		d.lenom AS nom_perm, d.lenom, d.laperm 
	FROM utilisateur u
		INNER JOIN droit d ON u.droit_id = d.id
    WHERE u.lelogin='$lelogin' AND u.lepass = '$lemdp';";
    $requete = mysqli_query($mysqli, $sql)or die(mysqli_error($mysqli));
    $recup_user = mysqli_fetch_assoc($requete);
    // vérifier si on a récupéré un utilisateur
    if (mysqli_num_rows($requete)) { // vaut true si 1 résultat (ou plus), false si 0
        // si l'utilisateur est bien connecté
        $_SESSION = $recup_user; // transformation des rÃ©sultats de la requÃªte en variable de session
        $_SESSION['sid'] = session_id(); // récupération de la clef de session
        $_SESSION['lelogin'] = $lelogin; // récupération du login (du POST aprÃ¨s traitement)
        // var_dump($_SESSION);
        // redirection vers la page d'accueil (pour Ã©viter les doubles connexions par F5)
        header('location: ' . CHEMIN_RACINE);
    }
}
$sql = "SELECT p.lenom,p.lextension,p.letitre,p.ladesc, u.lelogin,u.lenom,
    GROUP_CONCAT(r.id) AS rubid, 
    GROUP_CONCAT(r.lintitule SEPARATOR '~~') AS lintitule 
    FROM photo p
    INNER JOIN utilisateur u ON u.id = p.utilisateur_id
    LEFT JOIN photo_has_rubriques h ON h.photo_id = p.id
    LEFT JOIN rubriques r ON h.rubriques_id = r.id
    GROUP BY p.id
    ORDER BY p.id DESC; 
    ";
$recup_sql = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));

// si on est pas (ou plus) connecté
if (!isset($_SESSION['sid']) || $_SESSION['sid'] != session_id()) {
    ?>

    <form action="" name="connection" method="POST">
        <input type="text" name="lelogin" required />
        <input type="password" name="lepass" required />
        <input type="submit" value="Connexion" />
    </form>
    <?php
    // sinon on est connecté
} else {
$utilisateur_query=  mysqli_query($mysqli, "SELECT * FROM utilisateur where id=$_SESSION[id]");
$utilisateur_assoc=  mysqli_fetch_assoc($utilisateur_query);
$lenom=$utilisateur_assoc['lenom'];
    // texte d'accueil
    echo "<h3>Bonjour " .$lenom. "</h3>";
    echo "<p>Vous étes connecté en tant que <span title='" . $_SESSION['lenom'] . "'>" . $_SESSION['nom_perm'] . "</span></p>";
    echo "<h5><a href='./includes/deconnect.php'>Déconnexion</a></h5>";

    // liens  suivant la permission utilisateur
    switch ($_SESSION['laperm']) {
        // si on est l'admin
        case 0 :
            echo "<a href='#'>Administration</a>";
            break;
        // si on est modÃ©rateur
        case 1:
            echo "<a href='#'>Modération</a> ";
            break;
        // si autre droit (ici simple utilisateur)
        default :
            echo "<a href='membre.php'>Espace membre</a>";
    }
}
?>

<?php
require_once 'includes/footer.php';

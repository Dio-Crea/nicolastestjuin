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
                
    WHERE u.lelogin='$lelogin' AND u.lepass = '$lemdp' ;  ";
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
    ORDER BY p.id DESC
    
    ; 
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
    $utilisateur_query = mysqli_query($mysqli, "SELECT * FROM utilisateur where id=$_SESSION[id]");
    $utilisateur_assoc = mysqli_fetch_assoc($utilisateur_query);
    $lenom = $utilisateur_assoc['lenom'];
    // texte d'accueil
    echo "<h3>Bonjour " . $lenom . "</h3>";
    echo "<p>Vous étes connecté en tant que <span title='" . $_SESSION['lenom'] . "'>" . $_SESSION['nom_perm'] . "</span></p>";
    echo "<h5><a href='./includes/deconnect.php'>Déconnexion</a></h5>";

    // liens  suivant la permission utilisateur
    switch ($_SESSION['laperm']) {
        // si on est l'admin
        case 0 :
            echo "<a href='./administration.php'>Administration</a>";
            break;
        // si on est modérateur
        case 1:
            echo "<a href='#'>Modération</a> ";
            break;
        // si autre droit (ici simple utilisateur)
        default :
            echo "<a href='membre.php'>Espace membre</a>";
    }



////////////////////////////////////
//
//
// si on a envoyÃ© le formulaire et qu'un fichier est bien attachÃ©
    if (isset($_POST['letitre']) && isset($_FILES['lefichier'])) {

        // traitement des chaines de caractÃ¨res
        $letitre = $_POST['letitre'];
        $ladesc = $_POST['ladesc'];

        // rÃ©cupÃ©ration des paramÃ¨tres du fichier uploadÃ©
        $limage = $_FILES['lefichier'];

        // appel de la fonction d'envoi de l'image, le rÃ©sultat de la fonction est mise dans la variable $upload
        $upload = upload_originales($limage, $dossier_ori, $formats_acceptes);

        // si $upload n'est pas un tableau c'est qu'on a une erreur
        if (!is_array($upload)) {
            // on affiche l'erreur
            echo $upload;

            // si on a pas d'erreur, on va insÃ©rer dans la db et crÃ©er la miniature et grande image   
        } else {
            //var_dump($upload);
            // crÃ©ation de la grande image qui garde les proportions
            $gd_ok = creation_img($dossier_ori, $upload['nom'], $upload['extension'], $dossier_gd, $grande_large, $grande_haute, $grande_qualite);

            // crÃ©ation de la miniature centrÃ©e et coupÃ©e
            $min_ok = creation_img($dossier_ori, $upload['nom'], $upload['extension'], $dossier_mini, $mini_large, $mini_haute, $mini_qualite, false);

            // si la crÃ©ation des 2 images sont effectuÃ©es
            if ($gd_ok == true && $min_ok == true) {
                //var_dump($_POST);
                // prÃ©paration de la requÃªte (on utilise un tableau venant de la fonction upload_originales, de champs de formulaires POST traitÃ©s et d'une variable de session comme valeurs d'entrÃ©e)
                $sql = "INSERT INTO photo (lenom,lextension,lepoids,lahauteur,lalargeur,letitre,ladesc,utilisateur_id) 
	VALUES ('" . $upload['nom'] . "','" . $upload['extension'] . "'," . $upload['poids'] . "," . $upload['hauteur'] . "," . $upload['largeur'] . ",'$letitre','$ladesc'," . $_SESSION['id'] . ");";

                mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));

                // rÃ©cupÃ©ration de la derniÃ¨re id insÃ©rÃ©e par la requÃªte qui prÃ©cÃ¨de (dans photo par l'utilisateur actuel)
                $id_photo = mysqli_insert_id($mysqli);

                // vÃ©rification de l'existence des sections cochÃ©es dans le formulaire
                if (isset($_POST['section'])) {
                    foreach ($_POST['section'] AS $clef => $valeur) {
                        if (ctype_digit($valeur)) {
                            mysqli_query($mysqli, "INSERT INTO photo_has_rubriques VALUES ($id_photo,$valeur);")or die(mysqli_error($mysqli));
                        }
                    }
                }
            } else {
                echo 'Erreur lors de la crÃ©ation des images redimenssionnÃ©es';
            }
        }
    }



// si on confirme la suppression
    if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
        $idphoto = $_GET['delete'];
        $idutil = $_SESSION['id'];

        // rÃ©cupÃ©ration du nom de la photo
        $sql1 = "SELECT lenom, lextension FROM photo WHERE id=$idphoto;";
        $nom_photo = mysqli_fetch_assoc(mysqli_query($mysqli, $sql1));

        // supression dans la table photo_has_rubrique (sans l'utilisation de la clef Ã©trangÃ¨re)
        $sql2 = "DELETE FROM photo_has_rubriques WHERE photo_id = $idphoto";
        mysqli_query($mysqli, $sql2);

        // puis suppression dans la table photo
        $sql3 = "DELETE FROM photo WHERE id = $idphoto AND utilisateur_id = $idutil;";
        mysqli_query($mysqli, $sql3);
        echo $dossier_ori . $nom_photo['lenom'] . "." . $nom_photo['lextension'];

        // supression physique des fichiers
        unlink($dossier_ori . $nom_photo['lenom'] . "." . $nom_photo['lextension']);
        unlink($dossier_gd . $nom_photo['lenom'] . ".jpg");
        unlink($dossier_mini . $nom_photo['lenom'] . ".jpg");

        header('Location:./espaceclient.php');
    }





// /* RETIRER LIGNE WHERE  POUR LA PAGE ACCUEIL*/rÃ©cupÃ©rations des images de l'utilisateur connectÃ© dans la table photo avec leurs sections mÃªme si il n'y a pas de sections sÃ©lectionnÃ©es (jointure externe avec LEFT)
    $sql = "SELECT p.*, u.lenom as ulenom, GROUP_CONCAT(r.id) AS idrub, GROUP_CONCAT(r.lintitule SEPARATOR '|||' ) AS lintitule 
    FROM photo p
	LEFT JOIN photo_has_rubriques h ON h.photo_id = p.id
         INNER JOIN utilisateur u ON u.id = p.utilisateur_id
    LEFT JOIN rubriques r ON h.rubriques_id = r.id

        GROUP BY p.id
        ORDER BY p.id DESC
        LIMIT $limit_start, $pagination;
    ";
    $recup_sql = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));

// rÃ©cupÃ©ration de toutes les rubriques pour le formulaire d'insertion
    $sql = "SELECT * FROM rubriques ORDER BY lintitule ASC;";
    $recup_section = mysqli_query($mysqli, $sql);

//$sql_util=  mysqli_query($mysqli,"SELECT u.id,u.lenom,p.* FROM utilisateur u LEFT JOIN photo p WHERE u.id = p.utilisateur_id");
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title><?php echo $_SESSION['lelogin'] ?> - Votre Espace membre</title>
            <link rel="stylesheet" href="style.css" />
            <script src="monjs.js"></script>
        </head>
        <body>
            <div id="content">

                <div id="milieu">
                    <div id="formulaire">
                        <form action="" enctype="multipart/form-data" method="POST" name="onposte">
                            <input type="text" name="letitre" required /><br/>
                           <!-- <input type="hidden" name="MAX_FILE_SIZE" value="50000000" /> -->
                            <input type="file" name="lefichier" required /><br/>
                            <textarea name="ladesc"></textarea><br/>

                            <input type="submit" value="Envoyer le fichier" /><br/>
                            Sections : <?php
    // affichage des sections
    while ($ligne = mysqli_fetch_assoc($recup_section)) {
        echo $ligne['lintitule'] . " : <input type='checkbox' name='section[]' value='" . $ligne['id'] . "' > - ";
    }
    ?>
                        </form>
                    </div>
                    <div id="lesphotos">
                            <?php
                            while ($ligne = mysqli_fetch_assoc($recup_sql)) {
                                echo "<div class='miniatures'>";
                                echo "<h4>" . $ligne['letitre'] . "</h4>";
                                echo "<a href='" . CHEMIN_RACINE . $dossier_gd . $ligne['lenom'] . ".jpg' target='_blank'><img src='" . CHEMIN_RACINE . $dossier_mini . $ligne['lenom'] . ".jpg' alt='' /></a>";
                                echo "<p>" . $ligne['ladesc'] . "<br /><br />";
                                echo "<p>" . $ligne['ulenom'] . "<br /><br />";
                                // affichage des sections
                                $sections = explode('|||', $ligne['lintitule']);
                                //$idsections = explode(',',$ligne['idrub']);
                                foreach ($sections AS $key => $valeur) {
                                    echo " $valeur<br/>";
                                }
                                echo"<br/><a href='modif.php?id=" . $ligne['id'] . "'><img src='img/modifier.png' alt='modifier' /></a> <img onclick='supprime(" . $ligne['id'] . ");' src='img/supprimer.png' alt='supprimer' />
                     </p>";
                                echo "</div>";
                            }
                        }
echo "<div id='pagination'>" .'<p>Page :';
		// Boucle sur les pages
		for ($i = 1 ; $i <= $nb_pages ; $i++) {
		if ($i == $page )
			echo " $i";
		else
			echo " <a href=\"?page=$i\">$i</a> ";
		}
		echo ' </p>'."</div>";
                        ?>
                </div>
            </div>
            <div id="bas"></div>
        </div>



                    <?php
                    require_once 'includes/footer.php';
                    
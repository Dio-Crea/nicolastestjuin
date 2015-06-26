<?php
require_once 'includes/header.php';
?>
<?php 
$utilisateur_query2=  mysqli_query($mysqli, "SELECT * FROM utilisateur where id=1");
$utilisateur_assoc2=  mysqli_fetch_assoc($utilisateur_query2);
if (!isset($_SESSION['sid']) || $_SESSION['sid'] != session_id()) {
    
}

else{
$utilisateur_query=  mysqli_query($mysqli, "SELECT * FROM utilisateur where id=$_SESSION[id]");
$utilisateur_assoc=  mysqli_fetch_assoc($utilisateur_query);
$lenom=$utilisateur_assoc['lenom'];
    // texte d'accueil
    echo "<h3>Bienvenue " .$lenom. "</h3>";
    echo "<p>Vous étes connecté en tant que <span title='" . $_SESSION['lenom'] . "'>" . $_SESSION['nom_perm'] . "</span></p>";
    echo "<h5><a href='./includes/deconnect.php'>Déconnexion</a></h5>";

    // liens  suivant la permission utilisateur
    switch ($_SESSION['laperm']) {
        // si on est l'admin
        case 0 :
            echo "<a href='./administration.php'>Administration</a> ";
            break;
        // si on est modérateur
        case 1:
            echo "<a href='./moderation.php'>Modération</a> ";
            break;
        // si autre droit (ici simple utilisateur)
        default :
            echo "<a href='./espaceclient.php'>Espace membre</a>";
    }

}

if (isset($_POST['message']))  {
    
    $nom = strip_tags(trim($_POST['nom']));
    $prenom = strip_tags(trim($_POST['prenom']));
    $mail = strip_tags(trim($_POST['lemail']));
    $texte = strip_tags(trim($_POST['message']));
    $genre=$_POST['genre'];
    
    
    // votre mail
    $monmail = $utilisateur_assoc2['lemail'];
    $entete = 'Expéditeur : '.$genre." ".$nom."\r\n"   .$prenom."\r\n".   'Email : ' .$mail . "\r\n" ."\r\n". 'Message : ' ."\r\n"."\r\n". $texte . "\r\n" ;

    mail($monmail, $texte, $entete);
 


}


?>

<script type="text/javascript">
 function MaxLengthTextarea(objettextarea,maxlength){
  if (objettextarea.value.length > maxlength) {
    objettextarea.value = objettextarea.value.substring(0, maxlength);
    alert('Votre texte ne doit pas dépasser '+maxlength+' caractères!');
   }
}
</script>
                   

<?php if (!isset($_POST['message'])) {
                   
echo "    <div>Telepro-photos.fr - Nous contacter</div>";
}    

        ?>
                   

<?php if (isset($_POST['message'])) {

    
    echo "<titre_contact>Votre Message a bien été envoyé à ".$utilisateur_assoc2['lenom']."</titre_contact>";
    
}
?>


<div id="page_contact">
    
    
    <form action="" method="post" name="page_contact" id="contact"><br />
    
    <div id="form_contact">
         Mr <input type="radio" name="genre" value="Mr" id="mr" required> <br />       
        Mme <input type="radio" name="genre" value="Mme" id="mme" /> <br />
        Melle <input type="radio" name="genre" value="Melle" id="melle" /> <br />
        <input type="text" name="nom" placeholder="Votre Nom" required/><br />
        <input type="text" name="prenom" placeholder="Votre prenom" required/><br />
        
        <input type="email" name="lemail" placeholder="Votre Courrier" required/><br />
    
        <textarea name="message" onkeyup="javascript:MaxLengthTextarea(this, 500);" placeholder="Votre Demande" maxlenght="500" required /></textarea><br />
        
        <input type="submit" id="submit" value="Envoyer" />
    </form>
   </div>



<?php
require_once 'includes/footer.php';
?>
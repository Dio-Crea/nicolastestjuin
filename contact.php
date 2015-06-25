<?php
require_once 'includes/header.php';
?>
<?php 



if (isset($_POST['message'])) {
    
    $nom = strip_tags(trim($_POST['nom']));
    $mail = strip_tags(trim($_POST['lemail']));
    $texte = strip_tags(trim($_POST['message']));
    // votre mail
    $monmail = "dio.crea@outlook.be";
    $entete = 'Expéditeur : '.$nom."\r\n". 'Email : ' .$mail . "\r\n" ."\r\n". 'Message : ' ."\r\n"."\r\n". $texte . "\r\n" ;

    mail($monmail, $texte, $entete);
 
  
}
?>

                

<?php if (!isset($_POST['message'])) {
                   
echo "    <titre_contact>Contactez Nous</titre_contact>";
}    

        ?>
                   

<?php if (isset($_POST['message'])) {
    echo "<titre_contact>Votre Message a bien été envoyé</titre_contact>";
    
}
?>


<div id="page_contact">
    
    
    <form action="" method="post" name="page_contact" id="contact"><br />
    
    <div id="form_contact">
    
        <input type="text" name="nom" placeholder="Votre Nom"/><br />
   
        
        <input type="email" name="lemail" placeholder="Votre Courrier" /><br />
    
        <textarea name="message" placeholder="Votre Message" ></textarea><br />
        
        <input type="submit" id="submit" value="Envoyer" />
    </form>
   </div>



<?php
require_once 'includes/footer.php';
?>
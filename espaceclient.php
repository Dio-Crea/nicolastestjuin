<?php
require_once 'includes/header.php';

if(isset($_POST['lelogin'])&& isset($_POST['lepass'])){   
    $lelogin = htmlspecialchars(strip_tags($_POST['lelogin'],ENT_QUOTES));
    $lemdp = htmlspecialchars(strip_tags($_POST['lepass'],ENT_QUOTES));	
    $recup_util = mysqli_query($mysqli,"SELECT * FROM utilisateur WHERE lelogin='$lelogin' AND lepass='$lemdp'; ") or die("Erreur: ".mysqli_error($mysqli));
	 
	 
	 if(mysqli_num_rows($recup_util)){
        $recup_tab = mysqli_fetch_assoc($recup_util);
		$_SESSION['clef_unique']=  session_id();
		$_SESSION['lelogin'] = $recup_tab['lelogin'];
                $_SESSION['lepass'] = $recup_tab['lepass'];	    
	
          header("Location: ./");
           
    }else{
       echo $erreur_connect = "Login ou mot de passe incorrecte";
    }
}
?>

<form action="" name="connection" method="POST">
    <input type="text" name="lelogin" required />
    <input type="password" name="lepass" required />
    <input type="submit" value="Connexion" />
</form>

<?php
require_once 'includes/footer.php';
?>
<?php
session_start();
// suppresion des variables de session
$_SESSION = array();
// destruction du cookie si ils sont activÃ©s
if(ini_get("session.use_cookies")){
    $parametre = session_get_cookie_params();
    // on Ã©crase le cookie de session avec un cookie pÃ©rimÃ©
    setcookie(session_name(),'',time()-42000,$parametre['path'],$parametre['domain'],$parametre['secure'],$parametre['httponly']);
}
// destruction du fichier de session côté serveur
session_destroy();
// redirection vers la racine du dossier
header("Location: ../");
<?php /*
 * Fonction d'upload de l'image d'origine, renvoie un tableau si rÃ©ussie sinon renvoie une chaine de caractÃ¨re contenant l'erreur
 * Utilisation: upload_originales("$_FILE","url du dossier","tableau avec les extensions permises");
 *  
 */

function upload_originales($fichier,$destination,$ext){
    
    $sortie = array();
    
    // rÃ©cupÃ©ration du nom d'origine
    $nom_origine = $fichier['name'];
    
    // rÃ©cupÃ©ration de l'extension du fichier mise en minuscule et sans le .
    $extension_origine = substr(strtolower(strrchr($nom_origine,'.')),1);
    
    // si l'extension ne se trouve pas (!) dans le tableau contenant les extensions autorisÃ©es
    if(!in_array($extension_origine,$ext)){
        
        // envoi d'une erreur et arrÃªt de la fonction
        return "Erreur : Extension non autorisé";
        
    }
    
    // si l'extension est valide mais de type jpeg
    if($extension_origine==="jpeg"){ $extension_origine = "jpg"; }
    
    // crÃ©ation du nom final  (appel de la fonction chaine_hasard, pour la chaine de caractÃ¨re alÃ©atoire)
    $nom_final = nom_hasard(9).chaine_hasard(27);
    
    // on a besoin du nom final dans le tableau $sortie si la fonction rÃ©ussit
    $sortie['poids'] = filesize($fichier['tmp_name']);
    $sortie['largeur'] = getimagesize($fichier['tmp_name'])[0];
    $sortie['hauteur'] = getimagesize($fichier['tmp_name'])[1];
    $sortie['nom'] = $nom_final;
    $sortie['extension'] = $extension_origine;
    

    // on dÃ©place l'image du dossier temporaire vers le dossier 'originales'  avec le nom de fichier complet
    if(@move_uploaded_file($fichier['tmp_name'], $destination.$nom_final.".".$extension_origine)){
        return $sortie;
    // si erreur
    }else{
        return "Erreur lors de l'upload d'image";
    }
    
}

/*
 * 
 * renvoie une chaine au hasard de longueur Ã©gale au nombre passÃ© en paramÃ¨tre
 * appel => chaine_hasard(int);
 * 
 */

function nom_hasard($nb_hasard){
    $ladate=date("YmdHis");
    for($i=0;$i<$nb_hasard;$i++){  
        if($i==0){
            $debut="1";
            $fin="9";
        }else{
        
        $debut.="0";
        $fin.="9";
        }
    }
    
    $hasard=mt_rand($debut,$fin);
    return $ladate.$hasard;
}



function chaine_hasard($nombre_caracteres){
    $caracteres = "a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,0,1,2,3,4,5,6,7,8,9";
    $tableau = explode(",", $caracteres);
    $nb_element_tab = count($tableau);
    $sortie ="";
    for($i=0;$i<$nombre_caracteres;$i++){
        $hasard = mt_rand(0, $nb_element_tab-1);
        $sortie .= $tableau[$hasard];
    }
    return $sortie;
}


function traite_chaine($chaine){
    $sortie = htmlentities(strip_tags(trim($chaine)),ENT_QUOTES);
    return $sortie;
}

/*
 * 
 * Fonction qui crÃ©e les images en .jpg proportionelles ou coupÃ©es avec centrage avec comme paramÃ¨tres:
 * creation_img("chemin vers l'originales",
 *  "nom complet du fichier originale sans extension",
 *  "extension de l'originale",
 *  "dossier de destination",
 *  "largeur en pixel maximum de l'image",
 *  "hauteur maximale en pixel de l'image",
 *  "QualitÃ©e jpeg de 0 Ã  100",
 *  "Proportion (true par dÃ©faut), garde les proportions, mettre false si on souhaite centrer l'image et la couper");
 * 
 */

function creation_img($chemin_org, $nom,$extension,$destination,$largeur_max,$hauteur_max,$qualite, $proportion = true){
    
    // chemin + nom + '.' + extension de l'image Ã  traitÃ©e
   $chemin_image = $chemin_org.$nom.'.'.$extension;
    
    // rÃ©cupÃ©ration des paramÃ¨tres de l'images
    $param_image = getimagesize($chemin_image);
    
    // rÃ©cupÃ©ration de la largeur et de la hauteur d'origine en pixel
    $largeur_org = $param_image[0];
    $hauteur_org = $param_image[1];
    
    // calcul du ratio largeur originale avec la largeur maximale
    $ratio_l = $largeur_org / $largeur_max;
    // calcul du ratio hauteur originale avec la heuteur maximale
    $ratio_h = $hauteur_org / $hauteur_max;

    
    // crÃ©ation de l'image temporaire suivant le format
    switch ($extension){
        case 'jpg':
            $image_finale = imagecreatefromjpeg($chemin_image);
            
            break;
        
        case 'png':
            $image_finale = imagecreatefrompng($chemin_image);

            break;
        default:
            return false;
    }
    
    /*
     * Si on veut respecter le ratio
     */
    if($proportion==true){
    
        // on vÃ©rifie si un ratio est plus grand que l'autre (L > H)
    if($ratio_l>$ratio_h){
        
        // si la largeur originale est plus petite que largeur maximale on va garder la taille d'origine en Largeur mais aussi en hauteur!
        if($ratio_l < 1){
            $largeur_dest = $largeur_org;
            $hauteur_dest = $hauteur_org;
        }else{
            // on donne la largeur maximale comme rÃ©fÃ©rence
            $largeur_dest = $largeur_max;
            // on calcule la hauteur grÃ¢ce au ratio de large
            $hauteur_dest = round($hauteur_org/$ratio_l);
        }
        
    // sinon (le ratio hauteur est plus grand ou Ã©gale au ratio largeur)   
    }else{
        // si la hauteur originale est plus petite que hauteur maximale on va garder la taille d'origine en hauteur mais aussi en largeur!
        if($ratio_h < 1){
            $largeur_dest = $largeur_org;
            $hauteur_dest = $hauteur_org;
        }else{
            // on calcule la largeur grÃ¢ce au ratio de haut
            $largeur_dest = round($largeur_org/$ratio_h);
            // on donne la hauteur maximale comme rÃ©fÃ©rence
            $hauteur_dest = $hauteur_max;
        }
    }    
        
    // crÃ©ation d'une image vide aux bonnes dimensions dans laquelle ou colera l'image d'origine 
    $nouvelle_image = imagecreatetruecolor($largeur_dest, $hauteur_dest);

    
    // copie de l'image d'origine vers l'image finale
   imagecopyresampled($nouvelle_image, $image_finale, 0, 0, 0, 0, $largeur_dest, $hauteur_dest, $largeur_org, $hauteur_org);

    
    /*
     * Si on veut crÃ©er un fichier avec crop centrÃ©
     */
    }else{
    
    
       
      //$chemin_org, $nom,$extension,$destination,$largeur_max,$hauteur_max,$qualite, $proportion = true  
        
    // REFAIRE LE CALCUL
    if($ratio_l>$ratio_h){
        

            // on calcule la largeur grÃ¢ce au ratio de haut
            $largeur_dest = round($largeur_org/$ratio_h);
            // on donne la hauteur maximale comme rÃ©fÃ©rence
            $hauteur_dest = $hauteur_max;
            $centre_large = round(($largeur_dest-$largeur_max)/2);
            $centre_haut = 0;
            
        
    // sinon (le ratio hauteur est plus grand ou Ã©gale au ratio largeur)   
    }else{
        // si la hauteur originale est plus petite que hauteur maximale on va garder la taille d'origine en hauteur mais aussi en largeur!
 
            // on donne la largeur maximale comme rÃ©fÃ©rence
            $largeur_dest = $largeur_max;
            // on calcule la hauteur grÃ¢ce au ratio de large
            $hauteur_dest = round($hauteur_org/$ratio_l);
            $centre_large = 0;
            $centre_haut = round(($hauteur_dest-$hauteur_max)/2);
            
        }
     
     // crÃ©ation d'une image provisoire avant le crop
    $img_temp = imagecreatetruecolor($largeur_dest, $hauteur_dest);    
    // copie de l'image d'origine vers l'image finale
   imagecopyresampled($img_temp, $image_finale, 0, 0, 0, 0, $largeur_dest, $hauteur_dest, $largeur_org, $hauteur_org);    
        
        
    
    // crÃ©ation d'une image vide aux dimensions fixes passÃ©es Ã  la fonction
    $nouvelle_image = imagecreatetruecolor($largeur_max, $hauteur_max);    
    // copie de l'image d'origine vers l'image finale
   imagecopyresampled($nouvelle_image, $img_temp, 0, 0, $centre_large, $centre_haut, $largeur_dest, $hauteur_dest, $largeur_dest, $hauteur_dest);    
    // destruction de l'image temporaire prÃ©-crop
   imagedestroy($img_temp);
    }
    
    // crÃ©ation de l'image finale en .jpg dans le dossier de destination avec la qualitÃ© passÃ©e en paramÃ¨tre
    imagejpeg($nouvelle_image, $destination.$nom.'.jpg', $qualite);
    // destruction de l'image temporaire
    imagedestroy($nouvelle_image);

    
    return true;
}
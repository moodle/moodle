 <?php
    function demandeLiens()   {
        $dolibarr = "https://infans.dolibarrgestion.fr/";

            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ERROR  | E_PARSE );
        /********* Créer ma fonction Curl *************/
        /*********Récupérer le contenu de la page Web à partir de l'url***********/ 
        $url = $dolibarr . "devinfans/API/monCompteMoodle.php"; 
        /***********Saisir l'URL et la transmettre à la variable.*************/
        $data['demandeLiens']=1;
        $data['id_session']= $_POST['numerodesession'];
        $data['id_stagiaire']=$_POST['numerodestagiaire'];

        /***********Initialisez une session CURL.********************************/
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_HTTPHEADER, array());      
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch); 
        $tableauFichier=json_decode($response);

        $chemin = $tableauFichier->chemin;
// echo '<pre>';
// print_r( $tableauFichier->contenu );
// echo '</pre>';
        foreach($tableauFichier->contenu as $f) {
            if (in_array($f, ['.', '..']))      continue;
            $tab = explode("_", $f);

            
			$fin = $tab[count($tab)-1];
			$tab2 = explode(".", $fin);
            $extension = "." . $tab2[count($tab2)-1];

            $initial = $tab[0];
            $nbInitial = strlen($initial);
            if( in_array($initial, ["A", "J", "JX", "Q","G","N","P","PN","S","X","V"]) ) {
                $textLien = substr($f,$nbInitial+1);    
            }
			
			if( strstr(strtoupper($f), "CPS") !== FALSE )	continue;

            // $textLien = str_ireplace([".pdf",".doc", ".docx",".jpg",".jpeg",".png"], "", $textLien);
            $textLien = str_ireplace($extension, "", $textLien);
            $textLien = str_ireplace("_", " ", $textLien);
            $textLien = str_replace( array($_POST['numerodesession'],$_POST['numerodestagiaire']), "", $textLien );
			$textLien = str_ireplace( array($tableauFichier->nom, $tableauFichier->prenom, $tableauFichier->nom_supp_esp, $tableauFichier->prenom_supp_esp), "", $textLien );
            
			$textLien = str_ireplace("AGR1", "Votre agrément (page 2)", $textLien);
			$textLien = str_ireplace("AGR", "Votre agrément", $textLien);
			$textLien = str_ireplace("CNIV", "Votre pièce d'identité (Verso)", $textLien);
			$textLien = str_ireplace("CNI", "Votre pièce d'identité", $textLien);
			$textLien = str_ireplace("BS", "Votre bulletin de salaire", $textLien);
			$textLien = str_ireplace("RIB", "Votre RIB", $textLien);
			$textLien = str_ireplace("Certificate", "Certificat de signature", $textLien);
            
            $moodleLien= "img_extension/";
            switch($extension){
                case ".pdf":
                    $moodleLien .= 'pdf-icon.png';
                break;
                case ".doc":
                    $moodleLien .= 'doc.png';
                break;
                case ".jpeg":
                    $moodleLien .= 'jpeg.png';
                break;
                case ".jpg":
                    $moodleLien .= 'jpeg.png';
                break;
                case ".png":
                    $moodleLien .= 'png.png';
                break;
                case ".docx":
                    $moodleLien .= 'docx.png';
                break;
                default:
                    $moodleLien .= 'document.png';
            }

//proposition 2
         echo'<div class="mod2">';
            echo'<div class="mod3 mod4 ">';
                    echo '<p class="mod5"><a  class ="btn_fichier_telecharger mod5" href="'.$dolibarr.'custom/agefodd/session/download.php?path=' . $chemin . '/' . $f . '">';
                    echo '<img class ="btn_fichier_telecharger mod5"  src="'.$moodleLien.'"
                    />';
                    echo ucfirst($textLien) . '</a></p>
                    '; 
            echo'</div>';
        echo'</div>';
        }
    }
?>

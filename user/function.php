<?php
require_once('../config.php');
require_once($CFG->libdir.'/gdlib.php');

//function trier les formation (passé, en cours , à venir)
function sortFormation($username){
	global $CFG, $DB, $USER, $bdd;

    // select les informations concernant la formation                                                                                                                                                                                                                                                                                                                               nnnnnnn;ct toute les formation ou la stagiuaire elle inscrite
	$sql = "SELECT s.dated, s.datef, fc.ref_interne AS intitule_custo, 
                  fc.rowid, fc.ref, s.rowid, opca.num_OPCA_file as opcaIPE, s.num_OPCA_file as opcaIPE1,  
                  ts.rowid as idStagiaire, 
				  fce.presta_smart_agenda, st.fk_suivi, fc.rowid
				  
			FROM llx_agefodd_session AS s 
			INNER JOIN llx_agefodd_session_stagiaire as st ON s.rowid=st.fk_session_agefodd  
			INNER JOIN  llx_agefodd_stagiaire AS ts ON ts.rowid= st.fk_stagiaire
			INNER JOIN llx_agefodd_opca AS opca ON (opca.fk_session_agefodd = s.rowid AND ts.fk_soc = opca.fk_soc_trainee)
			INNER JOIN  llx_agefodd_formation_catalogue as fc ON s.fk_formation_catalogue=fc.rowid
			INNER JOIN llx_agefodd_formation_catalogue_extrafields AS fce ON fce.fk_object = fc.rowid

			WHERE ts.place_birth LIKE :username
			
			ORDER BY s.dated DESC";

	$req = $bdd->prepare($sql);
	$req->bindParam('username', $username);
	$req->execute();
	$infoformation = $req->fetchAll(PDO::FETCH_ASSOC);

	return $infoformation;  
}

// La fonction pour récuperer l'image
function setImage($ref){
   global $CFG, $DB, $USER;
   $linkimage=$DB->get_record('image_formations', ['ref'=>$ref]);
   return $linkimage;
}

function imgDefault($titre)	{
	// $titre = strtoupper($titre);
	$titre = str_ireplace( " - INFANS", "", $titre);
	
	$largeur_image = 125; // largeur de l'image en pixels
	$font_size = 12;
	$mots = wordwrap($titre, $largeur_image / ($font_size / 2), "\n");
	
	$lignes = explode("\n", $mots);
	
	$base_image = imagecreatefrompng('img_temp/degree.png');
	$font_path = 'img_temp/font/lgcBold.ttf';	// LouisGeorges Café
	$text_color = imagecolorallocate($base_image, 255, 255, 255);
	
	$y = 40;
	foreach ($lignes as $ligne) {
		$bbox = imagettfbbox($font_size, 0, $font_path, $ligne);

	// $text_width = $bbox[2] - $bbox[0];
	// $text_height = $bbox[3] - $bbox[5];
	// $text_width = 200;
	// $text_height = 50;
	// $x = (imagesx($base_image) - $text_width) / 2;
	// $y = (imagesy($base_image) - $text_height) / 2;

	$x = $bbox[0] + (imagesx($base_image) / 2) - ($bbox[4] / 2);
/*	$y = $bbox[1] + (imagesy($base_image) / 2) - ($bbox[5] / 2) - 5;
 */

		imagettftext($base_image, $font_size, 0, $x, $y, $text_color, $font_path, html_entity_decode($ligne));
		$y += $taille_police + 20;
	}
	// Add the text to the image
	// imagettftext($base_image, $font_size, 0, $x, $y, $text_color, $font_path, html_entity_decode($titre) );

	/* // Output the image to the browser or save it to a file
	header('Content-Type: image/png');
	$ret = imagepng($base_image);
	// var_dump(__LINE__, $ret); */
	return $base_image;
}

// refactoring du code
function afficheBlocFormation($info, $d, $f, $num, $lien)  {
	global $listImgToken;
	echo '<form action="https://moodle.infans.fr/user/formulaire.php" method="post">';
	echo '<input type="hidden" name="image" value="'.$lien.'">';
	echo '<input type="hidden" name="titredeformation" value="'.$info['intitule_custo'].'">';
	echo '<input type="hidden" name="numerodesession" value="'.$info['rowid'].'">';
	echo '<input type="hidden" name="numerodestagiaire" value="'.$info['idStagiaire'].'">';
	echo '<input type="hidden" name="numerodeparcours" value="'.(empty($num) ? $num1 : $num).'">';
	echo '<div class="item formation">';
	// var_dump($lien);
	$photo = str_ireplace("https://".$_SERVER['HTTP_HOST'], "", $lien);
	if( $lien && file_exists("..".$photo) )
		echo '<div class="image"><input type="image" name="submit" src="'.$lien.'" alt="Submit" style="width: 300px; height: 183px;" /></div>';
	else	{
		$token = bin2hex(random_bytes(5));
		imagepng(imgDefault( $info['intitule_custo'] ), $photo_temp = "img_temp/".$token.".png");
		echo '<div class="image"><input type="image" name="submit" src="'.$photo_temp.'" alt="Submit" style="width: 300px; height: 183px;" /></div>';
		$listImgToken[] = $photo_temp;
		// unlink($photo_temp);
	}
	echo '<div class="image1">';
	// echo '<p><strong>Dates : du </strong>' . date('d/m/Y', $d) . '<strong> au </strong>' . date('d/m/Y', $f) . '</p>';
	echo '<p>Du <strong>' . date('d/m/Y', $d) . '</strong> au <strong>' . date('d/m/Y', $f) . '</strong></p>';
	echo '</form>';
	echo '</div>';
	echo '</div>';
	echo '</form>';
	echo '<br>';
}

function fidelite($username)	{
	global $CFG, $DB, $USER, $bdd;
	
	$sql = "SELECT socVip.*, bareme.*, act.id, act.datep, act.datep2, act.code, act.note 

				FROM llx_agefodd_stagiaire AS st 
				INNER JOIN llx_societe AS soc ON soc.rowid = st.fk_soc
				INNER JOIN societe_pt_VIP_assmat AS socVip ON socVip.fk_soc = soc.rowid
				INNER JOIN bareme_pt_VIP_assmat AS bareme ON bareme.seuil_bas < socVip.nbPoints AND bareme.seuil_haut > socVip.nbPoints
				LEFT JOIN llx_actioncomm AS act ON act.fk_soc = soc.rowid AND act.code IN ('ProgFidelPhy', 'ProgFidelDem')

				WHERE st.place_birth LIKE :username"; 
				
	$req = $bdd->prepare($sql);
	$req->bindParam('username', $username);
	$req->execute();

	if( $req->rowCount() )	{
		return $req->fetchAll(PDO::FETCH_ASSOC);
	}
	else return 0;
}

function affiche_fidelite($fidel, $dateStart=0)	{
	$nbPoints = $fidel[0]['nbPoints'];
	$label = $fidel[0]['label'];
	$couleur = $fidel[0]['couleur'];
	
	if($dateStart)
		echo '<p>Vous nous êtes fidèle depuis&nbsp;: <strong>'.date('d/m/Y', $dateStart).'</strong>.</p>';
	echo '<p>Vous avez cumuler <strong>' . strval(($nbPoints?$nbPoints:0)) . '</strong> points.</p>';
	// echo '<p>Vous etes <strong>' . $label . '</strong> <strong>' . $couleur . '</strong>.</p>';
	echo '<p>&nbsp;</p>';
}	


function select_donnees($username)		{
	global $CFG, $DB, $USER, $bdd;
	
	$requete_select_donnees = "SELECT st.rowid AS idStagiaire, st.nom, st.prenom, st.civilite, st.tel2 AS gsm, st.mail, 
								st.address, st.zip, st.town, ste.numsecu, ste.nomNaissance, ste.num_passport_iperia
								FROM llx_agefodd_stagiaire AS st 
								INNER JOIN stagiaire_extrafields AS ste ON ste.stagiaireID = st.rowid
								WHERE st.place_birth LIKE :username
								LIMIT 1";
	
	$req = $bdd->prepare($requete_select_donnees);
	$req->bindParam('username', $username);
	$req->execute();
	
	return $req;
}

function affiche_donnees($username)		{
	$req = select_donnees($username);
	if( $req->rowCount() )	{
		$row = $req->fetch(PDO::FETCH_ASSOC);
		echo '<div class="contenerdonnees">';
			echo '<div class="blocks_donnees_bleu" id="contenerCivilite">';
			echo '<p>Civilité&nbsp;: '. ucwords(strtolower($row['civilite'])) .'</p>';
			echo '<p>Prénom&nbsp;: '. ucwords(strtolower($row['prenom'])) .'</p>';
			echo '<p>Nom&nbsp;: '. ucwords(strtolower($row['nom'])) .'</p>';
			echo '<p>Née&nbsp;: '. ucwords(strtolower($row['nomNaissance'])) .'</p>';
			echo '</div>';
			
			echo '<div class="blocks_donnees_transparent" id="contenerContact">';
			echo '<p>Téléphone&nbsp;: '. ucwords(strtolower($row['gsm'])) .'</p>';
			echo '<p>E-mail&nbsp;: '. ucwords(strtolower($row['mail'])) .'</p>';
			echo '<p>Adresse&nbsp;: '. nl2br(ucwords(strtolower($row['address']))) .'</p>';
			echo '<p>Code postal&nbsp;: '. ucwords(strtolower($row['zip'])) .'</p>';
			echo '<p>Ville&nbsp;: '. ucwords(strtolower($row['town'])) .'</p>';
			echo '</div>';
			
			echo '<div class="blocks_donnees_transparent" id="contenerDivers">';
			echo '<p>N° de sécurité sociale&nbsp;: '. strtoupper($row['numsecu']) .'</p>';
			echo '<p>N° de passport Ipéria&nbsp;: '. strtoupper($row['num_passport_iperia']) .'</p>';
			echo '</div>';
			
		echo '</div>';

		echo '<div class="zoneBoutton">';
		echo '<p>  <a href="'.$_SERVER['PHP_SELF'].'?edit=1#id_donnees">Modifier</a> </p>';
		echo '</div>';
	}
	else {
		echo '<p>Données indisponible</p>';
	}
}

function formulaire_donnees($username)		{
	global $erreursCivilite, $erreursContact, $erreursDivers;
	$req = select_donnees($username);
	if( $req->rowCount() )	{
		$row = $req->fetch(PDO::FETCH_ASSOC);
		echo '<form method="POST" action="'.$_SERVER['PHP_SELF'].'?edit=1#id_donnees">';
		echo '<input type="hidden" name="action" value="corrigeDonneesStagiaire" />';
		echo '<input type="hidden" name="idStagiaire" value="'.($_POST['idStagiaire']?$_POST['idStagiaire']:$row['idStagiaire']).'" />';
		
		echo '<div class="contenerdonnees">';
			echo '<div class="blocks_donnees_bleu" id="contenerCivilite">';
			echo '<div id="err1"></div>';
			echo '<div class="tableau">';
			echo '<p class="ligneTableau"><span class="cellGauche">Civilité&nbsp;:</span> 
				<span class="cellDroite">
				<select name="civilite">
					<option value="MME" ';
					if($_POST['civilite']=="MME")	echo "selected";
					elseif($row['civilite']=="MME")	echo "selected";
				echo '>Madame</option>';
			echo '	<option value="MR" ';
					if($_POST['civilite']=="MR")	echo "selected";
					elseif($row['civilite']=="MR")	echo "selected";
				echo '>Monsieur</option>
				</select>
				</span>
			</p>';
			echo '<p class="ligneTableau"><span class="cellGauche">Prénom&nbsp;:</span> 
				<span class="cellDroite"><input type="text" name="prenom" id="prenom" value="'. ($_POST['prenom']?$_POST['prenom']:$row['prenom']) .'" /></span></p>';
			echo '<p class="ligneTableau"><span class="cellGauche">Nom&nbsp;:</span> 
				<span class="cellDroite"><input type="text" name="nom" id="nom" value="'. ($_POST['nom']?$_POST['nom']:$row['nom']) .'" /></span></p>';
			echo '<p class="ligneTableau"><span class="cellGauche">Née&nbsp;:</span> 
				<span class="cellDroite"><input type="text" name="nomNaissance" id="nomNaissance" value="'. ($_POST['nomNaissance']?$_POST['nomNaissance']:$row['nomNaissance']) .'" /></span></p>';
			echo '</div>';	
			echo '</div>';
			afficheErreur($erreursCivilite, "err1");
			
			echo '<div class="blocks_donnees_transparent" id="contenerContact">';
			echo '<div id="err2"></div>';
			echo '<div class="tableau">';
			echo '<p class="ligneTableau"><span class="cellGauche">Téléphone&nbsp;:</span> 
				<span class="cellDroite"><input type="text" name="gsm" id="gsm" value="'. ($_POST['gsm']?$_POST['gsm']:$row['gsm']) .'" /></span></p>';
			echo '<p class="ligneTableau"><span class="cellGauche">E-mail&nbsp;:</span> 
				<span class="cellDroite"><input type="text" name="mail" id="mail" value="'. ($_POST['mail']?$_POST['mail']:$row['mail']) .'" /></span></p>';
			echo '<p class="ligneTableau"><span class="cellGauche">Adresse&nbsp;:</span> 
				<span class="cellDroite"><textarea name="address" id="address" rows="5" cols="33">'. ($_POST['address']?$_POST['address']:$row['address']) .'</textarea></span></p>';
			echo '<p class="ligneTableau"><span class="cellGauche">Code postal&nbsp;:</span> 
				<span class="cellDroite"><input type="text" name="zip" id="zip" value="'. ($_POST['zip']?$_POST['zip']:$row['zip']) . '" /></span></p>';
			echo '<p class="ligneTableau"><span class="cellGauche">Ville&nbsp;:</span> 
				<span class="cellDroite"><input type="text" name="town" id="town" value="'. ($_POST['town']?$_POST['town']:$row['town']) .'"</span></p>';
			echo '</div>';
			echo '</div>';
			afficheErreur($erreursContact, "err2");
			
			echo '<div class="blocks_donnees_transparent" id="contenerDivers">';
			echo '<div id="err3"></div>';
			echo '<div class="tableau">';
			echo '<p class="ligneTableau"><span class="cellGauche">N° de sécurité sociale&nbsp;:</span> 
				<span class="cellDroite"><input type="text" name="numsecu" id="numsecu" value="'. ($_POST['numsecu']?$_POST['numsecu']:$row['numsecu']) .'"</span></p>';
			echo '<p class="ligneTableau"><span class="cellGauche">N° de passport Ipéria&nbsp;:</span> 
				<span class="cellDroite"><input type="text" name="num_passport_iperia" id="num_passport_iperia" value="'. ($_POST['num_passport_iperia']?$_POST['num_passport_iperia']:$row['num_passport_iperia']) .'" /></p>';
			echo '</div>';
			echo '</div>';
			afficheErreur($erreursDivers, "err3");

		echo '</div>';
		
		echo '<div class="zoneBoutton">';
		echo '<input type="submit" value="Enregistrer" class="submitZoneBoutton" />';
		echo '</div>';

		echo '</form>';
	}
}

function validGsm($numero)		{
	return preg_match("#^(\+33|0)[67][0-9]{8}$#", $numero);
}

function numsecuInvalide( $t )	{
	$myPattern = '/^                        # début de chaîne
	(?<sexe>[12])                           # 1 ou 2 pour le sexe
	(?<naissance>[0-9]{2}(?:0[1-9]|1[0-2])) # année et mois de naissance (aamm)
	(?<departement>2[AB]|[0-9]{2})          # le département
	(?<numserie>[0-9]{6})                   # numéro de série sur six chiffres
	(?<controle>[0-9]{2})?                  # numéro de contrôle (facultatif)  
	$                                       # fin de chaîne
	/x';

	if (preg_match($myPattern, $t, $match)) {
		/* print "<ul>";
		printf ("<li>sexe : %s</li>", $match['sexe']);
		printf ("<li>année et mois de naissance : %s</li>", $match['naissance']);
		printf ("<li>département : %s</li>", $match['departement']);
		printf ("<li>numéro de série : %s</li>", $match['numserie']);
		printf ("<li>controle : %s</li>", $match['controle']);
		print "</ul>"; */
		$r = false;	//  true vaut valide
	}
	else $r = true;	//  true vaut invalide
	
	return $r;
}

function afficheErreur( $err, $numErreur )	{
	// var_dump($err);
	$ret = "";
	if( count($err) ) {
		$ret .= '<ul class=errList>';
		foreach( $err AS $code => $text )		{
			echo '<script>
			var el = document.getElementById("'.$code.'");
			el.style = "color:red;";
			// console.log(el);
			</script>';
			$ret .= '<li>' .$text. '</li>';
		}
		$ret .= '</ul>';
		echo '<script>
			var l = document.getElementById("'.$numErreur.'");
			console.log(l);
			l.innerHTML = "'.$ret.'";
			</script>';
	}
}

function select_events($type="")	{
	global $USER, $DB;
	
	$sql = "SELECT id, name, description, timestart, timeduration, location FROM mdl_event WHERE visible = 1 AND userid = :userid";
	$sql .= ($type=="AVANT" ? " AND timestart <= UNIX_TIMESTAMP(NOW())":($type=="APRES" ? " AND timestart >= UNIX_TIMESTAMP(NOW())":""));
	$sql .= " ORDER BY timestart";
	$param = array('userid' => $USER->id);
	
// echo $sql . ' ' . $USER->id;

	$result = $DB->get_records_sql($sql, $param);

	// $result = $DB->get_records('event', ['userid' => $USER->id, 'visible' => 1], 'timestart DESC', 'id,name, description, timestart, duration, location');
	
	return $result;
}

function affiche_events($events)	{
	echo '<div style="display:table;">';
	foreach( $events AS $event )	{
		echo '<div style="display:table-row;">';
		echo '<div class="tableau_event">' . $event->name .'</div>';
		echo '<div class="tableau_event">' . date('d/m/Y H\hi', $event->timestart) .'</div>';
		echo '<div class="tableau_event">' . floor($event->timeduration/60) .' min</div>';
		echo '<div class="tableau_event">' . $event->description .'</div>';
		echo '</div>';
	}
	echo '</div>';
}


/*
style=\"color: red; list-style-image: url(\'../img/redCross.jpg\'); font-weight: bold;width: 50px; height: auto;\"


    background-image: url(../img/redCross.jpg);
    list-style: none;
    background-repeat: no-repeat;
    background-position: left center;
    background-size: contain;
    padding: 0px 0px 1px 30px;
    font-weight: bold;




*/
?>
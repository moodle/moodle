<!DOCTYPE html>
<html>
<head>
   <meta charset="utf-8" />
   <title> Mon Compte</title>
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="stylesheet" href="https://moodle.infans.fr/user/mon_compteCss.css"> 
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css"/>
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css"/>
   <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>

</head>
<?php

  require_once('../config.php');
  
  require_once($CFG->dirroot.'/lib/datalib.php');
  require_once($CFG->dirroot.'/course/lib.php');
  include_once('../devINFANS/codoli.php');
  require_once('lib.php');
  require_once('function.php');
  require_once($CFG->dirroot.'/calendar/lib.php');
  
  require_login();
  
	if (isguestuser()) {  // Force them to see system default, no editing allowed
		// If guests are not allowed my moodle, send them to front page.
		if (empty($CFG->allowguestmymoodle)) {
			redirect(new moodle_url('/', array('redirect' => 0)));
		}

		$userid = null;
		$USER->editing = $edit = 0;  // Just in case
		$context = context_system::instance();
		$PAGE->set_blocks_editing_capability('moodle/my:configsyspages');  // unlikely :)
		$strguest = get_string('guest');
		$header = "$SITE->shortname: $strmymoodle ($strguest)";
		$pagetitle = $header;
	}
  
  
  $listImgToken = array();
  $debutFidelite = 0;
  
	if( $_POST['action'] == "corrigeDonneesStagiaire" )	{
		$idStagiaire = htmlspecialchars($_POST['idStagiaire']);
		$civilite = htmlspecialchars($_POST['civilite']);
		$prenom = htmlspecialchars($_POST['prenom']);
		$nom = htmlspecialchars($_POST['nom']);
		$nomNaissance = htmlspecialchars($_POST['nomNaissance']);
		$gsm = str_replace(" ", "", htmlspecialchars($_POST['gsm']));
		$mail = str_replace(" ", "", htmlspecialchars($_POST['mail']));
		// $address = htmlspecialchars($_POST['address']);
		$address = $_POST['address'];
		$zip = str_replace(" ", "", htmlspecialchars($_POST['zip']));
		$town = htmlspecialchars($_POST['town']);
		$numsecu = str_replace(" ", "", htmlspecialchars($_POST['numsecu']));
		$num_passport_iperia = str_replace(" ", "", htmlspecialchars($_POST['num_passport_iperia']));
		
// echo '<pre>';
// print_r($_POST);
// echo '</pre>';

// echo '<pre>';
// print_r($USER);
// echo '</pre>';
// echo '<pre style="color:red;">';

		$erreursCivilite = array();
		$erreursContact = array();
		$erreursDivers = array();
		
		if( ! $prenom )	$erreursCivilite['prenom'] = "Merci de saisir votre prénom.";
		if( ! $nom )	$erreursCivilite['nom'] = "Merci de saisir votre nom.";
		if( ! $nomNaissance )	$erreursCivilite['nomNaissance'] = "Merci de Saisir votre nom de naissance.";
	
		if( $gsm && ! validGsm($gsm) )	$erreursContact['gsm'] = "Numéro de téléphone invalide.";
		elseif ( ! $gsm )	$erreursContact['gsm'] = "Merci de saisir votre numéro de portable.";
		if( $mail && ! filter_var($mail, FILTER_VALIDATE_EMAIL) )	$erreursContact[] = "E-mail invalide.";
		elseif ( ! $mail )	$erreursContact['mail'] = "Merci de saisir votre email.";
		if( ! $address )	$erreursContact['mail'] = "Merci de saisir votre adresse postale.";
		if( ! $zip )	$erreursContact['zip'] = "Merci de saisir votre code postal.";
		if( ! $town )	$erreursContact['town'] = "Merci de saisir votre ville.";

		if( $numsecu && numsecuInvalide($numsecu) )	$erreursDivers['numsecu'] = "Numéro de sécurité sociale est invalide.";
		
		if( strtolower($USER->email) != strtolower($_POST['mail']) )	{
			$sql = "SELECT count(*) AS nb FROM mdl_user WHERE (username LIKE '".$mail."' OR email LIKE '".$mail."') AND id != " . $USER->id;
			$nb = $DB->get_records_sql($sql)[0]->nb;
			if($nb != 0)	{
				$erreursCivilite['mail'] = "L'email est déjà connu.";
			}
		}
		
// print_r($erreursContact);
// print_r($erreursDivers);
// echo '</pre>'; exit;

		
		if( !$erreursCivilite && !$erreursContact && !$erreursDivers )	{
			$sql = "UPDATE mdl_user SET firstname = '" .$prenom;
			$sql .= "', lastname = '" .$nom. "', email='" .$mail. "' WHERE id = " . $USER->id ;
			$result = $DB->execute($sql);
			
			$sql = 'UPDATE llx_agefodd_stagiaire as st 
							INNER JOIN stagiaire_extrafields as ste ON ste.stagiaireID = st.rowid
							INNER JOIN llx_societe AS soc ON soc.rowid = st.fk_soc

							SET st.nom = "' .$nom. '", st.prenom = "' .$prenom. '", st.tel2 = "' .$gsm. '", st.mail = "' .$mail. '", st.address = "' .$address. '", st.zip = "' .$zip. '", st.town = "' .$town. '",
							ste.numsecu = "' .$numsecu. '", ste.nomNaissance = "' .$nomNaissance. '", ste.num_passport_iperia = "' .$num_passport_iperia. '", 
							soc.nom = CONCAT("' .$nom. '", " ", "'.$prenom.'"), soc.address = "' .$address. '", soc.zip = "' .$zip. '", soc.town = "' .$town. '", soc.fax = "' .$gsm. '", soc.email = "' .$mail. '"


							WHERE st.rowid = ' . $idStagiaire;
			$bdd->query( $sql ); 
		}
		
		header("Location: " . $_SERVER['PHP_SELF']);
		exit;

		// exit( var_dump( $DB ) );	
//		exit(  );
	}
//   echo '<pre>';
//   print_r($USER);
//   echo '</pre>';
//   exit;
$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('standard');
$PAGE->set_title("About page");
$PAGE->set_heading("Bienvenue sur votre espace personnel " /* . $USER->firstname . " " . $USER->lastname */);
$PAGE->set_url($CFG->wwwroot . '/about.php');

if (!isloggedin()) {
   // Rediriger l'utilisateur vers la page de connexion si non connecté
   header('Location:'.$CFG->wwwroot . '/login/index.php');
   exit;
 } 

echo $OUTPUT->header();
?>
<body>

<?php

$infoformation = sortFormation($USER->username);
$dated = time();
$blocs = array(
    'en_cours' => array('titre' => 'Formations en cours', 'afficher' => false),
    'a_venir' => array('titre' => 'Formations à venir', 'afficher' => false),
    'terminees' => array('titre' => 'Formations passées', 'afficher' => false)
);
$e = 0;
$a = 0;
$t = 0;





// Vérifier si la formation est en cours
echo '<div class="row">';
echo '<div class="colonne1">';
echo '<h2><strong>Vos formations</strong></h2><br>';
echo '<div class="contener-fomulaires">';
foreach ($infoformation as $inform) {
	$date_debut = strtotime($inform['dated']);
	$date_fin = strtotime($inform['datef']);
	//  $course = get_course_by_shortname($inform['ref']);
	//  $linkimage = course_get_format($course)->get_course_image_url();
	$linkimage = setImage($inform['ref']);
	$opcafile = !empty($inform['opcaIPE']) ? $inform['opcaIPE'] : $inform['opcaIPE1'];

	if ($date_debut <= $dated && $date_fin > $dated) {
		if( !$debutFidelite || $date_debut < $debutFidelite )	$debutFidelite = $date_debut;
		if (!$blocs['en_cours']['afficher']) {
			$blocs['en_cours']['afficher'] = true;
			echo '<h3><strong>' . $blocs['en_cours']['titre'] . '</strong></h3><hr>';
		}
		echo '<div class="bloc-fomulaires">';
		$blocs['en_cours']['contenu'] .= '<div class="bloc-formation">' . afficheBlocFormation($inform, $date_debut, $date_fin, $opcafile, $linkimage->lien);
		echo '</div>';

		$presta = $inform['presta_smart_agenda'];
		if($inform['fk_suivi'] == 805)
			$suivi = 35;
		elseif($inform['fk_suivi'] == 814)
			$suivi = 36;
		else $suivi = 35;		// en attendant emilie robin
	}	
}
echo '</div>';



// Vérifier si la formation est à venir
echo '<div class="contener-fomulaires">';
foreach ($infoformation as $inform) {   
   $date_debut = strtotime($inform['dated']);
   $date_fin = strtotime($inform['datef']);
   //  $course = get_course_by_shortname($inform['ref']);
   //  $linkimage = course_get_format($course)->get_course_image_url();
   $linkimage = setImage($inform['ref']);
   $opcafile = !empty($inform['opcaIPE']) ? $inform['opcaIPE'] : $inform['opcaIPE1'];
  
  
   if ($date_debut > $dated) {
		if( !$debutFidelite || $date_debut < $debutFidelite )	$debutFidelite = $date_debut;
		if (!$blocs['a_venir']['afficher']) {
			$blocs['a_venir']['afficher'] = true;
			echo '<h3><strong>' . $blocs['a_venir']['titre'] . '</strong></h3><hr>';
		}
		echo '<div class=bloc-fomulaires">';
		$blocs['a_venir']['contenu'] .= '<div class="bloc-formation">' . afficheBlocFormation($inform, $date_debut, $date_fin, $opcafile, $linkimage->lien);
		echo '</div>';
	}
}
echo '</div>';

// Formation terminée
echo '<div class="contener-fomulaires">';
foreach ($infoformation as $inform) {   
	$date_debut = strtotime($inform['dated']);
	$date_fin = strtotime($inform['datef']);
	//  $course = get_course_by_shortname($inform['ref']);
	//  $linkimage = course_get_format($course)->get_course_image_url();
	$linkimage = setImage($inform['ref']);
	$opcafile = !empty($inform['opcaIPE']) ? $inform['opcaIPE'] : $inform['opcaIPE1'];

	if ( $date_fin < $dated && $date_debut > 1609455600) {
		if( !$debutFidelite || $date_debut < $debutFidelite )	$debutFidelite = $date_debut;
		if (!$blocs['terminees']['afficher']) {
			$blocs['terminees']['afficher'] = true;
			echo '<h3><strong>' . $blocs['terminees']['titre'] . '</strong></h3><hr>';
		}
		echo '<div class=bloc-fomulaires">';
		$blocs['terminees']['contenu'] .= '<div class="bloc-formation">' . afficheBlocFormation($inform, $date_debut, $date_fin, $opcafile, $linkimage->lien);
		echo '</div>';
	}
}
echo '</div>';
echo '</div>';
echo '<div class="colonne2">';
echo '<h2><strong>Votre profil</strong></h2><br>';
?>

<h3><strong>Programme fidélité</strong></h3><hr>

<?php
	$fidelite = fidelite($USER->username);
	
	/* if( ! $fidelite )	{
		echo '<p>blabla ... <br />... plus tard</p>';
	}
	else	{ */
		affiche_fidelite( $fidelite, $debutFidelite );
	/* } */
?>

<h3 id="id_donnees"><strong>Vos données</strong></h3><hr />
<?php
	if( array_key_exists('edit', $_GET) )
		formulaire_donnees($USER->username);
	else
		affiche_donnees($USER->username);
?>


<?php
	$events = select_events();

	if($events)	{
		echo '<h3 id="id_events"><strong>Vos dates importantes</strong></h3><hr />';
			$eventsAvant = select_events('AVANT');
			$eventsApres = select_events('APRES');
			
			echo '<ul class="tabs">
				<li><a id="blocClic1" href="#tab1">Dates passées</a></li>
				<li><a id="blocClic2" href="#tab2">Dates à venir</a></li>
			</ul>';

			echo '<div id="tab1" class="tab-content">';
			//Contenu de l\'onglet 1
			if( !$eventsAvant )
				echo '<p>Pas d\'éléments marquants</p>';
			else
				affiche_events($eventsAvant);
			echo '</div>
			<div id="tab2" class="tab-content">';
			  // Contenu de l\'onglet 2
			if( !$eventsApres )
				echo '<p>Pas d\'éléments marquants</p>';
			else
				affiche_events($eventsApres);
			echo '</div>';


		foreach( $events AS $event)	{
		}
	}
?> 




<?php	/////////  SMART AGENDA
	if($presta)	{
?>

<h3 id="smartAgendaCalendar"><strong>Prise de rendez-vous</strong></h3><hr />
<div id="smart-container"></div>
<script src="https://www.smartagenda.fr/pro/infans/smartwidget.js" type="text/javascript"></script>
<script>
	window.onload = function(){
	var options = {
		contenu : false,
		entete : false,
		footer : false,
		bandeau : false,
		infosimportantes : false,
		<?php if($presta != 'SUIVI') 	{ ?>
			presta : <?php echo $presta; ?>,
		<?php }
		else	{
		?>
		employe : <?php echo $suivi; ?>, 
		<?php 
		}
		?>
		affrdv : true
	};
	var smartwidget = new SMARTAGENDAwidget(
		'smart-container',
		'https://www.smartagenda.fr/pro/infans/',
		options
		);
	smartwidget.render();
	};

	/* Uncaught DOMException: Blocked a frame with origin
	setTimeout(() => {
		var tabsite = document.getElementById('smart-container-iframe').contentWindow.document.getElementById('tabsite');
		console.log(tabsite);
	}, 500);
	*/
	
</script>
<?php
	} // fin de if $presta
?>

</div>




<?php echo $OUTPUT->footer(); ?>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
</body>
</html>

<script type="text/javascript">
	var xhr = getXMLHttpRequest();
	<?php
		echo 'xhr.open("GET", "supprFilesTemp.php?';
		//var=' . $listImgToken[$i];
		for($i=0; $i<count($listImgToken); $i++)	{
			echo '&var'.$i.'='. $listImgToken[$i];
		}
		echo '", true);';
		echo 'xhr.send(null);';
	?>

	function getXMLHttpRequest() {
		var xhr = null;

		if (window.XMLHttpRequest || window.ActiveXObject) {
			if (window.ActiveXObject) {
				try {
					xhr = new ActiveXObject("Msxml2.XMLHTTP");
				} catch(e) {
					xhr = new ActiveXObject("Microsoft.XMLHTTP");
				}
			} else {
				xhr = new XMLHttpRequest(); 
			}
		} else {
			alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
			return null;
		}
		
		return xhr;
	}
</script>
<script>

	const myButton1 = document.getElementById('blocClic1');
	const myButton2 = document.getElementById('blocClic2');
	
	myButton1.addEventListener('click', function(event) {
		event.preventDefault(); // prevents the default action of the button click event
		// console.log('Button 1 clicked!');
		document.getElementById('tab1').style.display = 'block';
		document.getElementById('tab2').style.display = 'none';
		myButton1.style.color = 'white';
		myButton2.style.color = 'white';
		myButton1.style.backgroundColor  = '#99cede';
		myButton2.style.backgroundColor  = '#67c4d7';
	});
	myButton2.addEventListener('click', function(event) {
		event.preventDefault(); // prevents the default action of the button click event
		// console.log('Button 2 clicked!');
		document.getElementById('tab1').style.display = 'none';
		document.getElementById('tab2').style.display = 'block';
		myButton1.style.color = 'white';
		myButton2.style.color = 'white';
		myButton1.style.backgroundColor  = '#67c4d7';
		myButton2.style.backgroundColor  = '#99cede';
	});

</script>

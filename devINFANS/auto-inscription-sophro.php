<?php
set_include_path('/var/www/html/user');
require_once('../config.php');
require_once($CFG->libdir.'/gdlib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/editadvanced_form.php');
require_once($CFG->dirroot.'/user/editlib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/enrol/manual/locallib.php');

$user = new stdClass();
$dateDebut = $_POST['dated'];
$dateFin = $_POST['datef'];
$passClair = $_POST['password'];
$pass = hash_internal_user_password($passClair);

if($_POST['username'] != '')
	$user->username = $_POST['username'];

else	$user->username = $_POST['socid'];

if ($_POST['mail'] == '') {
	$user->email = $user->username."@formassmat.fr";
} 

else $user->email = $_POST['mail'];


$user->auth = "manual";
$user->suspended = 0;
$user->password = $pass;
$user->firstname = $_POST['nom'];
$user->lastname = utf8_decode($_POST['prenom']);
$user->phone1 = $_POST['tel'];
$user->timezone = 99;
$user->lang = "fr";
$user->mnethostid = 1;
$user->confirmed = 1;
$user->timecreated = time();

$lien = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname) or die("echec de la connection mysql");
$lien->set_charset("latin1");

$sql = 'SELECT * FROM mdl_user WHERE username = "' . $user->username . '" AND deleted = 0';

$result = $lien->query($sql);

// Vérification du résultat
if (!$result) {
	$message  = 'Requete invalide : /n';
	$message .= 'Requete complete : ' . $sql;
	die($message);
}


if ($result->num_rows > 0)	{
	$datas = $result->fetch_assoc();
	echo 'username deja existant. Session : '.$_POST['session'].'<br>';
	$idUser = $datas['id'];
	echo 'Id user moodle : '.$idUser.'<br>';
	echo 'Username : '.$datas['username'].'<br>';

}
else
{	//username inexistant dans la base moodle, on l'y cree
	$createuser = "INSERT INTO mdl_user VALUES ('', 'manual', 1, 0, 0, 0, 1, '".$user->username."', '".$pass."', '', '".$user->firstname."', '".$user->lastname."', '".$user->email."', 0, '', '', '', '', '', '".$user->phone1."', '', '', '', '', '', '', 'fr', 'gregorian', '', 99, 0, 0, 0, 0, '', '', 0, '', '', 1, 1, 0, 2, 0, 0, ".time().", ".time().", 0, '', '', '', '', '')";
	// $createuser = mysqli_real_escape_string($createuser);
	// $idUser = user_create_user($user, false, false);
	$lien->query($createuser);
	echo 'Utilisateur créer : '.$createuser.'<br>';
	// exit;
	$idUser = $lien->insert_id;
}

$codeMoodle = str_replace(' ', '', $_POST['codeMoodle']);
$separateurs = array(",", ".");
$codeMoodle = str_replace($separateurs, ';', $codeMoodle);
$list_codeMoodle = explode(";", $codeMoodle);
$nb_code = count ($list_codeMoodle);
// echo '<pre>';
// print_r( $list_codeMoodle );
// echo '</pre>';
for ($i=0; $i < $nb_code; $i++)	{ //modif Rayan 25/03 : boucle for, qui traite cours par cours, groupe par groupe (au cas ou il y ait plusieurs cours)
	$sql = 'SELECT e.id as enrolId, c.id as courseId 
		FROM mdl_enrol as e 
		LEFT JOIN mdl_course as c ON c.id = e.courseid
		WHERE e.enrol =  "manual"
		AND c.shortname = "'.$list_codeMoodle[$i].'" 
		AND e.roleid = 5';
	$result = $lien->query($sql);			
		
	// Vérification du résultat
	if (!$result) {
		$message  = 'Requete invalide : ';
		$message .= 'Requete complete : ' . $sql;
		die($message);
	}

	// a t'on une correspondance :
	$num_rows = $result->num_rows;
	if($num_rows != 1)	{
		exit('probleme de correspondance code formation ('.$num_rows.')');
	}


	//si un seul resultat :
	$datas = $result->fetch_assoc();  
	$id = $datas['courseId'];
	// $list_course_ids[$i] = $id;
	$enrol = $datas['enrolId'];

	echo $id.'<br>';
	echo $enrol.'<br>';

	$sql = 'SELECT * FROM mdl_user_enrolments WHERE userid = '.$idUser.' AND enrolid = '.$enrol;	// déja enrolé ?
	$result = $lien->query($sql);

	if (!$result)	{
		$message  = 'Requete invalide : ';
		$message .= 'Requete complete : ' . $sql;
		die($message);
	}

	// a t'on une correspondance :
	$num_rows = $result->num_rows;
	if($num_rows == 0)	{
		$sql = "INSERT INTO mdl_user_enrolments (status, enrolid, userid, timestart, timeend, timecreated, timemodified) VALUES(0, ".$enrol.", ".$idUser.", ".$dateDebut.", ".$dateFin.", ".time().", ".time().")";
		echo 'Creation de enrolments : '.$sql;
		$lien->query($sql); //modif Rayan, création propre de la requête pour l'inscription à un cours.
	}
	else
	{
		while ( $data = $result->fetch_assoc() )
		{
			if ($data['timestart'] != $dateDebut)
			{
				$update = "UPDATE mdl_user_enrolments SET timestart = ".$dateDebut.", timeend = ".$dateFin.", timemodified = ".time()." WHERE id = ".$data['id'];
				echo 'Update de time a faire : '.$update.'<br>';
				$lien->query($update);
			}
		}
	}


	$group = "SELECT * FROM mdl_groups WHERE name LIKE '".$_POST['groupe']."' AND courseid = 95";
	$groupquery = $lien->query($group);
	$data = $groupquery->fetch_assoc();
	$idgroup = $data['id'];
	$groupmember = "SELECT * FROM mdl_groups_members WHERE userid = ".$idUser." AND groupid = ".$idgroup;
	$groupmemberquery = $lien->query($groupmember);
	$nbligne = $groupmemberquery->num_rows;
	if ( $nbligne == 0 )
	{
		$ajoutmembregroup = "INSERT INTO mdl_groups_members VALUES ('', ".$idgroup.", ".$idUser.", ".time().", '', 0)";
		echo 'Ajout dans group members : '.$ajoutmembregroup.' <br>';
		$lien->query($ajoutmembregroup);

	}
}
exit();
?>
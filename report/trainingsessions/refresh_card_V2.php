<?php
include_once("../../config.php");

// defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/renderers/htmlrenderers.php');

include_once('lib_rapport.php');
function temps_activite ($id, $course) {
	
	$dated = $_POST['dated'];
	$datef = $_POST['datef'];

	$logs = use_stats_extract_logs($dated, $datef + 7884000, $id, $course);
	$aggregate = use_stats_aggregate_logs($logs, $dated + 7884000, $datef);
	$weekaggregate = use_stats_aggregate_logs($logs, time() - WEEKSECS, time());
// if($id == 953) { /// mme Bize / session 16264
// echo 'Etude encours pour mme bize <pre>';
// print_r( $logs );
// echo '<hr />';
// print_r( $aggregate );
// echo '<hr />';
// print_r( $weekaggregate );
// echo '</pre>';
// exit;
// }

	if (empty($aggregate['sessions'])) {
    $aggregate['sessions'] = array();
	}

	// Get course structure.

	$coursestructure = report_trainingsessions_get_course_structure($course, $items);
	// Time period form.

	$str = '';
	$dataobject = report_trainingsessions_print_html($str, $coursestructure, $aggregate, $done);

	var_dump($dataobject);
	if (empty($dataobject)) {
	    $dataobject = new stdClass();
	}


	$dataobject->items = $items;
	$dataobject->done = $done;

	if ($dataobject->done > $items) {
	    $dataobject->done = $items;
	}

	// In-activity.

	return @$aggregate['coursetotal'][$course]->elapsed;
}

echo '<pre>';
print_r( $_POST );
echo '</pre>';
// exit();
$username = str_replace(' ', '', $_POST['username']);
$separateurs = array(",");
$username = str_replace($separateurs, ';', $username);
$list_username = explode(";", $username);
$nb_user = count ($list_username);

echo '<pre>';
print_r( $list_username );
echo '</pre>';

$codeMoodle = str_replace(' ', '', $_POST['codeMoodle']);
$separateurs = array(",", ".");
$codeMoodle = str_replace($separateurs, ';', $codeMoodle);
$list_codeMoodle = explode(";", $codeMoodle);
$nb_code = count ($list_codeMoodle);


for ($i=0; $i < $nb_user; $i++)	{
	$sql = "SELECT * FROM mdl_user WHERE username LIKE '".$list_username[$i]."'";
	$req = $bdd->query($sql);
	$row = $req->fetch(PDO::FETCH_ASSOC);
	$userexistant = $req->rowCount();
	if ( $userexistant != 0 )
	{
		echo $sql.'<br>';
		echo $row['id'].'<br>';
		$sql1 = "SELECT enrol.courseid FROM mdl_enrol AS enrol INNER JOIN mdl_user_enrolments AS enrolment ON enrolment.enrolid = enrol.id WHERE enrolment.userid = ".$row['id']." AND enrolment.timestart = ".$_POST['dated'];
		echo $sql1.'<br>';
		$req1 = $bdd->query($sql1);
		$nbrow = $req1->rowCount();
		echo $nbrow.' lignes<br>';
		if ($nbrow === 0)
		{
			$sql1 = "SELECT enrol.courseid, course.shortname FROM mdl_enrol AS enrol INNER JOIN mdl_user_enrolments AS enrolment ON enrolment.enrolid = enrol.id INNER JOIN mdl_course AS course ON enrol.courseid = course.id WHERE enrolment.userid = ".$row['id'];
			$req1 = $bdd->query($sql1);
			while ($row1 = $req1->fetch(PDO::FETCH_ASSOC))
			{
				for ($m=0; $m < $nb_code; $m++)	{

					if ($row1['shortname'] === $list_codeMoodle[$m])
						{
							echo $row1['courseid'].'<br>';
							echo temps_activite($row['id'], $row1['courseid']).'<br>';
							echo seconds_to_hours(temps_activite($row['id'], $row1['courseid'])).'<br><br>';
							$retour .= $list_username[$i].'|'.seconds_to_hours(temps_activite($row['id'], $row1['courseid'])).'|';
						}
				}
			}
		}
		else
		{
			while ($row1 = $req1->fetch(PDO::FETCH_ASSOC))
				{
					echo $row1['courseid'].'<br>';
					echo temps_activite($row['id'], $row1['courseid']).'<br>';
					echo seconds_to_hours(temps_activite($row['id'], $row1['courseid'])).'<br><br>';
					$retour .= $list_username[$i].'|'.seconds_to_hours(temps_activite($row['id'], $row1['courseid'])).'|';
				}
		}
	}
	
}


/*if( isset($_POST['selectGroup']) )	{
	echo 'Patientez ...<br />... ca mouline';
	$selectGroup = intval($_POST['selectGroup']);
	$dated = date('d/m/y', $_POST['dated']);
	$datede = strtotime($dated);
	echo $_POST['dated'].'<br>';
	
	$codeMoodle = str_replace(' ', '', $_POST['codeMoodle']);
	$separateurs = array(",", ".");
	$codeMoodle = str_replace($separateurs, ';', $codeMoodle);
	$list_codeMoodle = explode(";", $codeMoodle);
	$nb_code = count ($list_codeMoodle);
	echo '<pre>';
	print_r( $list_codeMoodle );
	echo '</pre>';
	for ($i=0; $i < $nb_code; $i++)	{ //modif Rayan 25/03/2020 : ajout de la boucle for qui récupère les courseid concernée
		$sql = 'SELECT e.id as enrolId, c.id as courseId 
				FROM mdl_enrol as e 
				LEFT JOIN mdl_course as c ON c.id = e.courseid
				WHERE e.enrol =  "manual"
				AND c.shortname = "'.$list_codeMoodle[$i].'"';
		$req = $bdd->query($sql);
		$sqlrow = $req->fetch(PDO::FETCH_ASSOC);
		$courseid .= $sqlrow['courseId'].',';
		}
		$courseid = substr($courseid, 0, -1);
		$sql_utilisateur =  "SELECT DISTINCT user.id, user.username, groupes.courseid as groupcourse, enrol.courseid as enrolcourse, groupes.name FROM mdl_user AS user 
                                INNER JOIN mdl_groups_members as groupmember ON user.id = groupmember.userid
                                INNER JOIN mdl_groups AS groupes ON groupmember.groupid = groupes.id 
                                INNER JOIN mdl_user_enrolments AS enrolm ON user.id = enrolm.userid
                                INNER JOIN mdl_enrol AS enrol ON enrolm.enrolid = enrol.id
                                LEFT JOIN mdl_course AS course ON course.id = groupes.courseid
                                WHERE groupes.name LIKE '".date('d/m/Y', $_POST['dated'])."_".$_POST['selectGroup']."'";
	
	$sql_utilisateur .= " AND groupes.courseid IN(".$courseid.") AND enrol.courseid IN(".$courseid.") AND groupes.courseid = enrol.courseid ORDER BY user.id "; //modif requete rayan 25/03/2020 : précision ajouté sur le courseid, ainsi que l'égalité entre l'enrolcourse et le courseid. Le order BY est important au cas ou il y ait plusieurs cours, il permettra l'ajout du temps pour la stagiaire dans la suite du traitement.

	echo $sql_utilisateur;
	// exit;
	$reponse2 = $bdd->prepare($sql_utilisateur);
	$reponse2->execute();
	// $retour='';
	$row = $reponse2->rowCount();
	echo $row.'<br>';
	// if ( strstr($_POST['ref'], "80") )
	// {
	// 	while($donnees = $reponse2->fetch())    {
	// 	// if ( $donnees['groupcourse'] === $donnees['enrolcourse'] )
	// 	// {
	// 		$temps_eq=temps_activite($donnees['id'], $donnees['enrolcourse']);
	// 	// else $temps_eq=temps_equivalent($donnees['id'], $donnees['courseid']);
	// 		$retour .= $donnees['username'] . "|" . seconds_to_hours($temps_eq) . "|";
	// 	// }
	// 	}
	// }

	// if ( $row > 1 )
	// {
	// 	while($donnees = $reponse2->fetch())    {
	// 	if ( $donnees['groupcourse'] === $donnees['enrolcourse'] )
	// 	{
	// 		$temps_eq=temps_activite($donnees['id'], $donnees['enrolcourse']);
	// 	// else $temps_eq=temps_equivalent($donnees['id'], $donnees['courseid']);
	// 		$retour .= $donnees['username'] . "|" . seconds_to_hours($temps_eq) . "|";
	// 	}

	// 	}
	// }

	// if ( $row === 1 )
	// {
	if ($row === 0 ) //Rayan le 26/03/2020 : gestion des cas inscrits aux anciens cours 
	{
		$sql = "SELECT DISTINCT user.id, user.username, groupes.courseid as groupcourse, enrol.courseid as enrolcourse, groupes.name FROM mdl_user AS user 
                                INNER JOIN mdl_groups_members as groupmember ON user.id = groupmember.userid
                                INNER JOIN mdl_groups AS groupes ON groupmember.groupid = groupes.id
                                INNER JOIN mdl_user_enrolments AS enrolm ON user.id = enrolm.userid
                                INNER JOIN mdl_enrol AS enrol ON enrolm.enrolid = enrol.id
                                LEFT JOIN mdl_course AS course ON course.id = groupes.courseid
                                WHERE groupes.name LIKE '".date('d/m/Y', $_POST['dated'])."_".$_POST['selectGroup']."'";
        $sql .= " AND enrolm.timestart = '".$_POST['dated']."' AND groupes.courseid = enrol.courseid ORDER BY user.id";
        echo '<br>'.$sql;
		// exit;
		$reponse2 = $bdd->prepare($sql);
		$reponse2->execute();
		while($donnees = $reponse2->fetch())    {
		// if ( $donnees['groupcourse'] === $donnees['enrolcourse'] )
		// {
			$temps_eq=temps_activite($donnees['id'], $donnees['enrolcourse']);
		// else $temps_eq=temps_equivalent($donnees['id'], $donnees['courseid']);
			$retour .= $donnees['username'] . "|" . seconds_to_hours($temps_eq) . "|";
		// }
		}
	}
	else 
	{
		while($donnees = $reponse2->fetch())    {
		// if ( $donnees['groupcourse'] === $donnees['enrolcourse'] )
		// {
			$temps_eq=temps_activite($donnees['id'], $donnees['enrolcourse']);
		// else $temps_eq=temps_equivalent($donnees['id'], $donnees['courseid']);
			$retour .= $donnees['username'] . "|" . seconds_to_hours($temps_eq) . "|";
		// }
		}
	}*/
	// }
	// echo '<pre>';
	// print_r( $reponse2->execute() );
	// echo '</pre>';
	
	echo '<p>'.$retour.'</p>';
	// exit();
	// $t_dolibarr = explode("/", $_SERVER['HTTP_REFERER']);
	$url_dolibarr="http://usein.dolibarrgestion.fr/devinfans/tempsMoodleSession/";
	// $nb = count($t_dolibarr)-2;
	/*for($i=2; $i<$nb; $i++)	{
		$url_dolibarr .= $t_dolibarr[$i] . '/';
	}*/

	$url_dolibarr .= "retour_refresh.php";
	// exit;
	echo "<form id='data' action='".$url_dolibarr."' method='post'>";
	echo "<input type='hidden' name='selectGroup' value='". $_POST['selectGroup'] ."'>";
	echo "<input type='hidden' name='retourRefesh' value='". $retour ."'>";
	echo "</form>";
	echo "<script type='text/javascript'>document.forms['data'].submit();</script>";

// }


?>
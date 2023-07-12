<?php

include_once("../../config.php");
include('INC/connexion.php');

// defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/renderers/htmlrenderers.php');

include_once('lib_rapport.php');

function temps_activite ($id, $course) {
	
	$dated = strtotime($_POST['dated']);
	$datef = strtotime($_POST['datef']);

	$logs = use_stats_extract_logs($dated, $datef, $id, $course);
	$aggregate = use_stats_aggregate_logs($logs, $dated, $datef);
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

$selectGroup = $_POST['selectGroup'];

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
	echo $sql.'<br>';
	echo $row['id'].'<br>';
	$sql1 = "SELECT enrol.courseid FROM mdl_enrol AS enrol INNER JOIN mdl_user_enrolments AS enrolment ON enrolment.enrolid = enrol.id WHERE enrolment.userid = ".$row['id']." AND enrolment.timestart = ".strtotime($_POST['dated']);
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
/*if( isset($_POST['selectGroup']) )	{
	$dated = date('d/m/Y', $_POST['dated']);
	$codeMoodle = str_replace(' ', '', $_POST['codeMoodle']);
	$separateurs = array(",", ".");
	$codeMoodle = str_replace($separateurs, ';', $codeMoodle);
	$list_codeMoodle = explode(";", $codeMoodle);
	$nb_code = count ($list_codeMoodle);
	//modif Rayan 25/03 : on va chercher le cours concerné pour le relevé, precision sur la requête sql pour eviter les doublons de groupe, et bien correspondre l'enrolcourse et le courseid du groupe.
	for ($i=0; $i < $nb_code; $i++)	{
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
	// echo 'Patientez ...<br />... ca mouline';
	$selectGroup = intval($_POST['selectGroup']);
	$sql_utilisateur =  "SELECT DISTINCT user.id, user.username, groups.courseid, groups.name FROM mdl_user AS user 
                                INNER JOIN mdl_groups_members as groupmember ON user.id = groupmember.userid
                                INNER JOIN mdl_groups AS groups ON groupmember.groupid = groups.id
                                INNER JOIN mdl_user_enrolments AS enrolm ON user.id = enrolm.userid
                                INNER JOIN mdl_enrol AS enrol ON enrolm.enrolid = enrol.id
                                LEFT JOIN mdl_course AS course ON course.id = groups.courseid
                                WHERE groups.name LIKE '".$dated."_";
	
	$sql_utilisateur .= $selectGroup."'";
	$sql_utilisateur .= " AND groups.courseid IN(".$courseid.") AND enrol.courseid IN(".$courseid.") AND groups.courseid = enrol.courseid ORDER BY user.id ";
	// $sql_utilisateur .= "%' AND groups.timemodified = (SELECT MAX(timemodified) FROM mdl_groups WHERE name LIKE '%".$selectGroup."%') AND enrol.courseid = groups.courseid";
	
	// echo $sql_utilisateur;
	
	$reponse2 = $bdd->prepare($sql_utilisateur);
	$reponse2->execute();
	$row = $reponse2->rowCount();

	if ($row === 0 ) //Rayan le 26/03/2020 : gestion des cas inscrits aux anciens cours 
	{
		$sql = "SELECT DISTINCT user.id, user.username, groups.courseid as groupcourse, enrol.courseid as enrolcourse, groups.name FROM mdl_user AS user 
                                INNER JOIN mdl_groups_members as groupmember ON user.id = groupmember.userid
                                INNER JOIN mdl_groups AS groups ON groupmember.groupid = groups.id
                                INNER JOIN mdl_user_enrolments AS enrolm ON user.id = enrolm.userid
                                INNER JOIN mdl_enrol AS enrol ON enrolm.enrolid = enrol.id
                                LEFT JOIN mdl_course AS course ON course.id = groups.courseid
                                WHERE groups.name LIKE '".date('d/m/Y', $_POST['dated'])."_".$_POST['selectGroup']."'";
        $sql .= " AND enrolm.timestart = '".strtotime($_POST['dated'])."' AND groups.courseid = enrol.courseid ORDER BY user.id";
        // echo $sql;
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
	// echo '<p>'.$retour.'</p>';
	
	// $t_dolibarr = explode("/", $_SERVER['HTTP_REFERER']);
	// $url_dolibarr="http://";
	// $nb = count($t_dolibarr)-1;
	// 	for($i=2; $i<$nb; $i++)	{
	// 	$url_dolibarr .= $t_dolibarr[$i] . '/';
	// }

	$url_dolibarr .= "http://usein.dolibarrgestion.fr/devinfans/tempsMoodleSession/retour_refresh_cron.php";
	

	$postData = array(
	'selectGroup' => $selectGroup,
	'retourRefesh' => $retour,
	'idstagiaire' => $_POST['idstagiaire']
	);
	echo '<pre>';
	print_r($postData);
	echo '</pre>';
	// echo "url: ".$url_dolibarr."   ".$selectGroup."  ".$retour;

	$ch = curl_init();

	curl_setopt_array($ch, array(
	CURLOPT_URL => $url_dolibarr,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_POST => true,
	CURLOPT_POSTFIELDS => $postData,
	CURLOPT_FOLLOWLOCATION => true
	));

	$output = curl_exec($ch);
	curl_close($ch);
	echo $output;
	
	// echo "<form id='data' action='".$url_dolibarr."' method='post'>";
	// echo "<input type='hidden' name='selectGroup' value='". $selectGroup ."'>";
	// echo "<input type='hidden' name='retourRefesh' value='". $retour ."'>";
	// echo "</form>";
	// echo "<script type='text/javascript'>document.forms['data'].submit();</script>";

// }

?>
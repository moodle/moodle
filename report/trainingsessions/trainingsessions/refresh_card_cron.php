<?php

include_once("../../config.php");

// defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/renderers/htmlrenderers.php');

include_once('lib_rapport.php');

function temps_activite ($id, $course) {

	$logs = use_stats_extract_logs(1514761200, time(), $id, $course);
	$aggregate = use_stats_aggregate_logs($logs, 1514761200, time());
	$weekaggregate = use_stats_aggregate_logs($logs, time() - WEEKSECS, time());



	if (empty($aggregate['sessions'])) {
    $aggregate['sessions'] = array();
	}

	// Get course structure.

	$coursestructure = report_trainingsessions_get_course_structure($course, $items);
	// Time period form.

	$str = '';
	$dataobject = report_trainingsessions_print_html($str, $coursestructure, $aggregate, $done);

	// var_dump($dataobject);
	if (empty($dataobject)) {
	    $dataobject = new stdClass();
	}


	$dataobject->items = $items;
	$dataobject->done = $done;

	if ($dataobject->done > $items) {
	    $dataobject->done = $items;
	}

	// In-activity.

	return @$aggregate['activities'][$course]->elapsed;
}


if( isset($_POST['selectGroup']) )	{
	// echo 'Patientez ...<br />... ca mouline';
	$selectGroup = intval($_POST['selectGroup']);
	$sql_utilisateur =  "SELECT user.id, user.username, groups.courseid, groups.name FROM mdl_user AS user 
                                INNER JOIN mdl_groups_members as groupmember ON user.id = groupmember.userid
                                INNER JOIN mdl_groups AS groups ON groupmember.groupid = groups.id
                                WHERE groups.name REGEXP '(";
	
	$sql_utilisateur .= $selectGroup;
	$sql_utilisateur .= ")$'";
	
	// echo $sql_utilisateur;
	
	$reponse2 = $bdd->prepare($sql_utilisateur);
	$reponse2->execute();

	$retour='';
	while($donnees = $reponse2->fetch())    {
		// if ($_POST['dated'] > 1514761200)
			$temps_eq=temps_activite($donnees['id'], $donnees['courseid']);
		// else $temps_eq=temps_equivalent($donnees['id'], $donnees['courseid']);

		$retour .= $donnees['username'] . "|" . seconds_to_hours($temps_eq) . "|";
	}
	
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
	'retourRefesh' => $retour
	);

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

}

?>
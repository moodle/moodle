<?php
include_once('lib_rapport.php');
include('INC/connexion.php'); 
include_once("../../config.php");

require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/renderers/htmlrenderers.php');
include_once('../trainingsessions/htmlrenderers.php');


function temps_activite ($id, $course) {

	$dated = $_GET['date'];
	$datef = $_GET['datef'];

	$logs = use_stats_extract_logs($dated, $datef, $id, $course);
	$aggregate = use_stats_aggregate_logs($logs, $dated, $datef);
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

 if( isset($_GET['username']) )  {

	$lien_total = "https://formassmat-moodle.fr/report/trainingsessions/etat_temps_liste2.php?doss=".$_GET['doss']."&total=1";

	$codeMoodle = str_replace(' ', '', $_GET['codeMoodle']);
	$separateurs = array(",", ".");
	$codeMoodle = str_replace($separateurs, ';', $codeMoodle);
	$list_codeMoodle = explode(";", $codeMoodle);
	$nb_code = count ($list_codeMoodle);
	
	$username = str_replace(' ', '', $_GET['username']);
	$separateurs = array(",");
	$username = str_replace($separateurs, ';', $username);
	$list_username = explode(";", $username);
	$nb_user = count ($list_username);
	echo '<pre>';
	print_r( $_GET );
	echo '</pre>';
	echo '<pre>';
	print_r( $list_username );
	echo '</pre>';

	$j = 0;
	for ($i=0; $i < $nb_user; $i++)	{
		$sql = "SELECT * FROM mdl_user WHERE username LIKE '".$list_username[$i]."'";
		// echo $sql;
		$req = $bdd->query($sql);
		$row = $req->fetch(PDO::FETCH_ASSOC);
		$useligne = $req->rowCount();
		echo $useligne.'<br>';
		echo $sql.'<br>';
		// echo $row['id'].'<br>';
		$sql1 = "SELECT enrol.courseid FROM mdl_enrol AS enrol INNER JOIN mdl_user_enrolments AS enrolment ON enrolment.enrolid = enrol.id WHERE enrolment.userid = ".$row['id']." AND enrolment.timestart = ".$_GET['date'];
		echo $sql1.'<br>';
		$req1 = $bdd->query($sql1);
		$nbrow = $req1->rowCount();
		// exit;
		if ($nbrow === 0)
		{
			$sql1 = "SELECT enrol.courseid, course.shortname FROM mdl_enrol AS enrol INNER JOIN mdl_user_enrolments AS enrolment ON enrolment.enrolid = enrol.id INNER JOIN mdl_course AS course ON enrol.courseid = course.id WHERE enrolment.userid = ".$row['id'];
			$req1 = $bdd->query($sql1);
			$ligne = $req1->rowCount();
			if ( $ligne != 0 )
			{
				while ($row1 = $req1->fetch(PDO::FETCH_ASSOC))
					{
						for ($m=0; $m < $nb_code; $m++)	{
							if ($row1['shortname'] === $list_codeMoodle[$m])
							{
							$temps_eq=temps_activite($row['id'], $row1['courseid']);		// avant était sur $donnees['groupcourse'] FL 11/02/2020
							echo '<hr />';
							$PreNomStagiaire = $row['lastname'];
							$NomStagiaire = $row['firstname'];
							$tef = seconds_to_hours($temps_eq);
							$RefSession = $_GET['selectGroup'];

							$sql_nom_course = "SELECT shortname FROM `mdl_course` WHERE id = ".$row1['courseid'];
							$requete = $bdd->prepare($sql_nom_course);
							$requete->execute();

							$nom_course = $requete->fetch();
								// if ($temps_eq != 0) {
							$lien_total .= '&id'.$j.'='.$row['id'].'&course'.$j.'='.$row1['courseid'].'&PreNomStagiaire'.$j.'=' . $PreNomStagiaire . '&NomStagiaire'.$j.'=' . $NomStagiaire . '&tef'.$j.'=' . $tef . '&RefSession'.$j.'=' . $RefSession;
							$j++;
							}
						}
					}
			}
		}
		else
		{
			while ($row1 = $req1->fetch(PDO::FETCH_ASSOC))
				{
					$temps_eq=temps_activite($row['id'], $row1['courseid']);		// avant était sur $donnees['groupcourse'] FL 11/02/2020
					echo '<hr />';
					$PreNomStagiaire = $row['lastname'];
					$NomStagiaire = $row['firstname'];
					$tef = seconds_to_hours($temps_eq);
					$RefSession = $_GET['selectGroup'];
					$sql_nom_course = "SELECT shortname FROM `mdl_course` WHERE id = ".$row1['courseid'];
					$requete = $bdd->prepare($sql_nom_course);
					$requete->execute();

					$nom_course = $requete->fetch();
					// if ($temps_eq != 0) {
					$lien_total .= '&id'.$j.'='.$row['id'].'&course'.$j.'='.$row1['courseid'].'&PreNomStagiaire'.$j.'=' . $PreNomStagiaire . '&NomStagiaire'.$j.'=' . $NomStagiaire . '&tef'.$j.'=' . $tef . '&RefSession'.$j.'=' . $RefSession;
					$j++;
				}
		}
	
	}
	$y = 0;
	$str_cal = "";
	while (isset($_GET['cal'.$y])) {
		$str_cal .= "&cal".$y."=".$_GET['cal'.$y];
		$y++;
	}
	// echo $lien_total;
	header("Location: ".$lien_total."&nb=".$j.$str_cal."&date=".$_GET['date']."&datef=".$_GET['datef']);
 } 
else {
 	echo "<h1>Erreur: pas de sessionid</h1>";
 }

?>
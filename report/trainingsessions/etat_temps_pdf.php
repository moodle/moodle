<?php
include_once('lib_rapport.php');
include('INC/connexion.php'); 
include_once("../../config.php");

require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/renderers/htmlrenderers.php');
include_once('../trainingsessions/htmlrenderers.php');


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

 if( isset($_GET['selectGroup']) )  {
 	$array_group = explode(";", $_GET['selectGroup']);
       
	// FRANCK : 21/09/2017 ajout du telephone
    //$sql_utilisateur =  "SELECT user.id, user.username, user.email, user.lastname, user.firstname,  groups.courseid, groups.name FROM mdl_user AS user 
    $sql_utilisateur =  "SELECT user.id, user.username, user.email, user.lastname, user.firstname, user.phone1, groups.courseid, groups.name FROM mdl_user AS user 
                        INNER JOIN mdl_groups_members as groupmember ON user.id = groupmember.userid
                        INNER JOIN mdl_groups AS groups ON groupmember.groupid = groups.id
                        WHERE user.id NOT IN (377, 1159, 1468) AND groups.name REGEXP '(_";

    // 22/11/17 retrait du filtre sur courseid

    for ($i=0; isset($array_group[$i]) ; $i++) { 
        $sql_utilisateur .= $array_group[$i];
        if (isset($array_group[$i+1])) $sql_utilisateur .= '|';
    }

    $sql_utilisateur .= ")$' AND groups.timecreated > 1483225200 GROUP BY user.id ORDER BY groups.name" ; 


    
	$reponse2 = $bdd->prepare($sql_utilisateur);
	
	// 22/11/17 retrait du filtre sur courseid
	//$reponse2->execute(array($courseid));

	$reponse2->execute();

	$lien_total = "etat_temps_liste2.php?total=1";
	$i=0;
	while($donnees = $reponse2->fetch())    {

			$temps_eq=temps_activite($donnees['id'], $donnees['courseid']);
			

			// echo "temps: ".$donnees['id']." ".$donnees['courseid'];
			// exit();
		
			$PreNomStagiaire = $donnees['lastname'];
			$NomStagiaire = $donnees['firstname'];
			$tef = seconds_to_hours($temps_eq);
			// $RefSession = substr($donnees['name'] , -4);
			$t = explode('_', $donnees['name']);
			$RefSession = $t[1];
			
			$sql_nom_course = "SELECT shortname FROM `mdl_course` WHERE id = ".$donnees['courseid'];
			$requete = $bdd->prepare($sql_nom_course);
			$requete->execute();

			$nom_course = $requete->fetch();
			if ($temps_eq != 0) {
				$lien_total .= '&id'.$i.'='.$donnees['id'].'&course'.$i.'='.$donnees['courseid'].'&PreNomStagiaire'.$i.'=' . $PreNomStagiaire . '&NomStagiaire'.$i.'=' . $NomStagiaire . '&tef'.$i.'=' . $tef . '&RefSession'.$i.'=' . $RefSession;
				$i++;
			}
	}

	$y = 0;
	$str_cal = "";

	while (isset($_GET['cal'.$y])) {
		$str_cal .= "&cal".$y."=".$_GET['cal'.$y];
		$y++;
	}
	
	header("Location: ".$lien_total."&nb=".$i.$str_cal);
 } else {
 	echo "<h1>Erreur: pas de sessionid</h1>";
 }

?>
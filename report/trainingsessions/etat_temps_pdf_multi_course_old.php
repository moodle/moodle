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

 if( isset($_GET['selectGroup']) )  {
 	// $array_group = explode(";", $_GET['selectGroup']);
       
	// FRANCK : 21/09/2017 ajout du telephone
    //$sql_utilisateur =  "SELECT user.id, user.username, user.email, user.lastname, user.firstname,  groups.courseid, groups.name FROM mdl_user AS user 
   /* $sql_utilisateur =  "SELECT DISTINCT user.id, user.username, user.email, user.lastname, user.firstname, user.phone1, 
							GROUP_CONCAT(groups.courseid SEPARATOR '#') as list_courses, groups.name 
						
						FROM mdl_user AS user 
                        INNER JOIN mdl_groups_members as groupmember ON user.id = groupmember.userid
                        INNER JOIN mdl_groups AS groups ON groupmember.groupid = groups.id
                        WHERE user.id NOT IN (377, 1159, 1468) AND groups.name REGEXP '(_";

    // 22/11/17 retrait du filtre sur courseid

    for ($i=0; isset($array_group[$i]) ; $i++) { 
        $sql_utilisateur .= $array_group[$i];
        if (isset($array_group[$i+1])) $sql_utilisateur .= '|';
    }

    $sql_utilisateur .= ")$' GROUP BY user.id ORDER BY groups.name" ; 
   */ 
    /*$selectGroup = date('d/m/Y', $_GET['date']).'_'.$_GET['selectGroup'];
    $sql_utilisateur =  "SELECT DISTINCT user.id, user.username, user.lastname, user.firstname, groups.name, groups.courseid as groupcourse, enrol.courseid as enrolcourse FROM mdl_user AS user 
                                INNER JOIN mdl_groups_members as groupmember ON user.id = groupmember.userid
                                INNER JOIN mdl_groups AS groups ON groupmember.groupid = groups.id
                                INNER JOIN mdl_user_enrolments AS enrolm ON user.id = enrolm.userid
                                INNER JOIN mdl_enrol AS enrol ON enrolm.enrolid = enrol.id
                                LEFT JOIN mdl_course AS course ON course.id = groups.courseid
                                WHERE groups.name LIKE '";
	
	$sql_utilisateur .= $selectGroup."' ORDER BY user.id";*/
   // echo $sql_utilisateur;	exit;
	$codeMoodle = str_replace(' ', '', $_GET['codeMoodle']);
	$separateurs = array(",", ".");
	$codeMoodle = str_replace($separateurs, ';', $codeMoodle);
	$list_codeMoodle = explode(";", $codeMoodle);
	$nb_code = count ($list_codeMoodle);
	echo '<pre>';
	print_r( $list_codeMoodle );
	echo '</pre>';
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
	$selectGroup = date('d/m/Y', $_GET['date']).'_'.$_GET['selectGroup'];
	$sql_utilisateur =  "SELECT DISTINCT user.id,user.username, user.lastname, user.firstname, groups.courseid as groupcourse, enrol.courseid as enrolcourse, groups.name FROM mdl_user AS user 
                                INNER JOIN mdl_groups_members as groupmember ON user.id = groupmember.userid
                                INNER JOIN mdl_groups AS groups ON groupmember.groupid = groups.id
                                INNER JOIN mdl_user_enrolments AS enrolm ON user.id = enrolm.userid
                                INNER JOIN mdl_enrol AS enrol ON enrolm.enrolid = enrol.id
                                LEFT JOIN mdl_course AS course ON course.id = groups.courseid
                                WHERE groups.name LIKE '".$selectGroup."'";
	
	$sql_utilisateur .= " AND groups.courseid IN(".$courseid.") AND enrol.courseid IN(".$courseid.") AND groups.courseid = enrol.courseid ORDER BY user.id ";
    $reponse2 = $bdd->prepare($sql_utilisateur);
	
	$reponse2->execute();

	$lien_total = "etat_temps_liste2.php?doss=".$_GET['doss']."&total=1";
	$i=0;
	$row = $reponse2->rowCount();
	// echo $row; exit;
	if ($row === 0) //ajout Rayan gestion des anciens cours 25/03/2020
	{
		$sql = "SELECT DISTINCT user.id,user.username, user.lastname, user.firstname, groups.courseid as groupcourse, enrol.courseid as enrolcourse, groups.name FROM mdl_user AS user 
                                INNER JOIN mdl_groups_members as groupmember ON user.id = groupmember.userid
                                INNER JOIN mdl_groups AS groups ON groupmember.groupid = groups.id
                                INNER JOIN mdl_user_enrolments AS enrolm ON user.id = enrolm.userid
                                INNER JOIN mdl_enrol AS enrol ON enrolm.enrolid = enrol.id
                                LEFT JOIN mdl_course AS course ON course.id = groups.courseid
                                WHERE groups.name LIKE '".$selectGroup."'";
        $sql .= " AND enrolm.timestart = '".$_GET['date']."' AND groups.courseid = enrol.courseid ORDER BY user.id";
       	$reponse2 = $bdd->prepare($sql);
       	$reponse2->execute();
       	$row1 = $reponse2->rowCount();
       	// echo $sql;
       	// exit;
       	while($donnees = $reponse2->fetch() )    {
					$temps_eq=temps_activite($donnees['id'], $donnees['enrolcourse']);		// avant était sur $donnees['groupcourse'] FL 11/02/2020
					var_dump( $donnees['id'], $donnees['groupcourse'], $temps_eq );
					echo '<hr />';
					$PreNomStagiaire = $donnees['lastname'];
					$NomStagiaire = $donnees['firstname'];
					$tef = seconds_to_hours($temps_eq);
					$t = explode('_', $donnees['name']);
					$RefSession = $t[1];

					$sql_nom_course = "SELECT shortname FROM `mdl_course` WHERE id = ".$donnees['enrolcourse'];
					$requete = $bdd->prepare($sql_nom_course);
					$requete->execute();

					$nom_course = $requete->fetch();
					// if ($temps_eq != 0) {
						$lien_total .= '&id'.$i.'='.$donnees['id'].'&course'.$i.'='.$donnees['enrolcourse'].'&PreNomStagiaire'.$i.'=' . $PreNomStagiaire . '&NomStagiaire'.$i.'=' . $NomStagiaire . '&tef'.$i.'=' . $tef . '&RefSession'.$i.'=' . $RefSession;
						$i++;
					// }
				// }
			}
	}
	else
	{
		while($donnees = $reponse2->fetch() )    {
			if ( $row > 1 )
			{
				// if ( $donnees['groupcourse'] === $donnees['enrolcourse'] )	// commentaire FL 11/02/2020
				// {
					$temps_eq=temps_activite($donnees['id'], $donnees['enrolcourse']);		// avant était sur $donnees['groupcourse'] FL 11/02/2020
					var_dump( $donnees['id'], $donnees['groupcourse'], $temps_eq );
					echo '<hr />';
					$PreNomStagiaire = $donnees['lastname'];
					$NomStagiaire = $donnees['firstname'];
					$tef = seconds_to_hours($temps_eq);
					$t = explode('_', $donnees['name']);
					$RefSession = $t[1];

					$sql_nom_course = "SELECT shortname FROM `mdl_course` WHERE id = ".$donnees['enrolcourse'];
					$requete = $bdd->prepare($sql_nom_course);
					$requete->execute();

					$nom_course = $requete->fetch();
					// if ($temps_eq != 0) {
						$lien_total .= '&id'.$i.'='.$donnees['id'].'&course'.$i.'='.$donnees['enrolcourse'].'&PreNomStagiaire'.$i.'=' . $PreNomStagiaire . '&NomStagiaire'.$i.'=' . $NomStagiaire . '&tef'.$i.'=' . $tef . '&RefSession'.$i.'=' . $RefSession;
						$i++;
					// }
				// }
			}

			if ( $row === 1 )
			{
				$temps_eq=temps_activite($donnees['id'], $donnees['enrolcourse']);
				$PreNomStagiaire = $donnees['lastname'];
				$NomStagiaire = $donnees['firstname'];
				$tef = seconds_to_hours($temps_eq);
				// echo $tef; exit;
				$t = explode('_', $donnees['name']);
				$RefSession = $t[1];

				$sql_nom_course = "SELECT shortname FROM `mdl_course` WHERE id = ".$donnees['enrolcourse'];
				$requete = $bdd->prepare($sql_nom_course);
				$requete->execute();

				$nom_course = $requete->fetch();
				$lien_total .= '&id'.$i.'='.$donnees['id'].'&course'.$i.'='.$donnees['enrolcourse'].'&PreNomStagiaire'.$i.'=' . $PreNomStagiaire . '&NomStagiaire'.$i.'=' . $NomStagiaire . '&tef'.$i.'=' . $tef . '&RefSession'.$i.'=' . $RefSession;
						$i++;
						// echo $lien_total; exit;
			}
		}
	}
// exit;
	$y = 0;
	$str_cal = "";

	while (isset($_GET['cal'.$y])) {
		$str_cal .= "&cal".$y."=".$_GET['cal'.$y];
		$y++;
	}
	
	header("Location: ".$lien_total."&nb=".$i.$str_cal."&date=".$_GET['date']);
 } else {
 	echo "<h1>Erreur: pas de sessionid</h1>";
 }

?>
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

	$logs = use_stats_extract_logs($dated, $datef + 7884000, $id, $course);
	$aggregate = use_stats_aggregate_logs($logs, $dated, $datef + 7884000);
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

	return @$aggregate['coursetotal'][$course]->elapsed;
}


if( isset($_GET['username']) )  {

    $lien_total = "etat_temps_liste2.php?doss=".$_GET['doss']."&total=1";
    $code_cours = $_GET['codeMoodle'];
    $separateurs = array(",", ".");
	$codeMoodle = str_replace($separateurs, ';', $code_cours);
	$list_codeMoodle = explode(";", $code_cours);
	$nb_code = count ($list_codeMoodle);

    print_r($list_codeMoodle);
    exit;
    //tableau des username
    $username = str_replace(' ', '', $_GET['username']);
    $separateurs = array(",");
    $username = str_replace($separateurs, ';', $username);
    $list_username = explode(";", $username);
    $nb_user = count ($list_username);
    $j = 0;
    for ($i=0; $i < $nb_user; $i++)	{
        if ( $list_username[$i] != "" )
        {
            $sql = "SELECT * FROM mdl_user WHERE username LIKE '".$list_username[$i]."'";
            $req = $bdd->query($sql);
            $row = $req->fetch(PDO::FETCH_ASSOC);
            $userexistant = $req->rowCount();
            if ( $userexistant != 0 )
            {
                for ($j=0; $j < $nb_code; $j++)	{
                    $course = "SELECT id FROM mdl_course WHERE shortname LIKE '".$list_codeMoodle[$j]."'";
                    $reqcourse = $bdd->query($course);
                    $rowcourse = $reqcourse->fetch(PDO::FETCH_ASSOC);
                    $idcourse = $rowcourse['id'];
                    $temps = temps_activite($row['id'], $idcourse);
                    $PreNomStagiaire = $row['lastname'];
                    $NomStagiaire = $row['firstname'];
                    $tempstotal = seconds_to_hours($temps);
                    $RefSession = $_GET['selectGroup'];
                    $lien_total .= '&id'.$j.'='.$row['id'].'&course'.$j.'='.$idcourse.'&PreNomStagiaire'.$j.'=' . $PreNomStagiaire . '&NomStagiaire'.$j.'=' . $NomStagiaire . '&tef'.$j.'=' . $tempstotal . '&RefSession'.$j.'=' . $RefSession;
                }
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
    
    header("Location: ".$lien_total."&nb=".$j.$str_cal."&date=".$_GET['date']."&datef=".$_GET['datef']);
}

else
{
    exit('Pas de stagiaire');
}

?>
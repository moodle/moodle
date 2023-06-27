<?php
include_once("../../config.php");
// defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/use_stats/locallib.php');
require_once($CFG->dirroot . '/report/trainingsessions/locallib.php');
require_once($CFG->dirroot . '/report/trainingsessions/renderers/htmlrenderers.php');

include_once('lib_rapport.php');

function temps_activite ($id, $course) {
	
	$dated = strtotime($_POST['dated']);
	$datef = strtotime($_POST['datef']);

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

$code_cours = $_POST['codeMoodle'];
$username = $_POST['username'];
$idsession = $_POST['selectGroup'];
$idstagiaire = $_POST['idstagiaire'];

$sql = "SELECT * FROM mdl_user WHERE username LIKE '" . $username . "'";
$req = $bdd->query($sql);
$row = $req->fetch(PDO::FETCH_ASSOC);
if ( $username )
{
    if ( strstr($code_cours, 'ALTER') || strstr($code_cours, 'EXCEL') || strstr($code_cours, 'WORD') || strstr($code_cours, 'BUREAUTIQUE') )
    {
        $array_header = array(
            'Content-Type: application/json',
            'token:eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhYmFzZSI6ImFsdGVyY2FtcHVzdjRmb3JtYXNzbWF0IiwiaWF0IjoxNTgyODIzNTcwfQ.JQPK9EA3OjDx-TOO80NukUbyP-ljGadZwTUrq02shYo'
        );

        $url = "https://www.altercampus.fr/nj/users/getAllInList";
        $postData = array(
            "identifiant" => $username
        );
        $jsondata = json_encode($postData);

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $array_header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsondata,
            CURLOPT_FOLLOWLOCATION => true
        ));
        $output = curl_exec($ch);
        $output = json_decode($output, true);

        $idUtilisateur = $output[0]['idUtilisateur'];
        $url = "https://www.altercampus.fr/nj/resultats/autoFormationDetailsUtilisateur";
        $postData = array(
            "idUtilisateur" => $idUtilisateur
        );
        $jsondata = json_encode($postData);

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $array_header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsondata,
            CURLOPT_FOLLOWLOCATION => true
        ));
        $output = curl_exec($ch);
        $output = json_decode($output, true);
        if ( $output['resultat'][0]['tempsPasseTotal'] == '' ) $tempstotal = '00:00'; 
        else $tempstotal = str_replace('h', ':', $output['resultat'][0]['tempsPasseTotal']);
        $retour .= $username.';'.$tempstotal.':00|';
    }
    else
    {
        $sql = "SELECT * FROM mdl_user WHERE username LIKE '".$username."'";
        $req = $bdd->query($sql);
        $row = $req->fetch(PDO::FETCH_ASSOC);
        $userexistant = $req->rowCount();
        if ( $userexistant != 0 )
        {
            $course = "SELECT id FROM mdl_course WHERE shortname LIKE '".$code_cours."'";
            $reqcourse = $bdd->query($course);
            $rowcourse = $reqcourse->fetch(PDO::FETCH_ASSOC);
            $idcourse = $rowcourse['id'];
            $temps = seconds_to_hours(temps_activite($row['id'], $idcourse));
            $retour .= $username.';'.$temps.'|';
        }
    }

    $refresh_doli = 'http://usein.dolibarrgestion.fr/devinfans/tempsMoodleSession/retour_refresh_cron2.php';

    $postDataDoli = array ( 
        'selectGroup' => $idsession,
        'idstagiaire' => $idstagiaire,
        'retourRefesh' => $retour
    );

    $ch = curl_init();

    curl_setopt_array($ch, array(
        CURLOPT_URL => $refresh_doli,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postDataDoli,
        CURLOPT_FOLLOWLOCATION => true
    ));

    $output = curl_exec($ch);
    
    curl_close($ch);
    
    echo $output;
}
$retour = str_replace(NULL, '', $retour);
echo $retour;
?>
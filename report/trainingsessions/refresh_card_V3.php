<?php
include_once("../../config.php");


require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/renderers/htmlrenderers.php');

include_once('lib_rapport.php');

function temps_activite ($id, $course) {
	
	$dated = $_POST['dated'];
	$datef = $_POST['datef'];

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

$code_cours = $_POST['moodleStagiaire'];

//tableau des username
$username = str_replace(' ', '', $_POST['username']);
$separateurs = array(",");
$username = str_replace($separateurs, ';', $username);
$list_username = explode(";", $username);
$nb_user = count ($list_username);
$codeMoodle = str_replace(' ', '', $_POST['moodleStagiaire']);
$separateurs = array(",", ".");
$codeMoodle = str_replace($separateurs, ';', $codeMoodle);
$list_codeMoodle = explode(";", $codeMoodle);
//Parcours du tableau username

for ($i=0; $i<$nb_user; $i++)
{
    //Si le cours est sur altercampus
    if ( strstr($code_cours, 'ALTER') || strstr($code_cours, 'EXCEL') || strstr($code_cours, 'WORD') || strstr($code_cours, 'BUREAUTIQUE') )
    {
        $array_header = array(
            'Content-Type: application/json',
            'token:eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhYmFzZSI6ImFsdGVyY2FtcHVzdjRmb3JtYXNzbWF0IiwiaWF0IjoxNTgyODIzNTcwfQ.JQPK9EA3OjDx-TOO80NukUbyP-ljGadZwTUrq02shYo'
        );
    
        $url = "https://www.altercampus.fr/nj/users/getAllInList";
        $postData = array(
            "identifiant" => $list_username[$i]
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
        $tempstotal = str_replace('h', ':', $output['resultat'][0]['tempsPasseTotal']);
        $retour .= $list_username[$i].';'.$tempstotal.':00|';
    }
    else
    {
        $sql = "SELECT * FROM mdl_user WHERE username LIKE '".$list_username[$i]."'";
        $req = $bdd->query($sql);
        $row = $req->fetch(PDO::FETCH_ASSOC);
        $userexistant = $req->rowCount();
        if ( $userexistant != 0 )
        {
            for ($j=0; $j<count($list_codeMoodle); $j++)
            {
                $course = "SELECT id FROM mdl_course WHERE shortname LIKE '".$list_codeMoodle[$j]."'";
                $reqcourse = $bdd->query($course);
                $rowcourse = $reqcourse->fetch(PDO::FETCH_ASSOC);
                $idcourse = $rowcourse['id'];
                $temps = temps_activite($row['id'], $idcourse);
                $retour .= $list_username[$i].';'.seconds_to_hours($temps).'|';   
            }
        }
    }
}
echo $retour;
$url_dolibarr="http://usein.dolibarrgestion.fr/devinfans/tempsMoodleSession/";
if (count($list_codeMoodle) > 1 ) $url_dolibarr .= "retour_refresh.php";
else $url_dolibarr .= "retour_refresh2.php";
echo "<form id='data' action='".$url_dolibarr."' method='post'>";
echo "<input type='hidden' name='selectGroup' value='". $_POST['selectGroup'] ."'>";
echo "<input type='hidden' name='retourRefesh' value='". $retour ."'>";
echo "</form>";
echo "<script type='text/javascript'>document.forms['data'].submit();</script>";
?>
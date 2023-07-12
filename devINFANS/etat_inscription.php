<?php

include('connexion.php');

$login = $_POST['login'];
$group = $_POST['group'];
$course = $_POST['course'];


// userid + course id -> enrol id -> date -> group id 

$sql = "SELECT ue.timestart AS start, ue.timeend AS endt FROM mdl_user_enrolments AS ue INNER JOIN mdl_user AS u ON u.id = ue.userid INNER JOIN mdl_enrol AS e ON ue.enrolid = e.id INNER JOIN mdl_course AS c ON c.id = e.courseid WHERE c.shortname = '".$course."' AND u.username = '".$login."'";


$reponse2 = $bdd->prepare($sql);
$reponse2->execute();


$retour='';

$donnees = $reponse2->fetch();
	$dated=$donnees['start'];
	$datef=$donnees['endt'];
	$retour .= $dated . "|" . $datef . "|";


echo $retour;

?>
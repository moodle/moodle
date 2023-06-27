<?php
include('header_api.php');
// var_dump($bdd);
// echo '<pre>';
// print_r($_POST);
// echo '</pre>';

	if( isset($_POST['demande']) && isset($_POST['loginFOAD']) && $_POST['demande'] == 'enrolement' )	{
		$sql = "SELECT ue.enrolid, e.courseid
				FROM mdl_user as u
				LEFT JOIN mdl_user_enrolments as ue ON ue.userid = u.id
				LEFT JOIN mdl_enrol as e on e.id = ue.enrolid
				WHERE u.username = '" . $_POST['loginFOAD'] . "'
				AND timestart < UNIX_TIMESTAMP() AND timeend > UNIX_TIMESTAMP()";
		$req = $bdd->query($sql);
		// var_dump($req);
		$data = $req->fetch(PDO::FETCH_ASSOC);
		if($data)	{
			echo json_encode($data); //['enrolid'] . " ??? ";
		}
		else {
			echo 'Pb d\'enrolement';
		}
		exit;
	}
	else exit('lol');
// echo json_encode($req);

?>
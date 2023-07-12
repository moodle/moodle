<?php
include('../report/trainingsessions/INC/connexion.php');
require_once('../config.php');
require_once('../course/lib.php');
// require_once('../filelib.php');


$sql = "SELECT enrolments.userid, enrol.courseid FROM mdl_user_enrolments AS enrolments LEFT JOIN mdl_enrol AS enrol ON enrolments.enrolid = enrol.id WHERE enrolments.userid NOT IN (2,3,8803,125) AND enrol.courseid IS NOT NULL";
$req = $bdd->query($sql);
$i = 0;
while ($data = $req->fetch(PDO::FETCH_ASSOC))
	{
		$context = context_course::instance($data['courseid']);
		echo "Context id: ".$context->id." Courseid : ".$data['courseid']."<br>";
		$sqlrole = "SELECT * FROM mdl_role_assignments WHERE userid = ".$data['userid']." AND contextid = ".$context->id;
		$reqrole = $bdd->query($sqlrole);
		$nbligne = $reqrole->rowCount();
		if ($nbligne === 0)
		{
			$sql = "INSERT INTO mdl_role_assignments VALUES ('', 5, ".$context->id.", ".$data['userid'].", ".time().", 2, '', 0, 0)";
			$bdd->query($sql);
			echo $sql.'<br><br>';
			$i++;
		}	
	}
// echo $i;
exit;
?>
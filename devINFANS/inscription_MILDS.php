<?php
set_include_path('/var/www/html/user');
require_once('../config.php');
require_once($CFG->libdir.'/gdlib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/editadvanced_form.php');
require_once($CFG->dirroot.'/user/editlib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/enrol/manual/locallib.php');
$lien = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname) or die("echec de la connection mysql");
$lien->set_charset("latin1");


// $course = "SELECT enrol.id, groups.id as idgroup FROM mdl_enrol as enrol INNER JOIN mdl_course AS course ON course.id = enrol.courseid INNER JOIN mdl_groups AS groups ON groups.courseid = course.id WHERE course.shortname LIKE '".$_POST['course']."' AND groups.name LIKE 'LSF' AND enrol.enrol LIKE 'manual'";


// $courseresult = $lien->query($course);
// if ($courseresult->num_rows > 0)	{

//     $datacourse = $courseresult->fetch_assoc();
//     $idenrol = $datacourse['id'];
//     $idgroup = $datacourse['idgroup'];
//     echo 'enrol : '.$idenrol.' et groupe : '.$idgroup;
// }

$user = "SELECT * FROM mdl_user WHERE username LIKE '".$_POST['iduser']."'";
$result = $lien->query($user);
if ($result->num_rows == 0)
{
    echo $_POST['iduser'].'<br>';
    exit('user non inscrit moodle');
}
if ($result->num_rows > 0)	{

    $datas = $result->fetch_assoc();
    $iduser = $datas['id'];
    echo 'le userid : '.$iduser;
}
$sql = "INSERT INTO mdl_role_assignments VALUES('', 5, 21870, ".$iduser.", ".time().", 0, '', 0, 0)";
$lien->query($sql);

// $enrol = "DELETE FROM mdl_user_enrolments WHERE userid = ".$iduser." AND timestart = ".$_POST['dated'];
// $enrolresult = $lien->query($enrol);
// if (!$enrolresult)	{
//     $message  = 'Requete invalide : ';
//     $message .= 'Requete complete : ' . $enrol;
//     die($message);
// }

// $insertenrol = "INSERT INTO mdl_user_enrolments (status, enrolid, userid, timestart, timeend, timecreated, timemodified) VALUES(0, ".$idenrol.", ".$iduser.", ".$_POST['dated'].", ".$_POST['datef'].", ".time().", ".time().")";
// $lien->query($insertenrol);
// echo 'la requete enrol : '.$insertenrol;
// $group = "INSERT INTO mdl_groups_members VALUES ('', $idgroup, ".$iduser.", ".time().", '', 0)";
// $lien->query($group);
// echo ' la requete group : '.$group.'<br>';


?>
<?php


// $mysqli = new mysqli('localhost', 'root', 'jessica01', 'moodle');
include('../report/trainingsessions/INC/connexion.php');

$course = $_POST['course'];
$user = $_POST['user'];
$session = $_POST['session'];
$stag = $_POST['stag'];

$course = str_replace(' ', '', $course);
$separateurs = array(",", ".");
$couse = str_replace($separateurs, ';', $course);
$course = explode(";", $course);
$nb_code = count ($course);
$note_global = array();
// echo $nb_code.'<br>';
for ($i=0; $i < $nb_code; $i++)
{
//on cherche le course_id
$sql_course = "SELECT id FROM mdl_course WHERE shortname = '".$course[$i]."'";
// echo $sql_course;

$res_course = $bdd->query($sql_course);
// exit;

if( $res_course->rowCount() == 0) {
	exit('<br>Course id introuvable, contacter l"informatique');
}

$row_course = $res_course->fetch(PDO::FETCH_ASSOC);


$course_mdl = $row_course['id'];
// echo '<br>:'.$course_mdl;
//on cherche le user_id
$sql_user = "SELECT id FROM mdl_user WHERE username = '".$user."'";
$res_user = $bdd->query($sql_user);
$row_user = $res_user->fetch(PDO::FETCH_ASSOC);
if( !$row_user ) {
	exit('User id introuvable, contacter l"informatique');
}
$user_mdl = $row_user['id'];
// echo '<br>'.$user_mdl;
//on cherche le feedback id
$sql_fbk = "SELECT id FROM mdl_feedback WHERE course = '".$course_mdl."' ORDER BY id DESC LIMIT 1";
// exit($sql_fbk);
$res_fbk = $bdd->query($sql_fbk);
$row_fbk = $res_fbk->fetch(PDO::FETCH_ASSOC);

if( !$row_fbk ) {
	exit('Feedback id introuvable, contacter l"informatique');
}
$feed_id = $row_fbk['id'];
// exit( 'feed_i<br />' . $feed_id );
//on cherche le feedback_completed id
$sql_cpl = "SELECT id FROM mdl_feedback_completed WHERE userid = '".$user_mdl."' AND feedback = ".$feed_id;
// echo $sql_cpl; exit;
$res_cpl = $bdd->query($sql_cpl);
$row_cpl = $res_cpl->fetch(PDO::FETCH_ASSOC);
if( !$row_cpl ) {
	echo '<br>L"utilisateur n"a pas encore répondu au questionaire.';
}

else 
{
$cpl_id = $row_cpl['id'];
}

}
//on cherche les items 1 à 6
$sql_item = "SELECT id FROM mdl_feedback_item WHERE feedback = ".$feed_id. " AND position != 8 ORDER BY position";
// echo '<br>'.$sql_item; exit;
$res_item = $bdd->query($sql_item);
// if( mysqli_num_rows($res_item) == 0) {
	// print('Erreur item_id, contacter l"informatique');
// }
$items = array();
$i = 1;
while ($row_item = $res_item->fetch(PDO::FETCH_ASSOC)) {
	$items[$i] = $row_item['id'];
	$i++;
}

//on recupère les résultats
$notes = array();
$i = 1;
if(empty($cpl_id))
{
	$cpl_id = '""';
}

echo '<pre>';
print_r( $items );
echo '</pre>';

foreach ($items as $item) {
	$sql_value = "SELECT value FROM mdl_feedback_value WHERE completed = ".$cpl_id." AND item = ".$item ;
// exit($sql_value);
	$res_value = $bdd->query($sql_value);
	// if( mysqli_num_rows($res_value) == 0) {
		// var_dump($notes);
		// echo $sql_value;
		// print('Erreur value_id, contacter l"informatique');
	// }
	$row_value = $res_value->fetch(PDO::FETCH_ASSOC);
	$notes[$i] = $row_value['value'];
	$i++;
}

$i = 1;
$url = 'http://usein.dolibarrgestion.fr/devinfans/SuiviFOAD/refresh_notes_retour.php?s='.$session."&stag=".$stag;
$notes[7] = urlencode($notes[7]);
// exit();
foreach ($notes as $note) {
	$url .= "&n".$i."=".$note;
	$i++;
}
echo '<br>'.$url;
header("Location: ".$url);
// exit();

?>

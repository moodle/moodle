<?php
require('../../config.php');
require_login();
// Read the JSON file 
$json = file_get_contents($CFG->dirroot.'/clslink/school1.json');
  
// Decode the JSON file
$json_data = json_decode($json,true);
$qbclasses = $json_data["classes"];
$class_id = "1095101";
$ckey = array_search($class_id, array_column($qbclasses, 'sourcedId'));
$course = [];
if($ckey!==false){
	$course = $qbclasses[$ckey];
}


$clsname = 'page-editor-qubits';
$PAGE->add_body_class($clsname);
echo $OUTPUT->header();
echo "<pre>";
print_r($json_data);
print_r($course["qubitscourses"][0]);
echo "</pre>";
?>

<?php

echo $OUTPUT->footer();

?>
<?php
/**********************************************
 * Sets up an automatic grouping 
 **********************************************/
 
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo '<groupsresponse>';


require_once('../../config.php');
require_once('../lib/lib.php');

require_login($courseid);

groups_seed_random_number_generator();

$courseid     = required_param('courseid', PARAM_INT);

$noofstudents   = required_param('noofstudents', PARAM_INT);
$noofgroups     = required_param('noofgroups', PARAM_INT);
$distribevenly  = required_param('distribevenly');
$alphabetical   = required_param('alphabetical');
$generationtype = required_param('generationtype');

$groupingsettings->name =required_param('name');
$groupingsettings->description = required_param('description');
$groupingsettings->prefix = required_param('prefix');
$groupingsettings->defaultgroupdescription = required_param('defaultgroupdescription');
	
if (confirm_sesskey() and isteacheredit($courseid)) {
	if ($generationtype == 'nogroups') {
		$noofstudents = false;
	}
	
	$groupingid = groups_create_automatic_grouping($courseid, $noofstudents, $noofgroups, 
	                                               $distribevenly, $groupingsettings, false, $alphabetical); 
	if (!$groupingid) {
		echo '<error>Failed to create grouping</error>';
	} else {
		echo '<groupingid>'.$groupingid.'</groupingid>';
	}
}

echo '</groupsresponse>';
?>
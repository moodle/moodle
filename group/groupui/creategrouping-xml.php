<?php
/**********************************************
 * Adds a new grouping
 **********************************************/
 
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo '<groupsresponse>';

require_once('../../config.php');
require_once('../lib/lib.php');

$courseid= required_param('courseid', PARAM_INT);

$groupingsettings->name =required_param('groupingname');
$groupingsettings->description = required_param('description');

require_login($courseid);

if (confirm_sesskey() and isteacheredit($courseid)) {
	$groupingid = groups_create_grouping($courseid, $groupingsettings);
	
	if (!$groupingid) {
		echo '<error>Failed to create grouping</error>';
	} else {
		echo '<groupingid>'.$groupingid.'</groupingid>';
	}
}

echo '</groupsresponse>';
?>
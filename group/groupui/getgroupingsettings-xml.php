<?php
/**********************************************
 * Fetches the settings of a grouping and returns 
 * them in an XML format
 **********************************************/
 
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo '<groupsresponse>';

require_once('../../config.php');
require_once('../lib/lib.php');


$courseid = required_param('courseid', PARAM_INT);
$groupingid = required_param('groupingid', PARAM_INT);

require_login($courseid);
	
if (confirm_sesskey() and isteacheredit($courseid)) {
	$groupingsettings = groups_get_grouping_settings($groupingid);
	echo '<name>'.$groupingsettings->name.'</name>';
	echo '<description>'.$groupingsettings->description.'</description>';
	
}

echo '</groupsresponse>';
?>
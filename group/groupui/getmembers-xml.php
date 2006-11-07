<?php
/**********************************************
 * Gets the members of a group and returns them
 * in an XMl format
 **********************************************/
 
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo '<groupsresponse>';

include_once("../../../config.php");
include_once("../lib/lib.php");


$groupid = required_param('groupid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

require_login($courseid);
	
if (confirm_sesskey() and isteacheredit($courseid)) {

	$userids = groups_get_members($groupid);
	
	if ($userids != false) {		
		// Put the groupings into a hash and sort them
		foreach($userids as $userid) {
		    $listmembers[$userid] = groups_get_user_displayname($userid, $courseid);       
		}
		natcasesort($listmembers);
		
		
		// Print out the XML 
		
		echo "<option>";
		foreach($listmembers as $value=>$name) {
			echo "<name>$name</name>";
			echo "<value>$value</value>";
		}
		echo "</option>";
	}
}

echo '</groupsresponse>';
?>

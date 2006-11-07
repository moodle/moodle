<?php
include_once("../../../config.php");
include("../lib/lib.php");

$courseid     = required_param('courseid', PARAM_INT);
$groupingid = required_param('groupingid', PARAM_INT);


require_login($courseid);

// confirm_sesskey checks that this is a POST request	
if (isteacheredit($courseid)) {
	
	// Print the page and form
	$strgroups = get_string('groups');
	$strparticipants = get_string('participants');
	print_header("$course->shortname: $strgroups", "$course->fullname", 
	             "<a href=\"$CFG->wwwroot/course/view.php?id=$courseid\">$course->shortname</a> ".
	             "-> <a href=\"$CFG->wwwroot/user/index.php?id=$courseid\">$strparticipants</a> ".
	             "-> <a href=\"$CFG->wwwroot/course/groups/groupui/index.php?id=$courseid\">$strgroups</a>".
	             "-> Display grouping", "", "", true, '', user_login_string($course, $USER));
	
	$groupingsettings = groups_get_grouping_settings($groupingid);
	
	// Print the name of the grouping
	$name = $groupingsettings->name;
	print("<h1>$name</h1>");
	
	// Get the groups and group members for the grouping
	$groupids = groups_get_groups_in_grouping($groupingid);
	
	if ($groupids != false) {
		
		// Make sure the groups are in the right order 
		foreach($groupids as $groupid) {
		    $listgroups[$groupid] = groups_get_group_displayname($groupid);  
		}
		
		natcasesort($listgroups);
	
		// Go through each group in turn and print the group name and then the members	
		foreach($listgroups as $groupid=>$groupname) {
			print "<h2>$groupname</h2>";
			$userids = groups_get_members($groupid);
			if ($userids != false) {
				// Make sure the users are in the right order
				unset($listmembers);
				foreach($userids as $userid) {
			    	$listmembers[$userid] = groups_get_user_displayname($userid, $courseid);       
				}
				natcasesort($listmembers);
					
				foreach($listmembers as $userid=>$name) {
					print("$name<br>");
				}
			}
		}
	}
	
	print_footer($course);
	
}

?>

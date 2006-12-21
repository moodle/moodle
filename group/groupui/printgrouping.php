<?php
/**
 * Print groups in groupings, and members of groups.
 *
 * @copyright &copy; 2006 The Open University
 * @author J.White AT open.ac.uk 
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */
require_once('../../config.php');
require_once('../lib.php');

$courseid   = required_param('courseid', PARAM_INT);
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
	             "-> <a href=\"$CFG->wwwroot/group/groupui/index.php?id=$courseid\">$strgroups</a>".
	             "-> Display grouping", "", "", true, '', user_login_string($course, $USER));

	$groupingsettings = groups_get_grouping_settings($groupingid);

    if (! isset($groupingsettings->name)) {
        print_error('errorinvalidgrouping', 'group', groups_home_url($courseid));
    } else {
       // Print the name of the grouping
	   $name = $groupingsettings->name;
	   echo "<h1>$name</h1>\n";
    }

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
			echo "<h2>$groupname</h2>\n";
			$userids = groups_get_members($groupid);
			if ($userids != false) {
				// Make sure the users are in the right order
				unset($listmembers);
				foreach($userids as $userid) {
			    	$listmembers[$userid] = groups_get_user_displayname($userid, $courseid);       
				}
				natcasesort($listmembers);

                echo "<ol>\n";
				foreach($listmembers as $userid=>$name) {
				    echo "<li>$name</li>\n";
				}
                echo "</ol>\n";
			}
		}
	}

    print_footer($course);
}

?>
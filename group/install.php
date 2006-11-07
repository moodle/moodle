<?php
/**
 * Functions required for setting up the database to use the new groups.
 *
 * @copyright &copy; 2006 The Open University
 * @author J.White AT open.ac.uk
 * @author N.D.Freear@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */
require_once('../config.php');
require_once($CFG->dirroot.'/group/db/dbsetup.php');
require_once($CFG->dirroot.'/group/lib/utillib.php');

// Set up the database 
groups_create_database_tables();


// Change database tables - course table need to remove two fields add groupingid field
// Move everything over
// Course module instance need to add groupingid field
// Module table - add group support field. 
// Add deletable by teacher field. 

/**
 * Copies the moodle groups to a new grouping within IMS groups
 * @param int $courseid The id of the course
 * @return int The id of the grouping to which the groups have been copied, or false if an error occurred. 
 */
function groups_copy_moodle_groups_to_groups($courseid) {
    $groupingsettings = new Object();
	$groupingsettings->name = 'Old moodle groups';
	$groupingid = groups_create_grouping($courseid, $groupingsettings);

	$groupids = groups_get_moodle_groups($courseid);
	if (!$groupids) {
		$groupingid = false;
	} else {
		
		foreach($groupids as $groupid) {
			$groupcopied = groups_db_copy_moodle_group_to_imsgroup($groupid, $courseid);
			if (!$groupcopied) {
				$groupingid = false;
			}
			
			$groupadded = groups_add_group_to_grouping($groupid, $groupingid);
			if (!$groupadded) {
				$groupingid = false;
			}
		}
	}
	
	return $groupingid;
}	

?>
<?php
/**
 * A grouping is a set of groups that belong to a course. 
 * There may be any number of groupings for a course and a group may
 * belong to more than one grouping.
 *
 * @copyright &copy; 2006 The Open University
 * @author J.White AT open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */
require_once($CFG->dirroot.'/group/lib/basicgrouplib.php');
require_once($CFG->dirroot.'/group/db/dbgroupinglib.php');

define('GROUP_NOT_IN_GROUPING', -1);
define('GROUP_ANY_GROUPING',     0);


/**
 * Gets the information about a specified grouping
 * @param int $groupingid
 * @return object The grouping settings object - properties are name and 
 * description. 
 */
function groups_get_grouping_settings($groupingid) {
    error('missing');
}

/**
 * Gets a list of the groups not in any grouping, but in this course.
 * TODO: move to dbgroupinglib.php
 * @param $courseid If null or false, returns groupids 'not in a grouping sitewide'.
 * @return array An array of group IDs.
 */
function groups_get_groups_not_in_any_grouping($courseid) {
    global $CFG;

    $join = '';
    $where= '';
    if ($courseid) {
        $where= "AND g.courseid = '$courseid'";
    }
    $sql = "SELECT g.id
        FROM {$CFG->prefix}groups g
        $join
        WHERE g.id NOT IN 
        (SELECT groupid FROM {$CFG->prefix}groupings_groups)
        $where";

    $records = get_records_sql($sql);
    $groupids = groups_groups_to_groupids($records, $courseid);

    return $groupids;
}


?>

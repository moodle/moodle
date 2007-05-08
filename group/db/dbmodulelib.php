<?php
/*******************************************************************************
 * modulelib.php
 * 
 * This file contains functions to be used by modules to support groups. More
 * documentation is available on the Developer's Notes section of the Moodle 
 * wiki. 
 * 
 * For queries, suggestions for improvements etc. please post on the Groups 
 * forum on the moodle.org site.
 ******************************************************************************/
// @@@ Lots TO DO in this file


/**
 * Gets the groupingid for a particular course module instance returning
 * false if it is null. 
 * @param int  $cmid The id of the module instance.
 * @return int The grouping id (or false if it is null or an error occurred)
 */
function groups___db_m_get_groupingid($cmid) {
    // @@@ Check nulls are turned into false
    $query = get_record('course_modules', 'groupingid', $userid);
    return $query;
}

/**
 * Gets the groupingid for a particular course module instance 
 */
function groups_db_m_set_groupingid($cmid) {
    // @@@ TO DO
}


/**
 * Gets the group object associated with a group id. This group object can be 
 * used to get information such as the name of the group and the file for the 
 * group icon if it exists. (Look at the groups table in the database to see
 * the fields). 
 * @param int $groupid The id of the group
 * @return group The group object 
 */
function groups_db_m_get_group($groupid) {
}

?>

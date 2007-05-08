<?php

// @@@ TO DO Need to write these functions because they are needed by parts of
// the current moodle code where courses are deleted etc. 

/**
 * @param int $courseid If false, removes the user from all groups for all 
 * course
 */
function groups_remove_user_from_all_groups($userid, $courseid) {
    // @@@ TO DO 
}

function groups_remove_all_group_members($courseid, $showfeedback) {
    // @@@ TO DO 
}

function groups_remove_all_groups($courseid, $removemembers, $showfeedback) {
    // @@@ TO DO 
}

/**
 * Cleans up all the groups and groupings in a course (it does this best-effort 
 * i.e. if one deletion fails, it still attempts further deletions).
 * IMPORTANT: Note that if the groups and groupings are used by other courses 
 * somehow, they will still be deleted - it doesn't protect against this. 
 * @param int $courseid The id of the course
 * @return boolean True if the clean up was successful, false otherwise. 
 */
function groups_cleanup_groups($courseid) {
    $success = true;

    // Delete all the groupings 
    $groupings = groups_get_groupings($courseid);
    if ($groupings != false) {
        foreach($groupings as $groupingid) {
            $groupingdeleted = groups_delete_grouping($groupingid);
            if (!$groupingdeleted) {
                $success = false;
            }
        }
    }

    // Delete all the groups
    $groupids = groups_get_groups($courseid);
    if ($groupids != false) {
        foreach($groupids as $groupid) {
            $groupdeleted = groups_delete_group($groupid);

            if (!$groupdeleted) {
                $success = false;
            }
        }
    }

    return $success;
}

?>

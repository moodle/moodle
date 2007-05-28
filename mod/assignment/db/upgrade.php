<?php  //$Id$

// This file keeps track of upgrades to 
// the assignment module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php

function xmldb_assignment_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    if ($result && $oldversion < 2007052700) {
        require_once $CFG->dirroot.'/mod/assignment/lib.php';
        // we do not want grade items for orphaned activities
        $sql = "SELECT a.*, cm.idnumber as cmidnumber, a.course as courseid FROM {$CFG->prefix}assignment a, {$CFG->prefix}course_modules cm, {$CFG->prefix}modules m
                WHERE m.name='assignment' AND m.id=cm.module AND cm.instance=a.id";
        if ($rs = get_recordset_sql($sql)) {
            $db->debug = false;
            if ($rs->RecordCount() > 0) {
                while ($assignment = rs_fetch_next_record($rs)) {
                    $item = grade_get_items($assignment->course, 'grade', 'mod', 'assignment', $assignment->id);
                    if (!empty($item)) {
                        //already converted, it should not happen - probably interrupted upgrade?
                        continue;
                    }
                    $itemid = assignment_base::create_grade_item($assignment);
                    if ($rs2 = get_recordset('assignment_submissions', 'assignment', $assignment->id)) {
                        while ($sub = rs_fetch_next_record($rs2)) {
                            if ($sub->grade != -1 or !empty($sub->submissioncomment)) {
                                if ($sub->grade <0 ) {
                                    $sub->grade = null;
                                }
                                events_trigger('grade_added', array('itemid'=>$itemid, 'gradevalue'=>$sub->grade, 'userid'=>$sub->userid, 'feedback'=>$sub->submissioncomment, 'feedbackformat'=>$sub->format));
                            }
                        }
                        rs_close($rs2);
                    }
                }
            }
            $db->debug = true;
            rs_close($rs);
        }
    }

    return $result;
}

?>

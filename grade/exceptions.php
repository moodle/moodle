<?php  // $Id$

    require_once("../config.php");
    require_once("lib.php");
    
    $id       = required_param('id');              // course id
    $action   = optional_param('action', '');

    if (!$course = get_record('course', 'id', $id)) {
        error('No course ID');
    }

    require_login($course->id);

    require_capability('moodle/course:managegrades', get_context_instance(CONTEXT_MODULE, $id));

    $group = get_current_group($course->id);
    
    print_header("$course->shortname: ".get_string('grades'), $course->fullname, grade_nav($course, $action));

    grade_preferences_menu($action, $course, $group);

    grade_set_uncategorized();


/// We are in editing mode.  First, process any inputs there may be.

    if ($data = data_submitted()) {
        // make sure it is safe to process data
        if (!empty($USER->id)) {
            if (!confirm_sesskey()) {
                error(get_string('confirmsesskeybad', 'error'));
            }
        }
    
        if (!empty($data->nonmembersadd)) {            /// Add people to a grade_item
            if (!empty($data->nonmembers) and !empty($data->grade_itemid)) {
                $grade_itemmodified = false;
                foreach ($data->nonmembers as $userid) {
                    $record->courseid = $course->id;
                    $record->grade_itemid = $data->grade_itemid;
                    $record->userid = $userid;
                    if (!record_exists('grade_exceptions','courseid',$course->id,'grade_itemid',$data->grade_itemid,'userid',$userid)) {
                        if (!insert_record('grade_exceptions', $record)) {
                            notify(get_string('addexceptionerror','grades').$userid.':'.$data->grade_itemid);
                        }
                    }
                    $grade_itemmodified = true;
                }
            }
            $selectedgrade_item = $data->grade_itemid;

        } else if (!empty($data->membersremove)) {     /// Remove selected people from a particular grade_item
            if (!empty($data->members) and !empty($data->grade_itemid)) {
                foreach ($data->members as $userid) {
                    delete_records('grade_exceptions', 'userid', $userid, "grade_itemid", $data->grade_itemid, 'courseid', $course->id);
                }
            }
            $selectedgrade_item = $data->grade_itemid;
        }
    }

/// Calculate data ready to create the editing interface

    $strgradeitemnonmembers = get_string('gradeitemnonmembers','grades');
    $strgradeitemmembersselected = get_string('gradeitemmembersselected','grades');
    $strgradeitemremovemembers = get_string('gradeitemremovemembers','grades');
    $strgradeiteminfomembers = get_string('gradeiteminfomembers','grades');
    $strgradeitemadd = get_string('gradeitemadd','grades');
    $strgradeitemremove = get_string('gradeitemremove','grades');
    $strgradeiteminfo = get_string('gradeiteminfo','grades');
    $strgradeiteminfopeople = get_string('gradeiteminfopeople','grades');
    $strgradeitemrandomassign = get_string('gradeitemrandomassign','grades');
    $strgradeitemaddusers = get_string('gradeitemaddusers','grades');
    $strgradeitems = get_string('gradeitems','grades');
    $courseid = $course->id;
    $listgrade_items = array();
    $listmembers = array();
    $nonmembers = array();
    $grade_items = array();
    $grade_items = get_records('grade_item', 'courseid', $course->id);
    $grade_itemcount = count($grade_items);
    
    // we need to create a multidimensional array keyed by grade_itemid with all_students at each level
    if (isset($grade_items)) {
        foreach($grade_items as $grade_item) {
            $nonmembers[$grade_item->id] = array();
            if ($students = get_course_students($course->id)) {
                foreach ($students as $student) {
                    $nonmembers[$grade_item->id][$student->id] = fullname($student, true);
                }
                unset($students);
            }
        }
    }

    
    if ($grade_items) {
        foreach ($grade_items as $grade_item) {
            $modname = get_record('modules', 'id', $grade_item->modid);
            $itemname = get_record($modname->name, 'id', $grade_item->cminstance, 'course', $course->id);
            $grade_item->name = $itemname->name;
            $countusers = 0;
            $listmembers[$grade_item->id] = array();
            if ($grade_itemexceptions = grade_get_grade_item_exceptions($grade_item->id)) {
                foreach ($grade_itemexceptions as $grade_itemexception) {
                    $listmembers[$grade_item->id][$grade_itemexception->userid] = $nonmembers[$grade_item->id][$grade_itemexception->userid];
                    unset($nonmembers[$grade_item->id][$grade_itemexception->userid]);
                    $countusers++;
                }
            }
            $listgrade_items[$grade_item->id] = strip_tags(format_string($grade_item->name,true))." ($countusers)";
            if (!isset($grade_item->name)) {
                // these were items that have since been deleted
                unset($listgrade_items[$grade_item->id]);
                delete_records('grade_item', 'id', $grade_item->id);
                delete_records('grade_exceptions', 'grade_itemid', $grade_item->id, 'courseid', $course->id);
            }
            natcasesort($listmembers[$grade_item->id]);
            natcasesort($nonmembers[$grade_item->id]);
        }
        natcasesort($listgrade_items);
    }
    
    if (empty($selectedgrade_item)) {    // Choose the first group by default
        $selectedgrade_item = array_shift($temparr = array_keys($listgrade_items));
    }

    include('exceptions.html');

    print_footer($course);
    exit;
    
function grade_get_grade_item_exceptions($id) {
    
    global $CFG, $course;

    $contextlists = get_related_contexts_string(get_context_instance(CONTEXT_COURSE, $course->id));
    
    $sql = "SELECT ge.id, ge.userid 
            FROM {$CFG->prefix}grade_exceptions ge,
                 {$CFG->prefix}role_assignments ra 
            WHERE grade_itemid = $id 
                  AND ge.userid = ra.userid 
                  AND ra.contextid $contextlists";
    
    return get_records_sql($sql);
}

?>

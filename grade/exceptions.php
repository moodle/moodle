<?php

    require_once("../config.php");
    require_once("lib.php");
    
    if (isset($_REQUEST['action']) && !$action) {
        $action = $_REQUEST['action'];    
    }

    require_variable($id);              // course id
    optional_variable($download);
    if (! $course = get_record("course", "id", $id)) {
        error(get_string('errornocourse','grades'));
    }
    
    require_login($course->id);

    if (! $course = get_record("course", "id", $id)) {
        error(get_string('incorrectcourseid', 'grades'));
    }
    
    if (!isset($USER->editing)) {
        $USER->editing = false;
    }

    $editing = false;

    if (isteacheredit($course->id)) {
       if (isset($edit)) {
            if ($edit == "on") {
                $USER->editing = true;
            } else if ($edit == "off") {
                $USER->editing = false;
            }
        }

        $editing = $USER->editing;
    }
    
    require_variable($id);              // course id

    require_login($course->id);

    if (!$course = get_record("course", "id", $id)) {
        error(get_string('incorrectcourseid', 'grades'));
    }
    
    $preferences = grade_get_preferences();
    
    $loggedinas = "<p class=\"logininfo\">".user_login_string($course, $USER)."</p>";
    
    if (isteacher()) {
        if (isset($_REQUEST['student'])) {
            $student = $_REQUEST['student'];
        }
        if (!isset($student)) {
            $student = -1;
        }
    }
    else {
        $student = $USER->id;
    }
    
    $grade_menu = grade_get_grades_menu();
    
    print_header("$course->shortname", "$course->fullname", $grade_menu,"", "", true, grade_preferences_button(), $loggedinas);
    grade_preferences_menu();
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
                    delete_records('grade_exceptions', 'userid', $userid, "grade_itemid", $data->grade_itemid, 'course', $course->id);
                }
            }
            $selectedgrade_item = $data->grade_itemid;
        }
    }
    if (isset($selectedgrade_item)) {
        clean_param($selectedgrade_item, PARAM_CLEAN);
    }

/// Calculate data ready to create the editing interface

    $strgrade_itemnonmembers = get_string('grade_itemnonmembers','grades');
    $strgrade_itemmembersselected = get_string('grade_itemmembersselected','grades');
    $strgrade_itemremovemembers = get_string('grade_itemremovemembers','grades');
    $strgrade_iteminfomembers = get_string('grade_iteminfomembers','grades');
    $strgrade_itemadd = get_string('grade_itemadd','grades');
    $strgrade_itemremove = get_string('grade_itemremove','grades');
    $strgrade_iteminfo = get_string('grade_iteminfo','grades');
    $strgrade_iteminfopeople = get_string('grade_iteminfopeople','grades');
    $strgrade_itemrandomassign = get_string('grade_itemrandomassign','grades');
    $strgrade_itemaddusers = get_string('grade_itemaddusers','grades');
    $strgrade_items = get_string('grade_items','grades');
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
            $listgrade_items[$grade_item->id] = $grade_item->name." ($countusers)";
            if (!isset($grade_item->name)) {
                // these were items that have since been deleted
                unset($listgrade_items[$grade_item->id]);
                delete_records('grade_item', 'id', $grade_item->id);
            }
            natcasesort($listmembers[$grade_item->id]);
            natcasesort($nonmembers[$grade_item->id]);
        }
        natcasesort($listgrade_items);
    }
    
    if (empty($selectedgrade_item)) {    // Choose the first group by default
        $selectedgrade_item = array_shift(array_keys($listgrade_items));
    }

    include('exceptions.html');

    print_footer($course);
    exit;
    
function grade_get_grade_item_exceptions($id) {
    global $CFG;
    global $course;
    
    $sql = "SELECT ge.id, ge.userid FROM {$CFG->prefix}grade_exceptions ge, {$CFG->prefix}user_students us WHERE us.course=$course->id AND grade_itemid=$id AND ge.userid = us.userid AND us.course=ge.courseid";
    $grade_exceptions = get_records_sql($sql);
    return $grade_exceptions;
}

?>

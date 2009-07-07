<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Returns the aggregated or calculated course grade(s) in given course.
 * @public
 * @param int $courseid id of course
 * @param int $userid_or_ids optional id of the graded user or array of ids; if userid not used, returns only information about grade_item
 * @return information about course grade item scaleid, name, grade and locked status, etc. + user grades
 */
function grade_get_course_grades($courseid, $userid_or_ids=null) {

    $grade_item = grade_item::fetch_course_item($courseid);

    if ($grade_item->needsupdate) {
        grade_regrade_final_grades($courseid);
    }

    $item = new object();
    $item->scaleid    = $grade_item->scaleid;
    $item->name       = $grade_item->get_name();
    $item->grademin   = $grade_item->grademin;
    $item->grademax   = $grade_item->grademax;
    $item->gradepass  = $grade_item->gradepass;
    $item->locked     = $grade_item->is_locked();
    $item->hidden     = $grade_item->is_hidden();
    $item->grades     = array();

    switch ($grade_item->gradetype) {
        case GRADE_TYPE_NONE:
            continue;

        case GRADE_TYPE_VALUE:
            $item->scaleid = 0;
            break;

        case GRADE_TYPE_TEXT:
            $item->scaleid   = 0;
            $item->grademin   = 0;
            $item->grademax   = 0;
            $item->gradepass  = 0;
            break;
    }

    if (empty($userid_or_ids)) {
        $userids = array();

    } else if (is_array($userid_or_ids)) {
        $userids = $userid_or_ids;

    } else {
        $userids = array($userid_or_ids);
    }

    if ($userids) {
        $grade_grades = grade_grade::fetch_users_grades($grade_item, $userids, true);
        foreach ($userids as $userid) {
            $grade_grades[$userid]->grade_item =& $grade_item;

            $grade = new object();
            $grade->grade          = $grade_grades[$userid]->finalgrade;
            $grade->locked         = $grade_grades[$userid]->is_locked();
            $grade->hidden         = $grade_grades[$userid]->is_hidden();
            $grade->overridden     = $grade_grades[$userid]->overridden;
            $grade->feedback       = $grade_grades[$userid]->feedback;
            $grade->feedbackformat = $grade_grades[$userid]->feedbackformat;
            $grade->usermodified   = $grade_grades[$userid]->usermodified;
            $grade->dategraded     = $grade_grades[$userid]->get_dategraded();

            // create text representation of grade
            if ($grade_item->needsupdate) {
                $grade->grade          = false;
                $grade->str_grade      = get_string('error');
                $grade->str_long_grade = $grade->str_grade;

            } else if (is_null($grade->grade)) {
                $grade->str_grade      = '-';
                $grade->str_long_grade = $grade->str_grade;

            } else {
                $grade->str_grade = grade_format_gradevalue($grade->grade, $grade_item);
                if ($grade_item->gradetype == GRADE_TYPE_SCALE or $grade_item->get_displaytype() != GRADE_DISPLAY_TYPE_REAL) {
                    $grade->str_long_grade = $grade->str_grade;
                } else {
                    $a = new object();
                    $a->grade = $grade->str_grade;
                    $a->max   = grade_format_gradevalue($grade_item->grademax, $grade_item);
                    $grade->str_long_grade = get_string('gradelong', 'grades', $a);
                }
            }

            // create html representation of feedback
            if (is_null($grade->feedback)) {
                $grade->str_feedback = '';
            } else {
                $grade->str_feedback = format_text($grade->feedback, $grade->feedbackformat);
            }

            $item->grades[$userid] = $grade;
        }
    }

    return $item;
}

/**
 * Returns the aggregated or calculated course grade for the given user(s).
 * @public
 * @param int $userid
 * @param int $courseid optional id of course or array of ids, empty means all uses courses (returns array if not present)
 * @return mixed grade info or grades array including item info, false if error
 */
function grade_get_course_grade($userid, $courseid_or_ids=null) {

    if (!is_array($courseid_or_ids)) {
        if (empty($courseid_or_ids)) {
            if (!$courses = get_my_courses($userid, $sort='visible DESC,sortorder ASC', 'id')) {
                return false;
            }
            $courseids = array_keys($courses);
            return grade_get_course_grade($userid, $courseids);
        }
        if (!is_numeric($courseid_or_ids)) {
            return false;
        }
        if (!$grades = grade_get_course_grade($userid, array($courseid_or_ids))) {
            return false;
        } else {
            // only one grade - not array
            $grade = reset($grades);
            return $grade;
        }
    }

    foreach ($courseid_or_ids as $courseid) {
        $grade_item = grade_item::fetch_course_item($courseid);
        $course_items[$grade_item->courseid] = $grade_item;
    }

    $grades = array();
    foreach ($course_items as $grade_item) {
        if ($grade_item->needsupdate) {
            grade_regrade_final_grades($courseid);
        }

        $item = new object();
        $item->scaleid    = $grade_item->scaleid;
        $item->name       = $grade_item->get_name();
        $item->grademin   = $grade_item->grademin;
        $item->grademax   = $grade_item->grademax;
        $item->gradepass  = $grade_item->gradepass;
        $item->locked     = $grade_item->is_locked();
        $item->hidden     = $grade_item->is_hidden();

        switch ($grade_item->gradetype) {
            case GRADE_TYPE_NONE:
                continue;

            case GRADE_TYPE_VALUE:
                $item->scaleid = 0;
                break;

            case GRADE_TYPE_TEXT:
                $item->scaleid   = 0;
                $item->grademin   = 0;
                $item->grademax   = 0;
                $item->gradepass  = 0;
                break;
        }
        $grade_grade = new grade_grade(array('userid'=>$userid, 'itemid'=>$grade_item->id));
        $grade_grade->grade_item =& $grade_item;

        $grade = new object();
        $grade->grade          = $grade_grade->finalgrade;
        $grade->locked         = $grade_grade->is_locked();
        $grade->hidden         = $grade_grade->is_hidden();
        $grade->overridden     = $grade_grade->overridden;
        $grade->feedback       = $grade_grade->feedback;
        $grade->feedbackformat = $grade_grade->feedbackformat;
        $grade->usermodified   = $grade_grade->usermodified;
        $grade->dategraded     = $grade_grade->get_dategraded();
        $grade->item           = $item;

        // create text representation of grade
        if ($grade_item->needsupdate) {
            $grade->grade          = false;
            $grade->str_grade      = get_string('error');
            $grade->str_long_grade = $grade->str_grade;

        } else if (is_null($grade->grade)) {
            $grade->str_grade      = '-';
            $grade->str_long_grade = $grade->str_grade;

        } else {
            $grade->str_grade = grade_format_gradevalue($grade->grade, $grade_item);
            if ($grade_item->gradetype == GRADE_TYPE_SCALE or $grade_item->get_displaytype() != GRADE_DISPLAY_TYPE_REAL) {
                $grade->str_long_grade = $grade->str_grade;
            } else {
                $a = new object();
                $a->grade = $grade->str_grade;
                $a->max   = grade_format_gradevalue($grade_item->grademax, $grade_item);
                $grade->str_long_grade = get_string('gradelong', 'grades', $a);
            }
        }

        // create html representation of feedback
        if (is_null($grade->feedback)) {
            $grade->str_feedback = '';
        } else {
            $grade->str_feedback = format_text($grade->feedback, $grade->feedbackformat);
        }

        $grades[$grade_item->courseid] = $grade;
    }

    return $grades;
}

/**
 * Returns all grade items (including outcomes) or main item for a given activity identified by $cm object.
 *
 * @param object $cm A course module object (preferably with modname property)
 * @return mixed - array of grade item instances (one if $only_main_item true), false if error or not found
 */
function grade_get_grade_items_for_activity($cm, $only_main_item=false) {
    global $CFG;

    if (!isset($cm->modname)) {
        $cm = get_record_sql("SELECT cm.*, m.name, md.name as modname
                                FROM {$CFG->prefix}course_modules cm,
                                     {$CFG->prefix}modules md,
                               WHERE cm.id = {$cm->id} AND md.id = cm.module");
    }


    if (empty($cm) or empty($cm->instance) or empty($cm->course)) {
        debugging("Incorrect cm parameter in grade_get_grade_items_for_activity()!");
        return false;
    }

    if ($only_main_item) {
        return grade_item::fetch_all(array('itemtype'=>'mod', 'itemmodule'=>$cm->modname, 'iteminstance'=>$cm->instance, 'courseid'=>$cm->course, 'itemnumber'=>0));
    } else {
        return grade_item::fetch_all(array('itemtype'=>'mod', 'itemmodule'=>$cm->modname, 'iteminstance'=>$cm->instance, 'courseid'=>$cm->course));
    }
}

/**
 * Returns whether or not user received grades in main grade item for given activity.
 *
 * @param object $cm
 * @param int $userid
 * @return bool True if graded false if user not graded yet
 */
function grade_is_user_graded_in_activity($cm, $userid) {

    $grade_items = grade_get_grade_items_for_activity($cm, true);
    if (empty($grade_items)) {
        return false;
    }

    $grade_item = reset($grade_items);

    if ($grade_item->gradetype == GRADE_TYPE_NONE) {
        return false;
    }

    if ($grade_item->needsupdate) {
        // activity items should never fail to regrade
        grade_regrade_final_grades($grade_item->courseid);
    }

    if (!$grade = $grade_item->get_final($userid)) {
        return false;
    }

    if (is_null($grade->finalgrade)) {
        return false;
    }

    return true;
}

/**
 * Returns an array of activities (defined as $cm objects) which are gradeable from gradebook, outcomes are ignored.
 *
 * @param int $courseid If provided then restrict to one course.
 * @param string $modulename If defined (could be 'forum', 'assignment' etc) then only that type are returned.
 * @return array $cm objects
 */
function grade_get_gradable_activities($courseid, $modulename='') {
    global $CFG;

    if (empty($modulename)) {
        if (!$modules = get_records('modules', 'visible', '1')) {
            return false;
        }
        $result = array();
        foreach ($modules as $module) {
            if ($cms = grade_get_gradable_activities($courseid, $module->name)) {
                $result =  $result + $cms;
            }
        }
        if (empty($result)) {
            return false;
        } else {
            return $result;
        }
    }

    $sql = "SELECT cm.*, m.name, md.name as modname
              FROM {$CFG->prefix}grade_items gi, {$CFG->prefix}course_modules cm, {$CFG->prefix}modules md, {$CFG->prefix}$modulename m
             WHERE gi.courseid = $courseid AND
                   gi.itemtype = 'mod' AND
                   gi.itemmodule = '$modulename' AND
                   gi.itemnumber = 0 AND
                   gi.gradetype != ".GRADE_TYPE_NONE." AND
                   gi.iteminstance = cm.instance AND
                   cm.instance = m.id AND
                   md.name = '$modulename' AND
                   md.id = cm.module";

    return get_records_sql($sql);
}
?>

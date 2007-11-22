<?php // $Id$

///////////////////////////////////////////////////////////////////////////
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Library of functions for gradebook
 *
 * @author Moodle HQ developers
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

require_once($CFG->libdir . '/grade/constants.php');

require_once($CFG->libdir . '/grade/grade_category.php');
require_once($CFG->libdir . '/grade/grade_item.php');
require_once($CFG->libdir . '/grade/grade_grade.php');
require_once($CFG->libdir . '/grade/grade_scale.php');
require_once($CFG->libdir . '/grade/grade_outcome.php');

/***** PUBLIC GRADE API - only these functions should be used in modules *****/

/**
 * Submit new or update grade; update/create grade_item definition. Grade must have userid specified,
 * rawgrade and feedback with format are optional. rawgrade NULL means 'Not graded', missing property
 * or key means do not change existing.
 *
 * Only following grade item properties can be changed 'itemname', 'idnumber', 'gradetype', 'grademax',
 * 'grademin', 'scaleid', 'multfactor', 'plusfactor', 'deleted' and 'hidden'.
 *
 * Manual, course or category items can not be updated by this function.

 * @param string $source source of the grade such as 'mod/assignment'
 * @param int $courseid id of course
 * @param string $itemtype type of grade item - mod, block
 * @param string $itemmodule more specific then $itemtype - assignment, forum, etc.; maybe NULL for some item types
 * @param int $iteminstance instance it of graded subject
 * @param int $itemnumber most probably 0, modules can use other numbers when having more than one grades for each user
 * @param mixed $grades grade (object, array) or several grades (arrays of arrays or objects), NULL if updating grade_item definition only
 * @param mixed $itemdetails object or array describing the grading item, NULL if no change
 */
function grade_update($source, $courseid, $itemtype, $itemmodule, $iteminstance, $itemnumber, $grades=NULL, $itemdetails=NULL) {
    global $USER;

    // only following grade_item properties can be changed in this function
    $allowed = array('itemname', 'idnumber', 'gradetype', 'grademax', 'grademin', 'scaleid', 'multfactor', 'plusfactor', 'deleted', 'hidden');

    // grade item identification
    $params = compact('courseid', 'itemtype', 'itemmodule', 'iteminstance', 'itemnumber');

    if (is_null($courseid) or is_null($itemtype)) {
        debugging('Missing courseid or itemtype');
        return GRADE_UPDATE_FAILED;
    }

    if (!$grade_items = grade_item::fetch_all($params)) {
        // create a new one
        $grade_item = false;

    } else if (count($grade_items) == 1){
        $grade_item = reset($grade_items);
        unset($grade_items); //release memory

    } else {
        debugging('Found more than one grade item');
        return GRADE_UPDATE_MULTIPLE;
    }

    if (!empty($itemdetails['deleted'])) {
        if ($grade_item) {
            if ($grade_item->delete($source)) {
                return GRADE_UPDATE_OK;
            } else {
                return GRADE_UPDATE_FAILED;
            }
        }
        return GRADE_UPDATE_OK;
    }

/// Create or update the grade_item if needed

    if (!$grade_item) {
        if ($itemdetails) {
            $itemdetails = (array)$itemdetails;

            // grademin and grademax ignored when scale specified
            if (array_key_exists('scaleid', $itemdetails)) {
                if ($itemdetails['scaleid']) {
                    unset($itemdetails['grademin']);
                    unset($itemdetails['grademax']);
                }
            }

            foreach ($itemdetails as $k=>$v) {
                if (!in_array($k, $allowed)) {
                    // ignore it
                    continue;
                }
                if ($k == 'gradetype' and $v == GRADE_TYPE_NONE) {
                    // no grade item needed!
                    return GRADE_UPDATE_OK;
                }
                $params[$k] = $v;
            }
        }
        $grade_item = new grade_item($params);
        $grade_item->insert();

    } else {
        if ($grade_item->is_locked()) {
            $message = get_string('gradeitemislocked', 'grades', $grade_item->itemname);
            notice($message);
            return GRADE_UPDATE_ITEM_LOCKED;
        }

        if ($itemdetails) {
            $itemdetails = (array)$itemdetails;
            $update = false;
            foreach ($itemdetails as $k=>$v) {
                if (!in_array($k, $allowed)) {
                    // ignore it
                    continue;
                }
                if ($grade_item->{$k} != $v) {
                    $grade_item->{$k} = $v;
                    $update = true;
                }
            }
            if ($update) {
                $grade_item->update();
            }
        }
    }

/// Some extra checks
    // do we use grading?
    if ($grade_item->gradetype == GRADE_TYPE_NONE) {
        return GRADE_UPDATE_OK;
    }

    // no grade submitted
    if (empty($grades)) {
        return GRADE_UPDATE_OK;
    }

/// Finally start processing of grades
    if (is_object($grades)) {
        $grades = array($grades);
    } else {
        if (array_key_exists('userid', $grades)) {
            $grades = array($grades);
        }
    }

    $failed = false;
    foreach ($grades as $grade) {
        $grade = (array)$grade;
        if (empty($grade['userid'])) {
            $failed = true;
            debugging('Invalid userid in grade submitted');
            continue;
        } else {
            $userid = $grade['userid'];
        }

        $rawgrade       = false;
        $feedback       = false;
        $feedbackformat = FORMAT_MOODLE;
        $usermodified   = $USER->id;
        $datesubmitted  = null;
        $dategraded     = null;

        if (array_key_exists('rawgrade', $grade)) {
            $rawgrade = $grade['rawgrade'];
        }

        if (array_key_exists('feedback', $grade)) {
            $feedback = $grade['feedback'];
        }

        if (array_key_exists('feedbackformat', $grade)) {
            $feedbackformat = $grade['feedbackformat'];
        }

        if (array_key_exists('usermodified', $grade)) {
            $usermodified = $grade['usermodified'];
        }

        if (array_key_exists('datesubmitted', $grade)) {
            $datesubmitted = $grade['datesubmitted'];
        }

        if (array_key_exists('dategraded', $grade)) {
            $dategraded = $grade['dategraded'];
        }

        // update or insert the grade
        if (!$grade_item->update_raw_grade($userid, $rawgrade, $source, $feedback, $feedbackformat, $usermodified, $dategraded, $datesubmitted)) {
            $failed = true;
        }
    }

    if (!$failed) {
        return GRADE_UPDATE_OK;
    } else {
        return GRADE_UPDATE_FAILED;
    }
}

/**
 * Updates outcomes of user
 * Manual outcomes can not be updated.
 * @param string $source source of the grade such as 'mod/assignment'
 * @param int $courseid id of course
 * @param string $itemtype 'mod', 'block'
 * @param string $itemmodule 'forum, 'quiz', etc.
 * @param int $iteminstance id of the item module
 * @param int $userid ID of the graded user
 * @param array $data array itemnumber=>outcomegrade
 */
function grade_update_outcomes($source, $courseid, $itemtype, $itemmodule, $iteminstance, $userid, $data) {
    if ($items = grade_item::fetch_all(array('itemtype'=>$itemtype, 'itemmodule'=>$itemmodule, 'iteminstance'=>$iteminstance, 'courseid'=>$courseid))) {
        foreach ($items as $item) {
            if (!array_key_exists($item->itemnumber, $data)) {
                continue;
            }
            $grade = $data[$item->itemnumber] < 1 ? null : $data[$item->itemnumber];
            $item->update_final_grade($userid, $grade, $source);
        }
    }
}

/**
 * Returns grading information for given activity - optionally with users grades
 * Manual, course or category items can not be queried.
 * @param int $courseid id of course
 * @param string $itemtype 'mod', 'block'
 * @param string $itemmodule 'forum, 'quiz', etc.
 * @param int $iteminstance id of the item module
 * @param int $userid optional id of the graded user; if userid not used, returns only information about grade_item
 * @return array of grade information objects (scaleid, name, grade and locked status, etc.) indexed with itemnumbers
 */
function grade_get_grades($courseid, $itemtype, $itemmodule, $iteminstance, $userid_or_ids=0) {
    global $CFG;

    $return = new object();
    $return->items    = array();
    $return->outcomes = array();

    $course_item = grade_item::fetch_course_item($courseid);
    $needsupdate = array();
    if ($course_item->needsupdate) {
        $result = grade_regrade_final_grades($courseid);
        if ($result !== true) {
            $needsupdate = array_keys($result);
        }
    }

    if ($grade_items = grade_item::fetch_all(array('itemtype'=>$itemtype, 'itemmodule'=>$itemmodule, 'iteminstance'=>$iteminstance, 'courseid'=>$courseid))) {
        foreach ($grade_items as $grade_item) {
            $decimalpoints = null;

            if (empty($grade_item->outcomeid)) {
                // prepare information about grade item
                $item = new object();
                $item->itemnumber = $grade_item->itemnumber;
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
                        $grade->datesubmitted  = $grade_grades[$userid]->get_datesubmitted();
                        $grade->dategraded     = $grade_grades[$userid]->get_dategraded();

                        // create text representation of grade
                        if (in_array($grade_item->id, $needsupdate)) {
                            $grade->grade     = false;
                            $grade->str_grade = get_string('error');

                        } else if (is_null($grade->grade)) {
                            $grade->str_grade = '-';

                        } else {
                            $grade->str_grade = grade_format_gradevalue($grade->grade, $grade_item);
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
                $return->items[$grade_item->itemnumber] = $item;

            } else {
                if (!$grade_outcome = grade_outcome::fetch(array('id'=>$grade_item->outcomeid))) {
                    debugging('Incorect outcomeid found');
                    continue;
                }

                // outcome info
                $outcome = new object();
                $outcome->itemnumber = $grade_item->itemnumber;
                $outcome->scaleid    = $grade_outcome->scaleid;
                $outcome->name       = $grade_outcome->get_name();
                $outcome->locked     = $grade_item->is_locked();
                $outcome->hidden     = $grade_item->is_hidden();

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
                        $grade->feedback       = $grade_grades[$userid]->feedback;
                        $grade->feedbackformat = $grade_grades[$userid]->feedbackformat;
                        $grade->usermodified   = $grade_grades[$userid]->usermodified;

                        // create text representation of grade
                        if (in_array($grade_item->id, $needsupdate)) {
                            $grade->grade     = false;
                            $grade->str_grade = get_string('error');

                        } else if (is_null($grade->grade)) {
                            $grade->grade = 0;
                            $grade->str_grade = get_string('nooutcome', 'grades');

                        } else {
                            $grade->grade = (int)$grade->grade;
                            $scale = $grade_item->load_scale();
                            $grade->str_grade = format_string($scale->scale_items[(int)$grade->grade-1]);
                        }

                        // create html representation of feedback
                        if (is_null($grade->feedback)) {
                            $grade->str_feedback = '';
                        } else {
                            $grade->str_feedback = format_text($grade->feedback, $grade->feedbackformat);
                        }

                        $outcome->grades[$userid] = $grade;
                    }
                }
                $return->outcomes[$grade_item->itemnumber] = $outcome;

            }
        }
    }

    // sort results using itemnumbers
    ksort($return->items, SORT_NUMERIC);
    ksort($return->outcomes, SORT_NUMERIC);

    return $return;
}

/**
 * Returns whether or not there are any grades yet for the given course module object. A userid can be given to check for a single user's grades.
 *
 * @param object $cm
 * @param int $userid
 * @return bool True if grades are present, false otherwise
 */
function grade_exists($cm, $userid = null) {

    $grade_items = grade_get_grade_items_for_activity($cm);
    $grades_exist = false;

    // Query each grade_item for existing grades
    foreach ($grade_items as $gi) {
        $grades = $gi->get_final($userid);
        $grades_exist = $grades_exist || !empty($grades); // get_final should return false, an empty array or an array of grade_grade objects
    }

    return $grades_exist; 
}

/**
 * For a given activity module $cm object, return the related grade item object (or array of objects if there are more than one, or NULL if there are none).
 *
 * @param object $cm A course module object
 * @return mixed the related grade item object (or array of objects if there are more than one, or NULL if there are none)
 */
function grade_get_grade_items_for_activity($cm) {
    if (!isset($cm->instance) || !isset($cm->courseid)) {
        error("The coursemodule object you gave to grade_exists() isn't set up correctly. Either instance ($cm->instance) or courseid ($cm->courseid) field isn't set.");
    }
    
    // Get grade_item object for this course module (or array of grade_items)
    $grade_items = grade_item::fetch_all(array('iteminstance' => $cm->instance, 'courseid' => $cm->courseid));
    $std_grade_items = array();
    foreach ($grade_items as $key => $gi) {
        $std_grade_items[$key] = $gi->get_record_data();
    }

    if (count($std_grade_items) == 0 || empty($std_grade_items)) {
        return null; 
    } elseif (count($std_grade_items) == 1) {
        return reset($std_grade_items);
    } else {
        return $std_grade_items;
    } 
}

/**
 * Returns an array of activities (defined as $cm objects) for which grade_items are defined. 
 *  
 * @param int $courseid If provided then restrict to one course.
 * @param string $type If defined (could be 'forum', 'assignment' etc) then only that type are returned.
 * @return array $cm objects
 */
function grade_get_grade_activities($courseid = null, $type = null) {
    if ($grade_items = grade_get_grade_items($courseid, $type)) {
        $cms = array();

        foreach ($grade_items as $gi) {
            // Get moduleid
            $moduleid = get_field('modules', 'id', 'name', $gi->itemmodule);
            if ($cm = get_record('course_modules', 'instance', $gi->iteminstance, 'course', $gi->courseid, 'module', $moduleid)) {
                $cms[$cm->id] = $cm;
            }
        }
        return $cms;
    } else {
        return false;
    }
}

/**
 * Returns an array of $gradeitem objects.
 *
 * @param int $courseid If provided then restrict to one course.
 * @param string $type If defined (could be 'forum', 'assignment' etc) then only that type are returned.
 * @return array $gradeitem objects
 */
function grade_get_grade_items($courseid = null, $type = null) {
    // Get list of grade_items for the given course, of the given type
    $params = array();
    if (!empty($courseid)) {
        $params['courseid'] = $courseid;
    }
    if (!empty($type)) {
        $params['itemtype'] = 'mod';
        $params['itemmodule'] = $type;
    }
    $grade_items = $grade_items = grade_item::fetch_all($params);
    $std_grade_items = array();
    foreach ($grade_items as $key => $gi) {
        $std_grade_items[$key] = $gi->get_record_data();
    }
    return $std_grade_items;
} 

/**
 * Returns the float grade for the given user in the given grade_item / column. NULL if it doesn't exist. 
 *
 * @param object $gradeitem A grade_item object (properly instantiated, or plain stdClass)
 * @param object $user A user object or a userid (int)
 * @return float 
 */
function grade_get_user_grade($gradeitem, $userid) {
    if (!method_exists($gradeitem, 'get_final')) {
        $fetch_from_db = empty($gradeitem->id); 
        $gradeitem = new grade_item($gradeitem, $fetch_from_db);
    }
    
    if ($final = $gradeitem->get_final($userid)) {
        return $final->finalgrade;
    } else {
        return null;
    }
} 

/**
 * Returns the course grade(s) for the given user. 
 * If $course is not specified, then return an array of all the course grades for all the courses that user is a part of.
 *
 * @param object $user A user object or a userid (int)
 * @param object $course A course object or a courseid (int)
 * @return mixed Course grade or array of course grades if $course param is not given
 */
function grade_get_course_grade($userid, $courseid = null) {
    $coursegrades = array();

    // Get the course item(s)
    if (!empty($courseid)) {
        $courseitem  = grade_item::fetch_course_item($courseid);
        if ($final = $courseitem->get_final($userid)) { 
            return $final->finalgrade;
        } else {
            return null;
        }
    } else {
        $courses = get_my_courses($userid);
        foreach ($courses as $course_object) {
            $courseitem = grade_item::fetch_course_item($course_object->id);
            if ($final = $courseitem->get_final($userid)) {
                $coursegrades[$course_object->id] = $final->finalgrade;
            }
        }
        return $coursegrades;
    }
} 

/***** END OF PUBLIC API *****/


/**
 * Returns course gradebook setting
 * @param int $courseid
 * @param string $name of setting, maybe null if reset only
 * @param bool $resetcache force reset of internal static cache
 * @return string value, NULL if no setting
 */
function grade_get_setting($courseid, $name, $default=null, $resetcache=false) {
    static $cache = array();

    if ($resetcache or !array_key_exists($courseid, $cache)) {
        $cache[$courseid] = array();

    } else if (is_null($name)) {
        return null;

    } else if (array_key_exists($name, $cache[$courseid])) {
        return $cache[$courseid][$name];
    }

    if (!$data = get_record('grade_settings', 'courseid', $courseid, 'name', addslashes($name))) {
        $result = null;
    } else {
        $result = $data->value;
    }

    if (is_null($result)) {
        $result = $default;
    }

    $cache[$courseid][$name] = $result;
    return $result;
}

/**
 * Returns all course gradebook settings as object properties
 * @param int $courseid
 * @return object
 */
function grade_get_settings($courseid) {
     $settings = new object();
     $settings->id = $courseid;

    if ($records = get_records('grade_settings', 'courseid', $courseid)) {
        foreach ($records as $record) {
            $settings->{$record->name} = $record->value;
        }
    }

    return $settings;
}

/**
 * Add/update course gradebook setting
 * @param int $courseid
 * @param string $name of setting
 * @param string value, NULL means no setting==remove
 * @return void
 */
function grade_set_setting($courseid, $name, $value) {
    if (is_null($value)) {
        delete_records('grade_settings', 'courseid', $courseid, 'name', addslashes($name));

    } else if (!$existing = get_record('grade_settings', 'courseid', $courseid, 'name', addslashes($name))) {
        $data = new object();
        $data->courseid = $courseid;
        $data->name     = addslashes($name);
        $data->value    = addslashes($value);
        insert_record('grade_settings', $data);

    } else {
        $data = new object();
        $data->id       = $existing->id;
        $data->value    = addslashes($value);
        update_record('grade_settings', $data);
    }

    grade_get_setting($courseid, null, null, true); // reset the cache
}

/**
 * Returns string representation of grade value
 * @param float $value grade value
 * @param object $grade_item - by reference to prevent scale reloading
 * @param bool $localized use localised decimal separator
 * @param int $display type of display - raw, letter, percentage
 * @param int $decimalplaces number of decimal places when displaying float values
 * @return string
 */
function grade_format_gradevalue($value, &$grade_item, $localized=true, $displaytype=null, $decimals=null) {
    if ($grade_item->gradetype == GRADE_TYPE_NONE or $grade_item->gradetype == GRADE_TYPE_TEXT) {
        return '';
    }

    // no grade yet?
    if (is_null($value)) {
        return '-';
    }

    if ($grade_item->gradetype != GRADE_TYPE_VALUE and $grade_item->gradetype != GRADE_TYPE_SCALE) {
        //unknown type??
        return '';
    }

    if (is_null($displaytype)) {
        $displaytype = $grade_item->get_displaytype();
    }

    if (is_null($decimals)) {
        $decimals = $grade_item->get_decimals();
    }

    switch ($displaytype) {
        case GRADE_DISPLAY_TYPE_REAL:
            if ($grade_item->gradetype == GRADE_TYPE_SCALE) {
                if (!$scale = $grade_item->load_scale()) {
                    return get_string('error');
                }

                $value = (int)bounded_number($grade_item->grademin, $value, $grade_item->grademax);
                return format_string($scale->scale_items[$value-1]);

            } else {
                return format_float($value, $decimals, $localized);
            }

        case GRADE_DISPLAY_TYPE_PERCENTAGE:
            $min = $grade_item->grademin;
            $max = $grade_item->grademax;
            if ($min == $max) {
                return '';
            }
            $value = bounded_number($min, $value, $max);
            $percentage = (($value-$min)*100)/($max-$min);
            return format_float($percentage, $decimals, $localized).' %';

        case GRADE_DISPLAY_TYPE_LETTER:
            $context = get_context_instance(CONTEXT_COURSE, $grade_item->courseid);
            if (!$letters = grade_get_letters($context)) {
                return ''; // no letters??
            }

            $value = grade_grade::standardise_score($value, $grade_item->grademin, $grade_item->grademax, 0, 100);
            $value = bounded_number(0, $value, 100); // just in case
            foreach ($letters as $boundary => $letter) {
                if ($value >= $boundary) {
                    return format_string($letter);
                }
            }
            return '-'; // no match? maybe '' would be more correct

        default:
            return '';
    }
}

/**
 * Returns grade letters array used in context
 * @param object $context object or null for defaults
 * @return array of grade_boundary=>letter_string
 */
function grade_get_letters($context=null) {
    if (empty($context)) {
        //default grading letters
        return array('93'=>'A', '90'=>'A-', '87'=>'B+', '83'=>'B', '80'=>'B-', '77'=>'C+', '73'=>'C', '70'=>'C-', '67'=>'D+', '60'=>'D', '0'=>'F');
    }

    static $cache = array();

    if (array_key_exists($context->id, $cache)) {
        return $cache[$context->id];
    }

    if (count($cache) > 100) {
        $cache = array(); // cache size limit
    }

    $letters = array();

    $contexts = get_parent_contexts($context);
    array_unshift($contexts, $context->id);

    foreach ($contexts as $ctxid) {
        if ($records = get_records('grade_letters', 'contextid', $ctxid, 'lowerboundary DESC')) {
            foreach ($records as $record) {
                $letters[$record->lowerboundary] = $record->letter;
            }
        }

        if (!empty($letters)) {
            $cache[$context->id] = $letters;
            return $letters;
        }
    }

    $letters = grade_get_letters(null);
    $cache[$context->id] = $letters;
    return $letters;
}


/**
 * Verify new value of idnumber - checks for uniqueness of new idnumbers, old are kept intact
 * @param string idnumber string (with magic quotes)
 * @param object $cm used for course module idnumbers and items attached to modules
 * @param object $gradeitem is item idnumber
 * @return boolean true means idnumber ok
 */
function grade_verify_idnumber($idnumber, $grade_item=null, $cm=null) {
    if ($idnumber == '') {
        //we allow empty idnumbers
        return true;
    }

    // keep existing even when not unique
    if ($cm and $cm->idnumber == $idnumber) {
        return true;
    } else if ($grade_item and $grade_item->idnumber == $idnumber) {
        return true;
    }

    if (get_records('course_modules', 'idnumber', $idnumber)) {
        return false;
    }

    if (get_records('grade_items', 'idnumber', $idnumber)) {
        return false;
    }

    return true;
}

/**
 * Force final grade recalculation in all course items
 * @param int $courseid
 */
function grade_force_full_regrading($courseid) {
    set_field('grade_items', 'needsupdate', 1, 'courseid', $courseid);
}

/**
 * Updates all final grades in course.
 *
 * @param int $courseid
 * @param int $userid if specified, try to do a quick regrading of grades of this user only
 * @param object $updated_item the item in which
 * @return boolean true if ok, array of errors if problems found (item id is used as key)
 */
function grade_regrade_final_grades($courseid, $userid=null, $updated_item=null) {

    $course_item = grade_item::fetch_course_item($courseid);

    if ($userid) {
        // one raw grade updated for one user
        if (empty($updated_item)) {
            error("updated_item_id can not be null!");
        }
        if ($course_item->needsupdate) {
            $updated_item->force_regrading();
            return 'Can not do fast regrading after updating of raw grades';
        }

    } else {
        if (!$course_item->needsupdate) {
            // nothing to do :-)
            return true;
        }
    }

    $grade_items = grade_item::fetch_all(array('courseid'=>$courseid));
    $depends_on = array();

    // first mark all category and calculated items as needing regrading
    // this is slower, but 100% accurate
    foreach ($grade_items as $gid=>$gitem) {
        if (!empty($updated_item) and $updated_item->id == $gid) {
            $grade_items[$gid]->needsupdate = 1;

        } else if ($gitem->is_course_item() or $gitem->is_category_item() or $gitem->is_calculated()) {
            $grade_items[$gid]->needsupdate = 1;
        }

        // construct depends_on lookup array
        $depends_on[$gid] = $grade_items[$gid]->depends_on();
    }

    $errors = array();
    $finalids = array();
    $gids     = array_keys($grade_items);
    $failed = 0;

    while (count($finalids) < count($gids)) { // work until all grades are final or error found
        $count = 0;
        foreach ($gids as $gid) {
            if (in_array($gid, $finalids)) {
                continue; // already final
            }

            if (!$grade_items[$gid]->needsupdate) {
                $finalids[] = $gid; // we can make it final - does not need update
                continue;
            }

            $doupdate = true;
            foreach ($depends_on[$gid] as $did) {
                if (!in_array($did, $finalids)) {
                    $doupdate = false;
                    continue; // this item depends on something that is not yet in finals array
                }
            }

            //oki - let's update, calculate or aggregate :-)
            if ($doupdate) {
                $result = $grade_items[$gid]->regrade_final_grades($userid);

                if ($result === true) {
                    $grade_items[$gid]->regrading_finished();
                    $grade_items[$gid]->check_locktime(); // do the locktime item locking
                    $count++;
                    $finalids[] = $gid;

                } else {
                    $grade_items[$gid]->force_regrading();
                    $errors[$gid] = $result;
                }
            }
        }

        if ($count == 0) {
            $failed++;
        } else {
            $failed = 0;
        }

        if ($failed > 1) {
            foreach($gids as $gid) {
                if (in_array($gid, $finalids)) {
                    continue; // this one is ok
                }
                $grade_items[$gid]->force_regrading();
                $errors[$grade_items[$gid]->id] = 'Probably circular reference or broken calculation formula'; // TODO: localize
            }
            break; // oki, found error
        }
    }

    if (count($errors) == 0) {
        if (empty($userid)) {
            // do the locktime locking of grades, but only when doing full regrading
            grade_grade::check_locktime_all($gids);
        }
        return true;
    } else {
        return $errors;
    }
}

/**
 * For backwards compatibility with old third-party modules, this function can
 * be used to import all grades from activities with legacy grading.
 * @param int $courseid or null if all courses
 */
function grade_grab_legacy_grades($courseid=null) {

    global $CFG;

    if (!$mods = get_list_of_plugins('mod') ) {
        error('No modules installed!');
    }

    if ($courseid) {
        $course_sql = " AND cm.course=$courseid";
    } else {
        $course_sql = "";
    }

    foreach ($mods as $mod) {

        if ($mod == 'NEWMODULE') {   // Someone has unzipped the template, ignore it
            continue;
        }

        if (!$module = get_record('modules', 'name', $mod)) {
            //not installed
            continue;
        }

        if (!$module->visible) {
            //disabled module
            continue;
        }

        $fullmod = $CFG->dirroot.'/mod/'.$mod;

        // include the module lib once
        if (file_exists($fullmod.'/lib.php')) {
            include_once($fullmod.'/lib.php');
            // look for modname_grades() function - old gradebook pulling function
            // if present sync the grades with new grading system
            $gradefunc = $mod.'_grades';
            if (function_exists($gradefunc)) {

                // get all instance of the activity
                $sql = "SELECT a.*, cm.idnumber as cmidnumber, m.name as modname
                          FROM {$CFG->prefix}$mod a, {$CFG->prefix}course_modules cm, {$CFG->prefix}modules m
                         WHERE m.name='$mod' AND m.id=cm.module AND cm.instance=a.id $course_sql";

                if ($modinstances = get_records_sql($sql)) {
                    foreach ($modinstances as $modinstance) {
                        grade_update_mod_grades($modinstance);
                    }
                }
            }
        }
    }
}

/**
 * For testing purposes mainly, reloads grades from all non legacy modules into gradebook.
 */
function grade_grab_grades() {

    global $CFG;

    if (!$mods = get_list_of_plugins('mod') ) {
        error('No modules installed!');
    }

    foreach ($mods as $mod) {

        if ($mod == 'NEWMODULE') {   // Someone has unzipped the template, ignore it
            continue;
        }

        if (!$module = get_record('modules', 'name', $mod)) {
            //not installed
            continue;
        }

        if (!$module->visible) {
            //disabled module
            continue;
        }

        $fullmod = $CFG->dirroot.'/mod/'.$mod;

        // include the module lib once
        if (file_exists($fullmod.'/lib.php')) {
            include_once($fullmod.'/lib.php');
            // look for modname_grades() function - old gradebook pulling function
            // if present sync the grades with new grading system
            $gradefunc = $mod.'_update_grades';
            if (function_exists($gradefunc)) {
                $gradefunc();
            }
        }
    }
}

/**
 * Force full update of module grades in central gradebook - works for both legacy and converted activities.
 * @param object $modinstance object with extra cmidnumber and modname property
 * @return boolean success
 */
function grade_update_mod_grades($modinstance, $userid=0) {
    global $CFG;

    $fullmod = $CFG->dirroot.'/mod/'.$modinstance->modname;
    if (!file_exists($fullmod.'/lib.php')) {
        debugging('missing lib.php file in module');
        return false;
    }
    include_once($fullmod.'/lib.php');

    // does it use legacy grading?
    $gradefunc        = $modinstance->modname.'_grades';
    $updategradesfunc = $modinstance->modname.'_update_grades';
    $updateitemfunc   = $modinstance->modname.'_grade_item_update';

    if (function_exists($gradefunc)) {

        // legacy module - not yet converted
        if ($oldgrades = $gradefunc($modinstance->id)) {

            $grademax = $oldgrades->maxgrade;
            $scaleid = NULL;
            if (!is_numeric($grademax)) {
                // scale name is provided as a string, try to find it
                if (!$scale = get_record('scale', 'name', $grademax)) {
                    debugging('Incorrect scale name! name:'.$grademax);
                    return false;
                }
                $scaleid = $scale->id;
            }

            if (!$grade_item = grade_get_legacy_grade_item($modinstance, $grademax, $scaleid)) {
                debugging('Can not get/create legacy grade item!');
                return false;
            }

            $grades = array();
            foreach ($oldgrades->grades as $uid=>$usergrade) {
                if ($userid and $uid != $userid) {
                    continue;
                }
                $grade = new object();
                $grade->userid = $uid;

                if ($usergrade == '-') {
                    // no grade
                    $grade->rawgrade = null;

                } else if ($scaleid) {
                    // scale in use, words used
                    $gradescale = explode(",", $scale->scale);
                    $grade->rawgrade = array_search($usergrade, $gradescale) + 1;

                } else {
                    // good old numeric value
                    $grade->rawgrade = $usergrade;
                }
                $grades[] = $grade;
            }

            grade_update('legacygrab', $grade_item->courseid, $grade_item->itemtype, $grade_item->itemmodule,
                         $grade_item->iteminstance, $grade_item->itemnumber, $grades);
        }

    } else if (function_exists($updategradesfunc) and function_exists($updateitemfunc)) {
        //new grading supported, force updating of grades
        $updateitemfunc($modinstance);
        $updategradesfunc($modinstance, $userid);

    } else {
        // mudule does not support grading??
    }

    return true;
}

/**
 * Get and update/create grade item for legacy modules.
 */
function grade_get_legacy_grade_item($modinstance, $grademax, $scaleid) {

    // does it already exist?
    if ($grade_items = grade_item::fetch_all(array('courseid'=>$modinstance->course, 'itemtype'=>'mod', 'itemmodule'=>$modinstance->modname, 'iteminstance'=>$modinstance->id, 'itemnumber'=>0))) {
        if (count($grade_items) > 1) {
            debugging('Multiple legacy grade_items found.');
            return false;
        }

        $grade_item = reset($grade_items);

        if (is_null($grademax) and is_null($scaleid)) {
           $grade_item->gradetype  = GRADE_TYPE_NONE;

        } else if ($scaleid) {
            $grade_item->gradetype = GRADE_TYPE_SCALE;
            $grade_item->scaleid   = $scaleid;
            $grade_item->grademin  = 1;

        } else {
            $grade_item->gradetype  = GRADE_TYPE_VALUE;
            $grade_item->grademax   = $grademax;
            $grade_item->grademin   = 0;
        }

        $grade_item->itemname = $modinstance->name;
        $grade_item->idnumber = $modinstance->cmidnumber;

        $grade_item->update();

        return $grade_item;
    }

    // create new one
    $params = array('courseid'    =>$modinstance->course,
                    'itemtype'    =>'mod',
                    'itemmodule'  =>$modinstance->modname,
                    'iteminstance'=>$modinstance->id,
                    'itemnumber'  =>0,
                    'itemname'    =>$modinstance->name,
                    'idnumber'    =>$modinstance->cmidnumber);

    if (is_null($grademax) and is_null($scaleid)) {
        $params['gradetype'] = GRADE_TYPE_NONE;

    } else if ($scaleid) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid']   = $scaleid;
        $grade_item->grademin  = 1;
    } else {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $grademax;
        $params['grademin']  = 0;
    }

    $grade_item = new grade_item($params);
    $grade_item->insert();

    return $grade_item;
}

/**
 * Remove grade letters for given context
 * @param object $context
 */
function remove_grade_letters($context, $showfeedback) {
    $strdeleted = get_string('deleted');

    delete_records('grade_letters', 'contextid', $context->id);
    if ($showfeedback) {
        notify($strdeleted.' - '.get_string('letters', 'grades'));
    }
}
/**
 * Remove all grade related course data - history is kept
 * @param int $courseid
 * @param bool @showfeedback print feedback
 */
function remove_course_grades($courseid, $showfeedback) {
    $strdeleted = get_string('deleted');

    $course_category = grade_category::fetch_course_category($courseid);
    $course_category->delete('coursedelete');
    if ($showfeedback) {
        notify($strdeleted.' - '.get_string('grades', 'grades').', '.get_string('items', 'grades').', '.get_string('categories', 'grades'));
    }

    if ($outcomes = grade_outcome::fetch_all(array('courseid'=>$courseid))) {
        foreach ($outcomes as $outcome) {
            $outcome->delete('coursedelete');
        }
    }
    delete_records('grade_outcomes_courses', 'courseid', $courseid);
    if ($showfeedback) {
        notify($strdeleted.' - '.get_string('outcomes', 'grades'));
    }

    if ($scales = grade_scale::fetch_all(array('courseid'=>$courseid))) {
        foreach ($scales as $scale) {
            $scale->delete('coursedelete');
        }
    }
    if ($showfeedback) {
        notify($strdeleted.' - '.get_string('scales'));
    }

    delete_records('grade_settings', 'courseid', $courseid);
    if ($showfeedback) {
        notify($strdeleted.' - '.get_string('settings', 'grades'));
    }
}

/**
 * Builds an array of percentages indexed by integers for the purpose of building a select drop-down element.
 * @param int $steps The value between each level.
 * @param string $order 'asc' for 0-100 and 'desc' for 100-0
 * @param int $lowest The lowest value to include
 * @param int $highest The highest value to include
 */
function build_percentages_array($steps=1, $order='desc', $lowest=0, $highest=100) {
    // TODO reject or implement
}

/**
 * Grading cron job
 */
function grade_cron() {
    global $CFG;

    $now = time();

    $sql = "SELECT i.*
              FROM {$CFG->prefix}grade_items i
             WHERE i.locked = 0 AND i.locktime > 0 AND i.locktime < $now AND EXISTS (
                SELECT 'x' FROM {$CFG->prefix}grade_items c WHERE c.itemtype='course' AND c.needsupdate=0 AND c.courseid=i.courseid)";

    // go through all courses that have proper final grades and lock them if needed
    if ($rs = get_recordset_sql($sql)) {
        while ($item = rs_fetch_next_record($rs)) {
            $grade_item = new grade_item($item, false);
            $grade_item->locked = $now;
            $grade_item->update('locktime');
        }
        rs_close($rs);
    }

    $grade_inst = new grade_grade();
    $fields = 'g.'.implode(',g.', $grade_inst->required_fields);

    $sql = "SELECT $fields
              FROM {$CFG->prefix}grade_grades g, {$CFG->prefix}grade_items i
             WHERE g.locked = 0 AND g.locktime > 0 AND g.locktime < $now AND g.itemid=i.id AND EXISTS (
                SELECT 'x' FROM {$CFG->prefix}grade_items c WHERE c.itemtype='course' AND c.needsupdate=0 AND c.courseid=i.courseid)";

    // go through all courses that have proper final grades and lock them if needed
    if ($rs = get_recordset_sql($sql)) {
        while ($grade = rs_fetch_next_record($rs)) {
            $grade_grade = new grade_grade($grade, false);
            $grade_grade->locked = $now;
            $grade_grade->update('locktime');
        }
        rs_close($rs);
    }

    //TODO: do not run this cleanup every cron invocation
    // cleanup history tables
        if (!empty($CFG->gradehistorylifetime)) {  // value in days
            $histlifetime = $now - ($CFG->gradehistorylifetime * 3600 * 24);
            $tables = array('grade_outcomes_history', 'grade_categories_history', 'grade_items_history', 'grade_grades_history', 'scale_history');
            foreach ($tables as $table) {
                if (delete_records_select($table, "timemodified < '$histlifetime'")) {
                    mtrace("    Deleted old grade history records from '$table'");
                }
                
            }
        }


}

?>

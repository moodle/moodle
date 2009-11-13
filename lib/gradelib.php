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
 * Library of functions for gradebook - both public and internal
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

/////////////////////////////////////////////////////////////////////
///// Start of public API for communication with modules/blocks /////
/////////////////////////////////////////////////////////////////////

/**
 * Submit new or update grade; update/create grade_item definition. Grade must have userid specified,
 * rawgrade and feedback with format are optional. rawgrade NULL means 'Not graded', missing property
 * or key means do not change existing.
 *
 * Only following grade item properties can be changed 'itemname', 'idnumber', 'gradetype', 'grademax',
 * 'grademin', 'scaleid', 'multfactor', 'plusfactor', 'deleted' and 'hidden'. 'reset' means delete all current grades including locked ones.
 *
 * Manual, course or category items can not be updated by this function.
 * @public
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
    global $USER, $CFG;

    // only following grade_item properties can be changed in this function
    $allowed = array('itemname', 'idnumber', 'gradetype', 'grademax', 'grademin', 'scaleid', 'multfactor', 'plusfactor', 'deleted', 'hidden');
    // list of 10,5 numeric fields
    $floats  = array('grademin', 'grademax', 'multfactor', 'plusfactor');

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
            // no notice() here, test returned value instead!
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
                if (in_array($k, $floats)) {
                    if (grade_floats_different($grade_item->{$k}, $v)) {
                        $grade_item->{$k} = $v;
                        $update = true;
                    }

                } else {
                    if ($grade_item->{$k} != $v) {
                        $grade_item->{$k} = $v;
                        $update = true;
                    }
                }
            }
            if ($update) {
                $grade_item->update();
            }
        }
    }

/// reset grades if requested
    if (!empty($itemdetails['reset'])) {
        $grade_item->delete_all_grades('reset');
        return GRADE_UPDATE_OK;
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
        $grades = array($grades->userid=>$grades);
    } else {
        if (array_key_exists('userid', $grades)) {
            $grades = array($grades['userid']=>$grades);
        }
    }

/// normalize and verify grade array
    foreach($grades as $k=>$g) {
        if (!is_array($g)) {
            $g = (array)$g;
            $grades[$k] = $g;
        }

        if (empty($g['userid']) or $k != $g['userid']) {
            debugging('Incorrect grade array index, must be user id! Grade ignored.');
            unset($grades[$k]);
        }
    }

    if (empty($grades)) {
        return GRADE_UPDATE_FAILED;
    }

    $count = count($grades);
    if ($count == 1) {
        reset($grades);
        $uid = key($grades);
        $sql = "SELECT * FROM {$CFG->prefix}grade_grades WHERE itemid = $grade_item->id AND userid = $uid";

    } else if ($count < 200) {
        $uids = implode(',', array_keys($grades));
        $sql = "SELECT * FROM {$CFG->prefix}grade_grades WHERE itemid = $grade_item->id AND userid IN ($uids)";

    } else {
        $sql = "SELECT * FROM {$CFG->prefix}grade_grades WHERE itemid = $grade_item->id";
    }

    $rs = get_recordset_sql($sql);

    $failed = false;

    while (count($grades) > 0) {
        $grade_grade = null;
        $grade       = null;

        while ($rs and !rs_EOF($rs)) {
            if (!$gd = rs_fetch_next_record($rs)) {
                break;
            }
            $userid = $gd->userid;
            if (!isset($grades[$userid])) {
                // this grade not requested, continue
                continue;
            }
            // existing grade requested
            $grade       = $grades[$userid];
            $grade_grade = new grade_grade($gd, false);
            unset($grades[$userid]);
            break;
        }

        if (is_null($grade_grade)) {
            if (count($grades) == 0) {
                // no more grades to process
                break;
            }

            $grade       = reset($grades);
            $userid      = $grade['userid'];
            $grade_grade = new grade_grade(array('itemid'=>$grade_item->id, 'userid'=>$userid), false);
            $grade_grade->load_optional_fields(); // add feedback and info too
            unset($grades[$userid]);
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
        if (!$grade_item->update_raw_grade($userid, $rawgrade, $source, $feedback, $feedbackformat, $usermodified, $dategraded, $datesubmitted, $grade_grade)) {
            $failed = true;
        }
    }

    if ($rs) {
        rs_close($rs);
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
 * @public
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
 * @public
 * @param int $courseid id of course
 * @param string $itemtype 'mod', 'block'
 * @param string $itemmodule 'forum, 'quiz', etc.
 * @param int $iteminstance id of the item module
 * @param int $userid_or_ids optional id of the graded user or array of ids; if userid not used, returns only information about grade_item
 * @return array of grade information objects (scaleid, name, grade and locked status, etc.) indexed with itemnumbers
 */
function grade_get_grades($courseid, $itemtype, $itemmodule, $iteminstance, $userid_or_ids=null) {
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
                        if ($grade_item->gradetype == GRADE_TYPE_TEXT or $grade_item->gradetype == GRADE_TYPE_NONE) {
                            $grade->grade          = null;
                            $grade->str_grade      = '-';
                            $grade->str_long_grade = $grade->str_grade;

                        } else if (in_array($grade_item->id, $needsupdate)) {
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

                if (isset($return->outcomes[$grade_item->itemnumber])) {
                    // itemnumber duplicates - lets fix them!
                    $newnumber = $grade_item->itemnumber + 1;
                    while(grade_item::fetch(array('itemtype'=>$itemtype, 'itemmodule'=>$itemmodule, 'iteminstance'=>$iteminstance, 'courseid'=>$courseid, 'itemnumber'=>$newnumber))) {
                        $newnumber++;
                    }
                    $outcome->itemnumber    = $newnumber;
                    $grade_item->itemnumber = $newnumber;
                    $grade_item->update('system');
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

///////////////////////////////////////////////////////////////////
///// End of public API for communication with modules/blocks /////
///////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////
///// Internal API: used by gradebook plugins and Moodle core /////
///////////////////////////////////////////////////////////////////

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
 * @param int $displaytype type of display - GRADE_DISPLAY_TYPE_REAL, GRADE_DISPLAY_TYPE_PERCENTAGE, GRADE_DISPLAY_TYPE_LETTER
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
            return grade_format_gradevalue_real($value, $grade_item, $decimals, $localized);

        case GRADE_DISPLAY_TYPE_PERCENTAGE:
            return grade_format_gradevalue_percentage($value, $grade_item, $decimals, $localized);

        case GRADE_DISPLAY_TYPE_LETTER:
            return grade_format_gradevalue_letter($value, $grade_item);

        case GRADE_DISPLAY_TYPE_REAL_PERCENTAGE:
            return grade_format_gradevalue_real($value, $grade_item, $decimals, $localized) . ' (' .
                    grade_format_gradevalue_percentage($value, $grade_item, $decimals, $localized) . ')';

        case GRADE_DISPLAY_TYPE_REAL_LETTER:
            return grade_format_gradevalue_real($value, $grade_item, $decimals, $localized) . ' (' .
                    grade_format_gradevalue_letter($value, $grade_item) . ')';

        case GRADE_DISPLAY_TYPE_PERCENTAGE_REAL:
            return grade_format_gradevalue_percentage($value, $grade_item, $decimals, $localized) . ' (' .
                    grade_format_gradevalue_real($value, $grade_item, $decimals, $localized) . ')';

        case GRADE_DISPLAY_TYPE_LETTER_REAL:
            return grade_format_gradevalue_letter($value, $grade_item) . ' (' .
                    grade_format_gradevalue_real($value, $grade_item, $decimals, $localized) . ')';

        case GRADE_DISPLAY_TYPE_LETTER_PERCENTAGE:
            return grade_format_gradevalue_letter($value, $grade_item) . ' (' .
                    grade_format_gradevalue_percentage($value, $grade_item, $decimals, $localized) . ')';

        case GRADE_DISPLAY_TYPE_PERCENTAGE_LETTER:
            return grade_format_gradevalue_percentage($value, $grade_item, $decimals, $localized) . ' (' .
                    grade_format_gradevalue_letter($value, $grade_item) . ')';
        default:
            return '';
    }
}

function grade_format_gradevalue_real($value, $grade_item, $decimals, $localized) {
    if ($grade_item->gradetype == GRADE_TYPE_SCALE) {
        if (!$scale = $grade_item->load_scale()) {
            return get_string('error');
        }

        $value = $grade_item->bounded_grade($value);
        return format_string($scale->scale_items[$value-1]);

    } else {
        return format_float($value, $decimals, $localized);
    }
}

function grade_format_gradevalue_percentage($value, $grade_item, $decimals, $localized) {
    $min = $grade_item->grademin;
    $max = $grade_item->grademax;
    if ($min == $max) {
        return '';
    }
    $value = $grade_item->bounded_grade($value);
    $percentage = (($value-$min)*100)/($max-$min);
    return format_float($percentage, $decimals, $localized).' %';
}

function grade_format_gradevalue_letter($value, $grade_item) {
    $context = get_context_instance(CONTEXT_COURSE, $grade_item->courseid);
    if (!$letters = grade_get_letters($context)) {
        return ''; // no letters??
    }

    if (is_null($value)) {
        return '-';
    }

    $value = grade_grade::standardise_score($value, $grade_item->grademin, $grade_item->grademax, 0, 100);
    $value = bounded_number(0, $value, 100); // just in case
    foreach ($letters as $boundary => $letter) {
        if ($value >= $boundary) {
            return format_string($letter);
        }
    }
    return '-'; // no match? maybe '' would be more correct
}


/**
 * Returns grade options for gradebook category menu
 * @param int $courseid
 * @param bool $includenew include option for new category (-1)
 * @return array of grade categories in course
 */
function grade_get_categories_menu($courseid, $includenew=false) {
    $result = array();
    if (!$categories = grade_category::fetch_all(array('courseid'=>$courseid))) {
        //make sure course category exists
        if (!grade_category::fetch_course_category($courseid)) {
            debugging('Can not create course grade category!');
            return $result;
        }
        $categories = grade_category::fetch_all(array('courseid'=>$courseid));
    }
    foreach ($categories as $key=>$category) {
        if ($category->is_course_category()) {
            $result[$category->id] = get_string('uncategorised', 'grades');
            unset($categories[$key]);
        }
    }
    if ($includenew) {
        $result[-1] = get_string('newcategory', 'grades');
    }
    $cats = array();
    foreach ($categories as $category) {
        $cats[$category->id] = $category->get_name();
    }
    asort($cats, SORT_LOCALE_STRING);

    return ($result+$cats);
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
 * @param int $courseid - id numbers are course unique only
 * @param object $cm used for course module idnumbers and items attached to modules
 * @param object $gradeitem is item idnumber
 * @return boolean true means idnumber ok
 */
function grade_verify_idnumber($idnumber, $courseid, $grade_item=null, $cm=null) {
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

    if (get_records_select('course_modules', "course = $courseid AND idnumber='$idnumber'")) {
        return false;
    }

    if (get_records_select('grade_items', "courseid = $courseid AND idnumber='$idnumber'")) {
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
 * Forces regrading of all site grades - usualy when chanign site setings
 */
function grade_force_site_regrading() {
    global $CFG;
    $sql = "UPDATE {$CFG->prefix}grade_items SET needsupdate=1";
    execute_sql($sql, false);
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
            return array($course_item->id =>'Can not do fast regrading after updating of raw grades');
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
 * @param int $courseid
 */
function grade_grab_legacy_grades($courseid) {
    global $CFG;

    if (!$mods = get_list_of_plugins('mod') ) {
        error('No modules installed!');
    }

    foreach ($mods as $mod) {
        if ($mod == 'NEWMODULE') {   // Someone has unzipped the template, ignore it
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
                grade_grab_course_grades($courseid, $mod);
            }
        }
    }
}

/**
 * Refetches data from all course activities
 * @param int $courseid
 * @param string $modname
 * @return success
 */
function grade_grab_course_grades($courseid, $modname=null) {
    global $CFG;

    if ($modname) {
        $sql = "SELECT a.*, cm.idnumber as cmidnumber, m.name as modname
                  FROM {$CFG->prefix}$modname a, {$CFG->prefix}course_modules cm, {$CFG->prefix}modules m
                 WHERE m.name='$modname' AND m.visible=1 AND m.id=cm.module AND cm.instance=a.id AND cm.course=$courseid";

        if ($modinstances = get_records_sql($sql)) {
            foreach ($modinstances as $modinstance) {
                grade_update_mod_grades($modinstance);
            }
        }
        return;
    }

    if (!$mods = get_list_of_plugins('mod') ) {
        error('No modules installed!');
    }

    foreach ($mods as $mod) {
        if ($mod == 'NEWMODULE') {   // Someone has unzipped the template, ignore it
            continue;
        }

        $fullmod = $CFG->dirroot.'/mod/'.$mod;

        // include the module lib once
        if (file_exists($fullmod.'/lib.php')) {
            // get all instance of the activity
            $sql = "SELECT a.*, cm.idnumber as cmidnumber, m.name as modname
                      FROM {$CFG->prefix}$mod a, {$CFG->prefix}course_modules cm, {$CFG->prefix}modules m
                     WHERE m.name='$mod' AND m.visible=1 AND m.id=cm.module AND cm.instance=a.id AND cm.course=$courseid";

            if ($modinstances = get_records_sql($sql)) {
                foreach ($modinstances as $modinstance) {
                    grade_update_mod_grades($modinstance);
                }
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
        debugging('missing lib.php file in module ' . $modinstance->modname);
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

            if (!empty($oldgrades->grades)) {
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
                    $grades[$uid] = $grade;
                }

                grade_update('legacygrab', $grade_item->courseid, $grade_item->itemtype, $grade_item->itemmodule,
                             $grade_item->iteminstance, $grade_item->itemnumber, $grades);
            }
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
 * Returns list of currently used mods with legacy grading in course
 * @param $courseid int
 * @return array of modname=>modulenamestring mods with legacy grading
 */
function grade_get_legacy_modules($courseid) {
    global $CFG;

    if (!$mods = get_course_mods($courseid)) {
        return array();
    }
    $legacy = array();

    foreach ($mods as $mod) {
        $modname = $mod->modname;

        $modlib = "$CFG->dirroot/mod/$modname/lib.php";
        if (!$modlib) {
            continue;
        }
        include_once($modlib);
        $gradefunc = $modname.'_grades';
        if (!function_exists($gradefunc)) {
            continue;
        }
        $legacy[$modname] = get_string('modulename', $modname);
    }

    return $legacy;
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
 * @param bool $showfeedback print feedback
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
 * Called when course category deleted - cleanup gradebook
 * @param int $categoryid course category id
 * @param int $newparentid empty means everything deleted, otherwise id of category where content moved
 * @param bool $showfeedback print feedback
 */
function grade_course_category_delete($categoryid, $newparentid, $showfeedback) {
    $context = get_context_instance(CONTEXT_COURSECAT, $categoryid);
    delete_records('grade_letters', 'contextid', $context->id);
}

/**
 * Does gradebook cleanup when module uninstalled.
 */
function grade_uninstalled_module($modname) {
    global $CFG;

    $sql = "SELECT *
              FROM {$CFG->prefix}grade_items
             WHERE itemtype='mod' AND itemmodule='$modname'";

    // go all items for this module and delete them including the grades
    if ($rs = get_recordset_sql($sql)) {
        while ($item = rs_fetch_next_record($rs)) {
            $grade_item = new grade_item($item, false);
            $grade_item->delete('moduninstall');
        }
        rs_close($rs);
    }
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
            if (delete_records_select($table, "timemodified < $histlifetime")) {
                mtrace("    Deleted old grade history records from '$table'");
            }
        }
    }
}

/**
 * Resel all course grades
 * @param int $courseid
 * @return success
 */
function grade_course_reset($courseid) {

    // no recalculations
    grade_force_full_regrading($courseid);

    $grade_items = grade_item::fetch_all(array('courseid'=>$courseid));
    foreach ($grade_items as $gid=>$grade_item) {
        $grade_item->delete_all_grades('reset');
    }

    //refetch all grades
    grade_grab_course_grades($courseid);

    // recalculate all grades
    grade_regrade_final_grades($courseid);
    return true;
}

/**
 * Convert number to 5 decimalfloat, empty tring or null db compatible format
 * (we need this to decide if db value changed)
 * @param mixed number
 * @return mixed float or null
 */
function grade_floatval($number) {
    if (is_null($number) or $number === '') {
        return null;
    }
    // we must round to 5 digits to get the same precision as in 10,5 db fields
    // note: db rounding for 10,5 is different from php round() function
    return round($number, 5);
}

/**
 * Compare two float numbers safely. Uses 5 decimals php precision. Nulls accepted too.
 * Used for skipping of db updates
 * @param float $f1
 * @param float $f2
 * @return true if different
 */
function grade_floats_different($f1, $f2) {
    // note: db rounding for 10,5 is different from php round() function
    return (grade_floatval($f1) !== grade_floatval($f2));
}

?>

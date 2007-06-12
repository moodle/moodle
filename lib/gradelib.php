<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-2003  Martin Dougiamas  http://dougiamas.com       //
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

define('GRADE_AGGREGATE_MEAN', 0);
define('GRADE_AGGREGATE_MEDIAN', 1);
define('GRADE_AGGREGATE_SUM', 2);
define('GRADE_AGGREGATE_MODE', 3);

define('GRADE_CHILDTYPE_ITEM', 0);
define('GRADE_CHILDTYPE_CAT', 1);

define('GRADE_ITEM', 0); // Used to compare class names with CHILDTYPE values
define('GRADE_CATEGORY', 1); // Used to compare class names with CHILDTYPE values

define('GRADE_TYPE_NONE', 0);
define('GRADE_TYPE_VALUE', 1);
define('GRADE_TYPE_SCALE', 2);
define('GRADE_TYPE_TEXT', 3);

define('GRADE_UPDATE_OK', 0);
define('GRADE_UPDATE_FAILED', 1);
define('GRADE_UPDATE_MULTIPLE', 2);
define('GRADE_UPDATE_ITEM_DELETED', 3);
define('GRADE_UPDATE_ITEM_LOCKED', 4);


require_once($CFG->libdir . '/grade/grade_category.php');
require_once($CFG->libdir . '/grade/grade_item.php');
require_once($CFG->libdir . '/grade/grade_calculation.php');
require_once($CFG->libdir . '/grade/grade_grades_raw.php');
require_once($CFG->libdir . '/grade/grade_grades_final.php');
require_once($CFG->libdir . '/grade/grade_scale.php');
require_once($CFG->libdir . '/grade/grade_outcome.php');
require_once($CFG->libdir . '/grade/grade_history.php');
require_once($CFG->libdir . '/grade/grade_grades_text.php');
require_once($CFG->libdir . '/grade/grade_tree.php');

/***** PUBLIC GRADE API *****/

/**
 * Submit new or update grade; update/create grade_item definition. Grade must have userid specified,
 * gradevalue and feedback with format are optional. gradevalue NULL means 'Not graded', missing property
 * or key means do not change existing.
 *
 * Only following grade item properties can be changed 'itemname', 'idnumber', 'gradetype', 'grademax',
 * 'grademin', 'scaleid', 'multfactor', 'plusfactor', 'deleted'.
 *
 * @param string $source source of the grade such as 'mod/assignment', often used to prevent infinite loops when processing grade_updated events
 * @param int $courseid id of course
 * @param string $itemtype type of grade item - mod, block, gradecategory, calculated
 * @param string $itemmodule more specific then $itemtype - assignment, forum, etc.; maybe NULL for some item types
 * @param int $iteminstance instance it of graded subject
 * @param int $itemnumber most probably 0, modules can use other numbers when having more than one grades for each user
 * @param mixed $grades grade (object, array) or several grades (arrays of arrays or objects), NULL if updating rgade_item definition only\
 * @param mixed $itemdetails object or array describing the grading item, NULL if no change
 */
function grade_update($source, $courseid, $itemtype, $itemmodule, $iteminstance, $itemnumber, $grades=NULL, $itemdetails=NULL) {

    // only following grade_item properties can be changed in this function
    $allowed = array('itemname', 'idnumber', 'gradetype', 'grademax', 'grademin', 'scaleid', 'multfactor', 'plusfactor', 'deleted');

    if (is_null($courseid) or is_null($itemtype)) {
        debugging('Missing courseid or itemtype');
        return GRADE_UPDATE_FAILED;
    }

    $grade_item = new grade_item(compact('courseid', 'itemtype', 'itemmodule', 'iteminstance', 'itemnumber'), false);
    if (!$grade_items = $grade_item->fetch_all_using_this()) {
        // create a new one
        $grade_item = false;

    } else if (count($grade_items) == 1){
        $grade_item = reset($grade_items);
        unset($grade_items); //release memory

    } else {

        debugging('Found more than one grade item');
        return GRADE_UPDATE_MULTIPLE;
    }

/// Create or update the grade_item if needed
    if (!$grade_item) {
        $params = compact('courseid', 'itemtype', 'itemmodule', 'iteminstance', 'itemnumber');
        if ($itemdetails) {
            $itemdetails = (array)$itemdetails;
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
        if ($grade_item->locked) {
            debugging('Grading item is locked!');
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

    // no grading in deleted items
    if ($grade_item->deleted) {
        debugging('Grade item was already deleted!');
        return GRADE_UPDATE_ITEM_DELETED;
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
        }

        // get the raw grade if it exist
        $rawgrade = new grade_grades_raw(array('itemid'=>$grade_item->id, 'userid'=>$grade['userid']));
        $rawgrade->grade_item = &$grade_item; // we already have it, so let's use it

        // store these to keep track of original grade item settings
        $rawgrade->grademax = $grade_item->grademax;
        $rawgrade->grademin = $grade_item->grademin;
        $rawgrade->scaleid  = $grade_item->scaleid;

        if (array_key_exists('feedback', $grade)) {
            $rawgrade->feedback = $grade['feedback'];
            if (isset($grade['feedbackformat'])) {
                $rawgrade->feedbackformat = $grade['feedbackformat'];
            } else {
                $rawgrade->feedbackformat = FORMAT_MOODLE;
            }
        }

        $result = true;
        if ($rawgrade->id) {
            if (array_key_exists('gradevalue', $grade)) {
                $result = $rawgrade->update($grade['gradevalue'], $source);
            } else {
                $result = $rawgrade->update($rawgrade->gradevalue, $source);
            }

        } else {
            if (array_key_exists('gradevalue', $grade)) {
                $rawgrade->gradevalue = $grade['gradevalue'];
            } else {
                $rawgrade->gradevalue = null;
            }
            $result = $rawgrade->insert();
        }

        if (!$result) {
            $failed = true;
            debugging('Grade not updated');
            continue;
        }

        // load existing text annotation
        $rawgrade->load_text();

        // trigger grade_updated event notification
        $eventdata = new object();
        $eventdata->source            = $source;
        $eventdata->itemid            = $grade_item->id;
        $eventdata->courseid          = $grade_item->courseid;
        $eventdata->itemtype          = $grade_item->itemtype;
        $eventdata->itemmodule        = $grade_item->itemmodule;
        $eventdata->iteminstance      = $grade_item->iteminstance;
        $eventdata->itemnumber        = $grade_item->itemnumber;
        $eventdata->idnumber          = $grade_item->idnumber;
        $eventdata->userid            = $rawgrade->userid;
        $eventdata->gradevalue        = $rawgrade->gradevalue;
        $eventdata->feedback          = $rawgrade->feedback;
        $eventdata->feedbackformat    = (int)$rawgrade->feedbackformat;
        $eventdata->information       = $rawgrade->information;
        $eventdata->informationformat = (int)$rawgrade->informationformat;

        events_trigger('grade_updated', $eventdata);
    }

    if (!$failed) {
        return GRADE_UPDATE_OK;
    } else {
        return GRADE_UPDATE_FAILED;
    }
}


/**
* Tells a module whether a grade (or grade_item if $userid is not given) is currently locked or not.
* This is a combination of the actual settings in the grade tables and a check on moodle/course:editgradeswhenlocked.
* If it's locked to the current use then the module can print a nice message or prevent editing in the module.
* If no $userid is given, the method will always return the grade_item's locked state.
* If a $userid is given, the method will first check the grade_item's locked state (the column). If it is locked,
* the method will return true no matter the locked state of the specific grade being checked. If unlocked, it will
* return the locked state of the specific grade.
*
* @param int $courseid id of course
* @param string $itemtype 'mod', 'blocks', 'import', 'calculated' etc
* @param string $itemmodule 'forum, 'quiz', 'csv' etc
* @param int $iteminstance id of the item module
* @param int $itemnumber most probably 0, modules can use other numbers when having more than one grades for each user
* @param int $userid ID of the graded user
* @return boolean Whether the grade is locked or not
*/
function grade_is_locked($courseid, $itemtype, $itemmodule, $iteminstance, $itemnumber, $userid=NULL) {

    $grade_item = new grade_item(compact('courseid', 'itemtype', 'itemmodule', 'iteminstance', 'itemnumber'), false);
    if (!$grade_items = $grade_item->fetch_all_using_this()) {
        return false;

    } else if (count($grade_items) == 1){
        $grade_item = reset($grade_items);
        return $grade_item->is_locked($userid);

    } else {
        debugging('Found more than one grade item');
        foreach ($grade_items as $grade_item) {
            if ($grade_item->is_locked($userid)) {
                return true;
            }
        }
        return false;
    }
}

/***** END OF PUBLIC API *****/


/**
* Extracts from the gradebook all the grade items attached to the calling object.
* For example, an assignment may want to retrieve all the grade_items for itself,
* and get three outcome scales in return. This will affect the grading interface.
*
* Note: Each parameter refines the search. So if you only give the courseid,
*       all the grade_items for this course will be returned. If you add the
*       itemtype 'mod', all grade_items for this courseif AND for the 'mod'
*       type will be returned, etc...
*
* @param int $courseid The id of the course to which the grade items belong
* @param string $itemtype 'mod', 'blocks', 'import', 'calculated' etc
* @param string $itemmodule 'forum, 'quiz', 'csv' etc
* @param int $iteminstance id of the item module
* @param int $itemnumber Can be used to distinguish multiple grades for an activity
* @param string $itemname The name of the grade item
* @param int $idnumber grade item Primary Key
* @return array An array of grade items
*/
function grade_get_items($courseid, $itemtype=NULL, $itemmodule=NULL, $iteminstance=NULL, $itemnumber=NULL, $itemname=NULL, $idnumber=NULL) {
    $grade_item = new grade_item(compact('courseid', 'itemtype', 'itemmodule', 'iteminstance', 'itemname', 'itemnumber', 'idnumber'), false);
    $grade_items = $grade_item->fetch_all_using_this();
    return $grade_items;
}

/**
* For a given set of items, create a category to group them together (if one doesn't yet exist).
* Modules may want to do this when they are created. However, the ultimate control is in the gradebook interface itself.
*
* @param int $courseid
* @param string $fullname The name of the new category
* @param array $items An array of grade_items to group under the new category
* @param string $aggregation
* @return mixed New grade_category id if successful
*/
function grade_create_category($courseid, $fullname, $items, $aggregation=GRADE_AGGREGATE_MEAN) {
    $grade_category = new grade_category(compact('courseid', 'fullname', 'items', 'aggregation'));

    if (empty($grade_category->id)) {
        return $grade_category->insert();
    } else {
        return $grade_category->id;
    }
}

/**
 * Updates all grade_grades_final for each grade_item matching the given attributes.
 * The search is further restricted, so that only grade_items that have needs_update == TRUE
 * or that use calculation are retrieved.
 *
 * @param int $courseid
 * @param int $gradeitemid
 * @return int Number of grade_items updated
 */
function grade_update_final_grades($courseid=NULL, $gradeitemid=NULL) {
    $grade_item = new grade_item();
    $grade_item->courseid = $courseid;
    $grade_item->id = $gradeitemid;
    $grade_items = $grade_item->fetch_all_using_this();

    $count = 0;

    foreach ($grade_items as $gi) {
        $calculation = $gi->get_calculation();
        if (!empty($calculation) || $gi->needsupdate) {
            if ($gi->update_final_grade()) {
                $count++;
            }
        }
    }

    return $count;
}

/**
 * For backwards compatibility with old third-party modules, this function can
 * be used to import all grades from activities with legacy grading.
 */
function grade_grab_legacy_grades() {

    global $CFG, $db;

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
            $gradefunc = $mod.'_grades';
            if (function_exists($gradefunc)) {

                // get all instance of the activity
                $sql = "SELECT a.*, cm.idnumber as cmidnumber, m.name as modname
                          FROM {$CFG->prefix}$mod a, {$CFG->prefix}course_modules cm, {$CFG->prefix}modules m
                         WHERE m.name='$mod' AND m.id=cm.module AND cm.instance=a.id";

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
 * Force full update of module grades in central gradebook - works for both legacy and converted activities.
 * @param object $modinstance object with extra cmidnumber and modname property
 * @return boolean success
 */
function grade_update_mod_grades($modinstance) {
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
            foreach ($oldgrades->grades as $userid=>$usergrade) {
                $grade = new object();
                $grade->userid = $userid;

                if ($usergrade == '-') {
                    // no grade
                    $grade->gradevalue = null;

                } else if ($scaleid) {
                    // scale in use, words used
                    $gradescale = explode(",", $scale->scale);
                    $grade->gradevalue = array_search($usergrade, $gradescale) + 1;

                } else {
                    // good old numeric value
                    $grade->gradevalue = $usergrade;
                }
                $grades[] = $grade;
            }

            grade_update('legacygrab', $grade_item->courseid, $grade_item->itemtype, $grade_item->itemmodule,
                         $grade_item->iteminstance, $grade_item->itemnumber, $grades);
        }

    } else if (function_exists($updategradesfunc) and function_exists($updateitemfunc)) {
        //new grading supported, force updating of grades
        $updateitemfunc($modinstance);
        $updategradesfunc($modinstance);

    } else {
        // mudule does not support grading
    }

    return true;
}

/**
 * Get and update/create grade item for legacy modules.
 */
function grade_get_legacy_grade_item($modinstance, $grademax, $scaleid) {

    // does it already exist?
    if ($grade_items = grade_get_items($modinstance->course, 'mod', $modinstance->modname, $modinstance->id, 0)) {
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
 * Given a float value situated between a source minimum and a source maximum, converts it to the
 * corresponding value situated between a target minimum and a target maximum. Thanks to Darlene
 * for the formula :-)
 * @param float $gradevalue
 * @param float $source_min
 * @param float $source_max
 * @param float $target_min
 * @param float $target_max
 * @return float Converted value
 */
function standardise_score($gradevalue, $source_min, $source_max, $target_min, $target_max, $debug=false) {
    $factor = ($gradevalue - $source_min) / ($source_max - $source_min);
    $diff = $target_max - $target_min;
    $standardised_value = $factor * $diff + $target_min;
    if ($debug) {
        echo 'standardise_score debug info: (lib/gradelib.php)';
        print_object(array('gradevalue' => $gradevalue,
                           'source_min' => $source_min,
                           'source_max' => $source_max,
                           'target_min' => $target_min,
                           'target_max' => $target_max,
                           'result'     => $standardised_value));
    }
    return $standardised_value;
}


?>

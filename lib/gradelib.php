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

// category aggregation types
define('GRADE_AGGREGATE_MEAN_ALL', 0);
define('GRADE_AGGREGATE_MEAN_GRADED', 1);
define('GRADE_AGGREGATE_MEDIAN_ALL', 2);
define('GRADE_AGGREGATE_MEDIAN_GRADED', 3);
define('GRADE_AGGREGATE_MIN_ALL', 4);
define('GRADE_AGGREGATE_MIN_GRADED', 5);
define('GRADE_AGGREGATE_MAX_ALL', 6);
define('GRADE_AGGREGATE_MAX_GRADED', 7);
define('GRADE_AGGREGATE_MODE_ALL', 8);
define('GRADE_AGGREGATE_MODE_GRADED', 9);
define('GRADE_AGGREGATE_WEIGHTED_MEAN_ALL', 10);
define('GRADE_AGGREGATE_WEIGHTED_MEAN_GRADED', 11);
define('GRADE_AGGREGATE_EXTRACREDIT_MEAN_ALL', 12);
define('GRADE_AGGREGATE_EXTRACREDIT_MEAN_GRADED', 13);

// grade types
define('GRADE_TYPE_NONE', 0);
define('GRADE_TYPE_VALUE', 1);
define('GRADE_TYPE_SCALE', 2);
define('GRADE_TYPE_TEXT', 3);

// grade_update() return status
define('GRADE_UPDATE_OK', 0);
define('GRADE_UPDATE_FAILED', 1);
define('GRADE_UPDATE_MULTIPLE', 2);
define('GRADE_UPDATE_ITEM_DELETED', 3);
define('GRADE_UPDATE_ITEM_LOCKED', 4);

// Grate teables history tracking actions
define('GRADE_HISTORY_INSERT', 1);
define('GRADE_HISTORY_UPDATE', 2);
define('GRADE_HISTORY_DELETE', 3);

// Grader reports
define('GRADE_CATEGORY_CONTRACTED', 0); // The state of a category header in the grader report
define('GRADE_CATEGORY_EXPANDED', 1); // The state of a category header in the grader report

define('GRADE_REPORT_AGGREGATION_POSITION_LEFT', 0);
define('GRADE_REPORT_AGGREGATION_POSITION_RIGHT', 1);
define('GRADE_REPORT_AGGREGATION_VIEW_FULL', 0);
define('GRADE_REPORT_AGGREGATION_VIEW_COMPACT', 1);
define('GRADE_REPORT_GRADE_DISPLAY_TYPE_REAL', 0);
define('GRADE_REPORT_GRADE_DISPLAY_TYPE_PERCENTAGE', 1);
define('GRADE_REPORT_GRADE_DISPLAY_TYPE_LETTER', 2);
define('GRADE_REPORT_PREFERENCE_DEFAULT', 'default');
define('GRADE_REPORT_PREFERENCE_INHERIT', 'inherit');
define('GRADE_REPORT_PREFERENCE_UNUSED', -1);

// Common directories
define('GRADE_EDIT_DIR', $CFG->dirroot . '/grade/edit');
define('GRADE_EDIT_URL', $CFG->wwwroot . '/grade/edit');

require_once($CFG->libdir . '/grade/grade_category.php');
require_once($CFG->libdir . '/grade/grade_item.php');
require_once($CFG->libdir . '/grade/grade_grade.php');
require_once($CFG->libdir . '/grade/grade_scale.php');
require_once($CFG->libdir . '/grade/grade_outcome.php');
require_once($CFG->libdir . '/grade/grade_grade_text.php');
require_once($CFG->libdir . '/grade/grade_tree.php');

/***** PUBLIC GRADE API *****/

/**
 * Submit new or update grade; update/create grade_item definition. Grade must have userid specified,
 * rawgrade and feedback with format are optional. rawgrade NULL means 'Not graded', missing property
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
    global $USER;

    // only following grade_item properties can be changed in this function
    $allowed = array('itemname', 'idnumber', 'gradetype', 'grademax', 'grademin', 'scaleid', 'multfactor', 'plusfactor', 'deleted');

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
        } else {
            $usermodified = $USER->id;
        }

        // update or insert the grade
        if (!$grade_item->update_raw_grade($userid, $rawgrade, $source, null, $feedback, $feedbackformat, $usermodified)) {
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

    if (!$grade_items = grade_item::fetch_all(compact('courseid', 'itemtype', 'itemmodule', 'iteminstance', 'itemnumber'))) {
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
    // this is slower, but 100% accurate - this function is called only when there is
    // a change in grading setup, update of individual grade does not trigger this function
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
    if ($grade_items = grade_grade::fetch_all(array('courseid'=>$modinstance->course, 'itemtype'=>'mod', 'itemmodule'=>$modinstance->modname, 'iteminstance'=>$modinstance->id, 'itemnumber'=>0))) {
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
 * This function is used to migrade old date and settings from old gradebook into new grading system.
 * @param int $courseid
 */
function grade_upgrade_oldgradebook($courseid) {
    global $CFG;

    // regrade everything
    grade_force_full_regrading($courseid);

    // course grade data
    $course_category = grade_category::fetch_course_category($courseid);
    $course_item     = $course_category->get_grade_item();

    // first create all categories if needed
    $categories = array();
    $oldcats = get_records('grade_category', 'courseid', $courseid, 'id');

    if (empty($oldcats) or count($oldcats) == 1) {
        $course_category->aggregation = GRADE_AGGREGATE_MEAN_ALL;
        $course_category->update('upgrade');
        if ($oldcats) {
            $oldcat = reset($oldcats);
            $categories[$oldcat->id] =& $course_category;
        }

    } else {
        foreach ($oldcats as $oldcat) {
            $newcat = new grade_category(array('courseid'=>$courseid, 'fullname'=>$oldcat->name));
            $newcat->droplow     = $oldcat->drop_x_lowest;
            $newcat->aggregation = GRADE_AGGREGATE_MEAN_ALL;

            if (empty($newcat->id)) {
                $newcat->insert('upgrade');
            } else {
                $newcat->update('upgrade');
            }

            $categories[$oldcat->id] =& $newcat;

            $catitem = $newcat->get_grade_item();
            $catitem->gradetype       = GRADE_TYPE_VALUE;
            $catitem->plusfactor      = $oldcat->bonus_points;
            $catitem->hidden          = $oldcat->hidden;
            $catitem->aggregationcoef = $oldcat->weight;
            $catitem->update('upgrade');
        }

        $course_category->aggregation = GRADE_AGGREGATE_WEIGHTED_MEAN_ALL;
        $course_category->update('upgrade');
    }

    // get all grade items with mod details
    $sql = "SELECT gi.*, cm.idnumber as cmidnumber, m.name as modname
              FROM {$CFG->prefix}grade_item gi, {$CFG->prefix}course_modules cm, {$CFG->prefix}modules m
             WHERE gi.courseid=$courseid AND m.id=gi.modid AND cm.instance=gi.cminstance
          ORDER BY gi.sortorder ASC";

    if ($olditems = get_records_sql($sql)) {
        foreach ($olditems as $olditem) {
            $newitem = new grade_item(array('courseid'=>$olditem->courseid, 'itemtype'=>'mod', 'itemmodule'=>$olditem->modname, 'iteminstance'=>$olditem->cminstance, 'itemnumber'=>0));
            $newitem->multfactor      = $olditem->scale_grade;
            $newitem->aggregationcoef = $olditem->extra_credit;
            if ($olditem->extra_credit and $categories[$olditem->category]->aggregation != GRADE_AGGREGATE_EXTRACREDIT_MEAN_ALL) {
                $categories[$olditem->category]->aggregation = GRADE_AGGREGATE_EXTRACREDIT_MEAN_ALL;
                $categories[$olditem->category]->update('upgrade');
            }

            if (empty($newitem->id)) {
                $newitem->gradetype = GRADE_TYPE_NONE; // type not known yet
                $newitem->insert('upgrade');
            } else {
                $newitem->update('upgrade');
            }

            if (!empty($olditem->category)) {
                $newitem->set_parent($categories[$olditem->category]->id);
            }
        }
    }

    // setup up exception handling
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
?>

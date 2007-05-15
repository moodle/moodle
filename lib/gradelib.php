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
define('GRADE_TYPE_VALUE', 0);
define('GRADE_TYPE_SCALE', 1);
define('GRADE_TYPE_TEXT', 2);

require_once($CFG->libdir . '/grade/grade_category.php');
require_once($CFG->libdir . '/grade/grade_item.php');
require_once($CFG->libdir . '/grade/grade_calculation.php');
require_once($CFG->libdir . '/grade/grade_grades_raw.php');
require_once($CFG->libdir . '/grade/grade_grades_final.php');
require_once($CFG->libdir . '/grade/grade_scale.php');
require_once($CFG->libdir . '/grade/grade_outcome.php');
require_once($CFG->libdir . '/grade/grade_history.php');
require_once($CFG->libdir . '/grade/grade_grades_text.php');

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
* @param string $itemname The name of the grade item
* @param string $itemtype 'mod', 'blocks', 'import', 'calculated' etc
* @param string $itemmodule 'forum, 'quiz', 'csv' etc
* @param int $iteminstance id of the item module
* @param int $itemnumber Can be used to distinguish multiple grades for an activity
* @param int $idnumber grade item Primary Key
* @return array An array of grade items
*/
function grade_get_items($courseid, $itemname=NULL, $itemtype=NULL, $itemmodule=NULL, $iteminstance=NULL, $itemnumber=NULL, $idnumber=NULL) {
    $grade_item = new grade_item(compact('courseid', 'itemname', 'itemtype', 'itemmodule', 'iteminstance', 'itemnumber', 'idnumber'), false);
    $grade_items = $grade_item->fetch_all_using_this();
    return $grade_items;
}


/**
* Creates a new grade_item in case it doesn't exist. This function would be called when a module
* is created or updates, for example, to ensure grade_item entries exist.
* It's not essential though--if grades are being added later and a matching grade_item doesn't
* yet exist, the gradebook will create them on the fly.
* 
* @param 
* @return mixed New grade_item id if successful
*/
function grade_create_item($params) {
    $grade_item = new grade_item($params);
    return $grade_item->insert();
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
    return $grade_category->insert();
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
* @param string $itemtype 'mod', 'blocks', 'import', 'calculated' etc
* @param string $itemmodule 'forum, 'quiz', 'csv' etc
* @param int $iteminstance id of the item module
* @param int $itemnumber Optional number of the item to check
* @param int $userid ID of the user who owns the grade
* @return boolean Whether the grade is locked or not
*/
function grade_is_locked($itemtype, $itemmodule, $iteminstance, $itemnumber=NULL, $userid=NULL) {
    $grade_item = new grade_item(compact('itemtype', 'itemmodule', 'iteminstance', 'itemnumber'));
    return $grade_item->is_locked($userid);
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

/*
 * For backward compatibility with old third-party modules, this function is called
 * via to admin/cron.php to search all mod/xxx/lib.php files for functions named xxx_grades(),
 * if the current modules does not have grade events registered with the grade book.
 * Once the data is extracted, the event_trigger() function can be called to initiate 
 * an event as usual and copy/ *upgrade the data in the gradebook tables. 
 */
function grades_grab_grades() {
    
    global $CFG, $db;

    if (!$mods = get_list_of_plugins('mod') ) {
        error('No modules installed!');
    }

    foreach ($mods as $mod) {

        if ($mod == 'NEWMODULE') {   // Someone has unzipped the template, ignore it
            continue;
        }

        $fullmod = $CFG->dirroot .'/mod/'. $mod;

        // include the module lib once
        if (file_exists($fullmod.'/lib.php')) {
            include_once($fullmod.'/lib.php');
            // look for mod_grades() function - old grade book pulling function
            // to see if module supports grades, and check for event registration status
            $gradefunc = $mod.'_grades';
            // if this mod has grades, but grade_added event is not registered
            // then we need to pull grades into the new gradebook
            if (function_exists($gradefunc) && !event_is_registered($mod, $gradefunc)) {
                // get all instance of the mod
                $module = get_record('modules', 'name', $mod);
                if ($module && $modinstances = get_records('course_modules', 'module', $module->id)) {
                    foreach ($modinstances as $modinstance) {
                        // for each instance, call the xxx_grades() function
                        if ($grades = $gradefunc($modinstance->instance)) {
                            foreach ($grades->grades as $userid=>$usergrade) {                              
                                // make the grade_added eventdata
                                // missing grade event trigger
                                // trigger_event('grade_added', $eventdata);
                                unset($eventdata);
                                $eventdata->courseid =  $modinstance->course;
                                $eventdata->itemmodule = $mod;
                                $eventdata->iteminstance = $modinstance->instance;
                                $eventdata->gradetype = 0;
                                $eventdata->userid = $userid;
                                $eventdata->gradevalue = $usergrade;
                                trigger_event('grade_added', $eventdata);                             
                                                       
                            }
                        }
                    }
                }
            }
        }
    }
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


/*
 * Handles all grade_added and grade_updated events
 *
 * @param object $eventdata contains all the data for the event
 * @return boolean success
 *
 */
function grade_handler($eventdata) {

/// First let's make sure a grade_item exists for this grade
    $gradeitem = new grade_item($eventdata);
    
    if (empty($gradeitem->id)) {                      // Doesn't exist yet
        if (!$gradeitem->id = $gradeitem->insert()) { // Try to create a new item...
            debugging('Could not create a grade_item!');
            return false;
        }
    }

    $eventdata->itemid = $gradeitem->id;

/// Grade_item exists, now we can insert the new raw grade

    $rawgrade = new grade_grades_raw($eventdata); 

    if ($rawgrade->id) {
        $rawgrade->update($eventdata->gradevalue, 'event');
    } else {
        $rawgrade->insert();
    }
    
    // Check how it went

/// Are there other checks to do?

    return true;

}


?>

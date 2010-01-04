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
 * Library of interface functions and constants for module workshop
 *
 * All the core Moodle functions, neeeded to allow the module to work 
 * integrated in Moodle should be placed here.
 * All the workshop specific functions, needed to implement all the module 
 * logic, should go to locallib.php. This will help to save some memory when 
 * Moodle is performing actions across all modules.
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * The internal codes of the anonymity levels
 */
define('WORKSHOP_ANONYMITY_NONE',       0);     /* not anonymous */
define('WORKSHOP_ANONYMITY_AUTHORS',    1);     /* authors hidden from reviewers */
define('WORKSHOP_ANONYMITY_REVIEWERS',  2);     /* reviewers hidden from authors */
define('WORKSHOP_ANONYMITY_BOTH',       3);     /* fully anonymous */


/**
 * The internal codes of the example assessment modes
 */
define('WORKSHOP_EXAMPLES_VOLUNTARY',           0);
define('WORKSHOP_EXAMPLES_BEFORE_SUBMISSION',   1);
define('WORKSHOP_EXAMPLES_BEFORE_ASSESSMENT',   2);


/**
 * The internal codes of the required level of assessment similarity
 */
define('WORKSHOP_COMPARISON_VERYLOW',   0);     /* f = 1.00 */
define('WORKSHOP_COMPARISON_LOW',       1);     /* f = 1.67 */
define('WORKSHOP_COMPARISON_NORMAL',    2);     /* f = 2.50 */
define('WORKSHOP_COMPARISON_HIGH',      3);     /* f = 3.00 */
define('WORKSHOP_COMPARISON_VERYHIGH',  4);     /* f = 5.00 */


/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $workshop An object from the form in mod_form.php
 * @return int The id of the newly inserted workshop record
 */
function workshop_add_instance($workshop) {
    global $DB;

    $workshop->timecreated = time();
    $workshop->timemodified = $workshop->timecreated;

    return $DB->insert_record('workshop', $workshop);
}


/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $workshop An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function workshop_update_instance($workshop) {
    global $DB;

    $workshop->timemodified = time();
    $workshop->id = $workshop->instance;

    return $DB->update_record('workshop', $workshop);
}


/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function workshop_delete_instance($id) {
    global $DB;

    if (! $workshop = $DB->get_record('workshop', array('id' => $id))) {
        return false;
    }

    $result = true;

    # Delete any dependent records here #

    if (! $DB->delete_records('workshop', array('id' => $workshop->id))) {
        $result = false;
    }

    return $result;
}


/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @todo Finish documenting this function
 */
function workshop_user_outline($course, $user, $mod, $workshop) {
    $return = new stdClass;
    $return->time = 0;
    $return->info = '';
    return $return;
}


/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function workshop_user_complete($course, $user, $mod, $workshop) {
    return true;
}


/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in workshop activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function workshop_print_recent_activity($course, $isteacher, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}


/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function workshop_cron () {
    return true;
}


/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of workshop. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $workshopid ID of an instance of this module
 * @return mixed boolean/array of students
 */
function workshop_get_participants($workshopid) {
    return false;
}


/**
 * This function returns if a scale is being used by one workshop
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $workshopid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 */
function workshop_scale_used($workshopid, $scaleid) {
    $return = false;

    //$rec = get_record("workshop","id","$workshopid","scale","-$scaleid");
    //
    //if (!empty($rec) && !empty($scaleid)) {
    //    $return = true;
    //}

    return $return;
}


/**
 * Checks if scale is being used by any instance of workshop.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any workshop
 */
function workshop_scale_used_anywhere($scaleid) {
    if ($scaleid and record_exists('workshop', 'grade', -$scaleid)) {
        return true;
    } else {
        return false;
    }
}


/**
 * Execute post-install custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function workshop_install() {
    return true;
}


/**
 * Execute post-uninstall custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function workshop_uninstall() {
    return true;
}

////////////////////////////////////////////////////////////////////////////////
// Other functions needed by Moodle core follows. They can't be put into      //
// locallib.php because they are used by some core scripts (like modedit.php) //
// where locallib.php is not included.                                        //
////////////////////////////////////////////////////////////////////////////////

/**
 * Return an array of numeric values that can be used as maximum grades
 *
 * Used at several places where maximum grade for submission and grade for 
 * assessment are defined via a HTML select form element. By default it returns
 * an array 0, 1, 2, ..., 98, 99, 100.
 * 
 * @access public
 * @return array Array of integers
 */
function workshop_get_maxgrades() {

    $grades = array();
    for ($i=100; $i>=0; $i--) {
        $grades[$i] = $i;
    }
    return $grades;
}


/**
 * Return an array of possible numbers of assessments to be done
 *
 * Should always contain numbers 1, 2, 3, ... 10 and possibly others up to a reasonable value
 *
 * @return array Array of integers
 */
function workshop_get_numbers_of_assessments() {

    $options = array();
    $options[30] = 30;
    $options[20] = 20;
    $options[15] = 15;
    for ($i=10; $i>0; $i--) {
        $options[$i] = $i;
    }
    return $options;
}


/**
 * Return an array of possible values for weight of teacher assessment
 * 
 * @return array Array of integers 0, 1, 2, ..., 10
 */
function workshop_get_teacher_weights() {

    $weights = array();
    for ($i=10; $i>=0; $i--) {
        $weights[$i] = $i;
    }
    return $weights;
}


/**
 * Return an array of possible values of assessment dimension weight
 * 
 * @return array Array of integers 0, 1, 2, ..., 16
 */
function workshop_get_dimension_weights() {

    $weights = array();
    for ($i=16; $i>=0; $i--) {
        $weights[$i] = $i;
    }
    return $weights;
}


/**
 * Return an array of the localized grading strategy names
 *
 * @access public
 * $return array Array ['string' => 'string']
 */
function workshop_get_strategies() {

    $installed = get_list_of_plugins('mod/workshop/grading');
    $forms = array();
    foreach ($installed as $strategy) {
        $forms[$strategy] = get_string('strategy' . $strategy, 'workshop');
    }

    return $forms;
}


/**
 * Return an array of available anonymity modes
 *
 * @return array Array 'anonymity DB code'=>'anonymity mode name'
 */
function workshop_get_anonymity_modes() {
    
    $modes = array();
    $modes[WORKSHOP_ANONYMITY_NONE]      = get_string('anonymitynone', 'workshop');
    $modes[WORKSHOP_ANONYMITY_AUTHORS]   = get_string('anonymityauthors', 'workshop');
    $modes[WORKSHOP_ANONYMITY_REVIEWERS] = get_string('anonymityreviewers', 'workshop');
    $modes[WORKSHOP_ANONYMITY_BOTH]      = get_string('anonymityboth', 'workshop');

    return $modes;
}


/**
 * Return an array of available example assessment modes
 *
 * @return array Array 'mode DB code'=>'mode name'
 */
function workshop_get_example_modes() {

    $modes = array();
    $modes[WORKSHOP_EXAMPLES_VOLUNTARY]         = get_string('examplesvoluntary', 'workshop');
    $modes[WORKSHOP_EXAMPLES_BEFORE_SUBMISSION] = get_string('examplesbeforesubmission', 'workshop');
    $modes[WORKSHOP_EXAMPLES_BEFORE_ASSESSMENT] = get_string('examplesbeforeassessment', 'workshop');

    return $modes;
}


/**
 * Return array of assessment comparison levels
 *
 * The assessment comparison level influence how the grade for assessment is calculated.
 * Each object in the returned array provides information about the name of the level
 * and the value of the factor to be used in the calculation.
 * The structure of the returned array is
 * array[code int] of object (
 *                      ->name string,
 *                      ->value number,
 *                      )
 * where code if the integer code that is actually stored in the database.
 *
 * @return array Array of objects
 */
function workshop_get_comparison_levels() {

    $levels = array();

    $levels[WORKSHOP_COMPARISON_VERYHIGH] = new stdClass;
    $levels[WORKSHOP_COMPARISON_VERYHIGH]->name = get_string('comparisonveryhigh', 'workshop');
    $levels[WORKSHOP_COMPARISON_VERYHIGH]->value = 5.00;

    $levels[WORKSHOP_COMPARISON_HIGH] = new stdClass;
    $levels[WORKSHOP_COMPARISON_HIGH]->name = get_string('comparisonhigh', 'workshop');
    $levels[WORKSHOP_COMPARISON_HIGH]->value = 3.00;

    $levels[WORKSHOP_COMPARISON_NORMAL] = new stdClass;
    $levels[WORKSHOP_COMPARISON_NORMAL]->name = get_string('comparisonnormal', 'workshop');
    $levels[WORKSHOP_COMPARISON_NORMAL]->value = 2.50;

    $levels[WORKSHOP_COMPARISON_LOW] = new stdClass;
    $levels[WORKSHOP_COMPARISON_LOW]->name = get_string('comparisonlow', 'workshop');
    $levels[WORKSHOP_COMPARISON_LOW]->value = 1.67;

    $levels[WORKSHOP_COMPARISON_VERYLOW] = new stdClass;
    $levels[WORKSHOP_COMPARISON_VERYLOW]->name = get_string('comparisonverylow', 'workshop');
    $levels[WORKSHOP_COMPARISON_VERYLOW]->value = 1.00;

    return $levels;
}

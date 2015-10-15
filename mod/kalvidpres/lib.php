<?php
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
 * Kaltura video presentation main library file.
 *
 * @package    mod_kalvidpres
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $kalvidpres An object from the form in mod_form.php
 * @return int The id of the newly inserted kalvidassign record
 */
function kalvidpres_add_instance($kalvidpres) {
    global $DB, $CFG;
    require_once($CFG->dirroot.'/local/kaltura/locallib.php');

    $kalvidpres->timecreated = time();
    $kalvidpres->source = local_kaltura_build_kaf_uri($kalvidpres->source);
    $kalvidpres->id =  $DB->insert_record('kalvidpres', $kalvidpres);

    return $kalvidpres->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $kalvidpres An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function kalvidpres_update_instance($kalvidpres) {
    global $DB, $CFG;
    require_once($CFG->dirroot.'/local/kaltura/locallib.php');

    $kalvidpres->timemodified = time();
    $kalvidpres->id = $kalvidpres->instance;
    $kalvidpres->source = local_kaltura_build_kaf_uri($kalvidpres->source);

    $updated = $DB->update_record('kalvidpres', $kalvidpres);

    return $updated;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function kalvidpres_delete_instance($id) {
    global $DB;

    if (! $kalvidpres = $DB->get_record('kalvidpres', array('id' => $id))) {
        return false;
    }

    $DB->delete_records('kalvidpres', array('id' => $kalvidpres->id));

    return true;
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
function kalvidpres_user_outline($course, $user, $mod, $kalvidpres) {
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
function kalvidpres_user_complete($course, $user, $mod, $kalvidpres) {
    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in kalvidpres activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function kalvidpres_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 */
function kalvidpres_cron () {
    return false;
}

/**
 * Must return an array of users who are participants for a given instance
 * of kalvidpres. Must include every user involved in the instance, independient
 * of his role (student, teacher, admin...). The returned objects must contain
 * at least id property. See other modules as example.
 *
 * @param int $kalvidpresid ID of an instance of this module
 * @return boolean|array false if no participants, array of objects otherwise
 */
function kalvidpres_get_participants($kalvidpresid) {
    return false;
}

/**
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, null if doesn't know
 */
function kalvidpres_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_GROUPMEMBERSONLY:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return false;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}

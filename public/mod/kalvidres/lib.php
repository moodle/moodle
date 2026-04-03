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
 * Kaltura video resource library script.
 *
 * @package    mod_kalvidres
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
 * @param object $kalvidres An object from the form in mod_form.php
 * @return int The id of the newly inserted kalvidassign record
 */
function kalvidres_add_instance($kalvidres) {
    global $DB, $CFG;
    require_once($CFG->dirroot.'/local/kaltura/locallib.php');

    $kalvidres->timecreated = time();
    $kalvidres->source = local_kaltura_build_kaf_uri($kalvidres->source);
    $kalvidres->id =  $DB->insert_record('kalvidres', $kalvidres);

    // add timeline reminder event if requested by user
    $completionexpected = !empty($kalvidres->completionexpected) ? $kalvidres->completionexpected : null;
    \core_completion\api::update_completion_date_event($kalvidres->coursemodule, 'kalvidres', $kalvidres->id, $completionexpected);

    return $kalvidres->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $kalvidres An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function kalvidres_update_instance($kalvidres) {
    global $DB, $CFG;
    require_once($CFG->dirroot.'/local/kaltura/locallib.php');

    $kalvidres->timemodified = time();
    $kalvidres->id = $kalvidres->instance;
    $kalvidres->source = local_kaltura_build_kaf_uri($kalvidres->source);
    $updated = $DB->update_record('kalvidres', $kalvidres);

    // update timeline reminder event if requested by user
    $completionexpected = !empty($kalvidres->completionexpected) ? $kalvidres->completionexpected : null;
    \core_completion\api::update_completion_date_event($kalvidres->coursemodule, 'kalvidres', $kalvidres->id, $completionexpected);

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
function kalvidres_delete_instance($id) {
    global $DB;

    if (! $kalvidres = $DB->get_record('kalvidres', array('id' => $id))) {
        return false;
    }

    // delete timeline reminder event if set
    $cm = get_coursemodule_from_instance('kalvidres', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'kalvidres', $kalvidres->id, null);

    $DB->delete_records('kalvidres', array('id' => $kalvidres->id));

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
function kalvidres_user_outline($course, $user, $mod, $kalvidres) {
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
function kalvidres_user_complete($course, $user, $mod, $kalvidres) {
    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in kalvidres activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function kalvidres_print_recent_activity($course, $viewfullnames, $timestart) {
    // TODO: finish this function
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 */
function kalvidres_cron () {
    return false;
}

/**
 * Must return an array of users who are participants for a given instance
 * of kalvidres. Must include every user involved in the instance, independient
 * of his role (student, teacher, admin...). The returned objects must contain
 * at least id property. See other modules as example.
 *
 * @param int $kalvidresid ID of an instance of this module
 * @return boolean|array false if no participants, array of objects otherwise
 */
function kalvidres_get_participants($kalvidresid) {
    // TODO: finish this function
    return false;
}

/**
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, null if doesn't know
 */
function kalvidres_supports($feature) {
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
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_CONTENT;
        default:
            return null;
    }
}

/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @param int $userid User id to use for all capability checks, etc. Set to 0 for current user (default).
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function mod_kalvidres_core_calendar_provide_event_action(calendar_event $event,
                                                          \core_calendar\action_factory $factory,
                                                          int $userid = 0) {
    global $USER;

    if (!$userid) {
        $userid = $USER->id;
    }

    $cm = get_fast_modinfo($event->courseid, $userid)->instances['kalvidres'][$event->instance];

    if (!$cm->uservisible) {
        // The module is not visible to the user for any reason.
        return null;
    }

    $completion = new \completion_info($cm->get_course());

    $completiondata = $completion->get_data($cm, false, $userid);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    return $factory->create_instance(
        get_string('view'),
        new \moodle_url('/mod/kalvidres/view.php', array('id' => $cm->id)),
        1,
        true
    );
}
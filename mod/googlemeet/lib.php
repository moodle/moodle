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
 * Library of interface functions and constants.
 *
 * @package     mod_googlemeet
 * @copyright   2020 Rone Santos <ronefel@hotmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
function googlemeet_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:
            return false;
        case FEATURE_GROUPINGS:
            return false;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the mod_googlemeet into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $googlemeet An object from the form.
 * @param mod_googlemeet_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function googlemeet_add_instance($googlemeet, $mform = null) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/mod/googlemeet/locallib.php');

    if (isset($googlemeet->days)) {
        $googlemeet->days = json_encode($googlemeet->days);
    }

    if (isset($googlemeet->url)) {
        $url = googlemeet_clear_url($googlemeet->url);
        if ($url) {
            $googlemeet->url = $url;
        }
    }

    $googlemeet->timemodified = time();

    if (!$googlemeet->id = $DB->insert_record('googlemeet', $googlemeet)) {
        return false;
    }

    if (isset($googlemeet->days)) {
        $googlemeet->days = json_decode($googlemeet->days);
    }
    $events = googlemeet_construct_events_data_for_add($googlemeet);

    googlemeet_set_events($events);

    return $googlemeet->id;
}

/**
 * Updates an instance of the mod_googlemeet in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $googlemeet An object from the form in mod_form.php.
 * @param mod_googlemeet_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function googlemeet_update_instance($googlemeet, $mform = null) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/mod/googlemeet/locallib.php');

    $googlemeet->id = $googlemeet->instance;

    if (isset($googlemeet->addmultiply)) {
        if (isset($googlemeet->days)) {
            $googlemeet->days = json_encode($googlemeet->days);
        }
    } else {
        $googlemeet->addmultiply = 0;
        $googlemeet->days = null;
        $googlemeet->eventenddate = $googlemeet->eventdate;
        $googlemeet->period = null;
    }

    if (isset($googlemeet->url)) {
        $url = googlemeet_clear_url($googlemeet->url);
        if ($url) {
            $googlemeet->url = $url;
        }
    }

    $googlemeet->timemodified = time();

    $googlemeetupdated = $DB->update_record('googlemeet', $googlemeet);

    if (isset($googlemeet->days)) {
        $googlemeet->days = json_decode($googlemeet->days);
    }
    $events = googlemeet_construct_events_data_for_add($googlemeet);

    googlemeet_set_events($events);

    return $googlemeetupdated;
}

/**
 * Removes an instance of the mod_googlemeet from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function googlemeet_delete_instance($id) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/mod/googlemeet/locallib.php');

    $exists = $DB->get_record('googlemeet', array('id' => $id));
    if (!$exists) {
        return false;
    }

    googlemeet_delete_events($id);

    $DB->delete_records('googlemeet_recordings', ['googlemeetid' => $id]);

    $DB->delete_records('googlemeet', array('id' => $id));

    return true;
}

/**
 * Add a get_coursemodule_info function in case any feedback type wants to add 'extra' information
 * for the course (see resource).
 *
 * Given a course_module object, this function returns any "extra" information that may be needed
 * when printing this activity in a course listing.  See get_array_of_activities() in course/lib.php.
 *
 * @param stdClass $coursemodule The coursemodule object (record).
 * @return cached_cm_info An object on information that the courses
 *                        will know about (most noticeably, an icon).
 */
function googlemeet_get_coursemodule_info($coursemodule) {
    global $CFG, $DB;

    if (!$googlemeet = $DB->get_record(
        'googlemeet',
        ['id' => $coursemodule->instance],
        'id, name, url, intro, introformat'
    )) {
        return null;
    }

    $info = new cached_cm_info();
    $info->name = $googlemeet->name;

    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $info->content = format_module_intro('googlemeet', $googlemeet, $coursemodule->id, false);
    }

    return $info;
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param  stdClass $googlemeet googlemeet object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 * @since Moodle 3.0
 */
function googlemeet_view($googlemeet, $course, $cm, $context) {

    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $googlemeet->id
    );

    $event = \mod_googlemeet\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('googlemeet', $googlemeet);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * Returns a list of recordings from Google Meet
 *
 * @param array $params Array of parameters to a query.
 * @return stdClass $formattedrecordings    List of recordings
 */
function googlemeet_list_recordings($params) {
    global $DB;

    $recordings = $DB->get_records(
        'googlemeet_recordings',
        $params,
        'createdtime DESC',
        'id,googlemeetid,name,createdtime,duration,webviewlink,visible'
    );

    $formattedrecordings = [];
    foreach ($recordings as $recording) {
        $recording->createdtimeformatted = userdate($recording->createdtime);

        array_push($formattedrecordings, $recording);
    }

    return $formattedrecordings;
}

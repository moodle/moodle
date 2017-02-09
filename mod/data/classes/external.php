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
 * Database module external API
 *
 * @package    mod_data
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot . "/mod/data/locallib.php");

use mod_data\external\database_summary_exporter;

/**
 * Database module external functions
 *
 * @package    mod_data
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */
class mod_data_external extends external_api {

    /**
     * Describes the parameters for get_databases_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function get_databases_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'course id', VALUE_REQUIRED),
                    'Array of course ids', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Returns a list of databases in a provided list of courses,
     * if no list is provided all databases that the user can view will be returned.
     *
     * @param array $courseids the course ids
     * @return array the database details
     * @since Moodle 2.9
     */
    public static function get_databases_by_courses($courseids = array()) {
        global $PAGE;

        $params = self::validate_parameters(self::get_databases_by_courses_parameters(), array('courseids' => $courseids));
        $warnings = array();

        $mycourses = array();
        if (empty($params['courseids'])) {
            $mycourses = enrol_get_my_courses();
            $params['courseids'] = array_keys($mycourses);
        }

        // Array to store the databases to return.
        $arrdatabases = array();

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($dbcourses, $warnings) = external_util::validate_courses($params['courseids'], $mycourses);

            // Get the databases in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $databases = get_all_instances_in_courses("data", $dbcourses);

            foreach ($databases as $database) {

                $context = context_module::instance($database->coursemodule);
                // Remove fields added by get_all_instances_in_courses.
                unset($database->coursemodule, $database->section, $database->visible, $database->groupmode, $database->groupingid);

                // This information should be only available if the user can see the database entries.
                if (!has_capability('mod/data:viewentry', $context)) {
                    $fields = array('comments', 'timeavailablefrom', 'timeavailableto', 'timeviewfrom',
                                    'timeviewto', 'requiredentries', 'requiredentriestoview', 'maxentries', 'rssarticles',
                                    'singletemplate', 'listtemplate', 'listtemplateheader', 'listtemplatefooter', 'addtemplate',
                                    'rsstemplate', 'rsstitletemplate', 'csstemplate', 'jstemplate', 'asearchtemplate', 'approval',
                                    'manageapproved', 'defaultsort', 'defaultsortdir');

                    foreach ($fields as $field) {
                        unset($database->{$field});
                    }
                }

                // Check additional permissions for returning optional private settings.
                // I avoid intentionally to use can_[add|update]_moduleinfo.
                if (!has_capability('moodle/course:manageactivities', $context)) {

                    $fields = array('scale', 'assessed', 'assesstimestart', 'assesstimefinish', 'editany', 'notification',
                                    'timemodified');

                    foreach ($fields as $field) {
                        unset($database->{$field});
                    }
                }
                $exporter = new database_summary_exporter($database, array('context' => $context));
                $arrdatabases[] = $exporter->export($PAGE->get_renderer('core'));
            }
        }

        $result = array();
        $result['databases'] = $arrdatabases;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_databases_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 2.9
     */
    public static function get_databases_by_courses_returns() {

        return new external_single_structure(
            array(
                'databases' => new external_multiple_structure(
                    database_summary_exporter::get_read_structure()
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Utility function for validating a database.
     *
     * @param int $databaseid database instance id
     * @return array array containing the database object, course, context and course module objects
     * @since  Moodle 3.3
     */
    protected static function validate_database($databaseid) {
        global $DB;

        // Request and permission validation.
        $database = $DB->get_record('data', array('id' => $databaseid), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($database, 'data');

        $context = context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('mod/data:viewentry', $context);

        return array($database, $course, $cm, $context);
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function view_database_parameters() {
        return new external_function_parameters(
            array(
                'databaseid' => new external_value(PARAM_INT, 'data instance id')
            )
        );
    }

    /**
     * Simulate the data/view.php web interface page: trigger events, completion, etc...
     *
     * @param int $databaseid the data instance id
     * @return array of warnings and status result
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function view_database($databaseid) {

        $params = self::validate_parameters(self::view_database_parameters(), array('databaseid' => $databaseid));
        $warnings = array();

        list($database, $course, $cm, $context) = self::validate_database($params['databaseid']);

        // Call the data/lib API.
        data_view($database, $course, $cm, $context);

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.3
     */
    public static function view_database_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_data_access_information_parameters() {
        return new external_function_parameters(
            array(
                'databaseid' => new external_value(PARAM_INT, 'Database instance id.'),
                'groupid' => new external_value(PARAM_INT, 'Group id, 0 means that the function will determine the user group.',
                                                   VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Return access information for a given database.
     *
     * @param int $databaseid the database instance id
     * @param int $groupid (optional) group id, 0 means that the function will determine the user group
     * @return array of warnings and access information
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function get_data_access_information($databaseid, $groupid = 0) {

        $params = array('databaseid' => $databaseid, 'groupid' => $groupid);
        $params = self::validate_parameters(self::get_data_access_information_parameters(), $params);
        $warnings = array();

        list($database, $course, $cm, $context) = self::validate_database($params['databaseid']);

        $result = array(
            'warnings' => $warnings
        );

        $groupmode = groups_get_activity_groupmode($cm);
        if (!empty($params['groupid'])) {
            $groupid = $params['groupid'];
            // Determine is the group is visible to user.
            if (!groups_group_visible($groupid, $course, $cm)) {
                throw new moodle_exception('notingroup');
            }
        } else {
            // Check to see if groups are being used here.
            if ($groupmode) {
                $groupid = groups_get_activity_group($cm);
                // Determine is the group is visible to user (this is particullary for the group 0 -> all groups).
                if (!groups_group_visible($groupid, $course, $cm)) {
                    throw new moodle_exception('notingroup');
                }
            } else {
                $groupid = 0;
            }
        }
        // Group related information.
        $result['groupid'] = $groupid;
        $result['canaddentry'] = data_user_can_add_entry($database, $groupid, $groupmode, $context);

        // Now capabilities.
        $result['canmanageentries'] = has_capability('mod/data:manageentries', $context);
        $result['canapprove'] = has_capability('mod/data:approve', $context);

        // Now time access restrictions.
        list($result['timeavailable'], $warnings) = data_get_time_availability_status($database, $result['canmanageentries']);

        // Other information.
        $result['numentries'] = data_numentries($database);
        $result['entrieslefttoadd'] = data_get_entries_left_to_add($database, $result['numentries'], $result['canmanageentries']);
        $result['entrieslefttoview'] = data_get_entries_left_to_view($database, $result['numentries'], $result['canmanageentries']);
        $result['inreadonlyperiod'] = data_in_readonly_period($database);

        return $result;
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     * @since Moodle 3.3
     */
    public static function get_data_access_information_returns() {
        return new external_single_structure(
            array(
                'groupid' => new external_value(PARAM_INT, 'User current group id (calculated)'),
                'canaddentry' => new external_value(PARAM_BOOL, 'Whether the user can add entries or not.'),
                'canmanageentries' => new external_value(PARAM_BOOL, 'Whether the user can manage entries or not.'),
                'canapprove' => new external_value(PARAM_BOOL, 'Whether the user can approve entries or not.'),
                'timeavailable' => new external_value(PARAM_BOOL, 'Whether the database is available or not by time restrictions.'),
                'inreadonlyperiod' => new external_value(PARAM_BOOL, 'Whether the database is in read mode only.'),
                'numentries' => new external_value(PARAM_INT, 'The number of entries the current user added.'),
                'entrieslefttoadd' => new external_value(PARAM_INT, 'The number of entries left to complete the activity.'),
                'entrieslefttoview' => new external_value(PARAM_INT, 'The number of entries left to view other users entries.'),
                'warnings' => new external_warnings()
            )
        );
    }
}

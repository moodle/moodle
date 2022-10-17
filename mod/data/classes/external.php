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
use mod_data\external\record_exporter;
use mod_data\external\content_exporter;
use mod_data\external\field_exporter;
use mod_data\manager;

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
                $data = $exporter->export($PAGE->get_renderer('core'));
                $data->name = external_format_string($data->name, $context);
                $arrdatabases[] = $data;
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
        $manager = manager::create_from_coursemodule($cm);
        $manager->set_module_viewed($course);

        $result = [
            'status' => true,
            'warnings' => $warnings,
        ];
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

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_entries_parameters() {
        return new external_function_parameters(
            array(
                'databaseid' => new external_value(PARAM_INT, 'data instance id'),
                'groupid' => new external_value(PARAM_INT, 'Group id, 0 means that the function will determine the user group',
                                                   VALUE_DEFAULT, 0),
                'returncontents' => new external_value(PARAM_BOOL, 'Whether to return contents or not. This will return each entry
                                                        raw contents and the complete list view (using the template).',
                                                        VALUE_DEFAULT, false),
                'sort' => new external_value(PARAM_INT, 'Sort the records by this field id, reserved ids are:
                                                0: timeadded
                                                -1: firstname
                                                -2: lastname
                                                -3: approved
                                                -4: timemodified.
                                                Empty for using the default database setting.', VALUE_DEFAULT, null),
                'order' => new external_value(PARAM_ALPHA, 'The direction of the sorting: \'ASC\' or \'DESC\'.
                                                Empty for using the default database setting.', VALUE_DEFAULT, null),
                'page' => new external_value(PARAM_INT, 'The page of records to return.', VALUE_DEFAULT, 0),
                'perpage' => new external_value(PARAM_INT, 'The number of records to return per page', VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Return access information for a given feedback
     *
     * @param int $databaseid       the data instance id
     * @param int $groupid          (optional) group id, 0 means that the function will determine the user group
     * @param bool $returncontents  Whether to return the entries contents or not
     * @param str $sort             sort by this field
     * @param int $order            the direction of the sorting
     * @param int $page             page of records to return
     * @param int $perpage          number of records to return per page
     * @return array of warnings and the entries
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function get_entries($databaseid, $groupid = 0, $returncontents = false, $sort = null, $order = null,
            $page = 0, $perpage = 0) {
        global $PAGE, $DB;

        $params = array('databaseid' => $databaseid, 'groupid' => $groupid, 'returncontents' => $returncontents ,
                        'sort' => $sort, 'order' => $order, 'page' => $page, 'perpage' => $perpage);
        $params = self::validate_parameters(self::get_entries_parameters(), $params);
        $warnings = array();

        if (!empty($params['order'])) {
            $params['order'] = strtoupper($params['order']);
            if ($params['order'] != 'ASC' && $params['order'] != 'DESC') {
                throw new invalid_parameter_exception('Invalid value for sortdirection parameter (value: ' . $params['order'] . ')');
            }
        }

        list($database, $course, $cm, $context) = self::validate_database($params['databaseid']);
        // Check database is open in time.
        data_require_time_available($database, null, $context);

        if (!empty($params['groupid'])) {
            $groupid = $params['groupid'];
            // Determine is the group is visible to user.
            if (!groups_group_visible($groupid, $course, $cm)) {
                throw new moodle_exception('notingroup');
            }
        } else {
            // Check to see if groups are being used here.
            if ($groupmode = groups_get_activity_groupmode($cm)) {
                // We don't need to validate a possible groupid = 0 since it would be handled by data_search_entries.
                $groupid = groups_get_activity_group($cm);
            } else {
                $groupid = 0;
            }
        }

        $manager = manager::create_from_instance($database);

        list($records, $maxcount, $totalcount, $page, $nowperpage, $sort, $mode) =
            data_search_entries($database, $cm, $context, 'list', $groupid, '', $params['sort'], $params['order'],
                $params['page'], $params['perpage']);

        $entries = [];
        $contentsids = [];  // Store here the content ids of the records returned.
        foreach ($records as $record) {
            $user = user_picture::unalias($record, null, 'userid');
            $related = array('context' => $context, 'database' => $database, 'user' => $user);

            $contents = $DB->get_records('data_content', array('recordid' => $record->id));
            $contentsids = array_merge($contentsids, array_keys($contents));
            if ($params['returncontents']) {
                $related['contents'] = $contents;
            } else {
                $related['contents'] = null;
            }

            $exporter = new record_exporter($record, $related);
            $entries[] = $exporter->export($PAGE->get_renderer('core'));
        }

        // Retrieve total files size for the records retrieved.
        $totalfilesize = 0;
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_data', 'content');
        foreach ($files as $file) {
            if ($file->is_directory() || !in_array($file->get_itemid(), $contentsids)) {
                continue;
            }
            $totalfilesize += $file->get_filesize();
        }

        $result = array(
            'entries' => $entries,
            'totalcount' => $totalcount,
            'totalfilesize' => $totalfilesize,
            'warnings' => $warnings
        );

        // Check if we should return the list rendered.
        if ($params['returncontents']) {
            $parser = $manager->get_template('listtemplate', ['page' => $page]);
            $result['listviewcontents'] = $parser->parse_entries($records);
        }

        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.3
     */
    public static function get_entries_returns() {
        return new external_single_structure(
            array(
                'entries' => new external_multiple_structure(
                    record_exporter::get_read_structure()
                ),
                'totalcount' => new external_value(PARAM_INT, 'Total count of records.'),
                'totalfilesize' => new external_value(PARAM_INT, 'Total size (bytes) of the files included in the records.'),
                'listviewcontents' => new external_value(PARAM_RAW, 'The list view contents as is rendered in the site.',
                                                            VALUE_OPTIONAL),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_entry_parameters() {
        return new external_function_parameters(
            array(
                'entryid' => new external_value(PARAM_INT, 'record entry id'),
                'returncontents' => new external_value(PARAM_BOOL, 'Whether to return contents or not.', VALUE_DEFAULT, false),
            )
        );
    }

    /**
     * Return one entry record from the database, including contents optionally.
     *
     * @param int $entryid          the record entry id id
     * @param bool $returncontents  whether to return the entries contents or not
     * @return array of warnings and the entries
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function get_entry($entryid, $returncontents = false) {
        global $PAGE, $DB;

        $params = array('entryid' => $entryid, 'returncontents' => $returncontents);
        $params = self::validate_parameters(self::get_entry_parameters(), $params);
        $warnings = array();

        $record = $DB->get_record('data_records', array('id' => $params['entryid']), '*', MUST_EXIST);
        list($database, $course, $cm, $context) = self::validate_database($record->dataid);

        // Check database is open in time.
        $canmanageentries = has_capability('mod/data:manageentries', $context);
        data_require_time_available($database, $canmanageentries);

        $manager = manager::create_from_instance($database);

        if ($record->groupid != 0) {
            if (!groups_group_visible($record->groupid, $course, $cm)) {
                throw new moodle_exception('notingroup');
            }
        }

        // Check correct record entry. Group check was done before.
        if (!data_can_view_record($database, $record, $record->groupid, $canmanageentries)) {
            throw new moodle_exception('notapprovederror', 'data');
        }

        $related = array('context' => $context, 'database' => $database, 'user' => null);
        if ($params['returncontents']) {
            $related['contents'] = $DB->get_records('data_content', array('recordid' => $record->id));
        } else {
            $related['contents'] = null;
        }
        $exporter = new record_exporter($record, $related);
        $entry = $exporter->export($PAGE->get_renderer('core'));

        $result = array(
            'entry' => $entry,
            'ratinginfo' => \core_rating\external\util::get_rating_info($database, $context, 'mod_data', 'entry', array($record)),
            'warnings' => $warnings
        );
        // Check if we should return the entry rendered.
        if ($params['returncontents']) {
            $records = [$record];
            $parser = $manager->get_template('singletemplate');
            $result['entryviewcontents'] = $parser->parse_entries($records);
        }

        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.3
     */
    public static function get_entry_returns() {
        return new external_single_structure(
            array(
                'entry' => record_exporter::get_read_structure(),
                'entryviewcontents' => new external_value(PARAM_RAW, 'The entry as is rendered in the site.', VALUE_OPTIONAL),
                'ratinginfo' => \core_rating\external\util::external_ratings_structure(),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_fields_parameters() {
        return new external_function_parameters(
            array(
                'databaseid' => new external_value(PARAM_INT, 'Database instance id.'),
            )
        );
    }

    /**
     * Return the list of configured fields for the given database.
     *
     * @param int $databaseid the database id
     * @return array of warnings and the fields
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function get_fields($databaseid) {
        global $PAGE;

        $params = array('databaseid' => $databaseid);
        $params = self::validate_parameters(self::get_fields_parameters(), $params);
        $fields = $warnings = array();

        list($database, $course, $cm, $context) = self::validate_database($params['databaseid']);

        // Check database is open in time.
        $canmanageentries = has_capability('mod/data:manageentries', $context);
        data_require_time_available($database, $canmanageentries);

        $fieldinstances = data_get_field_instances($database);

        foreach ($fieldinstances as $fieldinstance) {
            $record = $fieldinstance->field;
            // Now get the configs the user can see with his current permissions.
            $configs = $fieldinstance->get_config_for_external();
            foreach ($configs as $name => $value) {
                // Overwrite.
                $record->{$name} = $value;
            }

            $exporter = new field_exporter($record, array('context' => $context));
            $fields[] = $exporter->export($PAGE->get_renderer('core'));
        }

        $result = array(
            'fields' => $fields,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.3
     */
    public static function get_fields_returns() {
        return new external_single_structure(
            array(
                'fields' => new external_multiple_structure(
                    field_exporter::get_read_structure()
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function search_entries_parameters() {
        return new external_function_parameters(
            array(
                'databaseid' => new external_value(PARAM_INT, 'data instance id'),
                'groupid' => new external_value(PARAM_INT, 'Group id, 0 means that the function will determine the user group',
                                                   VALUE_DEFAULT, 0),
                'returncontents' => new external_value(PARAM_BOOL, 'Whether to return contents or not.', VALUE_DEFAULT, false),
                'search' => new external_value(PARAM_NOTAGS, 'search string (empty when using advanced)', VALUE_DEFAULT, ''),
                'advsearch' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_ALPHANUMEXT, 'Field key for search.
                                                            Use fn or ln for first or last name'),
                            'value' => new external_value(PARAM_RAW, 'JSON encoded value for search'),
                        )
                    ), 'Advanced search', VALUE_DEFAULT, array()
                ),
                'sort' => new external_value(PARAM_INT, 'Sort the records by this field id, reserved ids are:
                                                0: timeadded
                                                -1: firstname
                                                -2: lastname
                                                -3: approved
                                                -4: timemodified.
                                                Empty for using the default database setting.', VALUE_DEFAULT, null),
                'order' => new external_value(PARAM_ALPHA, 'The direction of the sorting: \'ASC\' or \'DESC\'.
                                                Empty for using the default database setting.', VALUE_DEFAULT, null),
                'page' => new external_value(PARAM_INT, 'The page of records to return.', VALUE_DEFAULT, 0),
                'perpage' => new external_value(PARAM_INT, 'The number of records to return per page', VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Return access information for a given feedback
     *
     * @param int $databaseid       the data instance id
     * @param int $groupid          (optional) group id, 0 means that the function will determine the user group
     * @param bool $returncontents  whether to return contents or not
     * @param str $search           search text
     * @param array $advsearch      advanced search data
     * @param str $sort             sort by this field
     * @param int $order            the direction of the sorting
     * @param int $page             page of records to return
     * @param int $perpage          number of records to return per page
     * @return array of warnings and the entries
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function search_entries($databaseid, $groupid = 0, $returncontents = false, $search = '', $advsearch = [],
            $sort = null, $order = null, $page = 0, $perpage = 0) {
        global $PAGE, $DB;

        $params = array('databaseid' => $databaseid, 'groupid' => $groupid, 'returncontents' => $returncontents, 'search' => $search,
                        'advsearch' => $advsearch, 'sort' => $sort, 'order' => $order, 'page' => $page, 'perpage' => $perpage);
        $params = self::validate_parameters(self::search_entries_parameters(), $params);
        $warnings = array();

        if (!empty($params['order'])) {
            $params['order'] = strtoupper($params['order']);
            if ($params['order'] != 'ASC' && $params['order'] != 'DESC') {
                throw new invalid_parameter_exception('Invalid value for sortdirection parameter (value: ' . $params['order'] . ')');
            }
        }

        list($database, $course, $cm, $context) = self::validate_database($params['databaseid']);
        // Check database is open in time.
        data_require_time_available($database, null, $context);

        $manager = manager::create_from_instance($database);

        if (!empty($params['groupid'])) {
            $groupid = $params['groupid'];
            // Determine is the group is visible to user.
            if (!groups_group_visible($groupid, $course, $cm)) {
                throw new moodle_exception('notingroup');
            }
        } else {
            // Check to see if groups are being used here.
            if ($groupmode = groups_get_activity_groupmode($cm)) {
                // We don't need to validate a possible groupid = 0 since it would be handled by data_search_entries.
                $groupid = groups_get_activity_group($cm);
            } else {
                $groupid = 0;
            }
        }

        if (!empty($params['advsearch'])) {
            $advanced = true;
            $defaults = [];
            $fn = $ln = ''; // Defaults for first and last name.
            // Force defaults for advanced search.
            foreach ($params['advsearch'] as $adv) {
                if ($adv['name'] == 'fn') {
                    $fn = json_decode($adv['value']);
                    continue;
                }
                if ($adv['name'] == 'ln') {
                    $ln = json_decode($adv['value']);
                    continue;
                }
                $defaults[$adv['name']] = json_decode($adv['value']);
            }
            list($searcharray, $params['search']) = data_build_search_array($database, false, [], $defaults, $fn, $ln);
        } else {
            $advanced = null;
            $searcharray = null;
        }

        list($records, $maxcount, $totalcount, $page, $nowperpage, $sort, $mode) =
            data_search_entries($database, $cm, $context, 'list', $groupid, $params['search'], $params['sort'], $params['order'],
                $params['page'], $params['perpage'], $advanced, $searcharray);

        $entries = [];
        foreach ($records as $record) {
            $user = user_picture::unalias($record, null, 'userid');
            $related = array('context' => $context, 'database' => $database, 'user' => $user);
            if ($params['returncontents']) {
                $related['contents'] = $DB->get_records('data_content', array('recordid' => $record->id));
            } else {
                $related['contents'] = null;
            }

            $exporter = new record_exporter($record, $related);
            $entries[] = $exporter->export($PAGE->get_renderer('core'));
        }

        $result = array(
            'entries' => $entries,
            'totalcount' => $totalcount,
            'maxcount' => $maxcount,
            'warnings' => $warnings
        );

        // Check if we should return the list rendered.
        if ($params['returncontents']) {
            $parser = $manager->get_template('listtemplate', ['page' => $page]);
            $result['listviewcontents'] = $parser->parse_entries($records);
        }

        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.3
     */
    public static function search_entries_returns() {
        return new external_single_structure(
            array(
                'entries' => new external_multiple_structure(
                    record_exporter::get_read_structure()
                ),
                'totalcount' => new external_value(PARAM_INT, 'Total count of records returned by the search.'),
                'maxcount' => new external_value(PARAM_INT, 'Total count of records that the user could see in the database
                    (if all the search criterias were removed).', VALUE_OPTIONAL),
                'listviewcontents' => new external_value(PARAM_RAW, 'The list view contents as is rendered in the site.',
                                                            VALUE_OPTIONAL),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function approve_entry_parameters() {
        return new external_function_parameters(
            array(
                'entryid' => new external_value(PARAM_INT, 'Record entry id.'),
                'approve' => new external_value(PARAM_BOOL, 'Whether to approve (true) or unapprove the entry.',
                                                VALUE_DEFAULT, true),
            )
        );
    }

    /**
     * Approves or unapproves an entry.
     *
     * @param int $entryid          the record entry id id
     * @param bool $approve         whether to approve (true) or unapprove the entry
     * @return array of warnings and the entries
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function approve_entry($entryid, $approve = true) {
        global $PAGE, $DB;

        $params = array('entryid' => $entryid, 'approve' => $approve);
        $params = self::validate_parameters(self::approve_entry_parameters(), $params);
        $warnings = array();

        $record = $DB->get_record('data_records', array('id' => $params['entryid']), '*', MUST_EXIST);
        list($database, $course, $cm, $context) = self::validate_database($record->dataid);
        // Check database is open in time.
        data_require_time_available($database, null, $context);
        // Check specific capabilities.
        require_capability('mod/data:approve', $context);

        data_approve_entry($record->id, $params['approve']);

        $result = array(
            'status' => true,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.3
     */
    public static function approve_entry_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function delete_entry_parameters() {
        return new external_function_parameters(
            array(
                'entryid' => new external_value(PARAM_INT, 'Record entry id.'),
            )
        );
    }

    /**
     * Deletes an entry.
     *
     * @param int $entryid the record entry id
     * @return array of warnings success status
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function delete_entry($entryid) {
        global $PAGE, $DB;

        $params = array('entryid' => $entryid);
        $params = self::validate_parameters(self::delete_entry_parameters(), $params);
        $warnings = array();

        $record = $DB->get_record('data_records', array('id' => $params['entryid']), '*', MUST_EXIST);
        list($database, $course, $cm, $context) = self::validate_database($record->dataid);

        if (data_user_can_manage_entry($record, $database, $context)) {
            data_delete_record($record->id, $database, $course->id, $cm->id);
        } else {
            throw new moodle_exception('noaccess', 'data');
        }

        $result = array(
            'status' => true,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.3
     */
    public static function delete_entry_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'Always true. If we see this field it means that the entry was deleted.'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function add_entry_parameters() {
        return new external_function_parameters(
            array(
                'databaseid' => new external_value(PARAM_INT, 'data instance id'),
                'groupid' => new external_value(PARAM_INT, 'Group id, 0 means that the function will determine the user group',
                                                   VALUE_DEFAULT, 0),
                'data' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'fieldid' => new external_value(PARAM_INT, 'The field id.'),
                            'subfield' => new external_value(PARAM_NOTAGS, 'The subfield name (if required).', VALUE_DEFAULT, ''),
                            'value' => new external_value(PARAM_RAW, 'The contents for the field always JSON encoded.'),
                        )
                    ), 'The fields data to be created'
                ),
            )
        );
    }

    /**
     * Adds a new entry to a database
     *
     * @param int $databaseid the data instance id
     * @param int $groupid (optional) group id, 0 means that the function will determine the user group
     * @param array $data the fields data to be created
     * @return array of warnings and status result
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function add_entry($databaseid, $groupid, $data) {
        global $DB;

        $params = array('databaseid' => $databaseid, 'groupid' => $groupid, 'data' => $data);
        $params = self::validate_parameters(self::add_entry_parameters(), $params);
        $warnings = array();
        $fieldnotifications = array();

        list($database, $course, $cm, $context) = self::validate_database($params['databaseid']);

        $fields = $DB->get_records('data_fields', ['dataid' => $database->id]);
        if (empty($fields)) {
            throw new moodle_exception('nofieldindatabase', 'data');
        }

        // Check database is open in time.
        data_require_time_available($database, null, $context);

        $groupmode = groups_get_activity_groupmode($cm);
        // Determine default group.
        if (empty($params['groupid'])) {
            // Check to see if groups are being used here.
            if ($groupmode) {
                $groupid = groups_get_activity_group($cm);
            } else {
                $groupid = 0;
            }
        }

        // Group is validated inside the function.
        if (!data_user_can_add_entry($database, $groupid, $groupmode, $context)) {
            throw new moodle_exception('noaccess', 'data');
        }

        // Prepare the data as is expected by the API.
        $datarecord = new stdClass;
        foreach ($params['data'] as $data) {
            $subfield = ($data['subfield'] !== '') ? '_' . $data['subfield'] : '';
            // We ask for JSON encoded values because of multiple choice forms or checkboxes that use array parameters.
            $datarecord->{'field_' . $data['fieldid'] . $subfield} = json_decode($data['value']);
        }
        // Validate to ensure that enough data was submitted.
        $processeddata = data_process_submission($database, $fields, $datarecord);

        // Format notifications.
        if (!empty($processeddata->fieldnotifications)) {
            foreach ($processeddata->fieldnotifications as $field => $notififications) {
                foreach ($notififications as $notif) {
                    $fieldnotifications[] = [
                        'fieldname' => $field,
                        'notification' => $notif,
                    ];
                }
            }
        }

        // Create a new (empty) record.
        $newentryid = 0;
        if ($processeddata->validated && $recordid = data_add_record($database, $groupid)) {
            $newentryid = $recordid;
            // Now populate the fields contents of the new record.
            data_add_fields_contents_to_new_record($database, $context, $recordid, $fields, $datarecord, $processeddata);
        }

        $result = array(
            'newentryid' => $newentryid,
            'generalnotifications' => $processeddata->generalnotifications,
            'fieldnotifications' => $fieldnotifications,
        );
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.3
     */
    public static function add_entry_returns() {
        return new external_single_structure(
            array(
                'newentryid' => new external_value(PARAM_INT, 'True new created entry id. 0 if the entry was not created.'),
                'generalnotifications' => new external_multiple_structure(
                    new external_value(PARAM_RAW, 'General notifications')
                ),
                'fieldnotifications' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'fieldname' => new external_value(PARAM_TEXT, 'The field name.'),
                            'notification' => new external_value(PARAM_RAW, 'The notification for the field.'),
                        )
                    )
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function update_entry_parameters() {
        return new external_function_parameters(
            array(
                'entryid' => new external_value(PARAM_INT, 'The entry record id.'),
                'data' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'fieldid' => new external_value(PARAM_INT, 'The field id.'),
                            'subfield' => new external_value(PARAM_NOTAGS, 'The subfield name (if required).', VALUE_DEFAULT, null),
                            'value' => new external_value(PARAM_RAW, 'The new contents for the field always JSON encoded.'),
                        )
                    ), 'The fields data to be updated'
                ),
            )
        );
    }

    /**
     * Updates an existing entry.
     *
     * @param int $entryid the data instance id
     * @param array $data the fields data to be created
     * @return array of warnings and status result
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function update_entry($entryid, $data) {
        global $DB;

        $params = array('entryid' => $entryid, 'data' => $data);
        $params = self::validate_parameters(self::update_entry_parameters(), $params);
        $warnings = array();
        $fieldnotifications = array();
        $updated = false;

        $record = $DB->get_record('data_records', array('id' => $params['entryid']), '*', MUST_EXIST);
        list($database, $course, $cm, $context) = self::validate_database($record->dataid);
        // Check database is open in time.
        data_require_time_available($database, null, $context);

        if (!data_user_can_manage_entry($record, $database, $context)) {
            throw new moodle_exception('noaccess', 'data');
        }

        // Prepare the data as is expected by the API.
        $datarecord = new stdClass;
        foreach ($params['data'] as $data) {
            $subfield = ($data['subfield'] !== '') ? '_' . $data['subfield'] : '';
            // We ask for JSON encoded values because of multiple choice forms or checkboxes that use array parameters.
            $datarecord->{'field_' . $data['fieldid'] . $subfield} = json_decode($data['value']);
        }
        // Validate to ensure that enough data was submitted.
        $fields = $DB->get_records('data_fields', array('dataid' => $database->id));
        $processeddata = data_process_submission($database, $fields, $datarecord);

        // Format notifications.
        if (!empty($processeddata->fieldnotifications)) {
            foreach ($processeddata->fieldnotifications as $field => $notififications) {
                foreach ($notififications as $notif) {
                    $fieldnotifications[] = [
                        'fieldname' => $field,
                        'notification' => $notif,
                    ];
                }
            }
        }

        if ($processeddata->validated) {
            // Now update the fields contents.
            data_update_record_fields_contents($database, $record, $context, $datarecord, $processeddata);
            $updated = true;
        }

        $result = array(
            'updated' => $updated,
            'generalnotifications' => $processeddata->generalnotifications,
            'fieldnotifications' => $fieldnotifications,
            'warnings' => $warnings,
        );
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.3
     */
    public static function update_entry_returns() {
        return new external_single_structure(
            array(
                'updated' => new external_value(PARAM_BOOL, 'True if the entry was successfully updated, false other wise.'),
                'generalnotifications' => new external_multiple_structure(
                    new external_value(PARAM_RAW, 'General notifications')
                ),
                'fieldnotifications' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'fieldname' => new external_value(PARAM_TEXT, 'The field name.'),
                            'notification' => new external_value(PARAM_RAW, 'The notification for the field.'),
                        )
                    )
                ),
                'warnings' => new external_warnings()
            )
        );
    }
}

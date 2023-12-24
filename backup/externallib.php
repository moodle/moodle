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
 * External backup API.
 *
 * @package    core_backup
 * @category   external
 * @copyright  2018 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Backup external functions.
 *
 * @package    core_backup
 * @category   external
 * @copyright  2018 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 3.7
 */
class core_backup_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.7
     */
    public static function get_async_backup_progress_parameters() {
        return new external_function_parameters(
            array(
                'backupids' => new external_multiple_structure(
                        new external_value(PARAM_ALPHANUM, 'Backup id to get progress for', VALUE_REQUIRED, null, NULL_ALLOWED),
                        'Backup id to get progress for', VALUE_REQUIRED
                 ),
                'contextid' => new external_value(PARAM_INT, 'Context id', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
            )
        );
    }

    /**
     * Get asynchronous backup progress.
     *
     * @param string $backupids The ids of the backup to get progress for.
     * @param int $contextid The context the backup relates to.
     * @return array $results The array of results.
     * @since Moodle 3.7
     */
    public static function get_async_backup_progress($backupids, $contextid) {
        // Release session lock.
        \core\session\manager::write_close();

        // Parameter validation.
        self::validate_parameters(
                self::get_async_backup_progress_parameters(),
                array(
                    'backupids' => $backupids,
                    'contextid' => $contextid
                )
        );

        // Context validation.
        list($context, $course, $cm) = get_context_info_array($contextid);
        self::validate_context($context);

        if ($cm) {
            require_capability('moodle/backup:backupactivity', $context);
        } else {
            require_capability('moodle/backup:backupcourse', $context);
        }

        $results = array();
        foreach ($backupids as $backupid) {
            $results[] = backup_controller_dbops::get_progress($backupid);
        }

        return $results;
    }

    /**
     * Returns description of method result value
     *
     * @return \core_external\external_description
     * @since Moodle 3.7
     */
    public static function get_async_backup_progress_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'status'   => new external_value(PARAM_INT, 'Backup Status'),
                    'progress' => new external_value(PARAM_FLOAT, 'Backup progress'),
                    'backupid' => new external_value(PARAM_ALPHANUM, 'Backup id'),
                    'operation' => new external_value(PARAM_ALPHANUM, 'operation type'),
                ), 'Backup completion status'
          ), 'Backup data'
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.7
     */
    public static function get_async_backup_links_backup_parameters() {
        return new external_function_parameters(
                array(
                    'filename' => new external_value(PARAM_FILE, 'Backup filename', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
                    'contextid' => new external_value(PARAM_INT, 'Context id', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
                    'backupid' => new external_value(PARAM_ALPHANUMEXT, 'Backup id', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
                )
         );
    }

    /**
     * Get the data to be used when generating the table row for an asynchronous backup,
     * the table row updates via ajax when backup is complete.
     *
     * @param string $filename The file name of the backup file.
     * @param int $contextid The context the backup relates to.
     * @param string $backupid The backup ID to get the backup settings.
     * @since Moodle 3.7
     */
    public static function get_async_backup_links_backup($filename, $contextid, $backupid) {
        // Release session lock.
        \core\session\manager::write_close();

        // Parameter validation.
        self::validate_parameters(
                self::get_async_backup_links_backup_parameters(),
                    array(
                        'filename' => $filename,
                        'contextid' => $contextid,
                        'backupid' => $backupid,
                    )
                );

        // Context validation.
        list($context, $course, $cm) = get_context_info_array($contextid);
        self::validate_context($context);
        require_capability('moodle/backup:backupcourse', $context);

        // Backups without user info or with the anonymise functionality enabled are sent
        // to user's "user_backup" file area.
        $filearea = 'backup';
        // Get useful info to render async status in correct area.
        $bc = \backup_controller::load_controller($backupid);
        list($hasusers, $isannon) = \async_helper::get_userdata_backup_settings($bc);
        if ($hasusers && !$isannon) {
            if ($cm) {
                $filearea = 'activity';
            } else {
                $filearea = 'course';
            }
        }

        $results = \async_helper::get_backup_file_info($filename, $filearea, $contextid);

        return $results;
    }

    /**
     * Returns description of method result value.
     *
     * @return \core_external\external_description
     * @since Moodle 3.7
     */
    public static function get_async_backup_links_backup_returns() {
        return new external_single_structure(
            array(
               'filesize'   => new external_value(PARAM_TEXT, 'Backup file size'),
               'fileurl' => new external_value(PARAM_URL, 'Backup file URL'),
               'restoreurl' => new external_value(PARAM_URL, 'Backup restore URL'),
        ), 'Table row data.');
    }
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.7
     */
    public static function get_async_backup_links_restore_parameters() {
        return new external_function_parameters(
                array(
                    'backupid' => new external_value(PARAM_ALPHANUMEXT, 'Backup id', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
                    'contextid' => new external_value(PARAM_INT, 'Context id', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
                )
        );
    }

    /**
     * Get the data to be used when generating the table row for an asynchronous restore,
     * the table row updates via ajax when restore is complete.
     *
     * @param string $backupid The id of the backup record.
     * @param int $contextid The context the restore relates to.
     * @return array $results The array of results.
     * @since Moodle 3.7
     */
    public static function get_async_backup_links_restore($backupid, $contextid) {
        // Release session lock.
        \core\session\manager::write_close();

        // Parameter validation.
        self::validate_parameters(
                self::get_async_backup_links_restore_parameters(),
                    array(
                        'backupid' => $backupid,
                        'contextid' => $contextid
                    )
                );

        // Context validation.
        if ($contextid == 0) {
            $copyrec = \async_helper::get_backup_record($backupid);
            $context = context_course::instance($copyrec->itemid);
        } else {
            $context = context::instance_by_id($contextid);
        }
        self::validate_context($context);
        require_capability('moodle/restore:restorecourse', $context);

        $results = \async_helper::get_restore_url($backupid);

        return $results;
    }

    /**
     * Returns description of method result value.
     *
     * @return \core_external\external_description
     * @since Moodle 3.7
     */
    public static function get_async_backup_links_restore_returns() {
        return new external_single_structure(
                array(
                    'restoreurl' => new external_value(PARAM_URL, 'Restore url'),
                ), 'Table row data.');
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.9
     */
    public static function get_copy_progress_parameters() {
        return new external_function_parameters(
            array(
                'copies' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'backupid' => new external_value(PARAM_ALPHANUM, 'Backup id'),
                            'restoreid' => new external_value(PARAM_ALPHANUM, 'Restore id'),
                            'operation' => new external_value(PARAM_ALPHANUM, 'Operation type'),
                        ), 'Copy data'
                    ), 'Copy data'
                ),
            )
        );
    }

    /**
     * Get the data to be used when generating the table row for a course copy,
     * the table row updates via ajax when copy is complete.
     *
     * @param array $copies Array of ids.
     * @return array $results The array of results.
     * @since Moodle 3.9
     */
    public static function get_copy_progress($copies) {
        // Release session lock.
        \core\session\manager::write_close();

        // Parameter validation.
        self::validate_parameters(
            self::get_copy_progress_parameters(),
            array('copies' => $copies)
            );

        $results = array();

        foreach ($copies as $copy) {

            if ($copy['operation'] == \backup::OPERATION_BACKUP) {
                $copyid = $copy['backupid'];
            } else {
                $copyid = $copy['restoreid'];
            }

            $copyrec = \async_helper::get_backup_record($copyid);
            $context = context_course::instance($copyrec->itemid);
            self::validate_context($context);

            $copycaps = \core_course\management\helper::get_course_copy_capabilities();
            require_all_capabilities($copycaps, $context);

            if ($copy['operation'] == \backup::OPERATION_BACKUP) {
                $result = \backup_controller_dbops::get_progress($copyid);
                if ($result['status'] == \backup::STATUS_FINISHED_OK) {
                    $copyid = $copy['restoreid'];
                }
            }

            $results[] = \backup_controller_dbops::get_progress($copyid);
        }

        return $results;
    }

    /**
     * Returns description of method result value.
     *
     * @return \core_external\external_description
     * @since Moodle 3.9
     */
    public static function get_copy_progress_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'status'   => new external_value(PARAM_INT, 'Copy Status'),
                    'progress' => new external_value(PARAM_FLOAT, 'Copy progress'),
                    'backupid' => new external_value(PARAM_ALPHANUM, 'Copy id'),
                    'operation' => new external_value(PARAM_ALPHANUM, 'Operation type'),
                ), 'Copy completion status'
            ), 'Copy data'
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.9
     */
    public static function submit_copy_form_parameters() {
        return new external_function_parameters(
            array(
                'jsonformdata' => new external_value(PARAM_RAW, 'The data from the create copy form, encoded as a json array')
            )
        );
    }

    /**
     * Submit the course group form.
     *
     * @param string $jsonformdata The data from the form, encoded as a json array.
     * @return int new group id.
     */
    public static function submit_copy_form($jsonformdata) {

        // Release session lock.
        \core\session\manager::write_close();

        // We always must pass webservice params through validate_parameters.
        $params = self::validate_parameters(
            self::submit_copy_form_parameters(),
            array('jsonformdata' => $jsonformdata)
            );

        $formdata = json_decode($params['jsonformdata']);

        $data = array();
        parse_str($formdata, $data);

        $context = context_course::instance($data['courseid']);
        self::validate_context($context);
        $copycaps = \core_course\management\helper::get_course_copy_capabilities();
        require_all_capabilities($copycaps, $context);

        // Submit the form data.
        $course = get_course($data['courseid']);
        $mform = new \core_backup\output\copy_form(
            null,
            array('course' => $course, 'returnto' => '', 'returnurl' => ''),
            'post', '', ['class' => 'ignoredirty'], true, $data);
        $mdata = $mform->get_data();

        if ($mdata) {
            // Create the copy task.
            $copydata = \copy_helper::process_formdata($mdata);
            $copyids = \copy_helper::create_copy($copydata);
        } else {
            throw new moodle_exception('copyformfail', 'backup');
        }

        return json_encode($copyids);
    }

    /**
     * Returns description of method result value.
     *
     * @return \core_external\external_description
     * @since Moodle 3.9
     */
    public static function submit_copy_form_returns() {
        return new external_value(PARAM_RAW, 'JSON response.');
    }
}

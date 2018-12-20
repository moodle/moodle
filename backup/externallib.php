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

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

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
        global $CFG;
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

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
            $instanceid = $course->id;
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
     * @return external_description
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
                )
         );
    }

    /**
     * Get the data to be used when generating the table row for an asynchronous backup,
     * the table row updates via ajax when backup is complete.
     *
     * @param string $filename The file name of the backup file.
     * @param int $contextid The context the backup relates to.
     * @since Moodle 3.7
     */
    public static function get_async_backup_links_backup($filename, $contextid) {
        // Parameter validation.
        self::validate_parameters(
                self::get_async_backup_links_backup_parameters(),
                    array(
                        'filename' => $filename,
                        'contextid' => $contextid
                    )
                );

        // Context validation.
        list($context, $course, $cm) = get_context_info_array($contextid);
        self::validate_context($context);
        require_capability('moodle/backup:backupcourse', $context);

        if ($cm) {
            $filearea = 'activity';
        } else {
            $filearea = 'course';
        }

        $results = \async_helper::get_backup_file_info($filename, $filearea, $contextid);

        return $results;
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
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
        // Parameter validation.
        self::validate_parameters(
                self::get_async_backup_links_restore_parameters(),
                    array(
                        'backupid' => $backupid,
                        'contextid' => $contextid
                    )
                );

        // Context validation.
        $context = context::instance_by_id($contextid);
        self::validate_context($context);
        require_capability('moodle/restore:restorecourse', $context);

        $results = \async_helper::get_restore_url($backupid);

        return $results;
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     * @since Moodle 3.7
     */
    public static function get_async_backup_links_restore_returns() {
        return new external_single_structure(
                array(
                    'restoreurl' => new external_value(PARAM_URL, 'Restore url'),
                ), 'Table row data.');
    }
}

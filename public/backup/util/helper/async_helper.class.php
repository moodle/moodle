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
 * Helper functions for asynchronous backups and restores.
 *
 * @package    core
 * @copyright  2019 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/user/lib.php');

/**
 * Helper functions for asynchronous backups and restores.
 *
 * @package     core
 * @copyright   2019 Matt Porritt <mattp@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class async_helper  {

    /**
     * @var string $type The type of async operation.
     */
    protected $type = 'backup';

    /**
     * @var string $backupid The id of the backup or restore.
     */
    protected $backupid;

    /**
     * @var object $user The user who created the backup record.
     */
    protected $user;

    /**
     * @var object $backuprec The backup controller record from the database.
     */
    protected $backuprec;

    /**
     * Class constructor.
     *
     * @param string $type The type of async operation.
     * @param string $id The id of the backup or restore.
     */
    public function __construct($type, $id) {
        $this->type = $type;
        $this->backupid = $id;
        $this->backuprec = self::get_backup_record($id);
        $this->user = $this->get_user();
    }

    /**
     * Given a backup id return a the record from the database.
     * We use this method rather than 'load_controller' as the controller may
     * not exist if this backup/restore has completed.
     *
     * @param int $id The backup id to get.
     * @return object $backuprec The backup controller record.
     */
    public static function get_backup_record($id) {
        global $DB;

        $backuprec = $DB->get_record('backup_controllers', array('backupid' => $id), '*', MUST_EXIST);

        return $backuprec;
    }

    /**
     * Given a user id return a user object.
     *
     * @return object $user The limited user record.
     */
    private function get_user() {
        $userid = $this->backuprec->userid;
        $user = core_user::get_user($userid, '*', MUST_EXIST);

        return $user;
    }

    /**
     * Return appropriate description for current async operation {@see async_helper::type}
     *
     * @return string
     */
    private function get_operation_description(): string {
        $operations = [
            'backup' => new lang_string('backup'),
            'copy' => new lang_string('copycourse'),
            'restore' => new lang_string('restore'),
        ];

        return (string) ($operations[$this->type] ?? $this->type);
    }

    /**
     * Callback for preg_replace_callback.
     * Replaces message placeholders with real values.
     *
     * @param array $matches The match array from from preg_replace_callback.
     * @return string $match The replaced string.
     */
    private function lookup_message_variables($matches) {
        $options = array(
                'operation' => $this->get_operation_description(),
                'backupid' => $this->backupid,
                'user_username' => $this->user->username,
                'user_email' => $this->user->email,
                'user_firstname' => $this->user->firstname,
                'user_lastname' => $this->user->lastname,
                'link' => $this->get_resource_link(),
        );

        $match = $options[$matches[1]] ?? $matches[1];

        return $match;
    }

    /**
     * Get the link to the resource that is being backuped or restored.
     *
     * @return moodle_url $url The link to the resource.
     */
    private function get_resource_link() {
        // Get activity context only for backups.
        if ($this->backuprec->type == 'activity' && $this->type == 'backup') {
            $context = context_module::instance($this->backuprec->itemid);
        } else { // Course or Section which have the same context getter.
            $context = context_course::instance($this->backuprec->itemid);
        }

        // Generate link based on operation type.
        if ($this->type == 'backup') {
            // For backups simply generate link to restore file area UI.
            $url = new moodle_url('/backup/restorefile.php', array('contextid' => $context->id));
        } else {
            // For restore generate link to the item itself.
            $url = $context->get_url();
        }

        return $url;
    }

    /**
     * Sends a confirmation message for an aynchronous process.
     *
     * @return int $messageid The id of the sent message.
     */
    public function send_message() {
        global $USER;

        $subjectraw = get_config('backup', 'backup_async_message_subject');
        $subjecttext = preg_replace_callback(
                '/\{([-_A-Za-z0-9]+)\}/u',
                array('async_helper', 'lookup_message_variables'),
                $subjectraw);

        $messageraw = get_config('backup', 'backup_async_message');
        $messagehtml = preg_replace_callback(
                '/\{([-_A-Za-z0-9]+)\}/u',
                array('async_helper', 'lookup_message_variables'),
                $messageraw);
        $messagetext = html_to_text($messagehtml);

        $message = new \core\message\message();
        $message->component = 'moodle';
        $message->name = 'asyncbackupnotification';
        $message->userfrom          = $USER;
        $message->userto            = $this->user;
        $message->subject           = $subjecttext;
        $message->fullmessage       = $messagetext;
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessagehtml   = $messagehtml;
        $message->smallmessage      = '';
        $message->notification      = '1';

        $messageid = message_send($message);

        return $messageid;
    }

    /**
     * Check if asynchronous backup and restore mode is
     * enabled at system level.
     *
     * @return bool $async True if async mode enabled false otherwise.
     */
    public static function is_async_enabled() {
        global $CFG;

        $async = false;
        if (!empty($CFG->enableasyncbackup)) {
            $async = true;
        }

        return $async;
    }

    /**
     * Check if there is a pending async operation for given details.
     *
     * @param int $id The item id to check in the backup record.
     * @param string $type The type of operation: course, activity or section.
     * @param string $operation Operation backup or restore.
     * @return boolean $asyncpedning Is there a pending async operation.
     */
    public static function is_async_pending($id, $type, $operation) {
        global $DB, $USER, $CFG;
        $asyncpending = false;

        // Only check for pending async operations if async mode is enabled.
        require_once($CFG->dirroot . '/backup/util/interfaces/checksumable.class.php');
        require_once($CFG->dirroot . '/backup/backup.class.php');

        $select = 'userid = ? AND itemid = ? AND type = ? AND operation = ? AND execution = ? AND status < ? AND status > ?';
        $params = array(
            $USER->id,
            $id,
            $type,
            $operation,
            backup::EXECUTION_DELAYED,
            backup::STATUS_FINISHED_ERR,
            backup::STATUS_NEED_PRECHECK
        );

        $asyncrecord= $DB->get_record_select('backup_controllers', $select, $params);

        if ((self::is_async_enabled() && $asyncrecord) || ($asyncrecord && $asyncrecord->purpose == backup::MODE_COPY)) {
            $asyncpending = true;
        }
        return $asyncpending;
    }

    /**
     * Get the size, url and restore url for a backup file.
     *
     * @param string $filename The name of the file to get info for.
     * @param string $filearea The file area for the file.
     * @param int $contextid The context ID of the file.
     * @return array $results The result array containing the size, url and restore url of the file.
     */
    public static function get_backup_file_info($filename, $filearea, $contextid) {
        $fs = get_file_storage();
        $file = $fs->get_file($contextid, 'backup', $filearea, 0, '/', $filename);
        $filesize = display_size ($file->get_filesize());
        $fileurl = moodle_url::make_pluginfile_url(
            $file->get_contextid(),
            $file->get_component(),
            $file->get_filearea(),
            null,
            $file->get_filepath(),
            $file->get_filename(),
            true
            );

        $params = array();
        $params['action'] = 'choosebackupfile';
        $params['filename'] = $file->get_filename();
        $params['filepath'] = $file->get_filepath();
        $params['component'] = $file->get_component();
        $params['filearea'] = $file->get_filearea();
        $params['filecontextid'] = $file->get_contextid();
        $params['contextid'] = $contextid;
        $params['itemid'] = $file->get_itemid();
        $restoreurl = new moodle_url('/backup/restorefile.php', $params);
        $filesize = display_size ($file->get_filesize());

        $results = array(
            'filesize' => $filesize,
            'fileurl' => $fileurl->out(false),
            'restoreurl' => $restoreurl->out(false));

        return $results;
    }

    /**
     * Get the url of a restored backup item based on the backup ID.
     *
     * @param string $backupid The backup ID to get the restore location url.
     * @return array $urlarray The restored item URL as an array.
     */
    public static function get_restore_url($backupid) {
        global $DB;

        $backupitemid = $DB->get_field('backup_controllers', 'itemid', array('backupid' => $backupid), MUST_EXIST);
        $newcontext = context_course::instance($backupitemid);

        $restoreurl = $newcontext->get_url()->out();
        $urlarray = array('restoreurl' => $restoreurl);

        return $urlarray;
    }

    /**
     * Get markup for in progress async backups,
     * to use in backup table UI.
     *
     * @param string $filearea The filearea to get backup data for.
     * @param integer $instanceid The context id to get backup data for.
     * @return array $tabledata the rows of table data.
     */
    public static function get_async_backups($filearea, $instanceid) {
        global $DB;

        $backups = [];

        $table = 'backup_controllers';
        $select = 'execution = :execution AND status < :status1 AND status > :status2 ' .
            'AND operation = :operation';
        $params = [
            'execution' => backup::EXECUTION_DELAYED,
            'status1' => backup::STATUS_FINISHED_ERR,
            'status2' => backup::STATUS_NEED_PRECHECK,
            'operation' => 'backup',
        ];
        $sort = 'timecreated DESC';
        $fields = 'id, backupid, status, timecreated';

        if ($filearea == 'backup') {
            // Get relevant backup ids based on user id.
            $params['userid'] = $instanceid;
            $select = 'userid = :userid AND ' . $select;
            $records = $DB->get_records_select($table, $select, $params, $sort, $fields);
            foreach ($records as $record) {
                $bc = \backup_controller::load_controller($record->backupid);

                // Get useful info to render async status in correct area.
                list($hasusers, $isannon) = self::get_userdata_backup_settings($bc);
                // Backup has users and is not anonymised -> don't show it in users backup file area.
                if ($hasusers && !$isannon) {
                    continue;
                }

                $record->filename = $bc->get_plan()->get_setting('filename')->get_value();
                $bc->destroy();
                array_push($backups, $record);
            }
        } else {
            if ($filearea == 'course' || $filearea == 'activity') {
                // Get relevant backup ids based on context instance id.
                $params['itemid'] = $instanceid;
                $select = 'itemid = :itemid AND ' . $select;
                $records = $DB->get_records_select($table, $select, $params, $sort, $fields);
                foreach ($records as $record) {
                    $bc = \backup_controller::load_controller($record->backupid);

                    // Get useful info to render async status in correct area.
                    list($hasusers, $isannon) = self::get_userdata_backup_settings($bc);
                    // Backup has no user or is anonymised -> don't show it in course/activity backup file area.
                    if (!$hasusers || $isannon) {
                        continue;
                    }

                    $record->filename = $bc->get_plan()->get_setting('filename')->get_value();
                    $bc->destroy();
                    array_push($backups, $record);
                }
            }
        }

        return $backups;
    }

    /**
     * Get the user data settings for backups.
     *
     * @param \backup_controller $backupcontroller The backup controller object.
     * @return array Array of user data settings.
     */
    public static function get_userdata_backup_settings(\backup_controller $backupcontroller): array {
        $hasusers = (bool)$backupcontroller->get_plan()->get_setting('users')->get_value(); // Backup has users.
        $isannon = (bool)$backupcontroller->get_plan()->get_setting('anonymize')->get_value(); // Backup is anonymised.
        return [$hasusers, $isannon];
    }

    /**
     * Get the course name of the resource being restored.
     *
     * @param \context $context The Moodle context for the restores.
     * @return string $coursename The full name of the course.
     */
    public static function get_restore_name(\context $context) {
        global $DB;
        $instanceid = $context->instanceid;

        if ($context->contextlevel == CONTEXT_MODULE) {
            // For modules get the course name and module name.
            $cm = get_coursemodule_from_id('', $context->instanceid, 0, false, MUST_EXIST);
            $coursename = $DB->get_field('course', 'fullname', array('id' => $cm->course));
            $itemname = $coursename . ' - ' . $cm->name;
        } else {
            $itemname = $DB->get_field('course', 'fullname', array('id' => $context->instanceid));

        }

        return $itemname;
    }

    /**
     * Get all the current in progress async restores for a user.
     *
     * @param int $userid Moodle user id.
     * @return array $restores List of current restores in progress.
     */
    public static function get_async_restores($userid) {
        global $DB;

        $select = 'userid = ? AND execution = ? AND status < ? AND status > ? AND operation = ?';
        $params = array($userid, backup::EXECUTION_DELAYED, backup::STATUS_FINISHED_ERR, backup::STATUS_NEED_PRECHECK, 'restore');
        $restores = $DB->get_records_select(
            'backup_controllers',
            $select,
            $params,
            'timecreated DESC',
            'id, backupid, status, itemid, timecreated');

            return $restores;
    }

}


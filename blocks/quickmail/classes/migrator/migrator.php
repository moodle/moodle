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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\migrator;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\migrator\chunk_size_met_exception;
use \core\task\manager as task_manager;

class migrator {

    public $db;
    public $cfg;
    public $siteid;
    public $chunksize;
    public $migratedcount;
    public static $olddraftstable = 'block_quickmail_drafts';
    public static $oldlogtable = 'block_quickmail_log';
    public static $messagetable = 'block_quickmail_messages';
    public static $draftrecipienttable = 'block_quickmail_draft_recips';
    public static $messagerecipienttable = 'block_quickmail_msg_recips';
    public static $additionalemailtable = 'block_quickmail_msg_ad_email';

    public function __construct() {
        global $DB;
        global $CFG;

        $this->db = $DB;
        $this->cfg = $CFG;
        $this->site_id = SITEID;
        $this->chunk_size = $this->get_configured_chunk_size();
        $this->migrated_count = 0;
    }

    /**
     * Reports whether or not the migration task is enabled
     *
     * @return bool
     */
    public static function is_enabled() {
        $task = task_manager::get_scheduled_task('block_quickmail\tasks\migrate_legacy_data_task');

        return ! $task->get_disabled();
    }

    /**
     * Reports whether or not Quickmail's legacy tables exist
     *
     * @return bool
     */
    public static function old_tables_exist() {
        global $DB;

        $dbman = $DB->get_manager();

        return $dbman->table_exists(self::$olddraftstable) || $dbman->table_exists(self::$oldlogtable);
    }

    /**
     * Drops old tables
     *
     * @return void
     */
    public static function drop_old_tables() {
        global $DB;

        $dbman = $DB->get_manager();

        $draftstable = new \xmldb_table(self::$olddraftstable);

        if ($dbman->table_exists($draftstable)) {
            $dbman->drop_table($draftstable);
        }

        $logstable = new \xmldb_table(self::$oldlogtable);

        if ($dbman->table_exists($logstable)) {
            $dbman->drop_table($logstable);
        }
    }

    /**
     * Returns a count of all old records of a given type
     *
     * @param  string  $type  drafts|log
     * @return int
     */
    public static function total_count($type) {
        $migrator = new self();

        return $migrator->get_total_count($type);
    }

    /**
     * Returns a count of all migrated records of a given type
     *
     * @param  string  $type  drafts|log
     * @return int
     */
    public static function migrated_count($type) {
        $migrator = new self();

        return $migrator->get_migrated_count($type);
    }

    /**
     * Executes migration of any historical Quickmail data from old format to new, adhering to a configurable number of
     * transactions before stopping. Priority is given to course-level emails (not admin messages) with drafts first and
     * then sent messages second. After all course-level emails are complete, this process will move on to site-level
     * messages in the same fashion.
     *
     * NOTE: this process does not convert old email attachment data, alternate email, or signature data
     *
     * @return bool  whether or not the migration process has completed
     * @throws block_quickmail\migrator\chunk_size_met_exception
     * @throws \Exception  a catch all in case anything unexpected happens
     */
    public static function execute() {
        $migrator = new self();

        // Course drafts.
        $migrator->migrate(true, false);
        // Course sents.
        $migrator->migrate(false, false);
        // Site drafts.
        $migrator->migrate(true, true);
        // Site sents.
        $migrator->migrate(false, true);

        return true;
    }

    /**
     * Executes migration of historic data from old tables to new by creating messages with recipients and any additional emails
     *
     * @param  bool    $isdraft
     * @param  bool    $isadminmessage  whether or not this process should migrate course-level or site-level emails
     * @return void
     * @throws chunk_size_met_exception
     */
    public function migrate($isdraft, $isadminmessage) {
        if (!empty($this->chunk_size)) {
            // While we can pull an unmigrated message of the given status type (beginning with latest).
            while ($record = $this->find_latest_unmigrated($isdraft, $isadminmessage)) {
                $this->create_message($isdraft, $isadminmessage, $record);

                $this->mark_old_record_as_migrated($isdraft, $record);

                $this->migrated_count++;

                $this->check_chunk_size();
            }
        }
    }

    /**
     * Creates a new message record for the given old record
     *
     * @param  bool      $isdraft
     * @param  bool      $isadminmessage
     * @param  stdClass  $oldrecord
     * @return void
     */
    private function create_message($isdraft, $isadminmessage, $oldrecord) {
        // Construct a new message record.
        $message = (object) [
            'course_id' => $oldrecord->courseid,
            'user_id' => $oldrecord->userid,
            'message_type' => 'email',
            'subject' => $oldrecord->subject,
            'body' => $oldrecord->message,
            'editor_format' => $oldrecord->format,
            'sent_at' => $isdraft ? 0 : $oldrecord->time,
            'to_send_at' => 0,
            'is_draft' => $isdraft ? 1 : 0,
            'usermodified' => $oldrecord->userid,
            'timecreated' => $oldrecord->time,
            'timemodified' => $oldrecord->time,
            'timedeleted' => 0
        ];

        // Insert record as message, returning message id.
        $messageid = $this->db->insert_record(self::$messagetable, $message);

        // If original message had any recipients.
        if (!empty($oldrecord->mailto)) {
            $this->create_recipients($isdraft, $isadminmessage, $messageid, $oldrecord);
        }

        // If original message had any additional emails.
        if (!empty($oldrecord->additional_emails)) {
            $this->create_additional_emails_for_message($isadminmessage, $messageid, $oldrecord);
        }
    }

    /**
     * Creates new recipients for the given message id, depending on type
     *
     * @param  bool      $isdraft
     * @param  bool      $isadminmessage
     * @param  int       $messageid
     * @param  stdClass  $oldrecord
     * @return void
     */
    private function create_recipients($isdraft, $isadminmessage, $messageid, $oldrecord) {
        if ($isdraft) {
            $this->create_draft_recipients($isadminmessage, $messageid, $oldrecord);
        } else {
            $this->create_message_recipients($isadminmessage, $messageid, $oldrecord);
        }
    }

    /**
     * Creates new draft recipients for the given message id
     *
     * @param  bool      $isadminmessage
     * @param  int       $messageid
     * @param  stdClass  $oldrecord
     * @return void
     */
    private function create_draft_recipients($isadminmessage, $messageid, $oldrecord) {
        if ($isadminmessage) {
            $this->create_admin_draft_recipients($messageid, $oldrecord);
        } else {
            $this->create_course_draft_recipients($messageid, $oldrecord);
        }
    }

    /**
     * Creates new draft recipients for the given course-level message id
     *
     * @param  int       $messageid
     * @param  stdClass  $oldrecord
     * @return void
     */
    private function create_course_draft_recipients($messageid, $oldrecord) {
        // For each mailto user.
        foreach (explode(',', trim($oldrecord->mailto)) as $userid) {
            // Construct a new recipient record for this message.
            $recipient = (object) [
                'message_id' => $messageid,
                'type' => 'include',
                'recipient_type' => 'user',
                'recipient_id' => $userid,
                'usermodified' => $oldrecord->userid,
                'timecreated' => $oldrecord->time,
                'timemodified' => $oldrecord->time,
            ];

            $this->db->insert_record(self::$draftrecipienttable, $recipient);
        }
    }

    /**
     * Creates new draft recipients for the given site-level message id
     *
     * @param  int       $messageid
     * @param  stdClass  $oldrecord
     * @return void
     */
    private function create_admin_draft_recipients($messageid, $oldrecord) {
        // Construct a new recipient record for this message.
        $recipient = (object) [
            'message_id' => $messageid,
            'type' => 'include',
            'recipient_type' => 'filter',
            'recipient_filter' => $oldrecord->mailto,
            'usermodified' => $oldrecord->userid,
            'timecreated' => $oldrecord->time,
            'timemodified' => $oldrecord->time,
        ];

        $this->db->insert_record(self::$draftrecipienttable, $recipient);
    }

    /**
     * Creates new message recipients for the given message id
     *
     * Note: this skips creating site-level message recipients to avoid having to use old filters
     * to lookup users
     *
     * @param  bool      $isadminmessage
     * @param  int       $messageid
     * @param  stdClass  $oldrecord
     * @return void
     */
    private function create_message_recipients($isadminmessage, $messageid, $oldrecord) {
        if (!$isadminmessage) {
            // For each mailto user.
            foreach (explode(',', trim($oldrecord->mailto)) as $userid) {
                // Create a new record.
                $recipient = (object) [
                    'message_id' => $messageid,
                    'user_id' => $userid,
                    'sent_at' => $oldrecord->time,
                    'moodle_message_id' => 0,
                    'usermodified' => $userid,
                    'timecreated' => $oldrecord->time,
                    'timemodified' => $oldrecord->time
                ];

                $this->db->insert_record(self::$messagerecipienttable, $recipient);
            }
        }
    }

    /**
     * Creates new additional email record for the given message id
     *
     * @param  bool      $isadminmessage
     * @param  int       $messageid
     * @param  stdClass  $oldrecord
     * @return void
     */
    private function create_additional_emails_for_message($isadminmessage, $messageid, $oldrecord) {
        // Sanitize additional email string.
        $additionalemailstring = str_replace(';', ',', $oldrecord->additional_emails);

        // For each additional email.
        foreach (explode(',', trim($additionalemailstring)) as $email) {
            // Construct a new additional email record for this message.
            $addemail = (object) [
                'message_id' => $messageid,
                'email' => trim($email),
                'sent_at' => $oldrecord->time,
                'usermodified' => $oldrecord->userid,
                'timecreated' => $oldrecord->time,
                'timemodified' => $oldrecord->time
            ];

            $this->db->insert_record(self::$additionalemailtable, $addemail);
        }
    }

    /**
     * Returns the most recent unmigrated record of a given type, or null
     *
     * @param  bool    $isdraft
     * @param  bool    $isadminmessage  if true, will return only a site-scoped message (not course)
     * @return stdClass|null
     */
    private function find_latest_unmigrated($isdraft, $isadminmessage = false) {
        $sql = 'select * from ' . $this->get_raw_source_table_name($isdraft) . ' where has_migrated = 0';

        $sql .= $isadminmessage
            ? ' and courseid = ' . $this->site_id
            : ' and courseid != ' . $this->site_id;

        $sql .= ' order by id desc limit 1;';

        try {
            return $this->db->get_record_sql($sql, null, MUST_EXIST);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Updates the given record to a "migrated" status, depending on type
     *
     * @param  bool     $isdraft
     * @param  stdClass $oldrecord
     * @return void
     */
    private function mark_old_record_as_migrated($isdraft, $oldrecord) {
        $oldrecord->has_migrated = 1;

        $tablename = $isdraft
            ? self::$olddraftstable
            : self::$oldlogtable;

        $this->db->update_record($tablename, $oldrecord);
    }

    /**
     * Returns the full table name (including prefix) of the old source table depending on type of message
     *
     * @param  bool   $isdraft
     * @return string
     */
    private function get_raw_source_table_name($isdraft) {
        $name = $isdraft
            ? self::$olddraftstable
            : self::$oldlogtable;

        return $this->cfg->prefix . $name;
    }

    /**
     * Throws an exception if this migrate execution has exceeded the maximum number of configured iterations
     *
     * @return void
     * @throws chunk_size_met_exception
     */
    private function check_chunk_size() {
        if ($this->migrated_count >= $this->chunk_size) {
            throw new chunk_size_met_exception;
        }
    }

    /**
     * Returns count of given type of records
     *
     * @param  string  $type  drafts|log
     * @return int
     */
    public function get_total_count($type) {
        return (int) $this->db->count_records('block_quickmail_' . $type);
    }

    /**
     * Returns count of given type of records that have been migrated
     *
     * @param  string  $type  drafts|log
     * @return int
     */
    public function get_migrated_count($type) {
        return (int) $this->db->count_records('block_quickmail_' . $type, ['has_migrated' => 1]);
    }

    /**
     * Returns the configured chunk size amount, defaulting to 1000
     *
     * @return int
     */
    private function get_configured_chunk_size() {
        // Attempt to pull the configured chunk size and return.
        if ($chunksize = get_config('moodle', 'block_quickmail_migration_chunk_size')) {
            if (is_numeric($chunksize)) {
                return (int) $chunksize;
            }
        }

        // Default.
        return 1000;
    }

}

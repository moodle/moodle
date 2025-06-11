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

defined('MOODLE_INTERNAL') || die();

/**
 * function to migrate quickmail history files attachment to the new file version from 1.9 to 2.x
 */
function migrate_quickmail_20() {
    global $DB;
    // Migration of attachments.
    $fs = get_file_storage();
    $quickmaillogrecords = $DB->get_records_select('block_quickmail_log', 'attachment<>\'\'');
    foreach ($quickmaillogrecords as $quickmaillogrecord) {
        // Searching file into mdl_files.
        // Analysing attachment content.
        $filename = $quickmaillogrecord->attachment;
        $filepath = '';
        $notrootfile = strstr($quickmaillogrecord->attachment, '/');
        if ($notrootfile) {
            $filename = substr($quickmaillogrecord->attachment, strrpos($quickmaillogrecord->attachment, '/', -1) + 1);
            $filepath = '/'.substr($quickmaillogrecord->attachment, 0, strrpos($quickmaillogrecord->attachment, '/', -1) + 1);
        } else {
            $filepath = '/';
            $filename = $quickmaillogrecord->attachment;
        }
        $fs = get_file_storage();
                $coursecontext = context_course::instance($quickmaillogrecord->courseid);

        $coursefile = $fs->get_file($coursecontext->id, 'course', 'legacy', 0, $filepath, $filename);
        if ($coursefile) {
            if ($notrootfile) {
                // Rename.
                $filename = str_replace('/', '_', $quickmaillogrecord->attachment);
                $filepath = '/';
                $quickmaillogrecord->attachment = $filename;
                $DB->update_record('block_quickmail_log', $quickmaillogrecord);
            }
            $filerecord = array(
                                'contextid' => $coursecontext->id,
                                'component' => 'block_quickmail',
                                'filearea' => 'attachment_log',
                                'itemid' => $quickmaillogrecord->id,
                                'filepath' => $filepath,
                                'filename' => $filename,
                                'timecreated' => $coursefile->get_timecreated(),
                                'timemodified' => $coursefile->get_timemodified());
            if (!$fs->file_exists($coursecontext->id, 'block_quickmail', 'attachment_log', 0, $filepath, $filename)) {
                $fs->create_file_from_storedfile($filerecord, $coursefile->get_id());
            }
        }
    }
}

/*
 * Migrate all v1 DB data to v2 format
 */
function migrate_quickmail_v1_to_v2() {

    global $DB;

    $now = time();

    $dbman = $DB->get_manager();

    // Create some temp tables to assist in migration.
    foreach (['log', 'draft'] as $tabletype) {
        $table = new xmldb_table(get_temp_table_name($tabletype));

        $field = new xmldb_field('id');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->addField($field);

        $field = new xmldb_field('old_id');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);
        $table->addField($field);

        $field = new xmldb_field('new_id');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, false, 0);
        $table->addField($field);

        $field = new xmldb_field('type');
        $field->set_attributes(XMLDB_TYPE_CHAR, '8', null, XMLDB_NOTNULL);
        $table->addField($field);

        $field = new xmldb_field('message_created');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, false, 0);
        $table->addField($field);

        $field = new xmldb_field('recips_created');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, false, 0);
        $table->addField($field);

        $field = new xmldb_field('add_emails_created');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, false, 0);
        $table->addField($field);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
    }

    // Populte temp tables with necessary data.
    foreach (['log', 'draft'] as $tabletype) {

        // Grab old record data from the appropriate table.
        $olds = $DB->get_records(get_old_table_name($tabletype), null, '', 'id, courseid');

        // Insert a new temp record for each log/draft.
        foreach ($olds as $old) {
            $temprecord = (object) [
                'old_id' => $old->id,
                'type' => $old->courseid == SITEID ? 'admin' : 'standard' // This is an assumption!
            ];

            $DB->insert_record(get_temp_table_name($tabletype), $temprecord);
        }
    }

    unset($olds);

    // Migrate signature data.
    // Get all current signatures.
    $signatures = $DB->get_records(get_signature_table_name());

    // Update each record's "persistent" details.
    foreach ($signatures as $signature) {
        $signature->usermodified = $signature->user_id;
        $signature->timecreated = $now;
        $signature->timemodified = $now;

        $DB->update_record(get_signature_table_name(), $signature);
    }

    unset($signatures);

    // Begin the process of migrating messages.
    foreach (['log', 'draft'] as $tabletype) {
        // Iterate through each "message type".
        foreach (['standard', 'admin'] as $type) {
            // While we can fetch a temp message record for this specific type..
            while ($temp = $DB->get_record_select(get_temp_table_name($tabletype),
                "type = :type AND message_created = 0",
                ['type' => $type],
                '*',
                IGNORE_MULTIPLE)) {
                // Fetch the corresponding old record.
                $old = $DB->get_record(get_old_table_name($tabletype), ['id' => $temp->old_id], '*', IGNORE_MULTIPLE);

                // Create a new record.
                $message = (object) [
                    'course_id' => $old->courseid,
                    'user_id' => $old->userid,
                    'message_type' => 'email',
                    'subject' => $old->subject,
                    'body' => $old->message,
                    'editor_format' => $old->format,
                    'sent_at' => $tabletype == 'draft' ? 0 : $old->time,
                    'to_send_at' => 0,
                    'is_draft' => $tabletype == 'draft' ? 1 : 0,
                    'usermodified' => $old->userid,
                    'timecreated' => $old->time,
                    'timemodified' => $old->time,
                    'timedeleted' => 0
                ];

                // Insert record, grabbing new id.
                $messageid = $DB->insert_record(get_message_table_name(), $message);

                // Update the temp record to reflect that message has been created.
                $temp->new_id = $messageid;
                $temp->message_created = 1;

                $DB->update_record(get_temp_table_name($tabletype), $temp);
            }
        }
    }

    // Begin the process of migrating standard message recipients (not admin).
    // While we can fetch a temp "standard message" record for this specific type that has not had recipients created.
    while ($temp = $DB->get_record_select(get_temp_table_name('log'),
        "type = :type AND message_created = 1 AND recips_created = 0",
        ['type' => 'standard'],
        '*',
        IGNORE_MULTIPLE)) {
        // Fetch the corresponding log record.
        $log = $DB->get_record(get_old_table_name('log'), ['id' => $temp->old_id], '*', IGNORE_MULTIPLE);

        // Fetch the new message record.
        $message = $DB->get_record(get_message_table_name(), ['id' => $temp->new_id], '*', IGNORE_MULTIPLE);

        // Iterate over all "mailtos" (user ids).
        foreach (explode(', ', $log->mailto) as $userid) {
            // Create a new record.
            $recipient = (object) [
                'message_id' => $message->id,
                'user_id' => $userid,
                'sent_at' => $message->sent_at,
                'moodle_message_id' => 0,
                'usermodified' => $message->user_id,
                'timecreated' => $message->sent_at,
                'timemodified' => $message->sent_at
            ];

            // Insert record.
            $DB->insert_record(get_message_recips_table_name(), $recipient, false);

            // Update the temp record to reflect that message recipients have been created.
            $temp->recips_created = 1;

            $DB->update_record(get_temp_table_name('log'), $temp);
        }
    }

    // Begin the process of migrating standard draft recipients (not admin).
    // While we can fetch a temp "standard message" record for this specific type that has not had recipients created.
    while ($temp = $DB->get_record_select(get_temp_table_name('draft'),
        "type = :type AND message_created = 1 AND recips_created = 0",
        ['type' => 'standard'],
        '*',
        IGNORE_MULTIPLE)) {
        // Fetch the corresponding draft record.
        $draft = $DB->get_record(get_old_table_name('draft'), ['id' => $temp->old_id], '*', IGNORE_MULTIPLE);

        // Fetch the new message record.
        $message = $DB->get_record(get_message_table_name(), ['id' => $temp->new_id], '*', IGNORE_MULTIPLE);

        // Iterate over all "mailtos" (user ids).
        foreach (explode(', ', $draft->mailto) as $userid) {
            // Create a new record.
            $recipient = (object) [
                'message_id' => $message->id,
                'type' => 'include',
                'recipient_type' => 'user',
                'recipient_id' => $userid,
                'timecreated' => $message->timecreated,
                'timemodified' => $message->timemodified
            ];

            // Insert record.
            $DB->insert_record(get_draft_recips_table_name(), $recipient, false);
        }

        // Update the temp record to reflect that message recipients have been created.
        $temp->recips_created = 1;

        $DB->update_record(get_temp_table_name('draft'), $temp);
    }

    // Begin the process of migrating admin draft recipients.
    // While we can fetch a temp "admin message" record for this specific type that has not had recipients created.
    while ($temp = $DB->get_record_select(get_temp_table_name('draft'),
        "type = :type AND message_created = 1 AND recips_created = 0",
        ['type' => 'admin'],
        '*',
        IGNORE_MULTIPLE)) {
        // Fetch the corresponding draft record.
        $draft = $DB->get_record(get_old_table_name('draft'), ['id' => $temp->old_id], '*', IGNORE_MULTIPLE);

        // Fetch the new message record.
        $message = $DB->get_record(get_message_table_name(), ['id' => $temp->new_id], '*', IGNORE_MULTIPLE);

        // Create a new record using the serialized "mailto".
        $recipient = (object) [
            'message_id' => $message->id,
            'type' => 'include',
            'recipient_type' => 'filter',
            'recipient_filter' => $draft->mailto,
            'timecreated' => $message->timecreated,
            'timemodified' => $message->timemodified
        ];

        // Insert record.
        $DB->insert_record(get_draft_recips_table_name(), $recipient, false);

        // Update the temp record to reflect that message recipients have been created.
        $temp->recips_created = 1;

        $DB->update_record(get_temp_table_name('draft'), $temp);
    }

    // Begin the process of migrating additional email recipients.
    foreach (['log', 'draft'] as $tabletype) {
        // Iterate through each "message type".
        foreach (['standard', 'admin'] as $type) {
            // While we can fetch a temp "standard message" record for this specific type.
            while ($temp = $DB->get_record_select(get_temp_table_name($tabletype),
                "type = :type AND message_created = 1 AND recips_created = 1 AND add_emails_created = 0",
                ['type' => $type],
                '*',
                IGNORE_MULTIPLE)) {
                // Fetch the corresponding old record.
                $old = $DB->get_record(get_old_table_name($tabletype), ['id' => $temp->old_id], '*', IGNORE_MULTIPLE);

                // Fetch the new message record.
                $message = $DB->get_record(get_message_table_name(), ['id' => $temp->new_id], '*', IGNORE_MULTIPLE);

                // If the old record had additional emails.
                if (!empty($old->additional_emails)) {
                    // Iterate over each email.
                    foreach (explode(', ', $old->additional_emails) as $email) {
                        // Create a new record.
                        $addemail = (object) [
                            'message_id' => $message->id,
                            'email' => trim($email),
                            'sent_at' => $message->sent_at,
                            'usermodified' => $message->user_id,
                            'timecreated' => $message->timecreated,
                            'timemodified' => $message->timemodified
                        ];

                        // Insert record.
                        $DB->insert_record(get_add_emails_table_name(), $addemail, false);
                    }
                }

                // Update the temp record to reflect that message recipients have been created.
                $temp->add_emails_created = 1;

                $DB->update_record(get_temp_table_name($tabletype), $temp);
            }
        }
    }

    // Drop the temp tables.
    foreach (['log', 'draft'] as $tabletype) {
        $table = new xmldb_table(get_temp_table_name($tabletype));

        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
    }

    // All done!
    return true;
}

function get_old_table_name($tabletype) {
    return $tabletype == 'draft'
        ? 'block_quickmail_drafts'
        : 'block_quickmail_log';
}

function get_temp_table_name($tabletype) {
    return $tabletype == 'draft'
        ? 'block_quickmail_temp_draft_m'
        : 'block_quickmail_temp_log_m';
}

function get_temp_alt_table_name() {
    return 'block_quickmail_temp_alt_m';
}

function get_signature_table_name() {
    return 'block_quickmail_signatures';
}

function get_old_alt_email_table_name() {
    return 'block_quickmail_alternate';
}

function get_alt_email_table_name() {
    return 'block_quickmail_alt_emails';
}

function get_message_table_name() {
    return 'block_quickmail_messages';
}

function get_message_recips_table_name() {
    return 'block_quickmail_msg_recips';
}

function get_draft_recips_table_name() {
    return 'block_quickmail_draft_recips';
}

function get_add_emails_table_name() {
    return 'block_quickmail_msg_ad_email';
}

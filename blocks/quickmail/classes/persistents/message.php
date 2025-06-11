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

namespace block_quickmail\persistents;

defined('MOODLE_INTERNAL') || die();

use block_quickmail_cache;
use block_quickmail_string;
use block_quickmail\persistents\concerns\enhanced_persistent;
use block_quickmail\persistents\concerns\belongs_to_a_course;
use block_quickmail\persistents\concerns\belongs_to_a_user;
use block_quickmail\persistents\concerns\can_have_a_notification;
use block_quickmail\persistents\concerns\can_be_soft_deleted;
use block_quickmail\persistents\message_recipient;
use block_quickmail\persistents\message_draft_recipient;
use block_quickmail\persistents\message_additional_email;
use block_quickmail\persistents\message_attachment;
use block_quickmail\persistents\notification;
use block_quickmail\messenger\messenger;
use block_quickmail\messenger\message\substitution_code;
use block_quickmail\repos\user_repo;


class message extends \block_quickmail\persistents\persistent {

    use enhanced_persistent,
        belongs_to_a_course,
        belongs_to_a_user,
        can_have_a_notification,
        can_be_soft_deleted;

    /** Table name for the persistent. */
    const TABLE = 'block_quickmail_messages';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'course_id' => [
                'type' => PARAM_INT,
            ],
            'user_id' => [
                'type' => PARAM_INT,
            ],
            'message_type' => [
                'type' => PARAM_TEXT,
            ],
            'alternate_email_id' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'signature_id' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'notification_id' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'subject' => [
                'type' => PARAM_TEXT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
            'body' => [
                'type' => PARAM_RAW,
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
            'editor_format' => [
                'type' => PARAM_INT,
                'default' => 1, // TODO - Make this configurable?
            ],
            'sent_at' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'to_send_at' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'is_draft' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'send_receipt' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'send_to_mentors' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'is_sending' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'no_reply' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'timedeleted' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'deleted' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
        ];
    }

    // Relationships.
    /**
     * Returns the additional emails that are associated with this message
     *
     * Optionally, returns an array of emails
     *
     * @return array
     */
    public function get_additional_emails($asemailarray = false) {
        $messageid = $this->get('id');

        $additionals = message_additional_email::get_records(['message_id' => $messageid]);

        if (!$asemailarray) {
            return $additionals;
        }

        $emails = array_reduce($additionals, function ($carry, $additional) {
            $carry[] = $additional->get('email');

            return $carry;
        }, []);

        return $emails;
    }

    /**
     * Returns a check to see if the message is sent to ALL of the class.
     *
     * @return bool
     */
    public function check_course_msg()
    {
        global $DB;
        $params = array("message_id" => $this->get('id'), "course_id" => $this->get('course_id'));
        $foundall = $DB->record_exists('block_quickmail_msg_course', $params);
        return $foundall;
    }

    /**
     * Returns a check to see if the message is sent to ALL of the class.
     *
     * @return bool
     */
    public function populate_recip_course_msg()
    {
        global $DB;

        $messageid = $this->get('id');
        $cuser = $this->get('user_id');
        $course = $DB->get_record('course', ['id' => $this->get('course_id')]);
        $coursemsg = new message($messageid);

        $recipientuserids = user_repo::get_unique_course_user_ids_from_selected_entities(
            $course,
            $cuser,
            array("all")
        );

        // Now get that msg recipient into the table so it can be processed.
        foreach ($recipientuserids as $recipient) {
            message_recipient::create_new([
                'message_id' => $messageid,
                'user_id' => (int)$recipient,
            ]);
        }
    }

    public function quick_enrol_check($user_id, $course_id) {
        $context = \context_course::instance($course_id);
        $enrolled = is_enrolled($context, $user_id, '', true);
        return $enrolled;
    }
    /**
     * Returns the message recipients of a given status that are associated with this message
     *
     * Optionally, returns as an array of user ids
     *
     * @param  string  $status               sent|unsent|all
     * @param  bool    $asuseridarray
     * @return array
     */
    public function get_message_recipients($status = 'all', $asuseridarray = false) {
        $messageid = $this->get('id');

        // Do a quick check and see if this particular message is meant for ALL.
        // if ($this->check_course_msg()) {
            // There is a record for ALL people. Let's NOW create all the
            // recipients (most up to date with add/drops) and then send.
            // $this->populate_recip_course_msg();
        // }

        // Be sure we have a valid status.
        if (!in_array($status, ['all', 'sent', 'unsent'])) {
            $status = 'all';
        }

        // Get recipients based on status.

        // All.
        if ($status == 'all') {
            $recipients = message_recipient::get_records(['message_id' => $messageid]);

            // Unsent.
        } else if ($status == 'unsent') {
            $recipients = message_recipient::get_records(['message_id' => $messageid, 'sent_at' => 0]);

            // Sent.
        } else {
            global $DB;

            $recordset = $DB->get_recordset_sql("
            SELECT *
            FROM {block_quickmail_msg_recips} mr
            WHERE mr.message_id = ?
            AND mr.sent_at <> 0", [$messageid]);

            // Iterate through recordset, instantiate persistents, add to array.
            $recipients = [];
            foreach ($recordset as $record) {
                $recipients[] = new message_recipient(0, $record);
            }
            $recordset->close();
        }
        $checkedrecipients = [];
        foreach ($recipients as $recip) {
            if ($this->quick_enrol_check($recip->get('user_id'), $this->get('course_id'))) {
                $checkedrecipients[] = $recip;
            }
        }

        if (!$asuseridarray) {
            return $checkedrecipients;
        }

        $recipientids = array_reduce($checkedrecipients, function ($carry, $recipient) {
            $carry[] = $checkedrecipients->get('user_id');

            return $carry;
        }, []);

        return $recipientids;
    }

    /**
     * Returns an array of this message's recipient of a given status as user objects which contain the given properties
     *
     * @param  string  $status             sent|unsent|all
     * @param  string  $userproperties    moodle user properties that should be included in the return object
     * @return array   keyed by user id
     */
    public function get_message_recipient_users($status = 'all', $userproperties = 'email,firstname,lastname') {
        // Get an array of user ids from this message's recipients.
        $userids = array_map(function($recip) use ($status) {
            return $recip->get('user_id');
        }, $this->get_message_recipients($status));

        global $DB;

        // Fetch limited user object from these ids.
        $users = $DB->get_records_list('user', 'id', $userids, '', $userproperties);

        return $users;
    }

    /**
     * Returns any single stored user filter value for this message
     *
     * Note: this will only return a value for "broadcast" messages with a valid draft recipient filter value set
     *
     * @return mixed, defaults to empty string
     */
    public function get_broadcast_draft_recipient_filter() {
        if ($this->get_message_scope() !== 'broadcast') {
            return '';
        }

        $recipients = $this->get_message_draft_recipients();

        if (!$recip = reset($recipients)) {
            return '';
        }

        if (!$filter = $recip->get('recipient_filter')) {
            return '';
        }

        return @unserialize($filter);
    }

    /**
     * Returns the message draft recipients that are associated with this message
     *
     * @return array
     */
    public function get_message_draft_recipients($type = '', $askeyarray = false) {
        $messageid = $this->get('id');

        $params = [
            'message_id' => $messageid
        ];

        if ($type) {
            $params['type'] = $type;
        }

        $recipients = message_draft_recipient::get_records($params);

        if (!$askeyarray) {
            return $recipients;
        }

        $keyarray = array_map(function($recipient) {
            return $recipient->get_recipient_key();
        }, $recipients);

        return $keyarray;
    }

    /**
     * Returns the message attachments that are associated with this message
     *
     * @return array
     */
    public function get_message_attachments() {
        $messageid = $this->get('id');

        $attachments = message_attachment::get_records(['message_id' => $messageid]);

        return $attachments;
    }

    // The Getters.
    /**
     * Returns the status of this message
     *
     * @return string  deleted|drafted|queued|sending|sent
     */
    public function get_status() {
        if ($this->is_being_sent()) {
            return block_quickmail_string::get('sending');
        }

        if ($this->is_soft_deleted()) {
            return block_quickmail_string::get('deleted');
        }

        if ($this->is_message_draft()) {
            return block_quickmail_string::get('drafted');
        }

        if ($this->is_queued_message()) {
            return block_quickmail_string::get('queued');
        }

        return block_quickmail_string::get('sent');
    }

    public function get_to_send_in_future() {
        return $this->get('to_send_at') > time();
    }

    public function get_subject_preview($length = 20) {
        return $this->render_preview_string('subject', $length, '...', block_quickmail_string::get('preview_no_subject'));
    }

    public function get_body_preview($length = 40) {
        return strip_tags($this->render_preview_string('body', $length, '...', block_quickmail_string::get('preview_no_body')));
    }

    public function get_readable_created_at() {
        return $this->get_readable_date('timecreated');
    }

    public function get_readable_last_modified_at() {
        return $this->get_readable_date('timemodified');
    }

    public function get_readable_sent_at() {
        return $this->get_readable_date('sent_at');
    }

    public function get_readable_to_send_at() {
        return $this->get_readable_date('to_send_at');
    }

    /**
     * Reports whether or not this message is a draft
     *
     * @return bool
     */
    public function is_message_draft() {
        return (bool) $this->get('is_draft');
    }

    /**
     * Returns the "scope" of this message
     *
     * broadcast = site-level admin message
     * compose   = course-level message
     *
     * @return bool
     */
    public function get_message_scope() {
        return $this->get('course_id') == SITEID
            ? 'broadcast'
            : 'compose';
    }

    public function get_substitution_code_classes() {
        return substitution_code::get_code_classes_from_message($this);
    }

    /**
     * Reports whether or not this message is queued to be sent
     *
     * @return bool
     */
    public function is_queued_message() {
        $tobesent = (bool) $this->get('to_send_at');

        return (bool) $tobesent && ! $this->is_sent_message();
    }

    /**
     * Reports whether or not this message is marked as being sent at the moment
     *
     * @return bool
     */
    public function is_being_sent() {
        return (bool) $this->get('is_sending');
    }

    /**
     * Reports whether or not this message needs to send a receipt email
     *
     * @return bool
     */
    public function should_send_receipt() {
        return (bool) $this->get('send_receipt');
    }

    /**
     * Reports whether or not this message is marked as sent
     *
     * @return bool
     */
    public function is_sent_message() {
        return (bool) $this->get('sent_at');
    }

    /**
     * Returns the cached intended recipient count total for this message
     *
     * Attempts to set the total in the cache if not found
     *
     * @return int
     */
    public function cached_recipient_count() {
        $message = $this;

        return (int) block_quickmail_cache::store('qm_msg_recip_count')->add($this->get('id'), function() use ($message) {
            return count($message->get_message_recipients());
        });
    }

    /**
     * Returns the cached intended additional email count total for this message
     *
     * Attempts to set the total in the cache if not found
     *
     * @return int
     */
    public function cached_additional_email_count() {
        $message = $this;

        return (int) block_quickmail_cache::store('qm_msg_addl_email_count')->add($this->get('id'), function() use ($message) {
            return count($message->get_additional_emails());
        });
    }

    /**
     * Returns the cached file attachment count total for this message
     *
     * Attempts to set the total in the cache if not found
     *
     * @return int
     */
    public function cached_attachment_count() {
        $message = $this;

        return (int) block_quickmail_cache::store('qm_msg_attach_count')->add($this->get('id'), function() use ($message) {
            return count($message->get_message_attachments());
        });
    }

    // The Setters.
    /**
     * Update this message as having sent a receipt message
     *
     * @return void
     */
    public function mark_receipt_as_sent() {
        $this->set('send_receipt', 0);

        $this->update();
    }

    /**
     * Update this message's sending status to currently sending
     *
     * @return void
     */
    public function mark_as_sending() {
        $this->set('is_sending', 1);

        $this->update();
    }

    /**
     * Update this message as deleted
     *
     * @return void
     */
    public function mark_as_deleted() {
        $this->set('deleted', 1);
        $this->update();
    }

    // Persistent Hooks.
    /**
     * After delete hook
     *
     * @param  bool  $result
     * @return void
     */
    protected function after_delete($result) {
        // If this was a draft message (which are hard deleted), delete all related data.
        if ($this->is_message_draft()) {
            message_recipient::clear_all_for_message($this);
            message_draft_recipient::clear_all_for_message($this);
            message_additional_email::clear_all_for_message($this);
            message_attachment::clear_all_for_message($this);
        }
    }

    // Composition Methods.
    /**
     * Creates a new "compose" (course-scoped) or "broadcast" (site-scoped) message from the given sending user, course, and data
     *
     * @param  string  $type  compose|broadcast
     * @param  object  $user  moodle user
     * @param  object  $course  moodle course
     * @param  object  $data  transformed compose request data
     * @param  bool    $isdraft  whether or not this is a draft message
     * @return message
     */
    public static function create_type($type, $user, $course, $data, $isdraft = false) {
        // Create a new message.
        $message = self::create_new([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'message_type' => $data->message_type,
            'alternate_email_id' => $data->alternate_email_id,
            'signature_id' => $data->signature_id,
            'subject' => $data->subject,
            'body' => $data->message,
            'send_receipt' => $data->receipt,
            'to_send_at' => $data->to_send_at,
            'no_reply' => $data->no_reply,
            'send_to_mentors' => $data->mentor_copy,
            'is_draft' => (int) $isdraft
        ]);

        return $message;
    }

    /**
     * Creates a new "compose" (course-scoped) message from a notification
     *
     * Note: if no recipients are given, message will not be created
     *
     * @param  notification  $notification
     * @param  array         $recipientuserids   array of user ids to receive this notification message
     * @param  int           $timetosend   unix timestamp of time this message should be sent, defaults to ASAP
     * @return message
     * @throws \Exception
     */
    public static function create_from_notification(notification $notification, $recipientuserids, $timetosend = null) {
        if (!$course = $notification->get_course()) {
            throw new \Exception('Course no longer exists!');
        }

        if (!$user = $notification->get_user()) {
            throw new \Exception('User no longer exists!');
        }

        $message = self::create_new([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'message_type' => $notification->get('message_type'),
            'notification_id' => $notification->get('id'),
            'alternate_email_id' => $notification->get('alternate_email_id'),
            'signature_id' => $notification->get('signature_id'),
            'subject' => $notification->get('subject'),
            'body' => $notification->get('body'),
            'send_receipt' => $notification->get('send_receipt'),
            'to_send_at' => ! empty($timetosend) ? $timetosend : time() - 1,
            'no_reply' => $notification->get('no_reply'),
            'send_to_mentors' => $notification->get('send_to_mentors'),
            'is_draft' => 0
        ]);

        $message->sync_recipients($recipientuserids);

        return $message;
    }

    /**
     * Updates this draft message with the given data
     *
     * @param  object  $data      transformed compose/broadcast request data
     * @param  bool    $isdraft  whether or not this draft is still a draft after this update
     * @return message
     */
    public function update_draft($data, $isdraft = null) {
        if (is_null($isdraft)) {
            $isdraft = $this->is_message_draft();
        }

        $this->set('alternate_email_id', $data->alternate_email_id);
        $this->set('subject', $data->subject);
        $this->set('body', $data->message);
        $this->set('message_type', $data->message_type);
        $this->set('signature_id', $data->signature_id);
        $this->set('send_receipt', $data->receipt);
        $this->set('to_send_at', $data->to_send_at);
        $this->set('no_reply', $data->no_reply);
        $this->set('send_to_mentors', $data->mentor_copy);
        $this->set('is_draft', (bool) $isdraft);
        $this->update();

        // Return a refreshed message record.
        return $this->read();
    }

    /**
     * Replaces all recipients for this message with the given array of user ids
     *
     * @param  array  $recipientuserids
     * @return void
     */
    public function sync_recipients($recipientuserids = []) {
        // Clear all current recipients.
        message_recipient::clear_all_for_message($this);

        $count = 0;

        // Add all new recipients.
        foreach ($recipientuserids as $userid) {
            // If any exceptions, proceed gracefully to the next.
            try {
                message_recipient::create_for_message($this, ['user_id' => $userid]);
                $count++;
            } catch (\Exception $e) {
                // Most likely invalid user, exception thrown due to validation error
                // Log this?
                continue;
            }
        }

        // Cache the count for external use.
        block_quickmail_cache::store('qm_msg_recip_count')->put($this->get('id'), $count);

        // Refresh record - necessary?
        $this->read();
    }

    /**
     * Replaces all recipients for this message with the given array of user ids
     *
     * @param  array  $recipientuserids
     * @return void
     */
    public function sync_course_msg($course = 0) {
        // Clear all current recipients.
        message_course::clear_all_for_message($this);

        message_course::create_for_message($this, ['course_id' => $course]);

        // Add all new recipients.
        // foreach ($recipientuserids as $userid) {
        //     // If any exceptions, proceed gracefully to the next.
        //     try {
        //         message_recipient::create_for_message($this, ['user_id' => $userid]);
        //         $count++;
        //     } catch (\Exception $e) {
        //         // Most likely invalid user, exception thrown due to validation error
        //         // Log this?
        //         continue;
        //     }
        // }

        // // Cache the count for external use.
        // block_quickmail_cache::store('qm_msg_recip_count')->put($this->get('id'), $count);

        // // Refresh record - necessary?
        // $this->read();
    }

    /**
     * Replaces all "draft recipients" for this "compose" message with the given arrays of entity keys
     *
     * @param  array  $includekeycontainer  [role_*, group_*, user_*]
     * @return void
     */
    public function sync_compose_draft_recipients($includekeycontainer = [], $excludekeycontainer = []) {
        // Clear all current draft recipients.
        message_draft_recipient::clear_all_for_message($this);

        // Iterate through allowed "inclusion types".
        foreach (['include', 'exclude'] as $type) {
            $keycontainer = $type . 'keycontainer';
            // Iterate through the given named key container.
            foreach ($$keycontainer as $key) {
                $exploded = explode('_', $key);

                // If the key was a valid value.
                if (count($exploded) == 2 && in_array($exploded[0], ['role', 'group', 'user'])) {
                    // Set the attributes appropriately.
                    $recipienttype = $exploded[0];
                    $recipientid = $exploded[1];

                    // If the id is (potentially) valid.
                    if (is_numeric($recipientid)) {
                        // Create a record.
                        message_draft_recipient::create_for_message($this, [
                            'type' => $type,
                            'recipient_type' => $recipienttype,
                            'recipient_id' => $recipientid,
                        ]);
                    }
                }
            }
        }

        // Refresh record (necessary?).
        $this->read();
    }

    /**
     * Replaces all "draft recipients" for this "broadcast" message with the given filter string
     *
     * @param  string  $filtervalue   a serialized string
     * @return void
     */
    public function sync_broadcast_draft_recipients($filtervalue) {
        // Clear all current draft recipients.
        message_draft_recipient::clear_all_for_message($this);

        // Create a record.
        message_draft_recipient::create_for_message($this, [
            'type' => 'include',
            'recipient_type' => 'filter',
            'recipient_filter' => $filtervalue,
        ]);

        // Refresh record - necessary?
        $this->read();
    }

    /**
     * Replaces all additional emails for this message with the given array of emails
     *
     * @param  array  $additionalemails
     * @return void
     */
    public function sync_additional_emails($additionalemails = []) {
        // Clear all current additional emails.
        message_additional_email::clear_all_for_message($this);

        $count = 0;

        // Add all new additional emails.
        foreach ($additionalemails as $email) {
            // If the email is invalid, proceed gracefully to the next.
            try {
                message_additional_email::create_for_message($this, ['email' => $email]);
                $count++;
            } catch (\Exception $e) {
                // Most likely exception thrown due to validation error?
                // Log this?
                continue;
            }
        }

        // Cache the count for external use.
        block_quickmail_cache::store('qm_msg_addl_email_count')->put($this->get('id'), $count);

        // Refresh record - necessary?
        $this->read();
    }

    /**
     * Unqueue this message and move to draft status
     *
     * @return message
     */
    public function unqueue() {
        $this->set('to_send_at', 0);
        $this->set('is_draft', true);
        $this->update();

        // Return a refreshed message record.
        return $this->read();
    }

    /**
     * Returns a message of the given id which must belong to the given user id
     *
     * @param  int  $messageid
     * @param  int  $userid
     * @return mixed
     */
    public static function find_owned_by_user_or_null($messageid, $userid) {
        if (!$message = self::find_or_null($messageid)) {
            return null;
        }

        if (!$message->is_owned_by_user($userid)) {
            return null;
        }

        return $message;
    }

    // Send Methods.
    /**
     * Attempt to send this message immediately
     *
     * @return void
     */
    public function send() {
        // Instantiate a messenger of this message.
        $messenger = new messenger($this);

        // Send the message.
        $messenger->send();
    }

    // Utilities.
    /**
     * Returns an array of messages belonging to a specific course given an array of messages and course id
     *
     * @param  array  $messages
     * @param  int    $courseid
     * @return array
     */
    public static function filter_messages_by_course($messages, $courseid) {
        if ($courseid) {
            // If a course is selected, filter out any not belonging to the course and return.
            return array_filter($messages, function($msg) use ($courseid) {
                return $msg->get('course_id') == $courseid;
            });
        }

        // Otherwise, include all messages.
        return $messages;
    }

}

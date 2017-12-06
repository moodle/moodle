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
 * The mod_dataform dataform notification observer.
 *
 * @package    mod_dataform
 * @copyright  2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_dataform\observer;

defined('MOODLE_INTERNAL') || die();

class notification {

    /**
     * Returns notification observers for all Dataform events.
     *
     * @return array
     */
    public static function observers() {
        global $CFG;

        $observers = array();
        foreach (get_directory_list("$CFG->dirroot/mod/dataform/classes/event") as $filename) {
            if (strpos($filename, '_base.php') !== false) {
                continue;
            }
            $name = basename($filename, '.php');
            $observers[] = array(
                'eventname' => "\\mod_dataform\\event\\$name",
                'callback' => '\mod_dataform\observer\notification::notify',
            );
        }

        return $observers;
    }

    /**
     * Takes notification data and sends a message or email.
     * Expected data:
     * - sender => stdClass (User object)
     * - subject => string
     * - content => string
     * - contentformat => int (e.g. FORMAT_HTML). Optional; default FORMAT_PLAIN
     * - notification => bool|int
     * - recipients => array (Array of user objects)
     * - recipientemails => array (Array of email addresses)
     *
     * @param array $data Notification data
     * @return array
     */
    public static function notify(\core\event\base $event) {
        $dataformid = $event->other['dataid'];
        $man = \mod_dataform_notification_manager::instance($dataformid);

        $result = false;
        if ($rules = $man->get_type_rules_enabled()) {
            $params = array();
            $params['event'] = $event->eventname;
            $params['dataformid'] = $dataformid;
            $params['viewid'] = !empty($event->other['viewid']) ? $event->other['viewid'] : null;
            $params['entryid'] = !empty($event->other['entryid']) ? $event->other['entryid'] : null;

            foreach ($rules as $rule) {
                if ($rule->is_applicable($params)) {
                    $notification = new notification;
                    $message = $notification->prepare_data($event, $rule);
                    $result = ($result or $notification->send_message($message));
                }
            }
        }
        return $result;
    }

    /**
     * Takes notification data and sends a message or email.
     * Expected data:
     * - sender => stdClass (User object)
     * - subject => string
     * - content => string
     * - contentformat => int (e.g. FORMAT_HTML). Optional; default FORMAT_PLAIN
     * - notification => bool|int
     * - recipients => array (Array of user objects)
     * - recipientemails => array (Array of email addresses)
     *
     * @param array $data Notification data
     * @return array
     */
    public function send_message($data) {
        global $SITE;
        $res = array();

        $message = new \stdClass;
        $message->siteshortname   = format_string($SITE->fullname);
        $message->component       = 'mod_dataform';
        $message->name            = 'dataform_notification';
        $message->userfrom        = $data['sender'];
        $message->subject         = $data['subject'];
        $message->fullmessage     = $data['content'];
        $message->fullmessageformat = $data['contentformat'];
        $message->fullmessagehtml = !empty($data['contenthtml']) ? $data['contenthtml'] : $message->fullmessage;
        $message->smallmessage    = $message->fullmessagehtml;
        $message->notification    = (int) !empty($data['notification']);

        // Send message.
        if (!empty($data['recipients'])) {
            $res['method'] = 'message';

            // Message provider name.
            if (!empty($data['name'])) {
                $message->name = $data['name'];
            }

            foreach ($data['recipients'] as $recipient) {
                $message->userto = $recipient;

                if (!empty($recipient->mailformat) and $recipient->mailformat == FORMAT_HTML) {
                    $message->fullmessagehtml = format_text($message->fullmessagehtml, FORMAT_HTML);
                }

                $res[$recipient->id] = message_send($message);
            }
        }

        // Send email.
        if (!empty($data['recipientemails'])) {
            $res['method'] = 'email';

            foreach ($data['recipientemails'] as $recipient) {
                // Email directly rather than using the messaging system to ensure its not routed to a popup or jabber.
                $res[$recipient->id] = email_to_user(
                    $recipient,
                    $message->userfrom,
                    $message->subject,
                    $message->fullmessage,
                    $message->fullmessagehtml,
                    '', // Attachment.
                    '', // Attachname.
                    false, // Usetrueaddress.
                    '' // CFG forum_replytouser.
                );
            }
        }

        return $res;
    }

    /**
     *
     */
    public function prepare_data($event, $rule) {
        $data = $rule->block->get_data($event);
        $context = $rule->context;

        // Adjust sender.
        if (!empty($data->sender)) {
            // Get entry author id for sender author where applicable.
            if ($data->sender == 'author' and $event->relateduserid) {
                $data->sender = $event->relateduserid;
            }

            // Get event user id for sender where applicable.
            if ($data->sender == 'event' and $event->userid) {
                $data->sender = $event->userid;
            }
        }

        // Get entry author id for recipient author where applicable.
        if (!empty($data->recipient['author']) and $event->relateduserid) {
            $data->recipient['author'] = $event->relateduserid;
        }

        $content = $this->get_content($event, $data);
        $contenthtml = $content;

        // Prepare the message data.
        $message = array();
        $message['subject'] = $this->get_subject($event, $data);
        $message['content'] = $content;
        $message['contentformat'] = $this->get_content_format($data);
        $message['contenthtml'] = $contenthtml;
        $message['sender'] = $this->get_sender_user($data);
        $message['recipients'] = $this->get_recipient_users($data, $context);
        $message['recipientemails'] = $this->get_recipient_email_users($data);
        $message['notification'] = (int) empty($data->messagetype);

        return $message;
    }

    /**
     *
     * @param stdClass $data
     * @return string the subject text you want to send
     */
    protected function get_subject($event, $data) {
        $subject = !empty($data->subject) ? $data->subject : $event->get_name();
        return $subject;
    }

    /**
     *
     * @param stdClass $data
     * @return string the text you want to send
     */
    protected function get_content($event, $data) {
        $content = !empty($data->message) ? $data->message : $event->get_description();
        return $content;
    }

    /**
     *
     * @param stdClass $data
     * @return int format
     */
    protected function get_content_format($data) {
        $format = !empty($data->messageformat) ? $data->messageformat : FORMAT_PLAIN;
        return $format;
    }

    /**
     * format the html-part of the email
     *
     * @param stdClass $data
     * @return string the html you want to send
     */
    protected function get_content_html($data) {
        $contenthtml = !empty($data->contenthtml) ? $data->contenthtml : null;
        return $contenthtml;
    }


    /**
     * Returns sender user by id specified in $data->sender.
     *
     * @param stdClass $data
     * @return object $user
     */
    protected function get_sender_user($data) {
        global $DB, $USER;

        // No reply.
        if (empty($data->sender)) {
            $data->sender = \core_user::NOREPLY_USER;
        }

        return \core_user::get_user($data->sender);
    }

    /**
     * Returns list of recipient users.
     *
     * @param stdClass $data
     * @return array user objects
     */
    protected function get_recipient_users($data, $context) {
        $recipients = array();

        // Admin.
        if (!empty($data->recipient['admin'])) {
            $user = get_admin();
            $recipients[$user->id] = $user;
        }

        // Support.
        if (!empty($data->recipient['support'])) {
            if ($user = \core_user::get_support_user()) {
                $recipients[$user->id] = $user;
            }
        }

        // Author.
        if (!empty($data->recipient['author'])) {
            $recipients[$data->recipient['author']] = \core_user::get_user($data->recipient['author']);
        }

        // Username.
        if (!empty($data->recipient['username'])) {
            $usernames = explode(',', $data->recipient['username']);
            foreach ($usernames as $username) {
                if ($user = \core_user::get_user_by_username($username)) {
                    $recipients[$user->id] = $user;
                }
            }
        }

        // Notification roles.
        if (!empty($config->recipient['role'])) {
            if ($users = get_users_by_capability($context, 'mod/dataform:notification')) {
                foreach ($users as $userid => $user) {
                    $recipients[$userid] = $user;
                }
            }
        }

        return $recipients;
    }

    /**
     *
     *
     * @param stdClass $data
     * @return array user objects
     */
    protected function get_recipient_email_users($data) {
        $recipients = array();

        if (!empty($data->recipient['email'])) {
            $emails = explode(',', $data->recipient['email']);
            $userfields = array_fill_keys(get_all_user_name_fields(), '');
            foreach ($emails as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
                    $user = (object) $userfields;
                    $user->id = -1;
                    $user->email = $email;
                    $user->firstname = 'emailuser';
                    $user->lastname = '';
                    $user->maildisplay = true;
                    $user->mailformat = $this->get_content_format($data);
                    $recipients[] = $user;
                }
            }
        }
        return $recipients;
    }
}

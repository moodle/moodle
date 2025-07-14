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
 * Contains the definiton of the email message processors (sends messages to users via email)
 *
 * @package   message_email
 * @copyright 2008 Luis Rodrigues and Martin Dougiamas
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/message/output/lib.php');

/**
 * The email message processor
 *
 * @package   message_email
 * @copyright 2008 Luis Rodrigues and Martin Dougiamas
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_output_email extends message_output {
    /**
     * Processes the message (sends by email).
     * @param object $eventdata the event data submitted by the message sender plus $eventdata->savedmessageid
     */
    function send_message($eventdata) {
        global $CFG, $DB;

        // skip any messaging suspended and deleted users
        if ($eventdata->userto->auth === 'nologin' or $eventdata->userto->suspended or $eventdata->userto->deleted) {
            return true;
        }

        //the user the email is going to
        $recipient = null;

        //check if the recipient has a different email address specified in their messaging preferences Vs their user profile
        $emailmessagingpreference = get_user_preferences('message_processor_email_email', null, $eventdata->userto);
        $emailmessagingpreference = clean_param($emailmessagingpreference, PARAM_EMAIL);

        // If the recipient has set an email address in their preferences use that instead of the one in their profile
        // but only if overriding the notification email address is allowed
        if (!empty($emailmessagingpreference) && !empty($CFG->messagingallowemailoverride)) {
            //clone to avoid altering the actual user object
            $recipient = clone($eventdata->userto);
            $recipient->email = $emailmessagingpreference;
        } else {
            $recipient = $eventdata->userto;
        }

        // Check if we have attachments to send.
        $attachment = '';
        $attachname = '';
        if (!empty($CFG->allowattachments) && !empty($eventdata->attachment)) {
            if (empty($eventdata->attachname)) {
                // Attachment needs a file name.
                debugging('Attachments should have a file name. No attachments have been sent.', DEBUG_DEVELOPER);
            } else if (!($eventdata->attachment instanceof stored_file)) {
                // Attachment should be of a type stored_file.
                debugging('Attachments should be of type stored_file. No attachments have been sent.', DEBUG_DEVELOPER);
            } else {
                // Copy attachment file to a temporary directory and get the file path.
                $attachment = $eventdata->attachment->copy_content_to_temp();

                // Get attachment file name.
                $attachname = clean_filename($eventdata->attachname);
            }
        }

        // Configure mail replies - this is used for incoming mail replies.
        $replyto = '';
        $replytoname = '';
        if (isset($eventdata->replyto)) {
            $replyto = $eventdata->replyto;
            if (isset($eventdata->replytoname)) {
                $replytoname = $eventdata->replytoname;
            }
        }

        // We email messages from private conversations straight away, but for group we add them to a table to be sent later.
        $emailuser = true;
        if (!$eventdata->notification) {
            if ($eventdata->conversationtype == \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP) {
                $emailuser = false;
            }
        }

        if ($emailuser) {
            $result = email_to_user($recipient, $eventdata->userfrom, $eventdata->subject, $eventdata->fullmessage,
                $eventdata->fullmessagehtml, $attachment, $attachname, true, $replyto, $replytoname);
        } else {
            $messagetosend = new stdClass();
            $messagetosend->useridfrom = $eventdata->userfrom->id;
            $messagetosend->useridto = $recipient->id;
            $messagetosend->conversationid = $eventdata->convid;
            $messagetosend->messageid = $eventdata->savedmessageid;
            $result = $DB->insert_record('message_email_messages', $messagetosend, false);
        }

        // Remove an attachment file if any.
        if (!empty($attachment) && file_exists($attachment)) {
            unlink($attachment);
        }

        return $result;
    }

    /**
     * Creates necessary fields in the messaging config form.
     *
     * @param array $preferences An array of user preferences
     */
    function config_form($preferences){
        global $USER, $OUTPUT, $CFG;
        $string = '';

        $choices = array();
        $choices['0'] = get_string('textformat');
        $choices['1'] = get_string('htmlformat');
        $current = $preferences->mailformat;
        $string .= $OUTPUT->container(html_writer::label(get_string('emailformat'), 'mailformat'));
        $string .= $OUTPUT->container(html_writer::select($choices, 'mailformat', $current, false, array('id' => 'mailformat')));
        $string .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'userid', 'value' => $USER->id));

        if (!empty($CFG->allowusermailcharset)) {
            $choices = array();
            $charsets = get_list_of_charsets();
            if (!empty($CFG->sitemailcharset)) {
                $choices['0'] = get_string('site').' ('.$CFG->sitemailcharset.')';
            } else {
                $choices['0'] = get_string('site').' (UTF-8)';
            }
            $choices = array_merge($choices, $charsets);
            $current = $preferences->mailcharset;
            $string .= $OUTPUT->container(html_writer::label(get_string('emailcharset'), 'mailcharset'));
            $string .= $OUTPUT->container(
                html_writer::select($choices, 'preference_mailcharset', $current, false, array('id' => 'mailcharset'))
            );
        }

        if (!empty($CFG->messagingallowemailoverride)) {
            $inputattributes = array('size' => '30', 'name' => 'email_email', 'value' => $preferences->email_email,
                    'id' => 'email_email');
            $string .= html_writer::label(get_string('email', 'message_email'), 'email_email');
            $string .= $OUTPUT->container(html_writer::empty_tag('input', $inputattributes));

            if (empty($preferences->email_email) && !empty($preferences->userdefaultemail)) {
                $string .= $OUTPUT->container(get_string('ifemailleftempty', 'message_email', $preferences->userdefaultemail));
            }

            if (!empty($preferences->email_email) && !validate_email($preferences->email_email)) {
                $string .= $OUTPUT->container(get_string('invalidemail'), 'error');
            }

            $string .= '<br/>';
        }

        return $string;
    }

    /**
     * Parses the submitted form data and saves it into preferences array.
     *
     * @param stdClass $form preferences form class
     * @param array $preferences preferences array
     */
    function process_form($form, &$preferences){
        global $CFG;

        if (isset($form->email_email)) {
            $preferences['message_processor_email_email'] = clean_param($form->email_email, PARAM_EMAIL);
        }
        if (isset($form->preference_mailcharset)) {
            $preferences['mailcharset'] = $form->preference_mailcharset;
            if (!array_key_exists($preferences['mailcharset'], get_list_of_charsets())) {
                $preferences['mailcharset'] = '0';
            }
        }
        if (isset($form->mailformat) && isset($form->userid)) {
            require_once($CFG->dirroot.'/user/lib.php');

            $user = core_user::get_user($form->userid, '*', MUST_EXIST);
            $user->mailformat = clean_param($form->mailformat, PARAM_INT);
            user_update_user($user, false, false);
        }
    }

    /**
     * Returns the default message output settings for this output
     *
     * @return int The default settings
     */
    public function get_default_messaging_settings() {
        return MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED;
    }

    /**
     * Loads the config data from database to put on the form during initial form display
     *
     * @param array $preferences preferences array
     * @param int $userid the user id
     */
    function load_data(&$preferences, $userid){
        $preferences->email_email = get_user_preferences( 'message_processor_email_email', '', $userid);
    }

    /**
     * Returns true as message can be sent to internal support user.
     *
     * @return bool
     */
    public function can_send_to_any_users() {
        return true;
    }
}

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

namespace block_quickmail\messenger;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\messenger\messenger_interface;
use block_quickmail_config;
use block_quickmail_plugin;
use block_quickmail_string;
use block_quickmail_emailer;
use block_quickmail\persistents\message;
use block_quickmail\persistents\alternate_email;
use block_quickmail\persistents\message_recipient;
use block_quickmail\persistents\message_draft_recipient;
use block_quickmail\persistents\message_additional_email;
use block_quickmail\validators\message_form_validator;
use block_quickmail\validators\save_draft_message_form_validator;
use block_quickmail\requests\compose_request;
use block_quickmail\requests\broadcast_request;
use block_quickmail\exceptions\validation_exception;
use block_quickmail\messenger\factories\course_recipient_send\recipient_send_factory;
use block_quickmail\filemanager\message_file_handler;
use block_quickmail\filemanager\attachment_appender;
use block_quickmail\messenger\message\subject_prepender;
use block_quickmail\messenger\message\signature_appender;
use block_quickmail\repos\user_repo;
use moodle_url;
use html_writer;

class messenger implements messenger_interface {

    public $message;
    public $all_profile_fields;
    public $selected_profile_fields;

    public function __construct(message $message) {
        $this->message = $message;
        $this->all_profile_fields = block_quickmail_plugin::get_user_profile_field_array();
        $this->selected_profile_fields = block_quickmail_config::block('email_profile_fields');
    }

    // Message Composition Methods.
    /**
     * Creates a "compose" (course-scoped) message from the given user within the given course using the given form data
     *
     * Depending on the given form data, this message may be sent now or at some point in the future.
     * By default, the message delivery will be handled as individual adhoc tasks which are
     * picked up by a scheduled task.
     *
     * Optionally, a draft message may be passed which will use and update the draft information
     *
     * @param  object   $user            moodle user sending the message
     * @param  object   $course          course in which this message is being sent
     * @param  array    $formdata       message parameters which will be validated
     * @param  message  $draftmessage   a draft message (optional, defaults to null)
     * @param  bool     $sendastasks   if false, the message will be sent immediately
     * @return message
     * @throws validation_exception
     * @throws critical_exception
     */
    public static function compose($user, $course, $formdata, $draftmessage = null, $sendastasks = true) {
        // Validate basic message form data.
        self::validate_message_form_data($formdata, 'compose');

        // Get transformed (valid) post data.
        $transformeddata = compose_request::get_transformed_post_data($formdata);

        // Get a message instance for this type, either from draft or freshly created.
        $message = self::get_message_instance('compose', $user, $course, $transformeddata, $draftmessage, false);

        // TODO: Handle posted file attachments (moodle).
        $coursecontext = \context_course::instance($course->id);
        file_save_draft_area_files($transformeddata->attachments_draftitem_id,
                                   $coursecontext->id,
                                   'block_quickmail',
                                   'attachments',
                                   $message->get('id'),
                                   block_quickmail_config::get_filemanager_options());

        $transformeddata->message = file_save_draft_area_files($transformeddata->message_draftitem_id,
                                                               $coursecontext->id,
                                                               'block_quickmail',
                                                               'message_editor',
                                                               $message->get('id'),
                                                               block_quickmail_config::get_filemanager_options(),
                                                               $transformeddata->message);
        $message->set('body', $transformeddata->message);
        $message->update();

        
        // Make sure there are no duplicates in the incoming arrays.
        $includedentityids = array_unique($transformeddata->included_entity_ids);
        
        // Determine whether or not we're sending to all.
        $sendingtoall = in_array('all', $includedentityids);
        if ($sendingtoall) {

            return self::save_course_message(
                $message,
                $formdata,
                $transformeddata->additional_emails,
                $user,
                $course
            );
        }

        // Get only the resolved recipient user ids.
        $recipientuserids = user_repo::get_unique_course_user_ids_from_selected_entities(
            $course,
            $user,
            $transformeddata->included_entity_ids,
            $transformeddata->excluded_entity_ids
        );

        return self::send_message_to_recipients(
            $message,
            $formdata,
            $transformeddata->additional_emails,
            $recipientuserids,
            $sendastasks
        );
    }

    /**
     * Creates an "broadcast" (admin, site-scoped) message from the given user using the given user filter and form data
     *
     * Depending on the given form data, this message may be sent now or at some point in the future.
     * By default, the message delivery will be handled as individual adhoc tasks which are
     * picked up by a scheduled task.
     *
     * Optionally, a draft message may be passed which will use and update the draft information
     *
     * @param  object                                       $user                      moodle user sending the message
     * @param  object                                       $course                    the moodle "SITEID" course
     * @param  array                                        $formdata                  message parameters which will be validated
     * @param  block_quickmail_broadcast_recipient_filter   $broadcastrecipientfilter
     * @param  message                                      $draftmessage              (optional, defaults to null)
     * @param  bool                                         $sendastasks               if false, the message will send immediately
     * @return message
     * @throws validation_exception
     * @throws critical_exception
     */
    public static function broadcast(
        $user,
        $course,
        $formdata,
        $broadcastrecipientfilter,
        $draftmessage = null,
        $sendastasks = true) {
        // Validate basic message form data.
        self::validate_message_form_data($formdata, 'broadcast');

        // Be sure that we have at least one recipient from the given recipient filter results.
        if (!$broadcastrecipientfilter->get_result_user_count()) {
            throw new validation_exception(block_quickmail_string::get('validation_exception_message'),
                block_quickmail_string::get('no_included_recipients_validation'));
        }

        // Get transformed (valid) post data.
        $transformeddata = broadcast_request::get_transformed_post_data($formdata);

        // Get a message instance for this type, either from draft or freshly created.
        $message = self::get_message_instance('broadcast', $user, $course, $transformeddata, $draftmessage, false);

        // Get the filtered recipient user ids.
        $recipientuserids = $broadcastrecipientfilter->get_result_user_ids();

        return self::send_message_to_recipients(
            $message,
            $formdata,
            $transformeddata->additional_emails,
            $recipientuserids,
            $sendastasks
        );
    }

    private static function save_course_message($message, $formdata, $additionalemails, $user, $course) {

        // Handle saving and syncing of any uploaded file attachments.
        message_file_handler::handle_posted_attachments($message, $formdata, 'attachments');

        // Clear any draft recipients for this message, unnecessary at this point.
        message_draft_recipient::clear_all_for_message($message);

        // Clear any existing recipients, and add those that have been recently submitted.
        $message->sync_course_msg($course->id);

        // Clear any existing additional emails, and add those that have been recently submitted.
        $message->sync_additional_emails($additionalemails);

        // If not scheduled for delivery later.
        // if (!$message->get_to_send_in_future()) {
        //     // Get the block's configured "send now threshold" setting.
        //     $sendnowthreshold = (int) block_quickmail_config::get('send_now_threshold');

        //     // If not configured to send as tasks OR the number of recipients is below the send now threshold.
        //     if (!$sendastasks || (!empty($sendnowthreshold) && count($recipientuserids) <= $sendnowthreshold)) {
        //         // Begin sending now.
        //         $message->mark_as_sending();
        //         $messenger = new self($message);
        //         $messenger->send();

        //         return $message->read();
        //     }
        // }

        return $message;

     }
    /**
     * Handles sending a given message to the given recipient user ids
     *
     * This will clear any draft-related data for the message, and sync it's recipients/additional emails
     *
     * @param  message  $message              message object instance being sent
     * @param  array    $formdata            posted moodle form data (used for file attachment purposes)
     * @param  array    $additionalemails    array of additional email addresses to send to, optional, defaults to empty
     * @param  array    $recipientuserids   moodle user ids to receive the message
     * @param  bool     $sendastasks        if false, the message will be sent immediately
     * @return message
     * @throws critical_exception
     */
    private static function send_message_to_recipients(
        $message,
        $formdata,
        $additionalemails,
        $recipientuserids = [],
        $sendastasks = true) {
        
        // Handle saving and syncing of any uploaded file attachments.
        message_file_handler::handle_posted_attachments($message, $formdata, 'attachments');

        // Clear any draft recipients for this message, unnecessary at this point.
        message_draft_recipient::clear_all_for_message($message);

        // Clear any existing recipients, and add those that have been recently submitted.
        $message->sync_recipients($recipientuserids);

        // Clear any existing additional emails, and add those that have been recently submitted.
        $message->sync_additional_emails($additionalemails);

        // If not scheduled for delivery later.
        if (!$message->get_to_send_in_future()) {
            // Get the block's configured "send now threshold" setting.
            $sendnowthreshold = (int) block_quickmail_config::get('send_now_threshold');

            // If not configured to send as tasks OR the number of recipients is below the send now threshold.
            if (!$sendastasks || (!empty($sendnowthreshold) && count($recipientuserids) <= $sendnowthreshold)) {
                // Begin sending now.
                $message->mark_as_sending();
                $messenger = new self($message);
                $messenger->send();

                return $message->read();
            }
        }

        return $message;
    }

    // Message Drafting Methods.
    /**
     * Creates a draft "compose" (course-scoped) message from the given user within the given course using the given form data
     *
     * Optionally, a draft message may be passed which will be updated rather than created anew
     *
     * @param  object   $user            moodle user sending the message
     * @param  object   $course          course in which this message is being sent
     * @param  array    $formdata       message parameters which will be validated
     * @param  message  $draftmessage   a draft message (optional, defaults to null)
     * @return message
     * @throws validation_exception
     * @throws critical_exception
     */
    public static function save_compose_draft($user, $course, $formdata, $draftmessage = null) {
        self::validate_draft_form_data($formdata, 'compose');

        // Get transformed (valid) post data.
        $transformeddata = compose_request::get_transformed_post_data($formdata);

        // Get a message instance for this type, either from draft or freshly created.
        $message = self::get_message_instance('compose', $user, $course, $transformeddata, $draftmessage, true);

        // Handle posted file attachments.
        $coursecontext = \context_course::instance($course->id);
        file_save_draft_area_files($transformeddata->attachments_draftitem_id,
                                   $coursecontext->id,
                                   'block_quickmail',
                                   'attachments',
                                   $message->get('id'),
                                   block_quickmail_config::get_filemanager_options());

        // Sync posted attachments to message record.
        $transformeddata->message = file_save_draft_area_files($transformeddata->message_draftitem_id,
                                                               $coursecontext->id,
                                                               'block_quickmail',
                                                               'message_editor',
                                                               $message->get('id'),
                                                               block_quickmail_config::get_filemanager_options(),
                                                               $transformeddata->message);
        $message->set('body', $transformeddata->message);
        $message->update();

        // Clear any existing draft recipients, and add those that have been recently submitted.
        $message->sync_compose_draft_recipients($transformeddata->included_entity_ids, $transformeddata->excluded_entity_ids);

        // Get only the resolved recipient user ids.
        $recipientuserids = user_repo::get_unique_course_user_ids_from_selected_entities(
            $course,
            $user,
            $transformeddata->included_entity_ids,
            $transformeddata->excluded_entity_ids
        );

        // Clear any existing recipients, and add those that have been recently submitted.
        $message->sync_recipients($recipientuserids);

        // Clear any existing additional emails, and add those that have been recently submitted.
        $message->sync_additional_emails($transformeddata->additional_emails);

        return $message;
    }

    /**
     * Creates a draft "broadcast" (system-scoped) message from the given user within the given course using the given form data
     *
     * Optionally, a draft message may be passed which will be updated rather than created anew
     *
     * @param  object                                       $user            moodle user sending the message
     * @param  object                                       $course          course in which this message is being sent
     * @param  array                                        $formdata       message parameters which will be validated
     * @param  block_quickmail_broadcast_recipient_filter   $broadcastrecipientfilter
     * @param  message                                      $draftmessage   a draft message (optional, defaults to null)
     * @return message
     * @throws validation_exception
     * @throws critical_exception
     */
    public static function save_broadcast_draft($user, $course, $formdata, $broadcastrecipientfilter, $draftmessage = null) {
        self::validate_draft_form_data($formdata, 'broadcast');

        // Get transformed (valid) post data.
        $transformeddata = broadcast_request::get_transformed_post_data($formdata);

        // Get a message instance for this type, either from draft or freshly created.
        $message = self::get_message_instance('broadcast', $user, $course, $transformeddata, $draftmessage, true);

        // Handle posted file attachments (moodle).
        $coursecontext = \context_course::instance($course->id);
        file_save_draft_area_files(
            $transformeddata->attachments_draftitem_id,
            $coursecontext->id,
            'block_quickmail',
            'attachments',
            $message->get('id'),
            block_quickmail_config::get_filemanager_options()
        );

        // Sync posted attachments to message record.
        $transformeddata->message = file_save_draft_area_files(
            $transformeddata->message_draftitem_id,
            $coursecontext->id,
            'block_quickmail',
            'message_editor',
            $message->get('id'),
            block_quickmail_config::get_filemanager_options(),
            $transformeddata->message
        );
        $message->set('body', $transformeddata->message);
        $message->update();

        // Clear any existing draft recipient filters, and add this recently submitted value.
        $message->sync_broadcast_draft_recipients($broadcastrecipientfilter->get_filter_value());

        // Get the filtered recipient user ids.
        $recipientuserids = $broadcastrecipientfilter->get_result_user_ids();

        // Clear any existing recipients, and add those that have been recently submitted.
        $message->sync_recipients($recipientuserids);

        // Clear any existing additional emails, and add those that have been recently submitted.
        $message->sync_additional_emails($transformeddata->additional_emails);

        return $message;
    }

    /**
     * Creates and returns a new message given a draft message id
     *
     * @param  int    $draftid
     * @param  object $user       the user duplicating the draft
     * @return message
     */
    public static function duplicate_draft($draftid, $user) {
        // Get the draft to be duplicated.
        if (!$originaldraft = new message($draftid)) {
            throw new validation_exception(block_quickmail_string::get('could_not_duplicate'));
        }

        // Make sure it's a draft.
        if (!$originaldraft->is_message_draft()) {
            throw new validation_exception(block_quickmail_string::get('must_be_draft_to_duplicate'));
        }

        // Check that the draft belongs to the given user id.
        if (!$originaldraft->is_owned_by_user($user->id)) {
            throw new validation_exception(block_quickmail_string::get('must_be_owner_to_duplicate'));
        }

        // Create a new draft message from the original's data.
        $newdraft = message::create_new([
            'course_id' => $originaldraft->get('course_id'),
            'user_id' => $originaldraft->get('user_id'),
            'message_type' => $originaldraft->get('message_type'),
            'alternate_email_id' => $originaldraft->get('alternate_email_id'),
            'signature_id' => $originaldraft->get('signature_id'),
            'subject' => $originaldraft->get('subject'),
            'body' => $originaldraft->get('body'),
            'editor_format' => $originaldraft->get('editor_format'),
            'is_draft' => 1,
            'send_receipt' => $originaldraft->get('send_receipt'),
            'no_reply' => $originaldraft->get('no_reply'),
            'usermodified' => $user->id
        ]);

        // TODO: Duplicate files.
        message_file_handler::duplicate_files($originaldraft, $newdraft, 'attachments');
        message_file_handler::duplicate_files($originaldraft, $newdraft, 'message_editor');

        // Duplicate the message recipients.
        foreach ($originaldraft->get_message_recipients() as $recipient) {
            message_recipient::create_new([
                'message_id' => $newdraft->get('id'),
                'user_id' => $recipient->get('user_id'),
            ]);
        }

        // Duplicate the message draft recipients.
        foreach ($originaldraft->get_message_draft_recipients() as $recipient) {
            message_draft_recipient::create_new([
                'message_id' => $newdraft->get('id'),
                'type' => $recipient->get('type'),
                'recipient_type' => $recipient->get('recipient_type'),
                'recipient_id' => $recipient->get('recipient_id'),
                'recipient_filter' => $recipient->get('recipient_filter'),
            ]);
        }

        // Duplicate the message additional emails.
        foreach ($originaldraft->get_additional_emails() as $additionalemail) {
            message_additional_email::create_new([
                'message_id' => $newdraft->get('id'),
                'email' => $additionalemail->get('email'),
            ]);
        }

        return $newdraft;
    }

    /**
     * Creates and returns a new message given a message id
     *
     * Note: this does not duplicate the intended recipient data
     *
     * @param  int     $messageid
     * @param  object  $user         the user duplicating the message
     * @return message
     */
    public static function duplicate_message($messageid, $user) {
        // Get the message to be duplicated.
        if (!$originalmessage = new message($messageid)) {
            throw new validation_exception(block_quickmail_string::get('could_not_duplicate'));
        }

        // Make sure it's not a draft.
        if ($originalmessage->is_message_draft()) {
            throw new validation_exception(block_quickmail_string::get('could_not_duplicate'));
        }

        // Check that the message belongs to the given user id.
        if (!$originalmessage->is_owned_by_user($user->id)) {
            throw new validation_exception(block_quickmail_string::get('must_be_owner_to_duplicate'));
        }

        // Create a new draft message from the original's data.
        $newdraft = message::create_new([
            'course_id' => $originalmessage->get('course_id'),
            'user_id' => $originalmessage->get('user_id'),
            'message_type' => $originalmessage->get('message_type'),
            'alternate_email_id' => $originalmessage->get('alternate_email_id'),
            'signature_id' => $originalmessage->get('signature_id'),
            'subject' => $originalmessage->get('subject'),
            'body' => $originalmessage->get('body'),
            'editor_format' => $originalmessage->get('editor_format'),
            'is_draft' => 1,
            'send_receipt' => $originalmessage->get('send_receipt'),
            'no_reply' => $originalmessage->get('no_reply'),
            'usermodified' => $user->id
        ]);

        // Duplicate files.
        message_file_handler::duplicate_files($originalmessage, $newdraft, 'attachments');
        message_file_handler::duplicate_files($originalmessage, $newdraft, 'message_editor');

        // Duplicate the message additional emails.
        foreach ($originalmessage->get_additional_emails() as $additionalemail) {
            message_additional_email::create_new([
                'message_id' => $newdraft->get('id'),
                'email' => $additionalemail->get('email'),
            ]);
        }

        return $newdraft;
    }

    /**
     * Validates message form data for a given message "type" (compose/broadcast)
     *
     * @param  array   $formdata   message parameters which will be validated
     * @param  string  $type        compose|broadcast
     * @return void
     * @throws validation_exception
     */
    private static function validate_message_form_data($formdata, $type) {
        $extraparams = $type == 'broadcast'
            ? ['is_broadcast_message' => true]
            : [];

        // Validate form data.
        $validator = new message_form_validator($formdata, $extraparams);
        $validator->validate();

        // If errors, throw exception.
        if ($validator->has_errors()) {
            throw new validation_exception(block_quickmail_string::get('validation_exception_message'), $validator->errors);
        }
    }

    /**
     * Validates draft message form data for a given message "type" (compose/broadcast)
     *
     * @param  array   $formdata   message parameters which will be validated
     * @param  string  $type        compose|broadcast
     * @return void
     * @throws validation_exception
     */
    private static function validate_draft_form_data($formdata, $type) {
        $extraparams = $type == 'broadcast'
            ? ['is_broadcast_message' => true]
            : [];

        // Validate form data.
        $validator = new save_draft_message_form_validator($formdata, $extraparams);
        $validator->validate();

        // If errors, throw exception.
        if ($validator->has_errors()) {
            throw new validation_exception(block_quickmail_string::get('validation_exception_message'), $validator->errors);
        }
    }

    /**
     * Returns a message object instance of the given type from the given params
     *
     * If a draft message is passed, the draft message will be updated to "non-draft" status and returned
     * otherwise, a new message instance will be created with the given user, course, and posted data
     *
     * @param  string  $type               compose|broadcast
     * @param  object  $user               auth user creating the message
     * @param  object  $course             scoped course for this message
     * @param  object  $transformeddata   transformed posted form data
     * @param  message $draftmessage
     * @param  bool    $isdraft           whether or not this instance is being resolved for purposes of saving as draft
     * @return message
     */
    private static function get_message_instance($type, $user, $course, $transformeddata, $draftmessage = null, $isdraft = false) {
        // If draft message was passed.
        if (!empty($draftmessage)) {
            // If draft message was already sent (shouldn't happen).
            if ($draftmessage->is_sent_message()) {
                throw new validation_exception(block_quickmail_string::get('critical_error'));
            }

            // Update draft message, and remove draft status.
            $message = $draftmessage->update_draft($transformeddata, $isdraft);
        } else {
            // Create new message.
            $message = message::create_type($type, $user, $course, $transformeddata, $isdraft);
        }

        return $message;
    }

    // Messenger instance methods.

    /**
     * Sends the message to all of its recipients
     *
     * @return void
     */
    public function send() {
        // Iterate through all message recipients.
        foreach ($this->message->get_message_recipients() as $recipient) {
            // If any exceptions are thrown, gracefully move to the next recipient.
            if (!$recipient->has_been_sent_to()) {
                // Verify the user still exists, edge cases have been found to have missing users.
                $tempuserid = (int)$recipient->get('user_id');
                $tempmsgid = (int)$recipient->get('message_id');

                if ($recipient->account_exists($tempuserid)) {
                    $recipient->remove_recipient_from_message($tempmsgid, $tempuserid);
                    continue;
                }
                try {
                    // Send to recipient now.
                    $this->send_to_recipient($recipient);
                } catch (\Exception $e) {
                    // TODO: handle a failed send here?
                    continue;
                }
            }
        }

        $this->handle_message_post_send();
    }

    /**
     * Sends the message to the given recipient
     *
     * @param  message_recipient  $recipient   message recipient to recieve the message
     * @return bool
     */
    public function send_to_recipient($recipient) {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');
        $coursecontext = \context_course::instance($this->message->get("course_id"));
        $body = file_rewrite_pluginfile_urls(
            $this->message->get("body"),
            'pluginfile.php',
            $coursecontext->id,
            'block_quickmail',
            'message_editor',
            $this->message->get('id'),
            [
                'includetoken' => true,
            ]
        );

        $this->message->set("body", $body);

        // Instantiate recipient_send_factory.
        $recipientsendfactory = recipient_send_factory::make(
            $this->message,
            $recipient,
            $this->all_profile_fields,
            $this->selected_profile_fields);

        // Send recipient_send_factory.
        $recipientsendfactory->send();

        return true;
    }

    /**
     * Performs post-send actions
     *
     * @return void
     */
    public function handle_message_post_send() {
        // Send to any additional emails (if any).
        $this->send_message_additional_emails();

        // Send receipt message (if applicable).
        if ($this->message->should_send_receipt()) {
            $this->send_message_receipt();
        }

        // Update message as having been sent.
        $this->message->set('is_sending', 0);
        $this->message->set('sent_at', time());
        $this->message->update();
    }

    /**
     * Sends an email to each of this message's additional emails (if any)
     *
     * @return void
     */
    private function send_message_additional_emails() {
        $fromuser = $this->message->get_user();

        $subject = subject_prepender::format_course_subject(
            $this->message->get_course(),
            $this->message->get('subject')
        );

        $body = $this->message->get('body'); // TODO - Find some way to clean out any custom data fields for this fake user?

        // Append a signature to the formatted body, if appropriate.
        $body = signature_appender::append_user_signature_to_body(
            $body,
            $fromuser->id,
            $this->message->get('signature_id')
        );

        // Append attachment download links to the formatted body, if any.
        $body = attachment_appender::add_download_links($this->message, $body);

        foreach ($this->message->get_additional_emails() as $additionalemail) {
            if (!$additionalemail->has_been_sent_to()) {
                // Instantiate an emailer.
                $emailer = new block_quickmail_emailer($fromuser, $subject, $body);
                $emailer->to_email($additionalemail->get('email'));

                // Determine reply to parameters based off of message settings.
                if (!(bool) $this->message->get('no_reply')) {
                    // If the message has an alternate email, reply to that.
                    if ($alternateemail = alternate_email::find_or_null($this->message->get('alternate_email_id'))) {
                        $replytoemail = $alternateemail->get('email');
                        $replytoname = $alternateemail->get_fullname();

                        // Otherwise, reply to sending user.
                    } else {
                        $replytoemail = $fromuser->email;
                        $replytoname = fullname($fromuser);
                    }

                    $emailer->reply_to($replytoemail, $replytoname);
                }

                // Attempt to send the email.
                if ($emailer->send()) {
                    $additionalemail->mark_as_sent();
                }
            }
        }
    }

    /**
     * Sends an email receipt to the sending user, if necessary
     *
     * @return void
     */
    private function send_message_receipt() {
        $fromuser = $this->message->get_user();

        $subject = subject_prepender::format_for_receipt_subject(
            $this->message->get('subject')
        );

        $body = $this->get_receipt_message_body();

        // Instantiate an emailer.
        $emailer = new block_quickmail_emailer($fromuser, $subject, $body);
        $emailer->to_email($fromuser->email);

        // Determine reply to parameters based off of message settings.
        if (!(bool) $this->message->get('no_reply')) {
            $emailer->reply_to($fromuser->email, fullname($fromuser));
        }

        // Attempt to send the email.
        $emailer->send();

        // Flag message as having sent the receipt message.
        $this->message->mark_receipt_as_sent();
    }

    /**
     * Returns a body of text content for this message's send receipt
     *
     * @return string
     */
    private function get_receipt_message_body() {
        $data = (object) [];

        // Get any additional emails as a single string.
        if ($additionalemails = $this->message->get_additional_emails(true)) {
            $additionemailsstring = implode(', ', $additionalemails);
        } else {
            $additionemailsstring = get_string('none');
        }

        // Get subject with any prepend.
        $data->subject = subject_prepender::format_for_receipt_subject(
            $this->message->get('subject')
        );

        // TODO - format this course name based off of preference?
        $data->course_name = $this->message->get_course_property('fullname', '');
        $data->message_body = $this->message->get('body');
        $data->recipient_count = $this->message->cached_recipient_count();
        $data->sent_to_mentors = $this->message->get('send_to_mentors') ? get_string('yes') : get_string('no');
        $data->addition_emails_string = $additionemailsstring;
        $data->attachment_count = $this->message->cached_attachment_count();
        $data->sent_message_link = html_writer::link(new moodle_url('/blocks/quickmail/message.php',
                                   ['id' => $this->message->get('id')]),
                                   block_quickmail_string::get('here'));

        return block_quickmail_string::get('receipt_email_body', $data);
    }

}

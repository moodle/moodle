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

namespace block_quickmail\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use block_quickmail\forms\concerns\is_quickmail_form;
use block_quickmail_string;
use block_quickmail_config;
use block_quickmail\persistents\signature;

class broadcast_message_form extends \moodleform {

    use is_quickmail_form;

    public $errors;
    public $context;
    public $user;
    public $course;
    public $usersignaturearray;
    public $userdefaultsignatureid;
    public $courseconfigarray;
    public $draftmessage;

    /**
     * Instantiates and returns a compose message form
     *
     * @param  object    $context
     * @param  object    $user               (auth user)
     * @param  object    $course             moodle (site) course
     * @param  message   $draftmessage
     * @return \block_quickmail\forms\broadcast_message_form
     */
    public static function make($context, $user, $course, $draftmessage = null) {
        $targeturl = self::generate_target_url([
            'courseid' => $course->id,
            'draftid' => ! empty($draftmessage) ? $draftmessage->get('id') : 0,
        ]);

        // Get the auth user's current signatures as array (id => title).
        $usersignaturearray = signature::get_flat_array_for_user($user->id);

        // Get the auth user's default signature id, if any, defaulting to 0.
        if ($signature = signature::get_default_signature_for_user($user->id)) {
            $userdefaultsignatureid = $signature->get('id');
        } else {
            $userdefaultsignatureid = 0;
        }

        // Get config variables for this course, defaulting to block level.
        $courseconfigarray = block_quickmail_config::get('', $course->id);

        return new self($targeturl, [
            'context' => $context,
            'user' => $user,
            'course' => $course,
            'user_signature_array' => $usersignaturearray,
            'user_default_signature_id' => $userdefaultsignatureid,
            'course_config_array' => $courseconfigarray,
            'draft_message' => $draftmessage,
        ], 'post', '', ['id' => 'mform-compose']);
    }

    /*
     * Moodle form definition
     */
    public function definition() {

        $mform =& $this->_form;

        $this->context = $this->_customdata['context'];
        $this->user = $this->_customdata['user'];
        $this->course = $this->_customdata['course'];
        $this->user_signature_array = $this->_customdata['user_signature_array'];
        $this->user_default_signature_id = $this->_customdata['user_default_signature_id'];
        $this->course_config_array = $this->_customdata['course_config_array'];
        $this->draft_message = $this->_customdata['draft_message'];

        // Select recipients.
        $options = [
            'multiple' => true,
            'showsuggestions' => true,
            'casesensitive' => false,
            'tags' => false,
            'ajax' => ''
        ];

        // Subject (text).
        $mform->addElement(
            'text',
            'subject',
            block_quickmail_string::get('subject')
        );
        $mform->setType(
            'subject',
            PARAM_TEXT
        );

        // Inject default if draft mesage.
        if ($this->is_draft_message()) {
            $mform->setDefault(
                'subject',
                $this->draft_message->get('subject')
            );
        }

        // Additional_emails (text).
        if ($this->should_show_additional_email_input()) {
            $mform->addElement(
                'text',
                'additional_emails',
                block_quickmail_string::get('additional_emails')
            );
            $mform->setType(
                'additional_emails',
                PARAM_TEXT
            );
            $mform->addHelpButton(
                'additional_emails',
                'additional_emails',
                'block_quickmail'
            );

            // Inject default if draft mesage.
            if ($this->is_draft_message()) {
                $mform->setDefault(
                    'additional_emails',
                    implode(', ', $this->draft_message->get_additional_emails(true))
                );
            }
        } else {
            $mform->addElement(
                'hidden',
                'additional_emails',
                ''
            );
            $mform->setType(
                'additional_emails',
                PARAM_TEXT
            );
        }

        // Message_editor (textarea).
        // Inject default if draft mesage.
        $defaulttext = $this->is_draft_message()
            ? $this->draft_message->get('body')
            : '';

        $mform->addElement(
            'editor',
            'message_editor',
            block_quickmail_string::get('body'),
            '',
            $this->get_editor_options()
        )->setValue([
            'text' => $defaulttext
        ]);
        $mform->setType(
            'message_editor',
            PARAM_RAW
        );

        // Attachments (filemanager).
        $mform->addElement(
            'filemanager',
            'attachments',
            get_string('attachedfiles', 'repository'),
            null,
            block_quickmail_config::get_filemanager_options()
        );

        // Signatures (select).
        if ($this->should_show_signature_selection()) {
            $mform->addElement(
                'select',
                'signature_id',
                block_quickmail_string::get('signature'),
                $this->get_user_signature_options()
            );

            // Inject default for draft mesage.
            if ($this->is_draft_message()) {
                $mform->setDefault(
                    'signature_id',
                    $this->draft_message->get('signature_id')
                );

                // Otherwise, set to user's default signature, if any.
            } else {
                $mform->setDefault(
                    'signature_id',
                    $this->user_default_signature_id
                );
            }
        } else {
            $mform->addElement(
                'static',
                'add_signature_text',
                block_quickmail_string::get('signature'),
                block_quickmail_string::get('no_signatures_create', '<a href="'
                    . $this->get_create_signature_url() . '" id="create-signature-btn">'
                    . block_quickmail_string::get('create_new') . '</a>')
            );
            $mform->addElement(
                'hidden',
                'signature_id',
                0
            );
            $mform->setType(
                'signature_id',
                PARAM_INT
            );
        }

        // Message_type (select).
        if ($this->should_show_message_type_selection()) {
            $mform->addElement(
                'select',
                'message_type',
                block_quickmail_string::get('select_message_type'),
                $this->get_message_type_options()
            );

            // Inject default if draft mesage.
            $mform->setDefault(
                'message_type',
                $this->is_draft_message()
                    ? $this->draft_message->get('message_type')
                    : $this->course_config_array['default_message_type']
            );
        } else {
            $mform->addElement(
                'hidden',
                'message_type'
            );
            $mform->setDefault(
                'message_type',
                $this->course_config_array['default_message_type']
            );
        }

        // To_send_at (date/time).
        $mform->addElement(
            'date_time_selector',
            'to_send_at',
            block_quickmail_string::get('send_at'),
            $this->get_to_send_at_options()
        );

        // Inject default if draft mesage AND time to send is in the future.
        if ($this->should_set_default_time()) {
            $mform->setDefault(
                'to_send_at',
                $this->get_draft_default_send_time()
            );
        }

        // Receipt (radio) - receive a copy or not?
        $receiptoptions = [
            $mform->createElement('radio', 'receipt', '', get_string('yes'), 1),
            $mform->createElement('radio', 'receipt', '', get_string('no'), 0)
        ];

        $mform->addGroup(
            $receiptoptions,
            'receipt_action',
            block_quickmail_string::get('receipt'),
            [' '],
            false
        );
        $mform->addHelpButton(
            'receipt_action',
            'receipt',
            'block_quickmail'
        );

        $mform->setDefault(
            'receipt',
            $this->is_draft_message()
            ? $this->draft_message->get('send_receipt') // Inject default if draft mesage.
            : ! empty($this->course_config_array['receipt']) // Otherwise, go with this course's config.
        );

        // Mentor_copy (radio) - copy mentors of recipients or not?
        if ($this->should_show_copy_mentor()) {
            $mentorcopyoptions = [
                $mform->createElement('radio', 'mentor_copy', '', get_string('yes'), 1),
                $mform->createElement('radio', 'mentor_copy', '', get_string('no'), 0)
            ];

            $mform->addGroup(
                $mentorcopyoptions,
                'mentor_copy_action',
                block_quickmail_string::get('mentor_copy'),
                [' '],
                false
            );
            $mform->addHelpButton(
                'mentor_copy_action',
                'mentor_copy',
                'block_quickmail'
            );

            $mform->setDefault(
                'mentor_copy',
                $this->is_draft_message()
                ? $this->draft_message->get('send_to_mentors') // Inject default if draft mesage.
                : 0 // Otherwise, default to no.
            );
        } else {
            $mform->addElement(
                'hidden',
                'mentor_copy',
                0
            );
            $mform->setType(
                'mentor_copy',
                PARAM_INT
            );
        }

        // Buttons!
        $buttons = [
            $mform->createElement('submit', 'send', block_quickmail_string::get('send_message')),
            $mform->createElement('submit', 'save', block_quickmail_string::get('save_draft')),
            $mform->createElement('cancel', 'cancelbutton', get_string('cancel')),
        ];

        $mform->addGroup($buttons, 'actions', '&nbsp;', [' '], false);
    }

    /*
     * Moodle form validation
     */
    public function validation($data, $files) {
        $errors = [];

        // TODO - Check that we have at least one recipient!

        // Additional_emails - make sure each is valid.
        $cleansedadditionalemails = preg_replace('/\s+/', '', $data['additional_emails']);

        if (!empty($cleansedadditionalemails) && count(array_filter(explode(',', $cleansedadditionalemails), function($email) {
            return ! filter_var($email, FILTER_VALIDATE_EMAIL);
        }))) {
            $errors['additional_emails'] = block_quickmail_string::get('invalid_additional_emails_validation');
        }

        return $errors;
    }

    /**
     * Reports whether or not this is a draft message
     *
     * @return bool
     */
    private function is_draft_message() {
        return ! empty($this->draft_message);
    }

    /**
     * Returns an array of text editor master options
     *
     * @return array
     */
    private function get_editor_options() {
        return block_quickmail_config::get_editor_options($this->context);
    }

    /**
     * Reports whether or not this form should display the "additional emails" input
     *
     * @return bool
     */
    private function should_show_additional_email_input() {
        return (bool) $this->course_config_array['additionalemail'];
    }

    /**
     * Reports whether or not this form should display the signature selection input
     *
     * @return bool
     */
    private function should_show_signature_selection() {
        return count($this->user_signature_array);
    }

    /**
     * Reports whether or not this form should display the message type selection input
     *
     * @return bool
     */
    private function should_show_message_type_selection() {
        return (bool) $this->course_config_array['message_types_available'] == 'all';
    }

    /**
     * Reports whether or not this form should display the "copy mentor" input
     *
     * @return bool
     */
    private function should_show_copy_mentor() {
        return (bool) $this->course_config_array['allow_mentor_copy'];
    }

    /**
     * Returns the current user's signatures for selection, plus a "none" option
     *
     * @return array
     */
    private function get_user_signature_options() {
        return [0 => 'None'] + $this->user_signature_array;
    }

    /**
     * Returns the options for message type selection
     *
     * @return array
     */
    private function get_message_type_options() {
        return [
            'message' => block_quickmail_string::get('message_type_message'),
            'email' => block_quickmail_string::get('message_type_email')
        ];
    }

    /**
     * Returns the options for the "send at" time selection
     *
     * @return array
     */
    private function get_to_send_at_options() {
        $currentyear = date("Y");

        if (!$this->is_draft_message()) {
            $isoptional = true;
        } else {
            $isoptional = ! $this->draft_message->get_to_send_in_future();
        }

        return [
            'startyear' => $currentyear,
            'stopyear' => $currentyear + 1,
            'timezone' => 99,
            'step' => 15,
            'optional' => $isoptional
        ];
    }

    /**
     * Returns a string URL for signature creation
     *
     * @return string
     */
    private function get_create_signature_url() {
        return new \moodle_url('/blocks/quickmail/signatures.php', [
            'courseid' => $this->course->id
        ]);
    }

    /**
     * Report whether or not a default time should be set
     *
     * @return bool
     */
    private function should_set_default_time() {
        if (!$this->is_draft_message()) {
            return false;
        }

        return $this->draft_message->get_to_send_in_future();
    }

    /**
     * Returns the default timestamp for this message
     *
     * @return int
     */
    private function get_draft_default_send_time() {
        $tosendat = $this->draft_message->get('to_send_at');

        return make_timestamp(
            date("Y", $tosendat),
            date("n", $tosendat),
            date("j", $tosendat),
            date("G", $tosendat),
            date("i", $tosendat),
            date("s", $tosendat)
        );
    }

}

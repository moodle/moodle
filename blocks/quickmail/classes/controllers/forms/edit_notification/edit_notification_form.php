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

namespace block_quickmail\controllers\forms\edit_notification;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use block_quickmail\controllers\support\controller_form;
use block_quickmail_plugin;
use block_quickmail_string;
use block_quickmail_config;
use block_quickmail\messenger\message\substitution_code;

class edit_notification_form extends controller_form {

    /*
     * Moodle form definition
     */
    public function definition() {

        $mform =& $this->_form;

        $mform->addElement('html', '<div style=""><strong>'
            . $this->get_notification_type_interface()->get_title()
            . ' (' . ucfirst($this->get_notification('type')) . ')</strong></div>');
        $mform->addElement('html', '<div style="margin-bottom: 20px;">'
            . $this->get_notification_type_interface()->get_description() . '</div>');

        // Notification_name (text).
        $mform->addElement(
            'text',
            'notification_name',
            block_quickmail_string::get('notification_name'),
            ['size' => 40, 'placeholder' => 'My Notification Title']
        );

        $mform->setType(
            'notification_name',
            PARAM_TEXT
        );

        $mform->setDefault(
            'notification_name',
            $this->get_notification('name')
        );

        $mform->addRule('notification_name', get_string('required'), 'required', '', 'server');
        $mform->addRule('notification_name', get_string('err_maxlength',
            'form', (object)['format' => 40]), 'maxlength', 40, 'server');

        $mform->addHelpButton(
            'notification_name',
            'notification_name',
            'block_quickmail'
        );

        // Notification is enabled (radio).
        $enabledoptions = [
            $mform->createElement('radio', 'notification_is_enabled', '', get_string('yes'), 1),
            $mform->createElement('radio', 'notification_is_enabled', '', get_string('no'), 0)
        ];

        $mform->addGroup(
            $enabledoptions,
            'notification_is_enabled_action',
            block_quickmail_string::get('enable_notification'),
            [' '],
            false
        );
        $mform->addHelpButton(
            'notification_is_enabled_action',
            'notification_is_enabled',
            'block_quickmail'
        );

        $mform->setDefault(
            'notification_is_enabled',
            (int) $this->get_notification('is_enabled')
        );

        // If this notification is schedulable.
        if (!empty($this->get_schedule())) {
            $mform->addElement('html', '<hr>');

            $mform->addElement('html', '<div style=""><strong>' . block_quickmail_string::get('send_schedule') . '</strong></div>');

            $mform->addElement('html', '<div style="margin-bottom: 20px;">'
                . block_quickmail_string::get('set_notification_schedule_description') . '</div>');

            /*
             *  schedule_time_unit (select)
             * validation (if necessary for this model):
             * - required
             * - value must equal: 'day', 'week' or 'month'
             */
            $scheduletimeunitoptions = $this->get_time_unit_options(['day', 'week', 'month']);
            $mform->addElement(
                'select',
                'schedule_time_unit',
                block_quickmail_string::get('time_unit'),
                $scheduletimeunitoptions
            );

            $mform->setDefault(
                'schedule_time_unit',
                $this->get_schedule('unit')
            );

            $mform->addRule('schedule_time_unit', block_quickmail_string::get('invalid_time_unit'), 'required', '', 'server');
            $mform->addRule('schedule_time_unit', block_quickmail_string::get('invalid_time_unit'), 'callback',
                function($value) use ($scheduletimeunitoptions) {
                    return in_array($value, array_keys($scheduletimeunitoptions));
                }
                , 'server');

            /*
             * schedule_time_amount (text)
             * validation (if necessary for this model):
             * - required
             * - numeric
             * - integer
             * - greater than 0
             */
            $mform->addElement(
                'text',
                'schedule_time_amount',
                block_quickmail_string::get('time_amount'),
                ['size' => 4]
            );

            $mform->setType(
                'schedule_time_amount',
                PARAM_TEXT
            );

            $mform->setDefault(
                'schedule_time_amount',
                $this->get_schedule('amount')
            );

            $mform->addRule('schedule_time_amount',
                block_quickmail_string::get('invalid_time_amount'), 'required', '', 'server');
            $mform->addRule('schedule_time_amount',
                block_quickmail_string::get('invalid_time_amount'), 'numeric', '', 'server');
            $mform->addRule('schedule_time_amount',
                block_quickmail_string::get('invalid_time_amount'), 'nopunctuation', '', 'server');
            $mform->addRule('schedule_time_amount',
                block_quickmail_string::get('invalid_time_amount'), 'callback', function($value) {
                    return $value >= 1;
                }, 'server');

            // Schedule_begin_at (date/time).
            if (empty($this->get_notification_type_interface('last_run_at'))) {
                $mform->addElement(
                    'date_time_selector',
                    'schedule_begin_at',
                    block_quickmail_string::get('schedule_begin_at'),
                    $this->get_schedule_time_options(false)
                );

                if (!empty($this->get_schedule('begin_at'))) {
                    $mform->setDefault(
                        'schedule_begin_at',
                        $this->get_schedule('begin_at')
                    );
                }
            } else {
                $mform->addElement('static', 'notification_already_sent',
                    block_quickmail_string::get('schedule_begin_at'),
                    block_quickmail_string::get('notification_already_sent'));
            }

            // Schedule_end_at (date/time).
            $mform->addElement(
                'date_time_selector',
                'schedule_end_at',
                block_quickmail_string::get('schedule_end_at'),
                $this->get_schedule_time_options(false)
            );

            if (!empty($this->get_schedule('end_at'))) {
                $mform->setDefault(
                    'schedule_end_at',
                    $this->get_schedule('end_at')
                );
            }
        }

        // If this notification requires any conditions.
        if ($this->get_custom_data('notification_type') == 'event') {
            $mform->addElement('html', '<hr>');

            $mform->addElement('html', '<div style=""><strong>'
                . block_quickmail_string::get('edit_event_details') . '</strong></div>');

            $eventtimeunitoptions = $this->get_time_unit_options(['minute', 'hour', 'day']);

            /*
             *
             * time_delay_unit (select)
             * validation (if given):
             * - value must equal: 'minute', 'hour', or 'day'
             *
             */
            $mform->addElement(
                'select',
                'time_delay_unit',
                block_quickmail_string::get('time_delay_unit'),
                $eventtimeunitoptions
            );

            $mform->setDefault(
                'time_delay_unit',
                $this->get_notification_type_interface('time_delay_unit')
            );

            $mform->addRule('time_delay_unit', block_quickmail_string::get('invalid_time_unit'), 'callback',
                function($value) use ($eventtimeunitoptions) {
                    return in_array($value, array_keys($eventtimeunitoptions));
                }, 'server');

            /*
             *
             * time_delay_amount (text)
             * validation (if given):
             * - numeric
             * - greater than 0
             *
             */
            $mform->addElement(
                'text',
                'time_delay_amount',
                block_quickmail_string::get('time_amount'),
                ['size' => 4]
            );

            $mform->setType(
                'time_delay_amount',
                PARAM_TEXT
            );

            $mform->setDefault(
                'time_delay_amount',
                $this->get_notification_type_interface('time_delay_amount')
            );

            $mform->addRule('time_delay_amount', block_quickmail_string::get('invalid_time_amount'), 'callback', function($value) {
                if (empty($value)) {
                    return true;
                }

                if (!is_numeric($value)) {
                    return false;
                }

                return $value >= 1;
            }, 'server');

            if (!$this->get_custom_data('is_one_time_event')) {
                /*
                 *  mute_time_unit (select)
                 * validation (if given):
                 * - value must equal: 'minute', 'hour', or 'day'
                 */
                $mform->addElement(
                    'select',
                    'mute_time_unit',
                    block_quickmail_string::get('mute_time_unit'),
                    $eventtimeunitoptions
                );

                $mform->setDefault(
                    'mute_time_unit',
                    $this->get_notification_type_interface('mute_time_unit')
                );

                $mform->addRule('mute_time_unit', block_quickmail_string::get('invalid_time_unit'), 'callback',
                    function($value) use ($eventtimeunitoptions) {
                        return in_array($value, array_keys($eventtimeunitoptions));
                    }, 'server');

                /*
                 *
                 * mute_time_amount (text)
                 *
                 * validation (if necessary for this model):
                 * - required
                 * - numeric
                 * - integer
                 * - greater than 0
                 *
                 */
                $mform->addElement(
                    'text',
                    'mute_time_amount',
                    block_quickmail_string::get('time_amount'),
                    ['size' => 4]
                );

                $mform->setType(
                    'mute_time_amount',
                    PARAM_TEXT
                );

                $mform->setDefault(
                    'mute_time_amount',
                    $this->get_notification_type_interface('mute_time_amount')
                );

                $mform->addRule('mute_time_amount', block_quickmail_string::get('invalid_time_amount'), 'callback',
                    function($value) {
                        if (empty($value)) {
                            return true;
                        }

                        if (!is_numeric($value)) {
                            return false;
                        }

                        return $value >= 1;
                    }, 'server');
            }
        }

        // If this notification requires any conditions.
        if (!empty($this->get_custom_data('required_condition_keys'))) {
            $mform->addElement('html', '<hr>');

            $mform->addElement('html', '<div style=""><strong>'
            . block_quickmail_string::get('notification_conditions') . '</strong></div>');

            $mform->addElement('html', '<div style="margin-bottom: 20px;">'
            . $this->get_notification_type_interface()->get_condition_description() . '</div>');

            /*
             *
             * condition_time_unit (select)
             *
             * validation (if necessary for this model):
             * - required
             * - value must equal: 'day', 'week' or 'month'
             *
             */
            if ($this->requires_condition('time_unit')) {
                $conditiontimeunitoptions = $this->get_time_unit_options(['day', 'week', 'month']);

                $mform->addElement(
                    'select',
                    'condition_time_unit',
                    block_quickmail_string::get('time_unit'),
                    $conditiontimeunitoptions
                );

                $mform->addRule('condition_time_unit', block_quickmail_string::get('invalid_time_unit'), 'required', '', 'server');
                $mform->addRule('condition_time_unit', block_quickmail_string::get('invalid_time_unit'), 'callback',
                    function($value) use ($conditiontimeunitoptions) {
                        return in_array($value, array_keys($conditiontimeunitoptions));
                    }, 'server');

                $mform->setDefault(
                    'condition_time_unit',
                    $this->get_assigned_condition('time_unit') ?: ''
                );
            }

            /*
             *
             * condition_time_amount (text)
             *
             * validation (if necessary for this model):
             * - required
             * - numeric
             * - integer
             * - greater than 0
             *
             */
            if ($this->requires_condition('time_amount')) {
                $mform->addElement(
                    'text',
                    'condition_time_amount',
                    block_quickmail_string::get('time_amount'),
                    ['size' => 4]
                );

                $mform->setType(
                    'condition_time_amount',
                    PARAM_TEXT
                );

                $mform->setDefault(
                    'condition_time_amount',
                    $this->get_assigned_condition('time_amount') ?: ''
                );

                $mform->addRule('condition_time_amount',
                    block_quickmail_string::get('invalid_time_amount'), 'required', '', 'server');
                $mform->addRule('condition_time_amount',
                    block_quickmail_string::get('invalid_time_amount'), 'numeric', '', 'server');
                $mform->addRule('condition_time_amount',
                    block_quickmail_string::get('invalid_time_amount'), 'nopunctuation', '', 'server');
                $mform->addRule('condition_time_amount',
                    block_quickmail_string::get('invalid_time_amount'), 'callback',
                        function($value) {
                            return $value >= 1;
                        }, 'server');
            }

            /*
             * condition_time_relation (select)
             *
             * validation (if necessary for this model):
             * - required
             * - value must equal: 'before' or 'after'
             */
            if ($this->requires_condition('time_relation')) {
                $mform->addElement(
                    'select',
                    'condition_time_relation',
                    block_quickmail_string::get('time_relation'),
                    $this->get_time_relation_options()
                );

                $mform->setDefault(
                    'condition_time_relation',
                    $this->get_assigned_condition('time_relation') ?: ''
                );

                $mform->addRule('condition_time_relation',
                    block_quickmail_string::get('invalid_time_relation'), 'required', '', 'server');
                $mform->addRule('condition_time_relation',
                    block_quickmail_string::get('invalid_time_relation'), 'callback',
                        function($value) {
                            return in_array($value, ['before', 'after']);
                        }, 'server');
            }

            /*
             * condition_grade_greater_than (text)
             *
             * validation (if necessary for this model):
             * - required
             * - numeric
             * - integer
             * - greater than or equal to 0
             * - less than 100
             */
            if ($this->requires_condition('grade_greater_than')) {
                $mform->addElement(
                    'text',
                    'condition_grade_greater_than',
                    block_quickmail_string::get('condition_grade_greater_than'),
                    ['size' => 4]
                );

                $mform->setType(
                    'condition_grade_greater_than',
                    PARAM_TEXT
                );

                $mform->setDefault(
                    'condition_grade_greater_than',
                    $this->get_assigned_condition('grade_greater_than') ?: ''
                );

                $mform->addRule('condition_grade_greater_than',
                    block_quickmail_string::get('invalid_condition_grade_greater_than'), 'required', '', 'server');
                $mform->addRule('condition_grade_greater_than',
                    block_quickmail_string::get('invalid_condition_grade_greater_than'), 'numeric', '', 'server');
                $mform->addRule('condition_grade_greater_than',
                    block_quickmail_string::get('invalid_condition_grade_greater_than'), 'nopunctuation', '', 'server');
                $mform->addRule('condition_grade_greater_than',
                    block_quickmail_string::get('invalid_condition_grade_greater_than'), 'callback',
                        function($value) {
                            return $value >= 0 && $value < 100;
                        }, 'server');
            }

            /*
             * condition_grade_less_than (text)
             *
             * validation (if necessary for this model):
             * - required
             * - numeric
             * - integer
             * - greater than 0
             * - less than or equal to 100
             */
            if ($this->requires_condition('grade_less_than')) {
                $mform->addElement(
                    'text',
                    'condition_grade_less_than',
                    block_quickmail_string::get('condition_grade_less_than'),
                    ['size' => 4]
                );

                $mform->setType(
                    'condition_grade_less_than',
                    PARAM_TEXT
                );

                $mform->setDefault(
                    'condition_grade_less_than',
                    $this->get_assigned_condition('grade_less_than') ?: ''
                );

                $mform->addRule('condition_grade_less_than',
                    block_quickmail_string::get('invalid_condition_grade_less_than'), 'required', '', 'server');
                $mform->addRule('condition_grade_less_than',
                    block_quickmail_string::get('invalid_condition_grade_less_than'), 'numeric', '', 'server');
                $mform->addRule('condition_grade_less_than',
                    block_quickmail_string::get('invalid_condition_grade_less_than'), 'nopunctuation', '', 'server');
                $mform->addRule('condition_grade_less_than',
                    block_quickmail_string::get('invalid_condition_grade_less_than'), 'callback',
                        function($value) {
                            return $value > 0 && $value <= 100;
                        }, 'server');
            }
        }

        $mform->addElement('html', '<hr>');

        $mform->addElement('html', '<div style="margin-bottom: 20px;"><strong>'
            . block_quickmail_string::get('message_details') . '</strong></div>');

        // Message_subject (text).
        $mform->addElement(
            'text',
            'message_subject',
            block_quickmail_string::get('subject'),
            ['size' => 50]
        );
        $mform->setType(
            'message_subject',
            PARAM_TEXT
        );

        $mform->setDefault(
            'message_subject',
            $this->get_notification('subject')
        );

        $mform->addRule('message_subject', block_quickmail_string::get('missing_subject'), 'required', '', 'server');

        // Message_body (textarea).
        $mform->addElement(
            'editor',
            'message_body',
            block_quickmail_string::get('body'),
            '',
            $this->get_custom_data('editor_options')
        )->setValue([
            'text' => $this->get_notification('body')
        ]);
        $mform->setType(
            'message_body',
            PARAM_RAW
        );

        $mform->addRule('message_body', block_quickmail_string::get('missing_body'), 'required', '', 'server');

        $mform->addElement('static', 'reminder_description', '', $this->get_user_fields_html());

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
                $this->get_notification('message_type')
            );
        } else {
            $mform->addElement(
                'hidden',
                'message_type'
            );
            $mform->setDefault(
                'message_type',
                $this->get_custom_data('course_config_array')['default_message_type']
            );
            $mform->setType(
                'message_type',
                PARAM_TEXT
            );
        }

        // Message_send_to_mentors (radio) - copy mentors of recipients or not?
        if ($this->should_show_copy_mentor()) {
            $mentorcopyoptions = [
                $mform->createElement('radio', 'message_send_to_mentors', '', get_string('yes'), 1),
                $mform->createElement('radio', 'message_send_to_mentors', '', get_string('no'), 0)
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
                'message_send_to_mentors',
                (int) $this->get_notification('send_to_mentors')
            );
        } else {
            $mform->addElement(
                'hidden',
                'message_send_to_mentors',
                0
            );
            $mform->setType(
                'message_send_to_mentors',
                PARAM_INT
            );
        }

        // Buttons!
        $buttons = [
            $mform->createElement('cancel', 'cancelbutton', get_string('back')),
            $mform->createElement('submit', 'next', get_string('save', 'block_quickmail')),
        ];

        $mform->addGroup($buttons, 'actions', '&nbsp;', array(' '), false);
    }

    /**
     * Returns this selected notification, or if given, an attribute of this notification
     *
     * @param  string  $attr    optional
     * @return mixed
     */
    private function get_notification($attr = null) {
        $notification = $this->get_custom_data('notification');

        return ! empty($attr)
            ? $notification->get($attr)
            : $notification;
    }

    /**
     * Returns this selected notification type interface, or if given, an attribute of this notification type interface
     *
     * @param  string  $attr    optional
     * @return mixed
     */
    private function get_notification_type_interface($attr = null) {
        $notificationtypeinterface = $this->get_custom_data('notification_type_interface');

        return ! empty($attr)
            ? $notificationtypeinterface->get($attr)
            : $notificationtypeinterface;
    }

    /**
     * Returns this selected notification's schedule, or if given, an attribute of this notification
     *
     * @param  string  $attr    optional
     * @return mixed
     */
    private function get_schedule($attr = null) {
        if ( ! $schedule = $this->get_custom_data('schedule')) {
            return null;
        }

        return ! empty($attr)
            ? $schedule->get($attr)
            : $schedule;
    }

    /**
     * Returns an array of user-relative data fields that may be injected into the message body
     *
     * @return array
     */
    private function get_allowed_user_fields() {
        return substitution_code::get(['user', $this->get_custom_data('notification_object_type')]);
    }

    /**
     * Returns the HTML that should be displayed as the content of the "user substitution codes" helper display
     *
     * @return string
     */
    private function get_user_fields_html() {
        $html = '<p style="margin-bottom: 4px;"><i>' . block_quickmail_string::get('select_allowed_user_fields') . ':</i></p>';

        foreach ($this->get_allowed_user_fields() as $field) {
            $html .= '<div class="field-label user-field-label">[:' . $field . ':]</div>';
        }

        return $html;
    }

    /**
     * Reports whether or not this form should display the "copy mentor" input
     *
     * @return bool
     */
    private function should_show_copy_mentor() {
        return (bool) (
            $this->get_custom_data('allow_mentor_copy') && $this->get_custom_data('course_config_array')['allow_mentor_copy']);
    }

    /**
     * Reports whether or not this form should display the message type selection input
     *
     * @return bool
     */
    private function should_show_message_type_selection() {
        return $this->get_custom_data('course_config_array')['message_types_available'] == 'all';
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
     * Returns an array of time unit options for selection
     *
     * @return array
     */
    private function get_time_unit_options($units) {
        return block_quickmail_plugin::get_time_unit_selection_array($units);
    }

    /**
     * Returns the options for schedule time selection
     *
     * @param  bool  $optional  whether or not this field is optional
     * @return array
     */
    private function get_schedule_time_options($optional = true) {
        $currentyear = date("Y");

        return [
            'startyear' => $currentyear,
            'stopyear' => $currentyear + 1,
            'timezone' => 99,
            'step' => 15,
            'optional' => $optional
        ];
    }

    /**
     * Reports whether or not the given condition key is required
     *
     * @param  string  $key
     * @return bool
     */
    private function requires_condition($key) {
        return in_array($key, $this->get_custom_data('required_condition_keys'));
    }

    /**
     * Returns this notification's value for the given condition key
     *
     * @param  string  $key
     * @return mixed
     */
    private function get_assigned_condition($key) {
        if (!in_array($key, array_keys($this->get_custom_data('assigned_conditions')))) {
            return null;
        }

        return $this->get_custom_data('assigned_conditions')[$key];
    }

    /**
     * Returns the options condition_time_relation selection
     *
     * @return array
     */
    private function get_time_relation_options() {
        return [
            '' => get_string('select'),
            'before' => ucfirst(get_string('before')),
            'after' => ucfirst(get_string('after')),
        ];
    }

}

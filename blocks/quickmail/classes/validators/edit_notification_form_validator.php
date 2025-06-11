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

namespace block_quickmail\validators;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\validators\validator;
use block_quickmail_string;
use block_quickmail_config;
use block_quickmail\messenger\message\body_substitution_code_parser;
use block_quickmail\exceptions\body_parser_exception;

class edit_notification_form_validator extends validator {

    /**
     * Extra_params => notification_type          required
     * Extra_params => substitution_code_classes  default: (['user'])
     * Extra_params => required_condition_keys    default: ([])
     */

    /**
     * Defines this specific validator's validation rules
     *
     * @return void
     */
    public function validator_rules() {
        $this->validate_notification_name();
        $this->validate_message_subject();
        $this->validate_message_body();
        $this->validate_message_body_codes();
        $this->validate_message_type();
        $this->validate_schedule_params();
    }

    /**
     * Checks that the notification has a valid name, adding any errors to the stack
     *
     * @return void
     */
    private function validate_notification_name() {
        if ($this->is_missing('notification_name')) {
            $this->add_error(block_quickmail_string::get('missing_notification_name'));
        }

        if (strlen($this->form_data->notification_name) > 40) {
            $this->add_error(block_quickmail_string::get('notification_name_too_long'));
        }
    }

    /**
     * Checks that the notification has a valid subject line, adding any errors to the stack
     *
     * @return void
     */
    private function validate_message_subject() {
        if ($this->is_missing('message_subject')) {
            $this->add_error(block_quickmail_string::get('missing_subject'));
        }
    }

    /**
     * Checks that the message body exists, adding any errors to the stack
     *
     * @return void
     */
    private function validate_message_body() {
        if ($this->is_missing('message_body')) {
            $this->add_error(block_quickmail_string::get('missing_body'));
        }

        if (!array_key_exists('text', $this->form_data->message_body)) {
            $this->add_error(block_quickmail_string::get('missing_body'));
        }

        if (empty($this->form_data->message_body['text'])) {
            $this->add_error(block_quickmail_string::get('missing_body'));
        }
    }

    /**
     * Checks that the selected "message type" is allowed per site config, adding any errors to the stack
     *
     * @return void
     */
    private function validate_message_type() {
        if (!in_array($this->form_data->message_type, block_quickmail_config::get_supported_message_types())) {
            $this->add_error(block_quickmail_string::get('invalid_send_method'));
        }

        $supportedoption = $this->get_config('message_types_available');

        if ($supportedoption == 'all') {
            return;
        }

        if ($supportedoption !== $this->form_data->message_type) {
            $this->add_error(block_quickmail_string::get('invalid_send_method'));
        }
    }

    /**
     * Checks that the message body does not contain any unsupported custom user data keys, adding any errors to the stack
     *
     * @return void
     */
    private function validate_message_body_codes() {
        // Attempt to validate the message body to make sure any substitution codes are
        // formatted properly and are all allowed.
        try {
            $errors = body_substitution_code_parser::validate_body(
                          $this->form_data->message_body['text'],
                          $this->get_allowed_substitution_codes());

            foreach ($errors as $error) {
                $this->add_error($error);
            }
        } catch (body_parser_exception $e) {
            $this->add_error($e->getMessage());
        }
    }

    /**
     * Checks that the schedule time unit is given and valid for schedulable notifications
     *
     * @return void
     */
    public function validate_schedule_params() {
        if ($this->is_schedulable_notification()) {
            // If the submitted time unit is not supported.
            if (!in_array($this->form_data->schedule_time_unit, ['day', 'week', 'month'])) {
                $this->add_error(block_quickmail_string::get('invalid_schedule_time_unit'));
            }

            // If the submitted time amount is not supported.
            if (!is_numeric($this->form_data->schedule_time_amount)) {
                $this->add_error(block_quickmail_string::get('invalid_schedule_time_amount'));
            }
        }
    }

    /**
     * Checks that the given condition params are valid for notification
     *
     * @return void
     */
    public function validate_condition_params() {
        if (!empty($this->get_required_condition_keys())) {
            // If the submitted time unit is not supported.
            if (!in_array($this->form_data->condition_time_unit, ['day', 'week', 'month'])) {
                $this->add_error(block_quickmail_string::get('invalid_condition_time_unit'));
            }

            // If the submitted time amount is not supported.
            if (!is_numeric($this->form_data->condition_time_amount)) {
                $this->add_error(block_quickmail_string::get('invalid_condition_time_amount'));
            }
        }
    }

    /**
     * Returns an array of the substitution codes that are allowed for this notification
     *
     * @return array
     */
    private function get_allowed_substitution_codes() {
        return $this->get_extra_param_value('substitution_code_classes', ['user']);
    }

    /**
     * Returns an array of the condition keys that are required for this notification
     *
     * @return array
     */
    private function get_required_condition_keys() {
        return $this->get_extra_param_value('required_condition_keys', []);
    }

    /**
     * Reports whether or not this is a schedulable notification
     *
     * @return bool
     */
    private function is_schedulable_notification() {
        return $this->check_extra_params_value('notification_type', 'reminder');
    }

}

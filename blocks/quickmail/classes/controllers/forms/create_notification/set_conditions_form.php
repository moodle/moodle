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

namespace block_quickmail\controllers\forms\create_notification;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use block_quickmail\controllers\support\controller_form;
use block_quickmail_string;

class set_conditions_form extends controller_form {

    /*
     * Moodle form definition
     */
    public function definition() {

        $mform =& $this->_form;

        // View_form_name directive: TO BE INCLUDED ON ALL FORMS.
        $mform->addElement('hidden', 'view_form_name');
        $mform->setType('view_form_name', PARAM_TEXT);
        $mform->setDefault('view_form_name', $this->get_view_form_name());

        // Descriptive text.
        // Condition_description.
        $mform->addElement('html', '<div style="margin-bottom: 20px;">' . block_quickmail_string::get('notification_model_'
            . $this->get_session_stored('notification_type') . '_'
            . $this->get_session_stored('notification_model') . '_condition_description') . '</div>');

        /*
         * Condition_time_unit (select)
         *
         * Validation (if necessary for this model):
         * - required
         * - value must equal: 'day', 'week' or 'month'
         */
        if ($this->requires_condition('time_unit')) {
            $timeunitoptions = $this->get_time_unit_options();

            $mform->addElement(
                'select',
                'condition_time_unit',
                block_quickmail_string::get('time_unit'),
                $timeunitoptions
            );

            $mform->addRule('condition_time_unit', block_quickmail_string::get('invalid_time_unit'), 'required', '', 'server');
            $mform->addRule('condition_time_unit', block_quickmail_string::get('invalid_time_unit'), 'callback',
                function($value) use ($timeunitoptions) {
                    return in_array($value, array_keys($timeunitoptions));
                }, 'server');

            $mform->setDefault(
                'condition_time_unit',
                $this->has_session_stored('condition_time_unit') ? $this->get_session_stored('condition_time_unit') : ''
            );
        }

        /*
         * Condition_time_amount (text)
         *
         * Validation (if necessary for this model):
         * - required
         * - numeric
         * - integer
         * - greater than 0
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
                $this->has_session_stored('condition_time_amount') ? $this->get_session_stored('condition_time_amount') : ''
            );

            $mform->addRule('condition_time_amount', block_quickmail_string::get('invalid_time_amount'),
                'required', '', 'server');
            $mform->addRule('condition_time_amount', block_quickmail_string::get('invalid_time_amount'),
                'numeric', '', 'server');
            $mform->addRule('condition_time_amount', block_quickmail_string::get('invalid_time_amount'),
                'nopunctuation', '', 'server');
            $mform->addRule('condition_time_amount', block_quickmail_string::get('invalid_time_amount'), 'callback',
                function($value) {
                    return $value >= 1;
                }, 'server');
        }

        /* Condition_time_relation (select)
         *
         * Validation (if necessary for this model):
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
                $this->has_session_stored('condition_time_relation') ? $this->get_session_stored('condition_time_relation') : ''
            );

            $mform->addRule('condition_time_relation', block_quickmail_string::get('invalid_time_relation'), 'required',
                '', 'server');
            $mform->addRule('condition_time_relation', block_quickmail_string::get('invalid_time_relation'), 'callback',
                function($value) {
                    return in_array($value, ['before', 'after']);
                }, 'server');
        }

        /*
         * Condition_grade_greater_than (text)
         *
         * Validation (if necessary for this model):
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
                $this->has_session_stored('condition_grade_greater_than')
                    ? $this->get_session_stored('condition_grade_greater_than')
                    : ''
            );

            $mform->addRule('condition_grade_greater_than', block_quickmail_string::get('invalid_condition_grade_greater_than'),
                'required', '', 'server');
            $mform->addRule('condition_grade_greater_than', block_quickmail_string::get('invalid_condition_grade_greater_than'),
                'numeric', '', 'server');
            $mform->addRule('condition_grade_greater_than', block_quickmail_string::get('invalid_condition_grade_greater_than'),
                'nopunctuation', '', 'server');
            $mform->addRule('condition_grade_greater_than', block_quickmail_string::get('invalid_condition_grade_greater_than'),
                'callback',
                function($value) {
                    return $value >= 0 && $value < 100;
                }, 'server');
        }

        /* Condition_grade_less_than (text)
         *
         * Validation (if necessary for this model):
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
                $this->has_session_stored('condition_grade_less_than') ? $this->get_session_stored('condition_grade_less_than') : ''
            );

            $mform->addRule('condition_grade_less_than', block_quickmail_string::get('invalid_condition_grade_less_than'),
                'required', '', 'server');
            $mform->addRule('condition_grade_less_than', block_quickmail_string::get('invalid_condition_grade_less_than'),
                'numeric', '', 'server');
            $mform->addRule('condition_grade_less_than', block_quickmail_string::get('invalid_condition_grade_less_than'),
                'nopunctuation', '', 'server');
            $mform->addRule('condition_grade_less_than', block_quickmail_string::get('invalid_condition_grade_less_than'),
                'callback',
                function($value) {
                    return $value > 0 && $value <= 100;
                }, 'server');
        }

        // Buttons!
        $buttons = [
            $mform->createElement('submit', 'back', get_string('back')),
            $mform->createElement('submit', 'next', get_string('next')),
        ];

        $mform->addGroup($buttons, 'actions', '&nbsp;', array(' '), false);
    }

    /**
     * Reports whether or not the given condition key is required
     *
     * @param  string  $key
     * @return bool
     */
    private function requires_condition($key) {
        return in_array($key, $this->get_custom_data('condition_keys'));
    }

    /**
     * Returns the options condition_time_unit selection
     *
     * @return array
     */
    private function get_time_unit_options() {
        return [
            '' => get_string('select'),
            'day' => ucfirst(get_string('days')),
            'week' => ucfirst(get_string('weeks')),
            'month' => ucfirst(get_string('months')),
        ];
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

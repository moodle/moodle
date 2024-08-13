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

namespace tool_usertours\local\target;

/**
 * Selector target.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class selector extends base {
    /**
     * Convert the target value to a valid CSS selector for use in the
     * output configuration.
     *
     * @return string
     */
    public function convert_to_css() {
        return $this->step->get_targetvalue();
    }

    /**
     * Convert the step target to a friendly name for use in the UI.
     *
     * @return string
     */
    public function get_displayname() {
        return get_string('selectordisplayname', 'tool_usertours', $this->step->get_targetvalue());
    }

    /**
     * Get the default title.
     *
     * @return string
     */
    public function get_default_title() {
        return get_string('selector_defaulttitle', 'tool_usertours');
    }

    /**
     * Get the default content.
     *
     * @return string
     */
    public function get_default_content() {
        return get_string('selector_defaultcontent', 'tool_usertours');
    }

    /**
     * Add the target type configuration to the form.
     *
     * @param   MoodleQuickForm $mform      The form to add configuration to.
     * @return  $this
     */
    public static function add_config_to_form(\MoodleQuickForm $mform) {
        $mform->addElement('text', 'targetvalue_selector', get_string('cssselector', 'tool_usertours'), ['size' => '80']);
        $mform->setType('targetvalue_selector', PARAM_RAW);
        $mform->addHelpButton('targetvalue_selector', 'target_selector_targetvalue', 'tool_usertours');
    }

    /**
     * Add the disabledIf values.
     *
     * @param   MoodleQuickForm $mform      The form to add configuration to.
     */
    public static function add_disabled_constraints_to_form(\MoodleQuickForm $mform) {
        $mform->hideIf(
            'targetvalue_selector',
            'targettype',
            'noteq',
            \tool_usertours\target::get_target_constant_for_class(self::class)
        );
    }

    /**
     * Prepare data to submit to the form.
     *
     * @param   object          $data       The data being passed to the form
     */
    public function prepare_data_for_form($data) {
        $data->targetvalue_selector = $this->step->get_targetvalue();
    }

    /**
     * Fetch the targetvalue from the form for this target type.
     *
     * @param   stdClass        $data       The data submitted in the form
     * @return  string
     */
    public function get_value_from_form($data) {
        return $data->targetvalue_selector;
    }
}

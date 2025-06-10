<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * This file contains the customcert element coursefield's core interaction API.
 *
 * @package    customcertelement_coursefield
 * @copyright  2019 Catalyst IT
 * @author     Dan Marsden
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customcertelement_coursefield;

/**
 * The customcert element coursefield's core interaction API.
 *
 * @package    customcertelement_coursefield
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element extends \mod_customcert\element {

    /**
     * This function renders the form elements when adding a customcert element.
     *
     * @param \MoodleQuickForm $mform the edit form instance
     */
    public function render_form_elements($mform) {
        // Get the user profile fields.
        $coursefields = [
            'fullname' => get_string('fullnamecourse'),
            'shortname' => get_string('shortnamecourse'),
            'idnumber' => get_string('idnumbercourse'),
        ];
        // Get the course custom fields.
        $arrcustomfields = [];
        $handler = \core_course\customfield\course_handler::create();
        $customfields = $handler->get_fields();

        foreach ($customfields as $field) {
            $arrcustomfields[$field->get('id')] = $field->get_formatted_name();
        }

        // Combine the two.
        $fields = $coursefields + $arrcustomfields;
        \core_collator::asort($fields);

        // Create the select box where the user field is selected.
        $mform->addElement('select', 'coursefield', get_string('coursefield', 'customcertelement_coursefield'), $fields);
        $mform->setType('coursefield', PARAM_ALPHANUM);
        $mform->addHelpButton('coursefield', 'coursefield', 'customcertelement_coursefield');

        parent::render_form_elements($mform);
    }

    /**
     * This will handle how form data will be saved into the data column in the
     * customcert_elements table.
     *
     * @param \stdClass $data the form data
     * @return string the text
     */
    public function save_unique_data($data) {
        return $data->coursefield;
    }

    /**
     * Handles rendering the element on the pdf.
     *
     * @param \pdf $pdf the pdf object
     * @param bool $preview true if it is a preview, false otherwise
     * @param \stdClass $user the user we are rendering this for
     */
    public function render($pdf, $preview, $user) {

        $courseid = \mod_customcert\element_helper::get_courseid($this->id);
        $course = get_course($courseid);

        \mod_customcert\element_helper::render_content($pdf, $this, $this->get_course_field_value($course, $preview));
    }

    /**
     * Render the element in html.
     *
     * This function is used to render the element when we are using the
     * drag and drop interface to position it.
     */
    public function render_html() {
        global $COURSE;

        return \mod_customcert\element_helper::render_html_content($this, $this->get_course_field_value($COURSE, true));
    }

    /**
     * Sets the data on the form when editing an element.
     *
     * @param \MoodleQuickForm $mform the edit form instance
     */
    public function definition_after_data($mform) {
        if (!empty($this->get_data())) {
            $element = $mform->getElement('coursefield');
            $element->setValue($this->get_data());
        }
        parent::definition_after_data($mform);
    }

    /**
     * Helper function that returns the field value in a human-readable format.
     *
     * @param \stdClass $course the course we are rendering this for
     * @param bool $preview Is this a preview?
     * @return string
     */
    protected function get_course_field_value(\stdClass $course, bool $preview): string {

        // The user field to display.
        $field = $this->get_data();
        // The value to display - we always want to show a value here so it can be repositioned.
        if ($preview) {
            $value = $field;
        } else {
            $value = '';
        }
        if (is_number($field)) { // Must be a custom course profile field.
            $handler = \core_course\customfield\course_handler::create();
            $data = $handler->get_instance_data($course->id, true);
            if ($preview && empty($data[$field]->export_value())) {
                $fields = $handler->get_fields();
                $value = $fields[$field]->get('shortname');
            } else if (!empty($data[$field])) {
                $value = $data[$field]->export_value();
            }

        } else if (!empty($course->$field)) { // Field in the course table.
            $value = $course->$field;
        }

        $context = \mod_customcert\element_helper::get_context($this->get_id());
        return format_string($value, true, ['context' => $context]);
    }
}

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
 * This file contains the customcert element coursename's core interaction API.
 *
 * @package    customcertelement_coursename
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customcertelement_coursename;

/**
 * The customcert element coursename's core interaction API.
 *
 * @package    customcertelement_coursename
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element extends \mod_customcert\element {

    /**
     * The course short name.
     */
    const COURSE_SHORT_NAME = 1;

    /**
     * The course fullname.
     */
    const COURSE_FULL_NAME = 2;

    /**
     * This function renders the form elements when adding a customcert element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function render_form_elements($mform) {
        // The course name display options.
        $mform->addElement('select', 'coursenamedisplay', get_string('coursenamedisplay', 'customcertelement_coursename'),
            self::get_course_name_display_options());
        $mform->setType('coursenamedisplay', PARAM_INT);
        $mform->addHelpButton('coursenamedisplay', 'coursenamedisplay', 'customcertelement_coursename');

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
        return $data->coursenamedisplay;
    }

    /**
     * Handles rendering the element on the pdf.
     *
     * @param \pdf $pdf the pdf object
     * @param bool $preview true if it is a preview, false otherwise
     * @param \stdClass $user the user we are rendering this for
     */
    public function render($pdf, $preview, $user) {
        \mod_customcert\element_helper::render_content($pdf, $this, $this->get_course_name_detail());
    }

    /**
     * Render the element in html.
     *
     * This function is used to render the element when we are using the
     * drag and drop interface to position it.
     *
     * @return string the html
     */
    public function render_html() {
        return \mod_customcert\element_helper::render_html_content($this, $this->get_course_name_detail());
    }

    /**
     * Sets the data on the form when editing an element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function definition_after_data($mform) {
        if (!empty($this->get_data())) {
            $element = $mform->getElement('coursenamedisplay');
            $element->setValue($this->get_data());
        }
        parent::definition_after_data($mform);
    }

    /**
     * Helper function that returns the selected course name detail (i.e. name or short description) for display.
     *
     * @return string
     */
    protected function get_course_name_detail(): string {
        $courseid = \mod_customcert\element_helper::get_courseid($this->get_id());
        $course = get_course($courseid);
        $context = \mod_customcert\element_helper::get_context($this->get_id());

        // The name field to display.
        $field = $this->get_data();
        // The name value to display.
        $value = $course->fullname;
        if ($field == self::COURSE_SHORT_NAME) {
            $value = $course->shortname;
        }

        return format_string($value, true, ['context' => $context]);
    }

    /**
     * Helper function to return all the possible name display options.
     *
     * @return array returns an array of name options
     */
    public static function get_course_name_display_options(): array {
        return [
            self::COURSE_FULL_NAME => get_string('coursefullname', 'customcertelement_coursename'),
            self::COURSE_SHORT_NAME => get_string('courseshortname', 'customcertelement_coursename'),
        ];
    }
}

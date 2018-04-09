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
 * Course form.
 *
 * @package tool_generator
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form with options for creating large course.
 *
 * @package tool_generator
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_generator_make_course_form extends moodleform {

    /**
     * Course generation tool form definition.
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('select', 'size', get_string('size', 'tool_generator'),
                tool_generator_course_backend::get_size_choices());
        $mform->setDefault('size', tool_generator_course_backend::DEFAULT_SIZE);

        $mform->addElement('text', 'shortname', get_string('shortnamecourse'));
        $mform->addRule('shortname', get_string('missingshortname'), 'required', null, 'client');
        $mform->setType('shortname', PARAM_TEXT);

        $mform->addElement('text', 'fullname', get_string('fullnamecourse'));
        $mform->setType('fullname', PARAM_TEXT);

        $mform->addElement('editor', 'summary', get_string('coursesummary'));
        $mform->setType('summary', PARAM_RAW);

        $mform->addElement('submit', 'submit', get_string('createcourse', 'tool_generator'));
    }

    /**
     * Form validation.
     *
     * @param array $data
     * @param array $files
     * @return void
     */
    public function validation($data, $files) {
        global $DB;
        $errors = array();

        // Check course doesn't already exist.
        if (!empty($data['shortname'])) {
            // Check shortname.
            $error = tool_generator_course_backend::check_shortname_available($data['shortname']);
            if ($error) {
                $errors['shortname'] = $error;
            }
        }

        return $errors;
    }
}

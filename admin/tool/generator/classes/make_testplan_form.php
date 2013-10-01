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
 * Test plan form.
 *
 * @package tool_generator
 * @copyright 2013 David Monllaó
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/generator/classes/testplan_backend.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/generator/classes/course_backend.php');

/**
 * Form with options for creating large course.
 *
 * @package tool_generator
 * @copyright 2013 David Monllaó
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_generator_make_testplan_form extends moodleform {

    /**
     * Test plan form definition.
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('select', 'size', get_string('size', 'tool_generator'),
            tool_generator_testplan_backend::get_size_choices());
        $mform->setDefault('size', tool_generator_testplan_backend::DEFAULT_SIZE);

        $mform->addElement('select', 'courseid', get_string('targetcourse', 'tool_generator'),
            tool_generator_testplan_backend::get_course_options());

        $mform->addElement('advcheckbox', 'updateuserspassword', get_string('updateuserspassword', 'tool_generator'));
        $mform->addHelpButton('updateuserspassword', 'updateuserspassword', 'tool_generator');

        $mform->addElement('submit', 'submit', get_string('createtestplan', 'tool_generator'));
    }

    /**
     * Checks that the submitted data allows us to create a test plan.
     *
     * @param array $data
     * @param array $files
     * @return array An array of errors
     */
    public function validation($data, $files) {
        global $CFG;

        $errors = array();
        if (empty($CFG->tool_generator_users_password) || is_bool($CFG->tool_generator_users_password)) {
            $errors['updateuserspassword'] = get_string('error_nouserspassword', 'tool_generator');
        }

        // Better to repeat here the query than to do it afterwards and end up with an exception.
        if ($courseerrors = tool_generator_testplan_backend::has_selected_course_any_problem($data['courseid'], $data['size'])) {
            $errors = array_merge($errors, $courseerrors);
        }

        return $errors;
    }

}

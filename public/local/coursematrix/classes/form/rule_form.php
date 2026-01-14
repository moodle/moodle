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
 * Rule form definition.
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_coursematrix\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for editing a course matrix rule.
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rule_form extends \moodleform {
    /**
     * Define the form definition.
     */
    public function definition() {
        $mform = $this->_form;

        // Custom data passed to constructor.
        $dept = $this->_customdata['department'] ?? '';
        $job = $this->_customdata['jobtitle'] ?? '';

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // If we are editing a specific combo, make these readonly or static.
        $mform->addElement('static', 'department_static', get_string('department', 'local_coursematrix'));
        $mform->setDefault('department_static', $dept);

        $mform->addElement('hidden', 'department');
        $mform->setType('department', PARAM_TEXT);
        $mform->setDefault('department', $dept);

        $mform->addElement('static', 'jobtitle_static', get_string('jobtitle', 'local_coursematrix'));
        $mform->setDefault('jobtitle_static', $job);

        $mform->addElement('hidden', 'jobtitle');
        $mform->setType('jobtitle', PARAM_TEXT);
        $mform->setDefault('jobtitle', $job);

        // Courses autocomplete.
        global $DB;
        $allcourses = $DB->get_records_menu('course', [], 'fullname', 'id, fullname');
        if (isset($allcourses[1])) {
            unset($allcourses[1]);
        }

        $mform->addElement('autocomplete', 'courses', get_string('courses', 'local_coursematrix'), $allcourses, [
            'multiple' => true,
            'noselectionstring' => get_string('selectcourses', 'local_coursematrix'),
        ]);

        // Learning plans autocomplete.
        $allplans = $DB->get_records_menu('local_coursematrix_plans', [], 'name', 'id, name');
        if (!empty($allplans)) {
            $mform->addElement('autocomplete', 'learningplans', get_string('learningplans', 'local_coursematrix'), $allplans, [
                'multiple' => true,
                'noselectionstring' => get_string('selectlearningplans', 'local_coursematrix'),
            ]);
        } else {
            $mform->addElement('static', 'noplans', get_string('learningplans', 'local_coursematrix'),
                get_string('noplans', 'local_coursematrix'));
        }

        $this->add_action_buttons(true, get_string('save', 'local_coursematrix'));
    }
}

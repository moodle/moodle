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
 * Learning Plan form definition.
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_coursematrix\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for editing a learning plan.
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plan_form extends \moodleform {
    /**
     * Define the form definition.
     */
    public function definition() {
        global $DB;

        $mform = $this->_form;
        $planid = $this->_customdata['id'] ?? 0;

        $mform->addElement('hidden', 'id', $planid);
        $mform->setType('id', PARAM_INT);

        // Plan name.
        $mform->addElement('text', 'name', get_string('planname', 'local_coursematrix'), ['size' => 50]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', null, 'client');

        // Description.
        $mform->addElement('editor', 'description', get_string('plandescription', 'local_coursematrix'));
        $mform->setType('description', PARAM_RAW);

        // Courses - multi-select with autocomplete.
        $allcourses = $DB->get_records_menu('course', [], 'fullname', 'id, fullname');
        if (isset($allcourses[1])) {
            unset($allcourses[1]); // Remove site course.
        }

        $mform->addElement('header', 'coursesheader', get_string('plancourses', 'local_coursematrix'));

        $mform->addElement('autocomplete', 'courses', get_string('courses', 'local_coursematrix'), $allcourses, [
            'multiple' => true,
            'noselectionstring' => get_string('selectcourses', 'local_coursematrix'),
        ]);
        $mform->addHelpButton('courses', 'plancourses', 'local_coursematrix');

        // Note about due days.
        $mform->addElement('static', 'duedays_info', '',
            '<div class="alert alert-info">' .
            get_string('duedays', 'local_coursematrix') . ': Enter the number of days allowed to complete each course. ' .
            'The order courses are selected determines the sequence in the learning plan.' .
            '</div>'
        );

        // Due days for each course (we'll use a repeat group approach).
        // For simplicity, we'll accept comma-separated due days.
        $mform->addElement('textarea', 'duedays_raw', get_string('duedays', 'local_coursematrix'),
            ['rows' => 3, 'cols' => 50]);
        $mform->setType('duedays_raw', PARAM_TEXT);
        $mform->addElement('static', 'duedays_help', '',
            '<small class="text-muted">Enter due days for each course, one per line or comma-separated (e.g., "14, 14, 7"). ' .
            'If fewer values than courses, remaining courses default to 14 days.</small>'
        );

        // Reminders.
        $mform->addElement('header', 'remindersheader', get_string('reminders', 'local_coursematrix'));

        $mform->addElement('textarea', 'reminders_raw', get_string('daysbefore', 'local_coursematrix'),
            ['rows' => 2, 'cols' => 30]);
        $mform->setType('reminders_raw', PARAM_TEXT);
        $mform->setDefault('reminders_raw', '7, 3, 1');
        $mform->addElement('static', 'reminders_help', '',
            '<small class="text-muted">Enter days before due to send reminders, comma-separated (e.g., "7, 3, 1" = ' .
            'remind 7 days before, 3 days before, and 1 day before due date).</small>'
        );

        $this->add_action_buttons(true, get_string('save', 'local_coursematrix'));
    }

    /**
     * Process incoming data.
     */
    public function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return null;
        }

        // Parse duedays from raw input.
        if (!empty($data->duedays_raw)) {
            $duedays = preg_split('/[\s,]+/', trim($data->duedays_raw));
            $data->duedays = array_map('intval', $duedays);
        } else {
            $data->duedays = [];
        }

        // Ensure duedays array is same length as courses.
        if (!empty($data->courses)) {
            while (count($data->duedays) < count($data->courses)) {
                $data->duedays[] = 14; // Default.
            }
        }

        // Parse reminders from raw input.
        if (!empty($data->reminders_raw)) {
            $reminders = preg_split('/[\s,]+/', trim($data->reminders_raw));
            $data->reminders = array_filter(array_map('intval', $reminders));
        } else {
            $data->reminders = [];
        }

        return $data;
    }

    /**
     * Set data for editing.
     */
    public function set_data($data) {
        // Convert duedays array to raw string.
        if (!empty($data->duedays)) {
            $data->duedays_raw = implode(', ', $data->duedays);
        }

        // Convert reminders array to raw string.
        if (!empty($data->reminders)) {
            $data->reminders_raw = implode(', ', $data->reminders);
        }

        parent::set_data($data);
    }
}

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
 * @package    enrol_workdaystudent
 * @copyright  2025 onwards LSU Online & Continuing Education
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Set the namespace.
namespace enrol_workdaystudent\form;

defined('MOODLE_INTERNAL') || die();

// We need this.
require_once($CFG->libdir . '/formslib.php');

class unenroll_form extends \moodleform {
    protected function definition() {
        $mform = $this->_form;
        $users = $this->_customdata['users'] ?? [];
        $courseid = $this->_customdata['courseid'] ?? 0;

        // Make sure we have a courseid.
        if ($courseid) {
            $mform->addElement('hidden', 'id', $courseid);
            $mform->setType('id', PARAM_INT);
        }

        // make sure we have users.
        if ($users) {

            // Build the table.
            $table = new \html_table();
            $table->head = [
                get_string('fullname'),
                get_string('username'),
            ];

            // Loop through the users.
            foreach ($users as $user) {

                // Populate the table.
                $table->data[] = [
                    fullname($user),
                    s($user->username),
                ];
            }

            // Write out the table.
            $mform->addElement('html', \html_writer::table($table));
        } else {

            // It's awfully empty in here.
            $mform->addElement('html',
                \html_writer::tag('p', get_string('nounenrollcandidates', 'enrol_workdaystudent'))
            );
        }

        // Add le-buttons.
        $this->add_action_buttons(true, get_string('unenrollall', 'enrol_workdaystudent'));
    }
}

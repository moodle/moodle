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
 * A form for configuring of Moodle tab in Teams.
 *
 * @package local_o365
 * @author  Enovation Solutions
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2016 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace local_o365\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/lib/formslib.php');

/**
 * A form for configuring of Moodle tab in Teams.
 *
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2018 onwards Microsoft, Inc. (http://microsoft.com/)
 */
class teamstabconfiguration extends \moodleform {

    /**
     * Definition of the form.
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function definition() {
        $mform = $this->_form;

        $courseoptions = self::get_course_options();
        if ($courseoptions) {
            // User can access at least one course, show tab name field and course selector.
            $mform->addElement('text', 'local_o365_teams_tab_name', get_string('tab_name', 'local_o365'),
                    array('onchange' => 'onTabNameChange()'));
            $mform->setType('local_o365_teams_tab_name', PARAM_TEXT);
            $mform->setDefault('local_o365_teams_tab_name', get_string('tab_moodle', 'local_o365'));

            $courseselector = $mform->createElement('select', 'local_o365_teams_course',
                get_string('course_selector_label', 'local_o365'),
                $courseoptions, array('onchange' => 'onCourseChange()'));
            $courseselector->setSize(100);
            $courseselector->setMultiple(true);

            $mform->addElement($courseselector);
            $mform->setType('course', PARAM_INT);

        } else {
            // User cannot access any course, show message.
            $messagehtml = \html_writer::tag('p', get_string('teams_no_course', 'local_o365'));
            $mform->addElement('html', $messagehtml);
        }
    }

    /**
     * Return a list of courses that the user has access to, to be used as options in the drop down list.
     *
     * @return array
     * @throws \dml_exception
     */
    private function get_course_options() {
        global $DB, $USER;

        $courseoptions = array();

        if (is_siteadmin($USER->id)) {
            $courses = $DB->get_records('course', ['visible' => 1]);
            unset($courses[SITEID]);
        } else {
            $courses = enrol_get_users_courses($USER->id, true, null, 'fullname');
        }

        foreach ($courses as $course) {
            $courseoptions[$course->id] = $course->fullname . ' (' . $course->shortname . ')';
        }

        asort($courseoptions);

        return $courseoptions;
    }
}

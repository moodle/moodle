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
 * @package    block_wdsprefs
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/formslib.php");

class select_courses_form extends moodleform {
    public function definition() {
        $mform = $this->_form;

        // Add the step = assign crap.
        $this->_form->addElement('hidden', 'step', 'course');
        $this->_form->setType('step', PARAM_TEXT);

        // Get the sections for this period.
        $sectionsbycourse = $this->_customdata['sectionsbycourse'] ?? [];

        // Get the count of sections.
        $sectioncount = array_sum(array_map('count', $sectionsbycourse));

        // Get the courses.
        $courses = array_keys($sectionsbycourse);

        // Add the header.
        $mform->addElement('header',
            'wdsprefs:selectcoursesheader',
            get_string('wdsprefs:selectcoursesheader', 'block_wdsprefs'));

        // Instructions.
        $mform->addElement('html',
            '<div class="alert alert-info"><p>' .
            get_string('wdsprefs:crosssplitinstructions2', 'block_wdsprefs') .
            '</p></div>'
        );


        // Add checkboxes for course selection.
        foreach ($sectionsbycourse as $coursename => $sections) {

            // Get the section count for this course.
            $scount = count($sections);

            // Handle pluralization manually.
            $sectionslabel = $scount == 1 ?
                ' (' . $scount . ' ' . get_string('wdsprefs:section', 'block_wdsprefs') . ')' :
                ' (' . $scount . ' ' . get_string('wdsprefs:sections', 'block_wdsprefs') . ')';


            // Sanitize course name.
            $sanitized = str_replace([' ', '/'], ['_', '-'], $coursename);

            // Add the form items.
            $mform->addElement('advcheckbox', 'selectedcourses_' .
                $sanitized,
                '',
                $coursename .
                $sectionslabel
            );
        }

        // Add the form item for the number of course shells dropdown.
        $mform->addElement('select',
            'shellcount',
            get_string('wdsprefs:shellcount', 'block_wdsprefs'),
            array_combine(range(1, $sectioncount), range(1, $sectioncount)));

        // Set the default.
        $mform->setDefault('shellcount', 1);

        // Add the action buttons.
        $this->add_action_buttons(true, get_string('continue'));
    }
}

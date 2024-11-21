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
 * Block edit form class for the block_pimenkofeaturedcourses plugin.
 *
 * @package   block_pimenkofeaturedcourses
 * @copyright Pimenko | Sylvain Revenu
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_pimenkofeaturedcourses_edit_form extends block_edit_form {

    /**
     * @param $mform
     * @return void
     * @throws coding_exception
     */
    public function specific_definition($mform): void {
        global $DB, $PAGE;

        $mform = $this->_form;

        // For some reasons calling $this->page->requires->js_call_amd is not applying javascript to ur page ...
        // Using this for now.
        $PAGE->requires->js_call_amd('block_pimenkofeaturedcourses/updatebutton', 'init');

        // Section header title according to language file.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        $courses = get_courses();
        $courseslist = [];

        foreach ($courses as $key => $course) {
            // List des cours.
            // Remove main site course.
            if ($course->id !== '1') {
                $courseslist[$key] = format_string($course->fullname);
            }
        }

        $options = array(
            'multiple' => true,
            'noselectionstring' => get_string('allareas', 'search'),
            'data-updatebutton-field' => 'autocomplete',
        );
        $mform->addElement('autocomplete',
            'config_courseslist', get_string('searcharea', 'search'), $courseslist, $options);
        // Button to update format-specific options on format change (will be hidden by JavaScript).
        $mform->registerNoSubmitButton('updatecourseslist');
        $mform->addElement('submit', 'updatecourseslist', get_string('updatecourseslist', 'block_pimenkofeaturedcourses'), [
            'data-updatebutton-field' => 'updateButton',
            'class' => 'd-none',
        ]);

        $coursesselect = optional_param_array('config_courseslist', [], PARAM_TEXT);
        if (!$coursesselect) {
            $configdata = unserialize_object(base64_decode($this->block->instance->configdata));
            if (isset($configdata->courseslist)) {
                $coursesselect = $configdata->courseslist;
            }
        }

        if (isset($coursesselect)) {
            $coursesnumber = count($coursesselect);
            $options = [];
            for ($i = 1; $i <= $coursesnumber; $i++) {
                $options[] = $i;
            }

            foreach ($coursesselect as $course) {
                $select = $mform->addElement('select', 'config_course_order_' . clean_param($course, PARAM_INT),
                    get_string('orderof', 'block_pimenkofeaturedcourses') . ' ' . $courseslist[clean_param($course, PARAM_INT)],
                    $options);
                $select->setMultiple(false);
            }
        }

        // This will select the skills A and B.
    }
}

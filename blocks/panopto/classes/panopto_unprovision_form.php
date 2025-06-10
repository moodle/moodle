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
 * Create form for course unprovision.
 *
 * @package block_panopto
 * @copyright  Panopto 2020
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class panopto_unprovision_form extends moodleform {

    /**
     * @var string $title
     */
    protected $title = '';

    /**
     * @var string $description
     */
    protected $description = '';

    /**
     * Defines a Panopto unprovision form
     */
    public function definition() {

        global $DB;

        $mform = & $this->_form;

        // Get all categories with no children (all leaf nodes).
        $coursesraw = $DB->get_records_sql(
            'SELECT id, shortname, fullname FROM {course} WHERE id IN (SELECT moodleid FROM {block_panopto_foldermap})'
        );
        $courses = [];
        if ($coursesraw) {
            foreach ($coursesraw as $course) {
                $courses[$course->id] = $course->shortname . ': ' . $course->fullname;
            }
        }
        asort($courses);

        $select = $mform->addElement('select', 'courses', get_string('unprovisioncourseselect', 'block_panopto'), $courses);
        $select->setMultiple(true);
        $select->setSize(32);
        $mform->addHelpButton('courses', 'unprovisioncourseselect', 'block_panopto');

        $this->add_action_buttons(true, get_string('unprovision', 'block_panopto'));
    }
}

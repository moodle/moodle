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
 *
 * @package    block_cps
 * @copyright  2019 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class unwant_form extends moodleform {
    public function definition() {
        $m =& $this->_form;

        $semesters = ues_semester::get_all();

        $sections = $this->_customdata['sections'];

        $courses = ues_course::merge_sections($sections);

        unset($sections);

        foreach ($courses as $courseid => $course) {

            $m->addElement('header', 'course_'.$courseid, $course);

            $actions = array('all', 'none');

            $map = function ($action) use ($courseid) {
                $url = new moodle_url('/blocks/cps/unwant.php', array(
                    'select' => $action,
                    'what' => $courseid
                ));

                $attrs = array('id' => $action . '_' . $courseid);

                return html_writer::link($url, get_string($action), $attrs);
            };

            $cleanlinks = implode(' / ', array_map($map, $actions));

            $m->addElement('static', 'all_none_'.$courseid, '', $cleanlinks);

            foreach ($course->sections as $section) {
                $semester = $semesters[$section->semesterid];
                $id = 'course'.$courseid.'_section'.$section->id;

                $name = $semester->year . ' ' . $semester->name . ' ' . $section;

                $m->addElement('checkbox', 'section_'.$section->id, '', $name,
                    array('id' => $id));
            }
        }

        $buttons = array(
            $m->createElement('submit', 'save', get_string('savechanges')),
            $m->createElement('cancel')
        );

        $m->addGroup($buttons, 'buttons', '', array(' '), false);

        $m->closeHeaderBefore('buttons');
    }
}

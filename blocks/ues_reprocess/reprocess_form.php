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
 * @package    block_ues_reprocess
 * @copyright  2014 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class reprocess_form extends moodleform {
    public function definition() {
        global $USER, $COURSE;

        $m =& $this->_form;

        $stocked = array();

        foreach ($this->_customdata['sections'] as $section) {
            $userparams = array(
                'userid' => $USER->id,
                'sectionid' => $section->id,
                'primary_flag' => 1,
                'status' => ues::ENROLLED
            );

            $primary = ues_teacher::get($userparams);

            if ($primary and $primary->userid != $USER->id) {
                continue;
            }

            $semid = $section->semesterid;
            $couid = $section->courseid;

            if (!isset($stocked[$semid])) {
                $stocked[$semid] = array();
            }

            if (!isset($stocked[$semid][$couid])) {
                $stocked[$semid][$couid] = array();
            }

            $stocked[$semid][$couid][$section->id] = $section;
        }

        foreach ($stocked as $semesterid => $courses) {
            $semester = ues_semester::get(array('id' => $semesterid));

            $semname = "$semester->year $semester->name $semester->session_key";
            $m->addElement('header', 'sem_header_' . $semesterid, $semname);

            foreach ($courses as $courseid => $sections) {
                $course = ues_course::get(array('id' => $courseid));

                $name = "<strong>$semname $course->department $course->cou_number</strong>";
                $m->addElement('checkbox', 'course_'.$semesterid.'_' . $courseid, $name, '');

                foreach ($sections as $sectionid => $section) {
                    if (empty($section->section_reprocessed)) {
                        $m->addElement('checkbox', 'section_'.$sectionid, 'Section ' . $section->sec_number, '');

                        $m->disabledIf('section_'.$sectionid, 'course_'.$semesterid.'_'.$courseid, 'checked');
                    } else {
                        $m->addElement('static', 'section_'.$sectionid, 'Section ' . $section->sec_number, 'X');
                    }
                }

                $m->addElement('static', 'spacer_'.$semesterid.'_'.$courseid, '', '');
            }
        }

        $m->addElement('hidden', 'id', $this->_customdata['id']);
        $m->setType('id', PARAM_INT);

        $m->addElement('hidden', 'type', $this->_customdata['type']);
        $m->setType('type', PARAM_TEXT);

        $buttons = array(
            $m->createElement('submit', 'reprocess', get_string('reprocess', 'block_ues_reprocess')),
            $m->createElement('cancel')
        );

        $m->addGroup($buttons, 'subgroup', '', array(' '), false);
        $m->closeHeaderBefore('subgroup');
    }
}

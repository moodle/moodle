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
 * @copyright  2014 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class material_form extends moodleform {
    public function definition() {
        global $USER;

        $m =& $this->_form;

        $sections = $this->_customdata['sections'];

        $courses = ues_course::merge_sections($sections);

        $s = ues::gen_str('block_cps');

        $m->addElement('header', 'materials', $s('creating_materials'));

        foreach ($courses as $course) {
            $checkbox =& $m->addElement(
                'checkbox', 'material_'.$course->id, '', $course
            );
        }

        $buttons = array(
            $m->createElement('submit', 'save', get_string('savechanges')),
            $m->createElement('cancel')
        );

        $m->addGroup($buttons, 'buttons', '', array(' '), false);
        $m->closeHeaderBefore('buttons');
    }
}

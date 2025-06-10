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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once('lib.php');

class posting_period_form extends moodleform {
    public function definition() {
        $semesters = $this->_customdata['semesters'];

        $m =& $this->_form;

        $s = ues::gen_str('block_post_grades');

        $m->addElement('header', 'header', $s('posting_period'));

        $options = array();
        foreach ($semesters as $semester) {
            $options[$semester->id] = "$semester";
        }

        $m->addElement('select', 'post_type', $s('post_type'), post_grades::valid_types());

        $m->addElement('select', 'semesterid', $s('semester'), $options);
        $m->setType('semesterid', PARAM_INT);

        $m->addElement('date_time_selector', 'start_time', $s('start_time'));

        $m->addElement('date_time_selector', 'end_time', $s('end_time'));

        $m->addElement('checkbox', 'export_number', $s('export_number'));

        $m->addElement('hidden', 'id', '');
        $m->setType('id', PARAM_INT);

        $this->add_action_buttons();
    }
}

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

class query_form extends moodleform {
    public function definition() {
        $m =& $this->_form;

        $m->addElement('header', 'header', get_string('find_postings', 'block_post_grades'));
        $m->addElement('text', 'shortname', get_string('shortname'));
        $m->setType('shortname', PARAM_TEXT);

        $this->add_action_buttons();
    }
}

class reset_form extends moodleform {
    public function definition() {
        $m =& $this->_form;

        $m->addElement('header', 'header', get_string('reset_posting', 'block_post_grades'));
        $m->addElement('hidden', 'shortname', $this->_customdata['shortname']);
        $m->setType('shortname', PARAM_TEXT);

        $entries = $this->_customdata['entries'];

        if (empty($entries)) {
            global $OUTPUT;
            $msg = $OUTPUT->notification(get_string('nopostings', 'block_post_grades'));
            $m->addElement('static', 'no_elements', '', $msg);
        }

        foreach ($entries as $entry) {
            $a = new stdClass();
            $a->post_type = get_string($entry->post_type, 'block_post_grades');
            $a->fullname = fullname($entry);
            $a->course_name = $entry->fullname;
            $a->sec_number = $entry->sec_number;

            $label = get_string('posting_for', 'block_post_grades', $a);
            $m->addElement('checkbox', $entry->id, '', $label);
        }

        $this->add_action_buttons();
    }
}
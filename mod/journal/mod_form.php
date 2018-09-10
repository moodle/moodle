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

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_journal_mod_form extends moodleform_mod {

    public function definition() {
        global $COURSE;

        $mform = & $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('journalname', 'journal'), array('size' => '64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $this->standard_intro_elements(get_string('journalquestion', 'journal'));

        $options = array();
        $options[0] = get_string('alwaysopen', 'journal');
        for ($i = 1; $i <= 13; $i++) {
            $options[$i] = get_string('numdays', '', $i);
        }
        for ($i = 2; $i <= 16; $i++) {
            $days = $i * 7;
            $options[$days] = get_string('numweeks', '', $i);
        }
        $options[365] = get_string('numweeks', '', 52);
        $mform->addElement('select', 'days', get_string('daysavailable', 'journal'), $options);
        if ($COURSE->format == 'weeks') {
            $mform->setDefault('days', '7');
        } else {
            $mform->setDefault('days', '0');
        }

        $this->standard_grading_coursemodule_elements();

        $this->standard_coursemodule_elements();

        $this->add_action_buttons();
    }

}

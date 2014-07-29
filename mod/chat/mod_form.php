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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    //  It must be included from a Moodle page.
}

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_chat_mod_form extends moodleform_mod {

    /**
     * Define the chat activity settings form
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('chatname', 'chat'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->add_intro_editor(true, get_string('chatintro', 'chat'));

        // Chat sessions.
        $mform->addElement('header', 'sessionshdr', get_string('sessions', 'chat'));

        $mform->addElement('date_time_selector', 'chattime', get_string('chattime', 'chat'));

        $options = array();
        $options[0]  = get_string('donotusechattime', 'chat');
        $options[1]  = get_string('repeatnone', 'chat');
        $options[2]  = get_string('repeatdaily', 'chat');
        $options[3]  = get_string('repeatweekly', 'chat');
        $mform->addElement('select', 'schedule', get_string('repeattimes', 'chat'), $options);

        $options = array();
        $options[0]    = get_string('neverdeletemessages', 'chat');
        $options[365]  = get_string('numdays', '', 365);
        $options[180]  = get_string('numdays', '', 180);
        $options[150]  = get_string('numdays', '', 150);
        $options[120]  = get_string('numdays', '', 120);
        $options[90]   = get_string('numdays', '', 90);
        $options[60]   = get_string('numdays', '', 60);
        $options[30]   = get_string('numdays', '', 30);
        $options[21]   = get_string('numdays', '', 21);
        $options[14]   = get_string('numdays', '', 14);
        $options[7]    = get_string('numdays', '', 7);
        $options[2]    = get_string('numdays', '', 2);
        $mform->addElement('select', 'keepdays', get_string('savemessages', 'chat'), $options);

        $mform->addElement('selectyesno', 'studentlogs', get_string('studentseereports', 'chat'));
        $mform->addHelpButton('studentlogs', 'studentseereports', 'chat');

        $this->standard_coursemodule_elements();

        $this->add_action_buttons();
    }
}

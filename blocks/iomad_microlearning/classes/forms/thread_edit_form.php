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
 * @package   block_iomad_microlearning
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_microlearning\forms;

defined('MOODLE_INTERNAL') || die;

use \moodleform;

class thread_edit_form extends \moodleform {

    public function definition() {
        global $CFG;

        $mform =& $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'companyid');
        $mform->setType('companyid', PARAM_INT);

        $mform->addElement('text', 'name',
                            get_string('threadname', 'block_iomad_microlearning'),
                            'maxlength = "254" size = "50"');
        $mform->addHelpButton('name', 'threadname', 'block_iomad_microlearning');
        $mform->addRule('name',
                        get_string('missingname', 'block_iomad_microlearning'),
                        'required', null, 'client');
        $mform->setType('name', PARAM_MULTILANG);

        $mform->addElement('selectyesno', 'send_message',
                            get_string('send_message', 'block_iomad_microlearning'));
        $mform->addHelpButton('send_message', 'send_message', 'block_iomad_microlearning');

        $mform->addElement('date_selector', 'startdate', get_string('startdate', 'block_iomad_microlearning'));
        $mform->addHelpButton('startdate', 'startdate', 'block_iomad_microlearning');

        $mform->addElement('duration', 'message_preset',
                            get_string('message_preset', 'block_iomad_microlearning'), array('defaultunit' => 86400));
        $mform->addHelpButton('message_preset', 'message_preset', 'block_iomad_microlearning');

        $hourarray = array();
        $unit = array(0,1,2,3,4,5,6,7,8,9);
        $ten = array(0,1,2,3,4,5);
        foreach ($ten as $t) {
            foreach ($unit as $u) {
                if ($t == 2 and $u == 4) {
                    break 2;
                }
                $hourarray[$t.$u] = $t.$u;
            }
        }

        $minutearray = array();
        foreach ($ten as $t) {
            foreach ($unit as $u) {
                $minutearray[$t.$u] = $t.$u;
            }
        }

        $timegroup = array();
        $timegroup[] = $mform->createElement('select', 'hour', '', $hourarray);
        $timegroup[] = $mform->createElement('select', 'minute', '', $minutearray);
        $mform->addGroup($timegroup, 'message_time', get_string('message_time', 'block_iomad_microlearning'), ' ', false);
        $mform->addHelpButton('message_time', 'message_time', 'block_iomad_microlearning');

        $mform->addElement('duration', 'releaseinterval',
                            get_string('interval', 'block_iomad_microlearning'), array('defaultunit' => 86400));
        $mform->addHelpButton('releaseinterval', 'interval', 'block_iomad_microlearning');

        $mform->addElement('duration', 'defaultdue',
                            get_string('defaultdue', 'block_iomad_microlearning'), array('defaultunit' => 86400));
        $mform->addHelpButton('defaultdue', 'defaultdue', 'block_iomad_microlearning');

        $mform->addElement('selectyesno', 'halt_until_fulfilled',
                            get_string('halt_until_fulfilled', 'block_iomad_microlearning'));
        $mform->addHelpButton('halt_until_fulfilled', 'halt_until_fulfilled', 'block_iomad_microlearning');

        $mform->addElement('selectyesno', 'send_reminder',
                            get_string('send_reminder', 'block_iomad_microlearning'));
        $mform->addHelpButton('send_reminder', 'send_reminder', 'block_iomad_microlearning');

        $mform->addElement('duration', 'reminder1',
                            get_string('reminder1', 'block_iomad_microlearning'), array('defaultunit' => 86400));
        $mform->addHelpButton('reminder1', 'reminder1', 'block_iomad_microlearning');

        $mform->addElement('duration', 'reminder2',
                            get_string('reminder2', 'block_iomad_microlearning'), array('defaultunit' => 86400));
        $mform->addHelpButton('reminder2', 'reminder2', 'block_iomad_microlearning');

        $mform->addElement('selectyesno', 'active',
                            get_string('active', 'block_iomad_microlearning'));
        $mform->addHelpButton('active', 'active', 'block_iomad_microlearning');


        $this->add_action_buttons();
    }

    public function validation($data, $files) {
        global $DB;

        $errors = array();

        if ($threadbyname = $DB->get_record('microlearning_thread', array('companyid' => $data['companyid'], 'name' => trim($data['name'])))) {
            if ($threadbyname->id != $data['id']) {
                $errors['name'] = get_string('nameinuse', 'block_iomad_microlearning');
            }
        }
        return $errors;
    }
}

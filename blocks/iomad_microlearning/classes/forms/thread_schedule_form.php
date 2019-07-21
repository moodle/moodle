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

namespace block_iomad_microlearning\forms;

defined('MOODLE_INTERNAL') || die;

use \moodleform;

class thread_edit_form extends \moodleform {
    protected $threadid;
    protected $nuggets;

    public function __construct($actionurl, $threadid, $nuggets) {
        $this->threadid = $threadid;
        $this->nuggets = $nuggets;
    }

    public function definition() {
        global $CFG;

        $mform =& $this->_form;

        $mform->addElement('hidden', 'threadid');
        $mform->setType('threadid', PARAM_INT);

        $headhtml = '<table class="generaltable" width="95%"><thead><tr>
                     <th class="header c0" style="text-align:left;" scope="col">' . get_string('nugget', 'block_iomad_microlearning') . '</th>
                     <th class="header c1" style="text-align:right;" scope="col">' . get_string('order') . '</th>
                     <th class="header c2" style="text-align:right;" scope="col">' . get_string('scheduledate', 'block_iomad_microlearning') . '</th>
                     <th class="header c3" style="text-align:right;" scope="col">' . get_string('duedate', 'block_iomad_microlearning') . '</th>
                     <th class="header c4" style="text-align:right;" scope="col">' . get_string('reminder1', 'block_iomad_microlearning') . '</th>
                     <th class="header c5 lastcol" style="text-align:right;" scope="col">' . get_string('reminder2', 'block_iomad_microlearning') . '</th></tr></thead><tbody>';

        $mform->addElement('html', $headhtml);

        foreach ($this->nuggets as $nugget) {
            $mform->addElement('html', '<tr class=""><td class="cell c0" style="">' . format_text($nugget->name) .
                                       '</td><td class="cell c1" style="text-align:left;">');
            $mform->addElement('html', $nugget->order + 1 . '</td><td class="cell c2" style="text-align:left;">');
            $mform->addElement('date_time_selector', "schedulearray[$nugget->id]", '');
            $mform->addElement('html', '</td><td class="cell c2" style="text-align:left;">');
            $mform->addElement('date_time_selector', "duedatearray[$nugget->id]", '');
            $mform->addElement('html', '</td><td class="cell c3" style="text-align:left;">');
            $mform->addElement('date_time_selector', "reminder1array[$nugget->id]", '');
            $mform->addElement('html', '</td><td class="cell c4" style="text-align:left;">');
            $mform->addElement('date_time_selector', "reminder2array[$nugget->id]", '');
            $mform->addElement('html', '</td></tr>');
        }
        $mform->addElement('html', '</tbody></table>');

        // Add buttons.
        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('save'));
        $buttonarray[] = &$mform->createElement('submit', 'resetallbutton', get_string('resetschedule', 'block_iomad_microlearning'));
        $buttonarray[] = &$mform->createElement('cancel');

        $mform->addGroup($buttonarray, 'buttonarray', '', array(' '), false);

    }

    public function validation($data, $files) {
        global $DB;

        $errors = array();

        foreach ($this->nuggets as $nugget) {
            if ($data['duedatearray'][$nugget->id] < $data['schedulearray'][$nugget->id]) {
                $errors['duedatearray'][$nugget->id] = get_string('duedatebeforescheduledate', 'block_iomad_microlearning');
            }

            if ($data['reminder1datearray'][$nugget->id] < $data['schedulearray'][$nugget->id]) {
                $errors['reminder1datearray'][$nugget->id] = get_string('reminderdatebeforescheduledate', 'block_iomad_microlearning');
            }

            if ($data['reminder2datearray'][$nugget->id] < $data['schedulearray'][$nugget->id]) {
                $errors['reminder2datearray'][$nugget->id] = get_string('reminderdatebeforescheduledate', 'block_iomad_microlearning');
            }

            if ($data['reminder2datearray'][$nugget->id] < $data['reminder1datearray'][$nugget->id]) {
                $errors['reminder2datearray'][$nugget->id] = get_string('reminderdatesoutoforder', 'block_iomad_microlearning');
            }

            foreach ($this->nuggets as $check) {
                if ($check->order <= $nugget->order) {
                    // continue;
                }
                if ($data['schedulearray'][$check->id] < $data['schedulearray'][$nugget->id]) {
                    $errors['schedulearray'][$check->id] = get_string('scheduleoutoforder', 'block_iomad_microlearning');
                }
            }
        }
        return $errors;
    }
}

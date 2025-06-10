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

require_once($CFG->libdir.'/formslib.php');

require_once('lib.php');

class smart_file_form extends moodleform {
    public function definition() {
        global $COURSE;

        $s = function($key) {
            return get_string($key, 'gradeimport_smart');
        };

        $mform =& $this->_form;

        $mform->addElement('header', 'general', $s('upload_file'));

        $mform->addElement('hidden', 'id', $COURSE->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('filepicker', 'userfile', $s('file'));
        $mform->addRule('userfile', null, 'required');

        $options = $this->get_grade_item_options();

        $mform->addElement('select', 'grade_item_id', $s('grade_item'), $options);

        $this->add_action_buttons(false, $s('upload_file'));
    }

    public function get_grade_item_options() {
        global $COURSE, $DB;

        $s = function($key) {
            return get_string($key, 'gradeimport_smart');
        };

        $params = array('courseid' => $COURSE->id, 'locked' => false);

        $items = $DB->get_records('grade_items', $params, 'itemname asc',
            'id, gradetype, itemname, itemtype');

        $options = array();

        foreach ($items as $n => $item) {
            if ($item->itemtype == 'manual' and $item->gradetype > 0) {
                $options[$item->id] = $item->itemname;
            }
        }

        return $options;
    }
}

class smart_results_form extends moodleform {
    public function definition() {
        global $COURSE;

        $s = function($key) {
            return get_string($key, 'gradeimport_smart');
        };

        $mform =& $this->_form;

        $mform->addElement('header', 'general', $s('import_notices'));

        $data = $this->_customdata;

        $messages = isset($data['messages']) ? $data['messages'] : null;

        if (is_array($messages)) {
            foreach (array_unique($messages) as $message) {
                $mform->addElement('static', '', '', $message);
            }
        }
    }
}

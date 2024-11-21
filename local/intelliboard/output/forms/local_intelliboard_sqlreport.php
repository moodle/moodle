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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2018 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');

class local_intelliboard_sqlreport_form extends moodleform {

    function definition() {
        global $CFG, $DB;

        $mform = $this->_form;
        $data = $this->_customdata['data'];
        $data->sqlcode = base64_decode($data->sqlcode);

        $mform->addElement('header', 'general', get_string('sqlreport', 'local_intelliboard'));

        $mform->addElement('text', 'name', get_string('sqlreportname', 'local_intelliboard'));
        $mform->setType('name', PARAM_RAW);

        $mform->addElement('textarea', 'sqlcode', get_string('sqlreportcode', 'local_intelliboard'), ['readonly'=>true]);
        $mform->setType('sqlcode', PARAM_RAW);

        $options = [
            get_string('sqlreportinactive', 'local_intelliboard'),
            get_string('sqlreportactive', 'local_intelliboard'),
        ];
        $mform->addElement('select', 'status', get_string('status', 'local_intelliboard'), $options);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons();
        $this->set_data($data);
    }

    /**
     * Some basic validation
     *
     * @param $data
     * @param $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }
}

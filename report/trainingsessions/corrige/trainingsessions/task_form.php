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
 * @package    report_trainingsessions
 * @category   report
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class Task_Form extends moodleform {

    public function definition() {

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'view');
        $mform->setType('view', PARAM_TEXT);

        $mform->addElement('hidden', 'startday');
        $mform->setType('startday', PARAM_INT);

        $mform->addElement('hidden', 'startmonth');
        $mform->setType('startmonth', PARAM_INT);

        $mform->addElement('hidden', 'startyear');
        $mform->setType('startyear', PARAM_INT);

        $mform->addElement('hidden', 'endday');
        $mform->setType('endday', PARAM_INT);

        $mform->addElement('hidden', 'endmonth');
        $mform->setType('endmonth', PARAM_INT);

        $mform->addElement('hidden', 'endyear');
        $mform->setType('endyear', PARAM_INT);

        $mform->addElement('hidden', 'groupid');
        $mform->setType('groupid', PARAM_INT);

        $mform->addElement('hidden', 'taskid');
        $mform->setType('taskid', PARAM_INT);

        $mform->addElement('header', 'headernewtask', get_string('newtask', 'report_trainingsessions'));

        $optionsstr = get_string('group').': '.$this->_customdata['groupname'].' ';
        if ($this->_customdata['startyear'] == -1) {
            $str = get_string('coursestart', 'report_trainingsessions');
            $optionsstr .= '<br/>'.get_string('range', 'report_trainingsessions').': '.$str;
        } else {
            $rangestr = $this->_customdata['startyear'].'-'.$this->_customdata['startmonth'].'-'.$this->_customdata['startday'];
            $optionsstr .= '<br/>'.get_string('range', 'report_trainingsessions').': '.$rangestr;
        }
        if ($this->_customdata['endyear'] == -1) {
            $optionsstr .= ' => '.get_string('now', 'report_trainingsessions');
        } else {
            $optionsstr .= ' => '.$this->_customdata['endyear'].'-'.$this->_customdata['endmonth'].'-'.$this->_customdata['endday'];
        }

        $mform->addElement('hidden', 'taskname', get_string('task', 'report_trainingsessions', $optionsstr));
        $mform->setType('taskname', PARAM_TEXT);

        $label = get_string('taskname', 'report_trainingsessions');
        $desc = get_string('task', 'report_trainingsessions', $optionsstr);
        $mform->addElement('static', 'tasknamestatic', $label, $desc);

        // Which data to export and how to build the report.
        $layoutoptions = array(
            'onefulluserpersheet' => get_string('onefulluserpersheet', 'report_trainingsessions'),
            'oneuserperrow' => get_string('oneuserperrow', 'report_trainingsessions'),
            'sessionsonly' => get_string('sessionsonly', 'report_trainingsessions')
        );

        $mform->addElement('select', 'reportlayout', get_string('reportlayout', 'report_trainingsessions'), $layoutoptions);

        // Which data to export and how to build the report.
        $scopeoptions = array(
            'currentcourse' => get_string('currentcourse', 'report_trainingsessions'),
            'allcourses' => get_string('allcourses', 'report_trainingsessions'),
        );

        $mform->addElement('select', 'reportscope', get_string('reportscope', 'report_trainingsessions'), $scopeoptions);
        $mform->addHelpButton('reportscope', 'reportscope', 'report_trainingsessions');

        // What file format (file renderer) to use.
        $options = report_trainingsessions_get_batch_formats();
        $mform->addElement('select', 'reportformat', get_string('reportformat', 'report_trainingsessions'), $options);

        // In which directory to store results.
        $mform->addElement('text', 'outputdir', get_string('outputdir', 'report_trainingsessions'), array('size' => 80));
        $mform->setType('outputdir', PARAM_PATH);
        $mform->addHelpButton('outputdir', 'outputdir', 'report_trainingsessions');

        // When to perform the report.
        $mform->addElement('date_time_selector', 'batchdate', get_string('batchdate', 'report_trainingsessions'));
        $mform->addHelpButton('batchdate', 'batchdate', 'report_trainingsessions');

        // Do the report needs to be rerun later?
        $options = report_trainingsessions_get_batch_replays();
        $mform->addElement('select', 'replay', get_string('replay', 'report_trainingsessions'), $options);

        // If rerun, in what delay?
        $mform->addElement('text', 'replaydelay', get_string('replaydelay', 'report_trainingsessions'), array('size' => 10));
        $mform->setType('replaydelay', PARAM_INT);
        $mform->setDefault('replaydelay', 1440);
        $mform->disabledIf('replaydelay', 'replay');
        $mform->addHelpButton('replaydelay', 'replaydelay', 'report_trainingsessions');

        $this->add_action_buttons();
    }

    public function validation($data, $files = null) {
        global $CFG;

        if (preg_match('#^'.$CFG->dataroot.'#', $data['outputdir'])) {
            $errors['outputdir'] = get_string('errornoabsolutepath', 'report_trainingsessions');
        }

        if (preg_match('#^/#', $data['outputdir'])) {
            $errors['outputdir'] = get_string('errornoabsolutepath', 'report_trainingsessions');
        }
    }
}

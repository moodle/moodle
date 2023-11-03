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
 * Form for editing LearnerScript report dashboard block instances.
 * @package  block_reportdashboard
 * @author eAbyas Info Solutions
 */
defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/formslib.php');
use block_learnerscript\local\ls as ls;

class reportselect_form extends moodleform {

    public function definition() {
        global $DB, $CFG, $PAGE;

        $mform = $this->_form;
        $reportlist = $DB->get_records_select_menu('block_learnerscript',
            "global=1 AND visible=1 AND type !='statistics'", null, '', 'id,name');
        $reportsdata = $DB->get_field('config_plugins', 'value',
            array('name' => 'dashboardReports'));
        $reports = unserialize($reportsdata);
        $existingreports = array();
        if ($this->_customdata['coursels']) {
            if (!empty($reports)) {
                foreach ($reports as $key => $value) {
                    $existingreports[] = $value['report'];
                }
            }
        }
        ksort($reportlist);
        $mform->addElement('html', '<div class="dashboard_widgets">');
        $textgroup = array();
        $textgroup[] = &$mform->createElement('static', 'selecttext', '',
            '<span class="dashboard_checkbox">  </span>');
        $textgroup[] = &$mform->createElement('static', 'reportnametext', '',
            '<span class="dashboard_reportname"><b>Report Name</b></span>');
        $textgroup[] = &$mform->createElement('static', 'reporttypetext', '',
            '<span class="widget_reporttype"><b>Report Type</b></span>');
        $textgroup[] = &$mform->createElement('static', 'wieghttext', '',
            '<span class="widget_position"><b>Position</b></span>');
        $mform->addGroup($textgroup, 'selectreporttext', '');

        $weightoptions = array();
        for ($i = -block_manager::MAX_WEIGHT; $i <= block_manager::MAX_WEIGHT; $i++) {
            $weightoptions[$i] = $i;
        }
        $first = -10;
        $weightoptions[$first] = get_string('bracketfirst', 'block', $first);
        $last = end($weightoptions);
        $weightoptions[$last] = get_string('bracketlast', 'block', $last);
        array_shift($weightoptions);
        $i = 0;
        $rolereports = (new ls)->listofreportsbyrole($this->_customdata['coursels'], '',
            $this->_customdata['parentcheck']);

        foreach ($rolereports as $key => $value) {
            $report = $DB->get_record('block_learnerscript', array('id' => $value['id']));
            require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');
            $reportclassname = 'block_learnerscript\lsreports\report_' . $report->type;
            $properties = new stdClass;
            $reportclass = new $reportclassname($report, $properties);
            $group = array();
            $reportcontenttypes = (new ls)->cr_listof_reporttypes($report->id);

            $group[] = &$mform->createElement('advcheckbox', 'report', null, '',
                array('group' => 1, 'class' => 'listofreports checkbox_name'),
                array(null, $value['id']));
            $group[] = &$mform->createElement('static', 'reportname' . $value['id'] . '', '',
                        '<span class="widgetreport_name">' . trim($value['name']) . '</span>');
            $group[] = &$mform->createElement('select',
                get_string('reporttype', 'block_reportdashboard'),
                 get_string('reporttype', 'block_reportdashboard'), $reportcontenttypes,
                 array('class' => 'select-reporttype'));
            $group[] = &$mform->createElement('select', 'wieght' . $i . '', '', $weightoptions, array('class' => 'select-wieght'));
            $mform->addGroup($group, 'selectreport' . $i . '', '');
            if ( in_array($value['id'], $existingreports)) {
                $mform->setDefault('selectreport' . $i . '[report]', true);
            }
            $i++;
        }
        $submitlabel = get_string('addtodashboard', 'block_reportdashboard');
        $this->add_action_buttons(false, $submitlabel);
        $mform->addElement('html', '</div>');
    }
}
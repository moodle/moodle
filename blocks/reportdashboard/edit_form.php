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
 * Form for editing HTML block instances.
 *
 * @package   block_reportdashboard
 * @copyright 2017 eAbyas Info Solutions
 * @license   http://www.gnu.org/copyleft/gpl.reportdashboard GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
use block_learnerscript\local\ls;
class block_reportdashboard_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        global $CFG, $DB, $PAGE;
        $PAGE->requires->js('/blocks/reporttiles/js/jscolor.js', true);
        // Fields for editing HTML block title and contents.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $reportlist = $DB->get_records_select_menu('block_learnerscript', "global=1 AND visible=1 AND type!='statistics'",
                                                        null, '', 'id, name');
        ksort($reportlist);
        $reports = array();
        $reports[0] = 'Select Report';
        $rolereports = (new ls)->listofreportsbyrole();
        foreach ($rolereports as $report) {
            $reports[$report['id']] = $report['name'];
        }

        $mform->addElement('text', 'config_blocktitle', get_string('blocktitle', 'block_reportdashboard'));
        $mform->setType('config_blocktitle', PARAM_TEXT);

        $mform->addElement('select', 'config_reportlist', get_string('listofreports', 'block_reportdashboard'), $reports);
        $durations = ['all' => get_string('all', 'block_reportdashboard'),
                      'week' => get_string('week', 'block_reportdashboard'), 
                      'month' => get_string('month', 'block_reportdashboard'), 
                      'year' => get_string('year', 'block_reportdashboard')];
        $mform->addElement('select', 'config_reportduration', get_string('reportduration', 'block_reportdashboard'), $durations);
        $mform->addElement('advcheckbox', 'config_disableheader', get_string('disableheader', 'block_reportdashboard'),
                            'Disable widget header and actions', array('group' => 1), array(0, 1));
        $PAGE->requires->yui_module('moodle-block_reportdashboard-reportselect', 'M.block_reportdashboard.init_reportselect',
                            array(array('formid' => $mform->getAttribute('id'))));
        $mform->addElement('hidden', 'reportcontenttype');
        $mform->setType('reportcontenttype', PARAM_RAW);

        $tilescolourpicker = get_string('tilesbackground', 'block_reporttiles');
        $mform->addElement('text', 'config_tilescolourpicker', $tilescolourpicker,
            array('data-class' => 'jscolor', 'value' => '12445f'));
        $mform->setType('config_tilescolourpicker', PARAM_RAW);
        $mform->registerNoSubmitButton('updatereportselect');

        $mform->addElement('submit', 'updatereportselect', 'updatereportselect');
    }

    public function set_data($defaults) {

        if (!$this->block->user_can_edit() && !empty($this->block->config->title)) {
            // If a title has been set but the user cannot edit it format it nicely.
            $title = $this->block->config->title;
            $defaults->config_title = format_string($title, true, $this->page->context);
            // Remove the title from the config so that parent::set_data doesn't set it.
            unset($this->block->config->title);
        }
        parent::set_data($defaults);
        // Restore $text.
        if (!isset($this->block->config)) {
            $this->block->config = new stdClass();
        }
        if (isset($title)) {
            // Reset the preserved title.
            $this->block->config->title = $title;
        }
    }

    public function definition_after_data() {
        global $CFG, $DB, $OUTPUT;

        $mform = $this->_form;
        $reportid = $mform->getElementValue('config_reportlist');
        if (isset($reportid) && $reportid[0]) {
            if (!$report = $DB->get_record('block_learnerscript', array('id' => $reportid[0]))) {
                $reportcontenttypes = array(null => '--SELECT--');
            } else {
                $reportcontenttypes = (new block_learnerscript\local\ls)->cr_listof_reporttypes($reportid[0]);
            }

            $reportcontenttype = $mform->createElement('select', 'config_reportcontenttype',
                get_string('reportcontenttype', 'block_reportdashboard'), $reportcontenttypes);
            $mform->insertElementBefore($reportcontenttype, 'reportcontenttype');
        }
    }

}

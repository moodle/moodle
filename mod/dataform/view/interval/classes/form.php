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
 * @package dataformview
 * @subpackage interval
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformview_interval_form extends dataformview_grid_form {

    /**
     *
     */
    protected function definition_view_specific() {
        // View template.
        $this->definition_view_template();

        // Entry template.
        $this->definition_entry_template();

        // View interval.
        $this->definition_view_interval();

        // Submission settings.
        $this->definition_view_submission();
    }

    /**
     *
     */
    protected function definition_view_interval() {
        $mform =& $this->_form;

        // Specifications.
        $mform->addElement('header', 'intervalsettingshdr', get_string('interval', 'dataformview_interval'));

        // Interval.
        $intervals = array(0 => get_string('always'),
                        'hourly' => get_string('hourly', 'dataformview_interval'),
                        'daily' => get_string('daily', 'dataformview_interval'),
                        'weekly' => get_string('weekly', 'dataformview_interval'),
                        'monthly' => get_string('monthly', 'dataformview_interval'),
                        'custom' => get_string('custom', 'dataformview_interval'));

        $strminutes = get_string('minutes');
        $strhours = get_string('hours');
        $strdays = get_string('days');

        $intervalgrp = array();
        $intervalgrp[] = &$mform->createElement('select', 'param5', null, $intervals);
        $intervalgrp[] = &$mform->createElement('select', 'customminutes', null, range(0, 59));
        $intervalgrp[] = &$mform->createElement('select', 'customhours', null, range(0, 23));
        $intervalgrp[] = &$mform->createElement('select', 'customdays', null, range(0, 60));
        $mform->addGroup($intervalgrp, 'intervalgrp', get_string('refresh', 'dataformview_interval'), array("    $strminutes: ", " $strhours: ", " $strdays: "), false);
        $mform->disabledIf('customminutes', 'param5', 'neq', 'custom');
        $mform->disabledIf('customhours', 'param5', 'neq', 'custom');
        $mform->disabledIf('customdays', 'param5', 'neq', 'custom');

        // Selection type.
        $types = array(
            \mod_dataform_entry_manager::SELECT_FIRST_PAGE => get_string('firstpage', 'dataformview_interval'),
            \mod_dataform_entry_manager::SELECT_LAST_PAGE => get_string('lastpage', 'dataformview_interval'),
            \mod_dataform_entry_manager::SELECT_NEXT_PAGE => get_string('nextpage', 'dataformview_interval'),
            \mod_dataform_entry_manager::SELECT_RANDOM_PAGE => get_string('randompage', 'dataformview_interval'),
            \mod_dataform_entry_manager::SELECT_RANDOM_ENTRIES => get_string('randomentries', 'dataformview_interval'),
        );
        $mform->addElement('select', 'param4', get_string('selection', 'dataformview_interval'), $types);

        // Reset next.
        $mform->addElement('text', 'param8', get_string('resetnext', 'dataformview_interval'), array('size' => 8));
        $mform->setType('param8', PARAM_INT);
        $mform->setDefault('param8', 100);
        $mform->disabledIf('param8', 'param4', 'neq', mod_dataform_entry_manager::SELECT_NEXT_PAGE);
        $mform->addRule('param8', null, 'numeric', null, 'client');
    }

    /**
     *
     */
    public function data_preprocessing(&$data) {
        parent::data_preprocessing($data);
        if (!empty($data->param6)) {
            $customsecs = $data->param6;
            $customdays = floor($customsecs / 86400);
            $daysinsecs = $customdays * 86400;
            $customhours = floor(($customsecs - $daysinsecs) / 3600);
            $hoursinsecs = $customhours * 3600;
            $customminutes = floor(($customsecs - ($daysinsecs + $hoursinsecs)) / 60);
            $data->customdays = $customdays;
            $data->customhours = $customhours;
            $data->customminutes = $customminutes;
        }
    }

    /**
     *
     */
    public function set_data($data) {
        $this->data_preprocessing($data);
        parent::set_data($data);
    }

    /**
     *
     */
    public function get_data() {
        if ($data = parent::get_data()) {
            // Set custom refresh (param6).
            $customrefreshsecs = 0;
            if (!empty($data->customminutes)) {
                $customrefreshsecs += ($data->customminutes * 60);
                unset($data->customminutes);
            }
            if (!empty($data->customhours)) {
                $customrefreshsecs += ($data->customhours * 3600);
                unset($data->customhours);
            }
            if (!empty($data->customdays)) {
                $customrefreshsecs += ($data->customdays * 86400);
                unset($data->customdays);
            }
            $data->param6 = $customrefreshsecs;
        }
        return $data;
    }

}

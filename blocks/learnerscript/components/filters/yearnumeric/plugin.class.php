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
 * LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;

class plugin_yearnumeric extends pluginbase {

    public function init() {
        $this->form = false;
        $this->unique = true;
        $this->fullname = get_string('filteryearnumeric', 'block_learnerscript');
        $this->reporttypes = array('categories', 'sql');
    }

    public function summary($data) {
        return get_string('filteryearnumeric_summary', 'block_learnerscript');
    }

    public function execute($finalelements, $data) {

        $filteryearnumeric = optional_param('filter_yearnumeric', 0, PARAM_INT);
        if (!$filteryearnumeric) {
            return $finalelements;
        }

        if ($this->report->type != 'sql') {
            return array($filteryearnumeric);
        } else {
            if (preg_match("/%%FILTER_YEARNUMERIC:([^%]+)%%/i", $finalelements, $output)) {
                $replace = ' AND ' . $output[1] . ' LIKE \'%' . $filteryearnumeric . '%\'';
                return str_replace('%%FILTER_YEARNUMERIC:' . $output[1] . '%%', $replace, $finalelements);
            }
        }
        return $finalelements;
    }
    public function filter_data($selectoption = true){
        global $DB, $CFG;

        $filteryearnumeric = optional_param('filter_yearnumeric', 0, PARAM_INT);

        $reportclassname = 'block_learnerscript\lsreports\report_' . $this->report->type;
        $reportclass = new $reportclassname($this->report);

        foreach (explode(',', get_string('filteryearnumeric_list', 'block_learnerscript')) as $value) {
            $yearnumeric[$value] = $value;
        }

        if ($this->report->type != 'sql') {
            $components = (new block_learnerscript\local\ls)->cr_unserialize($this->report->components);
            $conditions = $components['conditions'];

            $yearnumericlist = $reportclass->elements_by_conditions($conditions);
        } else {
            $yearnumericlist = array_keys($yearnumeric);
        }

        $yearnumericoptions = array();
        if($selectoption){
            $yearnumericoptions[0] = get_string('filter_all', 'block_learnerscript');
        }
        if (!empty($yearnumericlist)) {
            // Todo: check that keys of yearnumeric array items are available.
            foreach ($yearnumeric as $key => $year) {
                $yearnumericoptions[$key] = $year;
            }
        }
        return $yearnumericoptions;
    }
    public function print_filter(&$mform) {
        
        $yearnumericoptions = $this->filter_data();
        $elestr = get_string('filteryearnumeric', 'block_learnerscript');
        $mform->addElement('select', 'filter_yearnumeric', $elestr, $yearnumericoptions);
        $mform->setType('filter_yearnumeric', PARAM_INT);
    }

}

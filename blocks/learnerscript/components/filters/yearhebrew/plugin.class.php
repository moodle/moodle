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

class plugin_yearhebrew extends pluginbase {

    public function init() {
        $this->form = false;
        $this->unique = true;
        $this->fullname = get_string('filteryearhebrew', 'block_learnerscript');
        $this->reporttypes = array('categories', 'sql');
    }

    public function summary($data) {
        return get_string('filteryearhebrew_summary', 'block_learnerscript');
    }

    public function execute($finalelements, $data) {

        $filteryearhebrew = optional_param('filter_yearhebrew', '', PARAM_RAW);
        if (!$filteryearhebrew) {
            return $finalelements;
        }

        if ($this->report->type != 'sql') {
            return array($filteryearhebrew);
        } else {
            if (preg_match("/%%FILTER_YEARHEBREW:([^%]+)%%/i", $finalelements, $output)) {
                $replace = ' AND ' . $output[1] . ' LIKE \'%' . $filteryearhebrew . '%\'';
                return str_replace('%%FILTER_YEARHEBREW:' . $output[1] . '%%', $replace, $finalelements);
            }
        }
        return $finalelements;
    }
    public function filter_data($selectoption = true){
        global $DB, $CFG;

        $filteryearhebrew = optional_param('filter_yearhebrew', 0, PARAM_RAW);

        $reportclassname = 'block_learnerscript\lsreports\report_' . $this->report->type;
        $reportclass = new $reportclassname($this->report);
        foreach (explode(',', get_string('filteryearhebrew_list', 'block_learnerscript')) as $value) {
            $yearhebrew[$value] = $value;
        }

        if ($this->report->type != 'sql') {
            $components = (new block_learnerscript\local\ls)->cr_unserialize($this->report->components);
            $conditions = $components['conditions'];

            $yearhebrewlist = $reportclass->elements_by_conditions($conditions);
        } else {
            $yearhebrewlist = array_keys($yearhebrew);
        }

        $yearhebrewoptions = array();
        if($selectoption){
            $yearhebrewoptions[0] = get_string('filter_all', 'block_learnerscript');
        }

        if (!empty($yearhebrewlist)) {
            // Todo: check that keys of yearhebrew array items are available.
            foreach ($yearhebrew as $key => $year) {
                $yearhebrewoptions[$key] = $year;
            }
        }
        return $yearhebrewoptions;
    }
    public function print_filter(&$mform) {
       

        $yearhebrewoptions = $this->filter_data();
        $elestr = get_string('filteryearhebrew', 'block_learnerscript');
        $mform->addElement('select', 'filter_yearhebrew', $elestr, $yearhebrewoptions);
        $mform->setType('filter_yearhebrew', PARAM_RAW);
    }

}

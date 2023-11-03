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
 * @date: 2020
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;
use stdClass;

class plugin_cohort extends pluginbase {

    public function init() {
        $this->form = false;
        $this->unique = true; 
        $this->placeholder = true;
        $this->singleselection = true;
        $this->fullname = get_string('filtercohort', 'block_learnerscript');
        $this->reporttypes = array('users', 'cohortusers');
        $this->filtertype = 'custom';
        if (!empty($this->reportclass->basicparams)) {
            foreach ($this->reportclass->basicparams as $basicparam) {
                if ($basicparam['name'] == 'cohort') {
                    $this->filtertype = 'basic';
                }
            }
        }
    }

    public function summary($data) {
        return get_string('filtercohort_summary', 'block_learnerscript');
    }

    public function execute($finalelements, $data, $filters) {

        $filtercohort = isset($filters['filter_cohort']) ? $filters['filter_cohort'] : 0;
        if (!$filtercohort) {
            return $finalelements;
        }

        if ($this->report->type != 'sql') {
            return array($filtercohort);
        } else {
            if (preg_match("/%%FILTER_COHORT:([^%]+)%%/i", $finalelements, $output)) {
                $replace = ' AND ' . $output[1] . ' = ' . $filtercohort;
                return str_replace('%%FILTER_COHORT:' . $output[1] . '%%', $replace, $finalelements);
            }
        }
        return $finalelements;
    }
    public function filter_data($selectoption = true){
        global $DB, $CFG;
        $properties = new stdClass();
        $properties->courseid = SITEID;

        $reportclassname = 'block_learnerscript\lsreports\report_' . $this->report->type;
        $reportclass = new $reportclassname($this->report, $properties);

        if ($this->report->type != 'sql') {
            $components = (new \block_learnerscript\local\ls)->cr_unserialize($this->report->components);
        } else {
            $cohortlist = array_keys($DB->get_records('cohort'));
        }

        $cohortoptions = array();
        if($selectoption){
            $cohortoptions[0] = $this->singleselection ?
                get_string('filter_cohort', 'block_learnerscript') : get_string('select') .' '. get_string('category');
        }

        if (empty($cohortlist)) {
            $cohorts = $DB->get_records_select('cohort', '', array(), '', 'id, name');

            foreach ($cohorts as $c) {
                $cohortoptions[$c->id] = format_string($c->name);
            }
        }
        return $cohortoptions;
    }
    public function selected_filter($selected) {
        $filterdata = $this->filter_data();
        return $filterdata[$selected];
    }
    public function print_filter(&$mform) {
        $cohortoptions = $this->filter_data(); 
        if ((!$this->placeholder || $this->filtertype == 'basic') && COUNT($cohortoptions) > 1) {
            unset($cohortoptions[0]);
        }
        $select = $mform->addElement('select', 'filter_cohort', get_string('cohort', 'block_learnerscript'), $cohortoptions,array('data-select2'=>1));
        $select->setHiddenLabel(true);
        $mform->setType('filter_cohort', PARAM_INT);
    }

}

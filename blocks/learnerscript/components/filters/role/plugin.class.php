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
use stdClass;
class plugin_role extends pluginbase {

    public function init() {
        $this->form = false;
        $this->unique = true;
        $this->singleselection = true;
        $this->fullname = get_string('filterrole', 'block_learnerscript');
        $this->reporttypes = array('categories', 'sql');
    }

    public function summary($data) {
        return get_string('filterrole_summary', 'block_learnerscript');
    }

    public function execute($finalelements, $data) {

        $filterrole = optional_param('filter_role', 0, PARAM_INT);
        if (!$filterrole) {
            return $finalelements;
        }

        if ($this->report->type != 'sql') {
            return array($filterrole);
        } else {
            if (preg_match("/%%FILTER_ROLE:([^%]+)%%/i", $finalelements, $output)) {
                $replace = ' AND ' . $output[1] . ' = ' . $filterrole . ' ';
                return str_replace('%%FILTER_ROLE:' . $output[1] . '%%', $replace, $finalelements);
            }
        }
        return $finalelements;
    }
    public function filter_data($selectoption = true){
        global $DB, $CFG;

        $filterrole = optional_param('filter_role', 0, PARAM_INT);

        $reportclassname = 'block_learnerscript\lsreports\report_' . $this->report->type;
        $properties = new stdClass;
        $reportclass = new $reportclassname($this->report, $properties);

        $systemroles = $DB->get_records('role');
        $roles = array();
        foreach ($systemroles as $role) {
            $roles[$role->id] = $role->shortname;
        }

        if ($this->report->type != 'sql') {
            $components = (new block_learnerscript\local\ls)->cr_unserialize($this->report->components);
            $conditions = $components['conditions'];

            $rolelist = $reportclass->elements_by_conditions($conditions);
        } else {
            $rolelist = $roles;
        }

        $roleoptions = array();
        if($selectoption){
            $roleoptions[0] = $this->singleselection ?
                        get_string('filter_role', 'block_learnerscript') : get_string('select') .' '. get_string('filterrole', 'block_learnerscript');
        }
        if (!empty($rolelist)) {
            // Todo: check that keys of role array items are available.
            foreach ($rolelist as $key => $role) {
                $roleoptions[$key] = $role;
            }
        }
        return $roleoptions;
    }
    public function selected_filter($selected) {
        $filterdata = $this->filter_data();
        return $filterdata[$selected];
    }
    public function print_filter(&$mform) {
        $roleoptions = $this->filter_data();
        $select = $mform->addElement('select', 'filter_role', get_string('filterrole', 'block_learnerscript'), $roleoptions,array('data-select2'=>1));
        $select->setHiddenLabel(true);
        $mform->setType('filter_role', PARAM_INT);
    }

}

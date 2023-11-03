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
use context_course;
class plugin_user extends pluginbase {

    public function init() {
        $this->form = false;
        $this->unique = true;
        $this->fullname = get_string('filteruser', 'block_learnerscript');
        $this->reporttypes = array('sql');
    }

    public function summary($data) {
        return get_string('filteruser_summary', 'block_learnerscript');
    }

    public function execute($finalelements, $data) {
        $filteruser = optional_param('filter_user', 0, PARAM_INT);
        if (!$filteruser) {
            return $finalelements;
        }

        if ($this->report->type != 'sql') {
            return array($filteruser);
        } else {
            if (preg_match("/%%FILTER_COURSEUSER:([^%]+)%%/i", $finalelements, $output)) {
                $replace = ' AND ' . $output[1] . ' = ' . $filteruser;
                return str_replace('%%FILTER_COURSEUSER:' . $output[1] . '%%', $replace, $finalelements);
            }
        }
        return $finalelements;
    }
     public function filter_data($selectoption = true, $request = array()){
         global $DB, $COURSE;

        $reportclassname = 'block_learnerscript\lsreports\report_' . $this->report->type;
        $reportclass = new $reportclassname($this->report);

        if ($this->report->type != 'sql') {
            $components = (new block_learnerscript\local\ls)->cr_unserialize($this->report->components);
            $conditions = $components['conditions'];
            $userlist = $reportclass->elements_by_conditions($conditions);
        } else {
            $coursecontext = context_course::instance($COURSE->id);
            $userlist = array_keys(get_users_by_capability($coursecontext, 'moodle/user:viewdetails'));
        }

        $useroptions = array();
        if($selectoption){
        $useroptions[0] = get_string('filter_user', 'block_learnerscript');
        }

        if (!empty($userlist)) {
            list($usql, $params) = $DB->get_in_or_equal($userlist);
            $users = $DB->get_records_select('user', "id $usql", $params);

            foreach ($users as $c) {
                $useroptions[$c->id] = format_string($c->lastname . ' ' . $c->firstname);
            }
        }
        return $useroptions;
    }
    public function selected_filter($selected, $request) {
        $filterdata = $this->filter_data(true, $request);
        return $filterdata[$selected];
    }
    public function print_filter(&$mform) {
        $useroptions = $this->filter_data();
        $select = $mform->addElement('select', 'filter_user', get_string('user'), $useroptions);
        $select->setHiddenLabel(true);
        $mform->setType('filter_user', PARAM_INT);
    }

}

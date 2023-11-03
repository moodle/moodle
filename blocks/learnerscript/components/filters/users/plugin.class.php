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

class plugin_users extends pluginbase {

    public function init() {
        $this->form = false;
        $this->unique = true;
        $this->singleselection = true;
        $this->placeholder = true;
        $this->maxlength = 0;
        $this->filtertype = 'custom';
        if (!empty($this->reportclass->basicparams)) {
            foreach ($this->reportclass->basicparams as $basicparam) {
                if ($basicparam['name'] == 'users') {
                    $this->filtertype = 'basic';
                }
            }
        }
        $this->fullname = get_string('filterusers', 'block_learnerscript');
        $this->reporttypes = array('sql', 'userassignments', 'usercourses',
            'student_performance', 'uniquelogins', 'userquizzes', 'users',
            'student_overall_performance', 'topic_wise_performance', 'usersscorm');
    }

    public function summary($data) {
        return get_string('filterusers_summary', 'block_learnerscript');
    }

    public function execute($finalelements, $data) {

        $filterusers = optional_param('filter_users', 0, PARAM_RAW);
        if (!$filterusers) {
            return $finalelements;
        }

        if ($this->report->type != 'sql') {
            return array($filterusers);
        } else {
            if (preg_match("/%%FILTER_SYSTEMUSER:([^%]+)%%/i", $finalelements, $output)) {
                $replace = ' AND ' . $output[1] . ' = ' . $filterusers;
                return str_replace('%%FILTER_SYSTEMUSER:' . $output[1] . '%%', $replace,
                    $finalelements);
            }
        }
        return $finalelements;
    }
    public function filter_data($selectoption = true, $request = array()){
        global $DB;
        $filter_users = '';
        $fusers = isset($request['filter_users']) ? $request['filter_users'] : 0;
        $filterusers = optional_param('filter_users', $fusers, PARAM_RAW);
        if (empty($this->reportclass->basicparams)) {
            $usersoptions = array(get_string('filter_user', 'block_learnerscript'));
        } 
        
        $filteruser = $this->reportclass->filters;
        if ($this->reportclass->basicparams) {
            $basicparams = array_column($this->reportclass->basicparams, 'name');
            if (in_array('courses', $basicparams)) {
                $courseoptions = (new \block_learnerscript\local\querylib)->filter_get_courses($this, false, false, false, false, false, false);
                $courseids = array_keys($courseoptions);
                $usercourseid = array_shift($courseids);
            } else {
                $usercourseid = 0;
            }
        } else {
            $usercourseid = 0;
        }
        $usersoptions = (new \block_learnerscript\local\querylib)->filter_get_users($this, $selectoption, false, $filteruser, $usercourseid, $filterusers);
        
        return $usersoptions;
    }
    public function selected_filter($selected, $request = array()) {
        $filterdata = $this->filter_data(false, $request);
        return $filterdata[$selected];
    }
    public function print_filter(&$mform) {
        if ($this->report->type == 'courseprofile' || $this->report->type == 'userprofile') {
            $selectoption = false;
        } else {
            $selectoption = true;
        }
        $request = array_merge($_POST, $_GET);
        $usersoptions = $this->filter_data(true, $request);
        if (!$this->placeholder || $this->filtertype == 'basic' && COUNT($usersoptions) > 1) {
            unset($usersoptions[0]);
        }
        $select = $mform->addElement('select', 'filter_users', null, $usersoptions,
                    array('data-select2-ajax' => true,
                          'data-maximum-selection-length' => $this->maxlength,
                        'data-action' => 'filterusers',
                        'data-3instanceid' => $this->reportclass->config->id));
        if (!$this->singleselection) {
            $select->setMultiple(true);
        }
        if ($this->required) {
            $select->setSelected(current(array_keys($usersoptions)));
        }
        $select->setHiddenLabel(true);
        $mform->setType('filter_users', PARAM_INT);

        $mform->addElement('hidden', 'filter_users_type', $this->filtertype);
        $mform->setType('filter_users_type', PARAM_RAW);
    }
}
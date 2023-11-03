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
 * LearnerScript
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas info solutions
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;

class plugin_courses extends pluginbase {

    public function init() {
        $this->form = false;
        $this->unique = true;
        $this->singleselection = true;
        $this->placeholder = true;
        $this->maxlength = 0;
        $this->filtertype = 'custom';
        if (!empty($this->reportclass->basicparams)) {
            foreach ($this->reportclass->basicparams as $basicparam) {
                if ($basicparam['name'] == 'courses') {
                    $this->filtertype = 'basic';
                }
            }
        }
        $this->fullname = get_string('filtercourses', 'block_learnerscript');
        $this->reporttypes = array('courses', 'sql', 'activitystatus', 'coursesoverview', 'student_performance',
                                    'courseparticipation', 'myassignments', 'userquizzes', 'userassignments', 'myquizs',
                                    'competencycompletion', 'courseaverage', 'scorm_activities_course',
                                    'popularresources', 'resources_accessed','badges','userbadges',
                                    'timespent', 'pageresourcetimespent','gradedactivity', 'assignment', 'quizzes'
                                    , 'usersscorm', 'scorm', 'myscorm', 'coursewisetimespent');
    }

    public function summary($data) {
        return get_string('filtercourses_summary', 'block_learnerscript');
    }

    public function execute($finalelements, $data, $filters) {
        $fcourse = isset($filters['filter_courses']) ? $filters['filter_courses'] : null;
        $filtercourses = optional_param('filter_courses', $fcourse, PARAM_INT);
        if (!$filtercourses) {
            return $finalelements;
        }

        if ($this->report->type != 'sql') {
            return array($filtercourses);
        } else {
            if (preg_match("/%%FILTER_COURSES:([^%]+)%%/i", $finalelements, $output)) {
                $replace = ' AND ' . $output[1] . ' = ' . $filtercourses;
                return str_replace('%%FILTER_COURSES:' . $output[1] . '%%', $replace, $finalelements);
            }
        }
        return $finalelements;
    }
    public function filter_data($selectoption = true, $request = array()){
        global $USER, $DB;
        $filter_courses = '';
        $fcourses = isset($request['filter_courses']) ? $request['filter_courses'] : 0;
        $filtercourses = optional_param('filter_courses', $fcourses, PARAM_RAW);
        if (empty($this->reportclass->basicparams)) {
            $coursesoptions = array(get_string('filter_courses', 'block_learnerscript'));
        } 
        
        $filtercourse = $this->reportclass->filters;
        if ($this->reportclass->basicparams) {
            $basicparams = array_column($this->reportclass->basicparams, 'name');
            if (in_array('users', $basicparams) && in_array('courses', $basicparams) && isset($basicparams[2]) && isset($basicparams[3]) && $basicparams[2] == 'users' && $basicparams[3] != 'courses') {
                $useroptions = (new \block_learnerscript\local\querylib)->filter_get_users($this, false, false, false, false, false);
                $userids = array_keys($useroptions);
                if (empty($request['filter_users'])) {
                    $courseuserid = array_shift($userids);
                } else {
                    $courseuserid = $request['filter_users'];
                }
            } else {
                $courseuserid = 0;
            }
        } else {
            $courseuserid = 0;
        }
        $courseoptions = (new \block_learnerscript\local\querylib)->filter_get_courses($this, $selectoption, false, $filtercourse, false, $courseuserid, $filtercourses);  
        
        return $courseoptions;
    }

    public function selected_filter($selected, $request = array()) {
        $filterdata = $this->filter_data(false, $request);
        return $filterdata[$selected];
    }
    public function print_filter(&$mform) {
        global $DB, $CFG, $USER;
        // $filtercourses = optional_param('filter_courses', 0, PARAM_INT);
        if ($this->report->type == 'courseprofile' || $this->report->type == 'userprofile') {
            $selectoption = false;
        } else {
            $selectoption = true;
        }
        $request = array_merge($_POST, $_GET);
        $courseoptions = $this->filter_data(true, $request);
        if ((!$this->placeholder || $this->filtertype == 'basic') && COUNT($courseoptions) > 1) {
            unset($courseoptions[0]);
        }

        $select = $mform->addElement('select', 'filter_courses', null,
            $courseoptions,
            array('data-select2-ajax' => true,
                  'data-maximum-selection-length' => $this->maxlength,
                  'data-action' => 'filtercourses',
                  'data-4instanceid' => $this->reportclass->config->id));
        $select->setHiddenLabel(true);
        if (!$this->singleselection) {
            $select->setMultiple(true);
        }
        if ($this->required) {
            $select->setSelected(current(array_keys($courseoptions)));
        }
        $mform->setType('filter_courses', PARAM_INT);

        $mform->addElement('hidden', 'filter_courses_type', $this->filtertype); 
        $mform->setType('filter_courses_type', PARAM_RAW);

    }
}
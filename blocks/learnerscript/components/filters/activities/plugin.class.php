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

class plugin_activities extends pluginbase {

	public function init() {
		$this->form = false;
		$this->unique = true;
		$this->singleselection = true; 
		$this->placeholder = true;
		$this->fullname = get_string('filteractivities', 'block_learnerscript');
		$this->filtertype = 'custom';
        if (!empty($this->reportclass->basicparams)) {
            foreach ($this->reportclass->basicparams as $basicparam) {
                if ($basicparam['name'] == 'activities') {
                    $this->filtertype = 'basic';
                }
            }
        }
		$this->reporttypes = array('listofactivities', 'useractivities', 'student_performance', 'gradedactivity');
	}

	public function summary($data) {
		return get_string('filteractivities_summary', 'block_learnerscript');
	}

	public function execute($finalelements, $data, $filters) {

		$filter_activities = isset($filters['filter_activities']) ? $filters['filter_activities'] : 0;
		if (!$filter_activities) {
			return $finalelements;
		}

		if ($this->report->type != 'sql') {
			return array($filter_activities);
		}
		return $finalelements;
	}
	public function filter_data($selectoption = true){
		global $DB, $CFG;
		require_once $CFG->dirroot . '/course/lib.php';
		$courseid = optional_param('courseid', SITEID, PARAM_INT);
		if ($courseid <= SITEID) { 
			/*$basicparams = array_column($this->reportclass->basicparams, 'name');
			if (in_array('courses', $basicparams)) {
	            $courseoptions = (new \block_learnerscript\local\querylib)->filter_get_courses($this, false, false, false, false, false, false);
	            $courseids = array_keys($courseoptions);
	            $activitycourseid = array_shift($courseids);
	        } else {
	            $activitycourseid = false;
	        }
	        $courseid = optional_param('filter_courses', $activitycourseid, PARAM_RAW);*/
			$courseid = optional_param('filter_courses', SITEID, PARAM_RAW);
		}
		$activities = array();
		if($selectoption){
			$activities[0] = $this->singleselection ?
						get_string('select_activity', 'block_learnerscript') : get_string('select') .' '. get_string('activities', 'block_learnerscript');
		}
		if (!empty($courses) && isset($courses)) {
				$activitieslist = get_array_of_activities($courseid);
				foreach ($activitieslist as $activity) {
					$activities[$activity->cm] = $activity->name;
				}
		} else {
			$activitieslist = get_array_of_activities($courseid);
			foreach ($activitieslist as $activity) {
				$activities[$activity->cm] = $activity->name;
			}
		}
		return $activities;
	}
	public function selected_filter($selected) {
        $filterdata = $this->filter_data();
        return $filterdata[$selected];
    }
	public function print_filter(&$mform) {
		$activities = $this->filter_data();
		if (!$this->placeholder || $this->filtertype == 'basic' && COUNT($activities) > 1) {
            unset($activities[0]);
        }
		$select = $mform->addElement('select', 'filter_activities', get_string('activities', 'block_learnerscript'), $activities,array('data-select2'=>1));
		$select->setHiddenLabel(true);
		$mform->setType('filter_activities', PARAM_INT);
	}

}

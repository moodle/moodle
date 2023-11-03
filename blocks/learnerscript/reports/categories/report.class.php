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
use block_learnerscript\local\reportbase;
defined('MOODLE_INTERNAL') || die();
class report_categories extends reportbase {

    public function __construct($report, $reportproperties) {
        global $USER;
        parent::__construct($report,$reportproperties);
        $this->components = array('columns', 'conditions', 'filters', 'permissions', 'calcs', 'plot');
        $this->columns = array('categoryfield' => ['categoryfield']);
        $this->courselevel = true;
        $this->parent = false;
        $this->defaultcolumn = 'id';
        $this->searchable = array("name", "description", "parent");
        $this->excludedroles = array("'student'");        
    }
    function count() {
        $this->sql = "SELECT COUNT(id)";
    }

    function select() {
        $this->sql = "SELECT * ";
    }
    
    function from() {
        $this->sql .=" FROM {course_categories}";
    }

    function filters() {}

    function where() {
        global $DB, $USER;
        $this->sql .=" WHERE 1 = 1 AND visible = :visible ";
        $this->params['visible'] = 1;
        if (!is_siteadmin()) {
            $categories = $DB->get_fieldset_sql("SELECT DISTINCT ct.instanceid
                FROM mdl_context as ct
                JOIN mdl_role_assignments as ra ON ra.contextid = ct.id
                WHERE ct.contextlevel = 40 AND ra.userid =". $USER->id );
            $usercategories = implode(',',$categories);
            if(!empty($usercategories)) {
                $this->sql .= " AND id IN ($usercategories) ";
            } else {
                $this->sql .= " AND id = 0 ";
            }
        }
        if ($this->conditionsenabled) {
            $conditions = implode(',', $this->conditionfinalelements);
            if (empty($conditions)) {
                return array(array(), 0);
            }
            $this->params['lsconditions'] = $conditions;
            $this->sql .= " AND id IN ($conditions)";
        }
        if ($this->ls_startdate > 0 && $this->ls_enddate) {
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
            $this->sql .= " AND timemodified BETWEEN :ls_fstartdate AND :ls_fenddate ";
        }
        parent::where();
    }

    function search() {
        global $DB;
        if (isset($this->search) && $this->search) {
            $this->searchable = array("name", "description");
            $statsql = array();
            foreach ($this->searchable as $key => $value) {
                $statsql[] =$DB->sql_like($value, "'%" . $this->search . "%'",$casesensitive = false,$accentsensitive = true, $notlike = false);
            }
            $fields = implode(" OR ", $statsql);           
            $this->sql .= " AND ($fields) ";
        }
    }

    function groupby() {
        
    }

    public function get_rows($elements, $sqlorder = '') {
        return $elements;
    }

}

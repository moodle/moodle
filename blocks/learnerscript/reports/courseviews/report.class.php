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

/** LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @subpackage learnerscript
 * @author: sreekanth<sreekanth@eabyas.in>
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\querylib;
use block_learnerscript\local\reportbase;
use block_learnerscript\report;
defined('MOODLE_INTERNAL') || die();
class report_courseviews extends reportbase implements report {
    /**
     * [__construct description]
     * @param object $report Report object
     * @param object $reportproperties Report properties object
     */
    public function __construct($report, $reportproperties) {
        global $USER;
        parent::__construct($report, $reportproperties);
        $this->components = array('columns', 'filters', 'permissions', 'calcs', 'plot');
        $this->columns = array('courseviews' => array('learner', 'views'));
        $this->courselevel = true;
        $this->basicparams = [['name' => 'courses']];
        $this->parent = false;
        $this->orderable = array('learner', 'views');
        $this->defaultcolumn = 'lsl.userid';
        $this->excludedroles = array("'student'");
    }
    public function init() {
        if (!isset($this->params['filter_courses'])) {
            $this->initial_basicparams('courses');
            $coursedata = array_keys($this->filterdata);
            $this->params['filter_courses'] = array_shift($coursedata);
        } 
        if (!$this->scheduling && isset($this->basicparams) && !empty($this->basicparams)) {
            $basicparams = array_column($this->basicparams, 'name');
            foreach ($basicparams as $basicparam) {
                if (empty($this->params['filter_' . $basicparam])) {
                    return false;
                }
            }
        }
        $this->courseid = $this->params['filter_courses'];
    }
    public function count() {
        $this->sql   = "SELECT COUNT(DISTINCT lsl.userid) ";
    }

    public function select() {
        $this->sql = "SELECT DISTINCT lsl.userid AS userid, COUNT(lsl.id) AS views";
        if (in_array('learner', $this->selectedcolumns)) {
            $this->sql .= ", CONCAT(u.firstname, ' ', u.lastname) AS learner";
        }
    }

    public function from() {
        $this->sql .= " FROM {logstore_standard_log} lsl";
    }

    public function joins() {
        $this->sql .= " JOIN {user} u ON u.id = lsl.userid";
       parent::joins();  
    }

    public function where() {
        $learnersql  = (new querylib)->get_learners('', $this->courseid);
        $this->sql .=" WHERE lsl.crud = 'r' AND lsl.userid > 2 AND u.confirmed = 1 
                         AND u.deleted = :deleted AND lsl.courseid = :courseid
                         ";
        if($learnersql){
            $this->sql .=" AND lsl.userid IN ($learnersql)";
        }
        $this->params['deleted'] =0;
        $this->params['courseid'] = $this->courseid;
        if ($this->ls_startdate > 0 && $this->ls_enddate) {
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
            $this->sql .= " AND lsl.timecreated BETWEEN :ls_fstartdate AND :ls_fenddate ";
        }
        parent::where();
    }
    
    public function search() {
        global $DB;
        if (isset($this->search) && $this->search) {
            $this->searchable = array("CONCAT(u.firstname, ' ', u.lastname)");
            $statsql = array();
            foreach ($this->searchable as $key => $value) {
                $statsql[] =$DB->sql_like($value, "'%" . $this->search . "%'",$casesensitive = false,$accentsensitive = true, $notlike = false);
            }
            $fields = implode(" OR ", $statsql);      
            $this->sql .= " AND ($fields) ";
        }
    }

    public function filters() {
    }
    public function groupby() {
        $this->sql .= " GROUP BY lsl.userid, u.firstname, u.lastname";
    }
    /**
     * @param  array $activites Activites
     * @return array $reportarray Activities information
     */
    public function get_rows($activites) {
        return $activites;
    }
}

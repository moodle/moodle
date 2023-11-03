<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License AS published by
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
 * @author: <jahnavi@eabyas.in>
 * @date: 2020
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\querylib;
use block_learnerscript\local\reportbase;
use block_learnerscript\local\ls as ls;
use block_learnerscript\report;

class report_competency extends reportbase implements report {
    /**
     * [__construct description]
     * @param [type] $report           [description]
     * @param [type] $reportproperties [description]
     */
    public function __construct($report, $reportproperties) {
        parent::__construct($report);
        $this->parent = false;
        $this->courselevel = true;
        $this->components = array('columns', 'filters', 'permissions', 'plot');
        $columns = ['competency', 'framework', 'course'];
        $this->columns = ['competencycolumns' => $columns];
        $this->orderable = array('competency', 'completedusers');
        $this->defaultcolumn = 'com.id';
        $this->excludedroles = array("'student'");
    }
    function init() {
        global $DB;
        if(!isset($this->params['filter_courses'])){
            $this->initial_basicparams('courses');
            $coursefilter = array_keys($this->filterdata);
            $this->params['filter_courses'] = array_shift($coursefilter);
        }
        if (!$this->scheduling && isset($this->basicparams) && !empty($this->basicparams)) {
            $basicparams = array_column($this->basicparams, 'name');
            foreach ($basicparams AS $basicparam) {
                if (empty($this->params['filter_' . $basicparam])) {
                    return false;
                }
            }
        }
    }

    function count() {
        $this->sql = "SELECT COUNT(DISTINCT com.id) ";
    }

    function select() {
        $courseid = $this->params['filter_courses'];
        $this->sql = "SELECT DISTINCT com.id, com.shortname AS competency, comf.shortname AS framework  ";
        parent::select();
    }

    function from() {
        $this->sql .= " FROM {competency} com ";
    }

    function joins() {
        $this->sql .= " JOIN {competency_framework} comf ON comf.id = com.competencyframeworkid 
";
        parent::joins();
    }

    function where() {
        $userid = isset($this->params['filter_users']) ? $this->params['filter_users'] : array();
        $this->sql .= " WHERE 1 = 1 ";

        if ((!is_siteadmin() || $this->scheduling) && !(new ls)->is_manager()) {
            if ($this->rolewisecourses != '') {
                $this->sql .= " AND ccom.courseid IN ($this->rolewisecourses) ";
            }
        }
        parent::where();
    }

    function search() {
        global $DB;
        if (isset($this->search) && $this->search) {
            $this->searchable =array("com.shortname", "comf.shortname");
            $statsql = array();
            foreach ($this->searchable as $key => $value) {
                $statsql[] =$DB->sql_like($value, "'%" . $this->search . "%'",$casesensitive = false,$accentsensitive = true, $notlike = false);
            }
            $fields = implode(" OR ", $statsql);          
            $this->sql .= " AND ($fields) ";
        }
    }

    function filters() {
        // if ($this->ls_startdate > 0 && $this->ls_enddate) {
        //     $this->sql .= " AND ra.timemodified BETWEEN $this->ls_startdate AND $this->ls_enddate ";
        // }
    }

    function groupby() {
        $this->sql .= " GROUP BY com.id, com.shortname, comf.shortname ";
    }
    /**
     * [get_rows description]
     * @param  array  $users [description]
     * @return [type]        [description]
     */
    public function get_rows($users = array()) {
        return $users;
    }
}

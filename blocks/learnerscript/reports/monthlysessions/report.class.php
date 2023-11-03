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
 * @author: jahnavi<jahnavi@eabyas.com>
 * @date: 2022
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\querylib;
use block_learnerscript\local\reportbase;
use block_learnerscript\local\ls as ls;
use block_learnerscript\report;

class report_monthlysessions extends reportbase implements report {
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
        $columns = ['month', 'sessionscount', 'timespent'];
        $this->columns = ['monthlysessions' => $columns];
        $this->filters = array('users');
        $this->orderable = array('');
        $this->defaultcolumn = 't1.month';
    }
    function init() {
        global $DB;
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
        global $DB;
        // if ($this->ls_startdate == 0) {
            $start    = new \DateTime('first day of January');
            $end      = new \DateTime('last day of December');
        // } else {
        //     $firstdate = $DB->get_field_sql("SELECT $this->ls_startdate AS startdate");
        //     $start = userdate($firstdate, '%Y-%m-%d');
        //     $lastdate = $DB->get_field_sql("SELECT $this->ls_enddate AS enddate");
        //     $end = userdate($lastdate, '%Y-%m-%d');
        // }
        $interval = \DateInterval::createFromDateString('1 month');
        $period   = new \DatePeriod($start, $interval, $end);
        $i = 0;
        foreach ($period as $dt) {
            $i++;
            $months = $dt->format("F") . "<br>\n";
            $monthslist[] = $months;
        }
        $totalmonths = COUNT($monthslist);
        $this->sql = "SELECT $totalmonths AS totalmonths ";
    }

    function select() {
        global $DB, $CFG;
        $start    = new \DateTime('first day of January');
        $end      = new \DateTime('last day of December');
        $interval = \DateInterval::createFromDateString('1 month');
        $period   = new \DatePeriod($start, $interval, $end);
        $i = 0;
        $concatsql = " ";
        if (!empty($this->params['filter_users'])) {
            $userid = $this->params['filter_users'];
            $concatsql .= " AND lsl.userid IN ($userid)"; 
        }
        foreach ($period as $dt) {
            $monthorder = $dt->format("m");
            $months = $dt->format("F");
            $monthsql = $dt->format('m-Y');
            $monthslist[] = $months;
            $sessionsql = "SELECT COUNT(DISTINCT lsl.id) AS sessionscount 
                                FROM mdl_logstore_standard_log AS lsl 
                                WHERE 1 = 1 $concatsql AND lsl.target = 'user' AND lsl.crud = 'r' AND
                                lsl.other LIKE '%sessionid%'";
            if ($CFG->dbtype == 'sqlsrv') {
                $sessionsql .= " AND FORMAT(DATEADD(s, lsl.timecreated, '1970-01-01 00:00:00'), 'MM-yyyy') = '" .$monthsql. "'";
            } else if($CFG->dbtype == 'pgsql') {
                $sessionsql .= " AND to_char(to_timestamp(lsl.timecreated), 'mm-YYYY') = '" .$monthsql. "'";
            } else {
                $sessionsql .= " AND FROM_UNIXTIME(lsl.timecreated, '%m-%Y') = '" .$monthsql. "'";
            }
            $sessionscount = $DB->get_field_sql($sessionsql);

            $timespentsql = "SELECT DISTINCT lsl.id, lsl.timecreated  
                                FROM mdl_logstore_standard_log AS lsl 
                                WHERE 1 = 1 $concatsql AND lsl.target = 'user' AND lsl.crud = 'r' AND
                                lsl.action LIKE 'loggedout' ";
            if ($CFG->dbtype == 'sqlsrv') {
                $timespentsql .= " AND FORMAT(DATEADD(s, lsl.timecreated, '1970-01-01 00:00:00'), 'MM-yyyy') = '" .$monthsql. "'";
            } else if($CFG->dbtype == 'pgsql') {
                $timespentsql .= " AND to_char(to_timestamp(lsl.timecreated), 'mm-YYYY') = '" .$monthsql. "'";
            } else {
                $timespentsql .= " AND FROM_UNIXTIME(lsl.timecreated, '%m-%Y') = '" .$monthsql. "'";
            }
            $timespentsql .= " ORDER BY id DESC ";
            $timespentdata = $DB->get_records_sql($timespentsql);
            if (!empty($timespentdata)) {
                $timediff = array();                
                foreach ($timespentdata as $tsql) {
                    if ($CFG->dbtype == 'sqlsrv') {
                        $logintimesql = "SELECT TOP 1 lsl.timecreated FROM mdl_logstore_standard_log lsl WHERE 1 = 1 $concatsql AND lsl.target = 'user' AND lsl.crud = 'r' AND
                                        lsl.action LIKE 'loggedin' AND lsl.timecreated < $tsql->timecreated ";
                    } else {
                        $logintimesql = "SELECT lsl.timecreated FROM mdl_logstore_standard_log lsl WHERE 1 = 1 $concatsql AND lsl.target = 'user' AND lsl.crud = 'r' AND
                                    lsl.action LIKE 'loggedin' AND lsl.timecreated < $tsql->timecreated ";
                    }
                    if ($CFG->dbtype == 'sqlsrv') {
                        $logintimesql .= " AND FORMAT(DATEADD(s, lsl.timecreated, '1970-01-01 00:00:00'), 'MM-yyyy') = '" .$monthsql. "' ORDER BY id DESC ";
                    } else if($CFG->dbtype == 'pgsql') {
                        $logintimesql .= " AND to_char(to_timestamp(lsl.timecreated), 'mm-YYYY') = '" .$monthsql. "' ORDER BY id DESC LIMIT 1 ";
                    } else {
                        $logintimesql .= " AND FROM_UNIXTIME(lsl.timecreated, '%m-%Y') = '" .$monthsql. "' ORDER BY id DESC LIMIT 1 ";
                    } 
                    $logintime = $DB->get_field_sql($logintimesql);
                    if (!empty($logintime)) {
                        $timediff[] = $tsql->timecreated - $logintime;
                    }
                }
                $totaltimespent = array_sum($timediff);
            } else {
                $totaltimespent = 0;
            }
            
            if ($i == 0) {
                $query .= "SELECT '".$months."' AS month, $sessionscount AS sessionscount, $totaltimespent AS timespent, $monthorder AS monthorder ";
            } else {
                $query .= " UNION SELECT '".$months."' AS month, $sessionscount AS sessionscount, $totaltimespent AS timespent, $monthorder AS monthorder ";
            }
            $i++;
        }
        $this->sql = " SELECT t1.month, t1.sessionscount, t1.timespent, t1.monthorder FROM ($query) AS t1 
                        ORDER BY t1.monthorder ASC";
        parent::select();
    }

    function from() { 
        $this->sql .= " ";
    }

    function joins() {
        $this->sql .= " ";
        parent::joins();
    }

    function where() {
        $this->sql .= " ";
        parent::where();
    }

    function search() {
    }

    function filters() {
    }
    function groupby() {
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

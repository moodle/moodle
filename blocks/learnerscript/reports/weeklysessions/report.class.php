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

class report_weeklysessions extends reportbase implements report {
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
        $columns = ['weekday', 'sessionscount', 'timespent'];
        $this->columns = ['weeklysessions' => $columns];
        $this->filters = array('users');        
        $this->orderable = array('weekday', 'sessionscount', 'timespent');
        $this->defaultcolumn = 't1.weekday';
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
       $start    = new \DateTime('monday this week');
        $end      = new \DateTime('next monday');
        $interval = new \DateInterval('P1D');
        $period   = new \DatePeriod($start, $interval, $end);
        foreach ($period as $dt) {
            $i++;
            $weekdays = $dt->format("l") . "<br>\n";
            $dayslist[] = $weekdays;
        }
        $totaldays = COUNT($dayslist);
        $this->sql = "SELECT $totaldays AS totaldays ";
    }

    function select() {
        global $DB, $CFG;
        $start    = new \DateTime('monday this week');
        $end      = new \DateTime('next monday');
        $interval = new \DateInterval('P1D');
        $period   = new \DatePeriod($start, $interval, $end);
        $i = 0;
        $concatsql = " ";        
        if (!empty($this->params['filter_users'])) {
            $userid = $this->params['filter_users'];
            $concatsql .= " AND lsl.userid IN ($userid)"; 
        }
        foreach ($period as $dt) {
            $weekdays = $dt->format("l");
            $weekday_number = $dt->format("w");
            $weekdaysql = $dt->format('d-m-Y');
            $weekdayslist[] = $weekdays;
            $sessionsql = "SELECT COUNT(DISTINCT lsl.id) AS sessionscount 
                                FROM mdl_logstore_standard_log AS lsl 
                                WHERE 1 = 1 $concatsql AND lsl.target = 'user' AND lsl.crud = 'r' AND
                                lsl.other LIKE '%sessionid%'";
            if ($CFG->dbtype == 'sqlsrv') {
                $sessionsql .= " AND FORMAT(DATEADD(s, lsl.timecreated, '1970-01-01 00:00:00'), 'dd-MM-yyyy') = '" .$weekdaysql. "'";
            } else if($CFG->dbtype == 'pgsql') {
                $sessionsql .= " AND to_char(to_timestamp(lsl.timecreated), 'dd-mm-YYYY') = '" .$weekdaysql. "'";
            } else {
                $sessionsql .= " AND FROM_UNIXTIME(lsl.timecreated, '%d-%m-%Y') = '" .$weekdaysql. "'";
            }
            $sessionscount = $DB->get_field_sql($sessionsql);

            $timespentsql = "SELECT DISTINCT lsl.id, lsl.timecreated  
                                FROM mdl_logstore_standard_log AS lsl 
                                WHERE 1 = 1 $concatsql AND lsl.target = 'user' AND lsl.crud = 'r' AND
                                lsl.action LIKE 'loggedout' ";
            if ($CFG->dbtype == 'sqlsrv') {
                $timespentsql .= " AND FORMAT(DATEADD(s, lsl.timecreated, '1970-01-01 00:00:00'), 'dd-MM-yyyy') = '" .$weekdaysql. "'";
            } else if($CFG->dbtype == 'pgsql') {
                $timespentsql .= " AND to_char(to_timestamp(lsl.timecreated), 'dd-mm-YYYY') = '" .$weekdaysql. "'";
            } else {
                $timespentsql .= " AND FROM_UNIXTIME(lsl.timecreated, '%d-%m-%Y') = '" .$weekdaysql. "'";
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
                        $logintimesql .= " AND FORMAT(DATEADD(s, lsl.timecreated, '1970-01-01 00:00:00'), 'dd-MM-yyyy') = '" .$weekdaysql. "' ORDER BY id DESC ";
                    } else if($CFG->dbtype == 'pgsql') {
                        $logintimesql .= " AND to_char(to_timestamp(lsl.timecreated), 'dd-mm-YYYY') = '" .$weekdaysql. "' ORDER BY id DESC LIMIT 1 ";
                    } else {
                        $logintimesql .= " AND FROM_UNIXTIME(lsl.timecreated, '%d-%m-%Y') = '" .$weekdaysql. "' ORDER BY id DESC LIMIT 1 ";
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
                $query .= "SELECT '".$weekdays."' AS weekday, $sessionscount AS sessionscount, $totaltimespent AS timespent, $weekday_number AS weekday_number ";
            } else {
                $query .= " UNION SELECT '".$weekdays."' AS weekday, $sessionscount AS sessionscount, $totaltimespent AS timespent, $weekday_number AS weekday_number ";
            }
            $i++;
        }
        $this->sql = " SELECT t1.weekday, t1.sessionscount, t1.timespent FROM ($query) AS t1 ORDER BY t1.weekday_number ASC";
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

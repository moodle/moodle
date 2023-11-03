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

class report_dailysessions extends reportbase implements report {
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
        $columns = ['date', 'sessionscount', 'timespent'];
        $this->columns = ['dailysessionscolumns' => $columns];
        $this->filters = array('users');
        $this->orderable = array('date', 'sessionscount', 'timespent');
        $this->defaultcolumn = 't1.date';
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
        $start    = new \DateTime('first day of this month');
        $end      = new \DateTime('last day of this month');
        $lastday = $end->modify('+1 day');
        $interval = \DateInterval::createFromDateString('1 day');
        $period   = new \DatePeriod($start, $interval, $lastday);
        $i = 0;
        foreach ($period as $dt) {
            $i++;
            $days = $dt->format("j") . "<br>\n";
            $dayslist[] = $days;
        }
        $totaldays = COUNT($dayslist);
        $this->sql = "SELECT $totaldays AS totaldays ";
    }

    function select() {
        global $DB, $CFG;
        $start    = new \DateTime('first day of this month');
        $end      = new \DateTime('last day of this month');
        $lastday = $end->modify('+1 day');
        $interval =  \DateInterval::createFromDateString('1 day');
        $period   = new \DatePeriod($start, $interval, $lastday);
        $i = 0;
        $concatsql = " ";
        if (!empty($this->params['filter_users'])) {
            $userid = $this->params['filter_users'];
            $concatsql .= " AND lsl.userid IN ($userid)"; 
        }
        foreach ($period as $dt) {
            $orderbyday = $dt->format('d-m-Y');
            $days = $dt->format('jS M Y');
            $daysql = $dt->format('d-m-Y');
            $dayslist[] = $days;
            $sessionsql = "SELECT COUNT(DISTINCT lsl.id) AS sessionscount 
                                FROM mdl_logstore_standard_log AS lsl 
                                WHERE 1 = 1 $concatsql AND lsl.target = 'user' AND lsl.crud = 'r' AND
                                lsl.other LIKE '%sessionid%'";
            if ($CFG->dbtype == 'sqlsrv') {
                $sessionsql .= " AND CONVERT(VARCHAR, DATEADD(s, lsl.timecreated, '1970-01-01 00:00:00'), 105) = '" .$daysql. "'";
            } else if($CFG->dbtype == 'pgsql') {
                $sessionsql .= " AND to_char(to_timestamp(lsl.timecreated), 'dd-mm-YYYY') = '" .$daysql. "'";
            } else {
                $sessionsql .= " AND FROM_UNIXTIME(lsl.timecreated, '%d-%m-%Y') = '" .$daysql. "'";
            }
            $sessionscount = $DB->get_field_sql($sessionsql);

            $timespentsql = "SELECT DISTINCT lsl.id, lsl.timecreated  
                                FROM mdl_logstore_standard_log AS lsl 
                                WHERE 1 = 1 $concatsql AND lsl.target = 'user' AND lsl.crud = 'r' AND
                                lsl.action LIKE 'loggedout' ";
            if ($CFG->dbtype == 'sqlsrv') {
                $timespentsql .= " AND CONVERT(VARCHAR, DATEADD(s, lsl.timecreated, '1970-01-01 00:00:00'), 105) = '" .$daysql. "'";
            } else if($CFG->dbtype == 'pgsql') {
                $timespentsql .= " AND to_char(to_timestamp(lsl.timecreated), 'dd-mm-YYYY') = '" .$daysql. "'";
            } else {
                $timespentsql .= " AND FROM_UNIXTIME(lsl.timecreated, '%d-%m-%Y') = '" .$daysql. "'";
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
                        $logintimesql .= " AND CONVERT(VARCHAR, DATEADD(s, lsl.timecreated, '1970-01-01 00:00:00'), 105) = '" .$daysql. "' ORDER BY id DESC ";
                    } else if($CFG->dbtype == 'pgsql') {
                        $logintimesql .= " AND to_char(to_timestamp(lsl.timecreated), 'dd-mm-YYYY') = '" .$daysql. "' ORDER BY id DESC LIMIT 1 ";
                    } else {
                        $logintimesql .= " AND FROM_UNIXTIME(lsl.timecreated, '%d-%m-%Y') = '" .$daysql. "' ORDER BY id DESC LIMIT 1 ";                        
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
                $query .= "SELECT '".$days."' AS date, $sessionscount AS sessionscount, $totaltimespent AS timespent, $orderbyday AS orderbyday ";
            } else {
                $query .= " UNION SELECT '".$days."' AS day, $sessionscount AS sessionscount, $totaltimespent AS timespent, $orderbyday AS orderbyday ";
            }
            $i++;
        }
        $this->sql = " SELECT t1.date, t1.sessionscount, t1.timespent, t1.orderbyday FROM ($query) AS t1 
                        ORDER BY t1.orderbyday ASC";
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

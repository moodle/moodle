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
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\ls as ls;
use block_learnerscript\local\reportbase;
use stdClass;
use html_table;

class report_statistics extends reportbase {

	function init() {
		$this->ls_startdate=0;
        $this->ls_enddate = time();
	}
	public function __construct($report, $reportproperties) {
		parent::__construct($report);
		$this->components = array('customsql', 'filters', 'permissions', 'calcs', 'plot');
		$this->parent = true;
	}
	function prepare_sql($sql) {
		global $DB, $USER, $CFG, $COURSE, $SESSION;
		$sql = str_replace('%%LS_STARTDATE%%', $this->ls_startdate, $sql);
		$sql = str_replace('%%LS_ENDDATE%%', $this->ls_enddate, $sql);
		$sql = str_replace('%%LS_ROLE%%', $this->role, $sql);

		// Enable debug mode from SQL query.
		$this->config->debug = (strpos($sql, '%%DEBUG%%') !== false) ? true : false;
		$sessiontimeout = $DB->get_field('config', 'value', array('name' => 'sessiontimeout'));
	
		// Pass special custom undefined variable as filter.
		// Security warning !!! can be used for sql injection.
		// Use %%FILTER_VAR%% in your sql code with caution.
		$filter_var = optional_param('filter_var', '', PARAM_RAW);
		if (!empty($filter_var)) {
			$sql = str_replace('%%FILTER_VAR%%', $filter_var, $sql);
		}

		$sql = str_replace('%%SESSIONTIMEOUT%%', $sessiontimeout, $sql);
		$sql = str_replace('%%USERID%%', $this->userid, $sql);
		$sql = str_replace('%%COURSEID%%', $this->courseid, $sql);
		$sql = str_replace('%%CATEGORYID%%', $COURSE->category, $sql); 

		// Current timestamp
		$date = new \DateTime();
		$timestamp = $date->getTimestamp(); 
		$sql = str_replace('%%UNIXTIME%%', $timestamp, $sql);

		//TOP & LIMIT
		if ($CFG->dbtype == 'sqlsrv') {
			$sql = str_replace('%%TOP%%', 'TOP 1', $sql);
		} else {
			$sql = str_replace('%%LIMIT%%', 'LIMIT 1', $sql);
		}

		if (($this->courseid != SITEID) && preg_match("/%%LS_COURSEID:([^%]+)%%/i", $sql, $output)) { 
			$replace = ' AND ' . $output[1] . ' = ' . $this->courseid; 
			$sql = str_replace('%%LS_COURSEID:' . $output[1] . '%%', $replace, $sql); 
		}

		if (!is_siteadmin() && $_SESSION['role'] != 'manager') {
	        if (preg_match("/%%DASHBOARDROLE:([^%]+)%%/i", $sql, $output)) {
	        	$currentrole = "'".$_SESSION['role']."'";
	            $replace = ' AND ' . $output[1] . ' =  ' . $currentrole . ' ';
	            $sql = str_replace('%%DASHBOARDROLE:' . $output[1] . '%%', $replace, $sql);
	        }
	    }

		if (preg_match("/%%FILTER_COURSES:([^%]+)%%/i", $sql, $output) && $this->courseid>1) {
			$replace = ' AND ' . $output[1] . ' = ' . $this->courseid;
			$sql = str_replace('%%FILTER_COURSES:' . $output[1] . '%%', $replace, $sql);
		}

		//Activities list
		$modules = $DB->get_fieldset_select('modules', 'name', '', array('visible' => 1));
        foreach ($modules as $modulename) {
            $aliases[] = $modulename;
            $activitylist[] = $modulename.'.name';
            $fields1[] = "COALESCE($modulename.name,'')";
        }
        $activities = $DB->sql_concat(...$fields1); 
        $sql = str_replace('%%ACTIVITIESLIST%%', $activities, $sql); 
        foreach ($aliases as $alias) {
            $activitiesquery .= " LEFT JOIN {".$alias."} AS $alias ON $alias.id = cm.instance AND m.name = '$alias'";
        }
        $sql = str_replace('%%ACTIVITIESQUERY%%', $activitiesquery, $sql);
        $activity = implode(',', $activitylist);
        $sql = str_replace('%%ACTIVITIES%%', $activity, $sql);

		// See http://en.wikipedia.org/wiki/Year_2038_problem
		$sql = str_replace(array('%%STARTTIME%%', '%%ENDTIME%%'), array('0', '2145938400'), $sql);
		$sql = str_replace('%%WWWROOT%%', $CFG->wwwroot, $sql);
		$sql = preg_replace('/%{2}[^%]+%{2}/i', '', $sql);

		$sql = str_replace('?', '[[QUESTIONMARK]]', $sql);

		return $sql;
	}

	function execute_query($sql) {
		global $remoteDB, $DB, $CFG;

		$sql = preg_replace('/\bprefix_(?=\w+)/i', $CFG->prefix, $sql);

		// Use a custom $DB (and not current system's $DB)
		// todo: major security issue
		// $remoteDBhost = get_config('block_learnerscript', 'dbhost');
		// if (empty($remoteDBhost)) {
		// 	$remoteDBhost = $CFG->dbhost;
		// }
		// $remoteDBname = get_config('block_learnerscript', 'dbname');
		// if (empty($remoteDBname)) {
		// 	$remoteDBname = $CFG->dbname;
		// }
		// $remoteDBuser = get_config('block_learnerscript', 'dbuser');
		// if (empty($remoteDBuser)) {
		// 	$remoteDBuser = $CFG->dbuser;
		// }
		// $remoteDBpass = get_config('block_learnerscript', 'dbpass');
		// if (empty($remoteDBpass)) {
		// 	$remoteDBpass = $CFG->dbpass;
		// }

		// $db_class = get_class($DB);
		// $remoteDB = new $db_class();
		// $remoteDB->connect($remoteDBhost, $remoteDBuser, $remoteDBpass, $remoteDBname, $CFG->prefix);

		$starttime = microtime(true);

		if (preg_match('/\b(INSERT|INTO|CREATE)\b/i', $sql)) {
			// Run special (dangerous) queries directly.
			$results = $DB->execute($sql);
		} else {
			$results = $DB->get_recordset_sql($sql, null, 0, 1);
		}

		// Update the execution time in the DB.
		// $updaterecord = $DB->get_record('block_learnerscript', array('id' => $this->config->id));
		$lastexecutiontime = round((microtime(true) - $starttime) * 1000);
		$this->config->lastexecutiontime = $lastexecutiontime;

		// $DB->update_record('block_learnerscript', $updaterecord);
        $DB->set_field('block_learnerscript', 'lastexecutiontime', $lastexecutiontime,  array('id' => $this->config->id));
		return $results;
	}

	function create_report($blockinstanceid = null, $start = 0, $length = -1, $search = '') {
		global $DB, $CFG, $PAGE;

		$PAGE->requires->jquery_plugin('ui-css');
		//$PAGE->requires->js('/blocks/learnerscript/js/tooltip.js');

		$components = (new ls)->cr_unserialize($this->config->components);

		$filters = (isset($components['filters']['elements'])) ? $components['filters']['elements'] : array();
		$calcs = (isset($components['calcs']['elements'])) ? $components['calcs']['elements'] : array();

		$tablehead = array();
		$finalcalcs = array();
		$finaltable = array();
		$tablehead = array();

		$components = (new ls)->cr_unserialize($this->config->components);
		$config = (isset($components['customsql']['config'])) ? $components['customsql']['config'] : new stdclass;
		$totalrecords = 0;

		$sql = '';
		if (isset($config->querysql)) {
			// FILTERS
			$sql = $config->querysql;
			if (!empty($filters)) {
				foreach ($filters as $f) {
					require_once($CFG->dirroot . '/blocks/learnerscript/components/filters/' . $f['pluginname'] . '/plugin.class.php');
					$classname = 'block_learnerscript\lsreports\plugin_' . $f['pluginname'];
					$class = new $classname($this->config);
					$sql = $class->execute($sql, $f['formdata']);
				}
			}

			$sql = $this->prepare_sql($sql);

			if ($rs = $this->execute_query($sql)) {
				foreach ($rs as $row) {
					if (empty($finaltable)) {
						foreach ($row as $colname => $value) {
							$tablehead[] = ucfirst(str_replace('_', ' ', $colname));
						}
					}
					$array_row = array_values((array) $row);
					foreach ($array_row as $ii => $cell) {
						$array_row[$ii] = str_replace('[[QUESTIONMARK]]', '?', $cell);
					}
					$totalrecords++; 
					if ($this->config->name == 'Maximum time spent on LMS' || $this->config->name == 'Average time spent on LMS' || $this->config->name == 'Total timespent') { 
						if ($array_row[0] > 0) {
							$array_row[0] = (new ls)->strTime($array_row[0]); 
						}
					} else if ($this->config->name == 'Maximum time spent in course' || $this->config->name == 'Maximum time spent in activity level') {
						if ($array_row[1] > 0) {
							$array_row[1] = (new ls)->strTime($array_row[1]); 
						}
					}
					$finaltable[] = $array_row;
				}
			}
		}
		$this->sql = $sql;
		$this->totalrecords = $totalrecords;
		if ($blockinstanceid == null) {
			$blockinstanceid = $this->config->id;
		}

		// Calcs

		$finalcalcs = $this->get_calcs($finaltable, $tablehead);

		$table = new stdclass;
		$table->id = 'reporttable_' . $blockinstanceid . '';
		$table->data = $finaltable;
		$table->head = $tablehead;

		$calcs = new html_table();
		$calcs->id = 'calcstable';
		$calcs->data = array($finalcalcs);
		$calcs->head = $tablehead;

		if (!$this->finalreport) {
			$this->finalreport = new StdClass;
		}
		$this->finalreport->table = $table;
		$this->finalreport->calcs = $calcs;

		return true;
	}

}
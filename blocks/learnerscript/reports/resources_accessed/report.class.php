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

/** LearnerScript
 * A Moodle block for creating customizable reports
 * @package blocks
 * @subpackage learnerscript
 * @author: Sreekanth<sreekanth@eabyas.in>
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\querylib;
use block_learnerscript\local\reportbase;
use block_learnerscript\report;
use block_learnerscript\local\ls as ls;
use context_system;
use stdClass;
use html_writer;
class report_resources_accessed extends reportbase implements report {
	/**
	 * @param object $report Report object
	 * @param object $reportproperties Report properties object
	 */
	public function __construct($report) {
		parent::__construct($report);
		$this->components = array('columns', 'conditions', 'ordering', 'permissions', 'filters', 'plot');
		$this->parent = true;
		$this->columns = array('resourcesaccessed' => array('userfullname', 'email', 'category', 'coursefullname', 'action', 'lastaccess', 'activityname', 'activitytype'));
		$this->courselevel = false;
		$this->basicparams = array(['name' => 'courses']);
		$this->filters = array('modules');
		$this->orderable = array('userfullname', 'email', 'category', 'coursefullname', 'courseshortname');
        $this->defaultcolumn = 'u.id, cm.id';
        $this->excludedroles = array("'student'");
	}
    function init() {
        $this->params['contextlevel'] = 70;
        $this->params['target'] = 'course_module';
        $this->params['action'] = 'viewed';
        if (!isset($this->params['filter_courses']) && $this->params['filter_courses'] > SITEID) {
            $this->initial_basicparams('courses');
            $filterdata = array_keys($this->filterdata);
            $this->params['filter_courses'] = array_shift($filterdata);   
        } 
        if (!$this->scheduling && isset($this->basicparams) && !empty($this->basicparams)) {
          $basicparams = array_column($this->basicparams, 'name');
          foreach ($basicparams as $basicparam) {
            if (empty($this->params['filter_' . $basicparam])) {
              return false;
            }
          }
        }
    }
    function count() {
        $this->sql  = "SELECT COUNT(DISTINCT (CONCAT(u.id,'-', cm.id))) ";
    }

    function select() {
        $this->sql = " SELECT CONCAT(u.id,'-', cm.id), lsl.userid, u.lastname, c.id AS courseid, c.shortname, c.idnumber, lsl.component";
        if (in_array('email', $this->selectedcolumns)) {
            $this->sql .= ", u.email AS email";
        }
        if (in_array('userfullname', $this->selectedcolumns)) {
            $this->sql .= ", u.firstname AS userfullname";
        }
        if (in_array('category', $this->selectedcolumns)) {
            $this->sql .= ", cc.name AS categoryname";
        }
        if (in_array('coursefullname', $this->selectedcolumns)) {
            $this->sql .= ", c.fullname AS coursefullname";
        }
        if (in_array('action', $this->selectedcolumns)) {
            $this->sql .= ", lsl.action AS action";
        }
        if (in_array('lastaccess', $this->selectedcolumns)) {
            $this->sql .= ", MAX(lsl.timecreated) AS timecreated ";
        }
        if (in_array('activityname', $this->selectedcolumns)) {
            $this->sql .= ", CONCAT(
                                    COALESCE(b.name,''),
                                    COALESCE(files.name,''),
                                    COALESCE(folder.name,''),
                                    COALESCE(imscp.name,''),
                                    COALESCE(p.name,''),
                                    COALESCE(url.name,'')
                                    ) activity ";
        }
        if (in_array('activitytype', $this->selectedcolumns)) {
            $this->sql .= ", m.name AS modulename ";
        }
    }

    function from() {
        $this->sql .= " FROM {logstore_standard_log} as lsl";
    }

    function joins() {
        $this->sql .=" JOIN {user} AS u ON u.id = lsl.userid
                         JOIN {course} AS c ON c.id = lsl.courseid
                         JOIN {course_categories} AS cc ON cc.id = c.category
                         JOIN {course_modules} AS cm ON cm.id = lsl.contextinstanceid AND cm.course = c.id
                         JOIN {modules} AS m ON m.id = cm.module
                    LEFT JOIN {book} AS b ON (b.id = lsl.objectid AND m.name = 'book')
                    LEFT JOIN {resource} AS files ON files.id = lsl.objectid AND m.name = 'resource'
                    LEFT JOIN {folder} AS folder ON folder.id = lsl.objectid AND m.name = 'folder'
                    LEFT JOIN {imscp} AS imscp ON imscp.id = lsl.objectid AND m.name = 'imscp'
                    LEFT JOIN {label} AS label ON label.id = lsl.objectid AND m.name = 'label'
                    LEFT JOIN {page} AS p ON p.id = lsl.objectid AND m.name = 'page'
                    LEFT JOIN {url} AS url ON url.id = lsl.objectid AND m.name = 'url' ";
    }

    function where() {
        $courseid = $this->params['filter_courses'];
        $this->sql .=" WHERE u.id > 2
                         AND lsl.target = :target AND lsl.contextlevel = :contextlevel AND lsl.action = :action
                         AND m.name IN ('book', 'resource', 'folder', 'imscp', 'label', 'page', 'url')
                         AND c.visible = 1 AND cm.visible = 1 AND cm.deletioninprogress = 0
                         AND u.deleted = 0 AND u.confirmed = 1";
        $coursesql  = (new querylib)->get_learners('','lsl.courseid');
        $this->sql .= " AND lsl.userid IN ($coursesql)";
        if (!is_siteadmin($this->userid) && !(new ls)->is_manager($this->userid, $this->contextlevel, $this->role)) {
            if ($this->rolewisecourses != '') {
                $this->sql .= " AND c.id IN ($this->rolewisecourses) ";
            }
        }
        parent::where();
    }

    function search() {
        global $DB;
        if (isset($this->search) && $this->search) {
            $this->searchable = array("CONCAT(u.firstname, ' ', u.lastname)", "u.email", "lsl.action", "c.fullname", "c.shortname","c.idnumber","cc.name",'b.name', 'files.name', 'folder.name', 'imscp.name', 'p.name', 'url.name','m.name');
            $statsql = array();
            foreach ($this->searchable as $key => $value) {
                $statsql[] =$DB->sql_like($value, "'%" . $this->search . "%'",$casesensitive = false,$accentsensitive = true, $notlike = false);
            }
            $fields = implode(" OR ", $statsql);           
            $this->sql .= " AND ($fields) ";
        }
    }

    function filters() {
        global $DB;
        if ($this->params['filter_courses'] > SITEID) {
            $this->sql .= " AND lsl.courseid IN (:filter_courses)";
        }
        if (isset($this->params['filter_modules']) && $this->params['filter_modules'] > 0) {
            $modulename = $DB->get_field('modules', 'name', array('id' => $this->params['filter_modules']));
            $this->sql .= " AND m.id IN (:filter_modules) ";
        }

        if ($this->ls_startdate > 0 && $this->ls_enddate) {
            $this->sql .= " AND lsl.timecreated BETWEEN :ls_fstartdate AND :ls_fenddate ";
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
        }
    }
    function groupby() {
        $this->sql .= " GROUP BY u.id, cm.id, lsl.userid, u.lastname, c.id , c.shortname, c.idnumber, lsl.component, u.email, u.firstname, cc.name, c.fullname , lsl.action, b.name, files.name, folder.name, imscp.name, p.name, url.name, m.name ";
    }
	/**
	 * @param  array $users users
	 * @return array $data users courses information
	 */
	public function get_rows($logs) {
		global $DB, $CFG, $OUTPUT, $USER;
		$systemcontext = context_system::instance();
		$data = array();
		$datefiltersql = '';
		if (!empty($logs)) {
			foreach ($logs as $log) {
				$report = new stdClass();
				$userrecord = $DB->get_record('user', array('id' => $log->userid));
				$reportid = $DB->get_field('block_learnerscript', 'id', array('type' => 'userprofile'));
				$userprofilepermissions = empty($reportid) ? false : (new reportbase($reportid))->check_permissions($USER->id, $systemcontext);
				if(empty($reportid) || empty($userprofilepermissions)){
					$report->userfullname .= $OUTPUT->user_picture($userrecord, array('size' => 30)) .html_writer::tag('a', fullname($userrecord), array('href' => $CFG->wwwroot.'/user/profile.php?id='.$log->userid.''));
				} else{
					$report->userfullname = $OUTPUT->user_picture($userrecord, array('size' => 30)) . html_writer::link("$CFG->wwwroot/blocks/learnerscript/viewreport.php?id=$reportid&filter_users=$userrecord->id", ucfirst(fullname($userrecord)), array("target" => "_blank"));
				}
				$report->email = $log->email;
				$report->category = $log->categoryname;
				$report->coursefullname = $log->coursefullname;
				$report->courseid = $log->courseid;
				$report->courseidnumber =  $log->idnumber ? $log->idnumber : '--';
				$report->activitytype = get_string('pluginname', $log->modulename);
				$report->activityname = $log->activity;
				$report->action = ucfirst($log->action);
				$report->lastaccess = userdate($log->timecreated);
				$data[] = $report;
			}
		}
		return $data;
	}
}

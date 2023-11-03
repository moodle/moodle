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
 * @author: Arun Kumar <arun@eabyas.in>
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\reportbase;
use block_learnerscript\local\querylib;
use block_learnerscript\local\ls as ls;

class report_scormparticipation extends reportbase {

    public function __construct($report, $reportproperties) {
        parent::__construct($report);
        $this->columns = array('scormparticipationcolumns' => array('username','course', 'scormname', 'attempt' , 'activitystate', 'finalgrade','firstaccess','lastaccess','totaltimespent'));
        $this->parent = true;
        if (is_siteadmin() || $this->role != 'student') {
            $this->basicparams = [['name' => 'courses']];
        }
        $this->components = array('columns', 'filters', 'permissions', 'calcs', 'plot');
        $this->courselevel = false;
        $this->orderable = array('username','course', 'scormname', 'attempt' , 'activitystate', 'finalgrade','firstaccess','lastaccess','totaltimespent');
        $this->defaultcolumn = "concat(s.id, '-', c.id, '-', ra.userid)";
	}
  function init() {
        if($this->role != 'student' && !isset($this->params['filter_users'])){
            $this->initial_basicparams('users');
            $fusers = array_keys($this->filterdata);
            $this->params['filter_users'] = array_shift($fusers);
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
    $this->sql .= "SELECT COUNT(DISTINCT (concat(s.id, '-', c.id, '-', ra.userid))) ";
  }

  function select() {
        $userid = isset($this->params['userid']) ? $this->params['userid'] : 0;
        $this->sql = "SELECT DISTINCT(concat(s.id, '-', c.id, '-', ra.userid)), s.id ,c.id AS courseid, ra.userid AS userid, cm.id AS cmid,
                                           m.id AS moduleid, st.scormid AS scormid, c.fullname AS course, s.name AS scormname, u.username as username"; 

        parent::select();

  }

  function from() {
    $this->sql .= " FROM {role_assignments} AS ra";
  }

  function joins() {
    $this->sql .= " JOIN {user} u ON u.id =ra.userid
                    JOIN {role} as r on r.id=ra.roleid AND r.shortname='student'
                   JOIN {context} AS ctx ON ctx.id = ra.contextid
                   JOIN {course} as c ON c.id = ctx.instanceid
                   JOIN {scorm} AS s ON s.course = c.id
                   JOIN {course_modules} AS cm ON cm.instance = s.id
                   JOIN {modules} AS m ON m.id = cm.module
              LEFT JOIN {scorm_scoes_track} AS st ON st.scormid = s.id
                   JOIN {scorm_scoes} ss ON ss.scorm = s.id
              LEFT JOIN {course_modules_completion} cc ON cc.coursemoduleid = cm.id";

    parent::joins();
  }

    function where() {
        $userid = isset($this->params['filter_users']) && $this->params['filter_users'] > 0
                    ? $this->params['filter_users'] : '';
        $this->params['userid'] = $userid;
        $this->sql .=" WHERE cm.visible = 1 AND
                                  cm.deletioninprogress = 0 AND c.visible = 1 AND m.name = 'scorm' AND u.id > 2 ";
        if(isset($this->params['filter_scorm']) && $this->params['filter_scorm']){
          $this->sql .= " AND s.id = ".$this->params['filter_scorm'];
        }
        if(isset($this->params['filter_status']) && $this->params['filter_status'] == 'completed'){
          $this->sql .= " AND cc.userid = ra.userid AND cc.completionstate <> 0" ;
        }
        if ((!is_siteadmin() || $this->scheduling) && !(new ls)->is_manager()) {
            if ($this->rolewisecourses != '') {
                $this->sql .= " AND c.id IN ($this->rolewisecourses) ";
            }
        }
        parent::where();
    }

    function search() {
        global $DB;
        if (isset($this->search) && $this->search) {
            $this->searchable = array("c.fullname", "s.name","u.username");
            $statsql = array();
            foreach ($this->searchable as $key => $value) {
                $statsql[] =$DB->sql_like($value, "'%" . $this->search . "%'",$casesensitive = false,$accentsensitive = true, $notlike = false);
            }
            $fields = implode(" OR ", $statsql);           
            $this->sql .= " AND ($fields) ";
        }
    }

    function filters() {
        
        $userid = isset($this->params['filter_users']) && $this->params['filter_users'] > 0
                    ? $this->params['filter_users'] : '';
        $this->courseid = isset($this->params['filter_courses']) ? $this->params['filter_courses'] : array();
        $this->params['userid'] = $userid;
        
        if($this->ls_startdate >= 0 && $this->ls_enddate) {
            $this->sql .= " AND ra.timemodified BETWEEN :ls_fstartdate AND :ls_fenddate ";
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
        }
        if (!empty($this->courseid) && $this->courseid != '_qf__force_multiselect_submission') {
          $courseid = $this->courseid;
          $this->sql .= " AND c.id = :courseid";
          $this->params['courseid'] = $courseid;
        }
    }

    function groupby() {
      $this->sql .= " GROUP BY s.id, c.id, ra.userid, cm.id, m.id, st.scormid, c.fullname, s.name, u.username";
    }
	public function get_rows($elements) {
		return $elements;
	}
    public function column_queries($columnname, $scormid) {
        global $CFG;
    }

}

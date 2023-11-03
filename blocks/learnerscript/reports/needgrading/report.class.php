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
use block_learnerscript\local\ls as ls;

defined('MOODLE_INTERNAL') || die();
class report_needgrading extends reportbase implements report {
    /**
     * [__construct description]
     * @param object $report Report object
     * @param object $reportproperties Report properties object
     */
    public function __construct($report, $reportproperties) {
        global $USER;
        parent::__construct($report, $reportproperties);
        $this->components = array('columns', 'filters', 'permissions', 'calcs', 'plot');
        $columns = ['username', 'course', 'module', 'assignment', 'datesubmitted', 'delay', 'grade'];
        $this->columns = ['needgrading' => $columns];
        $this->courselevel = true;
        if ($this->role != 'student') {
            //$this->basicparams = [['name' => 'users']];
        }
        $this->filters = array('users');
        $this->parent = true;
        $this->orderable = array('username', 'course', 'module', 'assignment', 'datesubmitted', 'delay', 'grade');
        $this->defaultcolumn = "concat(gg.itemid, '-', cm.course, '-', gg.userid, '-',cm.id)";
        $this->excludedroles = array("'student'");
    }
    function init() {
    }
    function count() {
       $this->sql = "SELECT COUNT(DISTINCT concat(gg.itemid, '-', cm.course, '-', gg.userid)) ";
    }

    function select() {
        $this->sql = "SELECT DISTINCT(concat(gg.itemid, '-', cm.course, '-', gg.userid, '-',cm.id)) , co.fullname as course, (SELECT concat(u.firstname,' ',u.lastname) FROM {user} u WHERE u.id=gg.userid) as username, gg.userid, gi.itemname as assignment, gg.timecreated as timecreated, m.name as module, cm.id as cmd";
    }

    function from() {
       $this->sql .= " FROM {grade_grades} gg";
    }

    function joins() {
        $this->sql .= " JOIN {grade_items} as gi on gi.id = gg.itemid
                        JOIN {course_modules} cm ON cm.instance = gi.iteminstance AND gi.itemtype = 'mod' 
                        JOIN {modules} m ON m.id = cm.module
                        JOIN {course} co ON co.id = cm.course
                        ";
    }

    function where() {
        global $DB;
        $userid = $this->userid;
        $this->sql .= " WHERE co.visible = 1 AND cm.visible = 1 AND m.visible = 1 
                        AND cm.course IN( SELECT DISTINCT c.id 
                        FROM {role_assignments} AS rl
                        JOIN {context} AS cxt ON cxt.id = rl.contextid
                        JOIN {user} AS u ON u.id=rl.userid
                        JOIN {user_enrolments} AS ue ON  ue.userid=u.id
                        JOIN {enrol} AS en ON ue.enrolid = en.id
                        JOIN {course} AS c ON c.id=en.courseid 
                        JOIN {role} r ON r.id = rl.roleid
                        WHERE ue.userid=u.id AND rl.userid>2 AND c.visible = 1 AND c.id = cxt.instanceid
                        AND cxt.contextlevel =50 AND rl.userid = $userid AND rl.roleid = 3) AND gg.finalgrade is null AND gi.itemmodule = 'qbassign' AND gg.timecreated is not null AND m.name = gi.itemmodule";

    }

    function search() {
       
    }

    function filters() { 
        if (isset($this->params['filter_users']) && $this->params['filter_users']) {
             $this->sql .= " AND gg.userid = ".$this->params['filter_users'];
        }
    } 

    function groupby() {
        $this->sql .= " GROUP BY gg.itemid, cm.course, gg.userid, cm.id, co.fullname, gi.itemname, gg.timecreated, m.name";
    }

    /**
     * @param  array $activites Activites
     * @return array $reportarray Activities information
     */
    public function get_rows($activites) {
        return $activites;
    }

    public function column_queries($column, $activityid, $courseid = null){
        global $DB;
    }
}

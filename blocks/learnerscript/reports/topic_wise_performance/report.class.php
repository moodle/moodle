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
use block_learnerscript\local\reportbase;
use block_learnerscript\report;
use context_system;
use context_course;
use stdClass;
use html_writer;

class report_topic_wise_performance extends reportbase implements report {

    private $relatedctxsql;

    /**
     * [__construct description]
     * @param [type] $report           [description]
     * @param [type] $reportproperties [description]
     */
    public function __construct($report, $reportproperties) {
        global $USER;
        parent::__construct($report, $reportproperties);
        $this->components = array('columns', 'filters', 'permissions', 'plot');
        $this->columns = array("topicwiseperformance" => array("learner", "email"));
        $this->basicparams = [['name' => 'courses']];
        $this->parent = false;
        $this->courselevel = true;
        $this->filters = array('users');
        $this->orderable = array('learner','email');
        $this->defaultcolumn = 'u.id';
        $this->excludedroles = array("'student'");
    }
    function init() {
        global $DB;
        if(!isset($this->params['filter_courses'])){
            $this->initial_basicparams('courses');
            $fcourses = array_keys($this->filterdata);
            $this->params['filter_courses'] = array_shift($fcourses);
        }
        if (!$this->scheduling && isset($this->basicparams) && !empty($this->basicparams)) {
            $basicparams = array_column($this->basicparams, 'name');
            foreach ($basicparams as $basicparam) {
                if (empty($this->params['filter_' . $basicparam])) {
                    return false;
                }
            }
        }
        $courseid = isset($this->params['filter_courses']) ? $this->params['filter_courses'] : SITEID ;
        $userid = isset($this->params['filter_users']) ? $this->params['filter_users'] : $this->userid;
        $context = context_course::instance($courseid);
        $roleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
        $ctxs = $context->get_parent_context_ids(true);
        list($this->relatedctxsql, $params) = $DB->get_in_or_equal($context->get_parent_context_ids(true), SQL_PARAMS_NAMED, 'relatedctx');
        $this->params = array_merge($this->params, $params);
        $this->params['contextlevel'] = CONTEXT_COURSE;
        $this->params['userid'] = $userid;
        $this->params['ej1_active'] = ENROL_USER_ACTIVE;
        $this->params['ej1_enabled'] = ENROL_INSTANCE_ENABLED;
        $this->params['ej1_now1'] = round(time(), -2); // improves db caching
        $this->params['ej1_now2'] = $this->params['ej1_now1'];
        $this->params['ej1_courseid'] = $courseid;
        $this->params['courseid'] = $courseid;
        $this->params['roleid'] = $roleid;
    }
    function count() {
        $this->sql = "SELECT COUNT(DISTINCT u.id) ";
    }

    function select() {
        $this->sql = "SELECT DISTINCT u.id, u.picture, u.firstname, u.lastname, CONCAT(u.firstname , u.lastname) as learner, u.email ";
    }

    function from() {
        $this->sql .= " FROM {user} u";
    }

    function joins() {
        parent::joins();
         $this->sql .="  JOIN (SELECT DISTINCT eu1_u.id, ej1_ue.timecreated
                         FROM {user} eu1_u
                         JOIN {user_enrolments} ej1_ue ON ej1_ue.userid = eu1_u.id
                         JOIN {enrol} ej1_e ON (ej1_e.id = ej1_ue.enrolid AND ej1_e.courseid = :ej1_courseid)
                            WHERE 1 = 1 AND ej1_ue.status = :ej1_active AND ej1_e.status = :ej1_enabled AND
                            ej1_ue.timestart < :ej1_now1 AND (ej1_ue.timeend = 0 OR ej1_ue.timeend > :ej1_now2) AND
                             eu1_u.deleted = 0) e ON e.id = u.id
                         LEFT JOIN {context} ctx ON (ctx.instanceid = u.id AND ctx.contextlevel = :contextlevel)";
    }

    function where() {
        $this->sql .=" WHERE u.id IN (SELECT userid
                                            FROM {role_assignments}
                                           WHERE roleid = :roleid AND contextid $this->relatedctxsql)";
        parent::where();
    }

    function search() {
        global $DB;
        if (isset($this->search) && $this->search) {
            $this->searchable = array("CONCAT(u.firstname, ' ', u.lastname)", "u.email");
            $statsql = array();
            foreach ($this->searchable as $key => $value) {
                $statsql[] =$DB->sql_like($value, "'%" . $this->search . "%'",$casesensitive = false,$accentsensitive = true, $notlike = false);
            }
            $fields = implode(" OR ", $statsql);       
            $this->sql .= " AND ($fields) ";
        }
    }

    function filters() {
        $userid = isset($this->params['filter_users']) ? $this->params['filter_users'] : $this->userid;
        $this->params['userid'] = $userid;
        if (isset($userid) && $userid > 0) {
            $this->sql .= " AND u.id = :userid";
        }
        if (isset($this->params['filter_modules']) && $this->params['filter_modules'] > 0) {
            $this->sql .= " AND cm.module = :filter_modules";
        }
        if($this->ls_startdate >= 0 && $this->ls_enddate) {
            $this->sql .= " AND u.timecreated BETWEEN :ls_startdate AND :ls_enddate ";
            $this->params['ls_startdate'] = ROUND($this->ls_startdate);
            $this->params['ls_enddate'] = ROUND($this->ls_enddate);
        }
    }

    function groupby() {
        
    }
    /**
     * [get_rows description]
     * @param  [type] $elements [description]
     * @return [type]           [description]
     */
    public function get_rows($elements) {
        global $DB, $CFG, $USER, $OUTPUT;
        $systemcontext = context_system::instance();
        $finalelements = array();
        if (!empty($elements)) {
            if (!empty($this->params['filter_courses']) && $this->params['filter_courses'] <> SITEID  && !$this->scheduling) {
                $courseid = $this->params['filter_courses'];
            }
            foreach ($elements as $record) {
                $report = new stdClass();
                $userrecord = $DB->get_record('user',array('id'=>$record->id));
                $reportid = $DB->get_field('block_learnerscript', 'id', array('type' => 'userprofile'), IGNORE_MULTIPLE);
                $userprofilepermissions = empty($reportid) ? false : (new reportbase($reportid))->check_permissions($this->userid, $systemcontext);
                if(empty($reportid) || empty($userprofilepermissions)){
                    $report->learner .= $OUTPUT->user_picture($userrecord, array('size' => 30)) .html_writer::tag('a', fullname($userrecord), array('href' => $CFG->wwwroot.'/user/profile.php?id='.$userrecord->id.''));

                }else{
                    $report->learner = $OUTPUT->user_picture($userrecord, array('size' => 30)) . html_writer::link("$CFG->wwwroot/blocks/learnerscript/viewreport.php?id=$reportid&filter_users=$record->id", ucfirst(fullname($userrecord)), array("target" => "_blank"));
                }
                $report->email = $record->email;
                $sections = $DB->get_records_sql("SELECT * FROM {course_sections} WHERE course = :courseid ORDER BY id", ['courseid' => $courseid]);
                $i = 0;
                foreach($sections as $section){
                    $coursemodulesql = "SELECT SUM(gg.finalgrade) / SUM(gi.grademax) AS score
                                          FROM {grade_items} AS gi
                                          JOIN {grade_grades} AS gg ON gg.itemid = gi.id
                                          JOIN {course_modules} AS cm ON cm.instance = gi.iteminstance
                                          JOIN {modules} AS m ON m.id = cm.module AND m.name = gi.itemmodule
                                         WHERE cm.section = :sectionid AND gg.userid = :recordid AND cm.visible = :visible ";
                    $coursemodulescore = $DB->get_field_sql($coursemodulesql, ['sectionid' => $section->id, 'recordid' => $record->id, 'visible' => 1]);
                    $sectionkey ="section$i";
                    if($coursemodulescore){
                        $report->{$sectionkey} = (ROUND($coursemodulescore *100,2)).' %';
                    } else {
                        $report->{$sectionkey} = '--';
                    }
                    $i++;
                }
                $data[] = $report;
            }
            return $data;
        }
        return $finalelements;
    }
}

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
 * @author: Jahnavi<jahnavi@eabyas.in>
 * @date: 2020
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\reportbase;
use block_learnerscript\report;
use context_system;
use stdClass;
use html_writer;

class report_cohortusers extends reportbase implements report {

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
        $this->columns = array('userfield' => array('userfield'), "cohortusercolumns" => array("learner", "email"));
        $this->basicparams = [['name' => 'cohort']];
        $this->parent = false;
        $this->courselevel = true;
        $this->filters = array('users');
        $this->orderable = array('learner','email');
        $this->defaultcolumn = 'u.id';
        $this->excludedroles = array("'student'");
    }
    function init() {
        global $DB;
        if(!isset($this->params['filter_cohort'])){
            $this->initial_basicparams('cohort');
            $fcohorts = array_keys($this->filterdata);
            $this->params['filter_cohort'] = array_shift($fcohorts);
        }
        if (!$this->scheduling && isset($this->basicparams) && !empty($this->basicparams)) {
            $basicparams = array_column($this->basicparams, 'name');
            foreach ($basicparams as $basicparam) {
                if (empty($this->params['filter_' . $basicparam])) {
                    return false;
                }
            }
        }
        $userid = isset($this->params['filter_users']) ? $this->params['filter_users'] : $this->userid;
        $roleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
        
        // $this->params = array_merge($this->params, $params);
        $this->params['userid'] = $userid;
        $this->params['ej1_active'] = ENROL_USER_ACTIVE;
        $this->params['ej1_enabled'] = ENROL_INSTANCE_ENABLED;
        $this->params['ej1_now1'] = round(time(), -2); // improves db caching
        $this->params['ej1_now2'] = $this->params['ej1_now1'];
        $this->params['roleid'] = $roleid;
    }
    function count() {
        $this->sql = "SELECT COUNT(DISTINCT u.id) ";
    }

    function select() {
        $this->sql = "SELECT DISTINCT u.id, u.picture, u.firstname, u.lastname, CONCAT(u.firstname , u.lastname) as learner, u.email, u.* ";
    }

    function from() {
        $this->sql .= " FROM {user} u";
    }

    function joins() {
        parent::joins();
         $this->sql .="  JOIN {cohort_members} cmem ON cmem.userid = u.id ";
    }

    function where() {
        $this->sql .=" WHERE 1 = 1";
        parent::where();
    }

    function search() {
        global $DB;
        if (isset($this->search) && $this->search) {
            $this->searchable =array("CONCAT(u.firstname, ' ', u.lastname)", "u.email");
            $statsql = array();
            foreach ($this->searchable as $key => $value) {
                $statsql[] =$DB->sql_like($value, "'%" . $this->search . "%'",$casesensitive = false,$accentsensitive = true, $notlike = false);
            }
            $fields = implode(" OR ", $statsql);     
            $this->sql .= " AND ($fields) ";
        }
    }

    function filters() {
        if (isset($this->params['filter_users'])
            && $this->params['filter_users'] >0
            && $this->params['filter_users'] != '_qf__force_multiselect_submission') {
            $this->sql .= " AND u.id IN (:filter_users) ";
        }
        if (isset($this->params['filter_cohort']) && $this->params['filter_cohort'] > 0) {
            $this->sql .= " AND cmem.cohortid IN (:filter_cohort)";
        }
        if($this->ls_startdate >= 0 && $this->ls_enddate) {
            $this->sql .= " AND u.timecreated BETWEEN :ls_fstartdate AND :ls_fenddate ";
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
        }
    }
	public function groupby() {
    
    }
    /**
     * [get_rows description]
     * @param  [type] $elements [description]
     * @return [type]           [description]
     */
    public function get_rows($elements) {
        global $DB, $CFG, $USER, $OUTPUT;
        $systemcontext = context_system::instance();
        // $finalelements = array();
        if (!empty($elements)) {
            $courseid = isset($this->params['filter_cohort']) ? $this->params['filter_cohort'] : 0;
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
                $report->id = $record->id;
                $sections = $DB->get_records_sql("SELECT DISTINCT c.id, c.fullname 
                                FROM {course} c 
                                JOIN {enrol} e ON e.courseid = c.id AND e.status = 0 
                                JOIN {user_enrolments} ue ON ue.enrolid = e.id AND ue.status = 0 
                                JOIN {role_assignments} ra ON ra.userid = ue.userid 
                                JOIN {context} ctx ON ctx.id = ra.contextid 
                                JOIN {cohort_members} cm ON cm.userid = ra.userid 
                                JOIN {cohort} co ON co.id = cm.cohortid
                                WHERE co.id = :courseid
                                AND ctx.instanceid = c.id", ['courseid' => $courseid]);
                $i = 0;
                foreach($sections as $section){
                    $usercompletionsql = "SELECT cc.timecompleted 
                                        FROM {course_completions} cc 
                                        WHERE cc.userid = :recordid AND cc.course = :sectionid";
                    $usercompletion = $DB->get_field_sql($usercompletionsql, ['recordid' => $record->id, 'sectionid' => $section->id]);
                    $sectionkey ="section$i";
                    if($usercompletion > 0){
                        $report->{$sectionkey} = '<span class="label label-success">Completed</span>';
                    } else {
                        $report->{$sectionkey} = '<span class="label label-warning">Not completed</span>';
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

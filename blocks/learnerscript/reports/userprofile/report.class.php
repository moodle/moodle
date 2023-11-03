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

/**
 * LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\reportbase;
use block_learnerscript\local\ls;
use block_learnerscript\local\querylib;
use html_table;
use stdClass;

class report_userprofile extends reportbase {
    /**
     * @param object $report Report object
     * @param object $reportproperties Report properties object
     */
    public function __construct($report, $reportproperties) {
        global $USER;
        parent::__construct($report, $reportproperties);
        $this->components = array('columns', 'conditions', 'ordering', 'filters', 'permissions');
        $this->parent = false;
        if ($this->role != 'student') {
            $this->basicparams = [['name' => 'users', 'singleselection' =>false, 'placeholder' => false, 'maxlength' => 5]];
        }
        $this->columns = array('userfield' => array('userfield'), 'userprofile' => array('enrolled', 'inprogress',
            'completed', 'completedcoursesgrade', 'quizes', 'assignments', 'scorms', 'badges', 'progress', 'status'));
        // $this->filters = array('users');
        $this->exports = false;
        $this->orderable = array();
        $this->defaultcolumn = 'u.id';
    }
    function init() {
        if (!$this->scheduling) {
		if($this->role != 'student' && !isset($this->params['filter_users'])){
		    $this->initial_basicparams('users');
		    $fusers = array_keys($this->filterdata);
		    $this->params['filter_users'] = array_shift($fusers);
		}
        }
        if (!$this->scheduling && isset($this->basicparams) && !empty($this->basicparams)) {
            $basicparams = array_column($this->basicparams, 'name');
            foreach ($basicparams as $basicparam) {
                if (empty($this->params['filter_' . $basicparam])) {
                    return false;
                }
            }
        }
        if ($this->ls_startdate >= 0 && $this->ls_enddate) {
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
        }
    }
    function count() {
     $this->sql  = " SELECT COUNT(DISTINCT u.id) ";     
    }

    function select() {
        $this->sql = " SELECT DISTINCT u.id , CONCAT(u.firstname,' ',u.lastname) AS fullname ";
        parent::select();
    }

    function from() {
        $this->sql  .= "FROM {user} u";
    }

    function joins() {
        $this->sql .=" JOIN {role_assignments} ra ON ra.userid = u.id "; 
        parent::joins();
    }

    function where() {
        $userid = isset($this->params['filter_users']) && $this->params['filter_users'] > 0
                    ? $this->params['filter_users'] : $this->userid;
        if(is_array($userid)){
            $userid = implode(',', $userid);
        }
        $this->params['userid'] = $userid; 
        $this->sql .=" WHERE u.confirmed = 1 AND u.deleted = 0 AND u.id IN ($userid)  
                        AND ra.timemodified BETWEEN :ls_fstartdate AND :ls_fenddate";
        
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

    function filters(){}
    function groupby() {
        global $CFG; 
        if ($CFG->dbtype != 'sqlsrv') {
            $this->sql .= " GROUP BY u.id";
        }
    }
    /**
     * @param  array $users users
     * @return array $data users courses information
     */
    public function get_rows($users) {
        return $users;
    }
    public function column_queries($column, $userid){
        $where = " AND %placeholder% = $userid";
        if (!is_siteadmin($this->userid) && !(new ls)->is_manager($this->userid)) {
            if ($this->rolewisecourses != '') {
                $coursefilter = " AND c.id IN ($this->rolewisecourses) ";
            } 
        }else{
          $coursefilter = "";
        }
        switch ($column) {
            case 'enrolled':
                $identy = "ue.userid";
                $query = "SELECT COUNT(DISTINCT c.id) AS enrolled 
                          FROM {user_enrolments} ue   
                          JOIN {enrol} e ON ue.enrolid = e.id AND e.status = 0
                          JOIN {role_assignments} ra ON ra.userid = ue.userid
                          JOIN {role} r ON r.id = ra.roleid AND r.shortname = 'student'
                          JOIN {context} AS ctx ON ctx.id = ra.contextid
                          JOIN {course} c ON c.id = ctx.instanceid AND  c.visible = 1 
                          WHERE ue.status = 0 $where $coursefilter";
                break;
            case 'inprogress':
                $identy = "ue.userid";
                $query = "SELECT (COUNT(DISTINCT c.id) - COUNT(DISTINCT cc.id)) AS inprogress 
                          FROM {user_enrolments} ue   
                          JOIN {enrol} e ON ue.enrolid = e.id AND e.status = 0
                          JOIN {role_assignments} ra ON ra.userid = ue.userid
                          JOIN {role} r ON r.id = ra.roleid AND r.shortname = 'student'
                          JOIN {context} AS ctx ON ctx.id = ra.contextid
                          JOIN {course} c ON c.id = ctx.instanceid AND  c.visible = 1 
                     LEFT JOIN {course_completions} cc ON cc.course = ctx.instanceid AND cc.userid = ue.userid AND cc.timecompleted > 0
                         WHERE 1=1 $where $coursefilter ";
                break;
            case 'completed':
                $identy = "cc.userid";
                $query = "SELECT COUNT(DISTINCT cc.course) AS completed 
                          FROM {user_enrolments} ue   
                          JOIN {enrol} e ON ue.enrolid = e.id AND e.status = 0
                          JOIN {role_assignments} ra ON ra.userid = ue.userid
                          JOIN {role} r ON r.id = ra.roleid AND r.shortname = 'student'
                          JOIN {context} AS ctx ON ctx.id = ra.contextid
                          JOIN {course} c ON c.id = ctx.instanceid AND  c.visible = 1 
                          JOIN {course_completions} cc ON cc.course = ctx.instanceid AND cc.userid = ue.userid AND cc.timecompleted > 0
                          WHERE ue.status = 0 $where $coursefilter ";
                break;
            case 'progress':
                $identy = "ra.userid";
                $query = "SELECT ROUND((COUNT(distinct cc.course) / COUNT(DISTINCT c.id)) *100, 2) as progress 
                            FROM {user_enrolments} ue   
                            JOIN {enrol} e ON ue.enrolid = e.id AND e.status = 0
                            JOIN {role_assignments} ra ON ra.userid = ue.userid
                            JOIN {role} r ON r.id = ra.roleid AND r.shortname = 'student'
                            JOIN {context} AS ctx ON ctx.id = ra.contextid
                            JOIN {course} c ON c.id = ctx.instanceid AND  c.visible = 1 
                       LEFT JOIN {course_completions} cc ON cc.course = ctx.instanceid AND cc.userid = ue.userid 
                             AND cc.timecompleted > 0 WHERE ue.status = 0 $where $coursefilter";
                break;
            case 'completedcoursesgrade':
                 $identy = "gg.userid";
                 $query = "SELECT CONCAT(ROUND(sum(gg.finalgrade), 2),' / ', ROUND(sum(gi.grademax), 2)) AS completedcoursesgrade 
                           FROM {grade_grades} AS gg
                           JOIN {grade_items} AS gi ON gi.id = gg.itemid
                           JOIN {course_completions} AS cc ON cc.course = gi.courseid
                           JOIN {course} AS c ON cc.course = c.id AND c.visible=1
                          WHERE gi.itemtype = 'course' AND cc.course = gi.courseid
                            AND cc.timecompleted IS NOT NULL 
                            AND gg.userid = cc.userid
                             $where $coursefilter ";
                break;
            case 'assignments':
                $identy = 'ra.userid';
                $query = "SELECT COUNT(cm.id) AS assignments
                                FROM {course_modules} AS cm
                                JOIN {modules} AS m ON m.id = cm.module 
                                JOIN {course} c ON c.id = cm.course AND c.visible = 1
                                JOIN {context} AS ctx ON c.id = ctx.instanceid 
                                JOIN {role_assignments} ra ON ctx.id = ra.contextid 
                                JOIN {role} r ON r.id = ra.roleid AND r.shortname = 'student'
                                WHERE m.name = 'qbassign'
                                AND cm.visible = 1 AND cm.deletioninprogress = 0 $where $coursefilter";
                break;
            case 'quizes':
                $identy = 'ra.userid';
                $query = "SELECT COUNT(cm.id) AS quizes
                                FROM {course_modules} AS cm
                                JOIN {modules} AS m ON m.id = cm.module 
                                JOIN {course} c ON c.id = cm.course AND c.visible = 1
                                JOIN {context} AS ctx ON c.id = ctx.instanceid 
                                JOIN {role_assignments} ra ON ctx.id = ra.contextid 
                                JOIN {role} r ON r.id = ra.roleid AND r.shortname = 'student'
                                WHERE m.name = 'quiz'
                                AND cm.visible = 1 AND cm.deletioninprogress = 0 $where $coursefilter";
                break;
            case 'scorms':
                $identy = 'ra.userid';
                $query = "SELECT COUNT(cm.id) AS scorms
                                FROM {course_modules} AS cm
                                JOIN {modules} AS m ON m.id = cm.module 
                                JOIN {course} c ON c.id = cm.course AND c.visible = 1
                                JOIN {context} AS ctx ON c.id = ctx.instanceid 
                                JOIN {role_assignments} ra ON ctx.id = ra.contextid 
                                JOIN {role} r ON r.id = ra.roleid AND r.shortname = 'student'
                                WHERE m.name = 'scorm'
                                AND cm.visible = 1 AND cm.deletioninprogress = 0 $where $coursefilter";
                break;
            case 'badges':
                $identy = "bi.userid";
                $query = "SELECT count(bi.id) AS badges FROM {badge_issued} as bi 
                          JOIN {badge} as b ON b.id = bi.badgeid
                         WHERE  bi.visible = 1 AND b.status != 0
                          AND b.status != 2 AND b.status != 4 
                           $where ";
                break;
            default:
                return false;
                break;
        }
        $query = str_replace('%placeholder%', $identy, $query);
        return $query;
    }
    function create_report($blockinstanceid = null){
        global $DB, $CFG;
        $components = (new ls)->cr_unserialize($this->config->components);
        $userid = isset($this->params['filter_users']) && $this->params['filter_users'] > 0
            ? $this->params['filter_users'] : $this->userid;


        $conditions = (isset($components['conditions']['elements']))? $components['conditions']['elements'] : array();
        $filters = (isset($components['filters']['elements']))? $components['filters']['elements'] : array();
        $columns = (isset($components['columns']['elements']))? $components['columns']['elements'] : array();
        $ordering = (isset($components['ordering']['elements']))? $components['ordering']['elements'] : array();
        $columnnames  = array();

        foreach ($columns as $key=>$column){
            if(isset($column['formdata']->column)){
                $columnnames[$column['formdata']->column] = $column['formdata']->columname;
                $this->selectedcolumns[] = $column['formdata']->column;
            }
        }
        $finalelements = array();
        $sqlorder = '';
        $orderingdata = array();

        if($this->ordercolumn){
            $this->sqlorder =  $this->selectedcolumns[$this->ordercolumn['column']] . " " . $this->ordercolumn['dir'];
        }else if(!empty($ordering)){
            foreach($ordering as $o){
                require_once($CFG->dirroot.'/blocks/learnerscript/components/ordering/'.$o['pluginname'].'/plugin.class.php');
                $classname = 'block_learnerscript\lsreports\plugin_'.$o['pluginname'];
                $classorder = new $classname($this->config);
                if ($classorder->sql) {
                    $orderingdata = $o['formdata'];
                    $this->sqlorder = $classorder->execute($orderingdata);
                }
            }
        }
        $conditionfinalelements = array();
        if(!empty($conditions)){
            $conditionfinalelements = $this->elements_by_conditions($components['conditions']);
        }

         $this->build_query(true);
        try {
            $this->totalrecords = $DB->count_records_sql($this->sql, $this->params);
        } catch (dml_exception $e) {
            $this->totalrecords = 0;
        }

        $this->build_query();
        // $this->sql .= " GROUP by $this->defaultcolumn ";
        if (is_array($this->sqlorder) && !empty($this->sqlorder)) {
            $this->sql .= " ORDER BY ". $this->sqlorder['column'] .' '. $this->sqlorder['dir'];
        } else {
            if (!empty($sqlorder)) {
                $this->sql .= " ORDER BY c.$sqlorder ";
            } else {
                $this->sql .= " ORDER BY $this->defaultcolumn DESC ";
            }
        }

        try {
            $finalelements = $DB->get_records_sql($this->sql, $this->params, $this->start, $this->length);
        } catch (dml_exception $e) {
            $finalelements = array();
        }
        $rows = $this->get_rows($finalelements);
        $reporttable = array();
        $tablehead = array();
        $tablealign =array();
        $tablesize = array();
        $tablewrap = array();
        $firstrow = true;
        $pluginscache = array();

         if($this->config->type == "topic_wise_performance"){
             $columns = (new ls)->learnerscript_sections_dynamic_columns($columns,$this->config,$this->params);
          }
        if($rows){
            $tempcols = array();
            foreach($rows as $r){
                foreach($columns as $c){
                    $c = (array) $c;
                    if (empty($c)) {
                        continue;
                    }
                        require_once($CFG->dirroot . '/blocks/learnerscript/components/columns/' . $c['pluginname'] . '/plugin.class.php');
                        $classname = 'block_learnerscript\lsreports\plugin_' . $c['pluginname'];

                        if(!isset($pluginscache[$classname])){
                            $class = new $classname($this->config,$c);
                            $pluginscache[$classname] = $class;
                        }
                        else{
                            $class = $pluginscache[$classname];
                        }
                        $class->reportfilterparams = $this->params;
                        $class->role = $this->role;
                        $class->reportinstance = $blockinstanceid ? $blockinstanceid : $this->config->id;
                        if(isset($c['formdata']->column)) {
                            // if(!empty($this->params['filter_users'])){
                            //     $userrecord= $DB->get_record('user', array('id' => $this->params['filter_users']));
                            //     $this->currentuser = $userrecord;
                            // }
                            if(method_exists($this, 'column_queries')){
                                if(isset($r->course)){
                                    $c['formdata']->subquery = $this->column_queries($c['formdata']->column, $r->id, $r->course);
                                    $this->currentcourseid = $r->course;
                                }else if(isset($r->user)){
                                    $c['formdata']->subquery = $this->column_queries($c['formdata']->column, $r->id, $r->user);
                                }else{
                                    $c['formdata']->subquery = $this->column_queries($c['formdata']->column, $r->id);
                                }
                            }
                            $tempcols[$c['formdata']->columname][] = $class->execute($c['formdata'], $r,
                                                                                $this->userid,
                                                                                $this->currentcourseid,
                                                                                $this->starttime,
                                                                                $this->endtime,
                                                                                $this->reporttype);
                        }

                    if($firstrow){
                        if(isset($c['formdata']->column)) {
                            $columnheading = !empty($c['formdata']->columname) ? $c['formdata']->columname : $c['formdata']->column;
                            $tablehead[$c['formdata']->columname] = $columnheading;
                            // $tablehead[$c['formdata']->column] = $c['formdata']->columname;
                        }
                        list($align,$size,$wrap) = $class->colformat($c['formdata']);
                        $tablealign[] = $align;
                        $tablesize[] = $size ? $size . '%' : '';
                        $tablewrap[] = $wrap;
                    }

                }
                $firstrow = false;

            }
            $reporttable = $tempcols;
        }
        // EXPAND ROWS
        $finaltable = array();
        $newcols = array();
        $i=0;
        foreach($reporttable as $key=>$row){
            $r = array_values($row);
           $r[] = $key;

           $finaltable[] = array_reverse($r);
                $i++;
        }
        // CALCS
        $finalheadcalcs = $this->get_calcs($finaltable, $tablehead);
        $finalcalcs = $finalheadcalcs->data;

        if ($blockinstanceid == null)
            $blockinstanceid = $this->config->id;

        // Make the table, head, columns, etc...

        $table = new html_table;
        // $table->id = 'repsorttable_' . $blockinstanceid . '';
        $table->data = $finaltable;
        if (is_array($userid)) {
            for ($i = 0; $i < (COUNT($userid) +1); $i++) {
                $table->head[] = '';
            }
        } else {
            for ($i = 0; $i < 2; $i++) {
                $table->head[] = '';
            }
        }
        $table->size = $tablesize;
        $table->align = $tablealign;
        $table->wrap = $tablewrap;
        $table->width = (isset($components['columns']['config']))? $components['columns']['config']->tablewidth : '';
        $table->summary = $this->config->summary;
        $table->tablealign = (isset($components['columns']['config']))? $components['columns']['config']->tablealign : 'center';
        $table->cellpadding = (isset($components['columns']['config']))? $components['columns']['config']->cellpadding : '5';
        $table->cellspacing = (isset($components['columns']['config']))? $components['columns']['config']->cellspacing : '1';

        if(!$this->finalreport) {
            $this->finalreport = new stdClass;
        }
        $this->finalreport->table = $table;
        $this->finalreport->calcs = null;
        return true;
    }
}

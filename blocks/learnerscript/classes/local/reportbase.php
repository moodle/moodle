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
 * A Moodle block for creating LearnerScript
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\local;
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/lib/evalmath/evalmath.class.php');
require_once($CFG->dirroot . '/blocks/learnerscript/components/columns/component.class.php');
use stdclass;
use block_learnerscript\form\filter_form;
use html_table;
use EvalMath;
use EvalWise;
use component_columns;
use block_learnerscript\local\ls;
use block_learnerscript\locla\querylib;
use context_system;

class reportbase {
    public $id = 0;
    public $components = array();
    public $finalreport;
    public $finalelements;
    public $totalrecords = 0;
    public $currentuser = 0;
    public $currentcourseid = 1;
    public $starttime = 0;
    public $endtime = 0;
    public $sql = '';
    public $designpage = true;
    public $tablehead;
    public $ordercolumn;
    public $sqlorder;
    public $exports = true;
    public $start = 0;
    public $length = 10;
    public $search;
    public $courseid;
    public $cmid;
    public $userid;
    public $status;
    public $filters;
    public $ls_startdate;
    public $ls_enddate;
    public $columns;
    public $basicparams;
    public $params;
    public $filterdata;
    public $role;
    public $contextlevel;
    public $parent = true;
    public $courselevel = false;
    public $conditionsenabled = false;
    public $reporttype = 'table';
    public $scheduling = false;
    public $colformat = false;
    public $calculations = false;
    public $preview = false;
    public $singleplot;
    public $rolewisecourses = '';
    public $groupcolumn;
    public $componentdata;
    private $graphcolumns;
    public $userroles;
    public $selectedcolumns;
    public $selectedfilters;
    public $conditionfinalelements = array();
    function __construct($report, $properties = null) {
        global $DB, $CFG, $USER;

        if (empty($report)) {
             return false;
        }
        if (is_numeric($report)){
            $this->config = $DB->get_record('block_learnerscript', array('id' => $report));
        } else {
            $this->config = $report;
        }
        
        $this->userid = isset($properties->userid) ? $properties->userid : $USER->id ;
        $this->courseid = $this->config->courseid;
        if ($USER->id == $this->userid) {
            $this->currentuser = $USER;
        } else {
            $this->currentuser = $DB->get_record('user', array('id' => $this->userid));
        }
        if(empty($this->role)){
            $this->role = isset($_SESSION['role'])  ? $_SESSION['role'] : (isset($properties->role) ? $properties->role : ''); 
        }
        if(empty($this->contextlevel)){
            $this->contextlevel = isset($_SESSION['ls_contextlevel'])  ? $_SESSION['ls_contextlevel'] : (isset($properties->contextlevel) ? $properties->contextlevel : ''); 
        }
        $this->ls_startdate = isset($properties->ls_startdate) ? $properties->ls_startdate : 0;
        $this->ls_enddate = isset($properties->ls_enddate) ? $properties->ls_enddate : time();
        $this->componentdata = (new ls)->cr_unserialize($this->config->components);
        $this->rolewisecourses = $this->rolewisecourses(); 
        $rolecontexts = $DB->get_records_sql("SELECT DISTINCT CONCAT(r.id, '@', rcl.id), 
                            r.shortname, rcl.contextlevel 
                            FROM {role} AS r 
                            JOIN {role_context_levels} AS rcl ON rcl.roleid = r.id AND rcl.contextlevel NOT IN (70)
                            WHERE 1 = 1  
                            ORDER BY rcl.contextlevel ASC");
        foreach ($rolecontexts as $rc) {
           if ($rc->contextlevel == 10 && ($rc->shortname == 'manager')) {
            continue;
           }
           $rcontext[] = $rc->shortname .'_'.$rc->contextlevel;
        }
        $this->moodleroles = isset($_SESSION['rolecontextlist']) ? $_SESSION['rolecontextlist'] : $rcontext;
        $this->contextrole = isset($_SESSION['role']) && isset($_SESSION['ls_contextlevel']) ? $_SESSION['role'] . '_' . $_SESSION['ls_contextlevel'] : $this->role .'_'.$this->contextlevel;
    }

    function init(){}

    function check_permissions($userid = null, $context) {
        global $DB, $CFG, $USER;
        if($userid == null){
            $userid = $USER->id;
        }

        if(is_siteadmin($userid) || (new ls)->is_manager($userid, $this->contextlevel, $this->role)){
            return true;
        }

        if (has_capability('block/learnerscript:managereports', $context, $userid)){
            return true;
        }

        if (empty($this->config->visible)){
            return false;
        }
        $permissions = (isset($this->componentdata['permissions'])) ? $this->componentdata['permissions'] : array();
        if (empty($permissions['elements'])) {
            return has_capability('block/learnerscript:viewreports', $context, $userid);
        } else {
            $i = 1;
            $cond = array();
            foreach ($permissions['elements'] as $p) {
                require_once($CFG->dirroot . '/blocks/learnerscript/components/permissions/' .
                    $p['pluginname'] . '/plugin.class.php');
                $classname = 'block_learnerscript\lsreports\plugin_' . $p['pluginname'];
                $class = new $classname($this->config);
                
                $class->role = $this->role;
                $class->userroles = isset($this->userroles) ? $this->userroles : '';
                $cond[$i] = $class->execute($userid, $context, $p['formdata']);
                $i++;
            }
            if (count($cond) == 1) {
                return $cond[1];
            } else {
                $m = new EvalMath;
                $orig = $dest = array();
                if (isset($permissions['config']) && isset($permissions['config']->conditionexpr)) {
                    $logic = trim($permissions['config']->conditionexpr);
                    // Security
                    // No more than: conditions * 10 chars
                    $logic = substr($logic, 0, count($permissions['elements']) * 10);
                    $logic = str_replace(array('and', 'or'), array('&&', '||'), strtolower($logic));
                    // More Security Only allowed chars
                    $logic = preg_replace('/[^&c\d\s|()]/i', '', $logic);
                    //$logic = str_replace('c','$c',$logic);
                    $logic = str_replace(array('&&', '||'), array('*', '+'), $logic);

                    for ($j = $i - 1; $j > 0; $j--) {
                        $orig[] = 'c' . $j;
                        $dest[] = ($cond[$j]) ? 1 : 0;
                    }
                    return $m->evaluate(str_replace($orig, $dest, $logic));
                } else {
                    return false;
                }
            }
        }
    }

    function add_filter_elements(&$mform) {
        global $DB, $CFG;
        $ls = new ls;
        
        $filters = (isset($this->componentdata['filters'])) ? $this->componentdata['filters'] : array();
        if(!empty($filters['elements'])) {
            foreach ($filters['elements'] as $f) {
                if ($f['formdata']->value) {
                    require_once($CFG->dirroot . '/blocks/learnerscript/components/filters/' .
                        $f['pluginname'] . '/plugin.class.php');
                    $classname = 'block_learnerscript\lsreports\plugin_' . $f['pluginname'];
                    $class = new $classname($this->config);
                    $class->singleselection = true;
                    $this->finalelements = $class->print_filter($mform);
                }
            }
        }
    }
    function initial_basicparams($pluginname) {
        global $CFG;
         require_once($CFG->dirroot . '/blocks/learnerscript/components/filters/' .
            $pluginname . '/plugin.class.php');
        $classname = 'block_learnerscript\lsreports\plugin_' . $pluginname;
        $class = new $classname($this->config);
        $class->singleselection = false;
        $selectoption = false;
        $filterarray = $class->filter_data($selectoption);
        $this->filterdata = $filterarray;
    }

    function add_basicparams_elements(&$mform) {
        global $DB, $CFG;
        $ls = new ls;
        
        $basicparams = (isset($this->basicparams)) ? $this->basicparams : array();
        if (!empty($basicparams)) {
            foreach ($basicparams  as $f) {
                if ($f['name'] == 'status') {
                    if($this->config->type == 'useractivities'){
                        $statuslist = array('all' => 'Select Status',
                                            'notcompleted'=>'Not Completed',
                                            'completed'=> 'Completed');
                    } else if ($this->config->type == 'coursesoverview') {
                        $statuslist = array('all' => 'Select Status',
                                            'inprogress'=>'In Progress',
                                            'completed'=> 'Completed');
                    } else {
                       $statuslist = array('all' => 'Select Status',
                                            'inprogress' => 'In Progress',
                                            'notyetstarted' => 'Not Yet Started',
                                            'completed' => 'Completed');
                    }
                    $this->finalelements = $mform->addElement('select', 'filter_status', '',
                        $statuslist, array('data-select2'=>true));
                }else{
                    require_once($CFG->dirroot . '/blocks/learnerscript/components/filters/' .
                        $f['name'] . '/plugin.class.php');
                    $classname = 'block_learnerscript\lsreports\plugin_' . $f['name'];
                    $class = new $classname($this->config);
                    $class->singleselection = isset($f['singleselection']) ? $f['singleselection'] : true;
                    $class->placeholder = isset($f['placeholder']) ? $f['placeholder'] : true;
                    $class->maxlength = isset($f['maxlength']) ? $f['maxlength'] : 0;
                    $class->required = true;
                    $this->finalelements = $class->print_filter($mform);
                }
            }
        }

    }

    var $filterform = null;

    function check_filters_request($action = null) {
        global $DB, $CFG;

        $filters = (isset($this->componentdata['filters'])) ? $this->componentdata['filters'] : array();

        if (!empty($filters['elements'])) {
            $formdata = new stdclass;
            $request = array_merge($_POST, $_GET);
            if ($request) {
                foreach ($request as $key => $val) {
                    if (strpos($key, 'filter_') !== false) {
                        $formdata->{$key} = $val;
                    }
                }
            }
            $this->instanceid = $this->config->id;

            $filterform = new filter_form($action, $this);

            $filterform->set_data($formdata);
            if ($filterform->is_cancelled()) {
                if ($action) {
                    redirect($action);
                } else {
                    redirect("$CFG->wwwroot/blocks/learnerscript/viewreport.php?id=" .
                        $this->config->id . "&courseid=" . $this->config->courseid);
                }
                die;
            }
            $this->filterform = $filterform;
        }
    }

    function print_filters($return = false) {
        if (!is_null($this->filterform) && !$return) {
            $this->filterform->display();
        } else if (!is_null($this->filterform)) {
            return $this->filterform->render();
        }
    }
    function evaluate_conditions($data, $logic) {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/blocks/learnerscript/reports/evalwise.class.php');
        $logic = trim(strtolower($logic));
        $logic = substr($logic, 0, count($data) * 10);
        $logic = str_replace(array('or', 'and', 'not'), array('+', '*', '-'), $logic);
        $logic = preg_replace('/[^\*c\d\s\+\-()]/i', '', $logic);

        $orig = $dest = array();
        for ($j = count($data); $j > 0; $j--) {
            $orig[] = 'c' . $j;
            $dest[] = $j;
        }
        $logic = str_replace($orig, $dest, $logic);
        $m = new EvalWise();
        $m->set_data($data);
        $result = $m->evaluate($logic);
        return $result;
    }

    function get_calcs($finaltable, $tablehead) {
        global $DB, $CFG;
       
        $calcs = (isset($this->componentdata['calculations']['elements'])) ? $this->componentdata['calculations']['elements'] : array();
        // Calcs doesn't work with multi-rows so far
        $columnscalcs = array();
        $calcstype = array();
        $calcsdatatype = array();
        $finalcalcs = array();
        if (!empty($calcs)) {
            foreach ($calcs as $calc) {
                $calc = (array) $calc;
                $calc['formdata'] = (object)$calc['formdata'];
                $calckey = $calc['formdata']->column;
                $columnscalcs[$calckey] = array();
                $calcstype[$calckey] = $calc['formdata']->columname;
                $calcsdatatype[$calc['id']] = $calc['pluginname'];
            }

            $columnstostore = array_keys($columnscalcs);
            foreach ($finaltable as $r) {
                foreach ($columnstostore as $c) {
                    if (isset($r[$c]))
                        $columnscalcs[$c][] = strip_tags($r[$c]);
                }
            }
            foreach ($calcs as $calc) {
                $calc = (array) $calc;
                $calc['formdata'] = $calc['formdata'];
                require_once($CFG->dirroot . '/blocks/learnerscript/components/calcs/' . $calc['pluginname'] . '/plugin.class.php');
                $classname = 'block_learnerscript\lsreports\plugin_' . $calc['pluginname'];
                $class = new $classname($this->config);
                $calckey = urldecode($calc['formdata']->column);
                $class->columnname = $calckey;
                $result = $class->execute($columnscalcs[$calckey]);
                $datakey = $calckey.'-'.$calc['pluginname'];

                $finalcalcs[$datakey] = $result;
            }
        }
        $calcsclass = new stdClass();
        $calcsclass->head = $calcstype;
        $calcsclass->data = $finalcalcs;
        $calcsclass->calcdata = $calcsdatatype;
        return $calcsclass;
    }

    function elements_by_conditions($conditions) {
        global $DB, $CFG;
        if (empty($conditions['elements'])) {
            $finalelements = $this->get_all_elements();
            return $finalelements;
        }
        $finalelements = array();
        $i = 1;
        foreach ($conditions['elements'] as $c) {
            require_once($CFG->dirroot.'/blocks/learnerscript/components/conditions/'.$c['pluginname'].'/plugin.class.php');
            $classname = 'block_learnerscript\lsreports\plugin_'.$c['pluginname'];
            $class = new $classname($this->config);
            $elements[$i] = $class->execute($c['formdata'], $this->currentuser, $this->currentcourseid);
            $i++;
        }
        if (count($conditions['elements']) == 1) {
            $finalelements = $elements[1];
        } else {
            $logic = $conditions['config']->conditionexpr;
            $finalelements = $this->evaluate_conditions($elements, $logic);
            if ($finalelements === false) {
                return false;
            }
        }
        return $finalelements;
    }

   public function build_query($count = false) {
        $this->init();
        if($count){
            $this->count();
        }else{
            $this->select();
        }
        $this->from();
        $this->joins($count);
        $this->where($count);
        $this->search();
        $this->filters();
        
        if (!$count) {
            $this->groupby();
        }
    }

    function where() {
        if($this->reporttype != 'table'  &&  isset($this->selectedcolumns)){
             $plot = (isset($this->componentdata['plot']['elements']))? $this->componentdata['plot']['elements'] : array();
             foreach ($plot as $e) {
                if($e['id'] == $this->reporttype){
                    if($e['pluginname'] == 'combination'){
                        foreach ($e['formdata']->yaxis_bar as $key) {
                            if(!empty($e['formdata']->{$key}) && method_exists($this, 'column_queries')) {
                               $this->sql .= ' AND (' . $this->column_queries($key,$this->defaultcolumn) . ')'.$e['formdata']->{$key}.''.$e['formdata']->{$key .'_value'}.'';
                            }
                        }
                        foreach ($e['formdata']->yaxis_line as $key) {
                            if(!empty($e['formdata']->{$key}) && method_exists($this, 'column_queries')) {
                           $this->sql .= ' AND (' . $this->column_queries($key,$this->defaultcolumn) . ')'.$e['formdata']->{$key}.''.$e['formdata']->{$key .'_value'}.'';
                            }
                        }
                    }else {
                        if (isset($e['formdata']->yaxis)) {
                            foreach ($e['formdata']->yaxis as $key) {
                                if(!empty($e['formdata']->{$key}) && method_exists($this, 'column_queries')) {
                               $this->sql .= ' AND (' . $this->column_queries($key,$this->defaultcolumn) . ')'.$e['formdata']->{$key}.''.$e['formdata']->{$key .'_value'}.'';
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    function select() {
        if(isset($this->sqlorder['column'])){
            if(method_exists($this, 'column_queries') && $this->column_queries($this->sqlorder['column'], $this->defaultcolumn)){
                $this->sql .= ' ,(' . $this->column_queries($this->sqlorder['column'], $this->defaultcolumn) . ') 
                                as '. $this->sqlorder['column'].'';
            }
        }
    }

    function joins() {
    }
    
    function get_all_elements() {
        global $DB; 
        try {
            $finalelements = $DB->get_records_sql($this->sql, $this->params, $this->start, $this->length);
        } catch (dml_exception $e) {
            $finalelements = array();
        }
        return $finalelements;
    }

    public function create_report($blockinstanceid = null){
        global $DB, $CFG, $USER;
        $context = context_system::instance();
        
        $this->check_permissions($this->userid, $context);
       
        $conditions = (isset($this->componentdata['conditions']['elements']))? $this->componentdata['conditions']['elements'] : array();
        $filters = (isset($this->componentdata['filters']['elements']))? $this->componentdata['filters']['elements'] : array();
        $columns = (isset($this->componentdata['columns']['elements']))? $this->componentdata['columns']['elements'] : array();
        $ordering = (isset($this->componentdata['ordering']['elements']))? $this->componentdata['ordering']['elements'] : array();
        $plot = (isset($this->componentdata['plot']['elements']))? $this->componentdata['plot']['elements'] : array();

        if($this->reporttype !== 'table'){
            $this->graphcolumns = [];
            foreach ($plot as $column) {
                if($column['id'] == $this->reporttype){
                    $this->graphcolumns = $column;
                }
            }
            if(!empty($this->graphcolumns['formdata']->columnsort) && $this->graphcolumns['formdata']->columnsort && $this->graphcolumns['formdata']->sorting){
                $this->sqlorder['column'] = $this->graphcolumns['formdata']->columnsort;
                $this->sqlorder['dir'] = $this->graphcolumns['formdata']->sorting;
            }
            if(!empty($this->graphcolumns['formdata']->limit) && $this->graphcolumns['formdata']->limit){
                $this->length = $this->graphcolumns['formdata']->limit;
            }

            if($this->graphcolumns['pluginname'] == 'combination'){
                $this->selectedcolumns = array_merge([$this->graphcolumns['formdata']->serieid] , 
                                                      $this->graphcolumns['formdata']->yaxis_line, 
                                                      $this->graphcolumns['formdata']->yaxis_bar);
            }else if($this->graphcolumns['pluginname'] == 'pie' || $this->graphcolumns['pluginname'] == 'treemap'){
                $this->selectedcolumns = [$this->graphcolumns['formdata']->areaname , $this->graphcolumns['formdata']->areavalue];
            }else {
                $this->selectedcolumns = array_merge([$this->graphcolumns['formdata']->serieid] , $this->graphcolumns['formdata']->yaxis);
            }
           
        }else{
            if ($this->preview && empty($columns)) {
                $columns = $this->preview_data();
            } 
            $columnnames  = array();
            foreach ($columns as $key=>$column) {
                if (isset($column['formdata']->column)) {
                    $columnnames[$column['formdata']->column] = $column['formdata']->columname;
                    $this->selectedcolumns[] = $column['formdata']->column;
                }
            }
        }
        
        $finalelements = array();
        $sqlorder = '';
        $orderingdata = array();
        if (!empty($this->ordercolumn)) {
            $this->sqlorder['column'] = $this->selectedcolumns[$this->ordercolumn['column']];
            $this->sqlorder['dir'] = $this->ordercolumn['dir'];
        }else if (!empty($ordering)) {
            foreach ($ordering as $o) {
                require_once($CFG->dirroot.'/blocks/learnerscript/components/ordering/' .
                    $o['pluginname'] . '/plugin.class.php');
                $classname = 'block_learnerscript\lsreports\plugin_'.$o['pluginname'];
                $classorder = new $classname($this->config);
                if ($classorder->sql) {
                    $orderingdata = $o['formdata'];
                    $sqlorder = $classorder->execute($orderingdata);
                }
            }
        }
        
        if (!empty($conditions)) {
            $this->conditionsenabled = true;
            $this->conditionfinalelements = $this->elements_by_conditions($this->componentdata['conditions']);
        }
        $this->params['siteid'] = SITEID;
        $this->build_query(true);

    if($this->reporttype == 'table'){
        if(is_siteadmin($this->userid) || (new ls)->is_manager($this->userid, $this->contextlevel, $this->role)){
            try {
                $this->totalrecords = $DB->count_records_sql($this->sql, $this->params);
            } catch (dml_exception $e) {
                $this->totalrecords = 0;
            } 
        }else{
            if($this->rolewisecourses != ''){
                try {
                    $this->totalrecords = $DB->count_records_sql($this->sql, $this->params);
                } catch (dml_exception $e) {
                    $this->totalrecords = 0;
                }    
            }else{
                $this->totalrecords = 0;
            }
        }
    }
        $this->build_query();
        $groupcolumn = isset($this->groupcolumn) ? $this->groupcolumn : $this->defaultcolumn;
        if ($this->config->type != 'userattendance' && $this->config->type != 'attendanceoverview' && $this->config->type != 'monthlysessions' && $this->config->type != 'weeklysessions' && $this->config->type != 'dailysessions' && $this->config->type != 'upcomingactivities' && $this->config->type != 'pendingactivities'){
            // $this->sql .= " GROUP by $groupcolumn ";
            if (is_array($this->sqlorder) && !empty($this->sqlorder)) {
                $this->sql .= " ORDER BY ". $this->sqlorder['column'] .' '. $this->sqlorder['dir'];
            } else {
                if (!empty($sqlorder)) {
                    $this->sql .= " ORDER BY $sqlorder ";
                } else {
                    $this->sql .= " ORDER BY $this->defaultcolumn DESC ";
                }
            }
        }
        if(is_siteadmin($this->userid) || (new ls)->is_manager($this->userid, $this->contextlevel, $this->role) || $this->role == 'manager'){
            $finalelements = $this->get_all_elements();
            $rows = $this->get_rows($finalelements);
        }else{
            if($this->rolewisecourses != ''){
                $finalelements = $this->get_all_elements();
                $rows = $this->get_rows($finalelements);
            }else{
                $rows = [];
            }
        }
        $reporttable = array();
        $tablehead = array();
        $tablealign =array();
        $tablesize = array();
        $tablewrap = array();
        $firstrow = true;
        $pluginscache = array();
        if ($this->config->type == "topic_wise_performance" || $this->config->type == 'cohortusers') {
            $columns = (new ls)->learnerscript_sections_dynamic_columns($columns, $this->config,
                $this->params);
        }
        if ($rows) {
            foreach ($rows as $r) {
                $tempcols = array();
                foreach ($columns as $c) {
                    $c = (array) $c;
                    if (empty($c)) {
                        continue;
                    }
                    require_once($CFG->dirroot . '/blocks/learnerscript/components/columns/' . $c['pluginname'] . '/plugin.class.php');
                    $classname = 'block_learnerscript\lsreports\plugin_' . $c['pluginname'];

                    if (!isset($pluginscache[$classname])) {
                        $class = new $classname($this->config, $c);
                        $pluginscache[$classname] = $class;
                    } else {
                        $class = $pluginscache[$classname];
                    }
                    $class->role = $this->role;
                    $class->colformat = $this->colformat;
                    $class->reportinstance = $blockinstanceid ? $blockinstanceid : $this->config->id;
                    $class->reportfilterparams = $this->params;
                    $rid = isset($r->id) ? $r->id : 0;
                    if (isset($c['formdata']->column) && 
                        (($this->config->type == "topic_wise_performance" || $this->config->type == 'cohortusers') || in_array($c['formdata']->column, $this->selectedcolumns))) {
                            if (!empty($this->params['filter_users'])) {
                                $this->currentuser = $this->params['filter_users'];
                            }
                        if(method_exists($this, 'column_queries')){
                            if(isset($r->course)){
                                $c['formdata']->subquery = $this->column_queries($c['formdata']->column, $rid, $r->course);
                                $this->currentcourseid = $r->course;
                            }else if(isset($r->user)){
                                $c['formdata']->subquery = $this->column_queries($c['formdata']->column, $rid, $r->user);
                            }else{
                                $c['formdata']->subquery = $this->column_queries($c['formdata']->column, $rid);
                            }
                        }
                        $columndata = $class->execute($c['formdata'], $r,$this->userid,
                                                                         $this->currentcourseid,
                                                                         $this->starttime,
                                                                         $this->endtime,
                                                                         $this->reporttype);
                        $tempcols[$c['formdata']->column] = $columndata;
                    }
                    if($firstrow){
                        if(isset($c['formdata']->column)) {
                            $columnheading = !empty($c['formdata']->columname) ? $c['formdata']->columname : $c['formdata']->column;
                            $tablehead[$c['formdata']->column] = $columnheading;
                        }
                        list($align,$size,$wrap) = $class->colformat($c['formdata']);
                        $tablealign[] = $align;
                        $tablesize[] = $size ? $size . '%' : '';
                        $tablewrap[] = $wrap;
                    }
                }

                $firstrow = false;
                $reporttable[] = $tempcols;
            }
        }

        // EXPAND ROWS
        $finaltable = array();
        $newcols = array();
        foreach($reporttable as $row){

            $col = array();
            $multiple = false;
            $nrows = 0;
            $mrowsi = array();

            foreach($row as $key=>$cell){
                if(!is_array($cell)){
                    $col[$key] = $cell;
                }
                else{
                    $multiple = true;
                    $nrows = count($cell);
                    $mrowsi[] = $key;
                }
            }
            if($multiple){
                $newrows = array();
                for($i = 0; $i < $nrows; $i++){
                    $newrows[$i] = $row;
                    foreach($mrowsi as $index){
                        $newrows[$i][$index] = $row[$index][$i];
                    }
                }
                foreach($newrows as $r)
                    $finaltable[] = $r;
            }
            else{
                $finaltable[] = $col;
            }
        }

        if ($blockinstanceid == null)
            $blockinstanceid = $this->config->id;

        // Make the table, head, columns, etc...

        $table = new stdClass;
        $table->id = 'reporttable_' . $blockinstanceid . '';
        $table->data = $finaltable;
        $table->head = $tablehead;
        $table->size = $tablesize;
        $table->align = $tablealign;
        $table->wrap = $tablewrap;
        $table->width = (isset($this->componentdata['columns']['config']))? $this->componentdata['columns']['config']->tablewidth : '';
        $table->summary = $this->config->summary;
        $table->tablealign = (isset($this->componentdata['columns']['config']))? $this->componentdata['columns']['config']->tablealign : 'center';
        $table->cellpadding = (isset($this->componentdata['columns']['config']))? $this->componentdata['columns']['config']->cellpadding : '5';
        $table->cellspacing = (isset($this->componentdata['columns']['config']))? $this->componentdata['columns']['config']->cellspacing : '1';
        $table->class = (isset($this->componentdata['columns']['config']))? $this->componentdata['columns']['config']->class : 'generaltable';
                // CALCS
       if($this->calculations){
            $finalheadcalcs = $this->get_calcs($finaltable, $tablehead);
            $finalcalcs = $finalheadcalcs->data;
            $calcs = new html_table();
            $calcshead = array();
            // $filterheads = array();
            $calcshead[] = 'Column Name';

            foreach ($finalheadcalcs->calcdata as $key=>$head) {
                    $calcshead[$head] = ucfirst(get_string($head, 'block_learnerscript'));
                    $calcshead1[$head] = $key;
            }
            $calcsdata = array();
            foreach ($finalheadcalcs->head as $key => $head) {
                $row =array();
                $row [] = $columnnames[$key];
                foreach ($calcshead1 as  $key1=>$value){
                    if(array_key_exists($key.'-'.$key1,$finalcalcs)){
                        $row [] = $finalcalcs[$key.'-'.$key1];
                    } else{
                        $row [] = 'N/A';
                    }
                }
                $calcsdata [] = $row;
            }

            $calcs->data = $calcsdata;
            $calcs->head = $calcshead;
            $calcs->size = $tablesize;
            $calcs->align = $tablealign;
            $calcs->wrap = $tablewrap;
            $calcs->summary = $this->config->summary;
            $calcs->attributes['class'] = (isset($this->componentdata['columns']['config'])) ? $this->componentdata['columns']['config']->class : 'generaltable';
            $this->finalreport = new stdClass();
            $this->finalreport->calcs = $calcs;
       }
        if(!$this->finalreport) {
            $this->finalreport = new stdClass;
        }
        $this->finalreport->table = $table;
        
        return true;
    }
    public function utf8_strrev($str) {
        preg_match_all('/./us', $str, $ar);
        return join('', array_reverse($ar[0]));
    }
    public function preview_data() {
        global $CFG, $DB;
        $allcolumns = $this->columns;
        $columns = array();
        $componentcolumns = get_list_of_plugins('blocks/learnerscript/components/columns');
        foreach ($allcolumns as $key => $c) {
            if (in_array($key, array_values($componentcolumns))) {
                require_once($CFG->dirroot . '/blocks/learnerscript/components/columns/' . $key . '/plugin.class.php');
                $pluginclassname = 'block_learnerscript\lsreports\plugin_' . $key;
                $pluginclass = new $pluginclassname(null);

                if ($pluginclass->type == 'advanced') {
                    require_once($CFG->dirroot . '/blocks/learnerscript/components/columns/' . $key . '/form.php');
                    $pluginclassformname = $key . '_form';
                    $compclass = new component_columns($this->config->id);
                    $pluginclassform = new $pluginclassformname(null, array('compclass' => $compclass));
                    $previewcolumns = $pluginclassform->advanced_columns();
                    foreach ($previewcolumns as $preview => $previewcolumn) {
                        $data = array();
                        $data['id'] = random_string(15);
                        $data['pluginname'] = $key;
                        $data['pluginfullname'] = get_string($key, 'block_learnerscript');
                        $data['summary'] = '';
                        $data['type'] = 'selectedcolumns';
                        $list = new stdClass;
                        $list->value = 0;
                        $list->columname = $previewcolumn;
                        $list->column = $preview;
                        $list->heading = $key;
                        $data['formdata'] = $list;
                        $columns[] = $data;
                    }
                } else {
                    foreach ($c as $value) {
                        $data = array();
                        $data['id'] = random_string(15);
                        $data['pluginname'] = $key;
                        $data['pluginfullname'] = get_string($key, 'block_learnerscript');
                        $data['summary'] = '';
                        $data['type'] = 'selectedcolumns';
                        $list = new stdClass;
                        $list->value = 0;
                        $list->columname = $value;
                        $list->column = $value;
                        $list->heading = $key;
                        $data['formdata'] = $list;
                        $columns[] = $data;
                    }
                }
            }
        }
        return $columns;
    }

    public function setup_conditions() {
        global $CFG, $DB;
        $conditionsdata = array();
        if (isset($this->components->conditions->elements)) {
            foreach ($this->components->conditions->elements as $key => $value) {
                $conditionsdata[] = $value['formdata'];
            }
        }

        $plugins = get_list_of_plugins('blocks/learnerscript/components/conditions');

        $conditionscolumns = array();
        $conditionscolumns['elements'] = array();
        $conditionscolumns['config'] = array();
        foreach ($plugins as $p) {
            require_once($CFG->dirroot . '/blocks/learnerscript/components/conditions/' . $p . '/plugin.class.php');
            $pluginclassname = 'block_learnerscript\lsreports\plugin_' . $p;
            $columns = array();
            $pluginclass = new $pluginclassname($this->config->type);
            if (in_array($this->config->type, $pluginclass->reporttypes)) {
                if ($pluginclass->unique && in_array($p, $currentplugins)) {
                  //  continue;
                }
                $uniqueid = random_string(15);
                $reportclassname = 'block_learnerscript\lsreports\report_' . $this->config->type;
                $report = $DB->get_record_sql("SELECT * FROM {block_learnerscript} WHERE id = " . $this->config->id);
                $properties = new stdClass();
                $reportclass = new $reportclassname($report, $properties);
                while (strpos($reportclass->config->components, $uniqueid) !== false) {
                    $uniqueid = random_string(15);
                }
                $columns['id'] = $uniqueid;
                $columns['formdata'] = $conditionsdata;
                $columns['value'] = (in_array($p, $conditionsdata)) ? true : false;
                $columns['pluginname'] = $p;
                if (method_exists($pluginclass, 'columns')) {
                    $columns['plugincolumns'] = $pluginclass->columns();
                } else {
                    $columns['plugincolumns'] = [];
                }
                $columns['form'] = $pluginclass->form;
                $columns['allowedops'] = $pluginclass->allowedops;
                $columns['pluginfullname'] = get_string($p, 'block_learnerscript');
                $columns['summery'] = get_string($p, 'block_learnerscript');
                $conditionscolumns['elements'][$p] = $columns;
            }
        }
        $conditionscolumns['conditionssymbols'] = array("=", ">", "<", ">=", "<=", "<>", "LIKE", "NOT LIKE", "LIKE % %");

        if (!empty($this->componentdata['conditions']['elements'])) {
            $finalelements = array();
            $finalelements['elements'] = array();
            $finalelements['selectedfields'] = array();
            $finalelements['selectedcondition'] = array();
            $finalelements['selectedvalue'] = array();
            $finalelements['sqlcondition'] = urldecode($this->componentdata['conditions']['config']->conditionexpr);
            foreach ($this->componentdata['conditions']['elements'] as $element) {
                $finalelements['elements'][] = $element['pluginname'];
                $finalelements['selectedfields'][] = $element['pluginname'] . ':' . $element['formdata']->field;
                $finalelements['selectedcondition'][$element['pluginname'] . ':' . $element['formdata']->field] = urldecode($element['formdata']->operator);
                $finalelements['selectedvalue'][$element['pluginname'] . ':' . $element['formdata']->field] = urldecode($element['formdata']->value);
            }
            $conditionscolumns['finalelements'] = $finalelements;
        }
        return $conditionscolumns;
    }
    public function rolewisecourses() {
        global $DB;
        if(!is_siteadmin($this->userid) && !(new ls)->is_manager($this->userid, $this->contextlevel, $this->role)) {
            if(!empty($this->componentdata['permissions']['elements'])){
                $roleincourse = array_filter($this->componentdata['permissions']['elements'], function($permission){
                   //Role in course permission
                    if($permission['pluginname'] == 'roleincourse'){
                        return true;
                    } 
                 });
            }
            if(!empty($roleincourse)){
                $currentroleid = $DB->get_field('role', 'id', ['shortname' => $this->role]);

                foreach ($roleincourse as $role) {
                    if(!empty($this->role) && (!isset($role['formdata']->contextlevel) || $role['formdata']->roleid != $currentroleid)) {
                        continue;
                    }
                    $permissionslib = new permissionslib($role['formdata']->contextlevel, 
                                                         $role['formdata']->roleid,
                                                         $this->userid);
                    $rolecontexts = $DB->get_records_sql("SELECT DISTINCT CONCAT(r.id, '@', rcl.id), 
                        r.shortname, rcl.contextlevel 
                        FROM {role} AS r 
                        JOIN {role_context_levels} AS rcl ON rcl.roleid = r.id AND rcl.contextlevel NOT IN (70)
                        WHERE 1 = 1  
                        ORDER BY rcl.contextlevel ASC");
                    foreach ($rolecontexts as $rc) {
                       if ($rc->contextlevel == 10 && ($rc->shortname == 'manager')) {
                        continue;
                       }
                       $rcontext[] = $rc->shortname .'_'.$rc->contextlevel;
                    }
                    $permissionslib->moodleroles = $rcontext;
                    if($permissionslib->has_permission()){
                          return implode(',', $permissionslib->get_rolewise_courses());
                      break;
                    }
                    
                }
            }
        }
    }
}

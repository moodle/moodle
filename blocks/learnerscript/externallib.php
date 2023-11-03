<?php
require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot . '/blocks/learnerscript/lib.php');
global $CFG, $DB, $USER, $OUTPUT, $PAGE;
use block_learnerscript\local\ls;
use block_learnerscript\local\reportbase;
use block_learnerscript\local\schedule;
use context_system as contextsystem;
use block_learnerscript_licence_setting as lssetting;
use tool_usertours\tour;
class block_learnerscript_external extends external_api {
    public static function rolewiseusers_parameters() {
        return new external_function_parameters(
            array(
                'roleid' => new external_value(PARAM_INT, 'role id of report', false),
                'term' => new external_value(PARAM_TEXT, 'Current search term in search box', false),
                'contextlevel' => new external_value(PARAM_INT, 'contextlevel of role', false),
                'page' => new external_value(PARAM_INT, 'Current page number to request', false),
                '_type' => new external_value(PARAM_RAW, 'A "request type" will be usually a query', false),
                'reportid' => new external_value(PARAM_RAW, 'Report id of report', false),
                'action' => new external_value(PARAM_TEXT, 'action', false),
                'maximumSelectionLength' => new external_value(PARAM_INT, 'maximum selection length to search', false),
                'courses' => new external_value(PARAM_INT, 'Course id of report', false)
            )
        );
    }
    public static function rolewiseusers($roleid, $term, $contextlevel, $page, $_type, $reportid, $action, $maximumSelectionLength, $courses) {
        global $PAGE, $DB, $CFG;
        $context = contextsystem::instance();
        $PAGE->set_context($context);
        $roles = $roleid;
        $search = $term;
        if ((has_capability('block/learnerscript:managereports', $context) ||
            has_capability('block/learnerscript:manageownreports', $context) ||
            is_siteadmin()) && !empty($roles)) {
            if ($roles == -1) {
                if ($CFG->dbtype == 'pgsql') {
                    $adminssql = "SELECT u.id, CONCAT(u.firstname, ' ' , u.lastname) AS fullname
                                    FROM {user} u, {config} cfg
                                   WHERE cfg.name = :siteadmins
                                     AND u.id::TEXT IN (cfg.value)";
                } else {
                    $adminssql = "SELECT u.id, CONCAT(u.firstname, ' ' , u.lastname) AS fullname
                                    FROM {user} u, {config} cfg
                                   WHERE cfg.name = :siteadmins
                                     AND u.id IN (cfg.value)";
                }
                $admins = $DB->get_records_sql($adminssql, ['siteadmins' => 'siteadmins']);
                $user_list = array();
                foreach ($admins as $admin) {
                    $user_list[] = ['id' => $admin->id, 'text' => $admin->fullname];
                }
            } else {
                $user_list = (new schedule)->rolewiseusers($roles, $term, $page, $reportid,$contextlevel);
            }
            $terms_data = array();
            $terms_data['total_count'] = sizeof($user_list);
            $terms_data['incomplete_results'] = false;
            $terms_data['items'] = $user_list;
            $return = $terms_data;
        } else {
            $terms_data = array();
            $terms_data['error'] = true;
            $terms_data['type'] = 'Warning';
            if (empty($roles)) {
                $terms_data['cap'] = false;
                $terms_data['msg'] = get_string('missingparam', 'block_learnerscript', 'Role');
            } else {
                $terms_data['cap'] = true;
                $terms_data['msg'] = get_string('badpermissions', 'block_learnerscript');
            }
            $terms_data['total_count'] = 0;
            $terms_data['incomplete_results'] = false;
            $terms_data['items'] = array();
            $return = $terms_data;
        }
        $data = json_encode($return);
        return $data;
    }

    public static function rolewiseusers_returns() {
        return new external_value(PARAM_RAW, 'data');
    }

    public static function roleusers_parameters() {
        return new external_function_parameters(
            array(
                'reportid' => new external_value(PARAM_INT, 'Report id of report', false),
                'scheduleid' => new external_value(PARAM_INT, 'selected schedule for report', false),
                'selectedroleid' => new external_value(PARAM_RAW, 'selected role for report', false),
                'roleid' => new external_value(PARAM_RAW, 'roleid for report', false),
                'contextlevel' => new external_value(PARAM_INT, 'contextlevel of role', false),
                'term' => new external_value(PARAM_TEXT, 'Current search term in search box', false),
                'type' => new external_value(PARAM_TEXT, 'A "request type" will be usually a query', false),
                'bullkselectedusers' => new external_value(PARAM_RAW, 'bulk users selected', false),
            )
        );
    }
    public static function roleusers($reportid, $scheduleid, $selectedroleid, $roleid,$contextlevel, $term, $type, $bullkselectedusers) {
        global $PAGE, $DB, $CFG;
        $roleid = json_decode($roleid);
        $bullkselectedusers = json_decode($bullkselectedusers);
        $context = contextsystem::instance();
        if ((has_capability('block/learnerscript:managereports', $context) ||
            has_capability('block/learnerscript:manageownreports', $context) ||
            is_siteadmin()) && !empty($reportid) && !empty($type) && !empty($roleid)) {
            if ($roleid == -1) {
                 $escselsql = "";
                if ($bullkselectedusers) {
                    $bullkselectedusersdata = implode(',', $bullkselectedusers);
                    $escselsql = " AND u.id NOT IN ($bullkselectedusersdata) ";
                }
                if ($CFG->dbtype == 'pgsql') {
                    $adminssql = "SELECT u.id, CONCAT(u.firstname, ' ' , u.lastname) AS fullname
                                FROM {user} u, {config} cfg
                               WHERE cfg.name = :siteadmins
                                 AND u.id::TEXT IN (cfg.value) $escselsql";
                } else {
                    $adminssql = "SELECT u.id, CONCAT(u.firstname, ' ' , u.lastname) AS fullname
                                FROM {user} u, {config} cfg
                               WHERE cfg.name = :siteadmins
                                 AND u.id IN (cfg.value) $escselsql";
                }
                $admins = $DB->get_records_sql($adminssql, ['siteadmins' => 'siteadmins']);
                $userslist = array();
                foreach ($admins as $admin) {
                    $userslist[] = ['id' => $admin->id, 'fullname' => $admin->fullname];
                }
            } else {
                $userslist = (new schedule)->schroleusers($reportid, $scheduleid, $type,
                                                    $roleid, $term, $bullkselectedusers, $contextlevel);
            }
            $terms_data = array();
            $terms_data['total_count'] = sizeof($userslist);
            $terms_data['incomplete_results'] = false;
            $terms_data['items'] = $userslist;
            $return = $terms_data;
        } else {
            $terms_data = array();
            $terms_data['error'] = true;
            $terms_data['type'] = 'Warning';
            if (empty($reportid)) {
                $terms_data['cap'] = false;
                $terms_data['msg'] = get_string('missingparam', 'block_learnerscript', 'ReportID');
            } else if (empty($type)) {
                $terms_data['cap'] = false;
                $terms_data['msg'] = get_string('missingparam', 'block_learnerscript', 'Type');
            } else if (empty($roles)) {
                $terms_data['cap'] = false;
                $terms_data['msg'] = get_string('missingparam', 'block_learnerscript', 'Role');
            } else {
                $terms_data['cap'] = true;
                $terms_data['msg'] = get_string('badpermissions', 'block_learnerscript');
            }
            $return = $terms_data;
        }
        $data = json_encode($return);
        return $data;
    }

    public static function roleusers_returns() {
        return new external_value(PARAM_RAW, 'data');
    }

    public static function viewschuserstable_parameters() {
        return new external_function_parameters(
            array(
                'reportid' => new external_value(PARAM_INT, 'Report id of report', false),
                'scheduleid' => new external_value(PARAM_INT, 'selected schedule for report', false),
                'schuserslist' => new external_value(PARAM_RAW, 'list of scheduled users', false),
            )
        );
    }
    public static function viewschuserstable($reportid, $scheduleid, $schuserslist) {
        global $PAGE;
        $context = contextsystem::instance();
        if ((has_capability('block/learnerscript:managereports', $context) ||
            has_capability('block/learnerscript:manageownreports', $context) ||
            is_siteadmin()) && !empty($schuserslist)) {
            $stable = new stdClass();
            $stable->table = true;
            $return = (new schedule)->viewschusers($reportid, $scheduleid, $schuserslist, $stable);
        } else {
            $terms_data = array();
            $terms_data['error'] = true;
            $terms_data['type'] = 'Warning';
            if (empty($schuserslist)) {
                $terms_data['cap'] = false;
                $terms_data['msg'] = get_string('missingparam', 'block_learnerscript', 'Schedule Users List');
            } else {
                $terms_data['cap'] = true;
                $terms_data['msg'] = get_string('badpermissions', 'block_learnerscript');
            }
            $return = $terms_data;
        }
        $data = json_encode($return);
        return $data;
    }

    public static function viewschuserstable_returns() {
        return new external_value(PARAM_RAW, 'data');
    }
    public static function manageschusers_is_allowed_from_ajax() {
        return true;
    }
    public static function manageschusers_parameters() {
        return new external_function_parameters(
            array(
                'reportid' => new external_value(PARAM_INT, 'report id of report', false),
                'scheduleid' => new external_value(PARAM_RAW, 'schedule id', false),
                'schuserslist' => new external_value(PARAM_RAW, '', false),
                'selectedroleid' => new external_value(PARAM_RAW, 'selected role id', false),
                'reportinstance' => new external_value(PARAM_INT, 'report instance', false),

            )
        );
    }
    public static function manageschusers($reportid, $scheduleid, $schuserslist, $selectedroleid, $reportinstance) {
        global $PAGE, $OUTPUT, $CFG;
        $context = contextsystem::instance();
        $PAGE->set_context($context);
        if ((has_capability('block/learnerscript:managereports', $context) ||
            has_capability('block/learnerscript:manageownreports', $context) ||
            is_siteadmin()) && !empty($reportid)) {
            $roles_list = (new schedule)->reportroles($selectedroleid, $reportid);
            $selectedusers = (new schedule)->selectesuserslist($schuserslist);
            $reqimage = $OUTPUT->image_url('req');
            $scheduledata = new \block_learnerscript\output\scheduledusers($reportid, $reqimage, $roles_list, $selectedusers, $scheduleid, $reportinstance);
            $learnerscript = $PAGE->get_renderer('block_learnerscript');
            $return = $learnerscript->render($scheduledata);
        } else {
            $terms_data = array();
            $terms_data['error'] = true;
            $terms_data['type'] = 'Warning';
            if (empty($reportid)) {
                $terms_data['cap'] = false;
                $terms_data['msg'] = get_string('missingparam', 'block_learnerscript', 'ReportID');
            } else {
                $terms_data['cap'] = true;
                $terms_data['msg'] = get_string('badpermissions', 'block_learnerscript');
            }
            $return = $terms_data;
        }
        $data = json_encode($return);
        return $data;
    }
    public static function manageschusers_returns() {
        return new external_value(PARAM_RAW, 'data');
    }

    public static function schreportform_parameters() {
        return new external_function_parameters(
            array(
                'reportid' => new external_value(PARAM_INT, 'report id of report', false),
                'instance' => new external_value(PARAM_INT, 'Instance', false),
                'schuserslist' => new external_value(PARAM_RAW, 'List of scheduled users', false),
            )
        );
    }
    public static function schreportform($reportid, $instance, $schuserslist) {
        global $PAGE, $OUTPUT, $CFG, $DB;
        $PAGE->set_context(contextsystem::instance());
        $context = contextsystem::instance();
        if ((has_capability('block/learnerscript:managereports', $context) ||
            has_capability('block/learnerscript:manageownreports', $context) ||
            is_siteadmin()) && !empty($reportid)) {
            require_once($CFG->dirroot . '/blocks/learnerscript/components/scheduler/schedule_form.php');
            $roles_list = (new schedule)->reportroles('', $reportid);
            list($schusers, $schusersids) = (new schedule)->userslist($reportid, $scheduleid);
            $exportoptions = (new ls)->cr_get_export_plugins();
            $frequencyselect = (new schedule)->get_options();
            $scheduledreport = $DB->get_record('block_ls_schedule', array('id' => $scheduleid));
            if (!empty($scheduledreport)) {
                $schedule_list = (new schedule)->getschedule($scheduledreport->frequency);
            } else {
                $schedule_list = array(null => '--SELECT--');
            }
            $scheduleform = new scheduled_reports_form($CFG->wwwroot . '/blocks/learnerscript/components/scheduler/schedule.php', array('id' => $reportid, 'scheduleid' => $scheduleid, 'AjaxForm' => true, 'roles_list' => $roles_list,
                'schusers' => $schusers, 'schusersids' => $schusersids, 'exportoptions' => $exportoptions, 'schedule_list' => $schedule_list, 'frequencyselect' => $frequencyselect, 'instance' => $instance));
            $return = $scheduleform->render();
        } else {
            $terms_data = array();
            $terms_data['error'] = true;
            $terms_data['type'] = 'Warning';
            if (empty($reportid)) {
                $terms_data['cap'] = false;
                $terms_data['msg'] = get_string('missingparam', 'block_learnerscript', 'ReportID');
            } else {
                $terms_data['cap'] = true;
                $terms_data['msg'] = get_string('badpermissions', 'block_learnerscript');
            }
            $return = $terms_data;
        }
        $data = json_encode($return);
        return $data;
    }

    public static function schreportform_returns() {
        return new external_value(PARAM_RAW, 'data');
    }

    public static function scheduledtimings_parameters() {
        return new external_function_parameters(
            array(
                'action' => new external_value(PARAM_TEXT, 'action', false),
                'reportid' => new external_value(PARAM_INT, 'report id of report', false),
                'search' => new external_value(PARAM_TEXT, 'search value', false),
                'length' => new external_value(PARAM_INT, 'length of string', false),
                'courseid' => new external_value(PARAM_INT, 'The id for the course', false)
            )
        );
    }
    public static function scheduledtimings($reportid, $courseid, $start, $length, $search) {
        global $PAGE, $OUTPUT;
        $context = contextsystem::instance();
        $learnerscript = $PAGE->get_renderer('block_learnerscript');
        if ((has_capability('block/learnerscript:managereports', $context) ||
            has_capability('block/learnerscript:manageownreports', $context) ||
            is_siteadmin()) && !empty($reportid)) {
            $return = $learnerscript->schedulereportsdata($reportid, $courseid, false, $start, $length, $search['value']);
        } else {
            $terms_data = array();
            $terms_data['error'] = true;
            $terms_data['type'] = 'Warning';
            if (empty($reportid)) {
                $terms_data['cap'] = false;
                $terms_data['msg'] = get_string('missingparam', 'block_learnerscript', 'ReportID');
            } else {
                $terms_data['cap'] = true;
                $terms_data['msg'] = get_string('badpermissions', 'block_learnerscript');
            }
            $return = $terms_data;
        }
        $data = json_encode($return);
        return $data;
    }

    public static function scheduledtimings_returns() {
        return new external_value(PARAM_RAW, 'data');
    }

    public static function generate_plotgraph_parameters() {
        return new external_function_parameters(
            array(
                'reportid' => new external_value(PARAM_INT, 'report id of report', false),
                'courseid' => new external_value(PARAM_INT, 'course id of course', false),
                'cmid' => new external_value(PARAM_INT, 'The course module id for the course', false),
                'status' => new external_value(PARAM_TEXT, 'status', false),
                'userid' => new external_value(PARAM_RAW, 'user id', false),
                'ls_fstartdate' => new external_value(PARAM_RAW, 'start date for date filter', false),
                'ls_fenddate' => new external_value(PARAM_RAW, 'end date for date filter', false),
                'reporttype' => new external_value(PARAM_RAW, 'type of report', false),
                'action' => new external_value(PARAM_RAW, 'action', false),
                'singleplot' => new external_value(PARAM_RAW, 'single plot', false),
                'cols' => new external_value(PARAM_RAW, 'columns', false),
                'instanceid' => new external_value(PARAM_RAW, 'id of instance', false),
                'container' => new external_value(PARAM_RAW, 'container', false),
                'filters' => new external_value(PARAM_RAW, 'applied filters', false),
                'basicparams' => new external_value(PARAM_RAW, 'basic params required to generate graph', false),
                'columnDefs' => new external_value(PARAM_RAW, 'column definitions', false),
                'reportdashboard' => new external_value(PARAM_RAW, 'report dashboard', false, true)
            )
        );
    }
    public static function generate_plotgraph($reportid, $courseid, $cmid, $status, $userid,
        $ls_fstartdate, $ls_fenddate, $reporttype, $action, $singleplot, $cols, $instanceid,
        $container, $filters, $basicparams, $columnDefs, $reportdashboard) {
        global $DB, $PAGE, $CFG;

        $ls = new ls();

        $filters =  json_decode($filters,true);
        $basicparams =  json_decode($basicparams,true);
        if (empty($basicparams)) {
            $basicparams = array();
        }
        $PAGE->set_context(contextsystem::instance());
        $learnerscript = $PAGE->get_renderer('block_learnerscript');

        if (!$report = $DB->get_record('block_learnerscript', array('id' => $reportid))) {
            print_error('reportdoesnotexists', 'block_learnerscript');
        }

        $properties = new stdClass();
        $properties->ls_startdate = !empty($filters['ls_fstartdate']) ? $filters['ls_fstartdate'] : 0;
        $properties->ls_enddate   = !empty($filters['ls_fenddate']) ? $filters['ls_fenddate'] : time();
        $reportclass = $ls->create_reportclass($reportid, $properties);
        $reportclass->params = array_merge( $filters, (array)$basicparams);
        $reportclass->cmid = $cmid;
        $reportclass->courseid = isset($courseid) ? $courseid : (isset($reportclass->params['filter_courses']) ? $reportclass->params['filter_courses'] : SITEID);
        $reportclass->status = $status;
        if ($reporttype != 'table') {
            $reportclass->start = 0;
            $reportclass->length = -1;
            $reportclass->reporttype = $reporttype;
        }
        if($reportdashboard && $report->type == 'statistics'){
            $reportdatatable = false;
        }else{
            $reportdatatable = true;
        }

        $reportclass->create_report();

        if ($reportdatatable && $reporttype == 'table') {
            $datacolumns = array();
            $columnDefs = array();
            $i = 0;
            $re = array();
            if (!empty($reportclass->orderable)) {
                $re = array_diff(array_keys($reportclass->finalreport->table->head), $reportclass->orderable);
            }
            if(empty($reportclass->finalreport->table->data)) {
                $return['tdata'] = '<div class="alert alert-info">' . get_string("nodataavailable", "block_learnerscript") . '</div>';
                $return['reporttype'] = 'table';
                $return['emptydata'] = 1;
                $reporttitle = get_string('report_' . $report->type, 'block_learnerscript');
                $return['reportname'] = (new ls)->get_reporttitle($reporttitle, $basicparams);
            } else {
                foreach ($reportclass->finalreport->table->head as $key => $value) {
                    $datacolumns[]['data'] = $value;
                    $columnDef = new stdClass();
                    $align = isset($reportclass->finalreport->table->align[$i]) ? $reportclass->finalreport->table->align[$i] : 'left';
                    $wrap = isset($reportclass->finalreport->table->wrap[$i]) && ($reportclass->finalreport->table->wrap[$i] == 'wrap') ? 'break-all' : 'normal';
                    $width = isset($reportclass->finalreport->table->size[$i]) ? $reportclass->finalreport->table->size[$i] : '';
                    $columnDef->className = 'dt-body-' . $align;
                    $columnDef->targets = $i;
                    $columnDef->wrap = $wrap;
                    $columnDef->width = $width;
                    if (!empty($re[$i]) && $re[$i])  {
                        $columnDef->orderable = false;
                    } else {
                        $columnDef->orderable = true;
                    }
                    $columnDefs[] = $columnDef;
                    $i++;
                }
                $export = explode(',', $reportclass->config->export);
                if (!empty($reportclass->finalreport->table->head)) {
                    $tablehead = (new ls)->report_tabledata($reportclass->finalreport->table);
                    $reporttable = new \block_learnerscript\output\reporttable($tablehead,
                        $reportclass->finalreport->table->id,
                        $export,
                        $reportid,
                        $reportclass->sql,
                        false,
                        false,
                        $instanceid,
                        $report->type
                    );
                    $return = array();
                    foreach ($reportclass->finalreport->table->data as $key => $value) {
                        $data[$key] = array_values($value);
                    }
                    $return['tdata'] = $learnerscript->render($reporttable);
                    $return['data'] =   array(
                                            "draw" => true,
                                            "recordsTotal" => $reportclass->totalrecords,
                                            "recordsFiltered" => $reportclass->totalrecords,
                                            "data" => $data
                                        );
                    $reporttitle = get_string('report_' . $report->type, 'block_learnerscript');
                    $return['reportname'] = (new ls)->get_reporttitle($reporttitle, $basicparams);
                    $return['columnDefs'] = $columnDefs;
                    $return['reporttype'] = 'table';
                    $return['emptydata'] = 0;
                } else {
                    $return['emptydata'] = 1;
                    $return['reporttype'] = 'table';
                    $return['tdata'] = '<div class="alert alert-info">' . get_string("nodataavailable", "block_learnerscript") . '</div>';
                }
            }
        } else {
            if ($report->type != 'statistics') {
                $seriesvalues = (isset($reportclass->componentdata['plot']['elements'])) ? $reportclass->componentdata['plot']['elements'] : array();
                $i = 0;
                $reporttitle = get_string('report_' . $report->type, 'block_learnerscript');
                $return['reportname'] = (new ls)->get_reporttitle($reporttitle, $basicparams);
                foreach ($seriesvalues as $g) {
                    if (($reporttype != '' && $g['id'] == $reporttype) || $i == 0) {
                        $return['plot'] = (new ls)->generate_report_plot($reportclass, $g);
                        if ($reporttype != '' && $g['id'] == $reporttype) {
                            break;
                        }
                    }
                    $return['plotoptions'][] = array('id' => $g['id'], 'title' => $g['formdata']->chartname, 'pluginname' => $g['pluginname']);
                    $i++;
                }
            } else {
                if ($reporttype == 'pie') {
                    foreach ($reportclass->finalreport->table->data[0] as $k => $r) {
                        $r = strip_tags($r);
                        if (is_numeric($r)) {
                            $piedata[] = ['name' => $reportclass->finalreport->table->head[$k], 'y' => $r];
                        }
                    }
                } elseif ($reporttype == 'solidgauge') {
                    $radius = 112;
                    $innerRadius = 88;
                    $colors = ['#90ed7d', 'rgb(67, 67, 72)', 'rgb(124, 181, 236)'];
                    foreach ($reportclass->finalreport->table->data[0] as $k => $r) {
                        $r = strip_tags($r);
                        $radius = $radius-25;
                        $innerRadius = $innerRadius-25;
                        if (is_numeric($r)) {
                            $piedata[] = ['name' => $reportclass->finalreport->table->head[$k],
                                          'data'=> [[ 'color'=> $colors[$k],'radius' =>$radius.'%','innerRadius'=> $innerRadius.'%' ,'y' => $r]]];
                        }
                    }
                }else{
                    $i = 0;
                    $categorydata = array();
                    if (!empty($reportclass->finalreport->table->data[0])) {
                        foreach ($reportclass->finalreport->table->data[0] as $k => $r) {
                                $r = strip_tags($r);
                                $r = is_numeric($r) ? $r : $r;
                                $seriesdata[] = $reportclass->finalreport->table->head[$k];
                                $graphdata[$i][] = $r;
                                $categorydata[] = $reportclass->finalreport->table->head[$k];
                                $i++;
                        }
                    }
                    $comdata = array();
                    $comdata['dataLabels'] = ['enabled' => 1];
                    $comdata['borderRadius'] = 5;
                    if(!empty($graphdata)) {
                        $i = 0;
                        foreach ($graphdata as $key => $value) {
                            if ($reporttype == 'table') {
                                $comdata['data'][] = [$value[0]];
                            } else {
                                $comdata['data'][] = ['y' => $value[0], 'label' => $value[0]];
                            }
                        $i++;
                        }
                        $piedata = [$comdata];
                    } else {
                        $piedata = $comdata;
                    }
                }
                $return['plot'] =  ['type' => $reporttype,
                                    'containerid' => 'reportcontainer' . $instanceid . '',
                                    'name' => $report->name,
                                    'categorydata' => $categorydata,
                                    'tooltip' => '{point.y}',
                                    'datalabels' => 1,
                                    'showlegend' => 0,
                                    'id' => '{point.y}',
                                    'height' => '210',
                                    'data' => $piedata];
                $return['plotoptions'][] = array('id' => random_string(5), 'title' => $report->name, 'pluginname' => $reporttype);
            }
        }
        if ($reporttype == 'table') {
            $data = json_encode($return, JSON_PRESERVE_ZERO_FRACTION);
        }else{
            $data = json_encode($return, JSON_NUMERIC_CHECK);
        }
        return $data;
    }

    public static function generate_plotgraph_returns() {
        return new external_value(PARAM_RAW, 'data');
    }

    public static function pluginlicence_parameters() {
        return new external_function_parameters(
            array(
                'licencekey' => new external_value(PARAM_RAW, 'licencekey', false),
                'expireddate' => new external_value(PARAM_RAW, 'expiry date', false)
            )
        );
    }
    public static function pluginlicence($licencekey, $expireddate) {
        if (!empty($expireddate) && !empty($licencekey)) {
            $explodedatetime = explode(' ', $expireddate);
            $explodedate = explode('-', $explodedatetime[0]);
            $explodetime = explode(':', $explodedatetime[1]);
            $expireddate = mktime($explodetime[0], $explodetime[1], $explodetime[2], $explodedate[1], $explodedate[2], $explodedate[0]);
            $return = (new schedule)->insert_licence($licencekey, $expireddate);
        } else {
            $terms_data = array();
            $terms_data['error'] = true;
            $terms_data['cap'] = false;
            $terms_data['type'] = 'Warning';
            $terms_data['msg'] = get_string('licencemissing', 'block_learnerscript');
            $return = $terms_data;
        }

        $data = json_encode($return);

        return $data;
    }

    public static function pluginlicence_returns() {
        return new external_value(PARAM_RAW, 'data');
    }

    public static function frequency_schedule_parameters() {
        return new external_function_parameters(
            array(
                'frequency' => new external_value(PARAM_INT, 'schedule frequency', false)
            )
        );
    }
    public static function frequency_schedule($frequency) {
        $return = (new schedule)->getschedule($frequency);
        if (empty($return)) {
            $return = array(null => '--SELECT--');
        }
        $data = json_encode($return);
        return $data;
    }

    public static function frequency_schedule_returns() {
        return new external_value(PARAM_RAW, 'data');
    }

    public static function reportobject_parameters() {
        return new external_function_parameters(
            array(
                'reportid' => new external_value(PARAM_INT, 'The context id for the course', false)
            )
        );
    }
    public static function reportobject($reportid) {
        global $DB, $CFG;
        if (!$report = $DB->get_record('block_learnerscript', array('id' => $reportid))) {
            print_error('reportdoesnotexists', 'block_learnerscript');
        }
        require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');
        $reportclassname = 'block_learnerscript\lsreports\report_' . $report->type;
        $reportclass = new $reportclassname($report);
        $reportclass->create_report();
        $return = (new ls)->cr_unserialize($reportclass->config->components);
        $data = json_encode($return);
        return $data;
    }

    public static function reportobject_returns() {
        return new external_value(PARAM_RAW, 'data');
    }

    public static function advancedcolumns_parameters() {
        return new external_function_parameters(
            array(
                'reportid' => new external_value(PARAM_RAW, 'report id of the report', false),
                'component' => new external_value(PARAM_TEXT, 'available components', false),
                'advancedcolumn' => new external_value(PARAM_INT, 'advanced columns', false),
                'jsonformdata' => new external_value(PARAM_INT, 'json form data', false)
            )
        );

    }
    public static function advancedcolumns($reportid, $component, $advancedcolumn, $jsonformdata) {
        global $CFG, $DB, $USER, $OUTPUT, $PAGE;
        $advancedcolumn = "coursefield";
        $component = "columns";
        $args = new stdClass();
        $args->reportid = $reportid;
        $args->component = $component;
        $args->pname = $advancedcolumn;
        $args->jsonformdata = 'jsondata';

        $return = block_learnerscript_plotforms_ajaxform($args);

        $data = json_encode($return);

        return $data;
    }
    public static function advancedcolumns_returns() {
        return new external_value(PARAM_RAW, 'data');
    }

    public static function reportcalculations_parameters() {
        return new external_function_parameters(
            array(
                'reportid' => new external_value(PARAM_RAW, 'report id of report', false)
            )
        );

    }
    public static function reportcalculations($reportid, $context) {
        global $CFG, $DB, $USER, $OUTPUT, $PAGE;
        $reportid = 1;
        $context = contextsystem::instance();

        $checkpermissions = (new reportbase($reportid))->check_permissions($USER->id, $context);
        if ((has_capability('block/learnerscript:managereports', $context) || has_capability('block/learnerscript:manageownreports', $context) || !empty($checkpermissions)) && !empty($reportid)) {
            $properties = new stdClass();
            $properties->start = 0;
            $properties->length = -1;
            $reportclass = (new ls)->create_reportclass($reportid, $properties);
            $reportclass->create_report();
            $table = html_writer::table($reportclass->finalreport->calcs);
            $reportname = $DB->get_field('block_learnerscript', 'name', array('id' => $reportid));
            $return = ['table' => $table, 'reportname' => $reportname];
        } else {
            $terms_data = array();
            $terms_data['error'] = true;
            $terms_data['type'] = 'Warning';
            if (empty($reportid)) {
                $terms_data['cap'] = false;
                $terms_data['msg'] = get_string('missingparam', 'block_learnerscript', 'ReportID');
            } else {
                $terms_data['cap'] = true;
                $terms_data['msg'] = get_string('badpermissions', 'block_learnerscript');
            }
            $return = $terms_data;
        }
        $data = json_encode($return);
        return $data;
    }

    public static function reportcalculations_returns() {
        return new external_value(PARAM_RAW, 'data');
    }

    public static function updatereport_conditions_parameters() {
        return new external_function_parameters(
            array(
                'reportid' => new external_value(PARAM_RAW, 'report id of report', false),
                'conditionsdata' => new external_value(PARAM_RAW, 'conditions used in report', false)
            )
        );

    }
    public static function updatereport_conditions($reportid, $conditionsdata) {
        global $CFG, $DB, $USER, $OUTPUT, $PAGE;
        if (!$report = $DB->get_record('block_learnerscript', array('id' => $reportid))) {
            print_error('reportdoesnotexists', 'block_learnerscript');
        }
        $conditionsdata = json_decode($conditionsdata);
        $conditions = array();
        $conditions['elements'] = array();
        $sqlcon = array();
        $i = 1;
        foreach ($conditionsdata->selectedfields as $elementstr) {

            $element = explode(':', $elementstr);

            $columns = array();
            $columns['id'] = random_string();
            $columns['formdata'] = (object) ['field' => $element[1],
                'operator' => $conditionsdata->selectedcondition->{$elementstr},
                'value' => $conditionsdata->selectedvalue->{$elementstr},
                'submitbutton' => get_string('add')];
            $columns['pluginname'] = $element[0];
            $columns['pluginfullname'] = get_string($element[0], 'block_learnerscript');
            $columns['summary'] = get_string($element[0], 'block_learnerscript');
            $conditions['elements'][] = $columns;
            $sqlcon[] = 'c' . $i;
            $i++;
        }

        $conditions['config'] = (object) ['conditionexpr' => ($conditionsdata->sqlcondition) ? strtolower($conditionsdata->sqlcondition) : implode(' and ', $sqlcon),
            'submitbutton' => get_string('update')];

        $unserialize = (new ls)->cr_unserialize($report->components);
        $unserialize['conditions'] = $conditions;

        $unserialize = (new ls)->cr_serialize($unserialize);
        $DB->update_record('block_learnerscript', (object) ['id' => $reportid, 'components' => $unserialize]);
        $data = null;
        return $data;
    }

    public static function updatereport_conditions_returns() {
        return new external_value(PARAM_RAW, 'data');
    }

    public static function plotforms_parameters() {
        return new external_function_parameters(
            array(
                'reportid' => new external_value(PARAM_RAW, 'report id of report', false),
                'context' => new external_value(PARAM_RAW, 'The context id for the course', false),
                'component' => new external_value(PARAM_RAW, 'The context id for the course', false),
                'pname' => new external_value(PARAM_RAW, 'The context id for the course', false),
                'jsonformdata' => new external_value(PARAM_RAW, 'json form data', false),
                'cid' => new external_value(PARAM_RAW, 'The id for the course', false)
            )
        );
    }
    public static function plotforms($reportid, $context, $component, $pname, $jsonformdata, $cid) {
        global $CFG, $DB, $USER, $OUTPUT, $PAGE;
        $context = contextsystem::instance();

        $args = new stdClass();
        $args->context = $context;
        $args->reportid = $reportid;
        $args->component = $component;
        $args->pname = $pname;
        $args->cid = $cid;
        $args->jsonformdata = $jsonformdata;
        $return = block_learnerscript_plotforms_ajaxform($args);
        $data = json_encode($return);
        return $data;
    }

    public static function plotforms_returns() {
        return new external_value(PARAM_RAW, 'data');
    }

    public static function designdata_parameters() {
        return new external_function_parameters(
            array(
                'frequency' => new external_value(PARAM_INT, 'The context id for the course', false)
            )
        );
    }
    public static function designdata($reportid) {
        global $DB, $CFG;
        $return = array();
        if (!$report = $DB->get_record('block_learnerscript', array('id' => $reportid))) {
            print_error('reportdoesnotexists', 'block_learnerscript');
        }
        require_once $CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php';
        $reportclassname = 'block_learnerscript\lsreports\report_' . $report->type;
        $reportclass = new $reportclassname($report);
        $reportclass->create_report(null, 0, 10);
        $components = unserialize($reportclass->config->components);
        $startTime = microtime(true);
        if ($report->type == 'sql') {
            $rows = $reportclass->get_rows(0, 10);
            $return['rows'] = $rows['rows'];
            $reportclass->columns = get_object_vars($return['rows'][0]);
            $reportclass->columns = array_keys($reportclass->columns);
        } else {
            if (!isset($reportclass->columns)) {
                $availablecolumns = (new ls)->report_componentslist($report, 'columns');
            } else {
                $availablecolumns = $reportclass->columns;
            }
            $reportTable = $reportclass->get_all_elements(0, 10);
            $return['rows'] = $reportclass->get_rows($reportTable[0]);
        }

        $return['reportdata'] = json_encode($r, JSON_FORCE_OBJECT);
        $return['time'] = "reportdata Time:  " . number_format((microtime(true) - $startTime), 4) . " Seconds\n";
        /*
         *Calculations data
         */
        $comp = 'calcs';
        $plugins = get_list_of_plugins('blocks/learnerscript/components/' . $comp);
        $optionsplugins = array();
        foreach ($plugins as $p) {
            require_once $CFG->dirroot . '/blocks/learnerscript/components/' . $comp . '/' . $p . '/plugin.class.php';
            $pluginclassname = 'block_learnerscript\lsreports\plugin_' . $p;
            $pluginclass = new $pluginclassname($report);
            if (in_array($report->type, $pluginclass->reporttypes)) {
                if ($pluginclass->unique && in_array($p, $currentplugins)) {
                    continue;
                }
                $optionsplugins[get_string($p, 'block_learnerscript')] = $p;
            }
        }
        asort($optionsplugins);
        $return['calculations'] = $optionsplugins;
        $return['time'] .= "Calcluations Time:  " . number_format((microtime(true) - $startTime), 4) . " Seconds\n";
        //Selected columns
        $activecolumns = array();

        if (isset($components['columns']['elements'])) {
            foreach ($components['columns']['elements'] as $key => $value) {
                $value = (array) $value;
                $components['columns']['elements'][$key] = (array) $components['columns']['elements'][$key];

                $components['columns']['elements'][$key]['formdata']->columname = urldecode($value['formdata']->columname);
                $activecolumns[] = $value['formdata']->column;
            }
            $return['selectedcolumns'] = $components['columns']['elements'];
        } else {
            $return['selectedcolumns'] = array();
        }

        $return['time'] .= "Selected columns Time:  " . number_format((microtime(true) - $startTime), 4) . " Seconds\n";
        //========{conditions}===========
        $conditionsdata = array();
        if (isset($components->conditions->elements)) {
            foreach ($components->conditions->elements as $key => $value) {
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
            $pluginclass = new $pluginclassname($report);
            if (in_array($report->type, $pluginclass->reporttypes)) {
                if ($pluginclass->unique && in_array($p, $currentplugins)) {
                    continue;
                }
                $uniqueid = random_string(15);
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
                    $columns['plugincolumns'] = array();
                }

                $columns['pluginfullname'] = get_string($p, 'block_learnerscript');
                $columns['summery'] = get_string($p, 'block_learnerscript');
                $conditionscolumns['elements'][$p] = $columns;
            }
        }
        $conditionscolumns['conditionssymbols'] = array("=", ">", "<", ">=", "<=", "<>", "LIKE", "NOT LIKE", "LIKE % %");
        if (!empty($components['conditions']['elements'])) {
            $finalelements = array();
            $finalelements['elements'] = array();
            $finalelements['selectedfields'] = array();
            $finalelements['selectedcondition'] = array();
            $finalelements['selectedvalue'] = array();
            $finalelements['sqlcondition'] = urldecode($components['conditions']['config']->conditionexpr);
            foreach ($components['conditions']['elements'] as $element) {
                $finalelements['elements'][] = $element['pluginname'];
                $finalelements['selectedfields'][] = $element['pluginname'] . ':' . $element['formdata']->field;
                $finalelements['selectedcondition'][$element['pluginname'] . ':' . $element['formdata']->field] = urldecode($element['formdata']->operator);
                $finalelements['selectedvalue'][$element['pluginname'] . ':' . $element['formdata']->field] = urldecode($element['formdata']->value);
            }
            $conditionscolumns['finalelements'] = $finalelements;
        }
        $return['conditioncolumns'] = $conditionscolumns;
        $return['time'] .= "Conditions Time:  " . number_format((microtime(true) - $startTime), 4) . " Seconds\n";
        //========{conditions end}===========

        //Filters
        $filterdata = array();
        if (isset($components['filters']['elements'])) {
            foreach ($components['filters']['elements'] as $key => $value) {
                $value = (array) $value;
                if ($value['formdata']->value) {
                    $filterdata[] = $value['pluginname'];
                }
            }
        }
        $filterplugins = get_list_of_plugins('blocks/learnerscript/components/filters');
        $filteroptions = array();
        $filterplugins = $reportclass->filters;
        foreach ($filterplugins as $p) {
            require_once($CFG->dirroot . '/blocks/learnerscript/components/filters/' . $p . '/plugin.class.php');
            if (file_exists($CFG->dirroot . '/blocks/learnerscript/components/filters/' . $p . '/form.php')) {
                continue;
            }
            $pluginclassname = 'block_learnerscript\lsreports\plugin_' . $p;
            $pluginclass = new $pluginclassname($report);
            if (in_array($report->type, $pluginclass->reporttypes)) {
                $uniqueid = random_string(15);
                while (strpos($reportclass->config->components, $uniqueid) !== false) {
                    $uniqueid = random_string(15);
                }
                $filtercolumns = array();
                $filtercolumns['id'] = $uniqueid;
                $filtercolumns['pluginname'] = $p;
                $filtercolumns['pluginfullname'] = get_string($p, 'block_learnerscript');
                $filtercolumns['summary'] = '';
                $columnss['name'] = get_string($p, 'block_learnerscript');
                $columnss['type'] = 'filters';
                $columnss['value'] = (in_array($p, $filterdata)) ? true : false;
                $filtercolumns['formdata'] = $columnss;
                $filterelements[] = $filtercolumns;
            }
        }
        $return['filtercolumns'] = $filterelements;
        $return['time'] .= "Filters Time:  " . number_format((microtime(true) - $startTime), 4) . " Seconds\n";
        //Ordering
        $comp = 'ordering';
        $plugins = get_list_of_plugins('blocks/learnerscript/components/' . $comp);
        $orderingplugin = array();
        foreach ($plugins as $p) {
            require_once($CFG->dirroot . '/blocks/learnerscript/components/' . $comp . '/' . $p . '/plugin.class.php');
            $pluginclassname = 'block_learnerscript\lsreports\plugin_' . $p;
            $pluginclass = new $pluginclassname($report);
            if (in_array($report->type, $pluginclass->reporttypes)) {
                $orderingplugin[$p] = get_string($p, 'block_learnerscript');
            }
        }
        asort($orderingplugin);
        $orderingdata = array();
        foreach ($orderingplugin as $key => $value) {
            $mstring = str_replace('fieldorder', '', $key);
            $tblcolumns = $DB->get_columns($mstring);
            $ordering = array();
            $ordering['column'] = $value;
            $ordering['type'] = 'Ordering';
            $ordering['ordercolumn'] = $key;
            $ordering['orderingcolumn'] = array_keys($tblcolumns);
            $orderingdata[] = $ordering;
        }
        $return['ordercolumns'] = (isset($components['ordering']['columns']) &&
            !empty($components['ordering']['columns'])) ? $components['ordering']['columns'] :
        $orderingdata;
        $return['time'] .= "Order columns Time:  " . number_format((microtime(true) - $startTime), 4) . " Seconds\n";
        //Columns
        if ($report->type == 'sql') {
            $columns = array();
            foreach ($reportclass->columns as $value) {
                $c = [];
                $uniqueid = random_string(15);
                while (strpos($reportclass->config->components, $uniqueid) !== false) {
                    $uniqueid = random_string(15);
                }
                $c['id'] = $uniqueid;
                $c['pluginname'] = 'sql';
                $c['pluginfullname'] = 'SQL';
                $c['summary'] = '';
                $c['type'] = 'columns';
                if (in_array($value, $activecolumns)) {
                    $columns['value'] = true;
                } else {
                    $columns['value'] = false;
                }
                $columns['columname'] = $value;
                $columns['column'] = $value;
                $columns['heading'] = '';
                $columns['wrap'] = '';
                $columns['align'] = '';
                $columns['size'] = '';
                $c['formdata'] = $columns;
                $elements[] = $c;
            }
        } else {
            $comp = 'columns';
            $cid = '';
            require_once($CFG->dirroot . '/blocks/learnerscript/components/columns/component.class.php');
            $compclass = new component_columns($report->id);
            $i = 0;
            foreach ($availablecolumns as $key => $values) {
                if (!isset($reportclass->columns)) {
                    $c = [];
                    $c['formdata']->column = $key;
                    $elements[] = $c;
                } else {
                    $columns = array();
                    foreach ($values as $value) {
                        $c = [];
                        $uniqueid = random_string(15);
                        while (strpos($reportclass->config->components, $uniqueid) !== false) {
                            $uniqueid = random_string(15);
                        }
                        $c['id'] = $uniqueid;
                        $c['pluginname'] = $key;
                        $c['pluginfullname'] = get_string($key, 'block_learnerscript');
                        $c['summary'] = '';
                        $c['type'] = 'columns';
                        if (in_array($value, $activecolumns)) {
                            $columns['value'] = true;
                        } else {
                            $columns['value'] = false;
                        }
                        $columns['columname'] = $value;
                        $columns['column'] = $value;
                        $columns['heading'] = $key;
                        $c['formdata'] = $columns;
                        $elements[] = $c;
                    }
                }
                $i++;
            }
        }
        $return['availablecolumns'] = $elements;
        $return['time'] .= "Available col Time:  " . number_format((microtime(true) - $startTime), 4) . " Seconds\n";
        if (!empty($components['calculations']['elements'])) {
            foreach ($components['calculations']['elements'] as $k => $ocalc) {
                $ocalc = (array) $ocalc;
                $calcpluginname[$ocalc['id']] = $ocalc['pluginname'];
            }
        } else {
            $components['calculations']['elements'] = array();
            $calcpluginname = array();
        }
        $return['calcpluginname'] = $calcpluginname;
        $return['calccolumns'] = $components['calculations']['elements'];
        //exports
        $exporttypes = array('pdf', 'csv', 'xls', 'ods');
        $exportlists = array();
        foreach ($exporttypes as $key => $exporttype) {
            $list = array();
            $list['name'] = $exporttype;
            if (in_array($exporttype, explode(',', $report->export))) {
                $list['value'] = true;
            } else {
                $list['value'] = false;
            }
            $exportlists[] = $list;
        }
        $return['exportlist'] = $exportlists;
        $return['time'] .= "Export Time:  " . number_format((microtime(true) - $startTime), 4) . " Seconds\n";

        $data = json_encode($return);
        return $data;
    }

    public static function designdata_returns() {
        return new external_value(PARAM_RAW, 'data');
    }
    public static function deletecomponenet_parameters() {
        return new external_function_parameters(
            array(
                'reportid' => new external_value(PARAM_INT, 'ReportID', false),
                'action' => new external_value(PARAM_TEXT, 'The context id for the course', false),
                'comp' => new external_value(PARAM_RAW, 'The context id for the course', false),
                'pname' => new external_value(PARAM_RAW, 'The context id for the course', false),
                'cid' => new external_value(PARAM_RAW, 'The context id for the course', false),
                'delete' => new external_value(PARAM_INT, 'Confirm Delete', false)
            )
        );

    }
    public static function deletecomponenet($reportid, $action, $comp, $pname, $cid, $delete) {
        global $CFG, $DB, $USER, $OUTPUT, $PAGE;
        if (!$report = $DB->get_record('block_learnerscript', array('id' => $reportid))){
            print_error('reportdoesnotexists', 'block_learnerscript');
        }
        $components = (new ls)->cr_unserialize($report->components);
        $elements = isset($components[$comp]['elements']) ? $components[$comp]['elements'] : array();
        if(count($elements) == 1 && $report->disabletable == 1){
            $success['success'] = true;
            $success['disabledelete'] = true;
        }else{
            foreach ($elements as $index => $e) {
                if ($e['id'] == $cid) {
                    if ($delete) {
                        unset($elements[$index]);
                        break;
                    }
                    $newindex = ($moveup) ? $index - 1 : $index + 1;
                    $tmp = $elements[$newindex];
                    $elements[$newindex] = $e;
                    $elements[$index] = $tmp;
                    break;
                }
            }
            $components[$comp]['elements'] = $elements;
            $report->components = (new ls)->cr_serialize($components);
            try {
                $DB->update_record('block_learnerscript', $report);
                $success['success'] = true;
                $success['disabledelete'] = false;
            } catch(exception $e) {
                $success['success'] = false;
                $success['disabledelete'] = false;
            }
        }
        return $success;
    }
    public static function deletecomponenet_returns() {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_RAW, 'success message'),
                'disabledelete' => new external_value(PARAM_RAW, 'message')
            )
        );
    }

    public static function reportfilterform_is_allowed_from_ajax() {
        return true;
    }
    public static function reportfilterform_parameters() {
        return new external_function_parameters(
            array(
                'action' => new external_value(PARAM_TEXT, 'The context id for the course', false),
                'reportid' => new external_value(PARAM_INT, 'ReportID', false),
                'instance' => new external_value(PARAM_INT, 'instanceID', false)
            )
        );

    }
    public static function reportfilterform($action, $reportid, $instance) {
        global $CFG, $DB, $USER, $OUTPUT, $PAGE;
        $context = contextsystem::instance();
        $PAGE->set_context($context);
            $reportrecord = new \block_learnerscript\local\reportbase($reportid);
            $reportrecord->customheader = true; // For not to display Form Header
            $filterrecords = (new ls)->cr_unserialize($reportrecord->config->components);
            if (!empty($filterrecords['filters']['elements'])) {
                $filtersarray = $filterrecords;
            } else {
                $filtersarray = array();
            }
            $reportrecord->instanceid = $instance;
            $filterform = new block_learnerscript\form\filter_form(null, $reportrecord);
            $reportfilterform = $filterform->render();
        return $reportfilterform;
    }

    public static function reportfilterform_returns() {
        return new external_value(PARAM_RAW, 'reportfilterform');
    }
    public static function importreports_parameters() {
        return new external_function_parameters(
            array(
                'total' => new external_value(PARAM_INT, 'Total reports', 0),
                'current' => new external_value(PARAM_INT, 'Current Report Position', 0),
                'errorreportspositiondata' => new external_value(PARAM_RAW, 'error report positions', 0),
                'lastreportposition' => new external_value(PARAM_INT, 'Last Report Position', 0)
            )
        );
    }
    public static function importreports($total, $current, $errorreportspositiondata = array(), $lastreportposition = 0) {
        global $CFG, $DB, $USER, $OUTPUT, $PAGE;
        $context = contextsystem::instance();

        $path = $CFG->dirroot . '/blocks/learnerscript/backup/';
        $learnerscriptreports = glob($path . '*.xml');
        $course = get_course(SITEID);

        if ($lastreportposition > 0) {
            $errorreportsposition = unserialize($errorreportspositiondata);
            foreach ($learnerscriptreports as $k => $learnerscriptreport) {
                if ((!empty($errorreportsposition) && in_array($k, $errorreportsposition)) || $k >= $lastreportposition) {
                    $finalreports[$k] = $learnerscriptreport;
                }
            }

            $position = $current;
            $importurl =  $finalreports[$position];
            $data = array();
            if (file_exists($finalreports[$position])
                && pathinfo($finalreports[$position], PATHINFO_EXTENSION) == 'xml') {
                $filedata = file_get_contents($importurl);
                $status = (new ls)->cr_import_xml($filedata, $course, false, true);
                if ($status) {
                    $data['import'] = true;
                } else {
                    $data['import'] = false;
                }
                $event = \block_learnerscript\event\import_report::create(array(
                    'objectid' => $position,
                    'context' => $context,
                    'other' => array('reportid' => $status,
                                     'status' => $data['import'],
                                     'position' => $position)
                ));
                $event->trigger();
                $currentposition = array_search($position, array_keys($finalreports));
                $nextposition = $currentposition + 1;
                $percent = $nextposition/$total * 100;
                $data['percent'] = round($percent, 0);
                $data['current'] = array_keys($finalreports)[$nextposition];
            }
        } else {
            $position = $current - 1;
            $finalreports = $learnerscriptreports;

            $importurl =  $finalreports[$position];
            $data = array();
            if (file_exists($finalreports[$position])
                && pathinfo($finalreports[$position], PATHINFO_EXTENSION) == 'xml') {
                $filedata = file_get_contents($importurl);
                $status = (new ls)->cr_import_xml($filedata, $course, false, true);
                if ($status) {
                    $data['import'] = true;
                } else {
                    $data['import'] = false;
                }
                $event = \block_learnerscript\event\import_report::create(array(
                    'objectid' => $position,
                    'context' => $context,
                    'other' => array('reportid' => $status,
                                     'status' => $data['import'],
                                     'position' => $position)
                ));
                $event->trigger();

                $percent = $current/$total * 100;
                $data['percent'] = round($percent, 0);
            }
        }

        $pluginsettings = new lssetting('block_learnerscript/lsreportconfigstatus',
                'lsreportconfigstatus', get_string('lsreportconfigstatus', 'block_learnerscript'), '', PARAM_BOOL, 2);
        $totallsreports = $DB->count_records('block_learnerscript');
        if (count($learnerscriptreports) <= $totallsreports) {
            $pluginsettings->config_write('lsreportconfigstatus', true);
        } else {
            $pluginsettings->config_write('lsreportconfigstatus', false);
        }

        $data = json_encode($data);
        return $data;
    }

    public static function importreports_returns() {
        return new external_value(PARAM_RAW, 'data');
    }

    public static function lsreportconfigimport_parameters() {
        return new external_function_parameters(
            array(
            )
        );
    }
    public static function lsreportconfigimport() {
        global $CFG, $DB, $USER, $OUTPUT, $PAGE;
        $pluginsettings = new lssetting('block_learnerscript/lsreportconfigimport',
                    'lsreportconfigimport', get_string('lsreportconfigimport', 'block_learnerscript'), '', PARAM_INT, 2);
        $return = $pluginsettings->config_write('lsreportconfigimport', 0);
        $data = json_encode($return);
        return $data;
    }

    public static function lsreportconfigimport_returns() {
        return new external_value(PARAM_RAW, 'data');
    }
    public static function resetlsconfig_parameters() {
        return new external_function_parameters(
            array(
                'step' => new external_value(PARAM_INT, 'Step', 0),
            )
        );
    }
    public static function resetlsconfig($step) {
        global $CFG, $DB, $USER, $OUTPUT, $PAGE;
        $PAGE->set_context(contextsystem::instance());
        switch ($step) {
            case 1:
                $DB->delete_records('logstore_standard_log',
                                        array('objecttable' => 'block_learnerscript'));

                $DB->delete_records('block_learnerscript');
                $DB->delete_records('block_ls_schedule');
                $return = array('next' => 2, 'percent' => 25);
            break;
            case 2:
                $blockinstancessql = "SELECT id
                                        FROM {block_instances}
                                        WHERE (pagetypepattern LIKE :pagetypepattern
                                        OR blockname = :blockname)";
                $blockinstances = $DB->get_fieldset_sql($blockinstancessql, ['pagetypepattern' => '%blocks-reportdashboard%', 'blockname' => 'coursels']);

                if (!empty($blockinstances)) {
                    blocks_delete_instances($blockinstances);
                }
                $return = array('next' => 3, 'percent' => 50);
            break;
            case 3:
                $usertours = $CFG->dirroot . '/blocks/learnerscript/usertours/';
                $usertoursjson = glob($usertours . '*.json');

                foreach ($usertoursjson as $usertour) {
                    $data = file_get_contents($usertour);
                    $tourconfig = json_decode($data);
                    $tourid = $DB->get_field('tool_usertours_tours', 'id', array('name' => $tourconfig->name));
                    if ($tourid > 0) {
                        $tour = tour::instance($tourid);
                        $tour->remove();
                    }
                }
                $return = array('next' => 4, 'percent' => 75);
            break;
            case 4:
                set_config('lsreportconfigstatus', 0, 'block_learnerscript');
                set_config('lsreportconfigimport', 0, 'block_learnerscript');
                $return = array('next' => 0, 'percent' => 100);
            break;
            default:
                $return = array('next' => 0, 'percent' => 0);
            break;
        }
        $data = json_encode($return);
        return $data;
    }
    public static function resetlsconfig_returns() {
        return new external_value(PARAM_RAW, 'data');
    }

    public static function filtercourses_parameters() {
        return new external_function_parameters(
            array(
                'action' => new external_value(PARAM_TEXT, 'action', false),
                'maximumSelectionLength' => new external_value(PARAM_INT, 'maximum selection length to search', false),
                'term' => new external_value(PARAM_TEXT, 'Current search term in search box', false),
                '_type' => new external_value(PARAM_RAW, 'A "request type" will be usually a query', false),
                'fiterdata' => new external_value(PARAM_RAW, 'fiterdata', false),
                'basicparamdata' => new external_value(PARAM_RAW, 'basicparamdata', false),
                'reportinstanceid' => new external_value(PARAM_INT, 'reportid', false),
                'courses' => new external_value(PARAM_RAW, 'Course id of report', false)
            )
        );
    }
    public static function filtercourses($action, $maximumSelectionLength, $term, $_type, $fiterdata, $basicparamdata, $reportinstanceid, $courses) {
        global $PAGE, $DB, $CFG;
        $context = contextsystem::instance();
        $PAGE->set_context($context);
        $search = $term;

        $filters =  json_decode($fiterdata,true);
        $basicparams =  json_decode($basicparamdata,true);
        $filterdata = array_merge($filters, $basicparams);
        $report = $DB->get_record('block_learnerscript', array('id' => $reportinstanceid));
        $reportclass = new stdClass();
        if(!empty($report) && $report->type){
            require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');
            $reportclassname = 'block_learnerscript\lsreports\report_' . $report->type;
            $properties = new stdClass;
            $reportclass = new $reportclassname($report, $properties);
        }
        $pluginclass = new stdClass();
        $pluginclass->report = new stdClass();
        $pluginclass->report->type = 'custom';
        $pluginclass->reportclass = $reportclass;
        $courseoptions = (new \block_learnerscript\local\querylib)->filter_get_courses($pluginclass, true, $search, $filterdata, $_type, false, $courses);
        $terms_data = array();
        $terms_data['total_count'] = sizeof($courseoptions);
        $terms_data['incomplete_results'] = false;
        $terms_data['items'] = $courseoptions;
        $return = $terms_data;
        $data = json_encode($return);
        return $data;
    }
    public static function filtercourses_returns() {
        return new external_value(PARAM_RAW, 'data');
    }
    public static function filterusers_parameters() {
        return new external_function_parameters(
            array(
                'action' => new external_value(PARAM_TEXT, 'action', false),
                'maximumSelectionLength' => new external_value(PARAM_INT, 'maximum selection length to search', false),
                'term' => new external_value(PARAM_TEXT, 'Current search term in search box', false),
                '_type' => new external_value(PARAM_RAW, 'A "request type" will be usually a query', false),
                'fiterdata' => new external_value(PARAM_RAW, 'fiterdata', false),
                'basicparamdata' => new external_value(PARAM_RAW, 'basicparamdata', false),
                'reportinstanceid' => new external_value(PARAM_INT, 'reportinstanceid', false),
                'courses' => new external_value(PARAM_INT, 'Course id of report', false)
            )
        );
    }
    public static function filterusers($action, $maximumSelectionLength, $term, $_type, $fiterdata, $basicparamdata, $reportinstanceid, $courses) {
        global $PAGE, $DB, $CFG;
        $context = contextsystem::instance();
        $PAGE->set_context($context);
        $search = $term;

        $filters =  json_decode($fiterdata,true);
        $basicparams =  json_decode($basicparamdata,true);

        $filterdata = array_merge($filters, $basicparams);

        $report = $DB->get_record('block_learnerscript', array('id' => $reportinstanceid));
        $reportclass = new stdClass();
        if(!empty($report) && $report->type){
            require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');
            $reportclassname = 'block_learnerscript\lsreports\report_' . $report->type;
            $properties = new stdClass;
            $reportclass = new $reportclassname($report, $properties);
        }
        $pluginclass = new stdClass();
        $pluginclass->report = new stdClass();
        $pluginclass->report->type = 'custom';
        $pluginclass->reportclass = $reportclass;
        $courseoptions = (new \block_learnerscript\local\querylib)->filter_get_users($pluginclass, true, $search, $filterdata, SITEID, $_type, $courses);
        $terms_data = array();
        $terms_data['total_count'] = sizeof($courseoptions);
        $terms_data['incomplete_results'] = false;
        $terms_data['items'] = $courseoptions;
        $return = $terms_data;
        $data = json_encode($return);
        return $data;
    }
    public static function filterusers_returns() {
        return new external_value(PARAM_RAW, 'data');
    }
}

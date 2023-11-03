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
 * LearnerScript Dashboard block plugin installation.
 *
 * @package    block_reportdashboard
 * @author     Arun Kumar Mukka
 * @copyright  2018 eAbyas Info Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');

use block_learnerscript\local\ls;
use block_learnerscript\local\reportbase;
use block_reportdashboard\local\reportdashboard;
global $CFG, $DB, $USER, $OUTPUT, $COURSE;
require_login();
class block_reportdashboard_external extends external_api {
    public static function userlist_parameters() {
        return new external_function_parameters(
            array(
                'term' => new external_value(PARAM_TEXT, 'The current search term in the search box', false, ''),
                '_type' => new external_value(PARAM_TEXT, 'A "request type", default query', false, ''),
                'query' => new external_value(PARAM_TEXT, 'Query', false, ''),
                'action' => new external_value(PARAM_TEXT, 'Action', false, ''),
                'userlist' => new external_value(PARAM_TEXT, 'Users list', false, ''),
                'reportid' => new external_value(PARAM_INT, 'Report ID', false, 0),
                'maximumSelectionLength' => new external_value(PARAM_INT, 'Maximum Selection Length to Search', false, 0),
                'setminimumInputLength' => new external_value(PARAM_INT, 'Minimum Input Length to Search', false, 2),
                'courses' => new external_value(PARAM_RAW, 'Course id of report', false)
            )
        );
    }
    public static function userlist($term, $_type, $query, $action, $userlist, $reportid, $maximumSelectionLength, $setminimumInputLength, $courses) {
        global $DB;
        $users = $DB->get_records_sql("SELECT * FROM {user} WHERE id > 2 AND deleted = 0 AND (firstname LIKE '%" . $term . "%' OR lastname LIKE '%" . $term . "%' OR username LIKE '%" . $term . "%' OR email LIKE '%" . $term . "%' )");
        $reportclass = (new ls)->create_reportclass($reportid);
        $reportclass->courseid = $reportclass->config->courseid;
        if ($reportclass->config->courseid == SITEID) {
            $context = context_system::instance();
        } else {
            $context = context_course::instance($reportclass->config->courseid);
        }
        $data = array();
        $permissions = (isset($reportclass->componentdata['permissions'])) ? $reportclass->componentdata['permissions'] : array();
        $contextlevel = $_SESSION['ls_contextlevel'];
        $role = $_SESSION['role'];
        foreach ($users as $user) {
            if ($user->id > 2) {
                $rolewiseusers = "SELECT  u.*  
                                FROM {user} AS u
                                JOIN {role_assignments}  AS lra ON lra.userid = u.id 
                                JOIN {role} AS r ON r.id = lra.roleid
                                JOIN {context} AS ctx ON ctx.id  = lra.contextid
                                WHERE u.confirmed = 1 AND u.suspended = 0  AND u.deleted = 0 AND u.id = $user->id AND ctx.contextlevel = :contextlevel AND r.shortname = ':role'"; 
                if(isset($role) && ($role == 'manager' || $role == 'editingteacher' || $role == 'teacher' || $role == 'student') && ($contextlevel == CONTEXT_COURSE)){
                        if ($courses <> SITEID) {
                            $rolewiseusers .= " AND ctx.instanceid = :courses";
                        }
                }
                $params = ['contextlevel' => $contextlevel, 'role' => $role, 'courses' => $courses];
                $rolewiseuser = $DB->get_record_sql($rolewiseusers, $params);
                if (!empty($rolewiseuser)) {
                    $contextlevel = $_SESSION['ls_contextlevel'];
                    $userroles = (new ls)->get_currentuser_roles($rolewiseuser->i, $contextleveld);
                    $reportclass->userroles = $userroles;
                    if ($reportclass->check_permissions($rolewiseuser->id, $context)) {
                        $data[] = ['id' => $rolewiseuser->id, 'text' => fullname($rolewiseuser)];
                    }
                }
            } else {
                $userroles = (new ls)->get_currentuser_roles($user->id);
                $reportclass->userroles = $userroles;
                if ($reportclass->check_permissions($user->id, $context)) {
                    $data[] = ['id' => $user->id, 'text' => fullname($user)];
               }
           } 
        }
        $return = ['total_count' => count($data), 'items' => $data];
        $data = json_encode($return);
        return $data;
    }
    public static function userlist_returns() {
        return new external_value(PARAM_RAW, 'data');
    }

    public static function reportlist_parameters() {
        return new external_function_parameters(
            array(
                'search' => new external_value(PARAM_RAW, 'Search value', false, ''),
            )
        );
    }
    public static function reportlist($search) {
        $context = context_system::instance();
        $search = 'admin';
        $sql = "SELECT id, name FROM {block_learnerscript} WHERE visible = 1 AND name LIKE '%$search%'";
        $params = ["'%" . $search ."%'"];
        $courselist = $DB->get_records_sql($sql, $params);
        $activitylist = array();
        foreach ($courselist as $cl) {
            global $CFG;
            if (!empty($cl)) {
                $checkpermissions = (new reportbase($cl->id))->check_permissions($USER->id, $context);
                if (!empty($checkpermissions) || has_capability('block/learnerscript:managereports', $context)) {
                    $modulelink = html_writer::link(new moodle_url('/blocks/learnerscript/viewreport.php',
                                array('id' => $cl->id)), $cl->name, array('id' => 'viewmore_id'));
                    $activitylist[] = ['id' => $cl->id, 'text' => $modulelink];
                }
            }
        }
        $termsdata = array();
        $termsdata['total_count'] = count($activitylist);
        $termsdata['incomplete_results'] = true;
        $termsdata['items'] = $activitylist;
        $return = $termsdata;
        $data = json_encode($return);
        return $data;
    }

    public static function reportlist_returns() {
        return new external_value(PARAM_RAW, 'data');
    }

    public static function sendemails_parameters() {
        return new external_function_parameters(
            array(
                'reportid' => new external_value(PARAM_INT, 'Report ID', false, 0),
                'instance' => new external_value(PARAM_INT, 'Reprot Instance', false),
                'pageurl' => new external_value(PARAM_LOCALURL, 'Page URL', false, ''),
            )
        );

    }
    public static function sendemails($reportid, $instance, $pageurl) {
        global $CFG, $PAGE;
        $PAGE->set_context(context_system::instance());
        $pageurl = $pageurl ? $pageurl : $CFG->wwwroot . '/blocks/reportdashboard/dashboard.php';
        require_once($CFG->dirroot . '/blocks/reportdashboard/email_form.php');
        $emailform = new analytics_emailform($pageurl, array('reportid' => $reportid, 'AjaxForm' => true, 'instance' => $instance));
        $return = $emailform->render();
        $data = json_encode($return);
        return $data;
    }

    public static function sendemails_returns() {
        return new external_value(PARAM_RAW, 'data');
    }

    public static function inplace_editable_dashboard_parameters() {
        return new external_function_parameters(
            array(
                'prevoiusdashboardname' => new external_value(PARAM_TEXT, 'The Prevoius Dashboard Name', false, ''),
                'pagetypepattern' => new external_value(PARAM_TEXT, 'The Page Patten Type', false, ''),
                'subpagepattern' => new external_value(PARAM_TEXT, 'The Sub Page Patten Type', false, ''),
                'value' => new external_value(PARAM_TEXT, 'The Dashboard Name', false, ''),
            )
        );
    }
    public static function inplace_editable_dashboard($prevoiusdashboardname, $pagetypepattern, $subpagepattern, $value) {
        global $DB, $PAGE;
        $explodepetten = explode('-', $pagetypepattern);
        $dashboardname = str_replace (' ', '', $value);
        if (strlen($dashboardname) > 30 || empty($dashboardname)) {
            return $prevoiusdashboardname;
        }
        $update = $DB->execute("UPDATE {block_instances} SET subpagepattern = '$dashboardname' WHERE subpagepattern = '$subpagepattern'");
        if ($update) {
            return $dashboardname;
        } else {
            return false;
        }
    }
    public static function inplace_editable_dashboard_returns() {
        return new external_value(PARAM_RAW, 'data');
    }

    public static function addtiles_to_dashboard_is_allowed_from_ajax() {
        return true;
    }
    public static function addtiles_to_dashboard_parameters() {
        return new external_function_parameters(
            array(
                'role' => new external_value(PARAM_TEXT, 'Role', false),
                'dashboardurl' => new external_value(PARAM_TEXT, 'Created Dashboard Name', false),
                'contextlevel' => new external_value(PARAM_INT, 'contextlevel of role', false),
            )
        );
    }
    public static function addtiles_to_dashboard($role, $dashboardurl, $contextlevel) {
        global $PAGE, $CFG, $DB;
        $contextlevel = $_SESSION['ls_contextlevel'];
        $PAGE->set_context(context_system::instance());
        $context = context_system::instance();
        if ((has_capability('block/learnerscript:managereports', $context) ||
            has_capability('block/learnerscript:manageownreports', $context) ||
            is_siteadmin())) {
            require_once $CFG->dirroot . '/blocks/reportdashboard/reporttiles_form.php';
            $seturl = !empty($role) ? '/blocks/reportdashboard/dashboard.php?role='.$role.'&contextlevel='.$contextlevel : '/blocks/reportdashboard/dashboard.php';
            if($dashboardurl != ''){
                $seturl = !empty($role) ? '/blocks/reportdashboard/dashboard.php?role='.$role.'&contextlevel='.$contextlevel.'&dashboardurl='.$dashboardurl.'' :'/blocks/reportdashboard/dashboard.php?dashboardurl='.$dashboardurl.'';
            }
            $staticreports = $DB->get_records_sql("SELECT id FROM {block_learnerscript}
                                                WHERE type = 'statistics' AND visible = :visible AND global = :global", ['visible' => 1, 'global' => 1]);
            $reporttiles = new reporttiles_form($CFG->wwwroot.$seturl);
            $rolereports = (new ls)->listofreportsbyrole($coursels, true, $parentcheck);
            if(!empty($rolereports)){
                $return = $reporttiles->render();
            } else{
                $return = '<div class="alert alert-info">'.get_string('statisticsreportsnotavailable',  'block_reportdashboard').'</div>';
            }
        } else {
            $terms_data = array();
            $terms_data['error'] = true;
            $terms_data['type'] = 'Warning';
            $terms_data['cap'] = true;
            $terms_data['msg'] = get_string('badpermissions', 'block_learnerscript');
            $return = $terms_data;
        }
        $data = json_encode($return);
        return $data;
    }
    public static function addtiles_to_dashboard_returns() {
        return new external_value(PARAM_RAW, 'data');
    }
    public static function addwidget_to_dashboard_is_allowed_from_ajax() {
        return true;
    }
    public static function addwidget_to_dashboard_parameters() {
        return new external_function_parameters(
            array(
                'role' => new external_value(PARAM_TEXT, 'Role', false),
                'dashboardurl' => new external_value(PARAM_TEXT, 'Created Dashboard Name', false),
                'contextlevel' => new external_value(PARAM_INT, 'contextlevel of role', false),
            )
        );
    }
    public static function addwidget_to_dashboard($role, $dashboardurl, $contextlevel) {
        global $PAGE, $CFG, $DB;
        $contextlevel = $_SESSION['ls_contextlevel'];
        $PAGE->set_context(context_system::instance());
        $context = context_system::instance();
        if ((has_capability('block/learnerscript:managereports', $context) ||
            has_capability('block/learnerscript:manageownreports', $context) ||
            is_siteadmin())) {
            $seturl = !empty($role) ? '/blocks/reportdashboard/dashboard.php?role='.$role.'&contextlevel='.$contextlevel : '/blocks/reportdashboard/dashboard.php';
            if($dashboardurl != ''){
                //$contextlevel = $_SESSION['ls_contextlevel'];
                $seturl = !empty($role) ? '/blocks/reportdashboard/dashboard.php?role='.$role.'&contextlevel='.$contextlevel.'&dashboardurl='.$dashboardurl.'' :'/blocks/reportdashboard/dashboard.php?dashboardurl='.$dashboardurl.'';
            }
            $coursels = false;
            $parentcheck = false;
            if ($dashboardurl == 'Course') {
                $coursels = true;
                $parentcheck = false;
            }
            require_once $CFG->dirroot . '/blocks/reportdashboard/reportselect_form.php';
            $reportselect = new reportselect_form($CFG->wwwroot.$seturl, array('coursels' => $coursels, 'parentcheck' => $parentcheck));
            $rolereports = (new ls)->listofreportsbyrole($coursels, false, $parentcheck);
            if(!empty($rolereports)) {
                $return = $reportselect->render();
            } else{
                $return = '<div class="alert alert-info">'.get_string('customreportsnotavailable',  'block_reportdashboard').'</div>';
            }
        } else {
            $terms_data = array();
            $terms_data['error'] = true;
            $terms_data['type'] = 'Warning';
            $terms_data['cap'] = true;
            $terms_data['msg'] = get_string('badpermissions', 'block_learnerscript');
            $return = $terms_data;
        }
        $data = json_encode($return);
        return $data;
    }
    public static function addwidget_to_dashboard_returns() {
        return new external_value(PARAM_RAW, 'data');
    }
}

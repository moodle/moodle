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
 * This file contains functions used by the participation reports
 *
 * @package   report_participation
 * @copyright 2014 Rajesh Taneja <rajesh@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returns log table name of preferred reader, if leagcy then return empty string.
 *
 * @return string table name
 */
function report_participation_get_log_table_name() {
    // Get prefered sql_internal_table_reader reader (if enabled).
    $logmanager = get_log_manager();
    $readers = $logmanager->get_readers();
    $logtable = '';

    // Get preferred reader.
    if (!empty($readers)) {
        foreach ($readers as $readerpluginname => $reader) {
            // If sql_internal_table_reader is preferred reader.
            if ($reader instanceof \core\log\sql_internal_table_reader) {
                $logtable = $reader->get_internal_log_table_name();
                break;
            }
        }
    }
    return $logtable;
}

/**
 * Return time options, which should be shown for record filtering.
 *
 * @param int $minlog Time of first log record available.
 * @return array time options.
 */
function report_participation_get_time_options($minlog) {
    $timeoptions = array();
    $now = usergetmidnight(time());

    // Days.
    for ($i = 1; $i < 7; $i++) {
        if (strtotime('-'.$i.' days',$now) >= $minlog) {
            $timeoptions[strtotime('-'.$i.' days',$now)] = get_string('numdays','moodle',$i);
        }
    }
    // Weeks.
    for ($i = 1; $i < 10; $i++) {
        if (strtotime('-'.$i.' weeks',$now) >= $minlog) {
            $timeoptions[strtotime('-'.$i.' weeks',$now)] = get_string('numweeks','moodle',$i);
        }
    }
    // Months.
    for ($i = 2; $i < 12; $i++) {
        if (strtotime('-'.$i.' months',$now) >= $minlog) {
            $timeoptions[strtotime('-'.$i.' months',$now)] = get_string('nummonths','moodle',$i);
        }
    }
    // Try a year.
    if (strtotime('-1 year',$now) >= $minlog) {
        $timeoptions[strtotime('-1 year',$now)] = get_string('lastyear');
    }
    return $timeoptions;
}

/**
 * Return action sql and params.
 *
 * @param string $action action to be filtered.
 * @param string $modname module name.
 * @return array actionsql and actionparams.
 */
function report_participation_get_action_sql($action, $modname) {
    global $CFG, $DB;

    $actionsql = '';
    $actionparams = array();

    $viewnames = array();
    $postnames = array();
    include_once($CFG->dirroot.'/mod/' . $modname . '/lib.php');

    $viewfun = $modname.'_get_view_actions';
    $postfun = $modname.'_get_post_actions';

    if (function_exists($viewfun)) {
        $viewnames = $viewfun();
    }

    if (function_exists($postfun)) {
        $postnames = $postfun();
    }

    switch ($action) {
        case 'view':
            $actions = $viewnames;
            break;
        case 'post':
            $actions = $postnames;
            break;
        default:
            // Some modules have stuff we want to hide, ie mail blocked etc so do actually need to limit here.
            $actions = array_merge($viewnames, $postnames);
    }

    if (!empty($actions)) {
        list($actionsql, $actionparams) = $DB->get_in_or_equal($actions, SQL_PARAMS_NAMED, 'action');
        $actionsql = " AND action $actionsql";
    }

    return array($actionsql, $actionparams);
}

/**
 * Return crud sql and params.
 *
 * @param string $action action to be filtered.
 * @return array crudsql and crudparams.
 */
function report_participation_get_crud_sql($action) {
    global $DB;

    switch ($action) {
        case 'view':
            $crud = 'r';
            break;
        case 'post':
            $crud = array('c', 'u', 'd');
            break;
        default:
            $crud = array('c', 'r', 'u', 'd');
    }

    list($crudsql, $crudparams) = $DB->get_in_or_equal($crud, SQL_PARAMS_NAMED, 'crud');
    $crudsql = " AND crud " . $crudsql;
    return array($crudsql, $crudparams);
}

/**
 * List of action filters.
 *
 * @return array
 */
function report_participation_get_action_options() {
    return array('' => get_string('allactions'),
            'view' => get_string('view'),
            'post' => get_string('post'),);
}

/**
 * Print filter form.
 *
 * @param stdClass $course course object.
 * @param int $timefrom Time from which records should be fetched.
 * @param int $minlog Time of first record present in log store.
 * @param string $action action to be filtered.
 * @param int $roleid Role to be filtered.
 * @param int $instanceid Instance id of module.
 */
function report_participation_print_filter_form($course, $timefrom, $minlog, $action, $roleid, $instanceid) {
    global $DB;

    $timeoptions = report_participation_get_time_options($minlog);

    $actionoptions = report_participation_get_action_options();

    $context = context_course::instance($course->id);
    $roles = get_roles_used_in_context($context);
    $rolesviewable = get_viewable_roles($context);

    $guestrole = get_guest_role();
    $roleoptions = array_intersect_key($rolesviewable, $roles) + [
        $guestrole->id => role_get_name($guestrole, $context),
    ];

    $modinfo = get_fast_modinfo($course);

    $modules = $DB->get_records_select('modules', "visible = 1", null, 'name ASC');

    $instanceoptions = array();
    foreach ($modules as $module) {
        if (empty($modinfo->instances[$module->name])) {
            continue;
        }
        $instances = array();
        foreach ($modinfo->instances[$module->name] as $cm) {
            // Skip modules such as label which do not actually have links;
            // this means there's nothing to participate in.
            if (!$cm->has_view()) {
                continue;
            }
            $instances[$cm->id] = format_string($cm->name);
        }
        if (count($instances) == 0) {
            continue;
        }
        $instanceoptions[] = array(get_string('modulenameplural', $module->name)=>$instances);
    }

    echo '<form class="participationselectform d-flex flex-wrap align-items-center" action="index.php" method="get"><div>'."\n".
        '<input type="hidden" name="id" value="'.$course->id.'" />'."\n";
    echo '<label for="menuinstanceid">'.get_string('activitymodule').'</label>'."\n";
    echo html_writer::select($instanceoptions, 'instanceid', $instanceid);
    echo '<label for="menutimefrom">'.get_string('lookback').'</label>'."\n";
    echo html_writer::select($timeoptions,'timefrom',$timefrom);
    echo '<label for="menuroleid">'.get_string('showonly').'</label>'."\n";
    echo html_writer::select($roleoptions,'roleid',$roleid,false);
    echo '<label for="menuaction">'.get_string('showactions').'</label>'."\n";
    echo html_writer::select($actionoptions, 'action', $action, false, ['class' => 'me-1']);
    echo '<input type="submit" value="'.get_string('go').'" class="btn btn-primary"/>'."\n</div></form>\n";
}

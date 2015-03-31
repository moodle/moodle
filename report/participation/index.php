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
 * Participation report
 *
 * @package    report
 * @subpackage participation
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/lib/tablelib.php');
require_once($CFG->dirroot.'/report/participation/locallib.php');

define('DEFAULT_PAGE_SIZE', 20);
define('SHOW_ALL_PAGE_SIZE', 5000);

$id         = required_param('id', PARAM_INT); // course id.
$roleid     = optional_param('roleid', 0, PARAM_INT); // which role to show
$instanceid = optional_param('instanceid', 0, PARAM_INT); // instance we're looking at.
$timefrom   = optional_param('timefrom', 0, PARAM_INT); // how far back to look...
$action     = optional_param('action', '', PARAM_ALPHA);
$page       = optional_param('page', 0, PARAM_INT);                     // which page to show
$perpage    = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT);  // how many per page
$currentgroup = optional_param('group', null, PARAM_INT); // Get the active group.

$url = new moodle_url('/report/participation/index.php', array('id'=>$id));
if ($roleid !== 0) $url->param('roleid');
if ($instanceid !== 0) $url->param('instanceid');
if ($timefrom !== 0) $url->param('timefrom');
if ($action !== '') $url->param('action');
if ($page !== 0) $url->param('page');
if ($perpage !== DEFAULT_PAGE_SIZE) $url->param('perpage');
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');

if ($action != 'view' and $action != 'post') {
    $action = ''; // default to all (don't restrict)
}

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    print_error('invalidcourse');
}

if ($roleid != 0 and !$role = $DB->get_record('role', array('id'=>$roleid))) {
    print_error('invalidrole');
}

require_login($course);
$context = context_course::instance($course->id);
require_capability('report/participation:view', $context);

$strparticipation = get_string('participationreport');
$strviews         = get_string('views');
$strposts         = get_string('posts');
$strreports       = get_string('reports');

$actionoptions = report_participation_get_action_options();
if (!array_key_exists($action, $actionoptions)) {
    $action = '';
}

$PAGE->set_title($course->shortname .': '. $strparticipation);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

$uselegacyreader = false; // Use legacy reader with sql_internal_table_reader to aggregate records.
$onlyuselegacyreader = false; // Use only legacy log table to aggregate records.

$logtable = report_participation_get_log_table_name(); // Log table to use for fetaching records.

// If no log table, then use legacy records.
if (empty($logtable)) {
    $onlyuselegacyreader = true;
}

// If no legacy and no logtable then don't proceed.
if (!$onlyuselegacyreader && empty($logtable)) {
    echo $OUTPUT->box_start('generalbox', 'notice');
    echo get_string('nologreaderenabled', 'report_participation');
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    die();
}

$modinfo = get_fast_modinfo($course);

$minloginternalreader = 0; // Time of first record in sql_internal_table_reader.

if ($onlyuselegacyreader) {
    // If no sql_inrenal_reader enabled then get min. time from log table.
    $minlog = $DB->get_field_sql('SELECT min(time) FROM {log} WHERE course = ?', array($course->id));
} else {
    $uselegacyreader = true;
    $minlog = $DB->get_field_sql('SELECT min(time) FROM {log} WHERE course = ?', array($course->id));

    // If legacy reader is not logging then get data from new log table.
    // Get minimum log time for this course from preferred log reader.
    $minloginternalreader = $DB->get_field_sql('SELECT min(timecreated) FROM {' . $logtable . '}
                                                 WHERE courseid = ?', array($course->id));
    // If new log store has oldest data then don't use old log table.
    if (empty($minlog) || ($minloginternalreader <= $minlog)) {
        $uselegacyreader = false;
        $minlog = $minloginternalreader;
    }

    // If timefrom is greater then first record in sql_internal_table_reader then get record from sql_internal_table_reader only.
    if (!empty($timefrom) && ($minloginternalreader < $timefrom)) {
        $uselegacyreader = false;
    }
}

// Print first controls.
report_participation_print_filter_form($course, $timefrom, $minlog, $action, $roleid, $instanceid);

$baseurl = new moodle_url('/report/participation/index.php', array(
    'id' => $course->id,
    'roleid' => $roleid,
    'instanceid' => $instanceid,
    'timefrom' => $timefrom,
    'action' => $action,
    'perpage' => $perpage,
    'group' => $currentgroup
));
$select = groups_allgroups_course_menu($course, $baseurl, true, $currentgroup);

// User cannot see any group.
if (empty($select)) {
    echo $OUTPUT->heading(get_string("notingroup"));
    echo $OUTPUT->footer();
    exit;
} else {
    echo $select;
}

// Fetch current active group.
$groupmode = groups_get_course_groupmode($course);
$currentgroup = $SESSION->activegroup[$course->id][$groupmode][$course->defaultgroupingid];

if (!empty($instanceid) && !empty($roleid)) {

    // Trigger a report viewed event.
    $event = \report_participation\event\report_viewed::create(array('context' => $context,
            'other' => array('instanceid' => $instanceid, 'groupid' => $currentgroup, 'roleid' => $roleid,
            'timefrom' => $timefrom, 'action' => $action)));
    $event->trigger();

    // from here assume we have at least the module we're using.
    $cm = $modinfo->cms[$instanceid];

    // Group security checks.
    if (!groups_group_visible($currentgroup, $course, $cm)) {
        echo $OUTPUT->heading(get_string("notingroup"));
        echo $OUTPUT->footer();
        exit;
    }

    $table = new flexible_table('course-participation-'.$course->id.'-'.$cm->id.'-'.$roleid);
    $table->course = $course;

    $table->define_columns(array('fullname','count','select'));
    $table->define_headers(array(get_string('user'),((!empty($action)) ? get_string($action) : get_string('allactions')),get_string('select')));
    $table->define_baseurl($baseurl);

    $table->set_attribute('cellpadding','5');
    $table->set_attribute('class', 'generaltable generalbox reporttable');

    $table->sortable(true,'lastname','ASC');
    $table->no_sorting('select');

    $table->set_control_variables(array(
                                        TABLE_VAR_SORT    => 'ssort',
                                        TABLE_VAR_HIDE    => 'shide',
                                        TABLE_VAR_SHOW    => 'sshow',
                                        TABLE_VAR_IFIRST  => 'sifirst',
                                        TABLE_VAR_ILAST   => 'silast',
                                        TABLE_VAR_PAGE    => 'spage'
                                        ));
    $table->setup();

    // We want to query both the current context and parent contexts.
    list($relatedctxsql, $params) = $DB->get_in_or_equal($context->get_parent_context_ids(true), SQL_PARAMS_NAMED, 'relatedctx');
    $params['roleid'] = $roleid;
    $params['instanceid'] = $instanceid;
    $params['timefrom'] = $timefrom;

    $groupsql = "";
    if (!empty($currentgroup)) {
        $groupsql = "JOIN {groups_members} gm ON (gm.userid = u.id AND gm.groupid = :groupid)";
        $params['groupid'] = $currentgroup;
    }

    $countsql = "SELECT COUNT(DISTINCT(ra.userid))
                   FROM {role_assignments} ra
                   JOIN {user} u ON u.id = ra.userid
                   $groupsql
                  WHERE ra.contextid $relatedctxsql AND ra.roleid = :roleid";

    $totalcount = $DB->count_records_sql($countsql, $params);

    list($twhere, $tparams) = $table->get_sql_where();
    if ($twhere) {
        $params = array_merge($params, $tparams);
        $matchcount = $DB->count_records_sql($countsql.' AND '.$twhere, $params);
    } else {
        $matchcount = $totalcount;
    }

    $modulename = get_string('modulename', $cm->modname);
    echo '<div id="participationreport">' . "\n";
    echo '<p class="modulename">' . $modulename . ' ' . $strviews . '<br />'."\n"
        . $modulename . ' ' . $strposts . '</p>'."\n";

    $table->initialbars($totalcount > $perpage);
    $table->pagesize($perpage, $matchcount);

    if ($uselegacyreader || $onlyuselegacyreader) {
        list($actionsql, $actionparams) = report_participation_get_action_sql($action, $cm->modname);
        $params = array_merge($params, $actionparams);
    }

    if (!$onlyuselegacyreader) {
        list($crudsql, $crudparams) = report_participation_get_crud_sql($action);
        $params = array_merge($params, $crudparams);
    }

    $usernamefields = get_all_user_name_fields(true, 'u');
    $users = array();
    // If using legacy log then get users from old table.
    if ($uselegacyreader || $onlyuselegacyreader) {
        $limittime = '';
        if ($uselegacyreader && !empty($minloginternalreader)) {
            $limittime = ' AND time < :tilltime ';
            $params['tilltime'] = $minloginternalreader;
        }
        $sql = "SELECT ra.userid, $usernamefields, u.idnumber, l.actioncount AS count
                  FROM (SELECT DISTINCT userid FROM {role_assignments} WHERE contextid $relatedctxsql AND roleid = :roleid ) ra
                  JOIN {user} u ON u.id = ra.userid
             $groupsql
             LEFT JOIN (
                    SELECT userid, COUNT(action) AS actioncount
                      FROM {log}
                     WHERE cmid = :instanceid
                           AND time > :timefrom " . $limittime . $actionsql .
                " GROUP BY userid) l ON (l.userid = ra.userid)";
        if ($twhere) {
            $sql .= ' WHERE '.$twhere; // Initial bar.
        }

        if ($table->get_sql_sort()) {
            $sql .= ' ORDER BY '.$table->get_sql_sort();
        }
        if (!$users = $DB->get_records_sql($sql, $params, $table->get_page_start(), $table->get_page_size())) {
            $users = array(); // Tablelib will handle saying 'Nothing to display' for us.
        }
    }

    // Get record from sql_internal_table_reader and merge with records got from legacy log (if needed).
    if (!$onlyuselegacyreader) {
        $sql = "SELECT ra.userid, $usernamefields, u.idnumber, COUNT(l.actioncount) AS count
                  FROM (SELECT DISTINCT userid FROM {role_assignments} WHERE contextid $relatedctxsql AND roleid = :roleid ) ra
                  JOIN {user} u ON u.id = ra.userid
             $groupsql
             LEFT JOIN (
                    SELECT userid, COUNT(crud) AS actioncount
                      FROM {" . $logtable . "}
                     WHERE contextinstanceid = :instanceid
                       AND timecreated > :timefrom" . $crudsql ."
                       AND edulevel = :edulevel
                       AND anonymous = 0
                       AND contextlevel = :contextlevel
                       AND (origin = 'web' OR origin = 'ws')
                  GROUP BY userid,timecreated) l ON (l.userid = ra.userid)";
        // We add this after the WHERE statement that may come below.
        $groupbysql = " GROUP BY ra.userid, $usernamefields, u.idnumber";

        $params['edulevel'] = core\event\base::LEVEL_PARTICIPATING;
        $params['contextlevel'] = CONTEXT_MODULE;

        if ($twhere) {
            $sql .= ' WHERE '.$twhere; // Initial bar.
        }
        $sql .= $groupbysql;
        if ($table->get_sql_sort()) {
            $sql .= ' ORDER BY '.$table->get_sql_sort();
        }
        if ($u = $DB->get_records_sql($sql, $params, $table->get_page_start(), $table->get_page_size())) {
            if (empty($users)) {
                $users = $u;
            } else {
                // Merge two users array.
                foreach ($u as $key => $value) {
                    if (isset($users[$key]) && !empty($users[$key]->count)) {
                        if ($value->count) {
                            $users[$key]->count += $value->count;
                        }
                    } else {
                        $users[$key] = $value;
                    }
                }
            }
            unset($u);
            $u = null;
        }
    }

    $data = array();

    $a = new stdClass();
    $a->count = $totalcount;
    $a->items = $role->name;

    if ($matchcount != $totalcount) {
        $a->count = $matchcount.'/'.$a->count;
    }

    echo '<h2>'.get_string('counteditems', '', $a).'</h2>'."\n";

    echo '<form action="'.$CFG->wwwroot.'/user/action_redir.php" method="post" id="studentsform">'."\n";
    echo '<div>'."\n";
    echo '<input type="hidden" name="id" value="'.$id.'" />'."\n";
    echo '<input type="hidden" name="returnto" value="'. s($PAGE->url) .'" />'."\n";
    echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />'."\n";

    foreach ($users as $u) {
        $data = array('<a href="'.$CFG->wwwroot.'/user/view.php?id='.$u->userid.'&amp;course='.$course->id.'">'.fullname($u,true).'</a>'."\n",
                      ((!empty($u->count)) ? get_string('yes').' ('.$u->count.') ' : get_string('no')),
                      '<input type="checkbox" class="usercheckbox" name="user'.$u->userid.'" value="'.$u->count.'" />'."\n",
                      );
        $table->add_data($data);
    }

    $table->print_html();

    if ($perpage == SHOW_ALL_PAGE_SIZE) {
        $perpageurl = new moodle_url($baseurl, array('perpage' => DEFAULT_PAGE_SIZE));
        echo html_writer::start_div('', array('id' => 'showall'));
        echo html_writer::link($perpageurl, get_string('showperpage', '', DEFAULT_PAGE_SIZE));
        echo html_writer::end_div();
    } else if ($matchcount > 0 && $perpage < $matchcount) {
        $perpageurl = new moodle_url($baseurl, array('perpage' => SHOW_ALL_PAGE_SIZE));
        echo html_writer::start_div('', array('id' => 'showall'));
        echo html_writer::link($perpageurl, get_string('showall', '', $matchcount));
        echo html_writer::end_div();
    }

    echo '<div class="selectbuttons">';
    echo '<input type="button" id="checkall" value="'.get_string('selectall').'" /> '."\n";
    echo '<input type="button" id="checknone" value="'.get_string('deselectall').'" /> '."\n";
    if ($perpage >= $matchcount) {
        echo '<input type="button" id="checknos" value="'.get_string('selectnos').'" />'."\n";
    }
    echo '</div>';
    echo '<div>';
    echo html_writer::label(get_string('withselectedusers'), 'formactionselect');
    $displaylist['messageselect.php'] = get_string('messageselectadd');
    echo html_writer::select($displaylist, 'formaction', '', array(''=>'choosedots'), array('id'=>'formactionselect'));
    echo $OUTPUT->help_icon('withselectedusers');
    echo '<input type="submit" value="' . get_string('ok') . '" />'."\n";
    echo '</div>';
    echo '</div>'."\n";
    echo '</form>'."\n";
    echo '</div>'."\n";

    $PAGE->requires->js_init_call('M.report_participation.init');
}

echo $OUTPUT->footer();

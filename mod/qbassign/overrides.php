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
 * This page handles listing of qbassign overrides
 *
 * @package    mod_qbassign
 * @copyright  2016 Ilya Tregubov
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/mod/qbassign/lib.php');
require_once($CFG->dirroot.'/mod/qbassign/locallib.php');
require_once($CFG->dirroot.'/mod/qbassign/override_form.php');


$cmid = required_param('cmid', PARAM_INT);
$mode = optional_param('mode', '', PARAM_ALPHA); // One of 'user' or 'group', default is 'group'.

$action   = optional_param('action', '', PARAM_ALPHA);
$redirect = $CFG->wwwroot.'/mod/qbassign/overrides.php?cmid=' . $cmid . '&amp;mode=group';

list($course, $cm) = get_course_and_cm_from_cmid($cmid, 'qbassign');
$qbassign = $DB->get_record('qbassign', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, false, $cm);

$context = context_module::instance($cm->id);

// Check the user has the required capabilities to list overrides.
require_capability('mod/qbassign:manageoverrides', $context);

$qbassigngroupmode = groups_get_activity_groupmode($cm);
$accessallgroups = ($qbassigngroupmode == NOGROUPS) || has_capability('moodle/site:accessallgroups', $context);

$overridecountgroup = $DB->count_records('qbassign_overrides', array('userid' => null, 'qbassignid' => $qbassign->id));

// Get the course groups that the current user can access.
$groups = $accessallgroups ? groups_get_all_groups($cm->course) : groups_get_activity_allowed_groups($cm);

// Default mode is "group", unless there are no groups.
if ($mode != "user" and $mode != "group") {
    if (!empty($groups)) {
        $mode = "group";
    } else {
        $mode = "user";
    }
}
$groupmode = ($mode == "group");

$url = new moodle_url('/mod/qbassign/overrides.php', array('cmid' => $cm->id, 'mode' => $mode));

$PAGE->set_url($url);
navigation_node::override_active_url(new moodle_url('/mod/qbassign/overrides.php', ['cmid' => $cmid]));

if ($action == 'movegroupoverride') {
    $id = required_param('id', PARAM_INT);
    $dir = required_param('dir', PARAM_ALPHA);

    if (confirm_sesskey()) {
        qbmove_group_override($id, $dir, $qbassign->id);
    }
    redirect($redirect);
}

// Display a list of overrides.
$PAGE->set_pagelayout('admin');
$PAGE->add_body_class('limitedwidth');
$PAGE->set_title(get_string('overrides', 'qbassign'));
$PAGE->set_heading($course->fullname);
$activityheader = $PAGE->activityheader;
$activityheader->set_attrs([
    'description' => '',
    'hidecompletion' => true,
    'title' => $activityheader->is_title_allowed() ? format_string($qbassign->name, true, ['context' => $context]) : ""
]);
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('overrides', 'mod_qbassign'), 2);
$overridemenu = new \mod_qbassign\output\override_actionmenu($url, $cm);
$renderer = $PAGE->get_renderer('mod_qbassign');
echo $renderer->render($overridemenu);

// Delete orphaned group overrides.
$sql = 'SELECT o.id
          FROM {qbassign_overrides} o
     LEFT JOIN {groups} g ON o.groupid = g.id
         WHERE o.groupid IS NOT NULL
               AND g.id IS NULL
               AND o.qbassignid = ?';
$params = array($qbassign->id);
$orphaned = $DB->get_records_sql($sql, $params);
if (!empty($orphaned)) {
    $DB->delete_records_list('qbassign_overrides', 'id', array_keys($orphaned));
}

$overrides = [];

// Fetch all overrides.
if ($groupmode) {
    $colname = get_string('group');
    // To filter the result by the list of groups that the current user has access to.
    if ($groups) {
        $params = ['qbassignid' => $qbassign->id];
        list($insql, $inparams) = $DB->get_in_or_equal(array_keys($groups), SQL_PARAMS_NAMED);
        $params += $inparams;

        $sql = "SELECT o.*, g.name
                  FROM {qbassign_overrides} o
                  JOIN {groups} g ON o.groupid = g.id
                 WHERE o.qbassignid = :qbassignid AND g.id $insql
              ORDER BY o.sortorder";

        $overrides = $DB->get_records_sql($sql, $params);
    }
} else {
    $colname = get_string('user');
    list($sort, $params) = users_order_by_sql('u');
    $params['qbassignid'] = $qbassign->id;

    $userfieldsapi = \core_user\fields::for_name();
    if ($accessallgroups) {
        $sql = 'SELECT o.*, ' . $userfieldsapi->get_sql('u', false, '', '', false)->selects . '
                  FROM {qbassign_overrides} o
                  JOIN {user} u ON o.userid = u.id
                 WHERE o.qbassignid = :qbassignid
              ORDER BY ' . $sort;

        $overrides = $DB->get_records_sql($sql, $params);
    } else if ($groups) {
        list($insql, $inparams) = $DB->get_in_or_equal(array_keys($groups), SQL_PARAMS_NAMED);
        $params += $inparams;

        $sql = 'SELECT o.*, ' . $userfieldsapi->get_sql('u', false, '', '', false)->selects . '
                  FROM {qbassign_overrides} o
                  JOIN {user} u ON o.userid = u.id
                  JOIN {groups_members} gm ON u.id = gm.userid
                 WHERE o.qbassignid = :qbassignid AND gm.groupid ' . $insql . '
              ORDER BY ' . $sort;

        $overrides = $DB->get_records_sql($sql, $params);
    }
}

// Initialise table.
$table = new html_table();
$table->headspan = array(1, 2, 1);
$table->colclasses = array('colname', 'colsetting', 'colvalue', 'colaction');
$table->head = array(
        $colname,
        get_string('overrides', 'qbassign'),
        get_string('action'),
);

$userurl = new moodle_url('/user/view.php', array());
$groupurl = new moodle_url('/group/overview.php', array('id' => $cm->course));

$overridedeleteurl = new moodle_url('/mod/qbassign/overridedelete.php');
$overrideediturl = new moodle_url('/mod/qbassign/overrideedit.php');

$hasinactive = false; // Whether there are any inactive overrides.

foreach ($overrides as $override) {

    $fields = array();
    $values = array();
    $active = true;

    // Check for inactive overrides.
    if (!$groupmode) {
        if (!is_enrolled($context, $override->userid)) {
            // User not enrolled.
            $active = false;
        } else if (!\core_availability\info_module::is_user_visible($cm, $override->userid)) {
            // User cannot access the module.
            $active = false;
        }
    }

    // Format allowsubmissionsfromdate.
    if (isset($override->allowsubmissionsfromdate)) {
        $fields[] = get_string('open', 'qbassign');
        $values[] = $override->allowsubmissionsfromdate > 0 ? userdate($override->allowsubmissionsfromdate) : get_string('noopen',
            'qbassign');
    }

    // Format duedate.
    if (isset($override->duedate)) {
        $fields[] = get_string('duedate', 'qbassign');
        $values[] = $override->duedate > 0 ? userdate($override->duedate) : get_string('noclose', 'qbassign');
    }

    // Format cutoffdate.
    if (isset($override->cutoffdate)) {
        $fields[] = get_string('cutoffdate', 'qbassign');
        $values[] = $override->cutoffdate > 0 ? userdate($override->cutoffdate) : get_string('noclose', 'qbassign');
    }

    // Format timelimit.
    if (isset($override->timelimit)) {
        $fields[] = get_string('timelimit', 'qbassign');
        $values[] = $override->timelimit > 0 ? format_time($override->timelimit) : get_string('none', 'qbassign');
    }

    // Icons.
    $iconstr = '';

    // Edit.
    $editurlstr = $overrideediturl->out(true, array('id' => $override->id));
    $iconstr = '<a title="' . get_string('edit') . '" href="'. $editurlstr . '">' .
            $OUTPUT->pix_icon('t/edit', get_string('edit')) . '</a> ';
    // Duplicate.
    $copyurlstr = $overrideediturl->out(true,
            array('id' => $override->id, 'action' => 'duplicate'));
    $iconstr .= '<a title="' . get_string('copy') . '" href="' . $copyurlstr . '">' .
            $OUTPUT->pix_icon('t/copy', get_string('copy')) . '</a> ';
    // Delete.
    $deleteurlstr = $overridedeleteurl->out(true,
            array('id' => $override->id, 'sesskey' => sesskey()));
    $iconstr .= '<a title="' . get_string('delete') . '" href="' . $deleteurlstr . '">' .
                $OUTPUT->pix_icon('t/delete', get_string('delete')) . '</a> ';

    if ($groupmode) {
        $usergroupstr = '<a href="' . $groupurl->out(true, ['group' => $override->groupid]) . '" >' .
            format_string($override->name, true, ['context' => $context]) . '</a>';

        // Move up.
        if ($override->sortorder > 1) {
            $iconstr .= '<a title="'.get_string('moveup').'" href="overrides.php?cmid=' . $cmid .
                '&amp;id=' . $override->id .'&amp;action=movegroupoverride&amp;dir=up&amp;sesskey='.sesskey().'">' .
                $OUTPUT->pix_icon('t/up', get_string('moveup')) . '</a> ';
        } else {
            $iconstr .= $OUTPUT->spacer() . ' ';
        }

        // Move down.
        if ($override->sortorder < $overridecountgroup) {
            $iconstr .= '<a title="'.get_string('movedown').'" href="overrides.php?cmid='.$cmid.
                '&amp;id=' . $override->id . '&amp;action=movegroupoverride&amp;dir=down&amp;sesskey='.sesskey().'">' .
                $OUTPUT->pix_icon('t/down', get_string('movedown')) . '</a> ';
        } else {
            $iconstr .= $OUTPUT->spacer() . ' ';
        }


    } else {
        $usergroupstr = html_writer::link($userurl->out(false,
                array('id' => $override->userid, 'course' => $course->id)),
                fullname($override));
    }

    $class = '';
    if (!$active) {
        $class = "dimmed_text";
        $usergroupstr .= '*';
        $hasinactive = true;
    }

    $usergroupcell = new html_table_cell();
    $usergroupcell->rowspan = count($fields);
    $usergroupcell->text = $usergroupstr;
    $actioncell = new html_table_cell();
    $actioncell->rowspan = count($fields);
    $actioncell->text = $iconstr;

    for ($i = 0; $i < count($fields); ++$i) {
        $row = new html_table_row();
        $row->attributes['class'] = $class;
        if ($i == 0) {
            $row->cells[] = $usergroupcell;
        }
        $cell1 = new html_table_cell();
        $cell1->text = $fields[$i];
        $row->cells[] = $cell1;
        $cell2 = new html_table_cell();
        $cell2->text = $values[$i];
        $row->cells[] = $cell2;
        if ($i == 0) {
            $row->cells[] = $actioncell;
        }
        $table->data[] = $row;
    }
}

// Output the table and button.
echo html_writer::start_tag('div', array('id' => 'qbassignoverrides'));
if (count($table->data)) {
    echo html_writer::table($table);
} else {
    if ($groupmode) {
        echo $OUTPUT->notification(get_string('nogroupoverrides', 'mod_qbassign'), 'info');
    } else {
        echo $OUTPUT->notification(get_string('nouseroverrides', 'mod_qbassign'), 'info');
    }
}

if ($hasinactive) {
    echo $OUTPUT->notification(get_string('inactiveoverridehelp', 'qbassign'), 'dimmed_text');
}

echo html_writer::start_tag('div', array('class' => 'buttons'));
$options = array();
if ($groupmode) {
    if (empty($groups)) {
        // There are no groups.
        echo $OUTPUT->notification(get_string('groupsnone', 'qbassign'), 'error');
        $options['disabled'] = true;
    }
} else {
    $users = array();
    // See if there are any users in the qbassign.
    if ($accessallgroups) {
        $users = get_enrolled_users($context, '', 0, 'u.id');
        $nousermessage = get_string('usersnone', 'qbassign');
    } else if ($groups) {
        $enrolledjoin = get_enrolled_join($context, 'u.id');
        list($ingroupsql, $ingroupparams) = $DB->get_in_or_equal(array_keys($groups), SQL_PARAMS_NAMED);
        $params = $enrolledjoin->params + $ingroupparams;
        $sql = "SELECT u.id
                  FROM {user} u
                  JOIN {groups_members} gm ON gm.userid = u.id
                       {$enrolledjoin->joins}
                 WHERE gm.groupid $ingroupsql
                       AND {$enrolledjoin->wheres}
              ORDER BY $sort";
        $users = $DB->get_records_sql($sql, $params);
        $nousermessage = get_string('usersnone', 'qbassign');
    } else {
        $nousermessage = get_string('groupsnone', 'qbassign');
    }
    $info = new \core_availability\info_module($cm);
    $users = $info->filter_user_list($users);

    if (empty($users)) {
        // There are no users.
        echo $OUTPUT->notification($nousermessage, 'error');
        $options['disabled'] = true;
    }
}
echo html_writer::end_tag('div');
echo html_writer::end_tag('div');

// Finish the page.
echo $OUTPUT->footer();

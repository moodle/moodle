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
 * This page handles listing of lesson overrides
 *
 * @package    mod_lesson
 * @copyright  2015 Jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/mod/lesson/lib.php');
require_once($CFG->dirroot.'/mod/lesson/locallib.php');
require_once($CFG->dirroot.'/mod/lesson/override_form.php');


$cmid = required_param('cmid', PARAM_INT);
$mode = optional_param('mode', '', PARAM_ALPHA); // One of 'user' or 'group', default is 'group'.

list($course, $cm) = get_course_and_cm_from_cmid($cmid, 'lesson');
$lesson = $DB->get_record('lesson', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, false, $cm);

$context = context_module::instance($cm->id);

// Check the user has the required capabilities to list overrides.
require_capability('mod/lesson:manageoverrides', $context);

$lessongroupmode = groups_get_activity_groupmode($cm);
$accessallgroups = ($lessongroupmode == NOGROUPS) || has_capability('moodle/site:accessallgroups', $context);

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

$url = new moodle_url('/mod/lesson/overrides.php', array('cmid' => $cm->id, 'mode' => $mode));

$PAGE->set_url($url);

// Display a list of overrides.
$PAGE->set_pagelayout('admin');
$PAGE->add_body_class('limitedwidth');
$PAGE->set_title(get_string('overrides', 'lesson'));
$PAGE->set_heading($course->fullname);
$PAGE->activityheader->set_attrs([
    'hidecompletion' => true,
    'description' => ''
]);
navigation_node::override_active_url(new moodle_url('/mod/lesson/overrides.php', ['cmid' => $cmid]));

$renderer = $PAGE->get_renderer('mod_lesson');

echo $OUTPUT->header();

// Delete orphaned group overrides.
$sql = 'SELECT o.id
          FROM {lesson_overrides} o
     LEFT JOIN {groups} g ON o.groupid = g.id
         WHERE o.groupid IS NOT NULL
               AND g.id IS NULL
               AND o.lessonid = ?';
$params = array($lesson->id);
$orphaned = $DB->get_records_sql($sql, $params);
if (!empty($orphaned)) {
    $DB->delete_records_list('lesson_overrides', 'id', array_keys($orphaned));
}

$overrides = [];

// Fetch all overrides.
if ($groupmode) {
    $colname = get_string('group');
    // To filter the result by the list of groups that the current user has access to.
    if ($groups) {
        $params = ['lessonid' => $lesson->id];
        list($insql, $inparams) = $DB->get_in_or_equal(array_keys($groups), SQL_PARAMS_NAMED);
        $params += $inparams;

        $sql = "SELECT o.*, g.name
                  FROM {lesson_overrides} o
                  JOIN {groups} g ON o.groupid = g.id
                 WHERE o.lessonid = :lessonid AND g.id $insql
              ORDER BY g.name";

        $overrides = $DB->get_records_sql($sql, $params);
    }
} else {
    $colname = get_string('user');
    list($sort, $params) = users_order_by_sql('u');
    $params['lessonid'] = $lesson->id;

    $userfieldsapi = \core_user\fields::for_name();
    if ($accessallgroups) {
        $sql = 'SELECT o.*, ' . $userfieldsapi->get_sql('u', false, '', '', false)->selects . '
                  FROM {lesson_overrides} o
                  JOIN {user} u ON o.userid = u.id
                 WHERE o.lessonid = :lessonid
              ORDER BY ' . $sort;

        $overrides = $DB->get_records_sql($sql, $params);
    } else if ($groups) {
        list($insql, $inparams) = $DB->get_in_or_equal(array_keys($groups), SQL_PARAMS_NAMED);
        $params += $inparams;

        $sql = 'SELECT o.*, ' . $userfieldsapi->get_sql('u', false, '', '', false)->selects . '
                  FROM {lesson_overrides} o
                  JOIN {user} u ON o.userid = u.id
                  JOIN {groups_members} gm ON u.id = gm.userid
                 WHERE o.lessonid = :lessonid AND gm.groupid ' . $insql . '
              ORDER BY ' . $sort;

        $overrides = $DB->get_records_sql($sql, $params);
    }
}

$overrides = $DB->get_records_sql($sql, $params);

$canoverride = true;
$errormessage = '';

if ($groupmode) {
    if (empty($groups)) {
        // There are no groups.
        $canoverride = false;
        $errormessage = get_string('groupsnone', 'lesson');
    }
} else {
    $users = array();
    // See if there are any users in the lesson.
    if ($accessallgroups) {
        $users = get_enrolled_users($context, '', 0, 'u.id');
        $nousermessage = get_string('usersnone', 'lesson');
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
        $nousermessage = get_string('usersnone', 'lesson');
    } else {
        $nousermessage = get_string('groupsnone', 'lesson');
    }
    $info = new \core_availability\info_module($cm);
    $users = $info->filter_user_list($users);

    if (empty($users)) {
        // There are no users.
        $canoverride = false;
        $errormessage = $nousermessage;
    }
}

// Initialise table.
$table = new html_table();
$table->headspan = array(1, 2, 1);
$table->colclasses = array('colname', 'colsetting', 'colvalue', 'colaction');
$table->head = array(
        $colname,
        get_string('overrides', 'lesson'),
        get_string('action'),
);

$userurl = new moodle_url('/user/view.php', array());
$groupurl = new moodle_url('/group/overview.php', array('id' => $cm->course));

$overridedeleteurl = new moodle_url('/mod/lesson/overridedelete.php');
$overrideediturl = new moodle_url('/mod/lesson/overrideedit.php');

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

    // Format available.
    if (isset($override->available)) {
        $fields[] = get_string('lessonopens', 'lesson');
        $values[] = $override->available > 0 ?
                userdate($override->available) : get_string('noopen', 'lesson');
    }

    // Format deadline.
    if (isset($override->deadline)) {
        $fields[] = get_string('lessoncloses', 'lesson');
        $values[] = $override->deadline > 0 ?
                userdate($override->deadline) : get_string('noclose', 'lesson');
    }

    // Format timelimit.
    if (isset($override->timelimit)) {
        $fields[] = get_string('timelimit', 'lesson');
        $values[] = $override->timelimit > 0 ?
                format_time($override->timelimit) : get_string('none', 'lesson');
    }

    // Format option to try a question again.
    if (isset($override->review)) {
        $fields[] = get_string('displayreview', 'lesson');
        $values[] = $override->review ?
                get_string('yes') : get_string('no');
    }

    // Format number of attempts.
    if (isset($override->maxattempts)) {
        $fields[] = get_string('maximumnumberofattempts', 'lesson');
        $values[] = $override->maxattempts > 0 ?
                $override->maxattempts : get_string('unlimited');
    }

    // Format retake allowed.
    if (isset($override->retake)) {
        $fields[] = get_string('retakesallowed', 'lesson');
        $values[] = $override->retake ?
                get_string('yes') : get_string('no');
    }

    // Format password.
    if (isset($override->password)) {
        $fields[] = get_string('usepassword', 'lesson');
        $values[] = $override->password !== '' ?
                get_string('enabled', 'lesson') : get_string('none', 'lesson');
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
    } else {
        $usergroupstr = '<a href="' . $userurl->out(true,
                array('id' => $override->userid)) . '" >' . fullname($override) . '</a>';
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

$overrideselect = new \mod_lesson\output\override_action_menu($cm->id, $url, $canoverride);
echo $renderer->render($overrideselect);

// Output the table and button.
echo html_writer::start_tag('div', array('id' => 'lessonoverrides'));
if (count($table->data)) {
    echo html_writer::table($table);
}

// No overrides to be displayed.
if (!$overrides) {
    echo $OUTPUT->notification(get_string('nooverridecreated', 'lesson'), 'info', false);
}

if ($hasinactive) {
    echo $OUTPUT->notification(get_string('inactiveoverridehelp', 'lesson'), 'dimmed_text');
}

echo html_writer::start_tag('div', array('class' => 'buttons'));
if (!empty($errormessage)) {
    echo $OUTPUT->notification($errormessage, 'error');
}
echo html_writer::end_tag('div');
echo html_writer::end_tag('div');

// Finish the page.
echo $OUTPUT->footer();

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
 * This page handles listing of quiz overrides
 *
 * @package    mod_quiz
 * @copyright  2010 Matt Petro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_quiz\quiz_settings;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/mod/quiz/lib.php');
require_once($CFG->dirroot.'/mod/quiz/locallib.php');

$cmid = required_param('cmid', PARAM_INT);
$mode = optional_param('mode', '', PARAM_ALPHA); // One of 'user' or 'group', default is 'group'.

$quizobj = quiz_settings::create_for_cmid($cmid);
$quiz = $quizobj->get_quiz();
$cm = $quizobj->get_cm();
$course = $quizobj->get_course();
$context = $quizobj->get_context();

require_login($course, false, $cm);

// Check the user has the required capabilities to list overrides.
$canedit = has_capability('mod/quiz:manageoverrides', $context);
if (!$canedit) {
    require_capability('mod/quiz:viewoverrides', $context);
}

$quizgroupmode = groups_get_activity_groupmode($cm);
$showallgroups = ($quizgroupmode == NOGROUPS) || has_capability('moodle/site:accessallgroups', $context);

// Get the course groups that the current user can access.
$groups = $showallgroups ? groups_get_all_groups($cm->course) : groups_get_activity_allowed_groups($cm);

// Default mode is "group", unless there are no groups.
if ($mode != "user" and $mode != "group") {
    if (!empty($groups)) {
        $mode = "group";
    } else {
        $mode = "user";
    }
}
$groupmode = ($mode == "group");

$url = new moodle_url('/mod/quiz/overrides.php', ['cmid' => $cm->id, 'mode' => $mode]);

$title = get_string('overridesforquiz', 'quiz',
        format_string($quiz->name, true, ['context' => $context]));
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->add_body_class('limitedwidth');
$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);
$PAGE->activityheader->disable();

// Activate the secondary nav tab.
$PAGE->set_secondary_active_tab("mod_quiz_useroverrides");

// Delete orphaned group overrides.
$sql = 'SELECT o.id
          FROM {quiz_overrides} o
     LEFT JOIN {groups} g ON o.groupid = g.id
         WHERE o.groupid IS NOT NULL
               AND g.id IS NULL
               AND o.quiz = ?';
$params = [$quiz->id];
$orphaned = $DB->get_records_sql($sql, $params);
if (!empty($orphaned)) {
    $DB->delete_records_list('quiz_overrides', 'id', array_keys($orphaned));
}

$overrides = [];
$colclasses = [];
$headers = [];

// Fetch all overrides.
if ($groupmode) {
    $headers[] = get_string('group');
    // To filter the result by the list of groups that the current user has access to.
    if ($groups) {
        $params = ['quizid' => $quiz->id];
        list($insql, $inparams) = $DB->get_in_or_equal(array_keys($groups), SQL_PARAMS_NAMED);
        $params += $inparams;

        $sql = "SELECT o.*, g.name
                  FROM {quiz_overrides} o
                  JOIN {groups} g ON o.groupid = g.id
                 WHERE o.quiz = :quizid AND g.id $insql
              ORDER BY g.name";

        $overrides = $DB->get_records_sql($sql, $params);
    }

} else {
    // User overrides.
    $colclasses[] = 'colname';
    $headers[] = get_string('user');
    $userfieldsapi = \core_user\fields::for_identity($context)->with_name()->with_userpic();
    $extrauserfields = $userfieldsapi->get_required_fields([\core_user\fields::PURPOSE_IDENTITY]);
    $userfieldssql = $userfieldsapi->get_sql('u', true, '', 'userid', false);
    foreach ($extrauserfields as $field) {
        $colclasses[] = 'col' . $field;
        $headers[] = \core_user\fields::get_display_name($field);
    }

    list($sort, $params) = users_order_by_sql('u', null, $context, $extrauserfields);
    $params['quizid'] = $quiz->id;

    if ($showallgroups) {
        $groupsjoin = '';
        $groupswhere = '';

    } else if ($groups) {
        list($insql, $inparams) = $DB->get_in_or_equal(array_keys($groups), SQL_PARAMS_NAMED);
        $groupsjoin = 'JOIN {groups_members} gm ON u.id = gm.userid';
        $groupswhere = ' AND gm.groupid ' . $insql;
        $params += $inparams;

    } else {
        // User cannot see any data.
        $groupsjoin = '';
        $groupswhere = ' AND 1 = 2';
    }

    $overrides = $DB->get_records_sql("
            SELECT o.*, {$userfieldssql->selects}
              FROM {quiz_overrides} o
              JOIN {user} u ON o.userid = u.id
                  {$userfieldssql->joins}
              $groupsjoin
             WHERE o.quiz = :quizid
               $groupswhere
             ORDER BY $sort
            ", array_merge($params, $userfieldssql->params));
}

// Initialise table.
$table = new html_table();
$table->head = $headers;
$table->colclasses = $colclasses;
$table->headspan = array_fill(0, count($headers), 1);

$table->head[] = get_string('overrides', 'quiz');
$table->colclasses[] = 'colsetting';
$table->colclasses[] = 'colvalue';
$table->headspan[] = 2;

if ($canedit) {
    $table->head[] = get_string('action');
    $table->colclasses[] = 'colaction';
    $table->headspan[] = 1;
}
$userurl = new moodle_url('/user/view.php', []);
$groupurl = new moodle_url('/group/overview.php', ['id' => $cm->course]);

$overridedeleteurl = new moodle_url('/mod/quiz/overridedelete.php');
$overrideediturl = new moodle_url('/mod/quiz/overrideedit.php');

$hasinactive = false; // Whether there are any inactive overrides.

foreach ($overrides as $override) {

    // Check if this override is active.
    $active = true;
    if (!$groupmode) {
        if (!has_capability('mod/quiz:attempt', $context, $override->userid)) {
            // User not allowed to take the quiz.
            $active = false;
        } else if (!\core_availability\info_module::is_user_visible($cm, $override->userid)) {
            // User cannot access the module.
            $active = false;
        }
    }
    if (!$active) {
        $hasinactive = true;
    }

    // Prepare the information about which settings are overridden.
    $fields = [];
    $values = [];

    // Format timeopen.
    if (isset($override->timeopen)) {
        $fields[] = get_string('quizopens', 'quiz');
        $values[] = $override->timeopen > 0 ?
                userdate($override->timeopen) : get_string('noopen', 'quiz');
    }
    // Format timeclose.
    if (isset($override->timeclose)) {
        $fields[] = get_string('quizcloses', 'quiz');
        $values[] = $override->timeclose > 0 ?
                userdate($override->timeclose) : get_string('noclose', 'quiz');
    }
    // Format timelimit.
    if (isset($override->timelimit)) {
        $fields[] = get_string('timelimit', 'quiz');
        $values[] = $override->timelimit > 0 ?
                format_time($override->timelimit) : get_string('none', 'quiz');
    }
    // Format number of attempts.
    if (isset($override->attempts)) {
        $fields[] = get_string('attempts', 'quiz');
        $values[] = $override->attempts > 0 ?
                $override->attempts : get_string('unlimited');
    }
    // Format password.
    if (isset($override->password)) {
        $fields[] = get_string('requirepassword', 'quiz');
        $values[] = $override->password !== '' ?
                get_string('enabled', 'quiz') : get_string('none', 'quiz');
    }

    // Prepare the information about who this override applies to.
    $extranamebit = $active ? '' : '*';
    $usercells = [];
    if ($groupmode) {
        $groupcell = new html_table_cell();
        $groupcell->rowspan = count($fields);
        $groupcell->text = html_writer::link(new moodle_url($groupurl, ['group' => $override->groupid]),
            format_string($override->name, true, ['context' => $context]) . $extranamebit);
        $usercells[] = $groupcell;
    } else {
        $usercell = new html_table_cell();
        $usercell->rowspan = count($fields);
        $usercell->text = html_writer::link(new moodle_url($userurl, ['id' => $override->userid]),
                fullname($override) . $extranamebit);
        $usercells[] = $usercell;

        foreach ($extrauserfields as $field) {
            $usercell = new html_table_cell();
            $usercell->rowspan = count($fields);
            $usercell->text = s($override->$field);
            $usercells[] = $usercell;
        }
    }

    // Prepare the actions.
    if ($canedit) {
        // Icons.
        $iconstr = '';

        // Edit.
        $editurlstr = $overrideediturl->out(true, ['id' => $override->id]);
        $iconstr = '<a title="' . get_string('edit') . '" href="' . $editurlstr . '">' .
                $OUTPUT->pix_icon('t/edit', get_string('edit')) . '</a> ';
        // Duplicate.
        $copyurlstr = $overrideediturl->out(true,
                ['id' => $override->id, 'action' => 'duplicate']);
        $iconstr .= '<a title="' . get_string('copy') . '" href="' . $copyurlstr . '">' .
                $OUTPUT->pix_icon('t/copy', get_string('copy')) . '</a> ';
        // Delete.
        $deleteurlstr = $overridedeleteurl->out(true,
                ['id' => $override->id, 'sesskey' => sesskey()]);
        $iconstr .= '<a title="' . get_string('delete') . '" href="' . $deleteurlstr . '">' .
                $OUTPUT->pix_icon('t/delete', get_string('delete')) . '</a> ';

        $actioncell = new html_table_cell();
        $actioncell->rowspan = count($fields);
        $actioncell->text = $iconstr;
    }

    // Add the data to the table.
    for ($i = 0; $i < count($fields); ++$i) {
        $row = new html_table_row();
        if (!$active) {
            $row->attributes['class'] = 'dimmed_text';
        }

        if ($i == 0) {
            $row->cells = $usercells;
        }

        $labelcell = new html_table_cell();
        $labelcell->text = $fields[$i];
        $row->cells[] = $labelcell;
        $valuecell = new html_table_cell();
        $valuecell->text = $values[$i];
        $row->cells[] = $valuecell;

        if ($canedit && $i == 0) {
            $row->cells[] = $actioncell;
        }

        $table->data[] = $row;
    }
}

// Work out what else needs to be displayed.
$addenabled = true;
$warningmessage = '';
if ($canedit) {
    if ($groupmode) {
        if (empty($groups)) {
            // There are no groups.
            $warningmessage = get_string('groupsnone', 'quiz');
            $addenabled = false;
        }
    } else {
        // See if there are any students in the quiz.
        if ($showallgroups) {
            $users = get_users_by_capability($context, 'mod/quiz:attempt', 'u.id');
            $nousermessage = get_string('usersnone', 'quiz');
        } else if ($groups) {
            $users = get_users_by_capability($context, 'mod/quiz:attempt', 'u.id', '', '', '', array_keys($groups));
            $nousermessage = get_string('usersnone', 'quiz');
        } else {
            $users = [];
            $nousermessage = get_string('groupsnone', 'quiz');
        }
        $info = new \core_availability\info_module($cm);
        $users = $info->filter_user_list($users);

        if (empty($users)) {
            // There are no students.
            $warningmessage = $nousermessage;
            $addenabled = false;
        }
    }
}

// Tertiary navigation.
echo $OUTPUT->header();
$renderer = $PAGE->get_renderer('mod_quiz');
$tertiarynav = new \mod_quiz\output\overrides_actions($cmid, $mode, $canedit, $addenabled);
echo $renderer->render($tertiarynav);

if ($mode === 'user') {
    echo $OUTPUT->heading(get_string('useroverrides', 'quiz'));
} else {
    echo $OUTPUT->heading(get_string('groupoverrides', 'quiz'));
}

// Output the table and button.
echo html_writer::start_tag('div', ['id' => 'quizoverrides']);
if (count($table->data)) {
    echo html_writer::table($table);
} else {
    if ($groupmode) {
        echo $OUTPUT->notification(get_string('overridesnoneforgroups', 'quiz'), 'info', false);
    } else {
        echo $OUTPUT->notification(get_string('overridesnoneforusers', 'quiz'), 'info', false);
    }
}
if ($hasinactive) {
    echo $OUTPUT->notification(get_string('inactiveoverridehelp', 'quiz'), 'info', false);
}

if ($warningmessage) {
    echo $OUTPUT->notification($warningmessage, 'error');
}

echo html_writer::end_tag('div');

// Finish the page.
echo $OUTPUT->footer();

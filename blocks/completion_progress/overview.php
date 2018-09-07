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
 * Completion Progress block overview page
 *
 * @package    block_completion_progress
 * @copyright  2018 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Include required files.
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/blocks/completion_progress/lib.php');
require_once($CFG->libdir.'/tablelib.php');

const USER_SMALL_CLASS = 20;   // Below this is considered small.
const USER_LARGE_CLASS = 200;  // Above this is considered large.
const DEFAULT_PAGE_SIZE = 20;
const SHOW_ALL_PAGE_SIZE = 5000;

// Gather form data.
$id       = required_param('instanceid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);
$page     = optional_param('page', 0, PARAM_INT); // Which page to show.
$perpage  = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT); // How many per page.
$group    = optional_param('group', 0, PARAM_INT); // Group selected.

// Determine course and context.
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = CONTEXT_COURSE::instance($courseid);

// Get specific block config and context.
$block = $DB->get_record('block_instances', array('id' => $id), '*', MUST_EXIST);
$config = unserialize(base64_decode($block->configdata));
$blockcontext = CONTEXT_BLOCK::instance($id);

// Set up page parameters.
$PAGE->set_course($course);
$PAGE->requires->css('/blocks/completion_progress/styles.css');
$PAGE->set_url(
    '/blocks/completion_progress/overview.php',
    array(
        'instanceid' => $id,
        'courseid'   => $courseid,
        'page'       => $page,
        'perpage'    => $perpage,
        'group'      => $group,
        'sesskey'    => sesskey(),
    )
);
$PAGE->set_context($context);
$title = get_string('overview', 'block_completion_progress');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add($title);
$PAGE->set_pagelayout('report');

// Check user is logged in and capable of accessing the Overview.
require_login($course, false);
require_capability('block/completion_progress:overview', $blockcontext);
confirm_sesskey();

// Start page output.
echo $OUTPUT->header();
echo $OUTPUT->heading($title, 2);
echo $OUTPUT->container_start('block_completion_progress');

// Check if activities/resources have been selected in config.
$activities = block_completion_progress_get_activities($courseid, $config);
if ($activities == null) {
    echo get_string('no_events_message', 'block_completion_progress');
    echo $OUTPUT->container_end();
    echo $OUTPUT->footer();
    die();
}
if (empty($activities)) {
    echo get_string('no_visible_events_message', 'block_completion_progress');
    echo $OUTPUT->container_end();
    echo $OUTPUT->footer();
    die();
}
$numactivities = count($activities);

// Determine if a role has been selected.
$sql = "SELECT DISTINCT r.id, r.name, r.archetype
          FROM {role} r, {role_assignments} a
         WHERE a.contextid = :contextid
           AND r.id = a.roleid
           AND r.archetype = :archetype";
$params = array('contextid' => $context->id, 'archetype' => 'student');
$studentrole = $DB->get_record_sql($sql, $params);
if ($studentrole) {
    $studentroleid = $studentrole->id;
} else {
    $studentroleid = 0;
}
$roleselected = optional_param('role', $studentroleid, PARAM_INT);
$rolewhere = $roleselected != 0 ? "AND a.roleid = $roleselected" : '';

// Output group selector if there are groups in the course.
echo $OUTPUT->container_start('progressoverviewmenus');
$groupselected = 0;
$groupuserid = $USER->id;
if (has_capability('moodle/site:accessallgroups', $context)) {
    $groupuserid = 0;
}
$groupids = array();
$groups = groups_get_all_groups($course->id, $groupuserid);
if (!empty($groups)) {
    $groupstodisplay = array(0 => get_string('allparticipants'));
    foreach ($groups as $groupid => $groupobject) {
        $groupstodisplay[$groupid] = $groupobject->name;
        $groupids[] = $groupid;
    }
    if (!in_array($group, $groupids)) {
        $group = 0;
        $PAGE->url->param('group', $group);
    }
    echo get_string('groupsvisible');
    echo $OUTPUT->single_select($PAGE->url, 'group', $groupstodisplay, $group);
}

// Output the roles menu.
$sql = "SELECT DISTINCT r.id, r.name, r.shortname
          FROM {role} r, {role_assignments} a
         WHERE a.contextid = :contextid
           AND r.id = a.roleid";
$params = array('contextid' => $context->id);
$roles = role_fix_names($DB->get_records_sql($sql, $params), $context);
$rolestodisplay = array(0 => get_string('allparticipants'));
foreach ($roles as $role) {
    $rolestodisplay[$role->id] = $role->localname;
}
echo '&nbsp;'.get_string('role');
echo $OUTPUT->single_select($PAGE->url, 'role', $rolestodisplay, $roleselected);
echo $OUTPUT->container_end();

// Apply group restrictions.
$params = array();
$groupjoin = '';
if ($group && $group != 0) {
    $groupjoin = 'JOIN {groups_members} g ON (g.groupid = :groupselected AND g.userid = u.id)';
    $params['groupselected'] = $group;
} else if ($groupuserid != 0 && !empty($groupids)) {
    $groupjoin = 'JOIN {groups_members} g ON (g.groupid IN ('.implode(',', $groupids).') AND g.userid = u.id)';
}

// Get the list of users enrolled in the course.
$picturefields = user_picture::fields('u');
$sql = "SELECT DISTINCT $picturefields, COALESCE(l.timeaccess, 0) AS lastonlinetime
          FROM {user} u
          JOIN {role_assignments} a ON (a.contextid = :contextid AND a.userid = u.id $rolewhere)
          $groupjoin
     LEFT JOIN {user_lastaccess} l ON (l.courseid = :courseid AND l.userid = u.id)";
$params['contextid'] = $context->id;
$params['courseid'] = $course->id;
$userrecords = $DB->get_records_sql($sql, $params);
if (get_config('block_completion_progress', 'showinactive') !== 1) {
    extract_suspended_users($context, $userrecords);
}
$userids = array_keys($userrecords);
$users = array_values($userrecords);
$numberofusers = count($users);
for ($i = 0; $i < $numberofusers; $i++) {
    $users[$i]->submissions = array();
}
$submissions = block_completion_progress_course_submissions($course->id);
foreach ($submissions as $mapping) {
    $mapvalues = explode('-', $mapping);
    $index = 0;
    while ($index < $numberofusers && $users[$index]->id != $mapvalues[0]) {
        $index++;
    }
    if ($index < $numberofusers) {
        $users[$index]->submissions[] = $mapvalues[1];
    }
}

$paged = $numberofusers > $perpage;
if (!$paged) {
    $page = 0;
}

// Form for messaging selected participants.
$formattributes = array('action' => $CFG->wwwroot.'/user/action_redir.php', 'method' => 'post', 'id' => 'participantsform');
echo html_writer::start_tag('form', $formattributes);
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'returnto', 'value' => s($PAGE->url->out(false))));

// Setup submissions table.
$table = new flexible_table('mod-block-completion-progress-overview');
$table->pagesize($perpage, $numberofusers);
$tablecolumns = array('picture', 'fullname', 'lastonline', 'progressbar', 'progress');
$table->define_columns($tablecolumns);
$tableheaders = array(
                    '',
                    get_string('fullname'),
                    get_string('lastonline', 'block_completion_progress'),
                    get_string('progressbar', 'block_completion_progress'),
                    get_string('progress', 'block_completion_progress')
                );
$table->define_headers($tableheaders);
$table->sortable(true);
$table->set_attribute('class', 'overviewTable');
$table->column_style_all('padding', '5px');
$table->column_style_all('text-align', 'left');
$table->column_style_all('vertical-align', 'middle');
$table->column_style('picture', 'width', '5%');
$table->column_style('fullname', 'width', '15%');
$table->column_style('lastonline', 'width', '15%');
$table->column_style('progressbar', 'min-width', '200px');
$table->column_style('progressbar', 'width', '*');
$table->column_style('progressbar', 'padding', '0');
$table->column_style('progress', 'text-align', 'center');
$table->column_style('progress', 'width', '8%');

$table->no_sorting('picture');
$table->no_sorting('progressbar');
$table->define_baseurl($PAGE->url);
$table->setup();

// Get range of students for page.
$startdisplay = $page * $perpage;
$enddisplay = ($startdisplay + $perpage > $numberofusers) ? $numberofusers : ($startdisplay + $perpage);
$sort = $table->get_sql_sort();
if (!$sort) {
     $sort = 'firstname DESC';
}
$sortbyprogress = strncmp($sort, 'progress', 8) == 0;
if ($sortbyprogress) {
    $startuser = 0;
    $enduser = $numberofusers;
} else {
    usort($users, 'block_completion_progress_compare_rows');
    $startuser = $startdisplay;
    $enduser = $enddisplay;
}

// Build array of user information.
$rows = array();
$exclusions = block_completion_progress_exclusions($course->id);
for ($i = $startuser; $i < $enduser; $i++) {
    $picture = $OUTPUT->user_picture($users[$i], array('course' => $course->id));
    $namelink = html_writer::link($CFG->wwwroot.'/user/view.php?id='.$users[$i]->id.'&course='.$course->id, fullname($users[$i]));
    if (empty($users[$i]->lastonlinetime)) {
        $lastonline = get_string('never');
    } else {
        $lastonline = userdate($users[$i]->lastonlinetime);
    }
    $useractivities = block_completion_progress_filter_visibility($activities, $users[$i]->id, $course->id, $exclusions);
    if (!empty($useractivities)) {
        $completions = block_completion_progress_completions($useractivities, $users[$i]->id, $course, $users[$i]->submissions);
        $progressbar = block_completion_progress_bar($useractivities, $completions, $config, $users[$i]->id, $course->id,
            $block->id, true);
        $progressvalue = block_completion_progress_percentage($useractivities, $completions);
        $progress = $progressvalue.'%';
    } else {
        $progressbar = get_string('no_visible_events_message', 'block_completion_progress');
        $progressvalue = 0;
        $progress = '?';
    }

    $rows[$i] = array(
        'firstname' => strtoupper($users[$i]->firstname),
        'lastname' => strtoupper($users[$i]->lastname),
        'picture' => $picture,
        'fullname' => $namelink,
        'lastonlinetime' => $users[$i]->lastonlinetime,
        'lastonline' => $lastonline,
        'progressbar' => $progressbar,
        'progressvalue' => $progressvalue,
        'progress' => $progress
    );
}

// Sort the user rows.
if ($sortbyprogress) {
    usort($rows, 'block_completion_progress_compare_rows');
}

// Build the table content and output.
if ($numberofusers > 0) {
    for ($i = $startdisplay; $i < $enddisplay; $i++) {
        $table->add_data(array($rows[$i]['picture'],
            $rows[$i]['fullname'], $rows[$i]['lastonline'],
            $rows[$i]['progressbar'], $rows[$i]['progress']));
    }
}
$table->print_html();

// Output paging controls.
$perpageurl = clone($PAGE->url);
if ($paged) {
    $perpageurl->param('perpage', SHOW_ALL_PAGE_SIZE);
    echo $OUTPUT->container(html_writer::link($perpageurl, get_string('showall', '', $numberofusers)), array(), 'showall');
} else if ($numberofusers > DEFAULT_PAGE_SIZE) {
    $perpageurl->param('perpage', DEFAULT_PAGE_SIZE);
    echo $OUTPUT->container(html_writer::link($perpageurl, get_string('showperpage', '', DEFAULT_PAGE_SIZE)), array(), 'showall');
}

// Organise access to JS for progress bars.
$jsmodule = array('name' => 'block_completion_progress', 'fullpath' => '/blocks/completion_progress/module.js');
$arguments = array(array($block->id), $userids);
$PAGE->requires->js_init_call('M.block_completion_progress.setupScrolling', array(), false, $jsmodule);
$PAGE->requires->js_init_call('M.block_completion_progress.init', $arguments, false, $jsmodule);

echo $OUTPUT->container_end();
echo $OUTPUT->footer();

/**
 * Compares two table row elements for ordering.
 *
 * @param  mixed $a element containing name, online time and progress info
 * @param  mixed $b element containing name, online time and progress info
 * @return order of pair expressed as -1, 0, or 1
 */
function block_completion_progress_compare_rows($a, $b) {
    global $sort;

    // Process each of the one or two orders.
    $orders = explode(',', $sort);
    foreach ($orders as $order) {

        // Extract the order information.
        $orderelements = explode(' ', trim($order));
        $aspect = $orderelements[0];
        $ascdesc = $orderelements[1];

        // Compensate for presented vs actual.
        switch ($aspect) {
            case 'name':
                $aspect = 'lastname';
                break;
            case 'lastonline':
                $aspect = 'lastonlinetime';
                break;
            case 'progress':
                $aspect = 'progressvalue';
                break;
        }

        // Check of order can be established.
        // Check of order can be established.
        if (is_array($a)) {
            $first = $a[$aspect];
            $second = $b[$aspect];
        } else {
            $first = $a->$aspect;
            $second = $b->$aspect;
        }

        if ($first < $second) {
            return $ascdesc == 'ASC' ? 1 : -1;
        }
        if ($first > $second) {
            return $ascdesc == 'ASC' ? -1 : 1;
        }
    }

    // If previous ordering fails, consider values equal.
    return 0;
}

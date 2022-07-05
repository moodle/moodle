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
 * Progress Bar block overview page
 *
 * @package    contrib
 * @subpackage block_iomad_progress
 * @copyright  2010 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Include required files.
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/blocks/iomad_progress/lib.php');
require_once($CFG->libdir.'/tablelib.php');

// Gather form data.
$id       = required_param('iomad_progressbarid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);
$departmentid = optional_param('departmentid', 0, PARAM_INT);

// Determine course and context.
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = block_iomad_progress_get_course_context($courseid);

// Get specific block config and context.
$iomad_progressblock = $DB->get_record('block_instances', array('id' => $id), '*', MUST_EXIST);
$iomad_progressconfig = unserialize(base64_decode($iomad_progressblock->configdata));
$iomad_progressblockcontext = block_iomad_progress_get_block_context($id);

// Set up page parameters.
$PAGE->set_course($course);
$PAGE->requires->css('/blocks/iomad_progress/styles.css');
$PAGE->set_url('/blocks/iomad_progress/overview.php', array('iomad_progressbarid' => $id, 'courseid' => $courseid, 'departmentid' => $departmentid));
$PAGE->set_context($context);
$title = get_string('overview', 'block_iomad_progress');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add($title);
$PAGE->set_pagelayout('base');

// Check user is logged in and capable of grading.
require_login($course, false);
require_capability('block/iomad_progress:overview', $iomad_progressblockcontext);

// Start page output.
echo $OUTPUT->header();
echo $OUTPUT->heading($title, 2);
echo $OUTPUT->container_start('block_iomad_progress');

// Get the modules to check iomad_progress on.
$modules = block_iomad_progress_modules_in_use($course->id);
if (empty($modules)) {
    echo get_string('no_events_config_message', 'block_iomad_progress');
    echo $OUTPUT->container_end();
    echo $OUTPUT->footer();
    die();
}

// Check if activities/resources have been selected in config.
$events = block_iomad_progress_event_information($iomad_progressconfig, $modules, $course->id);
if ($events == null) {
    echo get_string('no_events_message', 'block_iomad_progress');
    echo $OUTPUT->container_end();
    echo $OUTPUT->footer();
    die();
}
if (empty($events)) {
    echo get_string('no_visible_events_message', 'block_iomad_progress');
    echo $OUTPUT->container_end();
    echo $OUTPUT->footer();
    die();
}
$numevents = count($events);

// Determine if a role has been selected.
$sql = "SELECT DISTINCT r.id, r.name
          FROM {role} r, {role_assignments} a
         WHERE a.contextid = :contextid
           AND r.id = a.roleid
           AND r.shortname = :shortname";
$params = array('contextid' => $context->id, 'shortname' => 'student');
$studentrole = $DB->get_record_sql($sql, $params);
if ($studentrole) {
    $studentroleid = $studentrole->id;
} else {
    $studentroleid = 0;
}
$roleselected = optional_param('role', $studentroleid, PARAM_INT);
$rolewhere = $roleselected != 0 ? "AND a.roleid = $roleselected" : '';

// Output group selector if there are groups in the course.
echo $OUTPUT->container_start('iomad_progressoverviewmenus');
$groupuserid = 0;
if (!has_capability('moodle/site:accessallgroups', $context)) {
    $groupuserid = $USER->id;
}
$groups = groups_get_all_groups($course->id);
if (!empty($groups)) {
    $course->groupmode = 1;
    groups_print_course_menu($course, $PAGE->url);
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
echo '&nbsp;&nbsp;'.get_string('role');
echo $OUTPUT->single_select($PAGE->url, 'role', $rolestodisplay, $roleselected);

// Apply group restrictions.
$params = array();
$groupjoin = '';
$groupselected = groups_get_course_group($course);
if ($groupselected && $groupselected != 0) {
    $groupjoin = 'JOIN {groups_members} g ON (g.groupid = :groupselected AND g.userid = u.id)';
    $params['groupselected'] = $groupselected;
}

// IOMAD - get the appropriate users for the person looking.
$companyid = iomad::get_my_companyid($iomad_progressblockcontext);
// Check the department is valid.
if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
    print_error('invaliddepartment', 'block_iomad_company_admin');
}

// Get the associated department id.
$company = new company($companyid);
$parentlevel = company::get_company_parentnode($company->id);
$companydepartment = $parentlevel->id;

if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', context_system::instance())) {
    $userhierarchylevel = $parentlevel->id;
} else {
    $userlevel = $company->get_userlevel($USER);
    $userhierarchylevel = key($userlevel);
}
if ($departmentid == 0) {
    $departmentid = $userhierarchylevel;
}

// Get the appropriate list of departments.
$subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
$baseurl = new moodle_url('/blocks/iomad_progress/overview.php', array('iomad_progressbarid' => $id, 'course'=>$courseid, 'role'=>$roleselected));
echo '</br>'. get_string('department', 'block_iomad_company_admin');
echo $OUTPUT->single_select($baseurl, 'departmentid', $subhierarchieslist, $departmentid);
echo $OUTPUT->container_end();

if ($myusers = company::get_my_users($companyid, $departmentid)) {
    $myuserssql = " WHERE u.id IN (" . implode(',', array_keys($myusers)) . ") ";
} else {
    $myuserssql = " WHERE 1 = 2 ";
}

// Get the list of users enrolled in the course.
$picturefields = user_picture::fields('u');
$sql = "SELECT DISTINCT $picturefields, l.timeaccess as lastseen
         FROM {user} u
         JOIN {role_assignments} a ON (a.contextid = :contextid AND a.userid = u.id $rolewhere)
         $groupjoin
         LEFT JOIN {user_lastaccess} l ON (l.courseid = :courseid AND l.userid = u.id)
         $myuserssql";
$params['contextid'] = $context->id;
$params['courseid'] = $course->id;
$userrecords = $DB->get_records_sql($sql, $params);
$userids = array_keys($userrecords);
$users = array_values($userrecords);
$numberofusers = count($users);

// Form for messaging selected participants.
$formattributes = array('action' => $CFG->wwwroot.'/user/action_redir.php', 'method' => 'post', 'id' => 'participantsform');
echo html_writer::start_tag('form', $formattributes);
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'returnto', 'value' => s($PAGE->url->out(false))));

// Setup submissions table.
$table = new flexible_table('mod-block-iomad_progress-overview');
$tablecolumns = array('select', 'picture', 'fullname', 'lastonline', 'iomad_progressbar', 'iomad_progress');
$table->define_columns($tablecolumns);
$tableheaders = array(
                  '',
                  '',
                  get_string('fullname'),
                  get_string('lastonline', 'block_iomad_progress'),
                  get_string('iomad_progressbar', 'block_iomad_progress'),
                  get_string('iomad_progress', 'block_iomad_progress')
                );
$table->define_headers($tableheaders);
$table->sortable(true);

$table->set_attribute('class', 'generalbox');
$table->column_style_all('padding', '5px');
$table->column_style_all('text-align', 'left');
$table->column_style_all('vertical-align', 'middle');
$table->column_style('select', 'text-align', 'right');
$table->column_style('select', 'padding', '5px 0 5px 5px');
$table->column_style('iomad_progressbar', 'width', '200px');
$table->column_style('iomad_progress', 'text-align', 'center');

$table->no_sorting('select');
$table->no_sorting('picture');
$table->no_sorting('iomad_progressbar');
$table->define_baseurl($PAGE->url);
$table->setup();

// Build table of iomad_progress bars as they are marked.
for ($i = 0; $i < $numberofusers; $i++) {
    $selectattributes = array('type' => 'checkbox', 'class' => 'usercheckbox', 'name' => 'user'.$users[$i]->id);
    $select = html_writer::empty_tag('input', $selectattributes);
    $picture = $OUTPUT->user_picture($users[$i], array('course' => $course->id));
    $name = html_writer::link($CFG->wwwroot.'/user/view.php?id='.$users[$i]->id.'&course='.$course->id, fullname($users[$i]));
    if (empty($users[$i]->lastseen)) {
        $lastonline = get_string('never');
    } else {
        $lastonline = userdate($users[$i]->lastseen);
    }
    $userevents = block_iomad_progress_filter_visibility($events, $users[$i]->id, $context, $course);
    if (!empty($userevents)) {
        $attempts = block_iomad_progress_attempts($modules, $iomad_progressconfig, $userevents, $users[$i]->id, $course->id);
        $iomad_progressbar = block_iomad_progress_bar($modules, $iomad_progressconfig, $userevents, $users[$i]->id, $iomad_progressblock->id, $attempts,
            $course->id, true);
        $iomad_progressvalue = block_iomad_progress_percentage($userevents, $attempts, true);
        $iomad_progress = $iomad_progressvalue.'%';
    }
    else {
        $iomad_progressbar = get_string('no_visible_events_message', 'block_iomad_progress');
        $iomad_progressvalue = 0;
        $iomad_progress = '?';
    }

    $rows[] = array(
        'firstname' => $users[$i]->firstname,
        'lastname' => strtoupper($users[$i]->lastname),
        'select' => $select,
        'picture' => $picture,
        'fullname' => $name,
        'lastonlinetime' => (empty($users[$i]->lastseen) ? 0 : $users[$i]->lastseen),
        'lastonline' => $lastonline,
        'iomad_progressbar' => $iomad_progressbar,
        'iomad_progressvalue' => $iomad_progressvalue,
        'iomad_progress' => $iomad_progress
    );
}

// Build the table content and output.
if (!$sort = $table->get_sql_sort()) {
     $sort = 'lastname DESC';
}
if ($numberofusers > 0) {
    usort($rows, 'block_iomad_progress_compare_rows');
    foreach ($rows as $row) {
        $table->add_data(array($row['select'], $row['picture'],
            $row['fullname'], $row['lastonline'],
            $row['iomad_progressbar'], $row['iomad_progress']));
    }
}
$table->print_initials_bar();
$table->print_html();

// Output messaging controls.
echo html_writer::start_tag('div', array('class' => 'buttons'));
echo html_writer::empty_tag('input', array('type' => 'button', 'id' => 'checkall', 'value' => get_string('selectall')));
echo html_writer::empty_tag('input', array('type' => 'button', 'id' => 'checknone', 'value' => get_string('deselectall')));
$displaylist = array();
$displaylist['messageselect.php'] = get_string('messageselectadd');
if (!empty($CFG->enablenotes) && has_capability('moodle/notes:manage', $context)) {
    $displaylist['addnote.php'] = get_string('addnewnote', 'notes');
    $displaylist['groupaddnote.php'] = get_string('groupaddnewnote', 'notes');
}
echo html_writer::tag('label', get_string("withselectedusers"), array('for' => 'formactionid'));
echo html_writer::select($displaylist, 'formaction', '', array('' => 'choosedots'), array('id' => 'formactionid'));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'id', 'value' => $course->id));
echo html_writer::start_tag('noscript', array('style' => 'display:inline;'));
echo html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('ok')));
echo html_writer::end_tag('noscript');
echo $OUTPUT->help_icon('withselectedusers');
echo html_writer::end_tag('div');
echo html_writer::end_tag('form');

// Organise access to JS for messaging.
$module = array('name' => 'core_user', 'fullpath' => '/user/module.js');
$PAGE->requires->js_init_call('M.core_user.init_participation', null, false, $module);

// Organise access to JS for iomad_progress bars.
$jsmodule = array('name' => 'block_iomad_progress', 'fullpath' => '/blocks/iomad_progress/module.js');
$arguments = array(array($iomad_progressblock->id), $userids);
$PAGE->requires->js_init_call('M.block_iomad_progress.init', $arguments, false, $jsmodule);

echo $OUTPUT->container_end();
echo $OUTPUT->footer();

/**
 * Compares two table row elements for ordering.
 *
 * @param  mixed $a element containing name, online time and iomad_progress info
 * @param  mixed $b element containing name, online time and iomad_progress info
 * @return order of pair expressed as -1, 0, or 1
 */
function block_iomad_progress_compare_rows($a, $b) {
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
            case 'iomad_progress':
                $aspect = 'iomad_progressvalue';
                break;
        }

        // Check of order can be established.
        if ($a[$aspect] < $b[$aspect]) {
            return $ascdesc == 'ASC'?1:-1;
        }
        if ($a[$aspect] > $b[$aspect]) {
            return $ascdesc == 'ASC'?-1:1;
        }
    }

    // If previous ordering fails, consider values equal.
    return 0;
}

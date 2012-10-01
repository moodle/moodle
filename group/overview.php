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
 * Print an overview of groupings & group membership
 *
 * @copyright  Matt Clarkson mattc@catalyst.net.nz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package    core_group
 */

require_once('../config.php');
require_once($CFG->libdir . '/filelib.php');

$courseid   = required_param('id', PARAM_INT);
$groupid    = optional_param('group', 0, PARAM_INT);
$groupingid = optional_param('grouping', 0, PARAM_INT);

$returnurl = $CFG->wwwroot.'/group/index.php?id='.$courseid;
$rooturl   = $CFG->wwwroot.'/group/overview.php?id='.$courseid;

if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
    print_error('invalidcourse');
}

$url = new moodle_url('/group/overview.php', array('id'=>$courseid));
if ($groupid !== 0) {
    $url->param('group', $groupid);
}
if ($groupingid !== 0) {
    $url->param('grouping', $groupingid);
}
$PAGE->set_url($url);

// Make sure that the user has permissions to manage groups.
require_login($course);

$context = context_course::instance($courseid);
require_capability('moodle/course:managegroups', $context);

$strgroups           = get_string('groups');
$strparticipants     = get_string('participants');
$stroverview         = get_string('overview', 'group');
$strgrouping         = get_string('grouping', 'group');
$strgroup            = get_string('group', 'group');
$strnotingrouping    = get_string('notingrouping', 'group');
$strfiltergroups     = get_string('filtergroups', 'group');
$strnogroups         = get_string('nogroups', 'group');
$strdescription      = get_string('description');

// Get all groupings
$groupings = $DB->get_records('groupings', array('courseid'=>$courseid), 'name');
$members = array();
foreach ($groupings as $grouping) {
    $members[$grouping->id] = array();
}
$members[-1] = array(); //groups not in a grouping

// Get all groups
$groups = $DB->get_records('groups', array('courseid'=>$courseid), 'name');

$params = array('courseid'=>$courseid);
if ($groupid) {
    $groupwhere = "AND g.id = :groupid";
    $params['groupid']   = $groupid;
} else {
    $groupwhere = "";
}

if ($groupingid) {
    $groupingwhere = "AND gg.groupingid = :groupingid";
    $params['groupingid'] = $groupingid;
} else {
    $groupingwhere = "";
}

list($sort, $sortparams) = users_order_by_sql('u');

$sql = "SELECT g.id AS groupid, gg.groupingid, u.id AS userid, u.firstname, u.lastname, u.idnumber, u.username
          FROM {groups} g
               LEFT JOIN {groupings_groups} gg ON g.id = gg.groupid
               LEFT JOIN {groups_members} gm ON g.id = gm.groupid
               LEFT JOIN {user} u ON gm.userid = u.id
         WHERE g.courseid = :courseid $groupwhere $groupingwhere
      ORDER BY g.name, $sort";

$rs = $DB->get_recordset_sql($sql, array_merge($params, $sortparams));
foreach ($rs as $row) {
    $user = new stdClass();
    $user->id        = $row->userid;
    $user->firstname = $row->firstname;
    $user->lastname  = $row->lastname;
    $user->username  = $row->username;
    $user->idnumber  = $row->idnumber;
    if (!$row->groupingid) {
        $row->groupingid = -1;
    }
    if (!array_key_exists($row->groupid, $members[$row->groupingid])) {
        $members[$row->groupingid][$row->groupid] = array();
    }
    if(isset($user->id)){
        $members[$row->groupingid][$row->groupid][] = $user;
    }
}
$rs->close();

navigation_node::override_active_url(new moodle_url('/group/index.php', array('id'=>$courseid)));
$PAGE->navbar->add(get_string('overview', 'group'));

/// Print header
$PAGE->set_title($strgroups);
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('standard');
echo $OUTPUT->header();

// Add tabs
$currenttab = 'overview';
require('tabs.php');

/// Print overview
echo $OUTPUT->heading(format_string($course->shortname, true, array('context' => $context)) .' '.$stroverview, 3);

echo $strfiltergroups;

$options = array();
$options[0] = get_string('all');
foreach ($groupings as $grouping) {
    $options[$grouping->id] = strip_tags(format_string($grouping->name));
}
$popupurl = new moodle_url($rooturl.'&group='.$groupid);
$select = new single_select($popupurl, 'grouping', $options, $groupingid, array());
$select->label = $strgrouping;
$select->formid = 'selectgrouping';
echo $OUTPUT->render($select);

$options = array();
$options[0] = get_string('all');
foreach ($groups as $group) {
    $options[$group->id] = strip_tags(format_string($group->name));
}
$popupurl = new moodle_url($rooturl.'&grouping='.$groupingid);
$select = new single_select($popupurl, 'group', $options, $groupid, array());
$select->label = $strgroup;
$select->formid = 'selectgroup';
echo $OUTPUT->render($select);

/// Print table
$printed = false;
$hoverevents = array();
foreach ($members as $gpgid=>$groupdata) {
    if ($groupingid and $groupingid != $gpgid) {
        continue; // do not show
    }
    $table = new html_table();
    $table->head  = array(get_string('groupscount', 'group', count($groupdata)), get_string('groupmembers', 'group'), get_string('usercount', 'group'));
    $table->size  = array('20%', '70%', '10%');
    $table->align = array('left', 'left', 'center');
    $table->width = '90%';
    $table->data  = array();
    foreach ($groupdata as $gpid=>$users) {
        if ($groupid and $groupid != $gpid) {
            continue;
        }
        $line = array();
        $name = print_group_picture($groups[$gpid], $course->id, false, true, false) . format_string($groups[$gpid]->name);
        $description = file_rewrite_pluginfile_urls($groups[$gpid]->description, 'pluginfile.php', $context->id, 'group', 'description', $gpid);
        $options = new stdClass;
        $options->noclean = true;
        $options->overflowdiv = true;
        $jsdescription = trim(format_text($description, $groups[$gpid]->descriptionformat, $options));
        if (empty($jsdescription)) {
            $line[] = $name;
        } else {
            $line[] = html_writer::tag('span', $name, array('id'=>'group_'.$gpid));
            $hoverevents['group_'.$gpid] = $jsdescription;
        }
        $fullnames = array();
        foreach ($users as $user) {
            $fullnames[] = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$course->id.'">'.fullname($user, true).'</a>';
        }
        $line[] = implode(', ', $fullnames);
        $line[] = count($users);
        $table->data[] = $line;
    }
    if ($groupid and empty($table->data)) {
        continue;
    }
    if ($gpgid < 0) {
        echo $OUTPUT->heading($strnotingrouping, 3);
    } else {
        echo $OUTPUT->heading(format_string($groupings[$gpgid]->name), 3);
        $description = file_rewrite_pluginfile_urls($groupings[$gpgid]->description, 'pluginfile.php', $context->id, 'grouping', 'description', $gpgid);
        $options = new stdClass;
        $options->noclean = true;
        $options->overflowdiv = true;
        echo $OUTPUT->box(format_text($description, $groupings[$gpgid]->descriptionformat, $options), 'generalbox boxwidthnarrow boxaligncenter');
    }
    echo html_writer::table($table);
    $printed = true;
}

if (count($hoverevents)>0) {
    $PAGE->requires->string_for_js('description', 'moodle');
    $PAGE->requires->js_init_call('M.core_group.init_hover_events', array($hoverevents));
}

echo $OUTPUT->footer();

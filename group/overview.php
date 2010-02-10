<?php
/**
 * Print an overview of groupings & group membership
 *
 * @author  Matt Clarkson mattc@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */

require_once('../config.php');

$courseid   = required_param('id', PARAM_INT);
$groupid    = optional_param('group', 0, PARAM_INT);
$groupingid = optional_param('grouping', 0, PARAM_INT);

$returnurl = $CFG->wwwroot.'/group/index.php?id='.$courseid;
$rooturl   = $CFG->wwwroot.'/group/overview.php?id='.$courseid;

if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
    print_error('invalidcourse');
}

$url = new moodle_url('/group/overview.php', array('course'=>$courseid));
if ($groupid !== 0) {
    $url->param('group', $groupid);
}
if ($groupingid !== 0) {
    $url->param('grouping', $groupingid);
}
$PAGE->set_url($url);

// Make sure that the user has permissions to manage groups.
require_login($course);

$context = get_context_instance(CONTEXT_COURSE, $courseid);
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
if (empty($CFG->enablegroupings)) {
    $groupings  = array();
    $members    = array(-1 => array()); //groups not in a grouping
    $groupingid = 0;
} else {
    $groupings = $DB->get_records('groupings', array('courseid'=>$courseid), 'name');
    $members = array();
    foreach ($groupings as $grouping) {
        $members[$grouping->id] = array();
    }
    $members[-1] = array(); //groups not in a grouping
}

// Get all groups
$groups = $DB->get_records('groups', array('courseid'=>$courseid), 'name');

$params = array('courseid'=>$courseid);
if ($groupid) {
    $groupwhere = "AND g.id = :groupid";
    $params['groupid']   = $groupid;
} else {
    $groupwhere = "";
}

if (empty($CFG->enablegroupings)) {
    $sql = "SELECT g.id AS groupid, NULL AS groupingid, u.id AS userid, u.firstname, u.lastname, u.idnumber, u.username
              FROM {groups} g
                   LEFT JOIN {groups_members} gm ON g.id = gm.groupid
                   LEFT JOIN {user} u ON gm.userid = u.id
             WHERE g.courseid = :courseid $groupwhere
          ORDER BY g.name, u.lastname, u.firstname";
} else {
    if ($groupingid) {
        $groupingwhere = "AND gg.groupingid = :groupingid";
        $params['groupingid'] = $groupingid;
    } else {
        $groupingwhere = "";
    }
    $sql = "SELECT g.id AS groupid, gg.groupingid, u.id AS userid, u.firstname, u.lastname, u.idnumber, u.username
              FROM {groups} g
                   LEFT JOIN {groupings_groups} gg ON g.id = gg.groupid
                   LEFT JOIN {groups_members} gm ON g.id = gm.groupid
                   LEFT JOIN {user} u ON gm.userid = u.id
             WHERE g.courseid = :courseid $groupwhere $groupingwhere
          ORDER BY g.name, u.lastname, u.firstname";
}

if ($rs = $DB->get_recordset_sql($sql, $params)) {
    foreach ($rs as $row) {
        $user = new object();
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
}

$PAGE->requires->js('/lib/overlib/overlib.js', true);
$PAGE->requires->js('/lib/overlib/overlib_cssstyle.js', true);

$PAGE->navbar->add($strparticipants, new moodle_url('/user/index.php', array('id'=>$courseid)));
$PAGE->navbar->add($strgroups);

/// Print header
$PAGE->set_title($strgroups);
$PAGE->set_heading(': '.$strgroups);
echo $OUTPUT->header();

// Add tabs
$currenttab = 'overview';
require('tabs.php');

/// Print overview
echo $OUTPUT->heading(format_string($course->shortname) .' '.$stroverview, 3);

echo $strfiltergroups;

if (!empty($CFG->enablegroupings)) {
    $options = array();
    $options[0] = get_string('all');
    foreach ($groupings as $grouping) {
        $options[$grouping->id] = strip_tags(format_string($grouping->name));
    }
    $popupurl = new moodle_url($rooturl.'&group='.$groupid);
    $select = new single_select($popupurl, 'grouping', $options, $groupingid);
    $select->label = $strgrouping;
    $select->formid = 'selectgrouping';
    echo $OUTPUT->render($select);
}

$options = array();
$options[0] = get_string('all');
foreach ($groups as $group) {
    $options[$group->id] = strip_tags(format_string($group->name));
}
$popupurl = new moodle_url($rooturl.'&grouping='.$groupingid);
$select = new single_select($popupurl, 'group', $options, $groupid);
$select->label = $strgroup;
$select->formid = 'selectgroup';
echo $OUTPUT->render($select);

/// Print table
$printed = false;
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
        $name = format_string($groups[$gpid]->name);
        $description = file_rewrite_pluginfile_urls($groups[$gpid]->description, 'pluginfile.php', $context->id, 'course_group_description', $gpid);
        $options = new stdClass;
        $options->noclean = true;
        $jsdescription = addslashes_js(trim(format_text($description, $groups[$gpid]->descriptionformat, $options)));
        if (empty($jsdescription)) {
            $line[] = $name;
        } else {
            $jsstrdescription = addslashes_js($strdescription);
            $overlib = "return overlib('$jsdescription', BORDER, 0, FGCLASS, 'description', "
                      ."CAPTIONFONTCLASS, 'caption', CAPTION, '$jsstrdescription');";
            $line[] = '<span onmouseover="'.s($overlib).'" onmouseout="return nd();">'.$name.'</span>';
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
    if (!empty($CFG->enablegroupings)) {
        if ($gpgid < 0) {
            echo $OUTPUT->heading($strnotingrouping, 3);
        } else {
            echo $OUTPUT->heading(format_string($groupings[$gpgid]->name), 3);
            $description = file_rewrite_pluginfile_urls($groupings[$gpgid]->description, 'pluginfile.php', $context->id, 'course_grouping_description', $gpgid);
            $options = new stdClass;
            $options->noclean = true;
            echo $OUTPUT->box(format_text($description, $groupings[$gpgid]->descriptionformat, $options), 'generalbox boxwidthnarrow boxaligncenter');
        }
    }
    echo $OUTPUT->table($table);
    $printed = true;
}

echo $OUTPUT->footer();

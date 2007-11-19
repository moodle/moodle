<?php // $Id$
/**
 * Create and allocate users go groups
 *
 * @author  Matt Clarkson mattc@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */

require_once('../config.php');
require_once('lib.php');
require_once('autogroup_form.php');

if (!defined('AUTOGROUP_MIN_RATIO')) {
    define('AUTOGROUP_MIN_RATIO', 0.7); // means minimum member count is 70% in the smallest group
}

$courseid = required_param('courseid', PARAM_INT);

if (!$course = get_record('course', 'id', $courseid)) {
    error('invalidcourse');
}

// Make sure that the user has permissions to manage groups.
require_login($course);

$context       = get_context_instance(CONTEXT_COURSE, $courseid);
$systemcontext = get_context_instance(CONTEXT_SYSTEM);
require_capability('moodle/course:managegroups', $context);

$returnurl = $CFG->wwwroot.'/group/index.php?id='.$course->id;

$strgroups           = get_string('groups');
$strparticipants     = get_string('participants');
$strautocreategroups = get_string('autocreategroups', 'group');

// Print the page and form
$navlinks = array(array('name'=>$strparticipants, 'link'=>$CFG->wwwroot.'/user/index.php?id='.$courseid, 'type'=>'misc'),
                  array('name' => $strgroups, 'link' => "$CFG->wwwroot/group/index.php?id=$courseid", 'type' => 'misc'),
                  array('name' => $strautocreategroups, 'link' => null, 'type' => 'misc'));
$navigation = build_navigation($navlinks);

$preview = '';
$error = '';

/// Get applicable roles
$rolenames = array();
if ($roles = get_roles_used_in_context($context, true)) {
    $canviewroles    = get_roles_with_capability('moodle/course:view', CAP_ALLOW, $context);
    $doanythingroles = get_roles_with_capability('moodle/site:doanything', CAP_ALLOW, $systemcontext);

    foreach ($roles as $role) {
        if (!isset($canviewroles[$role->id])) {   // Avoid this role (eg course creator)
            continue;
        }
        if (isset($doanythingroles[$role->id])) {   // Avoid this role (ie admin)
            continue;
        }
        $rolenames[$role->id] = strip_tags(role_get_name($role, $context));   // Used in menus etc later on
    }
}

/// Create the form
$editform = new autogroup_form(null, array('roles' => $rolenames));
$editform->set_data(array('courseid' => $courseid, 'seed' => time()));

/// Handle form submission
if ($editform->is_cancelled()) {
    redirect($returnurl);

} elseif ($data = $editform->get_data(false)) {

    /// Allocate members from the selected role to groups
    switch ($data->allocateby) {
        case 'no':
        case 'random':
        case 'lastname':
            $orderby = 'lastname, firstname'; break;
        case 'firstname':
            $orderby = 'firstname, lastname'; break;
        case 'idnumber':
            $orderby = 'idnumber'; break;
        default:
            error('Unknown ordering');
    }
    $users = groups_get_potential_members($data->courseid, $data->roleid, $orderby);
    $usercnt = count($users);

    if ($data->allocateby == 'random') {
        srand($data->seed);
        shuffle($users);
    }

    $groups = array();

    // Plan the allocation
    if ($data->groupby == 'groups') {
    	$numgrps    = $data->number;
        $userpergrp = floor($usercnt/$numgrps);

    } else { // members
    	$numgrps    = ceil($usercnt/$data->number);
        $userpergrp = $data->number;

        if (!empty($data->nosmallgroups) and $usercnt % $data->number != 0) {
            // If there would be one group with a small number of member reduce the number of groups
            $missing = $userpergrp * $numgrps - $usercnt;
            if ($missing > $userpergrp * (1-AUTOGROUP_MIN_RATIO)) {
                // spread the users from the last small group
                $numgrps--;
                $userpergrp = floor($usercnt/$numgrps);
            }
        }
    }

    // allocate the users - all groups equal count first
    for ($i=0; $i<$numgrps; $i++) {
        $groups[$i] = array();
        $groups[$i]['name']    = groups_parse_name(trim($data->namingscheme), $i);
        $groups[$i]['members'] = array();
        if ($data->allocateby == 'no') {
            continue; // do not allocate users
        }
        for ($j=0; $j<$userpergrp; $j++) {
            if (empty($users)) {
                break 2;
            }
            $user = array_shift($users);
            $groups[$i]['members'][$user->id] = $user;
        }
    }
    // now distribute the rest
    if ($data->allocateby != 'no') {
        for ($i=0; $i<$numgrps; $i++) {
            if (empty($users)) {
                break 1;
            }
            $user = array_shift($users);
            $groups[$i]['members'][$user->id] = $user;
        }
    }

    if (isset($data->preview)) {
        $table = new object();
        if ($data->allocateby == 'no') {
            $table->head  = array(get_string('groupscount', 'group', $numgrps));
            $table->size  = array('100%');
            $table->align = array('left');
            $table->width = '40%';
        } else {
            $table->head  = array(get_string('groupscount', 'group', $numgrps), get_string('groupmembers', 'group'), get_string('usercounttotal', 'group', $usercnt));
            $table->size  = array('20%', '70%', '10%');
            $table->align = array('left', 'left', 'center');
            $table->width = '90%';
        }
        $table->data  = array();

        foreach ($groups as $group) {
            $line = array();
            if (groups_get_group_by_name($courseid, $group['name'])) {
                $line[] = '<span class="notifyproblem">'.get_string('groupnameexists', 'group', $group['name']).'</span>';
                $error = get_string('groupnameexists', 'group', $group['name']);
            } else {
                $line[] = $group['name'];
            }
            if ($data->allocateby != 'no') {
                $unames = array();
                foreach ($group['members'] as $user) {
                    $unames[] = fullname($user, true);
                }
                $line[] = implode(', ', $unames);
                $line[] = count($group['members']);
            }
            $table->data[] = $line;
        }

        $preview .= print_table($table, true);

    } else {
        $grouping = null;
        $createdgrouping = null;
        $createdgroups = array();
        $failed = false;

        // prepare grouping
        if (!empty($data->grouping)) {
            $groupingname = trim($data->groupingname);
            if ($data->grouping < 0) {
                $grouping = new object();
                $grouping->courseid = $COURSE->id;
                $grouping->name     = $groupingname;
                if (!$grouping->id = groups_create_grouping(addslashes_recursive($grouping))) {
                    $error = 'Can not create grouping'; //should not happen
                    $failed = true;
                }
                $createdgrouping = $grouping->id;
            } else {
                $grouping = groups_get_grouping($data->grouping);
            }
        }

        // Save the groups data
    	foreach ($groups as $key=>$group) {
            if (groups_get_group_by_name($courseid, $group['name'])) {
                $error = get_string('groupnameexists', 'group', $group['name']);
                $failed = true;
                break;
            }
            $newgroup = new object();
            $newgroup->courseid = $data->courseid;
            $newgroup->name     = $group['name'];
            if (!$groupid = groups_create_group(addslashes_recursive($newgroup))) {
                $error = 'Can not create group!'; // should not happen
                $failed = true;
                break;
            }
            $createdgroups[] = $groupid;
            foreach($group['members'] as $user) {
                groups_add_member($groupid, $user->id);
            }
            if ($grouping) {
                groups_assign_grouping($grouping->id, $groupid);
            }
        }

        if ($failed) {
            foreach ($createdgroups as $groupid) {
                groups_delete_group($groupid);
            }
            if ($createdgrouping) {
                groups_delete_grouping($createdgrouping);
            }
        } else {
            redirect($returnurl);
        }
    }
}

/// Print header
print_header_simple($strgroups, ': '.$strgroups, $navigation, '', '', true, '', navmenu($course));
print_heading($strautocreategroups);

if ($error != '') {
    notify($error);
}

/// Display the form
$editform->display();

if($preview !== '') {
	print_heading(get_string('groupspreview', 'group'));

    echo $preview;
}

print_footer($course);
?>
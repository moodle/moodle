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
require_once('autogroup_form.php');

$courseid = required_param('courseid', PARAM_INT);

if (!$course = get_record('course', 'id',$courseid)) {
    error('invalidcourse');
}

// Make sure that the user has permissions to manage groups.
require_login($course);

$context = get_context_instance(CONTEXT_COURSE, $courseid);
$sitecontext = get_context_instance(CONTEXT_SYSTEM);
require_capability('moodle/course:managegroups', $context);

$returnurl = $CFG->wwwroot.'/group/index.php?id='.$course->id;

$strgroups = get_string('groups');
$strparticipants = get_string('participants');
$stroverview = get_string('overview', 'group');
$strgrouping = get_string('grouping', 'group');
$strgroup = get_string('group', 'group');
$strnotingrouping = get_string('notingrouping', 'group');
$strfiltergroups = get_string('filtergroups', 'group');


// Print the page and form
$navlinks = array(array('name'=>$strparticipants, 'link'=>$CFG->wwwroot.'/user/index.php?id='.$courseid, 'type'=>'misc'),
                  array('name'=>$strgroups, 'link'=>'', 'type'=>'misc'));
$navigation = build_navigation($navlinks);


/// Get applicable roles
$rolenames = array();
$avoidroles = array();

if ($roles = get_roles_used_in_context($context, true)) {
    $canviewroles    = get_roles_with_capability('moodle/course:view', CAP_ALLOW, $context);
    $doanythingroles = get_roles_with_capability('moodle/site:doanything', CAP_ALLOW, $sitecontext);

    foreach ($roles as $role) {
        if (!isset($canviewroles[$role->id])) {   // Avoid this role (eg course creator)
            $avoidroles[] = $role->id;
            unset($roles[$role->id]);
            continue;
        }
        if (isset($doanythingroles[$role->id])) {   // Avoid this role (ie admin)
            $avoidroles[] = $role->id;
            unset($roles[$role->id]);
            continue;
        }
        $rolenames[$role->id] = strip_tags(role_get_name($role, $context));   // Used in menus etc later on
    }
}

/// Create the form
$editform = new autogroup_form('autogroup.php', array('roles' => $rolenames));
$editform->set_data(array('courseid' => $courseid,
                          'seed' => time()));



/// Handle form submission
if ($editform->is_cancelled()) {
    redirect($returnurl);
} elseif ($data = $editform->get_data()) {
    
   /// Allocate members from the selected role to groups
    if ($data->allocateby == 'random') {
    	$orderby = 'firstname';
    } else {
        $orderby = $data->allocateby;
    }
    $users = groups_get_potental_members($data->courseid, $data->roleid, $orderby);
    $usercnt = count($users);
    
    if ($data->allocateby == 'random') {
        srand ($data->seed);
        shuffle($users);
    }
    
    $groups = array();
    $i = 0;
    $cnt = 0;
    
    if ($data->groupby == 'groups') {
    	$numgrps = $data->number;
        $userpergrp = ceil($usercnt/$numgrps);
    } else {
    	$numgrps = ceil($data->number/$usercnt);
        $userpergrp = $data->number;
    }
    
    foreach($users as $id => $user) {
    	if (!isset($groups[$i])) { // Create a new group
    		$groups[$i]['name'] = groups_parse_name($data->namingschemegrp['namingscheme'], $i);
    	}
        @$groups[$i]['members'][] = &$users[$id];
        $cnt++;
        if ($cnt == $userpergrp) {
        	$cnt = 0;
            $i++;
        }
    }
   
   
    if (isset($data->preview)) {
       /// Print the groups preview
    	$preview = '<ul>';
        foreach ($groups as $group) {
        	$preview .= "<li>$group[name]\n<ul>";
            foreach ($group['members'] as $member) {
            	$preview .= '<li>'.fullname($member).'</li>';
            }
            $preview .= "</ul>\n</li>\n";
        }
        $preview .= '</ul>';
    } else {
       /// Save the groups data 
    	foreach ($groups as $group) {        
            $newgroup->timecreated = time();
            $newgroup->timemodified = $newgroup->timecreated;
            $newgroup->courseid = $data->courseid;
            $newgroup->name = $group['name'];
            $groupid = insert_record('groups', $newgroup);
            foreach($group['members'] as $user) {
            	$member->groupid = $groupid;
                $member->userid = $user->id;
                $member->timeadded = time();
                insert_record('groups_members', $member);
            }
        }
        redirect($returnurl);
    }

}
 
/// Print header
print_header_simple($strgroups, ': '.$strgroups, $navigation, '', '', true, '', navmenu($course));

/// Display the form
$editform->display();
if(isset($preview)) {
	print_heading_block(get_string('groupspreview', 'group'));
    print_box($preview);
}

print_footer($course);
?>
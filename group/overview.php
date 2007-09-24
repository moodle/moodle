<?php // $Id$
/**
 * Print an overview of groupings & group membership
 *
 * @author  Matt Clarkson mattc@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */
 
require_once('../config.php');

$courseid = required_param('id', PARAM_INT);
$groupid = optional_param('groupid', 0, PARAM_INT);
$groupingid = optional_param('groupingid', 0, PARAM_INT);

$returnurl = $CFG->wwwroot.'/group/index.php?id='.$courseid;

if (!$course = get_record('course', 'id',$courseid)) {
    error('invalidcourse');
}

// Make sure that the user has permissions to manage groups.
require_login($course);

$context = get_context_instance(CONTEXT_COURSE, $courseid);
require_capability('moodle/course:managegroups', $context);



$strgroups = get_string('groups');
$strparticipants = get_string('participants');
$stroverview = get_string('overview', 'group');
$strgrouping = get_string('grouping', 'group');
$strgroup = get_string('group', 'group');
$strnotingrouping = get_string('notingrouping', 'group');
$strfiltergroups = get_string('filtergroups', 'group');
$strnogroups = get_string('nogroups', 'group');
$strnogroupsassigned = get_string('nogroupsassigned', 'group');

// Print the page and form
$navlinks = array(array('name'=>$strparticipants, 'link'=>$CFG->wwwroot.'/user/index.php?id='.$courseid, 'type'=>'misc'),
                  array('name'=>$strgroups, 'link'=>'', 'type'=>'misc'));
$navigation = build_navigation($navlinks);

/// Print header
print_header_simple($strgroups, ': '.$strgroups, $navigation, '', '', true, '', navmenu($course));


if (!empty($CFG->enablegroupings)) {
    // Add tabs
    $currenttab = 'overview';
    require('tabs.php');
}

$groupings= array();

// Get groupings and child group id's
if (!empty($CFG->enablegroupings)) {
	$sql = "SELECT gs.id, gs.name, gg.groupid " .
           "FROM {$CFG->prefix}groupings gs " .
                "LEFT JOIN {$CFG->prefix}groupings_groups gg ON gs.id = gg.groupingid " .
           "WHERE gs.courseid = {$course->id} " .
           "ORDER BY gs.name, gs.id ";

    $rs = get_recordset_sql($sql);
    while ($row = rs_fetch_next_record($rs)) {
        $groupings[] = $row;
    }
}

// Get groups & group members
$sql = "SELECT g.id AS groupid, g.name, u.id AS userid, u.firstname, u.lastname, u.idnumber, u.username " .
       "FROM {$CFG->prefix}groups g " .
            "LEFT JOIN {$CFG->prefix}groups_members gm ON g.id = gm.groupid " .
            "LEFT JOIN {$CFG->prefix}user u ON gm.userid = u.id " .
       "WHERE g.courseid = {$course->id} " .
       "ORDER BY g.name, g.id ";
       
$rs = get_recordset_sql($sql);

$groupsmembers = array();

// Build a hash of keyed on groupid and userid;
while ($row = rs_fetch_next_record($rs)) {
	$groupsmembers[$row->groupid]->name = $row->name;
    $groupsmembers[$row->groupid]->groupid = $row->groupid;
    $groupsmembers[$row->groupid]->users[$row->userid] = $row;
    $groupsmembers[$row->groupid]->printed = false;
}

if (empty($groupsmembers)) {
    print_box($strnogroups);
} else {
	
    /// Print overview filter form
    
    echo '<form method="get" action="overview.php">';
    echo "<input type=\"hidden\" name=\"id\" value=\"{$course->id}\" />";
    echo "<label for=\"groupingselect\">$strfiltergroups $strgrouping </label>";
    echo '<select id="groupingselect" name="groupingid" onchange="this.parentNode.submit();">';
    echo '    <option value=""></option>';
    $lastgroupingid = false;
    foreach ($groupings as $grouping) {
        if ($lastgroupingid === false || $lastgroupingid != $grouping->id) {
            $selected = $grouping->id == $groupingid ? 'selected="selected"':'';
            echo "<option value=\"{$grouping->id}\" $selected>".format_string($grouping->name)."</option>\n";
        }
        $lastgroupingid = $grouping->id;
    }
    echo '</select>';
    
    echo "<label for=\"groupselect\"> $strgroup </label>";
    echo '<select id="groupselect" name="groupid" onchange="this.parentNode.submit();">';
    echo '    <option value=""></option>';
    $lastgroupid = false;
    
    foreach ($groupsmembers as $group) {
        if ($lastgroupid === false || $lastgroupid != $group->groupid) {
            $selected = $group->groupid == $groupid ? 'selected="selected"':'';
            echo "<option value=\"{$group->groupid}\" $selected>".format_string($group->name)."</option>\n";
        }
        $lastgroupid = $group->groupid ;
    }
    echo '</select>';
    
    echo '</form>';
    
    
    /// Print overview
    print_heading(format_string($course->shortname) .' '.$stroverview, 'center', 3);
    

    
    echo '<div id="grouping-groups-overview"><ul>';
    
    if (!empty($CFG->enablegroupings) && isset($groupings)) {
    	$lastgroupingid = false;
    	foreach ($groupings as $grouping) {
    		if (!empty($groupingid) && $groupingid != $grouping->id) {
                continue;
            }
            if (!empty($groupid) && $groupid != $grouping->groupid) {
                continue;
            }
    		if ($lastgroupingid === false || $lastgroupingid != $grouping->id) {
    			if($lastgroupingid !== false) {
                    echo '</ul></li>';
                }
    
                echo "<li>$strgrouping: {$grouping->name}<ul>\n";
                $lastgroupingid = $grouping->id;
            }
            if (isset($groupsmembers[$grouping->groupid])) {
                echo "<li>{$strgroup}: ".format_string($groupsmembers[$grouping->groupid]->name)."<ul>\n";
                foreach ($groupsmembers[$grouping->groupid]->users as $user) {
                    echo "<li><a href=\"{$CFG->wwwroot}/user/view.php?id={$user->userid}\">".fullname($user)."</a></li>\n";
                }
                echo "</ul></li>";
            }
            else {
            	echo "<li>$strnogroupsassigned</li>";
            }
            if (isset($groupsmembers[$grouping->groupid])) {
                $groupsmembers[$grouping->groupid]->printed = true;
            }
    	}
    }
    if ($lastgroupingid !== false) {
    	echo '</ul></li>';
    }
    echo '</ul>';
    
    // Print Groups not in a grouping
    
    
    if (empty($groupingid)) {
        
        $labelprinted = false;
        foreach($groupsmembers as $groupmembers) {
        	if ($groupmembers->printed) {
        		continue;
        	}
            if (!empty($groupid) && $groupid != $groupmembers->groupid) {
                continue;
            }
            if ($labelprinted === false) {
            	echo "<ul><li>$strnotingrouping<ul>";
                $labelprinted = true;
            }
            
            echo '<li>'.format_string($groupmembers->name).'<ul>';
            
            foreach ($groupmembers->users as $user) {
                echo "<li><a href=\"{$CFG->wwwroot}/user/view.php?id={$user->userid}\">".fullname($user)."</a></li>\n";
            } 
            echo "</ul></li>"; 	
        }
        if ($labelprinted !== false) {
            echo '</ul></li></ul>';
        }
    }
    echo '</div>';
}

print_footer($course);
?>

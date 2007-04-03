<?php
/**
 * The main group management user interface.
 *
 * @copyright &copy; 2006 The Open University
 * @author N.D.Freear AT open.ac.uk
 * @author J.White AT open.ac.uk 
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */
require_once('../config.php');
require_once('lib.php');
require_once($CFG->libdir.'/moodlelib.php');
require_once($CFG->libdir.'/json/JSON.php');

require_js('yui_yahoo');
require_js('yui_dom');
require_js('yui_connection');
require_js($CFG->wwwroot.'/group/lib/clientlib.js');


$success = true;

$courseid   = required_param('id', PARAM_INT);
$groupingid = optional_param('grouping', GROUP_NOT_IN_GROUPING, PARAM_INT);
$groupid    = optional_param('group', false, PARAM_INT);
$userid     = optional_param('user', false, PARAM_INT);
$action = groups_param_action();

if ($groupid) {
    $groupingsforgroup = groups_get_groupings_for_group($groupid);
    if ($groupingsforgroup) {
        // NOTE
        //   We currently assume that a group can only belong to one grouping.
        // FIXME
        //   The UI will have to be fixed if we want to support more than one
        //   groupings per group in the future.
        //
        // vy-shane AT moodle DOT com
        $groupingid = array_shift($groupingsforgroup);
    }
}


// Get the course information so we can print the header and
// check the course id is valid
$course = groups_get_course_info($courseid);
if (! $course) {
    $success = false;
    print_error('invalidcourse'); //'The course ID is invalid'
}

if ($success) {
    // Make sure that the user has permissions to manage groups.
    require_login($courseid);

    $context = get_context_instance(CONTEXT_COURSE, $courseid);
    if (! has_capability('moodle/course:managegroups', $context)) {
        redirect(); //"group.php?id=$course->id");   // Not allowed to see all groups
    }

    // Set the session key so we can check this later
    $sesskey = !empty($USER->id) ? $USER->sesskey : '';


    switch ($action) {
        case false: //OK, display form.
            break;

        case 'ajax_getgroupsingrouping':
            if (GROUP_NOT_IN_GROUPING == $groupingid) {
                $groupids = groups_get_groups_not_in_any_grouping($courseid);
            } else {
                $groupids = groups_get_groups_in_grouping($groupingid);
            }
            $group_names = groups_groupids_to_group_names($groupids);
            $json = new Services_JSON();
            echo $json->encode($group_names);
            die;  // Client side JavaScript takes it from here.

        case 'ajax_getmembersingroup':
            $members = array();

            if ($memberids = groups_get_members($groupid)) {
                $member_names = groups_userids_to_user_names($memberids, $courseid);
                $json = new Services_JSON();
                echo $json->encode($member_names);
            }
            die;  // Client side JavaScript takes it from here.

        case 'showgroupingsettingsform':
            redirect(groups_grouping_edit_url($courseid, $groupingid, false));
            break;
        case 'showgroupingpermsform':
            break;
        case 'deletegrouping':
            redirect(groups_grouping_edit_url($courseid, $groupingid, $html=false, $param='delete=1'));
            break;
        case 'showcreategroupingform':
            redirect(groups_grouping_edit_url($courseid, null, false));
            break;
        case 'printerfriendly':
            redirect('groupui/printgrouping.php?courseid='. $courseid .'&groupingid='. $groupingid);
            break;

        case 'showgroupsettingsform':
            redirect(groups_group_edit_url($courseid, $groupid, $groupingid, false));
            break;
        case 'deletegroup':
            redirect(groups_group_edit_url($courseid, $groupid, $groupingid, $html=false, $param='delete=1'));
            break;
        case 'removegroup':
            break;
        case 'showcreategroupform':
            // Allow groups to be created outside of groupings
            /*
            if (GROUP_NOT_IN_GROUPING == $groupingid) {
                print_error('errornotingrouping', 'group', groups_home_url($courseid), get_string('notingrouping', 'group'));
            }
            */
            redirect(groups_group_edit_url($courseid, null, $groupingid, false));
            break;
        case 'showcreateorphangroupform':
            redirect(groups_group_edit_url($courseid, null, null, false));
            break;
        case 'addgroupstogroupingform':
            break;
        case 'updategroups': //Currently reloading.
            break;

        case 'removemembers':
            break;
        case 'showaddmembersform':
            redirect(groups_members_add_url($courseid, $groupid, $groupingid, false));
            break;
        case 'updatemembers': //Currently reloading.
            break;

        default: //ERROR.
            if (debugging()) {
                error('Error, unknown button/action. Probably a user-interface bug!', groups_home_url($courseid));
            break;
        }
    }

    // Print the page and form
    $strgroups = get_string('groups');
    $strparticipants = get_string('participants');

    print_header("$course->shortname: $strgroups home", //TODO: home
                 $course->fullname, 
                 "<a href=\"$CFG->wwwroot/course/view.php?id=$courseid\">$course->shortname</a> ".
                 "-> <a href=\"$CFG->wwwroot/user/index.php?id=$courseid\">$strparticipants</a> ".
                 "-> $strgroups", '', '', true, '', user_login_string($course, $USER));

    $usehtmleditor = false;
    //TODO: eventually we'll implement all buttons, meantime hide the ones we haven't finished.
    $shownotdone  = false; 
    $disabled = 'disabled="disabled"';
    
    // Pre-disable buttons based on URL variables
    if (!empty($groupingid) && $groupingid > -1) {
        $showeditgroupsettingsform_disabled = '';
        $showeditgroupingsettingsform_disabled = '';
        $deletegroup_disabled = '';
        $deletegrouping_disabled = '';
        $printerfriendly_disabled = '';
        $showcreategroupform_disabled = '';
    } else {
        $showeditgroupsettingsform_disabled = $disabled; 
        $showeditgroupingsettingsform_disabled = $disabled; 
        $deletegroup_disabled = $disabled;
        $deletegrouping_disabled = $disabled;
        $printerfriendly_disabled = $disabled;
        $showcreategroupform_disabled = $disabled;
    }
    
    if ($groupingid == -1 && groups_count_groups_in_grouping(GROUP_NOT_IN_GROUPING, $courseid) > 0) {
        $printerfriendly_disabled = '';
    }

    if (!empty($groupid)) {
        $showaddmembersform_disabled = '';
        $showeditgroupsettingsform_disabled = ''; 
        $deletegroup_disabled = '';
    } else {
        $deletegroup_disabled = $disabled;
        $showeditgroupsettingsform_disabled = $disabled; 
        $showaddmembersform_disabled = $disabled; 
    }
    
    print_heading(format_string($course->shortname) .' '.$strgroups, 'center', 3);
    echo '<form id="groupeditform" action="index.php" method="post">'."\n";
    echo '<div>'."\n";
    echo '<input type="hidden" name="id" value="' . $courseid . '" />'."\n";

/*    
<input type="hidden" name="groupid" value="<?php p($selectedgroup) ?>" />
<input type="hidden" name="sesskey" value="<?php p($sesskey) ?>" />
<input type="hidden" name="roleid" value="<?php p($roleid) ?>" />
*/ 
    echo '<table cellpadding="10" class="generaltable generalbox groupmanagementtable boxaligncenter" summary="">'."\n";
    echo '<tr>'."\n";
    echo '<td class="generalboxcontent">'."\n";
    echo '<p><label for="groupings">' .  get_string('groupings', 'group') . '</label></p>'."\n";
    echo '<select name="grouping" id="groupings" size="15" class="select"';
    echo ' onchange="groupsCombo.refreshGroups(this.options[this.selectedIndex].value);">';

    $groupingids = groups_get_groupings($courseid);
    if (groups_count_groups_in_grouping(GROUP_NOT_IN_GROUPING, $courseid) > 0) {
        //NOTE, only show the pseudo-grouping if it has groups.
        $groupingids[] = GROUP_NOT_IN_GROUPING;
    }
    
    $sel_groupingid = -1;

    if ($groupingids) {    
        // Put the groupings into a hash and sort them
        foreach($groupingids as $id) {
            $listgroupings[$id] = groups_get_grouping_displayname($id, $courseid);
        }
        natcasesort($listgroupings);
        
        // Print out the HTML
        $count = 1;
        foreach($listgroupings as $id => $name) {
            $select = '';
            if ($groupingid == $id) { //|| $count <= 1) ??
                $select = ' selected="selected"';
                $sel_groupingid = $id;
            }
            echo "<option value=\"$id\"$select>$name</option>\n";
            $count++;
        }
    } else {
        echo '<option>&nbsp;</option>';
    }

    echo '</select>'."\n";

    
    echo '<p><input type="submit" name="act_updategroups" id="updategroups" value="'
            . get_string('showgroupsingrouping', 'group') . '" /></p>'."\n";
    echo '<p><input type="submit" ' . $showeditgroupingsettingsform_disabled . ' name="act_showgroupingsettingsform" id="showeditgroupingsettingsform" value="'
            . get_string('editgroupingsettings', 'group') . '" /></p>'."\n";
    
    if ($shownotdone) {
        echo '<p><input type="submit" '.$disabled.' name="act_showgroupingpermsform" '
                . 'id="showeditgroupingpermissionsform" value="'
                . get_string('editgroupingpermissions', 'group') . '" /></p>'."\n";
    } 
    
    echo '<p><input type="submit" ' . $deletegrouping_disabled . ' name="act_deletegrouping" id="deletegrouping" value="'
            . get_string('deletegrouping', 'group') . '" /></p>'."\n";
    echo '<p><input type="submit" name="act_showcreategroupingform" id="showcreategroupingform" value="'
            . get_string('creategrouping', 'group') . '" /></p>'."\n";
    
    if ($shownotdone) {
    echo '<p><input type="submit" '.$disabled.' name="act_createautomaticgroupingform" '
            . 'id="showcreateautomaticgroupingform" value="' 
            . get_string('createautomaticgrouping', 'group') . '" /></p>'."\n";
    }
    
    echo '<p><input type="submit" ' . $printerfriendly_disabled . ' name="act_printerfriendly" id="printerfriendly" value="'
            . get_string('printerfriendly', 'group') . '" /></p>'."\n";
    echo "</td>\n<td>\n";
    echo '<p><label for="groups" id="groupslabel">' .get_string('groupsinselectedgrouping', 'group') . '</label></p>'."\n";
    echo '<select name="group" id="groups" size="15" class="select" onchange="membersCombo.refreshMembers(this.options[this.selectedIndex].value);">'."\n";

    if (GROUP_NOT_IN_GROUPING == $sel_groupingid) {
        $groupids = groups_get_groups_not_in_any_grouping($courseid); //$sel_groupingid
    } else {
        $groupids = groups_get_groups_in_grouping($sel_groupingid);
    }
    if ($groupids) {
        // Put the groups into a hash and sort them
        $group_names = groups_groupids_to_group_names($groupids);
        
        // Print out the HTML
        $count = 1;
        foreach ($group_names as $group) {
            $select = '';
            if ($groupid == $group->id) { //|| $count <= 1) ??
                $select = ' selected="selected"';
                $sel_groupid = $group->id;
            }
            echo "<option value=\"{$group->id}\"$select>{$group->name}</option>\n";
            $count++;
        }
    } else { 
        // Print an empty option to avoid the XHTML error of having an empty select element
        echo '<option>&nbsp;</option>';
    }
    
    echo '</select>'."\n";
    echo '<p><input type="submit" name="act_updatemembers" id="updatemembers" value="'
            . get_string('showmembersforgroup', 'group') . '" /></p>'."\n";
    echo '<p><input type="submit" '. $showeditgroupsettingsform_disabled . ' name="act_showgroupsettingsform" id="showeditgroupsettingsform" value="'
            . get_string('editgroupsettings', 'group') . '" /></p>'."\n";
    echo '<p><input type="submit" '. $deletegroup_disabled . ' name="act_deletegroup" onclick="onDeleteGroup()" id="deletegroup" value="'
            . get_string('deleteselectedgroup', 'group') . '" /></p>'."\n";
    
    if ($shownotdone) {
        echo '<p><input type="submit" '.$disabled.' name="act_removegroup" '
                . 'id="removegroup" value="' . get_string('removegroupfromselectedgrouping', 'group') . '" /></p>'."\n";
    }
    
    echo '<p><input type="submit" ' . $showcreategroupform_disabled . ' name="act_showcreategroupform" id="showcreategroupform" value="'
            . get_string('creategroupinselectedgrouping', 'group') . '" /></p>'."\n";
    
    echo '<p><input type="submit" name="act_showcreateorphangroupform" id="showcreateorphangroupform" value="'
            . get_string('createorphangroup', 'group') . '" /></p>'."\n";
        
    if ($shownotdone) {
        echo '<p><input type="submit" '.$disabled.' name="act_addgroupstogroupingform" '
                . 'id="showaddgroupstogroupingform" value="' . get_string('addgroupstogrouping', 'group') . '" /></p>'."\n";
    }
    
    echo '</td>'."\n";
    echo '<td>'."\n";
    echo '<p><label for="members" id="memberslabel">' . get_string('membersofselectedgroup', 'group') . '</label></p>'."\n";
    echo '<select name="user[]" id="members" size="15" multiple="multiple" class="select">'."\n";
    
    if (isset($sel_groupid)) {
        $userids = groups_get_members($sel_groupid);
    }
    if (isset($userids)) { //&& is_array($userids)        
        // Put the groupings into a hash and sort them
        $user_names = groups_userids_to_user_names($userids, $courseid);
        if(empty($user_names)) {
            echo '<option>&nbsp;</option>';
        } else {
            foreach ($user_names as $user) {
                echo "<option value=\"{$user->id}\">{$user->name}</option>\n";
            }
        }
    } else { 
        // Print an empty option to avoid the XHTML error of having an empty select element
        echo '<option>&nbsp;</option>';
    }
    
    echo '</select>'."\n";

    if ($shownotdone) {
        echo '<p><input type="submit" '.$disabled.' name="act_removemembers" '
                . 'id="removemembers" value="' . get_string('removeselectedusers', 'group') . '"/></p>'."\n";
    }
    
    echo '<p><input type="submit" ' . $showaddmembersform_disabled . ' name="act_showaddmembersform" '
            . 'id="showaddmembersform" value="' . get_string('adduserstogroup', 'group'). '" /></p>'."\n";
    echo '</td>'."\n";
    echo '</tr>'."\n";
    echo '</table>'."\n";

    //<input type="hidden" name="rand" value="om" />
    echo '</div>'."\n";
    echo '</form>'."\n";
    
    echo '<script type="text/javascript">'."\n";
    echo '//<![CDATA['."\n";
    echo 'var groupsCombo = new UpdatableGroupsCombo("'.$CFG->wwwroot.'", '.$course->id.');'."\n";
    echo 'var membersCombo = new UpdatableMembersCombo("'.$CFG->wwwroot.'", '.$course->id.');'."\n";
    echo '//]]>'."\n";
    echo '</script>'."\n";

    print_footer($course);
}

?>
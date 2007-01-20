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
//require_once($CFG->libdir.'/json/JSON.php');


$success = true;
 
$courseid   = required_param('id', PARAM_INT);         
$groupingid = optional_param('grouping', -1, PARAM_INT);
$groupid    = optional_param('group', false, PARAM_INT);
$userid     = optional_param('user', false, PARAM_INT);

$action = groups_param_action();


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
        case 'ajax_getgroupsingrouping':
            $groups = groups_groupids_to_groups(groups_get_groups_in_grouping($groupingid));
            $json = new Services_JSON();
            echo $json->encode($groups);
            die;  // Client side JavaScript takes it from here.

        case 'ajax_getmembersingroup':
            $memberids = groups_get_members($groupid);
            $members = array();

            foreach ($memberids as $memberid) {
                $member = groups_get_user($memberid);
                array_push($members, $member);
            }
            $json = new Services_JSON();
            echo $json->encode($members);
            die;  // Client side JavaScript takes it from here.

        case 'showgroupingsettingsform':
            redirect(groups_grouping_edit_url($courseid, $groupingid, false));
            break;
        case 'showgroupingpermsform':
            break;
        case 'deletegrouping':
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
            redirect(groups_group_edit_url($courseid, $groupid, $groupingid, false, 'delete=1'));
            break;
        case 'removegroup':
            break;
        case 'showcreategroupform':
            if (GROUP_NOT_IN_GROUPING == $groupingid) {
                print_error('errornotingrouping', 'group', groups_home_url($courseid), get_string('notingrouping', 'group'));
            }
            redirect(groups_group_edit_url($courseid, null, $groupingid, false));
            break;
        case 'addgroupstogroupingsform':
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
        default:
            //print_error('Unknown action.');
            break;
    }


    // Print the page and form
    $strgroups = get_string('groups');
    $strparticipants = get_string('participants');

    print_header("$course->shortname: $strgroups home", //TODO: home
                 "$course->fullname", 
                 "<a href=\"$CFG->wwwroot/course/view.php?id=$courseid\">$course->shortname</a> ".
                 "-> <a href=\"$CFG->wwwroot/user/index.php?id=$courseid\">$strparticipants</a> ".
                 "-> $strgroups", '', '', true, '', user_login_string($course, $USER));

    $usehtmleditor = false;
    //TODO: eventually we'll implement all buttons, meantime hide the ones we haven't finised.
    $shownotdone  = false;
    
    print_heading($course->shortname.' '.$strgroups, 'center', 3);
?>
<form id="groupeditform" action="index.php" method="post">
    <fieldset class="invisiblefieldset">
    <input type="hidden" name="id" value="<?php echo $courseid; ?>" />

<?php /*    
<input type="hidden" name="groupid" value="<?php p($selectedgroup) ?>" />
<input type="hidden" name="sesskey" value="<?php p($sesskey) ?>" />
<input type="hidden" name="roleid" value="<?php p($roleid) ?>" />
*/
?>

    <table cellpadding="10" class="generaltable generalbox groupmanagementtable boxaligncenter" summary="">
        <tr>
            <td class="generalboxcontent">
                <p><label for="groupings"><?php print_string('groupings', 'group'); ?></label></p>
                <select name="grouping" id="groupings" size="15" class="select">
<?php
//onchange="onGroupingChange()"

    $groupingids = groups_get_groupings($courseid);
    $groupingids[] = GROUP_NOT_IN_GROUPING;

    if ($groupingids) {    
        // Put the groupings into a hash and sort them
        foreach($groupingids as $id) {
            $listgroupings[$id] = groups_get_grouping_displayname($id);
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
            echo "<option value=\"$id\"$select onclick=\"groupsCombo.refreshGroups($id);\">$name</option>\n";
            $count++;
        }
    }
?>
                </select>

            <p><input type="submit" name="act_updategroups" id="updategroups" value="<?php print_string('showgroupsingrouping', 'group'); ?>" /></p>
            <p><input type="submit" name="act_showgroupingsettingsform" id="showeditgroupingsettingsform" value="<?php print_string('editgroupingsettings', 'group'); ?>" /></p>
<?php if ($shownotdone) { ?>
            <p><input type="submit" disabled="disabled" name="act_showgroupingpermsform" id="showeditgroupingpermissionsform" value="<?php print_string('editgroupingpermissions', 'group'); ?>" /></p>
            <p><input type="submit" disabled="disabled" name="act_deletegrouping" id="deletegrouping" value="<?php print_string('deletegrouping', 'group'); ?>" /></p>
<?php } ?>
            <p><input type="submit" name="act_showcreategroupingform" id="showcreategroupingform" value="<?php print_string('creategrouping', 'group'); ?>" /></p>
<?php if ($shownotdone) { ?>
            <p><input type="submit" disabled="disabled" name="act_createautomaticgroupingform" id="showcreateautomaticgroupingform" value="<?php print_string('createautomaticgrouping', 'group'); ?>" /></p>
<?php } ?>
            <p><input type="submit" name="act_printerfriendly" id="printerfriendly" value="<?php print_string('printerfriendly', 'group'); ?>" /></p>
            </td>
            <td>
                <p><label for="groups" id="groupslabel"><?php print_string('groupsinselectedgrouping', 'group'); ?></label></p>
                <select name="group" id="groups" size="15" class="select">
<?php //onchange="onGroupChange()"
    if (GROUP_NOT_IN_GROUPING == $sel_groupingid) {
        $groupids = groups_get_groups_not_in_any_grouping($courseid); //$sel_groupingid
    } else {
        $groupids = groups_get_groups_in_grouping($sel_groupingid);
    }
    if ($groupids) {
        // Put the groupings into a hash and sort them
        foreach($groupids as $id) {
            $listgroups[$id] = groups_get_group_displayname($id);
        }
        
        natcasesort($listgroups);
        
        // Print out the HTML
        $count = 1;
        foreach($listgroups as $id => $name) {
            $select = '';
            if ($groupid == $id) { //|| $count <= 1) ??
                $select = ' selected="selected"';
                $sel_groupid = $id;
            }
            echo "<option value=\"$id\"$select >$name</option>\n";
            $count++;
        }
    }
?>                
                </select>

            <p><input type="submit" name="act_updatemembers" id="updatemembers" value="<?php print_string('showmembersforgroup', 'group'); ?>" /></p>
            <p><input type="submit" name="act_showgroupsettingsform" id="showeditgroupsettingsform" value="<?php print_string('editgroupsettings', 'group'); ?>" /></p>
            <p><input type="submit" name="act_deletegroup" onclick="onDeleteGroup()" id="deletegroup" value="<?php print_string('deleteselectedgroup', 'group'); ?>" /></p>
<?php if ($shownotdone) { ?>
            <p><input type="submit" disabled="disabled" name="act_removegroup" id="removegroup" value="<?php print_string('removegroupfromselectedgrouping', 'group'); ?>" /></p>
<?php } ?>
            <p><input type="submit" name="act_showcreategroupform" id="showcreategroupform" value="<?php print_string('creategroupinselectedgrouping', 'group'); ?>" /></p>
<?php if ($shownotdone) { ?>
            <p><input type="submit" disabled="disabled" name="act_addgroupstogroupingsform" id="showaddgroupstogroupingform" value="<?php print_string('addexistinggroupstogrouping', 'group'); ?>" /></p>
<?php } ?>
            </td>
            <td>
                <p><label for="members" id="memberslabel"><?php print_string('membersofselectedgroup', 'group'); ?></label></p>
                <select name="user[]" id="members" size="15" multiple="multiple" class="select">
<?php
    if (isset($sel_groupid)) {
        $userids = groups_get_members($sel_groupid);
    }
    if (isset($userids) && is_array($userids)) {       
        // Put the groupings into a hash and sort them
        foreach($userids as $id) {
            $listmembers[$id] = groups_get_user_displayname($id, $courseid);       
        }
        natcasesort($listmembers);

        foreach($listmembers as $id => $name) {
            echo "<option value=\"$id\">$name</option>\n";
        }
    }
?>
                </select>

<?php if ($shownotdone) { ?>
            <p><input type="submit" disabled="disabled" name="act_removemembers" id="removemembers" value="<?php print_string('removeselectedusers', 'group'); ?>"/></p>
<?php } ?>
            <p><input type="submit" name="act_showaddmembersform" id="showaddmembersform" value="<?php print_string('adduserstogroup', 'group'); ?>" /></p>
            </td>
        </tr>
    </table>

    <!--input type="hidden" name="rand" value="om" /-->
</fieldset>
</form>
<?php
    echo '<script type="text/javascript" src="'.$CFG->wwwroot.'/lib/yui/yahoo/yahoo-min.js"></script>';
    echo '<script type="text/javascript" src="'.$CFG->wwwroot.'/lib/yui/connection/connection-min.js"></script>';
    echo '<script type="text/javascript" src="'.$CFG->wwwroot.'/group/lib/clientlib.js"></script>'."\n";

    echo '<script type="text/javascript">'."\n";
    echo '//<![CDATA['."\n";
    echo 'var groupsCombo = new UpdatableGroupsCombo("'.$CFG->wwwroot.'", '.$course->id.');'."\n";
    echo 'var membersCombo = new UpdatableMembersCombo("'.$CFG->wwwroot.'", '.$course->id.');'."\n";
    echo '//]]>'."\n";
    echo '</script>'."\n";

    print_footer($course);
}

?>

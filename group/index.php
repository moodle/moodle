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
require_once($CFG->libdir.'/json/JSON.php');

require_js('yui_yahoo');
require_js('yui_dom');
require_js('yui_utilities');
require_js('yui_connection');
require_js($CFG->wwwroot.'/group/clientlib.js');

$courseid = required_param('id', PARAM_INT);
$groupid  = optional_param('group', false, PARAM_INT);
$userid   = optional_param('user', false, PARAM_INT);
$action   = groups_param_action();

$returnurl = $CFG->wwwroot.'/group/index.php?id='.$courseid;

// Get the course information so we can print the header and
// check the course id is valid

if (!$course = get_record('course', 'id',$courseid)) {
    $success = false;
    print_error('invalidcourse'); //'The course ID is invalid'
}

// Make sure that the user has permissions to manage groups.
require_login($course);

$context = get_context_instance(CONTEXT_COURSE, $courseid);
if (! has_capability('moodle/course:managegroups', $context)) {
    redirect(); //"group.php?id=$course->id");   // Not allowed to see all groups
}

switch ($action) {
    case false: //OK, display form.
        break;

    case 'ajax_getmembersingroup':
        $members = array();
        if ($members = groups_get_members($groupid)) {
            $member_names = array();
            foreach($members as $member) {
                $user = new object();
                $user->id   = $member->id;
                $user->name = fullname($member, true);
                $member_names[] = $user;
            }
            $json = new Services_JSON();
            echo $json->encode($member_names);
        }
        die;  // Client side JavaScript takes it from here.

    case 'deletegroup':
        redirect('group.php?delete=1&amp;courseid='.$courseid.'&amp;id='.$groupid);
        break;

    case 'showcreateorphangroupform':
        redirect('group.php?courseid='.$courseid);
        break;

    case 'showgroupsettingsform':
        redirect('group.php?courseid='.$courseid.'&amp;id='.$groupid);
        break;

    case 'updategroups': //Currently reloading.
        break;

    case 'removemembers':
        break;

    case 'showaddmembersform':
        redirect('members.php?group='.$groupid);
        break;

    case 'updatemembers': //Currently reloading.
        break;

    default: //ERROR.
        if (debugging()) {
            error('Error, unknown button/action. Probably a user-interface bug!', $returnurl);
        break;
    }
}

// Print the page and form
$strgroups = get_string('groups');
$strparticipants = get_string('participants');

$navlinks = array(array('name'=>$strparticipants, 'link'=>$CFG->wwwroot.'/user/index.php?id='.$courseid, 'type'=>'misc'),
                  array('name'=>$strgroups, 'link'=>'', 'type'=>'misc'));
$navigation = build_navigation($navlinks);

/// Print header
print_header_simple($strgroups, ': '.$strgroups, $navigation, '', '', true, '', navmenu($course));

if (!empty($CFG->enablegroupings)) {
    // Add tabs
    $currenttab = 'groups';
    require('tabs.php');
}

$disabled = 'disabled="disabled"';

$showeditgroupsettingsform_disabled = $disabled;
$deletegroup_disabled = $disabled;
$showcreategroupform_disabled = $disabled;

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

echo '<table cellpadding="6" class="generaltable generalbox groupmanagementtable boxaligncenter" summary="">'."\n";
echo '<tr>'."\n";


echo "<td>\n";
// NO GROUPINGS YET!
echo '<p><label for="groups"><span id="groupslabel">'.get_string('groups').':</span><span id="thegrouping">&nbsp;</span></label></p>'."\n";

echo '<select name="group" id="groups" size="15" class="select" onchange="membersCombo.refreshMembers(this.options[this.selectedIndex].value);"'."\n";
echo ' onclick="window.status=this.options[this.selectedIndex].title;" onmouseout="window.status=\'\';">'."\n";

$groups = groups_get_all_groups($courseid);

$sel_groupid = 0;

if ($groups) {
    // Print out the HTML
    foreach ($groups as $group) {
        $select = '';
        if ($groupid == $group->id) {
            $select = ' selected="selected"';
            $sel_groupid = $group->id;
        }
        $usercount = (int)count_records('groups_members', 'groupid', $group->id);
        $groupname = format_string($group->name).' ('.$usercount.')';

        echo "<option value=\"{$group->id}\"$select title=\"$groupname\">$groupname</option>\n";
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

echo '<p><input type="submit" name="act_showcreateorphangroupform" id="showcreateorphangroupform" value="'
        . get_string('creategroup', 'group') . '" /></p>'."\n";

echo '</td>'."\n";
echo '<td>'."\n";
echo '<p><label for="members"><span id="memberslabel">'.get_string('membersofselectedgroup', 'group').' </span><span id="thegroup">&nbsp;</span></label></p>'."\n";
//NOTE: the SELECT was, multiple="multiple" name="user[]" - not used and breaks onclick.
echo '<select name="user" id="members" size="15" class="select"'."\n";
echo ' onclick="window.status=this.options[this.selectedIndex].title;" onmouseout="window.status=\'\';">'."\n";

$member_names = array();

if ($sel_groupid) {
    if ($members = groups_get_members($groupid)) {
        foreach($members as $member) {
            $member_names[$member->id] = fullname($member, true);
        }
    }
}

if ($member_names) {
    // Put the groupings into a hash and sort them
    foreach ($member_names as $userid=>$username) {
        echo "<option value=\"{$userid}\" title=\"{$username}\">{$username}</option>\n";
    }

} else {
    // Print an empty option to avoid the XHTML error of having an empty select element
    echo '<option>&nbsp;</option>';
}

echo '</select>'."\n";

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

/**
 * Returns the first button action with the given prefix, taken from
 * POST or GET, otherwise returns false.
 * See /lib/moodlelib.php function optional_param.
 * @param $prefix 'act_' as in 'action'.
 * @return string The action without the prefix, or false if no action found.
 */
function groups_param_action($prefix = 'act_') {
    $action = false;
//($_SERVER['QUERY_STRING'] && preg_match("/$prefix(.+?)=(.+)/", $_SERVER['QUERY_STRING'], $matches)) { //b_(.*?)[&;]{0,1}/

    if ($_POST) {
        $form_vars = $_POST;
    }
    elseif ($_GET) {
        $form_vars = $_GET;
    }
    if ($form_vars) {
        foreach ($form_vars as $key => $value) {
            if (preg_match("/$prefix(.+)/", $key, $matches)) {
                $action = $matches[1];
                break;
            }
        }
    }
    if ($action && !preg_match('/^\w+$/', $action)) {
        $action = false;
        error('Action had wrong type.');
    }
    ///if (debugging()) echo 'Debug: '.$action;
    return $action;
}

?>
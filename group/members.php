<?php // $Id$
/**
 * Add/remove members from group.
 *
 * @copyright &copy; 2006 The Open University
 * @author N.D.Freear AT open.ac.uk
 * @author J.White AT open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */
require_once('../config.php');
require_once('lib.php');

define("MAX_USERS_PER_PAGE", 5000);

$groupid    = required_param('group', PARAM_INT);
$searchtext = optional_param('searchtext', '', PARAM_RAW); // search string
$showall    = optional_param('showall', 0, PARAM_BOOL);

if ($showall) {
    $searchtext = '';
}

if (!$group = get_record('groups', 'id', $groupid)) {
    error('Incorrect group id');
}

if (!$course = get_record('course', 'id', $group->courseid)) {
    print_error('invalidcourse');
}
$courseid = $course->id;

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $courseid);
require_capability('moodle/course:managegroups', $context);

$strsearch = get_string('search');
$strshowall = get_string('showall');
$returnurl = $CFG->wwwroot.'/group/index.php?id='.$courseid.'&group='.$group->id;


if ($frm = data_submitted() and confirm_sesskey()) {

    if (isset($frm->cancel)) {
        redirect($returnurl);

    } else if (isset($frm->add) and !empty($frm->addselect)) {

        foreach ($frm->addselect as $userid) {
            if (! $userid = clean_param($userid, PARAM_INT)) {
                continue;
            }
            if (!groups_add_member($groupid, $userid)) {
                print_error('erroraddremoveuser', 'group', $returnurl);
            }
        }

    } else if (isset($frm->remove) and !empty($frm->removeselect)) {

        foreach ($frm->removeselect as $userid) {
            if (! $userid = clean_param($userid, PARAM_INT)) {
                continue;
            }
            if (!groups_remove_member($groupid, $userid)) {
                print_error('erroraddremoveuser', 'group', $returnurl);
            }
        }
    }
}

$groupmembersoptions = '';
$groupmemberscount = 0;

// Get members, organised by role, and display
if ($groupmemberroles = groups_get_members_by_role($groupid,$courseid,'u.id,u.firstname,u.lastname')) {
    foreach($groupmemberroles as $roleid=>$roledata) {
        $groupmembersoptions .= '<optgroup label="'.htmlspecialchars($roledata->name).'">';
        foreach($roledata->users as $member) {
            $groupmembersoptions .= '<option value="'.$member->id.'">'.fullname($member, true).'</option>';
            $groupmemberscount ++;
        }
        $groupmembersoptions .= '</optgroup>';
    }
} else {
    $groupmembersoptions .= '<option>&nbsp;</option>';
}

$potentialmembers = array();
$potentialmembersoptions = '';
$potentialmemberscount = 0;

// Get potential members, organised by role, and count them
$potentialmembersbyrole = groups_get_users_not_in_group_by_role($courseid, $groupid, $searchtext);
$potentialmemberscount=0;
$potentialmembersids=array();
if (!empty($potentialmembersbyrole)) {
    foreach($potentialmembersbyrole as $roledata) {
        $potentialmemberscount+=count($roledata->users);
        $potentialmembersids=array_merge($potentialmembersids,array_keys($roledata->users));
    }
}

if ($potentialmemberscount <=  MAX_USERS_PER_PAGE) {

    if ($potentialmemberscount != 0) {
        // Get other groups user already belongs to
        $sql = "SELECT u.id AS userid, g.* FROM {$CFG->prefix}user u " .
                    "INNER JOIN {$CFG->prefix}groups_members gm ON u.id = gm.userid " .
                    "INNER JOIN {$CFG->prefix}groups g ON gm.groupid = g.id " .
               "WHERE u.id IN (".implode(',',$potentialmembersids).") AND g.courseid = {$course->id} ";
        $rs = get_recordset_sql($sql);
        $groups = array();
        $usergroups = array();
        while ($usergroup =  rs_fetch_next_record($rs)) {
            $usergroups[$usergroup->userid][$usergroup->id] = $usergroup;
        }
        rs_close($rs);

        foreach($potentialmembersbyrole as $roleid=>$roledata) {
            $potentialmembersoptions .= '<optgroup label="'.htmlspecialchars($roledata->name).'">';
            foreach($roledata->users as $member) {
                $name=htmlspecialchars(fullname($member, true));
                $potentialmembersoptions .= '<option value="'.$member->id.
                    '" title="'.$name.'">'.$name.
                    ' ('.@count($usergroups[$member->id]).')</option>';
            }
            $potentialmembersoptions .= '</optgroup>';
        }
    } else {
        $potentialmembersoptions .= '<option>&nbsp;</option>';
    }
}

// Print the page and form
$strgroups = get_string('groups');
$strparticipants = get_string('participants');
$stradduserstogroup = get_string('adduserstogroup', 'group');
$strusergroupmembership = get_string('usergroupmembership', 'group');

$groupname = format_string($group->name);

$navlinks = array();
$navlinks[] = array('name' => $strparticipants, 'link' => "$CFG->wwwroot/user/index.php?id=$courseid", 'type' => 'misc');
$navlinks[] = array('name' => $strgroups, 'link' => "$CFG->wwwroot/group/index.php?id=$courseid", 'type' => 'misc');
$navlinks[] = array('name' => $stradduserstogroup, 'link' => null, 'type' => 'misc');
$navigation = build_navigation($navlinks);

print_header("$course->shortname: $strgroups", $course->fullname, $navigation, '', '', true, '', user_login_string($course, $USER));

// Print Javascript for showing the selected users group membership
?>
<script type="text/javascript">
//<![CDATA[
var userSummaries = Array(
<?php
$membercnt = count($nonmembers);
$i=1;
foreach ($nonmembers as $userid => $potentalmember) {

    if (isset($usergroups[$userid])) {
        $usergrouplist = '<ul>';

        foreach ($usergroups[$userid] as $groupitem) {
            $usergrouplist .= '<li>'.addslashes_js(format_string($groupitem->name)).'</li>';
        }
        $usergrouplist .= '</ul>';
    }
    else {
    	$usergrouplist = '';
    }
    echo "'$usergrouplist'";
    if ($i < $membercnt) {
    	echo ', ';
    }
    $i++;
}
?>
);

function updateUserSummary() {

    var selectEl = document.getElementById('addselect');
    var summaryDiv = document.getElementById('group-usersummary');
    var length = selectEl.length;
    var selectCnt = 0;
    var selectIdx = -1;

    for(i=0;i<length;i++) {
        if (selectEl.options[i].selected) {
        	selectCnt++;
            selectIdx = i;
        }
    }

    if (selectCnt == 1 && userSummaries[selectIdx]) {
        summaryDiv.innerHTML = userSummaries[selectIdx];
    } else {
        summaryDiv.innerHTML = '';
    }

    return(true);
}
//]]>
</script>

<div id="addmembersform">
    <h3 class="main"><?php print_string('adduserstogroup', 'group'); echo ": $groupname"; ?></h3>

    <form id="assignform" method="post" action="members.php">
    <div>
    <input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />
    <input type="hidden" name="group" value="<?php echo $groupid; ?>" />

    <table cellpadding="6" class="generaltable generalbox groupmanagementtable boxaligncenter" summary="">
    <tr>
      <td valign="top">
          <p>
            <label for="removeselect"><?php print_string('existingmembers', 'group', $groupmemberscount); //count($contextusers) ?></label>
          </p>
          <select name="removeselect[]" size="20" id="removeselect" multiple="multiple"
                  onfocus="document.getElementById('assignform').add.disabled=true;
                           document.getElementById('assignform').remove.disabled=false;
                           document.getElementById('assignform').addselect.selectedIndex=-1;"
                  onclick="this.focus();updateUserSummary();">
          <?php echo $groupmembersoptions ?>
          </select></td>
      <td valign="top">
<?php // Hidden assignment? ?>

        <?php check_theme_arrows(); ?>
        <p class="arrow_button">
            <input name="add" id="add" type="submit" value="<?php echo $THEME->larrow.'&nbsp;'.get_string('add'); ?>" title="<?php print_string('add'); ?>" /><br />
            <input name="remove" id="remove" type="submit" value="<?php echo get_string('remove').'&nbsp;'.$THEME->rarrow; ?>" title="<?php print_string('remove'); ?>" />
        </p>
      </td>
      <td valign="top">
          <p>
            <label for="addselect"><?php print_string('potentialmembers', 'group', $potentialmemberscount); //$usercount ?></label>
          </p>
          <select name="addselect[]" size="20" id="addselect" multiple="multiple"
                  onfocus="updateUserSummary();document.getElementById('assignform').add.disabled=false;
                           document.getElementById('assignform').remove.disabled=true;
                           document.getElementById('assignform').removeselect.selectedIndex=-1;"
                  onclick="this.focus();updateUserSummary();">
          <?php
            if ($potentialmemberscount > MAX_USERS_PER_PAGE) {
                echo '<optgroup label="'.get_string('toomanytoshow').'"><option></option></optgroup>'."\n"
                        .'<optgroup label="'.get_string('trysearching').'"><option></option></optgroup>'."\n";
            } else {
                echo $potentialmembersoptions;
            }
          ?>
         </select>
         <br />
         <label for="searchtext" class="accesshide"><?php p($strsearch) ?></label>
         <input type="text" name="searchtext" id="searchtext" size="21" value="<?php p($searchtext, true) ?>"
                  onfocus ="getElementById('assignform').add.disabled=true;
                            getElementById('assignform').remove.disabled=true;
                            getElementById('assignform').removeselect.selectedIndex=-1;
                            getElementById('assignform').addselect.selectedIndex=-1;"
                  onkeydown = "var keyCode = event.which ? event.which : event.keyCode;
                               if (keyCode == 13) {
                                    getElementById('assignform').previoussearch.value=1;
                                    getElementById('assignform').submit();
                               } " />
         <input name="search" id="search" type="submit" value="<?php p($strsearch) ?>" />
         <?php
              if (!empty($searchtext)) {
                  echo '<br /><input name="showall" id="showall" type="submit" value="'.$strshowall.'" />'."\n";
              }
         ?>
       </td>
       <td valign="top">
        <p><?php echo($strusergroupmembership) ?></p>
        <div id="group-usersummary"></div>
       </td>
    </tr>
    <tr><td>
        <input type="submit" name="cancel" value="<?php print_string('backtogroups', 'group'); ?>" />
    </td></tr>
    </table>
    </div>
    </form>
</div>

<?php
    print_footer($course);
?>

<?php  // $Id$
/**
 * Add/remove group from grouping.
 * @package groups
 */
require_once('../config.php');
require_once('lib.php');

$groupingid = required_param('id', PARAM_INT);

if (!$grouping = get_record('groupings', 'id', $groupingid)) {
    error('Incorrect group id');
}

if (! $course = get_record('course', 'id', $grouping->courseid)) {
    print_error('invalidcourse');
}
$courseid = $course->id;

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $courseid);
require_capability('moodle/course:managegroups', $context);

$returnurl = $CFG->wwwroot.'/group/groupings.php?id='.$courseid;


if ($frm = data_submitted() and confirm_sesskey()) {

    if (isset($frm->cancel)) {
        redirect($returnurl);

    } else if (isset($frm->add) and !empty($frm->addselect)) {
        foreach ($frm->addselect as $groupid) {
            groups_assign_grouping($grouping->id, (int)$groupid);
        }

    } else if (isset($frm->remove) and !empty($frm->removeselect)) {
        foreach ($frm->removeselect as $groupid) {
            groups_unassign_grouping($grouping->id, (int)$groupid);
        }
    }
}


$currentmembers = array();
$potentialmembers  = array();

if ($groups = get_records('groups', 'courseid', $courseid, 'name')) {
    if ($assignment = get_records('groupings_groups', 'groupingid', $grouping->id)) {
        foreach ($assignment as $ass) {
            $currentmembers[$ass->groupid] = $groups[$ass->groupid];
            unset($groups[$ass->groupid]);
        }
    }
    $potentialmembers = $groups;
}

$currentmembersoptions = '';
$currentmemberscount = 0;
if ($currentmembers) {
    foreach($currentmembers as $group) {
        $currentmembersoptions .= '<option value="'.$group->id.'.">'.format_string($group->name).'</option>';
        $currentmemberscount ++;
    }

    // Get course managers so they can be hilited in the list
    if ($managerroles = get_config('', 'coursemanager')) {
        $coursemanagerroles = split(',', $managerroles);
        foreach ($coursemanagerroles as $roleid) {
            $role = get_record('role','id',$roleid);
            $canseehidden = has_capability('moodle/role:viewhiddenassigns', $context);
            $managers = get_role_users($roleid, $context, true, 'u.id', 'u.id ASC', $canseehidden);
        }
    }
} else {
    $currentmembersoptions .= '<option>&nbsp;</option>';
}

$potentialmembersoptions = '';
$potentialmemberscount = 0;
if ($potentialmembers) {
    foreach($potentialmembers as $group) {
        $potentialmembersoptions .= '<option value="'.$group->id.'.">'.format_string($group->name).'</option>';
        $potentialmemberscount ++;
    }
} else {
    $potentialmembersoptions .= '<option>&nbsp;</option>';
}

// Print the page and form
$strgroups = get_string('groups');
$strparticipants = get_string('participants');
$straddgroupstogroupings = get_string('addgroupstogroupings', 'group');

$groupingname = format_string($grouping->name);

$navlinks = array();
$navlinks[] = array('name' => $strparticipants, 'link' => "$CFG->wwwroot/user/index.php?id=$courseid", 'type' => 'misc');
$navlinks[] = array('name' => $strgroups, 'link' => "$CFG->wwwroot/group/index.php?id=$courseid", 'type' => 'misc');
$navlinks[] = array('name' => $straddgroupstogroupings, 'link' => null, 'type' => 'misc');
$navigation = build_navigation($navlinks);

print_header("$course->shortname: $strgroups", $course->fullname, $navigation, '', '', true, '', user_login_string($course, $USER));

?>
<div id="addmembersform">
    <h3 class="main"><?php print_string('addgroupstogroupings', 'group'); echo ": $groupingname"; ?></h3>

    <form id="assignform" method="post" action="">
    <div>
    <input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />

    <table summary="" cellpadding="5" cellspacing="0">
    <tr>
      <td valign="top">
          <label for="removeselect"><?php print_string('existingmembers', 'group', $currentmemberscount); //count($contextusers) ?></label>
          <br />
          <select name="removeselect[]" size="20" id="removeselect" multiple="multiple"
                  onfocus="document.getElementById('assignform').add.disabled=true;
                           document.getElementById('assignform').remove.disabled=false;
                           document.getElementById('assignform').addselect.selectedIndex=-1;">
          <?php echo $currentmembersoptions ?>
          </select></td>
      <td valign="top">

        <?php check_theme_arrows(); ?>
        <p class="arrow_button">
            <input name="add" id="add" type="submit" value="<?php echo '&nbsp;'.$THEME->larrow.' &nbsp; &nbsp; '.get_string('add'); ?>" title="<?php print_string('add'); ?>" />
            <br />
            <input name="remove" id="remove" type="submit" value="<?php echo '&nbsp; '.$THEME->rarrow.' &nbsp; &nbsp; '.get_string('remove'); ?>" title="<?php print_string('remove'); ?>" />
        </p>
      </td>
      <td valign="top">
          <label for="addselect"><?php print_string('potentialmembers', 'group', $potentialmemberscount); //$usercount ?></label>
          <br />
          <select name="addselect[]" size="20" id="addselect" multiple="multiple"
                  onfocus="document.getElementById('assignform').add.disabled=false;
                           document.getElementById('assignform').remove.disabled=true;
                           document.getElementById('assignform').removeselect.selectedIndex=-1;">
         <?php echo $potentialmembersoptions ?>
         </select>
         <br />
       </td>
    </tr>
    <tr><td>
        <input type="submit" name="cancel" value="<?php print_string('backtogroupings', 'group'); ?>" />
    </td></tr>
    </table>
    </div>
    </form>
</div>

<?php
    print_footer($course);


?>

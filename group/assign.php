<?php
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
require_once($CFG->libdir.'/moodlelib.php');

$success = true;

$courseid   = required_param('courseid', PARAM_INT);         
$groupingid = required_param('grouping', PARAM_INT);
$groupid    = required_param('group', PARAM_INT);

// Get the course information so we can print the header and
// check the course id is valid
$course = groups_get_course_info($courseid);
if (! $course) {
    $success = false;
    print_error('invalidcourse');
}
if (empty($groupid)) {
    $success = false;
    print_error('errorinvalidgroup', 'group', groups_home_url($courseid));
}

if ($success) {
    // Make sure that the user has permissions to manage groups.
    require_login($courseid);

    $context = get_context_instance(CONTEXT_COURSE, $courseid);
    if (! has_capability('moodle/course:managegroups', $context)) {
        redirect();
    }
    
    if ($frm = data_submitted() and confirm_sesskey()) { 

        if (isset($frm->cancel)) {
            redirect('index.php?id='. $courseid
                .'&grouping='. $groupingid .'&group='. $groupid);
        }
        elseif (isset($frm->add) and !empty($frm->addselect)) {

            foreach ($frm->addselect as $userid) {
                if (! $userid = clean_param($userid, PARAM_INT)) {
                    continue;
                }
                $success = groups_add_member($groupid, $userid);
                if (! $success) {
                    print_error('erroraddremoveuser', 'group', groups_home_url($courseid));
                }
            }
        }
        elseif (isset($frm->remove) and !empty($frm->removeselect)) {

            foreach ($frm->removeselect as $userid) {
                if (! $userid = clean_param($userid, PARAM_INT)) {
                    continue;
                }
                $success = groups_remove_member($groupid, $userid);
                if (! $success) {
                    print_error('erroraddremoveuser', 'group', groups_home_url($courseid));
                }
            }
        }
    }

    // Print the page and form
    $strgroups = get_string('groups');
    $strparticipants = get_string('participants');

    $groupname = groups_get_group_displayname($groupid);

    print_header("$course->shortname: $strgroups", 
                 $course->fullname, 
                 "<a href=\"$CFG->wwwroot/course/view.php?id=$courseid\">$course->shortname</a> ".
                 "-> <a href=\"$CFG->wwwroot/user/index.php?id=$courseid\">$strparticipants</a> ".
                 '-> <a href="' .format_string(groups_home_url($courseid, $groupid, $groupingid, false)) . "\">$strgroups</a>".
                 '-> '. get_string('adduserstogroup', 'group'), '', '', true, '', user_login_string($course, $USER));

?>
<div id="addmembersform">
    <h3 class="main"><?php print_string('adduserstogroup', 'group'); echo " $groupname"; ?></h3>

    <form id="assignform" method="post" action="">
    <div>
    <input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />
    <input type="hidden" name="courseid" value="<?php p($courseid); ?>" />
    <input type="hidden" name="grouping" value="<?php echo $groupingid; ?>" />
    <input type="hidden" name="group" value="<?php echo $groupid; ?>" />

    <table summary="" cellpadding="5" cellspacing="0">
    <tr>
      <td valign="top">
          <label for="removeselect"><?php print_string('existingusers', 'role'); //count($contextusers) ?></label>
          <br />
          <select name="removeselect[]" size="20" id="removeselect" multiple="multiple"
                  onfocus="document.getElementById('assignform').add.disabled=true;
                           document.getElementById('assignform').remove.disabled=false;
                           document.getElementById('assignform').addselect.selectedIndex=-1;">
<?php
    $userids = groups_get_members($groupid);
    
    if ($userids != false) {
        // Put the groupings into a hash and sorts them
        foreach ($userids as $userid) {
            $listmembers[$userid] = groups_get_user_displayname($userid, $courseid);       
        }
        natcasesort($listmembers);

        // Print out the HTML
        foreach($listmembers as $id => $name) {
            echo "<option value=\"$id\">$name</option>\n";
        }
    } else {
        echo '<option>&nbsp;</option>';
    }
?>
          </select></td>
      <td valign="top">
<?php // Hidden assignment? ?>

        <?php check_theme_arrows(); ?>
        <p class="arrow_button">
            <input name="add" id="add" type="submit" value="<?php echo '&nbsp;'.$THEME->larrow.' &nbsp; &nbsp; '.get_string('add'); ?>" title="<?php print_string('add'); ?>" />
            <br />
            <input name="remove" id="remove" type="submit" value="<?php echo '&nbsp; '.$THEME->rarrow.' &nbsp; &nbsp; '.get_string('remove'); ?>" title="<?php print_string('remove'); ?>" />
        </p>
      </td>
      <td valign="top">
          <label for="addselect"><?php print_string('potentialusers', 'role'); //$usercount ?></label>
          <br />
          <select name="addselect[]" size="20" id="addselect" multiple="multiple"
                  onfocus="document.getElementById('assignform').add.disabled=false;
                           document.getElementById('assignform').remove.disabled=true;
                           document.getElementById('assignform').removeselect.selectedIndex=-1;">
    <?php
    //TODO: If no 'showall' button, then set true.
    $showall = true;
    unset($userids);
    if (!$showall && $groupingid != GROUP_NOT_IN_GROUPING) {
        $userids = groups_get_users_not_in_any_group_in_grouping($courseid, $groupingid, $groupid);
    } else {
        $userids = groups_get_users_not_in_group($courseid, $groupid);
    }
    
    if ($userids != false) {
        // Put the groupings into a hash and sorts them
        foreach ($userids as $userid) {
            $nonmembers[$userid] = groups_get_user_displayname($userid, $courseid);       
        }
        natcasesort($nonmembers);

        // Print out the HTML
        foreach($nonmembers as $id => $name) {
            echo "<option value=\"$id\">$name</option>\n";
        }
    } else {
        echo '<option>&nbsp;</option>';
    }
    ?>
         </select>
         <br />
         <?php //TODO: Search box?
         
              /*if (!empty($searchtext)) {
                  echo '<input name="showall" type="submit" value="'.get_string('showall').'" />'."\n";
              }*/
         ?>
       </td>
    </tr>
    <tr><td>
        <input type="submit" name="cancel" value="<?php print_string('return', 'group'); ?>" />    
    </td></tr>
    </table>
    </div>
    </form>
</div>

<?php
    print_footer($course);
}

?>

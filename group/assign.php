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

define("MAX_USERS_PER_PAGE", 5000);

$groupid    = required_param('group', PARAM_INT);
$searchtext = optional_param('searchtext', '', PARAM_RAW); // search string
$showall    = optional_param('showall', 0, PARAM_BOOL);

if ($showall) {
    $searchtext = '';
}


require_login();
if (!$group = get_record('groups', 'id', $groupid)) {
    error('Incorrect group id');
}

if (! $course = get_record('course', 'id', $group->courseid)) {
    print_error('invalidcourse');
}

require_login($course);
$courseid = $course->id;

$strsearch = get_string('search');
$strshowall = get_string('showall');
$returnurl = $CFG->wwwroot.'/group/index.php?id='.$courseid.'&group='.$groupid;

$context = get_context_instance(CONTEXT_COURSE, $courseid);
require_capability('moodle/course:managegroups', $context);

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

                // MDL-9983
                $eventdata = new object();
                $eventdata -> groupid = $groupid;
                $eventdata -> userid = $userid;
                events_trigger('group_user_removed', $eventdata);
            }
        }
    }

    $groupmembers = groups_get_members($groupid);
    $groupmembersoptions = '';
    $groupmemberscount = 0;
    if ($groupmembers != false) {
        // Put the groupings into a hash and sorts them
        foreach ($groupmembers as $userid) {
            $listmembers[$userid] = groups_get_user_displayname($userid, $courseid);
            $groupmemberscount ++;
        }
        natcasesort($listmembers);

        // Print out the HTML
        foreach($listmembers as $id => $name) {
            $groupmembersoptions .= "<option value=\"$id\">$name</option>\n";
        }
    } else {
        $groupmembersoptions .= '<option>&nbsp;</option>';
    }

    $potentialmembers = array();
    $potentialmembersoptions = '';
    $potentialmemberscount = 0;

    $potentialmembers = groups_get_users_not_in_group($courseid, $groupid, $searchtext);
    if (!empty($potentialmembers)) {
        $potentialmemberscount = count($potentialmembers);
    } else {
        $potentialmemberscount = 0;
    }
    if ($potentialmemberscount <=  MAX_USERS_PER_PAGE) {

        if ($potentialmembers != false) {
            // Put the groupings into a hash and sorts them
            foreach ($potentialmembers as $userid => $user) {
                $nonmembers[$userid] = fullname($user);
                //$nonmembers[$userid] = groups_get_user_displayname($userid, $courseid);
            }
            natcasesort($nonmembers);

            // Print out the HTML
            foreach($nonmembers as $id => $name) {
                $potentialmembersoptions .= "<option value=\"$id\">$name</option>\n";
            }
        } else {
            $potentialmembersoptions .= '<option>&nbsp;</option>';
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
                 "-> <a href=\"$CFG->wwwroot/group/index.php?id=$courseid\">$strgroups</a>".
                 '-> '. get_string('adduserstogroup', 'group'), '', '', true, '', user_login_string($course, $USER));

?>
<div id="addmembersform">
    <h3 class="main"><?php print_string('adduserstogroup', 'group'); echo " $groupname"; ?></h3>

    <form id="assignform" method="post" action="">
    <div>
    <input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />
    <input type="hidden" name="group" value="<?php echo $groupid; ?>" />

    <table summary="" cellpadding="5" cellspacing="0">
    <tr>
      <td valign="top">
          <label for="removeselect"><?php print_string('existingmembers', 'group', $groupmemberscount); //count($contextusers) ?></label>
          <br />
          <select name="removeselect[]" size="20" id="removeselect" multiple="multiple"
                  onfocus="document.getElementById('assignform').add.disabled=true;
                           document.getElementById('assignform').remove.disabled=false;
                           document.getElementById('assignform').addselect.selectedIndex=-1;">
          <?php echo $groupmembersoptions ?>
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
          <label for="addselect"><?php print_string('potentialmembers', 'group', $potentialmemberscount); //$usercount ?></label>
          <br />
          <select name="addselect[]" size="20" id="addselect" multiple="multiple"
                  onfocus="document.getElementById('assignform').add.disabled=false;
                           document.getElementById('assignform').remove.disabled=true;
                           document.getElementById('assignform').removeselect.selectedIndex=-1;">
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
         <input type="text" name="searchtext" id="searchtext" size="30" value="<?php p($searchtext, true) ?>"
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
                  echo '<input name="showall" id="showall" type="submit" value="'.$strshowall.'" />'."\n";
              }
         ?>
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

<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Add/remove group from grouping.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   core_group
 */

require_once('../config.php');
require_once('lib.php');

$groupingid = required_param('id', PARAM_INT);

$PAGE->set_url('/group/assign.php', array('id'=>$groupingid));

if (!$grouping = $DB->get_record('groupings', array('id'=>$groupingid))) {
    throw new \moodle_exception('invalidgroupid');
}

if (!$course = $DB->get_record('course', array('id'=>$grouping->courseid))) {
    throw new \moodle_exception('invalidcourse');
}
$courseid = $course->id;

require_login($course);
$context = context_course::instance($courseid);
require_capability('moodle/course:managegroups', $context);

$returnurl = $CFG->wwwroot.'/group/groupings.php?id='.$courseid;


if ($frm = data_submitted() and confirm_sesskey()) {

    if (isset($frm->cancel)) {
        redirect($returnurl);

    } else if (isset($frm->add) and !empty($frm->addselect)) {
        foreach ($frm->addselect as $groupid) {
            // Ask this method not to purge the cache, we'll do it ourselves afterwards.
            groups_assign_grouping($grouping->id, (int)$groupid, null, false);
        }
        // Invalidate the course groups cache seeing as we've changed it.
        cache_helper::invalidate_by_definition('core', 'groupdata', array(), array($courseid));

        // Invalidate the user_group_groupings cache, too.
        cache_helper::purge_by_definition('core', 'user_group_groupings');
    } else if (isset($frm->remove) and !empty($frm->removeselect)) {
        foreach ($frm->removeselect as $groupid) {
            // Ask this method not to purge the cache, we'll do it ourselves afterwards.
            groups_unassign_grouping($grouping->id, (int)$groupid, false);
        }
        // Invalidate the course groups cache seeing as we've changed it.
        cache_helper::invalidate_by_definition('core', 'groupdata', array(), array($courseid));

        // Invalidate the user_group_groupings cache, too.
        cache_helper::purge_by_definition('core', 'user_group_groupings');
    }
}


$currentmembers = array();
$potentialmembers  = array();

if ($groups = $DB->get_records('groups', array('courseid'=>$courseid), 'name')) {
    if ($assignment = $DB->get_records('groupings_groups', array('groupingid'=>$grouping->id))) {
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
        $currentmembersoptions .= '<option value="' . $group->id . '." title="' . format_string($group->name) . '">' .
                format_string($group->name) . '</option>';
        $currentmemberscount ++;
    }

    // Get course managers so they can be highlighted in the list
    if ($managerroles = get_config('', 'coursecontact')) {
        $coursecontactroles = explode(',', $managerroles);
        foreach ($coursecontactroles as $roleid) {
            $role = $DB->get_record('role', array('id'=>$roleid));
            $managers = get_role_users($roleid, $context, true, 'u.id', 'u.id ASC');
        }
    }
} else {
    $currentmembersoptions .= '<option>&nbsp;</option>';
}

$potentialmembersoptions = '';
$potentialmemberscount = 0;
if ($potentialmembers) {
    foreach($potentialmembers as $group) {
        $potentialmembersoptions .= '<option value="' . $group->id . '." title="' . format_string($group->name) . '">' .
                format_string($group->name) . '</option>';
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

navigation_node::override_active_url(new moodle_url('/group/index.php', array('id'=>$course->id)));
$PAGE->set_pagelayout('admin');

$PAGE->navbar->add($strparticipants, new moodle_url('/user/index.php', array('id'=>$courseid)));
$PAGE->navbar->add(get_string('groupings', 'group'),
    new moodle_url('/group/groupings.php', ['id' => $courseid]));
$PAGE->navbar->add($straddgroupstogroupings);

/// Print header
$PAGE->set_title("$course->shortname: $strgroups");
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

?>
<div id="addmembersform">
    <h3 class="main"><?php print_string('addgroupstogroupings', 'group'); echo ": $groupingname"; ?></h3>
    <form id="assignform" method="post" action="">
    <div>
    <input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />
    <table summary="" class="table generaltable groupmanagementtable table-hover">
    <tr>
      <td id="existingcell">
          <label for="removeselect"><?php print_string('existingmembers', 'group', $currentmemberscount); ?></label>
          <div class="userselector" id="removeselect_wrapper">
          <select name="removeselect[]" size="20" id="removeselect" multiple="multiple"
                  onfocus="document.getElementById('assignform').add.disabled=true;
                           document.getElementById('assignform').remove.disabled=false;
                           document.getElementById('assignform').addselect.selectedIndex=-1;">
          <?php echo $currentmembersoptions ?>
          </select></div></td>
      <td id="buttonscell">
        <p class="arrow_button">
            <input class="btn btn-secondary" name="add" id="add" type="submit"
                   value="<?php echo $OUTPUT->larrow().'&nbsp;'.get_string('add'); ?>"
                   title="<?php print_string('add'); ?>" /><br>
            <input class="btn btn-secondary" name="remove" id="remove" type="submit"
                   value="<?php echo get_string('remove').'&nbsp;'.$OUTPUT->rarrow(); ?>"
                   title="<?php print_string('remove'); ?>" />
        </p>
      </td>
      <td id="potentialcell">
          <label for="addselect"><?php print_string('potentialmembers', 'group', $potentialmemberscount); ?></label>
          <div class="userselector" id="addselect_wrapper">
          <select name="addselect[]" size="20" id="addselect" multiple="multiple"
                  onfocus="document.getElementById('assignform').add.disabled=false;
                           document.getElementById('assignform').remove.disabled=true;
                           document.getElementById('assignform').removeselect.selectedIndex=-1;">
         <?php echo $potentialmembersoptions ?>
         </select>
          </div>
       </td>
    </tr>
    <tr><td colspan="3" id="backcell">
        <input class="btn btn-secondary" type="submit" name="cancel"
               value="<?php print_string('backtogroupings', 'group'); ?>" />
    </td></tr>
    </table>
    </div>
    </form>
</div>

<?php
    echo $OUTPUT->footer();

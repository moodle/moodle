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
 * Lets you assign roles to users in a particular context.
 *
 * @package    moodlecore
 * @subpackage role
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/roles/lib.php');

define("MAX_USERS_TO_LIST_PER_ROLE", 10);

$contextid      = required_param('contextid',PARAM_INT);
$roleid         = optional_param('roleid', 0, PARAM_INT);
$courseid       = optional_param('courseid', 0, PARAM_INT); // needed for user tabs
$extendperiod   = optional_param('extendperiod', 0, PARAM_INT);
$extendbase     = optional_param('extendbase', 3, PARAM_INT);

list($context, $course, $cm) = get_context_info_array($contextid);

$PAGE->set_url('/admin/roles/assign.php', array('contextid' => $contextid));
$PAGE->set_context($context);

$userid  = 0;
$tabfile = null;

if ($course) {
    $isfrontpage = ($context->contextlevel == CONTEXT_COURSE and $context->instanceid == SITEID);

} else {
    $isfrontpage = false;
    if ($context->contextlevel == CONTEXT_USER) {
        $courseid = optional_param('courseid', SITEID, PARAM_INT); // needed for user/tabs.php
        $course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
        $PAGE->url->param('courseid', $courseid);
        $userid = $context->instanceid;
    } else {
        $course = $SITE;
    }
}

// security
require_login($course, false, $cm);
require_capability('moodle/role:assign', $context);
$PAGE->set_context($context);

$contextname = print_context_name($context);
$courseid = $course->id;
$inmeta = $course->metacourse;

// These are needed early because of tabs.php
list($assignableroles, $assigncounts, $nameswithcounts) = get_assignable_roles($context, ROLENAME_BOTH, true);
$overridableroles = get_overridable_roles($context, ROLENAME_BOTH);

// Make sure this user can assign this role
if ($roleid && !isset($assignableroles[$roleid])) {
    $a = new stdClass;
    $a->roleid = $roleid;
    $a->context = $contextname;
    print_error('cannotassignrolehere', '', get_context_url($context), $a);
}

// Get some language strings
$straction = get_string('assignroles', 'role'); // Used by tabs.php

// Work out an appropriate page title.
if ($roleid) {
    $a = new stdClass;
    $a->role = $assignableroles[$roleid];
    $a->context = $contextname;
    $title = get_string('assignrolenameincontext', 'role', $a);
} else {
    if ($isfrontpage) {
        $title = get_string('frontpageroles', 'admin');
    } else {
        $title = get_string('assignrolesin', 'role', $contextname);
    }
}

// Build the list of options for the enrolment period dropdown.
$unlimitedperiod = get_string('unlimited');
for ($i=1; $i<=365; $i++) {
    $seconds = $i * 86400;
    $periodmenu[$seconds] = get_string('numdays', '', $i);
}
// Work out the apropriate default setting.
if ($extendperiod) {
    $defaultperiod = $extendperiod;
} else {
    $defaultperiod = $course->enrolperiod;
}

// Build the list of options for the starting from dropdown.
$timeformat = get_string('strftimedatefullshort');
$today = time();
$today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);

// MDL-12420, preventing course start date showing up as an option at system context and front page roles.
if ($course->startdate > 0) {
    $basemenu[2] = get_string('coursestart') . ' (' . userdate($course->startdate, $timeformat) . ')';
}
if ($course->enrollable != 2 || ($course->enrolstartdate == 0 || $course->enrolstartdate <= $today) && ($course->enrolenddate == 0 || $course->enrolenddate > $today)) {
    $basemenu[3] = get_string('today') . ' (' . userdate($today, $timeformat) . ')' ;
}
if ($course->enrollable == 2) {
    if($course->enrolstartdate > 0) {
        $basemenu[4] = get_string('courseenrolstart') . ' (' . userdate($course->enrolstartdate, $timeformat) . ')';
    }
    if($course->enrolenddate > 0) {
        $basemenu[5] = get_string('courseenrolend') . ' (' . userdate($course->enrolenddate, $timeformat) . ')';
    }
}

// Process any incoming role assignments before printing the header.
if ($roleid) {

    // Create the user selector objects.
    $options = array('context' => $context, 'roleid' => $roleid);

    $potentialuserselector = roles_get_potential_user_selector($context, 'addselect', $options);
    $currentuserselector = new existing_role_holders('removeselect', $options);

    // Process incoming role assignments
    $errors = array();
    if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
        $userstoassign = $potentialuserselector->get_selected_users();
        if (!empty($userstoassign)) {

            foreach ($userstoassign as $adduser) {
                $allow = true;
                if ($inmeta) {
                    if (has_capability('moodle/course:managemetacourse', $context, $adduser->id)) {
                        //ok
                    } else {
                        $managerroles = get_roles_with_capability('moodle/course:managemetacourse', CAP_ALLOW, $context);
                        if (!empty($managerroles) and !array_key_exists($roleid, $managerroles)) {
                            $erruser = $DB->get_record('user', array('id'=>$adduser->id), 'id, firstname, lastname');
                            $errors[] = get_string('metaassignerror', 'role', fullname($erruser));
                            $allow = false;
                        }
                    }
                }

                if ($allow) {
                    switch($extendbase) {
                        case 2:
                            $timestart = $course->startdate;
                            break;
                        case 3:
                            $timestart = $today;
                            break;
                        case 4:
                            $timestart = $course->enrolstartdate;
                            break;
                        case 5:
                            $timestart = $course->enrolenddate;
                            break;
                    }

                    if($extendperiod > 0) {
                        $timeend = $timestart + $extendperiod;
                    } else {
                        $timeend = 0;
                    }
                    if (! role_assign($roleid, $adduser->id, 0, $context->id, $timestart, $timeend)) {
                        $a = new stdClass;
                        $a->role = $assignableroles[$roleid];
                        $a->user = fullname($adduser);
                        $errors[] = get_string('assignerror', 'role', $a);
                    }
                }
            }

            $potentialuserselector->invalidate_selected_users();
            $currentuserselector->invalidate_selected_users();

            $rolename = $assignableroles[$roleid];
            add_to_log($course->id, 'role', 'assign', 'admin/roles/assign.php?contextid='.$context->id.'&roleid='.$roleid, $rolename, '', $USER->id);
            // Counts have changed, so reload.
            list($assignableroles, $assigncounts, $nameswithcounts) = get_assignable_roles($context, ROLENAME_BOTH, true);
        }
    }

    // Process incoming role unassignments
    if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
        $userstounassign = $currentuserselector->get_selected_users();
        if (!empty($userstounassign)) {

            foreach ($userstounassign as $removeuser) {
                if (! role_unassign($roleid, $removeuser->id, 0, $context->id)) {
                    $a = new stdClass;
                    $a->role = $assignableroles[$roleid];
                    $a->user = fullname($removeuser);
                    $errors[] = get_string('unassignerror', 'role', $a);
                } else if ($inmeta) {
                    sync_metacourse($courseid);
                    $newroles = get_user_roles($context, $removeuser->id, false);
                    if (empty($newroles) || array_key_exists($roleid, $newroles)) {
                        $errors[] = get_string('metaunassignerror', 'role', fullname($removeuser));
                    }
                }
            }

            $potentialuserselector->invalidate_selected_users();
            $currentuserselector->invalidate_selected_users();

            $rolename = $assignableroles[$roleid];
            add_to_log($course->id, 'role', 'unassign', 'admin/roles/assign.php?contextid='.$context->id.'&roleid='.$roleid, $rolename, '', $USER->id);
            // Counts have changed, so reload.
            list($assignableroles, $assigncounts, $nameswithcounts) = get_assignable_roles($context, ROLENAME_BOTH, true);
        }
    }
}

// Print the header and tabs
if ($context->contextlevel == CONTEXT_USER) {
    $user = $DB->get_record('user', array('id'=>$userid));
    $fullname = fullname($user, has_capability('moodle/site:viewfullnames', $context));

    /// course header
    $PAGE->set_title($title);
    if ($courseid != SITEID) {
        if (has_capability('moodle/course:viewparticipants', get_context_instance(CONTEXT_COURSE, $courseid))) {
            $PAGE->navbar->add(get_string('participants'), new moodle_url('/user/index.php', array('id'=>$courseid)));
        }
        $PAGE->set_heading($fullname);
    } else {
        $PAGE->set_heading($course->fullname);
    }
    $PAGE->navbar->add($fullname, new moodle_url("$CFG->wwwroot/user/view.php", array('id'=>$userid,'course'=>$courseid)));
    $PAGE->navbar->add($straction);
    echo $OUTPUT->header();

    $showroles = 1;
    $currenttab = 'assign';
    echo $OUTPUT->header();
    include($CFG->dirroot.'/user/tabs.php');

} else if ($context->contextlevel == CONTEXT_SYSTEM) {
    admin_externalpage_setup('assignroles', '', array('contextid' => $contextid, 'roleid' => $roleid));
    echo $OUTPUT->header();

} else if ($isfrontpage) {
    admin_externalpage_setup('frontpageroles', '', array('contextid' => $contextid, 'roleid' => $roleid));
    echo $OUTPUT->header();
    $currenttab = 'assign';
    include('tabs.php');

} else {
    echo $OUTPUT->header();
    $currenttab = 'assign';
    include('tabs.php');
}

// Print heading.
echo $OUTPUT->heading_with_help($title, 'assignroles');

if ($roleid) {
    // Show UI for assigning a particular role to users.
    // Print a warning if we are assigning system roles.
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        echo $OUTPUT->box(get_string('globalroleswarning', 'role'));
    }

    // Print the form.
$assignurl = new moodle_url($PAGE->url, array('roleid'=>$roleid));
?>
<form id="assignform" method="post" action="<?php echo $assignurl ?>"><div>
  <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />

  <table summary="" class="roleassigntable generaltable generalbox boxaligncenter" cellspacing="0">
    <tr>
      <td id="existingcell">
          <p><label for="removeselect"><?php print_string('extusers', 'role'); ?></label></p>
          <?php $currentuserselector->display() ?>
      </td>
      <td id="buttonscell">
          <div id="addcontrols">
              <input name="add" id="add" type="submit" value="<?php echo $OUTPUT->larrow().'&nbsp;'.get_string('add'); ?>" title="<?php print_string('add'); ?>" /><br />

              <?php print_collapsible_region_start('', 'assignoptions', get_string('enrolmentoptions', 'role'),
                    'assignoptionscollapse', true); ?>

              <p><label for="extendperiod"><?php print_string('enrolperiod') ?></label><br />
              <?php echo html_writer::select($periodmenu, 'extendperiod', $defaultperiod, $unlimitedperiod); ?></p>

              <p><label for="extendbase"><?php print_string('startingfrom') ?></label><br />
              <?php echo html_writer::select($basemenu, 'extendbase', $extendbase, false); ?></p>
              <?php print_collapsible_region_end(); ?>
          </div>

          <div id="removecontrols">
              <input name="remove" id="remove" type="submit" value="<?php echo get_string('remove').'&nbsp;'.$OUTPUT->rarrow(); ?>" title="<?php print_string('remove'); ?>" />
          </div>
      </td>
      <td id="potentialcell">
          <p><label for="addselect"><?php print_string('potusers', 'role'); ?></label></p>
          <?php $potentialuserselector->display() ?>
      </td>
    </tr>
  </table>
</div></form>

<?php
    $PAGE->requires->js_init_call('M.core_role.init_add_assign_page');

    if (!empty($errors)) {
        $msg = '<p>';
        foreach ($errors as $e) {
            $msg .= $e.'<br />';
        }
        $msg .= '</p>';
        echo $OUTPUT->box_start();
        echo $OUTPUT->notification($msg);
        echo $OUTPUT->box_end();
    }

    // Print a form to swap roles, and a link back to the all roles list.
    echo '<div class="backlink">';

    $select = new single_select($PAGE->url, 'roleid', $nameswithcounts, $roleid, null);
    $select->label = get_string('assignanotherrole', 'role');
    echo $OUTPUT->render($select);
    echo '<p><a href="' . $PAGE->url . '">' . get_string('backtoallroles', 'role') . '</a></p>';
    echo '</div>';

} else if (empty($assignableroles)) {
    // Print a message that there are no roles that can me assigned here.
    echo $OUTPUT->heading(get_string('notabletoassignroleshere', 'role'), 3);

} else {
    // Show UI for choosing a role to assign.

    // Print a warning if we are assigning system roles.
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        echo $OUTPUT->box(get_string('globalroleswarning', 'role'));
    }

    // Print instruction
    echo $OUTPUT->heading(get_string('chooseroletoassign', 'role'), 3);

    // sync metacourse enrolments if needed
    if ($inmeta) {
        sync_metacourse($course);
    }

    // Get the names of role holders for roles with between 1 and MAX_USERS_TO_LIST_PER_ROLE users,
    // and so determine whether to show the extra column.
    $roleholdernames = array();
    $strmorethanmax = get_string('morethan', 'role', MAX_USERS_TO_LIST_PER_ROLE);
    $showroleholders = false;
    foreach ($assignableroles as $roleid => $notused) {
        $roleusers = '';
        if (0 < $assigncounts[$roleid] && $assigncounts[$roleid] <= MAX_USERS_TO_LIST_PER_ROLE) {
            $roleusers = get_role_users($roleid, $context, false, 'u.id, u.lastname, u.firstname');
            if (!empty($roleusers)) {
                $strroleusers = array();
                foreach ($roleusers as $user) {
                    $strroleusers[] = '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $user->id . '" >' . fullname($user) . '</a>';
                }
                $roleholdernames[$roleid] = implode('<br />', $strroleusers);
                $showroleholders = true;
            }
        } else if ($assigncounts[$roleid] > MAX_USERS_TO_LIST_PER_ROLE) {
            $assignurl = new moodle_url($PAGE->url, array('roleid'=>$roleid));
            $roleholdernames[$roleid] = '<a href="'.$assignurl.'">'.$strmorethanmax.'</a>';
        } else {
            $roleholdernames[$roleid] = '';
        }
    }

    // Print overview table
    $table = new html_table();
    $table->tablealign = 'center';
    $table->width = '60%';
    $table->head = array(get_string('role'), get_string('description'), get_string('userswiththisrole', 'role'));
    $table->wrap = array('nowrap', '', 'nowrap');
    $table->align = array('left', 'left', 'center');
    if ($showroleholders) {
        $table->headspan = array(1, 1, 2);
        $table->wrap[] = 'nowrap';
        $table->align[] = 'left';
    }

    foreach ($assignableroles as $roleid => $rolename) {
        $description = format_string($DB->get_field('role', 'description', array('id'=>$roleid)));
        $assignurl = new moodle_url($PAGE->url, array('roleid'=>$roleid));
        $row = array('<a href="'.$assignurl.'">'.$rolename.'</a>',
                $description, $assigncounts[$roleid]);
        if ($showroleholders) {
            $row[] = $roleholdernames[$roleid];
        }
        $table->data[] = $row;
    }

    echo html_writer::table($table);

    if ($context->contextlevel > CONTEXT_USER) {
        echo '<div class="backlink"><a href="' . get_context_url($context) . '">' . get_string('backto', '', $contextname) . '</a></div>';
    }
}

echo $OUTPUT->footer();

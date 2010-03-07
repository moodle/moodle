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
 * Lets you override role definitions in contexts.
 *
 * @package    moodlecore
 * @subpackage role
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once("$CFG->dirroot/$CFG->admin/roles/lib.php");

$contextid = required_param('contextid', PARAM_INT);   // context id
$roleid    = required_param('roleid', PARAM_INT);   // requested role id

// security first
list($context, $course, $cm) = get_context_info_array($contextid);
require_login($course, false, $cm);
$safeoverridesonly = !has_capability('moodle/role:override', $context);
if ($safeoverridesonly) {
    require_capability('moodle/role:safeoverride', $context);
}

$PAGE->set_url('/admin/roles/override.php', array('contextid' => $contextid, 'roleid' => $roleid));
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

$courseid = $course->id;

$baseurl = $PAGE->url->out();
$returnurl = new moodle_url('/admin/roles/permissions.php', array('contextid' => $context->id));

$role = $DB->get_record('role', array('id'=>$roleid), '*', MUST_EXIST);

// These are needed early because of tabs.php
$assignableroles  = get_assignable_roles($context, ROLENAME_BOTH);
list($overridableroles, $overridecounts, $nameswithcounts) = get_overridable_roles($context, ROLENAME_BOTH, true);

// Work out an appropriate page title.
$contextname = print_context_name($context);
$straction = get_string('overrideroles', 'role'); // Used by tabs.php
$a = (object)array('context' => $contextname, 'role' => $overridableroles[$roleid]);
$title = get_string('overridepermissionsforrole', 'role', $a);

// Print the header and tabs
if ($context->contextlevel == CONTEXT_SYSTEM) {
    print_error('cannotoverridebaserole', 'error');

} else if ($context->contextlevel == CONTEXT_USER) {
    // NOTE: this is not linked from UI for now
    $userid = $context->instanceid;
    $user = $DB->get_record('user', array('id'=>$userid, 'deleted'=>0), '*', MUST_EXIST);
    $fullname = fullname($user, has_capability('moodle/site:viewfullnames', $context));

    // course header
    if ($isfrontpage) {
        $PAGE->set_heading($course->fullname);
    } else {
        if (has_capability('moodle/course:viewparticipants', get_context_instance(CONTEXT_COURSE, $courseid))) {
            $PAGE->navbar->add(get_string('participants'), new moodle_url('/user/index.php', array('id'=>$courseid)));
        }
        $PAGE->set_heading($fullname);
    }
    $PAGE->navbar->add($fullname, new moodle_url("$CFG->wwwroot/user/view.php", array('id'=>$userid,'course'=>$courseid)));
    $PAGE->navbar->add($straction);

    $showroles = 1;
    $currenttab = 'permissions';
    $tabfile = $CFG->dirroot.'/user/tabs.php';

} else if ($isfrontpage) {
    admin_externalpage_setup('frontpageroles', '', array(), $PAGE->url);
    $currenttab = 'permissions';
    $tabfile = 'tabs.php';

} else {
    $currenttab = 'permissions';
    $tabfile = 'tabs.php';
}


// Handle the cancel button.
if (optional_param('cancel', false, PARAM_BOOL)) {
    redirect($returnurl);
}

// Make sure this user can override that role
if (empty($overridableroles[$roleid])) {
    $a = new stdClass;
    $a->roleid = $roleid;
    $a->context = $contextname;
    print_error('cannotoverriderolehere', '', get_context_url($context), $a);
}

// If we are actually overriding a role, create the table object, and save changes if appropriate.
$overridestable = new override_permissions_table_advanced($context, $roleid, $safeoverridesonly);
$overridestable->read_submitted_permissions();

if (optional_param('savechanges', false, PARAM_BOOL) && confirm_sesskey()) {
    $overridestable->save_changes();
    $rolename = $overridableroles[$roleid];
    add_to_log($course->id, 'role', 'override', 'admin/roles/override.php?contextid='.$context->id.'&roleid='.$roleid, $rolename, '', $USER->id);
    redirect($returnurl);
}

// Finally start page output
echo $OUTPUT->header();
if ($tabfile) {
    include($tabfile);
}
echo $OUTPUT->heading_with_help($title, 'overrides');

// Show UI for overriding roles.
if (!empty($capabilities)) {
    echo $OUTPUT->box(get_string('nocapabilitiesincontext', 'role'), 'generalbox boxaligncenter');

} else {
    // Print the capabilities overrideable in this context
    echo $OUTPUT->box_start('generalbox capbox');

    ?>
<form id="overrideform" action="<?php echo $baseurl ?>" method="post"><div>
    <input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />
    <input type="hidden" name="roleid" value="<?php p($roleid); ?>" />
            <?php

            echo '<p class="overridenotice">' . get_string('highlightedcellsshowinherit', 'role') . ' </p>';
            $overridestable->display();

            if ($overridestable->has_locked_capabiltites()) {
                echo '<p class="overridenotice">' . get_string('safeoverridenotice', 'role') . "</p>\n";
            }

            ?>
    <div class="submit buttons">
        <input type="submit" name="savechanges" value="<?php print_string('savechanges') ?>" />
        <input type="submit" name="cancel" value="<?php print_string('cancel') ?>" />
    </div>
</div></form>
    <?php
    echo $OUTPUT->box_end();

}

// Print a form to swap roles, and a link back to the all roles list.
echo '<div class="backlink">';
$select = new single_select(new moodle_url($baseurl), 'roleid', $nameswithcounts, $roleid, null);
$select->label = get_string('overrideanotherrole', 'role');
echo $OUTPUT->render($select);
echo '<p><a href="' . $returnurl . '">' . get_string('backtoallroles', 'role') . '</a></p>';
echo '</div>';

echo $OUTPUT->footer();

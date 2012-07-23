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
 * Lets the user edit role definitions.
 *
 * Responds to actions:
 *   add       - add a new role
 *   duplicate - like add, only initialise the new role by using an existing one.
 *   edit      - edit the definition of a role
 *   view      - view the definition of a role
 *
 * @package    core
 * @subpackage role
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    require_once(dirname(__FILE__) . '/../../config.php');
    require_once($CFG->dirroot . '/' . $CFG->admin . '/roles/lib.php');

    $action = required_param('action', PARAM_ALPHA);
    if (!in_array($action, array('add', 'duplicate', 'edit', 'view'))) {
        throw new moodle_exception('invalidaccess');
    }
    if ($action != 'add') {
        $roleid = required_param('roleid', PARAM_INT);
    } else {
        $roleid = 0;
    }

/// Get the base URL for this and related pages into a convenient variable.
    $manageurl = $CFG->wwwroot . '/' . $CFG->admin . '/roles/manage.php';
    $defineurl = $CFG->wwwroot . '/' . $CFG->admin . '/roles/define.php';
    if ($action == 'duplicate') {
        $baseurl = $defineurl . '?action=add';
    } else {
        $baseurl = $defineurl . '?action=' . $action;
        if ($roleid) {
            $baseurl .= '&amp;roleid=' . $roleid;
        }
    }

/// Check access permissions.
    $systemcontext = context_system::instance();
    require_login();
    require_capability('moodle/role:manage', $systemcontext);
    admin_externalpage_setup('defineroles', '', array('action' => $action, 'roleid' => $roleid), $defineurl);

/// Handle the cancel button.
    if (optional_param('cancel', false, PARAM_BOOL)) {
        redirect($manageurl);
    }

/// Handle the toggle advanced mode button.
    $showadvanced = get_user_preferences('definerole_showadvanced', false);
    if (optional_param('toggleadvanced', false, PARAM_BOOL)) {
        $showadvanced = !$showadvanced;
        set_user_preference('definerole_showadvanced', $showadvanced);
    }

/// Get some basic data we are going to need.
    $roles = get_all_roles();
    $rolenames = role_fix_names($roles, $systemcontext, ROLENAME_ORIGINAL);
    $rolescount = count($roles);

/// Create the table object.
    if ($action == 'view') {
        $definitiontable = new view_role_definition_table($systemcontext, $roleid);
    } else if ($showadvanced) {
        $definitiontable = new define_role_table_advanced($systemcontext, $roleid);
    } else {
        $definitiontable = new define_role_table_basic($systemcontext, $roleid);
    }
    $definitiontable->read_submitted_permissions();
    if ($action == 'duplicate') {
        $definitiontable->make_copy();
    }

/// Process submission in necessary.
    if (optional_param('savechanges', false, PARAM_BOOL) && confirm_sesskey() && $definitiontable->is_submission_valid()) {
        $definitiontable->save_changes();
        add_to_log(SITEID, 'role', $action, 'admin/roles/define.php?action=view&roleid=' .
                $definitiontable->get_role_id(), $definitiontable->get_role_name(), '', $USER->id);
        redirect($manageurl);
    }

/// Print the page header and tabs.
    echo $OUTPUT->header();

    $currenttab = 'manage';
    include('managetabs.php');

    if ($action == 'add') {
        $title = get_string('addinganewrole', 'role');
    } else if ($action == 'duplicate') {
        $title = get_string('addingrolebycopying', 'role', $rolenames[$roleid]->localname);
    } else if ($action == 'view') {
        $title = get_string('viewingdefinitionofrolex', 'role', $rolenames[$roleid]->localname);
    } else if ($action == 'edit') {
        $title = get_string('editingrolex', 'role', $rolenames[$roleid]->localname);
    }
    echo $OUTPUT->heading_with_help($title, 'roles', 'role');

/// Work out some button labels.
    if ($action == 'add' || $action == 'duplicate') {
        $submitlabel = get_string('createthisrole', 'role');
    } else {
        $submitlabel = get_string('savechanges');
    }

/// On the view page, show some extra controls at the top.
    if ($action == 'view') {
        echo $OUTPUT->container_start('buttons');
        $options = array();
        $options['roleid'] = $roleid;
        $options['action'] = 'edit';
        echo $OUTPUT->single_button(new moodle_url($defineurl, $options), get_string('edit'));
        $options['action'] = 'reset';
        if ($definitiontable->get_archetype()) {
            echo $OUTPUT->single_button(new moodle_url($manageurl, $options), get_string('resetrole', 'role'));
        } else {
            echo $OUTPUT->single_button(new moodle_url($manageurl, $options), get_string('resetrolenolegacy', 'role'));
        }
        $options['action'] = 'duplicate';
        echo $OUTPUT->single_button(new moodle_url($defineurl, $options), get_string('duplicaterole', 'role'));
        echo $OUTPUT->single_button(new moodle_url($manageurl), get_string('listallroles', 'role'));
        echo $OUTPUT->container_end();
    }

    // Start the form.
    echo $OUTPUT->box_start('generalbox');
    if ($action == 'view') {
        echo '<div class="mform">';
    } else {
    ?>
<form id="rolesform" class="mform" action="<?php echo $baseurl; ?>" method="post"><div>
<input type="hidden" name="sesskey" value="<?php p(sesskey()) ?>" />
<div class="submit buttons">
    <input type="submit" name="savechanges" value="<?php echo $submitlabel; ?>" />
    <input type="submit" name="cancel" value="<?php print_string('cancel'); ?>" />
</div>
    <?php
    }

    // Print the form controls.
    $definitiontable->display();

/// Close the stuff we left open above.
    if ($action == 'view') {
        echo '</div>';
    } else {
        ?>
<div class="submit buttons">
    <input type="submit" name="savechanges" value="<?php echo $submitlabel; ?>" />
    <input type="submit" name="cancel" value="<?php print_string('cancel'); ?>" />
</div>
</div></form>
        <?php
    }
    echo $OUTPUT->box_end();

/// Print a link back to the all roles list.
    echo '<div class="backlink">';
    echo '<p><a href="' . $manageurl . '">' . get_string('backtoallroles', 'role') . '</a></p>';
    echo '</div>';

    echo $OUTPUT->footer();

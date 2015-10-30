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
 *   add       - add a new role (allows import, duplicate, archetype)
 *   export    - save xml role definition
 *   edit      - edit the definition of a role
 *   view      - view the definition of a role
 *
 * @package    core_role
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$action = required_param('action', PARAM_ALPHA);
if (!in_array($action, array('add', 'export', 'edit', 'reset', 'view'))) {
    throw new moodle_exception('invalidaccess');
}
if ($action != 'add') {
    $roleid = required_param('roleid', PARAM_INT);
} else {
    $roleid = 0;
}
$resettype = optional_param('resettype', '', PARAM_RAW);
$return = optional_param('return', 'manage', PARAM_ALPHA);

// Get the base URL for this and related pages into a convenient variable.
$baseurl = new moodle_url('/admin/roles/define.php', array('action'=>$action, 'roleid'=>$roleid));
$manageurl = new moodle_url('/admin/roles/manage.php');
if ($return === 'manage') {
    $returnurl = $manageurl;
} else {
    $returnurl = new moodle_url('/admin/roles/define.php', array('action'=>'view', 'roleid'=>$roleid));;
}

// Check access permissions.
$systemcontext = context_system::instance();
require_login();
require_capability('moodle/role:manage', $systemcontext);
admin_externalpage_setup('defineroles', '', array('action' => $action, 'roleid' => $roleid), new moodle_url('/admin/roles/define.php'));

// Export role.
if ($action === 'export') {
    core_role_preset::send_export_xml($roleid);
    die;
}

// Handle the toggle advanced mode button.
$showadvanced = get_user_preferences('definerole_showadvanced', false);
if (optional_param('toggleadvanced', false, PARAM_BOOL)) {
    $showadvanced = !$showadvanced;
    set_user_preference('definerole_showadvanced', $showadvanced);
}

// Get some basic data we are going to need.
$roles = get_all_roles();
$rolenames = role_fix_names($roles, $systemcontext, ROLENAME_ORIGINAL);
$rolescount = count($roles);

if ($action === 'add') {
    $title = get_string('addinganewrole', 'core_role');
} else if ($action == 'view') {
    $title = get_string('viewingdefinitionofrolex', 'core_role', $rolenames[$roleid]->localname);
} else if ($action == 'reset') {
    $title = get_string('resettingrole', 'core_role', $rolenames[$roleid]->localname);
} else {
    $title = get_string('editingrolex', 'core_role', $rolenames[$roleid]->localname);
}

// Decide how to create new role.
if ($action === 'add' and $resettype !== 'none') {
    $mform = new core_role_preset_form(null, array('action'=>'add', 'roleid'=>0, 'resettype'=>'0', 'return'=>'manage'));
    if ($mform->is_cancelled()) {
        redirect($manageurl);

    } else if ($data = $mform->get_data()) {
        $resettype = $data->resettype;
        $options = array(
            'shortname'     => 1,
            'name'          => 1,
            'description'   => 1,
            'permissions'   => 1,
            'archetype'     => 1,
            'contextlevels' => 1,
            'allowassign'   => 1,
            'allowoverride' => 1,
            'allowswitch'   => 1);
        if ($showadvanced) {
            $definitiontable = new core_role_define_role_table_advanced($systemcontext, 0);
        } else {
            $definitiontable = new core_role_define_role_table_basic($systemcontext, 0);
        }
        if (is_number($resettype)) {
            // Duplicate the role.
            $definitiontable->force_duplicate($resettype, $options);
        } else {
            // Must be an archetype.
            $definitiontable->force_archetype($resettype, $options);
        }

        if ($xml = $mform->get_file_content('rolepreset')) {
            $definitiontable->force_preset($xml, $options);
        }

    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->heading_with_help($title, 'roles', 'core_role');
        $mform->display();
        echo $OUTPUT->footer();
        die;
    }

} else if ($action === 'reset' and $resettype !== 'none') {
    if (!$role = $DB->get_record('role', array('id'=>$roleid))) {
        redirect($manageurl);
    }
    $resettype = empty($role->archetype) ? '0' : $role->archetype;
    $mform = new core_role_preset_form(null,
        array('action'=>'reset', 'roleid'=>$roleid, 'resettype'=>$resettype , 'permissions'=>1, 'archetype'=>1, 'contextlevels'=>1, 'return'=>$return));
    if ($mform->is_cancelled()) {
        redirect($returnurl);

    } else if ($data = $mform->get_data()) {
        $resettype = $data->resettype;
        $options = array(
            'shortname'     => $data->shortname,
            'name'          => $data->name,
            'description'   => $data->description,
            'permissions'   => $data->permissions,
            'archetype'     => $data->archetype,
            'contextlevels' => $data->contextlevels,
            'allowassign'   => $data->allowassign,
            'allowoverride' => $data->allowoverride,
            'allowswitch'   => $data->allowswitch);
        if ($showadvanced) {
            $definitiontable = new core_role_define_role_table_advanced($systemcontext, $roleid);
        } else {
            $definitiontable = new core_role_define_role_table_basic($systemcontext, $roleid);
        }
        if (is_number($resettype)) {
            // Duplicate the role.
            $definitiontable->force_duplicate($resettype, $options);
        } else {
            // Must be an archetype.
            $definitiontable->force_archetype($resettype, $options);
        }

        if ($xml = $mform->get_file_content('rolepreset')) {
            $definitiontable->force_preset($xml, $options);
        }

    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->heading_with_help($title, 'roles', 'core_role');
        $mform->display();
        echo $OUTPUT->footer();
        die;
    }

} else {
    // Create the table object.
    if ($action === 'view') {
        $definitiontable = new core_role_view_role_definition_table($systemcontext, $roleid);
    } else if ($showadvanced) {
        $definitiontable = new core_role_define_role_table_advanced($systemcontext, $roleid);
    } else {
        $definitiontable = new core_role_define_role_table_basic($systemcontext, $roleid);
    }
    $definitiontable->read_submitted_permissions();
}

// Handle the cancel button.
if (optional_param('cancel', false, PARAM_BOOL)) {
    redirect($returnurl);
}

// Process submission in necessary.
if (optional_param('savechanges', false, PARAM_BOOL) && confirm_sesskey() && $definitiontable->is_submission_valid()) {
    $definitiontable->save_changes();
    $tableroleid = $definitiontable->get_role_id();
    // Trigger event.
    $event = \core\event\role_capabilities_updated::create(
        array(
            'context' => $systemcontext,
            'objectid' => $roleid
        )
    );
    $event->set_legacy_logdata(array(SITEID, 'role', $action, 'admin/roles/define.php?action=view&roleid=' . $tableroleid,
        $definitiontable->get_role_name(), '', $USER->id));
    if (!empty($role)) {
        $event->add_record_snapshot('role', $role);
    }
    $event->trigger();

    if ($action === 'add') {
        redirect(new moodle_url('/admin/roles/define.php', array('action'=>'view', 'roleid'=>$definitiontable->get_role_id())));
    } else {
        redirect($returnurl);
    }
}

// Print the page header and tabs.
echo $OUTPUT->header();

$currenttab = 'manage';
require('managetabs.php');

echo $OUTPUT->heading_with_help($title, 'roles', 'core_role');

// Work out some button labels.
if ($action === 'add') {
    $submitlabel = get_string('createthisrole', 'core_role');
} else {
    $submitlabel = get_string('savechanges');
}

// On the view page, show some extra controls at the top.
if ($action === 'view') {
    echo $OUTPUT->container_start('buttons');
    $url = new moodle_url('/admin/roles/define.php', array('action'=>'edit', 'roleid'=>$roleid, 'return'=>'define'));
    echo $OUTPUT->single_button(new moodle_url($url), get_string('edit'));
    $url = new moodle_url('/admin/roles/define.php', array('action'=>'reset', 'roleid'=>$roleid, 'return'=>'define'));
    echo $OUTPUT->single_button(new moodle_url($url), get_string('resetrole', 'core_role'));
    $url = new moodle_url('/admin/roles/define.php', array('action'=>'export', 'roleid'=>$roleid));
    echo $OUTPUT->single_button(new moodle_url($url), get_string('export', 'core_role'));
    echo $OUTPUT->single_button($manageurl, get_string('listallroles', 'core_role'));
    echo $OUTPUT->container_end();
}

// Start the form.
echo $OUTPUT->box_start('generalbox');
if ($action === 'view') {
    echo '<div class="mform">';
} else {
    ?>
<form id="rolesform" class="mform" action="<?php p($baseurl->out(false)); ?>" method="post"><div>
<input type="hidden" name="sesskey" value="<?php p(sesskey()) ?>" />
<input type="hidden" name="return" value="<?php p($return); ?>" />
<input type="hidden" name="resettype" value="none" />
<div class="submit buttons">
    <input type="submit" name="savechanges" value="<?php p($submitlabel); ?>" />
    <input type="submit" name="cancel" value="<?php print_string('cancel'); ?>" />
</div>
    <?php
}

// Print the form controls.
$definitiontable->display();

// Close the stuff we left open above.
if ($action === 'view') {
    echo '</div>';
} else {
    ?>
<div class="submit buttons">
    <input type="submit" name="savechanges" value="<?php p($submitlabel); ?>" />
    <input type="submit" name="cancel" value="<?php print_string('cancel'); ?>" />
</div>
</div></form>
<?php
}
echo $OUTPUT->box_end();

// Print a link back to the all roles list.
echo '<div class="backlink">';
echo '<p><a href="' . s($manageurl->out(false)) . '">' . get_string('backtoallroles', 'core_role') . '</a></p>';
echo '</div>';

echo $OUTPUT->footer();

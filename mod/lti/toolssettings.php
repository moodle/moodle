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
 * This file contains the script used to register a new external tool.
 *
 * It is used to create a new form used to configure the capabilities
 * and services to be offered to the tool provider.
 *
 * @package mod_lti
 * @copyright  2014 Vital Source Technologies http://vitalsource.com
 * @author     Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/mod/lti/edit_form.php');
require_once($CFG->dirroot.'/mod/lti/locallib.php');

$action       = optional_param('action', '', PARAM_ALPHANUMEXT);
$id           = optional_param('id', '', PARAM_INT);
$tab          = optional_param('tab', '', PARAM_ALPHAEXT);

// No guest autologin.
require_login(0, false);

require_sesskey();

// Check this is for a tool created from a tool proxy.
$err = empty($id);
if (!$err) {
    $type = lti_get_type_type_config($id);
    $err = empty($type->toolproxyid);
}
if ($err) {
    $redirect = new moodle_url('/mod/lti/typessettings.php',
        array('action' => $action, 'id' => $id, 'sesskey' => sesskey(), 'tab' => $tab));
    redirect($redirect);
}

$pageurl = new moodle_url('/mod/lti/toolssettings.php');
if (!empty($id)) {
    $pageurl->param('id', $id);
}
$PAGE->set_url($pageurl);

admin_externalpage_setup('managemodules'); // Hacky solution for printing the admin page.

$redirect = "$CFG->wwwroot/$CFG->admin/settings.php?section=modsettinglti&tab={$tab}";

if ($action == 'accept') {
    lti_set_state_for_type($id, LTI_TOOL_STATE_CONFIGURED);
    redirect($redirect);
} else if (($action == 'reject') || ($action == 'delete')) {
    lti_set_state_for_type($id, LTI_TOOL_STATE_REJECTED);
    redirect($redirect);
}

$form = new mod_lti_edit_types_form($pageurl, (object)array('isadmin' => true, 'istool' => true));

if ($data = $form->get_data()) {
    $type = new stdClass();
    if (!empty($id)) {
        $type->id = $id;
        lti_update_type($type, $data);
    } else {
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        lti_add_type($type, $data);
    }
    redirect($redirect);
} else if ($form->is_cancelled()) {
    redirect($redirect);
}

$PAGE->set_title(format_string($SITE->shortname) . ': ' . get_string('toolsetup', 'lti'));
$PAGE->navbar->add(get_string('lti_administration', 'lti'), $CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section=modsettinglti');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('toolsetup', 'lti'));
echo $OUTPUT->box_start('generalbox');

if ($action == 'update') {
    $form->set_data($type);
}

$form->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();

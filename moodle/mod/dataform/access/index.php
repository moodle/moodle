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
 * @package dataformaccess
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');

$urlparams = new stdClass;

$urlparams->d          = optional_param('d', 0, PARAM_INT);
$urlparams->id         = optional_param('id', 0, PARAM_INT);

// Items list actions.
// Type of a rule to add.
$urlparams->type = optional_param('type', '', PARAM_ALPHA);
// Block id of rule to edit.
$urlparams->biid = optional_param('biid', 0, PARAM_INT);
// Update item.
$urlparams->update = optional_param('update', 0, PARAM_INT);
$urlparams->cancel = optional_param('cancel', 0, PARAM_BOOL);
// Enable context (show block).
$urlparams->enable = optional_param('enable', 0, PARAM_INT);
// Disable context (hide block).
$urlparams->disable = optional_param('disable', 0, PARAM_INT);
// Delete context (delete block).
$urlparams->delete = optional_param('delete', 0, PARAM_INT);

$urlparams->confirmed    = optional_param('confirmed', 0, PARAM_INT);

// Set a dataform object.
$df = mod_dataform_dataform::instance($urlparams->d, $urlparams->id);
$df->require_manage_permission('access');

$df->set_page('access/index', array('urlparams' => $urlparams));
$PAGE->set_context($df->context);

// Activate navigation node.
navigation_node::override_active_url(new moodle_url('/mod/dataform/access/index.php', array('id' => $df->cm->id)));

$aman = mod_dataform_access_manager::instance($df->id);

// DATA PROCESSING
// Enable.
if ($urlparams->enable and confirm_sesskey()) {
    $aman->set_rule_visibility($urlparams->enable, 1);
}
// Disable.
if ($urlparams->disable and confirm_sesskey()) {
    $aman->set_rule_visibility($urlparams->disable, 0);
}
// Delete.
if ($urlparams->delete and confirm_sesskey()) {
    $aman->delete_rule($urlparams->delete);
}

$output = $df->get_renderer();
echo $output->header(array('tab' => 'access', 'heading' => $df->name, 'urlparams' => $urlparams));

if ($accesstypes = $aman->get_types()) {
    foreach ($accesstypes as $blocktype => $accesstype) {
        $type = str_replace('dataformaccess', '', $blocktype);
        $rules = $aman->get_type_rules($type);
        echo $output->rules_admin_list('access', $accesstype, $blocktype, $rules);
    }
} else {
    echo $output->notification(get_string('acccesstypesnotfound', 'dataform'), 'notifyproblem');
}

echo $output->footer();

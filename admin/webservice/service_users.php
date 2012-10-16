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
 * Web services services UI
 *
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/webservice/lib.php');
require_once($CFG->dirroot . '/webservice/lib.php');

$id = required_param('id', PARAM_INT);

admin_externalpage_setup('externalserviceusers');

//define nav bar
$PAGE->set_url('/' . $CFG->admin . '/webservice/service_users.php', array('id' => $id));
$node = $PAGE->settingsnav->find('externalservices', navigation_node::TYPE_SETTING);
if ($node) {
    $node->make_active();
}
$PAGE->navbar->add(get_string('serviceusers', 'webservice'),
        new moodle_url('/' . $CFG->admin . '/webservice/service_users.php', array('id' => $id)));

$webservicemanager = new webservice();

/// Get the user_selector we will need.
$potentialuserselector = new service_user_selector('addselect',
                array('serviceid' => $id, 'displayallowedusers' => 0));
$alloweduserselector = new service_user_selector('removeselect',
                array('serviceid' => $id, 'displayallowedusers' => 1));

/// Process incoming user assignments to the service
if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
    $userstoassign = $potentialuserselector->get_selected_users();
    if (!empty($userstoassign)) {
        foreach ($userstoassign as $adduser) {
            $serviceuser = new stdClass();
            $serviceuser->externalserviceid = $id;
            $serviceuser->userid = $adduser->id;
            $webservicemanager->add_ws_authorised_user($serviceuser);
            add_to_log(SITEID, 'core', 'assign', $CFG->admin . '/webservice/service_users.php?id='
                    . $id, 'add', '', $adduser->id);
        }
        $potentialuserselector->invalidate_selected_users();
        $alloweduserselector->invalidate_selected_users();
    }
}

/// Process removing user assignments to the service
if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
    $userstoremove = $alloweduserselector->get_selected_users();
    if (!empty($userstoremove)) {
        foreach ($userstoremove as $removeuser) {
            $webservicemanager->remove_ws_authorised_user($removeuser, $id);
            add_to_log(SITEID, 'core', 'assign', $CFG->admin . '/webservice/service_users.php?id='
                    . $id, 'remove', '', $removeuser->id);
        }
        $potentialuserselector->invalidate_selected_users();
        $alloweduserselector->invalidate_selected_users();
    }
}
/// Print the form.
/// display the UI
$renderer = $PAGE->get_renderer('core', 'webservice');

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('selectauthorisedusers', 'webservice'), 3, 'main');
$selectoroptions = new stdClass();
$selectoroptions->serviceid = $id;
$selectoroptions->alloweduserselector = $alloweduserselector;
$selectoroptions->potentialuserselector = $potentialuserselector;
echo $renderer->admin_authorised_user_selector($selectoroptions);

/// get the missing capabilities for all users (will be displayed into the renderer)
$allowedusers = $webservicemanager->get_ws_authorised_users($id);
$usersmissingcaps = $webservicemanager->get_missing_capabilities_by_users($allowedusers, $id);

//add the missing capabilities to the allowed users object to be displayed by renderer
foreach ($allowedusers as &$alloweduser) {
    if (!is_siteadmin($alloweduser->id) and array_key_exists($alloweduser->id, $usersmissingcaps)) {
        $alloweduser->missingcapabilities = implode(', ', $usersmissingcaps[$alloweduser->id]);
    }
}

/// display the list of allowed users with their options (ip/timecreated / validuntil...)
//check that the user has the service required capability (if needed)
if (!empty($allowedusers)) {
    $renderer = $PAGE->get_renderer('core', 'webservice');
    echo $OUTPUT->heading(get_string('serviceuserssettings', 'webservice'), 3, 'main');
    echo $renderer->admin_authorised_user_list($allowedusers, $id);
}

echo $OUTPUT->footer();

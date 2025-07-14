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
 * Web services user settings UI
 *
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/webservice/lib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/webservice/forms.php');

$serviceid = required_param('serviceid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);

admin_externalpage_setup('externalserviceusersettings');

//define nav bar
$PAGE->set_url('/admin/webservice/service_user_settings.php', ['serviceid' => $serviceid, 'userid'  => $userid]);
$node = $PAGE->settingsnav->find('externalservices', navigation_node::TYPE_SETTING);
if ($node) {
    $node->make_active();
}

$returnurl = new moodle_url('/admin/webservice/service_users.php', ['id' => $serviceid]);
$PAGE->navbar->add(get_string('serviceusers', 'webservice'), $returnurl);
$PAGE->navbar->add(get_string('serviceusersettings', 'webservice'));

$formaction = new moodle_url('', array('id' => $serviceid, 'userid' => $userid));

$webservicemanager = new webservice();
$serviceuser = $webservicemanager->get_ws_authorised_user($serviceid, $userid);
$usersettingsform = new external_service_authorised_user_settings_form($formaction, $serviceuser);
$settingsformdata = $usersettingsform->get_data();

if ($usersettingsform->is_cancelled()) {
    redirect($returnurl);

} else if (!empty($settingsformdata) and confirm_sesskey()) {
    /// save user settings (administrator clicked on update button)
    $settingsformdata = (object)$settingsformdata;

    $serviceuserinfo = new stdClass();
    $serviceuserinfo->id = $serviceuser->serviceuserid;
    $serviceuserinfo->iprestriction = $settingsformdata->iprestriction;
    $serviceuserinfo->validuntil = $settingsformdata->validuntil;

    $webservicemanager->update_ws_authorised_user($serviceuserinfo);

    //TODO: assign capability

    //display successful notification
    $notification = $OUTPUT->notification(get_string('usersettingssaved', 'webservice'), 'notifysuccess');
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('serviceusersettings', 'webservice'), 3, 'main');
if (!empty($notification)) {
    echo $notification;
}
$usersettingsform->display();

echo $OUTPUT->footer();

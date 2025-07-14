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
 * LTI 1.3 page to create or delete deployments.
 *
 * This page is only used by LTI 1.3. Older versions do not require platforms to be registered with the tool during
 * registration.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\output\notification;
use enrol_lti\local\ltiadvantage\form\deployment_form;
use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\context_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use enrol_lti\local\ltiadvantage\repository\resource_link_repository;
use enrol_lti\local\ltiadvantage\repository\user_repository;
use enrol_lti\local\ltiadvantage\service\tool_deployment_service;

require_once(__DIR__ . '/../../config.php');
global $CFG, $OUTPUT;
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/enrol/lti/lib.php');

$action = required_param('action', PARAM_ALPHA);
$registrationid = required_param('registrationid', PARAM_INT);
if (!in_array($action, ['add', 'delete'])) {
    throw new coding_exception("Invalid action param '$action'");
}

// The page to go back to when the respective action has been performed.
$deploymentslisturl = new moodle_url($CFG->wwwroot . "/enrol/lti/register_platform.php",
    ['regid' => $registrationid, 'action' => 'view', 'tabselect' => 'tooldeployments']);

// Local anon helper to extend the nav for this page and call admin_externalpage_setup.
$pagesetup = function(string $pagetitle) {
    global $PAGE;
    navigation_node::override_active_url(
        new moodle_url('/admin/settings.php', ['section' => 'enrolsettingslti_registrations'])
    );
    admin_externalpage_setup('enrolsettingslti_deployment_manage', '', null, '', ['pagelayout' => 'admin']);
    $PAGE->navbar->add($pagetitle);
};

// Local anon helper to map the formdata to the dto required for the domain layer.
$maptodto = function($formdata): stdClass {
    return (object) [
        'registration_id' => $formdata->registrationid,
        'deployment_name' => $formdata->name,
        'deployment_id' => $formdata->deploymentid,
    ];
};

if ($action === 'add') {
    $pagesetup(get_string('deploymentadd', 'enrol_lti'));

    $pageurl = new moodle_url('/enrol/lti/manage_deployment.php', ['action' => 'add']);
    $mform = new deployment_form($pageurl->out(false));
    if ($data = $mform->get_data()) {
        $deploymentservice = new tool_deployment_service(new application_registration_repository(),
            new deployment_repository(), new resource_link_repository(), new context_repository(),
            new user_repository());
        $deploymentservice->add_tool_deployment($maptodto($data));
        redirect($deploymentslisturl, get_string('deploymentaddnotice', 'enrol_lti'), null,
            notification::NOTIFY_SUCCESS);
    } else if (!$mform->is_cancelled()) {

        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('deploymentadd', 'enrol_lti'));
        $mform->set_data([
            'registrationid' => $registrationid
        ]);
        $mform->display();
        echo $OUTPUT->footer();
        die();
    }
    redirect($deploymentslisturl);

} else if ($action === 'delete') {
    $id = required_param('id', PARAM_INT);
    $pagesetup(get_string('deploymentdelete', 'enrol_lti'));

    if (!optional_param('confirm', false, PARAM_BOOL)) {
        $continueparams = [
            'action' => 'delete',
            'id' => $id,
            'registrationid' => $registrationid,
            'sesskey' => sesskey(),
            'confirm' => true
        ];
        $continueurl = new moodle_url('/enrol/lti/manage_deployment.php', $continueparams);
        $deploymentrepo = new deployment_repository();
        $deployment = $deploymentrepo->find($id);
        if (!$deployment) {
            throw new coding_exception("Cannot delete non existent deployment '{$id}'.");
        }

        echo $OUTPUT->header();
        echo $OUTPUT->confirm(
            get_string('deploymentdeleteconfirm', 'enrol_lti', format_string($deployment->get_deploymentid())),
            $continueurl,
            $deploymentslisturl
        );
        echo $OUTPUT->footer();
    } else {
        require_sesskey();
        $deploymentservice = new tool_deployment_service(new application_registration_repository(),
            new deployment_repository(), new resource_link_repository(), new context_repository(),
            new user_repository());
        $deploymentservice->delete_tool_deployment($id);

        redirect($deploymentslisturl,
            get_string('deploymentdeletenotice', 'enrol_lti'), null,  notification::NOTIFY_SUCCESS);
    }
}

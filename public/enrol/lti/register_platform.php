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
 * LTI 1.3 page to create or edit a platform registration.
 *
 * This page is only used by LTI 1.3. Older versions do not require platforms to be registered with the tool during
 * registration.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\output\notification;
use enrol_lti\local\ltiadvantage\form\create_registration_form;
use enrol_lti\local\ltiadvantage\form\platform_registration_form;
use enrol_lti\local\ltiadvantage\entity\application_registration;
use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\context_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use enrol_lti\local\ltiadvantage\repository\resource_link_repository;
use enrol_lti\local\ltiadvantage\repository\user_repository;
use enrol_lti\local\ltiadvantage\service\application_registration_service;

require_once(__DIR__ . '/../../config.php');
global $CFG, $OUTPUT, $DB;
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/enrol/lti/lib.php');

$action = required_param('action', PARAM_ALPHA);
if (!in_array($action, ['add', 'view', 'edit', 'delete'])) {
    throw new coding_exception("Invalid action param '$action'");
}

// The page to go back to when the respective action has been performed.
$toolregistrationurl = new moodle_url($CFG->wwwroot . "/" . $CFG->admin . "/settings.php",
    ['section' => 'enrolsettingslti_registrations']);

// Local anon helper to extend the nav for this page and call admin_externalpage_setup.
$pagesetup = function(string $pagetitle) {
    global $PAGE;
    navigation_node::override_active_url(
        new moodle_url('/admin/settings.php', ['section' => 'enrolsettingslti_registrations'])
    );
    admin_externalpage_setup('enrolsettingslti_registrations_edit', '', null, '', ['pagelayout' => 'admin']);
    $PAGE->navbar->add($pagetitle);
};

if ($action == 'view') {
    $regid = required_param('regid', PARAM_INT);
    $tabselect = optional_param('tabselect', 'platformdetails', PARAM_ALPHA);
    global $PAGE;
    $pagesetup(get_string('registerplatformedit', 'enrol_lti'));
    $pageurl = new moodle_url('/enrol/lti/register_platform.php', ['action' => 'view', 'regid' => $regid]);

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('registerplatformedit', 'enrol_lti'));

    $renderer = $PAGE->get_renderer('enrol_lti');
    echo $renderer->render_registration_view($regid, $tabselect);
    echo $OUTPUT->footer();
    die();

} else if ($action === 'add') {
    $pagesetup(get_string('registerplatformadd', 'enrol_lti'));
    $pageurl = new moodle_url('/enrol/lti/register_platform.php', ['action' => 'add']);

    $mform = new create_registration_form($pageurl->out(false));
    if ($data = $mform->get_data()) {
        // Create the incomplete registration.
        $regservice = new application_registration_service(new application_registration_repository(),
            new deployment_repository(), new resource_link_repository(), new context_repository(),
            new user_repository());
        $draft = $regservice->create_draft_application_registration($data);

        // Redirect to the registration view, which will display endpoints and allow the user to complete the registration.
        redirect(new moodle_url('/enrol/lti/register_platform.php',
            ['action' => 'view', 'regid' => $draft->get_id(), 'tabselect' => 'tooldetails']));

    } else if (!$mform->is_cancelled()) {
        // Display the first step of registration creation.
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('registerplatformadd', 'enrol_lti'));
        $mform->display();
        echo $OUTPUT->footer();
        die();
    }
    redirect($toolregistrationurl);
} else if ($action === 'edit') {
    $regid = required_param('regid', PARAM_INT);
    $pagesetup(get_string('registerplatformedit', 'enrol_lti'));

    $pageurl = new moodle_url('/enrol/lti/register_platform.php', ['action' => 'edit', 'regid' => $regid]);
    $viewurl = new moodle_url('/enrol/lti/register_platform.php', ['action' => 'view', 'regid' => $regid]);

    $mform = new platform_registration_form($pageurl->out(false));
    if (($data = $mform->get_data()) && confirm_sesskey()) {
        $regservice = new application_registration_service(new application_registration_repository(),
            new deployment_repository(), new resource_link_repository(), new context_repository(),
            new user_repository());
        $regservice->update_application_registration($data);
        redirect($viewurl, get_string('registerplatformeditnotice', 'enrol_lti'), null,
            notification::NOTIFY_SUCCESS);
    } else if (!$mform->is_cancelled()) {
        // Anon helper to transform data.
        $maptoformdata = function(application_registration $registration): \stdClass {
            return (object) [
                'id' => $registration->get_id(),
                'name' => $registration->get_name(),
                'platformid' => $registration->get_platformid(),
                'clientid' => $registration->get_clientid(),
                'authenticationrequesturl' => $registration->get_authenticationrequesturl(),
                'jwksurl' => $registration->get_jwksurl(),
                'accesstokenurl' => $registration->get_accesstokenurl()
            ];
        };
        $appregistrationrepo = new application_registration_repository();
        $registration = $appregistrationrepo->find($regid);
        if (!$registration) {
            throw new coding_exception("cannot edit non-existent registration '{$regid}'.");
        }

        $mform->set_data($maptoformdata($registration));

        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('registerplatformedit', 'enrol_lti'));
        $mform->display();
        echo $OUTPUT->footer();
        die();
    }
    redirect($viewurl);
} else if ($action === 'delete') {
    $regid = required_param('regid', PARAM_INT);
    $pagesetup(get_string('registerplatformdelete', 'enrol_lti'));

    if (!optional_param('confirm', false, PARAM_BOOL)) {
        $continueparams = ['action' => 'delete', 'regid' => $regid, 'sesskey' => sesskey(), 'confirm' => true];
        $continueurl = new moodle_url('/enrol/lti/register_platform.php', $continueparams);
        $appregrepo = new application_registration_repository();
        $appreg = $appregrepo->find($regid);
        if (!$appreg) {
            throw new coding_exception("Cannot delete non existent application registration '{$regid}'.");
        }

        echo $OUTPUT->header();
        echo $OUTPUT->confirm(
            get_string('registerplatformdeleteconfirm', 'enrol_lti', format_string($appreg->get_name())),
            $continueurl,
            $toolregistrationurl
        );
        echo $OUTPUT->footer();
    } else {
        require_sesskey();
        $regservice = new application_registration_service(new application_registration_repository(),
            new deployment_repository(), new resource_link_repository(), new context_repository(),
            new user_repository());
        $regservice->delete_application_registration($regid);

        redirect($toolregistrationurl,
            get_string('registerplatformdeletenotice', 'enrol_lti'), null,  notification::NOTIFY_SUCCESS);
    }
}

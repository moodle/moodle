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
 * Handles LTI 1.3 resource link launches.
 *
 * See enrol/lti/launch_deeplink.php for deep linking launches.
 *
 * There are 2 pathways through this page:
 * 1. When first making a resource linking launch from the platform. The launch data is cached at this point, pending user
 * authentication, and the page is set such that the post-authentication redirect will return here.
 * 2. The post-authentication redirect. The launch data is fetched from the session launch cache, and the resource is displayed.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use enrol_lti\local\ltiadvantage\lib\http_client;
use enrol_lti\local\ltiadvantage\lib\issuer_database;
use enrol_lti\local\ltiadvantage\lib\launch_cache_session;
use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\context_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use enrol_lti\local\ltiadvantage\repository\legacy_consumer_repository;
use enrol_lti\local\ltiadvantage\repository\resource_link_repository;
use enrol_lti\local\ltiadvantage\repository\user_repository;
use enrol_lti\local\ltiadvantage\service\tool_launch_service;
use enrol_lti\local\ltiadvantage\utility\message_helper;
use Packback\Lti1p3\ImsStorage\ImsCookie;
use Packback\Lti1p3\LtiMessageLaunch;
use Packback\Lti1p3\LtiServiceConnector;

require_once(__DIR__ . '/../../config.php');
global $CFG;
require_once($CFG->libdir . '/filelib.php');

$idtoken = optional_param('id_token', null, PARAM_RAW);
$launchid = optional_param('launchid', null, PARAM_RAW);

if (!is_enabled_auth('lti')) {
    throw new moodle_exception('pluginnotenabled', 'auth', '', get_string('pluginname', 'auth_lti'));
}
if (!enrol_is_enabled('lti')) {
    throw new moodle_exception('enrolisdisabled', 'enrol_lti');
}
if (empty($idtoken) && empty($launchid)) {
    throw new coding_exception('Error: launch requires id_token');
}

// Support caching the launch and retrieving it after the account binding process described in auth::complete_login().
$sesscache = new launch_cache_session();
$issdb = new issuer_database(new application_registration_repository(), new deployment_repository());
$cookie = new ImsCookie();
$serviceconnector = new LtiServiceConnector($sesscache, new http_client(new curl()));
if ($idtoken) {
    $messagelaunch = LtiMessageLaunch::new($issdb, $sesscache, $cookie, $serviceconnector)
        ->validate();
}
if ($launchid) {
    $messagelaunch = LtiMessageLaunch::fromCache($launchid, $issdb, $sesscache, $serviceconnector);
}
if (empty($messagelaunch)) {
    throw new moodle_exception('Bad launch. Message launch data could not be found');
}

// Authenticate the platform user, which could be an instructor, an admin or a learner.
// Auth code needs to be told about consumer secrets for the purposes of migration, since these reside in enrol_lti.
$launchdata = $messagelaunch->getLaunchData();
if (!empty($launchdata['https://purl.imsglobal.org/spec/lti/claim/lti1p1']['oauth_consumer_key'])) {
    $legacyconsumerrepo = new legacy_consumer_repository();
    $legacyconsumersecrets = $legacyconsumerrepo->get_consumer_secrets(
        $launchdata['https://purl.imsglobal.org/spec/lti/claim/lti1p1']['oauth_consumer_key']
    );
}

// To authenticate, we need the resource's account provisioning mode for the given LTI role.
if (empty($launchdata['https://purl.imsglobal.org/spec/lti/claim/custom']['id'])) {
    throw new \moodle_exception('ltiadvlauncherror:missingid', 'enrol_lti');
}
$resourceuuid = $launchdata['https://purl.imsglobal.org/spec/lti/claim/custom']['id'];
$resource = array_values(\enrol_lti\helper::get_lti_tools(['uuid' => $resourceuuid]));
$resource = $resource[0] ?? null;
if (empty($resource) || $resource->status != ENROL_INSTANCE_ENABLED) {
    throw new \moodle_exception('ltiadvlauncherror:invalidid', 'enrol_lti', '', $resourceuuid);
}

$provisioningmode = message_helper::is_instructor_launch($launchdata) ? $resource->provisioningmodeinstructor
    : $resource->provisioningmodelearner;
$auth = get_auth_plugin('lti');
$auth->complete_login(
    $messagelaunch->getLaunchData(),
    new moodle_url('/enrol/lti/launch.php', ['launchid' => $messagelaunch->getLaunchId()]),
    $provisioningmode,
    $legacyconsumersecrets ?? []
);

require_login(null, false);
global $USER, $CFG, $PAGE;
$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/enrol/lti/launch.php'));
$PAGE->set_pagelayout('popup'); // Same layout as the tool.php page in Legacy 1.1/2.0 launches.
$PAGE->set_title(get_string('opentool', 'enrol_lti'));

$toollaunchservice = new tool_launch_service(
    new deployment_repository(),
    new application_registration_repository(),
    new resource_link_repository(),
    new user_repository(),
    new context_repository()
);
[$userid, $resource] = $toollaunchservice->user_launches_tool($USER, $messagelaunch);

$context = context::instance_by_id($resource->contextid);
if ($context->contextlevel == CONTEXT_COURSE) {
    $courseid = $context->instanceid;
    $redirecturl = new moodle_url('/course/view.php', ['id' => $courseid]);
} else if ($context->contextlevel == CONTEXT_MODULE) {
    $cm = get_coursemodule_from_id(false, $context->instanceid, 0, false, MUST_EXIST);
    $redirecturl = new moodle_url('/mod/' . $cm->modname . '/view.php', ['id' => $cm->id]);
} else {
    throw new moodle_exception('invalidcontext');
}

if (empty($CFG->allowframembedding)) {
    $stropentool = get_string('opentool', 'enrol_lti');
    echo html_writer::tag('p', get_string('frameembeddingnotenabled', 'enrol_lti'));
    echo html_writer::link($redirecturl, $stropentool, ['target' => '_blank']);
} else {
    redirect($redirecturl);
}

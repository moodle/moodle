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
 * Handles LTI 1.3 deep linking launches.
 *
 * There are 2 pathways through this page:
 * 1. When first making a deep linking launch from the platform. The launch data is cached at this point, pending user
 * authentication, and the page is set such that the post-authentication redirect will return here.
 * 2. The post-authentication redirect. The launch data is fetched from the session launch cache, and the resource
 * selection view is rendered.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use enrol_lti\local\ltiadvantage\lib\http_client;
use enrol_lti\local\ltiadvantage\lib\issuer_database;
use enrol_lti\local\ltiadvantage\lib\launch_cache_session;
use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use enrol_lti\local\ltiadvantage\repository\published_resource_repository;
use Packback\Lti1p3\ImsStorage\ImsCookie;
use Packback\Lti1p3\LtiMessageLaunch;
use Packback\Lti1p3\LtiServiceConnector;

require_once(__DIR__ . '/../../config.php');
global $OUTPUT, $PAGE, $CFG;
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

// First launch from the platform: get launch data and cache it in case the user's not authenticated.
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
    throw new moodle_exception('Bad launch. Deep linking launch data could not be found');
}

// Authenticate the instructor.
// Deep linking cannot use resource-specific provisioning modes, so it just uses a sensible 'existing accounts only' mode.
$auth = get_auth_plugin('lti');
$auth->complete_login(
    $messagelaunch->getLaunchData(),
    new moodle_url('/enrol/lti/launch_deeplink.php', ['launchid' => $messagelaunch->getLaunchId()]),
    auth_plugin_lti::PROVISIONING_MODE_PROMPT_EXISTING_ONLY
);

require_login(null, false);
global $USER, $CFG;
$PAGE->set_context(context_system::instance());
$url = new moodle_url('/enrol/lti/launch_deeplink.php');
$PAGE->set_url($url);
$PAGE->set_pagelayout('popup');
$PAGE->set_title(get_string('opentool', 'enrol_lti'));

// Get all the published_resource view objects and render them for selection.
global $USER;
$resourcerepo = new published_resource_repository();
$resources = $resourcerepo->find_all_for_user($USER->id);
$renderer = $PAGE->get_renderer('enrol_lti');

echo $OUTPUT->header();
echo $renderer->render_published_resource_selection_view($messagelaunch, $resources);
echo $OUTPUT->footer();

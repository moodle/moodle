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
 * Returns the deep link resource via a POST to the platform.
 *
 * @package     enrol_lti
 * @copyright   2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\http_client;
use enrol_lti\local\ltiadvantage\lib\lti_cookie;
use enrol_lti\local\ltiadvantage\lib\launch_cache_session;
use enrol_lti\local\ltiadvantage\lib\issuer_database;
use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use enrol_lti\local\ltiadvantage\repository\published_resource_repository;
use Packback\Lti1p3\DeepLinkResources\Resource;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiLineitem;
use Packback\Lti1p3\LtiMessageLaunch;
use Packback\Lti1p3\LtiServiceConnector;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
global $CFG, $DB, $PAGE, $USER;
require_once($CFG->libdir . '/filelib.php');
require_login(null, false);

confirm_sesskey();
$launchid = required_param('launchid', PARAM_TEXT);
$modules = optional_param_array('modules', [], PARAM_INT);
$grades = optional_param_array('grades', [], PARAM_INT);

$sesscache = new launch_cache_session();
$issdb = new issuer_database(new application_registration_repository(), new deployment_repository());
$cookie = new lti_cookie();
$serviceconnector = new LtiServiceConnector($sesscache, new http_client());
$messagelaunch = LtiMessageLaunch::fromCache($launchid, $issdb, $sesscache, $cookie, $serviceconnector);

if (!$messagelaunch->isDeepLinkLaunch()) {
    throw new coding_exception('Configuration can only be accessed as part of a content item selection deep link '.
        'launch.');
}
$sesscache->purge();

// Get the selected resources and create the resource link content items to post back.
$resourcerepo = new published_resource_repository();
$resources = $resourcerepo->find_all_by_ids_for_user($modules, $USER->id);

$contentitems = [];
foreach ($resources as $resource) {

    $contentitem = Resource::new()
        ->setUrl($CFG->wwwroot . '/enrol/lti/launch.php')
        ->setCustomParams(['id' => $resource->get_uuid()])
        ->setTitle($resource->get_name());

    // If the activity supports grading, and the user has selected it, then include line item information.
    if ($resource->supports_grades() && in_array($resource->get_id(), $grades)) {
        require_once($CFG->libdir . '/gradelib.php');

        $lineitem = LtiLineitem::new()
            ->setScoreMaximum($resource->get_grademax())
            ->setResourceId($resource->get_uuid());

        $contentitem->setLineitem($lineitem);
    }

    $contentitems[] = $contentitem;
}


global $USER, $CFG, $OUTPUT;
$PAGE->set_context(context_system::instance());
$url = new moodle_url('/enrol/lti/configure.php');
$PAGE->set_url($url);
$PAGE->set_pagelayout('popup');
echo $OUTPUT->header();
$dl = $messagelaunch->getDeepLink();

$formactionurl = $messagelaunch->getLaunchData()[LtiConstants::DL_DEEP_LINK_SETTINGS]['deep_link_return_url'];
echo <<<HTML
<form id="auto_submit" action="{$formactionurl}" method="POST">
    <input type="hidden" name="JWT" value="{$messagelaunch->getDeepLink()->getResponseJwt($contentitems)}" />
    <input type="submit" name="Go" />
</form>
<script>document.getElementById('auto_submit').submit();</script>
HTML;


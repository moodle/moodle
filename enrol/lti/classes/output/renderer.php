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
 * Renderer class for LTI enrolment
 *
 * @package    enrol_lti
 * @copyright  2016 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_lti\output;

defined('MOODLE_INTERNAL') || die();

use core\output\notification;
use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use Packback\Lti1p3\LtiMessageLaunch;
use plugin_renderer_base;

/**
 * Renderer class for LTI enrolment
 *
 * @package    enrol_lti
 * @copyright  2016 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {
    /**
     * Render the enrol_lti/proxy_registration template
     *
     * @param registration $registration The registration renderable
     * @return string html for the page
     */
    public function render_registration(registration $registration) {
        $data = $registration->export_for_template($this);
        return parent::render_from_template("enrol_lti/proxy_registration", $data);
    }

    /**
     * Render the content item selection (deep linking 2.0) view
     *
     * This view is a form containing a list of courses and modules which, once selected and submitted, will result in
     * a list of LTI Resource Link Content Items being sent back to the platform, allowing resource link creation to
     * take place.
     *
     * @param LtiMessageLaunch $launch the launch data.
     * @param array $resources array of published resources available to the current user.
     * @return string html
     */
    public function render_published_resource_selection_view(LtiMessageLaunch $launch, array $resources): string {
        global $CFG;
        $context = [
            'action' => $CFG->wwwroot . '/enrol/lti/configure.php',
            'launchid' => $launch->getLaunchId(),
            'hascontent' => !empty($resources),
            'sesskey' => sesskey(),
            'courses' => []
        ];
        foreach ($resources as $resource) {
            $context['courses'][$resource->get_courseid()]['fullname'] = $resource->get_coursefullname();
            if (!$resource->is_course()) {
                $context['courses'][$resource->get_courseid()]['modules'][] = [
                    'name' => $resource->get_name(),
                    'id' => $resource->get_id(),
                    'lineitem' => $resource->supports_grades()
                ];
                if (empty($context['courses'][$resource->get_courseid()]['shared_course'])) {
                    $context['courses'][$resource->get_courseid()]['shared_course'] = false;
                }
            } else {
                $context['courses'][$resource->get_courseid()]['shared_course'] = $resource->is_course();
                $context['courses'][$resource->get_courseid()]['id'] = $resource->get_id();
                $context['courses'][$resource->get_courseid()]['lineitem'] = $resource->supports_grades();
            }
        }
        $context['courses'] = array_values($context['courses']); // Reset keys for use in the template.
        return parent::render_from_template('enrol_lti/local/ltiadvantage/content_select', $context);
    }

    /**
     * Render the table applications which have been registered as LTI Advantage platforms.
     *
     * @param array $registrations The list of registrations to render.
     * @return string the html.
     */
    public function render_admin_setting_registered_platforms(array $registrations): string {
        $registrationscontext = [
            'registrations' => [],
            'addurl' => (new \moodle_url('/enrol/lti/register_platform.php', ['action' => 'add']))->out(false),
        ];
        $registrationscontext['hasregs'] = count($registrations) > 0;

        $deploymentrepository = new deployment_repository();
        foreach ($registrations as $reg) {
            $countdeployments = $deploymentrepository->count_by_registration($reg->get_id());
            $status = get_string('registrationstatuspending', 'enrol_lti');
            if ($reg->is_complete()) {
                $status = get_string('registrationstatusactive', 'enrol_lti');
            }
            $registrationscontext['registrations'][] = [
                'name' => $reg->get_name(),
                'issuer' => $reg->get_platformid(),
                'clientid' => $reg->get_clientid(),
                'hasdeployments' => $countdeployments > 0,
                'countdeployments' => $countdeployments,
                'isactive' => $reg->is_complete(),
                'statusstring' => $status,
                'tooldetailsurl' => (new \moodle_url('/enrol/lti/register_platform.php',
                    ['action' => 'view', 'regid' => $reg->get_id(), 'tabselect' => 'tooldetails']))->out(false),
                'platformdetailsurl' => (new \moodle_url('/enrol/lti/register_platform.php',
                    ['action' => 'view', 'regid' => $reg->get_id(), 'tabselect' => 'platformdetails']))->out(false),
                'deploymentsurl' => (new \moodle_url('/enrol/lti/register_platform.php',
                    ['action' => 'view', 'regid' => $reg->get_id(), 'tabselect' => 'tooldeployments']))->out(false),
                'deleteurl' => (new \moodle_url('/enrol/lti/register_platform.php',
                    ['action' => 'delete', 'regid' => $reg->get_id()]))->out(false)
            ];
        }

        // Notice to let users know this is LTI Advantage ONLY.
        $versionnotice = new notification(
            get_string('registeredplatformsltiversionnotice', 'enrol_lti'),
            notification::NOTIFY_INFO
        );
        $versionnotice->set_show_closebutton(false);

        $return = parent::render($versionnotice);
        $return .= parent::render_from_template('enrol_lti/local/ltiadvantage/registered_platforms',
            $registrationscontext);
        return $return;
    }

    /**
     * Renders the registration view page, allowing admins to view tool details, platform details and deployments.
     *
     * The template uses dynamic tabs, which renders with one active tab and uses js to change tabs if desired. E.g. if an anchor
     * link is used to go to another tab, the page will first load the active tab, then switch to the tab referenced in the anchor
     * using JS. To allow navigation to the page with a specific tab selected, and WITHOUT the js slowdown, this renderer method
     * allows callers to specify which tab is set as the active tab during first render.
     * Valid values correspond to the tab names in the enrol_lti/local/ltiadvantage/registration_view template, currently:
     * - 'tooldetails' - to render with the Tool details tab as the active tab
     * - 'platformdetails' - to render with the Platform details tab as the active tab
     * - 'tooldeployments' - to render with the Tool deployments tab as the active tab
     * By default, the platformdetails tab will be selected as active.
     *
     * @param int $registrationid the id of the registration to display information for.
     * @param string $activetab a string identifying the tab to preselect when rendering.
     * @return bool|string
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function render_registration_view(int $registrationid, string $activetab = '') {
        global $CFG;
        $validtabvals = ['tooldetails', 'platformdetails', 'tooldeployments'];
        $activetab = !empty($activetab) && in_array($activetab, $validtabvals) ? $activetab : 'platformdetails';
        $regrepo = new application_registration_repository();
        $registration = $regrepo->find($registrationid);

        $deploymentrepo = new deployment_repository();
        $deployments = $deploymentrepo->find_all_by_registration($registration->get_id());
        $deploymentscontext = [];
        foreach ($deployments as $deployment) {
            $deploymentscontext[] = [
                'name' => $deployment->get_deploymentname(),
                'deploymentid' => $deployment->get_deploymentid(),
                'deleteurl' => (new \moodle_url(
                    '/enrol/lti/manage_deployment.php',
                    ['action' => 'delete', 'id' => $deployment->get_id(), 'registrationid' => $registration->get_id()]
                ))->out(false)
            ];
        }

        $regurl = new \moodle_url('/enrol/lti/register.php', ['token' => $registration->get_uniqueid()]);

        $tcontext = [
            'tool_details_active' => $activetab == 'tooldetails',
            'platform_details_active' => $activetab == 'platformdetails',
            'tool_deployments_active' => $activetab == 'tooldeployments',
            'back_url' => (new \moodle_url('/admin/settings.php', ['section' => 'enrolsettingslti_registrations']))->out(false),
            'dynamic_registration_info' => get_string(
                'registrationurlinfomessage',
                'enrol_lti',
                get_docs_url('Publish_as_LTI_tool')
            ),
            'dynamic_registration_url' => [
                'name' => get_string('registrationurl', 'enrol_lti'),
                'url' => $regurl,
                'id' => uniqid()
            ],
            'manual_registration_info' => get_string('endpointltiversionnotice', 'enrol_lti'),
            'manual_registration_urls' => [
                [
                    'name' => get_string('toolurl', 'enrol_lti'),
                    'url' => $CFG->wwwroot . '/enrol/lti/launch.php',
                    'id' => uniqid()
                ],
                [
                    'name' => get_string('loginurl', 'enrol_lti'),
                    'url' => $CFG->wwwroot . '/enrol/lti/login.php?id=' . $registration->get_uniqueid(),
                    'id' => uniqid()
                ],
                [
                    'name' => get_string('jwksurl', 'enrol_lti'),
                    'url' => $CFG->wwwroot . '/enrol/lti/jwks.php',
                    'id' => uniqid()
                ],
                [
                    'name' => get_string('deeplinkingurl', 'enrol_lti'),
                    'url' => $CFG->wwwroot . '/enrol/lti/launch_deeplink.php',
                    'id' => uniqid()
                ],
            ],
            'platform_details_info' => get_string('platformdetailsinfo', 'enrol_lti'),
            'platform_details' => [
                [
                    'name' => get_string('registerplatform:name', 'enrol_lti'),
                    'value' => $registration->get_name()
                ],
                [
                    'name' => get_string('registerplatform:platformid', 'enrol_lti'),
                    'value' => $registration->get_platformid() ?? '',
                ],
                [
                    'name' => get_string('registerplatform:clientid', 'enrol_lti'),
                    'value' => $registration->get_clientid() ?? '',
                ],
                [
                    'name' => get_string('registerplatform:authrequesturl', 'enrol_lti'),
                    'value' => $registration->get_authenticationrequesturl() ?? '',
                ],
                [
                    'name' => get_string('registerplatform:jwksurl', 'enrol_lti'),
                    'value' => $registration->get_jwksurl() ?? '',
                ],
                [
                    'name' => get_string('registerplatform:accesstokenurl', 'enrol_lti'),
                    'value' => $registration->get_accesstokenurl() ?? '',
                ]
            ],
            'edit_platform_details_url' => (new \moodle_url('/enrol/lti/register_platform.php',
                ['action' => 'edit', 'regid' => $registration->get_id()]))->out(false),
            'deployments_info' => get_string('deploymentsinfo', 'enrol_lti'),
            'has_deployments' => !empty($deploymentscontext),
            'tool_deployments' => $deploymentscontext,
            'add_deployment_url' => (new \moodle_url('/enrol/lti/manage_deployment.php',
                ['action' => 'add', 'registrationid' => $registrationid]))->out(false)
        ];

        return parent::render_from_template('enrol_lti/local/ltiadvantage/registration_view',
            $tcontext);
    }

    /**
     * Render a warning, indicating to the user that cookies are require but couldn't be set.
     *
     * @return string the html.
     */
    public function render_cookies_required_notice(): string {
        $notification = new notification(get_string('cookiesarerequiredinfo', 'enrol_lti'), notification::NOTIFY_WARNING, false);
        $tcontext = [
            'heading' => get_string('cookiesarerequired', 'enrol_lti'),
            'notification' => $notification->export_for_template($this),
        ];

        return parent::render_from_template('enrol_lti/local/ltiadvantage/cookies_required_notice', $tcontext);
    }
}

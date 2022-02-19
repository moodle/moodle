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
     * Render the table of tool endpoints as part of admin_setting_toolendpoints.
     *
     * @param array $endpoints the endpoints array (see admin_setting_toolendpoints for details).
     * @return string the html.
     */
    public function render_admin_setting_tool_endpoints(array $endpoints): string {
        $return = parent::render_from_template('enrol_lti/local/ltiadvantage/tool_endpoints', $endpoints);
        return $return;
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
            $registrationscontext['registrations'][] = [
                'name' => $reg->get_name(),
                'issuer' => $reg->get_platformid(),
                'clientid' => $reg->get_clientid(),
                'hasdeployments' => $countdeployments > 0,
                'countdeployments' => $countdeployments,
                'editurl' => (new \moodle_url('/enrol/lti/register_platform.php',
                    ['action' => 'edit', 'regid' => $reg->get_id()]))->out(false),
                'deleteurl' => (new \moodle_url('/enrol/lti/register_platform.php',
                    ['action' => 'delete', 'regid' => $reg->get_id()]))->out(false),
                'deploymentsurl' => (new \moodle_url('/enrol/lti/app_tool_deployments.php',
                    ['registrationid' => $reg->get_id()]))->out(false)
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
     * Render the table of registered tool deployments for a given application registration.
     *
     * @param int $registrationid the application registration id.
     * @param array $deployments the list of deployments.
     * @return string the html.
     */
    public function render_registered_tool_deployments(int $registrationid, array $deployments): string {

        $deploymentscontext = [
            'deployments' => [],
            'hasdeployments' => !empty($deployments),
            'addurl' => (new \moodle_url('/enrol/lti/manage_deployment.php',
                ['action' => 'add', 'registrationid' => $registrationid]))->out(false),
            'backurl' => (new \moodle_url('/admin/settings.php',
                ['section' => 'enrolsettingslti_registrations']))->out(false),
        ];
        foreach ($deployments as $deployment) {
            $deploymentscontext['deployments'][] = [
                'name' => $deployment->get_deploymentname(),
                'deploymentid' => $deployment->get_deploymentid(),
                'deleteurl' => (new \moodle_url(
                    '/enrol/lti/manage_deployment.php',
                    ['action' => 'delete', 'id' => $deployment->get_id(), 'registrationid' => $registrationid]
                ))->out(false),
            ];
        }
        return parent::render_from_template('enrol_lti/local/ltiadvantage/registered_deployments',
            $deploymentscontext);
    }
}

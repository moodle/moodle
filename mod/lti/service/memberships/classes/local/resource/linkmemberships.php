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
 * This file contains a class definition for the Link Memberships resource
 *
 * @package    ltiservice_memberships
 * @copyright  2015 Vital Source Technologies http://vitalsource.com
 * @author     Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace ltiservice_memberships\local\resource;

use \mod_lti\local\ltiservice\service_base;
use ltiservice_memberships\local\service\memberships;
use core_availability\info;
use core_availability\info_module;

defined('MOODLE_INTERNAL') || die();

/**
 * A resource implementing Link Memberships.
 *
 * @package    ltiservice_memberships
 * @since      Moodle 3.0
 * @copyright  2015 Vital Source Technologies http://vitalsource.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class linkmemberships extends \mod_lti\local\ltiservice\resource_base {

    /**
     * Class constructor.
     *
     * @param ltiservice_memberships\local\service\memberships $service Service instance
     */
    public function __construct($service) {

        parent::__construct($service);
        $this->id = 'LtiLinkMemberships';
        $this->template = '/links/{link_id}/memberships';
        $this->variables[] = 'LtiLink.memberships.url';
        $this->formats[] = 'application/vnd.ims.lis.v2.membershipcontainer+json';
        $this->methods[] = 'GET';

    }

    /**
     * Execute the request for this resource.
     *
     * @param mod_lti\local\ltiservice\response $response  Response object for this request.
     */
    public function execute($response) {
        global $CFG, $DB;

        $params = $this->parse_template();
        $linkid = $params['link_id'];
        $role = optional_param('role', '', PARAM_TEXT);
        $limitnum = optional_param('limit', 0, PARAM_INT);
        $limitfrom = optional_param('from', 0, PARAM_INT);
        if ($limitnum <= 0) {
            $limitfrom = 0;
        }

        try {
            if (empty($linkid)) {
                throw new \Exception(null, 404);
            }
            if (!($lti = $DB->get_record('lti', array('id' => $linkid), 'id,course,typeid,servicesalt', IGNORE_MISSING))) {
                throw new \Exception(null, 404);
            }
            $tool = $DB->get_record('lti_types', array('id' => $lti->typeid));
            $toolproxy = $DB->get_record('lti_tool_proxies', array('id' => $tool->toolproxyid));
            if (!$this->check_tool_proxy($toolproxy->guid, $response->get_request_data())) {
                throw new \Exception(null, 401);
            }
            if (!($course = $DB->get_record('course', array('id' => $lti->course), 'id', IGNORE_MISSING))) {
                throw new \Exception(null, 404);
            }
            if (!($context = \context_course::instance($lti->course))) {
                throw new \Exception(null, 404);
            }
            $modinfo = get_fast_modinfo($course);
            $cm = get_coursemodule_from_instance('lti', $linkid, $lti->course, false, MUST_EXIST);
            $cm = $modinfo->get_cm($cm->id);
            $info = new info_module($cm);
            if ($info->is_available_for_all()) {
                $info = null;
            }

            $json = memberships::get_users_json($this, $context, $lti->course, $tool, $role, $limitfrom, $limitnum, $lti, $info);

            $response->set_content_type($this->formats[0]);
            $response->set_body($json);

        } catch (\Exception $e) {
            $response->set_code($e->getCode());
        }

    }

    /**
     * Parse a value for custom parameter substitution variables.
     *
     * @param string $value String to be parsed
     *
     * @return string
     */
    public function parse_value($value) {

        $id = optional_param('id', 0, PARAM_INT); // Course Module ID.
        if (!empty($id)) {
            $cm = get_coursemodule_from_id('lti', $id, 0, false, MUST_EXIST);
            $this->params['link_id'] = $cm->instance;
        }
        $value = str_replace('$LtiLink.memberships.url', parent::get_endpoint(), $value);

        return $value;

    }

}

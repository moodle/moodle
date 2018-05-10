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
 * This file contains a class definition for the Context Memberships resource
 *
 * @package    ltiservice_memberships
 * @copyright  2015 Vital Source Technologies http://vitalsource.com
 * @author     Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace ltiservice_memberships\local\resources;

use mod_lti\local\ltiservice\resource_base;
use ltiservice_memberships\local\service\memberships;
use core_availability\info_module;

defined('MOODLE_INTERNAL') || die();

/**
 * A resource implementing Context Memberships.
 *
 * @package    ltiservice_memberships
 * @since      Moodle 3.0
 * @copyright  2015 Vital Source Technologies http://vitalsource.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contextmemberships extends resource_base {

    /**
     * Class constructor.
     *
     * @param \ltiservice_memberships\local\service\memberships $service Service instance
     */
    public function __construct($service) {

        parent::__construct($service);
        $this->id = 'ToolProxyBindingMemberships';
        $this->template = '/{context_type}/{context_id}/bindings/{tool_code}/memberships';
        $this->variables[] = 'ToolProxyBinding.memberships.url';
        $this->formats[] = 'application/vnd.ims.lis.v2.membershipcontainer+json';
        $this->methods[] = 'GET';

    }

    /**
     * Execute the request for this resource.
     *
     * @param \mod_lti\local\ltiservice\response $response  Response object for this request.
     */
    public function execute($response) {
        global $DB;

        $params = $this->parse_template();
        $role = optional_param('role', '', PARAM_TEXT);
        $limitnum = optional_param('limit', 0, PARAM_INT);
        $limitfrom = optional_param('from', 0, PARAM_INT);
        $linkid = optional_param('rlid', '', PARAM_TEXT);
        $lti = null;
        $modinfo = null;

        if ($limitnum <= 0) {
            $limitfrom = 0;
        }

        try {
            if (!($course = $DB->get_record('course', array('id' => $params['context_id']), 'id', IGNORE_MISSING))) {
                throw new \Exception(null, 404);
            }
            if (!($context = \context_course::instance($course->id))) {
                throw new \Exception(null, 404);
            }
            if (!($tool = $DB->get_record('lti_types', array('id' => $params['tool_code']),
                                    'id,toolproxyid,enabledcapability,parameter', IGNORE_MISSING))) {
                throw new \Exception(null, 404);
            }
            if (!empty($linkid)) {
                if (!($lti = $DB->get_record('lti', array('id' => $linkid), 'id,course,typeid,servicesalt', IGNORE_MISSING))) {
                    throw new \Exception(null, 404);
                }
                $modinfo = get_fast_modinfo($course);
                $cm = get_coursemodule_from_instance('lti', $linkid, $lti->course, false, MUST_EXIST);
                $cm = $modinfo->get_cm($cm->id);
                $modinfo = new info_module($cm);
                if ($modinfo->is_available_for_all()) {
                    $modinfo = null;
                }
            }
            if ($tool->toolproxyid == 0) {
                if (!$this->check_type($params['tool_code'], $params['context_id'],
                        'ToolProxyBinding.memberships.url:get', null)) {
                    throw new \Exception(null, 403);
                }
            } else {
                $toolproxy = $DB->get_record('lti_tool_proxies', array('id' => $tool->toolproxyid), 'guid', IGNORE_MISSING);
                if (!$this->check_tool_proxy($toolproxy->guid, $response->get_request_data())) {
                    throw new \Exception(null, 403);
                }
            }
            $json = memberships::get_users_json($this, $context, $course->id, $tool, $role, $limitfrom, $limitnum, $lti, $modinfo);

            $response->set_content_type($this->formats[0]);
            $response->set_body($json);

        } catch (\Exception $e) {
            $response->set_code($e->getCode());
        }
    }

    /**
     * get permissions from the config of the tool for that resource
     *
     * @param int $typeid
     * @return array with the permissions related to this resource by the $lti_type or null if none.
     */
    public function get_permissions($typeid) {
        $tool = lti_get_type_type_config($typeid);
        if ($tool->ltiservice_memberships == '1') {
            return array('ToolProxyBinding.memberships.url:get');
        } else {
            return array();
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
        global $COURSE, $DB;

        if (strpos($value, '$ToolProxyBinding.memberships.url') !== false) {
            if ($COURSE->id === SITEID) {
                $this->params['context_type'] = 'Group';
            } else {
                $this->params['context_type'] = 'CourseSection';
            }
            $this->params['context_id'] = $COURSE->id;

            $id = optional_param('id', 0, PARAM_INT); // Course Module ID.
            if (!empty($id)) {
                $cm = get_coursemodule_from_id('lti', $id, 0, false, IGNORE_MISSING);
                $lti = $DB->get_record('lti', array('id' => $cm->instance), 'typeid', IGNORE_MISSING);
                if ($lti && !empty($lti->typeid)) {
                    $this->params['tool_code'] = $lti->typeid;
                }
            }
            $value = str_replace('$ToolProxyBinding.memberships.url', parent::get_endpoint(), $value);
        }
        return $value;

    }

}

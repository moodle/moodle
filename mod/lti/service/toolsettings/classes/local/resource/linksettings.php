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
 * This file contains a class definition for the Context Settings resource
 *
 * @package    ltiservice_toolsettings
 * @copyright  2014 Vital Source Technologies http://vitalsource.com
 * @author     Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace ltiservice_toolsettings\local\resource;

use ltiservice_toolsettings\local\resource\systemsettings;
use ltiservice_toolsettings\local\resource\contextsettings;
use ltiservice_toolsettings\local\service\toolsettings;

defined('MOODLE_INTERNAL') || die();

/**
 * A resource implementing the Context-level (ToolProxyBinding) Settings.
 *
 * @package    ltiservice_toolsettings
 * @since      Moodle 2.8
 * @copyright  2014 Vital Source Technologies http://vitalsource.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class linksettings extends \mod_lti\local\ltiservice\resource_base {

    /**
     * Class constructor.
     *
     * @param ltiservice_toolsettings\local\resource\linksettings $service Service instance
     */
    public function __construct($service) {

        parent::__construct($service);
        $this->id = 'LtiLinkSettings';
        $this->template = '/links/{link_id}';
        $this->variables[] = 'LtiLink.custom.url';
        $this->formats[] = 'application/vnd.ims.lti.v2.toolsettings+json';
        $this->formats[] = 'application/vnd.ims.lti.v2.toolsettings.simple+json';
        $this->methods[] = 'GET';
        $this->methods[] = 'PUT';

    }

    /**
     * Execute the request for this resource.
     *
     * @param mod_lti\local\ltiservice\response $response  Response object for this request.
     */
    public function execute($response) {
        global $DB, $COURSE;

        $params = $this->parse_template();
        $linkid = $params['link_id'];
        $bubble = optional_param('bubble', '', PARAM_ALPHA);
        $contenttype = $response->get_accept();
        $simpleformat = !empty($contenttype) && ($contenttype == $this->formats[1]);
        $ok = (empty($bubble) || ((($bubble == 'distinct') || ($bubble == 'all')))) &&
             (!$simpleformat || empty($bubble) || ($bubble != 'all')) &&
             (empty($bubble) || ($response->get_request_method() == 'GET'));
        if (!$ok) {
            $response->set_code(406);
        }

        $systemsetting = null;
        $contextsetting = null;
        if ($ok) {
            $ok = !empty($linkid);
            if ($ok) {
                $lti = $DB->get_record('lti', array('id' => $linkid), 'course,typeid', MUST_EXIST);
                $ltitype = $DB->get_record('lti_types', array('id' => $lti->typeid));
                $toolproxy = $DB->get_record('lti_tool_proxies', array('id' => $ltitype->toolproxyid));
                $ok = $this->check_tool_proxy($toolproxy->guid, $response->get_request_data());
            }
            if (!$ok) {
                $response->set_code(401);
            }
        }
        if ($ok) {
            $linksettings = lti_get_tool_settings($this->get_service()->get_tool_proxy()->id, $lti->course, $linkid);
            if (!empty($bubble)) {
                $contextsetting = new contextsettings($this->get_service());
                if ($COURSE == 'site') {
                    $contextsetting->params['context_type'] = 'Group';
                } else {
                    $contextsetting->params['context_type'] = 'CourseSection';
                }
                $contextsetting->params['context_id'] = $lti->course;
                $contextsetting->params['vendor_code'] = $this->get_service()->get_tool_proxy()->vendorcode;
                $contextsetting->params['product_code'] = $this->get_service()->get_tool_proxy()->id;
                $contextsettings = lti_get_tool_settings($this->get_service()->get_tool_proxy()->id, $lti->course);
                $systemsetting = new systemsettings($this->get_service());
                $systemsetting->params['tool_proxy_id'] = $this->get_service()->get_tool_proxy()->id;
                $systemsettings = lti_get_tool_settings($this->get_service()->get_tool_proxy()->id);
                if ($bubble == 'distinct') {
                    toolsettings::distinct_settings($systemsettings, $contextsettings, $linksettings);
                }
            } else {
                $contextsettings = null;
                $systemsettings = null;
            }
            if ($response->get_request_method() == 'GET') {
                $json = '';
                if ($simpleformat) {
                    $response->set_content_type($this->formats[1]);
                    $json .= "{";
                } else {
                    $response->set_content_type($this->formats[0]);
                    $json .= "{\n  \"@context\":\"http://purl.imsglobal.org/ctx/lti/v2/ToolSettings\",\n  \"@graph\":[\n";
                }
                $settings = toolsettings::settings_to_json($systemsettings, $simpleformat, 'ToolProxy', $systemsetting);
                $json .= $settings;
                $isfirst = strlen($settings) <= 0;
                $settings = toolsettings::settings_to_json($contextsettings, $simpleformat, 'ToolProxyBinding', $contextsetting);
                if (strlen($settings) > 0) {
                    if (!$isfirst) {
                        $json .= ",";
                        if (!$simpleformat) {
                            $json .= "\n";
                        }
                    }
                    $isfirst = false;
                }
                $json .= $settings;
                $settings = toolsettings::settings_to_json($linksettings, $simpleformat, 'LtiLink', $this);
                if ((strlen($settings) > 0) && !$isfirst) {
                    $json .= ",";
                    if (!$simpleformat) {
                        $json .= "\n";
                    }
                }
                $json .= $settings;
                if ($simpleformat) {
                    $json .= "\n}";
                } else {
                    $json .= "\n  ]\n}";
                }
                $response->set_body($json);
            } else { // PUT.
                $settings = null;
                if ($response->get_content_type() == $this->formats[0]) {
                    $json = json_decode($response->get_request_data());
                    $ok = !empty($json);
                    if ($ok) {
                        $ok = isset($json->{"@graph"}) && is_array($json->{"@graph"}) && (count($json->{"@graph"}) == 1) &&
                              ($json->{"@graph"}[0]->{"@type"} == 'LtiLink');
                    }
                    if ($ok) {
                        $settings = $json->{"@graph"}[0]->custom;
                        unset($settings->{'@id'});
                    }
                } else {  // Simple JSON.
                    $json = json_decode($response->get_request_data(), true);
                    $ok = !empty($json);
                    if ($ok) {
                        $ok = is_array($json);
                    }
                    if ($ok) {
                        $settings = $json;
                    }
                }
                if ($ok) {
                    lti_set_tool_settings($settings, $this->get_service()->get_tool_proxy()->id, $lti->course, $linkid);
                } else {
                    $response->set_code(406);
                }
            }
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
        $value = str_replace('$LtiLink.custom.url', parent::get_endpoint(), $value);

        return $value;

    }

}

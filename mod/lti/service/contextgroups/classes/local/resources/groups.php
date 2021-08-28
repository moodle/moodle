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
 * This file contains a class definition for the Course Groups resource
 *
 * @package    ltiservice_contextgroups
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @author     Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace ltiservice_contextgroups\local\resources;

use mod_lti\local\ltiservice\resource_base;
use ltiservice_contextgroups\local\service\contextgroups;
use core_availability\info_module;

defined('MOODLE_INTERNAL') || die();

/**
 * A resource implementing Context Groups.
 *
 * @package    ltiservice_contextgroups
 * @since      Moodle 3.12
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class groups extends resource_base {

    /**
     * Class constructor.
     *
     * @param \ltiservice_contextgroups\local\service\contextgroups $service Service instance
     */
    public function __construct($service) {

        parent::__construct($service);
        $this->id = 'ToolProxyBindingGroups';
        $this->template = '/{context_type}/{context_id}/bindings/{tool_code}/groups';
        $this->variables[] = 'ToolProxyBinding.groups.url';
        $this->formats[] = 'application/vnd.ims.lti-gs.v1.contextgroupcontainer+json';
        $this->methods[] = self::HTTP_GET;

    }

    /**
     * Execute the request for this resource.
     *
     * @param \mod_lti\local\ltiservice\response $response  Response object for this request.
     */
    public function execute($response) {
        global $DB;

        $params = $this->parse_template();
        $userid = optional_param('user_id', 0, PARAM_INT);
        $limitnum = optional_param('limit', 0, PARAM_INT);
        $limitfrom = optional_param('from', 0, PARAM_INT);

        if ($limitnum <= 0) {
            $limitfrom = 0;
        }

        try {
            if (!$this->check_tool($params['tool_code'], $response->get_request_data(),
                array(contextgroups::SCOPE_CONTEXT_GROUP_READ))) {
                throw new \Exception(null, 401);
            }
            if (!($course = $DB->get_record('course', array('id' => $params['context_id']), 'id',
                IGNORE_MISSING))) {
                throw new \Exception("Not Found: Course {$params['context_id']} doesn't exist", 404);
            }
            if (!$this->get_service()->is_allowed_in_context($params['tool_code'], $course->id)) {
                throw new \Exception(null, 404);
            }
            if (!($context = \context_course::instance($course->id))) {
                throw new \Exception("Not Found: Course instance {$course->id} doesn't exist", 404);
            }

            $data = groups_get_course_data($course->id);
            if (!empty($userid)) {
                $usergroups = groups_get_all_groups($course->id, $userid);
            } else {
                $usergroups = $data->groups;
            }

            $groups = new \stdClass();
            $groups->id = $this->get_endpoint();
            $groups->groups = array();
            $n = 0;
            $more = false;
            foreach ($usergroups as $group) {
                $n++;
                if ($limitnum > 0) {
                    if ($n <= $limitfrom) {
                        continue;
                    }
                    if (count($groups->groups) >= $limitnum) {
                        $more = true;
                        break;
                    }
                }
                $g = new \stdClass();
                $g->id = $group->id;
                $g->name = $group->name;
                foreach ($data->mappings as $mapping) {
                    if ($mapping->groupid === $group->id) {
                        $g->set_id = $mapping->groupingid;
                        break;
                    }
                }
                $groups->groups[] = $g;
            }

            if ($more) {
                $nextlimitfrom = $limitfrom + $limitnum;
                $nextpage = "{$this->get_endpoint()}?limit={$limitnum}&from={$nextlimitfrom}";
                if (!empty($userid)) {
                    $nextpage .= "&user_id={$userid}";
                }
                $response->add_additional_header("Link: <{$nextpage}>; rel=\"next\"");
            }
            $sep = '?';
            if ($more || !empty($limitfrom)) {
                if (!empty($limitnum)) {
                    $groups->id .= $sep . "limit={$limitnum}";
                    $sep = '&';
                }
                if (!empty($limitfrom)) {
                    $groups->id .= $sep . "from={$limitfrom}";
                    $sep = '&';
                }
            }
            if (!empty($userid)) {
                $groups->id .= $sep . "user_id={$userid}";
            }

            $response->set_content_type($this->formats[0]);

            $response->set_body(json_encode($groups));
        } catch (\Exception $e) {
            $response->set_code($e->getCode());
            $response->set_reason($e->getMessage());
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

        if (strpos($value, '$ToolProxyBinding.groups.url') !== false) {
            if ($COURSE->id === SITEID) {
                $this->params['context_type'] = 'Group';
            } else {
                $this->params['context_type'] = 'CourseSection';
            }
            $this->params['context_id'] = $COURSE->id;
            if ($tool = $this->get_service()->get_type()) {
                $this->params['tool_code'] = $tool->id;
            }
            $value = str_replace('$ToolProxyBinding.groups.url', parent::get_endpoint(), $value);
        }
        return $value;

    }

}

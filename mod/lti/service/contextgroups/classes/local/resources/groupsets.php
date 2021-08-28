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
 * This file contains a class definition for the Course Group Sets resource
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
 * A resource implementing Context Group Sets.
 *
 * @package    ltiservice_contextgroups
 * @since      Moodle 3.12
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class groupsets extends resource_base {

    /**
     * Class constructor.
     *
     * @param \ltiservice_contextgroups\local\service\contextgroups $service Service instance
     */
    public function __construct($service) {

        parent::__construct($service);
        $this->id = 'ToolProxyBindingGroupsets';
        $this->template = '/{context_type}/{context_id}/bindings/{tool_code}/groupsets';
        $this->variables[] = 'ToolProxyBinding.groupsets.url';
        $this->formats[] = 'application/vnd.ims.lti-gs.v1.contextgroupsetcontainer+json';
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
            if (!($course = $DB->get_record('course', array('id' => $params['context_id']), 'id,shortname,fullname',
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

            $groupings = new \stdClass();
            $groupings->id = $this->get_endpoint();
            $groupings->sets = array();
            $n = 0;
            $more = false;
            foreach ($data->groupings as $grouping) {
                $n++;
                if ($limitnum > 0) {
                    if ($n <= $limitfrom) {
                        continue;
                    }
                    if (count($groupings->sets) >= $limitnum) {
                        $more = true;
                        break;
                    }
                }
                $set = new \stdClass();
                $set->id = $grouping->id;
                $set->name = $grouping->name;
                $groupings->sets[] = $set;
            }

            if ($more) {
                $nextlimitfrom = $limitfrom + $limitnum;
                $nextpage = "{$this->get_endpoint()}?limit={$limitnum}&from={$nextlimitfrom}";
                $response->add_additional_header("Link: <{$nextpage}>; rel=\"next\"");
            }
            $sep = '?';
            if ($more || !empty($limitfrom)) {
                if (!empty($limitnum)) {
                    $groupings->id .= $sep . "limit={$limitnum}";
                    $sep = '&';
                }
                if (!empty($limitfrom)) {
                    $groupings->id .= $sep . "from={$limitfrom}";
                    $sep = '&';
                }
            }

            $response->set_content_type($this->formats[0]);

            $response->set_body(json_encode($groupings));
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

        if (strpos($value, '$ToolProxyBinding.groupsets.url') !== false) {
            if ($COURSE->id === SITEID) {
                $this->params['context_type'] = 'Group';
            } else {
                $this->params['context_type'] = 'CourseSection';
            }
            $this->params['context_id'] = $COURSE->id;
            if ($tool = $this->get_service()->get_type()) {
                $this->params['tool_code'] = $tool->id;
            }
            $value = str_replace('$ToolProxyBinding.groupsets.url', parent::get_endpoint(), $value);
        }
        return $value;

    }

}

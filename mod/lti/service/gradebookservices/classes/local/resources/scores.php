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
 * This file contains a class definition for the LISResult container resource
 *
 * @package    ltiservice_gradebookservices
 * @copyright  2017 Cengage Learning http://www.cengage.com
 * @author     Dirk Singels, Diego del Blanco, Claude Vervoort
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace ltiservice_gradebookservices\local\resources;

use ltiservice_gradebookservices\local\service\gradebookservices;
use mod_lti\local\ltiservice\resource_base;

defined('MOODLE_INTERNAL') || die();

/**
 * A resource implementing LISResult container.
 *
 * @package    ltiservice_gradebookservices
 * @copyright  2017 Cengage Learning http://www.cengage.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class scores extends resource_base {

    /**
     * Class constructor.
     *
     * @param \ltiservice_gradebookservices\local\service\gradebookservices $service Service instance
     */
    public function __construct($service) {

        parent::__construct($service);
        $this->id = 'Score.collection';
        $this->template = '/{context_id}/lineitems/{item_id}/lineitem/scores';
        $this->variables[] = 'Scores.url';
        $this->formats[] = 'application/vnd.ims.lis.v1.scorecontainer+json';
        $this->formats[] = 'application/vnd.ims.lis.v1.score+json';
        $this->methods[] = 'POST';

    }

    /**
     * Execute the request for this resource.
     *
     * @param \mod_lti\local\ltiservice\response $response  Response object for this request.
     */
    public function execute($response) {
        global $CFG, $DB;

        $params = $this->parse_template();
        $contextid = $params['context_id'];
        $itemid = $params['item_id'];

        // GET is disabled by the moment, but we have the code ready
        // for a future implementation.

        $isget = $response->get_request_method() === 'GET';
        if ($isget) {
            $contenttype = $response->get_accept();
        } else {
            $contenttype = $response->get_content_type();
        }
        $container = empty($contenttype) || ($contenttype === $this->formats[0]);
        // We will receive typeid when working with LTI 1.x, if not the we are in LTI 2.
        $typeid = optional_param('type_id', null, PARAM_ALPHANUM);
        if (is_null($typeid)) {
            if (!$this->check_tool_proxy(null, $response->get_request_data())) {
                $response->set_code(403);
                return;
            }
        } else {
            switch ($response->get_request_method()) {
                case 'GET':
                    $response->set_code(405);
                    $response->set_reason("GET requests are not allowed.");
                    return;
                case 'POST':
                    if (!$this->check_type($typeid, $contextid, 'Score.collection:post', $response->get_request_data())) {
                        $response->set_code(401);
                        $response->set_reason("This resource does not support POST requests.");
                        return;
                    }
                    break;
                default:  // Should not be possible.
                    $response->set_code(405);
                    return;
            }
        }
        if (empty($contextid) || !($container ^ ($response->get_request_method() === 'POST')) ||
                (!empty($contenttype) && !in_array($contenttype, $this->formats))) {
            $response->set_code(400);
            return;
        }
        if (!$DB->record_exists('course', array('id' => $contextid))) {
            $response->set_code(404);
            $response->set_reason("Not Found: Course $contextid doesn't exist.");
            return;
        }
        if (!$DB->record_exists('grade_items', array('id' => $itemid))) {
            $response->set_code(404);
            $response->set_reason("Not Found: Grade item $itemid doesn't exist.");
            return;
        }
        $item = $this->get_service()->get_lineitem($contextid, $itemid, $typeid);
        if ($item === false) {
            $response->set_code(403);
            $response->set_reason("Line item does not exist.");
            return;
        }
        $gbs = gradebookservices::find_ltiservice_gradebookservice_for_lineitem($itemid);
        $ltilinkid = null;
        if (isset($item->iteminstance)) {
            $ltilinkid = $item->iteminstance;
        } else if ($gbs && isset($gbs->ltilinkid)) {
            $ltilinkid = $gbs->ltilinkid;
        }
        if ($ltilinkid != null) {
            if (is_null($typeid)) {
                if (isset($item->iteminstance) && (!gradebookservices::check_lti_id($ltilinkid, $item->courseid,
                        $this->get_service()->get_tool_proxy()->id))) {
                    $response->set_code(403);
                    $response->set_reason("Invalid LTI id supplied.");
                    return;
                }
            } else {
                if (isset($item->iteminstance) && (!gradebookservices::check_lti_1x_id($ltilinkid, $item->courseid,
                        $typeid))) {
                    $response->set_code(403);
                    $response->set_reason("Invalid LTI id supplied.");
                    return;
                }
            }
        }
        $json = '[]';
        require_once($CFG->libdir.'/gradelib.php');
        switch ($response->get_request_method()) {
            case 'GET':
                $response->set_code(405);
                $response->set_reason("GET requests are not allowed.");
                break;
            case 'POST':
                try {
                    $json = $this->get_json_for_post_request($response, $response->get_request_data(), $item, $contextid, $typeid);
                    $response->set_content_type($this->formats[1]);
                } catch (\Exception $e) {
                    $response->set_code($e->getCode());
                    $response->set_reason($e->getMessage());
                }
                break;
            default:  // Should not be possible.
                $response->set_code(405);
                $response->set_reason("Invalid request method specified.");
                return;
        }
        $response->set_body($json);
    }

    /**
     * Generate the JSON for a POST request.
     *
     * @param \mod_lti\local\ltiservice\response $response Response object for this request.
     * @param string $body POST body
     * @param object $item Grade item instance
     * @param string $contextid
     * @param string $typeid
     *
     * @throws \Exception
     */
    private function get_json_for_post_request($response, $body, $item, $contextid, $typeid) {
        $score = json_decode($body);
        if (empty($score) ||
                !isset($score->userId) ||
                !isset($score->timestamp) ||
                !isset($score->gradingProgress) ||
                !isset($score->activityProgress) ||
                !isset($score->timestamp) ||
                isset($score->timestamp) && !gradebookservices::validate_iso8601_date($score->timestamp) ||
                (isset($score->scoreGiven) && !is_numeric($score->scoreGiven)) ||
                (isset($score->scoreMaximum) && !is_numeric($score->scoreMaximum)) ||
                (!gradebookservices::is_user_gradable_in_course($contextid, $score->userId))
                ) {
            throw new \Exception('Incorrect score received' . $score, 400);
        }
        $score->timemodified = intval($score->timestamp);

        if (!isset($score->scoreMaximum)) {
            $score->scoreMaximum = 1;
        }
        $response->set_code(200);
        $grade = \grade_grade::fetch(array('itemid' => $item->id, 'userid' => $score->userId));
        if ($grade &&  !empty($grade->timemodified)) {
            if ($grade->timemodified >= strtotime($score->timestamp)) {
                $exmsg = "Refusing score with an earlier timestamp for item " . $item->id . " and user " . $score->userId;
                throw new \Exception($exmsg, 409);
            }
        }
        if (isset($score->scoreGiven)) {
            if ($score->gradingProgress != 'FullyGraded') {
                $score->scoreGiven = null;
            }
        }
        gradebookservices::save_score($item, $score, $score->userId, $typeid);
    }

    /**
     * get permissions from the config of the tool for that resource
     *
     * @param int $typeid
     *
     * @return array with the permissions related to this resource by the $lti_type or null if none.
     */
    public function get_permissions($typeid) {
        $tool = lti_get_type_type_config($typeid);
        if ($tool->ltiservice_gradesynchronization == '1') {
            return array('Score.collection:post');
        } else if ($tool->ltiservice_gradesynchronization == '2') {
            return array('Score.collection:post');
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
        global $COURSE, $CFG;

        if (strpos($value, '$Scores.url') !== false) {
            require_once($CFG->libdir . '/gradelib.php');

            $resolved = '';
            $this->params['context_id'] = $COURSE->id;
            $id = optional_param('id', 0, PARAM_INT); // Course Module ID.
            if (!empty($id)) {
                $cm = get_coursemodule_from_id('lti', $id, 0, false, MUST_EXIST);
                $id = $cm->instance;
                $item = grade_get_grades($COURSE->id, 'mod', 'lti', $id);
                if ($item && $item->items) {
                    $this->params['item_id'] = $item->items[0]->id;
                    $resolved = parent::get_endpoint();
                }
            }
            $value = str_replace('$Scores.url', $resolved, $value);
        }

        return $value;
    }
}

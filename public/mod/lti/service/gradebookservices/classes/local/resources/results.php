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
 * This file contains a class definition for the LISResults container resource
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
class results extends resource_base {

    /**
     * Class constructor.
     *
     * @param \ltiservice_gradebookservices\local\service\gradebookservices $service Service instance
     */
    public function __construct($service) {

        parent::__construct($service);
        $this->id = 'Result.collection';
        $this->template = '/{context_id}/lineitems/{item_id}/lineitem/results';
        $this->variables[] = 'Results.url';
        $this->formats[] = 'application/vnd.ims.lis.v2.resultcontainer+json';
        $this->methods[] = 'GET';
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

        $isget = $response->get_request_method() === self::HTTP_GET;
        // We will receive typeid when working with LTI 1.x, if not the we are in LTI 2.
        $typeid = optional_param('type_id', null, PARAM_INT);

        $scope = gradebookservices::SCOPE_GRADEBOOKSERVICES_RESULT_READ;

        try {
            if (!$this->check_tool($typeid, $response->get_request_data(), array($scope))) {
                throw new \Exception(null, 401);
            }
            $typeid = $this->get_service()->get_type()->id;
            if (!($course = $DB->get_record('course', array('id' => $contextid), 'id', IGNORE_MISSING))) {
                throw new \Exception("Not Found: Course {$contextid} doesn't exist", 404);
            }
            if (!$this->get_service()->is_allowed_in_context($typeid, $course->id)) {
                throw new \Exception('Not allowed in context', 403);
            }
            if (!$DB->record_exists('grade_items', array('id' => $itemid))) {
                throw new \Exception("Not Found: Grade item {$itemid} doesn't exist", 404);
            }
            $item = $this->get_service()->get_lineitem($contextid, $itemid, $typeid);
            if ($item === false) {
                throw new \Exception('Line item does not exist', 404);
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
            require_once($CFG->libdir.'/gradelib.php');
            switch ($response->get_request_method()) {
                case 'GET':
                    $useridfilter = optional_param('user_id', 0, PARAM_INT);
                    $limitnum = optional_param('limit', 0, PARAM_INT);
                    $limitfrom = optional_param('from', 0, PARAM_INT);
                    $typeid = optional_param('type_id', null, PARAM_TEXT);
                    $json = $this->get_json_for_get_request($item->id, $limitfrom, $limitnum,
                            $useridfilter, $typeid, $response);
                    $response->set_content_type($this->formats[0]);
                    $response->set_body($json);
                    break;
                default:  // Should not be possible.
                    $response->set_code(405);
                    $response->set_reason("Invalid request method specified.");
                    return;
            }
            $response->set_body($json);
        } catch (\Exception $e) {
            $response->set_code($e->getCode());
            $response->set_reason($e->getMessage());
        }
    }

    /**
     * Generate the JSON for a GET request.
     *
     * @param int    $itemid     Grade item instance ID
     * @param int $limitfrom  Offset for the first result to include in this paged set
     * @param int $limitnum   Maximum number of results to include in the response, ignored if zero
     * @param int    $useridfilter     The user id to filter the results.
     * @param int    $typeid     Lti tool typeid (or null)
     * @param \mod_lti\local\ltiservice\response $response   The response element needed to add a header.
     *
     * @return string
     */
    private function get_json_for_get_request($itemid, $limitfrom, $limitnum, $useridfilter, $typeid, $response) {

        if ($useridfilter > 0) {
            $grades = \grade_grade::fetch_all(array('itemid' => $itemid, 'userid' => $useridfilter));
        } else {
            $grades = \grade_grade::fetch_all(array('itemid' => $itemid));
        }

        $firstpage = null;
        $nextpage = null;
        $prevpage = null;
        $lastpage = null;
        if ($grades && isset($limitnum) && $limitnum > 0) {
            // Since we only display grades that have been modified, we need to filter first in order to support
            // paging.
            $resultgrades = array_filter($grades, function ($grade) {
                return !empty($grade->timemodified);
            });
            // We save the total count to calculate the last page.
            $totalcount = count($resultgrades);
            // We slice to the requested item offset to insure proper item is always first, and we always return
            // first pageset of any remaining items.
            $grades = array_slice($resultgrades, $limitfrom);
            if (count($grades) > 0) {
                $pagedgrades = array_chunk($grades, $limitnum);
                $pageset = 0;
                $grades = $pagedgrades[$pageset];
            }
            if ($limitfrom >= $totalcount || $limitfrom < 0) {
                $outofrange = true;
            } else {
                $outofrange = false;
            }
            $limitprev = $limitfrom - $limitnum >= 0 ? $limitfrom - $limitnum : 0;
            $limitcurrent = $limitfrom;
            $limitlast = $totalcount - $limitnum + 1 >= 0 ? $totalcount - $limitnum + 1 : 0;
            $limitfrom += $limitnum;

            $baseurl = new \moodle_url($this->get_endpoint());
            if (is_null($typeid)) {
                $baseurl->param('limit', $limitnum);

                if (($limitfrom <= $totalcount - 1) && (!$outofrange)) {
                    $nextpage = new \moodle_url($baseurl, ['from' => $limitfrom]);
                }
                $firstpage = new \moodle_url($baseurl, ['from' => 0]);
                $canonicalpage = new \moodle_url($baseurl, ['from' => $limitcurrent]);
                $lastpage = new \moodle_url($baseurl, ['from' => $limitlast]);
                if (($limitcurrent > 0) && (!$outofrange)) {
                    $prevpage = new \moodle_url($baseurl, ['from' => $limitprev]);
                }
            } else {
                $baseurl->params(['type_id' => $typeid, 'limit' => $limitnum]);

                if (($limitfrom <= $totalcount - 1) && (!$outofrange)) {
                    $nextpage = new \moodle_url($baseurl, ['from' => $limitfrom]);
                }
                $firstpage = new \moodle_url($baseurl, ['from' => 0]);
                $canonicalpage = new \moodle_url($baseurl, ['from' => $limitcurrent]);
                if (($limitcurrent > 0) && (!$outofrange)) {
                    $prevpage = new \moodle_url($baseurl, ['from' => $limitprev]);
                }
            }
        }

        $jsonresults = [];
        $lineitem = new lineitem($this->get_service());
        $endpoint = $lineitem->get_endpoint();
        if ($grades) {
            foreach ($grades as $grade) {
                if (!empty($grade->timemodified)) {
                    array_push($jsonresults, gradebookservices::result_for_json($grade, $endpoint, $typeid));
                }
            }
        }

        if (isset($canonicalpage) && ($canonicalpage)) {
            $links = 'Link: <' . $firstpage->out() . '>; rel=“first”';
            if (!is_null($prevpage)) {
                $links .= ', <' . $prevpage->out() . '>; rel=“prev”';
            }
            $links .= ', <' . $canonicalpage->out() . '>; rel=“canonical”';
            if (!is_null($nextpage)) {
                $links .= ', <' . $nextpage->out() . '>; rel=“next”';
            }
            $links .= ', <' . $lastpage->out() . '>; rel=“last”';
            $response->add_additional_header($links);
        }
        return json_encode($jsonresults);
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
        if (strpos($value, '$Results.url') !== false) {
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
            $value = str_replace('$Results.url', $resolved, $value);
        }
        return $value;
    }
}

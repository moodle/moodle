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
 * This file contains a class definition for the LineItem resource
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
 * A resource implementing LineItem.
 *
 * @package    ltiservice_gradebookservices
 * @copyright  2017 Cengage Learning http://www.cengage.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lineitem extends resource_base {

    /**
     * Class constructor.
     *
     * @param gradebookservices $service Service instance
     */
    public function __construct($service) {

        parent::__construct($service);
        $this->id = 'LineItem.item';
        $this->template = '/{context_id}/lineitems/{item_id}/lineitem';
        $this->variables[] = 'LineItem.url';
        $this->formats[] = 'application/vnd.ims.lis.v2.lineitem+json';
        $this->methods[] = self::HTTP_GET;
        $this->methods[] = self::HTTP_PUT;
        $this->methods[] = self::HTTP_DELETE;

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
        // We will receive typeid when working with LTI 1.x, if not then we are in LTI 2.
        $typeid = optional_param('type_id', null, PARAM_INT);

        $scopes = array(gradebookservices::SCOPE_GRADEBOOKSERVICES_LINEITEM);
        if ($response->get_request_method() === self::HTTP_GET) {
            $scopes[] = gradebookservices::SCOPE_GRADEBOOKSERVICES_LINEITEM_READ;
        }

        try {
            if (!$this->check_tool($typeid, $response->get_request_data(), $scopes)) {
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
            require_once($CFG->libdir.'/gradelib.php');
            switch ($response->get_request_method()) {
                case self::HTTP_GET:
                    $this->get_request($response, $item, $typeid);
                    break;
                case self::HTTP_PUT:
                    $json = $this->process_put_request($response->get_request_data(), $item, $typeid);
                    $response->set_body($json);
                    $response->set_code(200);
                    break;
                case self::HTTP_DELETE:
                    $this->process_delete_request($item);
                    $response->set_code(204);
                    break;
            }
        } catch (\Exception $e) {
            $response->set_code($e->getCode());
            $response->set_reason($e->getMessage());
        }
    }

    /**
     * Process a GET request.
     *
     * @param \mod_lti\local\ltiservice\response $response Response object for this request.
     * @param object $item Grade item instance.
     * @param string $typeid Tool Type Id
     */
    private function get_request($response, $item, $typeid) {

        $response->set_content_type($this->formats[0]);
        $lineitem = gradebookservices::item_for_json($item, substr(parent::get_endpoint(),
            0, strrpos(parent::get_endpoint(), "/", -10)), $typeid);
        $response->set_body(json_encode($lineitem));

    }

    /**
     * Process a PUT request.
     *
     * @param string $body PUT body
     * @param \ltiservice_gradebookservices\local\resources\lineitem $olditem Grade item instance
     * @param string $typeid Tool Type Id
     *
     * @return string
     * @throws \Exception
     */
    private function process_put_request($body, $olditem, $typeid) {
        global $DB;
        $json = json_decode($body);
        if (empty($json) ||
                !isset($json->scoreMaximum) ||
                !isset($json->label)) {
            throw new \Exception(null, 400);
        }
        $item = \grade_item::fetch(array('id' => $olditem->id, 'courseid' => $olditem->courseid));
        $gbs = gradebookservices::find_ltiservice_gradebookservice_for_lineitem($olditem->id);
        $updategradeitem = false;
        $rescalegrades = false;
        $oldgrademax = grade_floatval($item->grademax);
        $upgradegradebookservices = false;
        if ($item->itemname !== $json->label) {
            $updategradeitem = true;
        }
        $item->itemname = $json->label;
        if (!is_numeric($json->scoreMaximum)) {
            throw new \Exception(null, 400);
        } else {
            if (grade_floats_different($oldgrademax,
                    grade_floatval($json->scoreMaximum))) {
                $updategradeitem = true;
                $rescalegrades = true;
            }
            $item->grademax = grade_floatval($json->scoreMaximum);
        }
        if ($gbs) {
            $resourceid = (isset($json->resourceId)) ? $json->resourceId : '';
            $tag = (isset($json->tag)) ? $json->tag : '';
            if ($gbs->tag !== $tag || $gbs->resourceid !== $resourceid) {
                $upgradegradebookservices = true;
            }
            $gbs->tag = $tag;
            $gbs->resourceid = $resourceid;
            $incomingurl = null;
            $incomingparams = null;
            if (isset($json->submissionReview)) {
                $incomingurl = $json->submissionReview->url ?? 'DEFAULT';
                if (isset($json->submissionReview->custom)) {
                    $incomingparams = params_to_string($json->submissionReview->custom);
                }
            }
            if ($gbs->subreviewurl ?? null !== $incomingurl || $gbs->subreviewparams ?? null !== $incomingparams) {
                $upgradegradebookservices = true;
                $gbs->subreviewurl = $incomingurl;
                $gbs->subreviewparams = $incomingparams;
            }
        }
        $ltilinkid = null;
        if (isset($json->resourceLinkId)) {
            if (is_numeric($json->resourceLinkId)) {
                $ltilinkid = $json->resourceLinkId;
                if ($gbs) {
                    if (intval($gbs->ltilinkid) !== intval($json->resourceLinkId)) {
                        $gbs->ltilinkid = $json->resourceLinkId;
                        $upgradegradebookservices = true;
                    }
                } else {
                    if (intval($item->iteminstance) !== intval($json->resourceLinkId)) {
                        $item->iteminstance = intval($json->resourceLinkId);
                        $updategradeitem = true;
                    }
                }
            } else {
                throw new \Exception(null, 400);
            }
        } else if (isset($json->ltiLinkId)) {
            if (is_numeric($json->ltiLinkId)) {
                $ltilinkid = $json->ltiLinkId;
                if ($gbs) {
                    if (intval($gbs->ltilinkid) !== intval($json->ltiLinkId)) {
                        $gbs->ltilinkid = $json->ltiLinkId;
                        $upgradegradebookservices = true;
                    }
                } else {
                    if (intval($item->iteminstance) !== intval($json->ltiLinkId)) {
                        $item->iteminstance = intval($json->ltiLinkId);
                        $updategradeitem = true;
                    }
                }
            } else {
                throw new \Exception(null, 400);
            }
        }
        if ($ltilinkid != null) {
            if (is_null($typeid)) {
                if (!gradebookservices::check_lti_id($ltilinkid, $item->courseid,
                        $this->get_service()->get_tool_proxy()->id)) {
                            throw new \Exception(null, 403);
                }
            } else {
                if (!gradebookservices::check_lti_1x_id($ltilinkid, $item->courseid,
                        $typeid)) {
                            throw new \Exception(null, 403);
                }
            }
        }
        if ($updategradeitem) {
            if (!$item->update('mod/ltiservice_gradebookservices')) {
                throw new \Exception(null, 500);
            }
            if ($rescalegrades) {
                $item->rescale_grades_keep_percentage(0, $oldgrademax, 0, $item->grademax);
            }
        }

        $lineitem = new lineitem($this->get_service());
        $endpoint = $lineitem->get_endpoint();

        if ($upgradegradebookservices) {
            if (is_null($typeid)) {
                $toolproxyid = $this->get_service()->get_tool_proxy()->id;
                $baseurl = null;
            } else {
                $toolproxyid = null;
                $baseurl = lti_get_type_type_config($typeid)->lti_toolurl;
            }
            $DB->update_record('ltiservice_gradebookservices', (object)array(
                    'id' => $gbs->id,
                    'gradeitemid' => $gbs->gradeitemid,
                    'courseid' => $gbs->courseid,
                    'toolproxyid' => $toolproxyid,
                    'typeid' => $typeid,
                    'baseurl' => $baseurl,
                    'ltilinkid' => $ltilinkid,
                    'resourceid' => $resourceid,
                    'tag' => $gbs->tag,
                    'subreviewurl' => $gbs->subreviewurl,
                    'subreviewparams' => $gbs->subreviewparams
            ));
        }

        if (is_null($typeid)) {
            $id = "{$endpoint}";
            $json->id = $id;
        } else {
            $id = "{$endpoint}?type_id={$typeid}";
            $json->id = $id;
        }
        return json_encode($json, JSON_UNESCAPED_SLASHES);

    }

    /**
     * Process a DELETE request.
     *
     * @param \ltiservice_gradebookservices\local\resources\lineitem $item Grade item instance
     * @throws \Exception
     */
    private function process_delete_request($item) {
        global $DB;

        $gradeitem = \grade_item::fetch(array('id' => $item->id));
        if (($gbs = gradebookservices::find_ltiservice_gradebookservice_for_lineitem($item->id)) == false) {
            throw new \Exception(null, 403);
        }
        if (!$gradeitem->delete('mod/ltiservice_gradebookservices')) {
            throw new \Exception(null, 500);
        } else {
            $sqlparams = array();
            $sqlparams['id'] = $gbs->id;
            if (!$DB->delete_records('ltiservice_gradebookservices', $sqlparams)) {
                throw new \Exception(null, 500);
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
        global $COURSE, $CFG;
        if (strpos($value, '$LineItem.url') !== false) {
            $resolved = '';
            require_once($CFG->libdir . '/gradelib.php');

            $this->params['context_id'] = $COURSE->id;
            if ($tool = $this->get_service()->get_type()) {
                $this->params['tool_code'] = $tool->id;
            }
            $id = optional_param('id', 0, PARAM_INT); // Course Module ID.
            if (empty($id)) {
                $hint = optional_param('lti_message_hint', "", PARAM_TEXT);
                if ($hint) {
                    $hintdec = json_decode($hint);
                    if (isset($hintdec->cmid)) {
                        $id = $hintdec->cmid;
                    }
                }
            }
            if (!empty($id)) {
                $cm = get_coursemodule_from_id('lti', $id, 0, false, MUST_EXIST);
                $id = $cm->instance;
                $item = grade_get_grades($COURSE->id, 'mod', 'lti', $id);
                if ($item && $item->items) {
                    $this->params['item_id'] = $item->items[0]->id;
                    $resolved = parent::get_endpoint();
                    $resolved .= "?type_id={$tool->id}";
                }
            }
            $value = str_replace('$LineItem.url', $resolved, $value);
        }
        return $value;
    }
}

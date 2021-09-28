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
 * This file contains a class definition for the LTI Gradebook Services
 *
 * @package    ltiservice_gradebookservices
 * @copyright  2017 Cengage Learning http://www.cengage.com
 * @author     Dirk Singels, Diego del Blanco, Claude Vervoort
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace ltiservice_gradebookservices\local\service;

use ltiservice_gradebookservices\local\resources\lineitem;
use ltiservice_gradebookservices\local\resources\lineitems;
use ltiservice_gradebookservices\local\resources\results;
use ltiservice_gradebookservices\local\resources\scores;
use mod_lti\local\ltiservice\resource_base;
use mod_lti\local\ltiservice\service_base;

defined('MOODLE_INTERNAL') || die();

/**
 * A service implementing LTI Gradebook Services.
 *
 * @package    ltiservice_gradebookservices
 * @copyright  2017 Cengage Learning http://www.cengage.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradebookservices extends service_base {

    /** Read-only access to Gradebook services */
    const GRADEBOOKSERVICES_READ = 1;
    /** Full access to Gradebook services */
    const GRADEBOOKSERVICES_FULL = 2;
    /** Scope for full access to Lineitem service */
    const SCOPE_GRADEBOOKSERVICES_LINEITEM = 'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem';
    /** Scope for full access to Lineitem service */
    const SCOPE_GRADEBOOKSERVICES_LINEITEM_READ = 'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly';
    /** Scope for access to Result service */
    const SCOPE_GRADEBOOKSERVICES_RESULT_READ = 'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly';
    /** Scope for access to Score service */
    const SCOPE_GRADEBOOKSERVICES_SCORE = 'https://purl.imsglobal.org/spec/lti-ags/scope/score';


    /**
     * Class constructor.
     */
    public function __construct() {

        parent::__construct();
        $this->id = 'gradebookservices';
        $this->name = get_string($this->get_component_id(), $this->get_component_id());

    }

    /**
     * Get the resources for this service.
     *
     * @return resource_base[]
     */
    public function get_resources() {

        // The containers should be ordered in the array after their elements.
        // Lineitems should be after lineitem.
        if (empty($this->resources)) {
            $this->resources = array();
            $this->resources[] = new lineitem($this);
            $this->resources[] = new lineitems($this);
            $this->resources[] = new results($this);
            $this->resources[] = new scores($this);
        }

        return $this->resources;
    }

    /**
     * Get the scope(s) permitted for this service.
     *
     * @return array
     */
    public function get_permitted_scopes() {

        $scopes = array();
        $ok = !empty($this->get_type());
        if ($ok && isset($this->get_typeconfig()['ltiservice_gradesynchronization'])) {
            if (!empty($setting = $this->get_typeconfig()['ltiservice_gradesynchronization'])) {
                $scopes[] = self::SCOPE_GRADEBOOKSERVICES_LINEITEM_READ;
                $scopes[] = self::SCOPE_GRADEBOOKSERVICES_RESULT_READ;
                $scopes[] = self::SCOPE_GRADEBOOKSERVICES_SCORE;
                if ($setting == self::GRADEBOOKSERVICES_FULL) {
                    $scopes[] = self::SCOPE_GRADEBOOKSERVICES_LINEITEM;
                }
            }
        }

        return $scopes;

    }

    /**
     * Get the scopes defined by this service.
     *
     * @return array
     */
    public function get_scopes() {
        return [self::SCOPE_GRADEBOOKSERVICES_LINEITEM_READ, self::SCOPE_GRADEBOOKSERVICES_RESULT_READ,
            self::SCOPE_GRADEBOOKSERVICES_SCORE, self::SCOPE_GRADEBOOKSERVICES_LINEITEM];
    }

    /**
     * Adds form elements for gradebook sync add/edit page.
     *
     * @param \MoodleQuickForm $mform Moodle quickform object definition
     */
    public function get_configuration_options(&$mform) {

        $selectelementname = 'ltiservice_gradesynchronization';
        $identifier = 'grade_synchronization';
        $options = [
            get_string('nevergs', $this->get_component_id()),
            get_string('partialgs', $this->get_component_id()),
            get_string('alwaysgs', $this->get_component_id())
        ];

        $mform->addElement('select', $selectelementname, get_string($identifier, $this->get_component_id()), $options);
        $mform->setType($selectelementname, 'int');
        $mform->setDefault($selectelementname, 0);
        $mform->addHelpButton($selectelementname, $identifier, $this->get_component_id());
    }

    /**
     * Return an array of key/values to add to the launch parameters.
     *
     * @param string $messagetype 'basic-lti-launch-request' or 'ContentItemSelectionRequest'.
     * @param string $courseid the course id.
     * @param object $user The user id.
     * @param string $typeid The tool lti type id.
     * @param string $modlti The id of the lti activity.
     *
     * The type is passed to check the configuration
     * and not return parameters for services not used.
     *
     * @return array of key/value pairs to add as launch parameters.
     */
    public function get_launch_parameters($messagetype, $courseid, $user, $typeid, $modlti = null) {
        global $DB;
        $launchparameters = array();
        $this->set_type(lti_get_type($typeid));
        $this->set_typeconfig(lti_get_type_config($typeid));
        // Only inject parameters if the service is enabled for this tool.
        if (isset($this->get_typeconfig()['ltiservice_gradesynchronization'])) {
            if ($this->get_typeconfig()['ltiservice_gradesynchronization'] == self::GRADEBOOKSERVICES_READ ||
                    $this->get_typeconfig()['ltiservice_gradesynchronization'] == self::GRADEBOOKSERVICES_FULL) {
                // Check for used in context is only needed because there is no explicit site tool - course relation.
                if ($this->is_allowed_in_context($typeid, $courseid)) {
                    $id = null;
                    if (!is_null($modlti)) {
                        $conditions = array('courseid' => $courseid, 'itemtype' => 'mod',
                                'itemmodule' => 'lti', 'iteminstance' => $modlti);

                        $coupledlineitems = $DB->get_records('grade_items', $conditions);
                        $conditionsgbs = array('courseid' => $courseid, 'ltilinkid' => $modlti);
                        $lineitemsgbs = $DB->get_records('ltiservice_gradebookservices', $conditionsgbs);
                        // If a link has more that one attached grade items, per spec we do not populate line item url.
                        if (count($lineitemsgbs) == 1) {
                            $id = reset($lineitemsgbs)->gradeitemid;
                        }
                        if (count($lineitemsgbs) < 2 && count($coupledlineitems) == 1) {
                            $coupledid = reset($coupledlineitems)->id;
                            if (!is_null($id) && $id != $coupledid) {
                                $id = null;
                            } else {
                                $id = $coupledid;
                            }
                        }
                    }
                    $launchparameters['gradebookservices_scope'] = implode(',', $this->get_permitted_scopes());
                    $launchparameters['lineitems_url'] = '$LineItems.url';
                    if (!is_null($id)) {
                        $launchparameters['lineitem_url'] = '$LineItem.url';
                    }
                }
            }
        }
        return $launchparameters;
    }

    /**
     * Fetch the lineitem instances.
     *
     * @param string $courseid ID of course
     * @param string $resourceid Resource identifier used for filtering, may be null
     * @param string $ltilinkid Resource Link identifier used for filtering, may be null
     * @param string $tag
     * @param int $limitfrom Offset for the first line item to include in a paged set
     * @param int $limitnum Maximum number of line items to include in the paged set
     * @param string $typeid
     *
     * @return array
     * @throws \Exception
     */
    public function get_lineitems($courseid, $resourceid, $ltilinkid, $tag, $limitfrom, $limitnum, $typeid) {
        global $DB;

        // Select all lti potential linetiems in site.
        $params = array('courseid' => $courseid);

        $sql = "SELECT i.*
                  FROM {grade_items} i
                 WHERE (i.courseid = :courseid)
               ORDER BY i.id";
        $lineitems = $DB->get_records_sql($sql, $params);

        // For each one, check the gbs id, and check that toolproxy matches. If so, add the
        // tag to the result and add it to a final results array.
        $lineitemstoreturn = array();
        $lineitemsandtotalcount = array();
        if ($lineitems) {
            foreach ($lineitems as $lineitem) {
                $gbs = $this->find_ltiservice_gradebookservice_for_lineitem($lineitem->id);
                if ($gbs && (!isset($tag) || (isset($tag) && $gbs->tag == $tag))
                        && (!isset($ltilinkid) || (isset($ltilinkid) && $gbs->ltilinkid == $ltilinkid))
                        && (!isset($resourceid) || (isset($resourceid) && $gbs->resourceid == $resourceid))) {
                    if (is_null($typeid)) {
                        if ($this->get_tool_proxy()->id == $gbs->toolproxyid) {
                            array_push($lineitemstoreturn, $lineitem);
                        }
                    } else {
                        if ($typeid == $gbs->typeid) {
                            array_push($lineitemstoreturn, $lineitem);
                        }
                    }
                } else if (($lineitem->itemtype == 'mod' && $lineitem->itemmodule == 'lti'
                        && !isset($resourceid) && !isset($tag)
                        && (!isset($ltilinkid) || (isset($ltilinkid)
                        && $lineitem->iteminstance == $ltilinkid)))) {
                    // We will need to check if the activity related belongs to our tool proxy.
                    $ltiactivity = $DB->get_record('lti', array('id' => $lineitem->iteminstance));
                    if (($ltiactivity) && (isset($ltiactivity->typeid))) {
                        if ($ltiactivity->typeid != 0) {
                            $tool = $DB->get_record('lti_types', array('id' => $ltiactivity->typeid));
                        } else {
                            $tool = lti_get_tool_by_url_match($ltiactivity->toolurl, $courseid);
                            if (!$tool) {
                                $tool = lti_get_tool_by_url_match($ltiactivity->securetoolurl, $courseid);
                            }
                        }
                        if (is_null($typeid)) {
                            if (($tool) && ($this->get_tool_proxy()->id == $tool->toolproxyid)) {
                                array_push($lineitemstoreturn, $lineitem);
                            }
                        } else {
                            if (($tool) && ($tool->id == $typeid)) {
                                array_push($lineitemstoreturn, $lineitem);
                            }
                        }
                    }
                }
            }
            $lineitemsandtotalcount = array();
            array_push($lineitemsandtotalcount, count($lineitemstoreturn));
            // Return the right array based in the paging parameters limit and from.
            if (($limitnum) && ($limitnum > 0)) {
                $lineitemstoreturn = array_slice($lineitemstoreturn, $limitfrom, $limitnum);
            }
            array_push($lineitemsandtotalcount, $lineitemstoreturn);
        }
        return $lineitemsandtotalcount;
    }

    /**
     * Fetch a lineitem instance.
     *
     * Returns the lineitem instance if found, otherwise false.
     *
     * @param string $courseid ID of course
     * @param string $itemid ID of lineitem
     * @param string $typeid
     *
     * @return \ltiservice_gradebookservices\local\resources\lineitem|bool
     */
    public function get_lineitem($courseid, $itemid, $typeid) {
        global $DB, $CFG;

        require_once($CFG->libdir . '/gradelib.php');
        $lineitem = \grade_item::fetch(array('id' => $itemid));
        if ($lineitem) {
            $gbs = $this->find_ltiservice_gradebookservice_for_lineitem($itemid);
            if (!$gbs) {
                // We will need to check if the activity related belongs to our tool proxy.
                $ltiactivity = $DB->get_record('lti', array('id' => $lineitem->iteminstance));
                if (($ltiactivity) && (isset($ltiactivity->typeid))) {
                    if ($ltiactivity->typeid != 0) {
                        $tool = $DB->get_record('lti_types', array('id' => $ltiactivity->typeid));
                    } else {
                        $tool = lti_get_tool_by_url_match($ltiactivity->toolurl, $courseid);
                        if (!$tool) {
                            $tool = lti_get_tool_by_url_match($ltiactivity->securetoolurl, $courseid);
                        }
                    }
                    if (is_null($typeid)) {
                        if (!(($tool) && ($this->get_tool_proxy()->id == $tool->toolproxyid))) {
                            return false;
                        }
                    } else {
                        if (!(($tool) && ($tool->id == $typeid))) {
                            return false;
                        }
                    }
                } else {
                    return false;
                }
            }
        }
        return $lineitem;
    }

    /**
     * Adds a decoupled (standalone) line item.
     * Decoupled line items are not directly attached to
     * an lti instance activity. They are recorded in
     * the gradebook as manual activities and the
     * gradebookservices is used to associate that manual column
     * with the tool in addition to storing the LTI related
     * metadata (resource id, tag).
     *
     * @param string $courseid ID of course
     * @param string $label label of lineitem
     * @param float $maximumscore maximum score of lineitem
     * @param string $baseurl
     * @param int|null $ltilinkid id of lti instance this line item is associated with
     * @param string|null $resourceid resource id of lineitem
     * @param string|null $tag tag of lineitem
     * @param int $typeid lti type to which this line item is associated with
     * @param int|null $toolproxyid lti2 tool proxy to which this lineitem is associated to
     *
     * @return int id of the created gradeitem
     */
    public function add_standalone_lineitem(string $courseid, string $label, float $maximumscore,
            string $baseurl, ?int $ltilinkid, ?string $resourceid, ?string $tag, int $typeid,
            int $toolproxyid = null) : int {
        global $DB;
        $params = array();
        $params['itemname'] = $label;
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $maximumscore;
        $params['grademin']  = 0;
        $item = new \grade_item(array('id' => 0, 'courseid' => $courseid));
        \grade_item::set_properties($item, $params);
        $item->itemtype = 'manual';
        $item->grademax = $maximumscore;
        $id = $item->insert('mod/ltiservice_gradebookservices');
        $DB->insert_record('ltiservice_gradebookservices', (object)array(
                'gradeitemid' => $id,
                'courseid' => $courseid,
                'toolproxyid' => $toolproxyid,
                'typeid' => $typeid,
                'baseurl' => $baseurl,
                'ltilinkid' => $ltilinkid,
                'resourceid' => $resourceid,
                'tag' => $tag
        ));
        return $id;
    }

    /**
     * Set a grade item.
     *
     * @param object $gradeitem Grade Item record
     * @param object $score Result object
     * @param int $userid User ID
     *
     * @throws \Exception
     * @deprecated since Moodle 3.7 MDL-62599 - please do not use this function any more.
     * @see gradebookservices::save_grade_item($gradeitem, $score, $userid)
     */
    public static function save_score($gradeitem, $score, $userid) {
        $service = new gradebookservices();
        $service->save_grade_item($gradeitem, $score, $userid);
    }

    /**
     * Saves a score received from the LTI tool.
     *
     * @param object $gradeitem Grade Item record
     * @param object $score Result object
     * @param int $userid User ID
     *
     * @throws \Exception
     */
    public function save_grade_item($gradeitem, $score, $userid) {
        global $DB, $CFG;
        $source = 'mod' . $this->get_component_id();
        if ($DB->get_record('user', array('id' => $userid)) === false) {
            throw new \Exception(null, 400);
        }
        require_once($CFG->libdir . '/gradelib.php');
        $finalgrade = null;
        $timemodified = null;
        if (isset($score->scoreGiven)) {
            $finalgrade = grade_floatval($score->scoreGiven);
            $max = 1;
            if (isset($score->scoreMaximum)) {
                $max = $score->scoreMaximum;
            }
            if (!is_null($max) && grade_floats_different($max, $gradeitem->grademax) && grade_floats_different($max, 0.0)) {
                // Rescale to match the grade item maximum.
                $finalgrade = grade_floatval($finalgrade * $gradeitem->grademax / $max);
            }
            if (isset($score->timestamp)) {
                $timemodified = strtotime($score->timestamp);
            } else {
                $timemodified = time();
            }
        }
        $feedbackformat = FORMAT_MOODLE;
        $feedback = null;
        if (!empty($score->comment)) {
            $feedback = $score->comment;
            $feedbackformat = FORMAT_PLAIN;
        }

        if ($gradeitem->is_manual_item()) {
            $result = $gradeitem->update_final_grade($userid, $finalgrade, null, $feedback, FORMAT_PLAIN, null, $timemodified);
        } else {
            if (!$grade = \grade_grade::fetch(array('itemid' => $gradeitem->id, 'userid' => $userid))) {
                $grade = new \grade_grade();
                $grade->userid = $userid;
                $grade->itemid = $gradeitem->id;
            }
            $grade->rawgrademax = $score->scoreMaximum;
            $grade->timemodified = $timemodified;
            $grade->feedbackformat = $feedbackformat;
            $grade->feedback = $feedback;
            $grade->rawgrade = $finalgrade;
            $status = grade_update($source, $gradeitem->courseid,
                $gradeitem->itemtype, $gradeitem->itemmodule,
                $gradeitem->iteminstance, $gradeitem->itemnumber, $grade);

            $result = ($status == GRADE_UPDATE_OK);
        }
        if (!$result) {
            debugging("failed to save score for item ".$gradeitem->id." and user ".$grade->userid);
            throw new \Exception(null, 500);
        }

    }

    /**
     * Get the json object representation of the grade item
     *
     * @param object $item Grade Item record
     * @param string $endpoint Endpoint for lineitems container request
     * @param string $typeid
     *
     * @return object
     */
    public static function item_for_json($item, $endpoint, $typeid) {

        $lineitem = new \stdClass();
        if (is_null($typeid)) {
            $typeidstring = "";
        } else {
            $typeidstring = "?type_id={$typeid}";
        }
        $lineitem->id = "{$endpoint}/{$item->id}/lineitem" . $typeidstring;
        $lineitem->label = $item->itemname;
        $lineitem->scoreMaximum = floatval($item->grademax);
        $gbs = self::find_ltiservice_gradebookservice_for_lineitem($item->id);
        if ($gbs) {
            $lineitem->resourceId = (!empty($gbs->resourceid)) ? $gbs->resourceid : '';
            $lineitem->tag = (!empty($gbs->tag)) ? $gbs->tag : '';
            if (isset($gbs->ltilinkid)) {
                $lineitem->resourceLinkId = strval($gbs->ltilinkid);
                $lineitem->ltiLinkId = strval($gbs->ltilinkid);
            }
        } else {
            $lineitem->tag = '';
            if (isset($item->iteminstance)) {
                $lineitem->resourceLinkId = strval($item->iteminstance);
                $lineitem->ltiLinkId = strval($item->iteminstance);
            }
        }

        return $lineitem;

    }

    /**
     * Get the object matching the JSON representation of the result.
     *
     * @param object  $grade              Grade record
     * @param string  $endpoint           Endpoint for lineitem
     * @param int  $typeid                The id of the type to include in the result url.
     *
     * @return object
     */
    public static function result_for_json($grade, $endpoint, $typeid) {

        if (is_null($typeid)) {
            $id = "{$endpoint}/results?user_id={$grade->userid}";
        } else {
            $id = "{$endpoint}/results?type_id={$typeid}&user_id={$grade->userid}";
        }
        $result = new \stdClass();
        $result->id = $id;
        $result->userId = $grade->userid;
        if (!empty($grade->finalgrade)) {
            $result->resultScore = floatval($grade->finalgrade);
            $result->resultMaximum = floatval($grade->rawgrademax);
            if (!empty($grade->feedback)) {
                $result->comment = $grade->feedback;
            }
            if (is_null($typeid)) {
                $result->scoreOf = $endpoint;
            } else {
                $result->scoreOf = "{$endpoint}?type_id={$typeid}";
            }
            $result->timestamp = date('c', $grade->timemodified);
        }
        return $result;
    }

    /**
     * Check if an LTI id is valid.
     *
     * @param string $linkid             The lti id
     * @param string  $course            The course
     * @param string  $toolproxy         The tool proxy id
     *
     * @return boolean
     */
    public static function check_lti_id($linkid, $course, $toolproxy) {
        global $DB;
        // Check if lti type is zero or not (comes from a backup).
        $sqlparams1 = array();
        $sqlparams1['linkid'] = $linkid;
        $sqlparams1['course'] = $course;
        $ltiactivity = $DB->get_record('lti', array('id' => $linkid, 'course' => $course));
        if ($ltiactivity->typeid == 0) {
            $tool = lti_get_tool_by_url_match($ltiactivity->toolurl, $course);
            if (!$tool) {
                $tool = lti_get_tool_by_url_match($ltiactivity->securetoolurl, $course);
            }
            return (($tool) && ($toolproxy == $tool->toolproxyid));
        } else {
            $sqlparams2 = array();
            $sqlparams2['linkid'] = $linkid;
            $sqlparams2['course'] = $course;
            $sqlparams2['toolproxy'] = $toolproxy;
            $sql = 'SELECT lti.*
                      FROM {lti} lti
                INNER JOIN {lti_types} typ ON lti.typeid = typ.id
                     WHERE lti.id = ?
                           AND lti.course = ?
                           AND typ.toolproxyid = ?';
            return $DB->record_exists_sql($sql, $sqlparams2);
        }
    }

    /**
     * Check if an LTI id is valid when we are in a LTI 1.x case
     *
     * @param string $linkid             The lti id
     * @param string  $course            The course
     * @param string  $typeid            The lti type id
     *
     * @return boolean
     */
    public static function check_lti_1x_id($linkid, $course, $typeid) {
        global $DB;
        // Check if lti type is zero or not (comes from a backup).
        $sqlparams1 = array();
        $sqlparams1['linkid'] = $linkid;
        $sqlparams1['course'] = $course;
        $ltiactivity = $DB->get_record('lti', array('id' => $linkid, 'course' => $course));
        if ($ltiactivity) {
            if ($ltiactivity->typeid == 0) {
                $tool = lti_get_tool_by_url_match($ltiactivity->toolurl, $course);
                if (!$tool) {
                    $tool = lti_get_tool_by_url_match($ltiactivity->securetoolurl, $course);
                }
                return (($tool) && ($typeid == $tool->id));
            } else {
                $sqlparams2 = array();
                $sqlparams2['linkid'] = $linkid;
                $sqlparams2['course'] = $course;
                $sqlparams2['typeid'] = $typeid;
                $sql = 'SELECT lti.*
                          FROM {lti} lti
                    INNER JOIN {lti_types} typ ON lti.typeid = typ.id
                         WHERE lti.id = ?
                               AND lti.course = ?
                               AND typ.id = ?';
                return $DB->record_exists_sql($sql, $sqlparams2);
            }
        } else {
            return false;
        }
    }

    /**
     * Updates the tag and resourceid values for a grade item coupled to an lti link instance.
     *
     * @param object $ltiinstance The lti instance to which the grade item is coupled to
     * @param string|null $resourceid The resourceid to apply to the lineitem. If empty string which will be stored as null.
     * @param string|null $tag The tag to apply to the lineitem. If empty string which will be stored as null.
     *
     */
    public static function update_coupled_gradebookservices(object $ltiinstance, ?string $resourceid, ?string $tag) : void {
        global $DB;

        if ($ltiinstance && $ltiinstance->typeid) {
            $gradeitem = $DB->get_record('grade_items', array('itemmodule' => 'lti', 'iteminstance' => $ltiinstance->id));
            if ($gradeitem) {
                $resourceid = (isset($resourceid) && empty(trim($resourceid))) ? null : $resourceid;
                $tag = (isset($tag) && empty(trim($tag))) ? null : $tag;
                $gbs = self::find_ltiservice_gradebookservice_for_lineitem($gradeitem->id);
                if ($gbs) {
                    $gbs->resourceid = $resourceid;
                    $gbs->tag = $tag;
                    $DB->update_record('ltiservice_gradebookservices', $gbs);
                } else {
                    $baseurl = lti_get_type_type_config($ltiinstance->typeid)->lti_toolurl;
                    $DB->insert_record('ltiservice_gradebookservices', (object)array(
                        'gradeitemid' => $gradeitem->id,
                        'courseid' => $gradeitem->courseid,
                        'typeid' => $ltiinstance->typeid,
                        'baseurl' => $baseurl,
                        'ltilinkid' => $ltiinstance->id,
                        'resourceid' => $resourceid,
                        'tag' => $tag
                    ));
                }
            }
        }
    }

    /**
     * Called when a new LTI Instance is added.
     *
     * @param object $lti LTI Instance.
     */
    public function instance_added(object $lti): void {
        self::update_coupled_gradebookservices($lti, $lti->lineitemresourceid ?? null, $lti->lineitemtag ?? null);
    }

    /**
     * Called when a new LTI Instance is updated.
     *
     * @param object $lti LTI Instance.
     */
    public function instance_updated(object $lti): void {
        self::update_coupled_gradebookservices($lti, $lti->lineitemresourceid ?? null, $lti->lineitemtag ?? null);
    }

    /**
     * Set the form data when displaying the LTI Instance form.
     *
     * @param object $defaultvalues Default form values.
     */
    public function set_instance_form_values(object $defaultvalues): void {
        $defaultvalues->lineitemresourceid = '';
        $defaultvalues->lineitemtag = '';
        if (is_object($defaultvalues) && $defaultvalues->instance) {
            $gbs = self::find_ltiservice_gradebookservice_for_lti($defaultvalues->instance);
            if ($gbs) {
                $defaultvalues->lineitemresourceid = $gbs->resourceid;
                $defaultvalues->lineitemtag = $gbs->tag;
            }
        }
    }

    /**
     * Deletes orphaned rows from the 'ltiservice_gradebookservices' table.
     *
     * Sometimes, if a gradebook entry is deleted and it was a lineitem
     * the row in the table ltiservice_gradebookservices can become an orphan
     * This method will clean these orphans. It will happens based on a task
     * because it is not urgent and we don't want to slow the service
     */
    public static function delete_orphans_ltiservice_gradebookservices_rows() {
        global $DB;

        $sql = "DELETE
                  FROM {ltiservice_gradebookservices}
                 WHERE gradeitemid NOT IN (SELECT id
                                             FROM {grade_items} gi)";
        $DB->execute($sql);
    }

    /**
     * Check if a user can be graded in a course
     *
     * @param int $courseid The course
     * @param int $userid The user
     * @return bool
     */
    public static function is_user_gradable_in_course($courseid, $userid) {
        global $CFG;

        $gradableuser = false;
        $coursecontext = \context_course::instance($courseid);
        if (is_enrolled($coursecontext, $userid, '', false)) {
            $roles = get_user_roles($coursecontext, $userid);
            $gradebookroles = explode(',', $CFG->gradebookroles);
            foreach ($roles as $role) {
                foreach ($gradebookroles as $gradebookrole) {
                    if ($role->roleid === $gradebookrole) {
                        $gradableuser = true;
                    }
                }
            }
        }

        return $gradableuser;
    }

    /**
     * Find the right element in the ltiservice_gradebookservice table for an lti instance
     *
     * @param string $instanceid The LTI module instance id
     * @return object gradebookservice for this line item
     */
    public static function find_ltiservice_gradebookservice_for_lti($instanceid) {
        global $DB;

        if ($instanceid) {
            $gradeitem = $DB->get_record('grade_items', array('itemmodule' => 'lti', 'iteminstance' => $instanceid));
            if ($gradeitem) {
                return self::find_ltiservice_gradebookservice_for_lineitem($gradeitem->id);
            }
        }
    }

    /**
     * Find the right element in the ltiservice_gradebookservice table for a lineitem
     *
     * @param string $lineitemid The lineitem (gradeitem) id
     * @return object gradebookservice if it exists
     */
    public static function find_ltiservice_gradebookservice_for_lineitem($lineitemid) {
        global $DB;
        if ($lineitemid) {
            return $DB->get_record('ltiservice_gradebookservices',
                    array('gradeitemid' => $lineitemid));
        }
    }

    /**
     * Validates specific ISO 8601 format of the timestamps.
     *
     * @param string $date The timestamp to check.
     * @return boolean true or false if the date matches the format.
     *
     */
    public static function validate_iso8601_date($date) {
        if (preg_match('/^([\+-]?\d{4}(?!\d{2}\b))((-?)((0[1-9]|1[0-2])' .
                '(\3([12]\d|0[1-9]|3[01]))?|W([0-4]\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\d|[12]\d{2}|3([0-5]\d|6[1-6])))' .
                '([T\s]((([01]\d|2[0-3])((:?)[0-5]\d)?|24\:?00)([\.,]\d+(?!:))?)?(\17[0-5]\d([\.,]\d+)?)' .
                '?([zZ]|([\+-])([01]\d|2[0-3]):?([0-5]\d)?)?)?)?$/', $date) > 0) {
            return true;
        } else {
            return false;
        }
    }
}

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
 * Class for doing reports for competency.
 *
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot.'/user/lib.php');

use core_user;
use context;
use core_competency\api as core_competency_api;
use core_competency\course_competency;
use core_competency\course_module_competency;
use core_competency\competency;
use core_competency\evidence;
use core_competency\template;
use core_competency\plan;
use core_competency\template_competency;
use core_competency\competency_framework;
use core_competency\user_competency;
use core_competency\user_competency_plan;
use core_tag_area;
use core_tag_tag;
use report_lpmonitoring\report_competency_config;
use report_lpmonitoring\event\user_competency_resetted;
use report_lpmonitoring\external;
use stdClass;
use Exception;
use required_capability_exception;
use moodle_exception;

/**
 * Class for doing reports for competency.
 *
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /** @var boolean iscmcompetencygradingenabled  **/
    static protected $iscmcompetencygradingenabled = false;

    /** @var boolean isdisplayratingenabled  **/
    static protected $isdisplayratingenabled = false;

    /**
     * Get scales from frameworkid.
     *
     * @param int $frameworkid The framework ID
     *
     * @return array Scale info
     */
    public static function get_scales_from_framework($frameworkid) {
        global $DB;
        // Read the framework.
        $framework = core_competency_api::read_framework($frameworkid);
        $scales = array();

        // Get the scale of the framework.
        $frameworkscale = $framework->get_scale();
        $scales[$frameworkscale->id] = array('id' => $frameworkscale->id, 'name' => $frameworkscale->name);

        $sql = "SELECT s.id, s.name
                  FROM {scale} s
                  JOIN {" . competency::TABLE . "} c
                    ON c.scaleid = s.id
                 WHERE c.competencyframeworkid = :frameworkid
              ORDER BY s.name ASC";

        // Extracting the results.
        $records = $DB->get_recordset_sql($sql, array('frameworkid' => $frameworkid));
        foreach ($records as $record) {
            $scales[$record->id] = array('id' => $record->id, 'name' => $record->name);
        }
        $records->close();

        return (array) (object) $scales;
    }

    /**
     * Get scales from templateid.
     *
     * @param int $templateid The template ID
     *
     * @return array Scale info
     */
    public static function get_scales_from_templateid($templateid) {
        // Read the template.
        $competencies = core_competency_api::list_competencies_in_template($templateid);
        $scales = array();
        foreach ($competencies as $competency) {
            $framework = $competency->get_framework();
            $scale = $competency->get_scale();
            if (isset($scale)) {
                $scaleid = $scale->id;
                $scalename = $scale->name;
            } else {
                $scaleid = $framework->get('scaleid');
                $scalename = $framework->get_scale()->name;
            }

            $scales[$scaleid] = array('frameworkid' => $framework->get('id'), 'scalename' => $scalename);
        }

        return $scales;
    }

    /**
     * Read the configuration associated to a competency framework and a scale. Return a record.
     *
     * @param int $frameworkid The id of the competency framework.
     * @param int $scaleid The id of the scale.
     *
     * @return report_competency_config
     */
    public static function read_report_competency_config($frameworkid, $scaleid) {

        // User has necessary capapbility if he can read the framework.
        $framework = core_competency_api::read_framework($frameworkid);

        $config = report_competency_config::read_framework_scale_config($frameworkid, $scaleid);
        if (!$config) {
            $record = new stdClass();
            $record->competencyframeworkid = $frameworkid;
            $record->scaleid = $scaleid;
            $config = new report_competency_config(0, $record);
            $config->set_default_scaleconfiguration();
        } else {
            $config->set_default_scaleconfiguration();
        }

        return $config;
    }

    /**
     * Create the configuration associated to a competency framework and a scale.
     *
     * @param stdClass $record Record containing all the data for an instance of the class.
     *
     * @return report_competency_config
     */
    public static function create_report_competency_config(stdClass $record) {
        global $DB;

        // Check the permissions before accessing configuration.
        $framework = new competency_framework($record->competencyframeworkid);
        if (!$framework->can_manage()) {
            throw new required_capability_exception($framework->get_context(), 'moodle/competency:competencymanage',
                'nopermissions', '');
        }

        if ($DB->record_exists(report_competency_config::TABLE,
                array('competencyframeworkid' => $record->competencyframeworkid, 'scaleid' => $record->scaleid))) {
            throw new exception('Can not create: configuration already exist');
        }

        $config = new report_competency_config(0, $record);
        $config->create();

        return $config;
    }

    /**
     * Update the configuration associated to a competency framework and a scale.
     *
     * @param stdClass $record The new details for the configuration.
     *                         Note - must contain an id that points to the configuration to update.
     *
     * @return boolean
     */
    public static function update_report_competency_config($record) {
        global $DB;

        // Check the permissions before accessing configuration.
        $framework = new competency_framework($record->competencyframeworkid);
        if (!$framework->can_manage()) {
            throw new required_capability_exception($framework->get_context(), 'moodle/competency:competencymanage',
                'nopermissions', '');
        }

        // Check for existing record.
        $recordconfig = $DB->get_record(report_competency_config::TABLE,
                array('competencyframeworkid' => $record->competencyframeworkid, 'scaleid' => $record->scaleid));

        if (!$recordconfig) {
            throw new Exception('Can not update: configuration does not exist');
        }

        $config = new report_competency_config($recordconfig->id);
        $config->from_record($record);

        // OK - all set.
        $result = $config->update();

        return $result;
    }

    /**
     * Delete the configuration associated to a competency framework and a scale.
     *
     * @param int $competencyframeworkid The cometency framework id.
     * @param int $scaleid The scale id.
     *
     * @return boolean
     */
    public static function delete_report_competency_config($competencyframeworkid, $scaleid = null) {
        global $DB;

        // Check the permissions before accessing configuration.
        if ($DB->record_exists(competency_framework::TABLE, array('id' => $competencyframeworkid))) {
            $framework = new competency_framework($competencyframeworkid);
            if (!$framework->can_manage()) {
                throw new required_capability_exception($framework->get_context(), 'moodle/competency:competencymanage',
                'nopermissions', '');
            }
        }

        $params = array('competencyframeworkid' => $competencyframeworkid);
        if ($scaleid != null) {
            $params['scaleid'] = $scaleid;
        }

        $result = $DB->delete_records(report_competency_config::TABLE, $params);

        return $result;
    }

    /**
     * Get learning plans from templateid.
     *
     * @param int $templateid The template ID
     * @param string $query The search query
     * @param array $scalesvalues scales values filter
     * @param boolean $scalefilterin Apply the scale filters on grade in plan, course or course module
     * @param string $scalesortorder Order by rating number ASC or DESC
     * @param boolean $withcomments Only plans with comments
     * @param boolean $withplans Only students with at least two plans
     * @return array( array(
     *                      'profileimage' => string,
     *                      'fullname' => string,
     *                      'email' => string,
     *                      'username' => string,
     *                      'userid' => int,
     *                      'planid' => int,
     *                      )
     *              )
     */
    public static function search_users_by_templateid($templateid, $query, $scalesvalues = array(), $scalefilterin = '',
            $scalesortorder = "ASC", $withcomments = false, $withplans = false) {
        global $CFG, $DB;
        if (!in_array(strtolower($scalesortorder), array('asc', 'desc'))) {
            throw new \coding_exception('Sort order must be ASC or DESC');
        }

        if ($scalefilterin == 'coursemodule' && !self::is_cm_comptency_grading_enabled()) {
            throw new \coding_exception('Grading competency in course module is disabled');
        }

        $template = core_competency_api::read_template($templateid);
        $context = $template->get_context();

        $userfieldsapi = \core_user\fields::for_identity($context, false)->with_userpic();
        $fields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
        $extrasearchfields = $userfieldsapi->get_required_fields([\core_user\fields::PURPOSE_IDENTITY]);

        list($wheresql, $whereparams) = users_search_sql($query, 'u', true, $extrasearchfields);
        list($sortsql, $sortparams) = users_order_by_sql('u', $query, $context);

        // Group scales values by scaleid.
        $scalefilter = array();
        if (!empty($scalesvalues)) {
            foreach ($scalesvalues as $scale) {
                $scalefilter[$scale['scaleid']][] = $scale['scalevalue'];
            }
        }
        $i = 1;
        $paramsfilter = array();
        $sqlfilterin = '';
        $sqlfilterinforplan = '';
        $sqlscalefilter = '';
        // Build scale filters SQL and params by final rating or by course rating.
        foreach ($scalefilter as $scaleid => $scalevalues) {
            list($insqlframework, $params1) = $DB->get_in_or_equal($scalevalues,
                    SQL_PARAMS_NAMED, 'gradeframework');
            list($insqlcompetency, $params2) = $DB->get_in_or_equal($scalevalues,
                    SQL_PARAMS_NAMED, 'gradecompetency');
            $querykeyname1 = 'scaleid1' . $i;
            $querykeyname2 = 'scaleid2' . $i;
            $or = ($i > 1) ? ' OR ' : '';
            $sqlfilterin .= $or . "(cf.scaleid = :$querykeyname1 AND ucc.grade $insqlframework AND c.scaleid IS NULL)
                            OR (c.scaleid = :$querykeyname2 AND ucc.grade $insqlcompetency)";

            $queryparams = array($querykeyname1 => $scaleid) + array($querykeyname2 => $scaleid);
            $paramsfilter = $paramsfilter + $params1 + $params2 + $queryparams;

            // If scale values in plan, we should build the "IN" SQL for both usercomp and usercompplan.
            if (!$scalefilterin) {
                list($insqlframework, $params1) = $DB->get_in_or_equal($scalevalues,
                    SQL_PARAMS_NAMED, 'gradeframework');
                list($insqlcompetency, $params2) = $DB->get_in_or_equal($scalevalues,
                    SQL_PARAMS_NAMED, 'gradecompetency');
                $querykeyname3 = 'scaleid3' . $i;
                $querykeyname4 = 'scaleid4' . $i;

                $sqlfilterinforplan .= $or . "(cf.scaleid = :$querykeyname3 AND ucp.grade $insqlframework AND c.scaleid IS NULL)
                            OR (c.scaleid = :$querykeyname4 AND ucp.grade $insqlcompetency)";

                $queryparams = array($querykeyname3 => $scaleid) + array($querykeyname4 => $scaleid);
                $paramsfilter = $paramsfilter + $params1 + $params2 + $queryparams;
            }
            $i++;
        }

        if ($sqlfilterin != '') {
            // Depending in filterbycourse param, we choose which table to use.
            if ($scalefilterin == 'course') {
                // We have to check if users are enroled in course and competency is linked to the course.
                $sqlscalefilter = "SELECT useridentifier,
                                          gradecount,
                                          tempid,
                                          $fields,
                                          p.id AS planid,
                                          p.name AS planname
                                    FROM  (
                                            (SELECT ucc.userid AS useridentifier, Count(ucc.grade) gradecount,
                                                    tc.templateid AS tempid
                                               FROM {" . \core_competency\template_competency::TABLE . "} tc
                                               JOIN {" . \core_competency\course_competency::TABLE . "} cc
                                                    ON tc.competencyid = cc.competencyid AND tc.templateid = :templateid
                                               JOIN {" . \core_competency\user_competency_course::TABLE . "} ucc
                                                    ON ucc.competencyid = tc.competencyid AND cc.courseid = ucc.courseid
                                               JOIN {user_enrolments} ue
						    ON ue.userid = ucc.userid
                                                    AND ue.status = :active
					       JOIN {enrol} e
						    ON ue.enrolid = e.id AND e.courseid = ucc.courseid
                                                    AND e.status = :enabled
                                               JOIN {" . \core_competency\competency::TABLE . "} c
                                                    ON c.id = ucc.competencyid
                                               JOIN {" . \core_competency\competency_framework::TABLE . "} cf
                                                    ON cf.id = c.competencyframeworkid
                                             WHERE ($sqlfilterin)
                                          GROUP BY useridentifier)
                                        ) usergrade";

                $paramsfilter += array('active' => ENROL_USER_ACTIVE);
                $paramsfilter += array('enabled' => ENROL_INSTANCE_ENABLED);
            } else if ($scalefilterin == 'coursemodule') {
                // We have to check if users are enroled in course and competency is linked to the course module.
                $sqlscalefilter = "SELECT useridentifier,
                                          gradecount,
                                          tempid,
                                          $fields,
                                          p.id AS planid,
                                          p.name AS planname
                                    FROM  (
                                            (SELECT ucc.userid AS useridentifier, Count(ucc.grade) gradecount,
                                                    tc.templateid AS tempid
                                               FROM {" . \core_competency\template_competency::TABLE . "} tc
                                               JOIN {" . \core_competency\course_module_competency::TABLE . "} cc
                                                    ON tc.competencyid = cc.competencyid AND tc.templateid = :templateid
                                               JOIN {" . \tool_cmcompetency\user_competency_coursemodule::TABLE . "} ucc
                                                    ON ucc.competencyid = tc.competencyid AND cc.cmid = ucc.cmid
                                               JOIN {course_modules} cm
                                                    ON cm.id = ucc.cmid
                                               JOIN {user_enrolments} ue
						    ON ue.userid = ucc.userid
                                                    AND ue.status = :active
					       JOIN {enrol} e
						    ON ue.enrolid = e.id AND e.courseid = cm.course
                                                    AND e.status = :enabled
                                               JOIN {" . \core_competency\competency::TABLE . "} c
                                                    ON c.id = ucc.competencyid
                                               JOIN {" . \core_competency\competency_framework::TABLE . "} cf
                                                    ON cf.id = c.competencyframeworkid
                                             WHERE ($sqlfilterin)
                                          GROUP BY useridentifier)
                                        ) usergrade";

                $paramsfilter += array('active' => ENROL_USER_ACTIVE);
                $paramsfilter += array('enabled' => ENROL_INSTANCE_ENABLED);
            } else {
                // SQL for usercomp and completed plans.
                $sqlscalefilter = "SELECT useridentifier,
                                          gradecount,
                                          tempid,
                                          $fields,
                                          p.id AS planid,
                                          p.name AS planname,
                                          p.status as planstatus,
                                          tableusercomp
                                    FROM  (
                                            (SELECT ucc.userid AS useridentifier, Count(ucc.grade) gradecount,
                                                    tc.templateid AS tempid, 'user_competency' AS tableusercomp
                                               FROM {" . \core_competency\template_competency::TABLE . "} tc
                                               JOIN {" . \core_competency\user_competency::TABLE . "} ucc
                                                    ON ucc.competencyid = tc.competencyid AND tc.templateid = :templateid
                                               JOIN {" . \core_competency\competency::TABLE . "} c
                                                    ON c.id = ucc.competencyid
                                               JOIN {" . \core_competency\competency_framework::TABLE . "} cf
                                                    ON cf.id = c.competencyframeworkid
                                             WHERE ($sqlfilterin)
                                          GROUP BY useridentifier)
                                          UNION
                                           (SELECT ucp.userid AS useridentifier, Count(ucp.grade) gradecount,
                                                   tc.templateid AS tempid, 'user_competency_plan' AS tableusercomp
                                              FROM {" . \core_competency\template_competency::TABLE . "} tc
                                              JOIN {" . \core_competency\plan::TABLE . "} p
                                                   ON p.templateid = tc.templateid AND tc.templateid = :templateid2
                                              JOIN {" . \core_competency\user_competency_plan::TABLE . "} ucp
                                                   ON ucp.competencyid = tc.competencyid AND p.id = ucp.planid
                                              JOIN {" . \core_competency\competency::TABLE . "} c
                                                   ON c.id = ucp.competencyid
                                              JOIN {" . \core_competency\competency_framework::TABLE . "} cf
                                                   ON cf.id = c.competencyframeworkid
                                             WHERE ($sqlfilterinforplan)
                                          GROUP BY useridentifier)
                                        ) usergrade";
            }
            // We sort by rating number.
            $sort = "gradecount $scalesortorder,$sortsql";
            $sql = "$sqlscalefilter
                    JOIN {" . \core_competency\plan::TABLE . "} p
                         ON usergrade.useridentifier = p.userid
                         AND p.templateid = usergrade.tempid
                    JOIN {user} u ON u.id = p.userid
                   WHERE $wheresql
                ORDER BY $sort";
        } else {
            // If no scale filter defined.
            $sql = "SELECT $fields, p.id as planid, p.name as planname
                  FROM {" . \core_competency\plan::TABLE . "} p
                  JOIN {user} u ON u.id = p.userid
                 WHERE p.templateid = :templateid
                       AND $wheresql
              ORDER BY $sortsql";
        }

        $params = $paramsfilter + $whereparams + $sortparams;
        $params += array('templateid' => $template->get('id')) + array('templateid2' => $template->get('id'));
        $result = $DB->get_recordset_sql($sql, $params);

        $users = array();
        foreach ($result as $key => $user) {
            // Make sure the ratings from user_competency table are not returned
            // if the plan is completed.
            if (isset($user->planstatus) && $user->planstatus == \core_competency\plan::STATUS_COMPLETE) {
                if ($user->tableusercomp == 'user_competency') {
                    continue;
                }
            }

            // Return only plans with comments.
            // We cannot filter easily the comments by sql so we do it afterwards.
            if ($withcomments) {
                $plan = new plan($user->planid);
                $nbcomments = $plan->get_comment_object()->count();
                if ($nbcomments == 0) {
                    continue;
                }
            } else {
                $nbcomments = 0;
            }

            // Return plans from which students have at least two plans.
            if ($withplans) {
                $allplans = core_competency_api::list_user_plans($user->id);

                $nbplans = count($allplans);
                if ($nbplans <= 1) {
                    continue;
                }
            } else {
                $nbplans = 0;
            }

            // Add user picture.
            $userplan = array();
            $userplan['profileimage'] = new \user_picture($user);
            $userplan['fullname'] = fullname($user);
            $userplan['userid'] = $user->id;
            $userplan['planname'] = $user->planname;
            $userplan['planid'] = $user->planid;
            $userplan['nbrating'] = (isset($user->gradecount)) ? $user->gradecount : 0;
            $userplan['nbcomments'] = $nbcomments;
            $userplan['nbplans'] = $nbplans;
            $usercontext = \context_user::instance($user->id);
            // Build identity fields.
            if (!empty($extrasearchfields) && has_capability('moodle/site:viewuseridentity', $usercontext)) {
                foreach ($extrasearchfields as $field) {
                    $userplan[$field] = $user->$field;
                }
            }
            $users[$user->id] = $userplan;
        }
        $result->close();
        return $users;
    }

    /**
     * Get scales proficient values from frameworkid.
     *
     * @param int $frameworkid The framework ID
     * @param int $scaleid The scale ID
     *
     * @return array Scale information
     */
    public static function get_scale_configuration_other_info($frameworkid, $scaleid) {
        global $DB;

        $scaleotherinfo = array();
        $scaleconfigurations = '';

        // Get scale configuration from competency first or framework second.
        $sql = "SELECT c.scaleconfiguration
                  FROM {" . competency::TABLE . "} c
                 WHERE c.competencyframeworkid = :frameworkid
                   AND c.scaleid = :scaleid";

        // Extracting the results.
        $records = $DB->get_recordset_sql($sql, array('frameworkid' => $frameworkid, 'scaleid' => $scaleid));
        foreach ($records as $record) {
            $scaleconfigurations = $record->scaleconfiguration;
        }
        if (empty($scaleconfigurations)) {
            // Read the framework.
            $framework = core_competency_api::read_framework($frameworkid);
            $scaleconfigurations = $framework->get('scaleconfiguration');
        }

        $scaleconfigurations = json_decode($scaleconfigurations);
        if (is_array($scaleconfigurations)) {
            // The first element of the array contains the scale ID.
            $scaleinfo = array_shift($scaleconfigurations);
        }

        // Get scale items.
        $scale = \grade_scale::fetch(array('id' => $scaleid));
        $scale->load_items();
        $scaleitems = $scale->scale_items;

        // Build scale other info.
        foreach ($scaleitems as $key => $value) {
            $proficient = false;
            foreach ($scaleconfigurations as $scaleconfiguration) {
                if ($key + 1 == $scaleconfiguration->id) {
                    if (isset($scaleconfiguration->proficient) && $scaleconfiguration->proficient) {
                        $proficient = true;
                    }
                    break;
                }
            }
            $scaleotherinfo[$key] = ['name' => $value, 'proficient' => $proficient];
        }

        return $scaleotherinfo;
    }

    /**
     * Read the plan information by plan ID or
     * template ID and return if possible, the previous and next plan having
     * the same template.
     *
     * @param int $planid The plan ID
     * @param int $templateid The template ID
     * @param array $scalesvalues The Scales values filter
     * @param boolean $scalefilterin Apply the scale filters on grade in plan, course or course module
     * @param string $sortorder Scale sort order
     * @param int $tagid The tag ID
     * @param int $withcomments Only plans with comments
     * @param int $withplans Only students with at least two plans
     * @return array((object) array(
     *                            'current' => \core_competency\plan,
     *                            'previous' => \stdClass
     *                            'next' => \stdClass
     *                        ))
     */
    public static function read_plan($planid = null, $templateid = null, $scalesvalues = array(), $scalefilterin = '',
            $sortorder = 'ASC', $tagid = null, $withcomments = false, $withplans = false) {

        if (empty($planid) && empty($templateid) && empty($tagid)) {
            throw new coding_exception('A plan ID and/or a template ID and/or a tag ID must be specified');
        }

        $currentplan = null;
        $prevplan = null;
        $nextplan = null;
        $userplans = [];
        // Get the current plan depending on the values passed in parameter.
        $currentplanid = $planid;
        if ( !empty($templateid) || !empty($tagid) ) {
            if (!empty($tagid)) {
                $userplans = self::search_plans_with_tag($tagid, $withcomments);
            } else {
                $userplans = array_values(self::search_users_by_templateid($templateid , '', $scalesvalues, $scalefilterin,
                        $sortorder, $withcomments, $withplans));
            }
            $currentindex = null;
            // We throw an exception if no plans are found.
            if (empty($userplans)) {
                throw new \moodle_exception('emptytemplate', 'report_lpmonitoring');
            } else {
                // When the plan ID is not specified, we set the first plan as the current one.
                if (empty($planid)) {
                    $currentindex = 0;
                    $currentplanid = $userplans[$currentindex]['planid'];
                } else {
                    // Search for the current plan in the list of plans based on the template.
                    foreach ($userplans as $index => $userplan) {
                        if ($userplan['planid'] == $planid) {
                            $currentindex = $index;
                            break;
                        }
                    }
                }

                // Get the previous and next plans based on the current plan index.
                if (isset($currentindex)) {
                    if (isset($userplans[$currentindex - 1])) {
                        $prevplan = (object) $userplans[$currentindex - 1];
                        $prevplan->tagid = $tagid;
                    }
                    if (isset($userplans[$currentindex + 1])) {
                        $nextplan = (object) $userplans[$currentindex + 1];
                        $nextplan->tagid = $tagid;
                    }
                }
            }
        }

        if (!empty($currentplanid)) {
            $plan = new plan($currentplanid);
            if (!$plan->can_read()) {
                $currentuserfullname = $userplans[$currentindex]['fullname'];
                throw new moodle_exception('nopermissionsplanview', 'report_lpmonitoring', '', $currentuserfullname);
            }
            $currentplan = core_competency_api::read_plan($currentplanid);
        }

        return (object) array(
            'current' => $currentplan,
            'previous' => $prevplan,
            'next' => $nextplan,
            'fullnavigation' => $userplans
        );
    }

    /**
     * Get comptency information for lpmonitoring report.
     *
     * @param int $userid User id.
     * @param int $competencyid Competency id.
     * @param int $planid Plan id.
     * @return \stdClass The record of competency detail
     */
    public static function get_competency_detail($userid, $competencyid, $planid) {
        global $DB;

        $competencydetails = new \stdClass();

        $plancompetency = core_competency_api::get_plan_competency($planid, $competencyid);
        $competency = $plancompetency->competency;

        // User has necessary capapbility if he can read the framework.
        $framework = core_competency_api::read_framework($competency->get('competencyframeworkid'));

        $competencydetails->userid = $userid;
        $competencydetails->planid = $planid;
        $competencydetails->competency = $competency;
        $competencydetails->framework = $framework;

        // Find de scale configuration associated to the competency.
        $scaleid = $competency->get('scaleid');
        $scaleconfig = $competency->get('scaleconfiguration');
        if ($scaleid === null) {
            $scaleid = $framework->get('scaleid');
            $scaleconfig = $framework->get('scaleconfiguration');
        }

        // Remove the scale ID from the config.
        $scaleconfig = json_decode($scaleconfig);
        if (!is_array($scaleconfig)) {
            throw new coding_exception('Unexpected scale configuration.');
        }
        array_shift($scaleconfig);
        $competencydetails->scaleconfig = $scaleconfig;

        // Find the scale infos.
        $scale = \grade_scale::fetch(array('id' => $scaleid));
        $scale = $scale->load_items();
        $newscale = array();
        foreach ($scale as $key => $value) {
            $newscale[$key + 1] = $value;
        }
        $competencydetails->scale = $newscale;

        // Find de scale configuration for the report.
        $reportscaleconfig = self::read_report_competency_config($framework->get('id'), $scaleid);
        $reportscaleconfig = json_decode($reportscaleconfig->get('scaleconfiguration'));
        if (!is_array($reportscaleconfig)) {
            throw new coding_exception('Unexpected report scale configuration.');
        }
        $competencydetails->reportscaleconfig = $reportscaleconfig;

        // Find rate for the competency.
        $competencydetails->usercompetency = $plancompetency->usercompetency;
        $competencydetails->usercompetencyplan = $plancompetency->usercompetencyplan;

        // Find the prior learning evidence linked to the competency.
        $competencydetails->userevidences = array();
        $evidences = core_competency_api::list_evidence($userid, $competencyid);
        $sql = "SELECT ue.*
                  FROM {competency_userevidence} ue
                  JOIN {competency_userevidencecomp} uec ON (ue.id = uec.userevidenceid)
                  WHERE ue.userid = ?
                  AND uec.competencyid = ?";
        $competencydetails->userevidences = $DB->get_records_sql($sql, array($userid, $competencyid));

        $courses = course_competency::get_courses_with_competency_and_user($competencyid, $userid);

        $competencydetails->courses = array();
        $coursesids = [];
        foreach ($courses as $course) {
            $courseinfo = new \stdClass();
            $courseinfo->course = $course;

            // Find rating in course.
            $courseinfo->usecompetencyincourse = core_competency_api::get_user_competency_in_course($course->id, $userid,
                    $competencyid);

            // Find most recent course evidences.
            $sort = 'timecreated';
            $order = 'DESC';
            $courseinfo->courseevidences = core_competency_api::list_evidence_in_course($userid, $course->id, $competencyid,
                    $sort, $order);

            // Find litteral note.
            $gradeitem = \grade_item::fetch_course_item($course->id);
            $gradegrade = new \grade_grade(array('itemid' => $gradeitem->id, 'userid' => $userid));
            $courseinfo->gradetxt = grade_format_gradevalue($gradegrade->finalgrade, $gradeitem, true, GRADE_DISPLAY_TYPE_LETTER);

            // Find modules evaluations.
            if (self::is_cm_comptency_grading_enabled()) {
                $modules = course_module_competency::list_course_modules($competencyid, $course->id);
                $courseinfo->modules = array();
                foreach ($modules as $cmid) {
                    $courseinfo->modules[] = \tool_cmcompetency\api::get_user_competency_in_coursemodule($cmid,
                        $userid, $competencyid);
                }
            }
            $competencydetails->courses[] = $courseinfo;
            $coursesids[] = $course->id;
        }

        $competencydetails->cms = array();
        if (self::is_cm_comptency_grading_enabled()) {
            $cms = \tool_cmcompetency\api::list_coursesmodules_using_competency($competencyid);

            foreach ($cms as $cm) {
                $cminfo = new \stdClass();
                $cmid = $cm;
                $cminfo->cmid = $cmid;
                $cm = get_coursemodule_from_id('', $cmid, 0, true);
                if (!in_array($cm->course, $coursesids)) {
                    continue;
                }

                // Find rating in course module.
                $cminfo->usecompetencyincm = \tool_cmcompetency\api::get_user_competency_in_coursemodule($cmid, $userid,
                        $competencyid);

                // Find most recent course module evidences.
                $sort = 'timecreated';
                $order = 'DESC';
                $cminfo->cmevidences = \tool_cmcompetency\api::list_evidence_in_coursemodule($userid, $cmid, $competencyid,
                        $sort, $order);

                // Calculate grade if exist.
                $cminfo->cm = $cm;
                $gradeitems = \grade_item::fetch_all(array('itemtype' => 'mod', 'itemmodule' => $cm->modname,
                        'iteminstance' => $cm->instance, 'courseid' => $cm->course));
                if (!empty($gradeitems)) {
                    $gradeitem = reset($gradeitems);
                    $gradegrade = new \grade_grade(array('itemid' => $gradeitem->id, 'userid' => $userid));
                    $cminfo->grade = grade_format_gradevalue($gradegrade->finalgrade, $gradeitem, true, GRADE_DISPLAY_TYPE_LETTER);
                } else {
                    $cminfo->grade = '-';
                }

                $competencydetails->cms[] = $cminfo;
            }
        }

        return $competencydetails;
    }

    /**
     * Get competency statistics for lpmonitoring report.
     *
     * @param int $competencyid Competency id.
     * @param int $templateid Template id.
     * @return \stdClass The record of competency statistics.
     */
    public static function get_competency_statistics($competencyid, $templateid) {
        // Prepare some data for competency stats (scale, colors configuration, ...).
        $competencystatistics = self::prepare_competency_stats_data($competencyid, $templateid);

        // Get plans by template.
        $userplans = plan::get_records_for_template($templateid);

        // Find rate for each user in the plan for the the competency.
        $competencystatistics->listusers = array();
        foreach ($userplans as $userplan) {
            $user = new stdClass();
            $user->userinfo = core_user::get_user($userplan->get('userid'), '*', \MUST_EXIST);
            // Throw an exception if user can not read the user competency.
            if (!user_competency::can_read_user($userplan->get('userid'))) {
                $userfullname = fullname($user->userinfo);
                throw new moodle_exception('nopermissionsusercompetencyview', 'report_lpmonitoring', '', $userfullname);
            }
            if ($userplan->get('status') == plan::STATUS_COMPLETE &&
                    !self::has_records_for_competency_user_in_plan($userplan->get('id'), $competencyid)) {
                continue;
            }

            $plancompetency = core_competency_api::get_plan_competency($userplan->get('id'), $competencyid);
            $user->usercompetency = $plancompetency->usercompetency;
            $user->usercompetencyplan = $plancompetency->usercompetencyplan;
            $competencystatistics->listusers[] = $user;
        }

        return $competencystatistics;
    }

    /**
     * Get competency statistics in course.
     *
     * @param int $competencyid Competency id.
     * @param int $templateid Template id.
     * @return \stdClass The record of competency statistics.
     */
    public static function get_competency_statistics_in_course($competencyid, $templateid) {
        // Prepare some data for competency stats (scale, colors configuration, ...).
        $competencystatistics = self::prepare_competency_stats_data($competencyid, $templateid);

        // Get course competency by template.
        $userplans = plan::get_records_for_template($templateid);

        // Find rate for each user in the plan for the the competency.
        $competencystatistics->listratings = array();
        foreach ($userplans as $plan) {
            $userid = $plan->get('userid');
            $courses = course_competency::get_courses_with_competency_and_user($competencyid, $userid);

            foreach ($courses as $course) {
                // Find ratings in course.
                $ucc = core_competency_api::get_user_competency_in_course($course->id, $userid, $competencyid);
                $competencystatistics->listratings[] = $ucc;
            }
        }

        return $competencystatistics;
    }

    /**
     * Get competency statistics in course modules.
     *
     * @param int $competencyid Competency id.
     * @param int $templateid Template id.
     * @return \stdClass The record of competency statistics.
     */
    public static function get_competency_statistics_in_coursemodules($competencyid, $templateid) {
        if (!self::is_cm_comptency_grading_enabled()) {
            throw new \coding_exception('Grading competency in course module is disabled');
        }

        // Prepare some data for competency stats (scale, colors configuration, ...).
        $competencystatistics = self::prepare_competency_stats_data($competencyid, $templateid);

        // Get course competency by template.
        $userplans = plan::get_records_for_template($templateid);

        // Find rate for each user in the plan for the the competency.
        $competencystatistics->listratings = array();
        foreach ($userplans as $plan) {
            $userid = $plan->get('userid');
            $courses = course_competency::get_courses_with_competency_and_user($competencyid, $userid);

            foreach ($courses as $course) {
                $modules = course_module_competency::list_course_modules($competencyid, $course->id);

                foreach ($modules as $cmid) {
                    // Find ratings in course modules.
                    $ucc = \tool_cmcompetency\api::get_user_competency_in_coursemodule($cmid, $userid, $competencyid);
                    $competencystatistics->listratings[] = $ucc;
                }
            }
        }

        return $competencystatistics;
    }

    /**
     * Prepare data for competency statistics.
     *
     * @param int $competencyid Competency id.
     * @param int $templateid Template id.
     * @return \stdClass The record of competency statistics.
     */
    protected static function prepare_competency_stats_data($competencyid, $templateid) {
        $competencystatistics = new \stdClass();

        $competency = template_competency::get_competency($templateid, $competencyid);

        // User has necessary capapbility if he can read the framework.
        $framework = core_competency_api::read_framework($competency->get('competencyframeworkid'));

        $competencystatistics->competency = $competency;
        $competencystatistics->framework = $framework;

        // Find de scale configuration associated to the competency.
        $scaleid = $competency->get('scaleid');
        $scaleconfig = $competency->get('scaleconfiguration');
        if ($scaleid === null) {
            $scaleid = $framework->get('scaleid');
            $scaleconfig = $framework->get('scaleconfiguration');
        }

        // Remove the scale ID from the config.
        $scaleconfig = json_decode($scaleconfig);
        if (!is_array($scaleconfig)) {
            throw new coding_exception('Unexpected scale configuration.');
        }
        array_shift($scaleconfig);
        $competencystatistics->scaleconfig = $scaleconfig;

        // Find the scale infos.
        $scale = \grade_scale::fetch(array('id' => $scaleid));
        $scale = $scale->load_items();
        $newscale = array();
        foreach ($scale as $key => $value) {
            $newscale[$key + 1] = $value;
        }
        $competencystatistics->scale = $newscale;

        // Find de scale configuration for the report.
        $reportscaleconfig = self::read_report_competency_config($framework->get('id'), $scaleid);
        $reportscaleconfig = json_decode($reportscaleconfig->get('scaleconfiguration'));
        if (!is_array($reportscaleconfig)) {
            throw new coding_exception('Unexpected report scale configuration.');
        }
        $competencystatistics->reportscaleconfig = $reportscaleconfig;
        return $competencystatistics;
    }

    /**
     * Search templates by contextid.
     *
     * @param context $context The context
     * @param string $query The search query
     * @param int $skip Number of records to skip (pagination)
     * @param int $limit Max of records to return (pagination)
     * @param string $includes Defines what other contexts to fetch frameworks from.
     *                         Accepted values are:
     *                          - children: All descendants
     *                          - parents: All parents, grand parents, etc...
     *                          - self: Context passed only.
     * @param bool $onlyvisible If should list only visible templates
     * @return array of competency_template
     */
    public static function search_templates($context, $query, $skip = 0, $limit = 0, $includes = 'children', $onlyvisible = false) {
        global $DB;

        // Get all the relevant contexts.
        $contexts = core_competency_api::get_related_contexts($context, $includes,
            array('moodle/competency:templateview', 'moodle/competency:templatemanage'));

        // First we do a permissions check.
        if (empty($contexts)) {
             throw new required_capability_exception($context, 'moodle/competency:templateview', 'nopermissions', '');
        }

        // Make the order by.
        $orderby = 'shortname ASC';

        // OK - all set.
        $template = new template();
        list($insql, $params) = $DB->get_in_or_equal(array_keys($contexts), SQL_PARAMS_NAMED);
        $select = "contextid $insql";

        if ($onlyvisible) {
            $select .= " AND visible = :visible";
            $params['visible'] = 1;
        }
        if ($query) {
            list($sqlquery, $paramsquery) = self::get_template_query_search($query);
            $select .= " AND $sqlquery";
            $params += $paramsquery;
        }

        return $template->get_records_select($select, $params, $orderby, '*', $skip, $limit);
    }

    /**
     * Produces a part of SQL query to filter template by the search string.
     *
     * @param string $search search string
     * @param string $tablealias alias of template table in the SQL query
     * @return array of two elements - SQL condition and array of named parameters
     */
    protected static function get_template_query_search($search, $tablealias = '') {
        global $DB;
        $params = array();
        if (empty($search)) {
            // This function should not be called if there is no search string, just in case return dummy query.
            return array('1=1', $params);
        }
        if ($tablealias && substr($tablealias, -1) !== '.') {
            $tablealias .= '.';
        }
        $searchparam = '%' . $DB->sql_like_escape($search) . '%';
        $conditions = array();
        $fields = array('shortname', 'description');
        $cnt = 0;
        foreach ($fields as $field) {
            $conditions[] = $DB->sql_like($tablealias . $field, ':csearch' . $cnt, false);
            $params['csearch' . $cnt] = $searchparam;
            $cnt++;
        }
        $sql = '(' . implode(' OR ', $conditions) . ')';
        return array($sql, $params);
    }

    /**
     * Check if competency exist for plan in the user_competency_plan Table.
     *
     * @param int $planid The plan ID
     * @param int $competencyid The competency ID
     * @return bool True if record exist
     */
    protected static function has_records_for_competency_user_in_plan($planid, $competencyid) {
        global $DB;
        $sql = "SELECT c.*
                  FROM {" . user_competency_plan::TABLE . "} ucp
                  JOIN {" . competency::TABLE . "} c
                    ON c.id = ucp.competencyid
                 WHERE ucp.planid = ?
                   AND ucp.competencyid = ?";
        return $DB->record_exists_sql($sql, array($planid, $competencyid));
    }

    /**
     * Return the list of tags that are associated to at least one plan that the user can read.
     *
     * @return array of tags
     */
    public static function search_tags_for_accessible_plans() {
        global $DB, $CFG;

        // Get all tags in the collection for competency plans.
        $namefield = empty($CFG->keeptagnamecase) ? 'name' : 'rawname';
        $collid = core_tag_area::get_collection('report_lpmonitoring', 'competency_plan');
        $recordstags = $DB->get_records('tag', array('tagcollid' => $collid), $namefield, 'id,' . $namefield . ' AS name ');

        // Loop through all tag instances to check if the user can manage the associated plans.
        $tagstoreturn = array();
        foreach ($recordstags as $tag) {
            $conditions = array('component' => 'report_lpmonitoring',
                            'itemtype' => 'competency_plan',
                            'tagid' => $tag->id
                        );
            $recordstaginstances = $DB->get_records('tag_instance', $conditions, '', 'itemid');
            foreach ($recordstaginstances as $taginstance) {
                // If the user can manage at least a plan with this tag, add it to the list of tags to return.
                $plan = new plan($taginstance->itemid);
                if ($plan->can_read()) {
                    $tagstoreturn[$tag->id] = $tag->name;
                    break;
                }
            }
        }

        return $tagstoreturn;
    }

    /**
     * Get the plans with a specific tag (but only plans that the user can view).
     *
     * @param int $tagid The tag id.
     * @param bool $withcomments Only plans with comments.
     * @return array( array(
     *                      'profileimage' => user_picture,
     *                      'profileimagesmall' => user_picture,
     *                      'fullname' => string,
     *                      'userid' => int,
     *                      'planid' => int,
     *                      'planname' => string
     *                      )
     *              )
     */
    public static function search_plans_with_tag($tagid, $withcomments) {
        $tag = core_tag_tag::get($tagid);
        $records = array();
        // Important to check if tag exists and not have just been removed.
        if ($tag) {
            $plans = $tag->get_tagged_items('report_lpmonitoring', 'competency_plan');

            foreach ($plans as $index => $planinfos) {
                $plan = new plan($planinfos->id);
                if ($plan->can_read()) {
                    // Return only plans with comments.
                    // We cannot filter easily the comments by sql so we do it afterwards.
                    if ($withcomments) {
                        $nbcomments = $plan->get_comment_object()->count();
                        if ($nbcomments == 0) {
                            continue;
                        }
                    } else {
                        $nbcomments = 0;
                    }

                    $users = user_get_users_by_id( array($planinfos->userid) );
                    $user = array_shift($users);

                    $profileimage = new \user_picture($user);

                    $record = array();
                    $record['profileimage'] = $profileimage;
                    $record['profileimagesmall'] = $record['profileimage'];
                    $record['fullname'] = fullname($user);
                    $record['userid'] = $user->id;
                    $record['email'] = $user->email;
                    $record['planid'] = $planinfos->id;
                    $record['planname'] = $planinfos->name;
                    $record['nbcomments'] = $nbcomments;

                    $records[] = $record;
                }
            }
        }
        return $records;
    }

    /**
     * Check if course module competency grading is enabled.
     */
    public static function is_cm_comptency_grading_enabled() {
        return self::$iscmcompetencygradingenabled;
    }

    /**
     * Check if display rating is enabled.
     */
    public static function is_display_rating_enabled() {
        return self::$isdisplayratingenabled;
    }

    /**
     * Reset a user competency grading.
     *
     * @param int $planid Plan id.
     * @param string $note A note to attach to the evidence.
     * @param int $competencyid Competency id.
     * @return evidence
     */
    protected static function reset_competency_grading($planid, $note, $competencyid) {
        global $USER;
        $plan = new plan($planid);

        $uc = core_competency_api::get_user_competency($plan->get('userid'), $competencyid);
        $context = $uc->get_context();
        if (!user_competency::can_grade_user($uc->get('userid'))) {
            throw new required_capability_exception($context, 'moodle/competency:competencygrade', 'nopermissions', '');
        }

        // Throws exception if competency not in plan.
        $competency = $uc->get_competency();
        $competencycontext = $competency->get_context();
        if (!has_any_capability(array('moodle/competency:competencyview', 'moodle/competency:competencymanage'),
                $competencycontext)) {
            throw new required_capability_exception($competencycontext, 'moodle/competency:competencyview', 'nopermissions', '');
        }

        // If there is actually a grade, we reset it.
        $actualgrade = $uc->get('grade');
        if (!is_null($actualgrade)) {
            $action = evidence::ACTION_OVERRIDE;
            $desckey = 'evidence_reset';

            $result = core_competency_api::add_evidence($uc->get('userid'),
                                      $competency,
                                      $context->id,
                                      $action,
                                      $desckey,
                                      'report_lpmonitoring',
                                      $plan->get('name'),
                                      false,
                                      null,
                                      null,
                                      $USER->id,
                                      $note);
            if ($result) {
                $uc->read();
                $event = user_competency_resetted::create_from_user_competency($uc);
                $event->trigger();
            }
            return $result;
        }
        return null;
    }

    /**
     * Reset the grading of users competencies (one particular competency or all competencies of a plan).
     *
     * @param int $planid Plan id.
     * @param string $note A note to attach to the evidence.
     * @param int $competencyid Competency id (or null for all competencies of this plan).
     */
    public static function reset_grading($planid, $note = null, $competencyid = null) {
        if (is_null($competencyid)) {
            $plan = new plan($planid);
            $competencies = $plan->get_competencies();
            foreach ($competencies as $competency) {
                self::reset_competency_grading($planid, $note, $competency->get('id'));
            }
        } else {
            self::reset_competency_grading($planid, $note, $competencyid);
        }
    }

    /**
     * Check if task exist for learning plan template.
     *
     * @param int $templateid the learning plan template id
     * @return boolean return true if task exist
     */
    public static function rating_task_exist($templateid) {
        $exist = false;
        $tasks = \core\task\manager::get_adhoc_tasks('report_lpmonitoring\task\rate_users_in_templates');
        foreach ($tasks as $task) {
            $cmdata = $task->get_custom_data();
            if ($cmdata->cms && $cmdata->cms->templateid == $templateid) {
                $exist = true;
                break;
            }
        }
        return $exist;
    }

    /**
     * Add rating task.
     *
     * @param int $templateid The learning plan template id
     * @param boolean $forcerating Force rating
     * @param string $scalesvalues json scale values
     */
    public static function add_rating_task($templateid, $forcerating, $scalesvalues) {
        global $USER;
        // Check if user can read template.
        \core_competency\api::read_template($templateid);
        // Check if there is current task for this learning plan template.
        if (self::rating_task_exist($templateid)) {
            throw new \moodle_exception('taskratingrunning', 'report_lpmonitoring');
        }

        // Build custom data for adhoc task.
        $customdata = [];
        $customdata['templateid'] = $templateid;
        $customdata['forcerating'] = $forcerating ? 1 : 0;
        $customdata['scalevalues'] = $scalesvalues;

        $task = new \report_lpmonitoring\task\rate_users_in_templates();
        $task->set_custom_data(array(
            'cms' => $customdata,
        ));
        $task->set_userid($USER->id);

        // Queue the task for the next run.
        \core\task\manager::queue_adhoc_task($task);
    }

    /**
     * Rate users in competencies template with default scales values.
     *
     * @param array $compdata Competencies with scales values associated.
     */
    public static function rate_users_in_template_with_defaultvalues($compdata) {
        if (isset($compdata->cms) && $compdata->cms->templateid) {
            try {
                $templateid = $compdata->cms->templateid;
                \core_competency\api::read_template($templateid);
                $plans = \core_competency\api::list_plans_for_template($templateid);
                $forcerating = $compdata->cms->forcerating;
                foreach ($plans as $plan) {
                    foreach ($compdata->cms->scalevalues as $data) {
                        $competency = \core_competency\template_competency::get_competency($templateid, $data->compid);
                        $ucc = \core_competency\api::get_user_competency($plan->get('userid'), $competency->get('id'));
                        if ($ucc->get('grade') === null || $forcerating) {
                            \core_competency\api::grade_competency_in_plan($plan, $competency->get('id'), $data->value);
                        }
                    }
                }
            } catch (\Exception $ex) {
                mtrace($ex->getMessage());
            }
        }
    }

    /**
     * Log user competency viewed in course event.
     * This function is similar to core_competency/api::user_competency_viewed_in_course but it does not validate the course.
     * The log event is saved even if the course is hidden.
     * Other checks can be done before calling the user_competency_viewed_in_course, but if this is called,
     * then the user viewed the data, so we log the info.
     *
     * @param user_competency_course|int $usercoursecompetencyorid The user competency course object or its ID.
     * @return bool
     */
    public static function user_competency_viewed_in_course($usercoursecompetencyorid) {
        core_competency_api::require_enabled();
        $ucc = $usercoursecompetencyorid;
        if (!is_object($ucc)) {
            $ucc = new user_competency_course($ucc);
        }

        if (!$ucc || !user_competency::can_read_user_in_course($ucc->get('userid'), $ucc->get('courseid'))) {
            throw new required_capability_exception($ucc->get_context(), 'moodle/competency:usercompetencyview',
                'nopermissions', '');
        }

        \core\event\competency_user_competency_viewed_in_course::create_from_user_competency_viewed_in_course($ucc)->trigger();
        return true;
    }

    /**
     * Get display rating setting for plan.
     *
     * @param int|plan $planorid The plan, or its ID.
     * @return Boolean If we have to display or not the rating for plan
     */
    public static function has_to_display_rating_for_plan($planorid) {
        if (self::$isdisplayratingenabled) {
            return \tool_lp\api::has_to_display_rating_for_plan($planorid);
        }
        return true;
    }

    /**
     * Check if display rating for plan can be reset to value of template.
     *
     * @param int|plan $planorid The plan, or its ID.
     * @return Boolean If we have a display rating set for plan
     */
    public static function can_reset_display_rating_for_plan($planorid) {
        if (self::$isdisplayratingenabled) {
            return \tool_lp\api::can_reset_display_rating_for_plan($planorid);
        }
        return false;
    }

    /**
     * Has to display rating setting for plan.
     *
     * @param int|plan $planorid The plan, or its ID.
     * @return Boolean If we have to display or not the rating for plan
     */
    public static function has_to_display_rating($planorid) {
        if (self::$isdisplayratingenabled) {
            return \tool_lp\api::has_to_display_rating($planorid);
        }
        return true;
    }
}


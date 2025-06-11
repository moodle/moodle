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
 * This is the external API for this report.
 *
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use context;
use context_user;
use core_user;
use core_competency\plan;
use core_competency\url;
use core_competency\external as core_competency_external;
use core_competency\api as core_competency_api;
use core_user\external\user_summary_exporter;
use core_competency\user_competency;
use core_competency\external\competency_exporter;
use core_competency\external\template_exporter;
use core_competency\external\user_competency_exporter;
use core_competency\external\user_competency_plan_exporter;
use core_comment\external\comment_area_exporter;
use core_tag_tag;
use report_lpmonitoring\api;
use report_lpmonitoring\external\list_plan_competency_report_exporter;
use report_lpmonitoring\external\scale_competency_summary_exporter;
use report_lpmonitoring\external\lpmonitoring_competency_detail_exporter;
use report_lpmonitoring\external\lpmonitoring_competency_statistics_exporter;
use report_lpmonitoring\external\lpmonitoring_competency_statistics_incourse_exporter;
use report_lpmonitoring\external\lpmonitoring_competency_statistics_incoursemodule_exporter;
use report_lpmonitoring\external\lpmonitoring_user_competency_summary_in_course_exporter;
use report_lpmonitoring\external\stats_plan_exporter;
use report_lpmonitoring\output\lpmonitoring_user_competency_summary_in_course;
use context_system;
use moodle_exception;


/**
 * This is the external API for this report.
 *
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {
    /**
     * Returns description of search_users_by_templateid() parameters.
     *
     * @return \external_function_parameters
     */
    public static function search_users_by_templateid_parameters() {
        $templateid = new external_value(
            PARAM_INT,
            'The learning plan template id',
            VALUE_REQUIRED
        );

        $query = new external_value(
            PARAM_TEXT,
            'The query search',
            VALUE_REQUIRED
        );
        $scalevalues = new external_value(
            PARAM_TEXT,
            'The scale values filter',
            VALUE_DEFAULT,
            ''
        );
        $scalefilterin = new external_value(
            PARAM_TEXT,
            'Apply scale filter on plan course or coursemodule',
            VALUE_DEFAULT,
            ''
        );
        $scalesortorder = new external_value(
            PARAM_TEXT,
            'Scale sort order',
            VALUE_DEFAULT,
            'ASC'
        );
        $withcomments = new external_value(
            PARAM_BOOL,
            'Only plans with comments',
            VALUE_DEFAULT,
            false
        );
        $withplans = new external_value(
            PARAM_BOOL,
            'Only students with at least two plans',
            VALUE_DEFAULT,
            false
        );

        $params = [
            'templateid' => $templateid,
            'query' => $query,
            'scalevalues' => $scalevalues,
            'scalefilterin' => $scalefilterin,
            'scalesortorder' => $scalesortorder,
            'withcomments' => $withcomments,
            'withplans' => $withplans,
        ];
        return new external_function_parameters($params);
    }

    /**
     * Get learning plans from templateid.
     *
     * @param int $templateid Template id.
     * @param string $query the query search.
     * @param string $scalevalues The scale values filter.
     * @param int $scalefilterin Apply the scale filters on grade in plan, course or course module.
     * @param string $scalesortorder The scale sort order ('ASC'/'DESC').
     * @param boolean $withcomments Only plans with comments.
     * @param boolean $withplans Only with at least 2 plans.
     *
     * @return array
     */
    public static function search_users_by_templateid($templateid, $query, $scalevalues, $scalefilterin, $scalesortorder,
            $withcomments, $withplans) {
        global $PAGE;
        $params = self::validate_parameters(self::search_users_by_templateid_parameters(), [
            'templateid' => $templateid,
            'query' => $query,
            'scalevalues' => $scalevalues,
            'scalefilterin' => $scalefilterin,
            'scalesortorder' => $scalesortorder,
            'withcomments' => $withcomments,
            'withplans' => $withplans,
        ]);

        $context = context_system::instance();
        self::validate_context($context);

        $records = api::search_users_by_templateid($params['templateid'], $params['query'],
                json_decode($params['scalevalues'], true), $params['scalefilterin'], $params['scalesortorder'],
                $params['withcomments'], $params['withplans']);

        foreach ($records as $key => $record) {
            $profileimage = $record['profileimage'];
            $profileimage->size = 0;
            $record['profileimagesmall']  = $profileimage->get_url($PAGE)->out(false);
            $records[$key] = $record;
        }
        return (array) (object) $records;
    }

    /**
     * Returns description of search_users_by_templateid() result value.
     *
     * @return external_multiple_structure
     */
    public static function search_users_by_templateid_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'fullname' => new external_value(PARAM_TEXT, 'The fullname of the user'),
                'profileimagesmall' => new external_value(PARAM_TEXT, 'The profile image small size'),
                'userid' => new external_value(PARAM_INT, 'The user id value'),
                'planid' => new external_value(PARAM_INT, 'The plan id value'),
                'nbrating' => new external_value(PARAM_INT, 'Total rating number'),
                'nbcomments' => new external_value(PARAM_INT, 'Number of comments on the plan'),
                'nbplans' => new external_value(PARAM_INT, 'Number of study plans'),
                'email' => new external_value(PARAM_TEXT, 'The email of the user', VALUE_OPTIONAL),
                'idnumber' => new external_value(PARAM_TEXT, 'The idnumber of the user', VALUE_OPTIONAL),
                'phone1' => new external_value(PARAM_TEXT, 'The phone1 of the user', VALUE_OPTIONAL),
                'phone2' => new external_value(PARAM_TEXT, 'The phone2 of the user', VALUE_OPTIONAL),
                'department' => new external_value(PARAM_TEXT, 'The department of the user', VALUE_OPTIONAL),
                'institution' => new external_value(PARAM_TEXT, 'The institution of the user', VALUE_OPTIONAL),
            ])
        );
    }

    /**
     * Returns description of search_templates() parameters.
     *
     * @return \external_function_parameters
     */
    public static function search_templates_parameters() {

        $query = new external_value(
            PARAM_TEXT,
            'The query search',
            VALUE_REQUIRED
        );
        $contextid = new external_value(
            PARAM_INT,
            'The context id',
            VALUE_REQUIRED
        );
        $skip = new external_value(
            PARAM_INT,
            'Number of records to skip',
            VALUE_DEFAULT,
            0
        );
        $limit = new external_value(
            PARAM_INT,
            'Max of records to return',
            VALUE_DEFAULT,
            0
        );
        $includes = new external_value(
            PARAM_TEXT,
            'Defines what other contexts to fetch templates',
            VALUE_DEFAULT,
            'children'
        );
        $onlyvisible = new external_value(
            PARAM_BOOL,
            'True if search in visible templates',
            VALUE_DEFAULT,
            true
        );

        $params = [
            'contextid' => $contextid,
            'query' => $query,
            'skip' => $skip,
            'limit' => $limit,
            'includes' => $includes,
            'onlyvisible' => $onlyvisible,
        ];
        return new external_function_parameters($params);
    }

    /**
     * Search templates.
     *
     * @param int $contextid Context id.
     * @param string $query the query search.
     * @param int $skip Number of records to skip (pagination)
     * @param int $limit Max of records to return (pagination)
     * @param string $includes Defines what other contexts to fetch templates from.
     *                         Accepted values are:
     *                          - children: All descendants
     *                          - parents: All parents, grand parents, etc...
     *                          - self: Context passed only.
     * @param bool $onlyvisible If should search only in visible templates
     * @return boolean
     */
    public static function search_templates($contextid, $query, $skip, $limit, $includes, $onlyvisible ) {
        global $PAGE;
        $params = self::validate_parameters(self::search_templates_parameters(), [
            'contextid' => $contextid,
            'query' => $query,
            'skip' => $skip,
            'limit' => $limit,
            'includes' => $includes,
            'onlyvisible' => $onlyvisible,
        ]);

        $context = self::get_context_from_params($params);
        self::validate_context($context);
        $output = $PAGE->get_renderer('core');

        $results = api::search_templates($context,
                $params['query'],
                $params['skip'],
                $params['limit'],
                $params['includes'],
                $params['onlyvisible']);

        $records = [];
        foreach ($results as $result) {
            $exporter = new template_exporter($result);
            $record = $exporter->export($output);
            array_push($records, $record);
        }
        return $records;
    }

    /**
     * Returns description of search_templates() result value.
     *
     * @return \external_description
     */
    public static function search_templates_returns() {
        return new external_multiple_structure(template_exporter::get_read_structure());
    }

    /**
     * Returns description of get_scales_from_template() parameters.
     *
     * @return \external_function_parameters
     */
    public static function get_scales_from_template_parameters() {
        $templateid = new external_value(
            PARAM_INT,
            'The learning plan template id',
            VALUE_REQUIRED
        );

        $params = [
            'templateid' => $templateid,
        ];
        return new external_function_parameters($params);
    }

    /**
     * Get scales from templateid.
     *
     * @param int $templateid Template id.
     *
     * @return boolean
     */
    public static function get_scales_from_template($templateid) {
        $params = self::validate_parameters(self::get_scales_from_template_parameters(), [
            'templateid' => $templateid,
        ]);

        $context = context_system::instance();
        self::validate_context($context);

        $results = api::get_scales_from_templateid($params['templateid']);
        $records = [];
        foreach ($results as $key => $value) {
            $scale = self::read_report_competency_config($value['frameworkid'], $key);
            $scale->name = $value['scalename'];
            $records[] = $scale;
        }
        return $records;
    }

    /**
     * Returns description of get_scales_from_template() result value.
     *
     * @return \external_description
     */
    public static function get_scales_from_template_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'The option value'),
                'name' => new external_value(PARAM_TEXT, 'The scale name'),
                'competencyframeworkid' => new external_value(PARAM_INT, 'The option value'),
                'scaleid' => new external_value(PARAM_INT, 'The option value'),
                'scaleconfiguration' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'id' => new external_value(PARAM_INT, 'The option value'),
                            'name' => new external_value(PARAM_TEXT, 'The option value'),
                            'color' => new external_value(PARAM_TEXT, 'The option value'),
                            'proficient' => new external_value(PARAM_BOOL, 'The proficient indicator'),
                        ]
                    )
                ),
            ])
        );
    }

    /**
     * Returns description of get_scales_from_framework() parameters.
     *
     * @return \external_function_parameters
     */
    public static function get_scales_from_framework_parameters() {
        $competencyframeworkid = new external_value(
            PARAM_INT,
            'The competency framework id',
            VALUE_REQUIRED
        );

        $params = [
            'competencyframeworkid' => $competencyframeworkid,
        ];
        return new external_function_parameters($params);
    }

    /**
     * Get scales from competencyframeworkid.
     *
     * @param int $competencyframeworkid Framework id.
     *
     * @return boolean
     */
    public static function get_scales_from_framework($competencyframeworkid) {

        $params = self::validate_parameters(self::get_scales_from_framework_parameters(), [
            'competencyframeworkid' => $competencyframeworkid,
        ]);

        return api::get_scales_from_framework($params['competencyframeworkid']);
    }

    /**
     * Returns description of get_scales_from_framework() result value.
     *
     * @return \external_description
     */
    public static function get_scales_from_framework_returns() {
        return new external_multiple_structure(
                    new external_single_structure([
                        'id' => new external_value(PARAM_INT, 'The option value'),
                        'name' => new external_value(PARAM_TEXT, 'The name of the scale'),
                    ])
            );
    }

    /**
     * Returns description of read_report_competency_config_parameters() parameters.
     *
     * @return \external_function_parameters
     */
    public static function read_report_competency_config_parameters() {
        $competencyframeworkid = new external_value(
            PARAM_INT,
            'The competency framework id',
            VALUE_REQUIRED
        );
        $scaleid = new external_value(
            PARAM_INT,
            'The scale id',
            VALUE_REQUIRED
        );
        $params = [
            'competencyframeworkid' => $competencyframeworkid,
            'scaleid' => $scaleid,
        ];
        return new external_function_parameters($params);
    }

    /**
     * Read report competency configuration
     *
     * @param int $competencyframeworkid Framework id.
     * @param int $scaleid Scale id.
     * @return \stdClass The record of report_competency_config
     */
    public static function read_report_competency_config($competencyframeworkid, $scaleid) {

        $params = self::validate_parameters(self::read_report_competency_config_parameters(), [
            'competencyframeworkid' => $competencyframeworkid,
            'scaleid' => $scaleid,
        ]);

        $reportcompetencyconfig = api::read_report_competency_config($params['competencyframeworkid'], $params['scaleid']);
        $scaleotherinfo = api::get_scale_configuration_other_info($params['competencyframeworkid'], $params['scaleid']);

        $record = new \stdClass();
        $record->id = $reportcompetencyconfig->get('id');
        $record->competencyframeworkid = $reportcompetencyconfig->get('competencyframeworkid');
        $record->scaleid = $reportcompetencyconfig->get('scaleid');
        $record->scaleconfiguration = [];
        $config = json_decode($reportcompetencyconfig->get('scaleconfiguration'));

        foreach ($config as $key => $valuescale) {
            $valuescale->proficient = $scaleotherinfo[$key]['proficient'];
            $valuescale->name = $scaleotherinfo[$key]['name'];
            $record->scaleconfiguration[] = (object) $valuescale;
        }

        return $record;
    }

    /**
     * Returns description of read_report_competency_config() result value.
     *
     * @return \external_description
     */
    public static function read_report_competency_config_returns() {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'The option value'),
            'competencyframeworkid' => new external_value(PARAM_INT, 'The option value'),
            'scaleid' => new external_value(PARAM_INT, 'The option value'),
            'scaleconfiguration' => new external_multiple_structure(
                new external_single_structure(
                    [
                        'id' => new external_value(PARAM_INT, 'The option value'),
                        'name' => new external_value(PARAM_TEXT, 'The option value'),
                        'color' => new external_value(PARAM_TEXT, 'The option value'),
                        'proficient' => new external_value(PARAM_BOOL, 'The proficient indicator'),
                    ]
                )
            ),
        ]);
    }

    /**
     * Returns description of read_report_competency_config_parameters() parameters.
     *
     * @return \external_function_parameters
     */
    public static function create_report_competency_config_parameters() {
        $competencyframeworkid = new external_value(
            PARAM_INT,
            'The competency framework id',
            VALUE_REQUIRED
        );
        $scaleid = new external_value(
            PARAM_INT,
            'The scale id',
            VALUE_REQUIRED
        );
        $scaleconfiguration = new external_value(
            PARAM_RAW,
            'The scaleconfiguration',
            VALUE_REQUIRED
        );

        $params = [
            'competencyframeworkid' => $competencyframeworkid,
            'scaleid' => $scaleid,
            'scaleconfiguration' => $scaleconfiguration,
        ];

        return new external_function_parameters($params);
    }

    /**
     * Create report competency configuration
     *
     * @param int $competencyframeworkid Framework id.
     * @param int $scaleid Scale id.
     * @param string $scaleconfiguration Scale configuration.
     * @return stdClass The new record
     */
    public static function create_report_competency_config($competencyframeworkid, $scaleid, $scaleconfiguration) {
        global $PAGE;

        $params = self::validate_parameters(self::create_report_competency_config_parameters(), [
            'competencyframeworkid' => $competencyframeworkid,
            'scaleid' => $scaleid,
            'scaleconfiguration' => $scaleconfiguration,
        ]);

        $params = (object) $params;
        $result = api::create_report_competency_config($params);
        $record = new \stdClass();
        $record->id = $result->get('id');
        $record->competencyframeworkid = $result->get('competencyframeworkid');
        $record->scaleid = $result->get('scaleid');
        $record->scaleconfiguration = [];

        $scaleotherinfo = api::get_scale_configuration_other_info($params->competencyframeworkid, $params->scaleid);
        $config = json_decode($result->get('scaleconfiguration'));
        foreach ($config as $key => $valuescale) {
            $valuescale->proficient = $scaleotherinfo[$key]['proficient'];
            $valuescale->name = $scaleotherinfo[$key]['name'];
            $record->scaleconfiguration[] = (object) $valuescale;
        }

        return $record;
    }

    /**
     * Returns description of read_report_competency_config() result value.
     *
     * @return \external_description
     */
    public static function create_report_competency_config_returns() {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'The option value'),
            'competencyframeworkid' => new external_value(PARAM_INT, 'The option value'),
            'scaleid' => new external_value(PARAM_INT, 'The option value'),
            'scaleconfiguration' => new external_multiple_structure(
                new external_single_structure(
                    [
                        'id' => new external_value(PARAM_INT, 'The option value'),
                        'name' => new external_value(PARAM_TEXT, 'The option value'),
                        'color' => new external_value(PARAM_TEXT, 'The option value'),
                        'proficient' => new external_value(PARAM_BOOL, 'The proficient indicator'),
                    ]
                )
            ),
        ]);
    }

    /**
     * Returns description of update_report_competency_config() parameters.
     *
     * @return \external_function_parameters
     */
    public static function update_report_competency_config_parameters() {
        $competencyframeworkid = new external_value(
            PARAM_INT,
            'The competency framework id',
            VALUE_REQUIRED
        );
        $scaleid = new external_value(
            PARAM_INT,
            'The scale id',
            VALUE_REQUIRED
        );
        $scaleconfiguration = new external_value(
            PARAM_RAW,
            'The scaleconfiguration',
            VALUE_REQUIRED
        );

        $params = [
            'competencyframeworkid' => $competencyframeworkid,
            'scaleid' => $scaleid,
            'scaleconfiguration' => $scaleconfiguration,
        ];

        return new external_function_parameters($params);
    }

    /**
     * Update an existing configuration for a framework and a scale.
     *
     * @param int $competencyframeworkid Framework id.
     * @param int $scaleid Scale id.
     * @param string $scaleconfiguration Scale configuration.
     * @return boolean
     */
    public static function update_report_competency_config($competencyframeworkid, $scaleid, $scaleconfiguration) {

        $params = self::validate_parameters(self::update_report_competency_config_parameters(), [
            'competencyframeworkid' => $competencyframeworkid,
            'scaleid' => $scaleid,
            'scaleconfiguration' => $scaleconfiguration,
        ]);

        $params = (object) $params;

        return api::update_report_competency_config($params);
    }

    /**
     * Returns description of uupdate_report_competency_config() result value.
     *
     * @return \external_description
     */
    public static function update_report_competency_config_returns() {
        return new external_value(PARAM_BOOL, 'True if the update was successful');
    }

    /**
     * Returns description of read_plan() parameters.
     *
     * @return \external_function_parameters
     */
    public static function read_plan_parameters() {
        return new external_function_parameters([
            'planid' => new external_value(PARAM_INT, 'The plan ID'),
            'templateid' => new external_value(PARAM_INT, 'The template ID'),
            'scalevalues' => new external_value(PARAM_TEXT, 'The scale values filter'),
            'scalefilterin' => new external_value(PARAM_TEXT, 'Apply the scale filters on grade in plan, course or course module'),
            'scalesortorder' => new external_value(PARAM_TEXT, 'Scale sort order', VALUE_DEFAULT, 'ASC'),
            'tagid' => new external_value(PARAM_INT, 'The tag ID'),
            'withcomments' => new external_value(PARAM_BOOL, 'Only plans with comments'),
            'withplans' => new external_value(PARAM_BOOL, 'Only students with at leats two plans'),
        ]);
    }

    /**
     * Get the plan information by plan ID or
     * template ID (first user returned from the list of plans).
     *
     * @param int $planid The plan ID
     * @param int $templateid The template ID
     * @param string $scalevalues The scales values filter
     * @param int $scalefilterin Apply the scale filters on grade in plan, course or course module
     * @param string $scalesortorder Scale sort order
     * @param int $tagid The tag ID
     * @param boolean $withcomments True to return only plans with at leat one comment
     * @param boolean $withplans True to return only students'plans with at leat two plans
     * @return array
     */
    public static function read_plan($planid, $templateid, $scalevalues = '', $scalefilterin = '',
            $scalesortorder= 'ASC', $tagid = null, $withcomments = false, $withplans = false) {
        global $PAGE;
        $context = context_system::instance();
        self::validate_context($context);

        $params = self::validate_parameters(self::read_plan_parameters(), [
                    'planid' => $planid,
                    'templateid' => $templateid,
                    'scalevalues' => $scalevalues,
                    'scalefilterin' => $scalefilterin,
                    'scalesortorder' => $scalesortorder,
                    'tagid' => $tagid,
                    'withcomments' => $withcomments,
                    'withplans' => $withplans,
                ]);

        $plans = api::read_plan($params['planid'], $params['templateid'],
                json_decode($params['scalevalues'], true), $params['scalefilterin'],
                $params['scalesortorder'], $params['tagid'], $params['withcomments'], $params['withplans']);
        self::validate_context($plans->current->get_context());

        $output = $PAGE->get_renderer('report_lpmonitoring');

        $planexport = new \stdClass();
        $planexport->id = $plans->current->get('id');
        $planexport->name = $plans->current->get('name');

        $status = $plans->current->get('status');
        $planexport->isactive = $status == plan::STATUS_ACTIVE;
        $planexport->isdraft = $status == plan::STATUS_DRAFT;
        $planexport->iscompleted = $status == plan::STATUS_COMPLETE;
        $planexport->iswaitingforreview = $status == plan::STATUS_WAITING_FOR_REVIEW;
        $planexport->isinreview = $status == plan::STATUS_IN_REVIEW;
        $planexport->statusname = $plans->current->get_statusname();
        $planexport->usercontext = $plans->current->get_context()->id;
        $planexport->canmanage = $plans->current->can_manage();
        $planexport->cangrade = user_competency::can_grade_user($plans->current->get('userid'));
        $planexport->displayrating = api::has_to_display_rating_for_plan($planexport->id);
        $planexport->canresetdisplayrating = api::can_reset_display_rating_for_plan($planexport->id);
        $planexport->isdisplayratingenabled = api::is_display_rating_enabled();
        // Set learning plan url.
        $planexport->url = url::plan($plans->current->get('id'))->out(false);
        // Get stats for plan.
        $uc = new \stdClass();
        $uc->usercompetencies = core_competency_api::list_plan_competencies($planexport->id);
        $statsexporter = new stats_plan_exporter($uc, ['plan' => $plans->current]);
        $planexport->stats = $statsexporter->export($output);

        $hasnavigation = false;

        $userexporter = new user_summary_exporter(core_user::get_user($plans->current->get('userid'), '*', \MUST_EXIST));
        $planexport->user = $userexporter->export($output);

        if (isset($plans->previous) || isset($plans->next)) {
            $hasnavigation = true;
        }

        $result = [
            'plan' => $planexport,
            'hasnavigation' => $hasnavigation,
        ];

        foreach ($plans->fullnavigation as $key => $plan) {
            $plan = (object) $plan;
            $profileimage = $plan->profileimage;
            $profileimage->size = 0;
            $plan->profileimage = $profileimage->get_url($PAGE)->out(false);
            $plan->current = $plan->planid == $plans->current->get('id') ? true : false;
            if (!empty($params['tagid'])) {
                $plan->tagid = $params['tagid'];
            }
            $plans->fullnavigation[$key] = $plan;
        }
        $result['fullnavigation'] = $plans->fullnavigation;

        if (isset($plans->previous)) {
            $profileimage = $plans->previous->profileimage;
            $plans->previous->profileimage = $profileimage->get_url($PAGE)->out(false);
            $profileimage->size = 0;
            $plans->previous->profileimagesmall  = $profileimage->get_url($PAGE)->out(false);
            $result['navprev'] = $plans->previous;
        }
        if (isset($plans->next)) {
            $profileimage = $plans->next->profileimage;
            $plans->next->profileimage = $profileimage->get_url($PAGE)->out(false);
            $profileimage->size = 0;
            $plans->next->profileimagesmall  = $profileimage->get_url($PAGE)->out(false);
            $result['navnext'] = $plans->next;
        }

        return external_api::clean_returnvalue(self::read_plan_returns(), $result);
    }

    /**
     * Returns description of read_plan() result value.
     *
     * @return \external_description
     */
    public static function read_plan_returns() {
        $plan = new external_single_structure([
            'id'   => new external_value(PARAM_INT, 'The plan ID'),
            'name' => new external_value(PARAM_TEXT, 'The plan name'),
            'user' => user_summary_exporter::get_read_structure(),
            'usercontext' => new external_value(PARAM_INT, 'The user context ID value'),
            'isactive' => new external_value(PARAM_BOOL, 'Is plan active'),
            'canmanage' => new external_value(PARAM_BOOL, 'Can manage user plan'),
            'cangrade' => new external_value(PARAM_BOOL, 'Can grade user plan'),
            'displayrating' => new external_value(PARAM_BOOL, 'Is ratings displayed for user'),
            'canresetdisplayrating' => new external_value(PARAM_BOOL, 'Plan has a display rating setting'),
            'isdraft' => new external_value(PARAM_BOOL, 'Is plan draft'),
            'iscompleted' => new external_value(PARAM_BOOL, 'Is plan completed'),
            'iswaitingforreview' => new external_value(PARAM_BOOL, 'Is plan completed'),
            'isdisplayratingenabled' => new external_value(PARAM_BOOL, 'Is hide/display rating enabled'),
            'isinreview' => new external_value(PARAM_BOOL, 'Is plan completed'),
            'statusname' => new external_value(PARAM_TEXT, 'The plan status name'),
            'url' => new external_value(PARAM_TEXT, 'The plan url'),
            'stats' => stats_plan_exporter::get_read_structure(),
        ], 'The plan information');

        $usernav = new external_single_structure([
            'fullname' => new external_value(PARAM_TEXT, 'The fullname of the user'),
            'profileimage' => new external_value(PARAM_TEXT, 'The profile image small size'),
            'profileimagesmall' => new external_value(PARAM_TEXT, 'The profile image small size'),
            'userid' => new external_value(PARAM_INT, 'The user ID value'),
            'planid' => new external_value(PARAM_INT, 'The plan ID value'),
            'tagid' => new external_value(PARAM_INT, 'The tag ID value', VALUE_OPTIONAL),
        ], 'The user and plan ID navigation information', VALUE_OPTIONAL);

        $fullnavigation = new external_multiple_structure(
            new external_single_structure([
                'fullname' => new external_value(PARAM_TEXT, 'The fullname of the user'),
                'planname' => new external_value(PARAM_TEXT, 'The plan name'),
                'email' => new external_value(PARAM_TEXT, 'The email of the user', VALUE_OPTIONAL),
                'idnumber' => new external_value(PARAM_TEXT, 'The idnumber of the user', VALUE_OPTIONAL),
                'profileimage' => new external_value(PARAM_TEXT, 'The profile image small size'),
                'userid' => new external_value(PARAM_INT, 'The user ID value'),
                'planid' => new external_value(PARAM_INT, 'The plan ID value'),
                'nbrating' => new external_value(PARAM_INT, 'The nb rating value', VALUE_OPTIONAL),
                'nbcomments' => new external_value(PARAM_INT, 'The number of comments', VALUE_OPTIONAL),
                'nbplans' => new external_value(PARAM_INT, 'The number of study plans', VALUE_OPTIONAL),
                'current' => new external_value(PARAM_BOOL, 'Is current user'),
                'tagid' => new external_value(PARAM_INT, 'The tag ID value', VALUE_OPTIONAL),
            ], 'Full navigation list', VALUE_OPTIONAL)
        );

        return new external_single_structure([
            'plan' => $plan,
            'hasnavigation' => new external_value(PARAM_BOOL, 'Has navigation returned for previous and/or next plans'),
            'navprev' => $usernav,
            'navnext' => $usernav,
            'fullnavigation' => $fullnavigation,
        ]);
    }

    /**
     * Returns description of get_competency_detail.
     *
     * @return \external_function_parameters
     */
    public static function get_competency_detail_parameters() {
        $userid = new external_value(
            PARAM_INT,
            'The user id',
            VALUE_REQUIRED
        );

        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );

        $planid = new external_value(
            PARAM_INT,
            'The plan id',
            VALUE_REQUIRED
        );

        $params = [
            'userid' => $userid,
            'competencyid' => $competencyid,
            'planid' => $planid,
        ];
        return new external_function_parameters($params);
    }

    /**
     * Returns the competency detail for lp_monitoring report.
     *
     * @param int $userid User id.
     * @param int $competencyid Competency id.
     * @param int $planid Plan id.
     *
     * @return array
     */
    public static function get_competency_detail($userid, $competencyid, $planid) {
        global $PAGE;

        $params = self::validate_parameters(self::get_competency_detail_parameters(), [
            'userid' => $userid,
            'competencyid' => $competencyid,
            'planid' => $planid,
        ]);
        $context = context_system::instance();
        self::validate_context($context);

        $result = api::get_competency_detail($params['userid'], $params['competencyid'], $params['planid']);
        $result->displayrating = \report_lpmonitoring\api::has_to_display_rating($params['planid']);

        $output = $PAGE->get_renderer('report_lpmonitoring');
        $exporter = new lpmonitoring_competency_detail_exporter($result);
        $record = $exporter->export($output);

        return $record;
    }

    /**
     * Returns description of get_competency_detail() result value.
     *
     * @return \external_description
     */
    public static function get_competency_detail_returns() {
        return lpmonitoring_competency_detail_exporter::get_read_structure();
    }

    /**
     * External function parameters structure.
     *
     * @return \external_description
     */
    public static function list_plan_competencies_parameters() {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'The plan ID.'),
        ]);
    }

    /**
     * List plan competencies.
     * @param int $id The plan ID.
     * @param boolean $withparent True if include parents in result.
     * @return array
     */
    public static function list_plan_competencies($id, $withparent = false) {
        global $PAGE;
        $plan = \core_competency\api::read_plan($id);
        $result = core_competency_external::list_plan_competencies($id);
        $displayrating = true;
        if ($plan->get('status') == \core_competency\plan::STATUS_ACTIVE) {
            $displayrating = api::has_to_display_rating($plan);
        }
        if ($withparent) {
            $output = $PAGE->get_renderer('report_lpmonitoring');
            $helper = new \core_competency\external\performance_helper();
            $competencieswithparents = [];
            $competencies = [];
        }

        foreach ($result as $key => $r) {
            $usercompetency = (isset($r->usercompetency)) ? $r->usercompetency : $r->usercompetencyplan;
            $proficiency = $usercompetency->proficiency;
            $r->isnotrated = false;
            $r->isproficient = false;
            $r->isnotproficient = false;
            $r->isparent = false;
            $r->isassessable = true;
            if ($withparent) {
                $assessedcompetency = new \core_competency\competency($r->competency->id);
                $ancestors = $assessedcompetency->get_ancestors();
                $firstlevelparent = reset($ancestors);

                // If there is a parent.
                if ($firstlevelparent) {
                    $context = $helper->get_context_from_competency($firstlevelparent);
                    $exporter = new \core_competency\external\competency_exporter($firstlevelparent, ['context' => $context]);
                    $parent = $exporter->export($output);
                    $r->competency->firstlevelparentid = $parent->id;

                    if (!in_array($parent->id, array_keys($competencieswithparents))) {
                        $newcompdetail = new \stdClass();
                        $newcompdetail->competency = $parent;
                        $newcompdetail->usercompetency = \core_competency\user_competency::get_multiple($plan->get('userid'),
                                [$parent->id]);
                        $newcompdetail->isparent = true;
                        $newcompdetail->isnotrated = false;
                        $newcompdetail->isproficient = false;
                        $newcompdetail->isnotproficient = false;
                        $newcompdetail->isassessable = false;
                        $competencieswithparents[$parent->id]['parent'] = $newcompdetail;
                        $competencieswithparents[$parent->id]['competencies'][] = $r;
                    } else {
                        $competencieswithparents[$parent->id]['competencies'][] = $r;
                    }
                } else {
                    // This is already a first level competency.
                    $r->isparent = true;
                    $r->isassessable = true;
                    $competencieswithparents[$r->competency->id]['parent'] = $r;
                }
            }
            if (!$displayrating) {
                $r->isnotrated = true;
                continue;
            }
            if (!isset($proficiency)) {
                $r->isnotrated = true;
            } else {
                if ($proficiency) {
                    $r->isproficient = true;
                } else {
                    $r->isnotproficient = true;
                }
            }
        }

        if ($withparent) {
            $onlylevel1 = true;
            foreach ($competencieswithparents as $cpm) {
                $competencies[] = $cpm['parent'];
                if (!empty($cpm['competencies'])) {
                    foreach ($cpm['competencies'] as $child) {
                        $competencies[] = $child;
                        $onlylevel1 = false;
                    }
                }
            }
            // If all competencies are level 1, we do not consider them as parents.
            if ($onlylevel1) {
                foreach ($competencies as $cmp) {
                    $cmp->isparent = false;
                }
            }
            return $competencies;
        }
        return $result;
    }

    /**
     * External function return structure.
     *
     * @return \external_description
     */
    public static function list_plan_competencies_returns() {
        $uc = user_competency_exporter::get_read_structure();
        $ucp = user_competency_plan_exporter::get_read_structure();

        $uc->required = VALUE_OPTIONAL;
        $ucp->required = VALUE_OPTIONAL;

        return new external_multiple_structure(
            new external_single_structure([
                'competency' => competency_exporter::get_read_structure(),
                'usercompetency' => $uc,
                'usercompetencyplan' => $ucp,
                'isproficient' => new external_value(PARAM_BOOL, 'True if the competency is proficient'),
                'isnotproficient' => new external_value(PARAM_BOOL, 'False if the competency is proficient'),
                'isnotrated' => new external_value(PARAM_BOOL, 'True if the competency is not rated'),
            ])
        );
    }

    /**
     * Returns description of get_competency_statistics.
     *
     * @return \external_function_parameters
     */
    public static function get_competency_statistics_parameters() {

        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );

        $templateid = new external_value(
            PARAM_INT,
            'The template id',
            VALUE_REQUIRED
        );

        $params = [
            'competencyid' => $competencyid,
            'templateid' => $templateid,
        ];
        return new external_function_parameters($params);
    }

    /**
     * Returns the competency statistics for lp_monitoring report.
     *
     * @param int $competencyid Competency id.
     * @param int $templateid Template id.
     * @return array
     */
    public static function get_competency_statistics($competencyid, $templateid) {
        global $PAGE;

        $params = self::validate_parameters(self::get_competency_statistics_parameters(), [
            'competencyid' => $competencyid,
            'templateid' => $templateid,
        ]);
        $context = context_system::instance();
        self::validate_context($context);

        $result = api::get_competency_statistics($params['competencyid'], $params['templateid']);

        $output = $PAGE->get_renderer('report_lpmonitoring');
        $exporter = new lpmonitoring_competency_statistics_exporter($result);
        $record = $exporter->export($output);

        return $record;
    }

    /**
     * Returns description of get_competency_statistics() result value.
     *
     * @return \external_description
     */
    public static function get_competency_statistics_returns() {
        return lpmonitoring_competency_statistics_exporter::get_read_structure();
    }

    /**
     * Returns description of get_competency_statistics_incourse.
     *
     * @return \external_function_parameters
     */
    public static function get_competency_statistics_incourse_parameters() {

        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );

        $templateid = new external_value(
            PARAM_INT,
            'The template id',
            VALUE_REQUIRED
        );

        $params = [
            'competencyid' => $competencyid,
            'templateid' => $templateid,
        ];
        return new external_function_parameters($params);
    }

    /**
     * Returns the competency statistics in course.
     *
     * @param int $competencyid Competency id.
     * @param int $templateid Template id.
     * @return array
     */
    public static function get_competency_statistics_incourse($competencyid, $templateid) {
        global $PAGE;

        $params = self::validate_parameters(self::get_competency_statistics_incourse_parameters(), [
            'competencyid' => $competencyid,
            'templateid' => $templateid,
        ]);
        $context = context_system::instance();
        self::validate_context($context);

        $result = api::get_competency_statistics_in_course($params['competencyid'], $params['templateid']);

        $output = $PAGE->get_renderer('report_lpmonitoring');
        $exporter = new lpmonitoring_competency_statistics_incourse_exporter($result);
        $record = $exporter->export($output);

        return $record;
    }

    /**
     * Returns description of get_competency_statistics_incourse() result value.
     *
     * @return \external_description
     */
    public static function get_competency_statistics_incourse_returns() {
        return lpmonitoring_competency_statistics_incourse_exporter::get_read_structure();
    }

    /**
     * Returns description of get_competency_statistics_incoursemodules.
     *
     * @return \external_function_parameters
     */
    public static function get_competency_statistics_incoursemodules_parameters() {

        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );

        $templateid = new external_value(
            PARAM_INT,
            'The template id',
            VALUE_REQUIRED
        );

        $params = [
            'competencyid' => $competencyid,
            'templateid' => $templateid,
        ];
        return new external_function_parameters($params);
    }

    /**
     * Returns the competency statistics in course modules.
     *
     * @param int $competencyid Competency id.
     * @param int $templateid Template id.
     * @return array
     */
    public static function get_competency_statistics_incoursemodules($competencyid, $templateid) {
        global $PAGE;

        $params = self::validate_parameters(self::get_competency_statistics_incoursemodules_parameters(), [
            'competencyid' => $competencyid,
            'templateid' => $templateid,
        ]);
        $context = context_system::instance();
        self::validate_context($context);

        $result = api::get_competency_statistics_in_coursemodules($params['competencyid'], $params['templateid']);

        $output = $PAGE->get_renderer('report_lpmonitoring');
        $exporter = new lpmonitoring_competency_statistics_incoursemodule_exporter($result);
        $record = $exporter->export($output);

        return $record;
    }

    /**
     * Returns description of get_competency_statistics_incoursemodules() result value.
     *
     * @return \external_description
     */
    public static function get_competency_statistics_incoursemodules_returns() {
        return lpmonitoring_competency_statistics_incoursemodule_exporter::get_read_structure();
    }

    /**
     * Describes the parameters for submit_manage_tags_form webservice.
     * @return external_function_parameters
     */
    public static function submit_manage_tags_form_parameters() {
        return new external_function_parameters(
            [
                'contextid' => new external_value(PARAM_INT, 'The context id'),
                'jsonformdata' => new external_value(PARAM_RAW, 'The data from the manage tags form, encoded as a json array'),
            ]
        );
    }

    /**
     * Submit the manage tags form.
     *
     * @param int $contextid The context id for the user.
     * @param string $jsonformdata The data from the form, encoded as a json array.
     * @return int the new number of tags associated to the learning plan.
     */
    public static function submit_manage_tags_form($contextid, $jsonformdata) {
        // We always must pass webservice params through validate_parameters.
        $params = self::validate_parameters(self::submit_manage_tags_form_parameters(),
                                            ['contextid' => $contextid, 'jsonformdata' => $jsonformdata]);

        $context = context::instance_by_id($params['contextid'], MUST_EXIST);
        self::validate_context($context);

        $serialiseddata = json_decode($params['jsonformdata']);

        $data = [];
        parse_str($serialiseddata, $data);

        // The last param is the ajax submitted data.
        $mform = new \report_lpmonitoring\form\tags(null, ['planid' => $data['planid']], 'post', '', null, true, $data);

        $validateddata = $mform->get_data();

        if ($validateddata) {
            // Save the tags.
            core_tag_tag::set_item_tags('report_lpmonitoring', 'competency_plan',
                                        $validateddata->planid, $context, $validateddata->tags);
        } else {
            // Generate a warning.
            throw new moodle_exception('errormanagetags', 'report_lpmonitoring');
        }

        return count(core_tag_tag::get_item_tags('report_lpmonitoring', 'competency_plan', $validateddata->planid));
    }

    /**
     * Returns description of submit_manage_tags_form() result value.
     *
     * @return \external_description
     */
    public static function submit_manage_tags_form_returns() {
        return new external_value(PARAM_INT, 'The number of tags associated to the learning plan');
    }

    /**
     * Describes the parameters for search_plans_with_tag webservice.
     * @return external_function_parameters
     */
    public static function search_plans_with_tag_parameters() {
        return new external_function_parameters(
            [
                'tagid' => new external_value(PARAM_INT, 'The tag ID'),
                'withcomments' => new external_value(PARAM_BOOL, 'Only plans with comments'),
            ]
        );
    }

    /**
     * Get the plans with a specific tag (but only plans that the user can view).
     *
     * @param int $tagid The tag id.
     * @param bool $withcomments The tag with comments.
     * @return array
     */
    public static function search_plans_with_tag($tagid, $withcomments) {
        global $PAGE;

        $context = context_system::instance();
        self::validate_context($context);

        $plans = api::search_plans_with_tag($tagid, $withcomments);
        foreach ($plans as $index => $plan) {
            // Return profileimage as url instead of object.
            $plans[$index]['profileimage'] = $plans[$index]['profileimage']->get_url($PAGE)->out(false);
            $plans[$index]['profileimagesmall'] = $plans[$index]['profileimage'];
        }
        return (array) (object) $plans;
    }

    /**
     * Returns description of search_plans_with_tag() result value.
     *
     * @return \external_description
     */
    public static function search_plans_with_tag_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'fullname' => new external_value(PARAM_TEXT, 'The fullname of the user'),
                'profileimage' => new external_value(PARAM_TEXT, 'The profile image small size'),
                'profileimagesmall' => new external_value(PARAM_TEXT, 'The profile image small size'),
                'userid' => new external_value(PARAM_INT, 'The user id value'),
                'planid' => new external_value(PARAM_INT, 'The plan id value'),
                'planname' => new external_value(PARAM_TEXT, 'The name of the learning plan template'),
            ])
        );
    }

    /**
     * Describes the parameters for search_tags_for_accessible_plans webservice.
     * @return external_function_parameters
     */
    public static function search_tags_for_accessible_plans_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Get the plans with a specific tag (but only plans that the user can view).
     *
     * @return array
     */
    public static function search_tags_for_accessible_plans() {
        $tags = api::search_tags_for_accessible_plans();
        $return = [];
        foreach ($tags as $tagid => $tag) {
            $return[] = ['id' => $tagid, 'tag' => $tag];
        }
        return $return;
    }

    /**
     * Returns description of search_tags_for_accessible_plans() result value.
     *
     * @return \external_description
     */
    public static function search_tags_for_accessible_plans_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'The tag ID'),
                'tag' => new external_value(PARAM_TEXT, 'The tag'),
            ])
        );
    }

    /**
     * Returns description of get_comment_area_for_plan() parameters.
     *
     * @return \external_function_parameters
     */
    public static function get_comment_area_for_plan_parameters() {
        $planid = new external_value(
            PARAM_INT,
            'The plan id',
            VALUE_REQUIRED
        );
        $params = ['planid' => $planid];
        return new external_function_parameters($params);
    }

    /**
     * Loads the data required to render a comment area of a learning plan.
     *
     * @param int $planid Learning Plan id.
     * @return stdClass
     */
    public static function get_comment_area_for_plan($planid) {
        global $PAGE;
        $params = self::validate_parameters(self::get_comment_area_for_plan_parameters(), [
            'planid' => $planid,
        ]);

        $plan = new plan($params['planid']);
        self::validate_context($plan->get_context());

        $output = $PAGE->get_renderer('core');
        $commentareaexporter = new comment_area_exporter($plan->get_comment_object());
        return $commentareaexporter->export($output);
    }

    /**
     * Returns description of get_comment_area_for_plan() result value.
     *
     * @return \external_description
     */
    public static function get_comment_area_for_plan_returns() {
        return comment_area_exporter::get_read_structure();
    }

    /**
     * Returns description of list_plan_competencies_report() parameters.
     *
     * @return \external_description
     */
    public static function list_plan_competencies_report_parameters() {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'The plan ID.'),
        ]);
    }

    /**
     * List plan competencies for the report.
     * @param  int $id The plan ID.
     * @return array
     */
    public static function list_plan_competencies_report($id) {
        global $PAGE;
        $context = context_system::instance();
        self::validate_context($context);
        $output = $PAGE->get_renderer('report_lpmonitoring');

        $plan = \core_competency\api::read_plan($id);
        $resultcompetencies = self::list_plan_competencies($id);
        $exporter = new list_plan_competency_report_exporter($resultcompetencies, ['plan' => $plan]);
        $result = $exporter->export($output);
        return $result;
    }

    /**
     * Returns description of list_plan_competencies_report() result value.
     *
     * @return \external_description
     */
    public static function list_plan_competencies_report_returns() {
        return list_plan_competency_report_exporter::get_read_structure();
    }

    /**
     * Returns description of list_plan_competencies_summary() parameters.
     *
     * @return \external_description
     */
    public static function list_plan_competencies_summary_parameters() {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'The plan ID.'),
        ]);
    }

    /**
     * List plan competencies for the summary.
     * @param  int $id The plan ID.
     * @return array
     */
    public static function list_plan_competencies_summary($id) {
        global $PAGE;
        $context = context_system::instance();
        self::validate_context($context);
        $output = $PAGE->get_renderer('report_lpmonitoring');

        $plan = \core_competency\api::read_plan($id);
        $resultcompetencies = self::list_plan_competencies($id, true);
        $exporter = new scale_competency_summary_exporter($resultcompetencies, ['plan' => $plan]);
        $result = $exporter->export($output);
        return $result;
    }

    /**
     * Returns description of list_plan_competencies_summary() result value.
     *
     * @return \external_description
     */
    public static function list_plan_competencies_summary_returns() {
        return scale_competency_summary_exporter::get_read_structure();
    }

    /**
     * Returns description of add_rating_task() parameters.
     *
     * @return \external_function_parameters
     */
    public static function add_rating_task_parameters() {
        $templateid = new external_value(
            PARAM_INT,
            'The template id',
            VALUE_REQUIRED
        );
        $defaultscalesvalues = new external_value(
            PARAM_RAW,
            'Default scales values',
            VALUE_REQUIRED
        );
        $forcerating = new external_value(
            PARAM_BOOL,
            'Force rating on users already rated',
            VALUE_DEFAULT,
            false
        );
        $params = [
            'templateid' => $templateid,
            'defaultscalesvalues' => $defaultscalesvalues,
            'forcerating' => $forcerating,
        ];
        return new external_function_parameters($params);
    }

    /**
     * Add task for rating competencies in template.
     *
     * @param int $templateid The template id
     * @param string $defaultscalesvalues Default scales values
     * @param boolean $forcerating Force rating on users already rated
     * @return boolean
     */
    public static function add_rating_task($templateid, $defaultscalesvalues, $forcerating) {
        $params = self::validate_parameters(
            self::add_rating_task_parameters(),
            [
                'templateid' => $templateid,
                'defaultscalesvalues' => $defaultscalesvalues,
                'forcerating' => $forcerating,
            ]
        );
        api::add_rating_task($params['templateid'], $params['forcerating'], json_decode($params['defaultscalesvalues']));
        return true;
    }

    /**
     * Returns description of add_rating_task() result value.
     *
     * @return \external_description
     */
    public static function add_rating_task_returns() {
        return new external_value(PARAM_BOOL, 'True if adding was successful');
    }

    /**
     * Returns description of reset_grading() parameter
     *
     * @return \external_function_parameters
     */
    public static function reset_grading_parameters() {
        $planid = new external_value(
            PARAM_INT,
            'Plan ID',
            VALUE_REQUIRED
        );
        $note = new external_value(
            PARAM_NOTAGS,
            'A note to attach to the evidence',
            VALUE_DEFAULT
        );
        $competencyid = new external_value(
            PARAM_INT,
            'Competency ID (or null for all competencies)',
            VALUE_REQUIRED
        );

        $params = [
            'planid' => $planid,
            'note' => $note,
            'competencyid' => $competencyid,
        ];
        return new external_function_parameters($params);
    }

    /**
     * Reset the grading of users competencies (one particular competency or all competencies of a plan).
     *
     * @param int $planid The learning plan ID.
     * @param string $note A note to attach to the evidence
     * @param int $competencyid The competency id (or null for all competencies of this plan).
     * @return bool
     */
    public static function reset_grading($planid, $note, $competencyid) {
        $params = self::validate_parameters(self::reset_grading_parameters(), [
            'planid' => $planid,
            'note' => $note,
            'competencyid' => $competencyid,
        ]);

        api::reset_grading($params['planid'], $params['note'], $params['competencyid']);

        return true;
    }

    /**
     * Returns description of reset_grading() result value.
     *
     * @return \external_value
     */
    public static function reset_grading_returns() {
        return new external_value(PARAM_BOOL, 'True if grade(s) was resetted');
    }

    /**
     * Returns description of user_competency_viewed_in_course() parameters.
     *
     * @return \external_function_parameters
     */
    public static function user_competency_viewed_in_course_parameters() {
        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );
        $userid = new external_value(
            PARAM_INT,
            'The user id',
            VALUE_REQUIRED
        );
        $courseid = new external_value(
            PARAM_INT,
            'The course id',
            VALUE_REQUIRED
        );
        $params = [
            'competencyid' => $competencyid,
            'userid' => $userid,
            'courseid' => $courseid,
        ];
        return new external_function_parameters($params);
    }

    /**
     * Log user competency viewed in course event.
     *
     * @param int $competencyid The competency ID.
     * @param int $userid The user ID.
     * @param int $courseid The course ID.
     * @return boolean
     */
    public static function user_competency_viewed_in_course($competencyid, $userid, $courseid) {
        $params = self::validate_parameters(self::user_competency_viewed_in_course_parameters(), [
            'competencyid' => $competencyid,
            'userid' => $userid,
            'courseid' => $courseid,
        ]);
        $ucc = core_competency_api::get_user_competency_in_course($params['courseid'], $params['userid'], $params['competencyid']);
        $result = api::user_competency_viewed_in_course($ucc);

        return $result;
    }

    /**
     * Returns description of user_competency_viewed_in_course() result value.
     *
     * @return \external_description
     */
    public static function user_competency_viewed_in_course_returns() {
        return new external_value(PARAM_BOOL, 'True if the event user competency viewed in course was logged');
    }

    /**
     * Returns description of data_for_user_competency_summary_in_course() parameters.
     *
     * @return \external_function_parameters
     */
    public static function data_for_user_competency_summary_in_course_parameters() {
        $userid = new external_value(
            PARAM_INT,
            'Data base record id for the user',
            VALUE_REQUIRED
        );
        $competencyid = new external_value(
            PARAM_INT,
            'Data base record id for the competency',
            VALUE_REQUIRED
        );
        $courseid = new external_value(
            PARAM_INT,
            'Data base record id for the course',
            VALUE_REQUIRED
        );

        $params = [
            'userid' => $userid,
            'competencyid' => $competencyid,
            'courseid' => $courseid,
        ];
        return new external_function_parameters($params);
    }

    /**
     * Read a user competency summary in course.
     *
     * @param int $userid The user id
     * @param int $competencyid The competency id
     * @param int $courseid The course id
     * @return \stdClass
     */
    public static function data_for_user_competency_summary_in_course($userid, $competencyid, $courseid) {
        global $PAGE;
        $params = self::validate_parameters(self::data_for_user_competency_summary_in_course_parameters(), [
            'userid' => $userid,
            'competencyid' => $competencyid,
            'courseid' => $courseid,
        ]);
        $context = context_user::instance($params['userid']);
        self::validate_context($context);
        $output = $PAGE->get_renderer('tool_lp');

        $renderable = new lpmonitoring_user_competency_summary_in_course($params['userid'], $params['competencyid'],
            $params['courseid']);
        return $renderable->export_for_template($output);
    }

    /**
     * Returns description of data_for_user_competency_summary_in_course() result value.
     *
     * @return \external_description
     */
    public static function data_for_user_competency_summary_in_course_returns() {
        return lpmonitoring_user_competency_summary_in_course_exporter::get_read_structure();
    }

    /**
     * Returns description of get_user_pdfs parameters.
     * @return external_function_parameters
     */
    public static function get_user_pdfs_parameters() {
        return new external_function_parameters(
            [
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'user' => new external_value(PARAM_RAW, 'User identifier. Currently configured to use ' .
                                    \get_config('report_lpmonitoring', 'studentidmapping') . ' (the config variable used is ' .
                                    'report_lpmonitoring | studentidmapping). Speak to your Moodle administrator for more ' .
                                    'details.'),

                            'cohort' => new external_value(PARAM_RAW, 'idnumber of cohort (optional). If set, will only include ' .
                                    'plans in the PDF that are associated with the given cohort (in the ' .
                                    'competency_templatecohort table).', VALUE_OPTIONAL),
                        ]
                    )
                ),
            ]
        );
    }

    /**
     * Returns description of get_user_pdf parameters.
     * @return external_single_parameters
     */
    public static function get_user_pdf_parameters() {
        return new external_function_parameters(
            [
                'params' => new external_single_structure(
                    [
                        'user' => new external_value(PARAM_RAW, 'User identifier. Currently configured to use ' .
                                \get_config('report_lpmonitoring', 'studentidmapping') . ' (the config variable used is ' .
                                'report_lpmonitoring | studentidmapping). Speak to your Moodle administrator for more ' .
                                'details.'),

                        'cohort' => new external_value(PARAM_RAW, 'idnumber of cohort (optional). If set, will only include ' .
                                'plans in the PDF that are associated with the given cohort (in the ' .
                                'competency_templatecohort table).', VALUE_OPTIONAL),
                    ]
                ),
            ]
        );
    }

    /**
     * Returns description of get_user_pdfs return value.
     * @return external_multiple_structure
     */
    public static function get_user_pdfs_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'user' => new external_value(PARAM_RAW, 'User identifier. Currently configured to use ' .
                            \get_config('report_lpmonitoring', 'studentidmapping') . ' (the config variable used is ' .
                            'report_lpmonitoring | studentidmapping). Speak to your Moodle administrator for more details.'),

                    'timecreated' => new external_value(PARAM_INT, 'Unix timestamp of the date / time the PDF was created.'),

                    'pdf' => new external_value(PARAM_RAW, 'Base 64 encoded string of the PDF file that was generated.'),

                    'cohort' => new external_value(PARAM_RAW, 'idnumber of cohort (optional). If set, will only include plans ' .
                            'in the PDF that are associated with the given cohort (in the competency_templatecohort table).',
                            VALUE_OPTIONAL),
                ]
            )
        );
    }

    /**
     * Returns description of get_user_pdf return value.
     * @return external_single_structure
     */
    public static function get_user_pdf_returns() {
        return new external_single_structure(
            [
                'user' => new external_value(PARAM_RAW, 'User identifier. Currently configured to use ' .
                        \get_config('report_lpmonitoring', 'studentidmapping') . ' (the config variable used is ' .
                        'report_lpmonitoring | studentidmapping). Speak to your Moodle administrator for more details.'),

                'timecreated' => new external_value(PARAM_INT, 'Unix timestamp of the date / time the PDF was created.'),

                'pdf' => new external_value(PARAM_RAW, 'Base 64 encoded string of the PDF file that was generated.'),

                'cohort' => new external_value(PARAM_RAW, 'idnumber of cohort (optional). If set, will only include plans ' .
                        'in the PDF that are associated with the given cohort (in the competency_templatecohort table).',
                        VALUE_OPTIONAL),
            ]
        );
    }

    /**
     * Generates and sends the requested PDFs, base64 encoded.
     * @param  array $users Array of users with user and cohort (optional) as keys.
     * @return array With the PDF, timecreated, cohort and user ID for each user requested.
     */
    public static function get_user_pdfs($users) {
        global $CFG, $DB;
        require_once($CFG->libdir . '/pdflib.php');

        $params = self::validate_parameters(self::get_user_pdfs_parameters(), ['users' => $users]);

        $studentidfield = \get_config('report_lpmonitoring', 'studentidmapping');

        if ($studentidfield != 'id') {
            $shortname = explode("profile_field_", $studentidfield)[1];
        }

        $users = [];
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/competency:planview', $context);

        foreach ($params['users'] as $user) {

            $user = (object)$user;

            if (isset($user->cohort)) {
                $cohort = $user->cohort;
            } else {
                $cohort = false;
            }

            // An exception gets thrown if we can't find a unique user or if there are no plans / competencies.
            try {
                if ($studentidfield != 'id') {
                    $userid = \report_lpmonitoring\external\user_pdf::get_userid_from_profile_field($shortname, $user->user);
                } else {
                    $userid = $user->user;
                }

                $userpdf = new \report_lpmonitoring\external\user_pdf($userid, $cohort);
                $user->pdf = $userpdf->get_encoded_pdf();
                $user->timecreated = $userpdf->timecreated;
            } catch (\Exception $e) {
                // If there's a problem, return false / 0 (along with the user / cohort parameters we originally received).
                $user->pdf = false;
                $user->timecreated = 0;
            }

            $users[] = (array)$user;
        }
        return $users;
    }

    /**
     * Generates and sends the requested PDF, base64 encoded.
     * @param array $params Associative array with user and cohort (optional) to generate a PDF for.
     * @return array With the PDF, timecreated, cohort and user ID for each user requested.
     */
    public static function get_user_pdf($params) {
        global $CFG, $DB;
        require_once($CFG->libdir . '/pdflib.php');

        $params = self::validate_parameters(self::get_user_pdf_parameters(), ['params' => $params]);

        $studentidfield = \get_config('report_lpmonitoring', 'studentidmapping');

        if ($studentidfield != 'id') {
            $shortname = explode("profile_field_", $studentidfield)[1];
        }

        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/competency:planview', $context);

        $params = (object)$params['params'];

        if (isset($params->cohort)) {
            $cohort = $params->cohort;
        } else {
            $cohort = false;
        }

        // An exception gets thrown if we can't find a unique user or if there are no plans / competencies.
        try {
            if ($studentidfield != 'id') {
                $userid = \report_lpmonitoring\external\user_pdf::get_userid_from_profile_field($shortname, $params->user);
            } else {
                $userid = $params->user;
            }

            $userpdf = new \report_lpmonitoring\external\user_pdf($userid, $cohort);
            $params->pdf = $userpdf->get_encoded_pdf();
            $params->timecreated = $userpdf->timecreated;
        } catch (\Exception $e) {
            // If there's a problem, return false / 0 (along with the user / cohort parameters we originally received).
            $params->pdf = false;
            $params->timecreated = 0;
        }
        return (array)$params;
    }
}

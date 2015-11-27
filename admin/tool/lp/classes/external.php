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
 * This is the external API for this tool.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp;

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->libdir/grade/grade_scale.php");

use context;
use context_system;
use context_course;
use context_user;
use coding_exception;
use external_api;
use external_function_parameters;
use external_value;
use external_format_value;
use external_single_structure;
use external_multiple_structure;
use invalid_parameter_exception;
use grade_scale;
use tool_lp\external\competency_framework_exporter;
use tool_lp\external\competency_summary_exporter;
use tool_lp\external\user_summary_exporter;
use tool_lp\external\user_competency_exporter;
use tool_lp\external\user_competency_plan_exporter;
use tool_lp\external\competency_exporter;
use tool_lp\external\course_summary_exporter;
use tool_lp\external\plan_exporter;
use tool_lp\external\template_exporter;

/**
 * This is the external API for this tool.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * Returns description of a generic list() parameters.
     *
     * @return \external_function_parameters
     */
    protected static function list_parameters_structure() {
        $filters = new external_multiple_structure(new external_single_structure(
            array(
                'column' => new external_value(PARAM_ALPHANUMEXT, 'Column name to filter by'),
                'value' => new external_value(PARAM_TEXT, 'Value to filter by. Must be exact match')
            )
        ));
        $sort = new external_value(
            PARAM_ALPHANUMEXT,
            'Column to sort by.',
            VALUE_DEFAULT,
            ''
        );
        $order = new external_value(
            PARAM_ALPHA,
            'Sort direction. Should be either ASC or DESC',
            VALUE_DEFAULT,
            ''
        );
        $skip = new external_value(
            PARAM_INT,
            'Skip this number of records before returning results',
            VALUE_DEFAULT,
            0
        );
        $limit = new external_value(
            PARAM_INT,
            'Return this number of records at most.',
            VALUE_DEFAULT,
            0
        );

        $params = array(
            'filters' => $filters,
            'sort' => $sort,
            'order' => $order,
            'skip' => $skip,
            'limit' => $limit
        );
        return new external_function_parameters($params);
    }

    /**
     * Returns a prepared structure to use a context parameters.
     * @return external_single_structure
     */
    protected static function get_context_parameters() {
        $id = new external_value(
            PARAM_INT,
            'Context ID. Either use this value, or level and instanceid.',
            VALUE_DEFAULT,
            0
        );
        $level = new external_value(
            PARAM_ALPHA,
            'Context level. To be used with instanceid.',
            VALUE_DEFAULT,
            ''
        );
        $instanceid = new external_value(
            PARAM_INT,
            'Context instance ID. To be used with level',
            VALUE_DEFAULT,
            0
        );
        return new external_single_structure(array(
            'contextid' => $id,
            'contextlevel' => $level,
            'instanceid' => $instanceid,
        ));
    }

    /**
     * Returns description of a generic count_x() parameters.
     *
     * @return \external_function_parameters
     */
    public static function count_parameters_structure() {
        $filters = new external_multiple_structure(new external_single_structure(
            array(
                'column' => new external_value(PARAM_ALPHANUMEXT, 'Column name to filter by'),
                'value' => new external_value(PARAM_TEXT, 'Value to filter by. Must be exact match')
            )
        ));

        $params = array(
            'filters' => $filters,
        );
        return new external_function_parameters($params);
    }

    /**
     * Returns description of create_competency_framework() parameters.
     *
     * @return \external_function_parameters
     */
    public static function create_competency_framework_parameters() {
        $structure = competency_framework_exporter::get_create_structure();
        $params = array('competencyframework' => $structure);
        return new external_function_parameters($params);
    }

    /**
     * Create a new competency framework
     *
     * @param array $competencyframework A single param with all the fields for a competency framework.
     * @return \stdClass The new record
     */
    public static function create_competency_framework($competencyframework) {
        global $PAGE;

        $params = self::validate_parameters(self::create_competency_framework_parameters(),
                                            array('competencyframework' => $competencyframework));

        $params = $params['competencyframework'];

        $context = self::get_context_from_params($params);
        self::validate_context($context);
        $output = $PAGE->get_renderer('tool_lp');

        unset($params['contextlevel']);
        unset($params['instanceid']);
        $params['contextid'] = $context->id;

        $params = (object) $params;
        $result = api::create_framework($params);
        $exporter = new competency_framework_exporter($result);
        $record = $exporter->export($output);
        return $record;
    }

    /**
     * Returns description of create_competency_framework() result value.
     *
     * @return \external_description
     */
    public static function create_competency_framework_returns() {
        return competency_framework_exporter::get_read_structure();
    }

    /**
     * Returns description of read_competency_framework() parameters.
     *
     * @return \external_function_parameters
     */
    public static function read_competency_framework_parameters() {
        $id = new external_value(
            PARAM_INT,
            'Data base record id for the framework',
            VALUE_REQUIRED
        );

        $params = array(
            'id' => $id,
        );
        return new external_function_parameters($params);
    }

    /**
     * Read a competency framework by id.
     *
     * @param int $id The id of the framework.
     * @return \stdClass
     */
    public static function read_competency_framework($id) {
        global $PAGE;

        $params = self::validate_parameters(self::read_competency_framework_parameters(),
                                            array(
                                                'id' => $id,
                                            ));

        $framework = api::read_framework($params['id']);
        self::validate_context($framework->get_context());
        $output = $PAGE->get_renderer('tool_lp');
        $exporter = new competency_framework_exporter($framework);
        $record = $exporter->export($output);
        return $record;
    }

    /**
     * Returns description of read_competency_framework() result value.
     *
     * @return \external_description
     */
    public static function read_competency_framework_returns() {
        return competency_framework_exporter::get_read_structure();
    }

    /**
     * Returns description of duplicate_competency_framework() parameters.
     *
     * @return \external_function_parameters
     */
    public static function duplicate_competency_framework_parameters() {
        $id = new external_value(
            PARAM_INT,
            'Data base record id for the framework',
            VALUE_REQUIRED
        );

        $params = array(
            'id' => $id,
        );
        return new external_function_parameters($params);
    }

    /**
     * Duplicate a competency framework
     *
     * @param int $id The competency framework id
     * @return boolean
     */
    public static function duplicate_competency_framework($id) {
        $params = self::validate_parameters(self::duplicate_competency_framework_parameters(),
                                            array(
                                                'id' => $id,
                                            ));

        $framework = api::read_framework($params['id']);
        self::validate_context($framework->get_context());

        return api::duplicate_framework($params['id']);
    }

    /**
     * Returns description of duplicate_competency_framework() result value.
     *
     * @return \external_description
     */
    public static function duplicate_competency_framework_returns() {
        return competency_framework_exporter::get_read_structure();
    }

    /**
     * Returns description of delete_competency_framework() parameters.
     *
     * @return \external_function_parameters
     */
    public static function delete_competency_framework_parameters() {
        $id = new external_value(
            PARAM_INT,
            'Data base record id for the framework',
            VALUE_REQUIRED
        );

        $params = array(
            'id' => $id,
        );
        return new external_function_parameters($params);
    }

    /**
     * Delete a competency framework
     *
     * @param int $id The competency framework id
     * @return boolean
     */
    public static function delete_competency_framework($id) {
        $params = self::validate_parameters(self::delete_competency_framework_parameters(),
                                            array(
                                                'id' => $id,
                                            ));

        $framework = api::read_framework($params['id']);
        self::validate_context($framework->get_context());

        return api::delete_framework($params['id']);
    }

    /**
     * Returns description of delete_competency_framework() result value.
     *
     * @return \external_description
     */
    public static function delete_competency_framework_returns() {
        return new external_value(PARAM_BOOL, 'True if the delete was successful');
    }

    /**
     * Returns description of update_competency_framework() parameters.
     *
     * @return \external_function_parameters
     */
    public static function update_competency_framework_parameters() {
        $structure = competency_framework_exporter::get_update_structure();
        $params = array('competencyframework' => $structure);
        return new external_function_parameters($params);
    }

    /**
     * Update an existing competency framework
     *
     * @param array $competencyframework An array with all the fields for a competency framework.
     * @return boolean
     */
    public static function update_competency_framework($competencyframework) {

        $params = self::validate_parameters(self::update_competency_framework_parameters(),
                                            array(
                                                'competencyframework' => $competencyframework
                                            ));

        $params = $params['competencyframework'];

        $framework = api::read_framework($params['id']);
        self::validate_context($framework->get_context());

        $params = (object) $params;

        return api::update_framework($params);
    }

    /**
     * Returns description of update_competency_framework() result value.
     *
     * @return \external_description
     */
    public static function update_competency_framework_returns() {
        return new external_value(PARAM_BOOL, 'True if the update was successful');
    }

    /**
     * Returns description of list_competency_frameworks() parameters.
     *
     * @return \external_function_parameters
     */
    public static function list_competency_frameworks_parameters() {
        $sort = new external_value(
            PARAM_ALPHANUMEXT,
            'Column to sort by.',
            VALUE_DEFAULT,
            ''
        );
        $order = new external_value(
            PARAM_ALPHA,
            'Sort direction. Should be either ASC or DESC',
            VALUE_DEFAULT,
            ''
        );
        $skip = new external_value(
            PARAM_INT,
            'Skip this number of records before returning results',
            VALUE_DEFAULT,
            0
        );
        $limit = new external_value(
            PARAM_INT,
            'Return this number of records at most.',
            VALUE_DEFAULT,
            0
        );
        $includes = new external_value(
            PARAM_ALPHA,
            'What other contextes to fetch the frameworks from. (children, parents, self)',
            VALUE_DEFAULT,
            'children'
        );

        $params = array(
            'sort' => $sort,
            'order' => $order,
            'skip' => $skip,
            'limit' => $limit,
            'context' => self::get_context_parameters(),
            'includes' => $includes
        );
        return new external_function_parameters($params);
    }

    /**
     * List the existing competency frameworks
     *
     * @param string $filters
     * @param int $sort
     * @param string $order
     * @param string $skip
     * @param int $limit
     * @param array $context
     * @param bool $includes
     *
     * @return array
     * @throws \required_capability_exception
     * @throws invalid_parameter_exception
     */
    public static function list_competency_frameworks($sort, $order, $skip, $limit, $context, $includes) {
        global $PAGE;

        $params = self::validate_parameters(self::list_competency_frameworks_parameters(),
                                            array(
                                                'sort' => $sort,
                                                'order' => $order,
                                                'skip' => $skip,
                                                'limit' => $limit,
                                                'context' => $context,
                                                'includes' => $includes
                                            ));

        $context = self::get_context_from_params($params['context']);
        self::validate_context($context);
        $output = $PAGE->get_renderer('tool_lp');

        if ($params['order'] !== '' && $params['order'] !== 'ASC' && $params['order'] !== 'DESC') {
            throw new invalid_parameter_exception('Invalid order param. Must be ASC, DESC or empty.');
        }

        $results = api::list_frameworks($params['sort'],
                                       $params['order'],
                                       $params['skip'],
                                       $params['limit'],
                                       $context,
                                       $params['includes']);
        $records = array();
        foreach ($results as $result) {
            $exporter = new competency_framework_exporter($result);
            $record = $exporter->export($output);
            array_push($records, $record);
        }
        return $records;
    }

    /**
     * Returns description of list_competency_frameworks() result value.
     *
     * @return \external_description
     */
    public static function list_competency_frameworks_returns() {
        return new external_multiple_structure(competency_framework_exporter::get_read_structure());
    }

    /**
     * Returns description of count_competency_frameworks() parameters.
     *
     * @return \external_function_parameters
     */
    public static function count_competency_frameworks_parameters() {
        $includes = new external_value(
            PARAM_ALPHA,
            'What other contextes to fetch the frameworks from. (children, parents, self)',
            VALUE_DEFAULT,
            'children'
        );

        $params = array(
            'context' => self::get_context_parameters(),
            'includes' => $includes
        );
        return new external_function_parameters($params);
    }

    /**
     * Count the existing competency frameworks
     *
     * @param string $filters Filters to use.
     * @return boolean
     */
    public static function count_competency_frameworks($context, $includes) {
        $params = self::validate_parameters(self::count_competency_frameworks_parameters(),
                                            array(
                                                'context' => $context,
                                                'includes' => $includes
                                            ));

        $context = self::get_context_from_params($params['context']);
        self::validate_context($context);

        return api::count_frameworks($context, $params['includes']);
    }

    /**
     * Returns description of count_competency_frameworks() result value.
     *
     * @return \external_description
     */
    public static function count_competency_frameworks_returns() {
        return new external_value(PARAM_INT, 'The number of competency frameworks found.');
    }

    /**
     * Returns description of data_for_competency_frameworks_manage_page() parameters.
     *
     * @return \external_function_parameters
     */
    public static function data_for_competency_frameworks_manage_page_parameters() {
        $params = array('pagecontext' => self::get_context_parameters());
        return new external_function_parameters($params);
    }

    /**
     * Loads the data required to render the competency_frameworks_manage_page template.
     *
     * @return boolean
     */
    public static function data_for_competency_frameworks_manage_page($pagecontext) {
        global $PAGE;

        $params = self::validate_parameters(
            self::data_for_competency_frameworks_manage_page_parameters(),
            array(
                'pagecontext' => $pagecontext
            )
        );
        $context = self::get_context_from_params($params['pagecontext']);
        self::validate_context($context);

        $renderable = new output\manage_competency_frameworks_page($context);
        $renderer = $PAGE->get_renderer('tool_lp');

        $data = $renderable->export_for_template($renderer);

        return $data;
    }

    /**
     * Returns description of data_for_competency_frameworks_manage_page() result value.
     *
     * @return \external_description
     */
    public static function data_for_competency_frameworks_manage_page_returns() {
        return new external_single_structure(array (
            'canmanage' => new external_value(PARAM_BOOL, 'True if this user has permission to manage competency frameworks'),
            'competencyframeworks' => new external_multiple_structure(
                competency_framework_exporter::get_read_structure()
            ),
            'pluginbaseurl' => new external_value(PARAM_LOCALURL, 'Url to the tool_lp plugin folder on this Moodle site'),
            'navigation' => new external_multiple_structure(
                new external_value(PARAM_RAW, 'HTML for a navigation item that should be on this page')
            )
        ));

    }

    /**
     * Returns description of create_competency() parameters.
     *
     * @return \external_function_parameters
     */
    public static function create_competency_parameters() {
        $structure = competency_exporter::get_create_structure();
        $params = array('competency' => $structure);
        return new external_function_parameters($params);
    }

    /**
     * Create a new competency
     *
     * @param array $competency All the fields for a competency record (including id)
     * @return array the competency
     */
    public static function create_competency($competency) {
        global $PAGE;

        $params = self::validate_parameters(self::create_competency_parameters(),
                                            array('competency' => $competency));

        $params = $params['competency'];
        $framework = api::read_framework($params['competencyframeworkid']);
        $context = $framework->get_context();
        self::validate_context($context);
        $output = $PAGE->get_renderer('tool_lp');

        $params = (object) $params;
        $result = api::create_competency($params);
        $exporter = new competency_exporter($result, array('context' => $context));
        $record = $exporter->export($output);
        return $record;
    }

    /**
     * Returns description of create_competency() result value.
     *
     * @return \external_description
     */
    public static function create_competency_returns() {
        return competency_exporter::get_read_structure();
    }

    /**
     * Returns description of read_competency() parameters.
     *
     * @return \external_function_parameters
     */
    public static function read_competency_parameters() {
        $id = new external_value(
            PARAM_INT,
            'Data base record id for the competency',
            VALUE_REQUIRED
        );

        $params = array(
            'id' => $id,
        );
        return new external_function_parameters($params);
    }

    /**
     * Read a competency by id.
     *
     * @param int $id The id of the competency
     * @return \stdClass
     */
    public static function read_competency($id) {
        global $PAGE;

        $params = self::validate_parameters(self::read_competency_parameters(),
                                            array(
                                                'id' => $id,
                                            ));

        $competency = api::read_competency($params['id']);
        $context = $competency->get_context();
        self::validate_context($context);
        $output = $PAGE->get_renderer('tool_lp');
        $exporter = new competency_exporter($competency, array('context' => $context));
        $record = $exporter->export($output);
        return $record;
    }

    /**
     * Returns description of read_competency() result value.
     *
     * @return \external_description
     */
    public static function read_competency_returns() {
        return competency_exporter::get_read_structure();
    }

    /**
     * Returns description of delete_competency() parameters.
     *
     * @return \external_function_parameters
     */
    public static function delete_competency_parameters() {
        $id = new external_value(
            PARAM_INT,
            'Data base record id for the competency',
            VALUE_REQUIRED
        );

        $params = array(
            'id' => $id,
        );
        return new external_function_parameters($params);
    }

    /**
     * Delete a competency
     *
     * @param int $id The competency id
     * @return boolean
     */
    public static function delete_competency($id) {
        $params = self::validate_parameters(self::delete_competency_parameters(),
                                            array(
                                                'id' => $id,
                                            ));

        $competency = api::read_competency($params['id']);
        $context = $competency->get_context();
        self::validate_context($context);

        return api::delete_competency($params['id']);
    }

    /**
     * Returns description of delete_competency() result value.
     *
     * @return \external_description
     */
    public static function delete_competency_returns() {
        return new external_value(PARAM_BOOL, 'True if the delete was successful');
    }

    /**
     * Returns description of update_competency() parameters.
     *
     * @return \external_function_parameters
     */
    public static function update_competency_parameters() {
        $structure = competency_exporter::get_update_structure();
        $params = array('competency' => $structure);
        return new external_function_parameters($params);
    }

    /**
     * Update an existing competency
     *
     * @param array $competency The array of competency fields (id is required).
     * @return boolean
     */
    public static function update_competency($competency) {

        $params = self::validate_parameters(self::update_competency_parameters(),
                                            array('competency' => $competency));
        $params = $params['competency'];

        $competency = api::read_competency($params['id']);
        self::validate_context($competency->get_context());

        $params = (object) $params;

        return api::update_competency($params);
    }

    /**
     * Returns description of update_competency_framework() result value.
     *
     * @return \external_description
     */
    public static function update_competency_returns() {
        return new external_value(PARAM_BOOL, 'True if the update was successful');
    }

    /**
     * Returns description of list_competencies() parameters.
     *
     * @return \external_function_parameters
     */
    public static function list_competencies_parameters() {
        return self::list_parameters_structure();
    }

    /**
     * List the existing competency.
     *
     * @param string $filters
     * @param int $sort
     * @param string $order
     * @param string $skip
     * @param int $limit
     *
     * @return array
     * @throws \required_capability_exception
     * @throws invalid_parameter_exception
     */
    public static function list_competencies($filters, $sort, $order, $skip, $limit) {
        global $PAGE;

        $params = self::validate_parameters(self::list_competencies_parameters(),
                                            array(
                                                'filters' => $filters,
                                                'sort' => $sort,
                                                'order' => $order,
                                                'skip' => $skip,
                                                'limit' => $limit
                                            ));

        if ($params['order'] !== '' && $params['order'] !== 'ASC' && $params['order'] !== 'DESC') {
            throw new invalid_parameter_exception('Invalid order param. Must be ASC, DESC or empty.');
        }

        $safefilters = array();
        $validcolumns = array('id', 'shortname', 'description', 'sortorder',
                              'idnumber', 'visible', 'parentid', 'competencyframeworkid');
        foreach ($params['filters'] as $filter) {
            if (!in_array($filter->column, $validcolumns)) {
                throw new invalid_parameter_exception('Filter column was invalid');
            }
            $safefilters[$filter->column] = $filter->value;
        }

        $context = null;
        if (isset($safefilters['competencyframeworkid'])) {
            $framework = api::read_framework($safefilters['competencyframeworkid']);
            $context = $framework->get_context();
        } else {
            $context = context_system::instance();
        }

        self::validate_context($context);
        $output = $PAGE->get_renderer('tool_lp');

        $results = api::list_competencies(
            $safefilters,
            $params['sort'],
            $params['order'],
            $params['skip'],
            $params['limit']
        );

        $records = array();
        foreach ($results as $result) {
            $exporter = new competency_exporter($result, array('context' => $context));
            $record = $exporter->export($output);
            array_push($records, $record);
        }
        return $records;
    }

    /**
     * Returns description of list_competencies() result value.
     *
     * @return \external_description
     */
    public static function list_competencies_returns() {
        return new external_multiple_structure(competency_exporter::get_read_structure());
    }

    /**
     * Returns description of search_competencies() parameters.
     *
     * @return \external_function_parameters
     */
    public static function search_competencies_parameters() {
        $searchtext = new external_value(
            PARAM_RAW,
            'Text to search for',
            VALUE_REQUIRED
        );
        $frameworkid = new external_value(
            PARAM_INT,
            'Competency framework id',
            VALUE_REQUIRED
        );

        $params = array(
            'searchtext' => $searchtext,
            'competencyframeworkid' => $frameworkid
        );
        return new external_function_parameters($params);
    }

    /**
     * List the existing competency frameworks
     *
     * @param string $searchtext Text to search.
     * @param int $competencyframeworkid Framework id.
     *
     * @return array
     */
    public static function search_competencies($searchtext, $competencyframeworkid) {
        global $PAGE;

        $params = self::validate_parameters(self::search_competencies_parameters(),
                                            array(
                                                'searchtext' => $searchtext,
                                                'competencyframeworkid' => $competencyframeworkid
                                            ));

        $framework = api::read_framework($params['competencyframeworkid']);
        $context = $framework->get_context();
        self::validate_context($context);
        $output = $PAGE->get_renderer('tool_lp');

        $results = api::search_competencies($params['searchtext'], $params['competencyframeworkid']);
        $records = array();
        foreach ($results as $result) {
            $exporter = new competency_exporter($result, array('context' => $context));
            $record = $exporter->export($output);

            array_push($records, $record);
        }

        return $records;
    }

    /**
     * Returns description of search_competencies() result value.
     *
     * @return \external_description
     */
    public static function search_competencies_returns() {
        return new external_multiple_structure(competency_exporter::get_read_structure());
    }

    /**
     * Returns description of count_competencies() parameters.
     *
     * @return \external_function_parameters
     */
    public static function count_competencies_parameters() {
        return self::count_parameters_structure();
    }

    /**
     * Count the existing competency frameworks.
     *
     * @param string $filters Filters to use.
     * @return boolean
     */
    public static function count_competencies($filters) {
        $params = self::validate_parameters(self::count_competencies_parameters(),
                                            array(
                                                'filters' => $filters
                                            ));

        $safefilters = array();
        $validcolumns = array('id', 'shortname', 'description', 'sortorder', 'idnumber',
                              'visible', 'parentid', 'competencyframeworkid');
        foreach ($params['filters'] as $filter) {
            if (!in_array($filter['column'], $validcolumns)) {
                throw new invalid_parameter_exception('Filter column was invalid');
            }
            $safefilters[$filter['column']] = $filter['value'];
        }

        $context = null;
        if (isset($safefilters['competencyframeworkid'])) {
            $framework = api::read_framework($safefilters['competencyframeworkid']);
            $context = $framework->get_context();
        } else {
            $context = context_system::instance();
        }

        self::validate_context($context);

        return api::count_competencies($safefilters);
    }

    /**
     * Returns description of count_competencies() result value.
     *
     * @return \external_description
     */
    public static function count_competencies_returns() {
        return new external_value(PARAM_INT, 'The number of competencies found.');
    }

    /**
     * Returns description of data_for_competencies_manage_page() parameters.
     *
     * @return \external_function_parameters
     */
    public static function data_for_competencies_manage_page_parameters() {
        $competencyframeworkid = new external_value(
            PARAM_INT,
            'The competency framework id',
            VALUE_REQUIRED
        );
        $search = new external_value(
            PARAM_RAW,
            'A search string',
            VALUE_DEFAULT,
            ''
        );
        $params = array(
            'competencyframeworkid' => $competencyframeworkid,
            'search' => $search
        );
        return new external_function_parameters($params);
    }

    /**
     * Loads the data required to render the competencies_manage_page template.
     *
     * @param int $competencyframeworkid Framework id.
     * @param string $search Text to search.
     *
     * @return boolean
     */
    public static function data_for_competencies_manage_page($competencyframeworkid, $search) {
        global $PAGE;

        $params = self::validate_parameters(self::data_for_competencies_manage_page_parameters(),
                                            array(
                                                'competencyframeworkid' => $competencyframeworkid,
                                                'search' => $search
                                            ));

        $framework = api::read_framework($params['competencyframeworkid']);
        self::validate_context($framework->get_context());
        $output = $PAGE->get_renderer('tool_lp');

        $renderable = new output\manage_competencies_page($framework, $params['search'], $framework->get_context());

        $data = $renderable->export_for_template($output);

        return $data;
    }

    /**
     * Returns description of data_for_competencies_manage_page() result value.
     *
     * @return \external_description
     */
    public static function data_for_competencies_manage_page_returns() {
        return new external_single_structure(array (
            'framework' => competency_framework_exporter::get_read_structure(),
            'canmanage' => new external_value(PARAM_BOOL, 'True if this user has permission to manage competency frameworks'),
            'pagecontextid' => new external_value(PARAM_INT, 'Context id for the framework'),
            'search' => new external_value(PARAM_RAW, 'Current search string'),
            'rulesmodules' => new external_multiple_structure(new external_single_structure(array(
                'type' => new external_value(PARAM_RAW, 'The rule type'),
                'amd' => new external_value(PARAM_RAW, 'The AMD module of the rule'),
                'name' => new external_value(PARAM_TEXT, 'The name of the rule'),
            )))
        ));

    }

    /**
     * Returns description of set_parent_competency() parameters.
     *
     * @return \external_function_parameters
     */
    public static function set_parent_competency_parameters() {
        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );
        $parentid = new external_value(
            PARAM_INT,
            'The new competency parent id',
            VALUE_REQUIRED
        );
        $params = array(
            'competencyid' => $competencyid,
            'parentid' => $parentid
        );
        return new external_function_parameters($params);
    }

    /**
     * Move the competency to a new parent.
     *
     * @param int $competencyid Competency id.
     * @param int $parentid Parent id.
     *
     * @return bool
     */
    public static function set_parent_competency($competencyid, $parentid) {
        $params = self::validate_parameters(self::set_parent_competency_parameters(),
                                            array(
                                                'competencyid' => $competencyid,
                                                'parentid' => $parentid
                                            ));

        $competency = api::read_competency($params['competencyid']);
        self::validate_context($competency->get_context());

        return api::set_parent_competency($params['competencyid'], $params['parentid']);
    }

    /**
     * Returns description of set_parent_competency() result value.
     *
     * @return \external_description
     */
    public static function set_parent_competency_returns() {
        return new external_value(PARAM_BOOL, 'True if the update was successful');
    }

    /**
     * Returns description of move_up_competency() parameters.
     *
     * @return \external_function_parameters
     */
    public static function move_up_competency_parameters() {
        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );
        $params = array(
            'id' => $competencyid,
        );
        return new external_function_parameters($params);
    }

    /**
     * Change the sort order of a competency.
     *
     * @param int $competencyid Competency id.
     * @return boolean
     */
    public static function move_up_competency($competencyid) {
        $params = self::validate_parameters(self::move_up_competency_parameters(),
                                            array(
                                                'id' => $competencyid,
                                            ));

        $competency = api::read_competency($params['id']);
        self::validate_context($competency->get_context());

        return api::move_up_competency($params['id']);
    }

    /**
     * Returns description of move_up_competency() result value.
     *
     * @return \external_description
     */
    public static function move_up_competency_returns() {
        return new external_value(PARAM_BOOL, 'True if the update was successful');
    }

    /**
     * Returns description of move_down_competency() parameters.
     *
     * @return \external_function_parameters
     */
    public static function move_down_competency_parameters() {
        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );
        $params = array(
            'id' => $competencyid,
        );
        return new external_function_parameters($params);
    }

    /**
     * Change the sort order of a competency.
     *
     * @param int $competencyid Competency id.
     * @return boolean
     */
    public static function move_down_competency($competencyid) {
        $params = self::validate_parameters(self::move_down_competency_parameters(),
                                            array(
                                                'id' => $competencyid,
                                            ));

        $competency = api::read_competency($params['id']);
        self::validate_context($competency->get_context());

        return api::move_down_competency($params['id']);
    }

    /**
     * Returns description of move_down_competency() result value.
     *
     * @return \external_description
     */
    public static function move_down_competency_returns() {
        return new external_value(PARAM_BOOL, 'True if the update was successful');
    }

    /**
     * Returns description of count_courses_using_competency() parameters.
     *
     * @return \external_function_parameters
     */
    public static function count_courses_using_competency_parameters() {
        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );
        $params = array(
            'id' => $competencyid,
        );
        return new external_function_parameters($params);
    }

    /**
     * Count the courses (visible to this user) that use this competency.
     *
     * @param int $competencyid Competency id.
     * @return int
     */
    public static function count_courses_using_competency($competencyid) {
        $params = self::validate_parameters(self::count_courses_using_competency_parameters(),
                                            array(
                                                'id' => $competencyid,
                                            ));

        $competency = api::read_competency($params['id']);
        self::validate_context($competency->get_context());

        return api::count_courses_using_competency($params['id']);
    }

    /**
     * Returns description of count_courses_using_competency() result value.
     *
     * @return \external_description
     */
    public static function count_courses_using_competency_returns() {
        return new external_value(PARAM_INT, 'The number of courses using this competency');
    }

    /**
     * Returns description of list_courses_using_competency() parameters.
     *
     * @return \external_function_parameters
     */
    public static function list_courses_using_competency_parameters() {
        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );
        $params = array(
            'id' => $competencyid,
        );
        return new external_function_parameters($params);
    }

    /**
     * Count the courses (visible to this user) that use this competency.
     *
     * @param int $competencyid Competency id.
     * @return array
     */
    public static function list_courses_using_competency($competencyid) {
        global $PAGE;

        $params = self::validate_parameters(self::list_courses_using_competency_parameters(),
                                            array(
                                                'id' => $competencyid,
                                            ));

        $competency = api::read_competency($params['id']);
        self::validate_context($competency->get_context());
        $output = $PAGE->get_renderer('tool_lp');

        $results = array();
        $courses = api::list_courses_using_competency($params['id']);
        foreach ($courses as $course) {
            $context = context_course::instance($course->id);
            $exporter = new course_summary_exporter($course, array('context' => $context));
            $result = $exporter->export($output);
            array_push($results, $result);
        }
        return $results;
    }

    /**
     * Returns description of list_courses_using_competency() result value.
     *
     * @return \external_description
     */
    public static function list_courses_using_competency_returns() {
        return new external_multiple_structure(course_summary_exporter::get_read_structure());
    }

    /**
     * Returns description of count_competencies_in_course() parameters.
     *
     * @return \external_function_parameters
     */
    public static function count_competencies_in_course_parameters() {
        $courseid = new external_value(
            PARAM_INT,
            'The course id',
            VALUE_REQUIRED
        );
        $params = array(
            'id' => $courseid,
        );
        return new external_function_parameters($params);
    }

    /**
     * Count the competencies (visible to this user) in this course.
     *
     * @param int $courseid The course id to check.
     * @return int
     */
    public static function count_competencies_in_course($courseid) {
        $params = self::validate_parameters(self::count_competencies_in_course_parameters(),
                                            array(
                                                'id' => $courseid,
                                            ));

        self::validate_context(context_course::instance($params['id']));

        return api::count_competencies_in_course($params['id']);
    }

    /**
     * Returns description of count_competencies_in_course() result value.
     *
     * @return \external_description
     */
    public static function count_competencies_in_course_returns() {
        return new external_value(PARAM_INT, 'The number of competencies in this course.');
    }

    /**
     * Returns description of list_competencies_in_course() parameters.
     *
     * @return \external_function_parameters
     */
    public static function list_competencies_in_course_parameters() {
        $courseid = new external_value(
            PARAM_INT,
            'The course id',
            VALUE_REQUIRED
        );
        $params = array(
            'id' => $courseid,
        );
        return new external_function_parameters($params);
    }

    /**
     * List the competencies (visible to this user) in this course.
     *
     * @param int $courseid The course id to check.
     * @return array
     */
    public static function list_competencies_in_course($courseid) {
        global $PAGE;

        $params = self::validate_parameters(self::list_competencies_in_course_parameters(),
                                            array(
                                                'id' => $courseid,
                                            ));

        self::validate_context(context_course::instance($params['id']));
        $output = $PAGE->get_renderer('tool_lp');

        $competencies = api::list_competencies_in_course($params['id']);
        $results = array();

        $contextcache = array();
        foreach ($competencies as $competency) {
            if (!isset($contextcache[$competency->get_competencyframeworkid()])) {
                $contextcache[$competency->get_competencyframeworkid()] = $competency->get_context();
            }
            $context = $contextcache[$competency->get_competencyframeworkid()];
            $exporter = new competency_exporter($competency, array('context' => $context));
            $record = $exporter->export($output);
            array_push($results, $record);
        }
        return $results;
    }

    /**
     * Returns description of list_competencies_in_course() result value.
     *
     * @return \external_description
     */
    public static function list_competencies_in_course_returns() {
        return new external_multiple_structure(competency_exporter::get_read_structure());
    }

    /**
     * Returns description of add_competency_to_course() parameters.
     *
     * @return \external_function_parameters
     */
    public static function add_competency_to_course_parameters() {
        $courseid = new external_value(
            PARAM_INT,
            'The course id',
            VALUE_REQUIRED
        );
        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );
        $params = array(
            'courseid' => $courseid,
            'competencyid' => $competencyid,
        );
        return new external_function_parameters($params);
    }

    /**
     * Count the competencies (visible to this user) in this course.
     *
     * @param int $courseid The course id to check.
     * @param int $competencyid Competency id.
     * @return int
     */
    public static function add_competency_to_course($courseid, $competencyid) {
        $params = self::validate_parameters(self::add_competency_to_course_parameters(),
                                            array(
                                                'courseid' => $courseid,
                                                'competencyid' => $competencyid,
                                            ));

        self::validate_context(context_course::instance($params['courseid']));

        return api::add_competency_to_course($params['courseid'], $params['competencyid']);
    }

    /**
     * Returns description of add_competency_to_course() result value.
     *
     * @return \external_description
     */
    public static function add_competency_to_course_returns() {
        return new external_value(PARAM_BOOL, 'True if successful.');
    }

    /**
     * Returns description of remove_competency_from_course() parameters.
     *
     * @return \external_function_parameters
     */
    public static function remove_competency_from_course_parameters() {
        $courseid = new external_value(
            PARAM_INT,
            'The course id',
            VALUE_REQUIRED
        );
        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );
        $params = array(
            'courseid' => $courseid,
            'competencyid' => $competencyid,
        );
        return new external_function_parameters($params);
    }

    /**
     * Count the competencies (visible to this user) in this course.
     *
     * @param int $courseid The course id to check.
     * @param int $competencyid Competency id.
     * @return int
     */
    public static function remove_competency_from_course($courseid, $competencyid) {
        $params = self::validate_parameters(self::remove_competency_from_course_parameters(),
                                            array(
                                                'courseid' => $courseid,
                                                'competencyid' => $competencyid,
                                            ));

        self::validate_context(context_course::instance($params['courseid']));

        return api::remove_competency_from_course($params['courseid'], $params['competencyid']);
    }

    /**
     * Returns description of remove_competency_from_course() result value.
     *
     * @return \external_description
     */
    public static function remove_competency_from_course_returns() {
        return new external_value(PARAM_BOOL, 'True if successful.');
    }

    /**
     * Returns description of data_for_course_competenies_page() parameters.
     *
     * @return \external_function_parameters
     */
    public static function data_for_course_competencies_page_parameters() {
        $courseid = new external_value(
            PARAM_INT,
            'The course id',
            VALUE_REQUIRED
        );
        $params = array('courseid' => $courseid);
        return new external_function_parameters($params);
    }

    /**
     * Loads the data required to render the course_competencies_page template.
     *
     * @param int $courseid The course id to check.
     * @return boolean
     */
    public static function data_for_course_competencies_page($courseid) {
        global $PAGE;
        $params = self::validate_parameters(self::data_for_course_competencies_page_parameters(),
                                            array(
                                                'courseid' => $courseid,
                                            ));
        self::validate_context(context_course::instance($params['courseid']));

        $renderable = new output\course_competencies_page($params['courseid']);
        $renderer = $PAGE->get_renderer('tool_lp');

        $data = $renderable->export_for_template($renderer);

        return $data;
    }

    /**
     * Returns description of data_for_course_competencies_page() result value.
     *
     * @return \external_description
     */
    public static function data_for_course_competencies_page_returns() {
        return new external_single_structure(array (
            'courseid' => new external_value(PARAM_INT, 'The current course id'),
            'canmanagecompetencyframeworks' => new external_value(PARAM_BOOL, 'User can manage competency frameworks'),
            'canmanagecoursecompetencies' => new external_value(PARAM_BOOL, 'User can manage linked course competencies'),
            'competencies' => new external_multiple_structure(
                competency_exporter::get_read_structure()
            ),
            'manageurl' => new external_value(PARAM_LOCALURL, 'Url to the manage competencies page.'),
        ));

    }

    /**
     * Returns description of reorder_course_competency() parameters.
     *
     * @return \external_function_parameters
     */
    public static function reorder_course_competency_parameters() {
        $courseid = new external_value(
            PARAM_INT,
            'The course id',
            VALUE_REQUIRED
        );
        $competencyidfrom = new external_value(
            PARAM_INT,
            'The competency id we are moving',
            VALUE_REQUIRED
        );
        $competencyidto = new external_value(
            PARAM_INT,
            'The competency id we are moving to',
            VALUE_REQUIRED
        );
        $params = array(
            'courseid' => $courseid,
            'competencyidfrom' => $competencyidfrom,
            'competencyidto' => $competencyidto,
        );
        return new external_function_parameters($params);
    }

    /**
     * Change the order of course competencies.
     *
     * @param int $courseid The course id
     * @param int $competencyidfrom The competency to move.
     * @param int $competencyidto The competency to move to.
     * @return bool
     */
    public static function reorder_course_competency($courseid, $competencyidfrom, $competencyidto) {
        $params = self::validate_parameters(self::reorder_course_competency_parameters(),
                                            array(
                                                'courseid' => $courseid,
                                                'competencyidfrom' => $competencyidfrom,
                                                'competencyidto' => $competencyidto,
                                            ));
        self::validate_context(context_course::instance($params['courseid']));

        return api::reorder_course_competency($params['courseid'], $params['competencyidfrom'], $params['competencyidto']);
    }

    /**
     * Returns description of reorder_course_competency() result value.
     *
     * @return \external_description
     */
    public static function reorder_course_competency_returns() {
        return new external_value(PARAM_BOOL, 'True if successful.');
    }

    /**
     * Returns description of reorder_template_competency() parameters.
     *
     * @return \external_function_parameters
     */
    public static function reorder_template_competency_parameters() {
        $templateid = new external_value(
            PARAM_INT,
            'The template id',
            VALUE_REQUIRED
        );
        $competencyidfrom = new external_value(
            PARAM_INT,
            'The competency id we are moving',
            VALUE_REQUIRED
        );
        $competencyidto = new external_value(
            PARAM_INT,
            'The competency id we are moving to',
            VALUE_REQUIRED
        );
        $params = array(
            'templateid' => $templateid,
            'competencyidfrom' => $competencyidfrom,
            'competencyidto' => $competencyidto,
        );
        return new external_function_parameters($params);
    }

    /**
     * Change the order of template competencies.
     *
     * @param int $templateid The template id
     * @param int $competencyidfrom The competency to move.
     * @param int $competencyidto The competency to move to.
     * @return bool
     */
    public static function reorder_template_competency($templateid, $competencyidfrom, $competencyidto) {
        $params = self::validate_parameters(self::reorder_template_competency_parameters(),
            array(
                'templateid' => $templateid,
                'competencyidfrom' => $competencyidfrom,
                'competencyidto' => $competencyidto,
            ));

        $template = api::read_template($params['templateid']);
        self::validate_context($template->get_context());

        return api::reorder_template_competency($params['templateid'], $params['competencyidfrom'], $params['competencyidto']);
    }

    /**
     * Returns description of reorder_template_competency() result value.
     *
     * @return \external_description
     */
    public static function reorder_template_competency_returns() {
        return new external_value(PARAM_BOOL, 'True if successful.');
    }

    /**
     * Returns description of create_template() parameters.
     *
     * @return \external_function_parameters
     */
    public static function create_template_parameters() {
        $structure = template_exporter::get_create_structure();
        $params = array('template' => $structure);
        return new external_function_parameters($params);
    }

    /**
     * Create a new learning plan template
     *
     * @param array $template The list of fields for the template.
     * @return \stdClass Record of new template.
     */
    public static function create_template($template) {
        global $PAGE;

        $params = self::validate_parameters(self::create_template_parameters(),
                                            array('template' => $template));
        $params = $params['template'];
        $context = self::get_context_from_params($params);
        self::validate_context($context);
        $output = $PAGE->get_renderer('tool_lp');

        unset($params['contextlevel']);
        unset($params['instanceid']);
        $params = (object) $params;
        $params->contextid = $context->id;

        $result = api::create_template($params);
        $exporter = new template_exporter($result);
        $record = $exporter->export($output);
        return $record;
    }

    /**
     * Returns description of create_template() result value.
     *
     * @return \external_description
     */
    public static function create_template_returns() {
        return template_exporter::get_read_structure();
    }

    /**
     * Returns description of read_template() parameters.
     *
     * @return \external_function_parameters
     */
    public static function read_template_parameters() {
        $id = new external_value(
            PARAM_INT,
            'Data base record id for the template',
            VALUE_REQUIRED
        );

        $params = array(
            'id' => $id,
        );
        return new external_function_parameters($params);
    }

    /**
     * Read a learning plan template by id.
     *
     * @param int $id The id of the template.
     * @return \stdClass
     */
    public static function read_template($id) {
        global $PAGE;

        $params = self::validate_parameters(self::read_template_parameters(),
                                            array(
                                                'id' => $id,
                                            ));

        $template = api::read_template($params['id']);
        self::validate_context($template->get_context());
        $output = $PAGE->get_renderer('tool_lp');

        $exporter = new template_exporter($template);
        $record = $exporter->export($output);
        return $record;
    }

    /**
     * Returns description of read_template() result value.
     *
     * @return \external_description
     */
    public static function read_template_returns() {
        return template_exporter::get_read_structure();
    }

    /**
     * Returns description of delete_template() parameters.
     *
     * @return \external_function_parameters
     */
    public static function delete_template_parameters() {
        $id = new external_value(
            PARAM_INT,
            'Data base record id for the template',
            VALUE_REQUIRED
        );

        $params = array(
            'id' => $id,
        );
        return new external_function_parameters($params);
    }

    /**
     * Delete a learning plan template
     *
     * @param int $id The learning plan template id
     * @return boolean
     */
    public static function delete_template($id) {
        $params = self::validate_parameters(self::delete_template_parameters(),
                                            array(
                                                'id' => $id,
                                            ));

        $template = api::read_template($params['id']);
        self::validate_context($template->get_context());

        return api::delete_template($params['id']);
    }

    /**
     * Returns description of delete_template() result value.
     *
     * @return \external_description
     */
    public static function delete_template_returns() {
        return new external_value(PARAM_BOOL, 'True if the delete was successful');
    }

    /**
     * Returns description of update_template() parameters.
     *
     * @return \external_function_parameters
     */
    public static function update_template_parameters() {
        $structure = template_exporter::get_update_structure();
        $params = array('template' => $structure);
        return new external_function_parameters($params);
    }

    /**
     * Update an existing learning plan template
     *
     * @param array $template The list of fields for the template.
     * @return boolean
     */
    public static function update_template($template) {

        $params = self::validate_parameters(self::update_template_parameters(),
                                            array('template' => $template));
        $params = $params['template'];
        $template = api::read_template($params['id']);
        self::validate_context($template->get_context());

        $params = (object) $params;

        return api::update_template($params);
    }

    /**
     * Returns description of update_template() result value.
     *
     * @return \external_description
     */
    public static function update_template_returns() {
        return new external_value(PARAM_BOOL, 'True if the update was successful');
    }

    /**
     * Returns description of duplicate_template() parameters.
     *
     * @return \external_function_parameters
     */
    public static function duplicate_template_parameters() {
        $templateid = new external_value(
            PARAM_INT,
            'The template id',
            VALUE_REQUIRED
        );

        $params = array(
            'id' => $templateid
        );
        return new external_function_parameters($params);
    }

    /**
     * Duplicate a learning plan template.
     *
     * @param int $id the id of the learning plan template to duplicate
     * @return boolean Record of new template.
     */
    public static function duplicate_template($id) {
        global $PAGE;

        $params = self::validate_parameters(self::duplicate_template_parameters(),
                                            array(
                                                'id' => $id,
                                            ));

        $template = api::read_template($params['id']);
        self::validate_context($template->get_context());
        $output = $PAGE->get_renderer('tool_lp');

        $result = api::duplicate_template($params['id']);
        $exporter = new template_exporter($result);
        return $exporter->export($output);
    }

    /**
     * Returns description of duplicate_template() result value.
     *
     * @return \external_description
     */
    public static function duplicate_template_returns() {
        return template_exporter::get_read_structure();
    }

    /**
     * Returns description of list_templates() parameters.
     *
     * @return \external_function_parameters
     */
    public static function list_templates_parameters() {
        $sort = new external_value(
            PARAM_ALPHANUMEXT,
            'Column to sort by.',
            VALUE_DEFAULT,
            ''
        );
        $order = new external_value(
            PARAM_ALPHA,
            'Sort direction. Should be either ASC or DESC',
            VALUE_DEFAULT,
            ''
        );
        $skip = new external_value(
            PARAM_INT,
            'Skip this number of records before returning results',
            VALUE_DEFAULT,
            0
        );
        $limit = new external_value(
            PARAM_INT,
            'Return this number of records at most.',
            VALUE_DEFAULT,
            0
        );
        $includes = new external_value(
            PARAM_ALPHA,
            'What other contexts to fetch the templates from. (children, parents, self)',
            VALUE_DEFAULT,
            'children'
        );

        $params = array(
            'sort' => $sort,
            'order' => $order,
            'skip' => $skip,
            'limit' => $limit,
            'context' => self::get_context_parameters(),
            'includes' => $includes
        );
        return new external_function_parameters($params);
    }

    /**
     * List the existing learning plan templates
     *
     * @param string $sort Field to sort by.
     * @param string $order Sort order.
     * @param int $skip Limitstart.
     * @param int $limit Number of rows to return.
     * @param array $context
     * @param bool $includes
     *
     * @return array
     */
    public static function list_templates($sort, $order, $skip, $limit, $context, $includes) {
        global $PAGE;

        $params = self::validate_parameters(self::list_templates_parameters(),
                                            array(
                                                'sort' => $sort,
                                                'order' => $order,
                                                'skip' => $skip,
                                                'limit' => $limit,
                                                'context' => $context,
                                                'includes' => $includes
                                            ));

        $context = self::get_context_from_params($params['context']);
        self::validate_context($context);
        $output = $PAGE->get_renderer('tool_lp');

        if ($params['order'] !== '' && $params['order'] !== 'ASC' && $params['order'] !== 'DESC') {
            throw new invalid_parameter_exception('Invalid order param. Must be ASC, DESC or empty.');
        }

        $results = api::list_templates($params['sort'],
                                       $params['order'],
                                       $params['skip'],
                                       $params['limit'],
                                       $context,
                                       $params['includes']);
        $records = array();
        foreach ($results as $result) {
            $exporter = new template_exporter($result);
            $record = $exporter->export($output);
            array_push($records, $record);
        }
        return $records;
    }

    /**
     * Returns description of list_templates() result value.
     *
     * @return \external_description
     */
    public static function list_templates_returns() {
        return new external_multiple_structure(template_exporter::get_read_structure());
    }

    /**
     * Returns description of count_templates() parameters.
     *
     * @return \external_function_parameters
     */
    public static function count_templates_parameters() {
        $includes = new external_value(
            PARAM_ALPHA,
            'What other contextes to fetch the frameworks from. (children, parents, self)',
            VALUE_DEFAULT,
            'children'
        );

        $params = array(
            'context' => self::get_context_parameters(),
            'includes' => $includes
        );
        return new external_function_parameters($params);
    }

    /**
     * Count the existing learning plan templates
     *
     * @param array $filters Filters to allow.
     * @return boolean
     */
    public static function count_templates($context, $includes) {
        $params = self::validate_parameters(self::count_templates_parameters(),
                                            array(
                                                'context' => $context,
                                                'includes' => $includes
                                            ));
        $context = self::get_context_from_params($params['context']);
        self::validate_context($context);

        return api::count_templates($context, $includes);
    }

    /**
     * Returns description of count_templates() result value.
     *
     * @return \external_description
     */
    public static function count_templates_returns() {
        return new external_value(PARAM_INT, 'The number of learning plan templates found.');
    }

    /**
     * Returns description of data_for_templates_manage_page() parameters.
     *
     * @return \external_function_parameters
     */
    public static function data_for_templates_manage_page_parameters() {
        $params = array('pagecontext' => self::get_context_parameters());
        return new external_function_parameters($params);
    }

    /**
     * Loads the data required to render the templates_manage_page template.
     *
     * @param array $pagecontext The page context info.
     * @return boolean
     */
    public static function data_for_templates_manage_page($pagecontext) {
        global $PAGE;

        $params = self::validate_parameters(self::data_for_templates_manage_page_parameters(), array(
            'pagecontext' => $pagecontext
        ));
        $context = self::get_context_from_params($params['pagecontext']);
        self::validate_context($context);

        $renderable = new output\manage_templates_page($context);
        $renderer = $PAGE->get_renderer('tool_lp');

        $data = $renderable->export_for_template($renderer);

        return $data;
    }

    /**
     * Returns description of data_for_templates_manage_page() result value.
     *
     * @return \external_description
     */
    public static function data_for_templates_manage_page_returns() {
        return new external_single_structure(array (
            'canmanage' => new external_value(PARAM_BOOL, 'True if this user has permission to manage learning plan templates'),
            'templates' => new external_multiple_structure(
                template_exporter::get_read_structure()
            ),
            'pluginbaseurl' => new external_value(PARAM_LOCALURL, 'Url to the tool_lp plugin folder on this Moodle site'),
            'navigation' => new external_multiple_structure(
                new external_value(PARAM_RAW, 'HTML for a navigation item that should be on this page')
            )
        ));

    }

    /**
     * Returns description of count_templates_using_competency() parameters.
     *
     * @return \external_function_parameters
     */
    public static function count_templates_using_competency_parameters() {
        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );
        $params = array(
            'id' => $competencyid,
        );
        return new external_function_parameters($params);
    }

    /**
     * Count the learning plan templates (visible to this user) that use this competency.
     *
     * @param int $competencyid Competency id.
     * @return int
     */
    public static function count_templates_using_competency($competencyid) {
        $params = self::validate_parameters(self::count_templates_using_competency_parameters(),
                                            array(
                                                'id' => $competencyid,
                                            ));

        $competency = api::read_competency($params['id']);
        self::validate_context($competency->get_context());

        return api::count_templates_using_competency($params['id']);
    }

    /**
     * Returns description of count_templates_using_competency() result value.
     *
     * @return \external_description
     */
    public static function count_templates_using_competency_returns() {
        return new external_value(PARAM_INT, 'The number of learning plan templates using this competency');
    }

    /**
     * Returns description of list_templates_using_competency() parameters.
     *
     * @return \external_function_parameters
     */
    public static function list_templates_using_competency_parameters() {
        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );
        $params = array(
            'id' => $competencyid,
        );
        return new external_function_parameters($params);
    }

    /**
     * List the learning plan templates (visible to this user) that use this competency.
     *
     * @param int $competencyid Competency id.
     * @return array
     */
    public static function list_templates_using_competency($competencyid) {
        global $PAGE;

        $params = self::validate_parameters(self::list_templates_using_competency_parameters(),
                                            array(
                                                'id' => $competencyid,
                                            ));

        $competency = api::read_competency($params['id']);
        self::validate_context($competency->get_context());
        $output = $PAGE->get_renderer('tool_lp');

        $templates = api::list_templates_using_competency($params['id']);
        $records = array();

        foreach ($templates as $template) {
            $exporter = new template_exporter($template);
            $record = $exporter->export($output);
            $records[] = $record;
        }

        return $records;
    }

    /**
     * Returns description of list_templates_using_competency() result value.
     *
     * @return \external_description
     */
    public static function list_templates_using_competency_returns() {
        return new external_multiple_structure(template_exporter::get_read_structure());
    }

    /**
     * Returns description of count_competencies_in_template() parameters.
     *
     * @return \external_function_parameters
     */
    public static function count_competencies_in_template_parameters() {
        $templateid = new external_value(
            PARAM_INT,
            'The template id',
            VALUE_REQUIRED
        );
        $params = array(
            'id' => $templateid,
        );
        return new external_function_parameters($params);
    }

    /**
     * Count the competencies (visible to this user) in this learning plan template.
     *
     * @param int $templateid The template id to check
     * @return int
     */
    public static function count_competencies_in_template($templateid) {
        global $PAGE;
        $params = self::validate_parameters(self::count_competencies_in_template_parameters(),
                                            array(
                                                'id' => $templateid,
                                            ));
        $template = api::read_template($params['id']);
        self::validate_context($template->get_context());

        return api::count_competencies_in_template($params['id']);
    }

    /**
     * Returns description of count_competencies_in_template() result value.
     *
     * @return \external_description
     */
    public static function count_competencies_in_template_returns() {
        return new external_value(PARAM_INT, 'The number of competencies in this learning plan template.');
    }

    /**
     * Returns description of list_competencies_in_template() parameters.
     *
     * @return \external_function_parameters
     */
    public static function list_competencies_in_template_parameters() {
        $templateid = new external_value(
            PARAM_INT,
            'The template id',
            VALUE_REQUIRED
        );
        $params = array(
            'id' => $templateid,
        );
        return new external_function_parameters($params);
    }

    /**
     * List the competencies (visible to this user) in this learning plan template.
     *
     * @param int $templateid Template id.
     * @return array
     */
    public static function list_competencies_in_template($templateid) {
        global $PAGE;

        $params = self::validate_parameters(self::list_competencies_in_template_parameters(),
                                            array(
                                                'id' => $templateid,
                                            ));

        $template = api::read_template($params['id']);
        self::validate_context($template->get_context());
        $output = $PAGE->get_renderer('tool_lp');

        $competencies = api::list_competencies_in_template($params['id']);
        $results = array();
        $contextcache = array();

        foreach ($competencies as $competency) {
            if (!isset($contextcache[$competency->get_competencyframeworkid()])) {
                $contextcache[$competency->get_competencyframeworkid()] = $competency->get_context();
            }
            $context = $contextcache[$competency->get_competencyframeworkid()];
            $exporter = new competency_exporter($competency, array('context' => $context));
            $record = $exporter->export($output);
            array_push($results, $record);
        }
        return $results;
    }

    /**
     * Returns description of list_competencies_in_template() result value.
     *
     * @return \external_description
     */
    public static function list_competencies_in_template_returns() {
        return new external_multiple_structure(competency_exporter::get_read_structure());
    }

    /**
     * Returns description of add_competency_to_template() parameters.
     *
     * @return \external_function_parameters
     */
    public static function add_competency_to_template_parameters() {
        $templateid = new external_value(
            PARAM_INT,
            'The template id',
            VALUE_REQUIRED
        );
        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );
        $params = array(
            'templateid' => $templateid,
            'competencyid' => $competencyid,
        );
        return new external_function_parameters($params);
    }

    /**
     * Count the competencies (visible to this user) in this template.
     *
     * @param int $templateid Template id.
     * @param int $competencyid Competency id.
     * @return int
     */
    public static function add_competency_to_template($templateid, $competencyid) {
        global $PAGE;
        $params = self::validate_parameters(self::add_competency_to_template_parameters(),
                                            array(
                                                'templateid' => $templateid,
                                                'competencyid' => $competencyid,
                                            ));

        $template = api::read_template($params['templateid']);
        self::validate_context($template->get_context());

        return api::add_competency_to_template($params['templateid'], $params['competencyid']);
    }

    /**
     * Returns description of add_competency_to_template() result value.
     *
     * @return \external_description
     */
    public static function add_competency_to_template_returns() {
        return new external_value(PARAM_BOOL, 'True if successful.');
    }

    /**
     * Returns description of add_competency_to_plan() parameters.
     *
     * @return \external_function_parameters
     */
    public static function add_competency_to_plan_parameters() {
        $planid = new external_value(
            PARAM_INT,
            'The plan id',
            VALUE_REQUIRED
        );
        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );
        $params = array(
            'planid' => $planid,
            'competencyid' => $competencyid,
        );
        return new external_function_parameters($params);
    }

    /**
     * add competency to a learning plan.
     *
     * @param int $planid Plan id.
     * @param int $competencyid Competency id.
     * @return int
     */
    public static function add_competency_to_plan($planid, $competencyid) {
        $params = self::validate_parameters(self::add_competency_to_plan_parameters(),
                                            array(
                                                'planid' => $planid,
                                                'competencyid' => $competencyid,
                                            ));

        $plan = api::read_plan($params['planid']);
        self::validate_context($plan->get_context());

        return api::add_competency_to_plan($params['planid'], $params['competencyid']);
    }

    /**
     * Returns description of add_competency_to_plan() result value.
     *
     * @return \external_description
     */
    public static function add_competency_to_plan_returns() {
        return new external_value(PARAM_BOOL, 'True if successful.');
    }

    /**
     * Returns description of remove_competency_from_plan() parameters.
     *
     * @return \external_function_parameters
     */
    public static function remove_competency_from_plan_parameters() {
        $planid = new external_value(
            PARAM_INT,
            'The plan id',
            VALUE_REQUIRED
        );
        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );
        $params = array(
            'planid' => $planid,
            'competencyid' => $competencyid,
        );
        return new external_function_parameters($params);
    }

    /**
     * Remove a competency from plan.
     *
     * @param int $planid Plan id.
     * @param int $competencyid Competency id.
     * @return int
     */
    public static function remove_competency_from_plan($planid, $competencyid) {
        $params = self::validate_parameters(self::remove_competency_from_plan_parameters(),
                                            array(
                                                'planid' => $planid,
                                                'competencyid' => $competencyid,
                                            ));
        $plan = api::read_plan($params['planid']);
        self::validate_context($plan->get_context());

        return api::remove_competency_from_plan($params['planid'], $params['competencyid']);
    }

    /**
     * Returns description of remove_competency_from_plan() result value.
     *
     * @return \external_description
     */
    public static function remove_competency_from_plan_returns() {
        return new external_value(PARAM_BOOL, 'True if successful.');
    }

    /**
     * Returns description of remove_competency_from_template() parameters.
     *
     * @return \external_function_parameters
     */
    public static function remove_competency_from_template_parameters() {
        $templateid = new external_value(
            PARAM_INT,
            'The template id',
            VALUE_REQUIRED
        );
        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );
        $params = array(
            'templateid' => $templateid,
            'competencyid' => $competencyid,
        );
        return new external_function_parameters($params);
    }

    /**
     * Returns description of reorder_plan_competency() parameters.
     *
     * @return \external_function_parameters
     */
    public static function reorder_plan_competency_parameters() {
        $planid = new external_value(
            PARAM_INT,
            'The plan id',
            VALUE_REQUIRED
        );
        $competencyidfrom = new external_value(
            PARAM_INT,
            'The competency id we are moving',
            VALUE_REQUIRED
        );
        $competencyidto = new external_value(
            PARAM_INT,
            'The competency id we are moving to',
            VALUE_REQUIRED
        );
        $params = array(
            'planid' => $planid,
            'competencyidfrom' => $competencyidfrom,
            'competencyidto' => $competencyidto,
        );
        return new external_function_parameters($params);
    }

    /**
     * Change the order of plan competencies.
     *
     * @param int $planid The plan id
     * @param int $competencyidfrom The competency to move.
     * @param int $competencyidto The competency to move to.
     * @return bool
     */
    public static function reorder_plan_competency($planid, $competencyidfrom, $competencyidto) {
        $params = self::validate_parameters(self::reorder_plan_competency_parameters(),
            array(
                'planid' => $planid,
                'competencyidfrom' => $competencyidfrom,
                'competencyidto' => $competencyidto,
            ));

        $plan = api::read_plan($params['planid']);
        self::validate_context($plan->get_context());

        return api::reorder_plan_competency($params['planid'], $params['competencyidfrom'], $params['competencyidto']);
    }

    /**
     * Returns description of reorder_plan_competency() result value.
     *
     * @return \external_description
     */
    public static function reorder_plan_competency_returns() {
        return new external_value(PARAM_BOOL, 'True if successful.');
    }

    /**
     * Count the competencies (visible to this user) in this learning plan template.
     *
     * @param int $templateid Template id.
     * @param int $competencyid Competency id.
     * @return int
     */
    public static function remove_competency_from_template($templateid, $competencyid) {
        $params = self::validate_parameters(self::remove_competency_from_template_parameters(),
                                            array(
                                                'templateid' => $templateid,
                                                'competencyid' => $competencyid,
                                            ));
        $template = api::read_template($params['templateid']);
        self::validate_context($template->get_context());

        return api::remove_competency_from_template($params['templateid'], $params['competencyid']);
    }

    /**
     * Returns description of remove_competency_from_template() result value.
     *
     * @return \external_description
     */
    public static function remove_competency_from_template_returns() {
        return new external_value(PARAM_BOOL, 'True if successful.');
    }

    /**
     * Returns description of data_for_template_competenies_page() parameters.
     *
     * @return \external_function_parameters
     */
    public static function data_for_template_competencies_page_parameters() {
        $templateid = new external_value(
            PARAM_INT,
            'The template id',
            VALUE_REQUIRED
        );
        $params = array('templateid' => $templateid, 'pagecontext' => self::get_context_parameters());
        return new external_function_parameters($params);
    }

    /**
     * Loads the data required to render the template_competencies_page template.
     *
     * @param int $templateid Template id.
     * @param array $pagecontext The page context info.
     * @return boolean
     */
    public static function data_for_template_competencies_page($templateid, $pagecontext) {
        global $PAGE;
        $params = self::validate_parameters(self::data_for_template_competencies_page_parameters(),
                                            array(
                                                'templateid' => $templateid,
                                                'pagecontext' => $pagecontext
                                            ));

        $context = self::get_context_from_params($params['pagecontext']);
        self::validate_context($context);

        $renderable = new output\template_competencies_page($params['templateid'], $context);
        $renderer = $PAGE->get_renderer('tool_lp');

        $data = $renderable->export_for_template($renderer);

        return $data;
    }

    /**
     * Returns description of data_for_template_competencies_page() result value.
     *
     * @return \external_description
     */
    public static function data_for_template_competencies_page_returns() {
        return new external_single_structure(array (
            'templateid' => new external_value(PARAM_INT, 'The current template id'),
            'canmanagecompetencyframeworks' => new external_value(PARAM_BOOL, 'User can manage competency frameworks'),
            'canmanagetemplates' => new external_value(PARAM_BOOL, 'User can manage learning plan templates'),
            'competencies' => new external_multiple_structure(
                competency_summary_exporter::get_read_structure()
            ),
            'manageurl' => new external_value(PARAM_LOCALURL, 'Url to the manage competencies page.'),
        ));

    }

    /**
     * Returns description of data_for_plan_competenies_page() parameters.
     *
     * @return \external_function_parameters
     */
    public static function data_for_plan_competencies_page_parameters() {
        $planid = new external_value(
            PARAM_INT,
            'The plan id',
            VALUE_REQUIRED
        );
        $params = array('planid' => $planid);
        return new external_function_parameters($params);
    }

    /**
     * Loads the data required to render the plan_competencies_page template.
     *
     * @param int $planid Learning Plan id.
     * @return boolean
     */
    public static function data_for_plan_competencies_page($planid) {
        global $PAGE;
        $params = self::validate_parameters(self::data_for_plan_competencies_page_parameters(),
                                            array(
                                                'planid' => $planid
                                            ));
        $plan = api::read_plan($params['planid']);
        self::validate_context($plan->get_context());

        $renderable = new output\plan_page($plan);
        $renderer = $PAGE->get_renderer('tool_lp');

        $data = $renderable->export_for_template($renderer);

        return $data;
    }

    /**
     * Returns description of data_for_plan_competencies_page() result value.
     *
     * @return \external_description
     */
    public static function data_for_plan_competencies_page_returns() {
        return new external_single_structure(array (
            'planid' => new external_value(PARAM_INT, 'Learning Plan id'),
            'canmanage' => new external_value(PARAM_BOOL, 'User can manage learning plan'),
            'iscompleted' => new external_value(PARAM_BOOL, 'Is the plan completed'),
            'competencies' => new external_multiple_structure(
                new external_single_structure(array(
                    'competency' => competency_exporter::get_read_structure(),
                    'usercompetency' => user_competency_exporter::get_read_structure(),
                    'usercompetencyplan' => user_competency_plan_exporter::get_read_structure()
                ))
            )
        ));
    }

    /**
     * Returns description of create_plan() parameters.
     *
     * @return \external_function_parameters
     */
    public static function create_plan_parameters() {
        $structure = plan_exporter::get_create_structure();
        $params = array('plan' => $structure);
        return new external_function_parameters($params);
    }

    /**
     * Create a new learning plan.
     *
     * @param array $plan List of fields for the plan.
     * @return array New plan record.
     */
    public static function create_plan($plan) {
        global $PAGE;

        $params = self::validate_parameters(self::create_plan_parameters(),
                                            array('plan' => $plan));
        $params = $params['plan'];

        $context = context_user::instance($params['userid']);
        self::validate_context($context);
        $output = $PAGE->get_renderer('tool_lp');

        $params = (object) $params;

        $result = api::create_plan($params);
        $exporter = new plan_exporter($result);
        return $exporter->export($output);
    }

    /**
     * Returns description of create_plan() result value.
     *
     * @return \external_description
     */
    public static function create_plan_returns() {
        return plan_exporter::get_read_structure();
    }

    /**
     * Returns description of update_plan() parameters.
     *
     * @return \external_function_parameters
     */
    public static function update_plan_parameters() {
        $structure = plan_exporter::get_update_structure();
        $params = array('plan' => $structure);
        return new external_function_parameters($params);
    }

    /**
     * Updates a new learning plan.
     *
     * @param array $plan Fields for the plan (id is required)
     * @return mixed
     */
    public static function update_plan($plan) {
        global $PAGE;

        $params = self::validate_parameters(self::update_plan_parameters(),
                                            array('plan' => $plan));

        $params = $params['plan'];

        $plan = api::read_plan($params['id']);
        self::validate_context($plan->get_context());
        $output = $PAGE->get_renderer('tool_lp');

        $params = (object) $params;
        $result = api::update_plan($params);
        $exporter = plan_exporter($result);
        $record = $exporter->export($output);
        return external_api::clean_returnvalue(self::update_plan_returns(), $record);
    }

    /**
     * Returns description of update_plan() result value.
     *
     * @return \external_description
     */
    public static function update_plan_returns() {
        return plan_exporter::get_read_structure();
    }

    /**
     * Returns description of complete_plan() parameters.
     *
     * @return \external_function_parameters
     */
    public static function complete_plan_parameters() {
        $planid = new external_value(
            PARAM_INT,
            'The plan id',
            VALUE_REQUIRED
        );
        $params = array('planid' => $planid);
        return new external_function_parameters($params);
    }

    /**
     * Complete Learning plan.
     *
     * @param int $planid plan id (id is required)
     * @return boolean
     */
    public static function complete_plan($planid) {
        $params = self::validate_parameters(self::complete_plan_parameters(),
                                            array(
                                                'planid' => $planid
                                            ));

        return api::complete_plan($params['planid']);
    }

    /**
     * Returns description of complete_plan() result value.
     *
     * @return \external_description
     */
    public static function complete_plan_returns() {
        return new external_value(PARAM_BOOL, 'True if completing learning plan was successful');
    }

    /**
     * Returns description of reopen_plan() parameters.
     *
     * @return \external_function_parameters
     */
    public static function reopen_plan_parameters() {
        $planid = new external_value(
            PARAM_INT,
            'The plan id',
            VALUE_REQUIRED
        );
        $params = array('planid' => $planid);
        return new external_function_parameters($params);
    }

    /**
     * Reopen Learning plan.
     *
     * @param int $planid plan id (id is required)
     * @return boolean
     */
    public static function reopen_plan($planid) {
        $params = self::validate_parameters(self::reopen_plan_parameters(),
                                            array(
                                                'planid' => $planid
                                            ));

        return api::reopen_plan($params['planid']);
    }

    /**
     * Returns description of reopen_plan() result value.
     *
     * @return \external_description
     */
    public static function reopen_plan_returns() {
        return new external_value(PARAM_BOOL, 'True if reopening learning plan was successful');
    }

    /**
     * Returns description of read_plan() parameters.
     *
     * @return \external_function_parameters
     */
    public static function read_plan_parameters() {
        $id = new external_value(
            PARAM_INT,
            'Data base record id for the plan',
            VALUE_REQUIRED
        );
        return new external_function_parameters(array('id' => $id));
    }

    /**
     * Read a plan by id.
     *
     * @param int $id The id of the plan.
     * @return \stdClass
     */
    public static function read_plan($id) {
        global $PAGE;

        $params = self::validate_parameters(self::read_plan_parameters(),
                                            array(
                                                'id' => $id,
                                            ));

        $plan = api::read_plan($params['id']);
        self::validate_context($plan->get_context());
        $output = $PAGE->get_renderer('tool_lp');

        $exporter = new plan_exporter($plan);
        $record = $exporter->export($output);
        return external_api::clean_returnvalue(self::read_plan_returns(), $record);
    }

    /**
     * Returns description of read_plan() result value.
     *
     * @return \external_description
     */
    public static function read_plan_returns() {
        return plan_exporter::get_read_structure();
    }

    /**
     * Returns description of delete_plan() parameters.
     *
     * @return \external_function_parameters
     */
    public static function delete_plan_parameters() {
        $id = new external_value(
            PARAM_INT,
            'Data base record id for the learning plan',
            VALUE_REQUIRED
        );

        $params = array(
            'id' => $id,
        );
        return new external_function_parameters($params);
    }

    /**
     * Delete a plan.
     *
     * @param int $id The plan id
     * @return boolean
     */
    public static function delete_plan($id) {
        $params = self::validate_parameters(self::delete_plan_parameters(),
                                            array(
                                                'id' => $id,
                                            ));

        $plan = api::read_plan($params['id']);
        self::validate_context($plan->get_context());

        return external_api::clean_returnvalue(self::delete_plan_returns(), api::delete_plan($params['id']));
    }

    /**
     * Returns description of delete_plan() result value.
     *
     * @return \external_description
     */
    public static function delete_plan_returns() {
        return new external_value(PARAM_BOOL, 'True if the delete was successful');
    }

    /**
     * Returns description of data_for_plans_page() parameters.
     *
     * @return \external_function_parameters
     */
    public static function data_for_plans_page_parameters() {
        $userid = new external_value(
            PARAM_INT,
            'The user id',
            VALUE_REQUIRED
        );
        $params = array('userid' => $userid);
        return new external_function_parameters($params);
    }

    /**
     * Loads the data required to render the plans_page template.
     *
     * @param int $userid User id.
     * @return boolean
     */
    public static function data_for_plans_page($userid) {
        global $PAGE;

        $params = self::validate_parameters(self::data_for_plans_page_parameters(),
                                            array(
                                                'userid' => $userid,
                                            ));

        $context = context_user::instance($params['userid']);
        self::validate_context($context);
        $output = $PAGE->get_renderer('tool_lp');

        $renderable = new \tool_lp\output\plans_page($params['userid']);

        return external_api::clean_returnvalue(self::data_for_plans_page_returns(), $renderable->export_for_template($output));
    }

    /**
     * Returns description of data_for_plans_page() result value.
     *
     * @return \external_description
     */
    public static function data_for_plans_page_returns() {
        return new external_single_structure(array (
            'userid' => new external_value(PARAM_INT, 'The learning plan user id'),
            'plans' => new external_multiple_structure(
                plan_exporter::get_read_structure()
            ),
            'pluginbaseurl' => new external_value(PARAM_LOCALURL, 'Url to the tool_lp plugin folder on this Moodle site'),
            'navigation' => new external_multiple_structure(
                new external_value(PARAM_RAW, 'HTML for a navigation item that should be on this page')
            )
        ));
    }

    /**
     * External function parameters structure.
     *
     * @return \external_description
     */
    public static function list_plan_competencies_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'The plan ID.')
        ));
    }

    /**
     * List plan competencies.
     * @param  int $id The plan ID.
     * @return array
     */
    public static function list_plan_competencies($id) {
        global $PAGE;

        $params = self::validate_parameters(self::list_plan_competencies_parameters(), array('id' => $id));
        $id = $params['id'];
        $plan = api::read_plan($id);
        $usercontext = $plan->get_context();
        self::validate_context($usercontext);
        $output = $PAGE->get_renderer('tool_lp');

        $result = api::list_plan_competencies($plan);

        if ($plan->get_status() == plan::STATUS_COMPLETE) {
            $ucproperty = 'usercompetencyplan';
        } else {
            $ucproperty = 'usercompetency';
        }

        $contextcache = array();
        $scalecache = array();

        foreach ($result as $key => $r) {
            if (!isset($scalecache[$r->competency->get_competencyframeworkid()])) {
                $scalecache[$r->competency->get_competencyframeworkid()] = $r->competency->get_framework()->get_scale();
            }
            $scale = $scalecache[$r->competency->get_competencyframeworkid()];

            if (!isset($contextcache[$r->competency->get_competencyframeworkid()])) {
                $contextcache[$r->competency->get_competencyframeworkid()] = $r->competency->get_context();
            }
            $context = $contextcache[$r->competency->get_competencyframeworkid()];

            $exporter = new competency_exporter($r->competency, array('context' => $context));
            $r->competency = $exporter->export($output);

            if ($r->usercompetency) {
                $exporter = new user_competency_exporter($r->usercompetency, array('scale' => $scale));
                $r->usercompetency = $exporter->export($output);
                unset($r->usercompetencyplan);
            } else {
                $exporter = new user_competency_plan_exporter($r->usercompetencyplan, array('scale' => $scale));
                $r->usercompetencyplan = $exporter->export($output);
                unset($r->usercompetency);
            }
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
            new external_single_structure(array(
                'competency' => competency_exporter::get_read_structure(),
                'usercompetency' => $uc,
                'usercompetencyplan' => $ucp
            ))
        );
    }

    /**
     * Returns the description of the get_scale_values() parameters.
     *
     * @return external_function_parameters.
     */
    public static function get_scale_values_parameters() {
        $scaleid = new external_value(
            PARAM_INT,
            'The scale id',
            VALUE_REQUIRED
        );
        $params = array('scaleid' => $scaleid);
        return new external_function_parameters($params);
    }

    /**
     * Get the values associated with a scale.
     *
     * @param int $scaleid Scale ID
     * @return array Values for a scale.
     */
    public static function get_scale_values($scaleid) {
        global $DB;
        $params = self::validate_parameters(self::get_scale_values_parameters(),
            array(
                'scaleid' => $scaleid,
            )
        );
        $context = context_system::instance();
        self::validate_context($context);
        // The following section is not learning plan specific and so has not been moved to the api.
        // Retrieve the scale value from the database.
        $scale = grade_scale::fetch(array('id' => $scaleid));
        // Reverse the array so that high levels are at the top.
        $scalevalues = array_reverse($scale->load_items());
        foreach ($scalevalues as $key => $value) {
            // Add a key (make the first value 1).
            $scalevalues[$key] = array(
                    'id' => $key + 1,
                    'name' => external_format_string($value, $context->id)
                );
        }
        return $scalevalues;
    }

    /**
     * Returns description of get_scale_values() result value.
     *
     * @return external_multiple_structure
     */
    public static function get_scale_values_returns() {
        return new external_multiple_structure(
            new external_single_structure(array(
                'id' => new external_value(PARAM_INT, 'Scale value ID'),
                'name' => new external_value(PARAM_RAW, 'Scale value name')
                )
            )
        );
    }

    /**
     * Returns the description of the add_related_competency_parameters() parameters.
     *
     * @return external_function_parameters.
     */
    public static function add_related_competency_parameters() {
        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );
        $relatedcompetencyid = new external_value(
            PARAM_INT,
            'The related competency id',
            VALUE_REQUIRED
        );
        $params = array(
            'competencyid' => $competencyid,
            'relatedcompetencyid' => $relatedcompetencyid
        );
        return new external_function_parameters($params);
    }

    /**
     * Adds a related competency.
     *
     * @param int $competencyid
     * @param int $relatedcompetencyid
     * @return bool
     */
    public static function add_related_competency($competencyid, $relatedcompetencyid) {
        $params = self::validate_parameters(self::add_related_competency_parameters(),
                                            array(
                                                'competencyid' => $competencyid,
                                                'relatedcompetencyid' => $relatedcompetencyid
                                            ));
        $competency = api::read_competency($params['competencyid']);
        self::validate_context($competency->get_context());

        return api::add_related_competency($params['competencyid'], $params['relatedcompetencyid']);
    }

    /**
     * Returns description of add_related_competency_returns() result value.
     *
     * @return external_description
     */
    public static function add_related_competency_returns() {
        return new external_value(PARAM_BOOL, 'True if successful.');
    }

    /**
     * Returns the description of the remove_related_competency_parameters() parameters.
     *
     * @return external_function_parameters.
     */
    public static function remove_related_competency_parameters() {
        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );
        $relatedcompetencyid = new external_value(
            PARAM_INT,
            'The related competency id',
            VALUE_REQUIRED
        );
        $params = array(
            'competencyid' => $competencyid,
            'relatedcompetencyid' => $relatedcompetencyid
        );
        return new external_function_parameters($params);
    }

    /**
     * Removes a related competency.
     *
     * @param int $competencyid
     * @param int $relatedcompetencyid
     * @return bool
     */
    public static function remove_related_competency($competencyid, $relatedcompetencyid) {
        $params = self::validate_parameters(self::remove_related_competency_parameters(),
                                            array(
                                                'competencyid' => $competencyid,
                                                'relatedcompetencyid' => $relatedcompetencyid
                                            ));
        $competency = api::read_competency($params['competencyid']);
        self::validate_context($competency->get_context());

        return api::remove_related_competency($params['competencyid'], $params['relatedcompetencyid']);
    }

    /**
     * Returns description of remove_related_competency_returns() result value.
     *
     * @return external_description
     */
    public static function remove_related_competency_returns() {
        return new external_value(PARAM_BOOL, 'True if successful.');
    }

    /**
     * Returns the description of the data_for_related_competencies_section_parameters() parameters.
     *
     * @return external_function_parameters.
     */
    public static function data_for_related_competencies_section_parameters() {
        $competencyid = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );
        return new external_function_parameters(array('competencyid' => $competencyid));
    }

    /**
     * Data to render in the related competencies section.
     *
     * @param int $competencyid
     * @return array Related competencies and whether to show delete action button or not.
     */
    public static function data_for_related_competencies_section($competencyid) {
        global $PAGE;

        $params = self::validate_parameters(self::data_for_related_competencies_section_parameters(),
                                            array(
                                                'competencyid' => $competencyid,
                                            ));
        $competency = api::read_competency($params['competencyid']);
        self::validate_context($competency->get_context());

        $renderable = new \tool_lp\output\related_competencies($params['competencyid']);
        $renderer = $PAGE->get_renderer('tool_lp');

        return $renderable->export_for_template($renderer);
    }

    /**
     * Returns description of data_for_related_competencies_section_returns() result value.
     *
     * @return external_description
     */
    public static function data_for_related_competencies_section_returns() {
        return new external_single_structure(array(
            'relatedcompetencies' => new external_multiple_structure(competency_exporter::get_read_structure()),
            'showdeleterelatedaction' => new external_value(PARAM_BOOL, 'Whether to show the delete relation link or not')
        ));
    }

    /**
     * Returns the description of external function parameters.
     *
     * @return external_function_parameters.
     */
    public static function search_users_parameters() {
        $query = new external_value(
            PARAM_RAW,
            'Query string'
        );
        $capability = new external_value(
            PARAM_RAW,
            'Required capability'
        );
        $limitfrom = new external_value(
            PARAM_INT,
            'Number of records to skip',
            VALUE_DEFAULT,
            0
        );
        $limitnum = new external_value(
            PARAM_RAW,
            'Number of records to fetch',
            VALUE_DEFAULT,
            100
        );
        return new external_function_parameters(array(
            'query' => $query,
            'capability' => $capability,
            'limitfrom' => $limitfrom,
            'limitnum' => $limitnum
        ));
    }

    /**
     * TODO: MDL-52243 Move this function to lib/accesslib.php
     *
     * Function used to return a list of users where the given user has a particular capability.
     * This is used e.g. to find all the users where someone is able to manage their learning plans,
     * it also would be useful for mentees etc.
     * @param $capability String - The capability string we are filtering for. If '' is passed,
     *                             an always matching filter is returned.
     * @param $userid int - The user id we are using for the access checks. Defaults to current user.
     * @param $type int - The type of named params to return (passed to $DB->get_in_or_equal).
     * @param $prefix string - The type prefix for the db table (passed to $DB->get_in_or_equal).
     * @return list($sql, $params) Same as $DB->get_in_or_equal().
     */
    public static function filter_users_with_capability_on_user_context_sql($capability,
                                                                            $userid = 0,
                                                                            $type=SQL_PARAMS_QM,
                                                                            $prefix='param') {
        global $USER, $DB;
        $allresultsfilter = array('> 0', array());
        $noresultsfilter = array('= -1', array());

        if (empty($capability)) {
            return $allresultsfilter;
        }

        if (!$capinfo = get_capability_info($capability)) {
            throw new coding_exception('Capability does not exist: ' . $capability);
        }

        if (empty($userid)) {
            $userid = $USER->id;
        }

        // Make sure the guest account and not-logged-in users never get any risky caps no matter what the actual settings are.
        if (($capinfo->captype === 'write') or ($capinfo->riskbitmask & (RISK_XSS | RISK_CONFIG | RISK_DATALOSS))) {
            if (isguestuser($userid) or $userid == 0) {
                return $noresultsfilter;
            }
        }

        if (is_siteadmin($userid)) {
            // No filtering for site admins.
            return $allresultsfilter;
        }

        // Check capability on system level.

        $sql = 'SELECT
                    rc.id,
                    ctx.id contextid,
                    ctx.instanceid,
                    rc.roleid,
                    rc.capability,
                    rc.permission
                FROM {role_capabilities} rc
                JOIN {role_assignments} ra ON ra.contextid = rc.contextid
                JOIN {context} ctx ON (ctx.id = rc.contextid)
                WHERE ctx.contextlevel = :userlevel AND ra.userid = :userid';
        $siterecords = $DB->get_records_sql($sql, array('userlevel' => CONTEXT_SYSTEM, 'userid' => $userid));
        $userrecords = $DB->get_records_sql($sql, array('userlevel' => CONTEXT_USER, 'userid' => $userid));

        $hassystem = false;
        if (!empty($siterecords)) {
            foreach ($siterecords as $record) {
                if ($record->permission == CAP_PROHIBIT) {
                    return $noresultsfilter;
                } else if ($record->permission == CAP_ALLOW) {
                    $hassystem = true;
                }
            }
        }

        if ($hassystem) {
            // If allowed at system, search for roles prohibiting the capability at user context.
            $excludeusers = array();
            foreach ($userrecords as $record) {
                if ($record->permission == CAP_PROHIBIT) {
                    $excludeusers[$record->instanceid] = $record->instanceid;
                }
            }

            // Construct SQL excluding users with this role assigned for this user.
            if (empty($excludeusers)) {
                return $allresultsfilter;
            }
            list($sql, $params) = $DB->get_in_or_equal($excludeusers, $type, $prefix, false);
        } else {
            // If not allowed at system, search for roles allowing the capability at user context.
            // Construct SQL excluding users with this role NOT assigned for this user.
            $allowusers = array();
            $prohibitusers = array();
            foreach ($userrecords as $record) {
                if ($record->permission == CAP_PROHIBIT) {
                    $prohibitusers[$record->instanceid] = $record->instanceid;
                }
                if ($record->permission == CAP_ALLOW) {
                    $allowusers[$record->instanceid] = $record->instanceid;
                }
            }

            foreach ($prohibitusers as $userid => $userid2) {
                unset($allowusers[$record->instanceid]);
            }

            if (empty($allowusers)) {
                return $noresultsfilter;
            }
            list($sql, $params) = $DB->get_in_or_equal($allowusers, $type, $prefix);
        }

        // Return the goods!.
        return array($sql, $params);
    }

    /**
     * Search users.
     *
     * @param string $query
     * @return array
     */
    public static function search_users($query, $capability = '', $limitfrom = 0, $limitnum = 100) {
        global $DB, $CFG, $PAGE, $USER;

        $params = self::validate_parameters(self::search_users_parameters(),
                                            array(
                                                'query' => $query,
                                                'capability' => $capability,
                                                'limitfrom' => $limitfrom,
                                                'limitnum' => $limitnum,
                                            ));
        $query = $params['query'];
        $cap = $params['capability'];
        $limitfrom = $params['limitfrom'];
        $limitnum = $params['limitnum'];

        $context = context_system::instance();
        self::validate_context($context);
        $output = $PAGE->get_renderer('tool_lp');

        list($filtercapsql, $filtercapparams) = self::filter_users_with_capability_on_user_context_sql($cap,
                                                                                                       $USER->id,
                                                                                                       SQL_PARAMS_NAMED);

        $extrasearchfields = array();
        if (!empty($CFG->showuseridentity) && has_capability('moodle/site:viewuseridentity', $context)) {
            $extrasearchfields = explode(',', $CFG->showuseridentity);
        }
        $fields = \user_picture::fields('u', $extrasearchfields);

        list($wheresql, $whereparams) = users_search_sql($query, 'u', true, $extrasearchfields);
        list($sortsql, $sortparams) = users_order_by_sql('u', $query, $context);

        $countsql = "SELECT COUNT('x') FROM {user} u WHERE $wheresql AND u.id $filtercapsql";
        $countparams = $whereparams + $filtercapparams;
        $sql = "SELECT $fields FROM {user} u WHERE $wheresql AND u.id $filtercapsql ORDER BY $sortsql";
        $params = $whereparams + $filtercapparams + $sortparams;

        $count = $DB->count_records_sql($countsql, $countparams);
        $result = $DB->get_recordset_sql($sql, $params, $limitfrom, $limitnum);

        $users = array();
        foreach ($result as $key => $user) {
            // Make sure all required fields are set.
            foreach (user_summary_exporter::define_properties() as $propertykey => $definition) {
                if (empty($user->$propertykey) || !in_array($propertykey, $extrasearchfields)) {
                    if ($propertykey != 'id') {
                        $user->$propertykey = '';
                    }
                }
            }
            $exporter = new user_summary_exporter($user);
            $newuser = $exporter->export($output);

            $users[$key] = $newuser;
        }
        $result->close();

        return array(
            'users' => $users,
            'count' => $count
        );
    }

    /**
     * Returns description of external function result value.
     *
     * @return external_description
     */
    public static function search_users_returns() {
        global $CFG;
        require_once($CFG->dirroot . '/user/externallib.php');
        return new external_single_structure(array(
            'users' => new external_multiple_structure(user_summary_exporter::get_read_structure()),
            'count' => new external_value(PARAM_INT, 'Total number of results.')
        ));
    }

}

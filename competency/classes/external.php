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
 * External API.
 *
 * @package    core_competency
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_competency;
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->libdir/grade/grade_scale.php");

use context;
use context_system;
use context_course;
use context_module;
use context_helper;
use context_user;
use coding_exception;
use external_api;
use external_function_parameters;
use external_value;
use external_format_value;
use external_single_structure;
use external_multiple_structure;
use invalid_parameter_exception;
use required_capability_exception;
use grade_scale;

use core_competency\external\competency_exporter;
use core_competency\external\competency_framework_exporter;
use core_competency\external\course_competency_exporter;
use core_competency\external\course_competency_settings_exporter;
use core_competency\external\course_module_competency_exporter;
use core_competency\external\evidence_exporter;
use core_competency\external\performance_helper;
use core_competency\external\plan_exporter;
use core_competency\external\template_exporter;
use core_competency\external\user_competency_exporter;
use core_competency\external\user_competency_plan_exporter;
use core_competency\external\user_evidence_competency_exporter;
use core_competency\external\user_evidence_exporter;

/**
 * External API class.
 *
 * @package    core_competency
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

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

        $params = self::validate_parameters(self::create_competency_framework_parameters(), array(
            'competencyframework' => $competencyframework
        ));
        $params = $params['competencyframework'];

        $context = self::get_context_from_params($params);
        self::validate_context($context);
        $output = $PAGE->get_renderer('core');

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

        $params = self::validate_parameters(self::read_competency_framework_parameters(), array(
            'id' => $id,
        ));

        $framework = api::read_framework($params['id']);
        self::validate_context($framework->get_context());
        $output = $PAGE->get_renderer('core');
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
     * Returns description of competency_viewed() parameters.
     *
     * @return \external_function_parameters
     */
    public static function competency_viewed_parameters() {
        $id = new external_value(
            PARAM_INT,
            'The competency id',
            VALUE_REQUIRED
        );
        $params = array(
            'id' => $id
        );
        return new external_function_parameters($params);
    }

    /**
     * Log event competency viewed.
     *
     * @param int $id The competency ID.
     * @return boolean
     */
    public static function competency_viewed($id) {
        $params = self::validate_parameters(self::competency_viewed_parameters(), array(
            'id' => $id
        ));
        return api::competency_viewed($params['id']);
    }

    /**
     * Returns description of competency_viewed() result value.
     *
     * @return \external_description
     */
    public static function competency_viewed_returns() {
        return new external_value(PARAM_BOOL, 'True if the event competency viewed was logged');
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
        global $PAGE;
        $params = self::validate_parameters(self::duplicate_competency_framework_parameters(), array(
            'id' => $id,
        ));

        $framework = api::read_framework($params['id']);
        self::validate_context($framework->get_context());

        $output = $PAGE->get_renderer('core');
        $framework = api::duplicate_framework($params['id']);
        $exporter = new competency_framework_exporter($framework);
        $record = $exporter->export($output);
        return $record;
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
        $params = self::validate_parameters(self::delete_competency_framework_parameters(), array(
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

        $params = self::validate_parameters(self::update_competency_framework_parameters(), array(
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
            'shortname'
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
        $onlyvisible = new external_value(
            PARAM_BOOL,
            'Only visible frameworks will be returned if visible true',
            VALUE_DEFAULT,
            false
        );
        $query = new external_value(
            PARAM_RAW,
            'A query string to filter the results',
            VALUE_DEFAULT,
            ''
        );

        $params = array(
            'sort' => $sort,
            'order' => $order,
            'skip' => $skip,
            'limit' => $limit,
            'context' => self::get_context_parameters(),
            'includes' => $includes,
            'onlyvisible' => $onlyvisible,
            'query' => $query,
        );
        return new external_function_parameters($params);
    }

    /**
     * List the existing competency frameworks
     *
     * @param int $sort
     * @param string $order
     * @param string $skip
     * @param int $limit
     * @param array $context
     * @param bool $includes
     * @param bool $onlyvisible
     * @param string $query
     *
     * @return array
     * @throws \required_capability_exception
     * @throws invalid_parameter_exception
     */
    public static function list_competency_frameworks($sort, $order, $skip, $limit, $context, $includes, $onlyvisible,
                                                      $query = '') {
        global $PAGE;

        $params = self::validate_parameters(self::list_competency_frameworks_parameters(), array(
            'sort' => $sort,
            'order' => $order,
            'skip' => $skip,
            'limit' => $limit,
            'context' => $context,
            'includes' => $includes,
            'onlyvisible' => $onlyvisible,
            'query' => $query,
        ));

        $context = self::get_context_from_params($params['context']);
        self::validate_context($context);
        $output = $PAGE->get_renderer('core');

        if ($params['order'] !== '' && $params['order'] !== 'ASC' && $params['order'] !== 'DESC') {
            throw new invalid_parameter_exception('Invalid order param. Must be ASC, DESC or empty.');
        }

        $results = api::list_frameworks($params['sort'],
                                       $params['order'],
                                       $params['skip'],
                                       $params['limit'],
                                       $context,
                                       $params['includes'],
                                       $params['onlyvisible'],
                                       $params['query']);
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
     * @param array $context
     * @param string $includes
     * @return int
     */
    public static function count_competency_frameworks($context, $includes) {
        $params = self::validate_parameters(self::count_competency_frameworks_parameters(), array(
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
     * Returns description of competency_framework_viewed() parameters.
     *
     * @return \external_function_parameters
     */
    public static function competency_framework_viewed_parameters() {
        $id = new external_value(
            PARAM_INT,
            'The competency framework id',
            VALUE_REQUIRED
        );

        $params = array(
            'id' => $id
        );
        return new external_function_parameters($params);
    }

    /**
     * Log event competency framework viewed.
     *
     * @param int $id The competency framework ID.
     * @return boolean
     */
    public static function competency_framework_viewed($id) {
        $params = self::validate_parameters(self::competency_framework_viewed_parameters(), array(
            'id' => $id
        ));
        return api::competency_framework_viewed($params['id']);

    }

    /**
     * Returns description of competency_framework_viewed() result value.
     *
     * @return \external_description
     */
    public static function competency_framework_viewed_returns() {
        return new external_value(PARAM_BOOL, 'True if the event competency framework was logged');
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

        $params = self::validate_parameters(self::create_competency_parameters(), array(
            'competency' => $competency
        ));

        $params = $params['competency'];
        $framework = api::read_framework($params['competencyframeworkid']);
        $context = $framework->get_context();
        self::validate_context($context);
        $output = $PAGE->get_renderer('core');

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

        $params = self::validate_parameters(self::read_competency_parameters(), array(
            'id' => $id,
        ));

        $competency = api::read_competency($params['id']);
        $context = $competency->get_context();
        self::validate_context($context);
        $output = $PAGE->get_renderer('core');
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
        $params = self::validate_parameters(self::delete_competency_parameters(), array(
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
        $params = self::validate_parameters(self::update_competency_parameters(), array(
            'competency' => $competency
        ));
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

        $params = self::validate_parameters(self::list_competencies_parameters(), array(
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
        $validcolumns = array('id', 'shortname', 'description', 'sortorder', 'idnumber',
            'parentid', 'competencyframeworkid');
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
        $output = $PAGE->get_renderer('core');

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

        $params = self::validate_parameters(self::search_competencies_parameters(), array(
            'searchtext' => $searchtext,
            'competencyframeworkid' => $competencyframeworkid
        ));

        $framework = api::read_framework($params['competencyframeworkid']);
        $context = $framework->get_context();
        self::validate_context($context);
        $output = $PAGE->get_renderer('core');

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
     * Count the existing competency frameworks.
     *
     * @param string $filters Filters to use.
     * @return boolean
     */
    public static function count_competencies($filters) {
        $params = self::validate_parameters(self::count_competencies_parameters(), array(
            'filters' => $filters
        ));

        $safefilters = array();
        $validcolumns = array('id', 'shortname', 'description', 'sortorder', 'idnumber', 'parentid', 'competencyframeworkid');
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
        $params = self::validate_parameters(self::set_parent_competency_parameters(), array(
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
        $params = self::validate_parameters(self::move_up_competency_parameters(), array(
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
        $params = self::validate_parameters(self::move_down_competency_parameters(), array(
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
        $params = self::validate_parameters(self::count_courses_using_competency_parameters(), array(
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
        $params = self::validate_parameters(self::count_competencies_in_course_parameters(), array(
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
     * Returns description of list_course_module_competencies() parameters.
     *
     * @return \external_function_parameters
     */
    public static function list_course_module_competencies_parameters() {
        $cmid = new external_value(
            PARAM_INT,
            'The course module id',
            VALUE_REQUIRED
        );
        $params = array(
            'cmid' => $cmid
        );
        return new external_function_parameters($params);
    }

    /**
     * List the course modules using this competency (visible to this user) in this course.
     *
     * @param int $cmid The course module id to check.
     * @return array
     */
    public static function list_course_module_competencies($cmid) {
        global $PAGE;

        $params = self::validate_parameters(self::list_course_module_competencies_parameters(), array(
            'cmid' => $cmid
        ));

        $context = context_module::instance($params['cmid']);
        self::validate_context($context);

        $output = $PAGE->get_renderer('core');

        $apiresult = api::list_course_module_competencies($params['cmid']);
        $result = array();

        foreach ($apiresult as $cmrecord) {
            $one = new \stdClass();
            $exporter = new competency_exporter($cmrecord['competency'], ['context' => $context]);
            $one->competency = $exporter->export($output);
            $exporter = new course_module_competency_exporter($cmrecord['coursemodulecompetency'], ['context' => $context]);
            $one->coursemodulecompetency = $exporter->export($output);

            $result[] = (array) $one;
        }

        return $result;
    }

    /**
     * Returns description of list_course_module_competencies() result value.
     *
     * @return \external_description
     */
    public static function list_course_module_competencies_returns() {
        return new external_multiple_structure(
            new external_single_structure(array(
                'competency' => competency_exporter::get_read_structure(),
                'coursemodulecompetency' => course_module_competency_exporter::get_read_structure()
            ))
        );
    }

    /**
     * Returns description of list_course_competencies() parameters.
     *
     * @return \external_function_parameters
     */
    public static function list_course_competencies_parameters() {
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
     * Returns description of count_course_module_competencies() parameters.
     *
     * @return \external_function_parameters
     */
    public static function count_course_module_competencies_parameters() {
        $cmid = new external_value(
            PARAM_INT,
            'The course module id',
            VALUE_REQUIRED
        );
        $params = array(
            'cmid' => $cmid
        );
        return new external_function_parameters($params);
    }

    /**
     * List the course modules using this competency (visible to this user) in this course.
     *
     * @param int $cmid The course module id to check.
     * @return array
     */
    public static function count_course_module_competencies($cmid) {
        $params = self::validate_parameters(self::count_course_module_competencies_parameters(), array(
            'cmid' => $cmid
        ));

        $context = context_module::instance($params['cmid']);
        self::validate_context($context);

        return api::count_course_module_competencies($params['cmid']);
    }

    /**
     * Returns description of count_course_module_competencies() result value.
     *
     * @return \external_description
     */
    public static function count_course_module_competencies_returns() {
        return new external_value(PARAM_INT, 'The number of competencies found.');
    }

    /**
     * List the competencies (visible to this user) in this course.
     *
     * @param int $courseid The course id to check.
     * @return array
     */
    public static function list_course_competencies($courseid) {
        global $PAGE;

        $params = self::validate_parameters(self::list_course_competencies_parameters(), array(
            'id' => $courseid,
        ));

        $coursecontext = context_course::instance($params['id']);
        self::validate_context($coursecontext);

        $output = $PAGE->get_renderer('core');

        $competencies = api::list_course_competencies($params['id']);
        $result = array();

        $contextcache = array();
        $helper = new performance_helper();
        foreach ($competencies as $competency) {
            $context = $helper->get_context_from_competency($competency['competency']);
            $exporter = new competency_exporter($competency['competency'], array('context' => $context));
            $competencyrecord = $exporter->export($output);
            $exporter = new course_competency_exporter($competency['coursecompetency']);
            $coursecompetencyrecord = $exporter->export($output);

            $result[] = array(
                'competency' => $competencyrecord,
                'coursecompetency' => $coursecompetencyrecord
            );
        }

        return $result;
    }

    /**
     * Returns description of list_course_competencies() result value.
     *
     * @return \external_description
     */
    public static function list_course_competencies_returns() {
        return new external_multiple_structure(
            new external_single_structure(array(
                'competency' => competency_exporter::get_read_structure(),
                'coursecompetency' => course_competency_exporter::get_read_structure()
            ))
        );
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
        $params = self::validate_parameters(self::add_competency_to_course_parameters(), array(
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
        $params = self::validate_parameters(self::remove_competency_from_course_parameters(), array(
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
        $params = self::validate_parameters(self::reorder_course_competency_parameters(), array(
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
        $params = self::validate_parameters(self::reorder_template_competency_parameters(), array(
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

        $params = self::validate_parameters(self::create_template_parameters(), array('template' => $template));
        $params = $params['template'];
        $context = self::get_context_from_params($params);
        self::validate_context($context);
        $output = $PAGE->get_renderer('core');

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

        $params = self::validate_parameters(self::read_template_parameters(), array('id' => $id));
        $template = api::read_template($params['id']);
        self::validate_context($template->get_context());
        $output = $PAGE->get_renderer('core');

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

        $deleteplans = new external_value(
            PARAM_BOOL,
            'Boolean to indicate if plans must be deleted',
            VALUE_REQUIRED
        );

        $params = array(
            'id' => $id,
            'deleteplans' => $deleteplans
        );
        return new external_function_parameters($params);
    }

    /**
     * Delete a learning plan template
     *
     * @param int $id The learning plan template id
     * @param boolean $deleteplans True to delete the plans associated to template or false to unlink them
     * @return boolean
     */
    public static function delete_template($id, $deleteplans = true) {
        $params = self::validate_parameters(self::delete_template_parameters(), array(
            'id' => $id,
            'deleteplans' => $deleteplans,
        ));

        $template = api::read_template($params['id']);
        self::validate_context($template->get_context());

        return api::delete_template($params['id'], $params['deleteplans']);
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

        $params = self::validate_parameters(self::update_template_parameters(), array('template' => $template));
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

        $params = self::validate_parameters(self::duplicate_template_parameters(), array('id' => $id));

        $template = api::read_template($params['id']);
        self::validate_context($template->get_context());
        $output = $PAGE->get_renderer('core');

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
        $onlyvisible = new external_value(
            PARAM_BOOL,
            'If should list only visible templates',
            VALUE_DEFAULT,
            false
        );

        $params = array(
            'sort' => $sort,
            'order' => $order,
            'skip' => $skip,
            'limit' => $limit,
            'context' => self::get_context_parameters(),
            'includes' => $includes,
            'onlyvisible' => $onlyvisible
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
     * @param bool $onlyvisible
     *
     * @return array
     */
    public static function list_templates($sort, $order, $skip, $limit, $context, $includes, $onlyvisible) {
        global $PAGE;

        $params = self::validate_parameters(self::list_templates_parameters(), array(
            'sort' => $sort,
            'order' => $order,
            'skip' => $skip,
            'limit' => $limit,
            'context' => $context,
            'includes' => $includes,
            'onlyvisible' => $onlyvisible
        ));

        $context = self::get_context_from_params($params['context']);
        self::validate_context($context);
        $output = $PAGE->get_renderer('core');

        if ($params['order'] !== '' && $params['order'] !== 'ASC' && $params['order'] !== 'DESC') {
            throw new invalid_parameter_exception('Invalid order param. Must be ASC, DESC or empty.');
        }

        $results = api::list_templates($params['sort'],
                                       $params['order'],
                                       $params['skip'],
                                       $params['limit'],
                                       $context,
                                       $params['includes'],
                                       $params['onlyvisible']);
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
     * @param array $context
     * @param string $includes
     * @return int
     */
    public static function count_templates($context, $includes) {
        $params = self::validate_parameters(self::count_templates_parameters(), array(
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
        $params = self::validate_parameters(self::count_templates_using_competency_parameters(), array(
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

        $params = self::validate_parameters(self::list_templates_using_competency_parameters(), array(
            'id' => $competencyid,
        ));

        $competency = api::read_competency($params['id']);
        self::validate_context($competency->get_context());
        $output = $PAGE->get_renderer('core');

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
        $params = self::validate_parameters(self::count_competencies_in_template_parameters(), array(
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

        $params = self::validate_parameters(self::list_competencies_in_template_parameters(), array(
            'id' => $templateid,
        ));

        $template = api::read_template($params['id']);
        self::validate_context($template->get_context());
        $output = $PAGE->get_renderer('core');

        $competencies = api::list_competencies_in_template($params['id']);
        $results = array();

        $helper = new performance_helper();
        foreach ($competencies as $competency) {
            $context = $helper->get_context_from_competency($competency);
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
        $params = self::validate_parameters(self::add_competency_to_template_parameters(), array(
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
        $params = self::validate_parameters(self::add_competency_to_plan_parameters(), array(
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
        $params = self::validate_parameters(self::remove_competency_from_plan_parameters(), array(
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
        $params = self::validate_parameters(self::reorder_plan_competency_parameters(), array(
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
     * Returns description of external function parameters.
     *
     * @return \external_function_parameters
     */
    public static function user_competency_cancel_review_request_parameters() {
        return new external_function_parameters(array(
            'userid' => new external_value(PARAM_INT, 'The user ID'),
            'competencyid' => new external_value(PARAM_INT, 'The competency ID'),
        ));
    }

    /**
     * External function user_competency_cancel_review_request.
     *
     * @param int $userid The user ID.
     * @param int $competencyid The competency ID.
     * @return boolean
     */
    public static function user_competency_cancel_review_request($userid, $competencyid) {
        $params = self::validate_parameters(self::user_competency_cancel_review_request_parameters(), array(
            'userid' => $userid,
            'competencyid' => $competencyid
        ));

        $context = context_user::instance($params['userid']);
        self::validate_context($context);

        return api::user_competency_cancel_review_request($userid, $competencyid);
    }

    /**
     * Returns description of external function result value.
     *
     * @return \external_function_parameters
     */
    public static function user_competency_cancel_review_request_returns() {
        return new external_value(PARAM_BOOL, 'The success');
    }

    /**
     * Returns description of external function parameters.
     *
     * @return \external_function_parameters
     */
    public static function user_competency_request_review_parameters() {
        return new external_function_parameters(array(
            'userid' => new external_value(PARAM_INT, 'The user ID'),
            'competencyid' => new external_value(PARAM_INT, 'The competency ID'),
        ));
    }

    /**
     * External function user_competency_request_review.
     *
     * @param int $userid The user ID.
     * @param int $competencyid The competency ID.
     * @return boolean
     */
    public static function user_competency_request_review($userid, $competencyid) {
        $params = self::validate_parameters(self::user_competency_request_review_parameters(), array(
            'userid' => $userid,
            'competencyid' => $competencyid,
        ));

        $context = context_user::instance($params['userid']);
        self::validate_context($context);

        return api::user_competency_request_review($userid, $competencyid);
    }

    /**
     * Returns description of external function result value.
     *
     * @return \external_function_parameters
     */
    public static function user_competency_request_review_returns() {
        return new external_value(PARAM_BOOL, 'The success');
    }

    /**
     * Returns description of external function parameters.
     *
     * @return \external_function_parameters
     */
    public static function user_competency_start_review_parameters() {
        return new external_function_parameters(array(
            'userid' => new external_value(PARAM_INT, 'The user ID'),
            'competencyid' => new external_value(PARAM_INT, 'The competency ID'),
        ));
    }

    /**
     * External function user_competency_start_review.
     *
     * @param int $userid The user ID.
     * @param int $competencyid The competency ID.
     * @return boolean
     */
    public static function user_competency_start_review($userid, $competencyid) {
        $params = self::validate_parameters(self::user_competency_start_review_parameters(), array(
            'userid' => $userid,
            'competencyid' => $competencyid
        ));

        $context = context_user::instance($params['userid']);
        self::validate_context($context);

        return api::user_competency_start_review($userid, $competencyid);
    }

    /**
     * Returns description of external function result value.
     *
     * @return \external_function_parameters
     */
    public static function user_competency_start_review_returns() {
        return new external_value(PARAM_BOOL, 'The success');
    }

    /**
     * Returns description of external function parameters.
     *
     * @return \external_function_parameters
     */
    public static function user_competency_stop_review_parameters() {
        return new external_function_parameters(array(
            'userid' => new external_value(PARAM_INT, 'The user ID'),
            'competencyid' => new external_value(PARAM_INT, 'The competency ID'),
        ));
    }

    /**
     * External function user_competency_stop_review.
     *
     * @param int $userid The user ID.
     * @param int $competencyid The competency ID.
     * @return boolean
     */
    public static function user_competency_stop_review($userid, $competencyid) {
        $params = self::validate_parameters(self::user_competency_stop_review_parameters(), array(
            'userid' => $userid,
            'competencyid' => $competencyid
        ));

        $context = context_user::instance($params['userid']);
        self::validate_context($context);

        return api::user_competency_stop_review($userid, $competencyid);
    }

    /**
     * Returns description of external function result value.
     *
     * @return \external_function_parameters
     */
    public static function user_competency_stop_review_returns() {
        return new external_value(PARAM_BOOL, 'The success');
    }

    /**
     * Returns description of template_has_related_data() parameters.
     *
     * @return \external_function_parameters
     */
    public static function template_has_related_data_parameters() {
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
     * Check if template has related data.
     *
     * @param int $templateid Template id.
     * @return boolean
     */
    public static function template_has_related_data($templateid) {
        $params = self::validate_parameters(self::template_has_related_data_parameters(), array(
            'id' => $templateid,
        ));

        $template = api::read_template($params['id']);
        self::validate_context($template->get_context());

        return api::template_has_related_data($params['id']);
    }

    /**
     * Returns description of template_has_related_data() result value.
     *
     * @return \external_description
     */
    public static function template_has_related_data_returns() {
        return new external_value(PARAM_BOOL, 'True if the template has related data');
    }

    /**
     * Count the competencies (visible to this user) in this learning plan template.
     *
     * @param int $templateid Template id.
     * @param int $competencyid Competency id.
     * @return int
     */
    public static function remove_competency_from_template($templateid, $competencyid) {
        $params = self::validate_parameters(self::remove_competency_from_template_parameters(), array(
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

        $params = self::validate_parameters(self::create_plan_parameters(), array('plan' => $plan));
        $params = $params['plan'];

        $context = context_user::instance($params['userid']);
        self::validate_context($context);
        $output = $PAGE->get_renderer('core');

        $params = (object) $params;

        $result = api::create_plan($params);
        $exporter = new plan_exporter($result, array('template' => null));
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

        $params = self::validate_parameters(self::update_plan_parameters(), array('plan' => $plan));

        $params = $params['plan'];

        $plan = api::read_plan($params['id']);
        self::validate_context($plan->get_context());
        $output = $PAGE->get_renderer('core');

        $params = (object) $params;
        $result = api::update_plan($params);
        $exporter = new plan_exporter($result, ['template' => $plan->get_template()]);
        return $exporter->export($output);
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
        $params = self::validate_parameters(self::complete_plan_parameters(), array(
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
        $params = self::validate_parameters(self::reopen_plan_parameters(), array(
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

        $params = self::validate_parameters(self::read_plan_parameters(), array(
            'id' => $id,
        ));

        $plan = api::read_plan($params['id']);
        self::validate_context($plan->get_context());
        $output = $PAGE->get_renderer('core');

        $exporter = new plan_exporter($plan, array('template' => $plan->get_template()));
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
        $params = self::validate_parameters(self::delete_plan_parameters(), array(
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
     * Returns description of external function parameters.
     *
     * @return \external_function_parameters
     */
    public static function plan_cancel_review_request_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'The plan ID'),
        ));
    }

    /**
     * External function plan_cancel_review_request.
     *
     * @param int $id The plan ID.
     * @return boolean
     */
    public static function plan_cancel_review_request($id) {
        $params = self::validate_parameters(self::plan_cancel_review_request_parameters(), array(
            'id' => $id
        ));

        $plan = api::read_plan($id);
        self::validate_context($plan->get_context());

        return api::plan_cancel_review_request($plan);
    }

    /**
     * Returns description of external function result value.
     *
     * @return \external_function_parameters
     */
    public static function plan_cancel_review_request_returns() {
        return new external_value(PARAM_BOOL, 'The success');
    }

    /**
     * Returns description of external function parameters.
     *
     * @return \external_function_parameters
     */
    public static function plan_request_review_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'The plan ID'),
        ));
    }

    /**
     * External function plan_request_review.
     *
     * @param int $id The plan ID.
     * @return boolean
     */
    public static function plan_request_review($id) {
        $params = self::validate_parameters(self::plan_request_review_parameters(), array(
            'id' => $id
        ));

        $plan = api::read_plan($id);
        self::validate_context($plan->get_context());

        return api::plan_request_review($plan);
    }

    /**
     * Returns description of external function result value.
     *
     * @return \external_function_parameters
     */
    public static function plan_request_review_returns() {
        return new external_value(PARAM_BOOL, 'The success');
    }

    /**
     * Returns description of external function parameters.
     *
     * @return \external_function_parameters
     */
    public static function plan_start_review_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'The plan ID'),
        ));
    }

    /**
     * External function plan_start_review.
     *
     * @param int $id The plan ID.
     * @return boolean
     */
    public static function plan_start_review($id) {
        $params = self::validate_parameters(self::plan_start_review_parameters(), array(
            'id' => $id
        ));

        $plan = api::read_plan($id);
        self::validate_context($plan->get_context());

        return api::plan_start_review($plan);
    }

    /**
     * Returns description of external function result value.
     *
     * @return \external_function_parameters
     */
    public static function plan_start_review_returns() {
        return new external_value(PARAM_BOOL, 'The success');
    }

    /**
     * Returns description of external function parameters.
     *
     * @return \external_function_parameters
     */
    public static function plan_stop_review_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'The plan ID'),
        ));
    }

    /**
     * External function plan_stop_review.
     *
     * @param int $id The plan ID.
     * @return boolean
     */
    public static function plan_stop_review($id) {
        $params = self::validate_parameters(self::plan_stop_review_parameters(), array(
            'id' => $id
        ));

        $plan = api::read_plan($id);
        self::validate_context($plan->get_context());

        return api::plan_stop_review($plan);
    }

    /**
     * Returns description of external function result value.
     *
     * @return \external_function_parameters
     */
    public static function plan_stop_review_returns() {
        return new external_value(PARAM_BOOL, 'The success');
    }

    /**
     * Returns description of external function parameters.
     *
     * @return \external_function_parameters
     */
    public static function approve_plan_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'The plan ID'),
        ));
    }

    /**
     * External function approve_plan.
     *
     * @param int $id The plan ID.
     * @return boolean
     */
    public static function approve_plan($id) {
        $params = self::validate_parameters(self::approve_plan_parameters(), array(
            'id' => $id,
        ));

        $plan = api::read_plan($id);
        self::validate_context($plan->get_context());

        return api::approve_plan($plan);
    }

    /**
     * Returns description of external function result value.
     *
     * @return \external_function_parameters
     */
    public static function approve_plan_returns() {
        return new external_value(PARAM_BOOL, 'The success');
    }

    /**
     * Returns description of external function parameters.
     *
     * @return \external_function_parameters
     */
    public static function unapprove_plan_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'The plan ID'),
        ));
    }

    /**
     * External function unapprove_plan.
     *
     * @param int $id The plan ID.
     * @return boolean
     */
    public static function unapprove_plan($id) {
        $params = self::validate_parameters(self::unapprove_plan_parameters(), array(
            'id' => $id,
        ));

        $plan = api::read_plan($id);
        self::validate_context($plan->get_context());

        return api::unapprove_plan($plan);
    }

    /**
     * Returns description of external function result value.
     *
     * @return \external_function_parameters
     */
    public static function unapprove_plan_returns() {
        return new external_value(PARAM_BOOL, 'The success');
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
        $output = $PAGE->get_renderer('core');

        $result = api::list_plan_competencies($plan);

        if ($plan->get('status') == plan::STATUS_COMPLETE) {
            $ucproperty = 'usercompetencyplan';
        } else {
            $ucproperty = 'usercompetency';
        }

        $helper = new performance_helper();
        foreach ($result as $key => $r) {
            $context = $helper->get_context_from_competency($r->competency);
            $scale = $helper->get_scale_from_competency($r->competency);

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
     * Returns description of external function parameters.
     *
     * @return \external_function_parameters
     */
    public static function list_user_plans_parameters() {
        return new external_function_parameters(array(
            'userid' => new external_value(PARAM_INT, 'The user ID'),
        ));
    }

    /**
     * External function list_user_plans.
     *
     * @param int $userid The user ID.
     * @return boolean
     */
    public static function list_user_plans($userid) {
        global $PAGE;
        $params = self::validate_parameters(self::list_user_plans_parameters(), array(
            'userid' => $userid
        ));

        $context = context_user::instance($params['userid']);
        self::validate_context($context);
        $output = $PAGE->get_renderer('core');

        $response = array();
        $plans = api::list_user_plans($params['userid']);
        foreach ($plans as $plan) {
            $exporter = new plan_exporter($plan, array('template' => $plan->get_template()));
            $response[] = $exporter->export($output);
        }

        return $response;
    }

    /**
     * Returns description of external function result value.
     *
     * @return \external_function_parameters
     */
    public static function list_user_plans_returns() {
        return new external_multiple_structure(
            plan_exporter::get_read_structure()
        );
    }

    /**
     * Returns description of external function parameters.
     *
     * @return \external_description
     */
    public static function read_user_evidence_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'The user evidence ID.'),
        ));
    }

    /**
     * Delete a user evidence.
     *
     * @param int $id The evidence id
     * @return boolean
     */
    public static function read_user_evidence($id) {
        global $PAGE;
        $params = self::validate_parameters(self::read_user_evidence_parameters(), array('id' => $id));

        $userevidence = api::read_user_evidence($params['id']);
        $context = $userevidence->get_context();
        self::validate_context($context);
        $output = $PAGE->get_renderer('core');

        $exporter = new user_evidence_exporter($userevidence, array('context' => $context,
            'competencies' => $userevidence->get_competencies()));
        return $exporter->export($output);
    }

    /**
     * Returns description of external function result value.
     *
     * @return \external_description
     */
    public static function read_user_evidence_returns() {
        return user_evidence_exporter::get_read_structure();
    }

    /**
     * Returns description of external function parameters.
     *
     * @return \external_function_parameters
     */
    public static function delete_user_evidence_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'The user evidence ID.'),
        ));
    }

    /**
     * Delete a user evidence.
     *
     * @param int $id The evidence id
     * @return boolean
     */
    public static function delete_user_evidence($id) {
        $params = self::validate_parameters(self::delete_user_evidence_parameters(), array('id' => $id));

        $userevidence = api::read_user_evidence($params['id']);
        self::validate_context($userevidence->get_context());

        return api::delete_user_evidence($userevidence->get('id'));
    }

    /**
     * Returns description of external function result value.
     *
     * @return \external_description
     */
    public static function delete_user_evidence_returns() {
        return new external_value(PARAM_BOOL, 'True if the delete was successful');
    }

    /**
     * Returns description of external function parameters.
     *
     * @return \external_function_parameters
     */
    public static function create_user_evidence_competency_parameters() {
        return new external_function_parameters(array(
            'userevidenceid' => new external_value(PARAM_INT, 'The user evidence ID.'),
            'competencyid' => new external_value(PARAM_INT, 'The competency ID.'),
        ));
    }

    /**
     * Delete a user evidence competency relationship.
     *
     * @param int $userevidenceid The user evidence id.
     * @param int $competencyid The competency id.
     * @return boolean
     */
    public static function create_user_evidence_competency($userevidenceid, $competencyid) {
        global $PAGE;
        $params = self::validate_parameters(self::create_user_evidence_competency_parameters(), array(
            'userevidenceid' => $userevidenceid,
            'competencyid' => $competencyid,
        ));

        $userevidence = api::read_user_evidence($params['userevidenceid']);
        self::validate_context($userevidence->get_context());

        $relation = api::create_user_evidence_competency($userevidence, $competencyid);
        $exporter = new user_evidence_competency_exporter($relation);
        return $exporter->export($PAGE->get_renderer('core'));
    }

    /**
     * Returns description of external function result value.
     *
     * @return \external_description
     */
    public static function create_user_evidence_competency_returns() {
        return user_evidence_competency_exporter::get_read_structure();
    }

    /**
     * Returns description of external function parameters.
     *
     * @return \external_function_parameters
     */
    public static function delete_user_evidence_competency_parameters() {
        return new external_function_parameters(array(
            'userevidenceid' => new external_value(PARAM_INT, 'The user evidence ID.'),
            'competencyid' => new external_value(PARAM_INT, 'The competency ID.'),
        ));
    }

    /**
     * Delete a user evidence competency relationship.
     *
     * @param int $userevidenceid The user evidence id.
     * @param int $competencyid The competency id.
     * @return boolean
     */
    public static function delete_user_evidence_competency($userevidenceid, $competencyid) {
        $params = self::validate_parameters(self::delete_user_evidence_competency_parameters(), array(
            'userevidenceid' => $userevidenceid,
            'competencyid' => $competencyid,
        ));

        $userevidence = api::read_user_evidence($params['userevidenceid']);
        self::validate_context($userevidence->get_context());

        return api::delete_user_evidence_competency($userevidence, $params['competencyid']);
    }

    /**
     * Returns description of external function result value.
     *
     * @return \external_description
     */
    public static function delete_user_evidence_competency_returns() {
        return new external_value(PARAM_BOOL, 'True if the delete was successful');
    }

    /**
     * Returns description of external function parameters.
     *
     * @return \external_function_parameters
     */
    public static function request_review_of_user_evidence_linked_competencies_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'The user evidence ID.')
        ));
    }

    /**
     * Send user evidence competencies to review.
     *
     * @param int $id The user evidence id.
     * @return boolean
     */
    public static function request_review_of_user_evidence_linked_competencies($id) {
        $params = self::validate_parameters(self::request_review_of_user_evidence_linked_competencies_parameters(), array(
            'id' => $id
        ));

        $userevidence = api::read_user_evidence($id);
        self::validate_context($userevidence->get_context());

        return api::request_review_of_user_evidence_linked_competencies($id);
    }

    /**
     * Returns description of external function result value.
     *
     * @return \external_description
     */
    public static function request_review_of_user_evidence_linked_competencies_returns() {
        return new external_value(PARAM_BOOL, 'True if all competencies were send to review');
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
        $scalevalues = $scale->load_items();
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
            ))
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
        $params = self::validate_parameters(self::add_related_competency_parameters(), array(
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
        $params = self::validate_parameters(self::remove_related_competency_parameters(), array(
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
     * Returns description of update_ruleoutcome_course_competency() parameters.
     *
     * @return \external_function_parameters
     */
    public static function set_course_competency_ruleoutcome_parameters() {
        $coursecompetencyid = new external_value(
            PARAM_INT,
            'Data base record id for the course competency',
            VALUE_REQUIRED
        );

        $ruleoutcome = new external_value(
            PARAM_INT,
            'Ruleoutcome value',
            VALUE_REQUIRED
        );

        $params = array(
            'coursecompetencyid' => $coursecompetencyid,
            'ruleoutcome' => $ruleoutcome,
        );
        return new external_function_parameters($params);
    }

    /**
     * Change the ruleoutcome of a course competency.
     *
     * @param int $coursecompetencyid The course competency id
     * @param int $ruleoutcome The ruleoutcome value
     * @return bool
     */
    public static function set_course_competency_ruleoutcome($coursecompetencyid, $ruleoutcome) {
        $params = self::validate_parameters(self::set_course_competency_ruleoutcome_parameters(), array(
            'coursecompetencyid' => $coursecompetencyid,
            'ruleoutcome' => $ruleoutcome,
        ));

        $coursecompetency = new course_competency($params['coursecompetencyid']);
        self::validate_context(context_course::instance($coursecompetency->get('courseid')));

        return api::set_course_competency_ruleoutcome($coursecompetency, $params['ruleoutcome']);
    }

    /**
     * Returns description of update_ruleoutcome_course_competency() result value.
     *
     * @return \external_value
     */
    public static function set_course_competency_ruleoutcome_returns() {
        return new external_value(PARAM_BOOL, 'True if the update was successful');
    }


    /**
     * Returns description of external function parameters.
     *
     * @return \external_function_parameters
     */
    public static function grade_competency_parameters() {
        $userid = new external_value(
            PARAM_INT,
            'User ID',
            VALUE_REQUIRED
        );
        $competencyid = new external_value(
            PARAM_INT,
            'Competency ID',
            VALUE_REQUIRED
        );
        $grade = new external_value(
            PARAM_INT,
            'New grade',
            VALUE_REQUIRED
        );
        $note = new external_value(
            PARAM_NOTAGS,
            'A note to attach to the evidence',
            VALUE_DEFAULT
        );

        $params = array(
            'userid' => $userid,
            'competencyid' => $competencyid,
            'grade' => $grade,
            'note' => $note,
        );
        return new external_function_parameters($params);
    }

    /**
     * Grade a competency.
     *
     * @param int $userid The user ID.
     * @param int $competencyid The competency id
     * @param int $grade The new grade value
     * @param string $note A note to attach to the evidence
     * @return bool
     */
    public static function grade_competency($userid, $competencyid, $grade, $note = null) {
        global $USER, $PAGE;
        $params = self::validate_parameters(self::grade_competency_parameters(), array(
            'userid' => $userid,
            'competencyid' => $competencyid,
            'grade' => $grade,
            'note' => $note
        ));

        $uc = api::get_user_competency($params['userid'], $params['competencyid']);
        self::validate_context($uc->get_context());

        $output = $PAGE->get_renderer('core');
        $evidence = api::grade_competency(
                $uc->get('userid'),
                $uc->get('competencyid'),
                $params['grade'],
                $params['note']
        );

        $scale = $uc->get_competency()->get_scale();
        $exporter = new evidence_exporter($evidence, [
            'actionuser' => $USER,
            'scale' => $scale,
            'usercompetency' => $uc,
            'usercompetencyplan' => null,
            'context' => $evidence->get_context()
        ]);
        return $exporter->export($output);
    }

    /**
     * Returns description of external function result value.
     *
     * @return \external_value
     */
    public static function grade_competency_returns() {
        return evidence_exporter::get_read_structure();
    }

    /**
     * Returns description of grade_competency_in_plan() parameters.
     *
     * @return \external_function_parameters
     */
    public static function grade_competency_in_plan_parameters() {
        $planid = new external_value(
            PARAM_INT,
            'Plan id',
            VALUE_REQUIRED
        );
        $competencyid = new external_value(
            PARAM_INT,
            'Competency id',
            VALUE_REQUIRED
        );
        $grade = new external_value(
            PARAM_INT,
            'New grade',
            VALUE_REQUIRED
        );
        $note = new external_value(
            PARAM_NOTAGS,
            'A note to attach to the evidence',
            VALUE_DEFAULT
        );

        $params = array(
            'planid' => $planid,
            'competencyid' => $competencyid,
            'grade' => $grade,
            'note' => $note
        );
        return new external_function_parameters($params);
    }

    /**
     * Grade a competency in a plan.
     *
     * @param int $planid The plan id
     * @param int $competencyid The competency id
     * @param int $grade The new grade value
     * @param string $note A note to add to the evidence
     * @return bool
     */
    public static function grade_competency_in_plan($planid, $competencyid, $grade, $note = null) {
        global $USER, $PAGE;

        $params = self::validate_parameters(self::grade_competency_in_plan_parameters(), array(
            'planid' => $planid,
            'competencyid' => $competencyid,
            'grade' => $grade,
            'note' => $note
        ));

        $plan = new plan($params['planid']);
        $context = $plan->get_context();
        self::validate_context($context);
        $output = $PAGE->get_renderer('core');

        $evidence = api::grade_competency_in_plan(
                $plan->get('id'),
                $params['competencyid'],
                $params['grade'],
                $params['note']
        );
        $competency = api::read_competency($params['competencyid']);
        $scale = $competency->get_scale();
        $exporter = new evidence_exporter($evidence, [
            'actionuser' => $USER,
            'scale' => $scale,
            'usercompetency' => null,
            'usercompetencyplan' => null,
            'context' => $evidence->get_context()
        ]);
        return $exporter->export($output);
    }

    /**
     * Returns description of grade_competency_in_plan() result value.
     *
     * @return \external_value
     */
    public static function grade_competency_in_plan_returns() {
        return evidence_exporter::get_read_structure();
    }

    /**
     * Returns description of user_competency_viewed() parameters.
     *
     * @return \external_function_parameters
     */
    public static function user_competency_viewed_parameters() {
        $usercompetencyid = new external_value(
            PARAM_INT,
            'The user competency id',
            VALUE_REQUIRED
        );
        $params = array(
            'usercompetencyid' => $usercompetencyid
        );
        return new external_function_parameters($params);
    }

    /**
     * Log user competency viewed event.
     *
     * @param int $usercompetencyid The user competency ID.
     * @return boolean
     */
    public static function user_competency_viewed($usercompetencyid) {
        $params = self::validate_parameters(self::user_competency_viewed_parameters(), array(
            'usercompetencyid' => $usercompetencyid
        ));

        $uc = api::get_user_competency_by_id($params['usercompetencyid']);
        $result = api::user_competency_viewed($uc);

        return $result;
    }

    /**
     * Returns description of user_competency_viewed() result value.
     *
     * @return \external_description
     */
    public static function user_competency_viewed_returns() {
        return new external_value(PARAM_BOOL, 'True if the event user competency viewed was logged');
    }

    /**
     * Returns description of user_competency_viewed_in_plan() parameters.
     *
     * @return \external_function_parameters
     */
    public static function user_competency_viewed_in_plan_parameters() {
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
        $planid = new external_value(
            PARAM_INT,
            'The plan id',
            VALUE_REQUIRED
        );
        $params = array(
            'competencyid' => $competencyid,
            'userid' => $userid,
            'planid' => $planid
        );
        return new external_function_parameters($params);
    }

    /**
     * Log user competency viewed in plan event.
     *
     * @param int $competencyid The competency ID.
     * @param int $userid The user ID.
     * @param int $planid The plan ID.
     * @return boolean
     */
    public static function user_competency_viewed_in_plan($competencyid, $userid, $planid) {
        $params = self::validate_parameters(self::user_competency_viewed_in_plan_parameters(), array(
            'competencyid' => $competencyid,
            'userid' => $userid,
            'planid' => $planid
        ));
        $pl = api::get_plan_competency($params['planid'], $params['competencyid']);
        $result = api::user_competency_viewed_in_plan($pl->usercompetency, $params['planid']);

        return $result;
    }

    /**
     * Returns description of user_competency_viewed_in_plan() result value.
     *
     * @return \external_description
     */
    public static function user_competency_viewed_in_plan_returns() {
        return new external_value(PARAM_BOOL, 'True if the event user competency viewed in plan was logged');
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
        $params = array(
            'competencyid' => $competencyid,
            'userid' => $userid,
            'courseid' => $courseid
        );
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
        $params = self::validate_parameters(self::user_competency_viewed_in_course_parameters(), array(
            'competencyid' => $competencyid,
            'userid' => $userid,
            'courseid' => $courseid
        ));
        $ucc = api::get_user_competency_in_course($params['courseid'], $params['userid'], $params['competencyid']);
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
     * Returns description of user_competency_plan_viewed() parameters.
     *
     * @return \external_function_parameters
     */
    public static function user_competency_plan_viewed_parameters() {
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
        $planid = new external_value(
            PARAM_INT,
            'The plan id',
            VALUE_REQUIRED
        );
        $params = array(
            'competencyid' => $competencyid,
            'userid' => $userid,
            'planid' => $planid
        );
        return new external_function_parameters($params);
    }

    /**
     * Log user competency plan viewed event.
     *
     * @param int $competencyid The competency ID.
     * @param int $userid The user ID.
     * @param int $planid The plan ID.
     * @return boolean
     */
    public static function user_competency_plan_viewed($competencyid, $userid, $planid) {
        $params = self::validate_parameters(self::user_competency_viewed_in_plan_parameters(), array(
            'competencyid' => $competencyid,
            'userid' => $userid,
            'planid' => $planid
        ));
        $pl = api::get_plan_competency($params['planid'], $params['competencyid']);
        $result = api::user_competency_plan_viewed($pl->usercompetencyplan);

        return $result;
    }

    /**
     * Returns description of user_competency_plan_viewed() result value.
     *
     * @return \external_description
     */
    public static function user_competency_plan_viewed_returns() {
        return new external_value(PARAM_BOOL, 'True if the event user competency plan viewed was logged');
    }

    /**
     * Returns description of grade_competency_in_course() parameters.
     *
     * @return \external_function_parameters
     */
    public static function grade_competency_in_course_parameters() {
        $courseid = new external_value(
            PARAM_INT,
            'Course id',
            VALUE_REQUIRED
        );
        $userid = new external_value(
            PARAM_INT,
            'User id',
            VALUE_REQUIRED
        );
        $competencyid = new external_value(
            PARAM_INT,
            'Competency id',
            VALUE_REQUIRED
        );
        $grade = new external_value(
            PARAM_INT,
            'New grade',
            VALUE_REQUIRED
        );
        $note = new external_value(
            PARAM_NOTAGS,
            'A note to attach to the evidence',
            VALUE_DEFAULT
        );

        $params = array(
            'courseid' => $courseid,
            'userid' => $userid,
            'competencyid' => $competencyid,
            'grade' => $grade,
            'note' => $note,
        );
        return new external_function_parameters($params);
    }

    /**
     * Grade a competency in a course.
     *
     * @param int $courseid The course id
     * @param int $userid The user id
     * @param int $competencyid The competency id
     * @param int $grade The new grade value
     * @param string $note A note to add to the evidence
     * @return bool
     */
    public static function grade_competency_in_course($courseid, $userid, $competencyid, $grade, $note = null) {
        global $USER, $PAGE, $DB;

        $params = self::validate_parameters(self::grade_competency_in_course_parameters(), array(
            'courseid' => $courseid,
            'userid' => $userid,
            'competencyid' => $competencyid,
            'grade' => $grade,
            'note' => $note
        ));

        $course = $DB->get_record('course', array('id' => $params['courseid']));
        $context = context_course::instance($course->id);
        self::validate_context($context);
        $output = $PAGE->get_renderer('core');

        $evidence = api::grade_competency_in_course(
                $params['courseid'],
                $params['userid'],
                $params['competencyid'],
                $params['grade'],
                $params['note']
        );
        $competency = api::read_competency($params['competencyid']);
        $scale = $competency->get_scale();
        $exporter = new evidence_exporter($evidence, array(
            'actionuser' => $USER,
            'scale' => $scale,
            'usercompetency' => null,
            'usercompetencyplan' => null,
            'context' => $evidence->get_context(),
        ));
        return $exporter->export($output);
    }

    /**
     * Returns description of grade_competency_in_course() result value.
     *
     * @return \external_value
     */
    public static function grade_competency_in_course_returns() {
        return evidence_exporter::get_read_structure();
    }

    /**
     * Returns description of unlink_plan_from_template_() parameters.
     *
     * @return \external_function_parameters
     */
    public static function unlink_plan_from_template_parameters() {
        $planid = new external_value(
            PARAM_INT,
            'Data base record id for the plan',
            VALUE_REQUIRED
        );

        $params = array(
            'planid' => $planid,
        );
        return new external_function_parameters($params);
    }

    /**
     * Unlink the plan from the template.
     *
     * @param int $planid The plan id
     * @return bool
     */
    public static function unlink_plan_from_template($planid) {
        $params = self::validate_parameters(self::unlink_plan_from_template_parameters(), array(
            'planid' => $planid,
        ));

        $plan = new plan($params['planid']);
        self::validate_context($plan->get_context());

        return api::unlink_plan_from_template($plan);
    }

    /**
     * Returns description of unlink_plan_from_template_() result value.
     *
     * @return \external_value
     */
    public static function unlink_plan_from_template_returns() {
        return new external_value(PARAM_BOOL, 'True if the unlink was successful');
    }

    /**
     * Returns description of template_viewed() parameters.
     *
     * @return \external_function_parameters
     */
    public static function template_viewed_parameters() {
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
     * Log the template viewed event.
     *
     * @param int $id the template id
     * @return array of warnings and status result
     * @throws moodle_exception
     */
    public static function template_viewed($id) {
        $params = self::validate_parameters(self::view_book_parameters(), array(
            'id' => $id
        ));

        $template = api::read_template($params['id']);
        self::validate_context($template->get_context());

        return api::template_viewed($params['id']);
    }

    /**
     * Returns description of template_viewed() result value.
     *
     * @return \external_value
     */
    public static function template_viewed_returns() {
        return new external_value(PARAM_BOOL, 'True if the log of the view was successful');
    }

    /**
     * Returns description of update_course_competency_settings() parameters.
     *
     * @return \external_function_parameters
     */
    public static function update_course_competency_settings_parameters() {
        $courseid = new external_value(
            PARAM_INT,
            'Course id for the course to update',
            VALUE_REQUIRED
        );
        $pushratingstouserplans = new external_value(
            PARAM_BOOL,
            'New value of the setting',
            VALUE_REQUIRED
        );
        $settings = new external_single_structure(array(
            'pushratingstouserplans' => $pushratingstouserplans
        ));
        $params = array(
            'courseid' => $courseid,
            'settings' => $settings,
        );
        return new external_function_parameters($params);
    }

    /**
     * Update the course competency settings
     *
     * @param int $courseid the course id
     * @param stdClass $settings The list of settings (currently only pushratingstouserplans).
     * @throws moodle_exception
     */
    public static function update_course_competency_settings($courseid, $settings) {
        $params = self::validate_parameters(self::update_course_competency_settings_parameters(), array(
            'courseid' => $courseid,
            'settings' => $settings
        ));

        $context = context_course::instance($params['courseid']);
        self::validate_context($context);
        $result = api::update_course_competency_settings($params['courseid'], $params['settings']);

        return $result;
    }

    /**
     * Returns description of update_course_competency_settings() result value.
     *
     * @return \external_value
     */
    public static function update_course_competency_settings_returns() {
        return new external_value(PARAM_BOOL, 'True if the update was successful.');
    }

    /**
     * Returns description of external function parameters.
     *
     * @return \external_function_parameters
     */
    public static function delete_evidence_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'The evidence ID'),
        ));
    }

    /**
     * External function delete_evidence.
     *
     * @param int $id The evidence ID.
     * @return boolean
     */
    public static function delete_evidence($id) {
        $params = self::validate_parameters(self::delete_evidence_parameters(), array(
            'id' => $id
        ));

        $evidence = api::read_evidence($params['id']);
        $uc = api::get_user_competency_by_id($evidence->get('usercompetencyid'));
        self::validate_context($uc->get_context());

        return api::delete_evidence($evidence);
    }

    /**
     * Returns description of external function result value.
     *
     * @return \external_function_parameters
     */
    public static function delete_evidence_returns() {
        return new external_value(PARAM_BOOL, 'The success');
    }

}

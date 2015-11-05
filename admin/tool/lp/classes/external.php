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
use external_api;
use external_function_parameters;
use external_value;
use external_format_value;
use external_single_structure;
use external_multiple_structure;
use invalid_parameter_exception;
use grade_scale;

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
     * Returns the external structure of a full competency_framework record.
     *
     * @return \external_single_structure
     */
    protected static function get_competency_framework_external_structure() {
        $id = new external_value(
            PARAM_INT,
            'Database record id'
        );
        $shortname = new external_value(
            PARAM_TEXT,
            'Short name for the competency framework'
        );
        $idnumber = new external_value(
            PARAM_TEXT,
            'If provided, must be a unique string to identify this competency framework'
        );
        $description = new external_value(
            PARAM_RAW,
            'Description for the framework'
        );
        $descriptionformat = new external_format_value(
            'Description format for the framework'
        );
        $descriptionformatted = new external_value(
            PARAM_RAW,
            'Description that has been formatted for display'
        );
        $scaleid = new external_value(
            PARAM_INT,
            'Scale id associated to the framework.'
        );
        $scaleconfiguration = new external_value(
            PARAM_TEXT,
            'Scale configuration.'
        );
        $taxonomies = new external_value(
            PARAM_RAW,
            'The taxonomy terms'
        );
        $visible = new external_value(
            PARAM_BOOL,
            'Is this framework visible?'
        );
        $timecreated = new external_value(
            PARAM_INT,
            'Timestamp this record was created'
        );
        $timemodified = new external_value(
            PARAM_INT,
            'Timestamp this record was modified'
        );
        $usermodified = new external_value(
            PARAM_INT,
            'User who modified this record last'
        );

        $returns = array(
            'id' => $id,
            'shortname' => $shortname,
            'idnumber' => $idnumber,
            'description' => $description,
            'descriptionformat' => $descriptionformat,
            'descriptionformatted' => $descriptionformatted,
            'scaleid' => $scaleid,
            'scaleconfiguration' => $scaleconfiguration,
            'taxonomies' => $taxonomies,
            'visible' => $visible,
            'timecreated' => $timecreated,
            'timemodified' => $timemodified,
            'usermodified' => $usermodified,
        );
        return new external_single_structure($returns);
    }

    /**
     * Returns description of create_competency_framework() parameters.
     *
     * @return \external_function_parameters
     */
    public static function create_competency_framework_parameters() {
        $shortname = new external_value(
            PARAM_TEXT,
            'Short name for the competency framework.',
            VALUE_REQUIRED
        );
        $idnumber = new external_value(
            PARAM_TEXT,
            'If provided, must be a unique string to identify this competency framework.',
            VALUE_DEFAULT,
            ''
        );
        $description = new external_value(
            PARAM_RAW,
            'Optional description for the framework',
            VALUE_DEFAULT,
            ''
        );
        $descriptionformat = new external_format_value(
            'Optional description format for the framework',
            VALUE_DEFAULT,
            FORMAT_HTML
        );
        $scaleid = new external_value(
            PARAM_INT,
            'Scale id associated to the framework.',
            VALUE_REQUIRED
        );
        $scaleconfiguration = new external_value(
            PARAM_TEXT,
            'Scale configuration.',
            VALUE_REQUIRED
        );
        $visible = new external_value(
            PARAM_BOOL,
            'Is this framework visible?',
            VALUE_DEFAULT,
            true
        );

        $params = array(
            'shortname' => $shortname,
            'idnumber' => $idnumber,
            'description' => $description,
            'descriptionformat' => $descriptionformat,
            'scaleid' => $scaleid,
            'scaleconfiguration' => $scaleconfiguration,
            'visible' => $visible,
            'context' => self::get_context_parameters(),
        );
        return new external_function_parameters($params);
    }

    /**
     * Create a new competency framework
     *
     * @param string $shortname The short name
     * @param string $idnumber The idnumber
     * @param string $description The description
     * @param int $descriptionformat The description format
     * @param int $scaleid The scale id associated to the framework
     * @param string $scaleconfiguration The scale configuration
     * @param bool $visible Is this framework visible.
     * @param array $context
     * @return \stdClass The new record
     */
    public static function create_competency_framework($shortname, $idnumber, $description, $descriptionformat, $scaleid,
                                                       $scaleconfiguration, $visible, $context) {
        $params = self::validate_parameters(self::create_competency_framework_parameters(),
                                            array(
                                                'shortname' => $shortname,
                                                'idnumber' => $idnumber,
                                                'description' => $description,
                                                'descriptionformat' => $descriptionformat,
                                                'scaleid' => $scaleid,
                                                'scaleconfiguration' => $scaleconfiguration,
                                                'visible' => $visible,
                                                'context' => $context,
                                            ));

        $context = self::get_context_from_params($params['context']);
        self::validate_context($context);

        unset($params['context']);
        $params['contextid'] = $context->id;

        $params = (object) $params;
        $result = api::create_framework($params);
        $record = $result->to_record();
        $record->descriptionformatted = format_text($record->description, $record->descriptionformat,
            array('context' => context_system::instance()));
        return $record;
    }

    /**
     * Returns description of create_competency_framework() result value.
     *
     * @return \external_description
     */
    public static function create_competency_framework_returns() {
        return self::get_competency_framework_external_structure();
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
        $params = self::validate_parameters(self::read_competency_framework_parameters(),
                                            array(
                                                'id' => $id,
                                            ));

        $result = api::read_framework($params['id']);
        $record = $result->to_record();
        $record->descriptionformatted = format_text($record->description, $record->descriptionformat,
            array('context' => context_system::instance()));
        return $record;
    }

    /**
     * Returns description of read_competency_framework() result value.
     *
     * @return \external_description
     */
    public static function read_competency_framework_returns() {
        return self::get_competency_framework_external_structure();
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

        return api::duplicate_framework($params['id']);
    }

    /**
     * Returns description of duplicate_competency_framework() result value.
     *
     * @return \external_description
     */
    public static function duplicate_competency_framework_returns() {
        return self::get_competency_framework_external_structure();
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
        $id = new external_value(
            PARAM_INT,
            'Data base record id for the framework',
            VALUE_REQUIRED
        );
        $shortname = new external_value(
            PARAM_TEXT,
            'Short name for the competency framework.',
            VALUE_REQUIRED
        );
        $idnumber = new external_value(
            PARAM_TEXT,
            'If provided, must be a unique string to identify this competency framework.',
            VALUE_REQUIRED
        );
        $description = new external_value(
            PARAM_RAW,
            'Description for the framework',
            VALUE_REQUIRED
        );
        $descriptionformat = new external_format_value(
            'Description format for the framework',
            VALUE_REQUIRED
        );
        $scaleid = new external_value(
            PARAM_INT,
            'Scale id associated to the framework.',
            VALUE_REQUIRED
        );
        $scaleconfiguration = new external_value(
            PARAM_TEXT,
            'Scale configuration.',
            VALUE_REQUIRED
        );
        $visible = new external_value(
            PARAM_BOOL,
            'Is this framework visible?',
            VALUE_REQUIRED
        );

        $params = array(
            'id' => $id,
            'shortname' => $shortname,
            'idnumber' => $idnumber,
            'description' => $description,
            'descriptionformat' => $descriptionformat,
            'scaleid' => $scaleid,
            'scaleconfiguration' => $scaleconfiguration,
            'visible' => $visible,
        );
        return new external_function_parameters($params);
    }

    /**
     * Update an existing competency framework
     *
     * @param int $id The competency framework id
     * @param string $shortname
     * @param string $idnumber
     * @param string $description
     * @param int $descriptionformat
     * @param int $scaleid The scale id associated to the framework
     * @param string $scaleconfiguration The scale configuration
     * @param boolean $visible
     * @return boolean
     */
    public static function update_competency_framework($id,
                                                       $shortname,
                                                       $idnumber,
                                                       $description,
                                                       $descriptionformat,
                                                       $scaleid,
                                                       $scaleconfiguration,
                                                       $visible) {

        $params = self::validate_parameters(self::update_competency_framework_parameters(),
                                            array(
                                                'id' => $id,
                                                'shortname' => $shortname,
                                                'idnumber' => $idnumber,
                                                'description' => $description,
                                                'descriptionformat' => $descriptionformat,
                                                'scaleid' => $scaleid,
                                                'scaleconfiguration' => $scaleconfiguration,
                                                'visible' => $visible
                                            ));

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
            $record = $result->to_record();
            $record->descriptionformatted = format_text($record->description, $record->descriptionformat,
                array('context' => context_system::instance()));
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
        return new external_multiple_structure(self::get_competency_framework_external_structure());
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
                self::get_competency_framework_external_structure()
            ),
            'pluginbaseurl' => new external_value(PARAM_LOCALURL, 'Url to the tool_lp plugin folder on this Moodle site'),
            'navigation' => new external_multiple_structure(
                new external_value(PARAM_RAW, 'HTML for a navigation item that should be on this page')
            )
        ));

    }

    /**
     * Returns the external structure of a full competency record.
     *
     * @param bool $includerelated Useful to avoid recursive structures.
     * @return \external_single_structure
     */
    protected static function get_competency_external_structure($includerelated = false) {
        $id = new external_value(
            PARAM_INT,
            'Database record id'
        );
        $shortname = new external_value(
            PARAM_TEXT,
            'Short name for the competency'
        );
        $idnumber = new external_value(
            PARAM_TEXT,
            'If provided, must be a unique string to identify this competency'
        );
        $description = new external_value(
            PARAM_RAW,
            'Description for the competency'
        );
        $descriptionformat = new external_format_value(
            'Description format for the competency'
        );
        $descriptionformatted = new external_value(
            PARAM_RAW,
            'Description formatted for display'
        );
        $visible = new external_value(
            PARAM_BOOL,
            'Is this competency visible?'
        );
        $sortorder = new external_value(
            PARAM_INT,
            'Relative sort order of this competency'
        );
        $competencyframeworkid = new external_value(
            PARAM_INT,
            'Competency framework id that this competency belongs to'
        );
        $parentid = new external_value(
            PARAM_INT,
            'Parent competency id. 0 means top level node.'
        );
        $timecreated = new external_value(
            PARAM_INT,
            'Timestamp this record was created'
        );
        $timemodified = new external_value(
            PARAM_INT,
            'Timestamp this record was modified'
        );
        $usermodified = new external_value(
            PARAM_INT,
            'User who modified this record last'
        );
        $parentid = new external_value(
            PARAM_INT,
            'The id of the parent competency.'
        );
        $path = new external_value(
            PARAM_RAW,
            'The path of parents all the way to the root of the tree.'
        );

        $returns = array(
            'id' => $id,
            'shortname' => $shortname,
            'idnumber' => $idnumber,
            'description' => $description,
            'descriptionformat' => $descriptionformat,
            'descriptionformatted' => $descriptionformatted,
            'visible' => $visible,
            'sortorder' => $sortorder,
            'timecreated' => $timecreated,
            'timemodified' => $timemodified,
            'usermodified' => $usermodified,
            'parentid' => $parentid,
            'competencyframeworkid' => $competencyframeworkid,
            'path' => $path,
        );

        if ($includerelated) {
            $returns['relatedcompetencies'] = new external_multiple_structure(
                self::get_competency_external_structure(false),
                'Related competencies',
                VALUE_OPTIONAL
            );
        }

        return new external_single_structure($returns);
    }

    /**
     * Returns the external structure of a full user_competency record.
     *
     * @param int $fordisplay When true, additional fields for display purposes will be added.
     * @param int $required If the stucture is required.
     * @return \external_single_structure
     */
    protected static function get_user_competency_external_structure($fordisplay = false, $required = VALUE_REQUIRED) {
        $id = new external_value(
            PARAM_INT,
            'Database record id'
        );
        $userid = new external_value(
            PARAM_INT,
            'User to whom this record belongs to'
        );
        $competencyid = new external_value(
            PARAM_INT,
            'The competency associated with this record'
        );
        $status = new external_value(
            PARAM_INT,
            'The status of the user competency'
        );
        $reviewerid = new external_value(
            PARAM_INT,
            'The reviewer ID'
        );
        $proficiency = new external_value(
            PARAM_BOOL,
            'Whether or not the user is proficient'
        );
        $grade = new external_value(
            PARAM_INT,
            'The scale grade'
        );
        $timecreated = new external_value(
            PARAM_INT,
            'Timestamp this record was created'
        );
        $timemodified = new external_value(
            PARAM_INT,
            'Timestamp this record was modified'
        );
        $usermodified = new external_value(
            PARAM_INT,
            'User who modified this record last'
        );

        $returns = array(
            'id' => $id,
            'userid' => $userid,
            'competencyid' => $competencyid,
            'status' => $status,
            'reviewerid' => $reviewerid,
            'proficiency' => $proficiency,
            'grade' => $grade,
            'timecreated' => $timecreated,
            'timemodified' => $timemodified,
            'usermodified' => $usermodified,
        );

        if ($fordisplay) {
            $gradename = new external_value(
                PARAM_TEXT,
                'User competency status name'
            );
            $proficiencyname = new external_value(
                PARAM_TEXT,
                'User competency proficiency name'
            );
            $statusname = new external_value(
                PARAM_TEXT,
                'User competency status name'
            );
            $returns['gradename'] = $gradename;
            $returns['proficiencyname'] = $proficiencyname;
            $returns['statusname'] = $statusname;
        }

        return new external_single_structure($returns, '', $required);
    }

    /**
     * Returns the external structure of a full user_competency_plan record.
     *
     * @param int $fordisplay When true, additional fields for display purposes will be added.
     * @param int $required If the stucture is required.
     * @return \external_single_structure
     */
    protected static function get_user_competency_plan_external_structure($fordisplay = false, $required = VALUE_REQUIRED) {
        $id = new external_value(
            PARAM_INT,
            'Database record id'
        );
        $userid = new external_value(
            PARAM_INT,
            'User to whom this record belongs to'
        );
        $competencyid = new external_value(
            PARAM_INT,
            'The competency associated with this record'
        );
        $proficiency = new external_value(
            PARAM_BOOL,
            'Whether or not the user is proficient'
        );
        $grade = new external_value(
            PARAM_INT,
            'The scale grade'
        );
        $planid = new external_value(
            PARAM_INT,
            'The plan id'
        );
        $timecreated = new external_value(
            PARAM_INT,
            'Timestamp this record was created'
        );
        $timemodified = new external_value(
            PARAM_INT,
            'Timestamp this record was modified'
        );
        $usermodified = new external_value(
            PARAM_INT,
            'User who modified this record last'
        );

        $returns = array(
            'id' => $id,
            'userid' => $userid,
            'competencyid' => $competencyid,
            'proficiency' => $proficiency,
            'grade' => $grade,
            'planid' => $planid,
            'timecreated' => $timecreated,
            'timemodified' => $timemodified,
            'usermodified' => $usermodified,
        );

        if ($fordisplay) {
            $gradename = new external_value(
                PARAM_TEXT,
                'User competency status name'
            );
            $proficiencyname = new external_value(
                PARAM_TEXT,
                'User competency proficiency name'
            );
            $returns['gradename'] = $gradename;
            $returns['proficiencyname'] = $proficiencyname;
        }

        return new external_single_structure($returns, '', $required);
    }

    /**
     * Returns description of create_competency() parameters.
     *
     * @return \external_function_parameters
     */
    public static function create_competency_parameters() {
        $shortname = new external_value(
            PARAM_TEXT,
            'Short name for the competency framework.',
            VALUE_REQUIRED
        );
        $idnumber = new external_value(
            PARAM_TEXT,
            'If provided, must be a unique string to identify this competency framework.',
            VALUE_DEFAULT,
            ''
        );
        $description = new external_value(
            PARAM_RAW,
            'Optional description for the framework',
            VALUE_DEFAULT,
            ''
        );
        $descriptionformat = new external_format_value(
            'Optional description format for the framework',
            VALUE_DEFAULT,
            FORMAT_HTML
        );
        $visible = new external_value(
            PARAM_BOOL,
            'Is this competency visible?',
            VALUE_DEFAULT,
            true
        );
        $competencyframeworkid = new external_value(
            PARAM_INT,
            'Which competency framework does this competency belong to?'
        );
        $parentid = new external_value(
            PARAM_INT,
            'The parent competency. 0 means this is a top level competency.'
        );

        $params = array(
            'shortname' => $shortname,
            'idnumber' => $idnumber,
            'description' => $description,
            'descriptionformat' => $descriptionformat,
            'visible' => $visible,
            'competencyframeworkid' => $competencyframeworkid,
            'parentid' => $parentid,
        );
        return new external_function_parameters($params);
    }

    /**
     * Create a new competency framework
     *
     * @param string $shortname
     * @param string $idnumber
     * @param string $description
     * @param int $descriptionformat
     * @param bool $visible
     * @param int $competencyframeworkid
     * @param int $parentid
     * @return string the template
     */
    public static function create_competency($shortname,
                                             $idnumber,
                                             $description,
                                             $descriptionformat,
                                             $visible,
                                             $competencyframeworkid,
                                             $parentid) {
        $params = self::validate_parameters(self::create_competency_parameters(),
                                            array(
                                                'shortname' => $shortname,
                                                'idnumber' => $idnumber,
                                                'description' => $description,
                                                'descriptionformat' => $descriptionformat,
                                                'visible' => $visible,
                                                'competencyframeworkid' => $competencyframeworkid,
                                                'parentid' => $parentid,
                                            ));

        $params = (object) $params;

        $result = api::create_competency($params);
        $record = $result->to_record();
        $record->descriptionformatted = format_text($record->description, $record->descriptionformat,
            array('context' => context_system::instance()));
        return $record;
    }

    /**
     * Returns description of create_competency() result value.
     *
     * @return \external_description
     */
    public static function create_competency_returns() {
        return self::get_competency_external_structure();
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
        $params = self::validate_parameters(self::read_competency_parameters(),
                                            array(
                                                'id' => $id,
                                            ));

        $result = api::read_competency($params['id']);
        $record = $result->to_record();
        $options = array('context' => context_system::instance());
        $record->descriptionformatted = format_text($record->description, $record->descriptionformat, $options);
        return $record;
    }

    /**
     * Returns description of read_competency() result value.
     *
     * @return \external_description
     */
    public static function read_competency_returns() {
        return self::get_competency_external_structure();
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
        $id = new external_value(
            PARAM_INT,
            'Data base record id for the competency',
            VALUE_REQUIRED
        );
        $shortname = new external_value(
            PARAM_TEXT,
            'Short name for the competency.',
            VALUE_REQUIRED
        );
        $idnumber = new external_value(
            PARAM_TEXT,
            'If provided, must be a unique string to identify this competency.',
            VALUE_REQUIRED
        );
        $description = new external_value(
            PARAM_RAW,
            'Description for the framework',
            VALUE_REQUIRED
        );
        $descriptionformat = new external_format_value(
            'Description format for the framework',
            VALUE_REQUIRED
        );
        $visible = new external_value(
            PARAM_BOOL,
            'Is this framework visible?',
            VALUE_REQUIRED
        );
        $ruletype = new external_value(
            PARAM_RAW,
            'The type of rule',
            VALUE_DEFAULT,
            null
        );
        $ruleoutcome = new external_value(
            PARAM_INT,
            'The outcome when the rule matches. Constant value competency::OUTCOME_*',
            VALUE_DEFAULT,
            competency::OUTCOME_NONE
        );
        $ruleconfig = new external_value(
            PARAM_RAW,
            'The extra config of the rule.',
            VALUE_DEFAULT,
            null
        );

        $params = array(
            'id' => $id,
            'shortname' => $shortname,
            'idnumber' => $idnumber,
            'description' => $description,
            'descriptionformat' => $descriptionformat,
            'visible' => $visible,
            'ruletype' => $ruletype,
            'ruleoutcome' => $ruleoutcome,
            'ruleconfig' => $ruleconfig,
        );
        return new external_function_parameters($params);
    }

    /**
     * Update an existing competency
     *
     * @param int $id The competency id
     * @param string $shortname
     * @param string $idnumber
     * @param string $description
     * @param int $descriptionformat
     * @param boolean $visible
     * @return boolean
     */
    public static function update_competency($id,
                                             $shortname,
                                             $idnumber,
                                             $description,
                                             $descriptionformat,
                                             $visible,
                                             $ruletype = null,
                                             $ruleoutcome = competency::OUTCOME_NONE,
                                             $ruleconfig = null) {

        $params = self::validate_parameters(self::update_competency_parameters(),
                                            array(
                                                'id' => $id,
                                                'shortname' => $shortname,
                                                'idnumber' => $idnumber,
                                                'description' => $description,
                                                'descriptionformat' => $descriptionformat,
                                                'visible' => $visible,
                                                'ruletype' => $ruletype,
                                                'ruleoutcome' => $ruleoutcome,
                                                'ruleconfig' => $ruleconfig,
                                            ));
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

        $results = api::list_competencies($safefilters,
                                                     $params['sort'],
                                                     $params['order'],
                                                     $params['skip'],
                                                     $params['limit']);
        $records = array();
        $options = array('context' => context_system::instance());
        foreach ($results as $result) {
            $record = $result->to_record();
            $record->descriptionformatted = format_text($record->description, $record->descriptionformat, $options);
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
        return new external_multiple_structure(self::get_competency_external_structure());
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
        $includerelated = new external_value(
            PARAM_BOOL,
            'Include or not related competencies',
            VALUE_DEFAULT,
            false
        );

        $params = array(
            'searchtext' => $searchtext,
            'competencyframeworkid' => $frameworkid,
            'includerelated' => $includerelated
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
    public static function search_competencies($searchtext, $competencyframeworkid, $includerelated = false) {
        $params = self::validate_parameters(self::search_competencies_parameters(),
                                            array(
                                                'searchtext' => $searchtext,
                                                'competencyframeworkid' => $competencyframeworkid,
                                                'includerelated' => $includerelated
                                            ));

        $results = api::search_competencies($params['searchtext'], $params['competencyframeworkid']);
        $options = array('context' => context_system::instance());
        $records = array();
        foreach ($results as $result) {
            $record = $result->to_record();

            if ($params['includerelated']) {
                $record->relatedcompetencies = array();
                $relatedcomps = $result->get_related_competencies();
                foreach ($relatedcomps as $comp) {
                    $comprecord = $comp->to_record();
                    // TODO Format using the context from the framework.
                    $comprecord->descriptionformatted = format_text($comprecord->description,
                        $comprecord->descriptionformat, $options);
                    $record->relatedcompetencies[] = $comprecord;
                }
            }

            // TODO Format using the context from the framework.
            $record->descriptionformatted = format_text($record->description, $record->descriptionformat, $options);
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
        return new external_multiple_structure(self::get_competency_external_structure(true));
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

        $framework = new competency_framework($params['competencyframeworkid']);

        $renderable = new output\manage_competencies_page($framework, $params['search'], $framework->get_context());
        $renderer = $PAGE->get_renderer('tool_lp');

        $data = $renderable->export_for_template($renderer);

        return $data;
    }

    /**
     * Returns description of data_for_competencies_manage_page() result value.
     *
     * @return \external_description
     */
    public static function data_for_competencies_manage_page_returns() {
        return new external_single_structure(array (
            'framework' => self::get_competency_framework_external_structure(),
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

        return api::set_parent_competency($competencyid, $parentid);
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
        $params = self::validate_parameters(self::list_courses_using_competency_parameters(),
                                            array(
                                                'id' => $competencyid,
                                            ));

        return api::list_courses_using_competency($params['id']);
    }

    /**
     * Returns description of list_courses_using_competency() result value.
     *
     * @return \external_description
     */
    public static function list_courses_using_competency_returns() {
        $id = new external_value(
            PARAM_INT,
            'Course id'
        );
        $visible = new external_value(
            PARAM_BOOL,
            'Is the course visible.'
        );
        $idnumber = new external_value(
            PARAM_TEXT,
            'Course id number'
        );
        $shortname = new external_value(
            PARAM_TEXT,
            'Course short name'
        );
        $shortnameformatted = new external_value(
            PARAM_RAW,
            'Shortname that has been formatted for display'
        );
        $fullname = new external_value(
            PARAM_TEXT,
            'Course fullname'
        );
        $fullnameformatted = new external_value(
            PARAM_RAW,
            'Fullname that has been formatted for display'
        );

        $returns = array(
            'id' => $id,
            'shortname' => $shortname,
            'shortnameformatted' => $shortnameformatted,
            'idnumber' => $idnumber,
            'fullname' => $fullname,
            'fullnameformatted' => $fullnameformatted,
            'visible' => $visible
        );
        return new external_multiple_structure(new external_single_structure($returns));
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
        $params = self::validate_parameters(self::list_competencies_in_course_parameters(),
                                            array(
                                                'id' => $courseid,
                                            ));

        $competencies = api::list_competencies_in_course($params['id']);
        $options = array('context' => context_system::instance());
        $results = array();
        foreach ($competencies as $competency) {
            $record = $competency->to_record();
            $record->descriptionformatted = format_text($record->description, $record->descriptionformat, $options);
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
        return new external_multiple_structure(self::get_competency_external_structure());
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
                self::get_competency_external_structure()
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
     * Returns the external structure of a full template record.
     *
     * @return \external_single_structure
     */
    protected static function get_template_external_structure() {
        $id = new external_value(
            PARAM_INT,
            'Database record id'
        );
        $shortname = new external_value(
            PARAM_TEXT,
            'Short name for the learning plan template'
        );
        $duedate = new external_value(
            PARAM_INT,
            'The default due date for instances of this plan.'
        );
        $duedateformatted = new external_value(
            PARAM_RAW,
            'Due date that has been formatted for display'
        );
        $description = new external_value(
            PARAM_RAW,
            'Description for the template'
        );
        $descriptionformat = new external_format_value(
            'Description format for the template'
        );
        $descriptionformatted = new external_value(
            PARAM_RAW,
            'Description that has been formatted for display'
        );
        $visible = new external_value(
            PARAM_BOOL,
            'Is this template visible?'
        );
        $timecreated = new external_value(
            PARAM_INT,
            'Timestamp this record was created'
        );
        $timemodified = new external_value(
            PARAM_INT,
            'Timestamp this record was modified'
        );
        $usermodified = new external_value(
            PARAM_INT,
            'User who modified this record last'
        );
        $contextid = new external_value(
            PARAM_INT,
            'The context ID the template belongs to'
        );

        $returns = array(
            'id' => $id,
            'shortname' => $shortname,
            'duedate' => $duedate,
            'duedateformatted' => $duedateformatted,
            'description' => $description,
            'descriptionformat' => $descriptionformat,
            'descriptionformatted' => $descriptionformatted,
            'visible' => $visible,
            'timecreated' => $timecreated,
            'timemodified' => $timemodified,
            'usermodified' => $usermodified,
            'contextid' => $contextid,
        );
        return new external_single_structure($returns);
    }

    /**
     * Returns description of create_template() parameters.
     *
     * @return \external_function_parameters
     */
    public static function create_template_parameters() {
        $shortname = new external_value(
            PARAM_TEXT,
            'Short name for the learning plan template.',
            VALUE_REQUIRED
        );
        $duedate = new external_value(
            PARAM_INT,
            'The default due date for instances of this plan',
            VALUE_DEFAULT,
            0
        );
        $description = new external_value(
            PARAM_RAW,
            'Optional description for the learning plan template',
            VALUE_DEFAULT,
            ''
        );
        $descriptionformat = new external_format_value(
            'Optional description format for the learning plan template',
            VALUE_DEFAULT,
            FORMAT_HTML
        );
        $visible = new external_value(
            PARAM_BOOL,
            'Is this learning plan template visible?',
            VALUE_DEFAULT,
            true
        );

        $params = array(
            'shortname' => $shortname,
            'duedate' => $duedate,
            'description' => $description,
            'descriptionformat' => $descriptionformat,
            'visible' => $visible,
            'context' => self::get_context_parameters()
        );
        return new external_function_parameters($params);
    }

    /**
     * Create a new learning plan template
     *
     * @param string $shortname The short name of the template.
     * @param string $idnumber The idnumber of the template.
     * @param int $duedate The due date for instances of this plan.
     * @param string $description The description of the template.
     * @param int $descriptionformat The format of the description
     * @param bool $visible Is this template visible.
     * @param array $context The context info.
     * @return \stdClass Record of new template.
     */
    public static function create_template($shortname, $duedate, $description, $descriptionformat, $visible, $context) {
        $params = self::validate_parameters(self::create_template_parameters(),
                                            array(
                                                'shortname' => $shortname,
                                                'duedate' => $duedate,
                                                'description' => $description,
                                                'descriptionformat' => $descriptionformat,
                                                'visible' => $visible,
                                                'context' => $context
                                            ));
        $context = self::get_context_from_params($params['context']);
        self::validate_context($context);

        unset($params['context']);
        $params = (object) $params;
        $params->contextid = $context->id;

        $result = api::create_template($params);
        $record = $result->to_record();
        $record->descriptionformatted = format_text($record->description, $record->descriptionformat,
            array('context' => context_system::instance()));
        $record->duedateformatted = userdate($record->duedate);
        return $record;
    }

    /**
     * Returns description of create_template() result value.
     *
     * @return \external_description
     */
    public static function create_template_returns() {
        return self::get_template_external_structure();
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
        $params = self::validate_parameters(self::read_template_parameters(),
                                            array(
                                                'id' => $id,
                                            ));

        $result = api::read_template($params['id']);
        $record = $result->to_record();
        $record->descriptionformatted = format_text($record->description, $record->descriptionformat,
            array('context' => context_system::instance()));
        $record->duedateformatted = userdate($record->duedate);
        return $record;
    }

    /**
     * Returns description of read_template() result value.
     *
     * @return \external_description
     */
    public static function read_template_returns() {
        return self::get_template_external_structure();
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
        $id = new external_value(
            PARAM_INT,
            'Data base record id for the template',
            VALUE_REQUIRED
        );
        $shortname = new external_value(
            PARAM_TEXT,
            'Short name for the learning plan template.',
            VALUE_REQUIRED
        );
        $duedate = new external_value(
            PARAM_INT,
            'Default due date for instances of this plan',
            VALUE_REQUIRED
        );
        $description = new external_value(
            PARAM_RAW,
            'Description for the template',
            VALUE_REQUIRED
        );
        $descriptionformat = new external_format_value(
            'Description format for the template',
            VALUE_REQUIRED
        );
        $visible = new external_value(
            PARAM_BOOL,
            'Is this template visible?',
            VALUE_REQUIRED
        );

        $params = array(
            'id' => $id,
            'shortname' => $shortname,
            'duedate' => $duedate,
            'description' => $description,
            'descriptionformat' => $descriptionformat,
            'visible' => $visible,
        );
        return new external_function_parameters($params);
    }

    /**
     * Update an existing learning plan template
     *
     * @param int $id The learning plan template id
     * @param string $shortname
     * @param string $idnumber
     * @param int $duedate
     * @param string $description
     * @param int $descriptionformat
     * @param boolean $visible
     * @return boolean
     */
    public static function update_template($id,
                                                       $shortname,
                                                       $duedate,
                                                       $description,
                                                       $descriptionformat,
                                                       $visible) {

        $params = self::validate_parameters(self::update_template_parameters(),
                                            array(
                                                'id' => $id,
                                                'shortname' => $shortname,
                                                'duedate' => $duedate,
                                                'description' => $description,
                                                'descriptionformat' => $descriptionformat,
                                                'visible' => $visible
                                            ));
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
        $params = self::validate_parameters(self::duplicate_template_parameters(),
                                            array(
                                                'id' => $id,
                                            ));

        $result = api::duplicate_template($params['id']);
        return $result->to_record();
    }

    /**
     * Returns description of duplicate_template() result value.
     *
     * @return \external_description
     */
    public static function duplicate_template_returns() {
        return self::get_template_external_structure();
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
            $record = $result->to_record();
            $record->descriptionformatted = format_text($record->description, $record->descriptionformat,
                array('context' => context_system::instance()));
            $record->duedateformatted = userdate($record->duedate);
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
        return new external_multiple_structure(self::get_template_external_structure());
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
                self::get_template_external_structure()
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

        return api::list_templates_using_competency($params['id']);
    }

    /**
     * Returns description of list_templates_using_competency() result value.
     *
     * @return \external_description
     */
    public static function list_templates_using_competency_returns() {
        return new external_multiple_structure(self::get_template_external_structure());
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

        $competencies = api::list_competencies_in_template($params['id']);
        $options = array('context' => context_system::instance());
        $results = array();
        foreach ($competencies as $competency) {
            $record = $competency->to_record();
            $record->descriptionformatted = format_text($record->description, $record->descriptionformat, $options);
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
        return new external_multiple_structure(self::get_competency_external_structure());
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
                self::get_competency_external_structure()
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
        $plan = api::read_plan($planid);
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
                    'competency' => self::get_competency_external_structure(),
                    'usercompetency' => self::get_user_competency_external_structure(true, VALUE_OPTIONAL),
                    'usercompetencyplan' => self::get_user_competency_plan_external_structure(true, VALUE_OPTIONAL)
                ))
            )
        ));
    }

    /**
     * A learning plan structure.
     *
     * @return \external_single_structure
     */
    protected static function get_plan_external_structure() {
        $id = new external_value(
            PARAM_INT,
            'Database record id'
        );
        $name = new external_value(
            PARAM_TEXT,
            'Name for the learning plan'
        );
        $description = new external_value(
            PARAM_RAW,
            'Description for the template'
        );
        $descriptionformat = new external_format_value(
            'Description format for the template'
        );
        $userid = new external_value(
            PARAM_INT,
            'Learning plan user id'
        );
        $templateid = new external_value(
            PARAM_INT,
            'Learning plan templateid'
        );
        $status = new external_value(
            PARAM_INT,
            'Learning plan status identifier.'
        );
        $duedate = new external_value(
            PARAM_INT,
            'The default due date for instances of this plan.'
        );
        $timecreated = new external_value(
            PARAM_INT,
            'Timestamp this record was created'
        );
        $timemodified = new external_value(
            PARAM_INT,
            'Timestamp this record was modified'
        );
        $usermodified = new external_value(
            PARAM_INT,
            'User who modified this record last'
        );

        // Extra params.
        $statusname = new external_value(
            PARAM_TEXT,
            'Learning plan status name'
        );
        $usercanupdate = new external_value(
            PARAM_BOOL,
            'Whether the current user can update this plan or not'
        );

        $returns = array(
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'descriptionformat' => $descriptionformat,
            'userid' => $userid,
            'templateid' => $templateid,
            'status' => $status,
            'duedate' => $duedate,
            'timecreated' => $timecreated,
            'timemodified' => $timemodified,
            'usermodified' => $usermodified,
            'statusname' => $statusname,
            'usercanupdate' => $usercanupdate
        );

        return new external_single_structure($returns);
    }

    /**
     * Returns description of create_plan() parameters.
     *
     * @return \external_function_parameters
     */
    public static function create_plan_parameters() {
        $name = new external_value(
            PARAM_TEXT,
            'Name for the learning plan template.',
            VALUE_REQUIRED
        );
        $description = new external_value(
            PARAM_RAW,
            'Optional description for the learning plan description',
            VALUE_DEFAULT,
            ''
        );
        $descriptionformat = new external_format_value(
            'Optional description format for the learning plan description',
            VALUE_DEFAULT,
            FORMAT_HTML
        );
        $userid = new external_value(
            PARAM_INT,
            'The learning plan user id',
            VALUE_REQUIRED
        );
        $templateid = new external_value(
            PARAM_INT,
            'Optional template id',
            VALUE_DEFAULT,
            0
        );
        $status = new external_value(
            PARAM_INT,
            'Optional template id',
            VALUE_DEFAULT,
            plan::STATUS_DRAFT
        );
        $duedate = new external_value(
            PARAM_INT,
            'The default due date for this plan',
            VALUE_DEFAULT,
            0
        );

        $params = array(
            'name' => $name,
            'description' => $description,
            'descriptionformat' => $descriptionformat,
            'userid' => $userid,
            'templateid' => $templateid,
            'status' => $status,
            'duedate' => $duedate
        );
        return new external_function_parameters($params);
    }

    /**
     * Create a new learning plan.
     *
     * @param string $name Name.
     * @param string $description Plan description.
     * @param string $descriptionformat Plan description format.
     * @param int $userid User id.
     * @param int $templateid Related template id.
     * @param int $status status.
     * @param int $duedate due date.
     *
     * @return mixed
     */
    public static function create_plan($name, $description, $descriptionformat, $userid, $templateid, $status, $duedate) {
        $params = self::validate_parameters(self::create_plan_parameters(),
                                            array(
                                                'name' => $name,
                                                'description' => $description,
                                                'descriptionformat' => $descriptionformat,
                                                'userid' => $userid,
                                                'templateid' => $templateid,
                                                'status' => $status,
                                                'duedate' => $duedate
                                            ));
        $params = (object) $params;

        $result = api::create_plan($params);
        $record = $result->to_record();
        $record->statusname = $result->get_statusname();
        $record->usercanupdate = $result->can_manage();
        return external_api::clean_returnvalue(self::create_plan_returns(), $record);
    }

    /**
     * Returns description of create_plan() result value.
     *
     * @return \external_description
     */
    public static function create_plan_returns() {
        return self::get_plan_external_structure();
    }

    /**
     * Returns description of update_plan() parameters.
     *
     * @return \external_function_parameters
     */
    public static function update_plan_parameters() {
        $id = new external_value(
            PARAM_INT,
            'Learning plan id',
            VALUE_REQUIRED
        );
        $name = new external_value(
            PARAM_TEXT,
            'Name for the learning plan template.',
            VALUE_REQUIRED
        );
        $description = new external_value(
            PARAM_RAW,
            'Optional description for the learning plan description',
            VALUE_DEFAULT,
            ''
        );
        $descriptionformat = new external_format_value(
            'Optional description format for the learning plan description',
            VALUE_DEFAULT,
            FORMAT_HTML
        );
        $userid = new external_value(
            PARAM_INT,
            'The learning plan user id',
            VALUE_REQUIRED
        );
        $templateid = new external_value(
            PARAM_INT,
            'Optional template id',
            VALUE_DEFAULT,
            0
        );
        $status = new external_value(
            PARAM_INT,
            'Optional template id',
            VALUE_DEFAULT,
            plan::STATUS_DRAFT
        );
        $duedate = new external_value(
            PARAM_INT,
            'The default due date for this plan',
            VALUE_DEFAULT,
            0
        );

        $params = array(
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'descriptionformat' => $descriptionformat,
            'userid' => $userid,
            'templateid' => $templateid,
            'status' => $status,
            'duedate' => $duedate
        );
        return new external_function_parameters($params);
    }

    /**
     * Updates a new learning plan.
     *
     * @param int $id Plan id.
     * @param string $name Name.
     * @param string $description Plan description.
     * @param string $descriptionformat Plan description format.
     * @param int $userid User id.
     * @param int $templateid Related template id.
     * @param int $status status.
     * @param int $duedate due date.
     *
     * @return mixed
     */
    public static function update_plan($id, $name, $description, $descriptionformat, $userid, $templateid, $status, $duedate) {
        $params = self::validate_parameters(self::update_plan_parameters(),
                                            array(
                                                'id' => $id,
                                                'name' => $name,
                                                'description' => $description,
                                                'descriptionformat' => $descriptionformat,
                                                'userid' => $userid,
                                                'templateid' => $templateid,
                                                'status' => $status,
                                                'duedate' => $duedate
                                            ));
        $params = (object) $params;

        $result = api::update_plan($params);
        $record = $result->to_record();
        $record->statusname = $result->get_statusname();
        $record->usercanupdate = $result->can_manage();
        return external_api::clean_returnvalue(self::update_plan_returns(), $record);
    }

    /**
     * Returns description of update_plan() result value.
     *
     * @return \external_description
     */
    public static function update_plan_returns() {
        return self::get_plan_external_structure();
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
        $params = self::validate_parameters(self::read_plan_parameters(),
                                            array(
                                                'id' => $id,
                                            ));

        $result = api::read_plan($params['id']);
        $record = $result->to_record();
        $record->statusname = $result->get_statusname();
        $record->usercanupdate = $result->can_manage();
        return external_api::clean_returnvalue(self::read_plan_returns(), $record);
    }

    /**
     * Returns description of read_plan() result value.
     *
     * @return \external_description
     */
    public static function read_plan_returns() {
        return self::get_plan_external_structure();
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

        $renderable = new \tool_lp\output\plans_page($params['userid']);
        $renderer = $PAGE->get_renderer('tool_lp');

        return external_api::clean_returnvalue(self::data_for_plans_page_returns(), $renderable->export_for_template($renderer));
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
                self::get_plan_external_structure()
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
        return new external_single_structure(array(
            'id' => new external_value(PARAM_INT, 'The plan ID.')
        ));
    }

    /**
     * List plan competencies.
     * @param  int $id The plan ID.
     * @return array
     */
    public static function list_plan_competencies($id) {
        $params = self::validate_parameters(self::list_plan_competencies_parameters(), array('id' => $id));
        $id = $params['id'];
        $plan = api::read_plan($id);
        $result = api::list_plan_competencies($plan);

        if ($plan->get_status() == plan::STATUS_COMPLETE) {
            $ucproperty = 'usercompetencyplan';
        } else {
            $ucproperty = 'usercompetency';
        }

        foreach ($result as $key => $r) {
            $r->competency = $r->competency->to_record();
            $r->competency->descriptionformatted = format_text($r->competency->description,
                $r->competency->descriptionformat, array('context' => $plan->get_context()));

            $ucrecord = $r->$ucproperty->to_record();
            unset($r->usercompetency);
            unset($r->usercompetencyplan);
            $r->$ucproperty = $ucrecord;
        }
        return $result;
    }

    /**
     * External function return structure.
     *
     * @return \external_description
     */
    public static function list_plan_competencies_returns() {
        return new external_multiple_structure(
            new external_single_structure(array(
                'competency' => self::get_competency_external_structure(),
                'usercompetency' => self::get_user_competency_external_structure(false, VALUE_OPTIONAL),
                'usercompetencyplan' => self::get_user_competency_plan_external_structure(false, VALUE_OPTIONAL),
            )
        ));
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
        // The following section is not learning plan specific and so has not been moved to the api.
        // Retrieve the scale value from the database.
        $scale = grade_scale::fetch(array('id' => $scaleid));
        // Reverse the array so that high levels are at the top.
        $scalevalues = array_reverse($scale->load_items());
        foreach ($scalevalues as $key => $value) {
            // Add a key (make the first value 1).
            $scalevalues[$key] = array(
                    'id' => $key + 1,
                    'name' => $value
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
            'relatedcompetencies' => new external_multiple_structure(self::get_competency_external_structure(true)),
            'showdeleterelatedaction' => new external_value(PARAM_BOOL, 'Whether to show the delete relation link or not')
        ));
    }
}

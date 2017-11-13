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
 * External cohort API
 *
 * @package    core_cohort
 * @category   external
 * @copyright  MediaTouch 2000 srl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

class core_cohort_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function create_cohorts_parameters() {
        return new external_function_parameters(
            array(
                'cohorts' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'categorytype' => new external_single_structure(
                                array(
                                    'type' => new external_value(PARAM_TEXT, 'the name of the field: id (numeric value
                                        of course category id) or idnumber (alphanumeric value of idnumber course category)
                                        or system (value ignored)'),
                                    'value' => new external_value(PARAM_RAW, 'the value of the categorytype')
                                )
                            ),
                            'name' => new external_value(PARAM_RAW, 'cohort name'),
                            'idnumber' => new external_value(PARAM_RAW, 'cohort idnumber'),
                            'description' => new external_value(PARAM_RAW, 'cohort description', VALUE_OPTIONAL),
                            'descriptionformat' => new external_format_value('description', VALUE_DEFAULT),
                            'visible' => new external_value(PARAM_BOOL, 'cohort visible', VALUE_OPTIONAL, true),
                        )
                    )
                )
            )
        );
    }

    /**
     * Create one or more cohorts
     *
     * @param array $cohorts An array of cohorts to create.
     * @return array An array of arrays
     * @since Moodle 2.5
     */
    public static function create_cohorts($cohorts) {
        global $CFG, $DB;
        require_once("$CFG->dirroot/cohort/lib.php");

        $params = self::validate_parameters(self::create_cohorts_parameters(), array('cohorts' => $cohorts));

        $transaction = $DB->start_delegated_transaction();

        $syscontext = context_system::instance();
        $cohortids = array();

        foreach ($params['cohorts'] as $cohort) {
            $cohort = (object)$cohort;

            // Category type (context id).
            $categorytype = $cohort->categorytype;
            if (!in_array($categorytype['type'], array('idnumber', 'id', 'system'))) {
                throw new invalid_parameter_exception('category type must be id, idnumber or system:' . $categorytype['type']);
            }
            if ($categorytype['type'] === 'system') {
                $cohort->contextid = $syscontext->id;
            } else if ($catid = $DB->get_field('course_categories', 'id', array($categorytype['type'] => $categorytype['value']))) {
                $catcontext = context_coursecat::instance($catid);
                $cohort->contextid = $catcontext->id;
            } else {
                throw new invalid_parameter_exception('category not exists: category '
                    .$categorytype['type'].' = '.$categorytype['value']);
            }
            // Make sure that the idnumber doesn't already exist.
            if ($DB->record_exists('cohort', array('idnumber' => $cohort->idnumber))) {
                throw new invalid_parameter_exception('record already exists: idnumber='.$cohort->idnumber);
            }
            $context = context::instance_by_id($cohort->contextid, MUST_EXIST);
            if ($context->contextlevel != CONTEXT_COURSECAT and $context->contextlevel != CONTEXT_SYSTEM) {
                throw new invalid_parameter_exception('Invalid context');
            }
            self::validate_context($context);
            require_capability('moodle/cohort:manage', $context);

            // Validate format.
            $cohort->descriptionformat = external_validate_format($cohort->descriptionformat);
            $cohort->id = cohort_add_cohort($cohort);

            list($cohort->description, $cohort->descriptionformat) =
                external_format_text($cohort->description, $cohort->descriptionformat,
                        $context->id, 'cohort', 'description', $cohort->id);
            $cohortids[] = (array)$cohort;
        }
        $transaction->allow_commit();

        return $cohortids;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function create_cohorts_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'cohort id'),
                    'name' => new external_value(PARAM_RAW, 'cohort name'),
                    'idnumber' => new external_value(PARAM_RAW, 'cohort idnumber'),
                    'description' => new external_value(PARAM_RAW, 'cohort description'),
                    'descriptionformat' => new external_format_value('description'),
                    'visible' => new external_value(PARAM_BOOL, 'cohort visible'),
                )
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function delete_cohorts_parameters() {
        return new external_function_parameters(
            array(
                'cohortids' => new external_multiple_structure(new external_value(PARAM_INT, 'cohort ID')),
            )
        );
    }

    /**
     * Delete cohorts
     *
     * @param array $cohortids
     * @return null
     * @since Moodle 2.5
     */
    public static function delete_cohorts($cohortids) {
        global $CFG, $DB;
        require_once("$CFG->dirroot/cohort/lib.php");

        $params = self::validate_parameters(self::delete_cohorts_parameters(), array('cohortids' => $cohortids));

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['cohortids'] as $cohortid) {
            // Validate params.
            $cohortid = validate_param($cohortid, PARAM_INT);
            $cohort = $DB->get_record('cohort', array('id' => $cohortid), '*', MUST_EXIST);

            // Now security checks.
            $context = context::instance_by_id($cohort->contextid, MUST_EXIST);
            if ($context->contextlevel != CONTEXT_COURSECAT and $context->contextlevel != CONTEXT_SYSTEM) {
                throw new invalid_parameter_exception('Invalid context');
            }
            self::validate_context($context);
            require_capability('moodle/cohort:manage', $context);
            cohort_delete_cohort($cohort);
        }
        $transaction->allow_commit();

        return null;
    }

    /**
     * Returns description of method result value
     *
     * @return null
     * @since Moodle 2.5
     */
    public static function delete_cohorts_returns() {
        return null;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function get_cohorts_parameters() {
        return new external_function_parameters(
            array(
                'cohortids' => new external_multiple_structure(new external_value(PARAM_INT, 'Cohort ID')
                    , 'List of cohort id. A cohort id is an integer.', VALUE_DEFAULT, array()),
            )
        );
    }

    /**
     * Get cohorts definition specified by ids
     *
     * @param array $cohortids array of cohort ids
     * @return array of cohort objects (id, courseid, name)
     * @since Moodle 2.5
     */
    public static function get_cohorts($cohortids = array()) {
        global $DB;

        $params = self::validate_parameters(self::get_cohorts_parameters(), array('cohortids' => $cohortids));

        if (empty($cohortids)) {
            $cohorts = $DB->get_records('cohort');
        } else {
            $cohorts = $DB->get_records_list('cohort', 'id', $params['cohortids']);
        }

        $cohortsinfo = array();
        foreach ($cohorts as $cohort) {
            // Now security checks.
            $context = context::instance_by_id($cohort->contextid, MUST_EXIST);
            if ($context->contextlevel != CONTEXT_COURSECAT and $context->contextlevel != CONTEXT_SYSTEM) {
                throw new invalid_parameter_exception('Invalid context');
            }
            self::validate_context($context);
            if (!has_any_capability(array('moodle/cohort:manage', 'moodle/cohort:view'), $context)) {
                throw new required_capability_exception($context, 'moodle/cohort:view', 'nopermissions', '');
            }

            list($cohort->description, $cohort->descriptionformat) =
                external_format_text($cohort->description, $cohort->descriptionformat,
                        $context->id, 'cohort', 'description', $cohort->id);

            $cohortsinfo[] = (array) $cohort;
        }
        return $cohortsinfo;
    }


    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function get_cohorts_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'ID of the cohort'),
                    'name' => new external_value(PARAM_RAW, 'cohort name'),
                    'idnumber' => new external_value(PARAM_RAW, 'cohort idnumber'),
                    'description' => new external_value(PARAM_RAW, 'cohort description'),
                    'descriptionformat' => new external_format_value('description'),
                    'visible' => new external_value(PARAM_BOOL, 'cohort visible'),
                )
            )
        );
    }

    /**
     * Returns the description of external function parameters.
     *
     * @return external_function_parameters
     */
    public static function search_cohorts_parameters() {
        $query = new external_value(
            PARAM_RAW,
            'Query string'
        );
        $includes = new external_value(
            PARAM_ALPHA,
            'What other contexts to fetch the frameworks from. (all, parents, self)',
            VALUE_DEFAULT,
            'parents'
        );
        $limitfrom = new external_value(
            PARAM_INT,
            'limitfrom we are fetching the records from',
            VALUE_DEFAULT,
            0
        );
        $limitnum = new external_value(
            PARAM_INT,
            'Number of records to fetch',
            VALUE_DEFAULT,
            25
        );
        return new external_function_parameters(array(
            'query' => $query,
            'context' => self::get_context_parameters(),
            'includes' => $includes,
            'limitfrom' => $limitfrom,
            'limitnum' => $limitnum
        ));
    }

    /**
     * Search cohorts.
     *
     * @param string $query
     * @param array $context
     * @param string $includes
     * @param int $limitfrom
     * @param int $limitnum
     * @return array
     */
    public static function search_cohorts($query, $context, $includes = 'parents', $limitfrom = 0, $limitnum = 25) {
        global $CFG;
        require_once($CFG->dirroot . '/cohort/lib.php');

        $params = self::validate_parameters(self::search_cohorts_parameters(), array(
            'query' => $query,
            'context' => $context,
            'includes' => $includes,
            'limitfrom' => $limitfrom,
            'limitnum' => $limitnum,
        ));
        $query = $params['query'];
        $includes = $params['includes'];
        $context = self::get_context_from_params($params['context']);
        $limitfrom = $params['limitfrom'];
        $limitnum = $params['limitnum'];

        self::validate_context($context);

        $manager = has_capability('moodle/cohort:manage', $context);
        if (!$manager) {
            require_capability('moodle/cohort:view', $context);
        }

        // TODO Make this more efficient.
        if ($includes == 'self') {
            $results = cohort_get_cohorts($context->id, $limitfrom, $limitnum, $query);
            $results = $results['cohorts'];
        } else if ($includes == 'parents') {
            $results = cohort_get_cohorts($context->id, $limitfrom, $limitnum, $query);
            $results = $results['cohorts'];
            if (!$context instanceof context_system) {
                $results = array_merge($results, cohort_get_available_cohorts($context, COHORT_ALL, $limitfrom, $limitnum, $query));
            }
        } else if ($includes == 'all') {
            $results = cohort_get_all_cohorts($limitfrom, $limitnum, $query);
            $results = $results['cohorts'];
        } else {
            throw new coding_exception('Invalid parameter value for \'includes\'.');
        }

        $cohorts = array();
        foreach ($results as $key => $cohort) {
            $cohortcontext = context::instance_by_id($cohort->contextid);
            if (!isset($cohort->description)) {
                $cohort->description = '';
            }
            if (!isset($cohort->descriptionformat)) {
                $cohort->descriptionformat = FORMAT_PLAIN;
            }

            list($cohort->description, $cohort->descriptionformat) =
                external_format_text($cohort->description, $cohort->descriptionformat,
                        $cohortcontext->id, 'cohort', 'description', $cohort->id);

            $cohorts[$key] = $cohort;
        }

        return array('cohorts' => $cohorts);
    }

    /**
     * Returns description of external function result value.
     *
     * @return external_description
     */
    public static function search_cohorts_returns() {
        return new external_single_structure(array(
            'cohorts' => new external_multiple_structure(
                new external_single_structure(array(
                    'id' => new external_value(PARAM_INT, 'ID of the cohort'),
                    'name' => new external_value(PARAM_RAW, 'cohort name'),
                    'idnumber' => new external_value(PARAM_RAW, 'cohort idnumber'),
                    'description' => new external_value(PARAM_RAW, 'cohort description'),
                    'descriptionformat' => new external_format_value('description'),
                    'visible' => new external_value(PARAM_BOOL, 'cohort visible'),
                ))
            )
        ));
    }



    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function update_cohorts_parameters() {
        return new external_function_parameters(
            array(
                'cohorts' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'ID of the cohort'),
                            'categorytype' => new external_single_structure(
                                array(
                                    'type' => new external_value(PARAM_TEXT, 'the name of the field: id (numeric value
                                        of course category id) or idnumber (alphanumeric value of idnumber course category)
                                        or system (value ignored)'),
                                    'value' => new external_value(PARAM_RAW, 'the value of the categorytype')
                                )
                            ),
                            'name' => new external_value(PARAM_RAW, 'cohort name'),
                            'idnumber' => new external_value(PARAM_RAW, 'cohort idnumber'),
                            'description' => new external_value(PARAM_RAW, 'cohort description', VALUE_OPTIONAL),
                            'descriptionformat' => new external_format_value('description', VALUE_DEFAULT),
                            'visible' => new external_value(PARAM_BOOL, 'cohort visible', VALUE_OPTIONAL),
                        )
                    )
                )
            )
        );
    }

    /**
     * Update cohorts
     *
     * @param array $cohorts
     * @return null
     * @since Moodle 2.5
     */
    public static function update_cohorts($cohorts) {
        global $CFG, $DB;
        require_once("$CFG->dirroot/cohort/lib.php");

        $params = self::validate_parameters(self::update_cohorts_parameters(), array('cohorts' => $cohorts));

        $transaction = $DB->start_delegated_transaction();
        $syscontext = context_system::instance();

        foreach ($params['cohorts'] as $cohort) {
            $cohort = (object) $cohort;

            if (trim($cohort->name) == '') {
                throw new invalid_parameter_exception('Invalid cohort name');
            }

            $oldcohort = $DB->get_record('cohort', array('id' => $cohort->id), '*', MUST_EXIST);
            $oldcontext = context::instance_by_id($oldcohort->contextid, MUST_EXIST);
            require_capability('moodle/cohort:manage', $oldcontext);

            // Category type (context id).
            $categorytype = $cohort->categorytype;
            if (!in_array($categorytype['type'], array('idnumber', 'id', 'system'))) {
                throw new invalid_parameter_exception('category type must be id, idnumber or system:' . $categorytype['type']);
            }
            if ($categorytype['type'] === 'system') {
                $cohort->contextid = $syscontext->id;
            } else if ($catid = $DB->get_field('course_categories', 'id', array($categorytype['type'] => $categorytype['value']))) {
                $cohort->contextid = $DB->get_field('context', 'id', array('instanceid' => $catid,
                    'contextlevel' => CONTEXT_COURSECAT));
            } else {
                throw new invalid_parameter_exception('category not exists: category='.$categorytype['value']);
            }

            if ($cohort->contextid != $oldcohort->contextid) {
                $context = context::instance_by_id($cohort->contextid, MUST_EXIST);
                if ($context->contextlevel != CONTEXT_COURSECAT and $context->contextlevel != CONTEXT_SYSTEM) {
                    throw new invalid_parameter_exception('Invalid context');
                }

                self::validate_context($context);
                require_capability('moodle/cohort:manage', $context);
            }

            if (!empty($cohort->description)) {
                $cohort->descriptionformat = external_validate_format($cohort->descriptionformat);
            }

            cohort_update_cohort($cohort);
        }

        $transaction->allow_commit();

        return null;
    }

    /**
     * Returns description of method result value
     *
     * @return null
     * @since Moodle 2.5
     */
    public static function update_cohorts_returns() {
        return null;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function add_cohort_members_parameters() {
        return new external_function_parameters (
            array(
                'members' => new external_multiple_structure (
                    new external_single_structure (
                        array (
                            'cohorttype' => new external_single_structure (
                                array(
                                    'type' => new external_value(PARAM_ALPHANUMEXT, 'The name of the field: id
                                        (numeric value of cohortid) or idnumber (alphanumeric value of idnumber) '),
                                    'value' => new external_value(PARAM_RAW, 'The value of the cohort')
                                )
                            ),
                            'usertype' => new external_single_structure (
                                array(
                                    'type' => new external_value(PARAM_ALPHANUMEXT, 'The name of the field: id
                                        (numeric value of id) or username (alphanumeric value of username) '),
                                    'value' => new external_value(PARAM_RAW, 'The value of the cohort')
                                )
                            )
                        )
                    )
                )
            )
        );
    }

    /**
     * Add cohort members
     *
     * @param array $members of arrays with keys userid, cohortid
     * @since Moodle 2.5
     */
    public static function add_cohort_members($members) {
        global $CFG, $DB;
        require_once($CFG->dirroot."/cohort/lib.php");

        $params = self::validate_parameters(self::add_cohort_members_parameters(), array('members' => $members));

        $transaction = $DB->start_delegated_transaction();
        $warnings = array();
        foreach ($params['members'] as $member) {
            // Cohort parameters.
            $cohorttype = $member['cohorttype'];
            $cohortparam = array($cohorttype['type'] => $cohorttype['value']);
            // User parameters.
            $usertype = $member['usertype'];
            $userparam = array($usertype['type'] => $usertype['value']);
            try {
                // Check parameters.
                if ($cohorttype['type'] != 'id' && $cohorttype['type'] != 'idnumber') {
                    $warning = array();
                    $warning['warningcode'] = '1';
                    $warning['message'] = 'invalid parameter: cohortype='.$cohorttype['type'];
                    $warnings[] = $warning;
                    continue;
                }
                if ($usertype['type'] != 'id' && $usertype['type'] != 'username') {
                    $warning = array();
                    $warning['warningcode'] = '1';
                    $warning['message'] = 'invalid parameter: usertype='.$usertype['type'];
                    $warnings[] = $warning;
                    continue;
                }
                // Extract parameters.
                if (!$cohortid = $DB->get_field('cohort', 'id', $cohortparam)) {
                    $warning = array();
                    $warning['warningcode'] = '2';
                    $warning['message'] = 'cohort '.$cohorttype['type'].'='.$cohorttype['value'].' not exists';
                    $warnings[] = $warning;
                    continue;
                }
                if (!$userid = $DB->get_field('user', 'id', array_merge($userparam, array('deleted' => 0,
                    'mnethostid' => $CFG->mnet_localhost_id)))) {
                    $warning = array();
                    $warning['warningcode'] = '2';
                    $warning['message'] = 'user '.$usertype['type'].'='.$usertype['value'].' not exists';
                    $warnings[] = $warning;
                    continue;
                }
                if ($DB->record_exists('cohort_members', array('cohortid' => $cohortid, 'userid' => $userid))) {
                    $warning = array();
                    $warning['warningcode'] = '3';
                    $warning['message'] = 'record already exists: cohort('.$cohorttype['type'].'='.$cohorttype['value'].' '.
                        $usertype['type'].'='.$usertype['value'].')';
                    $warnings[] = $warning;
                    continue;
                }
                $cohort = $DB->get_record('cohort', array('id'=>$cohortid), '*', MUST_EXIST);
                $context = context::instance_by_id($cohort->contextid, MUST_EXIST);
                if ($context->contextlevel != CONTEXT_COURSECAT and $context->contextlevel != CONTEXT_SYSTEM) {
                    $warning = array();
                    $warning['warningcode'] = '1';
                    $warning['message'] = 'Invalid context: '.$context->contextlevel;
                    $warnings[] = $warning;
                    continue;
                }
                self::validate_context($context);
            } catch (Exception $e) {
                throw new moodle_exception('Error', 'cohort', '', $e->getMessage());
            }
            if (!has_any_capability(array('moodle/cohort:manage', 'moodle/cohort:assign'), $context)) {
                throw new required_capability_exception($context, 'moodle/cohort:assign', 'nopermissions', '');
            }
            cohort_add_member($cohortid, $userid);
        }
        $transaction->allow_commit();
        // Return.
        $result = array();
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return null
     * @since Moodle 2.5
     */
    public static function add_cohort_members_returns() {
        return new external_single_structure(
            array(
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function delete_cohort_members_parameters() {
        return new external_function_parameters(
            array(
                'members' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'cohortid' => new external_value(PARAM_INT, 'cohort record id'),
                            'userid' => new external_value(PARAM_INT, 'user id'),
                        )
                    )
                )
            )
        );
    }

    /**
     * Delete cohort members
     *
     * @param array $members of arrays with keys userid, cohortid
     * @since Moodle 2.5
     */
    public static function delete_cohort_members($members) {
        global $CFG, $DB;
        require_once("$CFG->dirroot/cohort/lib.php");

        // Validate parameters.
        $params = self::validate_parameters(self::delete_cohort_members_parameters(), array('members' => $members));

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['members'] as $member) {
            $cohortid = $member['cohortid'];
            $userid = $member['userid'];

            $cohort = $DB->get_record('cohort', array('id' => $cohortid), '*', MUST_EXIST);
            $user = $DB->get_record('user', array('id' => $userid, 'deleted' => 0, 'mnethostid' => $CFG->mnet_localhost_id),
                '*', MUST_EXIST);

            // Now security checks.
            $context = context::instance_by_id($cohort->contextid, MUST_EXIST);
            if ($context->contextlevel != CONTEXT_COURSECAT and $context->contextlevel != CONTEXT_SYSTEM) {
                throw new invalid_parameter_exception('Invalid context');
            }
            self::validate_context($context);
            if (!has_any_capability(array('moodle/cohort:manage', 'moodle/cohort:assign'), $context)) {
                throw new required_capability_exception($context, 'moodle/cohort:assign', 'nopermissions', '');
            }

            cohort_remove_member($cohort->id, $user->id);
        }
        $transaction->allow_commit();
    }

    /**
     * Returns description of method result value
     *
     * @return null
     * @since Moodle 2.5
     */
    public static function delete_cohort_members_returns() {
        return null;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function get_cohort_members_parameters() {
        return new external_function_parameters(
            array(
                'cohortids' => new external_multiple_structure(new external_value(PARAM_INT, 'Cohort ID')),
            )
        );
    }

    /**
     * Return all members for a cohort
     *
     * @param array $cohortids array of cohort ids
     * @return array with cohort id keys containing arrays of user ids
     * @since Moodle 2.5
     */
    public static function get_cohort_members($cohortids) {
        global $DB;
        $params = self::validate_parameters(self::get_cohort_members_parameters(), array('cohortids' => $cohortids));

        $members = array();

        foreach ($params['cohortids'] as $cohortid) {
            // Validate params.
            $cohort = $DB->get_record('cohort', array('id' => $cohortid), '*', MUST_EXIST);
            // Now security checks.
            $context = context::instance_by_id($cohort->contextid, MUST_EXIST);
            if ($context->contextlevel != CONTEXT_COURSECAT and $context->contextlevel != CONTEXT_SYSTEM) {
                throw new invalid_parameter_exception('Invalid context');
            }
            self::validate_context($context);
            if (!has_any_capability(array('moodle/cohort:manage', 'moodle/cohort:view'), $context)) {
                throw new required_capability_exception($context, 'moodle/cohort:view', 'nopermissions', '');
            }

            $cohortmembers = $DB->get_records_sql("SELECT u.id FROM {user} u, {cohort_members} cm
                WHERE u.id = cm.userid AND cm.cohortid = ?
                ORDER BY lastname ASC, firstname ASC", array($cohort->id));
            $members[] = array('cohortid' => $cohortid, 'userids' => array_keys($cohortmembers));
        }
        return $members;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function get_cohort_members_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'cohortid' => new external_value(PARAM_INT, 'cohort record id'),
                    'userids' => new external_multiple_structure(new external_value(PARAM_INT, 'user id')),
                )
            )
        );
    }
}

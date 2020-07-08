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
 * Iomad External Web Services
 *
 * @package block_iomad_company_admin
 * @copyright 2017 E-LearnDesign Limited
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir . "/externallib.php");

class block_iomad_company_admin_external extends external_api {

    /**
     * block_iomad_company_admin_create_companies
     *
     * Return description of method parameters
     * @return external_function_parameters
     */
    public static function create_companies_parameters() {
        return new external_function_parameters(
            array(
                'companies' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_TEXT, 'Company long name'),
                            'shortname' => new external_value(PARAM_TEXT, 'Compay short name'),
                            'city' => new external_value(PARAM_TEXT, 'Company location city'),
                            'country' => new external_value(PARAM_TEXT, 'Company location country'),
                            'maildisplay' => new external_value(PARAM_INT, 'User default email display', VALUE_DEFAULT, 2),
                            'mailformat' => new external_value(PARAM_INT, 'User default email format', VALUE_DEFAULT, 1),
                            'maildigest' => new external_value(PARAM_INT, 'User default digest type', VALUE_DEFAULT, 0),
                            'autosubscribe' => new external_value(PARAM_INT, 'User default forum auto-subscribe', VALUE_DEFAULT, 1),
                            'trackforums' => new external_value(PARAM_INT, 'User default forum tracking', VALUE_DEFAULT, 0),
                            'htmleditor' => new external_value(PARAM_INT, 'User default text editor', VALUE_DEFAULT, 1),
                            'screenreader' => new external_value(PARAM_INT, 'User default screen reader', VALUE_DEFAULT, 0),
                            'timezone' => new external_value(PARAM_TEXT, 'User default timezone', VALUE_DEFAULT, '99'),
                            'lang' => new external_value(PARAM_TEXT, 'User default language', VALUE_DEFAULT, 'en'),
                            'suspended' => new external_value(PARAM_INT, 'Company is suspended when <> 0', VALUE_DEFAULT, 0),
                            'ecommerce' => new external_value(PARAM_INT, 'Ecommerce is disabled when = 0', VALUE_DEFAULT, 0),
                            'parentid' => new external_value(PARAM_INT, 'ID of parent company', VALUE_DEFAULT, 0),
                            'customcss' => new external_value(PARAM_TEXT, 'Company custom css'),
                            'validto' => new external_value(PARAM_INT, 'Contract termination date in unix timestamp', VALUE_DEFAULT, null),
                            'suspendafter' => new external_value(PARAM_INT, 'Number of seconds after termination date to suspend the company', VALUE_DEFAULT, 0),
                        )
                    )
                )
            )
        );
    }

    /**
     * block_iomad_company_admin_create_companies
     *
     * Implement create_company
     * @param $company
     * @return boolean success
     */
    public static function create_companies($companies) {
        global $CFG, $DB;

        // Validate parameters
        $params = self::validate_parameters(self::create_companies_parameters(), array('companies' => $companies));

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:company_add', $context);

        // Array to return newly created records
        $companyinfo = array();

        foreach ($params['companies'] as $company) {

            // does this company already exist
            if ($DB->get_record('company', array('name' => $company['name']))) {
                throw new invalid_parameter_exception('Company name is already being used');
            }
            if ($DB->get_record('company', array('shortname' => $company['shortname']))) {
                throw new invalid_parameter_exception('Company shortname is already being used');
            }

            // Create the company record
            $companyid = $DB->insert_record('company', $company);

            // Deal with certificate info.
            $certificateinforec = array('companyid' => $companyid,
                                        'uselogo' => 1,
                                        'usesignature' => 1,
                                        'useborder' => 1,
                                        'usewatermark' => 1,
                                        'showgrade' => 1);
            $DB->insert_record('companycertificate', $certificateinforec);

            // Fire an event for this.
            $eventother = array('companyid' => $companyid);
            $event = \block_iomad_company_admin\event\company_created::create(array('context' => context_system::instance(),
                                                                                    'userid' => '-1',
                                                                                    'objectid' => $companyid,
                                                                                    'other' => $eventother));
            $event->trigger();

            // Set up default department.
            company::initialise_departments($companyid);

            $newcompany = $DB->get_record('company', array('id' => $companyid));
            $companyinfo[] = (array)$newcompany;

            // Set up course category for company.
            $coursecat = new stdclass();
            $coursecat->name = $company['name'];
            $coursecat->sortorder = 999;
            $coursecat->id = $DB->insert_record('course_categories', $coursecat);
            $coursecat->context = context_coursecat::instance($coursecat->id);
            $categorycontext = $coursecat->context;
            $categorycontext->mark_dirty();
            $DB->update_record('course_categories', $coursecat);
            fix_course_sortorder();
            $companydetails = $DB->get_record('company', array('id' => $companyid));
            $companydetails->category = $coursecat->id;
            $DB->update_record('company', $companydetails);
        }

        return $companyinfo;
    }

    /**
     * block_iomad_company_admin_create_companies
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function create_companies_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                     'id' => new external_value(PARAM_INT, 'Companid ID'),
                     'name' => new external_value(PARAM_TEXT, 'Company long name'),
                     'shortname' => new external_value(PARAM_TEXT, 'Compay short name'),
                     'city' => new external_value(PARAM_TEXT, 'Company location city'),
                     'country' => new external_value(PARAM_TEXT, 'Company location country'),
                     'maildisplay' => new external_value(PARAM_INT, 'User default email display'),
                     'mailformat' => new external_value(PARAM_INT, 'User default email format'),
                     'maildigest' => new external_value(PARAM_INT, 'User default digest type'),
                     'autosubscribe' => new external_value(PARAM_INT, 'User default forum auto-subscribe'),
                     'trackforums' => new external_value(PARAM_INT, 'User default forum tracking'),
                     'htmleditor' => new external_value(PARAM_INT, 'User default text editor'),
                     'screenreader' => new external_value(PARAM_INT, 'User default screen reader'),
                     'timezone' => new external_value(PARAM_TEXT, 'User default timezone'),
                     'lang' => new external_value(PARAM_TEXT, 'User default language'),
                     'suspended' => new external_value(PARAM_INT, 'Company is suspended when <> 0'),
                     'ecommerce' => new external_value(PARAM_INT, 'Ecommerce is disabled when = 0', VALUE_DEFAULT, 0),
                     'parentid' => new external_value(PARAM_INT, 'ID of parent company', VALUE_DEFAULT, 0),
                     'customcss' => new external_value(PARAM_TEXT, 'Company custom css'),
                     'validto' => new external_value(PARAM_INT, 'Contract termination date in unix timestamp', VALUE_DEFAULT, null),
                     'suspendafter' => new external_value(PARAM_INT, 'Number of seconds after termination date to suspend the company', VALUE_DEFAULT, 0),
                )
            )
        );
    }

    /**
     * block_iomad_company_admin_get_companies
     *
     * Return description of method parameters
     * @return external_function_parameters
     */
    public static function get_companies_parameters() {
        return new external_function_parameters(
            array(
                'criteria' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'key' => new external_value(PARAM_ALPHA, 'the company column to search, expected keys (value format) are:
                                "id" (int) matching company id,
                                "name" (string) company name (Note: you can use % for searching but it may be considerably slower!),
                                "shortname" (string) company short name (Note: you can use % for searching but it may be considerably slower!),
                                "suspended" (bool) company is suspended or not,
                                "city" (string) matching company city,
                                "country" (string) matching company country,
                                "timezone" (int) company timezone,
                                "lang" (string) matching company language setting'),
                            'value' => new external_value(PARAM_RAW, 'the value to search')
                        )
                    ), 'the key/value pairs to be considered in company search. Values can not be empty.
                        Specify different keys only once (name => \'company1\', timezone => \'99\', ...) -
                        key occurences are forbidden.
                        The search is executed with AND operator on the criterias. Invalid criterias (keys) are ignored,
                        the search is still executed on the valid criterias.
                        You can search without criteria, but the function is not designed for it.
                        It could very slow or timeout. The function is designed to search some specific companies.'
                )
            )
        );
    }

    /**
     * block_iomad_company_admin_get_companies
     *
     * Implement get_companies
     * @param $companyids
     * @return array of objects
     */
    public static function get_companies($criteria = array()) {
        global $CFG, $DB;

        // Validate parameters
        $params = self::validate_parameters(self::get_companies_parameters(), array('criteria' => $criteria));

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:company_add', $context);

        $companies = array();
        $warnings = array();
        $sqlparams = array();
        $usedkeys = array();
        $sql = " shortname IS NOT NULL ";

        foreach ($params['criteria'] as $criteriaindex => $criteria) {

            // Check that the criteria has never been used.
            if (array_key_exists($criteria['key'], $usedkeys)) {
                throw new moodle_exception('keyalreadyset', '', '', null, 'The key ' . $criteria['key'] . ' can only be sent once');
            } else {
                $usedkeys[$criteria['key']] = true;
            }

            $invalidcriteria = false;
            // Clean the parameters.
            $paramtype = PARAM_RAW;
            switch ($criteria['key']) {
                case 'id':
                case 'timezone':
                    $paramtype = PARAM_INT;
                    break;
                case 'name':
                case 'shortname':
                case 'city':
                case 'country':
                    $paramtype = PARAM_RAW;
                    break;
                case 'lang':
                    $paramtype = PARAM_CLEAN;
                    break;
                case 'suspended':
                    $paramtype = PARAM_BOOL;
                    break;
                default:
                    // Send back a warning that this search key is not supported in this version.
                    // This warning will make the function extandable without breaking clients.
                    $warnings[] = array(
                        'item' => $criteria['key'],
                        'warningcode' => 'invalidfieldparameter',
                        'message' =>
                            'The search key \'' . $criteria['key'] . '\' is not supported, look at the web service documentation'
                    );
                    // Do not add this invalid criteria to the created SQL request.
                    $invalidcriteria = true;
                    unset($params['criteria'][$criteriaindex]);
                    break;
            }

            if (!$invalidcriteria) {
                $cleanedvalue = clean_param($criteria['value'], $paramtype);

                $sql .= ' AND ';

                // Create the SQL.
                switch ($criteria['key']) {
                    case 'id':
                    case 'timezone':
                    case 'lang':
                    case 'suspended':
                        $sql .= $criteria['key'] . ' = :' . $criteria['key'];
                        $sqlparams[$criteria['key']] = $cleanedvalue;
                        break;
                    case 'name':
                    case 'shortname':
                    case 'city':
                    case 'country':
                        $sql .= $DB->sql_like($criteria['key'], ':' . $criteria['key'], false);
                        $sqlparams[$criteria['key']] = $cleanedvalue;
                        break;
                    default:
                        break;
                }
            }
        }

        $companies = $DB->get_records_select('company', $sql, $sqlparams, 'id ASC');

        return array('companies' => $companies, 'warnings' => $warnings);
    }

    /**
     * block_iomad_company_admin_get_companies
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function get_companies_returns() {
        return new external_single_structure(
            array('companies' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                         'id' => new external_value(PARAM_INT, 'Companid ID'),
                         'name' => new external_value(PARAM_TEXT, 'Company long name'),
                         'shortname' => new external_value(PARAM_TEXT, 'Compay short name'),
                         'city' => new external_value(PARAM_TEXT, 'Company location city'),
                         'country' => new external_value(PARAM_TEXT, 'Company location country'),
                         'maildisplay' => new external_value(PARAM_INT, 'User default email display'),
                         'mailformat' => new external_value(PARAM_INT, 'User default email format'),
                         'maildigest' => new external_value(PARAM_INT, 'User default digest type'),
                         'autosubscribe' => new external_value(PARAM_INT, 'User default forum auto-subscribe'),
                         'trackforums' => new external_value(PARAM_INT, 'User default forum tracking'),
                         'htmleditor' => new external_value(PARAM_INT, 'User default text editor'),
                         'screenreader' => new external_value(PARAM_INT, 'User default screen reader'),
                         'timezone' => new external_value(PARAM_TEXT, 'User default timezone'),
                         'lang' => new external_value(PARAM_TEXT, 'User default language'),
                         'suspended' => new external_value(PARAM_INT, 'Company is suspended when <> 0'),
                         'ecommerce' => new external_value(PARAM_INT, 'Ecommerce is disabled when = 0', VALUE_DEFAULT, 0),
                         'parentid' => new external_value(PARAM_INT, 'ID of parent company', VALUE_DEFAULT, 0),
                         'customcss' => new external_value(PARAM_TEXT, 'Company custom css'),
                         'validto' => new external_value(PARAM_INT, 'Contract termination date in unix timestamp', VALUE_DEFAULT, null),
                         'suspendafter' => new external_value(PARAM_INT, 'Number of seconds after termination date to suspend the company', VALUE_DEFAULT, 0),
                         'companyterminated' => new external_value(PARAM_INT, 'Company contract is terminated when <> 0', VALUE_DEFAULT, 0),
                         )
                     )
                 ),
                'warnings' => new external_warnings('always set to \'key\'', 'faulty key name')
            )
        );
    }

    /**
     * block_iomad_company_admin_edit_companies
     *
     * Return description of method parameters
     * @return external_function_parameters
     */
    public static function edit_companies_parameters() {
        return new external_function_parameters(
            array(
                'companies' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Company id number'),
                            'name' => new external_value(PARAM_TEXT, 'Company long name', VALUE_OPTIONAL),
                            'shortname' => new external_value(PARAM_TEXT, 'Compay short name', VALUE_OPTIONAL),
                            'city' => new external_value(PARAM_TEXT, 'Company location city', VALUE_OPTIONAL),
                            'country' => new external_value(PARAM_TEXT, 'Company location country', VALUE_OPTIONAL),
                            'maildisplay' => new external_value(PARAM_INT, 'User default email display', VALUE_OPTIONAL),
                            'mailformat' => new external_value(PARAM_INT, 'User default email format', VALUE_OPTIONAL),
                            'maildigest' => new external_value(PARAM_INT, 'User default digest type', VALUE_OPTIONAL),
                            'autosubscribe' => new external_value(PARAM_INT, 'User default forum auto-subscribe', VALUE_OPTIONAL),
                            'trackforums' => new external_value(PARAM_INT, 'User default forum tracking', VALUE_OPTIONAL),
                            'htmleditor' => new external_value(PARAM_INT, 'User default text editor', VALUE_OPTIONAL),
                            'screenreader' => new external_value(PARAM_INT, 'User default screen reader', VALUE_OPTIONAL),
                            'timezone' => new external_value(PARAM_TEXT, 'User default timezone', VALUE_OPTIONAL),
                            'lang' => new external_value(PARAM_TEXT, 'User default language', VALUE_OPTIONAL),
                            'suspended' => new external_value(PARAM_INT, 'Company is suspended when <> 0', VALUE_DEFAULT, 0),
                            'ecommerce' => new external_value(PARAM_INT, 'Ecommerce is disabled when = 0', VALUE_DEFAULT, 0),
                            'parentid' => new external_value(PARAM_INT, 'ID of parent company', VALUE_DEFAULT, 0),
                            'customcss' => new external_value(PARAM_TEXT, 'Company custom css'),
                            'validto' => new external_value(PARAM_INT, 'Contract termination date in unix timestamp', VALUE_DEFAULT, null),
                            'suspendafter' => new external_value(PARAM_INT, 'Number of seconds after termination date to suspend the company', VALUE_DEFAULT, 0),
                            'companyterminated' => new external_value(PARAM_INT, 'Company contract is terminated when <> 0', VALUE_DEFAULT, 0),
                        )
                    )
                )
            )
        );
    }

    /**
     * block_iomad_company_admin_edit_companies
     *
     * Implement create_company
     * @param $company
     * @return boolean success
     */
    public static function edit_companies($companies) {
        global $CFG, $DB;

        // Validate parameters
        $params = self::validate_parameters(self::edit_companies_parameters(), array('companies' => $companies));

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:company_add', $context);

        foreach ($params['companies'] as $company) {

            $id = $company['id'];

            // does this company exist
            if (!$oldcompany = $DB->get_record('company', array('id' => $id))) {
                throw new invalid_parameter_exception("Company id=$id does not exist");
            }

            // Have we changed the contract end date?
            if (!empty($company->validto)) {
                if (!empty($oldcompany->companyterminated) && $company->validto > $oldcompany->validto) {
                    $company->companyterminated = 0;
                }
            }

            // Store this for reporting purposes.
            $copy = clone($oldcompany);

            // Copy whatever vars we have
            foreach ($company as $key => $value) {
                $oldcompany->$key = $value;
            }

            // check we haven't created a name clash
            if ($duplicate = $DB->get_record('company', array('name' => $oldcompany->name))) {
                if ($duplicate->id != $oldcompany->id) {
                    throw new invalid_parameter_exception('Duplicate company name');
                }
            }
            if ($duplicate = $DB->get_record('company', array('shortname' => $oldcompany->shortname))) {
                if ($duplicate->id != $oldcompany->id) {
                    throw new invalid_parameter_exception('Duplicate company shortname');
                }
            }

            // Update the company record
            $DB->update_record('company', $oldcompany);

            // Fire an event for this.
            $eventother = array('companyid' => $oldcompany->id,
                                'oldcompany' => json_encode($copy));
            $event = \block_iomad_company_admin\event\company_updated::create(array('context' => context_system::instance(),
                                                                                    'userid' => '-1',
                                                                                    'objectid' => $oldcompany->id,
                                                                                    'other' => $eventother));
            $event->trigger();

        }

        return true;
    }

    /**
     * block_iomad_company_admin_create_companies
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function edit_companies_returns() {
        return new external_value(PARAM_BOOL, 'Success or failure');
    }

    // Department Calls.

    /**
     * block_iomad_company_admin_get_departments
     *
     * Return description of method parameters
     * @return external_function_parameters
     */
    public static function get_departments_parameters() {
        return new external_function_parameters(
            array(
                'criteria' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'key' => new external_value(PARAM_ALPHA, 'the user column to search, expected keys (value format) are:
                                "id" (int) matching department id,
                                "name" (string) department name (Note: you can use % for searching but it may be considerably slower!),
                                "shortname" (string) department short name (Note: you can use % for searching but it may be considerably slower!),
                                "company" (int) matching company id,
                                "parent" (int) matching department parent id'),
                            'value' => new external_value(PARAM_RAW, 'the value to search')
                        )
                    ), 'the key/value pairs to be considered in user search. Values can not be empty.
                        Specify different keys only once (name => \'department1\', company => \'2\', ...) -
                        key occurences are forbidden.
                        The search is executed with AND operator on the criterias. Invalid criterias (keys) are ignored,
                        the search is still executed on the valid criterias.
                        You can search without criteria, but the function is not designed for it.
                        It could very slow or timeout. The function is designed to search some specific users.'
                )
            )
        );
    }

    /**
     * block_iomad_company_admin_get_departments
     *
     * Implement get_departments
     * @param $comapnyid
     * @return array of department records.
     */
    public static function get_departments($criteria = array()) {
        global $CFG, $DB;

        // Validate parameters
        $params = self::validate_parameters(self::get_departments_parameters(), array('criteria' => $criteria));

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:edit_all_departments', $context);

        // Validate the criteria and retrieve the users.
        $users = array();
        $warnings = array();
        $sqlparams = array();
        $usedkeys = array();
        $sql = ' company != 0 ';

        foreach ($params['criteria'] as $criteriaindex => $criteria) {

            // Check that the criteria has never been used.
            if (array_key_exists($criteria['key'], $usedkeys)) {
                throw new moodle_exception('keyalreadyset', '', '', null, 'The key ' . $criteria['key'] . ' can only be sent once');
            } else {
                $usedkeys[$criteria['key']] = true;
            }

            $invalidcriteria = false;
            // Clean the parameters.
            $paramtype = PARAM_RAW;
            switch ($criteria['key']) {
                case 'id':
                    $paramtype = PARAM_INT;
                    break;
                case 'name':
                case 'shortname':
                    $paramtype = PARAM_RAW;
                    break;
                case 'company':
                case 'parent':
                    $paramtype = PARAM_INT;
                    break;
                default:
                    // Send back a warning that this search key is not supported in this version.
                    // This warning will make the function extendable without breaking clients.
                    $warnings[] = array(
                        'item' => $criteria['key'],
                        'warningcode' => 'invalidfieldparameter',
                        'message' =>
                            'The search key \'' . $criteria['key'] . '\' is not supported, look at the web service documentation'
                    );
                    // Do not add this invalid criteria to the created SQL request.
                    $invalidcriteria = true;
                    unset($params['criteria'][$criteriaindex]);
                    break;
            }

            if (!$invalidcriteria) {
                $cleanedvalue = clean_param($criteria['value'], $paramtype);

                $sql .= ' AND ';

                // Create the SQL.
                switch ($criteria['key']) {
                    case 'id':
                    case 'company':
                    case 'parent':
                        $sql .= $criteria['key'] . ' = :' . $criteria['key'];
                        $sqlparams[$criteria['key']] = $cleanedvalue;
                        break;
                    case 'name':
                    case 'shortname':
                        $sql .= $DB->sql_like($criteria['key'], ':' . $criteria['key'], false);
                        $sqlparams[$criteria['key']] = $cleanedvalue;
                        break;
                    default:
                        break;
                }
            }
        }

        $departments = $DB->get_records_select('department', $sql, $sqlparams, 'id ASC');

        return array('departments' => $departments, 'warnings' => $warnings);
    }

     /**
     * block_iomad_company_admin_get_departments
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function get_departments_returns() {
        return new external_single_structure(
                array('departments' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'id' => new external_value(PARAM_INT, 'Department ID'),
                                'name' => new external_value(PARAM_TEXT, 'Department name'),
                                'shortname' => new external_value(PARAM_TEXT, 'Department short name'),
                                'company' => new external_value(PARAM_INT, 'Company ID'),
                                'parent' => new external_value(PARAM_INT, 'Department parent id'),
                                )
                            )
                       ),
                      'warnings' => new external_warnings('always set to \'key\'', 'faulty key name')
                    )
                );
    }

    // User handling

    /**
     * block_iomad_company_admin_assign_users
     *
     * Return description of method parameters
     * @return external_function_parameters
     */
    public static function assign_users_parameters() {
        return new external_function_parameters(
            array(
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'userid' => new external_value(PARAM_INT, 'User ID', VALUE_DEFAULT, 0),
                            'companyid' => new external_value(PARAM_INT, 'User company ID', VALUE_DEFAULT, 0),
                            'departmentid' => new external_value(PARAM_INT, 'User company department ID', VALUE_DEFAULT, 0),
                            'managertype' => new external_value(PARAM_INT, 'User manager type 0 => User, 1 => company manager 2 => department manager', VALUE_DEFAULT, 0),
                        )
                    )
                )
            )
        );
    }

    /**
     * block_iomad_company_admin_assign_users
     *
     * Implement assign_users
     * @param $comapnyid
     * @return array of department records.
     */
    public static function assign_users($users) {
        global $CFG, $DB;

        // Validate parameters
        $params = self::validate_parameters(self::assign_users_parameters(), array('users' => $users));

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:assign_company_manager', $context);

        $result = array();

        // Deal with the list of users.
        foreach ($params['users'] as $userrecord) {
            $succeeded = true;
            $errormessage = "";
            if (empty($userrecord['userid']) || empty($userrecord['companyid'])) {
                $succeeded = false;
                continue;
            }
            $company = new company($userrecord['companyid']);

            // Check if the company has gone over the user quota.
            if (!$company->check_usercount(1)) {
                $maxusers = $company->get('maxusers');
                $errormessage = get_string('maxuserswarning', 'block_iomad_company_admin', $maxusers);
            }

            if (!$company->assign_user_to_company($userrecord['userid'],
                                                  $userrecord['departmentid'],
                                                  $userrecord['managertype'],
                                                  true)) {
                $succeeded = false;
                $errormessage = "Unable to assign user";
            } else {

                // Create an event for this.
                $managertypes = $company->get_managertypes();
                $eventother = array('companyname' => $company->get_name(),
                                    'companyid' => $company->id,
                                    'usertype' => $userrecord['managertype'],
                                    'usertypename' => $managertypes[$userrecord['managertype']]);
                $event = \block_iomad_company_admin\event\company_user_assigned::create(array('context' => context_system::instance(),
                                                                                                'objectid' => $company->id,
                                                                                                'userid' => $userrecord['userid'],
                                                                                                'other' => $eventother));
                $event->trigger();
            }
            $result[] = array('userid' => $userrecord['userid'],
                              'companyid' => $company->id,
                              'result' => $succeeded,
                              'message' => $errormessage);
        }
        return array('users' => $result, 'warning' => array());;
    }

   /**
     * block_iomad_company_admin_assign_users
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function assign_users_returns() {
        return new external_single_structure(
                array('users' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'userid' => new external_value(PARAM_INT, 'Department ID'),
                                'companyid' => new external_value(PARAM_INT, 'Company ID'),
                                'result' => new external_value(PARAM_BOOL, 'Success or failure'),
                                'message' => new external_value(PARAM_TEXT, 'Failure message'),
                                )
                            )
                       ),
                      'warnings' => new external_warnings('always set to \'key\'', 'faulty key name')
                    )
                );
    }

    /**
     * block_iomad_company_admin_move_users
     *
     * Return description of method parameters
     * @return external_function_parameters
     */
    public static function move_users_parameters() {
        return new external_function_parameters(
            array(
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'userid' => new external_value(PARAM_INT, 'User ID', VALUE_DEFAULT, 0),
                            'companyid' => new external_value(PARAM_INT, 'User company ID', VALUE_DEFAULT, 0),
                            'departmentid' => new external_value(PARAM_INT, 'User company department ID', VALUE_DEFAULT, 0),
                            'managertype' => new external_value(PARAM_INT, 'User manager type 0 => User, 1 => company manager 2 => department manager', VALUE_DEFAULT, 0),
                        )
                    )
                )
            )
        );
    }

    /**
     * block_iomad_company_admin_move_users
     *
     * Implement move_users
     * @param $comapnyid
     * @return array of department records.
     */
    public static function move_users($users) {
        global $CFG, $DB;

        // Validate parameters
        $params = self::validate_parameters(self::assign_users_parameters(), array('users' => $users));

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:assign_company_manager', $context);

        $succeeded = true;

        // Deal with the list of users.
        foreach ($params['users'] as $userrecord) {
            if (empty($userrecord['userid']) || empty($userrecord['companyid'])) {
                $succeeded = false;
                continue;
            }
            $company = new company($userrecord['companyid']);
            if (!$company->assign_user_to_department($userrecord['departmentid'],
                                                     $userrecord['userid'],
                                                     $userrecord['managertype'],
                                                     true)) {
                $succeeded = false;
            }

            // Create an event for this.
            $managertypes = $company->get_managertypes();
            $eventother = array('companyname' => $company->get_name(),
                                'companyid' => $company->id,
                                'usertype' => $userrecord['managertype'],
                                'usertypename' => $managertypes[$userrecord['managertype']]);
            $event = \block_iomad_company_admin\event\company_user_assigned::create(array('context' => context_system::instance(),
                                                                                            'objectid' => $company->id,
                                                                                            'userid' => $adduser->id,
                                                                                            'other' => $eventother));
            $event->trigger();
        }
        return $succeeded;
    }

   /**
     * block_iomad_company_admin_move_users
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function move_users_returns() {
        return new external_value(PARAM_BOOL, 'Success or failure');
    }

    /**
     * block_iomad_company_admin_unassign_users
     *
     * Return description of method parameters
     * @return external_function_parameters
     */
    public static function unassign_users_parameters() {
        return new external_function_parameters(
            array(
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'userid' => new external_value(PARAM_INT, 'User ID', VALUE_DEFAULT, 0),
                            'companyid' => new external_value(PARAM_INT, 'User company ID', VALUE_DEFAULT, 0),
                            'usertype' => new external_value(PARAM_INT, 'Old user manager type', VALUE_DEFAULT, 0),
                        )
                    )
                )
            )
        );
    }

    /**
     * block_iomad_company_admin_unassign_users
     *
     * Implement unassign_users
     * @param $comapnyid
     * @return array of department records.
     */
    public static function unassign_users($users) {
        global $CFG, $DB;

        // Validate parameters
        $params = self::validate_parameters(self::unassign_users_parameters(), array('users' => $users));

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:assign_company_manager', $context);

        $succeeded = true;

        // Deal with the list of users.
        foreach ($params['users'] as $userrecord) {
            if (empty($userrecord['userid']) || empty($userrecord['companyid'])) {
                $succeeded = false;
                continue;
            }
            $company = new company($userrecord['companyid']);
            if (!$company->unassign_user_from_company($userrecord['userid'], true)) {
                $succeeded = false;
            }

            // Create an event for this.
            $managertypes = $company->get_managertypes();
            $eventother = array('companyname' => $company->get_name(),
                                'companyid' => $company->id,
                                'usertype' => $userrecord['usertype'],
                                'usertypename' => $managertypes[$userrecord['usertype']]);
            $event = \block_iomad_company_admin\event\company_user_unassigned::create(array('context' => context_system::instance(),
                                                                                            'objectid' => $company->id,
                                                                                            'userid' => $adduser->id,
                                                                                            'other' => $eventother));
            $event->trigger();

        }

        return $succeeded;
    }

   /**
     * block_iomad_company_admin_unassign_users
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function unassign_users_returns() {
        return new external_value(PARAM_BOOL, 'Success or failure');
    }

    // Course functions.

    /**
     * block_iomad_company_admin_assign_courses
     *
     * Return description of method parameters
     * @return external_function_parameters
     */
    public static function assign_courses_parameters() {
        return new external_function_parameters(
            array(
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'courseid' => new external_value(PARAM_INT, 'Course ID', VALUE_DEFAULT, 0),
                            'companyid' => new external_value(PARAM_INT, 'Course company ID', VALUE_DEFAULT, 0),
                            'departmentid' => new external_value(PARAM_INT, 'Course department ID', VALUE_DEFAULT, 0),
                            'owned' => new external_value(PARAM_BOOL, 'Does the company own the course', VALUE_DEFAULT, false),
                            'licensed' => new external_value(PARAM_BOOL, 'Is the course licensed', VALUE_DEFAULT, false),
                        )
                    )
                )
            )
        );
    }

    /**
     * block_iomad_company_admin_get_department_users
     *
     * Return description of method parameters
     * @return external_function_parameters
     */
    public static function get_department_users_parameters() {
        return new external_function_parameters(
            array(
                'departmentids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Department id'), 'List of department IDs', VALUE_DEFAULT, array()
                )
            )
        );
        //return new external_function_parameters(new external_value(PARAM_INT, 'Course id'), 'Course ID', VALUE_DEFAULT, 0);
    }

    /**
     * block_iomad_company_admin_get_department_users
     *
     * Implement get_departments
     * @param $comapnyid
     * @return array of department records.
     */
    public static function get_department_users($departmentids = array()) {
        global $CFG, $DB;

        // Validate parameters
        $params = self::validate_parameters(self::get_department_users_parameters(), $departmentids);

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:edit_users', $context);

        // Get course records
        if (empty($departmentids)) {
            return array();
        } else {
            $departmentinfo = array();
            foreach ($departmentids as $departmentid) {
                $departmentusers = $DB->get_records_sql("SELECT u.id, u.firstname, u.lastname, u.email, cu.companyid, cu.departmentid
                                                         FROM {user} u
                                                         JOIN {company_users} cu ON
                                                         (u.id = cu.userid)
                                                         WHERE cu.departmentid = :departmentid",
                                                         array('departmentid' => $departmeentid));
                $departmentinfo[$departmentid] = (array) $departmentusers;
            }
        }

        return $departmentinfo;
    }

   /**
     * block_iomad_company_admin_get_department_users
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function get_department_users_returns() {
        return new external_single_structure(
                array('users' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'id' => new external_value(PARAM_INT, 'User ID'),
                                'firstname' => new external_value(PARAM_TEXT, 'User firstname'),
                                'lastname' => new external_value(PARAM_TEXT, 'User lastname'),
                                'email' => new external_value(PARAM_TEXT, 'User email address'),
                                'companyid' => new external_value(PARAM_INT, 'Company ID'),
                                'departmentid' => new external_value(PARAM_INT, 'Department ID'),
                                )
                            )
                       ),
                      'warnings' => new external_warnings('always set to \'key\'', 'faulty key name')
                    )
                );
    }

    /**
     * block_iomad_company_admin_assign_courses
     *
     * Implement assign_courses
     * @param $comapnyid
     * @return array of department records.
     */
    public static function assign_courses($courses) {
        global $CFG, $DB;

        // Validate parameters
        $params = self::validate_parameters(self::assign_courses_parameters(), array('courses' => $courses));

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:managecourses', $context);

        $succeeded = true;

        // Deal with the list of users.
        foreach ($params['courses'] as $courserecord) {
            if (empty($courserecord['courseid']) || empty($courserecord['companyid'])) {
                $succeeded = false;
                continue;
            }
            if (!$course = $DB->get_record('course', array('id' => $courserecord['courseid']))) {
                $succeeded = false;
            } else {
                $company = new company($courserecord['companyid']);
                $company->add_course($course, $courserecord['departmentid'], $courserecord['owned'], $courserecord['licensed']);
            }
        }

        return $succeeded;
    }

   /**
     * block_iomad_company_admin_assign_courses
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function assign_courses_returns() {
        return new external_value(PARAM_BOOL, 'Success or failure');
    }

    /**
     * block_iomad_company_admin_unassign_courses
     *
     * Return description of method parameters
     * @return external_function_parameters
     */
    public static function unassign_courses_parameters() {
        return new external_function_parameters(
            array(
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'courseid' => new external_value(PARAM_INT, 'Course ID', VALUE_DEFAULT, 0),
                            'companyid' => new external_value(PARAM_INT, 'Course company ID', VALUE_DEFAULT, 0),
                        )
                    )
                )
            )
        );
    }

    /**
     * block_iomad_company_admin_unassign_courses
     *
     * Implement unassign_courses
     * @param $comapnyid
     * @return array of department records.
     */
    public static function unassign_courses($courses) {
        global $CFG, $DB;

        // Validate parameters
        $params = self::validate_parameters(self::unassign_courses_parameters(), array('courses' => $courses));

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:managecourses', $context);

        $succeeded = true;

        // Deal with the list of users.
        foreach ($params['courses'] as $courserecord) {
            if (empty($courserecord['courseid']) || empty($courserecord['companyid'])) {
                $succeeded = false;
                continue;
            }
            if (!$course = $DB->get_record('course', array('id' => $courserecord['courseid']))) {
                $succeeded = false;
            } else {
                company::remove_course($course, $courserecord['companyid']);
            }
        }

        return $succeeded;
    }

   /**
     * block_iomad_company_admin_unassign_courses
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function unassign_courses_returns() {
        return new external_value(PARAM_BOOL, 'Success or failure');
    }

    /**
     * block_iomad_company_admin_update_courses
     *
     * Return description of method parameters
     * @return external_function_parameters
     */
    public static function update_courses_parameters() {
        return new external_function_parameters(
            array(
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'courseid' => new external_value(PARAM_INT, 'Course ID', VALUE_DEFAULT, 0),
                            'licensed' => new external_value(PARAM_BOOL, 'Course licensed', VALUE_DEFAULT, false),
                            'shared' => new external_value(PARAM_INT, 'Course shared value', VALUE_DEFAULT, 0),
                            'validlength' => new external_value(PARAM_INT, 'Course training valid for in days', VALUE_DEFAULT, 0),
                            'warnexpire' => new external_value(PARAM_INT, 'Course days to warn before training expires', VALUE_DEFAULT, 0),
                            'warncompletion' => new external_value(PARAM_INT, 'Course days to warn if not completed in', VALUE_DEFAULT, 0),
                            'notifyperiod' => new external_value(PARAM_INT, 'Course warning email notify period', VALUE_DEFAULT, 0),
                        )
                    )
                )
            )
        );
    }

    /**
     * block_iomad_company_admin_update_courses
     *
     * Implement update_courses
     * @param $comapnyid
     * @return array of department records.
     */
    public static function update_courses($courses) {
        global $CFG, $DB, $USER;

        // Validate parameters
        $params = self::validate_parameters(self::update_courses_parameters(), array('courses' => $courses));

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:managecourses', $context);

        $succeeded = true;

        // Deal with the list of users.
        foreach ($params['courses'] as $courserecord) {
            if (empty($courserecord['courseid'])) {
                $succeeded = false;
                continue;
            }
            if (!$currentrecord = $DB->get_record('iomad_courses', array('courseid' => $courserecord['courseid']))) {
                $succeeded = false;
            } else {
                // Replace the record with the new one.
                $courserecord['id'] = $currentrecord->id;
                if (!$DB->update_record('iomad_courses', $courserecord)) {
                    $succeeded = false;
                }

                // Fire an event for this.
                $eventother = array('iomadcourse' => $currentrecord);
                $event = \block_iomad_company_admin\event\company_course_updated::create(array('context' => context_system::instance(),
                                                                                               'objectid' => $currentrecord->id,
                                                                                               'userid' => $USER->id,
                                                                                               'other' => $eventother));
                $event->trigger();

            }
        }

        return $succeeded;
    }

   /**
     * block_iomad_company_admin_update_courses
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function update_courses_returns() {
        return new external_value(PARAM_BOOL, 'Success or failure');
    }

    /**
     * block_iomad_company_admin_get_course_info
     *
     * Return description of method parameters
     * @return external_function_parameters
     */
    public static function get_course_info_parameters() {
        return new external_function_parameters(
            array(
                'courrseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Course id'), 'List of course IDs', VALUE_DEFAULT, array()
                )
            )
        );
        //return new external_function_parameters(new external_value(PARAM_INT, 'Course id'), 'Course ID', VALUE_DEFAULT, 0);
    }

    /**
     * block_iomad_company_admin_get_course_info
     *
     * Implement get_departments
     * @param $comapnyid
     * @return array of department records.
     */
    public static function get_course_info($courseids = array()) {
        global $CFG, $DB;

        // Validate parameters
        $params = self::validate_parameters(self::get_course_info_parameters(), $courseids);

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:managecourses', $context);

        // Get course records
        if (empty($courseids)) {
            $courses = $DB->get_records('iomad_course');
        } else {
            $courses = $DB->get_records_list('iomad_course', 'id', $params['courseids']);
        }

        // convert to suitable format (I think)
        $courseinfo = array();
        foreach ($courses as $course) {
            $courseinfo[] = (array) $course;
        }

        return $courseinfo;
    }

   /**
     * block_iomad_company_admin_get_course_info
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function get_course_info_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                     'id' => new external_value(PARAM_INT, 'Record ID'),
                     'courseid' => new external_value(PARAM_INT, 'Course ID'),
                     'licensed' => new external_value(PARAM_BOOL, 'Course licensed'),
                     'shared' => new external_value(PARAM_INT, 'Course shared value'),
                     'validlength' => new external_value(PARAM_INT, 'Course training valid for in days'),
                     'warnexpire' => new external_value(PARAM_INT, 'Course days to warn before training expires'),
                     'warncompletion' => new external_value(PARAM_INT, 'Course days to warn if not completed in'),
                     'notifyperiod' => new external_value(PARAM_INT, 'Course warning email notify period'),
                )
            )
        );
    }

    /*  License calls **/

    /**
     * block_iomad_company_admin_get_license_info
     *
     * Return description of method parameters
     * @return external_function_parameters
     */
    public static function get_license_info_parameters() {
        return new external_function_parameters(
            array(
                'criteria' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'key' => new external_value(PARAM_ALPHA, 'the user column to search, expected keys (value format) are:
                                "id" (int) matching user id,
                                "name" (string) license name (Note: you can use % for searching but it may be considerably slower!),
                                "startdate" (int) license start date in unix time,
                                "expirydate" (int) license expiry date in unix time,
                                "companyid" (int) license company id,
                                "parentid"  (int) license parent id for split licenses,
                                "program"  (bool) license is program,
                                "instant"  (bool) license is instant,
                                "type"  (int) license type (0 = standard, 1 = reusable, 3 = educator),
                                "reference" license reference (Note: you can use % for searching but it may be considerably slower!)'),
                            'value' => new external_value(PARAM_RAW, 'the value to search')
                        )
                    ), 'the key/value pairs to be considered in user search. Values can not be empty.
                        Specify different keys only once (name => \'license1\', companyid => \'2\', ...) -
                        key occurences are forbidden.
                        The search is executed with AND operator on the criterias. Invalid criterias (keys) are ignored,
                        the search is still executed on the valid criterias.
                        You can search without criteria, but the function is not designed for it.
                        It could very slow or timeout. The function is designed to search some specific users.'
                )
            )
        );
    }

    /**
     * block_iomad_company_admin_get_license_info
     *
     * Implement get_departments
     * @param $comapnyid
     * @return array of department records.
     */
    public static function get_license_info($criteria = array()) {
        global $CFG, $DB;

        // Validate parameters
        $params = self::validate_parameters(self::get_license_info_parameters(), array('criteria' => $criteria));

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:view_licenses', $context);

        // Validate the criteria and retrieve the licenses.
        $licenses = array();
        $warnings = array();
        $sqlparams = array();
        $usedkeys = array();
        $sql = ' allocation > 0 ';

        foreach ($params['criteria'] as $criteriaindex => $criteria) {

            // Check that the criteria has never been used.
            if (array_key_exists($criteria['key'], $usedkeys)) {
                throw new moodle_exception('keyalreadyset', '', '', null, 'The key ' . $criteria['key'] . ' can only be sent once');
            } else {
                $usedkeys[$criteria['key']] = true;
            }

            $invalidcriteria = false;
            // Clean the parameters.
            $paramtype = PARAM_RAW;
            switch ($criteria['key']) {
                case 'id':
                case 'companyid':
                case 'parentid':
                case 'startdate':
                case 'expirydate':
                case 'type':
                    $paramtype = PARAM_INT;
                    break;
                case 'program':
                case 'instant':
                    $paramtype = PARAM_BOOL;
                    break;
                case 'name':
                case 'reference':
                    $paramtype = PARAM_RAW;
                    break;
                default:
                    // Send back a warning that this search key is not supported in this version.
                    // This warning will make the function extandable without breaking clients.
                    $warnings[] = array(
                        'item' => $criteria['key'],
                        'warningcode' => 'invalidfieldparameter',
                        'message' =>
                            'The search key \'' . $criteria['key'] . '\' is not supported, look at the web service documentation'
                    );
                    // Do not add this invalid criteria to the created SQL request.
                    $invalidcriteria = true;
                    unset($params['criteria'][$criteriaindex]);
                    break;
            }

            if (!$invalidcriteria) {
                $cleanedvalue = clean_param($criteria['value'], $paramtype);

                $sql .= ' AND ';

                // Create the SQL.
                switch ($criteria['key']) {
                    case 'id':
                    case 'companyid':
                    case 'parentid':
                    case 'startdate':
                    case 'expirydate':
                    case 'program':
                    case 'type':
                    case 'instant':
                        $sql .= $criteria['key'] . ' = :' . $criteria['key'];
                        $sqlparams[$criteria['key']] = $cleanedvalue;
                        break;
                    case 'name':
                    case 'reference':
                        $sql .= $DB->sql_like($criteria['key'], ':' . $criteria['key'], false);
                        $sqlparams[$criteria['key']] = $cleanedvalue;
                        break;
                    default:
                        break;
                }
            }
        }

        $licenses = $DB->get_records_select('companylicense', $sql, $sqlparams, 'id ASC');

        return array('licenses' => $licenses, 'warnings' => $warnings);
    }

   /**
     * block_iomad_company_admin_get_license_info
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function get_license_info_returns() {
        return new external_single_structure(
            array('licenses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                              'id' => new external_value(PARAM_INT, 'license ID'),
                              'name' => new external_value(PARAM_TEXT, 'License name'),
                              'allocation' => new external_value(PARAM_INT, 'Number of license slots'),
                              'validlength' => new external_value(PARAM_INT, 'Course access length (days)'),
                              'expirydate' => new external_value(PARAM_INT, 'License expiry date'),
                              'used' => new external_value(PARAM_INT, 'Number allocated'),
                              'companyid' => new external_value(PARAM_INT, 'Company id'),
                              'parentid' => new external_value(PARAM_INT, 'Parent license id'),
                         )
                     )
                 ),
                'warnings' => new external_warnings('always set to \'key\'', 'faulty key name')
            )
        );
    }

    /**
     * block_iomad_company_admin_get_license_info
     *
     * Return description of method parameters
     * @return external_function_parameters
     */
    public static function create_licenses_parameters() {
        return new external_function_parameters(
            array(
                'licenses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                             'name' => new external_value(PARAM_TEXT, 'License name'),
                             'allocation' => new external_value(PARAM_INT, 'Number of license slots'),
                             'validlength' => new external_value(PARAM_INT, 'Course access length (days)'),
                             'startdate' => new external_value(PARAM_INT, 'Date from which the liucense is available (int = timestamp) '),
                             'expirydate' => new external_value(PARAM_INT, 'License expiry date (int = timestamp)'),
                             'used' => new external_value(PARAM_INT, 'Number how often the lic can be allocated'),
                             'companyid' => new external_value(PARAM_INT, 'Company id'),
                             'parentid' => new external_value(PARAM_INT, 'Parent license id'),
                             'type' => new external_value(PARAM_INT, 'License type - 0 = standard, 1 = reusable, 2 = standard educator, 3 = reusable educator'),
                             'program' => new external_value(PARAM_INT, 'Program pf courses 0 = no, 1 = yes'),
                             'reference' => new external_value(PARAM_TEXT, 'License reference'),
                             'instant' => new external_value(PARAM_INT, 'Instant access - 0 = no, 1 = yes'),
                             'courses' => new external_multiple_structure(
                                 new external_single_structure(
                                        array(
                                            'courseid'  => new external_value(PARAM_INT, 'Course ID'),
                                        )
                                 ),'one or many course IDs', VALUE_REQUIRED
                            )
                        ), 'one or many licenses'
                    )
                )
            )
        );
    }

    /**
     * block_iomad_company_admin_create_license
     *
     * Implement get_departments
     * @param $comapnyid
     * @return array of department records.
     */
    public static function create_licenses($licenses = array()) {
        global $DB, $USER;

        $params = self::validate_parameters(self::create_licenses_parameters(), array('licenses' => $licenses));

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:edit_licenses', $context);

        // Array to return newly created records
        $licenseinfo = array();

        foreach ($params['licenses'] as $license) {

            // Is this a program license?
            if (!empty($license->program)) {
                // Fix the actual allocation if so.
                $license['allocation'] = $license['allocation'] * count($license['courses']);
            }

            // Create the License record
            $licenseid = $DB->insert_record('companylicense', $license);

            // Deal with the courses
            foreach ($license['courses'] as $course) {
                $DB->insert_record('companylicense_courses', array('licenseid' => $licenseid,
                                                                          'courseid' => $course['courseid']));
            }

            // Create an event to deal with an parent license allocations.
            $newlicense = $DB->get_record('companylicense', array('id' => $licenseid));
            $eventother = array('licenseid' => $licenseid,
                                'parentid' => $newlicense->parentid);

            $event = \block_iomad_company_admin\event\company_license_created::create(array('context' => context_system::instance(),
                                                                                            'userid' => $USER->id,
                                                                                            'objectid' => $licenseid,
                                                                                            'other' => $eventother));

            $event->trigger();
            $newlicense->courses = $license['courses'];
            $licenseinfo[] = (array)$newlicense;
        }

        return $licenseinfo;
    }

    /**
     * block_iomad_company_admin_create_licenses
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function create_licenses_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                     'id' => new external_value(PARAM_INT, 'license ID'),
                     'name' => new external_value(PARAM_TEXT, 'License name'),
                     'allocation' => new external_value(PARAM_INT, 'Number of license slots'),
                     'validlength' => new external_value(PARAM_INT, 'Course access length (days)'),
                     'startdate' => new external_value(PARAM_INT, 'Date from which the liucense is available (int = timestamp) '),
                     'expirydate' => new external_value(PARAM_INT, 'License expiry date (int = timestamp)'),
                     'used' => new external_value(PARAM_INT, 'Number allocated'),
                     'companyid' => new external_value(PARAM_INT, 'Company id'),
                     'parentid' => new external_value(PARAM_INT, 'Parent license id'),
                     'type' => new external_value(PARAM_INT, 'License type - 0 = standard, 1 = reusable, 2 = standard educator, 3 = reusable educator'),
                     'program' => new external_value(PARAM_INT, 'Program pf courses 0 = no, 1 = yes'),
                     'reference' => new external_value(PARAM_TEXT, 'License reference'),
                     'instant' => new external_value(PARAM_INT, 'Instant access - 0 = no, 1 = yes'),
                     'courses' => new external_multiple_structure(
                         new external_single_structure(
                                array(
                                    'courseid'  => new external_value(PARAM_INT, 'Course ID'),
                                )
                         ),'one or many course IDs', VALUE_REQUIRED
                    )
                ), 'one or many licenses'
            )
        );
    }

    /**
     * block_iomad_company_admin_edit_licenses
     *
     * Return description of method parameters
     * @return external_function_parameters
     */
    public static function edit_licenses_parameters() {
        return new external_function_parameters(
            array(
                'licenses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                             'id' => new external_value(PARAM_INT, 'license ID'),
                             'name' => new external_value(PARAM_TEXT, 'License name'),
                             'allocation' => new external_value(PARAM_INT, 'Number of license slots'),
                             'validlength' => new external_value(PARAM_INT, 'Course access length (days)'),
                             'expirydate' => new external_value(PARAM_INT, 'License expiry date'),
                             'used' => new external_value(PARAM_INT, 'Number allocated'),
                             'companyid' => new external_value(PARAM_INT, 'Company id'),
                             'parentid' => new external_value(PARAM_INT, 'Parent license id'),
                             'type' => new external_value(PARAM_INT, 'License type - 0 = standard, 1 = reusable, 2 = standard educator, 3 = reusable educator'),
                             'program' => new external_value(PARAM_INT, 'Program pf courses 0 = no, 1 = yes'),
                             'reference' => new external_value(PARAM_TEXT, 'License reference'),
                             'instant' => new external_value(PARAM_INT, 'Instant access - 0 = no, 1 = yes'),
                             'courses' => new external_multiple_structure(
                                 new external_single_structure(
                                        array(
                                            'courseid'  => new external_value(PARAM_INT, 'Course ID'),
                                        )
                                 ),'one or many course IDs', VALUE_REQUIRED
                            )
                        ), 'one or many licenses'
                    )
                )
            )
        );
    }

    /**
     * block_iomad_company_admin_edit_licenses
     *
     * Implement create_company
     * @param $company
     * @return boolean success
     */
    public static function edit_licenses($licenses) {
        global $CFG, $DB, $USER;

        // Validate parameters
        $params = self::validate_parameters(self::edit_licenses_parameters(), array('licenses' => $licenses));

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:edit_licenses', $context);

        foreach ($params['licenses'] as $license) {

            $id = $license['id'];

            // does this company exist
            if (!$oldlicense = $DB->get_record('companylicense', array('id' => $id))) {
                throw new invalid_parameter_exception("License id=$id does not exist");
            }


            // Deal with course allocations if there are any.
            // Capture them for checking.
            $oldcourses = $DB->get_records('companylicense_courses', array('licenseid' => $licenseid), null, 'courseid');
            // Clear down all of them initially.
            $DB->delete_records('companylicense_courses', array('licenseid' => $licenseid));
            foreach ($license['courses'] as $course) {
                $DB->insert_record('companylicense_courses', array('licenseid' => $licenseid,
                                                                          'courseid' => $course['courseid']));
            }

            // Create an event to deal with an parent license allocations.
            $eventother = array('licenseid' => $oldlicense->id,
                                'parentid' => $oldlicense->parentid);
            $eventother['oldcourses'] = json_encode($oldcourses);
            if ($oldlicense->program != $license->program) {
                $eventother['programchange'] = true;
            }
            if ($oldlicense->startdate != $license->startdate) {
                $eventother['oldstartdate'] = $license->startdate;
            }
            if ($oldlicense->type != $license->type) {
                $eventother['educatorchange'] = true;
            }

            // Copy whatever vars we have
            foreach ($license as $key => $value) {
                $oldlicense->$key = $value;
            }

            // Update the company record
            $DB->update_record('companylicense', $oldlicense);

            // Create an event to deal with an parent license allocations.
            $eventother = array('licenseid' => $oldlicense->id,
                                'parentid' => $oldlicense->parentid);

            $event = \block_iomad_company_admin\event\company_license_created::create(array('context' => context_system::instance(),
                                                                                            'userid' => $USER->id,
                                                                                            'objectid' => $oldlicense->id,
                                                                                            'other' => $eventother));
            $event->trigger();
        }

        return true;
    }

    /**
     * block_iomad_company_admin_edit_licenses
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function edit_licenses_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                     'id' => new external_value(PARAM_INT, 'license ID'),
                     'name' => new external_value(PARAM_TEXT, 'License name'),
                     'allocation' => new external_value(PARAM_INT, 'Number of license slots'),
                     'validlength' => new external_value(PARAM_INT, 'Course access length (days)'),
                     'startdate' => new external_value(PARAM_INT, 'Date from which the liucense is available (int = timestamp) '),
                     'expirydate' => new external_value(PARAM_INT, 'License expiry date (int = timestamp)'),
                     'used' => new external_value(PARAM_INT, 'Number allocated'),
                     'companyid' => new external_value(PARAM_INT, 'Company id'),
                     'parentid' => new external_value(PARAM_INT, 'Parent license id'),
                     'type' => new external_value(PARAM_INT, 'License type - 0 = standard, 1 = reusable, 2 = standard educator, 3 = reusable educator'),
                     'program' => new external_value(PARAM_INT, 'Program pf courses 0 = no, 1 = yes'),
                     'reference' => new external_value(PARAM_TEXT, 'License reference'),
                     'instant' => new external_value(PARAM_INT, 'Instant access - 0 = no, 1 = yes'),
                     'courses' => new external_multiple_structure(
                         new external_single_structure(
                                array(
                                    'courseid'  => new external_value(PARAM_INT, 'Course ID'),
                                )
                         )
                    ),
                )
            )
        );
    }

    /**
     * block_iomad_company_admin_edit_licenses
     *
     * Return description of method parameters
     * @return external_function_parameters
     */
    public static function delete_licenses_parameters() {
        return new external_function_parameters(
            array(
                'licenses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                             'id' => new external_value(PARAM_INT, 'license ID'),
                        )
                    )
                )
            )
        );
    }

    /**
     * block_iomad_company_admin_delete_licenses
     *
     * Implement create_company
     * @param $company
     * @return boolean success
     */
    public static function delete_licenses($licenses) {
        global $CFG, $DB, $USER;

        // Validate parameters
        $params = self::validate_parameters(self::delete_licenses_parameters(), array('licenses' => $licenses));

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:edit_licenses', $context);

        foreach ($params['licenses'] as $license) {

            $id = $license['id'];

            // does this license exist
            if (!$oldlicense = $DB->get_record('companylicense', array('id' => $id))) {
                throw new invalid_parameter_exception("License id=$id does not exist");
            }

            $DB->delete_records('companylicense', array('id' => $id));

            // Create an event to deal with parent license allocations.
            $eventother = array('licenseid' => $oldlicense->id,
                                'parentid' => $oldlicense->parentid);

            $event = \block_iomad_company_admin\event\company_license_deleted::create(array('context' => context_system::instance(),
                                                                                            'userid' => $USER->id,
                                                                                            'objectid' => $oldlicense->parentid,
                                                                                            'other' => $eventother));
            $event->trigger();
        }

        return true;
    }

    /**
     * block_iomad_company_admin_create_companies
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function delete_licenses_returns() {
        return new external_value(PARAM_BOOL, 'Success or failure');
    }

    /**
     * block_iomad_company_admin_allocate_licenses
     *
     * Return description of method parameters
     * @return external_function_parameters
     */
    public static function allocate_licenses_parameters() {
        return new external_function_parameters(
            array(
                'licenses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                             'licenseid' => new external_value(PARAM_INT, 'License ID'),
                             'userid' => new external_value(PARAM_INT, 'User ID'),
                             'licensecourseid' => new external_value(PARAM_INT, 'Course ID'),
                        )
                    )
                )
            )
        );
    }

    /**
     * block_iomad_company_admin_allocate_licenses
     *
     * Implement create_company
     * @param $company
     * @return boolean success
     */
    public static function allocate_licenses($licenses) {
        global $CFG, $DB;

        // Validate parameters
        $params = self::validate_parameters(self::allocate_licenses_parameters(), array('licenses' => $licenses));

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:allocate_licenses', $context);

        // Get the time right now.
        $timestamp = time();

        foreach ($params['licenses'] as $license) {

            $licenseid = $license['licenseid'];

            // Does this license exist?
            if (!$oldlicense = $DB->get_record('companylicense', array('id' => $licenseid))) {
                throw new invalid_parameter_exception("License id=$licenseid does not exist");
            }

            // What about the company?
            if (!$companyrec = $DB->get_record('company', array('id' => $oldlicense->companyid))) {
                throw new invalid_parameter_exception("Company does not match for license id=$licenseid");
            }

            // The user?
            if (!$user = $DB->get_record('user', array('id' => $license['userid'], 'deleted' => 0))) {
                throw new invalid_parameter_exception("User id=" . $license['userid'] ." does not exist");
            }
            if ($user->suspended == 1) {
                throw new invalid_parameter_exception("User id=" . $license['userid'] ." is suspended");
            }

            // The course?
            if (!$course = $DB->get_record('course', array('id' => $license['licensecourseid']))) {
                throw new invalid_parameter_exception("Course id=" . $license['licensecourseid'] ." does not exist");
            }

            // Does the license include this course?
            if (!$DB->get_record('companylicense_courses', array('courseid' => $license['licensecourseid'],
                                                                 'licenseid' => $licenseid))) {
                throw new invalid_parameter_exception("Course id=" . $license['licensecourseid'] ." is not inculded in license id $licenseid");
            }

            // Has the license expired?
            if ($oldlicense->expirydate < $timestamp) {
                throw new invalid_parameter_exception("License id=$licenseid has expired");
            }

            // Is there any space left?
            if ($oldlicense->allocation <= $oldlicense->used) {
                throw new invalid_parameter_exception("License id=$licenseid has no free slots");
            }

            // Are we double allocating?
            $license['isusing'] = 0;
            if ($DB->get_record('companylicense_users', $license)) {
                throw new invalid_parameter_exception("User id=" . $user->id ." already has an unused license for that course.");
            }

            // Set up the rest of the record.
            $license['issuedate'] = $timestamp;
            $DB->insert_record('companylicense_users', $license);

            // Create an event.
            $eventother = array('licenseid' => $licenseid,
                                'issuedate' =>time(),
                                'duedate' => 0);
            $event = \block_iomad_company_admin\event\user_license_assigned::create(array('context' => context_system::instance(),
                                                                                          'objectid' => $licenseid,
                                                                                          'courseid' => $course->id,
                                                                                          'userid' => $user->id,
                                                                                          'other' => $eventother));
            $event->trigger();
        }

        return true;
    }

    /**
     * block_iomad_company_admin_allocate_licenses
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function allocate_licenses_returns() {
        return new external_value(PARAM_BOOL, 'Success or failure');
    }

    /**
     * block_iomad_company_admin_unallocate_licenses
     *
     * Return description of method parameters
     * @return external_function_parameters
     */
    public static function unallocate_licenses_parameters() {
        return new external_function_parameters(
            array(
                'licenses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                             'licenseid' => new external_value(PARAM_INT, 'License ID'),
                             'userid' => new external_value(PARAM_INT, 'User ID'),
                             'licensecourseid' => new external_value(PARAM_INT, 'Course ID'),
                        )
                    )
                )
            )
        );
    }

    /**
     * block_iomad_company_admin_unallocate_licenses
     *
     * Implement create_company
     * @param $company
     * @return boolean success
     */
    public static function unallocate_licenses($licenses) {
        global $CFG, $DB;

        // Validate parameters
        $params = self::validate_parameters(self::unallocate_licenses_parameters(), array('licenses' => $licenses));

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:allocate_licenses', $context);

        // Get the time right now.
        $timestamp = time();

        foreach ($params['licenses'] as $license) {

            $licenseid = $license['licenseid'];

            // Does this license exist?
            if (!$oldlicense = $DB->get_record('companylicense', array('id' => $licenseid))) {
                throw new invalid_parameter_exception("License id=$licenseid does not exist");
            }

            // What about the company?
            if (!$companyrec = $DB->get_record('company', array('id' => $oldlicense->companyid))) {
                throw new invalid_parameter_exception("Company does not match for license id=$licenseid");
            }

            // The user?
            if (!$user = $DB->get_record('user', array('id' => $license['userid'], 'deleted' => 0))) {
                throw new invalid_parameter_exception("User id=" . $license['userid'] ." does not exist");
            }
            if ($user->suspended == 1) {
                throw new invalid_parameter_exception("User id=" . $license['userid'] ." is suspended");
            }

            // The course?
            if (!$course = $DB->get_record('course', array('id' => $license['licensecourseid']))) {
                throw new invalid_parameter_exception("Course id=" . $license['licensecourseid'] ." does not exist");
            }

            // Does the license include this course?
            if (!$DB->get_record('companylicense_courses', array('courseid' => $license['licensecourseid'],
                                                                 'licenseid' => $licenseid))) {
                throw new invalid_parameter_exception("Course id=" . $license['licensecourseid'] ." is not inculded in license id $licenseid");
            }

            // Has the license expired?
            if ($oldlicense->expirydate < $timenow) {
                throw new invalid_parameter_exception("License id=$licenseid has expired");
            }

            // Is there any space left?
            if ($oldlicense->allocation <= $oldlicense->used) {
                throw new invalid_parameter_exception("License id=$licenseid has no free slots");
            }

            // Can we remove this?
            $license['isusing'] = 0;
            if (!$allocationrec = $DB->get_record('companylicense_users', $license)) {
                throw new invalid_parameter_exception("User id=" . $user->id ." has used the license for that course.");
            }

            // Set up the rest of the record.
            $DB->delete_record('companylicense_users', array('id' => $allocationrec->id));

            // Create an event.
            $eventother = array('licenseid' => $licenseid);
            $event = \block_iomad_company_admin\event\user_license_unassigned::create(array('context' => context_system::instance(),
                                                                                            'objectid' => $licenseid,
                                                                                            'courseid' => $course->id,
                                                                                            'userid' => $user->id,
                                                                                            'other' => $eventother));
            $event->trigger();
        }

        return true;
    }

    /**
     * block_iomad_company_admin_unallocate_licenses
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function unallocate_licenses_returns() {
        return new external_value(PARAM_BOOL, 'Success or failure');
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function enrol_users_parameters() {
        return new external_function_parameters(
                array(
                    'enrolments' => new external_multiple_structure(
                            new external_single_structure(
                                    array(
                                        'roleid' => new external_value(PARAM_INT, 'Role to assign to the user'),
                                        'userid' => new external_value(PARAM_INT, 'The user that is going to be enrolled'),
                                        'courseid' => new external_value(PARAM_INT, 'The course to enrol the user role in'),
                                        'timestart' => new external_value(PARAM_INT, 'Timestamp when the enrolment start', VALUE_OPTIONAL),
                                        'timeend' => new external_value(PARAM_INT, 'Timestamp when the enrolment end', VALUE_OPTIONAL),
                                        'suspend' => new external_value(PARAM_INT, 'set to 1 to suspend the enrolment', VALUE_OPTIONAL),
                                        'quantity' => new external_value(PARAM_INT, 'Number of items purchased.', VALUE_OPTIONAL)
                                    )
                            )
                    )
                )
        );
    }

    /**
     * Enrolment of users.
     *
     * Function throw an exception at the first error encountered.
     * @param array $enrolments  An array of user enrolment
     * @since Moodle 2.2
     */
    public static function enrol_users($enrolments) {
        global $DB, $CFG;

        require_once($CFG->libdir . '/enrollib.php');

        $params = self::validate_parameters(self::enrol_users_parameters(),
                array('enrolments' => $enrolments));

        //$transaction = $DB->start_delegated_transaction(); // Rollback all enrolment if an error occurs
                                                           // (except if the DB doesn't support it).

        // Get the current timestamp.
        $runtime = time();
        // Retrieve the manual enrolment plugin.
        $enrol = enrol_get_plugin('manual');
        if (empty($enrol)) {
            throw new moodle_exception('manualpluginnotinstalled', 'enrol_manual');
        }

        foreach ($params['enrolments'] as $enrolment) {
            // Get the company for the user.
            if (!$company = company::by_userid($enrolment['userid'])) {
                continue;
            }

            if (!$user = $DB->get_record('user', array('id' => $enrolment['userid']))) {
                continue;
            }
            // Is this a licensed course?
            if ($DB->get_record('iomad_courses', array('courseid' => $enrolment['courseid'], 'licensed' => 1))) {
                if (empty($enrolment['timestart'])) {
                    $enrolment['timestart'] = $runtime;
                }

                // Do we have a default access period?
                if (empty($enrolment['timeend'])) {
                    if (!empty($CFG->commerce_admin_default_license_access_length)) {
                        $enrolment['timeend'] = $runtime + $CFG->commerce_admin_default_license_access_length * 24 * 60 * 60;
                    } else {
                        // Set it to 30.
                        $enrolment['timeend'] = $runtime + 30 * 24 * 60 * 60;
                    }
                }

                // How about a default shelf life?
                if (!empty($CFG->commerce_admin_default_license_shelf_life)) {
                    $shelflife = $enrolment['timestart'] + $CFG->commerce_admin_default_license_shelf_life * 24 * 60 * 60;
                } else {
                    $shelflife =  $enrolment['timeend'] - $enrolment['timestart'];
                }

                // Create the license record.
                $licenserec = array('name' => $enrolment['userid'] . '-' . $enrolment['courseid'] . '-' . $enrolment['timestart'],
                                    'allocation' => $enrolment['quantity'],
                                    'validlength' => $shelflife,
                                    'startdate' => $enrolment['timestart'],
                                    'expirydate' => $enrolment['timeend'],
                                    'companyid' => $company->id,
                                    'instant' => true);
                $licenseid = $DB->insert_record('companylicense', $licenserec);
                $DB->insert_record('companylicense_courses', array('licenseid' => $licenseid, 'courseid' => $enrolment['courseid']));

                // Fire the license create event.
                $eventother = array('licenseid' => $licenseid,
                                    'parentid' => 0);

                $event = \block_iomad_company_admin\event\company_license_created::create(array('context' => context_system::instance(),
                                                                                                'userid' => $user->id,
                                                                                                'objectid' => $licenseid,
                                                                                                'other' => $eventother));
                $event->trigger();

                if ($enrolment['quantity'] == 1) {
                    // Allocate the license to the user.
                    $recordarray = array('licensecourseid' => $enrolment['courseid'],
                                         'userid' => $user->id,
                                         'licenseid' => $licenseid,
                                         'issuedate' => $runtime,
                                         'isusing' => 0);

                    $recordarray['id'] = $DB->insert_record('companylicense_users', $recordarray);
                    // Fire that event.
                    $eventother = array('licenseid' => $licenseid,
                                         'issuedate' => $runtime,
                                        'duedate' => $enrolment['timestart']);
                    $event = \block_iomad_company_admin\event\user_license_assigned::create(array('context' => context_course::instance($enrolment['courseid']),
                                                                                                  'objectid' => $recordarray['id'],
                                                                                                  'courseid' => $enrolment['courseid'],
                                                                                                  'userid' => $enrolment['userid'],
                                                                                                  'other' => $eventother));
                    $event->trigger();
                }
            } else {
                company_user::enrol($user, array($enrolment['courseid']), $company->id);
            }
        }

        return true;
    }

    /**
     * Returns description of method result value.
     *
     * @return null
     * @since Moodle 2.2
     */
    public static function enrol_users_returns() {
        return new external_value(PARAM_BOOL, 'True user enrolments succeeds');    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function check_token_parameters() {
        return new external_function_parameters(
            array(
                new external_single_structure(
                    array(
                          'username' => new external_value(PARAM_TEXT, 'The user that is going to be enrolled'),
                          'token' => new external_value(PARAM_TEXT, 'The user moodle session key'),
                    )
                )
            )
        );
    }

    /**
     * Users session check.
     *
     * Function throw an exception at the first error encountered.
     * @param array $enrolments  An array of user enrolment
     * @since Moodle 2.2
     */
    public static function check_token($token) {
        global $DB, $CFG;

        $params = self::validate_parameters(self::check_token_parameters(),
                array($token));

        if (!$userrec = $DB->get_record('user', array('username' => $token['username']))) {
            $result = array();
            $result['status'] = false;
            $result['warnings'] = array(array('item' => 'username',
                                              'username' => $token['username'],
                                              'warningcode' => 'userdoesntexist',
                                              'message' => "user doesn't exist"));
            return $result;
        }

        if (!$DB->get_record_select('company_transient_tokens', 'userid = :userid AND expires > :time', array('userid' => $userrec->id, 'time' => time()))) {
            $result = array();
            $result['status'] = false;
            $result['warnings'] = array(array('item' => 'token',
                                              'token' => $token['token'],
                                              'warningcode' => 'tokennotvalid',
                                               'message' => "Token is invalid"));
            return $result;
        }

        $result = array();
        $result['status'] = true;
        $result['warnings'] = array();
        return $result;
    }

    /**
     * Returns description of method result value.
     *
     * @return null
     * @since Moodle 2.2
     */
    public static function check_token_returns() {
        return  new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'Status: true only if token is valid'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function sync_users_parameters() {
        return new external_function_parameters(
            array('source' => new external_value(PARAM_URL, 'The user that is going to be enrolled'),
            )
        );
    }

    /**
     * Users sync call.
     *
     * Function throw an exception at the first error encountered.
     * @param array $enrolments  An array of user enrolment
     * @since Moodle 2.2
     */
    public static function sync_users($source) {
        global $DB, $CFG, $_POST;

        require_capability('moodle/user:update', context_system::instance());

        $params = self::validate_parameters(self::sync_users_parameters(),
                array('source' => $source));


        if (!empty($CFG->commerce_externalshop_url)) {
            // Do all companies have access?
            if (empty($CFG->commerce_admin_enableall)) {
                $companies = $DB->get_records('company', array('ecommerce' => 1));
            } else {
                $companies = $DB->get_records('company');
            }
            // Do any have their own shop enabled?
            foreach ($companies as $id => $company) {
                $name = "commerce_externalshop_url_$id";
                if (!empty($CFG->$name) && $CFG->$name != $params['source']) {
                    // Remove if it doesn't match this one.
                    unset($companies[$id]);
                }
            }
        } else {
            return true;
        }
        if (empty($companies)) {
            return true;
        }

        $companysql = " AND cu.companyid IN (" . join(',', array_keys($companies)) . ") ";
        $users = $DB->get_records_sql("SELECT distinct cu.userid from {company_users} cu
                                       JOIN {user} u ON (cu.userid = u.id)
                                       WHERE u.deleted = 0
                                       $companysql");
        foreach ($users as $user) {
            \core\event\user_updated::create_from_userid($user->userid)->trigger();
        }

        $result = array();
        $result['status'] = true;
        $result['warnings'] = array();
        return $result;
    }

    /**
     * Returns description of method result value.
     *
     * @return null
     * @since Moodle 2.2
     */
    public static function sync_users_returns() {
        return  new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'Status: true only if token is valid'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function restrict_capability_parameters() {
        return new external_function_parameters(
            array(
                'capability' => new external_value(PARAM_TEXT, 'The capability'),
                'roleid' => new external_value(PARAM_INT, 'Role ID'),
                'companyid' => new external_value(PARAM_INT, 'Company ID. Ignored if templateid is non-zero'),
                'allow' => new external_value(PARAM_BOOL, 'Set capability?'),
                'templateid' => new external_value(PARAM_INT, 'Template ID. Set to 0 if company restriction', VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Restrict capability
     * Non-zero $templateid identifies template rather than real company
     *
     * @param string $capability
     * @param int $roleid
     * @param int $companyid
     * @param bool $allow
     * @param int $templateid
     */
    public static function restrict_capability($capability, $roleid, $companyid, $allow, $templateid = 0) {
        global $CFG, $DB;

        $params = self::validate_parameters(self::restrict_capability_parameters(), [
            'capability' => $capability,
            'roleid' => $roleid,
            'companyid' => $companyid,
            'allow' => $allow,
            'templateid' => $templateid,
        ]);

        // Security.
        $context = context_system::instance();
        iomad::require_capability('block/iomad_company_admin:restrict_capabilities', $context);

        if (empty($params['templateid'])) {

            // dealing with a company restriction.
            // if box is unticked (false) an entry is created (or kept)
            // if box is ticked (true) any entry is deleted.
            $restriction = $DB->get_record('company_role_restriction', [
                    'roleid' => $params['roleid'],
                    'companyid' => $params['companyid'],
                    'capability' => $params['capability'],
            ]);
            if (!$params['allow']) {
                if (!$restriction) {
                    $restriction = new stdClass();
                    $restriction->companyid = $params['companyid'];
                    $restriction->roleid = $params['roleid'];
                    $restriction->capability = $params['capability'];
                    $DB->insert_record('company_role_restriction', $restriction);
                }
            } else {
                if ($restriction) {
                    $DB->delete_records('company_role_restriction', ['id' => $restriction->id]);
                }
            }
        } else  {

            // Dealing with a template restriction.
            // if box is unticked (false) an entry is created (or kept)
            // if box is ticked (true) any entry is deleted.
            $restriction = $DB->get_record('company_role_templates_caps', [
                    'roleid' => $params['roleid'],
                    'templateid' => $params['templateid'],
                    'capability' => $params['capability'],
            ]);
            if (!$params['allow']) {
                if (!$restriction) {
                    $restriction = new stdClass();
                    $restriction->templateid = $params['templateid'];
                    $restriction->roleid = $params['roleid'];
                    $restriction->capability = $params['capability'];
                    $DB->insert_record('company_role_templates_caps', $restriction);
                }
            } else {
                if ($restriction) {
                    $DB->delete_records('company_role_templates_caps', array('id' => $restriction->id));
                }
            }
        }
        reload_all_capabilities();

        return true;
    }

    /**
     * Returns description for restrict_capability
     * @return external_description
     */
    public static function restrict_capability_returns() {
        return new external_value(PARAM_BOOL, 'True capability update succeeds');
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function capability_delete_template_parameters() {
        return new external_function_parameters(
            array(
                'templateid' => new external_value(PARAM_INT, 'Template ID.'),
            )
        );
    }

    /**
     * Delete capability template
     *
     * @param int $templateid
     */
    public static function capability_delete_template($templateid) {
        global $CFG, $DB;

        $params = self::validate_parameters(self::capability_delete_template_parameters(), [
            'templateid' => $templateid,
        ]);

        // Security.
        $context = context_system::instance();
        iomad::require_capability('block/iomad_company_admin:restrict_capabilities', $context);

        $DB->delete_records('company_role_templates_caps', ['templateid' => $params['templateid']]);
        $DB->delete_records('company_role_templates', ['id' => $params['templateid']]);

        return true;
    }

    /**
     * Returns description for capability_delete_template
     * @return external_description
     */
    public static function capability_delete_template_returns() {
        return new external_value(PARAM_BOOL, 'True capability update succeeds');
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function get_license_from_id_parameters() {
        return new external_function_parameters(
            array(
                'licenseid' => new external_value(PARAM_INT, 'License ID.'),
            )
        );
    }

    /**
     * Return license info (given license ID)
     *
     * @param int $licenseid
     */
    public static function get_license_from_id($licenseid) {
        global $CFG, $DB;

        $params = self::validate_parameters(self::get_license_from_id_parameters(), [
            'licenseid' => $licenseid,
        ]);

        // Security.
        $context = context_system::instance();
        iomad::require_capability('block/iomad_company_admin:allocate_licenses', $context);

        // Get license
        $license = $DB->get_record('companylicense', ['id' => $params['licenseid']], '*', MUST_EXIST);

        // Get license courses (with extra)
        $sql = 'SELECT co.id AS id, co.fullname AS fullname
            FROM {course} co JOIN {companylicense_courses} clc
            ON co.id = clc.courseid
            WHERE clc.licenseid = :licenseid
            ORDER BY co.fullname';
        $liccourses = $DB->get_records_sql($sql, ['licenseid' => $params['licenseid']]);

        // Licenses used?
        $license->allallocated = $license->used >= $license->allocation;

        return [
            'license' => $license,
            'courses' => array_values($liccourses),
        ];
    }

    /**
     * Returns description for get_license_info
     * @return external_description
     */
    public static function get_license_from_id_returns() {
        return new external_single_structure([
            'license' => new external_single_structure([
                'id' => new external_value(PARAM_INT, 'License ID'),
                'name' => new external_value(PARAM_TEXT, 'License name'),
                'allocation' => new external_value(PARAM_INT, 'Allocation'),
                'validlength' => new external_value(PARAM_INT, 'Valid length'),
                'startdate' => new external_value(PARAM_INT, 'Start date'),
                'expirydate' => new external_value(PARAM_INT, 'Expiry date'),
                'used' => new external_value(PARAM_INT, 'Used'),
                'companyid' => new external_value(PARAM_INT, 'Company ID'),
                'parentid' => new external_value(PARAM_INT, 'Parent ID'),
                'type' => new external_value(PARAM_INT, 'License type - 0 = standard, 1 = reusable, 2 = standard educator, 3 = reusable educator'),
                'program' => new external_value(PARAM_BOOL, 'Program'),
                'reference' => new external_value(PARAM_TEXT, 'Reference'),
                'instant' => new external_value(PARAM_BOOL, 'Instant'),
                'allallocated' => new external_value(PARAM_BOOL, 'All licenses allocated'),
            ]),
            'courses' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'Course ID'),
                    'fullname' => new external_value(PARAM_TEXT, 'Course full name'),
                ]),
                'List of available or program courses for License'
            )
        ]);
    }

}

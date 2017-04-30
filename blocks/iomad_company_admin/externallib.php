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
                'companyids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Company id'), 'List of company IDs', VALUE_DEFAULT, array()
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
    public static function get_companies($companyids = array()) {
        global $CFG, $DB;

        // Validate parameters
        $params = self::validate_parameters(self::get_companies_parameters(), array('companyids' => $companyids));

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:company_add', $context);

        // Get company records
        if (empty($companyids)) {
            $companies = $DB->get_records('company');
        } else {
            $companies = $DB->get_records_list('company', 'id', $params['companyids']);
        }

        // convert to suitable format (I think)
        $companyinfo = array();
        foreach ($companies as $company) {
            $companyinfo[] = (array) $company;
        }

        return $companyinfo;
    }

    /**
     * block_iomad_company_admin_get_companies
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function get_companies_returns() {
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
                )
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
                'departmentids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Company id'), 'List of company IDs', VALUE_DEFAULT, array()
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
    public static function get_departments($companyids = array()) {
        global $CFG, $DB;

        // Validate parameters
        $params = self::validate_parameters(self::get_department_parameters(), $companyids);

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:edit_all_departments', $context);

        // Get course records
        if (empty($companyids)) {
            $departments = $DB->get_records('department');
        } else {
            $departments = $DB->get_records_list('department', 'company', $params['companyids']);
        }

        // convert to suitable format (I think)
        $departmentinfo = array();
        foreach ($departments as $department) {
            $departmentinfo[] = (array) $department;
        }

        return $departmentinfo;
    }

     /**
     * block_iomad_company_admin_get_departments
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function get_departments_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                     'id' => new external_value(PARAM_INT, 'Department ID'),
                     'name' => new external_value(PARAM_TEXT, 'Department name'),
                     'shortname' => new external_value(PARAM_TEXT, 'Department short name'),
                     'company' => new external_value(PARAM_INT, 'Company ID'),
                     'parent' => new external_value(PARAM_INT, 'Department parent id'),
                )
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
        $params = self::validate_parameters(self::assign_users_parameters(), $users);

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:assign_company_manager', $context);

        $succeeded = true;

        // Deal with the list of users.
        foreach ($users as $userrecord) {
            if (empty($userrecord->userid) || empty($userrecord->companyid)) {
                $succeeded = false;
                continue;
            }
            $company = new company($userrecord->companyid);
            if (!$company->assign_user_to_company($userrecord->userid,
                                                  $userrecord->departmentid,
                                                  $userrecord->managertype,
                                                  true)) {
                $succeeded = false;
            }

            // Create an event for this.
            $managertypes = $company->get_managertypes();
            $eventother = array('companyname' => $company->get_name(),
                                'companyid' => $company->id,
                                'usertype' => $userrecord->managertype,
                                'usertypename' => $managertypes[$userrecord->managertype]);
            $event = \block_iomad_company_admin\event\company_user_assigned::create(array('context' => context_system::instance(),
                                                                                            'objectid' => $company->id,
                                                                                            'userid' => $adduser->id,
                                                                                            'other' => $eventother));
            $event->trigger();
        }
        return $succeeded;
    }

   /**
     * block_iomad_company_admin_assign_users
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function assign_users_returns() {
        return new external_value(PARAM_BOOL, 'Success or failure');
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
        $params = self::validate_parameters(self::assign_users_parameters(), $users);

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:assign_company_manager', $context);

        $succeeded = true;

        // Deal with the list of users.
        foreach ($users as $userrecord) {
            if (empty($userrecord->userid) || empty($userrecord->companyid)) {
                $succeeded = false;
                continue;
            }
            $company = new company($userrecord->companyid);
            if (!$company->assign_user_to_department($userrecord->departmentid,
                                                     $userrecord->userid,
                                                     $userrecord->managertype,
                                                     true)) {
                $succeeded = false;
            }

            // Create an event for this.
            $managertypes = $company->get_managertypes();
            $eventother = array('companyname' => $company->get_name(),
                                'companyid' => $company->id,
                                'usertype' => $userrecord->managertype,
                                'usertypename' => $managertypes[$userrecord->managertype]);
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
        $params = self::validate_parameters(self::assign_users_parameters(), $users);

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:assign_company_manager', $context);

        $succeeded = true;

        // Deal with the list of users.
        foreach ($users as $userrecord) {
            if (empty($userrecord->userid) || empty($userrecord->companyid)) {
                $succeeded = false;
                continue;
            }
            $company = new company($userrecord->companyid);
            if (!$company->unassign_user_from_company($userrecord->userid, true)) {
                $succeeded = false;
            }

            // Create an event for this.
            $managertypes = $company->get_managertypes();
            $eventother = array('companyname' => $company->get_name(),
                                'companyid' => $company->id,
                                'usertype' => $userrecord->usertype,
                                'usertypename' => $managertypes[$roletype]);
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
     * block_iomad_company_admin_assign_courses
     *
     * Implement assign_courses
     * @param $comapnyid
     * @return array of department records.
     */
    public static function assign_courses($courses) {
        global $CFG, $DB;

        // Validate parameters
        $params = self::validate_parameters(self::assign_courses_parameters(), $courses);

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:managecourses', $context);

        $succeeded = true;

        // Deal with the list of users.
        foreach ($courses as $courserecord) {
            if (empty($courserecord->courseid) || empty($courserecord->companyid)) {
                $succeeded = false;
                continue;
            }
            if (!$course = $DB->get_record('course', array('id' => $courserecord->courseid))) {
                $succeeded = false;
            } else {
                $company = new company($courserecord->companyid);
                $company->add_course($course, $courserecord->departmentid, $courserecord->owned, $courserecord->licensed);
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
        $params = self::validate_parameters(self::assign_courses_parameters(), $courses);

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:managecourses', $context);

        $succeeded = true;

        // Deal with the list of users.
        foreach ($courses as $courserecord) {
            if (empty($courserecord->courseid) || empty($courserecord->companyid)) {
                $succeeded = false;
                continue;
            }
            if (!$course = $DB->get_record('course', array('id' => $courserecord->courseid))) {
                $succeeded = false;
            } else {
                company::remove_course($course, $courserecord->companyid);
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
        global $CFG, $DB;

        // Validate parameters
        $params = self::validate_parameters(self::assign_courses_parameters(), $courses);

        // Get/check context/capability
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/iomad_company_admin:managecourses', $context);

        $succeeded = true;

        // Deal with the list of users.
        foreach ($courses as $courserecord) {
            if (empty($courserecord->courseid)) {
                $succeeded = false;
                continue;
            }
            if (!$currentrecord = $DB->get_record('iomad_courses', array('courseid' => $courserecord->courseid))) {
                $succeeded = false;
            } else {
                // Replace the record with the new one.
                $courserecord->id = $currentrecord->id;
                if (!$DB->update_record('iomad_courses', $courserecord)) {
                    $succeeded = false;
                }
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
        $params = self::validate_parameters(self::assign_courses_parameters(), $courseids);

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
}

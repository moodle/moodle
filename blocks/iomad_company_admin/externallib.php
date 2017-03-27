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

        return true;
    }

    /**
     * block_iomad_company_admin_create_companies
     *
     * Returns description of method result value
     * @return external_description
     */
    public static function create_companies_returns() {
        return new external_value(PARAM_BOOL, 'Success or failure');
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
}


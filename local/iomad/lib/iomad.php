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

require_once(dirname(__FILE__) . '/../../../config.php');
require_once(dirname(__FILE__) . '/company.php');
require_once(dirname(__FILE__) . '/user.php');

require_once($CFG->dirroot.'/lib/formslib.php');

class iomad {

    /**
     * Gets the current users company ID depending on 
     * if the user is an admin and editing a company or is a
     * company user tied to a company.
     * @param $context - stdclass()
     * @returns int
     */
    public static function get_my_companyid($context, $required=true) {
        global $SESSION, $USER;
        // are we logged in?
        if (empty($USER->id)) {
            return -1;
        }

        // Set the companyid to bypass the company select form if possible.
        if (!empty($SESSION->currenteditingcompany)) {
            $companyid = $SESSION->currenteditingcompany;
        } else if (self::is_company_user()) {
            $companyid = self::companyid();
        } else if (self::has_capability('block/iomad_company_admin:edit_departments', $context) && $required) {
            redirect(new moodle_url('/local/iomad_dashboard/index.php'),
                                     get_string('pleaseselect', 'block_iomad_company_admin'));
        } else {
            $companyid = 0;
        }
        return $companyid;
    }

    /**
     * Check to see if a user is associated to a company.
     *
     * Returns int or false;
     *
     **/
    public static function is_company_user () {
        global $USER, $DB, $SESSION;

        if (empty($USER->id) && empty($SESSION->currenteditingcompany)) {
            // We are installing.  Go no further.
            return false;
        }

        if (!empty($SESSION->currenteditingcompany)) {
            return $SESSION->currenteditingcompany;
        } else if ($usercompanies = $DB->get_records('company_users', array('userid' => $USER->id), 'id', 'id,companyid', 0, 1)) {
            $usercompany = array_pop($usercompanies);
            return $usercompany->companyid;
        } else {
            return false;
        }
    }
    /**
     * Check to see if a user is a manager in a company.
     *
     * Returns int or false;
     *
     **/
    public static function is_company_admin () {
        global $USER, $DB;

        if ($usercompany = $DB->get_record('company_users', array('userid' => $USER->id))) {
            if ($usercompany->managertype > 0) {
                return $usercompany->companyid;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Get a users company id.
     *
     * Returns int;
     *
     **/
    public static function companyid() {
        global $USER;

        if ( self::is_company_user() ) {
            self::load_company();
            return $USER->company->id;
        }
        return 0;
    }

    /**
     * Get a users company shortname.
     *
     * Returns text;
     *
     **/
    public static function companyshortname() {
        global $USER;

        if ( self::is_company_user() ) {
            self::load_company();
            return $USER->company->shortname;
        }
        return "";
    }

    /**
     * Set up a users company in their profile.
     *
     **/
    public static function load_company() {
        global $USER;

        if (!isset($USER->company->id)) {
            if (self::is_company_user()) {
                $company = company::by_userid($USER->id);
                $fields = 'id, shortname, name';
                $cssfields = implode(',', $company->cssfields);
                if ($cssfields) {
                    $fields .= ', ' . $cssfields;
                }

                $USER->company = $company->get( $fields );
                $USER->company->logo_filename = $company->get_logo_filename();
            }
        }
    }

    /**
     * Get the company Custom CSS given an ID.
     *
     * Parameters = $companyid = int;
     * 
     * Returns text;
     **/
     public static function get_company_customcss($companyid) {
         global $DB;

         if ($companycustomcss = $DB->get_field('company', 'customcss', array('id' => $companyid))) {
             return $companycustomcss;
         } else {
             return '';
         }
     }

     /**
      * Get the company main colour given an ID.
      *
      * Parameters = $companyid = int;
      * 
      * Returns text;
      **/
     public static function get_company_maincolor($companyid) {
         global $DB;

         if ($companyothercss = $DB->get_field('company', 'maincolor', array('id' => $companyid))) {
             return 'body {color: '.$companyothercss. ' !important}';
         } else {
             return '';
         }
     }

     /**
      * Get the company heading colour given an ID.
      *
      * Parameters = $companyid = int;
      * 
      * Returns text;
      **/
     public static function get_company_headingcolor($companyid) {
         global $DB;

         if ($companyothercss = $DB->get_field('company', 'headingcolor', array('id' => $companyid))) {
             return '.block .header .title h2, .block .content h3 {color: '.$companyothercss.' !important}';
         } else {
             return '';
         }
     }

     /**
      * Get the company link colour given an ID.
      *
      * Parameters = $companyid = int;
      * 
      * Returns text;
      **/
     public static function get_company_linkcolor($companyid) {
         global $DB;

         if ($companyothercss = $DB->get_field('company', 'linkcolor', array('id' => $companyid))) {
             return 'a {color: '.$companyothercss.' !important}';
         } else {
             return '';
         }
     }

    /**
     * SQL text processing to add a company course table join
     *
     * Parameters - $alias = text;
     *
     * Returns text;
     *
     **/
    public static function join_company_course($alias = "{course}") {
        $companyid = self::companyid();
        if ($companyid > 0) {
            return " INNER JOIN {company_course} ON $alias.id = {company_course}.courseid
                     AND {company_course}.companyid = $companyid ";
        } else {
            return "";
        }
    }

    /**
     * SQL text processing to add a company user table join
     *
     * Parameters - $alias = text;
     *
     * Returns text;
     *
     **/
    public static function join_company_user($alias = "{user}") {
        if ($companyshortname = self::companyshortname()) {
            return " INNER JOIN {user_info_data} ON {user_info_data}.userid = $alias.id AND
                     {user_info_data}.data = '" . str_replace("'", "''", $companyshortname) . "'
                     INNER JOIN {user_info_field} ON {user_info_field}.id = {user_info_data}.fieldid
                     AND {user_info_field}.shortname = 'company' ";
        } else {
            return "";
        }
    }

    /**
     * Add license courses to the list of my courses
     *
     * Parameters - &$mycourses = array();
     *
     **/
    public static function iomad_add_license_courses(&$mycourses) {
        global $DB, $CFG, $USER;
        // Get the list of courses the user has a valid license for but not already enroled in.
        if ($licensecourses = $DB->get_records_sql("SELECT * FROM {course} c
                                                    WHERE c.id IN (
                                                     SELECT clu.licensecourseid
                                                     FROM {companylicense_users} clu
                                                     WHERE clu.userid = :userid
                                                     AND clu.isusing = 0
                                                    )", array('userid' => $USER->id))) {
            $mycourses = $mycourses + $licensecourses;
        }
        return;
    }

    /**
     * Add shared courses to the list of courses
     *
     * Parameters - &$courses = array();
     *
     **/
    public static function iomad_add_shared_courses(&$courses) {
        global $DB, $CFG, $USER;

        if (!empty($USER->profile['company'])) {
            $company = company::get_company_byuserid($USER->id);
            $sharedcourses = $DB->get_records_sql('SELECT * FROM {course} c
                                                   WHERE c.id IN (
                                                    SELECT courseid FROM {iomad_courses}
                                                    WHERE shared=1
                                                    AND licensed = 0
                                                   ) OR c.id IN (
                                                    SELECT pc.courseid FROM
                                                    {iomad_courses} pc
                                                    JOIN {company_shared_courses} csc
                                                    ON
                                                    csc.courseid=pc.courseid
                                                    AND csc.companyid = :companyid
                                                    AND pc.licensed = 0
                                                   )', array('companyid' => $company->id));
        } else {
            $sharedcourses = $DB->get_records_sql('SELECT * from {course} c
                                                   WHERE c.id IN (
                                                    SELECT courseid FROM {iomad_courses}
                                                    WHERE shared=1
                                                   )');
        }
        if (!empty($sharedcourses) && !empty($courses)) {
            foreach ($courses as $course) {
                if (!empty($sharedcourses[$course->id])) {
                    unset($sharedcourses[$course->id]);
                }
            }
            $courses = $courses + $sharedcourses;
        }
        return;
    }


    /**
     * IOMAD:
     * Filter categories to only show 'company' categories for the
     * current user. All other pass through as normal
     * @param array $categories list of category objects
     * @return array filtered list of categories
     */
    public static function iomad_filter_categories( $categories ) {
        global $DB, $USER;

        // Check if its the client admin.
        if (self::has_capability('block/iomad_company_admin:company_view_all', context_system::instance())) {
            return $categories;
        }

        $iomadcategories = array();
        foreach ($categories as $id => $category) {

            // Try to find category in company list.
            if ($company = $DB->get_record( 'company', array('category' => $id) ) ) {

                // If this is not the user's company then do not include.
                if (!empty( $USER->company )) {
                    if ($USER->company->id == $company->id) {
                        $iomadcategories[ $id ] = $category;
                    }
                }
            } else {
                $iomadcategories[ $id ] = $category;
            }
        }

        return $iomadcategories;
    }

    /**
     * IOMAD:
     * Filter courses to only show 'company' courses for the
     * current user. All other pass through as normal
     * @param array $courses list of courses objects
     * @return array filtered list of courses
     */
    public static function iomad_filter_courses( $courses ) {
        global $DB, $USER;

        // Check if its the client admin.
        if (self::has_capability('block/iomad_company_admin:company_view_all', context_system::instance())) {
            return $courses;
        }
        $context = context_system::instance();
        $mycompanyid = self::get_my_companyid($context);
        
        $iomadcourses = array();
        foreach ($courses as $id => $course) {
            // Try to find category in company list.
            if ($DB->get_record( 'company_course', array('courseid' => $id,
                                                         'companyid' => $mycompanyid) ) ) {
                // Include as tied to company.
                $iomadcoursess[ $id ] = $course;
            } else if ($DB->get_record( 'iomad_courses', array('courseid' => $id,
                                                              'shared' => 1) ) ) {
                // Include as open shared.
                $iomadcoursess[ $id ] = $course;
            } else if (!$DB->get_records('company_course', array('courseid' => $id))) {
                // Include as not a companycourse.
                $iomadcoursess[ $id ] = $course;
            }
        }

        return $iomadcourses;
    }

    /**
     * IOMAD:
     * Filter objects to only show 'company' objects for the
     * current user. All other pass through as normal
     * @param array $objects list of competency objects
     * @return array filtered list of objects.
     */
    public static function get_company_frameworkids($companyid) {
        global $DB;

        $companyframeworks = $DB->get_records('company_comp_frameworks', array('companyid' => $companyid));
        $closedsharedframeworks = $DB->get_records('company_shared_frameworks', array('companyid' => $companyid));
        $opensharedframeworks = $DB->get_records('iomad_frameworks', array('shared' => 1));
        $return = array();
        foreach($companyframeworks as $framework) {
            $return[$framework->frameworkid] = $framework->frameworkid;
        }
        foreach($closedsharedframeworks as $framework) {
            $return[$framework->frameworkid] = $framework->frameworkid;
        }
        foreach($opensharedframeworks as $framework) {
            $return[$framework->frameworkid] = $framework->frameworkid;
        }
        return $return;
    }

    /**
     * IOMAD:
     * Filter objects to only show 'company' objects for the
     * current user. All other pass through as normal
     * @param array $objects list of competency objects
     * @return array filtered list of objects.
     */
    public static function get_company_templateids($companyid) {
        global $DB;

        $companytemplates = $DB->get_records('company_comp_templates', array('companyid' => $companyid));
        $closedsharedtemplates = $DB->get_records('company_shared_templates', array('companyid' => $companyid));
        $opensharedtemplates = $DB->get_records('iomad_templates', array('shared' => 1));
        $return = array();
        foreach($companytemplates as $template) {
            $return[$template->templateid] = $template->templateid;
        }
        foreach($closedsharedtemplates as $template) {
            $return[$template->templateid] = $template->templateid;
        }
        foreach($opensharedtemplates as $template) {
            $return[$template->templateid] = $template->templateid;
        }
        return $return;
    }

    /** IOMAD:
     * Check if a category is attached to a company AND
     * the user belongs to a different company.
     * Otherwise, return true
     */
    public static function iomad_check_category($category) {
        global $CFG, $DB, $USER;

        // If we are installing this will be called to build
        // the basic category tree so just say yes.
        if (during_initial_install() || is_siteadmin($USER->id)) {
            return true;
        }

        // Try to find the category in company list.
        if (!empty($category->id) && $company = $DB->get_record( 'company', array('category' => $category->id) ) ) {

            // If this is not the user's company then we return false.
            if ($DB->get_record('company_users', array('userid' => $USER->id, 'companyid' => $company->id))) {
                // User is not assigned to this company - hide the category.
                return true;
            } else {
                return false;
            }
        }
        // Category is visible.
        return true;
    }

    public static function iomad_check_categoryid($categoryid) {
        global $CFG, $DB, $USER;

        // If we are installing this will be called to build
        // the basic category tree so just say yes.
        if (during_initial_install() || is_siteadmin($USER->id)) {
            return true;
        }

        // Try to find the category in company list.
        if (!empty($categoryid) && $company = $DB->get_record( 'company', array('category' => $categoryid) ) ) {

            // If this is not the user's company then we return false.
            if ($DB->get_record('company_users', array('userid' => $USER->id, 'companyid' => $company->id))) {
                // User is not assigned to this company - hide the category.
                return true;
            } else {
                return false;
            }
        }
        // Category is visible.
        return true;
    }

    /** IOMAD:
     * Check if a course is attached to a company AND
     * the user belongs to a different company.
     * Otherwise, return true
     */
    public static function iomad_check_course($course) {
        global $CFG, $DB, $USER;

        // If we are installing this will be called to build
        // the basic category tree so just say yes.
        if (during_initial_install() || is_siteadmin($USER->id)) {
            return true;
        }

        // Try to find the category in company list.
        if (!empty($course->id) && $company = $DB->get_record( 'company_course', array('courseid' => $course->id) ) ) {
            // If this is not the user's company then we return false.
            if ($DB->get_record('company_users', array('userid' => $USER->id, 'companyid' => $company->companyid))) {
                // User is not assigned to this company - hide the category.
                return true;
            } else {
                return false;
            }
        }
        // Category is visible.
        return true;
    }

    /**
     * Sets up a new user filter form.
     *
     * Parameters - $companyid = int;
     *
     * Returns form;
     **/
    public static function add_user_filter_form($companyid) {
        require_once('userfilterform.php');

        $mform = new user_filter_form(null, array('companyid' => $companyid));

        return $mform;
    }

    /**
     * Add the parameters which would be passed by the user filter form
     *
     * Parameters - &$params = array;
     *              $companyid = int;
     *
     **/
    public static function add_user_filter_params(&$params, $companyid) {
        global $DB, $CFG;

        $firstname       = optional_param('firstname', 0, PARAM_CLEAN);
        $lastname      = optional_param('lastname', '', PARAM_CLEAN);   // Md5 confirmation hash.
        $email  = optional_param('email', 0, PARAM_CLEAN);
        $sort         = optional_param('sort', 'name', PARAM_ALPHA);
        $dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
        $page         = optional_param('page', 0, PARAM_INT);
        $perpage      = optional_param('perpage', 30, PARAM_INT);        // How many per page?
        $search      = optional_param('search', '', PARAM_CLEAN);// Search string.
        $departmentid = optional_param('departmentid', 0, PARAM_INTEGER);
        $compfromraw = optional_param('compfrom', null, PARAM_RAW);
        $comptoraw = optional_param('compto', null, PARAM_RAW);

        // Process the params.
        $paramlist = array('firstname',
                           'lastname',
                           'email',
                           'search',
                           'compfrom',
                           'compto');
        //  Get the company additional optional user parameter names.
        $fieldnames = array();
        $idlist = array();
        $foundfields = false;

        if ($companyinfo = $DB->get_record('company', array('id' => $companyid))) {
            // Get field names from company category.
            if ($fields = $DB->get_records('user_info_field', array('categoryid' => $companyinfo->profileid))) {
                foreach ($fields as $field) {
                    $fieldnames[$field->id] = 'profile_field_'.$field->shortname;
                    ${'profile_field_'.$field->shortname} = optional_param('profile_field_'.$field->shortname, null, PARAM_RAW);
                }
            }
        }

        //  Get the global optional user parameter names.
        if ($globalfields = $DB->get_records_sql("SELECT * from {user_info_field} WHERE
                                                  categoryid NOT IN (
                                                    SELECT profileid from {company}
                                                  )")) {
            foreach ($globalfields as $field) {
                if ($field->shortname != 'company') {
                    if ($field->shortname == 'VANTAGE') {
                        $vantagefieldid = $field->id;
                    }
                    $fieldnames[$field->id] = 'profile_field_'.$field->shortname;
                    ${'profile_field_'.$field->shortname} = optional_param('profile_field_'.$field->shortname, null, PARAM_RAW);
                }
            }
             $fields = $fields + $globalfields;
        }

        // Deal with the user optional profile search.
        if (!empty($fieldnames)) {
            $fieldids = array();
            foreach ($fieldnames as $id => $fieldname) {
                $paramarray = array();
                if ($fields[$id]->datatype == "menu" ) {
                    $paramarray = explode("\n", $fields[$id]->param1);
                    if (!empty($paramarray[${$fieldname}])) {
                        ${$fieldname} = $paramarray[${$fieldname}];
                    }
                }
                if (!empty(${$fieldname}) ) {
                    $idlist[0] = "We found no one";
                    $fieldsql = $DB->sql_compare_text('data')." like '%".${$fieldname}."%' AND fieldid = $id";
                    if ($idfields = $DB->get_records_sql("SELECT userid from {user_info_data} WHERE $fieldsql")) {
                        $fieldids[] = $idfields;
                    }
                    if (!empty($paramarray)) {
                        $params[$fieldname] = array_search(${$fieldname}, $paramarray);
                    } else {
                        $params[$fieldname] = ${$fieldname};
                    }
                }
            }
            if (!empty($fieldids)) {
                $foundfields = true;
                $idlist = array_pop($fieldids);
                if (!empty($fieldids)) {
                    foreach ($fieldids as $fieldid) {
                        $idlist = array_intersect_key($idlist, $fieldid);
                        if (empty($idlist)) {
                            break;
                        }
                    }
                }
            }
        }
        $returnobj = new stdclass();
        $returnobj->foundfields = $foundfields;
        $returnobj->idlist = $idlist;

        return $returnobj;
    }

    /**
     * Get a list of users provided a list of parameters
     *
     * Parameters - &$params = array();
     *              $idlist = array();
     *              $sort = text;
     *              $dir = text;
     *              $departmentid = int;
     *
     * Return array();
     **/
    public static function get_user_sqlsearch($params, $idlist='', $sort, $dir, $departmentid, $nogrades=false, $allcourse=false) {
        global $DB, $CFG;

        if ($allcourse) {
            $sqlsort = " GROUP BY cc.id, co.id, u.id, d.name";
        } else {
            $sqlsort = " GROUP BY cc.id, u.id, cc.timestarted, cc.timecompleted, d.name";
        }
        if (!$nogrades) {
            $sqlsort .= ', cc.finalscore';
        }
        $sqlsearch = "u.id != '-1' and u.deleted = 0";
        $sqlsearch .= " AND u.id NOT IN (".$CFG->siteadmins.")";

        // Deal with suspended users.
        if (empty($params['showsuspended'])) {
            $sqlsearch .= " AND u.suspended = 0";
        }

        $returnobj = new stdclass();

        // Deal with search strings.
        $searchparams = array();
        if (!empty($idlist)) {
            $sqlsearch .= " AND u.id IN (".implode(',', array_keys($idlist)).") ";
        }
        if (!empty($params['firstname'])) {
            $sqlsearch .= " AND u.firstname LIKE :firstname ";
            $searchparams['firstname'] = '%'.$params['firstname'].'%';
        }

        if (!empty($params['lastname'])) {
            $sqlsearch .= " AND u.lastname LIKE :lastname ";
            $searchparams['lastname'] = '%'.$params['lastname'].'%';
        }

        if (!empty($params['email'])) {
            $sqlsearch .= " AND u.email LIKE :email ";
            $searchparams['email'] = '%'.$params['email'].'%';
        }
        if (!empty($params['compfrom'])) {
            $params['courseid2'] = $params['courseid'];
            if ($compfromids = $DB->get_records_sql("SELECT userid FROM {course_completions}
                                                     WHERE (course = :courseid
                                                     AND timecompleted < :compfrom
                                                     AND timecompleted IS NOT NULL)
                                                     OR (
                                                     course = :courseid2
                                                     AND timecompleted IS NULL)", $params)) {
                $sqlsearch .= " AND u.id NOT IN (".implode(',', array_keys($compfromids)).") ";
            }
        }

        if (!empty($params['compto'])) {
            if ($comptoids = $DB->get_records_sql("SELECT userid FROM {course_completions}
                                                   WHERE course = :courseid AND timecompleted > :compto", $params)) {
                $sqlsearch .= " AND u.id NOT IN (".implode(',', array_keys($comptoids)).") ";
            }
        }

        // Deal with how we sort the data.
        switch($sort) {
            case "firstname":
                $sqlsort .= " ORDER BY u.firstname $dir ";
            break;
            case "lastname":
                $sqlsort .= " ORDER BY u.lastname $dir ";
            break;
            case "email":
                $sqlsort .= " ORDER BY u.email $dir ";
            break;
            case "timecreated":
                $sqlsort .= " ORDER BY u.timecreated $dir ";
            break;
            case "timeenrolled":
                $sqlsort .= " ORDER BY cc.timeenrolled $dir ";
            break;
            case "timestarted":
                $sqlsort .= " ORDER BY cc.timestarted $dir ";
            break;
            case "timecompleted":
                $sqlsort .= " ORDER BY cc.timecompleted $dir ";
            break;
            case "department":
                $sqlsort .= " ORDER BY d.name $dir ";
            break;
            case "vantage":
                $sqlsort .= " ORDER BY uid.data $dir ";
            break;
            case "finalscore":
                $sqlsort .= " ORDER BY gg.finalgrade $dir ";
            break;
            default:
                if ($allcourse) {
                    $sqlsort .= " ORDER BY co.id $dir ";
                }
            break;
        }

        $returnobj->sqlsearch = $sqlsearch;
        $returnobj->sqlsort = $sqlsort;
        $returnobj->searchparams = $searchparams;
        $returnobj->departmentid = $departmentid;
        return $returnobj;
    }

    /**
     * Get completion summary info for a course
     *
     * Parameters - $departmentid = int;
     *              $courseid = int;
     *
     * Return array();
     **/
    public static function get_course_summary_info($departmentid, $courseid=0, $showsuspended) {
        global $DB;

        // Create a temporary table to hold the userids.
        $temptablename = 'tmp_'.uniqid();
        $dbman = $DB->get_manager();

        // Define table user to be created.
        $table = new xmldb_table($temptablename);
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        $dbman->create_temp_table($table);

        // Populate it.
        $alldepartments = company::get_all_subdepartments($departmentid);
        if (count($alldepartments) > 0 ) {
            // Deal with suspended or not.
            if (empty($showsuspended)) {
                $suspendedsql = " AND suspended = 0 ";
            } else {
                $suspendedsql = "";
            }
            $tempcreatesql = "INSERT INTO {".$temptablename."} (userid) SELECT userid from {company_users}
                              WHERE departmentid IN (".implode(',', array_keys($alldepartments)).") $suspendedsql";
        } else {
            $tempcreatesql = "";
        }
        $DB->execute($tempcreatesql);

        // All or one course?
        $courses = array();
        if (!empty($courseid)) {
            $courses[$courseid] = new stdclass();
            $courses[$courseid]->id = $courseid;
        } else {
            $courses = company::get_recursive_department_courses($departmentid);
        }

        // Process them!
        $returnarr = array();
        foreach ($courses as $course) {
            $courseobj = new stdclass();
            $courseobj->id = $course->courseid;
            $courseobj->numenrolled = $DB->count_records_sql("SELECT COUNT(cc.id) FROM {course_completions} cc
                                                   JOIN {".$temptablename."} tt ON (cc.userid = tt.userid)
                                                   WHERE
                                                   cc.course = :course", array('course' => $course->courseid));
            $courseobj->numnotstarted = $DB->count_records_sql("SELECT COUNT(cc.id) FROM {course_completions} cc
                                                   JOIN {".$temptablename."} tt ON (cc.userid = tt.userid)
                                                   WHERE
                                                   cc.course = :course AND
                                                   cc.timestarted = 0", array('course' => $course->courseid));
            $courseobj->numstarted = $DB->count_records_sql("SELECT COUNT(cc.id) FROM {course_completions} cc
                                                   JOIN {".$temptablename."} tt ON (cc.userid = tt.userid)
                                                   WHERE
                                                   cc.course = :course AND
                                                   cc.timestarted != 0", array('course' => $course->courseid));
            $courseobj->numcompleted = $DB->count_records_sql("SELECT COUNT(cc.id) FROM {course_completions} cc
                                                   JOIN {".$temptablename."} tt ON (cc.userid = tt.userid)
                                                   WHERE
                                                   cc.course = :course AND
                                                   cc.timecompleted IS NOT NULL", array('course' => $course->courseid));

            if (!$courseobj->coursename = $DB->get_field('course', 'fullname', array('id' => $course->courseid))) {
                continue;
            }
            $returnarr[$course->courseid] = $courseobj;
        }
        return $returnarr;
    }

    /** 
     * Get users into temporary table
     */
    private static function populate_temporary_users($temptablename, $searchinfo) {
        global $DB;


        // Create a temporary table to hold the userids.
        $dbman = $DB->get_manager();

        // Define table user to be created.
        $table = new xmldb_table($temptablename);
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        $dbman->create_temp_table($table);

        // Populate it.
        $alldepartments = company::get_all_subdepartments($searchinfo->departmentid);
        if (count($alldepartments) > 0 ) {
            $tempcreatesql = "INSERT INTO {".$temptablename."} (userid) SELECT userid from {company_users}
                              WHERE departmentid IN (".implode(',', array_keys($alldepartments)).")";
        } else {
            $tempcreatesql = "";
        }
        $DB->execute($tempcreatesql);

        return array($dbman, $table);
    }

    /**
     * Get user completion info for a course
     *
     * Parameters - $departmentid = int;
     *              $courseid = int;
     *              $page = int;
     *              $perpade = int;
     *
     * Return array();
     **/
    public static function get_user_course_completion_data($searchinfo, $courseid, $page=0, $perpage=0, $completiontype=0) {
        global $DB;

        $completiondata = new stdclass();

        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

        $temptablename = 'tmp_'.uniqid();
        list($dbman, $table) = self::populate_temporary_users($temptablename, $searchinfo);

        // Deal with completion types.
        if (!empty($completiontype)) {
            if ($completiontype == 1) {
                $completionsql = " AND cc.timeenrolled > 0 AND cc.timestarted = 0 ";
            } else if ($completiontype == 2 ) {
                $completionsql = " AND cc.timestarted > 0 AND cc.timecompleted IS NULL ";
            } else if ($completiontype == 3 ) {
                $completionsql = " AND cc.timecompleted IS NOT NULL  ";
            }
        } else {
            $completionsql = "";
        }
                
        // Get the user details.
        $shortname = addslashes($course->shortname);
        $countsql = "SELECT u.id ";
        $selectsql = "SELECT u.id,
                u.id as uid,
                u.firstname AS firstname,
                u.lastname AS lastname,
                u.email AS email,
                u.timecreated AS timecreated,
                '{$shortname}' AS coursename,
                '$courseid' AS courseid,
                cc.timeenrolled AS timeenrolled,
                cc.timestarted AS timestarted,
                cc.timecompleted AS timecompleted,
                d.name as department,
                gg.finalgrade as result ";
        $fromsql = " FROM {user} u, {course_completions} cc, {department} d, {company_users} du, {".$temptablename."} tt
                     LEFT JOIN {grade_grades} gg ON ( gg.itemid = (
                       SELECT id FROM {grade_items} WHERE courseid = $courseid AND itemtype='course'))

                WHERE $searchinfo->sqlsearch
                AND tt.userid = u.id
                AND cc.course = $courseid
                AND u.id = cc.userid
                AND du.userid = u.id
                AND d.id = du.departmentid
                AND gg.userid = u.id
                $completionsql
                $searchinfo->sqlsort ";

        $searchinfo->searchparams['courseid'] = $courseid;
        $users = $DB->get_records_sql($selectsql.$fromsql, $searchinfo->searchparams, $page * $perpage, $perpage);
        $countusers = $DB->get_records_sql($countsql.$fromsql, $searchinfo->searchparams);
        $numusers = count($countusers);

        $returnobj = new stdclass();
        $returnobj->users = $users;
        $returnobj->totalcount = $numusers;

        $dbman->drop_table($table);

        return $returnobj;
    }

    /**
     * Get all users completion info regardless of course
     *
     * Parameters - $departmentid = int;
     *              $page = int;
     *              $perpade = int;
     *
     * Return array();
     **/
    public static function get_all_user_course_completion_data($searchinfo, $page=0, $perpage=0, $completiontype=0) {
        global $DB;

        $completiondata = new stdclass();

        // Create a temporary table to hold the userids.
        $temptablename = 'tmp_'.uniqid();
        list($dbman, $table) = self::populate_temporary_users($temptablename, $searchinfo);

        // Deal with completion types.
        if (!empty($completiontype)) {
            if ($completiontype == 1) {
                $completionsql = " AND cc.timeenrolled > 0 AND cc.timestarted = 0 ";
            } else if ($completiontype == 2 ) {
                $completionsql = " AND cc.timestarted > 0 AND cc.timecompleted IS NULL ";
            } else if ($completiontype == 3 ) {
                $completionsql = " AND cc.timecompleted IS NOT NULL  ";
            }
        } else {
            $completionsql = "";
        }
                
        // Get the user details.
        $countsql = "SELECT CONCAT(co.id, u.id) AS id ";
        $selectsql = "
                SELECT
                CONCAT(co.id, u.id) AS id, 
                u.id AS uid,
                u.firstname AS firstname,
                u.lastname AS lastname,
                u.email AS email,
                u.timecreated AS timecreated,
                co.shortname AS coursename,
                co.id AS courseid,
                cc.timeenrolled AS timeenrolled,
                cc.timestarted AS timestarted,
                cc.timecompleted AS timecompleted,
                d.name as department,
                '0' as result ";
        $fromsql = " FROM {user} u, {course_completions} cc, {department} d, {company_users} du, {".$temptablename."} tt, {course} co

                WHERE $searchinfo->sqlsearch
                AND tt.userid = u.id
                AND co.id = cc.course
                AND u.id = cc.userid
                AND du.userid = u.id
                AND d.id = du.departmentid
                $completionsql
                $searchinfo->sqlsort ";

        $users = $DB->get_records_sql($selectsql.$fromsql, $searchinfo->searchparams, $page * $perpage, $perpage);
        $countusers = $DB->get_records_sql($countsql.$fromsql, $searchinfo->searchparams);
        $numusers = count($countusers);
        foreach ($users as $id => $user) {
            $gradeitem = $DB->get_record('grade_items', array('itemtype' => 'course', 'courseid' => $user->courseid));
            $grade = $DB->get_record('grade_grades', array('itemid' => $gradeitem->id, 'userid' => $user->uid));
            if ($grade) {
                $user->result = $grade->finalgrade;
            }
        }

        $returnobj = new stdclass();
        $returnobj->users = $users;
        $returnobj->totalcount = $numusers;

        $dbman->drop_table($table);

        return $returnobj;
    }

    /**
     * Get a list of users provided a list of parameters
     *
     * Parameters - &$params = array();
     *              $idlist = array();
     *              $sort = text;
     *              $dir = text;
     *              $departmentid = int;
     *
     * Return array();
     **/
    public static function get_user_license_sqlsearch($params, $idlist='', $sort, $dir, $departmentid, $licenses=false) {
        global $DB, $CFG;

        if (!empty($params['courseid']) && $params['courseid'] == 1) {
            if (!$licenses) {
                $sqlsort = " GROUP BY co.id, cl.name, d.name, u.id";
            } else {
                $sqlsort = " GROUP BY co.id, cl.name, d.name, u.id, clu.id";
            }
        } else {
            if (!$licenses) {
                $sqlsort = " GROUP BY cl.name, d.name, u.id";
            } else {
                $sqlsort = " GROUP BY cl.name, d.name, u.id, clu.id";
            }
        }
        $sqlsearch = "u.id != '-1' and u.deleted = 0";
        $sqlsearch .= " AND u.id NOT IN (".$CFG->siteadmins.")";

        // Deal with suspended users.
        if (empty($params['showsuspended'])) {
            $sqlsearch .= " AND u.suspended = 0";
        }

        $returnobj = new stdclass();

        // Deal with search strings.
        $searchparams = array();
        if (!empty($idlist)) {
            $sqlsearch .= " AND u.id IN (".implode(',', array_keys($idlist)).") ";
        }
        if (!empty($params['firstname'])) {
            $sqlsearch .= " AND u.firstname LIKE :firstname ";
            $searchparams['firstname'] = '%'.$params['firstname'].'%';
        }

        if (!empty($params['lastname'])) {
            $sqlsearch .= " AND u.lastname LIKE :lastname ";
            $searchparams['lastname'] = '%'.$params['lastname'].'%';
        }

        if (!empty($params['email'])) {
            $sqlsearch .= " AND u.email LIKE :email ";
            $searchparams['email'] = '%'.$params['email'].'%';
        }

        // Deal with how we sort the data.
        switch($sort) {
            case "firstname":
                $sqlsort .= " ORDER BY u.firstname $dir ";
            break;
            case "lastname":
                $sqlsort .= " ORDER BY u.lastname $dir ";
            break;
            case "email":
                $sqlsort .= " ORDER BY u.email $dir ";
            break;
            case "licensename":
                $sqlsort .= " ORDER BY cl.name $dir ";
            break;
            case "isusing":
                $sqlsort .= " ORDER BY clu.isusing $dir ";
            break;
            case "department":
                $sqlsort .= " ORDER BY d.name $dir ";
            break;
        }

        $returnobj->sqlsearch = $sqlsearch;
        $returnobj->sqlsort = $sqlsort;
        $returnobj->searchparams = $searchparams;
        $returnobj->departmentid = $departmentid;
        return $returnobj;
    }

    /**
     * Get license summary info for a course
     *
     * Parameters - $departmentid = int;
     *              $courseid = int;
     *
     * Return array();
     **/
    public static function get_course_license_summary_info($departmentid, $courseid=0, $showsuspended) {
        global $DB;

        // Create a temporary table to hold the userids.
        $temptablename = 'tmp_'.uniqid();
        $dbman = $DB->get_manager();

        // Define table user to be created.
        $table = new xmldb_table($temptablename);
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        $dbman->create_temp_table($table);

        // Populate it.
        $alldepartments = company::get_all_subdepartments($departmentid);
        if (count($alldepartments) > 0 ) {
            // Deal with suspended or not.
            if (empty($showsuspended)) {
                $suspendedsql = " AND suspended = 0 ";
            } else {
                $suspendedsql = "";
            }
            $tempcreatesql = "INSERT INTO {".$temptablename."} (userid) SELECT userid from {company_users}
                              WHERE departmentid IN (".implode(',', array_keys($alldepartments)).") $suspendedsql";
        } else {
            $tempcreatesql = "";
        }
        $DB->execute($tempcreatesql);

        // All or one course?
        $courses = array();
        if (!empty($courseid)) {
            $courses[$courseid] = new stdclass();
            $courses[$courseid]->id = $courseid;
        } else {
            $courses = company::get_recursive_department_courses($departmentid);
        }

        // Process them!
        $returnarr = array();
        foreach ($courses as $course) {
            $courseobj = new stdclass();
            $courseobj->id = $course->courseid;
            $timestamp = time();
            $courseobj->numlicenses = $DB->count_records_sql("SELECT COUNT(clu.id) FROM {companylicense_users} clu
                                                   JOIN {".$temptablename."} tt ON (clu.userid = tt.userid)
                                                   JOIN {companylicense} cl ON (cl.id = clu.licenseid)
                                                   WHERE
                                                   clu.licensecourseid = :courseid
                                                   AND cl.expirydate > :timestamp", array('courseid' => $course->courseid,
                                                                                          'timestamp' => $timestamp));
            $courseobj->numused = $DB->count_records_sql("SELECT COUNT(clu.id) FROM {companylicense_users} clu
                                                   JOIN {".$temptablename."} tt ON (clu.userid = tt.userid)
                                                   JOIN {companylicense} cl ON (cl.id = clu.licenseid)
                                                   WHERE
                                                   clu.licensecourseid = :courseid
                                                   AND cl.expirydate > :timestamp
                                                   AND
                                                   clu.isusing = 1", array('courseid' => $course->courseid,
                                                                           'timestamp' => $timestamp));
            $courseobj->numunused = $courseobj->numlicenses - $courseobj->numused;

            if (!$courseobj->coursename = $DB->get_field('course', 'fullname', array('id' => $course->courseid))) {
                continue;
            }
            $returnarr[$course->courseid] = $courseobj;
        }
        return $returnarr;
    }

    /**
     * Get all users completion info regardless of course
     *
     * Parameters - $departmentid = int;
     *              $page = int;
     *              $perpade = int;
     *
     * Return array();
     **/
    public static function get_all_user_course_license_data($searchinfo, $page=0, $perpage=0, $completiontype=0, $showsuspended = false, $showused = false) {
        global $DB;

        $completiondata = new stdclass();

        // Create a temporary table to hold the userids.
        $temptablename = 'tmp_'.uniqid();
        list($dbman, $table) = self::populate_temporary_users($temptablename, $searchinfo);

        // Deal with completion types.
        if (!empty($completiontype)) {
            if ($completiontype == 1) {
                $completionsql = " AND cc.timeenrolled > 0 AND cc.timestarted = 0 ";
            } else if ($completiontype == 2 ) {
                $completionsql = " AND cc.timestarted > 0 AND cc.timecompleted IS NULL ";
            } else if ($completiontype == 3 ) {
                $completionsql = " AND cc.timecompleted IS NOT NULL  ";
            }
        } else {
            $completionsql = "";
        }

        if (!$showsuspended) {
            $showsuspendedsql = "AND u.suspended = 0";
        } else {
            $showsuspendedsql = "";
        }                

        if (!$showused) {
            $showusedsql = "AND clu.isusing = 0";
        } else {
            $showusedsql = "";
        }                

        // Get the user details.
        $countsql = "SELECT clu.id AS id ";
        $selectsql = "
                SELECT
                clu.id AS id, 
                u.id AS uid,
                u.firstname AS firstname,
                u.lastname AS lastname,
                u.email AS email,
                u.currentlogin AS lastaccess,
                co.shortname AS coursename,
                co.id AS courseid,
                cl.id AS licenseid,
                cl.name AS licensename,
                d.name as department,
                cl.name,
                clu.isusing,
				clu.issuedate,
                '0' as result ";
        $fromsql = " FROM {user} u, {companylicense_users} clu, {department} d, {company_users} du, {".$temptablename."} tt, {course} co, {companylicense} cl

                WHERE $searchinfo->sqlsearch
                AND tt.userid = u.id
                AND co.id = clu.licensecourseid
                AND u.id = clu.userid
                AND du.userid = u.id
                AND d.id = du.departmentid
                AND cl.id = clu.licenseid
                AND cl.expirydate > :timestamp
                $showusedsql
                $showsuspendedsql
                $completionsql
                $searchinfo->sqlsort ";
        $searchinfo->searchparams['timestamp'] = time();
        $users = $DB->get_records_sql($selectsql.$fromsql, $searchinfo->searchparams, $page * $perpage, $perpage);
        $countusers = $DB->get_records_sql($countsql.$fromsql, $searchinfo->searchparams);
        $numusers = count($countusers);

        $returnobj = new stdclass();
        $returnobj->users = $users;
        $returnobj->totalcount = $numusers;

        $dbman->drop_table($table);

        return $returnobj;
    }

    public static function get_companies_listing($sort='name', $dir='ASC', $page=0, $recordsperpage=0,
                           $search='', $firstinitial='', $lastinitial='', $extraselect='', array $extraparams = null) {
        global $DB;
    
        $params = array();
    
        if (!empty($search)) {
            $search = trim($search);
            $select .= " AND (". $DB->sql_like("name", ':search1', false, false).
                       " OR ". $DB->sql_like('city', ':search2', false, false).
                       " OR country = :search3)";
            $params['search1'] = "%$search%";
            $params['search2'] = "%$search%";
            $params['search3'] = "$search";
        }
    
        if ($extraselect) {
            $select = $extraselect;
            $params = $params + (array)$extraparams;
        }
    
        if ($sort) {
            $sort = " ORDER BY $sort $dir";
        }
    
        // Warning: will return UNCONFIRMED USERS!
        return $DB->get_records_sql("SELECT *
                                     FROM {company}
                                     WHERE $select $sort",
                                     $params, $page, $recordsperpage);
    }
    
    /**
     * Get user completion info for a course
     *
     * Parameters - $departmentid = int;
     *              $courseid = int;
     *              $page = int;
     *              $perpade = int;
     *
     * Return array();
     **/
    public static function get_user_course_license_data($searchinfo, $courseid, $page=0, $perpage=0, $completiontype=0, $showsuspended = false, $showused = false) {
        global $DB;

        $completiondata = new stdclass();

        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

        $temptablename = 'tmp_'.uniqid();
        list($dbman, $table) = self::populate_temporary_users($temptablename, $searchinfo);

        if (!$showsuspended) {
            $showsuspendedsql = "AND u.suspended = 0";
        } else {
            $showsuspendedsql = "";
        }                

        if (!$showused) {
            $showusedsql = "AND clu.isusing = 0";
        } else {
            $showusedsql = "";
        }                

        // Get the user details.
        $shortname = addslashes($course->shortname);
        $countsql = "SELECT CONCAT(clu.id, u.id, clu.isusing) AS id";
        $selectsql = "SELECT
                CONCAT(clu.id, u.id) AS id, 
                u.id AS uid,
                u.firstname AS firstname,
                u.lastname AS lastname,
                u.email AS email,
                u.currentlogin AS lastaccess,
                '{$shortname}' AS coursename,
                '$courseid' AS courseid,
                clu.licenseid AS licenseid,
                clu.isusing AS isusing,
				clu.issuedate AS issuedate,
                d.name AS department,
                cl.name AS licensename ";
        $fromsql = " FROM {user} u, {companylicense_users} clu, {department} d, {company_users} du, {".$temptablename."} tt, {companylicense} cl
                     
                    WHERE $searchinfo->sqlsearch
                    AND tt.userid = u.id
                    AND clu.licensecourseid = $courseid
                    AND u.id = clu.userid
                    AND du.userid = u.id
                    AND d.id = du.departmentid
                    AND du.companyid = cl.companyid
                    AND cl.id = clu.licenseid
                    AND cl.expirydate > :timestamp
                    $showsuspendedsql
                    $showusedsql
                    $searchinfo->sqlsort ";

        $searchinfo->searchparams['courseid'] = $courseid;
        $searchinfo->searchparams['timestamp'] = time();
        $users = $DB->get_records_sql($selectsql.$fromsql, $searchinfo->searchparams, $page * $perpage, $perpage);
        $countusers = $DB->get_records_sql($countsql.$fromsql, $searchinfo->searchparams);
        $numusers = count($countusers);

        $returnobj = new stdclass();
        $returnobj->users = $users;
        $returnobj->totalcount = $numusers;

        $dbman->drop_table($table);

        return $returnobj;
    }

    /**
     * Copied from similarly named function in accesslib.php
     * modified to check iomad restrictions database.
     * @param unknown $capability
     * @param context $context
     * @param array $accessdata
     * @return boolean
     */
    private static function has_capability_in_accessdata($companyid, $capability, context $context, array &$accessdata) {
        global $CFG, $DB;
    
        // Build $paths as a list of current + all parent "paths" with order bottom-to-top
        $path = $context->path;
        $paths = array($path);
        while($path = rtrim($path, '0123456789')) {
            $path = rtrim($path, '/');
            if ($path === '') {
                break;
            }
            $paths[] = $path;
        }
    
        $roles = array();
        $switchedrole = false;
    
        // Find out if role switched
        if (!empty($accessdata['rsw'])) {
            // From the bottom up...
            foreach ($paths as $path) {
                if (isset($accessdata['rsw'][$path])) {
                    // Found a switchrole assignment - check for that role _plus_ the default user role
                    $roles = array($accessdata['rsw'][$path]=>null, $CFG->defaultuserroleid=>null);
                    $switchedrole = true;
                    break;
                }
            }
        }
    
        if (!$switchedrole) {
            // get all users roles in this context and above
            foreach ($paths as $path) {
                if (isset($accessdata['ra'][$path])) {
                    foreach ($accessdata['ra'][$path] as $roleid) {
                        $roles[$roleid] = null;
                    }
                }
            }
        }
    
        // Now find out what access is given to each role, going bottom-->up direction
        $allowed = false;
        foreach ($roles as $roleid => $ignored) {
            foreach ($paths as $path) {
                if (isset($accessdata['rdef']["{$path}:$roleid"][$capability])) {
                    $perm = (int)$accessdata['rdef']["{$path}:$roleid"][$capability];
                    if ($perm === CAP_PROHIBIT) {
                        // any CAP_PROHIBIT found means no permission for the user
                        return false;
                    }
                    if (is_null($roles[$roleid])) {
                        $roles[$roleid] = $perm;
                    }
                }
            }
            // CAP_ALLOW in any role means the user has a permission, we continue only to detect prohibits
            $restriction = $DB->get_record('company_role_restriction', array(
                    'companyid' => $companyid,
                    'roleid' => $roleid,
                    'capability' => $capability,
            ));
            if ($restriction) {
                return false;
            }
            $allowed = ($allowed or $roles[$roleid] === CAP_ALLOW);
        }
    
        return $allowed;
    }
    
    /**
     * IOMAD version 
     * @param unknown $capability
     * @param context $context
     * @param int $companyid (optional) check for different company (and right to access same).
     * @return bool
     */
    public static function has_capability($capability, context $context, $companyid = 0) {
        global $USER;
        
        // If original version says no then it's no.
        // (We also rely on this doing a bunch of sanity checks, so we don't have to)
        if (!has_capability($capability, $context)) {
            return false;
        }
        
        // If this is the admin then we'll believe it
        if (is_siteadmin()) {
            return true;
        }

        // If companyid supplied then check the user is a member
        if ($companyid) {
            if (!$DB->record_exists('company_users', ['companyid' => $companyid, 'userid' => $USER->id])) {
                return false;
            }
        } else {
        
            // Get user's current company. If no company then it must be true.
            if (!$companyid = self::companyid()) {
                return true;
            }
        }
        
        // Probably need to get accessdata (again), so...
        if (!isset($USER->access)) {
            load_all_capabilities();
        }
        $access =& $USER->access;
        
        return self::has_capability_in_accessdata($companyid, $capability, $context, $access);
    }
    
    /**
     * Iomad version of require_capability
     * @param unknown $capability
     * @param context $context
     * @param int $companyid (optional) check for different company (and right to access same).
     * @throws required_capability_exception
     */
    public static function require_capability($capability, context $context, $companyid = 0) {
        if (!self::has_capability($capability, $context, $companyid)) {
            throw new required_capability_exception($context, $capability, 'nopermissions', 'local_iomad');
        }
    }
    
    /**
     * Get IOMAD documentation link.
     */
    public static function documentation_link() {
        return 'http://docs.iomad.org/wiki/';
    }

    /**
     * Redirect on company URL matching
     *
     */
    public static function check_redirect($wwwroot, $rurl) {
        global $CFG, $DB;

        if ($rurl['host'] !=  $wwwroot['host']) {
            if ($companyrec = $DB->get_record('company', array('hostname' => $rurl['host']))) {
                $redirecturl = new moodle_url($CFG->wwwroot . '/local/iomad_signup/login.php',
                                              array('id' => $companyrec->id,
                                                    'code' => $companyrec->shortname));
                redirect($redirecturl);
            }
        }
    }
}

/**
 * User Filter form used on the Iomad pages.
 *
 */
class iomad_company_filter_form extends moodleform {
    protected $companyid;

    public function definition() {
        global $CFG, $DB, $USER, $SESSION;

        $mform =& $this->_form;
        $filtergroup = array();
        $mform->addElement('header', '', format_string(get_string('companysearchfields', 'local_iomad')));
        $mform->addElement('text', 'name', get_string('companynamefilter', 'local_iomad'), 'size="20"');
        $mform->addElement('text', 'city', get_string('companycityfilter', 'local_iomad'), 'size="20"');
        $mform->addElement('text', 'country', get_string('companycountryfilter', 'local_iomad'), 'size="20"');
        $mform->setType('name', PARAM_CLEAN);
        $mform->setType('city', PARAM_CLEAN);
        $mform->setType('country', PARAM_CLEAN);

        //if (has_capability('block/iomad_company_admin:suspendedcompanies', context_system::instance())) {
            $mform->addElement('checkbox', 'showsuspended', get_string('show_suspended_companies', 'local_iomad'));
        /*} else {
            $mform->addElement('hidden', 'showsuspended');
        }*/
        $mform->setType('showsuspended', PARAM_INT);

        $this->add_action_buttons(false, get_string('companyfilter', 'local_iomad'));
    }
}

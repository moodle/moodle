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

require_once('company.php');

class iomad {

    /**
     * Gets the current users company ID depending on 
     * if the user is an admin and editing a company or is a
     * company user tied to a company.
     * @param $context - stdclass()
     * @returns int
     */
    public static function get_my_companyid($context) {
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
        } else if (has_capability('block/iomad_company_admin:edit_departments', $context)) {
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
        global $USER, $DB;

        if ($usercompany = $DB->get_record('company_users', array('userid' => $USER->id))) {
            return $usercompany->companyid;
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
                                                     SELECT clc.courseid
                                                     FROM {companylicense_courses} clc
                                                     RIGHT JOIN {companylicense_users} clu
                                                     ON (clc.licenseid = clu.licenseid)
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
    public static function get_user_sqlsearch($params, $idlist='', $sort, $dir, $departmentid) {
        global $DB, $CFG;

        $sqlsort = " GROUP BY u.id, cc.timeenrolled, cc.timestarted, cc.timecompleted, d.name, gg.finalgrade";
        $sqlsearch = "u.id != '-1'";
        $sqlsearch .= " AND u.id NOT IN (".$CFG->siteadmins.")";
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
            if ($compfromids = $DB->get_records_sql("SELECT userid FROM {course_completions}
                                                     WHERE course = :courseid AND timecompleted < :compfrom
                                                     AND timecompleted IS NOT NULL", $params)) {
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
    public static function get_course_summary_info($departmentid, $courseid=0) {
        global $DB;

        // Create a temporary table to hold the userids.
        $temptablename = 'tmp_csum_users_'.time();
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
            $tempcreatesql = "INSERT INTO {".$temptablename."} (userid) SELECT userid from {company_users}
                              WHERE departmentid IN (".implode(',', array_keys($alldepartments)).")";
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
     * Get user completion info for a course
     *
     * Parameters - $departmentid = int;
     *              $courseid = int;
     *              $page = int;
     *              $perpade = int;
     *
     * Return array();
     **/
    public static function get_user_course_completion_data($searchinfo, $courseid, $page=0, $perpage=0) {
        global $DB;

        $completiondata = new stdclass();

        // Create a temporary table to hold the userids.
        $temptablename = 'tmp_ccomp_users_'.time();
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

        // Get the user details.
        if ($vantagefield = $DB->get_record('user_info_field', array('shortname' => 'VANTAGE'))) {
            $countsql = "SELECT u.id ";
            $selectsql = "SELECT u.id,
                    u.firstname AS firstname,
                    u.lastname AS lastname,
                    u.email AS email,
                    cc.timeenrolled AS timeenrolled,
                    cc.timestarted AS timestarted,
                    cc.timecompleted AS timecompleted,
                    d.name as department,
                    gg.finalgrade as result,
                    uid.data as vantage ";
            $fromsql = " FROM {user} u, {course_completions} cc, {department} d, {company_users} du,
                         {user_info_data} uid, {".$temptablename."} tt
                         LEFT JOIN {grade_grades} gg ON ( gg.itemid = (
                           SELECT id FROM {grade_items} WHERE courseid = $courseid AND itemtype='course'))

                    WHERE $searchinfo->sqlsearch
                    AND tt.userid = u.id
                    AND cc.course = $courseid
                    AND u.id = cc.userid
                    AND du.userid = u.id
                    AND d.id = du.departmentid
                    AND gg.userid = u.id
                    AND uid.userid = u.id
                    AND uid.fieldid = $vantagefield->id
                    $searchinfo->sqlsort ";
        } else {
            $countsql = "SELECT u.id ";
            $selectsql = "SELECT u.id,
                    u.firstname AS firstname,
                    u.lastname AS lastname,
                    u.email AS email,
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
                    $searchinfo->sqlsort ";

        }

        $searchinfo->searchparams['courseid'] = $courseid;
        $users = $DB->get_records_sql($selectsql.$fromsql, $searchinfo->searchparams, $page * $perpage, $perpage);
        $countusers = $DB->get_records_sql($countsql.$fromsql, $searchinfo->searchparams);
        $numusers = count($countusers);

        $returnobj = new stdclass();
        $returnobj->users = $users;
        $returnobj->totalcount = $numusers;
        return $returnobj;
    }
}

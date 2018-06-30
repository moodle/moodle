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
require_once(dirname(__FILE__) . '/iomad.php');
require_once(dirname(__FILE__) . '/user.php');

class company {
    public $id = 0;

    // These are the fields that will be retrieved by.
    public $cssfields = array('bgcolor_header', 'bgcolor_content');

    public function __construct($companyid) {
        $this->id = $companyid;
    }

    /**
     * Return an instance of the class using the company shortname
     *
     * Paramters -
     *             $userid = int;
     *
     * Returns class object.
     *
     **/
    public static function by_userid($userid) {
        global $DB, $SESSION;

        if (!empty($SESSION->currenteditingcompany)) {
            return new company($SESSION->currenteditingcompany);
        } else {
            if ($companies = $DB->get_records('company_users', array('userid' => $userid), 'companyid DESC')) {
                $company = array_pop($companies);
                return new company($company->companyid);
            } else {
                return false;
            }
        }
    }

    /**
     * Gets the company DB record.
     *
     * Paramters -
     *             $firelds = array();
     *
     * Returns - stdclass();
     *
     **/
    public function get($fields = '*') {
        global $DB;

        if ( $this->id == 0 ) {
            return '';
        }
        $companyrecord = $DB->get_record('company', array('id' => $this->id), $fields, MUST_EXIST);

        return $companyrecord;
    }

    /**
     * Gets the company name for the current instance
     *
     * Returns text;
     *
     **/
    public function get_name() {
        $companyrecord = $this->get('Name');
        return $companyrecord->name;
    }

    /**
     * Gets the types of managers available to the class
     *
     * Returns array();
     *
     **/
    public function get_managertypes() {
        global $CFG;

        $returnarray = array('0' => get_string('user', 'block_iomad_company_admin'));
        $systemcontext = context_system::instance();
        if (iomad::has_capability('block/iomad_company_admin:assign_company_manager', $systemcontext)) {
            $returnarray['1'] = get_string('companymanager', 'block_iomad_company_admin');
        }
        if (iomad::has_capability('block/iomad_company_admin:assign_department_manager', $systemcontext)) {
            $returnarray['2'] = get_string('departmentmanager', 'block_iomad_company_admin');
        }
        if (!$CFG->iomad_autoenrol_managers && iomad::has_capability('block/iomad_company_admin:assign_educator', $systemcontext)) {
            $returnarray['3'] = get_string('educator', 'block_iomad_company_admin');
        }
        return $returnarray;
    }

    /**
     * Gets the company short name for the current instance
     *
     * Returns text;
     *
     **/
    public function get_shortname() {
        $companyrecord = $this->get('shortname');
        return $companyrecord->shortname;
    }

    /**
     * Gets the company theme name for the current instance
     *
     * Returns text;
     *
     **/
    public function get_theme() {
        $companyrecord = $this->get('theme');
        return $companyrecord->theme;
    }

    /**
     * Gets the company parentid name for the current instance
     *
     * Returns int;
     *
     **/
    public function get_parentid() {
        if ($companyrecord = $this->get('parentid')) {
            return $companyrecord->parentid;
        } else {
            return false;
        }
    }

    /**
     * Recurses up the company tree to get the parent company.
     *
     * Returns int;
     *
     **/
    public function get_topcompanyid() {

        // Set the return id to by myself initially.
        $returnid = $this->id;

        // Check if I have a parent id.
        if ($parentid = $this->get_parentid()) {

            // Check it if has a parent id.
            $parentcompany = new company($parentid);
            $returnid = $parentcompany->get_topcompanyid();
        }

        return $returnid;
    }

    /**
     * Gets the file path for the company logo for the current instance
     *
     * Returns text;
     *
     **/
    public function get_logo_filename() {
        global $DB;

        $fs = get_file_storage();
        $context = context_system::instance();

        $files = $fs->get_area_files($context->id, 'theme_iomad', 'logo', $this->id,
                                     "sortorder, itemid, filepath, filename", false);

        // There should be only one file, but we'll still use a foreach as
        // the array indexes are based on the hash, just return the first one.
        foreach ($files as $f) {
            return $f->get_filename();
        }
    }

    /**
     * Gets the record set of all companies
     *
     * Parameters -
     *              $page = int;
     *              $perpage = int;
     *
     * Returns array;
     *
     **/
    public static function get_companies_rs($page=0, $perpage=0) {
        global $DB;

        return $DB->get_recordset('company', null, 'name', '*', $page, $perpage);
    }

    /**
     * Creates an array of companies to be used in a Select menu
     *
     * Returns array;
     *
     **/
    public static function get_companies_select($showsuspended=false) {
        global $DB, $USER;

        // Is this an admin, or a normal user?
        if (iomad::has_capability('block/iomad_company_admin:company_view_all', context_system::instance())) {
            if ($showsuspended) {
                $companies = $DB->get_recordset('company', array(), 'name', '*');
            } else {
                $companies = $DB->get_recordset('company', array('suspended' => 0), 'name', '*');
            }
        } else {
            if ($showsuspended) {
                $suspendedsql = '';
            } else {
                $suspendedsql = "AND suspended = 0";
            }
            $companies = $DB->get_recordset_sql("SELECT * FROM {company}
                                                 WHERE id IN (
                                                   SELECT companyid FROM {company_users}
                                                   WHERE userid = :userid )
                                                   $suspendedsql
                                                 ORDER BY name", array('userid' => $USER->id, 'suspended' => $showsuspended));
        }
        $companyselect = array();
        foreach ($companies as $company) {
            if (empty($company->suspended)) {
                $companyselect[$company->id] = $company->name;
            } else {
                $companyselect[$company->id] = $company->name . '(S)';
            }
        }
        return $companyselect;
    }

    /**
     * Creates an array of child companies to be used in a Select menu
     *
     * Returns array;
     *
     **/
    public function get_child_companies() {
        global $DB;

        $childcompanies = $DB->get_records('company', array('parentid' => $this->id));

        return $childcompanies;
    }

    /**
     * Creates a recursive array of child companies.
     *
     * Returns array;
     *
     **/
    public function get_child_companies_recursive() {
        global $DB;

        $returnarray = array();

        $childcompanies = $this->get_child_companies();
        foreach ($childcompanies as $child) {
            $returnarray[$child->id] = $child;
            $childcompany = new company($child->id);
            $returnarray = $returnarray + $childcompany->get_child_companies_recursive();
        }
        return $returnarray;
    }

    /**
     * Creates a recursive array of parent companies .
     *
     * Returns array;
     *
     **/
    public function get_parent_companies_recursive() {
        global $DB;

        $returnarray = array();

        // Check if I have a parent id.
        if ($parentid = $this->get_parentid()) {
            $returnarray[$parentid] = $parentid;

            // Check it if has a parent id.
            $parentcompany = new company($parentid);
            $returnarray = $returnarray + $parentcompany->get_parent_companies_recursive();
        }

        return $returnarray;
    }

    /**
     * Creates an array of child companies to be used in a Select menu
     *
     * Returns array;
     *
     **/
    public function get_child_companies_select() {
        global $DB, $USER;

        $companyselect = array();

        // Get all of the child companies.
        $companies = $this->get_child_companies_recursive();

        foreach ($companies as $company) {
            if (empty($company->suspended)) {
                $companyselect[$company->id] = $company->name;
            }
        }

        return $companyselect;
    }

    /**
     * Gets the name of a company given its ID
     *
     * Parameters -
     *              $companyid = int;
     *
     * Returns text;
     *
     **/
    public static function get_companyname_byid($companyid) {
        global $DB;
        $company = $DB->get_record('company', array('id' => $companyid));
        return $company->name;
    }

    /**
     * Gets the company record given a member
     *
     * Parameters -
     *              $userid = int;
     *
     * Returns stdclass();
     *
     **/
    public static function get_company_byuserid($userid) {
        global $DB;
        $company = $DB->get_record_sql("SELECT c.*
                                        FROM
                                            {company_users} cu
                                            INNER JOIN {company} c ON cu.companyid = c.id
                                        WHERE cu.userid = :userid
                                        ORDER BY cu.id
                                        LIMIT 1",
                                       array('userid' => $userid));
        return $company;
    }

    /**
     * Gets the user info category record associated to a company
     *
     * Parameters -
     *              $companyid = int;
     *
     * Returns stdclass() or false;
     *
     **/
    public static function get_category($companyid) {
        global $DB;
        if ($category = $DB->get_record_sql("SELECT uic.id, uic.name FROM
                                             {user_info_category} uic, {company} c
                                             WHERE c.id = ".$companyid."
                                             AND ".$DB->sql_compare_text('c.shortname'). "=".
                                             "'".$DB->sql_compare_text('uic.name')."'")) {
            return $category;
        } else {
            return false;
        }
    }

    /**
     * Get company role templates
     *
     **/
    public static function get_role_templates($companyid = 0) {
        global $DB;

        $context = context_system::instance();
        if (iomad::has_capability('block/iomad_company_admin:company_add', $context)) {
            $templates = $DB->get_records_menu('company_role_templates', array(), 'name', 'id,name');
        } else {
            $templates = $DB->get_records_sql_menu("SELECT crt.id,crt.name FROM {company_role_templates} crt
                                                    JOIN {company_role_templates_ass} crta
                                                    ON (crt.id = crta.templateid)
                                                    WHERE crta.companyid = :companyid
                                                    ORDEr BY crt.name",
                                                    array('companyid' => $companyid));
        }
        $templates = array('i' => get_string('inherit', 'block_iomad_company_admin')) + $templates;

        // Add the default.
        $templates = array(0 => get_string('none')) + $templates;

        return $templates;
    }

    /**
     * Apply company role templates
     *
     **/
    public function apply_role_templates($templateid = 0) {
        global $DB;

        if (!empty($templateid)) {
            $restrictions = $DB->get_records('company_role_templates_caps', array('templateid' => $templateid));
        } else {
            // Get the same role entries as for the parent company id.
            $restrictions = $DB->get_records('company_role_restriction', array('companyid' => $this->get_parentid()));
        }

        // Insert the restrictions.
        // Remove them first.
        $DB->delete_records('company_role_restriction', array('companyid' => $this->id));

        // Add the template.
        foreach ($restrictions as $restriction) {
            $DB->insert_record('company_role_restriction', array('companyid' => $this->id, 'roleid' => $restriction->roleid, 'capability' => $restriction->capability));
        }
    }

    /**
     * Assign company role templates
     *
     **/
    public function assign_role_templates($templates = array(), $clear = false) {
        global $DB;

        // Deal with any children.
        $children = $this->get_child_companies_recursive();
        foreach ($children as $child) {
            $childcompany = new company($child->id);
            $childcompany->assign_role_templates($templates, $clear);
        }

        // Final Deal with our own.
        if ($clear) {
            $DB->delete_records('company_role_templates_ass', array('companyid' => $this->id));
        }
        foreach ($templates as $templateid) {
            $DB->insert_record('company_role_templates_ass', array('companyid' => $this->id, 'templateid' => $templateid));
        }
    }

    /**
     * Associates a course to a company
     *
     * Parameters -
     *              $course = stdclass();
     *              $departmentid = int;
     *              $own = boolean;
     *
     **/
    public function add_course($course, $departmentid=0, $own=false, $licensed=false) {
        global $DB, $CFG;

        if ($departmentid != 0 ) {
            // Adding to a specified department.
            $companydepartment = $departmentid;
        } else {
            // Put course in default company department.
            $companydepartmentnode = self::get_company_parentnode($this->id);
            $companydepartment = $companydepartmentnode->id;
        }
        if (!$DB->record_exists('company_course', array('companyid' => $this->id,
                                                       'courseid' => $course->id))) {
            $DB->insert_record('company_course', array('companyid' => $this->id,
                                                      'courseid' => $course->id,
                                                      'departmentid' => $companydepartment));
        }

        // Set up defaults for course management.
        if (!$DB->get_record('iomad_courses', array('courseid' => $course->id))) {
            $DB->insert_record('iomad_courses', array('courseid' => $course->id,
                                                         'licensed' => $licensed,
                                                         'shared' => 0));
        }
        // Set up manager roles.
        if (!$licensed && $CFG->iomad_autoenrol_managers) {
            if ($companymanagers = $DB->get_records_sql("SELECT * FROM {company_users}
                                                         WHERE companyid = :companyid
                                                         AND managertype != 0", array('companyid' => $this->id))) {
                $companycoursenoneditorrole = $DB->get_record('role',
                                                   array('shortname' => 'companycoursenoneditor'));
                $companycourseeditorrole = $DB->get_record('role',
                                                            array('shortname' => 'companycourseeditor'));
                foreach ($companymanagers as $companymanager) {
                    if ($user = $DB->get_record('user', array('id' => $companymanager->userid,
                                                              'deleted' => 0)) ) {
                        if ($DB->record_exists('course', array('id' => $course->id))) {
                            if (!$own) {
                                // Not created by a company manager.
                                company_user::enrol($user, array($course->id), $this->id,
                                                    $companycoursenoneditorrole->id);
                            } else {
                                if ($companymanager->managertype == 2) {
                                    // Assign the department manager course access role.
                                    company_user::enrol($user, array($course->id), $this->id,
                                                        $companycoursenoneditorrole->id);
                                } else {
                                    // Assign the company manager course access role.
                                    company_user::enrol($user, array($course->id), $this->id,
                                                        $companycourseeditorrole->id);
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($own && $departmentid == 0) {
            // Add it to the list of company created courses.
            if (!$DB->record_exists('company_created_courses', array('companyid' => $this->id,
                                                                     'courseid' => $course->id))) {
                $DB->insert_record('company_created_courses', array('companyid' => $this->id,
                                                                    'courseid' => $course->id));
            }
        }

        return true;
    }

    /**
     * Removes a course from a company
     *
     * Parameters -
     *              $course = stdclass();
     *              $companyid = int;
     *              $departmentid = int;
     *
     **/
    public static function remove_course($course, $companyid, $departmentid=0) {
        global $DB;
        if ($departmentid == 0) {
            // Deal with the company departments.
            $companydepartments = $DB->get_records('department', array ('company' => $companyid));
            // Check if it was a company created course and remove if it was.
            if ($companycourse = $DB->get_record('company_created_courses',
                                                 array('companyid' => $companyid,
                                                       'courseid' => $course->id))) {
                $DB->delete_records('company_created_courses', array('id' => $companycourse->id));
            }
            // Check if its an unshared course in iomad.
            if ($DB->get_record('iomad_courses', array('courseid' => $course->id, 'shared' => 0))) {
                $DB->delete_records('iomad_courses', array('courseid' => $course->id, 'shared' => 0));
            }
            $DB->delete_records('company_course', array('companyid' => $companyid,
                                                       'courseid' => $course->id));
        } else {
            // Put course in default company department.
            $companydepartment = self::get_company_parentnode($companyid);
            self::assign_course_to_department($companydepartment->id, $course->id, $companyid);
        }

        return true;
    }

    /**
     * Gets the copmpany defined user account default variables
     *
     * Returns stdclass();
     *
     **/
    public function get_user_defaults() {
        global $DB;

        $companyrecord = $DB->get_record('company', array('id' => $this->id),
                       'city, country, maildisplay, mailformat, maildigest, autosubscribe,
                        trackforums, htmleditor, screenreader, timezone, lang',
                        MUST_EXIST);

        return $companyrecord;
    }

    /**
     * Get the user ids associated to a company
     * does not pass back any managers
     *
     * returns stdclass();
     *
     **/
    public function get_user_ids() {
        global $DB;

        // By default wherecondition retrieves all users except the
        // deleted, not confirmed and guest.
        $params['companyid'] = $this->id;
        $params['companyidforjoin'] = $this->id;

        $sql = " SELECT u.id, u.id AS mid
                FROM
	                {company_users} cu
                    INNER JOIN {user} u ON (cu.userid = u.id)
                WHERE u.deleted = 0
                      AND cu.managertype = 0";

        $order = ' ORDER BY u.lastname ASC, u.firstname ASC';

        return $DB->get_records_sql_menu($sql . $order, $params);
    }

    /**
     * Get all the user ids associated to a company
     *
     * returns stdclass();
     *
     **/
    public function get_all_user_ids() {
        global $DB;

        // By default wherecondition retrieves all users except the
        // deleted, not confirmed and guest.
        $params['companyid'] = $this->id;
        $params['companyidforjoin'] = $this->id;

        $sql = " SELECT u.id, u.id AS mid
                FROM
	                {company_users} cu
                    INNER JOIN {user} u ON (cu.userid = u.id)
                WHERE u.deleted = 0
                AND cu.companyid = :companyid";

        $order = ' ORDER BY u.lastname ASC, u.firstname ASC';

        return $DB->get_records_sql_menu($sql . $order, $params);
    }

    /**
     * Associates a user to a company
     *
     * Parameters -
     *              $userid = int;
     *              $departmentid = int;
     *              $managertype = int;
     *
     **/
    public function assign_user_to_company($userid, $departmentid = 0, $managertype = 0, $ws = false) {
        global $DB;

        // Were we passed a departmentid?
        if (!empty($departmentid)) {
            // Check its a department in this company.
            if (!$DB->get_record('department', array('id' => $departmentid, 'company' => $this->id))) {
                $defaultdepartment = self::get_company_parentnode($this->id);
                $departmentid = $defaultdepartment->id;
            }
        } else {
            // Make it the default department id.
            $defaultdepartment = self::get_company_parentnode($this->id);
            $departmentid = $defaultdepartment->id;
        }

        // Were we passed a manager type?  Check it.
        if ($managertype > 2) {
            // Default is standard user.
            $managertype = 0;
        }

        // if this is the only company, set the theme.
        if (!$DB->get_records('company_users', array('userid' => $userid))) {
            $DB->set_field('user', 'theme', $this->get_theme(), array('id' => $userid));
        }

        // Create the record.
        $userrecord = array();
        $userrecord['departmentid'] = $departmentid;
        $userrecord['userid'] = $userid;
        $userrecord['managertype'] = $managertype;
        $userrecord['companyid'] = $this->id;

        if ($DB->get_record('company_users', array('companyid' => $this->id, 'userid' => $userid))) {
            // Already in this company.  Nothing left to do.
            return true;
        }

        // Moving a user.
        if (!$DB->insert_record('company_users', $userrecord)) {
            if ($ws) {
                return false;
            } else {
                print_error(get_string('cantassignusersdb', 'block_iomad_company_admin'));
            }
        }

        return true;
    }

    /**
     * Removes a user from a company
     *
     * Parameters -
     *              $userid = int;
     *
     **/
    public function unassign_user_from_company($userid, $ws = false) {
        global $DB;

        // Moving a user.
        if (!$userrecord = $DB->get_record('company_users', array('companyid' => $this->id,
                                                                  'userid' => $userid))) {
            if ($ws) {
                return false;
            } else {
                print_error(get_string('cantassignusersdb', 'block_iomad_company_admin'));
            }
        }

        // Delete the record.
        $DB->delete_records('company_users', array('id' => $userrecord->id));

        // Deal with the company theme.
        $DB->set_field('user', 'theme', '', array('id' => $userid));

        return true;
    }

    public function assign_parent_managers($parentid, $finalcompanyid = 0) {
        global $DB;

        if (empty($finalcompanyid)) {
            $finalcompanyid = $this->id;
        }
        $parentcompany = new company($parentid);
        $parentmanagers = $parentcompany->get_company_managers();
        $finalcompany = new company($finalcompanyid);
        foreach ($parentmanagers as $managerid) {

            $finalcompany->assign_user_to_company($managerid->userid, 0, 1, true);
        }
        // Is there any more?
        $grandparentid = $parentcompany->get_parentid();
        if (!empty($grandparentid)) {
            $parentcompany->assign_parent_managers($grandparentid, $finalcompanyid);
        }
    }

    public function unassign_parent_managers($parentid, $finalcompanyid = 0) {
        global $DB;

        if (empty($finalcompanyid)) {
            $finalcompanyid = $this->id;
        }
        $parentcompany = new company($parentid);
        $parentmanagers = $parentcompany->get_company_managers();

        $finalcompany = new company($finalcompanyid);
        foreach ($parentmanagers as $managerid) {
            $finalcompany->unassign_user_from_company($managerid->userid, true);
        }
        // Is there any more?
        $grandparentid = $parentcompany->get_parentid();
        if (!empty($grandparentid)) {
            $parentcompany->unassign_parent_managers($grandparentid, $finalcompanyid);
        }
    }

    public function get_company_managers($managertype=1) {
        global $DB;

        return $DB->get_records('company_users', array('companyid' => $this->id, 'managertype' => $managertype), null, 'userid');
    }

    // Department functions.

    /**
     * Set up default company department.
     *
     * Parameters -
     *              $companyid = int;
     *
     **/
    public static function initialise_departments($companyid) {
        global $DB;
        $company = $DB->get_record('company', array('id' => $companyid));
        $parentnode = array();
        $parentnode['shortname'] = $company->shortname;
        $parentnode['name'] = $company->name;
        $parentnode['company'] = $company->id;
        $parentnode['parent'] = 0;
        $parentnodeid = $DB->insert_record('department', $parentnode);
        // Get the company user's ids.
        if ($userids = $DB->get_records('company_users', array('companyid' => $companyid))) {
            foreach ($userids as $userid) {
                $userid->departmentid = $parentnodeid;
                $DB->update_record('company_users', $userid);
            }
        }
        // Get the company courses.
        if ($companycourses = $DB->get_records('company_course', array('companyid' => $company->id))) {
            foreach ($companycourses as $companycourse) {
                $companycourse->departmentid = $parentnodeid;
                $DB->update_record('company_course', $companycourse);
            }
        }
    }

    /**
     * Set up default company department.
     *
     * Parameters -
     *              $companyid = INT;
     *              $currentdepartment = department obtject;
     *              $importtree = json decoded department tree;
     *              $toplevel = boolean - true if this is the first time the tree is being accessed so initial value will be the same as the parent department.
     *
     **/
    public static function import_departments($companyid, $currentdepartment, $importtree, $toplevel = false) {
        global $DB;

        if (!$toplevel) {
            // Creating a new department.
            $newdepartment = new stdclass();
            $newdepartment->name = $importtree->name;
            $newdepartment->shortname = $importtree->shortname;
            $newdepartment->company = $companyid;
            $newdepartment->parent = $currentdepartment->id;
            $newdepartment->id = $DB->insert_record('department', $newdepartment);
        } else {
            // Already created so pass it.
            $newdepartment = $currentdepartment;
        }
        // Are there any children?
        if (empty($importtree->children)) {
            return;
        } else {
            // Create them.
            foreach ($importtree->children as $child) {
                self::import_departments($companyid, $newdepartment, $child, false);
            }
        }
    }

    /**
     * Get the department a user is associated to.
     *
     * Parameters -
     *              $user = stdclass();
     *
     * Returns stdclass();
     *
     **/
    public function get_userlevel($user) {

        global $DB;
        if (is_siteadmin()) {
            return self::get_company_parentnode($this->id);
        }
        if ($userdepartment = $DB->get_record('company_users', array('userid' => $user->id, 'companyid' => $this->id))) {
            $userlevel = $DB->get_record('department', array('id' => $userdepartment->departmentid));
            return $userlevel;
        } else {
            return false;
        }
    }

    /**
     * Get the department a user is associated to.
     *
     * Parameters -
     *              $user = stdclass();
     *
     * Returns stdclass();
     *
     **/
    public static function get_usersupervisor($userid) {
        global $DB, $CFG;

        // get the company info.
        $companyinfo = self::get_company_byuserid($userid);
        if (!empty($companyinfo->emailprofileid)) {
            // Does the user have one defined by the company field?
            if (!$supervisor = $DB->get_record('user_info_data', array('userid' => $userid, 'fieldid' => $companyinfo->emailprofileid))) {
                return false;
            }
        } else if (!empty($CFG->companyemailprofileid)) {
            // Does the user have one defined by the default field?
            if (!$supervisor = $DB->get_record('user_info_data', array('userid' => $userid, 'fieldid' => $CFG->companyemailprofileid))) {
                return false;
            }
        }
        if (empty($supervisor)) {
            return false;
        }

        $emaillist = array();
        foreach(explode(',', $supervisor->data) as $testemail) {
            // Is it a valid email address?
            if (validate_email($testemail)) {
                // Are we diverting everything??
                if (empty($CFG->divertallemailsto)) {
                    $emaillist[$testemail] = $testemail;
                } else {
                    $emaillist[$CFG->divertallemailsto] = $CFG->divertallemailsto;
                }
            }
        }

        return $emaillist;
    }

    /**
     * Get the department details given an id.
     *
     * Parameters -
     *              $departmentid = int;
     *
     * Returns stdclass();
     *
     **/
    public static function get_departmentbyid($departmentid) {
        global $DB;
        return $DB->get_record('department', array('id' => $departmentid));
    }

    public static function get_parentdepartments($department) {
        global $DB;

        $returnarray = $department;
        // Check to see if its the top node.
        if (isset($department->id)) {
            if ($department->parent != 0) {
                $parent = self::get_department_parentnode($department->id);
                if ($parent->parent != 0 ) {

                    $returnarray->parents[] = self::get_parentdepartments($parent);
                } else {
                    $returnarray->parents[] = $parent;
                }
            }
        }

        return $returnarray;
    }

    /**
     * Get list of departments which are below this on on the tree.
     *
     * Parameters -
     *              $parent = stdclass();
     *
     * Returns array();
     *
     **/
    public static function get_subdepartments($parent, $ignorecurrentbranch = false) {
        global $DB;

        // Are we trimming a current branch?
        if (isset($parent->id) && $parent->id == $ignorecurrentbranch) {
            return $parent;
        }


        $returnarray = $parent;
        // Check to see if its the top node.
        if (isset($parent->id)) {
            if ($children = $DB->get_records('department', array('parent' => $parent->id), 'name', '*')) {
                foreach ($children as $child) {
                    $returnarray->children[] = self::get_subdepartments($child, $ignorecurrentbranch);
                }
            }
        }

        return $returnarray;
    }

    /**
     * Get an array of all subdepartments to be used in a select.
     *
     * Parameters -
     *              $parent = stdclass();
     *
     * Returns array();
     *
     **/
    public static function get_subdepartments_list($parent) {
        $subdepartmentstree = self::get_subdepartments($parent);
        $subdepartmentslist = self::get_department_list($subdepartmentstree);
        $returnlist = self::array_flatten($subdepartmentslist);
        unset($returnlist[$parent->id]);
        return $returnlist;
    }

    /**
     * Get a list of all departments
     *
     * Parameters -
     *              $tree = stdclass();
     *              $path = text;
     *
     * Returns array();
     *
     **/
    public static function get_department_list( $tree, $path='' ) {

        $flatlist = array();
        if (isset($tree->id)) {
            if (!empty($path)) {
                $flatlist[$tree->id] = $path . ' / ' . $tree->name;
            } else {
                $flatlist[$tree->id] = $tree->name;
            }
        }

        if (!empty($tree->children)) {
            foreach ($tree->children as $child) {
                if (!empty($path)) {
                    $flatlist[$child->id] = self::get_department_list($child, $path.' / '.$tree->name);
                } else {
                    $flatlist[$child->id] = self::get_department_list($child, $tree->name);
                }
            }
        }

        return $flatlist;
    }

    /**
     * Get a list of all departments
     *
     * Parameters -
     *              $tree = stdclass();
     *              $path = text;
     *
     * Returns array();
     *
     **/
    public static function get_parents_list($tree, &$return = array()) {

        if (isset($tree->id)) {
            $return[$tree->id] = $tree->id;
        }

        if (!empty($tree->parents)) {
            foreach ($tree->parents as $parent) {
                self::get_parents_list($parent, $return);
            }
        }
    }

    /**
     * The top level department given a companyid
     *
     * Parameters -
     *              $companyid = int;
     *
     * Returns stdclass() || false;
     *
     **/
    public static function get_company_parentnode($companyid) {
        global $DB;
        if (!$parentnode = $DB->get_record('department', array('company' => $companyid,
                                                               'parent' => '0'))) {
            self::initialise_departments($companyid);
            $parentnode = $DB->get_record('department', array('company' => $companyid,
                                                               'parent' => '0'));
        }
        return $parentnode;
    }

    /**
     * The parent department given a departmentid
     *
     * Parameters -
     *              $departmentid = int;
     *
     * Returns stdclass() || false;
     *
     **/
    public static function get_department_parentnode($departmentid) {
        global $DB;
        if ($department = $DB->get_record('department', array('id' => $departmentid))) {
            $parent = $DB->get_record('department', array('id' => $department->parent));
            return $parent;
        } else {
            return false;
        }
    }

    /**
     * All parent departments given a departmentid
     *
     * Parameters -
     *              $departmentid = int;
     *
     * Returns stdclass() || false;
     *
     **/
    public static function get_department_parentnodes($departmentid) {
        global $DB;

        $parents = array();
        while ($myparent = self::get_department_parentnode($departmentid)) {
            $parents[$myparent->id] = $myparent;
            $departmentid = $myparent->id;
        }
        return $parents;
    }

    /**
     * The top level department given a departmentid
     *
     * Parameters -
     *              $departmentid = int;
     *
     * Returns int;
     *
     **/
    public static function get_top_department($departmentid) {
        global $DB;
        $department = $DB->get_record('department', array('id' => $departmentid));
        $parentnode = self::get_company_parentnode($department->company);
        return $parentnode->id;
    }

    /**
     * Gets a department tree list given a company id.
     *
     * Parameters -
     *              $companyid = int;
     *
     * Returns array()
     *
     **/
    public static function get_all_departments($company) {

        $parentlist = array();
        $parentnode = self::get_company_parentnode($company);
        $parentlist[$parentnode->id] = array($parentnode->id => $parentnode->name);
        $departmenttree = self::get_subdepartments($parentnode);
        $departmentlist = self::array_flatten($parentlist +
                                              self::get_department_list($departmenttree));
        return $departmentlist;
    }

    /**
     * Get array of all departments given companyid
     * Used to display select tree
     * @param int companyid
     * @return array
     */
    public static function get_all_departments_raw($companyid) {
        $parentlist = array();
        $parentnode = self::get_company_parentnode($companyid);
        $departmenttree = self::get_subdepartments($parentnode);

        return $departmenttree;
    }

    /**
     * Get array of all departments given companyid
     * Used to display select tree
     * @param int companyid
     * @return array
     */
    public static function get_all_subdepartments_raw($departmentid, $ignorecurrentbranch = false) {

        // Are we trimming a current branch?
        if ($departmentid == $ignorecurrentbranch) {
            return;
        }

        $departmentnode = self::get_departmentbyid($departmentid);
        $departmenttree = self::get_subdepartments($departmentnode, $ignorecurrentbranch);

        return $departmenttree;
    }

    /**
     * function to flatten a multi-dimension array to a single dimension array.
     *
     * Parameters -
     *              $array = array();
     *              &$result = array();
     *
     * Returns array();
     *
     **/
    public static function array_flatten($array, &$result=null) {

        $r = null === $result;
        $i = 0;
        foreach ($array as $key => $value) {
            $i++;
            if (is_array($value)) {
                self::array_flatten($value, $result);
            } else {
                $result[$key] = $value;
            }
        }
        if ($r) {
            return $result;
        }
    }

    /**
     * Gets a list of the sub department tree list given a department id
     * including the passed department.
     *
     * Parameters -
     *              $parentnodeid = int;
     *
     * Returns array()
     *
     **/
    public static function get_all_subdepartments($parentnodeid) {

        $parentnode = self::get_departmentbyid($parentnodeid);
        $parentlist = array();
        $parentlist[$parentnodeid] = $parentnode->name;
        $departmenttree = self::get_subdepartments($parentnode);
        $departmentlist = self::array_flatten($parentlist +
                                              self::get_department_list($departmenttree));
        return $departmentlist;
    }

    /**
     * Gets a list of all users from this department down
     * including the passed department.
     *
     * Parameters -
     *              $departmentid = int;
     *
     * Returns array()
     *
     **/
    public static function get_recursive_department_users($departmentid) {
        global $DB;

        $departmentlist = self::get_all_subdepartments($departmentid);
        $userlist = array();
        foreach ($departmentlist as $id => $value) {
            $departmentusers = self::get_department_users($id);
            $userlist = $userlist + $departmentusers;
        }
        return $userlist;
    }

    /**
     * Gets all of the users that manager is responsible for
     *
     * Parameters -
     *             $companyid = int;
     *             $departmentid = int;
     *
     * Returns array()
     *
     **/
    public static function get_my_users($companyid=0, $departmentid=0) {
        global $USER;

        if (empty($companyid)) {
            return array();
        }
        $company = new company($companyid);
        if (empty($departmentid)) {
            if (is_siteadmin($USER->id)) {
                $department = self::get_company_parentnode($companyid);
                $departmentid = $department->id;
            } else {
                $department = $company->get_userlevel($USER);
                $departmentid = $department->id;
            }
        }
        return self::get_recursive_department_users($departmentid);
    }

    /**
     * Gets a list of the managers for that user
     *
     * Parameters -
     *             $userid = int;
     *             $managertype = int;
     *
     * Returns string
     *
     **/
    public static function get_my_managers($userid, $managertype) {
        global $DB, $USER;

        // Get the users department.
        $usercompanyinfo = $DB->get_record('company_users', array('userid' => $userid));
        // Get the list of parent departments.
        $userdepartment = self::get_departmentbyid($usercompanyinfo->departmentid);
        $departmentlist = self::get_parentdepartments($userdepartment);
        self::get_parents_list($departmentlist, $departments);

        // Get the managers in that list of departments.
        $managers = $DB->get_records_sql("SELECT userid FROM {company_users}
                                          WHERE managertype = :managertype
                                          AND userid != :userid
                                          AND departmentid IN (".implode(',', array_keys($departments)).")",
                                          array('managertype' => $managertype, 'userid' => $USER->id));
        //  return them.
        return $managers;
    }

    /**
     * Gets a list of the users that manager is responsible for
     *
     * Parameters -
     *             $companyid = int;
     *             $departmentid = int;
     *
     * Returns string
     *
     **/
    public static function get_my_users_list($companyid=0, $departmentid=0) {
        global $USER;

        if (empty($companyid)) {
            return array();
        }
        $userlist = self::get_my_users($companyid, $departmentid);
        $users = array();
        foreach ($userlist as $user) {
            $users[] = $user->userid;
        }
        return implode(',', $users);
    }

    /**
     * Gets a list of the users at this department id
     *
     * Parameters -
     *              $departmentid = int;
     *
     * Returns array()
     *
     **/
    public static function get_department_users($departmentid) {
        global $DB;
        if ($departmentusers = $DB->get_records('company_users',
                                                 array('departmentid' => $departmentid),
                                                 null,
                                                 'userid,id,companyid,managertype,departmentid,suspended')) {
            return $departmentusers;
        } else {
            return array();
        }
    }

    /**
     * Assign a user to a department.
     *
     * Parameters -
     *              $departmentid = int;
     *              $userid = int;
     *
     **/
    public static function assign_user_to_department($departmentid, $userid, $managertype = 0, $ws = false) {
        global $DB;

        $userrecord = array();
        $userrecord['departmentid'] = $departmentid;
        $userrecord['userid'] = $userid;

        // Moving a user.
        if ($currentuser = $DB->get_record('company_users', array('userid' => $userid))) {
            $currentuser->departmentid = $departmentid;
            if ($ws && !empty($managertype)) {
                $currentuser->managertype = $managertype;
            }
            if (!$DB->update_record('company_users', $currentuser)) {
                if ($ws) {
                    return false;
                } else {
                    print_error(get_string('cantupdatedepartmentusersdb', 'block_iomad_company_admin'));
                }
            }
        }
        return true;
    }

    /**
     * Creates a new department
     *
     * Parameters -
     *              $departmentid = int;
     *              $companyid = int;
     *              $fullname = string;
     *              $shortname = string;
     *              $parentid = int;
     *
     **/
    public static function create_department($departmentid, $companyid, $fullname,
                                      $shortname, $parentid=0) {
        global $DB;
        $newdepartment = array();
        if (!empty($departmentid)) {
            if ($departmentid == $parentid) {
                return;
            }
            $newdepartment['id'] = $departmentid;
        }
        if ($parentid) {
            $newdepartment['parent'] = $parentid;
        }
        $newdepartment['company'] = $companyid;
        $newdepartment['name'] = $fullname;
        $newdepartment['shortname'] = $shortname;
        if (isset($newdepartment['id'])) {
            // We are editing a current department.
            if (!$DB->update_record('department', $newdepartment)) {
                print_error(get_string('cantupdatedepartmentdb', 'block_iomad_company_admin'));
            }
        } else {
            // Adding a new department.
            if (!$DB->insert_record('department', $newdepartment)) {
                print_error(get_string('cantinsertdepartmentdb', 'block_iomad_company_admin'));
            }
        }

        return true;
    }

    /**
     * Delete a department.
     *
     * Parameters -
     *              $departmentid = int;
     *
     **/
    public static function delete_department($departmentid) {
        global $DB;
        if (!$DB->delete_records('department', array('id' => $departmentid))) {
            print_error(get_string('cantdeletedepartmentdb', 'blocks_iomad_company_admin'));
        }
        return true;
    }

    /**
     * Delete all departments from this point down moving all the associated things to targetid
     *
     * Parameters -
     *              $departmentid = int;
     *              $targetid = int;
     *
     **/
    public static function delete_department_recursive($departmentid, $targetdepartment=0) {
        // Get all the users from here and below.
        $userlist = self::get_recursive_department_users($departmentid);
        $departmentlist = self::get_all_subdepartments($departmentid);
        if ($targetdepartment == 0) {
            // Moving users to the parent node of the current department.
            $parentnode = self::get_department_parentnode($departmentid);
            $targetdepartment = $parentnode->id;
        }
        foreach ($userlist as $user) {
            //  Move the users.
            self::assign_user_to_department($targetdepartment, $user->id);
        }
        foreach ($departmentlist as $id => $value) {
            self::delete_department($id);
        }
    }

    /**
     * Check if a user is a manger of this department.
     *
     * Parameters -
     *              $departmentid = int;
     *
     * Return boolean;
     **/
    public static function can_manage_department($departmentid) {
        global $DB, $USER;
        if (iomad::has_capability('block/iomad_company_admin:edit_all_departments',
                                    context_system::instance())) {
            return true;
        } else if (!iomad::has_capability('block/iomad_company_admin:edit_departments',
                                    context_system::instance())) {
            return false;
        } else {
            $departmentrec = $DB->get_record('department', array('id' => $departmentid));
            $company = new company($departmentrec->company);
            // Get the list of departments at and below the user assignment.
            $userhierarchylevel = $company->get_userlevel($USER);
            $subhierarchytree = self::get_all_subdepartments($userhierarchylevel->id);
            if (isset($subhierarchytree[$departmentid])) {
                // Current department is a child of the users assignment.
                return true;
            } else {
                return false;
            }
        }
        // We shouldn't get this far, return a default no.
        return false;
    }

    /**
     * Gets a list of all courses from this department down
     * including the passed department.
     *
     * Parameters -
     *              $departmentid = int;
     *
     * Returns array()
     *
     **/
    public static function get_recursive_department_courses($departmentid) {
        global $DB;

        $departmentlist = self::get_all_subdepartments($departmentid);
        $courselist = array();
        foreach ($departmentlist as $id => $value) {
            $departmentcourses = self::get_department_courses($id);
            $courselist = $courselist + $departmentcourses;
        }
        // Get the top level courses.
        $companydepartment = self::get_top_department($departmentid);
        if ($companydepartment != $departmentid ) {
            $topdepartmentcourses = self::get_department_courses($companydepartment);
            $courselist = $courselist + $topdepartmentcourses;
        }
        //  Get the shared courses.
        $sharedcourses = $DB->get_records('iomad_courses', array('shared' => 1));
        return $courselist + $sharedcourses;
    }

    /**
     * Gets a list of all courses in this department
     *
     * Parameters -
     *              $departmentid = int;
     *
     * Returns array()
     *
     **/
    public static function get_department_courses($departmentid) {
        global $DB;
        if ($departmentcourses = $DB->get_records('company_course',
                                                   array('departmentid' => $departmentid))) {
            return $departmentcourses;
        } else {
            return array();
        }
    }

    /**
     * Assign a course to this department
     *
     * Parameters -
     *              $departmentid = int;
     *              $courseid = int;
     *              $companyid = int;
     *
     **/
    public static function assign_course_to_department($departmentid, $courseid, $companyid) {
        global $DB;

        // Moving a course.
        // Get all the department assignments which may exist taking
        // shared courses into consideration.
        if ($currentcourses = $DB->get_records('company_course',
                                                array('courseid' => $courseid))) {
            $foundcourse = false;
            foreach ($currentcourses as $currentcourse) {
                // Check if the found record belongs to the current company.
                if ($DB->get_record('department', array('company' => $companyid,
                                                        'id' => $departmentid))) {
                    $foundcourse = true;
                    //  Update it.
                    $currentcourse->departmentid = $departmentid;
                    if (!$DB->update_record('company_course', $currentcourse)) {
                        print_error(get_string('cantupdatedepartmentcoursesdb',
                                               'block_iomad_company_admin'));
                    }
                    break;
                }
            }
            if (!$foundcourse) {
                // Assigning a shared course to a new company.
                $courserecord = array();
                $courserecord['departmentid'] = $departmentid;
                $courserecord['courseid'] = $courseid;
                $courserecord['companyid'] = $companyid;
                if (!$DB->insert_record('company_course', $courserecord)) {
                    print_error(get_string('cantinsertdepartmentcoursesdb',
                                           'block_iomad_company_admin'));
                }
            }
        } else {
            // Assigning a new course to a company.
            $courserecord = array();
            $courserecord['departmentid'] = $departmentid;
            $courserecord['courseid'] = $courseid;
            $courserecord['companyid'] = $companyid;
            if (!$DB->insert_record('company_course', $courserecord)) {
                print_error(get_string('cantinsertdepartmentcoursesdb',
                                       'block_iomad_company_admin'));
            }
        }
        return true;
    }

    /**
     * Get a list of departments a course is associated to
     *
     * Parameters -
     *              $courseid = int;
     *
     *  Return array();
     **/
    public static function get_departments_by_course($courseid) {
        global $DB;
        if ($depts = $DB->get_records('company_course', array('courseid' => $courseid),
                                                                   null, 'departmentid')) {
            return array_keys($depts);
        } else {
            return array();
        }
    }

    // Licenses stuff.

    /**
     * Gets a list of all licenses from this department down
     * including the passed department.
     *
     * Parameters -
     *              $departmentid = int;
     *
     * Returns array()
     *
     **/
    public static function get_recursive_departments_licenses($departmentid) {

        // Get all the courses for this department down.
        $courses = self::get_recursive_department_courses($departmentid);
        $licenselist = array();
        foreach ($courses as $course) {
            $courselicenses = self::get_course_licenses($course->courseid);
            $licenselist = $licenselist + $courselicenses;
        }
        return $licenselist;
    }

    /**
     * Gets a list of all licenses for this course
     *
     * Parameters -
     *              $courseid = int;
     *
     * Returns array()
     *
     **/
    public static function get_course_licenses($courseid) {
        global $DB;
        if ($licenses = $DB->get_records('companylicense_courses', array('courseid' => $courseid),
                                                                          null, 'licenseid')) {
            return $licenses;
        } else {
            return array();
        }
    }

    /**
     * Gets a list of all courses for this license
     *
     * Parameters -
     *              $licenseid = int;
     *
     * Returns array()
     *
     **/
    public static function get_courses_by_license($licenseid) {
        global $DB;
        if ($courseids = $DB->get_records('companylicense_courses', array('licenseid' => $licenseid),
                                                                           null, 'courseid')) {
            $sql = "SELECT id, fullname FROM {course} WHERE id IN (".
                      implode(',', array_keys($courseids)).
                   ") ";
            if ($courses = $DB->get_records_sql($sql)) {
                return $courses;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    /** Update license usage.
     *
     * Parameters -
     *              $licenseid = int;
     *
     **/
    public static function update_license_usage($licenseid) {
        global $DB;

        // Get the allocation from any child licenses.
        if ($childusage = $DB->get_records_sql("SELECT sum(allocation) AS total
                                                FROM {companylicense}
                                                WHERE parentid = :parentid",
                                                array('parentid' => $licenseid))) {
            $child = array_pop($childusage);
            $childtotal = $child->total;
        } else {
            $childtotal = 0;
        }

        // Get the number of user assigned licenses for this license.
        if ($userusage = $DB->get_records_sql("SELECT count(id) AS total
                                               FROM {companylicense_users}
                                               WHERE licenseid = :licenseid",
                                               array('licenseid' => $licenseid))) {

            $user = array_pop($userusage);
            $usertotal = $user->total;
        } else {
            $usertotal = 0;
        }

        // If we have a license, update it.
        if ($license = $DB->get_record('companylicense', array('id' => $licenseid))) {
            $license->used = $childtotal + $usertotal;
            $DB->update_record('companylicense', $license);
        }
    }


    /** Check if a license is in a child company.
     *
     * Parameters -
     *              $licenseid = int;
     *
     * Returns Boolean
     *
     **/
    public function is_child_license($licenseid) {
        global $DB;

        if (!$licenseinfo = $DB->get_record('companylicense', array('id' => $licenseid))) {
            return false;
        }
        // Get the child companies.
        $childcompanies = $this->get_child_companies_recursive();

        // Check if they match the license company?
        foreach ($childcompanies as $childcompany) {
            if ($licenseinfo->companyid == $childcompany->id) {
                // If so then it is.
                return true;
            }
        }

        // Default return false.
        return false;
    }

    public function get_menu_courses($shared = false, $licensed = false, $groups = false, $default = true) {
        global $DB;

        // Deal with license option.
        if ($licensed) {
            $licensesql = "c.id NOT IN (
                             SELECT courseid FROM {iomad_courses}
                             WHERE licensed = 1
                           )
                           AND";
            $sharedlicsql = " AND licensed != 1 ";
        } else {
            $licensesql = "";
            $sharedlicsql = "";
        }

        // Deal with shared option.
        if ($shared) {
            $sharedsql = " OR
                           c.id IN (
                              SELECT courseid FROM {iomad_courses}
                              WHERE shared = 1
                              $sharedlicsql
                              AND courseid NOT IN (
                                  SELECT courseid FROM {company_course}
                                  WHERE companyid = :companyid2
                              )
                          )";
        } else {
            $sharedsql = "";
        }

        // Deal with groups option.
        if ($groups) {
            $groupsql = "c.groupmode != 0 AND";
        } else {
            $groupsql = "";
        }

        // Get the courses.
        $retcourses = $DB->get_records_sql_menu("SELECT c.id, c.fullname
                                                 FROM {course} c
                                                 WHERE
                                                 $groupsql
                                                 $licensesql
                                                 c.id IN (
                                                     SELECT courseid FROM {company_course}
                                                     WHERE companyid = :companyid
                                                 )
                                                 $sharedsql
                                                 ORDER BY c.fullname",
                                                 array('companyid' => $this->id,
                                                       'companyid2' => $this->id));

        // Add a default entry and return the courses.
        if ($default) {
            return array('0' => get_string('noselection', 'form')) + $retcourses;
        } else {
            return $retcourses;
        }
    }

    public function get_course_groups_menu($courseid) {
        global $DB;

        $retgroups =  $DB->get_records_sql_menu("SELECT g.id, g.description
                                                 FROM {groups} g
                                                 JOIN {company_course_groups} ccg
                                                 ON (g.id = ccg.groupid)
                                                 WHERE ccg.companyid = :companyid
                                                 AND ccg.courseid = :courseid",
                                                 array('companyid' => $this->id,
                                                       'courseid' => $courseid));

        return array('0' => get_string('noselection', 'form')) + $retgroups;
    }


    // Shared course stuff.

    /**
     * Create a company group for the passed course
     *
     * Parameters -
     *              $companyid = int;
     *              $courseid = int;
     *
     * Returns int;
     *
     **/
    public static function create_company_course_group($companyid, $courseid, $groupdata = null) {
        global $CFG, $DB;
        require_once($CFG->dirroot.'/group/lib.php');


        // Creates a company group within a shared course.
        $company = $DB->get_record('company', array('id' => $companyid));
        if (empty($groupdata)) {
            $data = new stdclass();
            $data->timecreated  = time();
            $data->timemodified = $data->timecreated;
            $data->name = $company->shortname;
            $data->description = get_string('coursegroup', 'block_iomad_company_admin') . $company->name;
            $data->courseid = $courseid;
        } else if (!empty($groupdata->groupid)) {
            // Already exists so we are updating it.
            $grouprecord = $DB->get_record('groups', array('id' => $groupdata->groupid), '*', MUST_EXIST);
            $DB->set_field('groups', 'description', $groupdata->description, array('id' => $grouprecord->id));
            return $grouprecord->id;
        } else {
            $data = new stdclass();
            $data->timecreated  = time();
            $data->timemodified = $data->timecreated;
            $data->name = $company->shortname . ' - ' . $groupdata->description;
            $data->description = $groupdata->description;
            $data->courseid = $courseid;
        }

        // Create the group record.
        $groupid = groups_create_group($data);

        // Create the pivot table entry.
        $grouppivot = array();
        $grouppivot['companyid'] = $companyid;
        $grouppivot['courseid'] = $courseid;
        $grouppivot['groupid'] = $groupid;

        // Write the data to the DB.
        if (!$DB->insert_record('company_course_groups', $grouppivot)) {
            print_error(get_string('cantcreatecompanycoursegroup', 'block_iomad_company_admin'));
        }
        return $groupid;
    }

    /**
     * Get the course group name for the company for the passed course
     *
     * Parameters -
     *              $companyid = int;
     *              $courseid = int;
     *
     * Returns string;
     *
     **/
    public static function get_company_groupname($companyid, $courseid) {
        global $DB;
        // Gets the company course groupname.
        $company = $DB->get_record('company', array('id' => $companyid));
        if (!$companygroup = $DB->get_record('company_course_groups', array('companyid' => $companyid,
                                                                          'courseid' => $courseid,
                                                                          'name' => $company->shortname))) {
            // Not got one, create a default.
            $companygroup->groupid = self::create_company_course_group($companyid, $courseid);
        }
        // Get the group information.
        $groupinfo = $DB->get_record('groups', array('id' => $companygroup->groupid));
        return $groupinfo->name;
    }

    /**
     * Get the course group for the company for the passed course
     *
     * Parameters -
     *              $companyid = int;
     *              $courseid = int;
     *
     * Returns stdclass();
     *
     **/
    public static function get_company_group($companyid, $courseid) {
        global $DB;

        $company = $DB->get_record('company', array('id' => $companyid));

        // Gets the company course groupname.
        if (!$companygroup = $DB->get_record_sql("SELECT ccg.*
                                                  FROM {company_course_groups} ccg
                                                  JOIN {groups} g
                                                  ON (ccg.groupid = g.id)
                                                  WHERE ccg.companyid = :companyid
                                                  AND ccg.courseid = :courseid
                                                  AND g.name = :name",
                                                  array('companyid' => $companyid,
                                                        'courseid' => $courseid,
                                                        'name' => $company->shortname))) {
            // Not got one, create a default.
            $companygroup = new stdclass();
            $companygroup->groupid = self::create_company_course_group($companyid, $courseid);
        }
        // Get the group information.
        $groupinfo = $DB->get_record('groups', array('id' => $companygroup->groupid));
        return $groupinfo;
    }

    /**
     * Add a company user to a shared course company group.
     *
     * Parameters -
     *              $courseid = int;
     *              $userid = int;
     *              $companyid = int;
     *
     **/
    public static function add_user_to_shared_course($courseid, $userid, $companyid, $groupid = 0) {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/group/lib.php');

        // Adds a user to a shared course.
        if (empty($groupid)) {
            $company = $DB->get_record('company', array('id' => $companyid));
            // Get the group id.
            if (!$groupinfo = $DB->get_record_sql("SELECT ccg.*
                                                  FROM {company_course_groups} ccg
                                                  JOIN {groups} g
                                                  ON (ccg.groupid = g.id)
                                                  WHERE ccg.companyid = :companyid
                                                  AND ccg.courseid = :courseid
                                                  AND g.name = :name",
                                                  array('companyid' => $companyid,
                                                        'courseid' => $courseid,
                                                        'name' => $company->shortname))) {
                $groupid = self::create_company_course_group($companyid, $courseid);
            } else {
                $groupid = $groupinfo->groupid;
            }
        }

        // Add the user to the group.
        groups_add_member($groupid, $userid);
    }

    /**
     * Remove a company user to a shared course company group.
     *
     * Parameters -
     *              $courseid = int;
     *              $userid = int;
     *              $companyid = int;
     *
     **/
    public static function remove_user_from_shared_course($courseid, $userid, $companyid) {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/group/lib.php');

        // Removes a user from a shared course.
        // Get the group id.
        if (!$groups = $DB->get_records_sql("SELECT gm.groupid
                                                FROM {groups_members} gm
                                                JOIN {groups} g
                                                ON (gm.groupid = g.id)
                                                WHERE g.courseid = :courseid
                                                AND gm.userid = :userid",
                                                array('userid' => $userid,
                                                      'courseid' => $courseid))) {
            return;  // Dont need to remove them.
        } else {
            foreach ($groups as $group)
            // Remove the user from the group.
            groups_remove_member($group->groupid, $userid);
        }

    }

    /**
     * Delete a shared course company group.
     *
     * Parameters -
     *              $companyid = int;
     *              $course = stdclass();
     *              $oktounenroll = boolean;
     *
     **/
    public static function delete_company_course_group($companyid, $course, $oktounenroll=false, $groupid = 0) {
        global $DB;
        // Removes a company group within a shared course.
        // Get the group.
        if ($group = self::get_company_group($companyid, $course->id)) {
            if (empty($groupid) || $groupid == $group->id) {
                // Check there are no members of the group unless oktounenroll.
                if (!$DB->get_records('company_course_groups', array('groupid' => $group->id)) ||
                    $oktounenroll) {
                    // Delete the group.
                    $DB->delete_records('groups', array('id' => $group->id));
                    $DB->delete_records('company_course_groups', array('companyid' => $companyid,
                                                                       'groupid' => $group->id,
                                                                       'courseid' => $course->id));
                    self::remove_course($course, $companyid);
                    return true;
                } else {
                    return "usersingroup";
                }
            } else {
                // Move everyone to the default company group.
                if ($groupusers = $DB->get_records('groups_members', array('groupid' => $groupid))) {
                    foreach($groupusers as $user) {
                        groups_add_member($group->id, $user->userid);
                        groups_remove_member($groupid, $user->userid);
                    }
                }
                $DB->delete_records('groups', array('id' => $groupid));
                $DB->delete_records('company_course_groups', array('groupid' => $groupid));
            }
        }
    }

    /**
     * Adds all company users to a shared course company group.
     *
     * Parameters -
     *              $companyid = int;
     *              $courseid = int;
     *
     **/
    public static function company_users_to_company_course_group($companyid, $courseid) {
        global $DB, $CFG;
        // Adds all the users to a company group within a shared course.

        require_once($CFG->dirroot.'/group/lib.php');

        // Get the group.
        if (!$groupid = self::get_company_group($companyid, $courseid)) {
            $groupid = self::create_company_course_group($companyid, $courseid);
        }
        // This is used for a course which is becoming shared.
        //  All current course enrolled users to this company group.
        if ($users = $DB->get_records_sql("SELECT userid FROM {user_enrolments}
                                           WHERE enrolid IN (
                                           SELECT id FROM {enrol} WHERE courseid = $courseid)")) {
            foreach ($users as $user) {
                if ($DB->get_record('user', array('id' => $user->userid))) {
                    groups_add_member($groupid, $user->userid);
                }
            }
        }
    }

    /**
     * Removes all company users and group from a course.
     *
     * Parameters -
     *              $companyid = int;
     *              $courseid = int;
     *
     **/
    public static function unenrol_company_from_course($companyid, $courseid) {
        global $DB;

        $timenow = time();
        // Get the company users.
        $companydepartment = self::get_company_parentnode($companyid);
        $companyusers = self::get_recursive_department_users($companydepartment->id);
        if ($group = self::get_company_group($companyid, $courseid)) {
            // End all enrolments now..
            if ($users = $DB->get_records_sql("SELECT * FROM {user_enrolments}
                                               WHERE enrolid IN (
                                                SELECT id FROM {enrol}
                                                WHERE courseid = $courseid)
                                               AND userid IN (".
                                                implode(',', array_keys($companyusers)).
                                               ")")) {
                foreach ($users as $user) {
                    $user->timeend = $timenow;
                    $DB->update_record('user_enrolments', $user);
                }
            }
            $DB->delete_records('company_course_groups', array('groupid', $group));
        }
        $DB->delete_records('company_shared_courses', array('courseid' => $courseid,
                                                            'companyid' => $companyid));
    }

    /**
     * Updates the theme reference for all the users in the company
     *
     * Parameters -
     *              $theme = string;
     *
     **/
    public function update_theme($theme) {
        global $DB;

        // Get the company users.
        $users = $this->get_all_user_ids();

        // Update their theme.
        foreach ($users as $userid) {
            if ($user = $DB->get_record('user', array('id' => $userid))) {
                $user->theme = $theme;
                $DB->update_record('user', $user);
            }
        }
    }

    /**
     * Suspends or Unsuspends a company and all of their users.
     *
     * Parameters -
     *              $theme = string;
     *
     **/
    public function suspend($suspend = true) {
        global $DB;

        // Get the company users.
        $users = $this->get_all_user_ids();

        // Update the users.
        foreach ($users as $userid) {
            if ($user = $DB->get_record('user', array('id' => $userid))) {
                // Does the user belong to another company?
                if ($DB->count_records('company_users', array('userid' => $userid)) > 1 ) {
                    // Belongs to more than one company.  Skip.
                    continue;
                }
                if (! $DB->get_record('company_users', array('userid' => $user->id, 'companyid' => $this->id, 'suspended' => 1))) {
                    $user->suspended  = $suspend;
                    $DB->update_record('user', $user);
                }
                if (!empty($suspend)) {
                    \core\session\manager::kill_user_sessions($user->id);
                }
            }
        }

        // Set the suspend field for the company.
        $DB->set_field('company', 'suspended', $suspend, array('id' => $this->id));

        // Deal with child companies.
        $childcompanies = $this->get_child_companies_recursive();
        if (!empty($childcompanies)) {
            foreach ($childcompanies as $childcomprec) {

                $childcompany = new company($childcomprec->id);
                $childcompany->suspend($suspend);
            }
        }
    }

    /**
     * Enables or disables ecommerce for a company.
     *
     * Parameters -
     *              $ecommerce = booloean;
     *
     **/
    public function ecommerce($ecommerce) {
        global $DB;

        // Set the ecommerce field for the company.
        $DB->set_field('company', 'ecommerce', $ecommerce, array('id' => $this->id));
    }

    /**
     * Checks that a passed department id is valid for the companyid.
     *
     * Parameters -
     *              $companyid = int;
     *              $departmentid = int;
     *
     * Returns boolean.
     *
     **/
    public static function check_valid_department($companyid, $departmentid) {
        global $DB;

        if ($DB->get_record('department', array('id' => $departmentid,
                                                'company' => $companyid))) {
            return true;
        } else {
            return false;
        }
        // Shouldn't get here.  Return a false in case.
        return false;
    }

    /**
     * Checks that a userid and department id is valid for the companyid.
     *
     * Parameters -
     *              $companyid = int;
     *              $departmentid = int;
     *              $userid = int;
     *
     * Returns boolean.
     *
     **/
    public static function check_valid_user($companyid, $userid, $deparmentid=0) {
        global $DB, $USER;

        $context = context_system::instance();
        // If current user is a site admin or they have appropriate capabilities then they can.
        if (is_siteadmin($USER->id) ||
            iomad::has_capability('block/iomad_company_admin:company_add', $context)) {
            return true;
        }

        if (!empty($departmentid) && $DB->get_record('company_users', array('departmentid' => $departmentid,
                                                                            'companyid' => $companyid,
                                                                            'userid' => $userid))) {
            return true;
        } else if ($DB->get_record('company_users', array('companyid' => $companyid,
                                                          'userid' => $userid))) {
            return true;
        } else {
            return false;
        }
        // Shouldn't get here.  Return a false in case.
        return false;
    }

    /**
     * Checks that the USER can edit a userid in a companyid.
     *
     * Parameters -
     *              $companyid = int;
     *              $userid = int;
     *
     * Returns boolean.
     *
     **/
    public static function check_canedit_user($companyid, $userid) {
        global $DB, $USER;

        // Can't edit an admin user here.
        if (is_siteadmin($userid)) {
            return false;
        }

        $context = context_system::instance();
        $context = context_system::instance();
        // If current user is a site admin or they have appropriate capabilities then they can.
        if (is_siteadmin($USER->id) ||
            iomad::has_capability('block/iomad_company_admin:company_add', $context)) {
            return true;
        }

        // Get my companyid.
        $mycompanyid = iomad::get_my_companyid($context);

        // If it doesn't match then return false.
        if ($mycompanyid != $companyid) {
            return false;
        }

        // Check if the user is in the company.
        if ($userrec = $DB->get_record('company_users', array('companyid' => $companyid,
                                                              'userid' => $userid))) {

            // Check the current user is a manager or not and what levels they can edit.
            if ($manrec = $DB->get_record('company_users', array('companyid' => $companyid,
                                                                 'userid' => $USER->id))) {
                if (empty($manrec->managertype)) {
                    return false;
                } else if ($manrec->managertype == 2 && $userrec->managertype == 1) {
                    return false;
                } else {
                    return true;
                }
            }
        }

        // Return a false by default.
        return false;
    }

    /**
     * Checks that a licenseid is valid for the companyid.
     *
     * Parameters -
     *              $companyid = int;
     *              $licenseid = int;
     *
     * Returns boolean.
     *
     **/
    public static function check_valid_company_license($companyid, $licenseid) {
        global $DB;

        if ($DB->get_record('companylicense', array('companyid' => $companyid,
                                                    'id' => $licenseid))) {
            return true;
        }

        // Is it a child license?
        $company = new company($companyid);

        // Return a false by default.
        return $company->is_child_license($licenseid);
    }

    /**
     * Checks that a two user id's are in the same company.
     *
     * Parameters -
     *              $userid = int;
     *
     * Returns boolean.
     *
     **/
    public static function check_can_manage($userid) {
        global $DB, $USER;

        $context = context_system::instance();
        // Set the companyid
        $companyid = iomad::get_my_companyid($context);

        // Get the list of users.
        $myusers = self::get_my_users($companyid);

        // If the user is in the list, return true.
        if (!empty($myusers[$userid])) {
            return true;
        }

        // Return a false by default.
        return false;
    }

    /**
     * Automatically enrols a users on un-licensed courses if its set in the config.
     *
     * Parameters -
     *              $user = stdclass();
     *
     **/
    public function autoenrol($user) {
        global $DB, $CFG, $SITE;

        // Get the courses which are assigned to the company which are not licensed.
        $courses = $DB->get_records_sql("SELECT courseid FROM {company_course} WHERE companyid = :companyid
                                         AND autoenrol = 1",
                                         array('companyid' => $this->id));

        // Are we also enrolling to unattached courses?
        if (!empty($CFG->local_iomad_signup_autoenrol_unassigned)) {
            $unassignedcourses = $DB->get_records_sql("SELECT id AS courseid FROM {course}
                                                       WHERE id NOT IN (
                                                        SELECT courseid FROM {company_course}
                                                       )
                                                       AND id != :siteid",
                                                       array('siteid' => $SITE->id));
            $courses = $courses + $unassignedcourses;
        }

        // Enrol the user onto them.
        foreach ($courses as $addcourse) {
            if ($course = $DB->get_record_sql("SELECT id FROM {course} WHERE id = :courseid AND visible = 1",
                                               array('courseid' => $addcourse->courseid))) {
                company_user::enrol($user, array($course->id));
            }
        }
    }

    // Competencies stuff.

    /**
     * Associates a ccompetency framework to a company
     *
     * Parameters -
     *              $framework = stdclass();
     *
     **/
    public static function add_competency_framework($companyid, $frameworkid) {
        global $DB;

        if (!$DB->record_exists('company_comp_frameworks', array('companyid' => $companyid,
                                                                 'frameworkid' => $frameworkid))) {
            $DB->insert_record('company_comp_frameworks', array('companyid' => $companyid,
                                                                'frameworkid' => $frameworkid));
        }
    }

    /**
     * Removes a course from a company
     *
     * Parameters -
     *              $course = stdclass();
     *              $companyid = int;
     *              $departmentid = int;
     *
     **/
    public static function remove_competency_framework($companyid, $frameworkid) {
        global $DB;

        $DB->delete_records('company_comp_frameworks', array('companyid' => $companyid,
                                                             'frameworkid' => $frameworkid));
    }

    /**
     * Associates a ccompetency framework to a company
     *
     * Parameters -
     *              $template = stdclass();
     *
     **/
    public static function add_competency_template($companyid, $templateid) {
        global $DB;

        if (!$DB->record_exists('company_comp_templates', array('companyid' => $companyid,
                                                                'templateid' => $templateid))) {
            $DB->insert_record('company_comp_templates', array('companyid' => $companyid,
                                                               'templateid' => $templateid));
        }
    }

    /**
     * Removes a course from a company
     *
     * Parameters -
     *              $template = stdclass();
     *
     **/
    public static function remove_competency_template($companyid, $templateid) {
        global $DB;

        $DB->delete_records('company_comp_templates', array('companyid' => $companyid,
                                                            'templateid' => $templateid));
    }

    /***  Event Handlers  ***/

    /**
     * Triggered via competency_framework_created event.
     *
     * @param \core\event\competency_framework_created $event
     * @return bool true on success.
     */
    public static function competency_framework_created(\core\event\competency_framework_created $event) {
        $data = $event->get_data();
        if (!empty($data['companyid'])) {
            self::add_competency_framework($data['companyid'], $event->objectid);
        }
        return true;
    }

    /**
     * Triggered via competency_framework_deleted event.
     *
     * @param \core\event\competency_framework_deleted $event
     * @return bool true on success.
     */
    public static function competency_framework_deleted(\core\event\competency_framework_deleted $event) {
        global $DB;
        $DB->delete_records('company_comp_frameworks', array('frameworkid' => $event->objectid));
        return true;
    }

    /**
     * Triggered via competency_template_created event.
     *
     * @param \core\event\competency_template_created $event
     * @return bool true on success.
     */
    public static function competency_template_created(\core\event\competency_template_created $event) {

        $data = $event->get_data();
        if (!empty($data['companyid'])) {
            self::add_competency_template($data['companyid'], $event->objectid);
        }
        return true;
    }

    /**
     * Triggered via competency_template_deleted event.
     *
     * @param \core\event\competency_template_deleted $event
     * @return bool true on success.
     */
    public static function competency_template_deleted(\core\event\competency_template_deleted $event) {
        global $DB;
        $DB->delete_records('company_comp_templates', array('templateid' => $event->objectid));
        return true;
    }

    /**
     * Triggered via course_deleted event.
     *
     * @param \core\event\course_deleted $event
     * @return bool true on success.
     */
    public static function course_deleted(\core\event\course_deleted $event) {
        global $DB;

        // Clear everything from the iomad_courses table.
        $DB->delete_records('iomad_courses', array('courseid' => $event->courseid));

        // Remove the course from company allocation tables.
        $DB->delete_records('company_course', array('courseid' => $event->courseid));

        // Remove the course from company created course tables.
        $DB->delete_records('company_created_courses', array('courseid' => $event->courseid));

        // Remove the course from company shared courses tables.
        $DB->delete_records('company_shared_courses', array('courseid' => $event->courseid));

        // Deal with licenses allocations.
        $DB->delete_records('companylicense_users', array('licensecourseid' => $event->courseid));
        $courselicenses = $DB->get_records('//companylicense_courses', array('courseid' => $event->courseid));
        foreach ($courselicenses as $courselicense) {
            // Delete the course from the license.
            $DB->delete_record('companylicense_courses', array('id' => $courselicense->id));
            // Does the license have any courses left?
            if ($DB->get_records('companylicense_courses', array('licensid' => $courselicense->licenseid))) {
                self::update_license_usage($courselicense->licenseid);
            } else {
                // Delete the license.  It no longer is valid.
                $DB->delete_records('companylicense', array('id' => $courselicense->licenseid));
            }
        }

        return true;
    }

    /**
     * Triggered via course_completed event.
     *
     * @param \core\event\course_completed $event
     * @return bool true on success.
     */
    public static function course_completed_supervisor(\core\event\course_completed $event) {
        global $DB, $CFG;

        $data = $event->get_data();
        $userid = $data['relateduserid'];
        $courseid = $data['courseid'];
        $timecompleted = $data['timecreated'];
        // Need to make sure any certificate is created.
        sleep(9);

        $course = $DB->get_record('course', array('id' => $courseid));
        $user = $DB->get_record('user', array('id' => $userid));
        $company = self::get_company_byuserid($userid);

        $supervisortemplate = new EmailTemplate('completion_course_supervisor', array('course' => $course, 'user' => $user, 'company' => $company));

        // Do we have a supervisor?
        if ($supervisoremails = self::get_usersupervisor($userid)) {
            foreach ($supervisoremails as $supervisoremail) {
                // Get the user info.
                if ($userinfo = $DB->get_record('user', array('id' => $userid, 'deleted' => 0, 'suspended' => 0))) {
                    if ($courseinfo = $DB->get_record('course', array('id' => $courseid))) {
                        // We have to do this manually as the normal Moodle functions require a proper registered user.
                        $params = new stdclass();
                        $params->fullname = $courseinfo->fullname;
                        $params->firstname = $userinfo->firstname;
                        $params->lastname = $userinfo->lastname;
                        $params->date = date('d-m-Y', time());
                        $mail = get_mailer();

                        $supportuser = core_user::get_support_user();
                        if (!empty($CFG->supportemail)) {
                            $supportuser->email = $CFG->supportemail;
                        }
                        if ($CFG->supportname) {
                            $supportuser->firstname = $CFG->supportname;
                        }

                        $subject = get_string('completion_course_supervisor_subject', 'block_iomad_company_admin', $params);
                        $messagetext = get_string('completion_course_supervisor_body', 'block_iomad_company_admin', $params);

                        $mail->Sender = $supportuser->firstname;
                        $mail->From     = $CFG->noreplyaddress;
                        $mail->FromName = $supportuser->firstname;
                        if (empty($CFG->divertallemailsto)) {
                            $mail->Subject = substr($subject, 0, 900);
                        } else {
                            $mail->Subject = substr('[DIVERTED ' . $supervisoremail . '] ' . $subject, 0, 900);
                        }

                        $mail->addAddress($supervisoremail, '');

                        // Add the certificate attachment.
                        // need to pause to make sure the other events create it.
                        $trackinfos = $DB->get_records_sql('SELECT * FROM {local_iomad_track}
                                                          WHERE userid = :userid
                                                          AND courseid = :courseid
                                                          ORDER BY id DESC',
                                                          array('userid' => $userid, 'courseid' => $courseid), 0, 1);
                        $trackinfo = array_pop($trackinfos);
                        if ($trackfileinfo = $DB->get_record('local_iomad_track_certs', array('trackid' => $trackinfo->id))) {
                            $fileinfo = $DB->get_record('files', array('itemid' => $trackinfo->id, 'component' => 'local_iomad_track', 'filename' => $trackfileinfo->filename));
                            $filedir1 = substr($fileinfo->contenthash,0,2);
                            $filedir2 = substr($fileinfo->contenthash,2,2);
                            $filepath = $CFG->dataroot . '/filedir/' . $filedir1 . '/' . $filedir2 . '/' . $fileinfo->contenthash;
                            $mimetype = mimeinfo('type', $trackfileinfo->filename);
                            $mail->addAttachment($filepath, $trackfileinfo->filename, 'base64', $mimetype);
                        }
                        // Set word wrap.
                        $mail->WordWrap = 79;

                        $mail->Body =  "\n$messagetext\n";
                        if (empty($CFG->noemailever)) {
                            $mail->send();
                        }

                    }
                }
            }
        }

        // Email the company managers.
        if ($mymanagers = self::get_my_managers($userid, 1)) {
            foreach ($mymanagers as $managerid) {
                // Get the user info.
                if ($managerinfo = $DB->get_record('user', array('id' => $managerid->userid, 'deleted' => 0, 'suspended' => 0))) {
                    if ($userinfo = $DB->get_record('user', array('id' => $userid, 'deleted' => 0, 'suspended' => 0))) {
                        if ($courseinfo = $DB->get_record('course', array('id' => $courseid))) {
                            $courseinfo->reporttext = fullname($userinfo) . ' has completed ' . $courseinfo->fullname . ' in ' . $company->name .
                                                       ' on ' . date($CFG->iomad_date_format, $timecompleted) . "\n";
                            EmailTemplate::send('course_completed_manager', array('course' => $courseinfo, 'user' => $managerinfo, 'company' => $company));
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * Triggered via user_deleted event.
     *
     * @param \core\event\user_deleted $event
     * @return bool true on success.
     */
    public static function user_deleted(\core\event\user_deleted $event) {
        global $DB;

        $userid = $event->objectid;
        $timestamp = time();

        // Get licenses which are reusable and can be removed.
        if ($reusablelicenses = $DB->get_records_sql("SELECT clu.*
                                                      FROM {companylicense_users} clu
                                                      JOIN {companylicense} cl ON (clu.licenseid = cl.id)
                                                      WHERE cl.type = 1
                                                      AND cl.expirydate > :timestamp
                                                      AND clu.userid = :userid",
                                                      array('timestamp' => $timestamp,
                                                            'userid' => $userid))) {
            foreach ($reusablelicenses as $reusablelicense) {
                $DB->delete_records('companylicense_users', array('id' => $reusablelicense->id));

                // Fire the license deleted event.
                $eventother = array('licenseid' => $reusablelicense->licenseid,
                                    'duedate' => 0);
                $event = \block_iomad_company_admin\event\user_license_unassigned::create(array('context' => context_course::instance($reusablelicense->licensecourseid),
                                                                                                'objectid' => $reusablelicense->licenseid,
                                                                                                'courseid' => $reusablelicense->licensecourseid,
                                                                                                'userid' => $reusablelicense->userid,
                                                                                                'other' => $eventother));
                $event->trigger();

                // Update the license usage.
                self::update_license_usage($reusablelicense->licenseid);
            }
        }

        // Get licenses which are unused, non-program and can be removed.
        if ($nonprogramlicenses = $DB->get_records_sql("SELECT clu.*
                                                        FROM {companylicense_users} clu
                                                        JOIN {companylicense} cl ON (clu.licenseid = cl.id)
                                                        WHERE cl.type = 0
                                                        AND cl.program = 0
                                                        AND clu.isusing = 0
                                                        AND cl.expirydate > :timestamp
                                                        AND clu.userid = :userid",
                                                        array('timestamp' => $timestamp,
                                                              'userid' => $userid))) {
            foreach ($nonprogramlicenses as $nonprogramlicense) {
                $DB->delete_records('companylicense_users', array('id' => $nonprogramlicense->id));

                // Fire the license deleted event.
                $eventother = array('licenseid' => $nonprogramlicense->licenseid,
                                    'duedate' => 0);
                $event = \block_iomad_company_admin\event\user_license_unassigned::create(array('context' => context_course::instance($nonprogramlicense->licensecourseid),
                                                                                                'objectid' => $nonprogramlicense->licenseid,
                                                                                                'courseid' => $nonprogramlicense->licensecourseid,
                                                                                                'userid' => $nonprogramlicense->userid,
                                                                                                'other' => $eventother));
                $event->trigger();

                // Update the license usage.
                self::update_license_usage($nonprogramlicense->licenseid);
            }
        }

        // Deal with program licenses.
        if ($programlicenses = $DB->get_records_sql("SELECT cl.*
                                                     FROM {companylicense} cl
                                                     JOIN {companylicense_users} clu ON (cl.id = clu.licenseid)
                                                     WHERE clu.userid = :userid
                                                     AND cl.expirydate > :timestamp
                                                     GROUP BY cl.id",
                                                     array('timestamp' => $timestamp,
                                                           'userid' => $userid))) {

            foreach ($programlicenses as $programlicense) {
                // Check if there is a used course here
                if ($DB->get_records('companylicense_users', array('userid' => $userid, 'licenseid' => $programlicense->id, 'isusing' => 1))) {
                    continue;
                } else {
                    $licencerecords = $DB->get_records('companylicense_users', array('userid' => $userid, 'licenseid' => $programlicense->id));

                    foreach ($licenserecords as $licenserecord) {
                        // Fire the license deleted event.
                        $eventother = array('licenseid' => $licenserecord->licenseid,
                                            'duedate' => 0);
                        $event = \block_iomad_company_admin\event\user_license_unassigned::create(array('context' => context_course::instance($licenserecord->licensecourseid),
                                                                                                        'objectid' => $licenserecord->licenseid,
                                                                                                        'courseid' => $licenserecord->licensecourseid,
                                                                                                        'userid' => $licenserecord->userid,
                                                                                                        'other' => $eventother));
                        $event->trigger();
                    }

                    // Update the license usage.
                    self::update_license_usage($programlicense->id);
                }
            }
        }

        // Remove the user from any company.
        $DB->delete_records('company_users', array('userid' => $userid));

        return true;
    }

    /**
     * Triggered via company_user_assigned event.
     *
     * @param \block_iomad_company_user\event\company_user_assigned $event
     * @return bool true on success.
     */
    public static function company_user_assigned(\block_iomad_company_admin\event\company_user_assigned $event) {
        global $DB;

        // We only care if its a company manager.
        if ($event->other['usertype'] != 1) {
            return true;
        }
        $companyid = $event->objectid;
        $userid = $event->userid;

        $company = new company($companyid);
        $childcompanies = $company->get_child_companies_recursive();

        foreach ($childcompanies as $child) {
            $childcompany = new company($child->id);
            $childcompany->assign_user_to_company($userid, 0, $event->other['usertype'], true);
        }

        return true;
    }

    /**
     * Triggered via company_user_unassigned event.
     *
     * @param \block_iomad_company_user\event\company_user_unassigned $event
     * @return bool true on success.
     */
    public static function company_user_unassigned(\block_iomad_company_admin\event\company_user_unassigned $event) {
        global $DB;

        // We only care if its a company manager.
        if ($event->other['usertype'] != 1) {
            return true;
        }
        $companyid = $event->objectid;
        $userid = $event->userid;

        $company = new company($companyid);
        $childcompanies = $company->get_child_companies_recursive();

        foreach ($childcompanies as $child) {
            $childcompany = new company($child->id);
            $childcompany->unassign_user_from_company($userid, true);
        }

        return true;
    }

    /**
     * Triggered via user_license_assigned event.
     *
     * @param \block_iomad_company_user\event\user_license_assigned $event
     * @return bool true on success.
     */
    public static function user_license_assigned(\block_iomad_company_admin\event\user_license_assigned $event) {
        global $DB, $CFG;

        $userid = $event->userid;
        $userlicid = $event->objectid;
        $licenseid = $event->other['licenseid'];
        $courseid = $event->courseid;
        $duedate = $event->other['duedate'];
        if (!empty($event->other['noemail'])) {
            $noemail = true;
        } else {
            $noemail = false;
        }

        if (!$licenserecord = $DB->get_record('companylicense', array('id'=>$licenseid))) {
            return;
        }

        if (!$course = $DB->get_record('course', array('id' => $courseid))) {
            return;
        }

        if (!$user = $DB->get_record('user', array('id' => $userid))) {
            return;
        }

        $license = new stdclass();
        $license->length = $licenserecord->validlength;
        $license->valid = date($CFG->iomad_date_format, $licenserecord->expirydate);

        if (!$noemail) {
        // Send out the email.
            EmailTemplate::send('license_allocated', array('course' => $course,
                                                           'user' => $user,
                                                           'due' => $duedate,
                                                           'license' => $license));
        }

        // Update the license usage.
        self::update_license_usage($licenseid);

        // Is this an immediate license?
        if (!empty($licenserecord->instant)) {
            if ($instance = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'license'))) {
                // Enrol the user on the course.
                $enrol = enrol_get_plugin('license');

                // Enrol the user in the course.
                // Is the license available yet?
                if (!empty($licenserecord->startdate) && $licenserecord->startdate > time()) {
                    // If not set up the enrolment from when it is.
                    $timestart = $licenserecord->startdate;
                } else {
                    // Otherwise start it now.
                    $timestart = time();
                }

                if ($licenserecord->type == 0 || $licenserecord->type == 2) {
                    // Set the timeend to be time start + the valid length for the license in days.
                    $timeend = $timestart + ($licenserecord->validlength * 24 * 60 * 60 );
                } else {
                    // Set the timeend to be when the license runs out.
                    $timeend = $licenserecord->expirydate;
                }

                if ($licenserecord->type < 2) {
                    $enrol->enrol_user($instance, $user->id, $instance->roleid, $timestart, $timeend);
                } else {
                    // Educator role.
                    if ($DB->get_record('iomad_courses', array('courseid' => $course->id, 'shared' => 0))) {
                        // Not shared.
                        $role = $DB->get_record('role', array('shortname' => 'companycourseeditor'));
                    } else {
                        // Shared.
                        $role = $DB->get_record('role', array('shortname' => 'companycoursenoneditor'));
                    }
                    $enrol->enrol_user($instance, $user->id, $role->id, $timestart, $timeend);
                }

                // Get the userlicense record.
                $userlicense = $DB->get_record('companylicense_users', array('id' => $userlicid));

                // Add the user to the appropriate course group.
                if (!empty($course->groupmode)) {
                    $userlicense = $DB->get_record('companylicense_users', array('id' => $userlicid));
                    self::add_user_to_shared_course($instance->courseid, $user->id, $license->companyid, $userlicense->groupid);
                }

                // Update the userlicense record to mark it as in use.
                $DB->set_field('companylicense_users', 'isusing', 1, array('id' => $userlicense->id));

                // Send welcome.
                if ($instance->customint4) {
                    $enrol->email_welcome_message($instance, $user);
                }
            }
        }

        return true;
    }

    /**
     * Triggered via user_license_unassigned event.
     *
     * @param \block_iomad_company_user\event\user_license_unassigned $event
     * @return bool true on success.
     */
    public static function user_license_unassigned(\block_iomad_company_admin\event\user_license_unassigned $event) {
        global $DB, $CFG, $PAGE;

        $userid = $event->userid;
        $licenseid = $event->other['licenseid'];
        $courseid = $event->courseid;

        if (!$licenserecord = $DB->get_record('companylicense', array('id' => $licenseid))) {
            return;
        }

        if (!$course = $DB->get_record('course', array('id' => $courseid))) {
            return;
        }

        if (!$user = $DB->get_record('user', array('id' => $userid, 'deleted' => 0, 'suspended' => 0))) {
            self::update_license_usage($licenseid);
            return;
        }

        // Check if there is an enrolment in the course for this user/license.
        $manager = new course_enrolment_manager($PAGE, $course);
        if ($enrolments = $manager->get_user_enrolments($userid)) {
            foreach ($enrolments as $ue) {
                $manager->unenrol_user($ue);
            }
        }

        $license = new stdclass();
        $license->length = $licenserecord->validlength;
        $license->valid = date($CFG->iomad_date_format, $licenserecord->expirydate);

        if ($emailrecs = $DB->get_records('email', array('userid' => $user->id,
                                                         'courseid' => $course->id,
                                                         'templatename' => 'license_allocated',
                                                         'sent' => null))) {
            // Delete the email as it hasn't been sent.
            foreach ($emailrecs as $emailrec) {
                $DB->delete_records('email', array('id' => $emailrec->id));
            }
        } else {
            // Send out the email.
            EmailTemplate::send('license_removed', array('course' => $course,
                                                         'user' => $user,
                                                         'license' => $license));

        }
        // Update the license usage.
        self::update_license_usage($licenseid);

        return true;
    }

    /**
     * Triggered via company_license_created event.
     *
     * @param \block_iomad_company_user\event\company_license_created $event
     * @return bool true on success.
     */
    public static function company_license_created(\block_iomad_company_admin\event\company_license_created $event) {
        global $DB, $CFG;

        $licenseid = $event->other['licenseid'];
        $parentid = $event->other['parentid'];

        if (!$licenserecord = $DB->get_record('companylicense', array('id' => $licenseid))) {
            return;
        }

        // Update the license usage.
        if (!empty($parentid)) {
            self::update_license_usage($parentid);
        }

        return true;
    }

    /**
     * Triggered via company_license_updated event.
     *
     * @param \block_iomad_company_user\event\company_license_updated $event
     * @return bool true on success.
     */
    public static function company_license_updated(\block_iomad_company_admin\event\company_license_updated $event) {
        global $DB, $CFG;

        $licenseid = $event->other['licenseid'];
        $parentid = $event->other['parentid'];

        if (!$licenserecord = $DB->get_record('companylicense', array('id' => $licenseid))) {
            return;
        }

        if (!empty($licenserecord->program)) {
            // This is a program of courses.
            // If it's been updated we need to deal with any course changes.
            $currentcourses = $DB->get_records('companylicense_courses', array('licenseid' => $licenseid), null, 'courseid');
            $oldcourses = (array) json_decode($event->other['oldcourses'], true);

            // check for courses being removed.
            foreach ($oldcourses as $oldcourse) {
                $oldcourseid = $oldcourse['courseid'];

                if (empty($currentcourses[$oldcourseid])) {
                    // Deal with enrolments.
                    if ($enrolments = $DB->get_records_sql("SELECT e.id FROM {enrol} e
                                                            JOIN {companylicense_courses} clc ON (e.courseid = clc.courseid AND e.status = 0)
                                                            WHERE clc.licenseid = :licenseid
                                                            AND e.courseid = :courseid",
                                                            array('licenseid' => $licenseid, 'courseid' => $oldcourseid))) {
                        foreach ($enrolments as $enrolid) {
                            $DB->delete_records('user_enrolments', array('enrolid' => $enrolid->id));
                        }
                    }
                    $DB->delete_records('companylicense_users', array('licensecourseid' => $oldcourseid, 'licenseid' => $licenseid));
                }
            }

            foreach ($currentcourses as $currentcourse) {
                $currcourseid = $currentcourse->courseid;
                if (empty($oldcourses[$currcourseid])) {
                    // We have a new course.  Add everyone.
                    if ($licusers = $DB->get_records_sql("SELECT DISTINCT userid
                                                      FROM {companylicense_users}
                                                      WHERE licenseid = :licenseid",
                                                      array('licenseid' => $licenseid))) {

                        foreach ($licusers as $licuser) {
                            $userlic = array('licenseid' => $licenseid,
                                             'userid' => $licuser->userid,
                                             'isusing' => 0,
                                             'licensecourseid' => $currentcourse->courseid,
                                             'issuedate' => time());
                            $DB->insert_record('companylicense_users', $userlic);
                        }
                    }
                }
            }

            if (isset($event->other['programchange']) && $licenserecord->program == 1) {
                // We have switched from an ordinary license to a program license.
                // Get the users who have courses in this licenses.
                if ($licusers = $DB->get_records_sql("SELECT DISTINCT userid
                                                      FROM {companylicense_users}
                                                      WHERE licenseid = :licenseid",
                                                      array('licenseid' => $licenseid))) {

                        foreach ($licusers as $licuser) {
                            foreach ($currentcourses as $currentcourse) {
                            // Check if they have a license allocated.
                            if (!$DB->get_record('companylicense_users', array('userid' => $licuser->userid,
                                                                               'licensecourseid' => $currentcourse->courseid,
                                                                               'licenseid' => $licenseid))) {
                                // If not, allocate it to them.
                                $userlic = array('licenseid' => $licenseid,
                                                 'userid' => $licuser->userid,
                                                 'isusing' => 0,
                                                 'licensecourseid' => $currentcourse->courseid,
                                                 'issuedate' => time());
                                $DB->insert_record('companylicense_users', $userlic);
                            }
                        }
                    }
                }
            }
        }

        // Update the license usage.
        self::update_license_usage($licenseid);

        // Deal with the parent.
        if (!empty($parentid)) {
            self::update_license_usage($parentid);
        }

        // Update the timeend for any users using this license.
        if (!empty($licenserecord->type)) {
            // This is a subscription license.
            // Update the enrolment end
            if ($enrolments = $DB->get_records_sql("SELECT e.id FROM {enrol} e
                                                    JOIN {companylicense_courses} clc ON (e.courseid = clc.courseid AND e.status = 0)
                                                    WHERE clc.licenseid = :licenseid",
                                                    array('licenseid' => $licenseid))) {
                foreach ($enrolments as $enrolid) {
                    $DB->execute("UPDATE {user_enrolments} SET timeend = :timeend
                                  WHERE enrolid = :enrolid",
                                  array('timeend' => $licenserecord->expirydate, 'enrolid' => $enrolid->id));
                }
            }
        }

        // Deal with any children.
        if ($children = $DB->get_records('companylicense', array('parentid' => $licenseid))) {
            foreach ($children as $child) {
                // Get the courses.
                 $oldcourses = $DB->get_records('companylicense_courses', array('licenseid' => $child->id), null, 'courseid');

                // Clear down all of them initially.
                $DB->delete_records('companylicense_courses', array('licenseid' => $child->id));
                if (!empty($currentcourses)) {
                    // Add the course license allocations.
                    foreach ($currentcourses as $selectedcourse) {
                        $DB->insert_record('companylicense_courses', array('licenseid' => $child->id, 'courseid' => $selectedcourse->courseid));
                    }
                }

                // Deal with the allocation amount if courses changed.
                if (!empty($child->program)) {
                    $old = count($oldcourses);
                    $new = count($currentcourses);
                    if ($old != $new) {
                        $allocation = $child->allocation / $old * $new;
                        $DB->set_field('companylicense', 'allocation', $allocation, array('id' => $child->id));
                    }
                }

                // Did we change anything else about the license?
                $child->validlength = $licenserecord->validlength;
                $child->expirydate = $licenserecord->expirydate;
                $child->type = $licenserecord->type;
                $child->startdate = $licenserecord->startdate;
                $child->instant = $licenserecord->instant;
                $DB->update_record('companylicense', $child);

                // Create an event to deal with any child license allocations.
                $eventother = $event->other;
                $eventother['licenseid'] = $child->id;
                $eventother['parentid'] = $licenseid;
                $eventother['oldcourses'] = json_encode($oldcourses);

                $event = \block_iomad_company_admin\event\company_license_updated::create(array('context' => context_system::instance(),
                                                                                                'userid' => $event->userid,
                                                                                                'objectid' => $child->id,
                                                                                                'other' => $eventother));
                $event->trigger();
            }
        }

        return true;
    }

    /**
     * Triggered via company_license_deleted event.
     *
     * @param \block_iomad_company_user\event\company_license_deleted $event
     * @return bool true on success.
     */
    public static function company_license_deleted(\block_iomad_company_admin\event\company_license_deleted $event) {
        global $DB, $CFG;

        $parentid = $event->other['parentid'];

        if (empty($parentid) || !$licenserecord = $DB->get_record('companylicense', array('id' => $parentid))) {
            return;
        }
        $DB->delete_records('companylicense_courses', array('licenseid' => $event->other['licenseid']));

        // Update the license usage.
        self::update_license_usage($parentid);

        return true;
    }

    /**
     * Triggered via company_suspended event.
     *
     * @param \block_iomad_company_user\event\company_suspended $event
     * @return bool true on success.
     */
    public static function company_suspended(\block_iomad_company_admin\event\company_suspended $event) {
        global $DB, $CFG;

        $companyid = $event->other['companyid'];

        if (empty($companyid) || !$companyrecord = $DB->get_record('company', array('id' => $companyid))) {
            return;
        }

        $suspendcompany = new company($companyid);
        $suspendcompany->suspend(true);

        return true;
    }

    /**
     * Triggered via company_unsuspended event.
     *
     * @param \block_iomad_company_user\event\company_unsuspended $event
     * @return bool true on success.
     */
    public static function company_unsuspended(\block_iomad_company_admin\event\company_unsuspended $event) {
        global $DB, $CFG;

        $companyid = $event->other['companyid'];

        if (empty($companyid) || !$companyrecord = $DB->get_record('company', array('id' => $companyid))) {
            return;
        }

        $suspendcompany = new company($companyid);
        $suspendcompany->suspend(false);

        return true;
    }

    /**
     * Triggered via user_course_expired event.
     *
     * @param \block_iomad_company_user\event\company_license_created $event
     * @return bool true on success.
     */
    public static function user_course_expired(\block_iomad_company_admin\event\user_course_expired $event) {
        global $DB, $CFG;

        $userid = $event->userid;
        $courseid = $event->courseid;

        // Remove enrolments
        $plugins = enrol_get_plugins(true);
        $instances = enrol_get_instances($courseid, true);
        foreach ($instances as $instance) {
            $plugin = $plugins[$instance->enrol];
            $plugin->unenrol_user($instance, $userid);
        }

        // Remove completions
        $DB->delete_records('course_completions', array('userid' => $userid, 'course' => $courseid));
        if ($compitems = $DB->get_records('course_completion_criteria', array('course' => $courseid))) {
            foreach ($compitems as $compitem) {
                $DB->delete_records('course_completion_crit_compl', array('userid' => $userid,
                                                                          'criteriaid' => $compitem->id));
            }
        }
        if ($modules = $DB->get_records_sql("SELECT id FROM {course_modules} WHERE course = :course AND completion != 0", array('course' => $courseid))) {
            foreach ($modules as $module) {
                $DB->delete_records('course_modules_completion', array('userid' => $userid, 'coursemoduleid' => $module->id));
            }
        }

        // Deal with SCORM.
        if ($scorms = $DB->get_records('scorm', array('course' => $courseid))) {
            foreach ($scorms as $scorm) {
                $DB->delete_records('scorm_scoes_track', array('userid' => $userid, 'scormid' => $scorm->id));
            }
        }

        // Remove grades
        if ($items = $DB->get_records('grade_items', array('courseid' => $courseid))) {
            foreach ($items as $item) {
                $DB->delete_records('grade_grades', array('userid' => $userid, 'itemid' => $item->id));
            }
        }

        // Remove quiz entries.
        if ($quizzes = $DB->get_records('quiz', array('course' => $courseid))) {
            // We have quiz(zes) so clear them down.
            foreach ($quizzes as $quiz) {
                $DB->delete_records('quiz_attempts', array('quiz' => $quiz->id, 'userid' => $userid));
                $DB->delete_records('quiz_grades', array('quiz' => $quiz->id, 'userid' => $userid));
                $DB->delete_records('quiz_overrides', array('quiz' => $quiz->id, 'userid' => $userid));
            }
        }

        // Remove certificate info.
        if ($certificates = $DB->get_records('iomadcertificate', array('course' => $courseid))) {
            foreach ($certificates as $certificate) {
                $DB->delete_records('iomadcertificate_issues', array('iomadcertificateid' => $certificate->id, 'userid' => $userid));
            }
        }

        // Remove feedback info.
        if ($feedbacks = $DB->get_records('feedback', array('course' => $courseid))) {
            foreach ($feedbacks as $feedback) {
                $DB->delete_records('feedback_completed', array('feedback' => $feedback->id, 'userid' => $userid));
                $DB->delete_records('feedback_completedtmp', array('feedback' => $feedback->id, 'userid' => $userid));
                $DB->delete_records('feedback_tracking', array('feedback' => $feedback->id, 'userid' => $userid));
            }
        }

        // Remove lesson info.
        if ($lessons = $DB->get_records('lesson', array('course' => $courseid))) {
            foreach ($lessons as $lesson) {
                $DB->delete_records('lesson_attempts', array('lessonid' => $lesson->id, 'userid' => $userid));
                $DB->delete_records('lesson_grades', array('lessonid' => $lesson->id, 'userid' => $userid));
                $DB->delete_records('lesson_branch', array('lessonid' => $lesson->id, 'userid' => $userid));
                $DB->delete_records('lesson_timer', array('lessonid' => $lesson->id, 'userid' => $userid));
            }
        }

        // Fix company licenses
        if ($licenses = $DB->get_records('companylicense_users', array('licensecourseid' => $courseid, 'userid' =>$userid, 'isusing' => 1, 'timecompleted' => null))) {
            $license = array_pop($licenses);
            $license->timecompleted = time();
            $DB->update_record('companylicense_users', $license);
        }

        return true;
    }

    /**
     * Send a user's supervisor a warning email that a user hasn't completed a course.
     *
     * @param user object
     * @param course object
     * @return bool true on success.
     */
    public static function send_supervisor_warning_email($user, $course) {
        global $DB, $CFG;

        $company = self::get_company_byuserid($user->id);
        $supervisortemplate = new EmailTemplate('completion_warn_supervisor', array('course' => $course, 'user' => $user, 'company' => $company));

        // Do we have a supervisor?
        if ($supervisoremails = self::get_usersupervisor($user->id)) {
            foreach ($supervisoremails as $supervisoremail) {
                $params = new stdclass();
                $params->fullname = $course->fullname;
                $params->firstname = $user->firstname;
                $params->lastname = $user->lastname;
                $mail = get_mailer();

                $supportuser = core_user::get_support_user();
                if (!empty($CFG->supportemail)) {
                    $supportuser->email = $CFG->supportemail;
                }
                if ($CFG->supportname) {
                    $supportuser->firstname = $CFG->supportname;
                }

                $subject = get_string('completion_warn_supervisor_subject', 'block_iomad_company_admin', $params);
                $messagetext = get_string('completion_warn_supervisor_body', 'block_iomad_company_admin', $params);

                $mail->Sender = $supportuser->firstname;
                $mail->FromName = $supportuser->firstname;
                $mail->From     = $CFG->noreplyaddress;
                if (empty($CFG->divertallemailsto)) {
                    $mail->Subject = substr($subject, 0, 900);
                } else {
                    $mail->Subject = substr('[DIVERTED ' . $user->email . '] ' . $subject, 0, 900);
                }

                $mail->addAddress($supervisoremail, '');

                // Set word wrap.
                $mail->WordWrap = 79;

                $mail->Body =  "\n$messagetext\n";
                if (empty($CFG->noemailever)) {
                    $mail->send();
                }
            }
        }
        return true;
    }

    /**
     * Send a user's supervisor a warning email that a user hasn't completed a course.
     *
     * @param user object
     * @param course object
     * @return bool true on success.
     */
    public static function send_supervisor_expiry_warning_email($user, $course) {
        global $DB, $CFG;

        $company = self::get_company_byuserid($user->id);
        $supervisortemplate = new EmailTemplate('completion_expiry_warn_supervisor', array('course' => $course, 'user' => $user, 'company' => $company));

        // Do we have a supervisor?
        if ($supervisoremails = self::get_usersupervisor($user->id)) {
            foreach ($supervisoremails as $supervisoremail) {
                $params = new stdclass();
                $params->fullname = $course->fullname;
                $params->firstname = $user->firstname;
                $params->lastname = $user->lastname;
                $mail = get_mailer();

                $supportuser = core_user::get_support_user();
                if (!empty($CFG->supportemail)) {
                    $supportuser->email = $CFG->supportemail;
                }
                if ($CFG->supportname) {
                    $supportuser->firstname = $CFG->supportname;
                }

                $subject = get_string('completion_expiry_warn_supervisor_subject', 'block_iomad_company_admin', $params);
                $messagetext = get_string('completion_expiry_warn_supervisor_body', 'block_iomad_company_admin', $params);

                $mail->Sender = $supportuser->firstname;
                $mail->From     = $CFG->noreplyaddress;
                $mail->FromName = $supportuser->firstname;
                if (empty($CFG->divertallemailsto)) {
                    $mail->Subject = substr($subject, 0, 900);
                } else {
                    $mail->Subject = substr('[DIVERTED ' . $user->email . '] ' . $subject, 0, 900);
                }

                $mail->addAddress($supervisoremail, '');

                // Set word wrap.
                $mail->WordWrap = 79;

                $mail->Body =  "\n$messagetext\n";
                if (empty($CFG->noemailever)) {
                    $mail->send();
                }

            }
        }
        return true;
    }
}

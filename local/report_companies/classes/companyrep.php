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
 * @package   local_report_companies
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_report_companies;

class companyrep{

    // Get the jsmodule setup thingy.
    public function getjsmodule() {
        $jsmodule = array(
            'name'     => 'local_report_completion',
            'fullpath' => '/local/report_completion/module.js',
            'requires' => array('base', 'node', 'charts', 'json'),
            'strings' => array(
                )
        );
        return $jsmodule;
    }

    /**
     * Create the select list of companies.
     * If the user is in the company managers table then the list is restricted.
     * @param object $user
     * @param int $companyid
     * @return array
     */
    public static function companylist($user, $companyid = null) {
        global $DB;

        // Create "empty" array.
        $companylist = array();

        // Get the companies they manage.
        $managedcompanies = array();
        if ($managers = $DB->get_records_sql("SELECT * from {company_users} WHERE
                                              userid = :userid
                                              AND managertype != 0", array('userid' => $user->id))) {
            foreach ($managers as $manager) {
                $managedcompanies[] = $manager->companyid;
            }
        }

        // Get companies information.
        if ($companyid) {
            $params = ['id' => $companyid];
        } else {
            $params = [];
        }
        if (!$companies = $DB->get_records('company', $params)) {
            return [];
        }

        // And finally build the list.
        foreach ($companies as $company) {

            // Is this a child company?
            if ($company->parentid) {
                $parent = $DB->get_record('company', ['id' => $company->parentid], '*', MUST_EXIST);
                $company->parentlink = new \moodle_url('/local/report_companies', ['companyid' => $company->parentid]);
                $company->parent = '<a href="' . $company->parentlink . '">' . $parent->name . '</a>';
            } else {
                $company->parent = '';
            }

            // If managers found then only allow selected companies.
            if (!empty($managedcompanies)) {
                if (!in_array($company->id, $managedcompanies)) {
                    continue;
                }
            }
            $companylist[$company->id] = $company;
        }

        $companylist = \block_iomad_company_admin\iomad_company_admin::order_companies_by_parent($companylist);

        return $companylist;
    }

    /**
     * Append the company managers to companies.
     * @param array $companies
     */
    public static function addmanagers(&$companies) {
        global $DB;

        // Iterate over companies adding their managers.
        foreach ($companies as $company) {

            // Company managers
            $company->companymanagers = $DB->get_records_sql(
                "SELECT u.* from {company_users} cu
                JOIN {user} u ON u.id = cu.userid
                WHERE companyid = :companyid
                AND managertype = 1", ['companyid' => $company->id]);

            // Department managers
            $company->departmentmanagers = $DB->get_records_sql(
                "SELECT DISTINCT u.* from {company_users} cu
                JOIN {user} u ON u.id = cu.userid
                WHERE companyid = :companyid
                AND managertype = 2", ['companyid' => $company->id]);

            $company->nomanagers = empty($company->departmentmanagers) && empty($company->companymanagers);
            $company->companymanagerscount = count($company->companymanagers);
            $company->departmentmanagerscount = count($company->departmentmanagers);
            $company->companymanagers = self::listusers($company->companymanagers);
            $company->departmentmanagers = self::listusers($company->departmentmanagers);
        }
    }

    /**
     * Append the company users to companies.
     * @param array $companies
     */
    public static function addusers( &$companies ) {
        global $DB;

        // Iterate over companies adding their managers.
        foreach ($companies as $company) {
            $users = array();
            if ($companyusers = $DB->get_records('company_users', array('companyid' => $company->id))) {
                foreach ($companyusers as $companyuser) {
                    if ($user = $DB->get_record( 'user', array('id' => $companyuser->userid))) {
                        $users[$user->id] = $user;
                    }
                }
            }
            $company->users = self::listusers($users);
            $company->nousers = empty($users);
            $company->userscount = count($users);
        }
    }

    // Append the company courses to companies.
    public static function addcourses( &$companies ) {
        global $DB;

        // Iterate over companies adding their managers.
        foreach ($companies as $company) {
            $courses = array();
            if ($companycourses = $DB->get_records( 'company_course', array('companyid' => $company->id))) {
                foreach ($companycourses as $companycourse) {
                    if ($course = $DB->get_record( 'course', array('id' => $companycourse->courseid))) {
                        $courses[$course->id] = $course;
                    }
                }
            }
            $company->courses = $courses;
            $company->nocourses = empty($courses);
            $company->coursescount = count($courses);
        }
    }

    /**
     * Update users for template
     * @param array users
     * @return array
     */
    public static function listusers($users) {
        global $CFG;

        foreach ($users as $user) {
            $user->link = new \moodle_url('/user/view.php', ['id' => $user->id]);
            $user->fullname = fullname($user);
        }

        return array_values($users);
    }

}


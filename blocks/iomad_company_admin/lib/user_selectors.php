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

require_once(dirname(__FILE__) . '/../../../enrol/locallib.php');

/**
 * base class for selecting users of a company
 */
abstract class company_user_selector_base extends user_selector_base {

    protected $companyid;
    protected $courseid;
    protected $departmentid;
    protected $courses;
    protected $company;
    protected $selectedcourses;

    public function __construct($name, $options) {
        $this->companyid  = $options['companyid'];
        if (isset ( $options['courseid']) ) {
            $this->courseid = $options['courseid'];
        }
        if (empty($options['departmentid'])) {
            $parentdepartment = company::get_company_parentnode($this->companyid);
            $this->departmentid = $parentdepartment->id;
        } else {
            $this->departmentid = $options['departmentid'];
        }
        if (!empty($options['courses'])) {
            $this->courses = $options['courses'];
        }
        if (!empty($options['selectedcourses'])) {
            $this->selectedcourses = $options['selectedcourses'];
        }
        $this->company = new company($this->companyid);

        parent::__construct($name, $options);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['companyid'] = $this->companyid;
        $options['file']    = 'blocks/iomad_company_admin/lib.php';
        if (!empty($this->courses)) {
            $options['courses'] = $this->courses;
        }
        if (!empty($this->selectedcourses)) {
            $options['selectedcourses'] = $this->selectedcourses;
        }

        return $options;
    }

    protected function get_course_user_ids() {
        global $CFG, $DB, $PAGE;
        if (!isset( $this->courseid) ) {
            return array();
        } else {
            $course = $DB->get_record('course', array('id' => $this->courseid));
            $courseenrolmentmanager = new courseenrolmentmanager($PAGE, $course);

            $users = $courseenrolmentmanager->get_users('lastname', $sort = 'ASC', $page = 0, $perpage = 0);

            // Only return the keys (user ids).
            return array_keys($users);
        }
    }
}

class current_company_managers_user_selector extends company_user_selector_base {
    /**
     * Company manager users
     * @param <type> $search
     * @return array
     */
    public function find_users($search) {
        global $CFG, $DB;
        // By default wherecondition retrieves all users except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['companyid'] = $this->companyid;

        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';
        $sql = " FROM {user} u
                JOIN {company_users} cu ON (u.id = cu.userid AND cu.companyid = :companyid)
                WHERE $wherecondition AND u.suspended = 0 ";

        $order = ' ORDER BY u.firstname ASC, u.lastname ASC';

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > $CFG->iomad_max_select_users) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('companymanagersmatching', 'block_iomad_company_admin', $search);
        } else {
            $groupname = get_string('companymanagers', 'block_iomad_company_admin');
        }

        return array($groupname => $availableusers);
    }
}


class potential_company_managers_user_selector extends company_user_selector_base {
    /**
     * Potential company manager users
     * @param <type> $search
     * @return array
     */
    public function find_users($search) {
        global $CFG, $DB;
        // By default wherecondition retrieves all users except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['companyid'] = $this->companyid;
        $params['companyidforjoin'] = $this->companyid;

        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM
                    {user} u INNER JOIN {company_users} cu ON (cu.userid = u.id AND cu.companyid = :companyid AND cu.managertype = 0)
                WHERE $wherecondition AND u.suspended = 0 ";

        $order = ' ORDER BY u.firstname ASC, u.lastname ASC';

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > $CFG->iomad_max_select_users) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('potmanagersmatching', 'block_iomad_company_admin', $search);
        } else {
            $groupname = get_string('potmanagers', 'block_iomad_company_admin');
        }

        return array($groupname => $availableusers);
    }
}

class current_company_users_user_selector extends company_user_selector_base {
    /**
     * Company users
     * @param <type> $search
     * @return array
     */
    public function find_users($search) {
        global $CFG, $DB;
        // By default wherecondition retrieves all users except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['companyid'] = $this->companyid;

        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM
                    {user} u INNER JOIN {company_users} cu ON (cu.companyid = :companyid AND cu.userid = u.id )
                WHERE $wherecondition AND u.suspended = 0 ";

        $order = ' ORDER BY u.firstname ASC, u.lastname ASC';

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > $CFG->iomad_max_select_users) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('companyusersmatching', 'block_iomad_company_admin', $search);
        } else {
            $groupname = get_string('companyusers', 'block_iomad_company_admin');
        }

        return array($groupname => $availableusers);
    }
}


class potential_company_users_user_selector extends company_user_selector_base {
    /**
     * Potential company users - only shows those users that aren't already assigned to a company
     * @param <type> $search
     * @return array
     */
    public function find_users($search) {
        global $CFG, $DB;
        // By default wherecondition retrieves all users except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['companyid'] = $this->companyid;
        $params['companyidforjoin'] = $this->companyid;

        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM
                    {user} u
                WHERE $wherecondition AND u.suspended = 0
                      AND u.id NOT IN (
                        SELECT userid
                        FROM
                            {company_users} )";

        $order = ' ORDER BY u.firstname ASC, u.lastname ASC';

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > $CFG->iomad_max_select_users) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('potusersmatching', 'block_iomad_company_admin', $search);
        } else {
            $groupname = get_string('potusers', 'block_iomad_company_admin');
        }

        return array($groupname => $availableusers);
    }
}

class current_company_course_user_selector extends company_user_selector_base {
    /**
     * Company users enrolled into the selected company course
     * @param <type> $search
     * @return array
     */
    public function find_users($search, $all = false) {
        global $CFG, $DB;

        // By default wherecondition retrieves all users except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['companyid'] = $this->companyid;

        if (in_array(0, $this->selectedcourses)) {
            // Deal with all.
            $companycourses = $this->company->get_menu_courses(true, true);
            unset($companycourses[0]);
            $coursesql = "AND e.courseid IN (" . join (',', array_keys($companycourses)). ")";
        } else {
            $coursesql = "AND e.courseid IN (" .  join (',', array_values($this->selectedcourses)) . ")";
        }

        if (!in_array(0, $this->selectedcourses) && count($this->selectedcourses) == 1) {
            $single = true;
        } else {
            $single = false;
        }

        // Deal with departments.
        $departmentlist = company::get_all_subdepartments($this->departmentid);
        $departmentsql = "";
        if (!empty($departmentlist)) {
            $departmentsql = " AND cu.departmentid in (".implode(',', array_keys($departmentlist)).")";
        }

        $fields      = 'SELECT ue.id as userenrolmentid, u.id as userid,' . $this->required_fields_sql('u') . ', u.email, c.id AS courseid, c.fullname';
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM {user} u
                    JOIN {company_users} cu
                    ON (cu.userid = u.id AND cu.managertype = 0 $departmentsql)
                    JOIN {user_enrolments} ue ON (ue.userid = u.id)
                    JOIN {enrol} e
                    ON (ue.enrolid = e.id AND ".$DB->sql_compare_text('e.enrol')."='manual' AND e.status = 0)
                    JOIN {course} c ON (e.courseid = c.id)
                WHERE $wherecondition AND u.suspended = 0
                    AND cu.companyid = :companyid
                    $coursesql";

        $order = ' ORDER BY u.firstname, u.lastname, c.fullname ASC';

        if (!$this->is_validating() && !$all) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > $CFG->iomad_max_select_users) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }
        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        // We want the enrolment id here not the user id.
        foreach ($availableusers as $id => $user) {
            $availableusers[$id]->id = $id;

        }
        // are we doing any post processing?
        if (!$single) {
            foreach ($availableusers as $id => $user) {
                $availableusers[$id]->email = $user->email . "(" . $user->fullname . ")";
            }
        }
        if ($search) {
            $groupname = get_string('currentlyenrolledusersmatching', 'block_iomad_company_admin', $search);
        } else {
            $groupname = get_string('currentlyenrolledusers', 'block_iomad_company_admin');
        }

        return array($groupname => $availableusers);
    }

    /**
     * Get the list of users that were selected by doing optional_param then validating the result.
     *
     * @return array of user objects.
     */
    protected function load_selected_users() {
        // See if we got anything.
        if ($this->multiselect) {
            $userids = optional_param_array($this->name, array(), PARAM_INT);
        } else if ($userid = optional_param($this->name, 0, PARAM_INT)) {
            $userids = array($userid);
        }
        // If there are no users there is nobody to load.
        if (empty($userids)) {
            return array();
        }

        // If we did, use the find_users method to validate the ids.
        $groupedusers = $this->find_users('', true);

        // Aggregate the resulting list back into a single one.
        $users = array();
        foreach ($groupedusers as $group) {
            foreach ($group as $user) {
                if (!isset($users[$user->userenrolmentid]) && empty($user->disabled) && in_array($user->userenrolmentid, $userids)) {
                    $users[$user->userenrolmentid] = $user;
                }
            }
        }

        // If we are only supposed to be selecting a single user, make sure we do.
        if (!$this->multiselect && count($users) > 1) {
            $users = array_slice($users, 0, 1);
        }

        return $users;
    }

    /**
     * Output one particular optgroup. Used by the preceding function output_options.
     *
     * @param string $groupname the label for this optgroup.
     * @param array $users the users to put in this optgroup.
     * @param boolean $select if true, select the users in this group.
     * @return string HTML code.
     */
    protected function output_optgroup($groupname, $users, $select) {
        if (!empty($users)) {
            $output = '  <optgroup label="' . htmlspecialchars($groupname) . ' (' . count($users) . ')">' . "\n";
            foreach ($users as $user) {
                $attributes = '';
                if (!empty($user->disabled)) {
                    $attributes .= ' disabled="disabled"';
                } else if ($select || isset($this->selected[$user->id])) {
                    $attributes .= ' selected="selected"';
                }
                unset($this->selected[$user->id]);
                $output .= '    <option' . $attributes . ' value="' . $user->userenrolmentid . '">' .
                        $this->output_user($user) . "</option>\n";
                if (!empty($user->infobelow)) {
                    // Poor man's indent  here is because CSS styles do not work in select options, except in Firefox.
                    $output .= '    <option disabled="disabled" class="userselector-infobelow">' .
                            '&nbsp;&nbsp;&nbsp;&nbsp;' . s($user->infobelow) . '</option>';
                }
            }
        } else {
            $output = '  <optgroup label="' . htmlspecialchars($groupname) . '">' . "\n";
            $output .= '    <option disabled="disabled">&nbsp;</option>' . "\n";
        }
        $output .= "  </optgroup>\n";
        return $output;
    }
}

class potential_company_course_user_selector extends company_user_selector_base {

    public function __construct($name, $options) {
        $this->companyid  = $options['companyid'];
        $this->departmentid = $options['departmentid'];
        $this->subdepartments = $options['subdepartments'];
        $this->parentdepartmentid = $options['parentdepartmentid'];
        parent::__construct($name, $options);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['companyid'] = $this->companyid;
        $options['courseid'] = $this->courseid;
        $options['departmentid'] = $this->departmentid;
        $options['subdepartments'] = $this->subdepartments;
        $options['parentdepartmentid'] = $this->parentdepartmentid;
        $options['file']    = 'blocks/iomad_company_admin/lib.php';
        return $options;
    }

    protected function get_courses_user_ids() {
        global $CFG, $DB;

        if (in_array(0, $this->selectedcourses)) {
            $selectedcourses = $this->company->get_menu_courses(true, true);
            unset ($selectedcourses[0]);
            $coursesql = "e.courseid IN (" . implode(',', array_keys($selectedcourses)) . ") ";
            $countsql = " HAVING count(ue.enrolid) = " . count($selectedcourses);
        } else {
            $selectedcourses = $this->selectedcourses;
            $coursesql = "e.courseid IN (" . implode(',', array_values($selectedcourses)) . ") ";
            $countsql = " HAVING count(ue.enrolid) = " . count($selectedcourses);
        }
        if (!isset( $this->selectedcourses) ) {
            return array();
        } else {
            $usersql = "SELECT ue.userid,count(ue.enrolid) AS enrolcount FROM {user_enrolments} ue
                        JOIN {enrol} e ON (ue.enrolid = e.id AND ".$DB->sql_compare_text('e.enrol')."='manual' AND e.status = 0)
                        WHERE $coursesql
                        GROUP BY ue.userid
                        $countsql";
            if ($users = $DB->get_records_sql($usersql)) {
                // Only return the keys (user ids).
                return array_keys($users);
            } else {
                return array();
            }
        }
    }

    /**
     * Company users enrolled into the selected company course
     * @param <type> $search
     * @return array
     */
    public function find_users($search, $all = false) {
        global $CFG, $DB;
        $companyrec = $DB->get_record('company', array('id' => $this->companyid));
        $company = new company($this->companyid);

        // Get the full company tree as we may need it.
        $topcompanyid = $company->get_topcompanyid();
        $topcompany = new company($topcompanyid);
        $companytree = $topcompany->get_child_companies_recursive();
        $parentcompanies = $company->get_parent_companies_recursive();

        // By default wherecondition retrieves all users except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['companyid'] = $this->companyid;
        $params['courseid'] = $this->courseid;

        // Deal with departments.
        $departmentlist = company::get_all_subdepartments($this->departmentid);
        $departmentsql = "";
        if (!empty($departmentlist)) {
            $departmentsql = " AND cu.departmentid IN (".implode(',', array_keys($departmentlist)).")";
        } else {
            $departmentsql = "";
        }

        // Deal with parent company managers
        if (!empty($parentcompanies)) {
            $userfilter = " AND u.id NOT IN (
                             SELECT userid FROM {company_users}
                             WHERE companyid IN (" . implode(',', array_keys($parentcompanies)) . "))";
        } else {
            $userfilter = "";
        }

        // Get the current enrolled users.
        $enrolledusers = $this->get_courses_user_ids();
        if (count($enrolledusers) > 0) {
            $userfilter .= " AND u.id NOT IN (" . implode(',', $enrolledusers) . ") ";
        }

        $fields      = 'SELECT DISTINCT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM
                    {user} u INNER JOIN {company_users} cu ON cu.userid = u.id
                WHERE $wherecondition  AND u.suspended = 0 $departmentsql
                    AND
                    cu.companyid = :companyid
                    $userfilter";

        $order = ' ORDER BY u.firstname ASC, u.lastname ASC';

        if (!$this->is_validating() && !$all) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > $CFG->iomad_max_select_users) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('potentialcourseusersmatching', 'block_iomad_company_admin', $search);
        } else {
            $groupname = get_string('potentialcourseusers', 'block_iomad_company_admin');
        }

        return array($groupname => $availableusers);
    }
}

class potential_department_user_selector extends user_selector_base {
    /**
     * Company users enrolled into the selected company course
     * @param <type> $search
     * @return array
     */

    protected $companyid;
    protected $departmentid;
    protected $roletype;
    protected $parentdepartmentid;
    protected $showothermanagers;

    public function __construct($name, $options) {
        $this->companyid  = $options['companyid'];
        $this->departmentid = $options['departmentid'];
        $this->roletype = $options['roletype'];
        $this->subdepartments = $options['subdepartments'];
        $this->parentdepartmentid = $options['parentdepartmentid'];
        $this->showothermanagers = $options['showothermanagers'];
        parent::__construct($name, $options);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['companyid'] = $this->companyid;
        $options['departmentid'] = $this->departmentid;
        $options['roletype'] = $this->roletype;
        $options['subdepartments'] = $this->subdepartments;
        $options['parentdepartmentid'] = $this->parentdepartmentid;
        $options['showothermanagers'] = $this->showothermanagers;
        $options['file']    = 'blocks/iomad_company_admin/lib.php';
        return $options;
    }

    protected function get_department_user_ids() {
        global $CFG, $DB;
        if (!isset( $this->departmentid) ) {
            return array();
        } else {
            if ($this->roletype != 3) {
                // We dont want users of this type in the list.
                if ($users = $DB->get_records('company_users', array('departmentid' => $this->departmentid,
                                                                     'managertype' => $this->roletype,
                                                                     'suspended' => 0), null, 'userid')) {
                    // Only return the keys (user ids).
                    return array_keys($users);
                } else {
                    return array();
                }
            } else {
                if ($users = $DB->get_records('company_users', array('companyid' => $this->companyid,
                                                                     'educator' => 1,
                                                                     'suspended' => 0), null, 'userid')) {
                    // Only return the keys (user ids).
                    return array_keys($users);
                } else {
                    return array();
                }
            }
        }
    }

    protected function process_other_company_managers(&$userlist) {
        global $CFG, $DB;
        foreach ($userlist as $id => $user) {
            $sql = "SELECT c.name FROM {company} c
                    INNER JOIN {company_users} cu ON c.id = cu.companyid
                    WHERE
                    cu.userid = $id
                    AND c.id != :companyid
                    ORDER BY cu.id";
            if ($companies = $DB->get_records_sql($sql, array('companyid' => $this->companyid), 0, 1)) {
                $company = array_shift($companies);
                $userlist[$id]->email = $user->email." - ".$company->name;
            }
        }
    }

    public function find_users($search) {
        global $CFG, $DB, $USER;
        $companyrec = $DB->get_record('company', array('id' => $this->companyid));
        $company = new company($this->companyid);

        // Get the full company tree as we may need it.
        $topcompanyid = $company->get_topcompanyid();
        $topcompany = new company($topcompanyid);
        $companytree = $topcompany->get_child_companies_recursive();
        $parentcompanies = $company->get_parent_companies_recursive();

        // By default wherecondition retrieves all users except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['companyid'] = $this->companyid;

        $fields      = 'SELECT DISTINCT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT DISTINCT COUNT(u.id)';

        $departmentusers = $this->get_department_user_ids();
        // Add the ID of the current User to exclude them from the results
        $departmentusers[] = $USER->id;
        if (!empty($parentcompanies)) {
            $userfilter = " AND NOT u.id IN (" . implode(",",$departmentusers) . ")
                            AND u.id NOT IN (
                              SELECT userid FROM {company_users}
                              WHERE companyid IN (" . implode(',', array_keys($parentcompanies)) . "))";
        } else {
            $userfilter = " AND NOT u.id IN (" . implode(",",$departmentusers) . ")";
        }

        if ($this->roletype != 0) {
            // Dealing with management possibles could be from anywhere.
            $deptids = implode(',', array_keys($this->subdepartments));
        } else {
            // Normal staff allocations.
            unset($this->subdepartments[$this->departmentid]);
            if ($this->departmentid == $this->parentdepartmentid->id) {
                $deptids = implode(',', array_keys($this->subdepartments));
            } else {
                if (!empty($this->subdepartments)) {
                    $deptids = $this->parentdepartmentid->id .','.implode(',', array_keys($this->subdepartments));
                } else {
                    $deptids = $this->parentdepartmentid->id;
                }
            }
        }

        if (!empty($deptids)) {
            $departmentsql = "AND du.departmentid in ($deptids)";
        } else {
            return array();
        }

        $sql = " FROM
                    {user} u
                    INNER JOIN {company_users} du ON du.userid = u.id
                WHERE $wherecondition AND u.suspended = 0
                    $departmentsql
                    $userfilter";

        $order = ' ORDER BY u.firstname ASC, u.lastname ASC';

        // Are we also looking for other managers?
        if (!empty($this->showothermanagers)) {
            $othermanagersql = " FROM {user} u
                                INNER JOIN {company_users} du on du.userid = u.id
                                WHERE $wherecondition
                                AND u.suspended = 0
                                AND du.managertype = 1
                                AND du.companyid != " . $this->companyid."
                                AND du.userid NOT IN (
                                  SELECT userid FROM {company_users}
                                  WHERE managertype = 1
                                  AND companyid = " . $this->companyid . ")";
        } else {
            $othermanagersql = " FROM {user} u where 1 = 2";
        }

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params)
                                     + $DB->count_records_sql($countfields . $othermanagersql, $params);
            if ($potentialmemberscount > $CFG->iomad_max_select_users) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }
        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params)
                          + $DB->get_records_sql($fields . $othermanagersql . $order, $params);
        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            if ($this->roletype != 0 && $this->roletype != 3) {
                $groupname = get_string('potmanagersmatching', 'block_iomad_company_admin', $search);
            } else {
                $groupname = get_string('potusersmatching', 'block_iomad_company_admin', $search);
            }
        } else {
            if ($this->roletype != 0 && $this->roletype != 3) {
                $groupname = get_string('potmanagers', 'block_iomad_company_admin');
            } else {
                $groupname = get_string('potusers', 'block_iomad_company_admin');
            }
        }

        // Process user names.
        $this->process_other_company_managers($availableusers);

        return array($groupname => $availableusers);
    }
}

class current_department_user_selector extends user_selector_base {
    /**
     * Company users enrolled into the selected company course
     * @param <type> $search
     * @return array
     */

    protected $companyid;
    protected $departmentid;
    protected $roletype;

    public function __construct($name, $options) {
        $this->companyid  = $options['companyid'];
        $this->departmentid = $options['departmentid'];
        $this->roletype = $options['roletype'];
        $this->showothermanagers = $options['showothermanagers'];
        parent::__construct($name, $options);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['companyid'] = $this->companyid;
        $options['departmentid'] = $this->departmentid;
        $options['roletype'] = $this->roletype;
        $options['file']    = 'blocks/iomad_company_admin/lib.php';
        $options['showothermanagers'] = $this->showothermanagers;
        return $options;
    }

    protected function get_department_user_ids() {
        global $CFG, $DB;
        if (!isset( $this->departmentid) ) {
            return array();
        } else {
            if ($users = $DB->get_records('company_users', array('departmentid' => $this->departmentid, 'suspended' => 0), null, 'userid')) {
                // Only return the keys (user ids).
                return array_values($users);
            } else {
                return array();
            }
        }
    }

    public function find_users($search) {
        global $CFG, $DB, $USER;
        $companyrec = $DB->get_record('company', array('id' => $this->companyid));
        $company = new company($this->companyid);

        // Get the full company tree as we may need it.
        $topcompanyid = $company->get_topcompanyid();
        $topcompany = new company($topcompanyid);
        $companytree = $topcompany->get_child_companies_recursive();
        $parentcompanies = $company->get_parent_companies_recursive();

        // By default wherecondition retrieves all users except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['companyid'] = $this->companyid;
        $params['thiscompanyid'] = $this->companyid;

        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        if ($this->roletype == 1 && !empty($parentcompanies)) {
            $othermanagersql = " AND cu.userid NOT IN (
                                   SELECT userid FROM {company_users}
                                   WHERE managertype = 1
                                   AND companyid IN (" . implode(',', array_keys($parentcompanies)) . "))";
        } else {
            $othermanagersql = "";
        }
        if ($this->roletype != 3) {
            $rolesql = "AND cu.managertype = ($this->roletype)";
        } else {
            $rolesql = "AND cu.educator = 1";
        }

        $sql = " FROM {user} u
                 INNER JOIN {company_users} cu ON cu.userid = u.id
                 WHERE $wherecondition $othermanagersql AND u.suspended = 0
                 $rolesql
                 AND  u.id != ($USER->id)
                    AND
                    cu.departmentid = ($this->departmentid)";

        $order = ' ORDER BY u.firstname ASC, u.lastname ASC';

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > $CFG->iomad_max_select_users) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }
        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            if ($this->roletype == 2) {
                $groupname = get_string('departmentmanagersmatching', 'block_iomad_company_admin', $search);
            } else if ($this->roletype == 0) {
                $groupname = get_string('departmentusersmatching', 'block_iomad_company_admin', $search);
            } else if ($this->roletype == 1) {
                $groupname = get_string('companymanagersmatching', 'block_iomad_company_admin', $search);
            } else if ($this->roletype == 3) {
                $groupname = get_string('curusersmatching', 'block_iomad_company_admin', $search);
            }
        } else {
            if ($this->roletype == 2) {
                $groupname = get_string('departmentmanagers', 'block_iomad_company_admin');
            } else if ($this->roletype == 0) {
                $groupname = get_string('departmentusers', 'block_iomad_company_admin');
            } else if ($this->roletype == 1) {
                $groupname = get_string('companymanagers', 'block_iomad_company_admin');
            } else if ($this->roletype == 3) {
                $groupname = get_string('curusers', 'block_iomad_company_admin');
            } else if ($this->roletype == 4) {
                $groupname = get_string('companyreporters', 'block_iomad_company_admin');
            }
        }

        return array($groupname => $availableusers);
    }
}

class potential_license_user_selector extends user_selector_base {
    /**
     * Company users enrolled into the selected company course
     * @param <type> $search
     * @return array
     */

    protected $companyid;
    protected $licenseid;
    protected $departmentid;
    protected $subdepartments;
    protected $parentdepartmentid;
    protected $program;
    protected $multiselect;
    protected $license;
    protected $selectedcourses;

    public function __construct($name, $options) {
        global $CFG, $DB;

        $this->companyid  = $options['companyid'];
        $this->licenseid = $options['licenseid'];
        $this->departmentid = $options['departmentid'];
        $this->subdepartments = $options['subdepartments'];
        $this->parentdepartmentid = $options['parentdepartmentid'];
        $this->program = $options['program'];
        $this->multiselect = $options['multiselect'];
        $this->license = $DB->get_record('companylicense', array('id' => $this->licenseid));
        $this->selectedcourses = $options['selectedcourses'];
        $this->courses = $options['courses'];
        unset($this->courses[0]);

        parent::__construct($name, $options);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['companyid'] = $this->companyid;
        $options['licenseid'] = $this->licenseid;
        $options['departmentid'] = $this->departmentid;
        $options['subdepartments'] = $this->subdepartments;
        $options['parentdepartmentid'] = $this->parentdepartmentid;
        $options['program'] = $this->program;
        $options['file']    = 'blocks/iomad_company_admin/lib.php';
        $options['multiselect']    = $this->multiselect;
        $options['selectedcourses'] = $this->selectedcourses;
        $options['courses'] = $this->courses;

        return $options;
    }

    protected function get_license_user_ids() {
        global $CFG, $DB;

        if (!isset( $this->license->id) ) {
            return array();
        } else {
            if (!empty($this->selectedcourses) && !in_array(0, $this->selectedcourses)) {
                $coursesql = " AND licensecourseid IN (" . implode(',', array_values($this->selectedcourses)) . ") ";
                $countsql = " HAVING count(licensecourseid) = " . count($this->selectedcourses);
            } else {
                $coursesql = " AND licensecourseid IN (" . implode(',', array_keys($this->courses)) . ") ";
                $countsql = " HAVING count(licensecourseid) = " . count($this->courses);
            }
            if ($this->program) {
                $usersql = "select DISTINCT userid from {companylicense_users} where licenseid=".$this->licenseid."
                            AND timecompleted IS NULL";
            } else {
                $usersql = "SELECT userid,count(licensecourseid) AS coursecount FROM {companylicense_users}
                            WHERE timecompleted IS NULL
                            $coursesql
                            GROUP BY userid
                            $countsql";
            }
            if ($users = $DB->get_records_sql($usersql)) {
                // Only return the keys (user ids).
                return array_keys($users);
            } else {
                return array();
            }
        }
    }

    protected function get_license_department_ids() {
        global $CFG, $DB, $USER;

        if (!isset( $this->licenseid) ) {
            return array();
        } else {
            if (!$DB->get_records_sql("SELECT pc.id
                                      FROM {iomad_courses} pc
                                      INNER JOIN {companylicense_courses} clc
                                      ON clc.courseid = pc.courseid
                                      WHERE clc.licenseid=$this->licenseid
                                      AND pc.shared=1")) {
                // Check if we are a shared course or not.
                $courses = $DB->get_records('companylicense_courses', array('licenseid' => $this->licenseid));
                $shared = false;
                foreach ($courses as $course) {
                    if ($DB->get_record_select('iomad_courses', "courseid='".$course->courseid."' AND shared!= 0")) {
                        $shared = true;
                    }
                }
                $sql = "SELECT DISTINCT d.id from {department} d, {company_course} cc, {companylicense_courses} clc
                        WHERE
                        d.id = cc.departmentid
                        AND
                        cc.courseid = clc.courseid
                        AND
                        clc.licenseid = ".$this->licenseid ."
                        AND d.company = ".$this->companyid;
                $departments = $DB->get_records_sql($sql);
                $shareddepartment = array();
                if ($shared) {
                    if (iomad::has_capability('block/iomad_company_admin:edit_licenses', context_system::instance())) {
                        // Need to add the top level department.
                        $shareddepartment = company::get_company_parentnode($this->companyid);
                        $departments = $departments + array($shareddepartment->id => $shareddepartment->id);
                    } else {
                        $company = new company($this->companyid);
                        $shareddepartment = $company->get_userlevel($USER);
                        $departments = $departments + array($shareddepartment->id => $shareddepartment->id);
                    }
                }
                if (!empty($departments)) {
                    // Only return the keys (user ids).
                    return array_keys($departments);
                } else {
                    return array();
                }
            } else {
                return array($this->departmentid);
            }
        }
    }

    protected function process_license_allocations(&$licenseusers) {
        global $CFG, $DB;
        foreach ($licenseusers as $id => $user) {

            $sql = "SELECT d.shortname FROM {department} d
                    INNER JOIN {company_users} cu ON cu.departmentid = d.id
                    WHERE
                    cu.userid = :userid
                    AND cu.companyid = :companyid";
            if ($department = $DB->get_record_sql($sql, array('userid'=> $id, 'companyid' => $this->companyid))) {
                $licenseusers[$id]->email = $user->email." (".$department->shortname.")";
            }
        }
    }

    public function find_users($search, $all = false) {
        global $CFG, $DB, $USER;

        // If there are no courses we can't display any users.
        if (empty($this->selectedcourses)) {
            return array();
        }

        $companyrec = $DB->get_record('company', array('id' => $this->companyid));
        $company = new company($this->companyid);

        // Get the full company tree as we may need it.
        $topcompanyid = $company->get_topcompanyid();
        $topcompany = new company($topcompanyid);
        $companytree = $topcompany->get_child_companies_recursive();
        $parentcompanies = $company->get_parent_companies_recursive();

        // By default wherecondition retrieves all users except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['companyid'] = $this->companyid;

        $fields      = 'SELECT DISTINCT ' . $this->required_fields_sql('u').', u.email, d.shortname ';
        $countfields = 'SELECT COUNT(1)';
        $myusers = company::get_my_users($this->companyid);

        // are we dealing with an educator license?
        if ($this->license->type > 1) {
            $edusql = " AND u.id IN (SELECT userid FROM {company_users} WHERE educator = 1) ";
        } else {
            $edusql = "";
        }
        $licenseusers = $this->get_license_user_ids();
        if (count($licenseusers) > 0 && (!$this->multiselect || !$this->program)) {
            $userfilter = " AND NOT u.id in (" . implode(',', $licenseusers) . ") ";
        } else {
            $userfilter = "";
        }

        // Add in a filter to return just the users belonging to the current USER.
        if (!empty($myusers)) {
            $userfilter .= " AND u.id in (".implode(',',array_keys($myusers)).") ";
        }

        // Deal with parent company managers
        if (!empty($parentcompanies)) {
            $userfilter .= " AND u.id NOT IN (
                              SELECT userid FROM {company_users}
                              WHERE companyid IN (" . implode(',', array_keys($parentcompanies)) . "))";
        }

        // Get the department ids for this license.
        $departmentids = array_keys(company::get_all_subdepartments($this->departmentid));
        $deptids = implode(',', $departmentids);

        if (!empty($deptids)) {
            $departmentsql = "AND du.departmentid in ($deptids)";
        } else {
            return array();
        }

        $sql = " FROM
                    {user} u
                    INNER JOIN {company_users} du ON du.userid = u.id
                    INNER JOIN {department} d ON d.id = du.departmentid
                WHERE $wherecondition AND u.suspended = 0
                    $departmentsql
                    $userfilter
                    $edusql";

        $order = ' ORDER BY u.firstname ASC, u.lastname ASC';

        if (!$this->is_validating() && !$all) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > $CFG->iomad_max_select_users) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }
        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        $this->process_license_allocations($availableusers);
        if ($search) {
            $groupname = get_string('potusersmatching', 'block_iomad_company_admin', $search);
        } else {
            $groupname = get_string('potusers', 'block_iomad_company_admin');
        }

        return array($groupname => $availableusers);
    }
}

class current_license_user_selector extends user_selector_base {
    /**
     * Company users enrolled into the selected company course
     * @param <type> $search
     * @return array
     */

    protected $companyid;
    protected $licenseid;
    protected $departmentid;
    protected $subdepartments;
    protected $parentdepartmentid;
    protected $program;
    protected $multiselect;
    protected $license;
    protected $selectedcourses;

    public function __construct($name, $options) {
        global $CFG, $DB;

        $this->companyid  = $options['companyid'];
        $this->licenseid = $options['licenseid'];
        $this->departmentid = $options['departmentid'];
        $this->subdepartments = $options['subdepartments'];
        $this->parentdepartmentid = $options['parentdepartmentid'];
        $this->program = $options['program'];
        $this->multiselect = $options['multiselect'];
        $this->selectedcourses = $options['selectedcourses'];
        $this->courses = $options['courses'];
        unset($this->courses[0]);
        $this->license = $DB->get_record('companylicense', array('id' => $this->licenseid));

        parent::__construct($name, $options);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['companyid'] = $this->companyid;
        $options['licenseid'] = $this->licenseid;
        $options['departmentid'] = $this->departmentid;
        $options['subdepartments'] = $this->subdepartments;
        $options['parentdepartmentid'] = $this->parentdepartmentid;
        $options['program'] = $this->program;
        $options['selectedcourses'] = $this->selectedcourses;
        $options['courses'] = $this->courses;
        $options['multiselect'] = $this->multiselect;
        $options['file']    = 'blocks/iomad_company_admin/lib.php';
        return $options;
    }

    protected function get_license_user_ids() {
        global $CFG, $DB;

        if (!isset( $this->licenseid) ) {
            return array();
        } else {
            if (!empty($this->selectedcourses) && !in_array(0, $this->selectedcourses)) {
                $coursesql = " AND licensecourseid IN (" . implode(',', array_values($this->selectedcourses)) . ") ";
                $countsql = " HAVING count(licensecourseid) = " . count($this->selectedcourses);
            } else {
                return array();
                $coursesql = "";
                $countsql = " HAVING count(licensecourseid) = " . count($this->courses);
            }

            $usersql = "SELECT userid, count(licensecourseid) AS coursecount
                        FROM {companylicense_users}
                        WHERE licenseid=".$this->licenseid."
                        $coursesql
                        AND id NOT IN (
                            SELECT id FROM {companylicense_users}
                            WHERE licenseid = :licenseid
                            AND timecompleted IS NOT NULL
                        ) AND userid IN (
                            SELECT userid
                            FROM {company_users}
                            WHERE departmentid IN (" .
                            implode(',', array_keys($this->subdepartments)) .
                            "))
                            GROUP BY userid
                            $countsql";

            if ($users = $DB->get_records_sql($usersql, array('licenseid' => $this->licenseid))) {
                // Only return the keys (user ids).
                return array_values($users);
            } else {
                return array();
            }
        }
    }

    protected function process_license_allocations(&$licenseusers) {
        global $CFG, $DB;
        foreach ($licenseusers as $id => $user) {
            $sql = "SELECT d.shortname from {department} d
                    INNER JOIN {company_users} cu ON cu.departmentid = d.id
                    WHERE
                    cu.userid = $id";
            if ($department = $DB->get_record_sql($sql)) {
                $licenseusers[$id]->email = $user->email." (".$department->shortname.")";
            }
            if ($licenseinfo = $DB->get_record('companylicense_users', array('userid' => $id,
                                                                             'licenseid' => $this->licenseid,
                                                                             'timecompleted' => null))) {
                if ($licenseinfo->isusing == 1) {
                    $licenseusers[$id]->firstname = '*'.$user->firstname;
                }
            }
        }
    }

    public function find_users($search, $all = false) {
        global $CFG, $DB, $USER;

        // If there are no courses we can't display any users.
        if (empty($this->selectedcourses)) {
            return array();
        }

        // By default wherecondition retrieves all users except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['companyid'] = $this->companyid;
        $params['licenseid'] = $this->licenseid;

        $licenseusers = $this->get_license_user_ids();
        $licenseuserids = "";
        if (count($licenseusers) > 0) {
            foreach ($licenseusers as $licenseuser) {
                if (!empty($licenseuserids)) {
                    $licenseuserids .= ','.$licenseuser->userid;
                } else {
                    $licenseuserids = $licenseuser->userid;
                }
            }
            if ($licenseuserids != ',') {
                $userfilter = $licenseuserids;
            } else {
                $userfilter = "";
            }
        } else {
            $userfilter = "";
        }

        // Are we dealing with a program?
        if (empty($this->program)) {
            if (!empty($this->selectedcourses) && !in_array(0, $this->selectedcourses)) {
                $coursesql = " AND clu.licensecourseid IN (" . implode(',', array_values($this->selectedcourses)) . ") ";
            } else {
                $coursesql = "";
            }
            $maxcount = $CFG->iomad_max_select_users;
            $fields      = 'SELECT DISTINCT clu.id as licenseid, ' . $this->required_fields_sql('u') . ', u.email, c.fullname, clu.isusing ';
            $countfields = 'SELECT COUNT(1)';

            $sql = " FROM
                     {companylicense_users} clu LEFT JOIN {user} u ON (clu.userid = u.id), {course} c
                     WHERE $wherecondition AND u.suspended = 0
                     AND clu.licensecourseid = c.id
                     AND clu.licenseid = :licenseid
                     $coursesql
                     AND clu.timecompleted IS NULL
                     AND userid IN (
                        SELECT userid
                        FROM {company_users}
                        WHERE departmentid IN (" .
                        implode(',', array_keys($this->subdepartments)) .
                     "))";
            $order = ' ORDER BY u.firstname , u.lastname, c.fullname ASC';
        } else {
            $maxcount = $CFG->iomad_max_select_users * count($this->courses);
            $fields      = 'SELECT clu.id as licenseid, ' . $this->required_fields_sql('u') . ', u.email, clu.isusing ';
            $countfields = 'SELECT COUNT(1)';

            $sql = " FROM
                     {companylicense_users} clu LEFT JOIN {user} u ON (clu.userid = u.id)
                     WHERE $wherecondition AND u.suspended = 0
                     AND clu.licenseid = :licenseid
                     AND clu.timecompleted IS NULL
                     AND userid IN (
                        SELECT userid
                        FROM {company_users}
                        WHERE departmentid IN (" .
                        implode(',', array_keys($this->subdepartments)) .
                     "))";
            $order = ' ORDER BY u.firstname ASC, u.lastname ASC';
        }
        if (!$this->is_validating() && !$all) {
            if (!empty($userfilter)) {
                $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
                if ($potentialmemberscount > $maxcount) {
                    return $this->too_many_results($search, $potentialmemberscount);
                }
            } else {
                $potentialmemberscount = 0;
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        // If we are a program then we only want one entry per user.
        if (!empty($this->program)) {
            $userlist = array();
            foreach ($availableusers as $id => $rawuser) {
                $userlist[$rawuser->id] = $rawuser;
            }
            $availableusers = $userlist;
        }

        foreach ($availableusers as $id => $rawuser) {
            if (empty($this->program) && (in_array(0, $this->selectedcourses) || count($this->selectedcourses) > 1)) {
                $availableusers[$id]->email .= ' (' . $rawuser->fullname . ')';
            }

            if (!empty($rawuser->isusing) && ($this->license->type == 0 || $this->license->type == 2)) {
                $availableusers[$id]->firstname = ' *' . $availableusers[$id]->firstname;
            }
        }

        if ($search) {
            $groupname = get_string('licenseusersmatching', 'block_iomad_company_admin', $search);
        } else {
            $groupname = get_string('licenseusers', 'block_iomad_company_admin');
        }
        return array($groupname => $availableusers);
    }

    /**
     * Output one particular optgroup. Used by the preceding function output_options.
     *
     * @param string $groupname the label for this optgroup.
     * @param array $users the users to put in this optgroup.
     * @param boolean $select if true, select the users in this group.
     * @return string HTML code.
     */
    protected function output_optgroup($groupname, $users, $select) {
        if (!empty($users)) {
            $output = '  <optgroup label="' . htmlspecialchars($groupname) . ' (' . count($users) . ')">' . "\n";
            foreach ($users as $user) {
                $attributes = '';
                if (!empty($user->disabled)) {
                    $attributes .= ' disabled="disabled"';
                } else if ($select || isset($this->selected[$user->id])) {
                    $attributes .= ' selected="selected"';
                }
//unset($this->selected[$user->id]);
                $output .= '    <option' . $attributes . ' value="' . $user->licenseid . '">' .
                        $this->output_user($user) . "</option>\n";
                if (!empty($user->infobelow)) {
                    // Poor man's indent  here is because CSS styles do not work in select options, except in Firefox.
                    $output .= '    <option disabled="disabled" class="userselector-infobelow">' .
                            '&nbsp;&nbsp;&nbsp;&nbsp;' . s($user->infobelow) . '</option>';
                }
            }
        } else {
            $output = '  <optgroup label="' . htmlspecialchars($groupname) . '">' . "\n";
            $output .= '    <option disabled="disabled">&nbsp;</option>' . "\n";
        }
        $output .= "  </optgroup>\n";
        return $output;
    }

    /**
     * Get the list of users that were selected by doing optional_param then validating the result.
     *
     * @return array of user objects.
     */
    protected function load_selected_users() {
        // See if we got anything.
        if ($this->multiselect) {
            $userids = optional_param_array($this->name, array(), PARAM_INT);
        } else if ($userid = optional_param($this->name, 0, PARAM_INT)) {
            $userids = array($userid);
        }
        // If there are no users there is nobody to load.
        if (empty($userids)) {
            return array();
        }

        // If we did, use the find_users method to validate the ids.
        $groupedusers = $this->find_users('', true);

        // Aggregate the resulting list back into a single one.
        $users = array();
        foreach ($groupedusers as $group) {
            foreach ($group as $user) {
                if (!isset($users[$user->licenseid]) && empty($user->disabled) && in_array($user->licenseid, $userids)) {
                    $users[$user->licenseid] = $user;
                }
            }
        }

        // If we are only supposed to be selecting a single user, make sure we do.
        if (!$this->multiselect && count($users) > 1) {
            $users = array_slice($users, 0, 1);
        }

        return $users;
    }
}

class current_company_group_user_selector extends company_user_selector_base {

    public function __construct($name, $options) {
        $this->companyid  = $options['companyid'];
        $this->courseid = $options['courseid'];
        $this->departmentid = $options['departmentid'];
        $this->groupid = $options['groupid'];

        parent::__construct($name, $options);
    }

    /**
     * Company users enrolled into the selected company course
     * @param <type> $search
     * @return array
     */
    public function find_users($search) {
        global $CFG, $DB;
        // By default wherecondition retrieves all users except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['companyid'] = $this->companyid;
        $params['courseid'] = $this->courseid;
        $params['groupid'] = $this->groupid;
        $params['liccourseid'] = $this->courseid;
        $params['licgroupid'] = $this->groupid;

        // Deal with departments.
        $departmentlist = company::get_all_subdepartments($this->departmentid);
        $departmentsql = "";
        if (!empty($departmentlist)) {
            $departmentsql = " AND cu.departmentid in (".implode(',', array_keys($departmentlist)).")";
        }

        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM
                    {user} u INNER JOIN {company_users} cu
                    ON cu.userid = u.id AND managertype = 0 $departmentsql
                WHERE $wherecondition AND u.suspended = 0
                    AND cu.companyid = :companyid
                    AND cu.userid IN (
                      SELECT userid
                      FROM {groups_members}
                      WHERE groupid=:groupid
                    )
                    OR cu.userid IN (
                      SELECT userid
                      FROM {companylicense_users}
                      WHERE isusing = 0
                      AND licensecourseid = :liccourseid
                      AND groupid = :licgroupid
                    )";

        $order = ' ORDER BY u.firstname ASC, u.lastname ASC';

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > $CFG->iomad_max_select_users) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('currentgroupusersmatching', 'block_iomad_company_admin', $search);
        } else {
            $groupname = get_string('currentgroupusers', 'block_iomad_company_admin');
        }

        return array($groupname => $availableusers);
    }
}

class potential_company_group_user_selector extends company_user_selector_base {

    public function __construct($name, $options) {
        $this->companyid  = $options['companyid'];
        $this->courseid = $options['courseid'];
        $this->departmentid = $options['departmentid'];
        $this->groupid = $options['groupid'];

        parent::__construct($name, $options);
    }

    /**
     * Company users enrolled into the selected company course
     * @param <type> $search
     * @return array
     */
    public function find_users($search) {
        global $CFG, $DB;
        $companyrec = $DB->get_record('company', array('id' => $this->companyid));
        $company = new company($this->companyid);

        // Get the full company tree as we may need it.
        $topcompanyid = $company->get_topcompanyid();
        $topcompany = new company($topcompanyid);
        $companytree = $topcompany->get_child_companies_recursive();
        $parentcompanies = $company->get_parent_companies_recursive();

        // By default wherecondition retrieves all users except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['companyid'] = $this->companyid;
        $params['courseid'] = $this->courseid;
        $params['groupid'] = $this->groupid;
        $params['liccourseid'] = $this->courseid;
        $params['licgroupid'] = $this->groupid;

        // Deal with departments.
        $departmentlist = company::get_all_subdepartments($this->departmentid);
        $departmentsql = "";
        if (!empty($departmentlist)) {
            $departmentsql = " AND cu.departmentid IN (".implode(',', array_keys($departmentlist)).")";
        } else {
            $departmentsql = "";
        }

        // Deal with parent company managers
        if (!empty($parentcompanies)) {
            $userfilter = " AND u.id NOT IN (
                             SELECT userid FROM {company_users}
                             WHERE companyid IN (" . implode(',', array_keys($parentcompanies)) . "))";
        } else {
            $userfilter = "";
        }

        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM
                    {user} u INNER JOIN {company_users} cu ON (cu.userid = u.id)
                WHERE $wherecondition  AND u.suspended = 0 $departmentsql
                    AND
                    cu.companyid = :companyid
                    $userfilter
                    AND u.id NOT IN (
                       SELECT userid from {groups_members}
                       WHERE groupid = :groupid
                    )
                    AND (
                      u.id IN (
                        SELECT DISTINCT(ue.userid)
                        FROM {user_enrolments} ue
                        INNER JOIN {enrol} e
                        ON ue.enrolid=e.id
                        WHERE e.courseid=:courseid
                      )
                      OR u.id IN (
                        SELECT userid
                        FROM {companylicense_users}
                        WHERE licensecourseid = :liccourseid
                        AND groupid != :licgroupid
                      )
                    )";

        $order = ' ORDER BY u.firstname ASC, u.lastname ASC';

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > $CFG->iomad_max_select_users) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }
        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('potentialgroupusersmatching', 'block_iomad_company_admin', $search);
        } else {
            $groupname = get_string('potentialgroupusers', 'block_iomad_company_admin');
        }

        return array($groupname => $availableusers);
    }
}

class current_company_thread_user_selector extends company_user_selector_base {
    public function __construct($name, $options) {
        $this->companyid  = $options['companyid'];
        $this->threadid = $options['threadid'];
        $this->departmentid = $options['departmentid'];

        parent::__construct($name, $options);
    }

    /**
     * Company users enrolled into the selected company course
     * @param <type> $search
     * @return array
     */
    public function find_users($search, $all = false) {
        global $CFG, $DB;

        // By default wherecondition retrieves all users except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['companyid'] = $this->companyid;
        $params['threadid'] = $this->threadid;

        // Deal with departments.
        $departmentlist = company::get_all_subdepartments($this->departmentid);
        $departmentsql = "";
        if (!empty($departmentlist)) {
            $departmentsql = " AND cu.departmentid in (".implode(',', array_keys($departmentlist)).")";
        }

        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM {user} u
                 INNER JOIN {company_users} cu
                 ON (cu.userid = u.id $departmentsql)
                WHERE $wherecondition AND u.suspended = 0
                    AND cu.companyid = :companyid
                    AND cu.userid IN
                     (SELECT DISTINCT userid
                     FROM {microlearning_thread_user}
                     WHERE threadid=:threadid)";

        $order = ' ORDER BY u.lastname ASC, u.firstname ASC';

        if (!$this->is_validating() && !$all) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > $CFG->iomad_max_select_users) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('currentlyenrolledusersmatching', 'block_iomad_company_admin', $search);
        } else {
            $groupname = get_string('currentlyenrolledusers', 'block_iomad_company_admin');
        }

        return array($groupname => $availableusers);
    }
}

class potential_company_thread_user_selector extends company_user_selector_base {

    public function __construct($name, $options) {
        $this->companyid  = $options['companyid'];
        $this->threadid  = $options['threadid'];
        $this->departmentid = $options['departmentid'];
        $this->subdepartments = $options['subdepartments'];
        $this->parentdepartmentid = $options['parentdepartmentid'];
        parent::__construct($name, $options);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['companyid'] = $this->companyid;
        $options['threadid'] = $this->threadid;
        $options['departmentid'] = $this->departmentid;
        $options['subdepartments'] = $this->subdepartments;
        $options['parentdepartmentid'] = $this->parentdepartmentid;
        $options['file']    = 'blocks/iomad_company_admin/lib.php';
        return $options;
    }

    /**
     * Company users enrolled into the selected company course
     * @param <type> $search
     * @return array
     */
    public function find_users($search, $all = false) {
        global $CFG, $DB;

        $companyrec = $DB->get_record('company', array('id' => $this->companyid));
        $company = new company($this->companyid);

        // Get the full company tree as we may need it.
        $topcompanyid = $company->get_topcompanyid();
        $topcompany = new company($topcompanyid);
        $companytree = $topcompany->get_child_companies_recursive();
        $parentcompanies = $company->get_parent_companies_recursive();

        // By default wherecondition retrieves all users except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['companyid'] = $this->companyid;
        $params['threadid'] = $this->threadid;

        // Deal with departments.
        $departmentlist = company::get_all_subdepartments($this->departmentid);
        $departmentsql = "";
        if (!empty($departmentlist)) {
            $departmentsql = " AND cu.departmentid IN (".implode(',', array_keys($departmentlist)).")";
        } else {
            $departmentsql = "";
        }

        // Deal with parent company managers
        if (!empty($parentcompanies)) {
            $userfilter = " AND u.id NOT IN (
                             SELECT userid FROM {company_users}
                             WHERE companyid IN (" . implode(',', array_keys($parentcompanies)) . "))";
        } else {
            $userfilter = "";
        }

        // No site admins.
        $userfilter .= " AND u.id NOT IN (" .$CFG->siteadmins .") ";

        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM {user} u INNER JOIN {company_users} cu ON cu.userid = u.id
                WHERE $wherecondition  AND u.suspended = 0 $departmentsql
                    AND
                    cu.companyid = :companyid
                    AND cu.managertype = 0
                    AND cu.userid not in ( ". $CFG->siteadmins .")
                    $userfilter
                    AND u.id NOT IN
                     (SELECT DISTINCT userid
                     FROM {microlearning_thread_user}
                     WHERE threadid=:threadid)";

        $order = ' ORDER BY u.lastname ASC, u.firstname ASC';

        if (!$this->is_validating() && !$all) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > $CFG->iomad_max_select_users) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('potentialcourseusersmatching', 'block_iomad_company_admin', $search);
        } else {
            $groupname = get_string('potentialcourseusers', 'block_iomad_company_admin');
        }

        return array($groupname => $availableusers);
    }
}

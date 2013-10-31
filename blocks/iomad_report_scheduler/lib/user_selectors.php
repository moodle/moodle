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
    const MAX_USERS_PER_PAGE = 100;

    protected $companyid;
    protected $courseid;

    public function __construct($name, $options) {
        $this->companyid  = $options['companyid'];
        if ( isset ( $options['courseid'] ) ) {
            $this->courseid = $options['courseid'];
        }
        parent::__construct($name, $options);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['companyid'] = $this->companyid;
        $options['file']    = 'blocks/iomad_company_admin/lib.php';
        return $options;
    }

    protected function get_course_user_ids() {
        global $DB, $PAGE;
        if ( !isset( $this->courseid ) ) {
            return array();
        } else {
            $course = $DB->get_record('course', array('id' => $this->courseid));
            $courseenrolmentmanager = new course_enrolment_manager($PAGE, $course);

            $users = $courseenrolmentmanager->get_users('lastname', $perpage = 0);

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
        global $DB;
        // By default wherecondition retrieves all users except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['companyid'] = $this->companyid;

        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM {user} u
                JOIN {companymanager} cm ON (u.id = cm.userid AND cm.companyid = :companyid)
                WHERE $wherecondition ";

        $order = ' ORDER BY u.lastname ASC, u.firstname ASC';

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > company_user_selector_base::MAX_USERS_PER_PAGE) {
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
        global $DB;
        // By default wherecondition retrieves all users except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['companyid'] = $this->companyid;
        $params['companyidforjoin'] = $this->companyid;
        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM
	                {user_info_field} muif
	                INNER JOIN {user_info_data} muid ON muif.id = muid.fieldid
                    INNER JOIN {user} u ON (muid.userid = u.id AND muif.shortname = 'company')
                    INNER JOIN {company} c ON (c.id = :companyidforjoin AND muid.data = c.shortname )
                WHERE $wherecondition
                      AND u.id NOT IN (SELECT cm.userid
                                         FROM {companymanager} cm
                                         WHERE cm.companyid = :companyid)";

        $order = ' ORDER BY u.lastname ASC, u.firstname ASC';

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > company_user_selector_base::MAX_USERS_PER_PAGE) {
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
        global $DB;
        // By default wherecondition retrieves all users except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['companyid'] = $this->companyid;

        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM
	                {user_info_field} muif
	                INNER JOIN {user_info_data} muid ON muif.id = muid.fieldid
                    INNER JOIN {user} u ON (muid.userid = u.id AND muif.shortname = 'company')
                    INNER JOIN {company} c ON (c.id = :companyid AND muid.data = c.shortname )
                WHERE $wherecondition ";

        $order = ' ORDER BY u.lastname ASC, u.firstname ASC';

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > company_user_selector_base::MAX_USERS_PER_PAGE) {
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
        global $DB;
        // By default wherecondition retrieves all users except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['companyid'] = $this->companyid;
        $params['companyidforjoin'] = $this->companyid;

        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM
                    {user} u
                WHERE $wherecondition
                      AND NOT EXISTS (
                        SELECT muid.userid
                        FROM
                            {user_info_data} muid
                            INNER JOIN {user_info_field} muif ON muif.id = muid.fieldid AND muif.shortname = 'company'
                        WHERE
                            muid.userid = u.id
                            AND
                            muid.data != ''
                      )";

        $order = ' ORDER BY u.lastname ASC, u.firstname ASC';

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > company_user_selector_base::MAX_USERS_PER_PAGE) {
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
    public function find_users($search) {
        global $DB;
        // By default wherecondition retrieves all users except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['companyid'] = $this->companyid;

        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        $enrolledusers = $this->get_course_user_ids();
        if ( count($enrolledusers) > 0 ) {
            $enrolledids = implode(',', $enrolledusers);
            $userfilter = " AND u.id in ($enrolledids) ";
        } else {
            $userfilter = " AND 1 = 0 ";
        }

        $sql = " FROM
	                {user_info_field} muif
	                INNER JOIN {user_info_data} muid ON muif.id = muid.fieldid
	                INNER JOIN {user} u ON muid.userid = u.id
	                INNER JOIN {company} c ON c.shortname = muid.data AND c.id = :companyid
                WHERE $wherecondition
                    AND
                    muif.shortname = 'company'
                    $userfilter";

        $order = ' ORDER BY u.lastname ASC, u.firstname ASC';

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > company_user_selector_base::MAX_USERS_PER_PAGE) {
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

class potential_company_course_user_selector extends company_user_selector_base {
    /**
     * Company users enrolled into the selected company course
     * @param <type> $search
     * @return array
     */
    public function find_users($search) {
        global $DB;
        // By default wherecondition retrieves all users except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['companyid'] = $this->companyid;

        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        $enrolledusers = $this->get_course_user_ids();
        if ( count($enrolledusers) > 0 ) {
            $enrolledids = implode(',', $enrolledusers);
            $userfilter = " AND NOT u.id in ($enrolledids) ";
        } else {
            $userfilter = "";
        }

        $sql = " FROM
	                {user_info_field} muif
	                INNER JOIN {user_info_data} muid ON muif.id = muid.fieldid
	                INNER JOIN {user} u ON muid.userid = u.id
	                INNER JOIN {company} c ON c.shortname = muid.data AND c.id = :companyid
                WHERE $wherecondition
                    AND
                    muif.shortname = 'company'
                    $userfilter";

        $order = ' ORDER BY u.lastname ASC, u.firstname ASC';

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > company_user_selector_base::MAX_USERS_PER_PAGE) {
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

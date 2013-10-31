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

require_once(dirname(__FILE__) . '/../../../local/iomad/lib/blockpage.php');
require_once(dirname(__FILE__) . '/../../../local/course_selector/lib.php');

/**
 * base class for selecting courses of a company 
 */
abstract class company_course_selector_base extends course_selector_base {
    const MAX_COURSES_PER_PAGE = 100;

    protected $companyid;

    public function __construct($name, $options) {
        $this->companyid  = $options['companyid'];
        parent::__construct($name, $options);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['companyid'] = $this->companyid;
        $options['file']    = 'blocks/iomad_company_admin/lib/course_selectors.php';
        return $options;
    }
}

class current_company_course_selector extends company_course_selector_base {
    /**
     * Company courses
     * @param <type> $search
     * @return array
     */
    public function find_courses($search) {
        global $DB, $CFG;
        // By default wherecondition retrieves all courses except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'c');
        $params['companyid'] = $this->companyid;

        $fields      = 'SELECT ' . $this->required_fields_sql('c');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM {course} c
                INNER JOIN {companycourse} cc ON (c.id = cc.courseid AND cc.companyid = :companyid)
                WHERE $wherecondition ";

        $order = ' ORDER BY c.sortorder, c.fullname ASC';

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > company_course_selector_base::MAX_COURSES_PER_PAGE) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availablecourses = $DB->get_records_sql($fields . $sql . $order, $params);

        // Check for global courses option is on and find them if so.
        $globalcoursesql = " FROM {course} c WHERE c.id !='1' AND c.id not in (SELECT cc.courseid from {companycourse} cc )
                             AND $wherecondition ";

        $globalcourses = $DB->get_records_sql($fields . $globalcoursesql . $order, $params);

        if (empty($availablecourses) && empty($globalcourses)) {
            return array();
        }

        // Set up empty return.
        $coursearray = array();
        if (!empty($availablecourses)) {
            if ($search) {
                $groupname = get_string('companycoursesmatching', 'block_iomad_company_admin', $search);
            } else {
                $groupname = get_string('companycourses', 'block_iomad_company_admin');
            }
            $coursearray[$groupname] = $availablecourses;
        }

        // Deal with global courses list if available.
        if (!empty($globalcourses)) {
            if ($search) {
                $groupname = get_string('globalcoursesmatching', 'block_iomad_company_admin', $search);
            } else {
                $groupname = get_string('globalcourses', 'block_iomad_company_admin');
            }
            $coursearray[$groupname] = $globalcourses;
        }

        return $coursearray;
    }
}


class potential_company_course_selector extends company_course_selector_base {
    /**
     * Potential company manager courses
     * @param <type> $search
     * @return array
     */
    public function find_courses($search) {
        global $DB, $SITE;
        // By default wherecondition retrieves all courses except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'c');
        $params['companyid'] = $this->companyid;
        $params['siteid'] = $SITE->id;

        $fields      = 'SELECT ' . $this->required_fields_sql('c');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM {course} c
                WHERE $wherecondition
                      AND c.id <> :siteid
                      AND NOT EXISTS ( SELECT NULL FROM {companycourse} WHERE courseid = c.id )";

        $order = ' ORDER BY c.sortorder, c.fullname ASC';

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > company_course_selector_base::MAX_COURSES_PER_PAGE) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availablecourses = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availablecourses)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('potcoursesmatching', 'block_iomad_company_admin', $search);
        } else {
            $groupname = get_string('potcourses', 'block_iomad_company_admin');
        }

        return array($groupname => $availablecourses);
    }
}

/**
 * Selector for any course
 */
class any_course_selector extends course_selector_base {
    /**
     * Any courses
     * @param <type> $search
     * @return array
     */
    public function find_courses($search) {
        global $DB;
        // By default wherecondition retrieves all courses except the deleted, not confirmed and guest.
        list($wherecondition, $params) = $this->search_sql($search, 'c');

        $fields      = 'SELECT ' . $this->required_fields_sql('c');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM {course} c
                WHERE $wherecondition";

        $order = ' ORDER BY c.sortorder, c.fullname ASC';

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > company_course_selector_base::MAX_COURSES_PER_PAGE) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availablecourses = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availablecourses)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('coursesmatching', 'block_iomad_company_admin', $search);
        } else {
            $groupname = get_string('courses', 'block_iomad_company_admin');
        }

        return array($groupname => $availablecourses);
    }
}


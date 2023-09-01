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
 * One Roster Enrolment Client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\entities;

use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\collections\classes as classes_collection;
use enrol_oneroster\local\collections\classes_for_school as classes_for_school_collection;

use enrol_oneroster\local\collections\courses as courses_collection;
use enrol_oneroster\local\collections\courses_for_school as courses_for_school_collection;

use enrol_oneroster\local\collections\terms as terms_collection;
use enrol_oneroster\local\collections\terms_for_school as terms_for_school_collection;

use enrol_oneroster\local\collections\users as users_collection;

use enrol_oneroster\local\collections\enrollments as enrollments_collection;

use enrol_oneroster\local\endpoints\rostering as rostering_endpoint;
use enrol_oneroster\local\filter;
use stdClass;

/**
 * One Roster School entity.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class school extends org {

    /**
     * Get the operation ID for the endpoint, otherwise known as the name of the endpoint.
     *
     * @param   container_interface $container
     * @return  string
     */
    protected static function get_operation_id(container_interface $container): string {
        return rostering_endpoint::getSchool;
    }

    /**
     * Fetch the list of courses relevant to this school.
     *
     * @param   array $params
     * @param   filter|null $filter
     * @param   callable $recordfilter
     * @return  courses_collection
     */
    public function get_courses(
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): courses_collection {
        return $this->container->get_collection_factory()->get_courses_for_school($this, $params, $filter, $recordfilter);
    }

    /**
     * Fetch the list of academic term sessions for this school.
     *
     * @param   array $params
     * @param   filter|null $filter
     * @param   callable $recordfilter
     * @return  terms_collection
     */
    public function get_terms(
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): terms_collection {
        return $this->container->get_collection_factory()->get_terms_for_school($this, $params, $filter, $recordfilter);
    }

    /**
     * Fetch the list of classes for this school.
     *
     * @param   array $params
     * @param   filter|null $filter
     * @param   callable $recordfilter
     * @return  classes_collection
     */
    public function get_classes(
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): classes_collection {
        return $this->container->get_collection_factory()->get_classes_for_school($this, $params, $filter, $recordfilter);
    }

    /**
     * Fetch all users in the School.
     *
     * Note: Some endpoints do not filter or Array properties correctly so this endpoint cannot necessarily be relied
     * upon to return data only for the current school.
     *
     * @param   array $params
     * @param   filter|null $filter
     * @param   callable $recordfilter
     * @return  users_collection
     */
    public function get_users(
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): users_collection {
        // Note: This list of users must be re-filtered because some endpoints.
        return $this->container->get_collection_factory()->get_users_for_school($this, $params, $filter, $recordfilter);
    }

    /**
     * Fetch all enrollments in the School.
     *
     * @param   array $params
     * @param   filter|null $filter
     * @param   callable $recordfilter
     * @return  enrollments_collection
     */
    public function get_enrollments(
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null

    ): enrollments_collection {
        return $this->container->get_collection_factory()->get_enrollments_for_school($this, $params, $filter, $recordfilter);
    }
}

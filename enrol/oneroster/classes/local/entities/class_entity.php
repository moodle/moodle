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

use coding_exception;
use enrol_oneroster\local\converter;
use enrol_oneroster\local\collections\enrollments as enrollments_collection;
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\interfaces\course_representation;
use enrol_oneroster\local\interfaces\user_representation;
use enrol_oneroster\local\interfaces\coursecat_representation;
use enrol_oneroster\local\entity;
use enrol_oneroster\local\filter;
use enrol_oneroster\local\endpoints\rostering as rostering_endpoint;
use stdClass;

/**
 * One Roster Class entity.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class class_entity extends entity implements course_representation {

    /**
     * Get the operation ID for the endpoint, otherwise known as the name of the endpoint.
     *
     * @param   container_interface $container
     * @return  string
     */
    protected static function get_operation_id(container_interface $container): string {
        return static::get_generic_operation_id($container);
    }

    /**
     * Get the operation ID for the endpoint which returns the generic representation of this type.
     *
     * For example a school is a subtype of the organisation object. You can fetch a school from the organisatino
     * endpoint, but you cannot fetch an organisation from the school endpoint.
     *
     * @param   container_interface $container
     * @return  string
     */
    protected static function get_generic_operation_id(container_interface $container): string {
        return rostering_endpoint::getClass;
    }

    /**
     * Parse the data returned from the One Roster Endpoint.
     *
     * @param   container_interface $container The container for this client
     * @param   stdClass $data The raw data returned from the endpoint
     * @return  stdClass The parsed data
     */
    protected static function parse_returned_row(container_interface $container, stdClass $data): stdClass {
        if (!property_exists($data, 'class')) {
            throw new coding_exception("The returned data is missing the 'class' property");
        }
        return $data->class;
    }

    /**
     * Get the course that this class belongs to.
     *
     * @return  course The owning class
     */
    public function get_course(): ?course {
        // Fetch the course details.
        $courseref = $this->get('course');

        // The parentref is a guidref and should contain both the sourcedId and the type.
        // It also contains an href, but this is not reliable and cannot be used.
        return $this->container->get_entity_factory()->fetch_course_by_id($courseref->sourcedId);
    }

    /**
     * Get the terms that this class is in.
     *
     * @return  Iterable The list of terms for this class.
     */
    public function get_terms(): Iterable {
        // Note: There is no endpoint to return the list of terms for a class.
        foreach ($this->get('terms') as $termref) {
            yield $this->container->get_entity_factory()->fetch_academic_session_by_id($termref->sourcedId);
        }
    }

    /**
     * Get the data which represents this One Roster Object as a Moodle course.
     *
     * @return  stdClass
     */
    public function get_course_data(): stdClass {
        $coursedata = (object) [
            'idnumber' => $this->get('sourcedId'),
            'fullname' => $this->get('title'),

            // Note: The courseCode is not guaranteed to be unique.
            // This may need to be adjusted accordingly, or using an admin setting.
            'shortname' => $this->get('sourcedId'),

            // Valid states are 'active' or 'tobedeleted'.
            'visible' => ($this->get('status') === 'active'),
        ];

        $startdate = null;
        $enddate = null;
        foreach ($this->get_terms() as $term) {
            $utsstart = converter::from_date_to_unix($term->get('startDate'));
            if ($startdate) {
                $startdate = min($startdate, $utsstart);
            } else {
                $startdate = $utsstart;
            }

            $utsend = converter::from_date_to_unix($term->get('endDate'));
            if ($enddate) {
                $enddate = max($enddate, $utsend);
            } else {
                $enddate = $utsend;
            }
        }

        if ($startdate) {
            $coursedata->startdate = $startdate;
        }

        if ($enddate) {
            $coursedata->enddate = $enddate;
        }

        return $coursedata;
    }

    /**
     * Get the course shortname for this course representation.
     *
     * @return  string
     */
    protected function get_course_shortname(): string {
        return $this->get_container()->get_service()->get_short_name_for_course_representation($this);
    }

    /**
     * Fetch the parent organsiation that this class is in.
     * In Moodle speak, this is a course category.
     *
     * @return coursecat_representation
     */
    public function get_course_category(): coursecat_representation {
        // A class belongs to a school.
        // Fetch the course details.
        $guidref = $this->get('school');

        // The guidref and should contain both the sourcedId and the type.
        // It also contains an href, but this is not reliable and cannot be used.
        return $this->container->get_entity_factory()->fetch_org_by_id($guidref->sourcedId);
    }

    /**
     * Fetch all enrollments in the Class.
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
        if ($filter === null) {
            $filter = $this->container->get_filter_instance();
        }

        $filter->add_filter('class', $this->get('sourcedId'));
        return $this->container->get_collection_factory()->get_enrollments($params, $filter, $recordfilter);
    }
}

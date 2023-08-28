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
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\interfaces\course_representation;
use enrol_oneroster\local\interfaces\enrollment_representation;
use enrol_oneroster\local\entity;
use enrol_oneroster\local\endpoints\rostering as rostering_endpoint;
use stdClass;

/**
 * One Roster enrollment entity.
 *
 * Note: The use of incorrect spelling (enrolment vs. enrollment) is deliberate to reflect the One Roster
 * implementation.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrollment extends entity implements enrollment_representation {

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
        return rostering_endpoint::getEnrollment;
    }

    /**
     * Parse the data returned from the One Roster Endpoint.
     *
     * @param   container_interface $container The container for this client
     * @param   stdClass $data The raw data returned from the endpoint
     * @return  stdClass The parsed data
     */
    protected static function parse_returned_row(container_interface $container, stdClass $data): stdClass {
        if (!property_exists($data, 'enrollment')) {
            throw new coding_exception("The returned data is missing the 'enrollment' property");
        }
        return $data->enrollment;
    }

    /**
     * Get the user that this enrollment relates to.
     *
     * @return  user The user that the enrollment relates to.
     */
    public function get_user_entity(): ?user {
        // Fetch the user details.
        $userref = $this->get('user');

        // The parentref is a guidref and should contain both the sourcedId and the type.
        // It also contains an href, but this is not reliable and cannot be used.
        return $this->container->get_entity_factory()->fetch_user_by_id($userref->sourcedId);
    }

    /**
     * Get the class that this class belongs to.
     *
     * @return  class_entity The owning class
     */
    public function get_class_entity(): ?class_entity {
        // Fetch the class details.
        $classref = $this->get('class');

        // The parentref is a guidref and should contain both the sourcedId and the type.
        // It also contains an href, but this is not reliable and cannot be used.
        return $this->container->get_entity_factory()->fetch_class_by_id($classref->sourcedId);
    }

    /**
     * Get the data which represents this One Roster Object as a Moodle User.
     *
     * @return  stdClass
     */
    public function get_enrolment_data(): stdClass {
        $data = (object) [
            'timestart' => 0,
            'timeend' => 0,
        ];

        if ($timestart = $this->get('beginDate')) {
            $data->timestart = converter::from_date_to_unix($timestart);
        }

        if ($timeend = $this->get('endDate')) {
            $data->timeend = converter::from_date_to_unix($timeend);
        }

        if ($this->get('status') === 'active') {
            $data->status = ENROL_USER_ACTIVE;
        } else {
            $data->status = ENROL_USER_SUSPENDED;
        }

        return $data;
    }

    /**
     * Get the data relating to the One Roster Role.
     *
     * Note: This uses the One Roster role representation.
     * It must be translated by the client per user-defined mappings.
     *
     * @return  stdClass
     */
    public function get_role_data(): stdClass {
        return (object) [
            'role' => $this->get('role'),
        ];
    }

    /**
     * Get the representation of a Moodle course that this enrollment is in.
     *
     * @return  course_representation
     */
    public function get_course_representation(): course_representation {
        // A user is enrolled in a class, which we use as a representation of a Moodle course.
        return $this->get_class_entity();
    }
}

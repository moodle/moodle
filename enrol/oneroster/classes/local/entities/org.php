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
use enrol_oneroster\local\filter;
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\interfaces\coursecat_representation;
use enrol_oneroster\local\endpoints\rostering as rostering_endpoint;
use enrol_oneroster\local\entity;
use stdClass;
use OutOfRangeException;

/**
 * One Roster Organisation entity.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class org extends entity implements coursecat_representation {

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
        return rostering_endpoint::getOrg;
    }

    /**
     * Parse the data returned from the One Roster Endpoint.
     *
     * @param   container_interface $container The container for this client
     * @param   stdClass $data The raw data returned from the endpoint
     * @return  stdClass The parsed data
     */
    protected static function parse_returned_row(container_interface $container, stdClass $data): stdClass {
        // Some invalid properties exist, for example in the Aeries implementation.
        $properties = [
            'org',
            'school',
        ];

        foreach ($properties as $property) {
            if (property_exists($data, $property)) {
                return $data->{$property};
            }
        }
        throw new OutOfRangeException("The returned data is missing the 'org' property");
    }

    /**
     * Get the data which represents this One Roster Object as a Moodle course category.
     *
     * @return  stdClass
     */
    public function get_course_category_data(): stdClass {
        return (object) [
            'idnumber' => $this->get('sourcedId'),
            'name' => $this->get('name'),
            'visible' => ($this->get('status') === 'active'),
        ];
    }

    /**
     * Get the parent entity.
     *
     * @return  org|null An Organisation of the relevant type if a parent was found, otherwise null.
     */
    public function get_parent(): ?org {
        // Fetch the parent if there is one.
        $parentref = $this->get('parent');

        if ($parentref === null) {
            // No parent to return.
            return null;
        }

        if (!property_exists($parentref, 'sourcedId')) {
            // Parent Ref not correctly filled.
            return null;
        }

        // The parentref is a guidref and should contain both the sourcedId and the type.
        // It also contains an href, but this is not reliable and cannot be used.
        return $this->container->get_entity_factory()->fetch_org_by_id($parentref->sourcedId);
    }
}

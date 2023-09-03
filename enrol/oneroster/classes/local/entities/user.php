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
use enrol_oneroster\local\endpoints\rostering as rostering_endpoint;
use enrol_oneroster\local\entity;
use enrol_oneroster\local\interfaces\user_representation;
use enrol_oneroster\local\interfaces\container as container_interface;
use stdClass;

/**
 * One Roster Course entity.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user extends entity implements user_representation {

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
        return rostering_endpoint::getUser;
    }

    /**
     * Parse the data returned from the One Roster Endpoint.
     *
     * @param   container_interface $container The container for this client
     * @param   stdClass $data The raw data returned from the endpoint
     * @return  stdClass The parsed data
     */
    protected static function parse_returned_row(container_interface $container, stdClass $data): stdClass {
        if (!property_exists($data, 'user')) {
            throw new coding_exception("The returned data is missing the 'user' property");
        }
        return $data->user;
    }

    /**
     * Get the user that this class belongs to.
     *
     * @return  org The owner organisation
     */
    public function get_orgs(): org {
        // Fetch the user details.
        $guidref = $this->get('org');

        // The guidref and should contain both the sourcedId and the type.
        // It also contains an href, but this is not reliable and cannot be used.
        return $this->container->get_collection_factory()->get_orgs($guidref->sourcedId);
    }

    /**
     * Get the data which represents this One Roster Object as a Moodle User.
     *
     * @return  stdClass
     */
    public function get_user_data(): stdClass {
        return (object) [
            'idnumber' => $this->get('sourcedId'),
            'status' => $this->get('status'),
            'username' => strtolower($this->get('identifier')),
            //'username' => $this->get('identifier'),
            'email' => $this->get('email'),
            'password' => $this->get('password') ?? '',
            'firstname' => $this->get('givenName'),
            'lastname' => $this->get('familyName'),
        ];
    }

    /**
     * Get the list of agents that this user has.
     *
     * Typically this is from the perspective of the student, but it must map both ways.
     *
     * @return  Iterable The list of agents for this user.
     */
    public function get_agent_entities(): Iterable {
        foreach ($this->get('agents') as $userref) {
            // The parentref is a guidref and should contain both the sourcedId and the type.
            // It also contains an href, but this is not reliable and cannot be used.
            yield $this->container->get_entity_factory()->fetch_user_by_id($userref->sourcedId);
        }
    }
}

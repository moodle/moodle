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

namespace enrol_oneroster\local\collections;

use enrol_oneroster\local\entity;
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\collection;
use enrol_oneroster\local\endpoints\rostering as rostering_endpoint;
use stdClass;

/**
 * One Roster academicSessions collection.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class academic_sessions extends collection {

    /**
     * Get the operation ID for the endpoint, otherwise known as the name of the endpoint.
     *
     * @param   container_interface $container The container to which this entity belongs
     * @return  string
     */
    protected static function get_operation_id(container_interface $container): string {
        return rostering_endpoint::getAllAcademicSessions;
    }

    /**
     * Parse the data returned from the One Roster Endpoint.
     *
     * @param   container_interface $container
     * @param   stdClass $data The raw data returned from the endpoint
     * @return  array The parsed data
     */
    protected static function parse_returned_row(container_interface $container, stdClass $data): entity {
        return $container->get_entity_factory()->get_academic_session_from_result($data);
    }
}

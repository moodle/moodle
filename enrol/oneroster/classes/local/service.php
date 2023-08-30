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

namespace enrol_oneroster\local;

use enrol_oneroster\local\interfaces\course_representation as course_representation_interface;
use enrol_oneroster\local\interfaces\container as container_interface;

/**
 * One Roster Service definition.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class service {

    /** @var container_interface The container that this service relates to */
    protected $container = null;

    /** @var array A list of optional endpoints */
    protected static $optionalendpoints = [];

    /**
     * Constructor for the service.
     *
     * @param   container_interface $container The container that this service relates to
     */
    public function __construct(container_interface $container) {
        $this->container = $container;
    }

    /**
     * Check whether this service this optional endpoint.
     *
     * An endpoint is assumed to be supported unless otherwise specified.
     *
     * @param   string $endpoint
     * @return  bool
     */
    public static function supports_endpoint(string $endpoint): bool {
        if (array_key_exists($endpoint, static::$optionalendpoints)) {
            return static::$optionalendpoints[$endpoint];
        }

        return true;
    }

    /**
     * Get the course shortname for the specified course representation.
     *
     * @param   course_representation_interface $entity
     * @return  string
     */
    public function get_short_name_for_course_representation(course_representation_interface $entity): string {
        return $entity->get('sourcedId');
    }
}

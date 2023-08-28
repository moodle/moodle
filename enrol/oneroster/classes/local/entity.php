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

use coding_exception;
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\interfaces\entity_factory as entity_factory_interface;
use enrol_oneroster\local\interfaces\filter as filter_interface;
use stdClass;

/**
 * One Roster Entity.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class entity {

    /** @var container_interface The container for the One Roster version */
    protected $container;

    /** @var string The sourcedId of the entity */
    protected $id;

    /** @var stdClass The retrieved data */
    protected $data;

    /**
     * Create a new instance of an entity.
     *
     * @param   container_interface $container The containre for this OR version
     * @param   string $id The sourcedId of the entity
     * @param   stdClass|null $data The data to pre-seed the entity with if it is already known
     */
    public function __construct(container_interface $container, string $id, ?stdClass $data = null) {
        $this->container = $container;
        $this->id = $id;

        if ($data !== null) {
            $this->data = $data;
        }
    }

    /**
     * Return the data for this entity, fetching it if it has not yet been retrieved.
     *
     * @return  stdClass The data for this entity
     */
    public function get_data(): stdClass {
        if ($this->data === null) {
            $this->refresh_data();
        }

        return $this->data;
    }

    /**
     * Refresh the data.
     *
     * @return  stdClass The data for this entity
     */
    public function refresh_data(): stdClass {
        $this->data = static::fetch_data(
            $this->container,
            [
                ':id' => $this->id,
            ]
        );

        return $this->data;
    }

    /**
     * Fetch the data.
     *
     * @param   container_interface $container
     * @param   array $params The search criterion
     * @param   filter_interface|null $filter Any additional filter to provide
     * @return  stdClass
     */
    public static function fetch_data(container_interface $container, array $params, ?filter_interface $filter = null): stdClass {
        $data = $container->get_rostering_endpoint()
            ->execute(static::get_operation_id($container), $filter, $params);

        return static::parse_returned_row($container, $data);
    }

    /**
     * Fetch an arbitrary value from the entity.
     *
     * @param   string $name The name of the value to fetch
     * @return  mixed The value at that point.
     */
    public function get(string $name) {
        $data = $this->get_data();
        if (property_exists($data, $name)) {
            return $data->{$name};
        }

        return null;
    }

    /**
     * Get the operation ID for the endpoint, otherwise known as the name of the endpoint.
     *
     * @param   container_interface $container
     * @return  string
     */
    abstract protected static function get_operation_id(container_interface $container): string;

    /**
     * Get the operation ID for the endpoint which returns the generic representation of this type.
     *
     * For example a school is a subtype of the organisation object. You can fetch a school from the organisation
     * endpoint, but you cannot fetch an organisation from the school endpoint.
     *
     * @param   container_interface $container
     * @return  string
     */
    abstract protected static function get_generic_operation_id(container_interface $container): string;

    /**
     * Parse the data returned from the One Roster Endpoint.
     *
     * @param   container_interface $container
     * @param   stdClass $data The raw data returned from the endpoint
     * @return  stdClass The parsed data
     */
    abstract protected static function parse_returned_row(container_interface $container, stdClass $data): stdClass;
}

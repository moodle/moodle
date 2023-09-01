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

use IteratorAggregate;
use Traversable;
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\interfaces\entity_factory as entity_factory_interface;
use enrol_oneroster\local\interfaces\filter as filter_interface;
use stdClass;

/**
 * One Roster v1p1 Collection.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class collection implements IteratorAggregate {

    /** @var container_interface The container to which this entity belongs */
    protected $container;

    /** @var array The params to use with this endpoint */
    protected $params = [];

    /** @var stdClass The retrieved data */
    protected $data;

    /** @var filter_interface|null The filter to be applied to the collection */
    protected $filter = null;

    /** @var callable A record filter to be applied during the fetch */
    protected $recordfilter = null;

    /**
     * Create a new instance of an entity.
     *
     * @param   container_interface $container The container to which this entity belongs
     * @param   array $params All of the parameters, including those required as filter args
     * @param   null|filter $filter A filter to apply to the Endpoint call
     * @param   callable $recordfilter A filter to apply after retrievin reuslts
     */
    public function __construct(
        container_interface $container,
        array $params = [],
        ?filter $filter = null,
        callable $recordfilter = null
    ) {
        $this->container = $container;

        $this->filter = $this->process_filter($filter);

        // Process the parameters.
        // Some web services may change the arguments depending on the web services available.
        $this->params = $this->process_params($params);

        $this->recordfilter = $recordfilter;
    }

    /**
     * Fetch the data in this collection.
     *
     * @return  Traversable
     */
    public function getIterator(): Traversable { // @codingStandardsIgnoreLine
        return $this->get_data();
    }

    /**
     * Process the current filter, creating an empty filter if none was specified.
     *
     * @param   filter_interface|null $filter
     * @return  filter_interface
     */
    protected function process_filter(?filter_interface $filter): filter_interface {
        if ($filter) {
            return $filter;
        }

        return $this->container->get_filter_instance();
    }

    /**
     * Process the supplied parameters and modify them as required.
     *
     * @param   array $params
     * @return  array
     */
    protected function process_params(array $params): array {
        return $params;
    }

    /**
     * Return the data for this entity, fetching it if it has not yet been retrieved.
     *
     * @return  array The data for this entity
     */
    public function get_data(): Iterable {
        if ($this->data === null) {
            $this->refresh_data();
        }

        return $this->data;
    }

    /**
     * Refresh the data.
     *
     * @return  array The data for this entity
     */
    public function refresh_data(): Iterable {
        $this->data = $this->container->get_rostering_endpoint()->execute_paginated_function(
            static::get_operation_id($this->container),
            $this->get_filter(),
            $this->get_params(),
            function($data) {
                $data = static::parse_returned_row($this->container, $data);
                if ($this->recordfilter && !call_user_func($this->recordfilter, $data)) {
                    return null;
                }

                return $data;
            }
        );

        return $this->data;
    }

    /**
     * Get the filter object to use when fetching this collection.
     *
     * @return  filter_interface
     */
    protected function get_filter(): ?filter_interface {
        return $this->filter;
    }

    /**
     * Get the parameteres used to fetch this collection.
     *
     * @return  array
     */
    protected function get_params(): array {
        return $this->params;
    }

    /**
     * Get the operation ID for the endpoint, otherwise known as the name of the endpoint.
     *
     * @param   container_interface $container
     * @return  string
     */
    abstract protected static function get_operation_id(container_interface $container): string;

    /**
     * Parse the data returned from the One Roster Endpoint.
     *
     * @param   container_interface $container
     * @param   stdClass $data The raw data returned from the endpoint
     * @return  stdClass The parsed data
     */
    abstract static protected function parse_returned_row(container_interface $container, stdClass $data): entity;
}

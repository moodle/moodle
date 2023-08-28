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

use enrol_oneroster\local\interfaces\cache_factory as cache_factory_interface;
use enrol_oneroster\local\interfaces\client as client_interface;
use enrol_oneroster\local\interfaces\collection_factory as collection_factory_interface;
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\interfaces\entity_factory as entity_factory_interface;
use enrol_oneroster\local\interfaces\filter as filter_interface;
use enrol_oneroster\local\interfaces\rostering_endpoint as rostering_endpoint_interface;

/**
 * One Roster 1.1 Factory Manager.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class container implements container_interface {

    /** @var client_interface The client instance */
    protected $client = null;

    /** @var service $service */
    protected $service = null;

    /**
     * Constructor for the new container.
     *
     * @param   client_interface $client
     */
    public function __construct(client_interface $client) {
        $this->client = $client;
    }

    /**
     * Get the client instance.
     *
     * @return  root_or_client
     */
    public function get_client(): client_interface {
        return $this->client;
    }

    /**
     * Set the service, if known.
     *
     * @param   service $service
     */
    public function set_service(service $service): container_interface {
        $this->service = $service;

        return $this;
    }

    /**
     * Get the service.
     *
     * @return  service
     */
    public function get_service(): service {
        if ($this->service === null) {
            $this->service = new service($this);
        }

        return $this->service;
    }

    /**
     * Check whether the service supports the specified endpoint.
     *
     * @param   string $endpoint
     * @return  bool
     */
    public function supports(string $endpoint): bool {
        return $this->get_service()->supports_endpoint($endpoint);
    }

    /**
     * Get the Rostering endpoint in use.
     *
     * @return  rostering_endpoint_interface
     */
    abstract public function get_rostering_endpoint(): rostering_endpoint_interface;

    /**
     * Get the Entity Factory.
     *
     * @return  entity_factory_interface
     */
    abstract public function get_entity_factory(): entity_factory_interface;

    /**
     * Get the Collection Factory.
     *
     * @return  collection_factory_interface
     */
    abstract public function get_collection_factory(): collection_factory_interface;

    /**
     * Get the Cache Factory.
     *
     * @return  cache_factory_interface
     */
    abstract public function get_cache_factory(): cache_factory_interface;

    /**
     * Get an instance of a filter.
     *
     * @return  filter_interface
     */
    abstract public function get_filter_instance(): filter_interface;
}

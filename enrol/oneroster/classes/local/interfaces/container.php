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

namespace enrol_oneroster\local\interfaces;

use enrol_oneroster\local\interfaces\client as client_interface;
use enrol_oneroster\local\service;

/**
 * One Roster Entity Factory.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface container {

    /**
     * Constructor for the new container.
     *
     * @param   client_interface $client
     */
    public function __construct(client_interface $client);

    /**
     * Get the client instance.
     *
     * @return  root_or_client
     */
    public function get_client(): client_interface;

    /**
     * Get the Rostering endpoint in use.
     *
     * @return  rostering_endpoint_interface
     */
    public function get_rostering_endpoint(): rostering_endpoint;

    /**
     * Get the Entity Factory.
     *
     * @return  entity_factory
     */
    public function get_entity_factory(): entity_factory;

    /**
     * Get the Collection Factory.
     *
     * @return  collection_factory
     */
    public function get_collection_factory(): collection_factory;

    /**
     * Get the Cache Factory.
     *
     * @return  cache_factory
     */
    public function get_cache_factory(): cache_factory;

    /**
     * Set the service, if known.
     *
     * @param   service $service
     */
    public function set_service(service $service): container;

    /**
     * Get the service.
     *
     * @return  service
     */
    public function get_service(): service;

    /**
     * Check whether the service supports the specified endpoint.
     *
     * @param   string $endpoint
     * @return  bool
     */
    public function supports(string $endpoint): bool;
}

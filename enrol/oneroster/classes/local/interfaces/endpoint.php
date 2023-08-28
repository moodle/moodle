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

use BadMethodCallException;
use coding_exception;
use enrol_oneroster\local\command;
use enrol_oneroster\local\interfaces\client as client_interface;
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\interfaces\filter as filter_interface;
use stdClass;

/**
 * One Roster Endpoint.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface endpoint {

    /**
     * Create the endpoint.
     *
     * @param   container_interface $container
     */
    public function __construct(container_interface $container);

    /**
     * Execeute the supplied method.
     *
     * @param   string $method
     * @param   null|filter_interface $filter
     * @param   array $params
     */
    public function execute(string $method, ?filter_interface $filter = null, array $params = []);

    /**
     * Execeute the supplied command.
     *
     * @param   command $command
     * @param   null|filter_interface $filter
     */
    public function execute_command(command $command, ?filter_interface $filter = null);

    /**
     * Exeucte a function which returns a collection.
     *
     * @param   string $method HTTP Method
     * @param   filter_interface $filter
     * @param   array $params
     * @param   callable $callback
     * @return  Iterable
     */
    public function execute_paginated_function(
        string $method,
        filter_interface $filter = null,
        array $params = [],
        callable $callback
    ): Iterable;
}

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

use BadMethodCallException;
use coding_exception;
use enrol_oneroster\local\command;
use enrol_oneroster\local\interfaces\client as client_interface;
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\interfaces\endpoint as endpoint_interface;
use enrol_oneroster\local\interfaces\filter as filter_interface;
use stdClass;

/**
 * One Roster Endpoint.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class endpoint implements endpoint_interface {

    /** @var container_interface The container containing the client, service, and all factories */
    protected $container;

    /** @var array List of commands and their configuration */
    protected static $commands = [];

    /**
     * Constructor for the endpoint.
     *
     * @param   container_interface $container
     */
    final public function __construct(container_interface $container) {
        $this->container = $container;
    }

    /**
     * Get the client related to this endpoint.
     *
     * @return  client_interface
     */
    final protected function get_client(): client_interface {
        return $this->container->get_client();
    }

    /**
     * Execeute the supplied method.
     *
     * @param   string $method
     * @param   null|filter_interface $filter
     * @param   array $params
     * @return  mixed
     */
    public function execute(string $method, ?filter_interface $filter = null, array $params = []) {
        return $this->get_client()->execute($this->get_http_method($method, $params), $filter)->response;
    }

    /**
     * Execeute the supplied command.
     *
     * @param   command $command
     * @param   null|filter_interface $filter
     * @return  mixed
     */
    public function execute_command(command $command, ?filter_interface $filter = null) {
        return $this->get_client()->execute($command, $filter)->response;
    }

    /**
     * Exeucte a function which returns a collection.
     *
     * @param   string $method
     * @param   filter_interface $filter
     * @param   array $params
     * @param   callable $callback
     */
    public function execute_paginated_function(
        string $method,
        filter_interface $filter = null,
        array $params = [],
        callable $callback
    ): Iterable {
        if (!array_key_exists('offset', $params)) {
            $params['offset'] = 0;
        }

        if (!array_key_exists('limit', $params)) {
            $params['limit'] = get_config('enrol_oneroster', 'pagesize');
        }

        $command = $this->get_http_method($method, $params);
        $command->require_collection();

        do {
            $result = $this->get_client()->execute($command, $filter);
            $response = $result->response;

            $collection = null;
            foreach ($command->get_collection_names() as $collectionname) {
                if (property_exists($response, $collectionname)) {
                    $collection = $response->{$collectionname};
                    break;
                }
            }

            if ($collection === null) {
                throw new coding_exception("Unable to find any collection in the response");
            }

            foreach ($collection as $item) {
                if ($result = $callback($item)) {
                    yield $result;
                }
            }

            $params['offset'] += $params['limit'];

            $morepages = false;
            if (count($collection) >= $params['limit']) {
                $morepages = true;
                $command = $this->get_http_method($method, $params);
            }

        } while ($morepages);
    }

    /**
     * Get the HTTP Method details for the specified method.
     *
     * @param string $method The name of the OneRoster endpoint
     * @param array $params The param to apply
     * @return stdClass The endpoint details, including the URL, and method.
     */
    protected function get_http_method(string $method, array $params): command {
        $command = static::get_command_data($method);

        return new command(
            $this,
            $command['url'],
            $command['method'],
            $command['description'],
            isset($command['collection']) ? $command['collection'] : null,
            isset($command['defaultsort']) ? $command['defaultsort'] : null,
            isset($command['defaultsortorder']) ? $command['defaultsortorder'] : null,
            $params
        );
    }

    /**
     * Get the command data for the specified command.
     *
     * @param   string $command
     * @return  array
     */
    protected static function get_command_data(string $command): ?array {
        if (array_key_exists($command, self::$commands)) {
            return self::$commands[$command];
        }

        throw new BadMethodCallException("Unknown method call '{$command}'");
    }

    /**
     * Get the list of all commands.
     *
     * @return  array
     */
    public static function get_all_commands(): array {
        return self::$commands;
    }
}

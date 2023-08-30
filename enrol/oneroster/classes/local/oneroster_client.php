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
 * One Roster Client.
 *
 * This plugin synchronises enrolment and roles with a One Roster endpoint.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local;

use BadMethodCallException;
use enrol_oneroster\client_helper;
use enrol_oneroster\local\command;
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\interfaces\filter;
use enrol_oneroster\local\interfaces\rostering_client;
use enrol_oneroster\local\interfaces\rostering_endpoint;
use enrol_oneroster\plugin as enrol_oneroster_plugin;
use moodle_exception;
use moodle_url;
use null_progress_trace;
use progress_trace;
use stdClass;

/**
 * One Roster Client.
 *
 * Note: This is implemented as a Trait to allow support of both OAuth1, and OAuth2, which are both implemented in
 * upstream Moodle by use of abstract classes.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait oneroster_client {
    /** @var progress_trace The logging system */
    protected $trace;

    /** @var container_interface The container used for fetching factories */
    protected $container;

    /** @var plugin An instance of the enrol_oneroster_plugin enrolment plugin */
    protected $instance;

    /**
     * Get the enrol_oneroster_plugin instance.
     *
     * @return  enrol_oneroster_plugin
     */
    protected function get_plugin_instance(): enrol_oneroster_plugin {
        if ($this->instance === null) {
            $this->instance = new enrol_oneroster_plugin();
        }

        return $this->instance;
    }

    /**
     * Set the log tracer.
     *
     * @param progress_trace $trace
     */
    public function set_trace(progress_trace $trace): void {
        $this->trace = $trace;
    }

    /**
     * Get the log tracer.
     *
     * @return progress_trace
     */
    public function get_trace(): progress_trace {
        if ($this->trace === null) {
            return new null_progress_trace();
        }

        return $this->trace;
    }

    /**
     * Execute the supplied command.
     *
     * @param   command $command The command to execute
     * @param   filter $filter
     * @return  stdClass
     */
    public function execute(command $command, filter $filter = null): stdClass {
        $url = new moodle_url($command->get_url($this->baseurl));
        $params = $command->get_params();
        $method = $command->get_method();
        $sort = $command->get_sort();

        // Add pagination.
        if (array_key_exists('limit', $params)) {
            $url->param('limit', $params['limit']);
            unset($params['limit']);
        }

        if (array_key_exists('offset', $params)) {
            $url->param('offset', $params['offset']);
            unset($params['offset']);
        }

        // Sorting.
        if ($sort) {
            $url->param('sort', $sort);
            if ($sortorder = $command->get_sort_order()) {
                $url->param('orderBy', $sortorder);
            }
        }

        // Filtering.
        if ($filter && !empty((string) $filter)) {
            $url->param('filter', (string) $filter);
        }

        // Fields.
        if (array_key_exists('fields', $params)) {
            $url->param('fields', (string) $params['fields']);
            unset($params['fields']);
        }

        if (!empty($params) && $method !== client_helper::POST) {
            throw new BadMethodCallException(sprintf(
                'The http method called for %s is %s but it has to be POST' .
                ' if you want to pass the JSON params %s',
                $url,
                $method,
                json_encode($params)
            ));
        }

        $options = [
            'CURLOPT_CONNECTTIMEOUT' => 300,
        ];

        if ($method === client_helper::POST) {
            $result = $this->post($url->out(false), $params, $options);
        } else if ($method === client_helper::GET) {
            $result = $this->get($url->out(false), $params, $options);
        }

        $info = $this->get_request_info();
        \enrol_oneroster\local\exceptions\exception::check_and_throw_from_http_response($result, $info, $url, $params);

        $response = json_decode($result);

        if (is_null($response)) {
            throw new moodle_exception("Could not decode JSON token response: " . $result);
        }

        if (!empty($response->error)) {
            throw new moodle_exception($response->error . ' ' . $response->error_description);
        }

        return (object) [
            'info' => $info,
            'response' => $response,
        ];
    }

    /**
     * Get the entity factory for this One Roster implementation.
     *
     * @return  container_interface
     */
    abstract public function get_container(): container_interface;

    /**
     * Perform all availilable synchronisations.
     *
     * @param   int $onlysincetime
     */
    public function synchronise(?int $onlysincetime = null): void {
        if (class_implements($this, rostering_client::class)) {
            $this->sync_roster($onlysincetime);
        }
    }

    /**
     * Authenticate against the One Roster endpoint as required.
     */
    abstract public function authenticate(): void;
}

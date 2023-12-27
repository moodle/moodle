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

namespace communication_matrix\local;

use communication_matrix\matrix_client;
use GuzzleHttp\Psr7\Request;
use OutOfRangeException;

/**
 * A command to be sent to the Matrix server.
 *
 * This class is a wrapper around the PSR-7 Request Interface implementation provided by Guzzle.
 *
 * It takes a set of common parameters and configurations and turns them into a Request that can be called against a live server.
 *
 * @package    communication_matrix
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class command extends Request {
    /** @var array $command The raw command data */
    /** @var array|null $params The parameters passed into the command */
    /** @var bool $sendasjson Whether to send params as JSON */
    /** @var bool $requireauthorization Whether authorization is required for this request */
    /** @var bool $ignorehttperrors Whether to ignore HTTP Errors */
    /** @var array $query Any query parameters to set on the URL */

    /** @var array|null Any parameters not used in the URI which are to be passed to the server via body or query params */
    protected array $remainingparams = [];

    /**
     * Create a new Command.
     *
     * @param matrix_client $client The URL for this method
     * @param string $method (GET|POST|PUT|DELETE)
     * @param string $endpoint The URL
     * @param array $params Any parameters to pass
     * @param array $query Any query parameters to set on the URL
     * @param bool $ignorehttperrors Whether to ignore HTTP Errors
     * @param bool $requireauthorization Whether authorization is required for this request
     * @param bool $sendasjson Whether to send params as JSON
     */
    public function __construct(
        protected matrix_client $client,
        string $method,
        string $endpoint,
        protected array $params = [],
        protected array $query = [],
        protected bool $ignorehttperrors = false,
        protected bool $requireauthorization = true,
        protected bool $sendasjson = true,
    ) {
        foreach ($params as $name => $value) {
            if ($name[0] === ':') {
                if (preg_match("/{$name}\\b/", $endpoint) !== 1) {
                    throw new OutOfRangeException("Parameter not found in URL '{$name}'");
                }

                $endpoint = preg_replace("/{$name}\\b/", urlencode($value), $endpoint);
                unset($params[$name]);
            }
        }

        // Store the modified params.
        $this->remainingparams = $params;

        if (str_contains($endpoint, '/:')) {
            throw new OutOfRangeException("URL contains untranslated parameters '{$endpoint}'");
        }

        // Process the required headers.
        $headers = [
            'Content-Type' => 'application/json',
        ];

        if ($this->require_authorization()) {
            $headers['Authorization'] = 'Bearer ' . $this->client->get_token();
        }

        // Construct the final request.
        parent::__construct(
            $method,
            $this->get_url($endpoint),
            $headers,
        );
    }

    /**
     * Get the URL of the endpoint on the server.
     *
     * @param string $endpoint
     * @return string
     */
    protected function get_url(string $endpoint): string {
        return sprintf(
            "%s/%s",
            $this->client->get_server_url(),
            $endpoint,
        );
    }

    /**
     * Get all parameters, including those set in the URL.
     *
     * @return array
     */
    public function get_all_params(): array {
        return $this->params;
    }

    /**
     * Get the parameters provided to the command which are not used in the URL.
     *
     * These are typically passed to the server as query or body parameters instead.
     *
     * @return array
     */
    public function get_remaining_params(): array {
        return $this->remainingparams;
    }

    /**
     * Get the Guzzle options to pass into the request.
     *
     * @return array
     */
    public function get_options(): array {
        $options = [];

        if (count($this->query)) {
            $options['query'] = $this->query;
        }

        if ($this->should_send_params_as_json()) {
            $options['json'] = $this->get_remaining_params();
        }

        if ($this->should_ignore_http_errors()) {
            $options['http_errors'] = false;
        }

        return $options;
    }

    /**
     * Whether authorization is required.
     *
     * Based on the 'authorization' attribute set in a raw command.
     *
     * @return bool
     */
    public function require_authorization(): bool {
        return $this->requireauthorization;
    }

    /**
     * Whether to ignore http errors on the response.
     *
     * Based on the 'ignore_http_errors' attribute set in a raw command.
     *
     * @return bool
     */
    public function should_ignore_http_errors(): bool {
        return $this->ignorehttperrors;
    }

    /**
     * Whether to send remaining parameters as JSON.
     *
     * @return bool
     */
    public function should_send_params_as_json(): bool {
        return $this->sendasjson;
    }
}

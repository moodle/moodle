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

namespace core\oauth2\discovery;

use core\http_client;
use GuzzleHttp\Exception\ClientException;

/**
 * Simple reader class, allowing OAuth 2 Authorization Server Metadata to be read from an auth server's well-known.
 *
 * {@link https://www.rfc-editor.org/rfc/rfc8414}
 *
 * @package    core
 * @copyright  2023 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_server_config_reader {

    /** @var \stdClass the config object read from the discovery document. */
    protected \stdClass $metadata;

    /** @var \moodle_url the base URL for the auth server which was last used during a read.*/
    protected \moodle_url $issuerurl;

    /**
     * Constructor.
     *
     * @param http_client $httpclient an http client instance.
     * @param string $wellknownsuffix the well-known suffix, defaulting to 'oauth-authorization-server'.
     */
    public function __construct(protected http_client $httpclient,
        protected string $wellknownsuffix = 'oauth-authorization-server') {
    }

    /**
     * Read the metadata from the remote host.
     *
     * @param \moodle_url $issuerurl the auth server issuer URL.
     * @return \stdClass the configuration data object.
     * @throws ClientException|\GuzzleHttp\Exception\GuzzleException if the http client experiences any problems.
     */
    public function read_configuration(\moodle_url $issuerurl): \stdClass {
        $this->issuerurl = $issuerurl;
        $this->validate_uri();

        $url = $this->get_configuration_url()->out(false);
        $response = $this->httpclient->request('GET', $url);
        $this->metadata = json_decode($response->getBody());
        return $this->metadata;
    }

    /**
     * Make sure the base URI is suitable for use in discovery.
     *
     * @return void
     * @throws \moodle_exception if the URI fails validation.
     */
    protected function validate_uri() {
        if (!empty($this->issuerurl->get_query_string())) {
            throw new \moodle_exception('Error: '.__METHOD__.': Auth server base URL cannot contain a query component.');
        }
        if (strtolower($this->issuerurl->get_scheme()) !== 'https') {
            throw new \moodle_exception('Error: '.__METHOD__.': Auth server base URL must use HTTPS scheme.');
        }
        // This catches URL fragments. Since a query string is ruled out above, out_omit_querystring(false) returns only fragments.
        if ($this->issuerurl->out_omit_querystring() != $this->issuerurl->out(false)) {
            throw new \moodle_exception('Error: '.__METHOD__.': Auth server base URL must not contain fragments.');
        }
    }

    /**
     * Get the Auth server metadata URL.
     *
     * Per {@link https://www.rfc-editor.org/rfc/rfc8414#section-3}, if the issuer URL contains a path component,
     * the well known suffix is added between the host and path components.
     *
     * @return \moodle_url the full URL to the auth server metadata.
     */
    protected function get_configuration_url(): \moodle_url {
        $path = $this->issuerurl->get_path();
        if (!empty($path) && $path !== '/') {
            // Insert the well known suffix between the host and path components.
            $port = $this->issuerurl->get_port() ? ':'.$this->issuerurl->get_port() : '';
            $uri = $this->issuerurl->get_scheme() . "://" . $this->issuerurl->get_host() . $port ."/".
                ".well-known/" . $this->wellknownsuffix . $path;
        } else {
            // No path, just append the well known suffix.
            $uri = $this->issuerurl->out(false);
            $uri .= (substr($uri, -1) == '/' ? '' : '/');
            $uri .= ".well-known/$this->wellknownsuffix";
        }

        return new \moodle_url($uri);
    }
}

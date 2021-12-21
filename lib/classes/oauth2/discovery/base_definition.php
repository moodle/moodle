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

use curl;
use stdClass;
use moodle_exception;
use core\oauth2\issuer;
use core\oauth2\endpoint;

/**
 * Class for provider discovery definition, to allow services easily discover and process information.
 * This abstract class is called from core\oauth2\api when discovery points need to be updated.
 *
 * @package    core
 * @since      Moodle 3.11
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_definition {

    /**
     * Get the URL for the discovery manifest.
     *
     * @param issuer $issuer The OAuth issuer the endpoints should be discovered for.
     * @return string The URL of the discovery file, containing the endpoints.
     */
    public abstract static function get_discovery_endpoint_url(issuer $issuer): string;

    /**
     * Process the discovery information and create endpoints defined with the expected format.
     *
     * @param issuer $issuer The OAuth issuer the endpoints should be discovered for.
     * @param stdClass $info The discovery information, with the endpoints to process and create.
     * @return void
     */
    protected abstract static function process_configuration_json(issuer $issuer, stdClass $info): void;

    /**
     * Process how to map user field information.
     *
     * @param issuer $issuer The OAuth issuer the endpoints should be discovered for.
     * @return void
     */
    protected abstract static function create_field_mappings(issuer $issuer): void;

    /**
     * Self-register the issuer if the 'registration' endpoint exists and client id and secret aren't defined.
     *
     * @param issuer $issuer The OAuth issuer to register.
     * @return void
     */
    protected abstract static function register(issuer $issuer): void;

    /**
     * Create endpoints for this issuer.
     *
     * @param issuer $issuer Issuer the endpoints should be created for.
     * @return issuer
     */
    public static function create_endpoints(issuer $issuer): issuer {
        static::discover_endpoints($issuer);

        return $issuer;
    }

    /**
     * If the discovery endpoint exists for this issuer, try and determine the list of valid endpoints.
     *
     * @param issuer $issuer
     * @return int The number of discovered services.
     */
    public static function discover_endpoints($issuer): int {
        // Early return if baseurl is empty.
        if (empty($issuer->get('baseurl'))) {
            return 0;
        }

        // Get the discovery URL and check if it has changed.
        $creatediscoveryendpoint = false;
        $url = $issuer->get_endpoint_url('discovery');
        $providerurl = static::get_discovery_endpoint_url($issuer);
        if (!$url || $url != $providerurl) {
            $url = $providerurl;
            $creatediscoveryendpoint = true;
        }

        // Remove the existing endpoints before starting discovery.
        foreach (endpoint::get_records(['issuerid' => $issuer->get('id')]) as $endpoint) {
            // Discovery endpoint will be removed only if it will be created later, once we confirm it's working as expected.
            if ($creatediscoveryendpoint || $endpoint->get('name') != 'discovery_endpoint') {
                $endpoint->delete();
            }
        }

        // Early return if discovery URL is empty.
        if (empty($url)) {
            return 0;
        }

        $curl = new curl();
        if (!$json = $curl->get($url)) {
            $msg = 'Could not discover end points for identity issuer: ' . $issuer->get('name') . " [URL: $url]";
            throw new moodle_exception($msg);
        }

        if ($msg = $curl->error) {
            throw new moodle_exception('Could not discover service endpoints: ' . $msg);
        }

        $info = json_decode($json);
        if (empty($info)) {
            $msg = 'Could not discover end points for identity issuer: ' . $issuer->get('name') . " [URL: $url]";
            throw new moodle_exception($msg);
        }

        if ($creatediscoveryendpoint) {
            // Create the discovery endpoint (because it didn't exist and the URL exists and is returning some valid JSON content).
            static::create_discovery_endpoint($issuer, $url);
        }

        static::process_configuration_json($issuer, $info);
        static::create_field_mappings($issuer);
        static::register($issuer);

        return endpoint::count_records(['issuerid' => $issuer->get('id')]);
    }

    /**
     * Helper method to create discovery endpoint.
     *
     * @param issuer $issuer Issuer the endpoints should be created for.
     * @param string $url Discovery endpoint URL.
     * @return endpoint The endpoint created.
     *
     * @throws \core\invalid_persistent_exception
     */
    protected static function create_discovery_endpoint(issuer $issuer, string $url): endpoint {
        $record = (object) [
            'issuerid' => $issuer->get('id'),
            'name' => 'discovery_endpoint',
            'url' => $url,
        ];
        $endpoint = new endpoint(0, $record);
        $endpoint->create();

        return $endpoint;
    }

}

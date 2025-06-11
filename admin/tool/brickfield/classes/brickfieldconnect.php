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

namespace tool_brickfield;

defined('MOODLE_INTERNAL') || die;

// The curl class is in filelib.
global $CFG;
require_once("{$CFG->libdir}/filelib.php");

use curl;
use moodle_url;

/**
 * Class brickfieldconnect. Contains all function to connect to Brickfield external services.
 *
 * @package     tool_brickfield
 * @author      2020 Onwards Mike Churchward <mike@brickfieldlabs.ie>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL
 */
class brickfieldconnect extends curl {

    /** @var string The base api uri. */
    private static $baseapiuri = 'https://api.mybrickfield.ie/moodle/';

    /** @var array Endpoint details for setting and checking a site registration */
    const ACTION_CHECK_REGISTRATION = [
        'endpoint' => 'checkRegister',
        'method' => 'get',
    ];

    /** @var array Endpoint details for sending site summary data */
    const ACTION_SEND_SUMMARY = [
        'endpoint' => 'summary',
        'method' => 'post',
    ];

    /**
     * Object method to test whether site is already registered.
     * @return bool
     */
    public function is_registered(): bool {
        return !empty($this->get_registration_id_for_credentials());
    }

    /**
     * Update registration of this site.
     * @param   string $apikey The API key to use for the registration attempt
     * @param   string $secretkey The secret key to use
     * @return  bool
     */
    public function update_registration(string $apikey, string $secretkey): bool {
        $registrationid = $this->get_registration_id_for_credentials($apikey, $secretkey);
        if (empty($registrationid)) {
            return false;
        }

        (new registration())->set_siteid($registrationid);
        return true;
    }

    /**
     * Send the summary data to Brickfield.
     * @return bool
     * @throws \dml_exception
     */
    public function send_summary(): bool {
        // Run a registration check.
        if (!(new registration())->validate()) {
            return false;
        }

        $headers = $this->get_common_headers();

        // Sanity-check $headers 'id' value.
        if (!isset($headers['id'])) {
            return false;
        }

        $this->set_headers($headers);
        $summary = accessibility::get_summary_data($headers['id']);
        $body = json_encode($summary, JSON_UNESCAPED_SLASHES);
        $result = json_decode($this->call(self::ACTION_SEND_SUMMARY, $body));
        if (is_object($result) && ((int)$result->statusCode === 200)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the URL required for the command.
     *
     * @param   array $command The command to call, for example see self::ACTION_REGISTER
     * @return  string The complete URL
     */
    protected function get_url_for_command(array $command): string {
        return $this->get_baseapiuri() . $command['endpoint'];
    }

    /**
     * Call the specified command.
     *
     * @param array $command The command to call, for example see self::ACTION_REGISTER
     * @param array|string $params The params provided to the call
     * @return  string The response body
     */
    protected function call(array $command, $params = ''): string {
        $url = $this->get_url_for_command($command);
        if ($command['method'] === 'get') {
            return $this->get($url, $params);
        }

        if ($command['method'] === 'post') {
            return $this->post($url, $params);
        }

        return '';
    }

    /**
     * Get the common headers used for all calls to the Brickfields endpoints.
     *
     * @return  array
     */
    protected function get_common_headers(): array {
        $headers = [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'application/json',
            'siteurl' => static::get_siteurl(),
        ];

        if (static::has_registration_key()) {
            $registration = new registration();
            $headers['secret'] = $registration->get_api_key();
            $headers['userhash'] = $registration->get_secret_key();
            $headers['id'] = $registration->get_siteid();
        }

        return $headers;
    }

    /**
     * Set headers on the request from the specified list of headers.
     *
     * @param   array $headers An array of header name => value
     */
    protected function set_headers(array $headers) {
        foreach ($headers as $key => $value) {
            $this->setHeader("{$key}: {$value}");
        }
    }

    /**
     * Whether the site currently has a registration key stored.
     *
     * @return  bool
     */
    protected function has_registration_key(): bool {
        $registration = new registration();
        $localkey = $registration->get_api_key();
        $localhash = $registration->get_secret_key();
        $localid = $registration->get_siteid();

        if (!$localhash || !$localkey || !$localid) {
            return false;
        }

        return true;
    }

    /**
     * Get a normalised URL for the site.
     *
     * @return  string
     */
    protected function get_siteurl(): string {
        return (new moodle_url('/'))->out(false);
    }

    /**
     * Get the registration ID for the given set of credentials.
     * @param   null|string $apikey The API key to use for the registration attempt
     * @param   null|string $secretkey The secret key to use
     * @return  null|string The registration ID if registration was successful, or null if not
     */
    protected function get_registration_id_for_credentials(?string $apikey = null, ?string $secretkey = null): string {
        $headers = $this->get_common_headers();
        if ($apikey || $secretkey) {
            $headers['secret'] = $apikey;
            $headers['userhash'] = $secretkey;
        } else if (!$this->has_registration_key()) {
            return '';
        }

        $this->set_headers($headers);
        $response = $this->call(self::ACTION_CHECK_REGISTRATION);

        if ((int)$this->info['http_code'] !== 200) {
            // The response was unsuccessful.
            return '';
        }

        $result = json_decode($response);
        if (!$result) {
            // The response could not be decoded.
            return '';
        }

        if ((int)$result->statusCode !== 200) {
            // The data from the response suggests a failure.
            return '';
        }

        // Decode the actual result.
        $registrationdata = json_decode($result->body);
        if (empty($registrationdata) || !is_array($registrationdata)) {
            // Unable to decode the body of the response.
            return '';
        }

        if (!property_exists($registrationdata[0], 'id') || !property_exists($registrationdata[0]->id, 'N')) {
            // Unable to find a valid id in the response.
            return '';
        }

        return $registrationdata[0]->id->N;
    }

    /**
     * Get the check registration API URI.
     * @return string
     */
    protected function get_baseapiuri(): string {
        $baseapiuri = get_config(manager::PLUGINNAME, 'baseapiuri');
        if (!empty($baseapiuri)) {
            return $baseapiuri;
        } else {
            set_config('baseapiuri', self::$baseapiuri, manager::PLUGINNAME);
            return self::$baseapiuri;
        }
    }
}

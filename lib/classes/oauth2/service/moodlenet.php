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

namespace core\oauth2\service;

use core\http_client;
use core\oauth2\discovery\auth_server_config_reader;
use core\oauth2\endpoint;
use core\oauth2\issuer;
use GuzzleHttp\Psr7\Request;

/**
 * MoodleNet OAuth 2 configuration.
 *
 * @package    core
 * @copyright  2023 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodlenet implements issuer_interface {

    /**
     * Get the issuer template to display in the form.
     *
     * @return issuer the issuer.
     */
    public static function init(): ?issuer {
        $record = (object) [
            'name' => 'MoodleNet',
            'image' => 'https://moodle.net/favicon.ico',
            'baseurl' => 'https://moodle.net',
            'loginscopes' => '',
            'loginscopesoffline' => '',
            'loginparamsoffline' => '',
            'showonloginpage' => issuer::SERVICEONLY,
            'servicetype' => 'moodlenet',
        ];
        $issuer = new issuer(0, $record);

        return $issuer;
    }

    /**
     * Create the endpoints for the issuer.
     *
     * @param issuer $issuer the issuer instance.
     * @return issuer the issuer instance.
     */
    public static function create_endpoints(issuer $issuer): issuer {
        self::discover_endpoints($issuer);
        return $issuer;
    }

    /**
     * Read the OAuth 2 Auth Server Metadata.
     *
     * @param issuer $issuer the issuer instance.
     * @return int the number of endpoints created.
     */
    public static function discover_endpoints($issuer): int {
        $baseurl = $issuer->get('baseurl');
        if (empty($baseurl)) {
            return 0;
        }

        $endpointscreated = 0;
        $config = [];
        if (defined('BEHAT_SITE_RUNNING')) {
            $config['verify'] = false;
        }
        $configreader = new auth_server_config_reader(new http_client($config));
        try {
            $config = $configreader->read_configuration(new \moodle_url($baseurl));

            foreach ($config as $key => $value) {
                if (substr_compare($key, '_endpoint', -strlen('_endpoint')) === 0) {
                    $record = new \stdClass();
                    $record->issuerid = $issuer->get('id');
                    $record->name = $key;
                    $record->url = $value;

                    $endpoint = new endpoint(0, $record);
                    $endpoint->create();
                    $endpointscreated++;
                }

                if ($key == 'scopes_supported') {
                    $issuer->set('scopessupported', implode(' ', $value));
                    $issuer->update();
                }
            }
        } catch (\Exception $e) {
            throw new \moodle_exception('Could not read service configuration for issuer: ' . $issuer->get('name'));
        }

        try {
            self::client_registration($issuer);
        } catch (\Exception $e) {
            throw new \moodle_exception('Could not register client for issuer: ' . $issuer->get('name'));
        }

        return $endpointscreated;
    }

    /**
     * Perform (open) OAuth 2 Dynamic Client Registration with the MoodleNet application.
     *
     * @param issuer $issuer the issuer instance containing the service baseurl.
     * @return void
     */
    protected static function client_registration(issuer $issuer): void {
        global $CFG, $SITE;

        $clientid = $issuer->get('clientid');
        $clientsecret = $issuer->get('clientsecret');

        if (empty($clientid) && empty($clientsecret)) {
            $url = $issuer->get_endpoint_url('registration');
            if ($url) {
                $scopes = str_replace("\r", " ", $issuer->get('scopessupported'));
                $hosturl = $CFG->wwwroot;

                $request = [
                    'client_name' => $SITE->fullname,
                    'client_uri' => $hosturl,
                    'logo_uri' => $hosturl . '/pix/moodlelogo.png',
                    'tos_uri' => $hosturl,
                    'policy_uri' => $hosturl,
                    'software_id' => 'moodle',
                    'software_version' => $CFG->version,
                    'redirect_uris' => [
                        $hosturl . '/admin/oauth2callback.php'
                    ],
                    'token_endpoint_auth_method' => 'client_secret_basic',
                    'grant_types' => [
                        'authorization_code',
                        'refresh_token'
                    ],
                    'response_types' => [
                        'code'
                    ],
                    'scope' => $scopes
                ];

                $config = [];
                if (defined('BEHAT_SITE_RUNNING')) {
                    $config['verify'] = false;
                }
                $client = new http_client($config);
                $request = new Request(
                    'POST',
                    $url,
                    [
                        'Content-type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    json_encode($request)
                );

                try {
                    $response = $client->send($request);
                    $responsebody = $response->getBody()->getContents();
                    $decodedbody = json_decode($responsebody, true);
                    if (is_null($decodedbody)) {
                        throw new \moodle_exception('Error: ' . __METHOD__ . ': Failed to decode response body. Invalid JSON.');
                    }
                    $issuer->set('clientid', $decodedbody['client_id']);
                    $issuer->set('clientsecret', $decodedbody['client_secret']);
                    $issuer->update();
                } catch (\Exception $e) {
                    $msg = "Could not self-register {$issuer->get('name')}. Wrong URL or JSON data [URL: $url]";
                    throw new \moodle_exception($msg);
                }
            }
        }
    }
}

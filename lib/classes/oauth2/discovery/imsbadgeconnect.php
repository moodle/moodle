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
 * Class for IMS Open Badge Connect API (aka OBv2.1) discovery definition.
 *
 * @package    core
 * @since      Moodle 3.11
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class imsbadgeconnect extends base_definition {

    /**
     * Get the URL for the discovery manifest.
     *
     * @param issuer $issuer The OAuth issuer the endpoints should be discovered for.
     * @return string The URL of the discovery file, containing the endpoints.
     */
    public static function get_discovery_endpoint_url(issuer $issuer): string {
        $url = $issuer->get('baseurl');
        if (!empty($url)) {
            // Add slash at the end of the base url.
            $url .= (substr($url, -1) == '/' ? '' : '/');
            // Append the well-known file for IMS OBv2.1.
            $url .= '.well-known/badgeconnect.json';
        }

        return $url;
    }

    /**
     * Process the discovery information and create endpoints defined with the expected format.
     *
     * @param issuer $issuer The OAuth issuer the endpoints should be discovered for.
     * @param stdClass $info The discovery information, with the endpoints to process and create.
     * @return void
     */
    protected static function process_configuration_json(issuer $issuer, stdClass $info): void {
        $info = array_pop($info->badgeConnectAPI);
        foreach ($info as $key => $value) {
            if (substr_compare($key, 'Url', - strlen('Url')) === 0 && !empty($value)) {
                $record = new stdClass();
                $record->issuerid = $issuer->get('id');
                // Convert key names from xxxxUrl to xxxx_endpoint, in order to make it compliant with the Moodle oAuth API.
                $record->name = strtolower(substr($key, 0, - strlen('Url'))) . '_endpoint';
                $record->url = $value;

                $endpoint = new endpoint(0, $record);
                $endpoint->create();
            } else if ($key == 'scopesOffered') {
                // Get and update supported scopes.
                $issuer->set('scopessupported', implode(' ', $value));
                $issuer->update();
            } else if ($key == 'image' && empty($issuer->get('image'))) {
                // Update the image with the value in the manifest file if it's valid and empty in the issuer.
                $url = filter_var($value, FILTER_SANITIZE_URL);
                if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
                    $issuer->set('image', $url);
                    $issuer->update();
                }
            }
        }
    }

    /**
     * Process how to map user field information.
     *
     * @param issuer $issuer The OAuth issuer the endpoints should be discovered for.
     * @return void
     */
    protected static function create_field_mappings(issuer $issuer): void {
        // In that case, there are no user fields to map.
    }

    /**
     * Self-register the issuer if the 'registration' endpoint exists and client id and secret aren't defined.
     *
     * @param issuer $issuer The OAuth issuer to register.
     * @return void
     */
    protected static function register(issuer $issuer): void {
        global $CFG, $SITE;

        $clientid = $issuer->get('clientid');
        $clientsecret = $issuer->get('clientsecret');

        // Registration request for getting client id and secret will be done only they are empty in the issuer.
        // For now this can't be run from PHPUNIT (because IMS testing platform needs real URLs). In the future, this
        // request can be moved to the moodle-exttests repository.
        if (empty($clientid) && empty($clientsecret) && (!defined('PHPUNIT_TEST') || !PHPUNIT_TEST)) {
            $url = $issuer->get_endpoint_url('registration');
            if ($url) {
                $scopes = str_replace("\r", " ", $issuer->get('scopessupported'));

                // Add slash at the end of the site URL.
                $hosturl = $CFG->wwwroot;
                $hosturl .= (substr($CFG->wwwroot, -1) == '/' ? '' : '/');

                // Create the registration request following the format defined in the IMS OBv2.1 specification.
                $request = [
                    'client_name' => $SITE->fullname,
                    'client_uri' => $hosturl,
                    'logo_uri' => $hosturl . 'pix/f/moodle-256.png',
                    'tos_uri' => $hosturl,
                    'policy_uri' => $hosturl,
                    'software_id' => 'moodle',
                    'software_version' => $CFG->version,
                    'redirect_uris' => [
                        $hosturl . 'admin/oauth2callback.php'
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
                $jsonrequest = json_encode($request);

                $curl = new curl();
                $curl->setHeader(['Content-type: application/json']);
                $curl->setHeader(['Accept: application/json']);

                // Send the registration request.
                if (!$jsonresponse = $curl->post($url, $jsonrequest)) {
                    $msg = 'Could not self-register identity issuer: ' . $issuer->get('name') .
                        ". Wrong URL or JSON data [URL: $url]";
                    throw new moodle_exception($msg);
                }

                // Process the response and update client id and secret if they are valid.
                $response = json_decode($jsonresponse);
                if (property_exists($response, 'client_id')) {
                    $issuer->set('clientid', $response->client_id);
                    $issuer->set('clientsecret', $response->client_secret);
                    $issuer->update();
                } else {
                    $msg = 'Could not self-register identity issuer: ' . $issuer->get('name') .
                        '. Invalid response ' . $jsonresponse;
                    throw new moodle_exception($msg);
                }
            }
        }
    }

}

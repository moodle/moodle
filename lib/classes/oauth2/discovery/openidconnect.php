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

use stdClass;
use core\oauth2\issuer;
use core\oauth2\endpoint;
use core\oauth2\user_field_mapping;

/**
 * Class for Open ID Connect discovery definition.
 *
 * @package    core
 * @since      Moodle 3.11
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class openidconnect extends base_definition {

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
            // Append the well-known file for OIDC.
            $url .= '.well-known/openid-configuration';
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
        foreach ($info as $key => $value) {
            if (substr_compare($key, '_endpoint', - strlen('_endpoint')) === 0) {
                $record = new stdClass();
                $record->issuerid = $issuer->get('id');
                $record->name = $key;
                $record->url = $value;

                $endpoint = new endpoint(0, $record);
                $endpoint->create();
            }

            if ($key == 'scopes_supported') {
                $issuer->set('scopessupported', implode(' ', $value));
                $issuer->update();
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
        // Remove existing user field mapping.
        foreach (user_field_mapping::get_records(['issuerid' => $issuer->get('id')]) as $userfieldmapping) {
            $userfieldmapping->delete();
        }

        // Create the default user field mapping list.
        $mapping = [
            'given_name' => 'firstname',
            'middle_name' => 'middlename',
            'family_name' => 'lastname',
            'email' => 'email',
            'nickname' => 'alternatename',
            'picture' => 'picture',
            'address' => 'address',
            'phone' => 'phone1',
            'locale' => 'lang',
        ];

        foreach ($mapping as $external => $internal) {
            $record = (object) [
                'issuerid' => $issuer->get('id'),
                'externalfield' => $external,
                'internalfield' => $internal
            ];
            $userfieldmapping = new user_field_mapping(0, $record);
            $userfieldmapping->create();
        }
    }

    /**
     * Self-register the issuer if the 'registration' endpoint exists and client id and secret aren't defined.
     *
     * @param issuer $issuer The OAuth issuer to register.
     * @return void
     */
    protected static function register(issuer $issuer): void {
        // Registration not supported (at least for now).
    }

}

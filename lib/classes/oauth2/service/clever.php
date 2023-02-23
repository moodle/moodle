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

use core\oauth2\issuer;
use core\oauth2\discovery\openidconnect;
use core\oauth2\endpoint;
use core\oauth2\user_field_mapping;

/**
 * Class for Clever OAuth service, with the specific methods related to it.
 *
 * @package    core
 * @copyright  2022 OpenStax
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class clever extends openidconnect implements issuer_interface {
    /**
     * Build an OAuth2 issuer, with all the default values for this service.
     *
     * @return issuer The issuer initialised with proper default values.
     */
    public static function init(): issuer {
        $record = (object) [
            'name' => 'Clever',
            'image' => 'https://apps.clever.com/favicon.ico',
            'basicauth' => 1,
            'baseurl' => '',
            'showonloginpage' => issuer::LOGINONLY,
            'servicetype' => 'clever',
        ];

        return new issuer(0, $record);
    }

    /**
     * Create endpoints for this issuer.
     *
     * @param issuer $issuer Issuer the endpoints should be created for.
     * @return issuer
     */
    public static function create_endpoints(issuer $issuer): issuer {
        $endpoints = [
            'authorization_endpoint' => 'https://clever.com/oauth/authorize',
            'token_endpoint' => 'https://clever.com/oauth/tokens',
            'userinfo_endpoint' => 'https://api.clever.com/v3.0/me',
            'userdata_endpoint' => 'https://api.clever.com/v3.0/users'
        ];
        foreach ($endpoints as $name => $url) {
            $record = (object) [
                'issuerid' => $issuer->get('id'),
                'name' => $name,
                'url' => $url
            ];
            $endpoint = new endpoint(0, $record);
            $endpoint->create();
        }

        // Create the field mappings.
        $mapping = [
            'data-id' => 'idnumber',
            'data-name-first' => 'firstname',
            'data-name-last' => 'lastname',
            'data-email' => 'email'
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

        return $issuer;
    }
}

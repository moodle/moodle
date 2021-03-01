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
use core\oauth2\endpoint;
use core\oauth2\user_field_mapping;
use core\oauth2\discovery\openidconnect;

/**
 * Class for Microsoft oAuth service, with the specific methods related to it.
 *
 * @package    core
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class microsoft extends openidconnect implements issuer_interface {

    /**
     * Build an OAuth2 issuer, with all the default values for this service.
     *
     * @return issuer The issuer initialised with proper default values.
     */
    public static function init(): issuer {
        $record = (object) [
            'name' => 'Microsoft',
            'image' => 'https://www.microsoft.com/favicon.ico',
            'baseurl' => '',
            'loginscopes' => 'openid profile email user.read',
            'loginscopesoffline' => 'openid profile email user.read offline_access',
            'showonloginpage' => issuer::EVERYWHERE,
            'servicetype' => 'microsoft',
        ];

        $issuer = new issuer(0, $record);
        return $issuer;
    }

    /**
     * Create endpoints for this issuer.
     *
     * @param issuer $issuer Issuer the endpoints should be created for.
     * @return issuer
     */
    public static function create_endpoints(issuer $issuer): issuer {
        $endpoints = [
            'authorization_endpoint' => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
            'token_endpoint' => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
            'userinfo_endpoint' => 'https://graph.microsoft.com/v1.0/me/',
            'userpicture_endpoint' => 'https://graph.microsoft.com/v1.0/me/photo/$value',
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
            'givenName' => 'firstname',
            'surname' => 'lastname',
            'userPrincipalName' => 'email',
            'displayName' => 'alternatename',
            'officeLocation' => 'address',
            'mobilePhone' => 'phone1',
            'preferredLanguage' => 'lang'
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

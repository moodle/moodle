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

/**
 * Class linkedin.
 *
 * Custom oauth2 issuer for linkedin as it doesn't support OIDC and has a different way to get
 * key information for users - firstname, lastname, email.
 *
 * @copyright  2021 Peter Dias
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package    core
 */
class linkedin implements issuer_interface {
    /**
     * Build an OAuth2 issuer, with all the default values for this service.
     *
     * @return issuer The issuer initialised with proper default values.
     */
    public static function init(): issuer {
        $record = (object) [
            'name' => 'LinkedIn',
            'image' => 'https://static.licdn.com/scds/common/u/images/logos/favicons/v1/favicon.ico',
            'baseurl' => 'https://api.linkedin.com/v2',
            'loginscopes' => 'r_liteprofile r_emailaddress',
            'loginscopesoffline' => 'r_liteprofile r_emailaddress',
            'showonloginpage' => issuer::EVERYWHERE,
            'servicetype' => 'linkedin',
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
            'authorization_endpoint' => 'https://www.linkedin.com/oauth/v2/authorization',
            'token_endpoint' => 'https://www.linkedin.com/oauth/v2/accessToken',
            'email_endpoint' => 'https://api.linkedin.com/v2/emailAddress?q=members&projection=(elements*(handle~))',
            'userinfo_endpoint' => "https://api.linkedin.com/v2/me?projection=(localizedFirstName,localizedLastName,"
                                        . "profilePicture(displayImage~digitalmediaAsset:playableStreams))",
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
            'localizedFirstName' => 'firstname',
            'localizedLastName' => 'lastname',
            'elements[0]-handle~-emailAddress' => 'email',
            'profilePicture-displayImage~-elements[0]-identifiers[0]-identifier' => 'picture'
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

    /**
     * Linkedin does not have a discovery url that could be found. Return empty.
     * @param issuer $issuer
     * @return int
     */
    public static function discover_endpoints($issuer): int {
        return 0;
    }
}

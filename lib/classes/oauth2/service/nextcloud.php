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
 * Class for Nextcloud oAuth service, with the specific methods related to it.
 *
 * @package    core
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class nextcloud extends openidconnect implements issuer_interface {

    /**
     * Build an OAuth2 issuer, with all the default values for this service.
     *
     * @return issuer The issuer initialised with proper default values.
     */
    public static function init(): issuer {
        $record = (object) [
            'name' => 'Nextcloud',
            'image' => 'https://nextcloud.com/wp-content/themes/next/assets/img/common/favicon.png?x16328',
            'basicauth' => 1,
            'servicetype' => 'nextcloud',
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
        // Nextcloud has a custom baseurl. Thus, the creation of endpoints has to be done later.
        $baseurl = $issuer->get('baseurl');
        // Add trailing slash to baseurl, if needed.
        if (substr($baseurl, -1) !== '/') {
            $baseurl .= '/';
        }

        $endpoints = [
            // Baseurl will be prepended later.
            'authorization_endpoint' => 'index.php/apps/oauth2/authorize',
            'token_endpoint' => 'index.php/apps/oauth2/api/v1/token',
            'userinfo_endpoint' => 'ocs/v2.php/cloud/user?format=json',
            'webdav_endpoint' => 'remote.php/webdav/',
            'ocs_endpoint' => 'ocs/v1.php/apps/files_sharing/api/v1/shares',
        ];

        foreach ($endpoints as $name => $url) {
            $record = (object) [
                'issuerid' => $issuer->get('id'),
                'name' => $name,
                'url' => $baseurl . $url,
            ];
            $endpoint = new \core\oauth2\endpoint(0, $record);
            $endpoint->create();
        }

        // Create the field mappings.
        $mapping = [
            'ocs-data-email' => 'email',
            'ocs-data-id' => 'username',
        ];
        foreach ($mapping as $external => $internal) {
            $record = (object) [
                'issuerid' => $issuer->get('id'),
                'externalfield' => $external,
                'internalfield' => $internal
            ];
            $userfieldmapping = new \core\oauth2\user_field_mapping(0, $record);
            $userfieldmapping->create();
        }
        return $issuer;
    }
}

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
            'baseurl' => 'https://login.microsoftonline.com/common/v2.0',
            'loginscopes' => 'openid profile email user.read',
            'loginscopesoffline' => 'openid profile email user.read offline_access',
            'showonloginpage' => issuer::EVERYWHERE,
            'servicetype' => 'microsoft',
        ];

        $issuer = new issuer(0, $record);
        return $issuer;
    }

    #[\Override]
    protected static function create_field_mappings(issuer $issuer): void {
        // Remove existing user field mapping.
        foreach (user_field_mapping::get_records(['issuerid' => $issuer->get('id')]) as $userfieldmapping) {
            $userfieldmapping->delete();
        }

        // Create the field mappings.
        $mapping = [
            'sub' => 'idnumber',
            'given_name' => 'firstname',
            'family_name' => 'lastname',
            'email' => 'email',
            'displayName' => 'alternatename',
            'officeLocation' => 'address',
            'mobilePhone' => 'phone1',
            'locale' => 'lang',
        ];

        foreach ($mapping as $external => $internal) {
            $record = (object) [
                'issuerid' => $issuer->get('id'),
                'externalfield' => $external,
                'internalfield' => $internal,
            ];
            $userfieldmapping = new user_field_mapping(0, $record);
            $userfieldmapping->create();
        }
    }
}

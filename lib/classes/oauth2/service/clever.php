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
            'baseurl' => 'https://clever.com',
            'showonloginpage' => issuer::LOGINONLY,
            'servicetype' => 'clever',
        ];

        return new issuer(0, $record);
    }

    /**
     * Create field mappings for this issuer.
     *
     * @param issuer $issuer Issuer the field mappings should be created for.
     */
    public static function create_field_mappings(issuer $issuer): void {
        // Perform OIDC default field mapping.
        parent::create_field_mappings($issuer);

        // Create the additional 'sub' field mapping.
        $record = (object) [
            'issuerid' => $issuer->get('id'),
            'externalfield' => 'sub',
            'internalfield' => 'idnumber',
        ];
        $userfieldmapping = new user_field_mapping(0, $record);
        $userfieldmapping->create();
    }
}

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

use core\oauth2\discovery\openidconnect;
use core\oauth2\issuer;

/**
 * Class linkedin.
 *
 * OAuth 2 issuer for linkedin which is mostly OIDC compliant, with a few notable exceptions which require working around:
 *
 * 1. LinkedIn don't provide their OIDC discovery doc at {ISSUER}/.well-known/openid-configuration as the spec requires.
 * i.e. https://www.linkedin.com/.well-known/openid-configuration isn't present.
 * Instead, they make the configuration available at https://www.linkedin.com/oauth/.well-known/openid-configuration.
 * See: https://openid.net/specs/openid-connect-discovery-1_0.html#ProviderConfig
 *
 * 2. LinkedIn don't return 'locale' as a string in the userinfo but instead return an object with 'language' and 'country' props.
 * See: https://openid.net/specs/openid-connect-core-1_0.html#StandardClaims
 * This is resolved in {@see \core\oauth2\client\linkedin::get_userinfo()}
 *
 * @copyright  2021 Peter Dias
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package    core
 */
class linkedin extends openidconnect {
    /**
     * Build an OAuth2 issuer, with all the default values for this service.
     *
     * @return issuer The issuer initialised with proper default values.
     */
    public static function init(): issuer {
        $record = (object) [
            'name' => 'LinkedIn',
            'image' => 'https://static.licdn.com/scds/common/u/images/logos/favicons/v1/favicon.ico',
            'baseurl' => 'https://www.linkedin.com/oauth', // The /oauth is where .well-known/openid-configuration lives.
            'loginscopes' => 'openid profile email',
            'loginscopesoffline' => 'openid profile email',
            'showonloginpage' => issuer::EVERYWHERE,
            'servicetype' => 'linkedin',
        ];

        $issuer = new issuer(0, $record);
        return $issuer;
    }
}

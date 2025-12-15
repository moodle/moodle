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

namespace core\oauth2\client;

use core\oauth2\client;
use stdClass;

/**
 * Custom oauth2 client for Microsoft to handle specific requirements.
 *
 * @package    core
 * @copyright  2025 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class microsoft extends client {

    #[\Override]
    public function get_additional_upgrade_token_parameters(): array {
        $issuer = $this->get_issuer();
        if ($issuer->get('showonloginpage') == $issuer::SMTPWITHXOAUTH2) {
            // We are using this issuer for SMTP with XOAUTH2.
            // We need to add the SMTP scope to the token request.
            return [
                'scope' => 'https://outlook.office.com/SMTP.Send',
            ];
        }
        return parent::get_additional_upgrade_token_parameters();
    }

    #[\Override]
    protected function map_userinfo_to_fields(stdClass $userinfo): array {
        // Microsoft returns different field names depending on account type:
        // - Work/School accounts: OpenID Connect standard (given_name, family_name)
        // - Personal accounts: Non-standard lowercase (givenname, familyname)
        // We need to check both formats to support all Microsoft account types.
        //
        // Additionally, we provide bidirectional fallback to handle sites that have not yet
        // run the database upgrade to update field mappings from the old format to the new format.

        // Add fallback mappings for personal accounts if the standard fields are not present.
        if (empty($userinfo->given_name) && !empty($userinfo->givenname)) {
            $userinfo->given_name = $userinfo->givenname;
        }
        if (empty($userinfo->family_name) && !empty($userinfo->familyname)) {
            $userinfo->family_name = $userinfo->familyname;
        }

        // Add reverse fallback for sites with old database mappings (givenname/familyname).
        // This ensures work/school accounts work even before the database upgrade runs.
        if (empty($userinfo->givenname) && !empty($userinfo->given_name)) {
            $userinfo->givenname = $userinfo->given_name;
        }
        if (empty($userinfo->familyname) && !empty($userinfo->family_name)) {
            $userinfo->familyname = $userinfo->family_name;
        }

        // Call parent to handle the standard mapping.
        return parent::map_userinfo_to_fields($userinfo);
    }
}

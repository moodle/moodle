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

/**
 * Class linkedin - Custom client handler to fetch data from linkedin
 *
 * Custom oauth2 client for linkedin as it doesn't support OIDC and has a different way to get
 * key information for users - firstname, lastname, email.
 *
 * @copyright  2021 Peter Dias
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package    core
 */
class linkedin extends client {
    /**
     * Override to handle LinkedIn's non-spec-compliant 'locale' field, which isn't a string (e.g. 'en-US') but an object.
     *
     * @return array|false
     */
    public function get_userinfo() {
        $rawuserinfo = $this->get_raw_userinfo();
        if ($rawuserinfo === false) {
            return false;
        }

        if (!empty($rawuserinfo->locale) && is_object($rawuserinfo->locale)) {
            if (!empty($rawuserinfo->locale->language) && !empty($rawuserinfo->locale->country)) {
                $rawuserinfo->locale = "{$rawuserinfo->locale->language}-{$rawuserinfo->locale->country}";
            } else {
                unset($rawuserinfo->locale);
            }
        }

        return $this->map_userinfo_to_fields($rawuserinfo);
    }
}

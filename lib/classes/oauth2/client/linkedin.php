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
     * Fetch the user info from the userinfo and email endpoint and map fields back
     *
     * @return array|false
     */
    public function get_userinfo() {
        $user = array_merge(parent::get_userinfo(), $this->get_useremail());
        return $user;
    }

    /**
     * Get the email address of the user from the email endpoint
     *
     * @return array|false
     */
    private function get_useremail() {
        $url = $this->get_issuer()->get_endpoint_url('email');

        $response = $this->get($url);
        if (!$response) {
            return false;
        }
        $userinfo = new \stdClass();
        try {
            $userinfo = json_decode($response);
        } catch (\Exception $e) {
            return false;
        }

        return $this->map_userinfo_to_fields($userinfo);
    }
}

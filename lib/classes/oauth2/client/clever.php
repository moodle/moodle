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
 * Class clever - Custom client handler to fetch data from Clever
 *
 * @package    core
 * @copyright  2022 OpenStax
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class clever extends client {
    /**
     * Fetch the user id from the userinfo endpoint and then query userdata
     *
     * @return array|false
     */
    public function get_userinfo() {
        $userinfo = parent::get_userinfo();
        $userid = $userinfo['idnumber'];

        return $this->get_userdata($userid);
    }

    /**
     * Obtain user name and email data via the userdata endpoint
     *
     * @param string $userid User ID value
     * @return array|false
     */
    private function get_userdata($userid) {
        $url = $this->get_issuer()->get_endpoint_url('userdata');
        $url .= '/' . $userid;

        $response = $this->get($url);
        if (!$response) {
            return false;
        }

        $userinfo = json_decode($response);
        if (json_last_error() != JSON_ERROR_NONE) {
            debugging('Error encountered while decoding user information: ' . json_last_error_msg());
            return false;
        }

        return $this->map_userinfo_to_fields($userinfo);
    }
}

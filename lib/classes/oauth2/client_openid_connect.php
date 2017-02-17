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

/**
 * Configurable oauth2 client class.
 *
 * @package    core\oauth2
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\oauth2;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use stdClass;
use Exception;

/**
 * Configurable oauth2 client class where the urls come from DB.
 *
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class client_openid_connect extends client {

    /**
     * Returns a mapping of openid properties to moodle properties.
     *
     * @return array
     */
    private function get_mapping() {
        return [
            'given_name' => 'firstname',
            'middle_name' => 'middlename',
            'family_name' => 'lastname',
            'email' => 'email',
            'username' => 'username',
            'website' => 'url',
            'nickname' => 'alternatename',
            'picture' => 'picture',
            'address' => 'address',
            'phone' => 'phone',
            'locale' => 'lang'
        ];
    }

    public function get_additional_login_parameters() {
        if ($this->system) {
            return ['access_type' => 'offline', 'prompt' => 'consent'];
        }
        return [];
    }

    public function get_userinfo() {
        $url = $this->get_issuer()->get_endpoint_url('userinfo');
        $response = $this->get($url);
        if (!$response) {
            return false;
        }
        $userinfo = new stdClass();
        try {
            $userinfo = json_decode($response);
        } catch (Exception $e) {
            return false;
        }
        if (!empty($userinfo->preferred_username)) {
            $userinfo->username = $userinfo->preferred_username;
        } else {
            $userinfo->username = $userinfo->sub;
        }

        $map = $this->get_mapping();

        $user = new stdClass();
        foreach ($map as $openidproperty => $moodleproperty) {
            if (!empty($userinfo->$openidproperty)) {
                $user->$moodleproperty = $userinfo->$openidproperty;
            }
        }

        return (array)$user;
    }

}

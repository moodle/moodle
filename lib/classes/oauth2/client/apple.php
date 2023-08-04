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
use Firebase\JWT\JWT;
use moodle_url;
/**
 * Class apple - Custom client handler to fetch data from apple
 *
 * Custom oauth2 client for apple as it doesn't support OIDC and has a different way to get
 * key information for users - username, email.
 *
 * @copyright  2022 Sreenivasula@eabyas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package    core
 */
class apple extends client {

    public function __construct($issuer, $returnurl, $scopesrequired, $system = false, $autorefresh = false) {
        $this->issuer = $issuer;
        if (empty($returnurl)) {
            $returnurl = new moodle_url('/');
        }
        $this->clientid = $issuer->get('clientid');
        $this->clientsecret = $issuer->get('clientsecret');
        $this->tokenurl = $issuer->get_endpoint_url('token');
        parent::__construct($this->issuer, $returnurl, $scopesrequired);
    }
    /**
     * Fetch the user info from the idtoken
     *
     * @return array|false
     */
    public function get_userinfo() {
        $user = [];
        if (!empty($this->refreshtoken)) {
            $postfields = [
                'client_id' => $this->clientid,
                'client_secret' => $this->clientsecret,
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->refreshtoken,
            ];
            $curl = new \curl();
            $header = array(
                "accept: application/json",
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded"
              );
            $curl->setHeader($header);
            $bodyparams = http_build_query($postfields);
            $curlparams = htmlspecialchars_decode($bodyparams);
            $response = $curl->post($this->tokenurl, $curlparams);
            $response = json_decode($response);
            $idtoken = explode('.', $response->id_token);
            $payload = JWT::urlsafeB64Decode($idtoken[1]);
            $userrecord = JWT::jsonDecode($payload);
            $user['username'] = $userrecord->email;
            $user['email'] = $userrecord->email;
        }
        return $user;
    }

    public function get_raw_userinfo() {
        return $this->get_userinfo();
    }

}

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
 * Moodle Customised version of the PHPMailer OAuth class
 *
 * @package    core
 * @copyright  2022 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_phpmailer_oauth extends \PHPMailer\PHPMailer\OAuth {

    #[\Override]
    protected function getToken() {
        return $this->provider->get_accesstoken()->token;
    }

    #[\Override]
    public function getOauth64() {
        // Get a new token if it's not available.
        if ($this->oauthToken === null) {
            $this->oauthToken = $this->getToken();
        }

        $accesstoken = $this->provider->get_accesstoken();
        // Renew the token if it's expired.
        if (isset($accesstoken->expires) && time() >= $accesstoken->expires) {
            if (isset($accesstoken->refreshtoken)) {
                $this->provider->upgrade_token($accesstoken->refreshtoken, 'refresh_token');
            }
            $this->oauthToken = $this->getToken();
        }

        return base64_encode(
            'user=' .
            $this->oauthUserEmail .
            "\001auth=Bearer " .
            $this->oauthToken .
            "\001\001"
        );
    }
}

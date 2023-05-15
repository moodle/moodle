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
 * Helpers for authenticating mobile users through tokens
 *
 * @package    mod_hvp
 * @copyright  2019 Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_hvp;

defined('MOODLE_INTERNAL') || die();

class mobile_auth {

    const VALID_TIME = 60;

    /**
     * Generate embed auth token
     *
     * @param string $secret Secret phrase added to the hash
     * @param int $validfor Time factor that determines how long the token is valid
     *
     * @return array Login token and secret
     * @throws \Exception
     */
    public static function create_embed_auth_token($secret = null, $validfor = null) {
        if (!$validfor) {
            $validfor = self::get_time_factor();
        }

        if (empty($secret)) {
            if (function_exists('random_bytes')) {
                $secret = base64_encode(random_bytes(15));
            } else if (function_exists('openssl_random_pseudo_bytes')) {
                $secret = base64_encode(openssl_random_pseudo_bytes(15));
            } else {
                $secret = uniqid('', true);
            }
        }

        return [
            hash('md5', 'embed_auth' . $validfor . $secret),
            $secret
        ];
    }

    /**
     * Validate embed auth token
     *
     * @param string $token
     * @param string $secret
     *
     * @return bool True if valid token was supplied
     * @throws \Exception
     */
    public static function validate_embed_auth_token($token, $secret) {
        $timefactor = self::get_time_factor();
        // Splitting into two halves and allowing both allows for fractions roundup in the time factor.
        list($generatedtoken) = self::create_embed_auth_token($secret, $timefactor);
        list($generatedtoken2) = self::create_embed_auth_token($secret, $timefactor - 1);
        return $token === $generatedtoken || $token === $generatedtoken2;
    }

    /**
     * Check if provided user_id and token are valid for authenticating the user
     *
     * @param string $userid
     * @param string $token
     *
     * @return bool True if token and user_id is valid
     * @throws \dml_exception
     */
    public static function has_valid_token($userid, $secret) {
        global $DB;

        if (!$userid || !$secret) {
            return false;
        }

        $auth = $DB->get_record('hvp_auth', array(
            'user_id' => $userid,
        ));
        if (!$auth) {
            return false;
        }

        $isvalid = self::validate_embed_auth_token($auth->secret, $secret);

        // Cleanup user's token when used.
        if ($isvalid) {
            $DB->delete_records('hvp_auth', array(
                'user_id' => $userid
            ));
        }

        return $isvalid;
    }

    /**
     * Get time factor for how long the token is valid
     *
     * @return float
     */
    public static function get_time_factor() {
        return ceil(time() / self::VALID_TIME);
    }
}

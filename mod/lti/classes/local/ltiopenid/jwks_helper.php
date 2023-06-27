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
 * This files exposes functions for LTI 1.3 Key Management.
 *
 * @package    mod_lti
 * @copyright  2020 Claude Vervoort (Cengage)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_lti\local\ltiopenid;

/**
 * This class exposes functions for LTI 1.3 Key Management.
 *
 * @package    mod_lti
 * @copyright  2020 Claude Vervoort (Cengage)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class jwks_helper {

    /**
     * Returns the private key to use to sign outgoing JWT.
     *
     * @return array keys are kid and key in PEM format.
     */
    public static function get_private_key() {
        $privatekey = get_config('mod_lti', 'privatekey');
        $kid = get_config('mod_lti', 'kid');
        return [
            "key" => $privatekey,
            "kid" => $kid
        ];
    }

    /**
     * Returns the JWK Key Set for this site.
     * @return array keyset exposting the site public key.
     */
    public static function get_jwks() {
        $jwks = array('keys' => array());

        $privatekey = self::get_private_key();
        $res = openssl_pkey_get_private($privatekey['key']);
        $details = openssl_pkey_get_details($res);

        $jwk = array();
        $jwk['kty'] = 'RSA';
        $jwk['alg'] = 'RS256';
        $jwk['kid'] = $privatekey['kid'];
        $jwk['e'] = rtrim(strtr(base64_encode($details['rsa']['e']), '+/', '-_'), '=');
        $jwk['n'] = rtrim(strtr(base64_encode($details['rsa']['n']), '+/', '-_'), '=');
        $jwk['use'] = 'sig';

        $jwks['keys'][] = $jwk;
        return $jwks;
    }

}

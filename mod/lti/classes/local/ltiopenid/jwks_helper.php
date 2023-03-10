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

use Firebase\JWT\JWT;

/**
 * This class exposes functions for LTI 1.3 Key Management.
 *
 * @package    mod_lti
 * @copyright  2020 Claude Vervoort (Cengage)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class jwks_helper {

    /**
     *
     * See https://www.imsglobal.org/spec/security/v1p1#approved-jwt-signing-algorithms.
     * @var string[]
     */
    private static $ltisupportedalgs = [
        'RS256' => 'RSA',
        'RS384' => 'RSA',
        'RS512' => 'RSA',
        'ES256' => 'EC',
        'ES384' => 'EC',
        'ES512' => 'EC'
    ];

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

    /**
     * Take an array of JWKS keys and infer the 'alg' property for a single key, if missing, based on an input JWT.
     *
     * This only sets the 'alg' property for a single key when all the following conditions are met:
     * - The key's 'kid' matches the 'kid' provided in the JWT's header.
     * - The key's 'alg' is missing.
     * - The JWT's header 'alg' matches the algorithm family of the key (the key's kty).
     * - The JWT's header 'alg' matches one of the approved LTI asymmetric algorithms.
     *
     * Keys not matching the above are left unchanged.
     *
     * @param array $jwks the keyset array.
     * @param string $jwt the JWT string.
     * @return array the fixed keyset array.
     */
    public static function fix_jwks_alg(array $jwks, string $jwt): array {
        $jwtparts = explode('.', $jwt);
        $jwtheader = json_decode(JWT::urlsafeB64Decode($jwtparts[0]), true);
        if (!isset($jwtheader['kid'])) {
            throw new \moodle_exception('Error: kid must be provided in JWT header.');
        }

        foreach ($jwks['keys'] as $index => $key) {
            // Only fix the key being referred to in the JWT.
            if ($jwtheader['kid'] != $key['kid']) {
                continue;
            }

            // Only fix the key if the alg is missing.
            if (!empty($key['alg'])) {
                continue;
            }

            // The header alg must match the key type (family) specified in the JWK's kty.
            if (!isset(static::$ltisupportedalgs[$jwtheader['alg']]) ||
                    static::$ltisupportedalgs[$jwtheader['alg']] != $key['kty']) {
                throw new \moodle_exception('Error: Alg specified in the JWT header is incompatible with the JWK key type');
            }

            $jwks['keys'][$index]['alg'] = $jwtheader['alg'];
        }

        return $jwks;
    }

}

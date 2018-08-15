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
 * This file returns an array of available public keys
 *
 * @package    mod_lti
 * @copyright  2019 Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_DEBUG_DISPLAY', true);
define('NO_MOODLE_COOKIES', true);

require_once(__DIR__ . '/../../config.php');

$jwks = array('keys' => array());

$privatekey = get_config('mod_lti', 'privatekey');
$res = openssl_pkey_get_private($privatekey);
$details = openssl_pkey_get_details($res);

$jwk = array();
$jwk['kty'] = 'RSA';
$jwk['alg'] = 'RS256';
$jwk['kid'] = get_config('mod_lti', 'kid');
$jwk['e'] = strtr(base64_encode($details['rsa']['e']), '+/', '-_');
$jwk['n'] = strtr(base64_encode($details['rsa']['n']), '+/', '-_');
$jwk['use'] = 'sig';

$jwks['keys'][] = $jwk;

@header('Content-Type: application/json; charset=utf-8');

echo json_encode($jwks, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

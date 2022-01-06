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
 * Redirect the user to registration with token and openid config url as query params.
 *
 * @package mod_lti
 * @copyright  2020 Cengage
 * @author     Claude Vervoort
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Firebase\JWT\JWT;

use mod_lti\local\ltiopenid\jwks_helper;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir.'/weblib.php');

require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

$starturl = required_param('url', PARAM_URL);
$now = time();
$token = [
    "sub" => random_string(15),
    "scope" => "reg",
    "iat" => $now,
    "exp" => $now + HOURSECS
];
$privatekey = jwks_helper::get_private_key();
$regtoken = JWT::encode($token, $privatekey['key'], 'RS256', $privatekey['kid']);
$confurl = new moodle_url('/mod/lti/openid-configuration.php');
$url = new moodle_url($starturl);
$url->param('openid_configuration', $confurl->out(false));
$url->param('registration_token', $regtoken);
header("Location: ".$url->out(false));

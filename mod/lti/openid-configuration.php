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
 * This file returns the OpenId/LTI Configuration for this site.
 *
 * It is part of the LTI Tool Dynamic Registration, and used by
 * tools to get the site configuration and registration end-point.
 *
 * @package    mod_lti
 * @copyright  2020 Claude Vervoort (Cengage), Carlos Costa, Adrian Hutchinson (Macgraw Hill)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_lti\local\ltiopenid\registration_helper;

define('NO_DEBUG_DISPLAY', true);
define('NO_MOODLE_COOKIES', true);
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/lti/locallib.php');
require_once($CFG->libdir.'/weblib.php');

$scopes = registration_helper::lti_get_service_scopes();
$scopes[] = 'openid';
$conf = [
    'issuer' => $CFG->wwwroot,
    'token_endpoint' => (new moodle_url('/mod/lti/token.php'))->out(false),
    'token_endpoint_auth_methods_supported' => ['private_key_jwt'],
    'token_endpoint_auth_signing_alg_values_supported' => ['RS256'],
    'jwks_uri' => (new moodle_url('/mod/lti/certs.php'))->out(false),
    'registration_endpoint' => (new moodle_url('/mod/lti/openid-registration.php'))->out(false),
    'scopes_supported' => $scopes,
    'response_types_supported' => ['id_token'],
    'subject_types_supported' => ['public', 'pairwise'],
    'id_token_signing_alg_values_supported' => ['RS256'],
    'claims_supported' => ['sub', 'iss', 'name', 'given_name', 'family_name', 'email'],
    'https://purl.imsglobal.org/spec/lti-platform-configuration ' => [
        'product_family_code' => 'moodle',
        'version' => $CFG->release,
        'messages_supported' => ['LtiResourceLink', 'LtiDeepLinkingRequest'],
        'placements' => ['AddContentMenu'],
        'variables' => array_keys(lti_get_capabilities())
    ]
];

@header('Content-Type: application/json; charset=utf-8');

echo json_encode($conf, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

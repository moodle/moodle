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
 * Represents an oauth2 token.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\oauth2;

use auth_oidc\jwt;
use auth_oidc\oidcclient;
use local_o365\httpclientinterface;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/auth/oidc/lib.php');

/**
 * Represents an oauth2 token.
 */
class apptoken extends token {

    /**
     * Get a token instance for a new resource.
     *
     * @param int $userid
     * @param string $tokenresource The new resource.
     * @param clientdata $clientdata Client information.
     * @param httpclientinterface $httpclient An HTTP client.
     *
     * @return token|bool A constructed token for the new resource, or false if failure.
     */
    public static function get_for_new_resource($userid, $tokenresource, clientdata $clientdata, $httpclient) {
        $token = static::get_app_token($tokenresource, $clientdata, $httpclient);
        if (!empty($token)) {
            $expiry = $token['expires_on'] ?? time() + $token['expires_in'];
            if (get_config('auth_oidc', 'idptype') == AUTH_OIDC_IDP_TYPE_MICROSOFT_IDENTITY_PLATFORM) {
                $token['resource'] = $tokenresource;
                $token['scope'] = null;
            }
            static::store_new_token(null, $token['access_token'], $expiry, null, $token['scope'], $token['resource']);
            return static::instance(null, $tokenresource, $clientdata, $httpclient);
        } else {
            return false;
        }
    }

    /**
     * Get an app-only token.
     *
     * This is used by both get_for_new_resource and refresh, since refreshing an app-token is the same as
     * getting a new token.
     *
     * @param string $tokenresource The desired token resource.
     * @param clientdata $clientdata Client credentials object.
     * @param httpclientinterface $httpclient An HTTP client.
     *
     * @return array|bool If successful, an array of token parameters. False if unsuccessful.
     */
    public static function get_app_token($tokenresource, clientdata $clientdata, $httpclient) {
        $tokenendpoint = $clientdata->get_apptokenendpoint();

        switch (get_config('auth_oidc', 'idptype')) {
            case AUTH_OIDC_IDP_TYPE_MICROSOFT_ENTRA_ID:
                $params = [
                    'client_id' => $clientdata->get_clientid(),
                    'client_secret' => $clientdata->get_clientsecret(),
                    'grant_type' => 'client_credentials',
                    'resource' => $tokenresource,
                ];
                break;
            case AUTH_OIDC_IDP_TYPE_MICROSOFT_IDENTITY_PLATFORM:
                if (get_config('auth_oidc', 'clientauthmethod') == AUTH_OIDC_AUTH_METHOD_CERTIFICATE) {
                    $params = [
                        'client_id' => $clientdata->get_clientid(),
                        'scope' => 'https://graph.microsoft.com/.default',
                        'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
                        'client_assertion' => oidcclient::generate_client_assertion(),
                        'grant_type' => 'client_credentials',
                    ];
                } else {
                    // AUTH_OIDC_AUTH_METHOD_SECRET case.
                    $params = [
                        'client_id' => $clientdata->get_clientid(),
                        'client_secret' => $clientdata->get_clientsecret(),
                        'grant_type' => 'client_credentials',
                        'scope' => 'https://graph.microsoft.com/.default',
                    ];
                }
                break;
        }

        $params = http_build_query($params, '', '&');
        $header = [
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: ' . strlen($params),
        ];
        $httpclient->resetheader();
        $httpclient->setheader($header);
        $tokenresult = $httpclient->post($tokenendpoint, $params);
        $tokenresult = @json_decode($tokenresult, true);
        if (!empty($tokenresult) && isset($tokenresult['token_type']) && $tokenresult['token_type'] === 'Bearer') {
            if (empty($tokenresult['scope'])) {
                $tokenresult['scope'] = '';
            }
            return $tokenresult;
        } else {
            $errmsg = 'Problem encountered getting a new token.';
            // Clear tokens for privacy.
            if (isset($tokenresult['access_token'])) {
                $tokenresult['access_token'] = '---';
            }
            if (isset($tokenresult['refresh_token'])) {
                $tokenresult['refresh_token'] = '---';
            }
            $debuginfo = [
                'tokenresult' => $tokenresult,
                'resource' => $tokenresource,
            ];
            \local_o365\utils::debug($errmsg, __METHOD__, $debuginfo);
            return false;
        }
    }

    /**
     * Refresh the application only token.
     *
     * @return bool Success/Failure.
     * @throws moodle_exception
     */
    public function refresh() {
        $result = static::get_app_token($this->tokenresource, $this->clientdata, $this->httpclient);

        if (!empty($result) && is_array($result) && isset($result['access_token'])) {
            $originaltokenresource = $this->tokenresource;
            $this->token = $result['access_token'];
            $this->expiry = $result['expires_on'] ?? time() + $result['expires_in'];
            $this->refreshtoken = $result['access_token'];
            $this->scope = $result['scope'];
            if (isset($result['resource'])) {
                $this->tokenresource = $result['resource'];
            }
            $existingtoken = static::get_stored_token(null, $originaltokenresource);
            if (!empty($existingtoken)) {
                $newtoken = [
                    'scope' => $this->scope,
                    'token' => $this->token,
                    'expiry' => $this->expiry,
                    'tokenresource' => $this->tokenresource,
                ];
                $this->update_stored_token($existingtoken, $newtoken);
            } else {
                static::store_new_token(null, $this->token, $this->expiry, $this->refreshtoken, $this->scope, $this->tokenresource);
            }
            return true;
        } else {
            throw new moodle_exception('errorcouldnotrefreshtoken', 'local_o365');
        }
    }

    /**
     * Get stored token for a user and resourse.
     *
     * @param int $userid The ID of the user to get the token for.
     * @param string $tokenresource The resource to get the token for.
     *
     * @return array Array of token data.
     */
    protected static function get_stored_token($userid, $tokenresource) {
        $tokens = get_config('local_o365', 'apptokens');
        if (empty($tokens)) {
            return false;
        }
        $tokens = unserialize($tokens);
        if (isset($tokens[$tokenresource])) {
            // App tokens do not have a user.
            $tokens[$tokenresource]['user_id'] = null;
            // App tokens do not have a refresh token.
            $tokens[$tokenresource]['refreshtoken'] = $tokens[$tokenresource]['token'];
            return $tokens[$tokenresource];
        } else {
            return false;
        }
    }

    /**
     * Update the stored token.
     *
     * @param array $existingtoken Array of existing token data.
     * @param array $newtoken Array of new token data.
     * @return bool Success/Failure.
     */
    protected function update_stored_token($existingtoken, $newtoken) {
        $tokens = get_config('local_o365', 'apptokens');
        if ($tokens) {
            $tokens = unserialize($tokens);
        } else {
            $tokens = [];
        }
        if (isset($tokens[$existingtoken['tokenresource']])) {
            unset($tokens[$existingtoken['tokenresource']]);
        }
        // App tokens do not use refresh tokens.
        if (isset($newtoken['refreshtoken'])) {
            unset($newtoken['refreshtoken']);
        }
        $tokens[$newtoken['tokenresource']] = $newtoken;
        $tokens = serialize($tokens);
        $existingapptokenssetting = get_config('local_o365', 'apptokens');
        if ($existingapptokenssetting != $tokens) {
            add_to_config_log('apptokens', $existingapptokenssetting, $tokens, 'local_o365');
        }
        set_config('apptokens', $tokens, 'local_o365');
        return true;
    }

    /**
     * Delete a stored token.
     *
     * @param array $existingtoken The existing token record.
     * @return bool Success/Failure.
     */
    protected function delete_stored_token($existingtoken) {
        $tokens = get_config('local_o365', 'apptokens');
        if (empty($tokens)) {
            return true;
        }
        $tokens = unserialize($tokens);
        if (isset($tokens[$existingtoken['tokenresource']])) {
            unset($tokens[$existingtoken['tokenresource']]);
        }
        $tokens = serialize($tokens);
        $existingapptokenssetting = get_config('local_o365', 'apptokens');
        if ($existingapptokenssetting != $tokens) {
            add_to_config_log('apptokens', $existingapptokenssetting, $tokens, 'local_o365');
        }
        set_config('apptokens', $tokens, 'local_o365');
        return true;
    }

    /**
     * Store a new app token.
     *
     * @param int $userid
     * @param string $token Token access token.
     * @param int $expiry Token expiry timestamp.
     * @param string $refreshtoken Token refresh token (unused in this token type).
     * @param string $scope Token scope.
     * @param string $tokenresource Token resource.
     *
     * @return array Array of new token information.
     */
    public static function store_new_token($userid, $token, $expiry, $refreshtoken, $scope, $tokenresource) {
        if (!$tokenresource) {
            $tokenresource = 'https://graph.microsoft.com';
        }
        $tokens = get_config('local_o365', 'apptokens');
        if (empty($tokens)) {
            $tokens = [];
        } else {
            $tokens = unserialize($tokens);
        }
        $newtoken = [
            'token' => $token,
            'expiry' => $expiry,
            'scope' => $scope,
            'tokenresource' => $tokenresource,
        ];
        $tokens[$tokenresource] = $newtoken;
        $tokens = serialize($tokens);
        $existingapptokenssetting = get_config('local_o365', 'apptokens');
        if ($existingapptokenssetting != $tokens) {
            add_to_config_log('apptokens', $existingapptokenssetting, $tokens, 'local_o365');
        }
        set_config('apptokens', $tokens, 'local_o365');
        return $newtoken;
    }
}

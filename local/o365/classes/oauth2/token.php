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
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\oauth2;

use local_o365\httpclientinterface;
use local_o365\utils;
use moodle_exception;

/**
 * Represents an oauth2 token.
 */
class token {
    /** @var string The access token. */
    protected $token;

    /** @var int The timestamp of when the token expires. */
    protected $expiry;

    /** @var string The refresh token. */
    protected $refreshtoken;

    /** @var string The token's scope. */
    protected $scope;

    /** @var string The token's resource. */
    protected $tokenresource;

    /** @var clientdata Client data used for refreshing the token if needed. */
    protected $clientdata;

    /** @var httpclientinterface An HTTP client used for refreshing the token if needed. */
    protected $httpclient;

    /** @var int The ID of the user the token belongs to. */
    protected $userid;

    /**
     * Constructor.
     *
     * @param string $token The access token.
     * @param int $expiry The timestamp of when the token expires.
     * @param string $refreshtoken The refresh token.
     * @param string $scope The token's scope.
     * @param string $tokenresource The token's resource.
     * @param int $userid
     * @param clientdata $clientdata Client data used for refreshing the token if needed.
     * @param httpclientinterface $httpclient An HTTP client used for refreshing the token if needed.
     */
    public function __construct($token, $expiry, $refreshtoken, $scope, $tokenresource, $userid,
                                clientdata $clientdata, httpclientinterface $httpclient) {
        $this->token = $token;
        $this->expiry = $expiry;
        $this->refreshtoken = $refreshtoken;
        $this->scope = $scope;
        $this->tokenresource = $tokenresource;
        $this->userid = $userid;
        $this->clientdata = $clientdata;
        $this->httpclient = $httpclient;
    }

    /**
     * Get the access token.
     *
     * @return string $token The access token.
     */
    public function get_token() {
        return $this->token;
    }

    /**
     * Get the timestamp of when the token expires.
     *
     * @return int $expiry The timestamp of when the token expires.
     */
    public function get_expiry() {
        return $this->expiry;
    }

    /**
     * Get the refresh token.
     *
     * @return string $refreshtoken The refresh token.
     */
    public function get_refreshtoken() {
        return $this->refreshtoken;
    }

    /**
     * Get the token's scope.
     *
     * @return string $scope The token's scope.
     */
    public function get_scope() {
        return $this->scope;
    }

    /**
     * Get the token's resource.
     *
     * @return string The token's resource.
     */
    public function get_tokenresource() {
        return $this->tokenresource;
    }

    /**
     * Get the token's userid.
     *
     * @return int|null The token's userid.
     */
    public function get_userid() {
        return $this->userid;
    }

    /**
     * Determine whether the token is expired.
     *
     * @return bool Whether the token is expired.
     */
    public function is_expired() {
        return ($this->expiry <= time()) ? true : false;
    }

    /**
     * Get a token for a given resource and user.
     *
     * @param int $userid
     * @param string $tokenresource The new resource.
     * @param clientdata $clientdata Client information.
     * @param httpclientinterface $httpclient An HTTP client.
     * @param bool $forcecreate
     *
     * @return token|bool A constructed token for the new resource, or false if failure.
     */
    public static function instance($userid, $tokenresource, clientdata $clientdata, $httpclient, $forcecreate = false) {
        $token = static::get_stored_token($userid, $tokenresource);
        if (!empty($token)) {
            $token = new static($token['token'], $token['expiry'], $token['refreshtoken'], $token['scope'], $token['tokenresource'],
                $token['user_id'], $clientdata, $httpclient);
            return $token;
        } else {
            if ($tokenresource === 'https://graph.microsoft.com') {
                if ($userid == 0 && $forcecreate) {
                    $token = static::get_for_new_resource($userid, $tokenresource, $clientdata, $httpclient);
                    if (!empty($token)) {
                        return $token;
                    }
                }
                // This is the base resource we need to get tokens for other resources. If we don't have this, we can't continue.
                utils::debug('Cannot retrieve a token for the base resource.', __METHOD__);
                return null;
            } else {
                $token = static::get_for_new_resource($userid, $tokenresource, $clientdata, $httpclient);
                if (!empty($token)) {
                    return $token;
                }
            }
        }
        return null;
    }

    /**
     * Given a token for one resource, attempt to get a token for a different resource.
     *
     * @param token $token The starting token.
     * @param string $newtokenresource The new resource.
     * @param clientdata $clientdata Client information.
     * @param httpclientinterface $httpclient An HTTP client.
     *
     * @return token A constructed token for the new resource, or false if failure.
     */
    public static function jump_tokenresource(token $token, $newtokenresource, clientdata $clientdata,
        httpclientinterface $httpclient) {
        $params = [
            'client_id' => $clientdata->get_clientid(),
            'client_secret' => $clientdata->get_clientsecret(),
            'grant_type' => 'refresh_token',
            'refresh_token' => $token->get_refreshtoken(),
            'resource' => $newtokenresource,
        ];
        $params = http_build_query($params, '', '&');
        $tokenendpoint = $clientdata->get_tokenendpoint();

        $header = [
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: ' . strlen($params),
        ];
        $httpclient->resetheader();
        $httpclient->setheader($header);
        $tokenresult = $httpclient->post($tokenendpoint, $params);
        $tokenresult = @json_decode($tokenresult, true);

        if (!empty($tokenresult) && isset($tokenresult['token_type']) && $tokenresult['token_type'] === 'Bearer') {
            $userid = $token->get_userid();
            $newtoken = new token($tokenresult['access_token'], $tokenresult['expires_on'],
                $tokenresult['refresh_token'], $tokenresult['scope'], $tokenresult['resource'], $userid, $clientdata, $httpclient);
            return $newtoken;
        } else {
            $errmsg = 'Problem encountered getting a new token.';
            if (isset($tokenresult['access_token'])) {
                $tokenresult['access_token'] = '---';
            }
            if (isset($tokenresult['refresh_token'])) {
                $tokenresult['refresh_token'] = '---';
            }
            $debuginfo = [
                'tokenresult' => $tokenresult,
                'resource' => $newtokenresource,
            ];
            utils::debug($errmsg, __METHOD__, $debuginfo);
        }
    }

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
        $graphtoken = static::instance($userid, 'https://graph.microsoft.com', $clientdata, $httpclient);
        if (!empty($graphtoken)) {
            $params = [
                'client_id' => $clientdata->get_clientid(),
                'client_secret' => $clientdata->get_clientsecret(),
                'grant_type' => 'refresh_token',
                'refresh_token' => $graphtoken->get_refreshtoken(),
                'resource' => $tokenresource,
            ];
            $params = http_build_query($params, '', '&');
            $tokenendpoint = $clientdata->get_tokenendpoint();

            $header = [
                'Content-Type: application/x-www-form-urlencoded',
                'Content-Length: ' . strlen($params),
            ];
            $httpclient->resetheader();
            $httpclient->setheader($header);
            $tokenresult = $httpclient->post($tokenendpoint, $params);
            $tokenresult = @json_decode($tokenresult, true);

            if (!empty($tokenresult) && isset($tokenresult['token_type']) && $tokenresult['token_type'] === 'Bearer') {
                $expiry = $tokenresult['expires_on'] ?? time() + $tokenresult['expires_in'];
                if (get_config('auth_oidc', 'idptype') === AUTH_OIDC_IDP_TYPE_MICROSOFT_IDENTITY_PLATFORM) {
                    $tokenresult['resource'] = $tokenresource;
                    $tokenresult['scope'] = null;
                    $tokenresult['refresh_token'] = null;
                }
                static::store_new_token($userid, $tokenresult['access_token'], $expiry,
                        $tokenresult['refresh_token'], $tokenresult['scope'], $tokenresult['resource']);
                $token = static::instance($userid, $tokenresource, $clientdata, $httpclient);
                return $token;
            } else {
                $errmsg = 'Problem encountered getting a new token.';
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
                utils::debug($errmsg, __METHOD__, $debuginfo);
            }
        }
        return false;
    }

    /**
     * Get stored token for a user and resource.
     *
     * @param int $userid The ID of the user to get the token for.
     * @param string $tokenresource The resource to get the token for.
     *
     * @return array|null Array of token data or null if none found.
     */
    protected static function get_stored_token($userid, $tokenresource) {
        global $DB;
        if ($tokenresource === 'https://graph.microsoft.com') {
            $sql = 'SELECT tok.id,
                           tok.scope,
                           tok.tokenresource,
                           tok.token,
                           tok.expiry,
                           tok.refreshtoken
                      FROM {auth_oidc_token} tok
                      JOIN {user} u
                           ON u.id = tok.userid
                     WHERE u.id = ?';
            $params = [$userid];
            $record = $DB->get_record_sql($sql, $params);
            if (!empty($record)) {
                $record->user_id = $userid;
                return (array)$record;
            }
        } else {
            $record = $DB->get_record('local_o365_token', ['user_id' => $userid, 'tokenresource' => $tokenresource]);
            if (!empty($record)) {
                return (array)$record;
            }
        }
        return null;
    }

    /**
     * Update the stored token.
     *
     * @param array $existingtoken Array of existing token data.
     * @param array $newtoken Array of new token data.
     * @return bool Success/Failure.
     */
    protected function update_stored_token($existingtoken, $newtoken) {
        global $DB;
        if (!empty($existingtoken) && !empty($newtoken)) {
            $newtoken['id'] = $existingtoken['id'];
            if (empty($newtoken['refreshtoken'])) {
                $newtoken['refreshtoken'] = '';
            }
            $DB->update_record('local_o365_token', (object)$newtoken);
            return true;
        }
        return false;
    }

    /**
     * Delete a stored token.
     *
     * @param array $existingtoken The existing token record.
     * @return bool Success/Failure.
     */
    protected function delete_stored_token($existingtoken) {
        global $DB;
        if (!empty($existingtoken['id'])) {
            $DB->delete_records('local_o365_token', ['id' => $existingtoken['id']]);
            return true;
        }
        return false;
    }

    /**
     * Store a new token.
     *
     * @param int $userid
     * @param string $token Token access token.
     * @param int $expiry Token expiry timestamp.
     * @param string $refreshtoken Token refresh token.
     * @param string $scope Token scope.
     * @param string $tokenresource Token resource.
     *
     * @return array Array of new token information.
     */
    public static function store_new_token($userid, $token, $expiry, $refreshtoken, $scope, $tokenresource) {
        global $DB;
        $newtoken = new \stdClass;
        $newtoken->user_id = $userid;
        $newtoken->tokenresource = $tokenresource;
        $newtoken->scope = $scope;
        $newtoken->token = $token;

        // Default expiry is 1 hour, if we didn't get an expiry, play it safe and expire in 45 mins.
        $newtoken->expiry = (!empty($expiry)) ? $expiry : time() + (60 * 45);

        // Refresh tokens *sometimes* don't exist...
        $newtoken->refreshtoken = (!empty($refreshtoken)) ? $refreshtoken : '';

        $newtoken->id = $DB->insert_record('local_o365_token', $newtoken);
        return $newtoken;
    }

    /**
     * Refresh the token.
     *
     * @return bool Success/Failure.
     * @throws moodle_exception
     */
    public function refresh() {
        $result = '';
        if (!empty($this->refreshtoken)) {
            $params = [
                'client_id' => $this->clientdata->get_clientid(),
                'client_secret' => $this->clientdata->get_clientsecret(),
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->refreshtoken,
                'resource' => $this->tokenresource,
            ];
            $params = http_build_query($params, '', '&');
            $tokenendpoint = $this->clientdata->get_tokenendpoint();

            $header = [
                'Content-Type: application/x-www-form-urlencoded',
                'Content-Length: ' . strlen($params),
            ];
            $this->httpclient->resetheader();
            $this->httpclient->setheader($header);

            $result = $this->httpclient->post($tokenendpoint, $params);
            $result = json_decode($result, true);
        }
        if (!empty($result) && is_array($result) && isset($result['access_token'])) {
            $originaltokenresource = $this->tokenresource;

            $this->token = $result['access_token'];
            $this->expiry = $result['expires_on'] ?? time() + $result['expires_in'];
            $this->refreshtoken = $result['refresh_token'];
            $this->scope = $result['scope'];
            $this->tokenresource = $result['resource'];

            $existingtoken = $this->get_stored_token($this->userid, $originaltokenresource);
            if (!empty($existingtoken)) {
                $newtoken = [
                    'scope' => $this->scope,
                    'token' => $this->token,
                    'expiry' => $this->expiry,
                    'refreshtoken' => $this->refreshtoken,
                    'tokenresource' => $this->tokenresource,
                ];
                $this->update_stored_token($existingtoken, $newtoken);
            }
            return true;
        } else {
            // Couldn't refresh token with the stored information. Wipe the stored information and go from the original login token.
            $existingtoken = $this->get_stored_token($this->userid, $this->tokenresource);
            if (!empty($existingtoken)) {
                $this->delete_stored_token($existingtoken);
            }
            $token = static::get_for_new_resource($this->userid, $this->tokenresource, $this->clientdata, $this->httpclient);
            if (!empty($token)) {
                $this->token = $token->get_token();
                $this->expiry = $token->get_expiry();
                $this->refreshtoken = $token->get_refreshtoken();
                $this->scope = $token->get_scope();
                $this->tokenresource = $token->get_tokenresource();
                return true;
            } else {
                throw new moodle_exception('errorcouldnotrefreshtoken', 'local_o365');
                return false;
            }
        }
    }
}

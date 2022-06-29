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
 * Configurable oauth2 client class.
 *
 * @package    core
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\oauth2;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/oauthlib.php');
require_once($CFG->libdir . '/filelib.php');

use moodle_url;
use moodle_exception;
use stdClass;

/**
 * Configurable oauth2 client class. URLs come from DB and access tokens from either DB (system accounts) or session (users').
 *
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class client extends \oauth2_client {

    /** @var \core\oauth2\issuer $issuer */
    private $issuer;

    /** @var bool $system */
    protected $system = false;

    /** @var bool $autorefresh whether this client will use a refresh token to automatically renew access tokens.*/
    protected $autorefresh = false;

    /**
     * Constructor.
     *
     * @param issuer $issuer
     * @param moodle_url|null $returnurl
     * @param string $scopesrequired
     * @param boolean $system
     * @param boolean $autorefresh whether refresh_token grants are used to allow continued access across sessions.
     */
    public function __construct(issuer $issuer, $returnurl, $scopesrequired, $system = false, $autorefresh = false) {
        $this->issuer = $issuer;
        $this->system = $system;
        $this->autorefresh = $autorefresh;
        $scopes = $this->get_login_scopes();
        $additionalscopes = explode(' ', $scopesrequired);

        foreach ($additionalscopes as $scope) {
            if (!empty($scope)) {
                if (strpos(' ' . $scopes . ' ', ' ' . $scope . ' ') === false) {
                    $scopes .= ' ' . $scope;
                }
            }
        }
        if (empty($returnurl)) {
            $returnurl = new moodle_url('/');
        }
        $this->basicauth = $issuer->get('basicauth');
        parent::__construct($issuer->get('clientid'), $issuer->get('clientsecret'), $returnurl, $scopes);
    }

    /**
     * Returns the auth url for OAuth 2.0 request
     * @return string the auth url
     */
    protected function auth_url() {
        return $this->issuer->get_endpoint_url('authorization');
    }

    /**
     * Get the oauth2 issuer for this client.
     *
     * @return \core\oauth2\issuer Issuer
     */
    public function get_issuer() {
        return $this->issuer;
    }

    /**
     * Override to append additional params to a authentication request.
     *
     * @return array (name value pairs).
     */
    public function get_additional_login_parameters() {
        $params = '';

        if ($this->system || $this->can_autorefresh()) {
            // System clients and clients supporting the refresh_token grant (provided the user is authenticated) add
            // extra params to the login request, depending on the issuer settings. The extra params allow a refresh
            // token to be returned during the authorization_code flow.
            if (!empty($this->issuer->get('loginparamsoffline'))) {
                $params = $this->issuer->get('loginparamsoffline');
            }
        } else {
            // This is not a system client, nor a client supporting the refresh_token grant type, so just return the
            // vanilla login params.
            if (!empty($this->issuer->get('loginparams'))) {
                $params = $this->issuer->get('loginparams');
            }
        }

        if (empty($params)) {
            return [];
        }
        $result = [];
        parse_str($params, $result);
        return $result;
    }

    /**
     * Override to change the scopes requested with an authentiction request.
     *
     * @return string
     */
    protected function get_login_scopes() {
        if ($this->system || $this->can_autorefresh()) {
            // System clients and clients supporting the refresh_token grant (provided the user is authenticated) add
            // extra scopes to the login request, depending on the issuer settings. The extra params allow a refresh
            // token to be returned during the authorization_code flow.
            return $this->issuer->get('loginscopesoffline');
        } else {
            // This is not a system client, nor a client supporting the refresh_token grant type, so just return the
            // vanilla login scopes.
            return $this->issuer->get('loginscopes');
        }
    }

    /**
     * Returns the token url for OAuth 2.0 request
     *
     * We are overriding the parent function so we get this from the configured endpoint.
     *
     * @return string the auth url
     */
    protected function token_url() {
        return $this->issuer->get_endpoint_url('token');
    }

    /**
     * We want a unique key for each issuer / and a different key for system vs user oauth.
     *
     * @return string The unique key for the session value.
     */
    protected function get_tokenname() {
        $name = 'oauth2-state-' . $this->issuer->get('id');
        if ($this->system) {
            $name .= '-system';
        }
        return $name;
    }

    /**
     * Store a token between requests. Uses session named by get_tokenname for user account tokens
     * and a database record for system account tokens.
     *
     * @param stdClass|null $token token object to store or null to clear
     */
    protected function store_token($token) {
        if (!$this->system) {
            parent::store_token($token);
            return;
        }

        $this->accesstoken = $token;

        // Create or update a DB record with the new token.
        $persistedtoken = access_token::get_record(['issuerid' => $this->issuer->get('id')]);
        if ($token !== null) {
            if (!$persistedtoken) {
                $persistedtoken = new access_token();
                $persistedtoken->set('issuerid', $this->issuer->get('id'));
            }
            // Update values from $token. Don't use from_record because that would skip validation.
            $persistedtoken->set('token', $token->token);
            if (isset($token->expires)) {
                $persistedtoken->set('expires', $token->expires);
            } else {
                // Assume an arbitrary time span of 1 week for access tokens without expiration.
                // The "refresh_system_tokens_task" is run hourly (by default), so the token probably won't last that long.
                $persistedtoken->set('expires', time() + WEEKSECS);
            }
            $persistedtoken->set('scope', $token->scope);
            $persistedtoken->save();
        } else {
            if ($persistedtoken) {
                $persistedtoken->delete();
            }
        }
    }

    /**
     * Retrieve a stored token from session (user accounts) or database (system accounts).
     *
     * @return stdClass|null token object
     */
    protected function get_stored_token() {
        if ($this->system) {
            $token = access_token::get_record(['issuerid' => $this->issuer->get('id')]);
            if ($token !== false) {
                return $token->to_record();
            }
            return null;
        }

        return parent::get_stored_token();
    }

    /**
     * Get a list of the mapping user fields in an associative array.
     *
     * @return array
     */
    protected function get_userinfo_mapping() {
        $fields = user_field_mapping::get_records(['issuerid' => $this->issuer->get('id')]);

        $map = [];
        foreach ($fields as $field) {
            $map[$field->get('externalfield')] = $field->get('internalfield');
        }
        return $map;
    }

    /**
     * Override which upgrades the authorization code to an access token and stores any refresh token in the DB.
     *
     * @param string $code the authorisation code
     * @return bool true if the token could be upgraded
     * @throws moodle_exception
     */
    public function upgrade_token($code) {
        $upgraded = parent::upgrade_token($code);
        if (!$this->can_autorefresh()) {
            return $upgraded;
        }

        // For clients supporting auto-refresh, try to store a refresh token.
        if (!empty($this->refreshtoken)) {
            $refreshtoken = (object) [
                'token' => $this->refreshtoken,
                'scope' => $this->scope
            ];
            $this->store_user_refresh_token($refreshtoken);
        }

        return $upgraded;
    }

    /**
     * Override which in addition to auth code upgrade, also attempts to exchange a refresh token for an access token.
     *
     * @return bool true if the user is logged in as a result, false otherwise.
     */
    public function is_logged_in() {
        global $DB, $USER;

        $isloggedin = parent::is_logged_in();

        // Attempt to exchange a user refresh token, but only if required and supported.
        if ($isloggedin || !$this->can_autorefresh()) {
            return $isloggedin;
        }

        // Autorefresh is supported. Try to negotiate a login by exchanging a stored refresh token for an access token.
        $issuerid = $this->issuer->get('id');
        $refreshtoken = $DB->get_record('oauth2_refresh_token', ['userid' => $USER->id, 'issuerid' => $issuerid]);
        if ($refreshtoken) {
            try {
                $tokensreceived = $this->exchange_refresh_token($refreshtoken->token);
                if (empty($tokensreceived)) {
                    // No access token was returned, so invalidate the refresh token and return false.
                    $DB->delete_records('oauth2_refresh_token', ['id' => $refreshtoken->id]);
                    return false;
                }

                // Otherwise, save the access token and, if provided, the new refresh token.
                $this->store_token($tokensreceived['access_token']);
                if (!empty($tokensreceived['refresh_token'])) {
                    $this->store_user_refresh_token($tokensreceived['refresh_token']);
                }
                return true;
            } catch (\moodle_exception $e) {
                // The refresh attempt failed either due to an error or a bad request. A bad request could be received
                // for a number of reasons including expired refresh token (lifetime is not specified in OAuth 2 spec),
                // scope change or if app access has been revoked manually by the user (tokens revoked).
                // Remove the refresh token and suppress the exception, allowing the user to be taken through the
                // authorization_code flow again.
                $DB->delete_records('oauth2_refresh_token', ['id' => $refreshtoken->id]);
            }
        }

        return false;
    }

    /**
     * Whether this client should automatically exchange a refresh token for an access token as part of login checks.
     *
     * @return bool true if supported, false otherwise.
     */
    protected function can_autorefresh(): bool {
        global $USER;

        // Auto refresh is only supported when the follow criteria are met:
        // a) The client is not a system client. The exchange process for system client refresh tokens is handled
        // externally, via a call to client->upgrade_refresh_token().
        // b) The user is authenticated.
        // c) The client has been configured with autorefresh enabled.
        return !$this->system && ($this->autorefresh && !empty($USER->id));
    }

    /**
     * Store the user's refresh token for later use.
     *
     * @param stdClass $token a refresh token.
     */
    protected function store_user_refresh_token(stdClass $token): void {
        global $DB, $USER;

        $id = $DB->get_field('oauth2_refresh_token', 'id', ['userid' => $USER->id,
            'scopehash' => sha1($token->scope), 'issuerid' => $this->issuer->get('id')]);
        $time = time();
        if ($id) {
            $record = [
                'id' => $id,
                'timemodified' => $time,
                'token' => $token->token
            ];
            $DB->update_record('oauth2_refresh_token', $record);
        } else {
            $record = [
                'timecreated' => $time,
                'timemodified' => $time,
                'userid' => $USER->id,
                'issuerid' => $this->issuer->get('id'),
                'token' => $token->token,
                'scopehash' => sha1($token->scope)
            ];
            $DB->insert_record('oauth2_refresh_token', $record);
        }
    }

    /**
     * Attempt to exchange a refresh token for a new access token.
     *
     * If successful, will return an array of token objects in the form:
     * Array
     * (
     *     [access_token] => stdClass object
     *         (
     *             [token] => 'the_token_string'
     *             [expires] => 123456789
     *             [scope] => 'openid files etc'
     *         )
     *     [refresh_token] => stdClass object
     *         (
     *             [token] => 'the_refresh_token_string'
     *             [scope] => 'openid files etc'
     *         )
     *  )
     * where the 'refresh_token' will only be provided if supplied by the auth server in the response.
     *
     * @param string $refreshtoken the refresh token to exchange.
     * @return null|array array containing access token and refresh token if provided, null if the exchange was denied.
     * @throws moodle_exception if an invalid response is received or if the response contains errors.
     */
    protected function exchange_refresh_token(string $refreshtoken): ?array {
        $params = array('refresh_token' => $refreshtoken,
            'grant_type' => 'refresh_token'
        );

        if ($this->basicauth) {
            $idsecret = urlencode($this->issuer->get('clientid')) . ':' . urlencode($this->issuer->get('clientsecret'));
            $this->setHeader('Authorization: Basic ' . base64_encode($idsecret));
        } else {
            $params['client_id'] = $this->issuer->get('clientid');
            $params['client_secret'] = $this->issuer->get('clientsecret');
        }

        // Requests can either use http GET or POST.
        if ($this->use_http_get()) {
            $response = $this->get($this->token_url(), $params);
        } else {
            $response = $this->post($this->token_url(), $this->build_post_data($params));
        }

        if ($this->info['http_code'] !== 200) {
            $debuginfo = !empty($this->error) ? $this->error : $response;
            throw new moodle_exception('oauth2refreshtokenerror', 'core_error', '', $this->info['http_code'], $debuginfo);
        }

        $r = json_decode($response);

        if (!empty($r->error)) {
            throw new moodle_exception($r->error . ' ' . $r->error_description);
        }

        if (!isset($r->access_token)) {
            return null;
        }

        // Store the token an expiry time.
        $accesstoken = new stdClass();
        $accesstoken->token = $r->access_token;
        if (isset($r->expires_in)) {
            // Expires 10 seconds before actual expiry.
            $accesstoken->expires = (time() + ($r->expires_in - 10));
        }
        $accesstoken->scope = $this->scope;

        $tokens = ['access_token' => $accesstoken];

        if (isset($r->refresh_token)) {
            $this->refreshtoken = $r->refresh_token;
            $newrefreshtoken = new stdClass();
            $newrefreshtoken->token = $this->refreshtoken;
            $newrefreshtoken->scope = $this->scope;
            $tokens['refresh_token'] = $newrefreshtoken;
        }

        return $tokens;
    }

    /**
     * Override which, in addition to deleting access tokens, also deletes any stored refresh token.
     */
    public function log_out() {
        global $DB, $USER;
        parent::log_out();
        if (!$this->can_autorefresh()) {
            return;
        }

        // For clients supporting autorefresh, delete the stored refresh token too.
        $issuerid = $this->issuer->get('id');
        $refreshtoken = $DB->get_record('oauth2_refresh_token', ['userid' => $USER->id, 'issuerid' => $issuerid,
            'scopehash' => sha1($this->scope)]);
        if ($refreshtoken) {
            $DB->delete_records('oauth2_refresh_token', ['id' => $refreshtoken->id]);
        }
    }

    /**
     * Upgrade a refresh token from oauth 2.0 to an access token, for system clients only.
     *
     * @param \core\oauth2\system_account $systemaccount
     * @return boolean true if token is upgraded succesfully
     */
    public function upgrade_refresh_token(system_account $systemaccount) {
        $receivedtokens = $this->exchange_refresh_token($systemaccount->get('refreshtoken'));

        // No access token received, so return false.
        if (empty($receivedtokens)) {
            return false;
        }

        // Store the access token and, if provided by the server, the new refresh token.
        $this->store_token($receivedtokens['access_token']);
        if (isset($receivedtokens['refresh_token'])) {
            $systemaccount->set('refreshtoken', $receivedtokens['refresh_token']->token);
            $systemaccount->update();
        }

        return true;
    }

    /**
     * Fetch the user info from the user info endpoint and map all
     * the fields back into moodle fields.
     *
     * @return array|false Moodle user fields for the logged in user (or false if request failed)
     * @throws moodle_exception if the response is empty after decoding it.
     */
    public function get_userinfo() {
        $url = $this->get_issuer()->get_endpoint_url('userinfo');
        if (empty($url)) {
            return false;
        }

        $response = $this->get($url);
        if (!$response) {
            return false;
        }
        $userinfo = new stdClass();
        try {
            $userinfo = json_decode($response);
        } catch (\Exception $e) {
            return false;
        }

        if (is_null($userinfo)) {
            // Throw an exception displaying the original response, because, at this point, $userinfo shouldn't be empty.
            throw new moodle_exception($response);
        }

        return $this->map_userinfo_to_fields($userinfo);
    }

    /**
     * Maps the oauth2 response to userfields.
     *
     * @param stdClass $userinfo
     * @return array
     */
    protected function map_userinfo_to_fields(stdClass $userinfo): array {
        $map = $this->get_userinfo_mapping();

        $user = new stdClass();
        foreach ($map as $openidproperty => $moodleproperty) {
            // We support nested objects via a-b-c syntax.
            $getfunc = function($obj, $prop) use (&$getfunc) {
                $proplist = explode('-', $prop, 2);

                // The value of proplist[0] can be falsey, so just check if not set.
                if (empty($obj) || !isset($proplist[0])) {
                    return false;
                }

                if (preg_match('/^(.*)\[([0-9]*)\]$/', $proplist[0], $matches)
                        && count($matches) == 3) {
                    $property = $matches[1];
                    $index = $matches[2];
                    $obj = $obj->{$property}[$index] ?? null;
                } else if (!empty($obj->{$proplist[0]})) {
                    $obj = $obj->{$proplist[0]};
                } else if (is_array($obj) && !empty($obj[$proplist[0]])) {
                    $obj = $obj[$proplist[0]];
                } else {
                    // Nothing found after checking all possible valid combinations, return false.
                    return false;
                }

                if (count($proplist) > 1) {
                    return $getfunc($obj, $proplist[1]);
                }
                return $obj;
            };

            $resolved = $getfunc($userinfo, $openidproperty);
            if (!empty($resolved)) {
                $user->$moodleproperty = $resolved;
            }
        }

        if (empty($user->username) && !empty($user->email)) {
            $user->username = $user->email;
        }

        if (!empty($user->picture)) {
            $user->picture = download_file_content($user->picture, null, null, false, 10, 10, true, null, false);
        } else {
            $pictureurl = $this->issuer->get_endpoint_url('userpicture');
            if (!empty($pictureurl)) {
                $user->picture = $this->get($pictureurl);
            }
        }

        if (!empty($user->picture)) {
            // If it doesn't look like a picture lets unset it.
            if (function_exists('imagecreatefromstring')) {
                $img = @imagecreatefromstring($user->picture);
                if (empty($img)) {
                    unset($user->picture);
                } else {
                    imagedestroy($img);
                }
            }
        }

        return (array)$user;
    }
}

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
 * Configurable OAuth2 client class.
 *
 * @package    core_badges
 * @subpackage badges
 * @copyright  2020 Tung Thai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Tung Thai <Tung.ThaiDuc@nashtechglobal.com>
 */

namespace core_badges\oauth2;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/oauthlib.php');
require_once($CFG->libdir . '/filelib.php');
require_once('badge_backpack_oauth2.php');

use moodle_url;
use moodle_exception;
use stdClass;

define('BACKPACK_CHALLENGE_METHOD', 'S256');
define('BACKPACK_CODE_VERIFIER_TIME', 60);

/**
 * Configurable OAuth2 client to request authorization and store token. Use the PKCE method to verifier authorization.
 *
 * @copyright  2020 Tung Thai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Tung Thai <Tung.ThaiDuc@nashtechglobal.com>
 */
class client extends \core\oauth2\client {

    /**  @var \core\oauth2\issuer */
    private $issuer;

    /** @var string $clientid client identifier issued to the client */
    private $clientid = '';

    /** @var string $clientsecret The client secret. */
    private $clientsecret = '';

    /** @var moodle_url $returnurl URL to return to after authenticating */
    private $returnurl = null;

    /** @var string $grantscope */
    protected $grantscope = '';

    /** @var string $scope */
    protected $scope = '';

    /** @var bool basicauth */
    protected $basicauth = true;

    /** @var string|null backpack object */
    public $backpack = '';

    /**
     * client constructor.
     *
     * @param issuer $issuer oauth2 service.
     * @param string $returnurl return url after login
     * @param string $additionalscopes the scopes has been granted
     * @param null $backpack backpack object.
     * @throws \coding_exception error message.
     */
    public function __construct(\core\oauth2\issuer $issuer, $returnurl = '', $additionalscopes = '',
                                $backpack = null) {
        $this->issuer = $issuer;
        $this->clientid = $issuer->get('clientid');
        $this->returnurl = $returnurl;
        $this->clientsecret = $issuer->get('clientsecret');
        $this->backpack = $backpack;
        $this->grantscope = $additionalscopes;
        $this->scope = $additionalscopes;
        parent::__construct($issuer, $returnurl, $additionalscopes, false);
    }

    /**
     * Get login url.
     *
     * @return moodle_url
     * @throws \coding_exception
     * @throws moodle_exception
     */
    public function get_login_url() {
        $callbackurl = self::callback_url();
        $scopes = $this->issuer->get('scopessupported');

        // Removed the scopes does not support in authorization.
        $excludescopes = ['profile', 'openid'];
        $arrascopes = explode(' ', $scopes);
        foreach ($excludescopes as $exscope) {
            $key = array_search($exscope, $arrascopes);
            if (isset($key)) {
                unset($arrascopes[$key]);
            }
        }
        $scopes = implode(' ', $arrascopes);

        $params = array_merge(
            [
                'client_id' => $this->clientid,
                'response_type' => 'code',
                'redirect_uri' => $callbackurl->out(false),
                'state' => $this->returnurl->out_as_local_url(false),
                'scope' => $scopes,
                'code_challenge' => $this->code_challenge(),
                'code_challenge_method' => BACKPACK_CHALLENGE_METHOD,
            ]
        );
        return new moodle_url($this->auth_url(), $params);
    }

    /**
     * Generate code challenge.
     *
     * @return string
     */
    public function code_challenge() {
        $random = bin2hex(openssl_random_pseudo_bytes(43));
        $verifier = $this->base64url_encode(pack('H*', $random));
        $challenge = $this->base64url_encode(pack('H*', hash('sha256', $verifier)));
        $_SESSION['SESSION']->code_verifier = $verifier;
        return $challenge;
    }

    /**
     * Get code verifier.
     *
     * @return bool
     */
    public function code_verifier() {
        if (isset($_SESSION['SESSION']) && !empty($_SESSION['SESSION']->code_verifier)) {
            return $_SESSION['SESSION']->code_verifier;
        }
        return false;
    }

    /**
     * Generate base64url encode.
     *
     * @param string $plaintext text to convert.
     * @return string
     */
    public function base64url_encode($plaintext) {
        $base64 = base64_encode($plaintext);
        $base64 = trim($base64, "=");
        $base64url = strtr($base64, '+/', '-_');
        return ($base64url);
    }

    /**
     * Callback url where the request is returned to.
     *
     * @return moodle_url url of callback
     */
    public static function callback_url() {
        return new moodle_url('/badges/oauth2callback.php');
    }

    /**
     * Check and refresh token to keep login on backpack site.
     *
     * @return bool
     * @throws \coding_exception
     * @throws moodle_exception
     */
    public function is_logged_in() {

        // Has the token expired?
        if (isset($this->accesstoken->expires) && time() >= $this->accesstoken->expires) {
            if (isset($this->accesstoken->refreshtoken)) {
                return $this->upgrade_token($this->accesstoken->refreshtoken, 'refresh_token');
            } else {
                throw new moodle_exception('Could not refresh oauth token, please try again.');
            }
        }

        if (isset($this->accesstoken->token) && isset($this->accesstoken->scope)) {
            return true;
        }

        // If we've been passed then authorization code generated by the
        // authorization server try and upgrade the token to an access token.
        $code = optional_param('oauth2code', null, PARAM_RAW);
        // Note - sometimes we may call is_logged_in twice in the same request - we don't want to attempt
        // to upgrade the same token twice.
        if ($code && $this->upgrade_token($code, 'authorization_code')) {
            return true;
        }

        return false;
    }

    /**
     * Request new token.
     *
     * @param string $code code verify from Auth site.
     * @param string $granttype grant type.
     * @return bool
     * @throws moodle_exception
     */
    public function upgrade_token($code, $granttype = 'authorization_code') {
        $callbackurl = self::callback_url();

        if ($granttype == 'authorization_code') {
            $params = array('code' => $code,
                'grant_type' => $granttype,
                'redirect_uri' => $callbackurl->out(false),
                'scope' => $this->get_scopes(),
                'code_verifier' => $this->code_verifier()
            );
        } else if ($granttype == 'refresh_token') {
            $this->basicauth = false;
            $params = array('refresh_token' => $code,
                'grant_type' => $granttype,
                'scope' => $this->get_scopes(),
            );
        }
        if ($this->basicauth) {
            $idsecret = urlencode($this->clientid) . ':' . urlencode($this->clientsecret);
            $this->setHeader('Authorization: Basic ' . base64_encode($idsecret));
        } else {
            $params['client_id'] = $this->clientid;
            $params['client_secret'] = $this->clientsecret;
        }
        // Requests can either use http GET or POST.
        $response = $this->post($this->token_url(), $this->build_post_data($params));
        $r = json_decode($response);
        if ($this->info['http_code'] !== 200) {
            throw new moodle_exception('Could not upgrade oauth token');
        }

        if (is_null($r)) {
            throw new moodle_exception("Could not decode JSON token response");
        }

        if (!empty($r->error)) {
            throw new moodle_exception($r->error . ' ' . $r->error_description);
        }

        if (!isset($r->access_token)) {
            return false;
        }

        // Store the token an expiry time.
        $accesstoken = new stdClass;
        $accesstoken->token = $r->access_token;
        if (isset($r->expires_in)) {
            // Expires 10 seconds before actual expiry.
            $accesstoken->expires = (time() + ($r->expires_in - 10));
        }
        if (isset($r->refresh_token)) {
            $this->refreshtoken = $r->refresh_token;
            $accesstoken->refreshtoken = $r->refresh_token;
        }
        $accesstoken->scope = $r->scope;

        // Also add the scopes.
        $this->store_token($accesstoken);

        return true;
    }

    /**
     * Store a token to verify for send request.
     *
     * @param null|stdClass $token
     */
    protected function store_token($token) {
        global $USER;

        $this->accesstoken = $token;
        // Create or update a DB record with the new token.
        $persistedtoken = badge_backpack_oauth2::get_record(['externalbackpackid' => $this->backpack->id, 'userid' => $USER->id]);
        if ($token !== null) {
            if (!$persistedtoken) {
                $persistedtoken = new badge_backpack_oauth2();
                $persistedtoken->set('issuerid', $this->backpack->oauth2_issuerid);
                $persistedtoken->set('externalbackpackid', $this->backpack->id);
                $persistedtoken->set('userid', $USER->id);
            } else {
                $persistedtoken->set('timemodified', time());
            }
            // Update values from $token. Don't use from_record because that would skip validation.
            $persistedtoken->set('usermodified', $USER->id);
            $persistedtoken->set('token', $token->token);
            $persistedtoken->set('refreshtoken', $token->refreshtoken);
            $persistedtoken->set('expires', $token->expires);
            $persistedtoken->set('scope', $token->scope);
            $persistedtoken->save();
        } else {
            if ($persistedtoken) {
                $persistedtoken->delete();
            }
        }
    }

    /**
     * Get token of current user.
     *
     * @return stdClass|null token object
     */
    protected function get_stored_token() {
        global $USER;

        $token = badge_backpack_oauth2::get_record(['externalbackpackid' => $this->backpack->id, 'userid' => $USER->id]);
        if ($token !== false) {
            $token = $token->to_record();
            return $token;
        }
        return null;
    }

    /**
     * Get scopes granted.
     *
     * @return null|string
     */
    protected function get_scopes() {
        if (!empty($this->grantscope)) {
            return $this->grantscope;
        }
        $token = $this->get_stored_token();
        if ($token) {
            return $token->scope;
        }
        return null;
    }
}

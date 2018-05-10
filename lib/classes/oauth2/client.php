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
 * Configurable oauth2 client class where the urls come from DB.
 *
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class client extends \oauth2_client {

    /** @var \core\oauth2\issuer $issuer */
    private $issuer;

    /** @var bool $system */
    protected $system = false;

    /**
     * Constructor.
     *
     * @param issuer $issuer
     * @param moodle_url|null $returnurl
     * @param string $scopesrequired
     * @param boolean $system
     */
    public function __construct(issuer $issuer, $returnurl, $scopesrequired, $system = false) {
        $this->issuer = $issuer;
        $this->system = $system;
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
        if ($this->system) {
            if (!empty($this->issuer->get('loginparamsoffline'))) {
                $params = $this->issuer->get('loginparamsoffline');
            }
        } else {
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
        if ($this->system) {
            return $this->issuer->get('loginscopesoffline');
        } else {
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
     * Upgrade a refresh token from oauth 2.0 to an access token
     *
     * @param \core\oauth2\system_account $systemaccount
     * @return boolean true if token is upgraded succesfully
     * @throws moodle_exception Request for token upgrade failed for technical reasons
     */
    public function upgrade_refresh_token(system_account $systemaccount) {
        $refreshtoken = $systemaccount->get('refreshtoken');

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
            throw new moodle_exception('Could not upgrade oauth token');
        }

        $r = json_decode($response);

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
        $accesstoken->scope = $this->scope;
        // Also add the scopes.
        $this->store_token($accesstoken);

        if (isset($r->refresh_token)) {
            $systemaccount->set('refreshtoken', $r->refresh_token);
            $systemaccount->update();
            $this->refreshtoken = $r->refresh_token;
        }

        return true;
    }

    /**
     * Fetch the user info from the user info endpoint and map all
     * the fields back into moodle fields.
     *
     * @return array|false Moodle user fields for the logged in user (or false if request failed)
     */
    public function get_userinfo() {
        $url = $this->get_issuer()->get_endpoint_url('userinfo');
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

        $map = $this->get_userinfo_mapping();

        $user = new stdClass();
        foreach ($map as $openidproperty => $moodleproperty) {
            // We support nested objects via a-b-c syntax.
            $getfunc = function($obj, $prop) use (&$getfunc) {
                $proplist = explode('-', $prop, 2);
                if (empty($proplist[0]) || empty($obj->{$proplist[0]})) {
                    return false;
                }
                $obj = $obj->{$proplist[0]};

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

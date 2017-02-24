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
 * @package    core\oauth2
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\oauth2;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/oauthlib.php');
require_once($CFG->libdir . '/filelib.php');

use moodle_url;
use curl;
use stdClass;

/**
 * Configurable oauth2 client class where the urls come from DB.
 *
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class client extends \oauth2_client {

    /** @var \core\oauth2\issuer $issuer */
    private $issuer;

    /** @var bool $system */
    protected $system = false;

    /**
     * Constructor.
     *
     * @param issuer $issuer
     * @param moodle_url $returnurl
     */
    public function __construct(issuer $issuer, moodle_url $returnurl, $scopesrequired, $system) {
        $this->issuer = $issuer;
        $this->system = $system;
        $scopes = $this->get_login_scopes();
        $additionalscopes = explode(' ', $scopesrequired);

        foreach ($additionalscopes as $scope) {
            if (strpos(' ' . $scopes . ' ', ' ' . $scope . ' ') === false) {
                $scopes .= ' ' . $scope;
            }
        }
        parent::__construct($issuer->get('clientid'), $issuer->get('clientsecret'), $returnurl, $scopes);
    }

    public static function create(issuer $issuer, moodle_url $returnurl, $scopesrequired, $system = false) {
        if ($issuer->get('behaviour') == issuer::BEHAVIOUR_OPENID_CONNECT) {
            return new client_openid_connect($issuer, $returnurl, $scopesrequired, $system);
        } else if ($issuer->get('behaviour') == issuer::BEHAVIOUR_OAUTH2) {
            return new client_oauth2($issuer, $returnurl, $scopesrequired, $system);
        } else if ($issuer->get('behaviour') == issuer::BEHAVIOUR_MICROSOFT) {
            return new client_microsoft($issuer, $returnurl, $scopesrequired, $system);
        }
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
        return [];
    }

    /**
     * Override to change the scopes requested with an authentiction request.
     *
     * @return string
     */
    protected function get_login_scopes() {
        return 'openid profile email';
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

    protected function get_userinfo_mapping() {
        $fields = user_field_mapping::get_records(['issuerid' => $this->issuer->get('id')]);

        $map = [];
        foreach ($fields as $field) {
            $map[$field->get('externalfield')] = $field->get('internalfield');
        }
        return $map;
    }

    public function get_userinfo() {
        $url = $this->get_issuer()->get_endpoint_url('userinfo');
        $response = $this->get($url);
        if (!$response) {
            return false;
        }
        $userinfo = new stdClass();
        try {
            $userinfo = json_decode($response);
        } catch (Exception $e) {
            return false;
        }

        $map = $this->get_userinfo_mapping();

        $user = new stdClass();
        foreach ($map as $openidproperty => $moodleproperty) {
            if (!empty($userinfo->$openidproperty)) {
                $user->$moodleproperty = $userinfo->$openidproperty;
            }
        }

        if (!empty($user->picture)) {
            $user->picture = download_file_content($user->picture, null, null, false, 10, 10, true, null, false);
        } else {
            $pictureurl = $this->issuer->get_endpoint_url('userpicture');
            if (!empty($pictureurl)) {
                $user->picture = $this->get($pictureurl);
            }
        }

        return (array)$user;
    }
}

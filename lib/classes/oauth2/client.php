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

    public function get_issuer() {   
        return $this->issuer;
    }

    public function get_additional_login_parameters() {
        if ($this->issuer->get('behaviour') == issuer::BEHAVIOUR_OPENID_CONNECT) {
            return ['access_type' => 'offline', 'prompt' => 'consent'];
        }
        return [];
    }

    protected function get_login_scopes() {
        return 'openid profile email';
    }

    /**
     * Returns the token url for OAuth 2.0 request
     * @return string the auth url
     */
    protected function token_url() {
        return $this->issuer->get_endpoint_url('token');
    }

    protected function get_tokenname() {
        $name = static::class;
        if ($this->system) {
            $name .= '-system';
        }
        return $name;
    }

}

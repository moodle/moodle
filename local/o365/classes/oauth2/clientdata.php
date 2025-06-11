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
 * Class representing oauth2 client data.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\oauth2;

use moodle_exception;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/auth/oidc/lib.php');

/**
 * Class representing oauth2 client data.
 */
class clientdata {
    /** @var string The registerd client ID. */
    protected $clientid;

    /** @var string The registered client secreet. */
    protected $clientsecret;

    /** @var string The authorization endpoint URI. */
    protected $authendpoint;

    /** @var string The token endpoint URI. */
    protected $tokenendpoint;

    /** @var bool The app-only token endpoint URI. */
    protected $apptokenendpoint = false;

    /**
     * Constructor.
     *
     * @param string $clientid The registerd client ID.
     * @param string $clientsecret The registered client secreet.
     * @param string $authendpoint The authorization endpoint URI.
     * @param string $tokenendpoint The token endpoint URI.
     * @param string $apptokenendpoint The app-only token endpoint URI.
     */
    public function __construct($clientid, $clientsecret, $authendpoint, $tokenendpoint, $apptokenendpoint = null) {
        $this->clientid = $clientid;
        $this->clientsecret = $clientsecret;
        $this->authendpoint = $authendpoint;
        $this->tokenendpoint = $tokenendpoint;
        if (!empty($apptokenendpoint)) {
            $this->apptokenendpoint = $apptokenendpoint;
        } else {
            $tenant = get_config('local_o365', 'entratenant');
            if (!empty($tenant)) {
                $this->apptokenendpoint = static::get_apptokenendpoint_from_tenant($tenant);
            } else {
                $tenantid = get_config('local_o365', 'entratenantid');
                if (!empty($tenantid)) {
                    $this->apptokenendpoint = static::get_apptokenendpoint_from_tenant($tenantid);
                } else {
                    \local_o365\utils::debug('Did not populate clientdata:apptokenendpoint because no tenant was present',
                        __METHOD__);
                }
            }
        }
    }

    /**
     * Return the app token end point of the tenant.
     *
     * @param string $tenant
     * @return string
     */
    public static function get_apptokenendpoint_from_tenant($tenant) {
        $idptype = get_config('auth_oidc', 'idptype');
        if ($idptype == AUTH_OIDC_IDP_TYPE_MICROSOFT_ENTRA_ID) {
            return 'https://login.microsoftonline.com/' . $tenant . '/oauth2/token';
        } else if ($idptype == AUTH_OIDC_IDP_TYPE_MICROSOFT_IDENTITY_PLATFORM) {
            return 'https://login.microsoftonline.com/' . $tenant . '/oauth2/v2.0/token';
        } else {
            return '';
        }
    }

    /**
     * Get an instance from auth_oidc config.
     *
     * @param string|null $tenant
     * @return clientdata The constructed client data creds.
     * @throws moodle_exception
     */
    public static function instance_from_oidc($tenant = null) {
        $cfg = get_config('auth_oidc');

        if (!auth_oidc_is_setup_complete()) {
            throw new moodle_exception('erroracpauthoidcnotconfig', 'local_o365');
        }

        $apptokenendpoint = null;
        if (!empty($tenant)) {
            $apptokenendpoint = static::get_apptokenendpoint_from_tenant($tenant);
        }
        $clientsecret = null;
        if (property_exists($cfg, 'clientsecret')) {
            $clientsecret = $cfg->clientsecret;
        }

        return new static($cfg->clientid, $clientsecret, $cfg->authendpoint, $cfg->tokenendpoint, $apptokenendpoint);
    }

    /**
     * Get the registered client ID.
     *
     * @return string The registered client ID.
     */
    public function get_clientid() {
        return $this->clientid;
    }

    /**
     * Get the registered client secret.
     *
     * @return string The registered client secret.
     */
    public function get_clientsecret() {
        return $this->clientsecret;
    }
    /**
     * Get the authorization endpoint URI.
     *
     * @return string The authorization endpoint URI.
     */
    public function get_authendpoint() {
        return $this->authendpoint;
    }

    /**
     * Get the token endpoint URI.
     *
     * @return string The token endpoint URI.
     */
    public function get_tokenendpoint() {
        return $this->tokenendpoint;
    }

    /**
     * Get the token for app-only authentication.
     *
     * @return string The app-only token endpoint URI.
     */
    public function get_apptokenendpoint() {
        return $this->apptokenendpoint;
    }
}

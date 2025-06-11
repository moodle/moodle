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

namespace enrol_lti\local\ltiadvantage\entity;

/**
 * Class application_registration.
 *
 * This class represents an LTI Advantage Application Registration.
 * Each registered application may contain one or more deployments of the Moodle tool.
 * This registration provides the security contract for all tool deployments belonging to the registration.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class application_registration {

    /** @var int|null the if of this registration instance, or null if it hasn't been stored yet. */
    private $id;

    /** @var string the name of the application being registered. */
    private $name;

    /** @var \moodle_url the issuer identifying the platform, as provided by the platform. */
    private $platformid;

    /** @var string the client id as provided by the platform. */
    private $clientid;

    /** @var \moodle_url the authentication request URL, as provided by the platform. */
    private $authenticationrequesturl;

    /** @var \moodle_url the certificate URL, as provided by the platform. */
    private $jwksurl;

    /** @var \moodle_url the access token URL, as provided by the platform. */
    private $accesstokenurl;

    /** @var string a unique identifier used by the registration in the initiate_login_uri to act as registration identifier.*/
    private $uniqueid;

    /** @var int status of the registration, either incomplete (draft) or complete (all required data present). */
    private $status;

    /** @var int const representing the incomplete state */
    const REGISTRATION_STATUS_INCOMPLETE = 0;

    /** @var int const representing a complete state */
    const REGISTRATION_STATUS_COMPLETE = 1;

    /**
     * The application_registration constructor.
     *
     * @param string $name the descriptor for this application registration.
     * @param string $uniqueid a unique identifier for the registration used in place of client_id in the login URI.
     * @param \moodle_url|null $platformid the URL of application
     * @param string|null $clientid unique id for the client on the application
     * @param \moodle_url|null $authenticationrequesturl URL to send OIDC Auth requests to.
     * @param \moodle_url|null $jwksurl URL to use to get public keys from the application.
     * @param \moodle_url|null $accesstokenurl URL to use to get an access token from the application, used in service calls.
     * @param int|null $id the id of the object instance, if being created from an existing store item.
     */
    private function __construct(string $name, string $uniqueid, ?\moodle_url $platformid, ?string $clientid,
            ?\moodle_url $authenticationrequesturl, ?\moodle_url $jwksurl, ?\moodle_url $accesstokenurl, ?int $id = null) {

        if (empty($name)) {
            throw new \coding_exception("Invalid 'name' arg. Cannot be an empty string.");
        }
        if (empty($uniqueid)) {
            throw new \coding_exception("Invalid 'uniqueid' arg. Cannot be an empty string.");
        }

        // Resolve the registration status.
        $iscomplete = (!is_null($platformid) && !is_null($clientid) && !is_null($authenticationrequesturl) &&
            !is_null($authenticationrequesturl) && !is_null($jwksurl) && !is_null($accesstokenurl));
        $this->status = $iscomplete ? self::REGISTRATION_STATUS_COMPLETE : self::REGISTRATION_STATUS_INCOMPLETE;

        $this->name = $name;
        $this->uniqueid = $uniqueid;
        $this->platformid = $platformid;
        $this->clientid = $clientid;
        $this->authenticationrequesturl = $authenticationrequesturl;
        $this->jwksurl = $jwksurl;
        $this->accesstokenurl = $accesstokenurl;
        $this->id = $id;
    }

    /**
     * Factory method to create a new instance of an application registration
     *
     * @param string $name the descriptor for this application registration.
     * @param string $uniqueid a unique identifier for the registration used in place of client_id in the login URI.
     * @param \moodle_url $platformid the URL of application
     * @param string $clientid unique id for the client on the application
     * @param \moodle_url $authenticationrequesturl URL to send OIDC Auth requests to.
     * @param \moodle_url $jwksurl URL to use to get public keys from the application.
     * @param \moodle_url $accesstokenurl URL to use to get an access token from the application, used in service calls.
     * @param int|null $id the id of the object instance, if being created from an existing store item.
     * @return application_registration the application_registration instance.
     * @throws \coding_exception if an invalid clientid is provided.
     */
    public static function create(string $name, string $uniqueid, \moodle_url $platformid, string $clientid,
            \moodle_url $authenticationrequesturl, \moodle_url $jwksurl, \moodle_url $accesstokenurl,
            ?int $id = null): application_registration {

        if (empty($clientid)) {
            throw new \coding_exception("Invalid 'clientid' arg. Cannot be an empty string.");
        }

        return new self($name, $uniqueid, $platformid, $clientid, $authenticationrequesturl, $jwksurl, $accesstokenurl, $id);
    }

    /**
     * Factory method to create a draft application registration.
     *
     * @param string $name the descriptor for the draft application registration.
     * @param string $uniqueid a unique identifier for the registration used in place of client_id in the login URI.
     * @param int|null $id the id of the object instance, if being created from an existing store item.
     * @return application_registration the application_registration instance.
     */
    public static function create_draft(string $name, string $uniqueid, ?int $id = null): application_registration {
        return new self($name, $uniqueid, null, null, null, null, null, $id);
    }

    /**
     * Get the integer id of this object instance.
     *
     * Will return null if the instance has not yet been stored.
     *
     * @return null|int the id, if set, otherwise null.
     */
    public function get_id(): ?int {
        return $this->id;
    }

    /**
     * Get the name of the application being registered.
     *
     * @return string the name.
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Sets the name of this registration.
     *
     * @param string $name the new name to set.
     * @throws \coding_exception if the provided name is invalid.
     */
    public function set_name(string $name): void {
        if (empty($name)) {
            throw new \coding_exception("Invalid 'name' arg. Cannot be an empty string.");
        }
        $this->name = $name;
    }

    /**
     * Return the local unique client id of the registration.
     *
     * @return string the id.
     */
    public function get_uniqueid(): string {
        return $this->uniqueid;
    }

    /**
     * Get the platform id.
     *
     * @return \moodle_url|null the platformid/issuer URL.
     */
    public function get_platformid(): ?\moodle_url {
        return $this->platformid;
    }

    /**
     * Sets the platformid/issuer for this registration.
     *
     * @param \moodle_url $platformid the platform id / iss to set.
     */
    public function set_platformid(\moodle_url $platformid): void {
        $this->platformid = $platformid;
    }

    /**
     * Get the client id.
     *
     * @return string|null the client id.
     */
    public function get_clientid(): ?string {
        return $this->clientid;
    }

    /**
     * Sets the client id for this registration.
     *
     * @param string $clientid the client id
     * @throws \coding_exception if the client id is invalid.
     */
    public function set_clientid(string $clientid): void {
        if (empty($clientid)) {
            throw new \coding_exception("Invalid 'clientid' arg. Cannot be an empty string.");
        }
        $this->clientid = $clientid;
    }

    /**
     * Get the authentication request URL.
     *
     * @return \moodle_url|null the authentication request URL.
     */
    public function get_authenticationrequesturl(): ?\moodle_url {
        return $this->authenticationrequesturl;
    }

    /**
     * Sets the authentication request URL for this registration.
     *
     * @param \moodle_url $authenticationrequesturl the authentication request URL.
     */
    public function set_authenticationrequesturl(\moodle_url $authenticationrequesturl): void {
        $this->authenticationrequesturl = $authenticationrequesturl;
    }

    /**
     * Get the JWKS URL.
     *
     * @return \moodle_url|null the JWKS URL.
     */
    public function get_jwksurl(): ?\moodle_url {
        return $this->jwksurl;
    }

    /**
     * Sets the JWKS URL for this registration.
     *
     * @param \moodle_url $jwksurl the JWKS URL.
     */
    public function set_jwksurl(\moodle_url $jwksurl): void {
        $this->jwksurl = $jwksurl;
    }

    /**
     * Get the access token URL.
     *
     * @return \moodle_url|null the access token URL.
     */
    public function get_accesstokenurl(): ?\moodle_url {
        return $this->accesstokenurl;
    }

    /**
     * Sets the access token URL for this registration.
     *
     * @param \moodle_url $accesstokenurl the access token URL.
     */
    public function set_accesstokenurl(\moodle_url $accesstokenurl): void {
        $this->accesstokenurl = $accesstokenurl;
    }

    /**
     * Add a tool deployment to this registration.
     *
     * @param string $name human readable name for the deployment.
     * @param string $deploymentid the unique id of the tool deployment in the platform.
     * @return deployment the new deployment.
     * @throws \coding_exception if trying to add a deployment to an instance without an id assigned.
     */
    public function add_tool_deployment(string $name, string $deploymentid): deployment {

        if (empty($this->get_id())) {
            throw new \coding_exception("Can't add deployment to a resource_link that hasn't first been saved.");
        }

        return deployment::create(
            $this->get_id(),
            $deploymentid,
            $name
        );
    }

    /**
     * Check whether this registration is complete or not.
     */
    public function is_complete(): bool {
        return $this->status == self::REGISTRATION_STATUS_COMPLETE;
    }

    /**
     * Attempt to progress this registration to the 'complete' state, provided required state exists.
     *
     * @see REGISTRATION_STATUS_COMPLETE
     *
     * @throws \coding_exception if the registration isn't in a state to be transitioned to complete.
     */
    public function complete_registration(): void {
        // Check completeness of registration.
        if (is_null($this->platformid)) {
            throw new \coding_exception("Unable to complete registration. Platform ID is missing.");
        }
        if (is_null($this->clientid)) {
            throw new \coding_exception("Unable to complete registration. Client ID is missing.");
        }
        if (is_null($this->accesstokenurl)) {
            throw new \coding_exception("Unable to complete registration. Access token URL is missing.");
        }
        if (is_null($this->authenticationrequesturl)) {
            throw new \coding_exception("Unable to complete registration. Authentication request URL is missing.");
        }
        if (is_null($this->jwksurl)) {
            throw new \coding_exception("Unable to complete registration. JWKS URL is missing.");
        }
        $this->status = self::REGISTRATION_STATUS_COMPLETE;
    }
}

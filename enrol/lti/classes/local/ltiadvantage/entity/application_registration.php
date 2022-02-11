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

    /**
     * The application_registration constructor.
     *
     * @param string $name the descriptor for this application registration.
     * @param \moodle_url $platformid the URL of application
     * @param string $clientid unique id for the client on the application
     * @param \moodle_url $authenticationrequesturl URL to send OIDC Auth requests to.
     * @param \moodle_url $jwksurl URL to use to get public keys from the application.
     * @param \moodle_url $accesstokenurl URL to use to get an access token from the application, used in service calls.
     * @param int|null $id the id of the object instance, if being created from an existing store item.
     */
    private function __construct(string $name, \moodle_url $platformid, string $clientid,
            \moodle_url $authenticationrequesturl, \moodle_url $jwksurl, \moodle_url $accesstokenurl, int $id = null) {

        if (empty($name)) {
            throw new \coding_exception("Invalid 'name' arg. Cannot be an empty string.");
        }
        if (empty($clientid)) {
            throw new \coding_exception("Invalid 'clientid' arg. Cannot be an empty string.");
        }
        $this->name = $name;
        $this->platformid = $platformid;
        $this->clientid = $clientid;
        $this->authenticationrequesturl = $authenticationrequesturl;
        $this->jwksurl = $jwksurl;
        $this->accesstokenurl = $accesstokenurl;
        $this->id = $id;
    }

    /**
     * Factory method to create a new instance of an application repository
     *
     * @param string $name the descriptor for this application registration.
     * @param \moodle_url $platformid the URL of application
     * @param string $clientid unique id for the client on the application
     * @param \moodle_url $authenticationrequesturl URL to send OIDC Auth requests to.
     * @param \moodle_url $jwksurl URL to use to get public keys from the application.
     * @param \moodle_url $accesstokenurl URL to use to get an access token from the application, used in service calls.
     * @param int|null $id the id of the object instance, if being created from an existing store item.
     * @return application_registration the application_registration instance.
     */
    public static function create(string $name, \moodle_url $platformid, string $clientid,
            \moodle_url $authenticationrequesturl, \moodle_url $jwksurl, \moodle_url $accesstokenurl,
            int $id = null): application_registration {

        return new self($name, $platformid, $clientid, $authenticationrequesturl, $jwksurl, $accesstokenurl, $id);
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
     * Get the platform id.
     *
     * @return \moodle_url the platformid/issuer URL.
     */
    public function get_platformid(): \moodle_url {
        return $this->platformid;
    }

    /**
     * Get the client id.
     *
     * @return string the client id.
     */
    public function get_clientid(): string {
        return $this->clientid;
    }

    /**
     * Get the authentication request URL.
     *
     * @return \moodle_url the authentication request URL.
     */
    public function get_authenticationrequesturl(): \moodle_url {
        return $this->authenticationrequesturl;
    }

    /**
     * Get the JWKS URL.
     *
     * @return \moodle_url the JWKS URL.
     */
    public function get_jwksurl(): \moodle_url {
        return $this->jwksurl;
    }

    /**
     * Get the access token URL.
     *
     * @return \moodle_url the access token URL.
     */
    public function get_accesstokenurl(): \moodle_url {
        return $this->accesstokenurl;
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
}

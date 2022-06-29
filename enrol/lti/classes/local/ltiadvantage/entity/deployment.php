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
 * Class deployment.
 *
 * This class represents an LTI Advantage Tool Deployment (http://www.imsglobal.org/spec/lti/v1p3/#tool-deployment).
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class deployment {
    /** @var int|null the id of this object instance, or null if it has not been saved yet. */
    private $id;

    /** @var string the name of this deployment. */
    private $deploymentname;

    /** @var string The platform-issued deployment id. */
    private $deploymentid;

    /** @var int the local ID of the application registration to which this deployment belongs. */
    private $registrationid;

    /** @var string|null the legacy consumer key, if the deployment instance is migrated from a legacy consumer. */
    private $legacyconsumerkey;

    /**
     * The private deployment constructor.
     *
     * @param string $deploymentname the name of this deployment.
     * @param string $deploymentid the platform-issued deployment id.
     * @param int $registrationid the local ID of the application registration.
     * @param int|null $id the id of this object instance, or null if it is a new instance which has not yet been saved.
     * @param string|null $legacyconsumerkey the 1.1 consumer key associated with this deployment, used for upgrades.
     */
    private function __construct(string $deploymentname, string $deploymentid, int $registrationid, ?int $id = null,
            ?string $legacyconsumerkey = null) {

        if (!is_null($id) && $id <= 0) {
            throw new \coding_exception('id must be a positive int');
        }
        if (empty($deploymentname)) {
            throw new \coding_exception("Invalid 'deploymentname' arg. Cannot be an empty string.");
        }
        if (empty($deploymentid)) {
            throw new \coding_exception("Invalid 'deploymentid' arg. Cannot be an empty string.");
        }
        $this->deploymentname = $deploymentname;
        $this->deploymentid = $deploymentid;
        $this->registrationid = $registrationid;
        $this->id = $id;
        $this->legacyconsumerkey = $legacyconsumerkey;
    }

    /**
     * Factory method to create a new instance of a deployment.
     *
     * @param int $registrationid the local ID of the application registration.
     * @param string $deploymentid the platform-issued deployment id.
     * @param string $deploymentname the name of this deployment.
     * @param int|null $id optional local id of this object instance, omitted for new deployment objects.
     * @param string|null $legacyconsumerkey the 1.1 consumer key associated with this deployment, used for upgrades.
     * @return deployment the deployment instance.
     */
    public static function create(int $registrationid, string $deploymentid, string $deploymentname,
            ?int $id = null, ?string $legacyconsumerkey = null): deployment {
        return new self($deploymentname, $deploymentid, $registrationid, $id, $legacyconsumerkey);
    }

    /**
     * Return the object id.
     *
     * @return int|null the id.
     */
    public function get_id(): ?int {
        return $this->id;
    }

    /**
     * Return the short name of this tool deployment.
     *
     * @return string the short name.
     */
    public function get_deploymentname(): string {
        return $this->deploymentname;
    }

    /**
     * Get the deployment id string.
     *
     * @return string deploymentid
     */
    public function get_deploymentid(): string {
        return $this->deploymentid;
    }

    /**
     * Get the id of the application_registration.
     *
     * @return int the id of the application_registration instance to which this deployment belongs.
     */
    public function get_registrationid(): int {
        return $this->registrationid;
    }

    /**
     * Get the legacy consumer key to which this deployment instance is mapped.
     *
     * @return string|null the legacy consumer key, if set, else null.
     */
    public function get_legacy_consumer_key(): ?string {
        return $this->legacyconsumerkey;
    }

    /**
     * Factory method to add a platform-specific context to the deployment.
     *
     * @param string $contextid the contextid, as supplied by the platform during launch.
     * @param array $types the context types the context represents, as supplied by the platform during launch.
     * @return context the context instance.
     * @throws \coding_exception if the context could not be created.
     */
    public function add_context(string $contextid, array $types): context {
        if (!$this->get_id()) {
            throw new \coding_exception('Can\'t add context to a deployment that hasn\'t first been saved');
        }

        return context::create($this->get_id(), $contextid, $types);
    }

    /**
     * Factory method to create a resource link from this deployment instance.
     *
     * @param string $resourcelinkid the platform-issued string id of the resource link.
     * @param int $resourceid the local published resource to which this link points.
     * @param int|null $contextid the platform context instance in which the resource link resides, if available.
     * @return resource_link the resource_link instance.
     * @throws \coding_exception if the resource_link can't be created.
     */
    public function add_resource_link(string $resourcelinkid, int $resourceid,
            int $contextid = null): resource_link {

        if (!$this->get_id()) {
            throw new \coding_exception('Can\'t add resource_link to a deployment that hasn\'t first been saved');
        }
        return resource_link::create($resourcelinkid, $this->get_id(), $resourceid, $contextid);
    }

    /**
     * Set the legacy consumer key for this instance, indicating that the deployment has been migrated from a consumer.
     *
     * @param string $key the legacy consumer key.
     * @throws \coding_exception if the key is invalid.
     */
    public function set_legacy_consumer_key(string $key): void {
        if (strlen($key) > 255) {
            throw new \coding_exception('Legacy consumer key too long. Cannot exceed 255 chars.');
        }
        $this->legacyconsumerkey = $key;
    }
}

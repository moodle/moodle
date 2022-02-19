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
 * Class resource_link.
 *
 * This class represents an LTI Advantage Resource Link (http://www.imsglobal.org/spec/lti/v1p3/#resource-link).
 *
 * @package    enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class resource_link {

    /** @var int|null the id of this object, or null if the object hasn't been stored yet. */
    private $id;

    /** @var string resourcelinkid the id of the resource link as supplied by the platform. */
    private $resourcelinkid;

    /** @var int $deploymentid the local id of the deployment instance to which this resource link belongs. */
    private $deploymentid;

    /** @var int|null $contextid the id of local context object representing the platform context, or null. */
    private $contextid;

    /** @var int The id of the local published resource this resource_link points to. */
    private $resourceid;

    /** @var ags_info|null the grade service for this resource_link, null if not applicable/not provided. */
    private $gradeservice;

    /** @var nrps_info|null the names and roles service for this resource_link, null if not applicable/not provided. */
    private $namesrolesservice;

    /**
     * The private resource_link constructor.
     *
     * @param string $resourcelinkid the id of the resource link as supplied by the platform.
     * @param int $deploymentid the local id of the deployment instance to which this resource link belongs.
     * @param int $resourceid the id of the local resource to which this link refers.
     * @param int|null $contextid the id local context object representing the context within the platform.
     * @param int|null $id the local id of this resource_link object.
     * @throws \coding_exception if the instance is unable to be created.
     */
    private function __construct(string $resourcelinkid, int $deploymentid, int $resourceid, ?int $contextid = null,
            int $id = null) {

        if (empty($resourcelinkid)) {
            throw new \coding_exception('Error: resourcelinkid cannot be an empty string');
        }
        $this->resourcelinkid = $resourcelinkid;
        $this->deploymentid = $deploymentid;
        $this->resourceid = $resourceid;
        $this->contextid = $contextid;
        $this->id = $id;
        $this->gradeservice = null;
        $this->namesrolesservice = null;
    }

    /**
     * Factory method to create an instance.
     *
     * @param string $resourcelinkid the resourcelinkid, as provided by the platform.
     * @param int $deploymentid the local id of the deployment to which this resource link belongs.
     * @param int $resourceid the id of the local resource this resource_link refers to.
     * @param int|null $contextid the id of the local context object representing the platform context.
     * @param int|null $id the local id of the resource link instance.
     * @return resource_link the newly created instance.
     */
    public static function create(string $resourcelinkid, int $deploymentid, int $resourceid, ?int $contextid = null,
            int $id = null): resource_link {

        return new self($resourcelinkid, $deploymentid, $resourceid, $contextid, $id);
    }

    /**
     * Return the id of this object instance.
     *
     * @return null|int the id or null if the object has not yet been stored.
     */
    public function get_id(): ?int {
        return $this->id;
    }

    /**
     * Get the resourcelinkid as provided by the platform.
     *
     * @return string the resourcelinkid.
     */
    public function get_resourcelinkid(): string {
        return $this->resourcelinkid;
    }

    /**
     * Return the id of the deployment to which this resource link belongs.
     *
     * This id is the local id of the deployment instance, not the deploymentid provided by the platform.
     *
     * @return int the deployment id.
     */
    public function get_deploymentid(): int {
        return $this->deploymentid;
    }

    /**
     * Get the local id of the published resource to which this resource link refers.
     *
     * @return int the id of the published resource.
     */
    public function get_resourceid(): int {
        return $this->resourceid;
    }

    /**
     * Return the id of the context object holding information about where this resource link resides.
     *
     * @return int|null the id or null if not present.
     */
    public function get_contextid(): ?int {
        return $this->contextid;
    }

    /**
     * Link this resource_link instance with a context.
     *
     * @param int $contextid the local id of the context instance containing information about the platform context.
     */
    public function set_contextid(int $contextid): void {
        if ($contextid <= 0) {
            throw new \coding_exception('Context id must be a positive int');
        }
        $this->contextid = $contextid;
    }

    /**
     * Set which local published resource this resource link refers to.
     *
     * @param int $resourceid the published resource id.
     */
    public function set_resourceid(int $resourceid): void {
        if ($resourceid <= 0) {
            throw new \coding_exception('Resource id must be a positive int');
        }
        $this->resourceid = $resourceid;
    }

    /**
     * Add grade service information to this resource_link instance.
     *
     * @param \moodle_url $lineitemsurl the service URL for get/put of line items.
     * @param \moodle_url|null $lineitemurl the service URL if only a single line item is present in the platform.
     * @param string[] $scopes the string array of grade service scopes which may be used by the service.
     */
    public function add_grade_service(\moodle_url $lineitemsurl, ?\moodle_url $lineitemurl = null, array $scopes = []) {
        $this->gradeservice = ags_info::create($lineitemsurl, $lineitemurl, $scopes);
    }

    /**
     * Get the grade service attached to this resource_link instance, or null if there isn't one associated.
     *
     * @return ags_info|null the grade service object instance, or null if not found.
     */
    public function get_grade_service(): ?ags_info {
        return $this->gradeservice;
    }

    /**
     * Add names and roles service information to this resource_link instance.
     *
     * @param \moodle_url $contextmembershipurl the service URL for memberships.
     * @param string[] $serviceversions the string array of supported service versions.
     */
    public function add_names_and_roles_service(\moodle_url $contextmembershipurl, array $serviceversions): void {
        $this->namesrolesservice = nrps_info::create($contextmembershipurl, $serviceversions);
    }

    /**
     * Get the names and roles service attached to this resource_link instance, or null if there isn't one associated.
     *
     * @return nrps_info|null the names and roles service object instance, or null if not found.
     */
    public function get_names_and_roles_service(): ?nrps_info {
        return $this->namesrolesservice;
    }

    /**
     * Factory method to create a user from this resource_link instance.
     *
     * This is useful for associating the user with the resource link and resource I.e. the user was created when
     * launching a specific resource link.
     *
     * @param int $userid the id of the Moodle user record.
     * @param string $sourceid the id of the user on the platform.
     * @param string $lang the user's lang code.
     * @param string $city the user's city.
     * @param string $country the user's country.
     * @param string $institution the user's institution.
     * @param string $timezone the user's timezone.
     * @param int|null $maildisplay the user's maildisplay, which can be omitted to use sensible defaults.
     * @return user the user instance.
     * @throws \coding_exception if trying to add a user to an as-yet-unsaved resource_link instance.
     */
    public function add_user(int $userid, string $sourceid, string $lang,
            string $city, string $country, string $institution, string $timezone,
            ?int $maildisplay = null): user {

        if (empty($this->get_id())) {
            throw new \coding_exception('Can\'t add user to a resource_link that hasn\'t first been saved');
        }

        return user::create_from_resource_link($this->get_id(), $this->get_resourceid(), $userid,
            $this->get_deploymentid(), $sourceid, $lang, $timezone, $city, $country,
            $institution, $maildisplay);
    }
}

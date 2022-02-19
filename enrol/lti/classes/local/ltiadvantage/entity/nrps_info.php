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
 * Class nrps_info, instances of which represent a names and roles provisioning service for a resource.
 *
 * For information about Names and Role Provisioning Services 2.0, see http://www.imsglobal.org/spec/lti-nrps/v2p0.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later */
class nrps_info {

    /** @var \moodle_url the memberships URL for the service. */
    private $contextmembershipsurl;

    /** @var float[] the array of supported service versions. */
    private $serviceversions;

    // Service versions are specified by the platform during launch.
    // See http://www.imsglobal.org/spec/lti-nrps/v2p0#lti-1-3-integration.
    /** @var string version 1.0 */
    private const SERVICE_VERSION_1 = '1.0';

    /** @var string version 2.0 */
    private const SERVICE_VERSION_2 = '2.0';

    // Scope that must be requested as part of making a service call.
    // See: http://www.imsglobal.org/spec/lti-nrps/v2p0#lti-1-3-integration.
    /** @var string the scope to request to make service calls. */
    private $servicescope = 'https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly';

    /**
     * The private nrps_info constructor.
     *
     * @param \moodle_url $contextmembershipsurl the memberships URL.
     * @param string[] $serviceversions the supported service versions.
     */
    private function __construct(\moodle_url $contextmembershipsurl, array $serviceversions = [self::SERVICE_VERSION_2]) {
        $this->contextmembershipsurl = $contextmembershipsurl;
        $this->set_service_versions($serviceversions);
    }

    /**
     * Factory method to create a new nrps_info instance.
     *
     * @param \moodle_url $contextmembershipsurl the memberships URL.
     * @param string[] $serviceversions the supported service versions.
     * @return nrps_info the object instance.
     */
    public static function create(\moodle_url $contextmembershipsurl,
            array $serviceversions = [self::SERVICE_VERSION_2]): nrps_info {
        return new self($contextmembershipsurl, $serviceversions);
    }

    /**
     * Check whether the supplied service version is valid or not.
     *
     * @param string $serviceversion the service version to check.
     * @return bool true if valid, false otherwise.
     */
    private function is_valid_service_version(string $serviceversion): bool {
        $validversions = [
            self::SERVICE_VERSION_1,
            self::SERVICE_VERSION_2
        ];

        return in_array($serviceversion, $validversions);
    }

    /**
     * Tries to set the supported service versions for this instance.
     *
     * @param array $serviceversions the service versions to set.
     * @throws \coding_exception if any of the supplied versions are not valid.
     */
    private function set_service_versions(array $serviceversions): void {
        if (empty($serviceversions)) {
            throw new \coding_exception('Service versions array cannot be empty');
        }
        $serviceversions = array_unique($serviceversions);
        foreach ($serviceversions as $serviceversion) {
            if (!$this->is_valid_service_version($serviceversion)) {
                throw new \coding_exception("Invalid Names and Roles service version '{$serviceversion}'");
            }
        }
        $this->serviceversions = $serviceversions;
    }

    /**
     * Get the service URL for this grade service instance.
     *
     * @return \moodle_url the service URL.
     */
    public function get_context_memberships_url(): \moodle_url {
        return $this->contextmembershipsurl;
    }

    /**
     * Get the supported service versions for this grade service instance.
     *
     * @return string[] the array of supported service versions.
     */
    public function get_service_versions(): array {
        return $this->serviceversions;
    }

    /**
     * Get the nrps service scope.
     *
     * @return string the service scope.
     */
    public function get_service_scope(): string {
        return $this->servicescope;
    }
}

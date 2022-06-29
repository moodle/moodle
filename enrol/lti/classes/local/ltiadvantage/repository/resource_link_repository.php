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

namespace enrol_lti\local\ltiadvantage\repository;
use enrol_lti\local\ltiadvantage\entity\deployment;
use enrol_lti\local\ltiadvantage\entity\resource_link;

/**
 * Class resource_link_repository.
 *
 * This class encapsulates persistence logic for \enrol_lti\local\entity\resource_link type objects.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class resource_link_repository {

    /** @var string the name of the table to which the entity will be persisted */
    private $table = 'enrol_lti_resource_link';

    /** @var string the name of the table to which user-entity mappings will have been persisted. */
    private $userresourcelinkmaptable = 'enrol_lti_user_resource_link';

    /**
     * Convert a record into an object and return it.
     *
     * @param \stdClass $record the record from the store.
     * @return resource_link a resource_link object.
     */
    private function resource_link_from_record(\stdClass $record): resource_link {
        $resourcelink = resource_link::create(
            $record->resourcelinkid,
            $record->ltideploymentid,
            $record->resourceid,
            $record->lticontextid,
            $record->id
        );

        if ($record->lineitemsservice) {
            $scopes = [];
            if ($record->lineitemscope) {
                $lineitemscopes = json_decode($record->lineitemscope);
                foreach ($lineitemscopes as $lineitemscope) {
                    $scopes[] = $lineitemscope;
                }
            }
            if ($record->resultscope) {
                $scopes[] = $record->resultscope;
            }
            if ($record->scorescope) {
                $scopes[] = $record->scorescope;
            }
            $resourcelink->add_grade_service(
                new \moodle_url($record->lineitemsservice),
                $record->lineitemservice ? new \moodle_url($record->lineitemservice) : null,
                $scopes
            );
        }

        if ($record->contextmembershipsurl) {
            $resourcelink->add_names_and_roles_service(
                new \moodle_url($record->contextmembershipsurl),
                json_decode($record->nrpsserviceversions)
            );
        }

        return $resourcelink;
    }

    /**
     * Get a list of resource_link objects from a list of records.
     *
     * @param array $records the list of records to transform.
     * @return array the array of resource_link instances.
     */
    private function resource_links_from_records(array $records): array {
        $resourcelinks = [];
        foreach ($records as $record) {
            $resourcelinks[] = $this->resource_link_from_record($record);
        }
        return $resourcelinks;
    }

    /**
     * Get a stdClass object ready for persisting, based on the supplied resource_link object.
     *
     * @param resource_link $resourcelink the resource link instance.
     * @return \stdClass the record.
     */
    private function record_from_resource_link(resource_link $resourcelink): \stdClass {

        $gradeservice = $resourcelink->get_grade_service();
        $nrpservice = $resourcelink->get_names_and_roles_service();

        $record = [
            'id' => $resourcelink->get_id(),
            'resourcelinkid' => $resourcelink->get_resourcelinkid(),
            'ltideploymentid' => $resourcelink->get_deploymentid(),
            'resourceid' => $resourcelink->get_resourceid(),
            'lticontextid' => $resourcelink->get_contextid(),
            'lineitemsservice' => $gradeservice ? $gradeservice->get_lineitemsurl()->out(false) : null,
            'lineitemservice' => null,
            'lineitemscope' => null,
            'resultscope' => $gradeservice ? $gradeservice->get_resultscope() : null,
            'scorescope' => $gradeservice ? $gradeservice->get_scorescope() : null,
            'contextmembershipsurl' => $nrpservice ? $nrpservice->get_context_memberships_url()->out(false) : null,
            'nrpsserviceversions' => $nrpservice ? json_encode($nrpservice->get_service_versions()) : null
        ];

        if ($gradeservice  && ($lineitemurl = $gradeservice->get_lineitemurl())) {
            $record['lineitemservice'] = $lineitemurl->out(false);
        }
        if ($gradeservice && ($lineitemscopes = $gradeservice->get_lineitemscope())) {
            $record['lineitemscope'] = json_encode($lineitemscopes);
        }

        return (object) $record;
    }

    /**
     * Save a resource link instance in the store.
     *
     * @param resource_link $resourcelink the object to save.
     * @return resource_link the saved object.
     */
    public function save(resource_link $resourcelink): resource_link {
        global $DB;
        $id = $resourcelink->get_id();
        $exists = $id ? $this->exists($id) : false;
        if ($id && !$exists) {
            throw new \coding_exception("Cannot save resource_link with id '{$id}'. The record does not exist.");
        }

        $record = $this->record_from_resource_link($resourcelink);
        $timenow = time();
        if ($exists) {
            $record->timemodified = $timenow;
            $DB->update_record($this->table, $record);
        } else {
            $record->timecreated = $record->timemodified = $timenow;
            $id = $DB->insert_record($this->table, $record);
            $record->id = $id;
        }

        return $this->resource_link_from_record($record);
    }

    /**
     * Find and return a resource_link by id.
     *
     * @param int $id the id of the resource_link object.
     * @return resource_link|null the resource_link object, or null if the object cannot be found.
     */
    public function find(int $id): ?resource_link {
        global $DB;
        try {
            $record = $DB->get_record($this->table, ['id' => $id], '*', MUST_EXIST);
            return $this->resource_link_from_record($record);
        } catch (\dml_missing_record_exception $ex) {
            return null;
        }
    }

    /**
     * Get a resource by id, within a given tool deployment.
     *
     * @param deployment $deployment the deployment instance.
     * @param string $resourcelinkid the resourcelinkid from the platform.
     * @return resource_link|null the resource link instance, or null if not found.
     */
    public function find_by_deployment(deployment $deployment, string $resourcelinkid): ?resource_link {
        global $DB;
        try {
            $record = $DB->get_record($this->table, ['ltideploymentid' => $deployment->get_id(),
                'resourcelinkid' => $resourcelinkid], '*', MUST_EXIST);
            return $this->resource_link_from_record($record);
        } catch (\dml_missing_record_exception $ex) {
            return null;
        }
    }

    /**
     * Find resource_link objects based on the resource and a given launching user.
     *
     * @param int $resourceid the local id of the resource (enrol_lti_tools id)
     * @param int $userid the local id of the enrol_lti\local\ltiadvantage\user object
     * @return array an array of resource_links
     */
    public function find_by_resource_and_user(int $resourceid, int $userid): array {
        global $DB;
        $sql = "SELECT r.id, r.resourcelinkid, r.resourceid, r.ltideploymentid, r.lticontextid, r.lineitemsservice,
                       r.lineitemservice, r.lineitemscope, r.resultscope, r.scorescope, r.contextmembershipsurl,
                       r.nrpsserviceversions, r.timecreated, r.timemodified
                  FROM {enrol_lti_resource_link} r
                  JOIN {enrol_lti_user_resource_link} ur
                    ON (r.id = ur.resourcelinkid)
                 WHERE ur.ltiuserid = :ltiuserid
                   AND r.resourceid = :resourceid";
        $records = $DB->get_records_sql($sql, ['ltiuserid' => $userid, 'resourceid' => $resourceid]);
        return $this->resource_links_from_records($records);
    }

    /**
     * Gets all mapped resource links for a given resource.
     *
     * @param int $resourceid the local id of the shared resource.
     * @return array the array of resource_link instances.
     */
    public function find_by_resource(int $resourceid): array {
        global $DB;
        $records = $DB->get_records($this->table, ['resourceid' => $resourceid]);
        return $this->resource_links_from_records($records);
    }

    /**
     * Check whether or not the given resource_link object exists.
     *
     * @param int $id the unique id the resource_link.
     * @return bool true if found, false otherwise.
     */
    public function exists(int $id): bool {
        global $DB;
        return $DB->record_exists($this->table, ['id' => $id]);
    }

    /**
     * Delete a resource_link based on id.
     *
     * @param int $id the id of the resource_link to remove.
     */
    public function delete(int $id) {
        global $DB;
        // First remove all enrol_lti_user_resource_link mappings.
        $DB->delete_records($this->userresourcelinkmaptable, ['resourcelinkid' => $id]);

        // And the resource_link itself.
        $DB->delete_records($this->table, ['id' => $id]);
    }

    /**
     * Delete all resource links for a given deployment, as well as any mappings between users and the respective links.
     *
     * @param int $deploymentid the id of the deployment instance.
     */
    public function delete_by_deployment(int $deploymentid): void {
        global $DB;

        // First remove all enrol_lti_user_resource_link mappings.
        $DB->delete_records_select(
            $this->userresourcelinkmaptable,
            "resourcelinkid IN (SELECT id FROM {{$this->table}} WHERE ltideploymentid = :ltideploymentid)",
            ['ltideploymentid' => $deploymentid]
        );

        // And remove the resource_link entries themselves.
        $DB->delete_records($this->table, ['ltideploymentid' => $deploymentid]);
    }

    /**
     * Delete all resource_link instances referring to the resource identified by $resourceid.
     *
     * @param int $resourceid the id of the published resource.
     */
    public function delete_by_resource(int $resourceid) {
        global $DB;

        // First remove all enrol_lti_user_resource_link mappings.
        $DB->delete_records_select(
            $this->userresourcelinkmaptable,
            "resourcelinkid IN (SELECT id FROM {{$this->table}} WHERE resourceid = :resourceid)",
            ['resourceid' => $resourceid]
        );

        // And remove the resource_link entries themselves.
        $DB->delete_records($this->table, ['resourceid' => $resourceid]);
    }
}

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

/**
 * The deployment_repository class.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class deployment_repository {

    /** @var string $deploymenttable the name of table containing deployments. */
    private $deploymenttable = 'enrol_lti_deployment';

    /**
     * Create a valid record from a deployment instance.
     *
     * @param deployment $deployment the deployment.
     * @return \stdClass a compatible record.
     */
    private function record_from_deployment(deployment $deployment): \stdClass {
        $record = (object) [
            'name' => $deployment->get_deploymentname(),
            'deploymentid' => $deployment->get_deploymentid(),
            'platformid' => $deployment->get_registrationid(),
            'legacyconsumerkey' => $deployment->get_legacy_consumer_key()
        ];
        if ($id = $deployment->get_id()) {
            $record->id = $id;
        }
        return $record;
    }

    /**
     * Create a list of deployments based on a list of records.
     *
     * @param array $records an array of deployment records.
     * @return deployment[]
     */
    private function deployments_from_records(array $records): array {
        if (empty($records)) {
            return [];
        }
        return array_map(function($record) {
            return $this->deployment_from_record($record);
        }, $records);
    }

    /**
     * Create a valid deployment from a record.
     *
     * @param \stdClass $record the record.
     * @return deployment the deployment instance.
     */
    private function deployment_from_record(\stdClass $record): deployment {
        $deployment = deployment::create(
            $record->platformid,
            $record->deploymentid,
            $record->name,
            $record->id,
            $record->legacyconsumerkey
        );
        return $deployment;
    }

    /**
     * Save a deployment to the store.
     *
     * @param deployment $deployment the deployment instance to save.
     * @return deployment the saved deployment instance.
     */
    public function save(deployment $deployment): deployment {
        global $DB;
        $id = $deployment->get_id();
        $exists = $id ? $this->exists($id) : false;

        $record = $this->record_from_deployment($deployment);
        $timenow = time();
        if ($exists) {
            $record->timemodified = $timenow;
            $DB->update_record($this->deploymenttable, $record);
        } else {
            $record->timecreated = $record->timemodified = $timenow;
            $id = $DB->insert_record($this->deploymenttable, $record);
            $record->id = $id;
        }

        return $this->deployment_from_record($record);
    }

    /**
     * Find and return a deployment, by id.
     *
     * @param int $id the id of the deployment to find.
     * @return deployment|null
     */
    public function find(int $id): ?deployment {
        global $DB;
        try {
            $record = $DB->get_record($this->deploymenttable, ['id' => $id], '*', MUST_EXIST);
            return $this->deployment_from_record($record);
        } catch (\dml_missing_record_exception $e) {
            return null;
        }
    }

    /**
     * Determine whether a deployment exists in the repository.
     *
     * @param int $id the identifier of the deployment
     * @return bool true if the deployment exists, false otherwise.
     */
    public function exists(int $id): bool {
        global $DB;
        return $DB->record_exists($this->deploymenttable, ['id' => $id]);
    }

    /**
     * Delete a deployment from the store.
     *
     * @param int $id the id of the deployment object to remove.
     */
    public function delete(int $id): void {
        global $DB;
        $DB->delete_records($this->deploymenttable, ['id' => $id]);
    }

    /**
     * Delete all deployments for the given registration.
     *
     * @param int $registrationid the registration id.
     */
    public function delete_by_registration(int $registrationid): void {
        global $DB;
        $DB->delete_records($this->deploymenttable, ['platformid' => $registrationid]);
    }

    /**
     * Return a count of how many deployments exists for a given application_registration.
     *
     * @param int $registrationid the id of the application_registration instance.
     * @return int the number of deployments found.
     */
    public function count_by_registration(int $registrationid): int {
        global $DB;
        return $DB->count_records($this->deploymenttable, ['platformid' => $registrationid]);
    }

    /**
     * Get a deployment based on its deploymentid and a for a given application registration id.
     *
     * @param int $registrationid the id of the application_registration to which the deployment belongs.
     * @param string $deploymentid the deploymentid of the deployment, as set by the platform.
     * @return deployment|null deployment if found, otherwise null.
     */
    public function find_by_registration(int $registrationid, string $deploymentid): ?deployment {
        global $DB;
        try {
            $sql = "SELECT eld.id, eld.name, eld.deploymentid, eld.platformid, eld.legacyconsumerkey
                      FROM {".$this->deploymenttable."} eld
                      JOIN {enrol_lti_app_registration} elar
                        ON (eld.platformid = elar.id)
                     WHERE elar.id = :registrationid
                       AND eld.deploymentid = :deploymentid";
            $params = ['registrationid' => $registrationid, 'deploymentid' => $deploymentid];
            $record = $DB->get_record_sql($sql, $params, MUST_EXIST);
            return $this->deployment_from_record($record);
        } catch (\dml_missing_record_exception $e) {
            return null;
        }
    }

    /**
     * Get all deployments for a given application registration id.
     *
     * @param int $registrationid the id of the application_registration to which the deployment belongs.
     * @return deployment[]|null deployments if found, otherwise null.
     */
    public function find_all_by_registration(int $registrationid): ?array {
        global $DB;

        $sql = "SELECT eld.id, eld.name, eld.deploymentid, eld.platformid, eld.legacyconsumerkey
                  FROM {".$this->deploymenttable."} eld
                  JOIN {enrol_lti_app_registration} elar
                    ON (eld.platformid = elar.id)
                 WHERE elar.id = :registrationid";
        $records = $DB->get_records_sql($sql, ['registrationid' => $registrationid]);
        return $this->deployments_from_records($records);
    }
}

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
use enrol_lti\local\ltiadvantage\entity\application_registration;

/**
 * Class application_registration_repository.
 *
 * @package    enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class application_registration_repository {

    /** @var string $applicationregistrationtable the table containing application registrations. */
    private $applicationregistrationtable = 'enrol_lti_app_registration';

    /**
     * Create an application_registration instance from a record.
     *
     * @param \stdClass $record the record.
     * @return application_registration an application_registration instance.
     */
    private function application_registration_from_record(\stdClass $record): application_registration {

        if ($record->status == application_registration::REGISTRATION_STATUS_INCOMPLETE) {
            $appreg = application_registration::create_draft(
                $record->name,
                $record->uniqueid,
                $record->id
            );
            if (!empty($record->platformid)) {
                $appreg->set_platformid(new \moodle_url($record->platformid));
            }
            if (!empty($record->clientid)) {
                $appreg->set_clientid($record->clientid);
            }
            if (!empty($record->authenticationrequesturl)) {
                $appreg->set_authenticationrequesturl(new \moodle_url($record->authenticationrequesturl));
            }
            if (!empty($record->jwksurl)) {
                $appreg->set_jwksurl(new \moodle_url($record->jwksurl));
            }
            if (!empty($record->accesstokenurl)) {
                $appreg->set_accesstokenurl(new \moodle_url($record->accesstokenurl));
            }
        } else if ($record->status == application_registration::REGISTRATION_STATUS_COMPLETE) {
            $appreg = application_registration::create(
                $record->name,
                $record->uniqueid,
                new \moodle_url($record->platformid),
                $record->clientid,
                new \moodle_url($record->authenticationrequesturl),
                new \moodle_url($record->jwksurl),
                new \moodle_url($record->accesstokenurl),
                $record->id
            );
        }
        return $appreg;
    }

    /**
     * Get an array of application_registration instances from a set of records.
     *
     * @param \stdClass[] $records the array of records.
     * @return array|application_registration[] the array of object instances.
     */
    private function application_registrations_from_records(array $records): array {
        if (empty($records)) {
            return [];
        }
        return array_map(function($record) {
            return $this->application_registration_from_record($record);
        }, $records);
    }

    /**
     * Convert the application_registration object into a stdClass for use with the data store.
     *
     * @param application_registration $appregistration the app registration.
     * @return \stdClass the record.
     */
    private function record_from_application_registration(application_registration $appregistration): \stdClass {
        $appregistrationrecord = [
            'name' => $appregistration->get_name(),
            'uniqueid' => $appregistration->get_uniqueid(),
            'status' => $appregistration->is_complete() ? application_registration::REGISTRATION_STATUS_COMPLETE
                : application_registration::REGISTRATION_STATUS_INCOMPLETE
        ];

        $platformid = $appregistration->get_platformid();
        $clientid = $appregistration->get_clientid();
        $authrequesturl = $appregistration->get_authenticationrequesturl();
        $jwksurl = $appregistration->get_jwksurl();
        $accesstokenurl = $appregistration->get_accesstokenurl();

        $appregistrationrecord['platformid'] = !is_null($platformid) ? $platformid->out(false) : null;
        $appregistrationrecord['clientid'] = $clientid;
        $appregistrationrecord['authenticationrequesturl'] = !is_null($authrequesturl) ? $authrequesturl->out(false) : null;
        $appregistrationrecord['jwksurl'] = !is_null($jwksurl) ? $jwksurl->out(false) : null;
        $appregistrationrecord['accesstokenurl'] = !is_null($accesstokenurl) ? $accesstokenurl->out(false) : null;

        if ($platformid && $clientid) {
            $indexhash = $this->get_unique_index_hash($appregistration->get_platformid()->out(false),
                $appregistration->get_clientid());
            $appregistrationrecord['platformclienthash'] = $indexhash;
        }

        if ($platformid) {
            $indexhash = $this->get_unique_index_hash($appregistration->get_platformid()->out(false),
                $appregistration->get_uniqueid());
            $appregistrationrecord['platformuniqueidhash'] = $indexhash;
        }

        if ($id = $appregistration->get_id()) {
            $appregistrationrecord['id'] = $id;
        }

        return (object) $appregistrationrecord;
    }

    /**
     * Gets a hash of the {platformid, clientid} tuple for use in indexing purposes.
     *
     * @param string $platformid the platformid of the registration.
     * @param string $clientid the clientid of the registration
     * @return string a SHA256 hash.
     */
    private function get_unique_index_hash(string $platformid, string $clientid): string {
        return hash('sha256', $platformid . ':' . $clientid);
    }

    /**
     * Find a registration by id.
     *
     * @param int $id the id of the application registration.
     * @return null|application_registration the registration object if found, otherwise null.
     */
    public function find(int $id): ?application_registration {
        global $DB;
        try {
            $record = $DB->get_record($this->applicationregistrationtable, ['id' => $id], '*', MUST_EXIST);
            return $this->application_registration_from_record($record);
        } catch (\dml_missing_record_exception $e) {
            return null;
        }
    }

    /**
     * Get all app registrations in the repository.
     *
     * @return application_registration[] the array of application registration instances.
     */
    public function find_all(): array {
        global $DB;
        return $this->application_registrations_from_records($DB->get_records($this->applicationregistrationtable));
    }

    /**
     * Find a registration by its unique {platformid, uniqueid} tuple.
     *
     * @param string $platformid the url of the platform (the issuer).
     * @param string $uniqueid the locally uniqueid of the tool registration.
     * @return application_registration|null application registration instance if found, else null.
     */
    public function find_by_platform_uniqueid(string $platformid, string $uniqueid): ?application_registration {
        global $DB;
        try {
            $indexhash = $this->get_unique_index_hash($platformid, $uniqueid);
            $record = $DB->get_record($this->applicationregistrationtable, ['platformuniqueidhash' => $indexhash], '*',
                MUST_EXIST);
            return $this->application_registration_from_record($record);
        } catch (\dml_missing_record_exception $e) {
            return null;
        }
    }

    /**
     * Find a registration by its uniqueid.
     *
     * @param string $uniqueid the uniqueid identifying the registration.
     * @return application_registration|null application_registration instance if found, else null.
     */
    public function find_by_uniqueid(string $uniqueid): ?application_registration {
        global $DB;
        try {
            $record = $DB->get_record($this->applicationregistrationtable, ['uniqueid' => $uniqueid], '*', MUST_EXIST);
            return $this->application_registration_from_record($record);
        } catch (\dml_missing_record_exception $e) {
            return null;
        }
    }

    /**
     * Find a registration by its unique {platformid, clientid} tuple.
     *
     * @param string $platformid the url of the platform (the issuer).
     * @param string $clientid the client_id of the tool registration on the platform.
     * @return application_registration|null application registration instance if found, else null.
     */
    public function find_by_platform(string $platformid, string $clientid): ?application_registration {
        global $DB;
        try {
            $indexhash = $this->get_unique_index_hash($platformid, $clientid);
            $record = $DB->get_record($this->applicationregistrationtable, ['platformclienthash' => $indexhash], '*',
                MUST_EXIST);
            return $this->application_registration_from_record($record);
        } catch (\dml_missing_record_exception $e) {
            return null;
        }
    }

    /**
     * Find an application_registration corresponding to the local id of a given tool deployment.
     *
     * @param int $deploymentid the local id of the tool deployment object.
     * @return application_registration|null the application_registration instance or null if not found.
     */
    public function find_by_deployment(int $deploymentid): ?application_registration {
        global $DB;
        try {
            $sql = "SELECT a.id, a.name, a.platformid, a.clientid, a.authenticationrequesturl, a.jwksurl,
                           a.accesstokenurl, a.uniqueid, a.status, a.timecreated, a.timemodified
                      FROM {enrol_lti_app_registration} a
                      JOIN {enrol_lti_deployment} d
                        ON (d.platformid = a.id)
                     WHERE d.id = :id";
            $record = $DB->get_record_sql($sql, ['id' => $deploymentid], MUST_EXIST);
            return $this->application_registration_from_record($record);
        } catch (\dml_missing_record_exception $e) {
            return null;
        }
    }

    /**
     * Save an application_registration instance to the store.
     *
     * @param application_registration $appregistration the application registration instance.
     * @return application_registration the saved application registration instance.
     */
    public function save(application_registration $appregistration): application_registration {
        global $DB;
        $id = $appregistration->get_id();
        $exists = $id ? $this->exists($id) : false;

        $record = $this->record_from_application_registration($appregistration);
        $timenow = time();
        if ($exists) {
            $record->timemodified = $timenow;
            $DB->update_record($this->applicationregistrationtable, $record);
        } else {
            $record->timecreated = $record->timemodified = $timenow;
            $appregid = $DB->insert_record($this->applicationregistrationtable, $record);
            $record->id = $appregid;
        }

        return $this->application_registration_from_record($record);
    }

    /**
     * Report whether an application_registration with id $id exists or not.
     *
     * @param int $appregid the id of the application_registration
     * @return bool true if the object exists, false otherwise.
     */
    public function exists(int $appregid): bool {
        global $DB;
        return $DB->record_exists($this->applicationregistrationtable, ['id' => $appregid]);
    }

    /**
     * Delete the application_registration identified by id.
     *
     * @param int $id the id of the object to delete.
     */
    public function delete(int $id): void {
        global $DB;
        $DB->delete_records($this->applicationregistrationtable, ['id' => $id]);
    }
}

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
use enrol_lti\local\ltiadvantage\entity\context;

/**
 * Class context_repository.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class context_repository {

    /** @var string the name of the table storing object data. */
    private $contexttable = 'enrol_lti_context';

    /**
     * Generate a context instance from a record.
     *
     * @param \stdClass $record the record.
     * @return context the context instance.
     */
    private function context_from_record(\stdClass $record): context {
        $context = context::create(
            $record->ltideploymentid,
            $record->contextid,
            json_decode($record->type),
            $record->id
        );
        return $context;
    }

    /**
     * Generate a record from a context instance.
     *
     * @param context $context the context instance.
     * @return \stdClass the resulting record.
     */
    private function record_from_context(context $context): \stdClass {

        $record = [
            'contextid' => $context->get_contextid(),
            'ltideploymentid' => $context->get_deploymentid(),
            'type' => json_encode($context->get_types()),
        ];

        if ($id = $context->get_id()) {
            $record['id'] = $id;
        }

        return (object) $record;
    }

    /**
     * Save the context to the store.
     *
     * @param context $context the context to save.
     * @return context the saved context instance.
     */
    public function save(context $context): context {
        global $DB;
        $id = $context->get_id();
        $exists = $id ? $this->exists($id) : false;

        $record = $this->record_from_context($context);
        $timenow = time();
        if ($exists) {
            $record->timemodified = $timenow;
            $DB->update_record($this->contexttable, $record);
        } else {
            $record->timecreated = $record->timemodified = $timenow;
            $id = $DB->insert_record($this->contexttable, $record);
            $record->id = $id;
        }

        return $this->context_from_record($record);
    }

    /**
     * Find a context by id.
     *
     * @param int $id the id of the instance.
     * @return context|null the context, if found, else null.
     */
    public function find(int $id): ?context {
        global $DB;
        try {
            $record = $DB->get_record($this->contexttable, ['id' => $id], '*', MUST_EXIST);
            return $this->context_from_record($record);
        } catch (\dml_missing_record_exception $e) {
            return null;
        }
    }

    /**
     * Find a context by it's platform-issued context id string.
     *
     * @param string $contextid the id of the context on the platform.
     * @param int $deploymentid the id of the local deployment instance in which the contextid is unique.
     * @return context|null the context instance, if found, else null.
     */
    public function find_by_contextid(string $contextid, int $deploymentid): ?context {
        global $DB;
        try {
            $record = $DB->get_record($this->contexttable,
                ['contextid' => $contextid, 'ltideploymentid' => $deploymentid], '*', MUST_EXIST);
            return $this->context_from_record($record);
        } catch (\dml_missing_record_exception $e) {
            return null;
        }
    }

    /**
     * Check whether the context identified by 'id' exists in the store.
     *
     * @param int $id the id of the instance to check.
     * @return bool true if found, false otherwise.
     */
    public function exists(int $id): bool {
        global $DB;
        return $DB->record_exists($this->contexttable, ['id' => $id]);
    }

    /**
     * Delete the context identified by 'id' from the store.
     *
     * @param int $id the id of context to delete.
     */
    public function delete(int $id): void {
        global $DB;
        $DB->delete_records($this->contexttable, ['id' => $id]);
    }

    /**
     * Delete all contexts under a given deployment.
     *
     * @param int $deploymentid the id of the local deployment instance.
     */
    public function delete_by_deployment(int $deploymentid): void {
        global $DB;
        $DB->delete_records($this->contexttable, ['ltideploymentid' => $deploymentid]);
    }
}

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

namespace core_xapi;

use core_xapi\local\state;

/**
 * The state store manager.
 *
 * @package    core_xapi
 * @since      Moodle 4.2
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class state_store {

    /** @var string component name in frankenstyle. */
    protected $component;

    /**
     * Constructor for a xAPI handler base class.
     *
     * @param string $component the component name
     */
    public function __construct(string $component) {
        $this->component = $component;
    }

    /**
     * Convert the xAPI activity ID into an item ID integer.
     *
     * @throws xapi_exception if the activity id is not numeric.
     * @param string $activityid the provided activity ID
     * @return int
     */
    protected function activity_id_to_item_id(string $activityid): int {
        if (!is_numeric($activityid)) {
            throw new xapi_exception('The state store can only store numeric activity IDs.');
        }
        return intval($activityid);
    }

    /**
     * Delete any extra state data stored in the database.
     *
     * This method will be called only if the state is accepted by validate_state.
     *
     * Plugins may override this method add extra clean up tasks to the deletion.
     *
     * @param state $state
     * @return bool if the state is removed
     */
    public function delete(state $state): bool {
        global $DB;
        $data = [
            'component' => $this->component,
            'userid' => $state->get_user()->id,
            'itemid' => $this->activity_id_to_item_id($state->get_activity_id()),
            'stateid' => $state->get_state_id(),
            'registration' => $state->get_registration(),
        ];
        return $DB->delete_records('xapi_states', $data);
    }

    /**
     * Get a state object from the database.
     *
     * This method will be called only if the state is accepted by validate_state.
     *
     * Plugins may override this method if they store some data in different tables.
     *
     * @param state $state
     * @return state|null the state
     */
    public function get(state $state): ?state {
        global $DB;
        $data = [
            'component' => $this->component,
            'userid' => $state->get_user()->id,
            'itemid' => $this->activity_id_to_item_id($state->get_activity_id()),
            'stateid' => $state->get_state_id(),
            'registration' => $state->get_registration(),
        ];
        $record = $DB->get_record('xapi_states', $data);
        if ($record) {
            $statedata = null;
            if ($record->statedata !== null) {
                $statedata = json_decode($record->statedata, null, 512, JSON_THROW_ON_ERROR);
            }
            $state->set_state_data($statedata);
            return $state;
        }

        return null;
    }

    /**
     * Inserts an state object into the database.
     *
     * This method will be called only if the state is accepted by validate_state.
     *
     * Plugins may override this method if they store some data in different tables.
     *
     * @param state $state
     * @return bool if the state is inserted/updated
     */
    public function put(state $state): bool {
        global $DB;
        $data = [
            'component' => $this->component,
            'userid' => $state->get_user()->id,
            'itemid' => $this->activity_id_to_item_id($state->get_activity_id()),
            'stateid' => $state->get_state_id(),
            'registration' => $state->get_registration(),
        ];
        $record = $DB->get_record('xapi_states', $data) ?: (object) $data;
        if (isset($record->id)) {
            $record->statedata = json_encode($state->jsonSerialize());
            $record->timemodified = time();
            $result = $DB->update_record('xapi_states', $record);
        } else {
            $data['statedata'] = json_encode($state->jsonSerialize());
            $data['timecreated'] = time();
            $data['timemodified'] = $data['timecreated'];
            $result = $DB->insert_record('xapi_states', $data);
        }
        return $result ? true : false;
    }

    /**
     * Reset all states from the component.
     * The given parameters are filters to decide the states to reset. If no parameters are defined, the only filter applied
     * will be the component.
     *
     * Plugins may override this method if they store some data in different tables.
     *
     * @param string|null $itemid
     * @param int|null $userid
     * @param string|null $stateid
     * @param string|null $registration
     */
    public function reset(
        ?string $itemid = null,
        ?int $userid = null,
        ?string $stateid = null,
        ?string $registration = null
    ): void {
        global $DB;

        $data = [
            'component' => $this->component,
        ];
        if ($itemid) {
            $data['itemid'] = $this->activity_id_to_item_id($itemid);
        }
        if ($userid) {
            $data['userid'] = $userid;
        }
        if ($stateid) {
            $data['stateid'] = $stateid;
        }
        if ($registration) {
            $data['registration'] = $registration;
        }
        $DB->set_field('xapi_states', 'statedata', null, $data);
    }

    /**
     * Remove all states from the component
     * The given parameters are filters to decide the states to wipe. If no parameters are defined, the only filter applied
     * will be the component.
     *
     * Plugins may override this method if they store some data in different tables.
     *
     * @param string|null $itemid
     * @param int|null $userid
     * @param string|null $stateid
     * @param string|null $registration
     */
    public function wipe(
        ?string $itemid = null,
        ?int $userid = null,
        ?string $stateid = null,
        ?string $registration = null
    ): void {
        global $DB;
        $data = [
            'component' => $this->component,
        ];
        if ($itemid) {
            $data['itemid'] = $this->activity_id_to_item_id($itemid);
        }
        if ($userid) {
            $data['userid'] = $userid;
        }
        if ($stateid) {
            $data['stateid'] = $stateid;
        }
        if ($registration) {
            $data['registration'] = $registration;
        }
        $DB->delete_records('xapi_states', $data);
    }

    /**
     * Get all state ids from a specific activity and agent.
     *
     * Plugins may override this method if they store some data in different tables.
     *
     * @param string|null $itemid
     * @param int|null $userid
     * @param string|null $registration
     * @param int|null $since filter ids updated since a specific timestamp
     * @return string[] the state ids values
     */
    public function get_state_ids(
        ?string $itemid = null,
        ?int $userid = null,
        ?string $registration = null,
        ?int $since = null,
    ): array {
        global $DB;
        $select = 'component = :component';
        $params = [
            'component' => $this->component,
        ];
        if ($itemid) {
            $select .= ' AND itemid = :itemid';
            $params['itemid'] = $this->activity_id_to_item_id($itemid);
        }
        if ($userid) {
            $select .= ' AND userid = :userid';
            $params['userid'] = $userid;
        }
        if ($registration) {
            $select .= ' AND registration = :registration';
            $params['registration'] = $registration;
        }
        if ($since) {
            $select .= ' AND timemodified > :since';
            $params['since'] = $since;
        }
        return $DB->get_fieldset_select('xapi_states', 'stateid', $select, $params, '');
    }

    /**
     * Execute a state store clean up.
     *
     * Plugins can override this methos to provide an alternative clean up logic.
     */
    public function cleanup(): void {
        global $DB;
        $xapicleanupperiod = get_config('core', 'xapicleanupperiod');
        if (empty($xapicleanupperiod)) {
            return;
        }
        $todelete = time() - $xapicleanupperiod;
        $DB->delete_records_select(
            'xapi_states',
            'component = :component AND timemodified < :todelete',
            ['component' => $this->component, 'todelete' => $todelete]
        );
    }
}

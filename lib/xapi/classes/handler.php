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
use core_xapi\local\statement;
use core_xapi\xapi_exception;

/**
 * Class handler handles basic xAPI statements and states.
 *
 * @package    core_xapi
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class handler {

    /** @var string component name in frankenstyle. */
    protected $component;

    /** @var state_store the state_store instance. */
    protected $statestore;

    /**
     * Constructor for a xAPI handler base class.
     *
     * @param string $component the component name
     */
    final protected function __construct(string $component) {
        $this->component = $component;
        $this->statestore = $this->get_state_store();
    }

    /**
     * Returns the xAPI handler of a specific component.
     *
     * @param string $component the component name in frankenstyle.
     * @return handler|null a handler object or null if none found.
     * @throws xapi_exception
     */
    final public static function create(string $component): self {
        if (self::supports_xapi($component)) {
            $classname = "\\$component\\xapi\\handler";
            return new $classname($component);
        }
        throw new xapi_exception('Unknown handler');
    }

    /**
     * Whether a component supports (and implements) xAPI.
     *
     * @param string $component the component name in frankenstyle.
     * @return bool true if the given component implements xAPI handler; false otherwise.
     */
    final public static function supports_xapi(string $component): bool {
        $classname = "\\$component\\xapi\\handler";
        return class_exists($classname);
    }

    /**
     * Convert a statement object into a Moodle xAPI Event.
     *
     * If a statement is accepted by validate_statement the component must provide a event
     * to handle that statement, otherwise the statement will be rejected.
     *
     * Note: this method must be overridden by the plugins which want to use xAPI.
     *
     * @param statement $statement
     * @return \core\event\base|null a Moodle event to trigger
     */
    abstract public function statement_to_event(statement $statement): ?\core\event\base;

    /**
     * Return true if group actor is enabled.
     *
     * Note: this method must be overridden by the plugins which want to
     * use groups in statements.
     *
     * @return bool
     */
    public function supports_group_actors(): bool {
        return false;
    }

    /**
     * Process a bunch of statements sended to a specific component.
     *
     * @param statement[] $statements an array with all statement to process.
     * @return int[] return an specifying what statements are being stored.
     */
    public function process_statements(array $statements): array {
        $result = [];
        foreach ($statements as $key => $statement) {
            try {
                // Ask the plugin to convert into an event.
                $event = $this->statement_to_event($statement);
                if ($event) {
                    $event->trigger();
                    $result[$key] = true;
                } else {
                    $result[$key] = false;
                }
            } catch (\Exception $e) {
                $result[$key] = false;
            }
        }
        return $result;
    }

    /**
     * Validate a xAPI state.
     *
     * Check if the state is valid for this handler.
     *
     * This method is used also for the state get requests so the validation
     * cannot rely on having state data.
     *
     * Note: this method must be overridden by the plugins which want to use xAPI states.
     *
     * @param state $state
     * @return bool if the state is valid or not
     */
    abstract protected function validate_state(state $state): bool;

    /**
     * Process a state save request.
     *
     * @param state $state the state object
     * @return bool if the state can be saved
     */
    public function save_state(state $state): bool {
        if (!$this->validate_state($state)) {
            throw new xapi_exception('The state is not accepted, so it cannot be saved');
        }
        return $this->statestore->put($state);
    }

    /**
     * Process a state save request.
     *
     * @param state $state the state object
     * @return state|null the resulting loaded state
     */
    public function load_state(state $state): ?state {
        if (!$this->validate_state($state)) {
            throw new xapi_exception('The state is not accepted, so it cannot be loaded');
        }
        $state = $this->statestore->get($state);
        return $state;
    }

    /**
     * Process a state delete request.
     *
     * @param state $state the state object
     * @return bool if the deletion is successful
     */
    public function delete_state(state $state): bool {
        if (!$this->validate_state($state)) {
            throw new xapi_exception('The state is not accepted, so it cannot be deleted');
        }
        return $this->statestore->delete($state);
    }

    /**
     * Delete all states from this component.
     *
     * @param string|null $itemid
     * @param int|null $userid
     * @param string|null $stateid
     * @param string|null $registration
     */
    public function wipe_states(
        ?string $itemid = null,
        ?int $userid = null,
        ?string $stateid = null,
        ?string $registration = null
    ): void {
        $this->statestore->wipe($itemid, $userid, $stateid, $registration);
    }

    /**
     * Reset all states from this component.
     *
     * @param string|null $itemid
     * @param int|null $userid
     * @param string|null $stateid
     * @param string|null $registration
     */
    public function reset_states(
        ?string $itemid = null,
        ?int $userid = null,
        ?string $stateid = null,
        ?string $registration = null
    ): void {
        $this->statestore->reset($itemid, $userid, $stateid, $registration);
    }

    /**
     * Return a valor state store for this component.
     *
     * Plugins may override this method is they want to use a different
     * state store class.
     * @return state_store the store to use to get/put/delete states.
     */
    public function get_state_store(): state_store {
        return new state_store($this->component);
    }
}

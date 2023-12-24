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

namespace core_xapi\local\helper;

use core_component;
use core_xapi\local\state;
use core_xapi\local\statement\item_agent;
use core_xapi\xapi_exception;
use JsonException;
use stdClass;

/**
 * State trait helper, with common methods.
 *
 * @package    core_xapi
 * @since      Moodle 4.2
 * @copyright  2023 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait state_trait {
    /**
     * Check component name.
     *
     * Note: this function is separated mainly for testing purposes to
     * be overridden to fake components.
     *
     * @throws xapi_exception if component is not available
     * @param string $component component name
     */
    protected static function validate_component(string $component): void {
        // Check that $component is a real component name.
        $dir = core_component::get_component_directory($component);
        if (!$dir) {
            throw new xapi_exception("Component $component not available.");
        }
    }

    /**
     * Convert a JSON agent into a valid item_agent.
     *
     * @throws xapi_exception if JSON cannot be parsed
     * @param string $agentjson JSON encoded agent structure
     * @return item_agent the agent
     */
    private static function get_agent_from_json(string $agentjson): item_agent {
        try {
            $agentdata = json_decode($agentjson, null, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new xapi_exception('No agent detected');
        }
        return item_agent::create_from_data($agentdata);
    }

    /**
     * Check that $USER is actor in state.
     *
     * @param state $state The state
     * @return bool if $USER is actor of the state
     */
    private static function check_state_user(state $state): bool {
        global $USER;
        $user = $state->get_user();
        if ($user->id != $USER->id) {
            return false;
        }
        return true;
    }

    /**
     * Convert the state data JSON into valid object.
     *
     * @throws xapi_exception if JSON cannot be parsed
     * @param string $statedatajson JSON encoded structure
     * @return stdClass the state data structure
     */
    private static function get_statedata_from_json(string $statedatajson): stdClass {
        try {
            // Force it to be an object, because some statedata might be sent as array instead of JSON.
            $statedata = json_decode($statedatajson, false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new xapi_exception('Invalid state data format');
        }
        return $statedata;
    }
}

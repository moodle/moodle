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

namespace core_xapi\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use core_xapi\handler;
use core_xapi\iri;
use core_xapi\xapi_exception;

/**
 * This is the external API for generic xAPI states deletion.
 *
 * @package    core_xapi
 * @since      Moodle 4.3
 * @copyright  2023 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_states extends external_api {

    use \core_xapi\local\helper\state_trait;

    /**
     * Process a state delete request.
     *
     * @param string $component The component name in frankenstyle.
     * @param string $activityiri The activity IRI.
     * @param string $agent The agent JSON.
     * @param string|null $registration The xAPI registration UUID.
     * @return void
     */
    public static function execute(
        string $component,
        string $activityiri,
        string $agent,
        ?string $registration = null,
    ): void {
        global $USER;

        [
            'component' => $component,
            'activityId' => $activityiri,
            'agent' => $agent,
            'registration' => $registration,
        ] = self::validate_parameters(self::execute_parameters(), [
            'component' => $component,
            'activityId' => $activityiri,
            'agent' => $agent,
            'registration' => $registration,
        ]);

        static::validate_component($component);

        $handler = handler::create($component);

        $activityid = iri::extract($activityiri, 'activity');
        $agent = self::get_agent_from_json($agent);
        $user = $agent->get_user();
        if ($user->id != $USER->id) {
            throw new xapi_exception('State agent is not the current user');
        }
        $handler->wipe_states($activityid, $user->id, null, $registration);
    }

    /**
     * Parameters for execute.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'component' => new external_value(PARAM_COMPONENT, 'Component name'),
            'activityId' => new external_value(PARAM_URL, 'xAPI activity ID IRI'),
            'agent' => new external_value(PARAM_RAW, 'The xAPI agent json'),
            'registration' => new external_value(PARAM_ALPHANUMEXT, 'The xAPI registration UUID', VALUE_DEFAULT, null)
        ]);
    }

    /**
     * Return for execute.
     */
    public static function execute_returns() {
        return null;
    }
}

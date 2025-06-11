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

use core_xapi\handler;
use core_xapi\xapi_exception;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_value;
use core_xapi\iri;
use core_xapi\local\statement\item_agent;

/**
 * This is the external API for generic xAPI get all states ids.
 *
 * @package    core_xapi
 * @since      Moodle 4.2
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_states extends external_api {

    use \core_xapi\local\helper\state_trait;

    /**
     * Parameters for execute
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'component' => new external_value(PARAM_COMPONENT, 'Component name'),
            'activityId' => new external_value(PARAM_URL, 'xAPI activity ID IRI'),
            'agent' => new external_value(PARAM_RAW, 'The xAPI agent json'),
            'registration' => new external_value(PARAM_ALPHANUMEXT, 'The xAPI registration UUID', VALUE_DEFAULT, null),
            'since' => new external_value(PARAM_TEXT, 'Filter ids stored since the timestamp (exclusive)', VALUE_DEFAULT, null),
        ]);
    }

    /**
     * Process a get states request.
     *
     * @param string $component The component name in frankenstyle.
     * @param string $activityiri The activity IRI.
     * @param string $agent The agent JSON.
     * @param string|null $registration The xAPI registration UUID.
     * @param string|null $since A ISO 8601 timestamps or a numeric timestamp.
     * @return array the list of the stored state ids
     */
    public static function execute(
        string $component,
        string $activityiri,
        string $agent,
        ?string $registration = null,
        ?string $since = null
    ): array {
        global $USER;

        [
            'component' => $component,
            'activityId' => $activityiri,
            'agent' => $agent,
            'registration' => $registration,
            'since' => $since,
        ] = self::validate_parameters(self::execute_parameters(), [
            'component' => $component,
            'activityId' => $activityiri,
            'agent' => $agent,
            'registration' => $registration,
            'since' => $since,
        ]);

        static::validate_component($component);

        $handler = handler::create($component);

        $agent = self::get_agent_from_json($agent);
        $user = $agent->get_user();

        if ($user->id !== $USER->id) {
            throw new xapi_exception('State agent is not the current user');
        }

        $activityid = iri::extract($activityiri, 'activity');
        $createdsince = self::convert_since_param_to_timestamp($since);
        $store = $handler->get_state_store();

        return $store->get_state_ids(
            $activityid,
            $user->id,
            $registration,
            $createdsince
        );
    }

    /**
     * Convert the xAPI since param into a Moodle integer timestamp.
     *
     * According to xAPI standard, the "since" param must follow the ISO 8601
     * format. However, because Moodle do not use this format, we accept both
     * numeric timestamp and ISO 8601.
     *
     * @param string|null $since A ISO 8601 timestamps or a numeric timestamp.
     * @return null|int the resulting timestamp or null if since is null.
     */
    private static function convert_since_param_to_timestamp(?string $since): ?int {
        if ($since === null) {
            return null;
        }
        if (is_numeric($since)) {
            return intval($since);
        }
        try {
            $datetime = new \DateTime($since);
            return $datetime->getTimestamp();
        } catch (\Exception $exception) {
            throw new xapi_exception("Since param '$since' is not in ISO 8601 or a numeric timestamp format");
        }
    }

    /**
     * Return for execute.
     *
     * @return external_multiple_structure
     */
    public static function execute_returns(): external_multiple_structure {
        return new external_multiple_structure(
            new external_value(PARAM_RAW, 'State ID'),
            'List of state Ids'
        );
    }
}

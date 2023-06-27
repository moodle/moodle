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

/**
 * This is the external API for generic xAPI handling.
 *
 * @package    core_xapi
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_xapi\external;

use core_xapi\local\statement;
use core_xapi\handler;
use core_xapi\xapi_exception;
use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;
use external_warnings;
use core_component;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir .'/externallib.php');

/**
 * This is the external API for generic xAPI handling.
 *
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class post_statement extends external_api {

    /**
     * Parameters for execute
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters(
            [
                'component' => new external_value(PARAM_COMPONENT, 'Component name', VALUE_REQUIRED),
                'requestjson' => new external_value(PARAM_RAW, 'json object with all the statements to post', VALUE_REQUIRED)
            ]
        );
    }

    /**
     * Process a statement post request.
     *
     * @param string $component component name (frankenstyle)
     * @param string $requestjson json object with all the statements to post
     * @return bool[] storing acceptance of every statement
     */
    public static function execute(string $component, string $requestjson): array {

        $params = self::validate_parameters(self::execute_parameters(), array(
            'component' => $component,
            'requestjson' => $requestjson,
        ));
        $component = $params['component'];
        $requestjson = $params['requestjson'];

        static::validate_component($component);

        $handler = handler::create($component);

        $statements = self::get_statements_from_json($requestjson);

        if (!self::check_statements_users($statements, $handler)) {
            throw new xapi_exception('Statements actor is not the current user');
        }

        $result = $handler->process_statements($statements);

        // In case no statement is processed, an error must be returned.
        if (count(array_filter($result)) == 0) {
            throw new xapi_exception('No statement can be processed.');
        }
        return $result;
    }

    /**
     * Return for execute.
     */
    public static function execute_returns() {
        return new external_multiple_structure(
            new external_value(PARAM_BOOL, 'If the statement is accepted'),
            'List of statements storing acceptance results'
        );
    }

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
     * Convert mulitple types of statement request into an array of statements.
     *
     * @throws xapi_exception if JSON cannot be parsed
     * @param string $requestjson json encoded statements structure
     * @return statement[] array of statements
     */
    private static function get_statements_from_json(string $requestjson): array {
        $request = json_decode($requestjson);
        if ($request === null) {
            throw new xapi_exception('JSON error: '.json_last_error_msg());
        }
        $result = [];
        if (is_array($request)) {
            foreach ($request as $data) {
                $result[] = statement::create_from_data($data);
            }
        } else {
            $result[] = statement::create_from_data($request);
        }
        if (empty($result)) {
            throw new xapi_exception('No statements detected');
        }
        return $result;
    }

    /**
     * Check that $USER is actor in all statements.
     *
     * @param statement[] $statements array of statements
     * @param handler $handler specific xAPI handler
     * @return bool if $USER is actor in all statements
     */
    private static function check_statements_users(array $statements, handler $handler): bool {
        global $USER;

        foreach ($statements as $statement) {
            if ($handler->supports_group_actors()) {
                $users = $statement->get_all_users();
                if (!isset($users[$USER->id])) {
                    return false;
                }
            } else {
                $user = $statement->get_user();
                if ($user->id != $USER->id) {
                    return false;
                }
            }
        }
        return true;
    }
}

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
 * One Roster Enrolment Client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\v1p1\endpoints;

use enrol_oneroster\client_helper;
use enrol_oneroster\local\endpoints\rostering as parent_endpoint;

/**
 * One Roster Endpoint for the v1p1 client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rostering extends parent_endpoint {

    // @codingStandardsIgnoreStart UpperCaseConstantNameSniff

    /** @var string Endpoint name to fetch classes for a user */
    const getClassesForUser = 'getClassesForUser';

    // @codingStandardsIgnoreEnd UpperCaseConstantNameSniff

    /** @var array List of commands and their configuration */
    protected static $commands = [
        self::getClassesForUser => [
            'url' => '/users/:user_id/classes',
            'method' => client_helper::GET,
            'description' => 'Return the collection of classes attended by this user.',
            'collection' => [
                'classes',
            ],
        ],
    ];

    /**
     * Get details of the required scope for the One Roster OAuth2 client.
     *
     * @return  array
     */
    public static function get_required_scopes(): array {
        return [
            'https://purl.imsglobal.org/spec/or/v1p1/scope/roster.readonly',
        ];
    }

    /**
     * Get the URL for the specified endpoint.
     *
     * @param   string $baseurl
     * @param   string $endpoint
     * @return  string
     */
    public function get_url_for_command(string $baseurl, string $endpoint): string {
        return "{$baseurl}{$endpoint}";
    }

    /**
     * Get the command data for the specified command.
     *
     * @param   string $command
     * @return  array
     */
    protected static function get_command_data(string $command): array {
        if (array_key_exists($command, self::$commands)) {
            return self::$commands[$command];
        }

        return parent::get_command_data($command);
    }

    /**
     * Get the list of all commands.
     *
     * @return  array
     */
    public static function get_all_commands(): array {
        return array_merge(
            parent::get_all_commands(),
            self::$commands
        );
    }
}

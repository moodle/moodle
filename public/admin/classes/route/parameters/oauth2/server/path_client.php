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

namespace core_admin\route\parameters\oauth2\server;

use core\exception\not_found_exception;
use core\router\schema\example;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Client parameter referenced in the path.
 *
 * @package    core_admin
 * @copyright  2026 Mihail Gehoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class path_client extends \core\router\schema\parameters\path_parameter implements
    \core\router\schema\parameters\mapped_property_parameter,
    \core\router\schema\referenced_object
{
    /**
     * Create a new client parameter.
     *
     * @param string $name The name of the parameter to use for the course ID
     * @param mixed ...$extra Additional arguments
     */
    public function __construct(string $name = 'client', ...$extra) {
        $extra['name'] = $name;
        $extra['type'] = \core\param::INT;
        $extra['description'] = <<<EOF
        The client ID, which is the ID of the OAuth2 Server client in the database.
        EOF;
        $extra['examples'] = [
            new example(
                name: 'A client ID',
                value: 54,
            ),
        ];

        parent::__construct(...$extra);
    }

    /**
     * Get the client object for the given identifier.
     *
     * @param string $value A client ID value from the route parameter
     * @return \core\oauth2\server\entity\client_entity The client entity
     * @throws not_found_exception If the client cannot be found
     */
    protected function get_client_for_value(string $value): \core\oauth2\server\entity\client_entity {
        $clientmanager = \core\di::get(\core\oauth2\server\client_manager::class);
        $cliententity = $clientmanager->get_client_by_id((int) $value);

        if ($cliententity) {
            return $cliententity;
        }

        throw new not_found_exception('client', $value);
    }

    /**
     * Add client parameter to the request.
     *
     * @param ServerRequestInterface $request The request object
     * @param string $value A client ID value from the route parameter
     * @return ServerRequestInterface The updated request object
     */
    public function add_attributes_for_parameter_value(
        ServerRequestInterface $request,
        string $value,
    ): ServerRequestInterface {
        $client = $this->get_client_for_value($value);

        return $request->withAttribute("{$this->name}entity", $client);
    }
}

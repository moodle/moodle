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

namespace core\router\parameters;

use core\exception\not_found_exception;
use core\param;
use core\user;
use core\router\schema\example;
use core\router\schema\parameters\mapped_property_parameter;
use core\router\schema\referenced_object;
use Psr\Http\Message\ServerRequestInterface;

/**
 * A parameter representing a user.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class path_user extends \core\router\schema\parameters\path_parameter implements
    mapped_property_parameter,
    referenced_object
{
    /**
     * Create a new instance of the path_user.
     *
     * @param string $name The name of the parameter to use for the identifier
     * @param mixed ...$extra Additional arguments
     */
    public function __construct(
        string $name = 'user',
        ...$extra,
    ) {
        $extra['name'] = $name;
        $extra['type'] = param::RAW;
        $extra['description'] = <<<EOF
        The user identifier.

        This can be the magic string 'current', or the user's id, idnumber, or username.

        If specifying an id, the value should be in the format `id:[id]`.

        If specifying an idnumber, the value should be in the format `idnumber:[idnumber]`.

        If specifying a username, the value should be in the format `username:[username]`.
        EOF;
        $extra['examples'] = [
            new example(
                name: 'The current user',
                value: 'current',
            ),
            new example(
                name: 'A user specified by their user id',
                value: '94853',
            ),
            new example(
                name: 'A user specified by their idnumber',
                value: 'idnumber:some-student-idnumber               ',
            ),
            new example(
                name: 'A user specified by their username',
                value: 'username:lyona1',
            ),
        ];

        parent::__construct(...$extra);
    }

    /**
     * Get the user object for the given identifier.
     *
     * @param string $value A user id, idnumber, or username
     * @return object
     * @throws not_found_exception If the user cannot be found
     */
    protected function get_user_for_value(string $value): mixed {
        global $USER;

        if ($value === 'current') {
            return $USER;
        }

        $data = null;
        if (is_numeric($value)) {
            $data = user::get_user($value);
        } else if (str_starts_with($value, 'idnumber:')) {
            $data = user::get_user_by_idnumber(substr($value, strlen('idnumber:')));
        } else if (str_starts_with($value, 'username:')) {
            $data = user::get_user_by_username(substr($value, strlen('username:')));
        }

        if ($data) {
            return $data;
        }
        throw new not_found_exception('user', $value);
    }

    #[\Override]
    public function add_attributes_for_parameter_value(
        ServerRequestInterface $request,
        string $value,
    ): ServerRequestInterface {
        $user = $this->get_user_for_value($value);

        $request = $request->withAttribute($this->name, $user);

        if ($user->id) {
            $request = $request->withAttribute("{$this->name}context", \core\context\user::instance($user->id));
        }

        return $request;
    }

    #[\Override]
    public function get_schema_from_type(param $type): \stdClass {
        $schema = parent::get_schema_from_type($type);

        $schema->pattern = "^(";
        $schema->pattern .= implode("|", [
            'current',
            '\d+',
            'idnumber:.+',
            'username:.+',
        ]);
        $schema->pattern .= ")$";

        return $schema;
    }
}

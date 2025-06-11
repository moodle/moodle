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

namespace core_user\route\api;

use core\exception\coding_exception;
use core\exception\invalid_parameter_exception;
use core\param;
use core\router\route;
use core\router\schema\objects\scalar_type;
use core\router\schema\response\payload_response;
use core\router\schema\response\content\payload_response_type;
use core\router\schema\response\response_type;
use core\user;
use core_user\route\responses\user_preferences_response;
use stdClass;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * User preference API handler.
 *
 * @package    core_user
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[route(
    path: '/{user}/preferences',
    pathtypes: [
        new \core\router\parameters\path_user(),
    ],
)]
class preferences {
    /**
     * Fetch all user preferences, or a specific user preference.
     *
     * @param ResponseInterface $response
     * @param ServerRequestInterface $request
     * @param stdClass $user
     * @param null|string $preference
     * @return payload_response
     */
    #[route(
        path: '[/{preference}]',
        title: 'Fetch user preferences',
        description: 'Fetch one user preference, or all user preferences',
        pathtypes: [
            new \core\router\schema\parameters\path_parameter(
                name: 'preference',
                type: param::RAW,
            ),
        ],
        responses: [
            new user_preferences_response(),
        ],
    )]
    public function get_preferences(
        ResponseInterface $response,
        ServerRequestInterface $request,
        stdClass $user,
        ?string  $preference,
    ): payload_response {
        $this->check_user($user);

        $result = get_user_preferences(
            name: $preference,
            user: $user,
        );

        if (!is_array($result)) {
            // Check if we received just one preference.
            $result = [$preference => $result];
        }

        return new payload_response($result, $request, $response);
    }

    /**
     * Set a set of user preferences.
     *
     * @param ResponseInterface $response
     * @param stdClass $user
     * @return payload_response
     */
    #[route(
        method: ['POST'],
        title: 'Set or update multiple user preferences',
        requestbody: new \core\router\schema\request_body(
            content: new payload_response_type(
                schema: new \core\router\schema\objects\schema_object(
                    content: [
                        'preferences' => new \core\router\schema\objects\array_of_strings(
                            keyparamtype: param::TEXT,
                            valueparamtype: param::RAW,
                        ),
                    ],
                ),
            ),
        ),
        responses: [
            new user_preferences_response(),
        ],
    )]
    public function set_preferences(
        ResponseInterface $response,
        ServerRequestInterface $request,
        stdClass $user,
    ): payload_response {
        $this->check_user($user);

        $values = $request->getParsedBody();
        $preferences = $values['preferences'] ?? [];

        foreach ($preferences as $preference => $value) {
            $this->set_single_preference($user, $preference, $value);
        }

        $result = array_filter(
            get_user_preferences(
                user: $user,
            ),
            fn ($preference) => array_key_exists($preference, $preferences),
            ARRAY_FILTER_USE_KEY,
        );

        return new payload_response($result, $request, $response);
    }

    /**
     * Set a single user preference.
     *
     * @param ResponseInterface $response
     * @param string $themename
     * @param string $component
     * @param null|string $identifier
     * @return response_type
     */
    #[route(
        path: '/{preference}',
        method: ['POST'],
        title: 'Set a single user preference',
        description: 'Set a single user preference',
        pathtypes: [
            new \core\router\schema\parameters\path_parameter(
                name: 'preference',
                type: param::RAW,
            ),
        ],
        requestbody: new \core\router\schema\request_body(
            content: new payload_response_type(
                schema: new \core\router\schema\objects\schema_object(
                    content: [
                        'value' => new scalar_type(param::RAW),
                    ],
                ),
            ),
        ),
        responses: [
            new \core\router\schema\response\response(
                statuscode: 200,
                description: 'OK',
                content: [
                    new \core\router\schema\response\content\json_media_type(
                        schema: new \core\router\schema\objects\array_of_strings(
                            keyparamtype: param::TEXT,
                            valueparamtype: param::RAW,
                        ),
                        examples: [
                            new \core\router\schema\example(
                                name: 'A single preference value',
                                summary: 'A json response containing a single preference',
                                value: [
                                    "drawers-open-index" => "1",
                                ],
                            ),
                        ]
                    ),
                ],
            ),
        ],
    )]
    public function set_preference(
        ResponseInterface $response,
        ServerRequestInterface $request,
        stdClass $user,
        ?string $preference,
    ): response_type {
        $this->check_user($user);

        $values = $request->getParsedBody();
        $value = $values['value'] ?? null;
        $this->set_single_preference($user, $preference, $value);

        return $this->get_preferences($response, $request, $user, $preference);
    }

    /**
     * Set a single user preference.
     *
     * @param \stdClass $user
     * @param string $preference
     * @param mixed $value
     * @throws \core\exception\access_denied_exception
     * @throws \invalid_parameter_exception
     */
    protected function set_single_preference(
        stdClass $user,
        string $preference,
        mixed $value,
    ): void {
        try {
            $definition = user::get_preference_definition($preference);
        } catch (coding_exception $e) {
            throw new invalid_parameter_exception("Invalid preference '$preference'");
        }

        if (!user::can_edit_preference($preference, $user)) {
            throw new \core\exception\access_denied_exception('You do not have permission to edit this preference.');
        }

        if (isset($definition['type'])) {
            $type = param::from_type($definition['type']);
            $value = $this->standardise_value($type, $value);
        }

        $cleanvalue = user::clean_preference($value, $preference);
        if ($cleanvalue !== $value) {
            throw new \invalid_parameter_exception("Invalid value for preference '$preference': '{$value}'");
        }
        $value = $cleanvalue;
        set_user_preference($preference, $value, $user->id);
    }

    /**
     * Ensure that the requested user meets the requirements.
     *
     * @param stdClass $user
     * @throws invalid_parameter_exception
     */
    protected function check_user(stdClass $user): void {
        global $USER;

        if ($user->id !== $USER->id) {
            throw new \core\exception\access_denied_exception(
                'You do not have permission to view or edit preferences for other users.',
            );
        }
    }

    /**
     * Standardise value based on type.
     *
     * Note: We cannot use \core\param here because we only want to cast some types.
     * Requests do not have an inherent understanding of anything but strings. We need to be strict on typing of integers and bools.
     *
     * @param string param $type
     * @param mixed $value
     * @return mixed
     */
    protected function standardise_value(param $type, mixed $value): mixed {
        if (is_numeric($value) || is_bool($value)) {
            switch ($type) {
                case param::INT:
                case param::BOOL:
                    $value = (int) $value;
            }
        }

        return $value;
    }
}

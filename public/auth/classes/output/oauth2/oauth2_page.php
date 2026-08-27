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

namespace core_auth\output\oauth2;

/**
 * Abstract class containing shared functionality for OAuth2 pages.
 *
 * @package    core_auth
 * @copyright  2026 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class oauth2_page implements
    \core\output\named_templatable,
    \core\output\renderable {
    /** @var \League\OAuth2\Server\Entities\ClientEntityInterface The client entity */
    protected \League\OAuth2\Server\Entities\ClientEntityInterface $client;

    /**
     * Get the user information for the currently logged in user.
     *
     * @param \core\output\core_renderer $renderer
     * @return object
     */
    protected function get_user_info(\core\output\core_renderer $renderer): \stdClass {
        return (object) [
            'username' => $this->user->username,
            'fullname' => \core_user::get_fullname($this->user),
            'email' => $this->user->email,
            'userpicture' => $renderer->user_picture($this->user, [
                'includefullname' => true,
                'class' => 'userpicture',
            ]),
            'profileurl' => \core\user::get_profile_url($this->user)->out(false),
        ];
    }

    /**
     * Get the display information for this page's client entity.
     *
     * @return \stdClass
     */
    protected function get_client_info(): \stdClass {
        return self::describe_client($this->client);
    }

    /**
     * Build display information for an OAuth2 client.
     *
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $client
     * @return \stdClass
     */
    public static function describe_client(\League\OAuth2\Server\Entities\ClientEntityInterface $client): \stdClass {
        return (object) [
            'name' => $client->getName(),
            'description' => format_text($client->get_description(), FORMAT_MOODLE),
            'identifier' => $client->getIdentifier(),
            'isconfidential' => $client->isConfidential(),
        ];
    }
}

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

namespace enrol_lti\local\ltiadvantage\lib;

use auth_lti\local\ltiadvantage\utility\cookie_helper;
use Packback\Lti1p3\Interfaces\ICookie;

/**
 * Cookie representation used by the lti1p3 library code.
 *
 * This implementation is a copy of the Packback ImsCookie implementation, a class previously included in the library
 * but which is now deprecated there.
 *
 * @package    enrol_lti
 * @copyright  2024 Jake Dallimore <jrhdallimore@gmail.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lti_cookie implements ICookie {

    public function getCookie(string $name): ?string {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }
        // Look for backup cookie if same site is not supported by the user's browser.
        if (isset($_COOKIE['LEGACY_'.$name])) {
            return $_COOKIE['LEGACY_'.$name];
        }

        return null;
    }

    public function setCookie(string $name, string $value, int $exp = 3600, array $options = []): void {
        $cookieoptions = [
            'expires' => time() + $exp,
        ];

        // SameSite none and secure will be required for tools to work inside iframes.
        $samesiteoptions = [
            'samesite' => 'None',
            'secure' => true,
        ];

        setcookie($name, $value, array_merge($cookieoptions, $samesiteoptions, $options));

        // Necessary, since partitioned can't be set via setcookie yet.
        cookie_helper::add_attributes_to_cookie_response_header($name, ['Partitioned']);

        // Set a second fallback cookie in the event that "SameSite" is not supported.
        setcookie('LEGACY_'.$name, $value, array_merge($cookieoptions, $options));
    }
}

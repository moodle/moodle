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
 * One Roster Client.
 *
 * This plugin synchronises enrolment and roles with a One Roster endpoint.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster;

use enrol_oneroster\local\interfaces\client as client_interface;
use moodle_url;

/**
 * One Roster Client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class client_helper {
    /** @var One Roster Version 1.1 */
    const VERSION_V1P1 = 'v1p1';

    /** @var One Roster Version 1.2 */
    const VERSION_V1P2 = 'v1p2';

    /** @var Version 1.0 of the OAuth specification */
    const OAUTH_10 = 'oauth1';

    /** @var Version 2.0 of the OAuth specification */
    const OAUTH_20 = 'oauth2';

    /** @var A POST request */
    const POST = 'POST';

    /** @var A GET request */
    const GET = 'GET';

    /**
     * Get an instance of the One Roster API.
     *
     * @param string $oauthversion The version of the OAuth specification to use
     * @param string $version The One Roster version to use
     * @param string $tokenurl The OAuth2 server
     * @param string $server The server hosting the One Roster endpoint
     * @param string $clientid The OAuth2 Client ID.
     * @param string $clientsecret The secret associated witht the oauth2 client
     * @return client
     */
    public static function get_client(
        string $oauthversion,
        string $version,
        string $tokenurl,
        string $server,
        string $clientid,
        string $clientsecret
    ): client_interface {
        $classname = "\\enrol_oneroster\\local\\$version\\{$oauthversion}_client";
        if (!class_exists($classname)) {
            throw new \InvalidArgumentException("Unknown API Version '{$version}' ({$classname})");
        }

        return new $classname($tokenurl, $server, $clientid, $clientsecret);
    }
}

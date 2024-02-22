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

use Packback\Lti1p3\Interfaces\ICache;

/**
 * The launch_cache_session, providing a temporary session store for launch information.
 *
 * This is used to store the launch information while the user is transitioned through the Moodle authentication flows
 * and back to the deep linking launch handler (launch_deeplink.php).
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class launch_cache_session implements ICache {

    /**
     * Get the launch data from the cache.
     *
     * @param string $key the launch id.
     * @return array|null the launch data.
     */
    public function getLaunchData(string $key): ?array {
        global $SESSION;
        if (isset($SESSION->enrol_lti_launch[$key])) {
            return unserialize($SESSION->enrol_lti_launch[$key]);
        }
        return null;
    }

    /**
     * Add launch data to the cache.
     *
     * @param string $key the launch id.
     * @param array $jwtBody the launch data.
     */
    public function cacheLaunchData(string $key, array $jwtBody): void {
        global $SESSION;
        $SESSION->enrol_lti_launch[$key] = serialize($jwtBody);
    }

    /**
     * Cache the nonce.
     *
     * @param string $nonce the nonce.
     * @param string $state the state.
     */
    public function cacheNonce(string $nonce, string $state): void {
        global $SESSION;
        $SESSION->enrol_lti_launch_nonce[$nonce] = $state;
    }

    /**
     * Check whether the cache contains the nonce.
     *
     * @param string $nonce the nonce
     * @param string $state the state
     * @return bool true if found, false otherwise.
     */
    public function checkNonceIsValid(string $nonce, string $state): bool {
        global $SESSION;
        return isset($SESSION->enrol_lti_launch_nonce[$nonce]) && $SESSION->enrol_lti_launch_nonce[$nonce] == $state;
    }

    /**
     * Delete all data from the session cache.
     */
    public function purge() {
        global $SESSION;
        unset($SESSION->enrol_lti_launch);
    }

    /**
     * Cache the access token.
     *
     * @param string $key the key
     * @param string $accessToken the access token
     */
    public function cacheAccessToken(string $key, string $accessToken): void {
        global $SESSION;
        $SESSION->enrol_lti_launch_token[$key] = $accessToken;
    }

    /**
     * Get a cached access token.
     *
     * @param string $key the key to check.
     * @return string|null the token string, or null if not found.
     */
    public function getAccessToken(string $key): ?string {
        global $SESSION;
        return $SESSION->enrol_lti_launch_token[$key] ?? null;
    }

    /**
     * Clear the access token from the cache.
     *
     * @param string $key the key to purge.
     */
    public function clearAccessToken(string $key): void {
        global $SESSION;
        unset($SESSION->enrol_lti_launch_token[$key]);
    }
}

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

namespace auth_iomadsaml2;

use moodle_url;

/**
 * Static list of api methods for auth iomadsaml2 configuration.
 *
 * @package   auth_iomadsaml2
 * @author    Brendan Heywood <brendan@catalyst-au.net>
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * IdP logout callback. Called only when logout is initiated from IdP.
     * {@see \auth_iomadsaml2\sp\iomadsaml2-logout}
     */
    public static function logout_from_idp_front_channel(): void {
        // The SP session will be cleaned up. Log user out of Moodle.
        require_logout();
    }

    /**
     * SP logout callback. Called in case of normal Moodle logout.
     * {@see auth::logoutpage_hook}
     *
     * @param array $state Information about the current logout operation.
     */
    public static function after_logout_from_sp($state): void {
        global $iomadsaml2config;

        $cookiename = $iomadsaml2config['session.cookie.name'];
        $sessid = $_COOKIE[$cookiename];

        // In SSP should do this for us but remove stored SP session data.
        $storeclass = $iomadsaml2config['store.type'];
        $store = new $storeclass;
        $store->delete('session', $sessid);

        redirect(new moodle_url($state['ReturnTo']));
    }

    /**
     * Used to populate authproc.sp config attribute with a list of callbacks
     * defined in other components.
     *
     * @return array
     */
    public static function authproc_filters_hook(): array {
        $authprocfilters = [];
        $authprocfilters[50] = array(
            'class' => 'core:AttributeMap',
            'oid2name',
        );
        $callbacks = get_plugins_with_function('extend_auth_iomadsaml2_proc', 'lib.php');
        foreach ($callbacks as $plugins) {
            foreach ($plugins as $pluginfunction) {
                $filters = $pluginfunction();
                foreach ($filters as $key => $value) {
                    $key = self::check_filters_priority($key, $authprocfilters);
                    $authprocfilters[$key] = $value;
                }
            }
        }
        return $authprocfilters;
    }

    /**
     * Helper method to find unique key {@see self::iomadsaml2_authproc_filters_hook}.
     *
     * @param int $priority
     * @param array $filters
     * @return int
     */
    private static function check_filters_priority($priority, $filters): int {
        $uniquekey = false;
        while (!$uniquekey) {
            if (!array_key_exists($priority, $filters)) {
                $uniquekey = true;
            } else {
                $priority++;
            }
        }
        return $priority;
    }

    /**
     * Is the plugin enabled.
     *
     * @return bool
     */
    public static function is_enabled(): bool {
        return is_enabled_auth('iomadsaml2');
    }
}

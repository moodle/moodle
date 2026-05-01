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

namespace core;

/**
 * Authentication plugin registry.
 *
 * Provides methods for checking, retrieving, and listing authentication plugins.
 *
 * Note: This class should be fetched using DI.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class authentication {
    /**
     * Returns true if the auth plugin file exists and is readable.
     *
     * @param string $auth Name of authentication plugin
     * @return bool
     */
    public function plugin_exists(string $auth): bool {
        global $CFG;

        if (file_exists("{$CFG->dirroot}/auth/$auth/auth.php")) {
            return is_readable("{$CFG->dirroot}/auth/$auth/auth.php");
        }
        return false;
    }

    /**
     * Checks if a given plugin is in the list of enabled authentication plugins.
     *
     * @param string $auth Authentication plugin name
     * @return bool Whether the plugin is enabled
     */
    public function is_enabled(string $auth): bool {
        if (empty($auth)) {
            return false;
        }

        $enabled = $this->get_enabled_plugins();

        return in_array($auth, $enabled);
    }

    /**
     * Returns an authentication plugin instance.
     *
     * @param string $auth Name of authentication plugin
     * @return \auth_plugin_base An instance of the required authentication plugin
     * @throws \moodle_exception If the plugin does not exist
     */
    public function get_plugin(string $auth): \auth_plugin_base {
        global $CFG;

        if (!$this->plugin_exists($auth)) {
            throw new \moodle_exception('authpluginnotfound', 'debug', '', $auth);
        }

        require_once("{$CFG->dirroot}/auth/$auth/auth.php");
        $class = "auth_plugin_$auth";
        return new $class();
    }

    /**
     * Returns array of active auth plugins.
     *
     * @param bool $fix Fix $CFG->auth if needed. Only set if logged in as admin.
     * @return array
     */
    public function get_enabled_plugins(bool $fix = false): array {
        global $CFG;

        $default = ['manual', 'nologin'];

        if (empty($CFG->auth)) {
            $auths = [];
        } else {
            $auths = explode(',', $CFG->auth);
        }

        $auths = array_unique($auths);
        $oldauthconfig = implode(',', $auths);
        foreach ($auths as $k => $authname) {
            if (in_array($authname, $default)) {
                // The manual and nologin plugin never need to be stored.
                unset($auths[$k]);
            } else if (!$this->plugin_exists($authname)) {
                debugging(get_string('authpluginnotfound', 'debug', $authname));
                unset($auths[$k]);
            }
        }

        // Ideally only explicit interaction from a human admin should trigger a
        // change in auth config, see MDL-70424 for details.
        if ($fix) {
            $newconfig = implode(',', $auths);
            if (!isset($CFG->auth) || $newconfig != $CFG->auth) {
                add_to_config_log('auth', $oldauthconfig, $newconfig, 'core');
                set_config('auth', $newconfig);
            }
        }

        return array_merge($default, $auths);
    }

    /**
     * Returns true if an internal authentication method is being used.
     *
     * @param string $auth Form of authentication required
     * @return bool
     */
    public function is_internal(string $auth): bool {
        $authplugin = $this->get_plugin($auth);
        return $authplugin->is_internal();
    }

    /**
     * Returns true if the user is a 'restored' one.
     *
     * Used in the login process to inform the user and allow them to reset the password.
     *
     * @param string $username Username to be checked
     * @return bool
     */
    public function is_restored_user(string $username): bool {
        global $CFG, $DB;

        return $DB->record_exists('user', [
            'username' => $username,
            'mnethostid' => $CFG->mnet_localhost_id,
            'password' => 'restored',
        ]);
    }
}

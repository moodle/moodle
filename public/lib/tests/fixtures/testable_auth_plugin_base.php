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
 * Provides testable_auth_plugin_base class.
 *
 * @package    core
 * @subpackage fixtures
 * @category   test
 * @copyright  2024 Catalyst IT
 * @author     Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_auth_plugin_base extends \auth_plugin_base {

    /**
     * Override to add test auth plugin
     *
     * @return array of plugin classes
     */
    public static function get_enabled_auth_plugin_classes(): array {
        $plugins = parent::get_enabled_auth_plugin_classes();
        $plugins[] = new testable_auth_plugin_base();
        return $plugins;
    }

    /**
     * Identify a Moodle account on the CLI
     *
     * For example a plugin might use posix_geteuid and posix_getpwuid
     * to find the username of the OS level user and then match that
     * against Moodle user accounts.
     *
     * @return null|stdClass User user record if found
     */
    public function find_cli_user(): ?stdClass {
        global $DB;
        $user = $DB->get_record('user', ['username' => 'abcdef']);
        if ($user) {
            return $user;
        }
        return null;
    }

}

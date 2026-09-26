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

namespace local_requirelogin;

/**
 * Site access policy: anonymous visitors see the login page, not the catalog.
 *
 * Existing sites keep an older saved value of forcelogin=0, so the front page
 * still lists courses, teachers and summaries. Course pages already call
 * require_login(). This policy applies that same gate to the rest of the site
 * and keeps an admin save from turning it back off.
 *
 * @package    local_requirelogin
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class policy {
    /**
     * Core settings this policy owns.
     *
     * forcelogin sends anonymous requests to the login page.
     * autologinguests must stay off, otherwise require_login() creates a guest
     * session and the catalog renders anyway.
     * guestloginbutton must stay hidden so the login page is not a bypass.
     *
     * @return array<string, int>
     */
    public static function settings(): array {
        return [
            'forcelogin' => 1,
            'autologinguests' => 0,
            'guestloginbutton' => 0,
        ];
    }

    /**
     * Apply the policy for this request and lock the admin settings.
     */
    public static function apply(): void {
        global $CFG;

        if (environment::is_automated_test()) {
            return;
        }

        if (!isset($CFG->config_php_settings) || !is_array($CFG->config_php_settings)) {
            $CFG->config_php_settings = [];
        }

        foreach (self::settings() as $name => $value) {
            $CFG->$name = $value;
            $CFG->config_php_settings[$name] = $value;
        }
    }

    /**
     * Store the policy so a later plugin removal still leaves login required.
     */
    public static function persist(): void {
        if (environment::is_automated_test()) {
            return;
        }
        foreach (self::settings() as $name => $value) {
            set_config($name, $value);
        }
    }
}

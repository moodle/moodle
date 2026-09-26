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
 * Where this policy is allowed to run.
 *
 * PHPUnit and Behat install every local plugin and expect Moodle's own login
 * and role defaults. Applying the policy there would change those suites.
 *
 * @package    local_requirelogin
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class environment {
    /**
     * True when the current process is an automated Moodle test site.
     */
    public static function is_automated_test(): bool {
        if (defined('PHPUNIT_TEST') && PHPUNIT_TEST) {
            return true;
        }
        if (defined('BEHAT_SITE_RUNNING') && BEHAT_SITE_RUNNING) {
            return true;
        }
        return false;
    }
}

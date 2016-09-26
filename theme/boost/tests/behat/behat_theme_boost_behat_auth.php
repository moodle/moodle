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
 * Basic authentication steps definitions.
 *
 * @package    core_auth
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');
require_once(__DIR__ . '/../../../../auth/tests/behat/behat_auth.php');

/**
 * Log in log out steps definitions.
 *
 * @package    core_auth
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_boost_behat_auth extends behat_auth {

    public function i_log_out() {
        // There is no longer any need to worry about whether the navigation
        // bar needs to be expanded; user_menu now lives outside the
        // hamburger.

        // However, the user menu *always* needs to be expanded. if running JS.
        if ($this->running_javascript()) {
            $xpath = "//div[@class='usermenu']//a[contains(concat(' ', @class, ' '), ' dropdown-toggle ')]";

            $this->execute('behat_general::i_click_on', array($xpath, "xpath_element"));
        }

        // No need to check for exceptions as it will checked after this step execution.
        $this->execute('behat_general::click_link', get_string('logout'));
    }
}

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
 * Behat login related steps definitions.
 *
 * @package    core
 * @category   test
 * @copyright  2016 Universite de Montreal
 * @author     Gilles-Philippe Leblanc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL used, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

/**
 * Contains functions used by behat to test functionality.
 *
 * @package    core
 * @category   test
 * @copyright  2016 Universite de Montreal
 * @author     Gilles-Philippe Leblanc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_login extends behat_base {

    /**
     * Force a password change for a specific user.
     *
     * @Given /^I force a password change for user "([^"]*)"$/
     * @param string $username The username of the user whose password will expire
     */
    public function i_force_a_password_change_for_user($username) {
        $user = core_user::get_user_by_username($username, 'id', null, MUST_EXIST);
        set_user_preference("auth_forcepasswordchange", true, $user);
    }
}

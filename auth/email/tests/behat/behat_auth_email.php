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
 * Step definition for auth_email
 *
 * @package    auth_email
 * @category   test
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Step definition for auth_email.
 *
 * @package    auth_email
 * @category   test
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_auth_email extends behat_base {

    /**
     * Emulate clicking on confirmation link from the email
     *
     * @When /^I confirm email for "(?P<username>(?:[^"]|\\")*)"$/
     *
     * @param string $username
     */
    public function i_confirm_email_for($username) {
        global $DB;
        $secret = $DB->get_field('user', 'secret', ['username' => $username], MUST_EXIST);
        $confirmationurl = new moodle_url('/login/confirm.php');
        $confirmationpath = $confirmationurl->out_as_local_url(false);
        $url = $confirmationpath .  '?' . 'data='. $secret .'/'. $username;

        $this->execute('behat_general::i_visit', [$url]);
    }
}

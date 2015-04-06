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
 * Behat message-related steps definitions.
 *
 * @package    core_message
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Messaging system steps definitions.
 *
 * @package    core_message
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_message extends behat_base {

    /**
     * Sends a message to the specified user from the logged user. The user full name should contain the first and last names.
     *
     * @Given /^I send "(?P<message_contents_string>(?:[^"]|\\")*)" message to "(?P<user_full_name_string>(?:[^"]|\\")*)" user$/
     * @param string $messagecontent
     * @param string $userfullname
     */
    public function i_send_message_to_user($messagecontent, $userfullname) {

        $steps = array();
        $steps[] = new Given('I am on homepage');
        if ($this->running_javascript()) {
            $steps[] = new Given('I follow "' . get_string('messages', 'message') . '" in the user menu');
        } else {
            $steps[] = new Given('I follow "' . get_string('messages', 'message') . '"');
        }
        $steps[] = new Given('I set the field "' . get_string('searchcombined', 'message') .
            '" to "' . $this->escape($userfullname) . '"');
        $steps[] = new Given('I press "' . get_string('searchcombined', 'message') . '"');
        $steps[] = new Given('I follow "' . $this->escape(get_string('sendmessageto', 'message', $userfullname)) . '"');
        $steps[] = new Given('I set the field "id_message" to "' . $this->escape($messagecontent) . '"');
        $steps[] = new Given('I press "' . get_string('sendmessage', 'message') . '"');

        return $steps;
    }

}

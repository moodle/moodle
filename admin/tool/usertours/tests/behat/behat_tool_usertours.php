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
 * User tour related steps definitions.
 *
 * @package    tool_usertours
 * @category   test
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode;
/**
 * User tour related steps definitions.
 *
 * @package    tool_usertours
 * @category   test
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_tool_usertours extends behat_base {
    /**
     * Add a new user tour.
     *
     * @Given /^I add a new user tour with:$/
     * @param TableNode $table
     */
    public function i_add_a_new_user_tour_with(TableNode $table) {
        $this->execute('behat_tool_usertours::i_open_the_user_tour_settings_page');
        $this->execute('behat_general::click_link', get_string('newtour', 'tool_usertours'));

        // Fill form and post.
        $this->execute('behat_forms::i_set_the_following_fields_to_these_values', $table);
        $this->execute('behat_forms::press_button', get_string('savechanges', 'moodle'));
        $this->execute('behat_general::i_wait_to_be_redirected');
    }

    /**
     * Add new steps to a user tour.
     *
     * @Given /^I add steps to the "(?P<tour_name_string>(?:[^"]|\\")*)" tour:$/
     * @param   string      $tourname   The name of the tour to add steps to.
     * @param   TableNode   $table
     */
    public function i_add_steps_to_the_named_tour($tourname, TableNode $table) {
        $this->execute('behat_tool_usertours::i_open_the_user_tour_settings_page');
        $this->execute('behat_general::click_link', $this->escape($tourname));
        $this->execute('behat_tool_usertours::i_add_steps_to_the_tour', $table);
    }

    /**
     * Add new steps to the current user tour.
     *
     * @Given /^I add steps to the tour:$/
     * @param   TableNode   $table
     */
    public function i_add_steps_to_the_tour(TableNode $table) {
        foreach ($table->getHash() as $step) {
            $this->execute('behat_general::click_link', get_string('newstep', 'tool_usertours'));

            foreach ($step as $locator => $value) {
                $this->execute('behat_forms::i_set_the_field_to', [$this->escape($locator), $this->escape($value)]);
            }

            $this->execute('behat_forms::press_button', get_string('savechanges', 'moodle'));
            $this->execute('behat_general::i_wait_to_be_redirected');
        }
    }

    /**
     * Navigate to the user tour settings page.
     *
     * @Given /^I open the User tour settings page$/
     */
    public function i_open_the_user_tour_settings_page() {
        $this->execute(
            'behat_navigation::i_navigate_to_in_site_administration',
            get_string('appearance', 'admin') . ' > ' .
                get_string('usertours', 'tool_usertours')
        );
    }
}

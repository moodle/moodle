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
 * Behat groups-related steps definitions.
 *
 * @package    core_group
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Groups-related steps definitions.
 *
 * @package    core_group
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_groups extends behat_base {

    /**
     * Add the specified user to the group. You should be in the groups page when running this step. The user should be specified like "Firstname Lastname (user@example.com)".
     *
     * @Given /^I add "(?P<user_fullname_string>(?:[^"]|\\")*)" user to "(?P<group_name_string>(?:[^"]|\\")*)" group members$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $username
     * @param string $groupname
     */
    public function i_add_user_to_group_members($userfullname, $groupname) {
        // Select the group in the select.
        $this->execute('behat_forms::i_set_the_field_to', [get_string('groups', 'core'), $this->escape($groupname)]);

        // Press "Add/remove users".
        $this->execute('behat_general::i_click_on', [get_string('adduserstogroup', 'group'), "button"]);

        // Select the user.
        $this->execute('behat_forms::i_set_the_field_to', ["addselect", $this->escape($userfullname)]);

        // Click add button.
        $this->execute('behat_general::i_click_on', [get_string('add', 'core'), "button"]);

        // Returning to the main groups page.
        $this->execute('behat_general::i_click_on', [get_string('backtogroups', 'group'), "button"]);
    }

    /**
     * A single or comma-separated list of groups expected within a grouping, on group overview page.
     *
     * @Given /^the group overview should include groups "(?P<groups_string>(?:[^"]|\\")*)" in grouping "(?P<grouping_string>(?:[^"]|\\")*)"$/
     * @param string $groups one or comma seperated list of groups.
     * @param string $grouping grouping in which all group should be present.
     */
    public function the_groups_overview_should_include_groups_in_grouping($groups, $grouping) {

        $groups = array_map('trim', explode(',', $groups));

        foreach ($groups as $groupname) {
            // Find the table after the H3 containing the grouping name, then look for the group name in the first column.
            $xpath = "//h3[normalize-space(.) = '{$grouping}']/following-sibling::div[contains(@class, 'table-responsive')]" .
                "/table//tr//td[contains(concat(' ', normalize-space(@class), ' '), ' c0 ')][normalize-space(.) = '{$groupname}' ]";

            $this->execute('behat_general::should_exist', array($xpath, 'xpath_element'));
        }
    }
}

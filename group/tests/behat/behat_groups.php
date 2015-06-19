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

use Behat\Behat\Context\Step\Then;
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

        $userfullname = $this->getSession()->getSelectorsHandler()->xpathLiteral($userfullname);

        // Using a xpath liternal to avoid problems with quotes and double quotes.
        $groupname = $this->getSession()->getSelectorsHandler()->xpathLiteral($groupname);

        // We don't know the option text as it contains the number of users in the group.
        $select = $this->find_field('groups');
        $xpath = "//select[@id='groups']/descendant::option[contains(., $groupname)]";
        $groupoption = $this->find('xpath', $xpath);
        $fulloption = $groupoption->getText();
        $select->selectOption($fulloption);

        // Here we don't need to wait for the AJAX response.
        $this->find_button(get_string('adduserstogroup', 'group'))->click();

        // Wait for add/remove members page to be loaded.
        $this->getSession()->wait(self::TIMEOUT * 1000, self::PAGE_READY_JS);

        // Getting the option and selecting it.
        $select = $this->find_field('addselect');
        $xpath = "//select[@id='addselect']/descendant::option[contains(., $userfullname)]";
        $memberoption = $this->find('xpath', $xpath);
        $fulloption = $memberoption->getText();
        $select->selectOption($fulloption);

        // Click add button.
        $this->find_button(get_string('add'))->click();

        // Wait for the page to load.
        $this->getSession()->wait(self::TIMEOUT * 1000, self::PAGE_READY_JS);

        // Returning to the main groups page.
        $this->find_button(get_string('backtogroups', 'group'))->click();
    }

    /**
     * A comma-separated list of groups expected on the page, with a list of groupings each of them should be found in.
     *
     * @Given /^the groups overview should include groups "(?P<group_list_string>(?:[^"]|\\")*)" in groupings "(?P<grouping_list_string>(?:[^"]|\\")*)"$/
     * @param string $grouplist
     * @param string $groupinglist
     * @return Then[]
     */
    public function the_groups_overview_should_include_groups($grouplist, $groupinglist) {
        if (!$grouplist) {
            return array();
        }

        $steps = array();
        $groups = array_map('trim', explode(',', $grouplist));
        $groupings = array_map('trim', explode(',', $groupinglist));
        if (count($groups) != count($groupings)) {
            throw new Exception('Groups and groupings lists must contain the same number of items');
        }

        $groupingname = reset($groupings);
        foreach ($groups as $groupname) {
            // Find the table after the H3 containing the grouping name, then look for the group name in the first column.
            $xpath = "//h3[normalize-space(.) = '{$groupingname}']/following-sibling::table//tr//".
                "td[contains(concat(' ', normalize-space(@class), ' '), ' c0 ')][normalize-space(.) = '{$groupname}' ]";

            $steps[] = new Then('"'.$xpath.'" "xpath_element" should exist');

            $groupingname = next($groupings);
        }

        return $steps;
    }

    /**
     * A comma-separated list of the names of groups not expected on the page
     *
     * @Given /^the groups overview should not include groups "(?P<group_list_string>(?:[^"]|\\")*)"$/
     * @param string $grouplist
     * @return Then[]
     */
    public function the_groups_overview_should_not_include_groups($grouplist) {
        if (!$grouplist) {
            return array();
        }

        $steps = array();
        $groups = array_map('trim', explode(',', $grouplist));
        foreach ($groups as $groupname) {
            $steps[] = new Then('"'.$groupname.'" "table_row" should not exist');
        }

        return $steps;
    }

    /**
     * A comma-separated list of the group members expected on the page, with a list of groups each of them should be found in.
     *
     * @Given /^the groups overview should include members "(?P<member_list_string>(?:[^"]|\\")*)" in groups "(?P<group_list_string>(?:[^"]|\\")*)"$/
     * @param string $memberlist
     * @param string $grouplist
     * @return Then[]
     */
    public function the_groups_overview_should_include_members($memberlist, $grouplist) {
        if (!$memberlist) {
            return array();
        }

        $steps = array();
        $members = array_map('trim', explode(',', $memberlist));
        $groups = array_map('trim', explode(',', $grouplist));
        if (count($members) != count($groups)) {
            throw new Exception('Group members and groups lists must contain the same number of items');
        }

        $groupname = reset($groups);
        foreach ($members as $membername) {
            $steps[] = new Then('"'.$membername.'" "text" should exist in the "'.$groupname.'" "table_row"');
            $groupname = next($groups);
        }

        return $steps;
    }

    /**
     * A comma-separated list of the names of group members not expected on the page
     *
     * @Given /^the groups overview should not include members "(?P<member_list_string>(?:[^"]|\\")*)"$/
     * @param string $memberlist
     * @return Then[]
     */
    public function the_groups_overview_should_not_include_members($memberlist) {
        if (!$memberlist) {
            return array();
        }

        $steps = array();
        $members = array_map('trim', explode(',', $memberlist));
        foreach ($members as $membername) {
            $steps[] = new Then('I should not see "'.$membername.'"');
        }

        return $steps;
    }
}

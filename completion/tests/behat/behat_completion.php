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
 * Completion steps definitions.
 *
 * @package    core_completion
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Steps definitions to deal with course and activities completion.
 *
 * @package    core_completion
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_completion extends behat_base {

    /**
     * Checks that the specified user has completed the specified activity of the current course.
     *
     * @Then /^"(?P<user_fullname_string>(?:[^"]|\\")*)" user has completed "(?P<activity_name_string>(?:[^"]|\\")*)" activity$/
     * @param string $userfullname
     * @param string $activityname
     */
    public function user_has_completed_activity($userfullname, $activityname) {

        // Will throw an exception if the element can not be hovered.
        $titleliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral($userfullname . ", " . $activityname . ": Completed");
        $xpath = "//table[@id='completion-progress']" .
            "/descendant::img[contains(@title, $titleliteral)]";

        return array(
            new Given('I go to the current course activity completion report'),
            new Given('I hover "' . $this->escape($xpath) . '" "xpath_element"')
        );
    }

    /**
     * Checks that the specified user has not completed the specified activity of the current course.
     *
     * @Then /^"(?P<user_fullname_string>(?:[^"]|\\")*)" user has not completed "(?P<activity_name_string>(?:[^"]|\\")*)" activity$/
     * @param string $userfullname
     * @param string $activityname
     */
    public function user_has_not_completed_activity($userfullname, $activityname) {

        // Will throw an exception if the element can not be hovered.
        $titleliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral($userfullname . ", " . $activityname . ": Not completed");
        $xpath = "//table[@id='completion-progress']" .
            "/descendant::img[contains(@title, $titleliteral)]";
        return array(
            new Given('I go to the current course activity completion report'),
            new Given('I hover "' . $this->escape($xpath) . '" "xpath_element"')
        );

        return $steps;
    }

    /**
     * Goes to the current course activity completion report.
     *
     * @Given /^I go to the current course activity completion report$/
     */
    public function go_to_the_current_course_activity_completion_report() {

        $steps = array();

        // Expand reports node if we can't see the link.
        try {
            $this->find('xpath', "//div[@id='settingsnav']" .
                "/descendant::li" .
                "/descendant::li[not(contains(concat(' ', normalize-space(@class), ' '), ' collapsed '))]" .
                "/descendant::p[contains(., '" . get_string('pluginname', 'report_progress') . "')]");
        } catch (ElementNotFoundException $e) {
            $steps[] = new Given('I expand "' . get_string('reports') . '" node');
        }

        $steps[] = new Given('I follow "' . get_string('pluginname', 'report_progress') . '"');

        return $steps;
    }

    /**
     * Toggles completion tracking for course being in the course page.
     *
     * @When /^completion tracking is "(?P<completion_status_string>Enabled|Disabled)" in current course$/
     * @param string $completionstatus The status, enabled or disabled.
     */
    public function completion_is_toggled_in_course($completionstatus) {

        $toggle = strtolower($completionstatus) == 'enabled' ? get_string('yes') : get_string('no');

        return array(
            new Given('I follow "'.get_string('editsettings').'"'),
            new Given('I set the field "'.get_string('enablecompletion', 'completion').'" to "'.$toggle.'"'),
            new Given('I press "'.get_string('savechanges').'"')
        );
    }
}

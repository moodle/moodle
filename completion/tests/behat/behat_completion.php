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

use Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

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
        $titleliteral = behat_context_helper::escape($userfullname . ", " . $activityname . ": Completed");
        $xpath = "//table[@id='completion-progress']" .
            "/descendant::img[contains(@title, $titleliteral)]";

        $this->execute("behat_completion::go_to_the_current_course_activity_completion_report");
        $this->execute("behat_general::should_exist",
            array($this->escape($xpath), "xpath_element")
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
        $titleliteral = behat_context_helper::escape($userfullname . ", " . $activityname . ": Not completed");
        $xpath = "//table[@id='completion-progress']" .
            "/descendant::img[contains(@title, $titleliteral)]";

        $this->execute("behat_completion::go_to_the_current_course_activity_completion_report");
        $this->execute("behat_general::should_exist", array($this->escape($xpath), "xpath_element"));
    }

    /**
     * Goes to the current course activity completion report.
     *
     * @Given /^I go to the current course activity completion report$/
     */
    public function go_to_the_current_course_activity_completion_report() {
        $completionnode = get_string('pluginname', 'report_progress');
        $reportsnode = get_string('courseadministration') . ' > ' . get_string('reports');

        $this->execute("behat_navigation::i_navigate_to_node_in", array($completionnode, $reportsnode));
    }

    /**
     * Toggles completion tracking for course being in the course page.
     *
     * @When /^completion tracking is "(?P<completion_status_string>Enabled|Disabled)" in current course$/
     * @param string $completionstatus The status, enabled or disabled.
     */
    public function completion_is_toggled_in_course($completionstatus) {

        $toggle = strtolower($completionstatus) == 'enabled' ? get_string('yes') : get_string('no');

        // Go to course editing.
        $this->execute("behat_general::click_link", get_string('editsettings'));

        // Expand all the form fields.
        $this->execute("behat_forms::i_expand_all_fieldsets");

        // Enable completion.
        $this->execute("behat_forms::i_set_the_field_to",
            array(get_string('enablecompletion', 'completion'), $toggle));

        // Save course settings.
        $this->execute("behat_forms::press_button", get_string('savechangesanddisplay'));
    }

    /**
     * Checks if the activity with specified name is maked as complete.
     *
     * @Given /^the "(?P<activityname_string>(?:[^"]|\\")*)" "(?P<activitytype_string>(?:[^"]|\\")*)" activity with "(manual|auto)" completion should be marked as complete$/
     */
    public function activity_marked_as_complete($activityname, $activitytype, $completiontype) {
        if ($completiontype == "manual") {
            $imgalttext = get_string("completion-alt-manual-y", 'core_completion', $activityname);
            $xpathtocheck = "//input[@type='image'][contains(@alt, '$imgalttext')]";
        } else {
            $imgalttext = get_string("completion-alt-auto-y", 'core_completion', $activityname);
            $xpathtocheck = "//img[contains(@alt, '$imgalttext')]";
        }
        $csselementforactivitytype = "li.modtype_".strtolower($activitytype);

        $this->execute("behat_general::should_exist_in_the",
            array($xpathtocheck, "xpath_element", $csselementforactivitytype, "css_element")
        );

    }

    /**
     * Checks if the activity with specified name is maked as complete.
     *
     * @Given /^the "(?P<activityname_string>(?:[^"]|\\")*)" "(?P<activitytype_string>(?:[^"]|\\")*)" activity with "(manual|auto)" completion should be marked as not complete$/
     */
    public function activity_marked_as_not_complete($activityname, $activitytype, $completiontype) {
        if ($completiontype == "manual") {
            $imgalttext = get_string("completion-alt-manual-n", 'core_completion', $activityname);
            $xpathtocheck = "//input[@type='image'][contains(@alt, '$imgalttext')]";
        } else {
            $imgalttext = get_string("completion-alt-auto-n", 'core_completion', $activityname);
            $xpathtocheck = "//img[contains(@alt, '$imgalttext')]";
        }
        $csselementforactivitytype = "li.modtype_".strtolower($activitytype);

        $this->execute("behat_general::should_exist_in_the",
            array($xpathtocheck, "xpath_element", $csselementforactivitytype, "css_element")
        );

    }
}

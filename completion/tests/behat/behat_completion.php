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
        $titleliteral = $userfullname . ", " . $activityname . ": Completed";
        $xpath = "//table[@id='completion-progress']";

        $this->execute("behat_completion::go_to_the_current_course_activity_completion_report");
        $this->execute("behat_general::should_exist_in_the",
            array($titleliteral, "icon", $xpath, "xpath_element")
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
        $titleliteral = $userfullname . ", " . $activityname . ": Not completed";
        $xpath = "//table[@id='completion-progress']";

        $this->execute("behat_completion::go_to_the_current_course_activity_completion_report");
        $this->execute("behat_general::should_exist_in_the",
            array($titleliteral, "icon", $xpath, "xpath_element")
        );
    }

    /**
     * Goes to the current course activity completion report.
     *
     * @Given /^I go to the current course activity completion report$/
     */
    public function go_to_the_current_course_activity_completion_report() {
        $completionnode = get_string('pluginname', 'report_progress');
        $reportsnode = get_string('reports');

        $this->execute("behat_navigation::i_navigate_to_in_current_page_administration",
                $reportsnode . ' > ' . $completionnode);
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
        } else {
            $imgalttext = get_string("completion-alt-auto-y", 'core_completion', $activityname);
        }
        $activityxpath = "//li[contains(concat(' ', @class, ' '), ' modtype_" . strtolower($activitytype) . " ')]";
        $activityxpath .= "[descendant::*[contains(text(), '" . $activityname . "')]]";

        $this->execute("behat_general::should_exist_in_the",
            array($imgalttext, "icon", $activityxpath, "xpath_element")
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
        } else {
            $imgalttext = get_string("completion-alt-auto-n", 'core_completion', $activityname);
        }
        $activityxpath = "//li[contains(concat(' ', @class, ' '), ' modtype_" . strtolower($activitytype) . " ')]";
        $activityxpath .= "[descendant::*[contains(text(), '" . $activityname . "')]]";

        $this->execute("behat_general::should_exist_in_the",
            array($imgalttext, "icon", $activityxpath, "xpath_element")
        );
    }

    /**
     * Checks if the activity with specified name shows a information completion checkbox (i.e. showing the completion tracking
     * configuration).
     *
     * @Given /^the "(?P<activityname_string>(?:[^"]|\\")*)" "(?P<activitytype_string>(?:[^"]|\\")*)" activity with "(manual|auto)" completion shows a configuration completion checkbox/
     * @param string $activityname The activity name.
     * @param string $activitytype The activity type.
     * @param string $completiontype The completion type.
     */
    public function activity_has_configuration_completion_checkbox($activityname, $activitytype, $completiontype) {
        if ($completiontype == "manual") {
            $imgname = 'i/completion-manual-enabled';
        } else {
            $imgname = 'i/completion-auto-enabled';
        }
        $iconxpath = "//li[contains(concat(' ', @class, ' '), ' modtype_" . strtolower($activitytype) . " ')]";
        $iconxpath .= "[descendant::*[contains(text(), '" . $activityname . "')]]";
        $iconxpath .= "/descendant::span[@class='actions']/descendant::img[contains(@src, 'i/completion-')]";

        $this->execute("behat_general::the_attribute_of_should_contain",
            array("src", $iconxpath, "xpath_element", $imgname)
        );
    }

    /**
     * Checks if the activity with specified name shows a tracking completion checkbox (i.e. showing my completion tracking status)
     *
     * @Given /^the "(?P<activityname_string>(?:[^"]|\\")*)" "(?P<activitytype_string>(?:[^"]|\\")*)" activity with "(manual|auto)" completion shows a status completion checkbox/
     * @param string $activityname The activity name.
     * @param string $activitytype The activity type.
     * @param string $completiontype The completion type.
     */
    public function activity_has_status_completion_checkbox($activityname, $activitytype, $completiontype) {
        if ($completiontype == "manual") {
            $imgname = 'i/completion-manual-';
        } else {
            $imgname = 'i/completion-auto-';
        }
        $iconxpath = "//li[contains(concat(' ', @class, ' '), ' modtype_" . strtolower($activitytype) . " ')]";
        $iconxpath .= "[descendant::*[contains(text(), '" . $activityname . "')]]";
        $iconxpath .= "/descendant::span[@class='actions']/descendant::img[contains(@src, 'i/completion-')]";

        $this->execute("behat_general::the_attribute_of_should_contain",
            array("src", $iconxpath, "xpath_element", $imgname)
        );

        $this->execute("behat_general::the_attribute_of_should_not_contain",
            array("src", $iconxpath, "xpath_element", '-enabled')
        );
    }

    /**
     * Checks if the activity with specified name does not show any completion checkbox.
     *
     * @Given /^the "(?P<activityname_string>(?:[^"]|\\")*)" "(?P<activitytype_string>(?:[^"]|\\")*)" activity does not show any completion checkbox/
     * @param string $activityname The activity name.
     * @param string $activitytype The activity type.
     */
    public function activity_has_not_any_completion_checkbox($activityname, $activitytype) {
        $iconxpath = "//li[contains(concat(' ', @class, ' '), ' modtype_" . strtolower($activitytype) . " ')]";
        $iconxpath .= "[descendant::*[contains(text(), '" . $activityname . "')]]";
        $iconxpath .= "/descendant::img[contains(@src, 'i/completion-')]";

        $this->execute("behat_general::should_not_exist",
            array($iconxpath, "xpath_element")
        );
    }
}

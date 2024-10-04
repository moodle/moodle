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
                $reportsnode);
        $this->execute("behat_general::i_click_on_in_the", [$completionnode, "link", "region-main", "region"]);
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
        $this->execute("behat_general::click_link", get_string('settings'));

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
     * Checks if the activity with specified name is maked as complete.
     *
     * @When the :conditionname completion condition of :activityname is displayed as :completionstatus
     * @param string $conditionname The completion condition text.
     * @param string $activityname The activity name.
     * @param string $completionstatus The completion status. Must be either of the following: 'todo', 'done', 'failed'.
     */
    public function activity_completion_condition_displayed_as(string $conditionname, string $activityname,
            string $completionstatus): void {

        if (!in_array($completionstatus, ['todo', 'done', 'failed'])) {
            throw new coding_exception('Invalid completion status. It must be of type "todo", "done", or "failed".');
        }

        $text = get_string("completion_automatic:$completionstatus", 'core_course');

        $conditionslistlabel = get_string('completionrequirements', 'core_course', $activityname);
        $selector = "div[aria-label='$conditionslistlabel']";

        try {
            // If there is a dropdown, open it.
            $dropdownnode = $this->find('css', $selector . ' .dropdown-menu');
            if (!$dropdownnode->hasClass('show')) {
                $params = ["button.dropdown-toggle", "css_element", $selector, "css_element"];
                $this->execute("behat_general::i_click_on_in_the", $params);
            }
        } catch (ElementNotFoundException $e) {
            // If the dropdown does not exist, we are in the activity page, all good.
        }

        $xpath = "//div[@aria-label='$conditionslistlabel']//span[text()='$conditionname']/..";
        $this->execute("behat_general::assert_element_contains_text", [$text, $xpath, "xpath_element"]);
    }

    /**
     * Checks if the activity with specified name is maked as complete.
     *
     * @When the :conditionname completion condition of :activityname overridden by :username is displayed as :completionstatus
     * @param string $conditionname The completion condition text.
     * @param string $activityname The activity name.
     * @param string $username The full name of the user overriding the student's activity completion.
     * @param string $completionstatus The override completion status. Must be either of the following: 'todo', 'done'.
     */
    public function overridden_activity_completion_condition_displayed_as(string $conditionname, string $activityname,
            string $username, string $completionstatus): void {
        if (!in_array($completionstatus, ['todo', 'done'])) {
            throw new coding_exception('Invalid override completion status. It must be of type "todo" or "done".');
        }

        $conditionlabel = get_string('completion_setby:auto:' . $completionstatus, 'core_course', (object)[
            'condition' => $conditionname,
            'setby' => $username,
        ]);
        $conditionbadge = "div[aria-label='$conditionlabel']";

        $conditionslistlabel = get_string('completionrequirements', 'core_course', $activityname);
        $completionconditions = "div[aria-label='$conditionslistlabel']";

        $params = [$conditionbadge, 'css_element', $completionconditions, 'css_element'];
        $this->execute("behat_general::should_exist_in_the", $params);
    }

    /**
     * Checks the manual completion state of an activity.
     *
     * @Given /^the manual completion button of "(?P<activityname>(?:[^"]|\\")*)" is displayed as "(?P<completionstatus>(?:[^"]|\\")*)"$/
     * @param string $activityname The activity name.
     * @param string $completionstatus The completion status shown on the manual completion button.
     *                                 Must be either 'Mark as done' or 'Done'.
     */
    public function manual_completion_button_displayed_as(string $activityname, string $completionstatus): void {
        if (!in_array($completionstatus, ['Mark as done', 'Done'])) {
            throw new coding_exception('Invalid completion status. It must be "Mark as done" or "Done".');
        }

        $langstringkey = $completionstatus === 'Done' ? 'done' : 'markdone';
        $conditionslistlabel = get_string('completion_manual:aria:' . $langstringkey, 'core_course', $activityname);
        $selector = "button[aria-label='$conditionslistlabel']";

        $this->execute("behat_general::assert_element_contains_text", [$completionstatus, $selector, "css_element"]);
    }

    /**
     * Checks the manual completion state of an activity.
     *
     * @Given /^the manual completion button of "(?P<activityname>(?:[^"]|\\")*)" overridden by "(?P<username>(?:[^"]|\\")*)" is displayed as "(?P<completionstatus>(?:[^"]|\\")*)"$/
     * @param string $activityname The activity name.
     * @param string $username The full name of the user overriding the student's activity completion.
     * @param string $completionstatus The completion status shown on the manual completion button.
     *                                 Must be either 'Mark as done' or 'Done'.
     */
    public function overridden_manual_completion_button_displayed_as(string $activityname, string $username,
            string $completionstatus): void {
        if (!in_array($completionstatus, ['Mark as done', 'Done'])) {
            throw new coding_exception('Invalid completion status. It must be "Mark as done" or "Done".');
        }

        $langstringkey = $completionstatus === 'Done' ? 'done' : 'markdone';
        $conditionslistlabel = get_string('completion_setby:manual:' . $langstringkey, 'core_course', (object)[
            'activityname' => $activityname,
            'setby' => $username,
        ]);
        $selector = "button[aria-label='$conditionslistlabel']";

        $this->execute("behat_general::assert_element_contains_text", [$completionstatus, $selector, "css_element"]);
    }

    /**
     * Toggles the manual completion button for a given activity.
     *
     * @Given /^I toggle the manual completion state of "(?P<activityname>(?:[^"]|\\")*)"$/
     * @param string $activityname The activity name.
     */
    public function toggle_the_manual_completion_state(string $activityname): void {
        $selector = "button[data-action=toggle-manual-completion][data-activityname='{$activityname}']";

        $this->execute("behat_general::i_click_on", [$selector, "css_element"]);
    }

    /**
     * Check that the activity does show completion information.
     *
     * @Given /^there should be no completion information shown for "(?P<activityname>(?:[^"]|\\")*)"$/
     * @param string $activityname The activity name.
     */
    public function there_should_be_no_completion_for_activity(string $activityname): void {
        $containerselector = "div[data-region=activity-information][data-activityname='$activityname']";
        try {
            $this->find('css_element', $containerselector);
        } catch (ElementNotFoundException $e) {
            // If activity information container does not exist (activity dates not shown, completion info not shown), all good.
            return;
        }

        // Otherwise, ensure that the completion information does not exist.
        $elementselector = "div[data-region=completion-info]";
        $params = [$elementselector, "css_element", $containerselector, "css_element"];
        $this->execute("behat_general::should_not_exist_in_the", $params);
    }

    /**
     * Check that the manual completion button for the activity is disabled.
     *
     * @Given /^the manual completion button for "(?P<activityname>(?:[^"]|\\")*)" should be disabled$/
     * @param string $activityname The activity name.
     */
    public function the_manual_completion_button_for_activity_should_be_disabled(string $activityname): void {
        $selector = "div[data-region='activity-information'][data-activityname='$activityname'] button";

        $params = [$selector, "css_element"];
        $this->execute("behat_general::the_element_should_be_disabled", $params);
    }

    /**
     * Check that the manual completion button for the activity does not exist.
     *
     * @Given /^the manual completion button for "(?P<activityname>(?:[^"]|\\")*)" should not exist/
     * @param string $activityname The activity name.
     */
    public function the_manual_completion_button_for_activity_should_not_exist(string $activityname): void {
        $selector = "div[data-region=activity-information][data-activityname='$activityname'] button";

        $params = [$selector, "css_element"];
        $this->execute('behat_general::should_not_exist', $params);
    }

    /**
     * Check that the manual completion button for the activity exists.
     *
     * @Given /^the manual completion button for "(?P<activityname>(?:[^"]|\\")*)" should exist/
     * @param string $activityname The activity name.
     */
    public function the_manual_completion_button_for_activity_should_exist(string $activityname): void {
        $selector = "div[data-region=activity-information][data-activityname='$activityname'] button";

        $params = [$selector, "css_element"];
        $this->execute('behat_general::should_exist', $params);
    }

    /**
     * Check that the activity has the given automatic completion condition.
     *
     * @When :activityname should have the :conditionname completion condition
     * @param string $activityname The activity name.
     * @param string $conditionname The automatic condition name.
     */
    public function activity_should_have_the_completion_condition(string $activityname, string $conditionname): void {
        $containerselector = "div[data-region=activity-information][data-activityname='$activityname']";

        try {
            // If there is a dropdown, open it.
            $dropdownnode = $this->find('css', $containerselector . ' .dropdown-menu');
            if (!$dropdownnode->hasClass('show')) {
                $params = ["button.dropdown-toggle", "css_element", $containerselector, "css_element"];
                $this->execute("behat_general::i_click_on_in_the", $params);
            }
        } catch (ElementNotFoundException $e) {
            // If the dropdown does not exist, we are in the activity page, all good.
        }

        $params = [$conditionname, $containerselector, 'css_element'];
        $this->execute("behat_general::assert_element_contains_text", $params);
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
        $iconxpath .= "/descendant::div[@class='actions']/descendant::img[contains(@src, 'i/completion-')]";

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
        $iconxpath .= "/descendant::div[@class='actions']/descendant::img[contains(@src, 'i/completion-')]";

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

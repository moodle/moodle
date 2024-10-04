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

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_deprecated_base.php');

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ExpectationException as ExpectationException;
use Behat\Mink\Exception\DriverException as DriverException;
use Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Behat steps in plugin block_social_activities
 *
 * @package    block_social_activities
 * @category   test
 * @copyright  2024 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_block_social_activities_deprecated extends behat_deprecated_base {
    /**
     * Returns the DOM node of the activity in the social activities block
     *
     * @todo MDL-78077 This will be deleted in Moodle 6.0.
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $activityname The activity name
     * @return NodeElement
     */
    protected function get_social_block_activity_node($activityname) {
        $activityname = behat_context_helper::escape($activityname);
        $xpath = "//*[contains(concat(' ',normalize-space(@class),' '),' block_social_activities ')]//li[contains(., $activityname)]";

        return $this->find('xpath', $xpath);
    }

    /**
     * Finds the element containing a specific activity in the social activity block.
     *
     * @todo MDL-78077 This will be deleted in Moodle 6.0.
     * @throws ElementNotFoundException
     * @param string $element
     * @param string $selectortype
     * @param string $activityname
     * @return NodeElement
     */
    protected function get_social_block_activity_element($element, $selectortype, $activityname) {
        $activitynode = $this->get_social_block_activity_node($activityname);

        $exception = new ElementNotFoundException($this->getSession(), "'{$element}' '{$selectortype}' in '{$activityname}'");
        return $this->find($selectortype, $element, $exception, $activitynode);
    }

    /**
     * Checks that the specified activity in the social activities block should have the specified editing icon.
     *
     * This includes items in the action menu for the item (does not require it to be open)
     *
     * You should be in the course page with editing mode turned on.
     *
     * @todo MDL-78077 This will be deleted in Moodle 6.0.
     * @deprecated since 5.0
     *
     * @Then /^"(?P<activity_name_string>(?:[^"]|\\")*)" activity in social activities block should have "(?P<icon_name_string>(?:[^"]|\\")*)" editing icon$/
     * @param string $activityname
     * @param string $iconname
     */
    public function activity_in_social_activities_block_should_have_editing_icon($activityname, $iconname) {
        $this->deprecated_message([
            'behat_block_social_activities::activity_in_social_activities_block_should_have_editing_icon is deprecated',
            'Use: I should see WHATEVER in the ACTIVITYNAME "activity"',
        ]);

        $activitynode = $this->get_social_block_activity_node($activityname);

        $notfoundexception = new ExpectationException('"' . $activityname . '" doesn\'t have a "' .
            $iconname . '" editing icon', $this->getSession());
        $this->find('named_partial', ['link', $iconname], $notfoundexception, $activitynode);
    }

    /**
     * Checks that the specified activity in the social activities block should not have the specified editing icon.
     *
     * This includes items in the action menu for the item (does not require it to be open)
     *
     * You should be in the course page with editing mode turned on.
     *
     * @todo MDL-78077 This will be deleted in Moodle 6.0.
     * @deprecated since 5.0
     *
     * @Then /^"(?P<activity_name_string>(?:[^"]|\\")*)" activity in social activities block should not have "(?P<icon_name_string>(?:[^"]|\\")*)" editing icon$/
     * @param string $activityname
     * @param string $iconname
     */
    public function activity_in_social_activities_block_should_not_have_editing_icon($activityname, $iconname) {
        $this->deprecated_message([
            'behat_block_social_activities::activity_in_social_activities_block_should_not_have_editing_icon is deprecated',
            'Use: I should not see WHATEVER in the ACTIVITYNAME "activity"',
        ]);

        $activitynode = $this->get_social_block_activity_node($activityname);

        try {
            $this->find('named_partial', ['link', $iconname], false, $activitynode);
            throw new ExpectationException('"' . $activityname . '" has a "' . $iconname .
                '" editing icon when it should not', $this->getSession());
        } catch (ElementNotFoundException $e) {
            // This is good, the menu item should not be there.
            return;
        }
    }

    /**
     * Clicks on the specified element of the activity. You should be in the course page with editing mode turned on.
     *
     * @todo MDL-78077 This will be deleted in Moodle 6.0.
     * @deprecated since 5.0
     *
     * @Given /^I click on "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>(?:[^"]|\\")*)" in the "(?P<activity_name_string>(?:[^"]|\\")*)" activity in social activities block$/
     * @param string $element
     * @param string $selectortype
     * @param string $activityname
     */
    public function i_click_on_in_the_activity_in_social_activities_block($element, $selectortype, $activityname) {
        $this->deprecated_message([
            'behat_block_social_activities::i_click_on_in_the_activity_in_social_activities_block is deprecated',
            'Use: I open ACTIVITYNAME actions menu & I choose OPTIONTEXT in the open action menu',
        ]);

        $element = $this->get_social_block_activity_element($element, $selectortype, $activityname);
        $element->click();
    }

    /**
     * Checks that the specified activity is hidden in the social activities block.
     *
     * @todo MDL-78077 This will be deleted in Moodle 6.0.
     * @deprecated since 5.0
     *
     * @Then /^"(?P<activity_name_string>(?:[^"]|\\")*)" activity in social activities block should be hidden$/
     * @param string $activityname
     */
    public function activity_in_social_activities_block_should_be_hidden($activityname) {
        $this->deprecated_message([
            'behat_block_social_activities::activity_in_social_activities_block_should_be_hidden is deprecated',
            'Use: I should see "Hidden from students" in the "ACTIVITYNAME" "core_courseformat > Activity visibility"',
        ]);

        $activitynode = $this->get_social_block_activity_node($activityname);
        $exception = new ExpectationException('"' . $activityname . '" is not hidden', $this->getSession());
        $this->find('named_partial', ['badge', get_string('hiddenfromstudents')], $exception, $activitynode);
    }

    /**
     * Checks that the specified activity is hidden in the social activities block.
     *
     * @todo MDL-78077 This will be deleted in Moodle 6.0.
     * @deprecated since 5.0
     *
     * @Then /^"(?P<activity_name_string>(?:[^"]|\\")*)" activity in social activities block should be available but hidden from course page$/
     * @param string $activityname
     */
    public function activity_in_social_activities_block_should_be_available_but_hidden_from_course_page($activityname) {
        $this->deprecated_message([
            'behat_block_social_activities::activity_in_social_activities_block_should_be_available_but_hidden_from_course_page is deprecated',
            'Use: I should see "Available but not shown on course page" in the "ACTIVITYNAME" "core_courseformat > Activity visibility"',
        ]);

        $activitynode = $this->get_social_block_activity_node($activityname);
        $exception = new ExpectationException('"' . $activityname . '" is not hidden but available', $this->getSession());
        $this->find('named_partial', ['badge', get_string('hiddenoncoursepage')], $exception, $activitynode);
    }

    /**
     * Opens an activity actions menu in the social activities block if it is not already opened.
     *
     * @todo MDL-78077 This will be deleted in Moodle 6.0.
     * @deprecated since 5.0
     *
     * @Given /^I open "(?P<activity_name_string>(?:[^"]|\\")*)" actions menu in social activities block$/
     * @throws DriverException The step is not available when Javascript is disabled
     * @param string $activityname
     */
    public function i_open_actions_menu_in_social_activities_block($activityname) {
        $this->deprecated_message([
            'behat_block_social_activities::i_open_actions_menu_in_social_activities_block is deprecated',
            'Use: I open "ACTIVITYNAME" actions menu',
        ]);

        $activityname = behat_context_helper::escape($activityname);
        $xpath = "//*[contains(concat(' ',normalize-space(@class),' '),' block_social_activities ')]//li[contains(., $activityname)]";
        $this->execute('behat_action_menu::i_open_the_action_menu_in', [$xpath, 'xpath_element']);
    }
}

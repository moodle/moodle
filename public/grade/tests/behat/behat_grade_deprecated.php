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

require_once(__DIR__ . '/../../../lib/behat/behat_deprecated_base.php');

/**
 * Steps definitions that are now deprecated and will be removed in the next releases.
 *
 * This file only contains the steps that previously were in the behat_*.php files in the SAME DIRECTORY.
 * When deprecating steps from other components or plugins, create a behat_COMPONENT_deprecated.php
 * file in the same directory where the steps were defined.
 *
 * @package    core_grades
 * @category   test
 * @copyright  2023 Ilya Tregubov
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_grade_deprecated extends behat_deprecated_base {

    /**
     * Confirm if a value is within the search widget within the gradebook.
     *
     * Examples:
     * - I confirm "User" in "user" search within the gradebook widget exists
     * - I confirm "Group" in "group" search within the gradebook widget exists
     * - I confirm "Grade item" in "grade" search within the gradebook widget exists
     *
     * @Given /^I confirm "(?P<needle>(?:[^"]|\\")*)" in "(?P<haystack>(?:[^"]|\\")*)" search within the gradebook widget exists$/
     * @param string $needle The value to search for.
     * @param string $haystack The type of the search widget.
     * @deprecated since 4.5
     */
    public function i_confirm_in_search_within_the_gradebook_widget_exists($needle, $haystack) {
        $this->deprecated_message('behat_general::i_confirm_in_search_combobox_exists');

        $this->execute("behat_general::wait_until_the_page_is_ready");

        // Set the default field to search and handle any special preamble.
        $selector = '.usersearchdropdown';
        if (strtolower($haystack) === 'group') {
            $selector = '.groupsearchdropdown';
            $trigger = ".groupsearchwidget";
            $node = $this->find("css_element", $selector);
            if (!$node->isVisible()) {
                $this->execute("behat_general::i_click_on", [$trigger, "css_element"]);
            }
        } else if (strtolower($haystack) === 'grade') {
            $selector = '.gradesearchdropdown';
            $trigger = ".gradesearchwidget";
            $node = $this->find("css_element", $selector);
            if (!$node->isVisible()) {
                $this->execute("behat_general::i_click_on", [$trigger, "css_element"]);
            }
        }

        $this->execute("behat_general::assert_element_contains_text",
            [$needle, $selector, "css_element"]);
    }

    /**
     * Confirm if a value is not within the search widget within the gradebook.
     *
     * Examples:
     * - I confirm "User" in "user" search within the gradebook widget does not exist
     * - I confirm "Group" in "group" search within the gradebook widget does not exist
     * - I confirm "Grade item" in "grade" search within the gradebook widget does not exist
     *
     * @Given /^I confirm "(?P<needle>(?:[^"]|\\")*)" in "(?P<haystack>(?:[^"]|\\")*)" search within the gradebook widget does not exist$/
     * @param string $needle The value to search for.
     * @param string $haystack The type of the search widget.
     * @deprecated since 4.5
     */
    public function i_confirm_in_search_within_the_gradebook_widget_does_not_exist($needle, $haystack) {
        $this->deprecated_message('behat_general::i_confirm_in_search_combobox_does_not_exist');

        $this->execute("behat_general::wait_until_the_page_is_ready");

        // Set the default field to search and handle any special preamble.
        $selector = '.usersearchdropdown';
        if (strtolower($haystack) === 'group') {
            $selector = '.groupsearchdropdown';
            $trigger = ".groupsearchwidget";
            $node = $this->find("css_element", $selector);
            if (!$node->isVisible()) {
                $this->execute("behat_general::i_click_on", [$trigger, "css_element"]);
            }
        } else if (strtolower($haystack) === 'grade') {
            $selector = '.gradesearchdropdown';
            $trigger = ".gradesearchwidget";
            $node = $this->find("css_element", $selector);
            if (!$node->isVisible()) {
                $this->execute("behat_general::i_click_on", [$trigger, "css_element"]);
            }
        }

        $this->execute("behat_general::assert_element_not_contains_text",
            [$needle, $selector, "css_element"]);
    }

    /**
     * Clicks on an option from the specified search widget in the current gradebook page.
     *
     * Examples:
     * - I click on "Student" in the "user" search widget
     * - I click on "Group" in the "group" search widget
     * - I click on "Grade item" in the "grade" search widget
     *
     * @Given /^I click on "(?P<needle>(?:[^"]|\\")*)" in the "(?P<haystack>(?:[^"]|\\")*)" search widget$/
     * @param string $needle The value to search for.
     * @param string $haystack The type of the search widget.
     * @deprecated since 4.5
     */
    public function i_click_on_in_search_widget(string $needle, string $haystack) {
        $this->deprecated_message('behat_general::i_click_on_in_search_combobox');

        $this->execute("behat_general::wait_until_the_page_is_ready");

        // Set the default field to search and handle any special preamble.
        $string = get_string('searchusers', 'core');
        $selector = '.usersearchdropdown';
        if (strtolower($haystack) === 'group') {
            $string = get_string('searchgroups', 'core');
            $selector = '.groupsearchdropdown';
            $trigger = ".groupsearchwidget";
            $node = $this->find("css_element", $selector);
            if (!$node->isVisible()) {
                $this->execute("behat_general::i_click_on", [$trigger, "css_element"]);
            }
        } else if (strtolower($haystack) === 'grade') {
            $string = get_string('searchitems', 'core');
            $selector = '.gradesearchdropdown';
            $trigger = ".gradesearchwidget";
            $node = $this->find("css_element", $selector);
            if (!$node->isVisible()) {
                $this->execute("behat_general::i_click_on", [$trigger, "css_element"]);
            }
        }

        $this->execute("behat_forms::set_field_value", [$string, $needle]);
        $this->execute("behat_general::wait_until_exists", [$needle, "list_item"]);

        $this->execute('behat_general::i_click_on_in_the', [
            $needle, "list_item",
            $selector, "css_element",
        ]);
        $this->execute("behat_general::i_wait_to_be_redirected");
    }
}

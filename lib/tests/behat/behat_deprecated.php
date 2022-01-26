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
 * Steps definitions that will be deprecated in the next releases.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException,
    Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Gherkin\Node\PyStringNode as PyStringNode;

/**
 * Deprecated behat step definitions.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_deprecated extends behat_base {

    /**
     * Throws an exception if $CFG->behat_usedeprecated is not allowed.
     *
     * @throws Exception
     * @param string|array $alternatives Alternative/s to the requested step
     * @param bool $throwexception If set to true we always throw exception, irrespective of behat_usedeprecated setting.
     * @return void
     */
    protected function deprecated_message($alternatives, $throwexception = false) {
        global $CFG;

        // We do nothing if it is enabled.
        if (!empty($CFG->behat_usedeprecated) && !$throwexception) {
            return;
        }

        if (is_scalar($alternatives)) {
            $alternatives = array($alternatives);
        }

        // Show an appropriate message based on the throwexception flag.
        if ($throwexception) {
            $message = 'This step has been removed. Rather than using this step you can:';
        } else {
            $message = 'Deprecated step, rather than using this step you can:';
        }

        // Add all alternatives to the message.
        foreach ($alternatives as $alternative) {
            $message .= PHP_EOL . '- ' . $alternative;
        }

        if (!$throwexception) {
            $message .= PHP_EOL . '- Set $CFG->behat_usedeprecated in config.php to allow the use of deprecated steps
                    if you don\'t have any other option';
        }

        throw new Exception($message);
    }

    /**
     * Clicks link with specified id|title|alt|text in the flat navigation drawer.
     *
     * @When /^I select "(?P<link_string>(?:[^"]|\\")*)" from flat navigation drawer$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $link
     * @deprecated Since Moodle 4.0
     */
    public function i_select_from_flat_navigation_drawer(string $link) {
        self::deprecated_message(['i_select_from_primary_navigation', 'i_select_from_secondary_navigation'], true);

        $this->i_open_flat_navigation_drawer();
        $this->execute('behat_general::i_click_on_in_the', [$link, 'link', '#nav-drawer', 'css_element']);
    }

    /**
     * Hover over a specific day in the calendar.
     *
     * @Given /^I hover over day "(?P<dayofmonth>\d+)" of this month in the calendar$/
     * @param int $day The day of the current month
     *
     * @deprecated since 4.0 MDL-72810. This tested the three-month calendar pseudo block, which has been removed.
     * @todo MDL-73117 This will be deleted in Moodle 4.4.
     */
    public function i_hover_over_day_of_this_month_in_calendar($day) {
        $this->deprecated_message('Check information in the course or timeline calendar blocks or full calendar, as appropriate.');

        $summarytitle = userdate(time(), get_string('strftimemonthyear'));
        // The current month table.
        $currentmonth = "table[descendant::*[self::caption[contains(concat(' ', normalize-space(.), ' '), ' {$summarytitle} ')]]]";

        // Strings for the class cell match.
        $cellclasses  = "contains(concat(' ', normalize-space(@class), ' '), ' day ')";
        $daycontains  = "text()[contains(concat(' ', normalize-space(.), ' '), ' {$day} ')]";
        $daycell      = "td[{$cellclasses}]";
        $dayofmonth   = "a[{$daycontains}]";

        $xpath = '//' . $currentmonth . '/descendant::' . $daycell . '/' . $dayofmonth;
        $this->execute("behat_general::i_hover", [$xpath, "xpath_element"]);
    }

    /**
     * Click a specific day in the calendar.
     *
     * @Given /^I click day "(?P<dayofmonth>\d+)" of this month in the calendar$/
     * @param int $day The day of the current month
     *
     * @deprecated since 4.0 MDL-72810. This tested the three-month calendar pseudo block, which has been removed.
     * @todo MDL-73117 This will be deleted in Moodle 4.4.
     */
    public function i_click_day_of_this_month_in_calendar($day) {
        $this->deprecated_message('Check information in the course or timeline calendar blocks or full calendar, as appropriate.');

        // The current month table.
        $currentmonth = "table[descendant::*[self::caption[contains(concat(' ', normalize-space(.), ' '), ' {$summarytitle} ')]]]";

        // Strings for the class cell match.
        $cellclasses  = "contains(concat(' ', normalize-space(@class), ' '), ' day ')";
        $daycontains  = "text()[contains(concat(' ', normalize-space(.), ' '), ' {$day} ')]";
        $daycell      = "td[{$cellclasses}]";
        $dayofmonth   = "a[{$daycontains}]";

        $xpath = '//' . $currentmonth . '/descendant::' . $daycell . '/' . $dayofmonth;
        $this->execute("behat_general::wait_until_the_page_is_ready");
        $this->execute("behat_general::i_click_on", array($xpath, "xpath_element"));
        $this->execute("behat_general::wait_until_the_page_is_ready");
    }

    /**
     * Adds the specified enrolment method to the current course filling the form with the provided data.
     *
     * @Given /^I add "(?P<enrolment_method_name_string>(?:[^"]|\\")*)" enrolment method with:$/
     * @param string $enrolmethod
     * @param TableNode $table
     *
     * @deprecated since 4.0 MDL-72090. We now need the course to enrol in. Please use i_add_enrolment_method_for_with()
     * @todo MDL-71733 This will be deleted in Moodle 4.4.
     */
    public function i_add_enrolment_method_with($enrolmethod, TableNode $table) {
        $this->deprecated_message(['i_add_enrolment_method_for_with']);

        // Navigate to enrolment method page.
        $parentnodes = get_string('users', 'admin');
        $this->execute("behat_navigation::i_navigate_to_in_current_page_administration",
            array($parentnodes .' > '. get_string('type_enrol_plural', 'plugin'))
        );

        // Select enrolment method.
        $this->execute('behat_forms::i_select_from_the_singleselect',
            array($this->escape($enrolmethod), get_string('addinstance', 'enrol'))
        );

        // Wait again, for page to reloaded.
        $this->execute('behat_general::i_wait_to_be_redirected');

        // Set form fields.
        $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $table);

        // Ensure we get button in focus, before pressing button.
        if ($this->running_javascript()) {
            $this->execute('behat_general::i_press_named_key', ['', 'tab']);
        }

        // Save changes.
        $this->execute("behat_forms::press_button", get_string('addinstance', 'enrol'));
    }
}

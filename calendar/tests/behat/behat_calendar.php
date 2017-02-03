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
 * Behat calendar-related steps definitions.
 *
 * @package    core_calendar
 * @category   test
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL used, this file may be required by behat before including /config.php.
require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;

/**
 * Contains functions used by behat to test functionality.
 *
 * @package    core_calendar
 * @category   test
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_calendar extends behat_base {

    /**
     * Create event when starting on the front page.
     *
     * @Given /^I create a calendar event with form data:$/
     * @param TableNode $data
     */
    public function i_create_a_calendar_event_with_form_data($data) {
        // Go to current month page.
        $this->execute("behat_general::click_link", get_string('monththis', 'calendar'));

        // Create event.
        $this->i_create_a_calendar_event($data);
    }

    /**
     * Create event.
     *
     * @Given /^I create a calendar event:$/
     * @param TableNode $data
     */
    public function i_create_a_calendar_event($data) {
        // Get the event name.
        $eventname = $data->getRow(1);
        $eventname = $eventname[1];

        // Click to create new event.
        $this->execute("behat_general::i_click_on", array(get_string('newevent', 'calendar'), "button"));

        // Set form fields.
        $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $data);

        // Save event.
        $this->execute("behat_forms::press_button", get_string('savechanges'));

        // Check if event is created. Being last step, don't need to wait or check for exceptions.
        $this->execute("behat_general::assert_page_contains_text", $eventname);

    }

    /**
     * Hover over a specific day in the calendar.
     *
     * @Given /^I hover over day "(?P<dayofmonth>\d+)" of this month in the calendar$/
     * @param int $day The day of the current month
     */
    public function i_hover_over_day_of_this_month_in_calendar($day) {
        $summarytitle = get_string('calendarheading', 'calendar', userdate(time(), get_string('strftimemonthyear')));
        // The current month table.
        $currentmonth = "table[contains(concat(' ', normalize-space(@summary), ' '), ' {$summarytitle} ')]";

        // Strings for the class cell match.
        $cellclasses  = "contains(concat(' ', normalize-space(@class), ' '), ' day ')";
        $daycontains  = "text()[contains(concat(' ', normalize-space(.), ' '), ' {$day} ')]";
        $daycell      = "td[{$cellclasses}]";
        $dayofmonth   = "a[{$daycontains}]";

        $xpath = '//' . $currentmonth . '/descendant::' . $daycell . '/' . $dayofmonth;
        $this->execute("behat_general::i_hover", array($xpath, "xpath_element"));

    }

    /**
     * Hover over today in the calendar.
     *
     * @Given /^I hover over today in the calendar$/
     */
    public function i_hover_over_today_in_the_calendar() {
        // For window's compatibility, using %d and not %e.
        $todaysday = trim(strftime('%d'));
        $todaysday = ltrim($todaysday, '0');
        return $this->i_hover_over_day_of_this_month_in_calendar($todaysday);
    }
}
